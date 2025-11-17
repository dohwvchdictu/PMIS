<?php

namespace App\Livewire\ModeOfProcurement;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\MopGroup;
use App\Models\Procurement;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\MopItem; // Unused, but kept for context
use App\Models\MopLot; // Unused, but kept for context
use Livewire\WithPagination;

class ModeOfProcurementIndexPage extends Component
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

        return view('livewire.mode-of-procurement.mode-of-procurement-index-page', [
            'procurements' => $procurements,
        ]);
    }
}
