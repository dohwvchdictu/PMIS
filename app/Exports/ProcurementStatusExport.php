<?php

namespace App\Exports;

use App\Models\BidSchedule;
use App\Models\Procurement;
use App\Models\PrSvp;
use App\Models\Supply;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ProcurementStatusExport implements FromCollection, WithCustomStartCell, WithMapping, WithEvents, WithStyles, ShouldAutoSize
{
    protected string $search;
    protected string $startDate;
    protected string $endDate;
    protected int $year;
    protected int $quarter;

    protected $bidScheduleMap;
    protected $prSvpMap;
    protected $supplyMap;

    /** Total columns in the report (A–W) */
    private const LAST_COL = 'W';
    private const LAST_COL_IDX = 23; // 1-based

    public function __construct(
        string $search,
        string $startDate,
        string $endDate,
        int $year,
        int $quarter
    ) {
        $this->search = $search;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->year = $year;
        $this->quarter = $quarter;
    }

    // -------------------------------------------------------------------------
    // Data starts at row 6 (rows 1-5 are the header block)
    // -------------------------------------------------------------------------

    public function startCell(): string
    {
        return 'A7';
    }

    // -------------------------------------------------------------------------
    // Query
    // -------------------------------------------------------------------------

    public function collection()
    {
        $query = Procurement::query()
            ->with([
                'clusterCommittee',
                'fundSource',
                'currentPrStage',
                'mopLots.modeOfProcurement',
                'pr_items.prstage',
                'pr_items.mopItems.modeOfProcurement',
                'pr_items.postProcurement.pmu.pmuPos',
                'postProcurement.pmu.pmuPos',
            ])
            ->whereBetween('date_receipt', [$this->startDate, $this->endDate])
            ->where('pr_number', 'like', $this->year . '-%')
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('procurement_type', 'perLot')
                        ->whereHas('prLotPrstages', fn($sq) => $sq->where('pr_stage_id', 7));
                })->orWhere(function ($sub) {
                    $sub->where('procurement_type', '!=', 'perLot')
                        ->whereHas('prItemPrstages', fn($sq) => $sq->where('pr_stage_id', 7));
                });
            })
            ->latest('date_receipt');

        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('pr_number', 'like', $term)
                    ->orWhere('procurement_program_project', 'like', $term);
            });
        }

        $procurements = $query->get();

        // Batch-load BidSchedule and PrSvp
        $allUids = [];
        $allProcIds = $procurements->pluck('procID')->filter()->toArray();

        foreach ($procurements as $p) {
            if ($p->procurement_type === 'perLot') {
                $uid = $p->mopLots->sortByDesc('mode_order')->first()?->uid;
                if ($uid) {
                    $allUids[] = $uid;
                }
            }
        }

        $this->bidScheduleMap = collect();
        $this->prSvpMap = collect();

        if (!empty($allUids) && !empty($allProcIds)) {
            $this->bidScheduleMap = BidSchedule::whereIn('mop_uid', $allUids)
                ->whereIn('ref_id', $allProcIds)
                ->get()
                ->keyBy(fn($b) => $b->ref_id . '_' . $b->mop_uid);

            $this->prSvpMap = PrSvp::whereIn('mop_uid', $allUids)
                ->whereIn('ref_id', $allProcIds)
                ->get()
                ->keyBy(fn($s) => $s->ref_id . '_' . $s->mop_uid);
        }

        // Batch-load Supply/SupplyPo
        $poNos = [];
        foreach ($procurements as $p) {
            if ($p->procurement_type === 'perLot') {
                foreach ($p->postProcurement?->pmu?->pmuPos ?? [] as $pmuPo) {
                    if ($pmuPo->po_contract_number) {
                        $poNos[] = $pmuPo->po_contract_number;
                    }
                }
            } else {
                foreach ($p->pr_items->filter(fn($i) => ($i->prstage?->pr_stage_id == 7)) as $item) {
                    foreach ($item->postProcurement?->pmu?->pmuPos ?? [] as $pmuPo) {
                        if ($pmuPo->po_contract_number) {
                            $poNos[] = $pmuPo->po_contract_number;
                        }
                    }
                }
            }
        }

        $this->supplyMap = collect();
        if (!empty($poNos)) {
            $this->supplyMap = Supply::whereIn('po_contract_number', $poNos)
                ->with('supplyPos')
                ->get()
                ->keyBy('po_contract_number');
        }

        // Expand rows (only stage-7 items for per-item procurements)
        $rows = collect();
        foreach ($procurements as $p) {
            if ($p->procurement_type === 'perLot') {
                $rows->push($this->buildRow($p, null));
            } else {
                foreach ($p->pr_items->filter(fn($i) => ($i->prstage?->pr_stage_id == 7)) as $item) {
                    $rows->push($this->buildRow($p, $item));
                }
            }
        }

        return $rows;
    }

    // -------------------------------------------------------------------------
    // Map (rows already built as arrays)
    // -------------------------------------------------------------------------

    public function map($row): array
    {
        return $row;
    }

    // -------------------------------------------------------------------------
    // Row builder
    // -------------------------------------------------------------------------

    private function buildRow($procurement, $item): array
    {
        $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y') : '';

        $preProcConf = $adsPostIb = $preBidConf = $eligibility = '';
        $subOpen = $bidEval = $postQual = $modeName = '';

        if ($procurement->procurement_type === 'perLot') {
            $latestMop = $procurement->mopLots->sortByDesc('mode_order')->first();
            $modeName = $latestMop?->modeOfProcurement?->modeofprocurements ?? '';
            $modeId = $latestMop?->mode_of_procurement_id;
            $key = $procurement->procID . '_' . ($latestMop?->uid ?? '');

            $bidSched = in_array($modeId, [2, 3, 4, 5, 6])
                ? $this->bidScheduleMap->get($key)
                : null;
            $prSvp = in_array($modeId, range(7, 24))
                ? $this->prSvpMap->get($key)
                : null;

            $preProcConf = $fmt($bidSched?->pre_proc_conference);
            $adsPostIb = $bidSched ? $fmt($bidSched->ads_post_ib) : $fmt($prSvp?->ads_post_ib);
            $preBidConf = $fmt($bidSched?->pre_bid_conf);
            $eligibility = $fmt($bidSched?->eligibility_check);
            $subOpen = $fmt($bidSched?->sub_open_bids);
            $bidEval = $fmt($bidSched?->bid_evaluation_date);
            $postQual = $fmt($bidSched?->post_qualification_date);
        } else {
            $latestMop = $item?->mopItems->sortByDesc('mode_order')->first();
            $modeName = $latestMop?->modeOfProcurement?->modeofprocurements ?? '';
        }

        // Per-item rows have their own PostProcurement linked to prItemID; use it.
        $post = ($item !== null) ? $item->postProcurement : $procurement->postProcurement;
        $pmuPo = $post?->pmu?->pmuPos->first();

        $noticeOfAward = $fmt($post?->notice_of_award);
        $contractSigning = $fmt($pmuPo?->contract_signing_date);
        $noticeToProceed = $fmt($pmuPo?->notice_to_proceed_date);

        $deliveryCompletion = '';
        $inspectionAcceptance = '';

        if ($pmuPo?->po_contract_number) {
            $supply = $this->supplyMap->get($pmuPo->po_contract_number);
            $supplyPo = $supply?->supplyPos->first();
            $deliveryCompletion = $fmt($supplyPo?->delivery_completion);
            $inspectionAcceptance = $fmt($supplyPo?->date_of_acceptance);
        }

        $abcTotal = $item ? ($item->amount ?? '') : ($procurement->abc ?? '');
        $contractTotal = $post?->awarded_amount ?? '';

        return [
            $procurement->pr_number,
            $item ? ($item->description ?? '') : ($procurement->procurement_program_project ?? ''),
            $procurement->clusterCommittee?->clustercommittee ?? '',
            $modeName,
            $preProcConf,
            $adsPostIb,
            $preBidConf,
            $eligibility,
            $subOpen,
            $bidEval,
            $postQual,
            $noticeOfAward,
            $contractSigning,
            $noticeToProceed,
            $deliveryCompletion,
            $inspectionAcceptance,
            $procurement->fundSource?->fundsources ?? '',
            $abcTotal !== '' ? (float) $abcTotal : '',
            '', // MOOE
            '', // CO
            $contractTotal !== '' && $contractTotal !== null ? (float) $contractTotal : '',
            '', // MOOE
            '', // CO
        ];
    }

    // -------------------------------------------------------------------------
    // Complex header via AfterSheet event
    // -------------------------------------------------------------------------

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $quarterName = ['1st', '2nd', '3rd', '4th'][$this->quarter - 1] ?? '';

                // ── Row 1: Report title ───────────────────────────────────────
                $sheet->mergeCells('A1:' . self::LAST_COL . '1');
                $sheet->setCellValue('A1', 'Procurement Status Report');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '047857']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(24);

                // ── Row 2: Year / Quarter ─────────────────────────────────────
                $sheet->mergeCells('A2:' . self::LAST_COL . '2');
                $sheet->setCellValue('A2', 'Year: ' . $this->year . '    Quarter: ' . $quarterName);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(18);

                // ── Row 3: Region/Hospital ────────────────────────────────────
                $sheet->mergeCells('A3:' . self::LAST_COL . '3');
                $sheet->setCellValue('A3', 'Region/Hospital:    DEPARTMENT OF HEALTH -WESTERN VISAYAS CENTER FOR HEALTH DEVELOPMENT');
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F2937']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                ]);
                $sheet->getRowDimension(3)->setRowHeight(18);

                // ── Row 4: Column group headers ───────────────────────────────
                // Cols: A=Code(PAP), B=Project, C=PMO/End-User, D=Mode
                //       E-P=Actual Procurement Activity (12 cols)
                //       Q=Source of Funds
                //       R-T=ABC PhP (3)
                //       U-W=Contract Cost PhP (3)
    
                // Single-col cells span rows 4–5
                foreach (['A', 'B', 'C', 'D', 'Q'] as $col) {
                    $sheet->mergeCells($col . '4:' . $col . '5');
                }
                $sheet->setCellValue('A4', 'Code (PAP)');
                $sheet->setCellValue('B4', 'Procurement Project');
                $sheet->setCellValue('C4', 'PMO/End-User (Cluster)');
                $sheet->setCellValue('D4', 'Mode of Procurement');
                $sheet->setCellValue('Q4', 'Source of Funds');

                // Group spans
                $sheet->mergeCells('E4:P4');
                $sheet->setCellValue('E4', 'Actual Procurement Activity');
                $sheet->mergeCells('R4:T4');
                $sheet->setCellValue('R4', 'ABC (PhP)');
                $sheet->mergeCells('U4:W4');
                $sheet->setCellValue('U4', 'Contract Cost (PhP)');

                // ── Row 5: Sub-headers ────────────────────────────────────────
                $subHeaders = [
                    'E5' => 'Pre-Proc Conference',
                    'F5' => 'Ads/Post of IB',
                    'G5' => 'Pre-bid Conf',
                    'H5' => 'Eligibility Check',
                    'I5' => 'Sub/Open of Bids',
                    'J5' => 'Bid Evaluation',
                    'K5' => 'Post Qual',
                    'L5' => 'Notice of Award',
                    'M5' => 'Contract Signing',
                    'N5' => 'Notice to Proceed',
                    'O5' => 'Delivery/Completion',
                    'P5' => 'Inspection & Acceptance',
                    'R5' => 'Total',
                    'S5' => 'MOOE',
                    'T5' => 'CO',
                    'U5' => 'Total',
                    'V5' => 'MOOE',
                    'W5' => 'CO',
                ];
                foreach ($subHeaders as $cell => $value) {
                    $sheet->setCellValue($cell, $value);
                }

                // ── Style rows 4–5 ────────────────────────────────────────────
                $headerStyle = [
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '047857']],
                    ],
                ];
                $sheet->getStyle('A4:' . self::LAST_COL . '5')->applyFromArray($headerStyle);
                $sheet->getRowDimension(4)->setRowHeight(30);
                $sheet->getRowDimension(5)->setRowHeight(40);

                // ── Row 6: Section header ─────────────────────────────────────
                $sheet->mergeCells('A6:' . self::LAST_COL . '6');
                $sheet->setCellValue('A6', 'COMPLETED PROCUREMENT ACTIVITIES');
                $sheet->getStyle('A6')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '374151']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F3F4F6']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER, 'indent' => 2],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                    ],
                ]);
                $sheet->getRowDimension(6)->setRowHeight(20);

                // ── Style data rows ───────────────────────────────────────────
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 7) {
                    $dataStyle = [
                        'borders' => [
                            'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1D5DB']],
                        ],
                        'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => false],
                    ];
                    $sheet->getStyle('A7:' . self::LAST_COL . $lastRow)->applyFromArray($dataStyle);

                    // Alternate row shading
                    for ($r = 7; $r <= $lastRow; $r++) {
                        if ($r % 2 !== 0) {
                            $sheet->getStyle('A' . $r . ':' . self::LAST_COL . $r)
                                ->getFill()
                                ->setFillType(Fill::FILL_SOLID)
                                ->getStartColor()->setRGB('F9FAFB');
                        }
                    }

                    // Right-align ABC Total and Contract Cost Total columns
                    $sheet->getStyle('R7:R' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                    $sheet->getStyle('U7:U' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                    // Center date columns (E–P)
                    $sheet->getStyle('E7:P' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // ── Freeze panes below header ─────────────────────────────────
                $sheet->freezePane('A7');

                // ── Column widths ─────────────────────────────────────────────
                $widths = [
                    'A' => 16,
                    'B' => 36,
                    'C' => 20,
                    'D' => 22,
                    'E' => 14,
                    'F' => 14,
                    'G' => 12,
                    'H' => 14,
                    'I' => 14,
                    'J' => 14,
                    'K' => 12,
                    'L' => 14,
                    'M' => 14,
                    'N' => 14,
                    'O' => 16,
                    'P' => 18,
                    'Q' => 18,
                    'R' => 14,
                    'S' => 14,
                    'T' => 12,
                    'U' => 14,
                    'V' => 14,
                    'W' => 12,
                ];
                foreach ($widths as $col => $width) {
                    $sheet->getColumnDimension($col)->setWidth($width)->setAutoSize(false);
                }
            },
        ];
    }

    // -------------------------------------------------------------------------
    // Styles (applied to data area via WithStyles; header done in AfterSheet)
    // -------------------------------------------------------------------------

    public function styles(Worksheet $sheet)
    {
        // No additional styles needed here; all handled via AfterSheet.
        return [];
    }
}
