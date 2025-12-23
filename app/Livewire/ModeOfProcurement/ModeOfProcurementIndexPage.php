<?php

namespace App\Livewire\ModeOfProcurement;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\BacType;
use App\Models\MopGroup;
use App\Models\Procurement;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\MopItem;
use App\Models\MopLot;
use App\Models\BidSchedule;
use App\Models\NtfBidSchedule;
use App\Models\PrSvp;
use Livewire\WithPagination;

class ModeOfProcurementIndexPage extends Component
{
    use WithPagination;

    // Pagination
    public $perPage = 10;
    public $itemsPerPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'bacCategoryFilter' => ['except' => null],
        'perPage' => ['except' => 10],
    ];
    protected $paginationTheme = 'tailwind';

    // Search
    public $search = '';

    // Filters
    public $bacCategoryFilter = null;

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

    public function updatingBacCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Get the current mode and status for a per-item procurement
     */
    private function getItemModeAndStatus($item)
    {
        $prItemID = $item->prItemID;

        // Get the latest MOP item for this PR item
        $latestMop = MopItem::where('prItemID', $prItemID)
            ->with('modeOfProcurement')
            ->orderBy('mode_order', 'desc')
            ->first();

        if (!$latestMop) {
            return ['mode' => null, 'status' => null];
        }

        $status = null;
        $modeId = $latestMop->mode_of_procurement_id;

        // Mode 1 - No bidding schedule needed
        if ($modeId == 1) {
            return [
                'mode' => $latestMop->modeOfProcurement,
                'status' => null
            ];
        }

        // Check for bidding result based on mode (2, 3, 4)
        if (in_array($modeId, [2, 3, 4])) {
            $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
                ->where('ref_id', $prItemID)
                ->first();

            if ($bidSchedule && $bidSchedule->bidding_result) {
                $status = $bidSchedule->bidding_result;
            }
        }

        // Check NTF result for mode 4
        if ($modeId == 4) {
            $ntfSchedule = NtfBidSchedule::where('mop_uid', $latestMop->uid)
                ->where('ref_id', $prItemID)
                ->first();

            if ($ntfSchedule && $ntfSchedule->ntf_bidding_result) {
                $status = $ntfSchedule->ntf_bidding_result;
            }
        }

        // Check SVP resolution for mode 5
        if ($modeId == 5) {
            $prSvp = PrSvp::where('mop_uid', $latestMop->uid)
                ->where('ref_id', $prItemID)
                ->first();

            if ($prSvp && $prSvp->resolution_number) {
                $status = 'COMPLETED';
            }
        }

        return [
            'mode' => $latestMop->modeOfProcurement,
            'status' => $status
        ];
    }

    /**
     * Get the current mode of procurement and status for a procurement
     */
    private function getCurrentModeAndStatus($procurement)
    {
        if ($procurement->procurement_type === 'perLot') {
            // Get the latest MOP lot
            $latestMop = $procurement->mopLots()
                ->with('modeOfProcurement')
                ->orderBy('mode_order', 'desc')
                ->first();

            if (!$latestMop) {
                return ['mode' => null, 'status' => null];
            }

            $status = null;

            // Mode 1 - No bidding schedule needed, just return the mode
            if ($latestMop->mode_of_procurement_id == 1) {
                return [
                    'mode' => $latestMop->modeOfProcurement,
                    'status' => null
                ];
            }

            // Check for bidding result based on mode (2, 3, 4)
            if (in_array($latestMop->mode_of_procurement_id, [2, 3, 4])) {
                $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
                    ->where('ref_id', $procurement->procID)
                    ->first();

                if ($bidSchedule && $bidSchedule->bidding_result) {
                    $status = $bidSchedule->bidding_result;
                }
            }

            // Check NTF result for mode 4
            if ($latestMop->mode_of_procurement_id == 4) {
                $ntfSchedule = NtfBidSchedule::where('mop_uid', $latestMop->uid)
                    ->where('ref_id', $procurement->procID)
                    ->first();

                if ($ntfSchedule && $ntfSchedule->ntf_bidding_result) {
                    $status = $ntfSchedule->ntf_bidding_result;
                }
            }

            // Check SVP resolution for mode 5
            if ($latestMop->mode_of_procurement_id == 5) {
                $prSvp = PrSvp::where('mop_uid', $latestMop->uid)
                    ->where('ref_id', $procurement->procID)
                    ->first();

                if ($prSvp && $prSvp->resolution_number) {
                    $status = 'COMPLETED';
                }
            }

            return [
                'mode' => $latestMop->modeOfProcurement,
                'status' => $status
            ];
        } else {
            // For perItem, we can't determine a single mode
            return ['mode' => null, 'status' => 'Multiple'];
        }
    }

    public function render()
    {
        $query = Procurement::query()
            ->with([
                'currentPrStage.procurementStage',
                'mopLots.modeOfProcurement',
                'pr_items',
                'category.bacType'
            ])
            ->latest();

        // Search filter
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', $searchTerm)
                    ->orWhere('procurement_program_project', 'like', $searchTerm);
            });
        }

        // BAC Category filter
        if ($this->bacCategoryFilter) {
            $query->whereHas('category', function ($q) {
                $q->where('bac_type_id', $this->bacCategoryFilter);
            });
        }

        $procurements = $query->paginate($this->perPage);

        // Add current mode and status to each procurement
        foreach ($procurements as $procurement) {
            $modeStatus = $this->getCurrentModeAndStatus($procurement);
            $procurement->currentMode = $modeStatus['mode'];
            $procurement->currentStatus = $modeStatus['status'];

            // For per-item procurements, add mode and status to each item
            if ($procurement->procurement_type === 'perItem') {
                foreach ($procurement->pr_items as $item) {
                    $itemModeStatus = $this->getItemModeAndStatus($item);
                    $item->currentMode = $itemModeStatus['mode'];
                    $item->currentStatus = $itemModeStatus['status'];
                }
            }
        }

        // Get BAC Categories for filter (ordered by name)
        $bacCategories = BacType::orderBy('name', 'asc')->get();

        return view('livewire.mode-of-procurement.mode-of-procurement-index-page', [
            'procurements' => $procurements,
            'bacCategories' => $bacCategories,
        ]);
    }
}
