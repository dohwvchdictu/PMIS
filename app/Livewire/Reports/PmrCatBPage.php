<?php

namespace App\Livewire\Reports;

use App\Exports\PmrCatBExport;
use App\Models\BidSchedule;
use App\Models\ClusterCommittee;
use App\Models\FundSource;
use App\Models\ModeOfProcurement;
use App\Models\Procurement;
use App\Models\PrSvp;
use App\Models\PmuPo;
use App\Models\Supply;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

#[Title("PMR (CAT B) | PMIS")]
class PmrCatBPage extends Component
{
    use WithPagination;

    public bool $showFilters = false;
    public int $perPage = 10;
    public int $year;
    public string $search = '';
    public $clusterFilter = null;
    public $fundSourceFilter = null;
    public $currentModeFilter = null;

    protected $queryString = [
        'search'            => ['except' => ''],
        'perPage'           => ['except' => 10],
        'year'              => ['except' => ''],
        'clusterFilter'     => ['except' => null],
        'fundSourceFilter'  => ['except' => null],
        'currentModeFilter' => ['except' => null],
    ];

    protected $paginationTheme = 'tailwind';

    public function mount(): void
    {
        $this->year = now()->year;
    }

    public function updatingSearch()            { $this->resetPage(); }
    public function updatingPerPage()           { $this->resetPage(); }
    public function updatingYear()              { $this->resetPage(); }
    public function updatingClusterFilter()     { $this->resetPage(); }
    public function updatingFundSourceFilter()  { $this->resetPage(); }
    public function updatingCurrentModeFilter() { $this->resetPage(); }

    public function clearFilters(): void
    {
        $this->search            = '';
        $this->clusterFilter     = null;
        $this->fundSourceFilter  = null;
        $this->currentModeFilter = null;
        $this->resetPage();
    }

    public function exportToExcel()
    {
        $fileName = 'PMR_CAT_B_' . $this->year . '_' . now()->format('Y-m-d') . '.xlsx';

        return Excel::download(
            new PmrCatBExport(
                $this->search,
                $this->year,
                $this->clusterFilter,
                $this->fundSourceFilter,
                $this->currentModeFilter
            ),
            $fileName
        );
    }

    public function render()
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
                // perItem relations
                'pr_items.mopItems.modeOfProcurement',
                'pr_items.prstage.stage',
                'pr_items.currentItemRemark.remark',
                'pr_items.postProcurement.supplier',
                'pr_items.postProcurement.pmu',
            ])
            ->whereHas('category', fn($q) => $q->where('bac_type_id', 2))
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
            $query->where(function ($q) {
                $q->where(function ($sub) {
                    $sub->where('procurement_type', 'perLot')
                        ->whereHas('mopLots', fn($sq) => $sq
                            ->where('mode_of_procurement_id', $this->currentModeFilter)
                            ->whereRaw('mode_order = (SELECT MAX(mode_order) FROM mop_lot WHERE procID = procurements.procID)')
                        );
                })->orWhere(function ($sub) {
                    $sub->where('procurement_type', '!=', 'perLot')
                        ->whereHas('pr_items.mopItems', fn($sq) => $sq
                            ->where('mode_of_procurement_id', $this->currentModeFilter)
                            ->whereRaw('mode_order = (SELECT MAX(mode_order) FROM mop_item WHERE prItemID = pr_items.prItemID)')
                        );
                });
            });
        }

        $procurements = $query->paginate($this->perPage);

        // Collect IDs for batch loading
        $allLotProcIds = [];
        $allLotUids    = [];
        $allItemIds    = [];
        $allItemUids   = [];

        foreach ($procurements as $p) {
            if ($p->procurement_type === 'perLot') {
                $allLotProcIds[] = $p->procID;
                $uid = $p->mopLots->sortByDesc('mode_order')->first()?->uid;
                if ($uid) $allLotUids[] = $uid;
            } else {
                foreach ($p->pr_items as $item) {
                    $allItemIds[] = $item->prItemID;
                    $uid = $item->mopItems->sortByDesc('mode_order')->first()?->uid;
                    if ($uid) $allItemUids[] = $uid;
                }
            }
        }

        // Lot-level bid schedules: grouped by procID_mopUid keyed by bidding_number
        $lotBidScheduleMap = collect();
        if (!empty($allLotProcIds) && !empty($allLotUids)) {
            $lotBidScheduleMap = BidSchedule::whereIn('ref_id', $allLotProcIds)
                ->whereIn('mop_uid', $allLotUids)
                ->get()
                ->groupBy(fn($b) => $b->ref_id . '_' . $b->mop_uid)
                ->map(fn($group) => $group->keyBy('bidding_number'));
        }

        // Item-level bid schedules: grouped by prItemID_mopUid keyed by bidding_number
        $itemBidScheduleMap = collect();
        if (!empty($allItemIds) && !empty($allItemUids)) {
            $itemBidScheduleMap = BidSchedule::whereIn('ref_id', $allItemIds)
                ->whereIn('mop_uid', $allItemUids)
                ->get()
                ->groupBy(fn($b) => $b->ref_id . '_' . $b->mop_uid)
                ->map(fn($group) => $group->keyBy('bidding_number'));
        }

        // Lot-level PrSvp
        $lotPrSvpMap = collect();
        if (!empty($allLotProcIds) && !empty($allLotUids)) {
            $lotPrSvpMap = PrSvp::whereIn('ref_id', $allLotProcIds)
                ->whereIn('mop_uid', $allLotUids)
                ->get()
                ->keyBy(fn($s) => $s->ref_id . '_' . $s->mop_uid);
        }

        // Item-level PrSvp
        $itemPrSvpMap = collect();
        if (!empty($allItemIds) && !empty($allItemUids)) {
            $itemPrSvpMap = PrSvp::whereIn('ref_id', $allItemIds)
                ->whereIn('mop_uid', $allItemUids)
                ->get()
                ->keyBy(fn($s) => $s->ref_id . '_' . $s->mop_uid);
        }

        // PmuPo maps
        $lotPmuPoMap = !empty($allLotProcIds)
            ? PmuPo::whereIn('ref_id', $allLotProcIds)->get()->keyBy('ref_id')
            : collect();

        $itemPmuPoMap = !empty($allItemIds)
            ? PmuPo::whereIn('ref_id', $allItemIds)->get()->keyBy('ref_id')
            : collect();

        // Supply map (all PO numbers from both lot and item pmuPos)
        $poNos = $lotPmuPoMap->pluck('po_contract_number')
            ->merge($itemPmuPoMap->pluck('po_contract_number'))
            ->filter()
            ->unique()
            ->toArray();

        $supplyMap = !empty($poNos)
            ? Supply::whereIn('po_contract_number', $poNos)->with('supplyPos')->get()->keyBy('po_contract_number')
            : collect();

        return view('livewire.reports.pmr-cat-b-page', [
            'procurements'      => $procurements,
            'lotBidScheduleMap' => $lotBidScheduleMap,
            'itemBidScheduleMap'=> $itemBidScheduleMap,
            'lotPrSvpMap'       => $lotPrSvpMap,
            'itemPrSvpMap'      => $itemPrSvpMap,
            'lotPmuPoMap'       => $lotPmuPoMap,
            'itemPmuPoMap'      => $itemPmuPoMap,
            'supplyMap'         => $supplyMap,
            'modes'             => $this->modes,
            'clusterOptions'    => $this->clusterOptions,
            'fundSources'       => $this->fundSources,
        ]);
    }

    public function getModesProperty()
    {
        return ModeOfProcurement::orderBy('id')->get()
            ->map(fn($m) => ['id' => $m->id, 'name' => $m->modeofprocurements]);
    }

    public function getClusterOptionsProperty()
    {
        return ClusterCommittee::orderBy('clustercommittee')->get()
            ->map(fn($c) => ['id' => $c->id, 'name' => $c->clustercommittee]);
    }

    public function getFundSourcesProperty()
    {
        return FundSource::orderBy('fundsources')->get()
            ->map(fn($f) => ['id' => $f->id, 'name' => $f->fundsources]);
    }
}
