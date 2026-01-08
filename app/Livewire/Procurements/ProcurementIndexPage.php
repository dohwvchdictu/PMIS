<?php

namespace App\Livewire\Procurements;

use App\Models\Remarks;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Procurement;
use App\Models\Division;
use App\Models\ClusterCommittee;
use App\Models\EndUser;
use App\Models\FundSource;
use App\Models\Remark;

class ProcurementIndexPage extends Component
{
    use WithPagination;

    // Pagination
    public $perPage = 10;
    public $itemsPerPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'divisionFilter' => ['except' => ''],
        'clusterCommitteeFilter' => ['except' => ''],
        'endUserFilter' => ['except' => ''],
        'fundSourceFilter' => ['except' => ''],
        'remarkFilter' => ['except' => ''],
        'earlyProcurementFilter' => ['except' => ''], // Add this
    ];
    protected $paginationTheme = 'tailwind';

    // Search
    public $search = '';

    // Filters
    public $divisionFilter = '';
    public $clusterCommitteeFilter = '';
    public $endUserFilter = '';
    public $fundSourceFilter = '';
    public $remarkFilter = '';
    public $earlyProcurementFilter = ''; // Add this

    // Modal
    public $showModal = false;
    public $selectedProcurement;

    // Early Procurement
    public $showEarlyPrompt = false;
    public $early = null;

    // Collapsible functionality
    public $expandedProcurementId = null;

    // Form / Reference Data
    public $form = [];
    public $categories = [];
    public $divisions = [];
    public $clusterCommittees = [];
    public $venueSpecifics = [];
    public $venueProvinces = [];
    public $endUsers = [];
    public $fundSources = [];
    public $remarks = [];

    public function mount()
    {
        // Load initial filter options
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

    /**
     * Load filter options based on current filter selections (cascading)
     */
    public function loadFilterOptions()
    {
        // If no filters applied, show all options
        if (!$this->divisionFilter && !$this->clusterCommitteeFilter && !$this->endUserFilter && !$this->fundSourceFilter && !$this->remarkFilter && !$this->earlyProcurementFilter) {
            $this->divisions = Division::orderBy('abbreviation')->get();
            $this->clusterCommittees = ClusterCommittee::orderBy('clustercommittee')->get();
            $this->endUsers = EndUser::orderBy('endusers')->get();
            $this->fundSources = FundSource::orderBy('fundsources')->get();
            $this->remarks = Remarks::orderBy('remarks')->get();
            return;
        }

        // Build separate queries for each filter option
        // Division options - based on other filters (exclude divisionFilter)
        $divisionQuery = Procurement::query();
        if (!empty($this->clusterCommitteeFilter)) {
            $divisionQuery->where('cluster_committees_id', $this->clusterCommitteeFilter);
        }
        if (!empty($this->endUserFilter)) {
            $divisionQuery->where('end_users_id', $this->endUserFilter);
        }
        if (!empty($this->fundSourceFilter)) {
            $divisionQuery->where('fund_source_id', $this->fundSourceFilter);
        }
        if (!empty($this->remarkFilter)) {
            $divisionQuery->where(function ($q) {
                $q->whereHas('currentLotRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                })->orWhereHas('pr_items.currentItemRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                });
            });
        }
        if ($this->earlyProcurementFilter !== '') {
            $divisionQuery->where('early_procurement', $this->earlyProcurementFilter);
        }
        $divisionIds = $divisionQuery->distinct()->pluck('divisions_id')->filter();
        $this->divisions = Division::whereIn('id', $divisionIds)->orderBy('abbreviation')->get();

        // Cluster options - based on other filters (exclude clusterCommitteeFilter)
        $clusterQuery = Procurement::query();
        if (!empty($this->divisionFilter)) {
            $clusterQuery->where('divisions_id', $this->divisionFilter);
        }
        if (!empty($this->endUserFilter)) {
            $clusterQuery->where('end_users_id', $this->endUserFilter);
        }
        if (!empty($this->fundSourceFilter)) {
            $clusterQuery->where('fund_source_id', $this->fundSourceFilter);
        }
        if (!empty($this->remarkFilter)) {
            $clusterQuery->where(function ($q) {
                $q->whereHas('currentLotRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                })->orWhereHas('pr_items.currentItemRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                });
            });
        }
        if ($this->earlyProcurementFilter !== '') {
            $clusterQuery->where('early_procurement', $this->earlyProcurementFilter);
        }
        $clusterIds = $clusterQuery->distinct()->pluck('cluster_committees_id')->filter();
        $this->clusterCommittees = ClusterCommittee::whereIn('id', $clusterIds)->orderBy('clustercommittee')->get();

        // End User options - based on other filters (exclude endUserFilter)
        $endUserQuery = Procurement::query();
        if (!empty($this->divisionFilter)) {
            $endUserQuery->where('divisions_id', $this->divisionFilter);
        }
        if (!empty($this->clusterCommitteeFilter)) {
            $endUserQuery->where('cluster_committees_id', $this->clusterCommitteeFilter);
        }
        if (!empty($this->fundSourceFilter)) {
            $endUserQuery->where('fund_source_id', $this->fundSourceFilter);
        }
        if (!empty($this->remarkFilter)) {
            $endUserQuery->where(function ($q) {
                $q->whereHas('currentLotRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                })->orWhereHas('pr_items.currentItemRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                });
            });
        }
        if ($this->earlyProcurementFilter !== '') {
            $endUserQuery->where('early_procurement', $this->earlyProcurementFilter);
        }
        $endUserIds = $endUserQuery->distinct()->pluck('end_users_id')->filter();
        $this->endUsers = EndUser::whereIn('id', $endUserIds)->orderBy('endusers')->get();

        // Fund Source options - based on other filters (exclude fundSourceFilter)
        $fundQuery = Procurement::query();
        if (!empty($this->divisionFilter)) {
            $fundQuery->where('divisions_id', $this->divisionFilter);
        }
        if (!empty($this->clusterCommitteeFilter)) {
            $fundQuery->where('cluster_committees_id', $this->clusterCommitteeFilter);
        }
        if (!empty($this->endUserFilter)) {
            $fundQuery->where('end_users_id', $this->endUserFilter);
        }
        if (!empty($this->remarkFilter)) {
            $fundQuery->where(function ($q) {
                $q->whereHas('currentLotRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                })->orWhereHas('pr_items.currentItemRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                });
            });
        }
        if ($this->earlyProcurementFilter !== '') {
            $fundQuery->where('early_procurement', $this->earlyProcurementFilter);
        }
        $fundIds = $fundQuery->distinct()->pluck('fund_source_id')->filter();
        $this->fundSources = FundSource::whereIn('id', $fundIds)->orderBy('fundsources')->get();

        // Remark options - based on other filters (exclude remarkFilter)
        $remarkQuery = Procurement::query();
        if (!empty($this->divisionFilter)) {
            $remarkQuery->where('divisions_id', $this->divisionFilter);
        }
        if (!empty($this->clusterCommitteeFilter)) {
            $remarkQuery->where('cluster_committees_id', $this->clusterCommitteeFilter);
        }
        if (!empty($this->endUserFilter)) {
            $remarkQuery->where('end_users_id', $this->endUserFilter);
        }
        if (!empty($this->fundSourceFilter)) {
            $remarkQuery->where('fund_source_id', $this->fundSourceFilter);
        }
        if ($this->earlyProcurementFilter !== '') {
            $remarkQuery->where('early_procurement', $this->earlyProcurementFilter);
        }

        // Get remark IDs from both lot and item remarks
        $lotRemarkIds = $remarkQuery->clone()
            ->whereHas('currentLotRemark')
            ->with('currentLotRemark')
            ->get()
            ->pluck('currentLotRemark.remarks_id')
            ->filter();

        $itemRemarkIds = $remarkQuery->clone()
            ->whereHas('pr_items.currentItemRemark')
            ->with('pr_items.currentItemRemark')
            ->get()
            ->flatMap(function ($procurement) {
                return $procurement->pr_items->pluck('currentItemRemark.remarks_id');
            })
            ->filter();

        $remarkIds = $lotRemarkIds->merge($itemRemarkIds)->unique();
        $this->remarks = Remarks::whereIn('id', $remarkIds)->orderBy('remarks')->get();
    }

    /**
     * Toggle expanded/collapsed state for procurement items
     */
    public function toggle($property, $value)
    {
        $value = (string) $value;

        if ($this->$property === $value) {
            $this->$property = null;
        } else {
            $this->$property = $value;
        }
    }

    /**
     * Show the early procurement prompt modal.
     */
    public function promptEarlyProcurement()
    {
        $this->showEarlyPrompt = true;
    }

    /**
     * Handle early procurement confirmation and redirect to create page.
     */
    public function confirmEarly($isEarly)
    {
        $this->early = $isEarly;
        $this->showEarlyPrompt = false;

        return redirect()->route('procurements.create', ['early' => $isEarly ? 1 : 0]);
    }

    /**
     * Select a procurement from the modal.
     */
    public function selectProcurement($procurementId)
    {
        $this->selectedProcurement = Procurement::find($procurementId);
        $this->showModal = false;

        $this->form = $this->selectedProcurement->toArray();
    }

    /**
     * Reset pagination when search or filters change.
     */
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

    public function updatedEarlyProcurementFilter() // Add this method
    {
        $this->resetPage();
        $this->loadFilterOptions();
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->search = '';
        $this->divisionFilter = '';
        $this->clusterCommitteeFilter = '';
        $this->endUserFilter = '';
        $this->fundSourceFilter = '';
        $this->remarkFilter = '';
        $this->earlyProcurementFilter = ''; // Add this
        $this->resetPage();
        $this->loadFilterOptions(); // Reload all options
    }

    public function render()
    {
        $query = Procurement::query()
            ->with([
                'currentPrStage.procurementStage',
                'division',
                'clusterCommittee',
                'endUser',
                'fundSource',
                'currentLotRemark.remark',
                'pr_items' => function ($query) {
                    $query->with(['prstage.stage', 'currentItemRemark.remark']);
                }
            ])
            ->latest();

        // Apply search filter
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', $searchTerm)
                    ->orWhere('procurement_program_project', 'like', $searchTerm);
            });
        }

        // Apply division filter
        if (!empty($this->divisionFilter)) {
            $query->where('divisions_id', $this->divisionFilter);
        }

        // Apply cluster/committee filter
        if (!empty($this->clusterCommitteeFilter)) {
            $query->where('cluster_committees_id', $this->clusterCommitteeFilter);
        }

        // Apply end user filter
        if (!empty($this->endUserFilter)) {
            $query->where('end_users_id', $this->endUserFilter);
        }

        // Apply fund source filter
        if (!empty($this->fundSourceFilter)) {
            $query->where('fund_source_id', $this->fundSourceFilter);
        }

        // Apply remark filter
        if (!empty($this->remarkFilter)) {
            $query->where(function ($q) {
                // Filter for perLot procurements
                $q->whereHas('currentLotRemark', function ($subQ) {
                    $subQ->where('remarks_id', $this->remarkFilter);
                })
                    // OR filter for perItem procurements
                    ->orWhereHas('pr_items.currentItemRemark', function ($subQ) {
                        $subQ->where('remarks_id', $this->remarkFilter);
                    });
            });
        }

        // Apply early procurement filter
        if ($this->earlyProcurementFilter !== '') {
            $query->where('early_procurement', $this->earlyProcurementFilter);
        }

        $procurements = $query->paginate($this->perPage);

        return view('livewire.procurements.procurement-index-page', [
            'procurements' => $procurements,
        ]);
    }
}
