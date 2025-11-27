<?php

namespace App\Livewire\Procurements;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Procurement;
use App\Models\Division;
use App\Models\ClusterCommittee;
use App\Models\EndUser;
use App\Models\FundSource;

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
    ];
    protected $paginationTheme = 'tailwind';

    // Search
    public $search = '';

    // Filters
    public $divisionFilter = '';
    public $clusterCommitteeFilter = '';
    public $endUserFilter = '';
    public $fundSourceFilter = '';

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

    public function mount()
    {
        // Load filter options
        $this->divisions = Division::orderBy('abbreviation')->get();
        $this->clusterCommittees = ClusterCommittee::orderBy('clustercommittee')->get();
        $this->endUsers = EndUser::orderBy('endusers')->get();
        $this->fundSources = FundSource::orderBy('fundsources')->get();

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

    public function updatingDivisionFilter()
    {
        $this->resetPage();
    }

    public function updatingClusterCommitteeFilter()
    {
        $this->resetPage();
    }

    public function updatingEndUserFilter()
    {
        $this->resetPage();
    }

    public function updatingFundSourceFilter()
    {
        $this->resetPage();
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
        $this->resetPage();
    }

    public function viewPdf(string $filepath): void
    {
        $url = asset('storage/' . $filepath);
        $this->dispatch('show-pdf-modal', url: $url);
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
                'pr_items' => function ($query) {
                    $query->with('prstage.stage');
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

        $procurements = $query->paginate($this->perPage);

        return view('livewire.procurements.procurement-index-page', [
            'procurements' => $procurements,
        ]);
    }
}
