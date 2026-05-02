<?php

namespace App\Livewire\Reports;

use App\Models\BidSchedule;
use App\Models\Procurement;
use App\Models\PrSvp;
use App\Models\Supply;
use App\Exports\ProcurementStatusExport;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title("Procurement Status Report | PMIS")]
class ProcurementStatusPage extends Component
{
    use WithPagination;

    public int $year;
    public int $quarter;
    public string $search = '';
    public int $perPage = 5;
    public int $ongoingPerPage = 5;
    public bool $showAdvancedFilters = false;
    public string $pmoEndUserFilter = '';
    public string $sourceOfFundsFilter = '';
    public string $categoryFilter = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'year' => ['except' => ''],
        'quarter' => ['except' => ''],
        'perPage' => ['except' => 5],
        'ongoingPerPage' => ['except' => 5],
        'showAdvancedFilters' => ['except' => false],
        'pmoEndUserFilter' => ['except' => ''],
        'sourceOfFundsFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
    ];

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->quarter = (int) ceil(now()->month / 3);
    }

    // =====================================================================
    // FILTERING LOGIC EXPLANATION
    // =====================================================================
    //
    // COMPLETED PROCUREMENT ACTIVITIES:
    //   - Shows PRs/items that have EVER reached stage 7 (Forwarded to PMU)
    //   - Checks all historical stage records, not just current stage
    //   - Includes PRs currently at stage 7 or those that advanced past it
    //   - Applies: date_receipt, pr_number prefix, search filters
    //
    // ON-GOING PROCUREMENT ACTIVITIES:
    //   - Shows PRs/items that have NEVER reached stage 7
    //   - These are early-stage procurements still in pre-PMU stages
    //   - Applies: date_receipt, pr_number prefix, search filters
    //
    // Both sections respect the same date range (year-1 to end of selected quarter)
    // and PR number year filter to capture early procurement activities.
    // =====================================================================

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function getQuarterDates(): array
    {
        $quarterEnd = [
            1 => '03-31',
            2 => '06-30',
            3 => '09-30',
            4 => '12-31',
        ];
        return [($this->year - 1) . '-01-01', $this->year . '-' . $quarterEnd[$this->quarter]];
    }

    public static function quarterName(int $q): string
    {
        return ['1st', '2nd', '3rd', '4th'][$q - 1] ?? (string) $q;
    }

    // -------------------------------------------------------------------------
    // Property updaters
    // -------------------------------------------------------------------------

    public function updatingSearch(): void
    {
        $this->resetPage();
        $this->resetPage('ongoingPage');
    }
    public function updatingYear(): void
    {
        $this->resetPage();
        $this->resetPage('ongoingPage');
    }
    public function updatingQuarter(): void
    {
        $this->resetPage();
        $this->resetPage('ongoingPage');
    }
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }
    public function updatingOngoingPerPage(): void
    {
        $this->resetPage('ongoingPage');
    }

    public function updatingPmoEndUserFilter(): void
    {
        $this->resetPage();
        $this->resetPage('ongoingPage');
    }

    public function updatingSourceOfFundsFilter(): void
    {
        $this->resetPage();
        $this->resetPage('ongoingPage');
    }

    public function updatingCategoryFilter(): void
    {
        $this->resetPage();
        $this->resetPage('ongoingPage');
    }

    // -------------------------------------------------------------------------
    // Shared filter helper
    // -------------------------------------------------------------------------

    private function applyCommonFilters($query): void
    {
        if (!empty($this->search)) {
            $term = '%' . $this->search . '%';
            $query->where(function ($q) use ($term) {
                $q->where('pr_number', 'like', $term)
                    ->orWhere('procurement_program_project', 'like', $term);
            });
        }
        if (!empty($this->pmoEndUserFilter)) {
            $query->whereHas('clusterCommittee', fn($q) => $q->where('clustercommittee', $this->pmoEndUserFilter));
        }
        if (!empty($this->sourceOfFundsFilter)) {
            $query->whereHas('fundSource', fn($q) => $q->where('fundsources', $this->sourceOfFundsFilter));
        }
        if (!empty($this->categoryFilter)) {
            $query->whereHas('category', fn($q) => $q->where('category', $this->categoryFilter));
        }
    }

    // -------------------------------------------------------------------------
    // Aggregate totals across ALL records (not just current page)
    // -------------------------------------------------------------------------

    private function computeTotals(string $startDate, string $endDate): array
    {
        $completedAll = Procurement::query()
            ->with(['pr_items.prstage', 'pr_items.postProcurement', 'postProcurement'])
            ->whereBetween('date_receipt', [$startDate, $endDate])
            ->where('pr_number', 'like', $this->year . '-%')
            ->where(function ($q) use ($endDate) {
                $q->where(function ($sub) use ($endDate) {
                    $sub->where('procurement_type', 'perLot')
                        ->whereHas('prLotPrstages', fn($sq) => $sq->where('pr_stage_id', 7))
                        ->whereHas('postProcurement', fn($sq) => $sq->where('notice_of_award', '<=', $endDate));
                })->orWhere(function ($sub) use ($endDate) {
                    $sub->where('procurement_type', '!=', 'perLot')
                        ->whereHas('prItemPrstages', fn($sq) => $sq->where('pr_stage_id', 7))
                        ->whereHas('pr_items.postProcurement', fn($sq) => $sq->where('notice_of_award', '<=', $endDate));
                });
            });
        $this->applyCommonFilters($completedAll);

        $completedAbc      = 0.0;
        $completedContract = 0.0;
        $ongoingAbc        = 0.0;
        $completedRowCount = 0;
        $ongoingRowCount   = 0;

        foreach ($completedAll->get() as $p) {
            if ($p->procurement_type === 'perLot') {
                $completedAbc      += (float) ($p->abc ?? 0);
                $completedContract += (float) ($p->postProcurement?->awarded_amount ?? 0);
                $completedRowCount++;
            } else {
                foreach ($p->pr_items as $item) {
                    if ($item->prstage?->pr_stage_id == 7) {
                        $completedAbc      += (float) ($item->amount ?? 0);
                        $completedContract += (float) ($item->postProcurement?->awarded_amount ?? 0);
                        $completedRowCount++;
                    } else {
                        $ongoingAbc += (float) ($item->amount ?? 0);
                        $ongoingRowCount++;
                    }
                }
            }
        }

        $ongoingAll = Procurement::query()
            ->with(['pr_items'])
            ->whereBetween('date_receipt', [$startDate, $endDate])
            ->where('pr_number', 'like', $this->year . '-%')
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('procurement_type', 'perLot')
                        ->whereDoesntHave('prLotPrstages', fn($sq) => $sq->where('pr_stage_id', 7));
                })->orWhere(function ($sub) {
                    $sub->where('procurement_type', '!=', 'perLot')
                        ->whereDoesntHave('prItemPrstages', fn($sq) => $sq->where('pr_stage_id', 7));
                });
            });
        $this->applyCommonFilters($ongoingAll);

        foreach ($ongoingAll->get() as $p) {
            if ($p->procurement_type === 'perLot') {
                $ongoingAbc += (float) ($p->abc ?? 0);
                $ongoingRowCount++;
            } else {
                foreach ($p->pr_items as $item) {
                    $ongoingAbc += (float) ($item->amount ?? 0);
                    $ongoingRowCount++;
                }
            }
        }

        return [
            'completedAbc'      => $completedAbc,
            'completedContract' => $completedContract,
            'ongoingAbc'        => $ongoingAbc,
            'completedRowCount' => $completedRowCount,
            'ongoingRowCount'   => $ongoingRowCount,
        ];
    }

    // -------------------------------------------------------------------------
    // Export
    // -------------------------------------------------------------------------

    public function exportToExcel()
    {
        [$startDate, $endDate] = $this->getQuarterDates();
        $fileName = 'Procurement_Status_Report_' . $this->year . '_Q' . $this->quarter . '.xlsx';

        return Excel::download(
            new ProcurementStatusExport(
                $this->search,
                $startDate,
                $endDate,
                $this->year,
                $this->quarter,
                $this->pmoEndUserFilter,
                $this->sourceOfFundsFilter,
                $this->categoryFilter
            ),
            $fileName
        );
    }

    // -------------------------------------------------------------------------
    // Render
    // -------------------------------------------------------------------------

    public function render()
    {
        [$startDate, $endDate] = $this->getQuarterDates();

        $query = Procurement::query()
            ->with([
                'category',
                'clusterCommittee',
                'fundSource',
                'currentPrStage',
                'mopLots.modeOfProcurement',
                'pr_items.prstage',
                'pr_items.mopItems.modeOfProcurement',
                'pr_items.postProcurement.pmu.pmuPos',
                'postProcurement.pmu.pmuPos',
            ])
            ->whereBetween('date_receipt', [$startDate, $endDate])
            ->where('pr_number', 'like', $this->year . '-%')
            ->where(function ($q) use ($endDate) {
                $q->where(function ($sub) use ($endDate) {
                    $sub->where('procurement_type', 'perLot')
                        ->whereHas('prLotPrstages', fn($sq) => $sq->where('pr_stage_id', 7))
                        ->whereHas('postProcurement', fn($sq) => $sq->where('notice_of_award', '<=', $endDate));
                })->orWhere(function ($sub) use ($endDate) {
                    $sub->where('procurement_type', '!=', 'perLot')
                        ->whereHas('prItemPrstages', fn($sq) => $sq->where('pr_stage_id', 7))
                        ->whereHas('pr_items.postProcurement', fn($sq) => $sq->where('notice_of_award', '<=', $endDate));
                });
            });

        $this->applyCommonFilters($query);

        $procurements = $query->latest('date_receipt')->paginate($this->perPage);

        // Batch-load BidSchedule and PrSvp (per-lot only)
        $allUids = [];
        foreach ($procurements as $p) {
            if ($p->procurement_type === 'perLot') {
                $uid = $p->mopLots->sortByDesc('mode_order')->first()?->uid;
                if ($uid) $allUids[] = $uid;
            } else {
                foreach ($p->pr_items as $item) {
                    $uid = $item->mopItems->sortByDesc('mode_order')->first()?->uid;
                    if ($uid) $allUids[] = $uid;
                }
            }
        }

        $bidScheduleMap = collect();
        $prSvpMap = collect();

        if (!empty($allUids)) {
            $bidScheduleMap = BidSchedule::whereIn('mop_uid', $allUids)
                ->get()
                ->keyBy(fn($b) => $b->ref_id . '_' . $b->mop_uid);

            $prSvpMap = PrSvp::whereIn('mop_uid', $allUids)
                ->get()
                ->keyBy(fn($s) => $s->ref_id . '_' . $s->mop_uid);
        }

        // Batch-load Supply/SupplyPo via PmuPo.po_contract_number
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

        $supplyMap = collect();
        if (!empty($poNos)) {
            $supplyMap = Supply::whereIn('po_contract_number', $poNos)
                ->with('supplyPos')
                ->get()
                ->keyBy('po_contract_number');
        }

        // Build display rows — prepend section header
        $rows = [['_section_header' => 'COMPLETED PROCUREMENT ACTIVITIES']];
        foreach ($procurements as $p) {
            if ($p->procurement_type === 'perLot') {
                $rows[] = $this->buildRow($p, null, $bidScheduleMap, $prSvpMap, $supplyMap);
            } else {
                // Filter per-item rows: only show items that have reached stage 7 in history
                foreach ($p->pr_items->filter(
                    fn($i) =>
                    $i->procurement?->prItemPrstages?->contains(fn($s) => $s->pr_stage_id == 7)
                ) as $item) {
                    $rows[] = $this->buildRow($p, $item, $bidScheduleMap, $prSvpMap, $supplyMap);
                }
            }
        }

        // ── ON-GOING: procurements NOT at stage 7 ────────────────────────────
        // Filters: NEVER reached stage 7 in entire history + date range + search
        $ongoingQuery = Procurement::query()
            ->with([
                'category',
                'clusterCommittee',
                'fundSource',
                'currentPrStage',
                'mopLots.modeOfProcurement',
                'pr_items.prstage',
                'pr_items.mopItems.modeOfProcurement',
                'pr_items.postProcurement.pmu.pmuPos',
                'postProcurement.pmu.pmuPos',
            ])
            ->whereBetween('date_receipt', [$startDate, $endDate])
            ->where('pr_number', 'like', $this->year . '-%')
            ->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('procurement_type', 'perLot')
                        ->whereDoesntHave('prLotPrstages', fn($sq) => $sq->where('pr_stage_id', 7));
                })->orWhere(function ($sub) {
                    // Include any non-perLot PR that has at least one item
                    // whose latest stage is not 7 (covers pure-ongoing and mixed PRs)
                    $sub->where('procurement_type', '!=', 'perLot')
                        ->whereHas('pr_items', fn($sq) => $sq->whereDoesntHave('prstage', fn($s) => $s->where('pr_stage_id', 7)));
                });
            });

        $this->applyCommonFilters($ongoingQuery);

        $ongoingProcurements = $ongoingQuery->latest('date_receipt')->paginate($this->ongoingPerPage, ['*'], 'ongoingPage');

        // Batch-load for ongoing
        $ogUids = [];
        foreach ($ongoingProcurements->items() as $p) {
            if ($p->procurement_type === 'perLot') {
                $uid = $p->mopLots->sortByDesc('mode_order')->first()?->uid;
                if ($uid) $ogUids[] = $uid;
            } else {
                foreach ($p->pr_items as $item) {
                    $uid = $item->mopItems->sortByDesc('mode_order')->first()?->uid;
                    if ($uid) $ogUids[] = $uid;
                }
            }
        }

        $ogBidScheduleMap = collect();
        $ogPrSvpMap = collect();
        if (!empty($ogUids)) {
            $ogBidScheduleMap = BidSchedule::whereIn('mop_uid', $ogUids)
                ->get()
                ->keyBy(fn($b) => $b->ref_id . '_' . $b->mop_uid);

            $ogPrSvpMap = PrSvp::whereIn('mop_uid', $ogUids)
                ->get()
                ->keyBy(fn($s) => $s->ref_id . '_' . $s->mop_uid);
        }

        $ogPoNos = [];
        foreach ($ongoingProcurements->items() as $p) {
            if ($p->procurement_type === 'perLot') {
                foreach ($p->postProcurement?->pmu?->pmuPos ?? [] as $pmuPo) {
                    if ($pmuPo->po_contract_number)
                        $ogPoNos[] = $pmuPo->po_contract_number;
                }
            }
        }
        $ogSupplyMap = collect();
        if (!empty($ogPoNos)) {
            $ogSupplyMap = Supply::whereIn('po_contract_number', $ogPoNos)
                ->with('supplyPos')
                ->get()
                ->keyBy('po_contract_number');
        }

        $ongoingRows = [['_section_header' => 'ON-GOING PROCUREMENT ACTIVITIES']];
        foreach ($ongoingProcurements->items() as $p) {
            if ($p->procurement_type === 'perLot') {
                $ongoingRows[] = $this->buildRow($p, null, $ogBidScheduleMap, $ogPrSvpMap, $ogSupplyMap);
            } else {
                foreach ($p->pr_items->filter(fn($i) => ($i->prstage?->pr_stage_id ?? 0) != 7) as $item) {
                    $ongoingRows[] = $this->buildRow($p, $item, $ogBidScheduleMap, $ogPrSvpMap, $ogSupplyMap);
                }
            }
        }

        $totals = $this->computeTotals($startDate, $endDate);

        $completedAbcTotal      = $totals['completedAbc'];
        $completedContractTotal = $totals['completedContract'];
        $completedSavingsTotal  = $completedAbcTotal - $completedContractTotal;
        $ongoingAbcTotal        = $totals['ongoingAbc'];
        $completedRowCount      = $totals['completedRowCount'];
        $ongoingRowCount        = $totals['ongoingRowCount'];
        $summaryAbcTotal        = $completedAbcTotal + $ongoingAbcTotal;
        $summaryPctCompleted    = $summaryAbcTotal > 0 ? ($completedAbcTotal / $summaryAbcTotal) * 100 : 0;
        $summaryPctOngoing      = $summaryAbcTotal > 0 ? ($ongoingAbcTotal   / $summaryAbcTotal) * 100 : 0;

        return view('livewire.reports.procurement-status-page', [
            'procurements'          => $procurements,
            'rows'                  => $rows,
            'ongoingRows'           => $ongoingRows,
            'ongoingProcurements'   => $ongoingProcurements,
            'completedAbcTotal'     => $completedAbcTotal,
            'completedContractTotal'=> $completedContractTotal,
            'completedSavingsTotal' => $completedSavingsTotal,
            'ongoingAbcTotal'       => $ongoingAbcTotal,
            'completedRowCount'     => $completedRowCount,
            'ongoingRowCount'       => $ongoingRowCount,
            'summaryAbcCompleted'   => $completedAbcTotal,
            'summaryAbcOngoing'     => $ongoingAbcTotal,
            'summaryAbcTotal'       => $summaryAbcTotal,
            'summaryPctCompleted'   => $summaryPctCompleted,
            'summaryPctOngoing'     => $summaryPctOngoing,
            'summaryPctTotal'       => $summaryPctCompleted + $summaryPctOngoing,
            'pmoEndUserOptions'     => \App\Models\ClusterCommittee::distinct()->pluck('clustercommittee')->filter()->sort()->values(),
            'sourceOfFundsOptions'  => \App\Models\FundSource::distinct()->pluck('fundsources')->filter()->sort()->values(),
            'categoryOptions'       => \App\Models\Category::where('is_active', true)->orderBy('category')->pluck('category')->filter()->values(),
        ]);
    }

    // -------------------------------------------------------------------------
    // Row builder
    // -------------------------------------------------------------------------

    public function buildRow($procurement, $item, $bidScheduleMap, $prSvpMap, $supplyMap): array
    {
        $fmt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('m/d/Y') : '';

        // ── Mode & schedule dates ──────────────────────────────────────────────
        $preProcConf = $adsPostIb = $preBidConf = $eligibility = '';
        $subOpen = $bidEval = $postQual = $modeName = '';

        if ($procurement->procurement_type === 'perLot') {
            $latestMop = $procurement->mopLots->sortByDesc('mode_order')->first();
            $modeName = $latestMop?->modeOfProcurement?->modeofprocurements ?? '';
            $modeId = $latestMop?->mode_of_procurement_id;
            $key = $procurement->procID . '_' . ($latestMop?->uid ?? '');

            $bidSched = in_array($modeId, [2, 3, 4, 5, 6])
                ? $bidScheduleMap->get($key)
                : null;
            $prSvp = in_array($modeId, range(7, 24))
                ? $prSvpMap->get($key)
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
            $modeName  = $latestMop?->modeOfProcurement?->modeofprocurements ?? '';
            $modeId    = $latestMop?->mode_of_procurement_id;
            $key       = ($item?->prItemID ?? '') . '_' . ($latestMop?->uid ?? '');

            $bidSched = in_array($modeId, [2, 3, 4, 5, 6])
                ? $bidScheduleMap->get($key)
                : null;
            $prSvp = in_array($modeId, range(7, 24))
                ? $prSvpMap->get($key)
                : null;

            $preProcConf = $fmt($bidSched?->pre_proc_conference);
            $adsPostIb   = $bidSched ? $fmt($bidSched->ads_post_ib) : $fmt($prSvp?->ads_post_ib);
            $preBidConf  = $fmt($bidSched?->pre_bid_conf);
            $eligibility = $fmt($bidSched?->eligibility_check);
            $subOpen     = $fmt($bidSched?->sub_open_bids);
            $bidEval     = $fmt($bidSched?->bid_evaluation_date);
            $postQual    = $fmt($bidSched?->post_qualification_date);
        }

        // ── Post-procurement & PMU dates ──────────────────────────────────────
        // Per-item rows have their own PostProcurement linked to prItemID; use it.
        $post = ($item !== null) ? $item->postProcurement : $procurement->postProcurement;
        $pmuPo = $post?->pmu?->pmuPos->first();

        $noticeOfAward = $fmt($post?->notice_of_award);
        $contractSigning = $fmt($pmuPo?->contract_signing_date);
        $noticeToProceed = $fmt($pmuPo?->notice_to_proceed_date);

        // ── Supply dates ──────────────────────────────────────────────────────
        $deliveryCompletion = '';
        $inspectionAcceptance = '';

        if ($pmuPo?->po_contract_number) {
            $supply = $supplyMap->get($pmuPo->po_contract_number);
            $supplyPo = $supply?->supplyPos->first();
            $deliveryCompletion = $fmt($supplyPo?->delivery_completion);
            $inspectionAcceptance = $fmt($supplyPo?->date_of_acceptance);
        }

        // ── ABC amount (use item amount for per-item rows) ────────────────────
        $abcTotal = $item
            ? ($item->amount ?? '')
            : ($procurement->abc ?? '');

        $contractTotal = $post?->awarded_amount ?? '';
        $endUser = $procurement->clusterCommittee?->clustercommittee ?? '';
        $fundSource = $procurement->fundSource?->fundsources ?? '';
        // ── Determine ABC MOOE vs CO split ─────────────────────────────────────
        $abcMooe = '';
        $abcCo = '';

        $category = $procurement->category?->category ?? '';

        $coCategories = ['IT Peripherals/Equipment', 'Medical Equipment', 'Medical Devices'];
        $isCo = $abcTotal >= 50000 && collect($coCategories)->contains(
            fn($kw) => stripos($category, $kw) !== false
        );

        if ($abcTotal !== '') {
            $abcCo = $isCo ? $abcTotal : 0;
            $abcMooe = $isCo ? 0 : $abcTotal;
        }

        $contractMooe = '';
        $contractCo = '';

        if ($contractTotal !== '' && $contractTotal !== null) {
            $contractCo = $isCo ? $contractTotal : 0;
            $contractMooe = $isCo ? 0 : $contractTotal;
        }

        return [
            'proc_id' => $procurement->procID,
            'code_pap' => $procurement->pr_number,
            'project' => $item ? ($item->description ?? '') : ($procurement->procurement_program_project ?? ''),
            'end_user' => $endUser,
            'mode' => $modeName,
            'pre_proc_conf' => $preProcConf,
            'ads_post_ib' => $adsPostIb,
            'pre_bid_conf' => $preBidConf,
            'eligibility' => $eligibility,
            'sub_open' => $subOpen,
            'bid_eval' => $bidEval,
            'post_qual' => $postQual,
            'notice_of_award' => $noticeOfAward,
            'contract_signing' => $contractSigning,
            'notice_to_proceed' => $noticeToProceed,
            'delivery_completion' => $deliveryCompletion,
            'inspection_acceptance' => $inspectionAcceptance,
            'fund_source' => $procurement->fundSource?->fundsources ?? '',
            'abc_total' => $abcTotal,
            'abc_mooe' => $abcMooe,
            'abc_co' => $abcCo,
            'contract_total' => $contractTotal,
            'contract_mooe' => $contractMooe,
            'contract_co' => $contractCo,
        ];
    }
}
