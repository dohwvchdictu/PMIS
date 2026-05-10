<?php

namespace App\Exports;

use App\Models\BidSchedule;
use App\Models\Procurement;
use App\Models\PmuPo;
use App\Models\PrSvp;
use App\Models\Supply;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PmrCatAExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths
{
    protected $search;
    protected $year;
    protected $clusterFilter;
    protected $fundSourceFilter;
    protected $currentModeFilter;

    protected $bidScheduleMap;
    protected $prSvpMap;
    protected $pmuPoMap;
    protected $supplyMap;

    public function __construct($search, $year, $clusterFilter, $fundSourceFilter, $currentModeFilter)
    {
        $this->search            = $search;
        $this->year              = $year;
        $this->clusterFilter     = $clusterFilter;
        $this->fundSourceFilter  = $fundSourceFilter;
        $this->currentModeFilter = $currentModeFilter;
    }

    public function collection()
    {
        $query = Procurement::query()
            ->with([
                'currentPrStage.procurementStage',
                'division',
                'clusterCommittee',
                'category.bacType',
                'fundSource',
                'endUser',
                'venueSpecific',
                'venueProvincesHUC',
                'mopLots.modeOfProcurement',
                'currentLotRemark.remark',
                'postProcurement.supplier',
                'postProcurement.pmu',
            ])
            ->whereHas('category', fn($q) => $q->where('bac_type_id', 1))
            ->where('pr_number', 'like', $this->year . '-%')
            ->orderBy('pr_number', 'asc');

        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(fn($q) => $q
                ->where('pr_number', 'like', $term)
                ->orWhere('procurement_program_project', 'like', $term)
            );
        }

        if ($this->clusterFilter) {
            $query->where('cluster_committees_id', $this->clusterFilter);
        }

        if ($this->fundSourceFilter) {
            $query->where('fund_source_id', $this->fundSourceFilter);
        }

        if ($this->currentModeFilter) {
            $query->whereHas('mopLots', fn($q) => $q
                ->where('mode_of_procurement_id', $this->currentModeFilter)
                ->whereRaw('mode_order = (SELECT MAX(mode_order) FROM mop_lot WHERE procID = procurements.procID)')
            );
        }

        $procurements = $query->get();

        $allProcIds = $procurements->pluck('procID')->filter()->toArray();
        $allUids = [];
        foreach ($procurements as $p) {
            $uid = $p->mopLots->sortByDesc('mode_order')->first()?->uid;
            if ($uid) $allUids[] = $uid;
        }

        $this->bidScheduleMap = collect();
        if (!empty($allProcIds) && !empty($allUids)) {
            $this->bidScheduleMap = BidSchedule::whereIn('ref_id', $allProcIds)
                ->whereIn('mop_uid', $allUids)
                ->get()
                ->groupBy(fn($b) => $b->ref_id . '_' . $b->mop_uid)
                ->map(fn($group) => $group->keyBy('bidding_number'));
        }

        $this->prSvpMap = collect();
        if (!empty($allProcIds) && !empty($allUids)) {
            $this->prSvpMap = PrSvp::whereIn('ref_id', $allProcIds)
                ->whereIn('mop_uid', $allUids)
                ->get()
                ->keyBy(fn($s) => $s->ref_id . '_' . $s->mop_uid);
        }

        $this->pmuPoMap = !empty($allProcIds)
            ? PmuPo::whereIn('ref_id', $allProcIds)->get()->keyBy('ref_id')
            : collect();

        $poNos = $this->pmuPoMap->pluck('po_contract_number')->filter()->toArray();
        $this->supplyMap = !empty($poNos)
            ? Supply::whereIn('po_contract_number', $poNos)->with('supplyPos')->get()->keyBy('po_contract_number')
            : collect();

        return $procurements;
    }

    public function headings(): array
    {
        return [
            'PR Number',
            'IB Number',
            'NP No.',
            'Procurement Program / Project',
            'Date Receipt',
            'RBAC/SBAC',
            'DTRACK #',
            'UniCode',
            'Division',
            'Cluster / Committee',
            'Category',
            'Venue (Specific)',
            'Venue (Province/HUC)',
            'Category / Venue',
            'w/ Approved PPMP',
            'APP (Updated)',
            'Immediate Date Needed',
            'Date Needed',
            'PMO / End-User',
            'EPA',
            'Source of Funds',
            'Expense Class',
            'ABC',
            'Mode of Procurement',
            'ABC <=> 50k',
            'Pre-Proc Conference',
            'Ads/Post of IB',
            'Pre-bid Conf',
            'Eligibility Check',
            'Sub/Open of Bids',
            '1st Bidding Date',
            '1st Bidding Result',
            '2nd Bidding Date',
            '2nd Bidding Result',
            'Bid Evaluation Date',
            'Post Qual Date',
            'Resolution No. (Recom. Mode)',
            'NTF No.',
            'NTF Bidding Date',
            'NTF Bidding Result',
            'RFQ No.',
            'Canvass Date',
            'Date Returned of Canvass',
            'Abstract of Canvass Date',
            'Date of BAC Resolution (Award)',
            'Notice of Award Date',
            'Awarded Amount',
            'Award Notice Number',
            'Date of Posting of Award (PhilGEPS)',
            'Supplier',
            'Procurement Stage',
            'Remarks (Status)',
            'Remarks (Notes)',
            'Reschedule / Cancellation Letter',
            'Date Forwarded to PMU',
            'PhilGEPS Posting Ref No.',
            'Contract Amount',
            'PO / Contract Number',
            'Contract Signing / PO',
            'Notice to Proceed',
            'Delivery / Completion',
            'Inspection & Acceptance',
            'List of Invited Observers',
            'Observers (Pre-bid Conf)',
            'Observers (Eligibility)',
            'Observers (Sub/Open)',
            'Observers (Bid Eval)',
            'Observers (Post Qual)',
            'Delivery/Completion/Acceptance',
            'Remarks (Changes from APP)',
        ];
    }

    public function map($procurement): array
    {
        $fmt = function ($d) {
            if (!$d) return '';
            try { return Carbon::parse($d)->format('M d, Y'); } catch (\Exception $e) { return $d; }
        };

        $latestMop = $procurement->mopLots->sortByDesc('mode_order')->first();
        $modeId    = $latestMop?->mode_of_procurement_id;

        $isBidding = in_array($modeId, [2, 3, 4, 5, 6]);
        $isSvp     = in_array($modeId, range(7, 24));

        $key          = $procurement->procID . '_' . ($latestMop?->uid ?? '');
        $biddingGroup = $this->bidScheduleMap->get($key);
        $bidding1     = $biddingGroup?->get(1);
        $bidding2     = $biddingGroup?->get(2);
        $mainBid      = $bidding1 ?? $biddingGroup?->first();

        $prSvp    = $this->prSvpMap->get($key);
        $pmuPo    = $this->pmuPoMap->get($procurement->procID);
        $supply   = $this->supplyMap->get($pmuPo?->po_contract_number);
        $supplyPo = $supply?->supplyPos->first();
        $post     = $procurement->postProcurement;
        $pmu      = $post?->pmu;

        return [
            $procurement->pr_number,
            $isBidding ? ($mainBid?->ib_number ?? '') : '',
            '',
            $procurement->procurement_program_project,
            $fmt($procurement->date_receipt),
            $procurement->category?->bacType?->abbreviation ?? '',
            $procurement->dtrack_no ?? '',
            $procurement->unicode ?? '',
            $procurement->division?->divisions ?? '',
            $procurement->clusterCommittee?->clustercommittee ?? '',
            $procurement->category?->category ?? '',
            $procurement->venueSpecific?->name ?? '',
            $procurement->venueProvincesHUC?->province_huc ?? '',
            $procurement->category_venue ?? '',
            $procurement->approved_ppmp ? 'Yes' : 'No',
            $procurement->app_updated ? 'Yes' : 'No',
            $fmt($procurement->immediate_date_needed),
            $fmt($procurement->date_needed),
            $procurement->endUser?->endusers ?? '',
            $procurement->early_procurement ? 'Yes' : 'No',
            $procurement->fundSource?->fundsources ?? '',
            $procurement->expense_class ?? '',
            $procurement->abc !== null ? (float) $procurement->abc : '',
            $latestMop?->modeOfProcurement?->modeofprocurements ?? '',
            $procurement->abc_50k ?? '',
            $isBidding ? $fmt($mainBid?->pre_proc_conference) : '',
            $isBidding ? $fmt($mainBid?->ads_post_ib) : ($isSvp ? $fmt($prSvp?->ads_post_ib) : ''),
            $isBidding ? $fmt($mainBid?->pre_bid_conf) : '',
            $isBidding ? $fmt($mainBid?->eligibility_check) : '',
            $isBidding ? $fmt($mainBid?->sub_open_bids) : '',
            $isBidding ? $fmt($bidding1?->sub_open_bids) : '',
            $isBidding ? ($bidding1?->bidding_result ?? '') : '',
            $isBidding ? $fmt($bidding2?->sub_open_bids) : '',
            $isBidding ? ($bidding2?->bidding_result ?? '') : '',
            $isBidding ? $fmt($mainBid?->bid_evaluation_date) : '',
            $isBidding ? $fmt($mainBid?->post_qualification_date) : '',
            $isBidding ? ($mainBid?->resolution_number_mop ?? '') : ($isSvp ? ($prSvp?->resolution_number_mop ?? '') : ''),
            '',
            '',
            '',
            $isSvp ? ($prSvp?->rfq_no ?? '') : '',
            $isSvp ? $fmt($prSvp?->canvass_date) : '',
            $isSvp ? $fmt($prSvp?->date_returned_of_canvass) : '',
            $isSvp ? $fmt($prSvp?->abstract_of_canvass_date) : '',
            $fmt($post?->resolution_award_date),
            $fmt($post?->notice_of_award),
            $post?->awarded_amount !== null ? (float) $post->awarded_amount : '',
            $post?->notice_of_award_number ?? '',
            $fmt($post?->philgeps_posting_of_award),
            $post?->supplier?->name ?? '',
            $procurement->currentPrStage?->procurementStage?->procurementstage ?? '',
            $procurement->currentLotRemark?->remark?->remarks ?? '',
            $procurement->currentLotRemark?->notes ?? '',
            '',
            $fmt($pmu?->date_forwarded),
            $isBidding ? ($mainBid?->philgeps_posting_ref_no ?? '') : ($isSvp ? ($prSvp?->philgeps_posting_ref_no ?? '') : ''),
            $pmuPo?->contract_amount !== null ? (float) $pmuPo->contract_amount : '',
            $pmuPo?->po_contract_number ?? '',
            $fmt($pmuPo?->contract_signing_date),
            $fmt($pmuPo?->notice_to_proceed_date),
            $fmt($supplyPo?->delivery_completion),
            $fmt($supplyPo?->date_of_acceptance),
            $isBidding ? ($mainBid?->list_invited_observers ?? '') : '',
            $isBidding ? ($mainBid?->obsrvr_prebid_conf ?? '') : '',
            $isBidding ? ($mainBid?->obsrvr_eligibility ?? '') : '',
            $isBidding ? ($mainBid?->obsrvr_sub_open_of_bid ?? '') : '',
            $isBidding ? ($mainBid?->obsrvr_bid ?? '') : '',
            $isBidding ? ($mainBid?->obsrvr_post_qual ?? '') : '',
            '',
            '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol    = 'BR'; // 70 columns
        $highestRow = $sheet->getHighestRow();

        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 11,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '059669'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ]);

        $sheet->getStyle("A2:{$lastCol}{$highestRow}")->applyFromArray([
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
        ]);

        // ABC (col 23 = W)
        $sheet->getStyle("W2:W{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('_("₱"* #,##0.00_);_("₱"* (#,##0.00);_("₱"* "-"??_);_(@_)');

        // Awarded Amount (col 47 = AU)
        $sheet->getStyle("AU2:AU{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('_("₱"* #,##0.00_);_("₱"* (#,##0.00);_("₱"* "-"??_);_(@_)');

        // Contract Amount (col 57 = BE)
        $sheet->getStyle("BE2:BE{$highestRow}")
            ->getNumberFormat()
            ->setFormatCode('_("₱"* #,##0.00_);_("₱"* (#,##0.00);_("₱"* "-"??_);_(@_)');

        $sheet->getRowDimension(1)->setRowHeight(40);

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A'  => 18, // PR Number
            'B'  => 18, // IB Number
            'C'  => 12, // NP No.
            'D'  => 45, // Procurement Program / Project  ← wrap
            'E'  => 14, // Date Receipt
            'F'  => 12, // RBAC/SBAC
            'G'  => 16, // DTRACK #
            'H'  => 16, // UniCode
            'I'  => 30, // Division                        ← wrap
            'J'  => 28, // Cluster / Committee             ← wrap
            'K'  => 25, // Category                        ← wrap
            'L'  => 25, // Venue (Specific)                ← wrap
            'M'  => 22, // Venue (Province/HUC)            ← wrap
            'N'  => 30, // Category / Venue                ← wrap
            'O'  => 16, // w/ Approved PPMP
            'P'  => 14, // APP (Updated)
            'Q'  => 18, // Immediate Date Needed
            'R'  => 14, // Date Needed
            'S'  => 30, // PMO / End-User                  ← wrap
            'T'  => 10, // EPA
            'U'  => 25, // Source of Funds                 ← wrap
            'V'  => 16, // Expense Class
            'W'  => 18, // ABC
            'X'  => 30, // Mode of Procurement             ← wrap
            'Y'  => 14, // ABC <=> 50k
            'Z'  => 18, // Pre-Proc Conference
            'AA' => 18, // Ads/Post of IB
            'AB' => 16, // Pre-bid Conf
            'AC' => 16, // Eligibility Check
            'AD' => 18, // Sub/Open of Bids
            'AE' => 16, // 1st Bidding Date
            'AF' => 20, // 1st Bidding Result              ← wrap
            'AG' => 16, // 2nd Bidding Date
            'AH' => 20, // 2nd Bidding Result              ← wrap
            'AI' => 18, // Bid Evaluation Date
            'AJ' => 16, // Post Qual Date
            'AK' => 30, // Resolution No. (Recom. Mode)    ← wrap
            'AL' => 14, // NTF No.
            'AM' => 16, // NTF Bidding Date
            'AN' => 18, // NTF Bidding Result
            'AO' => 14, // RFQ No.
            'AP' => 16, // Canvass Date
            'AQ' => 22, // Date Returned of Canvass
            'AR' => 22, // Abstract of Canvass Date
            'AS' => 24, // Date of BAC Resolution (Award)
            'AT' => 20, // Notice of Award Date
            'AU' => 18, // Awarded Amount
            'AV' => 22, // Award Notice Number
            'AW' => 24, // Date of Posting of Award (PhilGEPS)
            'AX' => 35, // Supplier                        ← wrap
            'AY' => 30, // Procurement Stage               ← wrap
            'AZ' => 25, // Remarks (Status)                ← wrap
            'BA' => 35, // Remarks (Notes)                 ← wrap
            'BB' => 25, // Reschedule / Cancellation Letter
            'BC' => 20, // Date Forwarded to PMU
            'BD' => 24, // PhilGEPS Posting Ref No.
            'BE' => 18, // Contract Amount
            'BF' => 22, // PO / Contract Number
            'BG' => 20, // Contract Signing / PO
            'BH' => 18, // Notice to Proceed
            'BI' => 18, // Delivery / Completion
            'BJ' => 22, // Inspection & Acceptance
            'BK' => 35, // List of Invited Observers        ← wrap
            'BL' => 30, // Observers (Pre-bid Conf)         ← wrap
            'BM' => 30, // Observers (Eligibility)          ← wrap
            'BN' => 30, // Observers (Sub/Open)             ← wrap
            'BO' => 30, // Observers (Bid Eval)             ← wrap
            'BP' => 30, // Observers (Post Qual)            ← wrap
            'BQ' => 22, // Delivery/Completion/Acceptance
            'BR' => 30, // Remarks (Changes from APP)       ← wrap
        ];
    }
}
