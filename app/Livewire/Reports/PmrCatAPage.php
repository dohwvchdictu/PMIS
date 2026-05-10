<?php

namespace App\Livewire\Reports;

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

#[Title("PMR (CAT A) | PMIS")]
class PmrCatAPage extends Component
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
            ])
            ->whereHas('category', fn($q) => $q->where('bac_type_id', 1))
            ->where('pr_number', 'like', $this->year . '-%')
            ->latest('date_receipt');

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

        $procurements = $query->paginate($this->perPage);

        $allProcIds = $procurements->pluck('procID')->filter()->toArray();
        $allUids    = [];
        foreach ($procurements as $p) {
            $uid = $p->mopLots->sortByDesc('mode_order')->first()?->uid;
            if ($uid) $allUids[] = $uid;
        }

        // All bid schedules grouped by procID_mopUid, then keyed by bidding_number
        // so we can access 1st (bidding_number=1) and 2nd (bidding_number=2) bidding separately
        $bidScheduleMap = collect();
        if (!empty($allProcIds) && !empty($allUids)) {
            $bidScheduleMap = BidSchedule::whereIn('ref_id', $allProcIds)
                ->whereIn('mop_uid', $allUids)
                ->get()
                ->groupBy(fn($b) => $b->ref_id . '_' . $b->mop_uid)
                ->map(fn($group) => $group->keyBy('bidding_number'));
        }

        $prSvpMap = collect();
        if (!empty($allProcIds) && !empty($allUids)) {
            $prSvpMap = PrSvp::whereIn('ref_id', $allProcIds)
                ->whereIn('mop_uid', $allUids)
                ->get()
                ->keyBy(fn($s) => $s->ref_id . '_' . $s->mop_uid);
        }

        $pmuPoMap = !empty($allProcIds)
            ? PmuPo::whereIn('ref_id', $allProcIds)->get()->keyBy('ref_id')
            : collect();

        $poNos = $pmuPoMap->pluck('po_contract_number')->filter()->toArray();
        $supplyMap = !empty($poNos)
            ? Supply::whereIn('po_contract_number', $poNos)->with('supplyPos')->get()->keyBy('po_contract_number')
            : collect();

        return view('livewire.reports.pmr-cat-a-page', [
            'procurements'   => $procurements,
            'bidScheduleMap' => $bidScheduleMap,
            'prSvpMap'       => $prSvpMap,
            'pmuPoMap'       => $pmuPoMap,
            'supplyMap'      => $supplyMap,
            'modes'          => $this->modes,
            'clusterOptions' => $this->clusterOptions,
            'fundSources'    => $this->fundSources,
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
