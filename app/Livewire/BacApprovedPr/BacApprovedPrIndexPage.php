<?php

namespace App\Livewire\BacApprovedPr;

use App\Models\BACApprovedPR;
use App\Models\ClusterCommittee;
use App\Models\Division;
use App\Models\EndUser;
use App\Models\FundSource;
use App\Models\Procurement;
use App\Models\Remarks;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('BAC Approved PR | PMIS')]
class BacApprovedPrIndexPage extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';
    public int $perPage = 10;

    // Filters
    public $divisionFilter = '';
    public $clusterCommitteeFilter = '';
    public $endUserFilter = '';
    public $fundSourceFilter = '';
    public $remarkFilter = '';
    public $earlyProcurementFilter = '';

    // Filter options
    public $divisions = [];
    public $clusterCommittees = [];
    public $endUsers = [];
    public $fundSources = [];
    public $remarks = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'divisionFilter' => ['except' => ''],
        'clusterCommitteeFilter' => ['except' => ''],
        'endUserFilter' => ['except' => ''],
        'fundSourceFilter' => ['except' => ''],
        'remarkFilter' => ['except' => ''],
        'earlyProcurementFilter' => ['except' => ''],
    ];

    public function mount()
    {
        $this->loadFilterOptions();

        if (session('alert')) {
            $alert = session('alert');

            LivewireAlert::title($alert['title'])
                ->{$alert['type']}()
                    ->text($alert['message'])
                    ->toast()
                    ->position('top-end')
                    ->show();
        }
    }

    public function loadFilterOptions()
    {
        // All option queries are scoped to procurements that have a BAC Approved PR
        $base = fn() => Procurement::whereHas('bacApprovedPr');

        if (!$this->divisionFilter && !$this->clusterCommitteeFilter && !$this->endUserFilter && !$this->fundSourceFilter && !$this->remarkFilter && !$this->earlyProcurementFilter) {
            $this->divisions = Division::whereIn('id', $base()->distinct()->pluck('divisions_id')->filter())->orderBy('abbreviation')->get();
            $this->clusterCommittees = ClusterCommittee::whereIn('id', $base()->distinct()->pluck('cluster_committees_id')->filter())->orderBy('clustercommittee')->get();
            $this->endUsers = EndUser::whereIn('id', $base()->distinct()->pluck('end_users_id')->filter())->orderBy('endusers')->get();
            $this->fundSources = FundSource::whereIn('id', $base()->distinct()->pluck('fund_source_id')->filter())->orderBy('fundsources')->get();

            $lotRemarkIds = $base()->whereHas('currentLotRemark')->with('currentLotRemark')->get()->pluck('currentLotRemark.remarks_id')->filter();
            $itemRemarkIds = $base()->whereHas('pr_items.currentItemRemark')->with('pr_items.currentItemRemark')->get()
                ->flatMap(fn($p) => $p->pr_items->pluck('currentItemRemark.remarks_id'))->filter();
            $this->remarks = Remarks::whereIn('id', $lotRemarkIds->merge($itemRemarkIds)->unique())->orderBy('remarks')->get();
            return;
        }

        // Division options — exclude divisionFilter, apply others
        $divisionQuery = $base();
        if (!empty($this->clusterCommitteeFilter)) $divisionQuery->where('cluster_committees_id', $this->clusterCommitteeFilter);
        if (!empty($this->endUserFilter)) $divisionQuery->where('end_users_id', $this->endUserFilter);
        if (!empty($this->fundSourceFilter)) $divisionQuery->where('fund_source_id', $this->fundSourceFilter);
        if (!empty($this->remarkFilter)) {
            $divisionQuery->where(function ($q) {
                $q->whereHas('currentLotRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter))
                    ->orWhereHas('pr_items.currentItemRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter));
            });
        }
        if ($this->earlyProcurementFilter !== '') $divisionQuery->where('early_procurement', $this->earlyProcurementFilter);
        $this->divisions = Division::whereIn('id', $divisionQuery->distinct()->pluck('divisions_id')->filter())->orderBy('abbreviation')->get();

        // Cluster options — exclude clusterCommitteeFilter, apply others
        $clusterQuery = $base();
        if (!empty($this->divisionFilter)) $clusterQuery->where('divisions_id', $this->divisionFilter);
        if (!empty($this->endUserFilter)) $clusterQuery->where('end_users_id', $this->endUserFilter);
        if (!empty($this->fundSourceFilter)) $clusterQuery->where('fund_source_id', $this->fundSourceFilter);
        if (!empty($this->remarkFilter)) {
            $clusterQuery->where(function ($q) {
                $q->whereHas('currentLotRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter))
                    ->orWhereHas('pr_items.currentItemRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter));
            });
        }
        if ($this->earlyProcurementFilter !== '') $clusterQuery->where('early_procurement', $this->earlyProcurementFilter);
        $this->clusterCommittees = ClusterCommittee::whereIn('id', $clusterQuery->distinct()->pluck('cluster_committees_id')->filter())->orderBy('clustercommittee')->get();

        // End User options — exclude endUserFilter, apply others
        $endUserQuery = $base();
        if (!empty($this->divisionFilter)) $endUserQuery->where('divisions_id', $this->divisionFilter);
        if (!empty($this->clusterCommitteeFilter)) $endUserQuery->where('cluster_committees_id', $this->clusterCommitteeFilter);
        if (!empty($this->fundSourceFilter)) $endUserQuery->where('fund_source_id', $this->fundSourceFilter);
        if (!empty($this->remarkFilter)) {
            $endUserQuery->where(function ($q) {
                $q->whereHas('currentLotRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter))
                    ->orWhereHas('pr_items.currentItemRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter));
            });
        }
        if ($this->earlyProcurementFilter !== '') $endUserQuery->where('early_procurement', $this->earlyProcurementFilter);
        $this->endUsers = EndUser::whereIn('id', $endUserQuery->distinct()->pluck('end_users_id')->filter())->orderBy('endusers')->get();

        // Fund Source options — exclude fundSourceFilter, apply others
        $fundQuery = $base();
        if (!empty($this->divisionFilter)) $fundQuery->where('divisions_id', $this->divisionFilter);
        if (!empty($this->clusterCommitteeFilter)) $fundQuery->where('cluster_committees_id', $this->clusterCommitteeFilter);
        if (!empty($this->endUserFilter)) $fundQuery->where('end_users_id', $this->endUserFilter);
        if (!empty($this->remarkFilter)) {
            $fundQuery->where(function ($q) {
                $q->whereHas('currentLotRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter))
                    ->orWhereHas('pr_items.currentItemRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter));
            });
        }
        if ($this->earlyProcurementFilter !== '') $fundQuery->where('early_procurement', $this->earlyProcurementFilter);
        $this->fundSources = FundSource::whereIn('id', $fundQuery->distinct()->pluck('fund_source_id')->filter())->orderBy('fundsources')->get();

        // Remark options — exclude remarkFilter, apply others
        $remarkQuery = $base();
        if (!empty($this->divisionFilter)) $remarkQuery->where('divisions_id', $this->divisionFilter);
        if (!empty($this->clusterCommitteeFilter)) $remarkQuery->where('cluster_committees_id', $this->clusterCommitteeFilter);
        if (!empty($this->endUserFilter)) $remarkQuery->where('end_users_id', $this->endUserFilter);
        if (!empty($this->fundSourceFilter)) $remarkQuery->where('fund_source_id', $this->fundSourceFilter);
        if ($this->earlyProcurementFilter !== '') $remarkQuery->where('early_procurement', $this->earlyProcurementFilter);

        $lotRemarkIds = $remarkQuery->clone()->whereHas('currentLotRemark')->with('currentLotRemark')->get()->pluck('currentLotRemark.remarks_id')->filter();
        $itemRemarkIds = $remarkQuery->clone()->whereHas('pr_items.currentItemRemark')->with('pr_items.currentItemRemark')->get()
            ->flatMap(fn($p) => $p->pr_items->pluck('currentItemRemark.remarks_id'))->filter();
        $this->remarks = Remarks::whereIn('id', $lotRemarkIds->merge($itemRemarkIds)->unique())->orderBy('remarks')->get();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedDivisionFilter()
    {
        $this->resetPage();
        $this->loadFilterOptions();
    }

    public function updatedClusterCommitteeFilter()
    {
        $this->resetPage();
        $this->loadFilterOptions();
    }

    public function updatedEndUserFilter()
    {
        $this->resetPage();
        $this->loadFilterOptions();
    }

    public function updatedFundSourceFilter()
    {
        $this->resetPage();
        $this->loadFilterOptions();
    }

    public function updatedRemarkFilter()
    {
        $this->resetPage();
        $this->loadFilterOptions();
    }

    public function updatedEarlyProcurementFilter()
    {
        $this->resetPage();
        $this->loadFilterOptions();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->divisionFilter = '';
        $this->clusterCommitteeFilter = '';
        $this->endUserFilter = '';
        $this->fundSourceFilter = '';
        $this->remarkFilter = '';
        $this->earlyProcurementFilter = '';
        $this->resetPage();
        $this->loadFilterOptions();
    }

    public function viewPdf(string $url): void
    {
        $this->dispatch('show-pdf-modal', url: $url);
    }

    public function render()
    {
        $query = BACApprovedPR::with('procurement')
            ->whereHas('procurement', function ($q) {
                if (!empty($this->search)) {
                    $searchTerm = '%' . $this->search . '%';
                    $q->where(function ($sq) use ($searchTerm) {
                        $sq->where('pr_number', 'like', $searchTerm)
                            ->orWhere('procurement_program_project', 'like', $searchTerm);
                    });
                }
                if (!empty($this->divisionFilter)) $q->where('divisions_id', $this->divisionFilter);
                if (!empty($this->clusterCommitteeFilter)) $q->where('cluster_committees_id', $this->clusterCommitteeFilter);
                if (!empty($this->endUserFilter)) $q->where('end_users_id', $this->endUserFilter);
                if (!empty($this->fundSourceFilter)) $q->where('fund_source_id', $this->fundSourceFilter);
                if (!empty($this->remarkFilter)) {
                    $q->where(function ($rq) {
                        $rq->whereHas('currentLotRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter))
                            ->orWhereHas('pr_items.currentItemRemark', fn($s) => $s->where('remarks_id', $this->remarkFilter));
                    });
                }
                if ($this->earlyProcurementFilter !== '') $q->where('early_procurement', $this->earlyProcurementFilter);
            })
            ->latest($this->sortField);

        $approvedPrs = $query->paginate($this->perPage);

        return view('livewire.bac-approved-pr.bac-approved-pr-index-page', [
            'approvedPrs' => $approvedPrs,
        ]);
    }
}
