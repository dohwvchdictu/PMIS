<?php

namespace App\Livewire\Procurements;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Procurement;

class ProcurementIndexPage extends Component
{
    use WithPagination;

    // Pagination
    public $perPage = 10;
    public $itemsPerPage = 10; // Add this for items pagination

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];
    protected $paginationTheme = 'tailwind';

    // Search
    public $search = '';

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
        // Convert to string for consistent comparison
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
     * Reset pagination when search term changes.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
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
                'pr_items' => function ($query) {
                    // Don't paginate here, we'll handle it in the blade
                    $query->with('prstage.stage');
                }
            ])
            ->latest();

        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', $searchTerm)
                    ->orWhere('procurement_program_project', 'like', $searchTerm);
            });
        }

        $procurements = $query->paginate($this->perPage);

        return view('livewire.procurements.procurement-index-page', [
            'procurements' => $procurements,
        ]);
    }
}
