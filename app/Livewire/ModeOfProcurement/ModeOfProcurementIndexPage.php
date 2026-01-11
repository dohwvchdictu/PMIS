<?php

namespace App\Livewire\ModeOfProcurement;

use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\BacType;
use App\Models\Category;
use App\Models\MopGroup;
use App\Models\Procurement;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use App\Models\MopItem;
use App\Models\MopLot;
use App\Models\BidSchedule;
use App\Models\PrSvp;
use Livewire\WithPagination;

class ModeOfProcurementIndexPage extends Component
{
    use WithPagination;

    // Pagination
    public $perPage = 10;
    public $itemsPerPage = 10;
    public $ibNumberFilter = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'bacCategoryFilter' => ['except' => null],
        'categoryFilter' => ['except' => null],
        'ibNumberFilter' => ['except' => null],
        'perPage' => ['except' => 10],
    ];
    protected $paginationTheme = 'tailwind';

    // Search
    public $search = '';

    // Filters
    public $bacCategoryFilter = null;
    public $categoryFilter = null;

    // Modal
    public $showModal = false;
    public $selectedProcurement;

    // Early Procurement
    public $showEarlyPrompt = false;
    public $early = null;

    // Collapsible functionality
    public $expandedProcurementId = null;
    public $expandedGroupId = null;

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
     * Toggle group expansion
     */
    public function toggleGroup($groupId)
    {
        if ($this->expandedGroupId === $groupId) {
            $this->expandedGroupId = null;
        } else {
            $this->expandedGroupId = $groupId;
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

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingIbNumberFilter()
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

        $latestMop = MopItem::where('prItemID', $prItemID)
            ->with('modeOfProcurement')
            ->orderBy('mode_order', 'desc')
            ->first();

        if (!$latestMop) {
            return ['mode' => null, 'status' => null];
        }

        $status = null;
        $modeId = $latestMop->mode_of_procurement_id;

        // Mode 1 - No schedule needed
        if ($modeId == 1) {
            return [
                'mode' => $latestMop->modeOfProcurement,
                'status' => null
            ];
        }

        // Check bidding modes (2-6)
        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
                ->where('ref_id', $prItemID)
                ->first();

            if ($bidSchedule && $bidSchedule->bidding_result) {
                $status = $bidSchedule->bidding_result;
            }
        }

        // Check SVP modes (7-24)
        if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
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
            $latestMop = $procurement->mopLots()
                ->with('modeOfProcurement')
                ->orderBy('mode_order', 'desc')
                ->first();

            if (!$latestMop) {
                return ['mode' => null, 'status' => null];
            }

            $status = null;
            $modeId = $latestMop->mode_of_procurement_id;

            // Mode 1 - No schedule needed
            if ($modeId == 1) {
                return [
                    'mode' => $latestMop->modeOfProcurement,
                    'status' => null
                ];
            }

            // Check bidding modes (2-6)
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
                    ->where('ref_id', $procurement->procID)
                    ->first();

                if ($bidSchedule && $bidSchedule->bidding_result) {
                    $status = $bidSchedule->bidding_result;
                }
            }

            // Check SVP modes (7-24)
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
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
            return ['mode' => null, 'status' => 'Multiple'];
        }
    }

    /**
     * Check if procurement is groupable (per-lot with mode 2-6 and has IB number)
     */
    private function isGroupable($procurement)
    {
        if ($procurement->procurement_type !== 'perLot') {
            return false;
        }

        $latestMop = $procurement->mopLots()
            ->orderBy('mode_order', 'desc')
            ->first();

        if (!$latestMop) {
            return false;
        }

        $modeId = $latestMop->mode_of_procurement_id;

        // Only bidding modes (2-6) are groupable
        if (!in_array($modeId, [2, 3, 4, 5, 6])) {
            return false;
        }

        // Check if has IB number
        $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
            ->where('ref_id', $procurement->procID)
            ->first();

        return $bidSchedule && !empty($bidSchedule->ib_number);
    }

    /**
     * Get IB number for a procurement
     */
    private function getIBNumber($procurement)
    {
        $latestMop = $procurement->mopLots()
            ->orderBy('mode_order', 'desc')
            ->first();

        if (!$latestMop) {
            return null;
        }

        $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
            ->where('ref_id', $procurement->procID)
            ->first();

        return $bidSchedule ? $bidSchedule->ib_number : null;
    }

    /**
     * Process per-lot procurements and group by IB number
     */
    private function processPerLotGrouping($procurements)
    {
        $ibGroups = [];
        $ungrouped = [];

        foreach ($procurements as $procurement) {
            if ($this->isGroupable($procurement)) {
                $ibNumber = $this->getIBNumber($procurement);

                if ($ibNumber) {
                    if (!isset($ibGroups[$ibNumber])) {
                        $ibGroups[$ibNumber] = [
                            'ib_number' => $ibNumber,
                            'mode' => $procurement->currentMode,
                            'procurements' => [],
                            'total_abc' => 0,
                            'count' => 0,
                            'statuses' => [],
                        ];
                    }

                    $ibGroups[$ibNumber]['procurements'][] = $procurement;
                    $ibGroups[$ibNumber]['total_abc'] += $procurement->abc ?? 0;
                    $ibGroups[$ibNumber]['count']++;
                    $ibGroups[$ibNumber]['statuses'][] = $procurement->currentStatus;
                }
            } else {
                $ungrouped[] = $procurement;
            }
        }

        // Calculate overall status for each group
        foreach ($ibGroups as $key => $group) {
            $statuses = array_filter($group['statuses']);

            if (empty($statuses)) {
                $ibGroups[$key]['overall_status'] = 'PENDING';
            } elseif (count(array_unique($statuses)) === 1) {
                $ibGroups[$key]['overall_status'] = 'ALL_' . strtoupper($statuses[0]);
            } else {
                $ibGroups[$key]['overall_status'] = 'MIXED';
            }
        }

        return [
            'ib_groups' => $ibGroups,
            'ungrouped' => $ungrouped,
        ];
    }

    public function getIbNumbersProperty()
    {
        return BidSchedule::select('ib_number')
            ->whereNotNull('ib_number')
            ->where('ib_number', '!=', '')
            ->distinct()
            ->orderBy('ib_number', 'asc')
            ->get()
            ->map(function ($item) {
                return [
                    'id' => $item->ib_number,
                    'name' => $item->ib_number
                ];
            });
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

        // Filter out Mode ID = 1 for per-lot procurements
        $query->where(function ($q) {
            $q->where('procurement_type', 'perLot')
                ->whereHas('mopLots', function ($sub) {
                    $sub->where('mode_of_procurement_id', '!=', 1);
                });

            // Keep per-item for now (will handle later)
            $q->orWhere('procurement_type', 'perItem');
        });

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

        // Category filter
        if ($this->categoryFilter) {
            $query->where('category_id', $this->categoryFilter);
        }

        // IB Number filter
        if ($this->ibNumberFilter) {
            $query->where(function ($q) {
                // For perLot procurements - match by procID
                $q->where(function ($subQ) {
                    $subQ->where('procurement_type', 'perLot')
                        ->whereExists(function ($exists) {
                            $exists->select(\DB::raw(1))
                                ->from('bid_schedules')
                                ->whereColumn('bid_schedules.ref_id', 'procurements.procID')
                                ->where('bid_schedules.ib_number', $this->ibNumberFilter);
                        });
                })
                    // For perItem procurements - match through pr_items
                    ->orWhere(function ($subQ) {
                        $subQ->where('procurement_type', 'perItem')
                            ->whereExists(function ($exists) {
                                $exists->select(\DB::raw(1))
                                    ->from('pr_items')
                                    ->whereColumn('pr_items.procID', 'procurements.procID')
                                    ->whereExists(function ($bidExists) {
                                        $bidExists->select(\DB::raw(1))
                                            ->from('bid_schedules')
                                            ->whereColumn('bid_schedules.ref_id', 'pr_items.prItemID')
                                            ->where('bid_schedules.ib_number', $this->ibNumberFilter);
                                    });
                            });
                    });
            });

            // Auto-expand the filtered IB group
            if ($this->ibNumberFilter) {
                $this->expandedGroupId = $this->ibNumberFilter;
            }
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

        // Process grouping for per-lot procurements
        $groupedData = $this->processPerLotGrouping($procurements);

        // Get BAC Categories for filter
        $bacCategories = BacType::orderBy('name', 'asc')->get();

        // Get Categories for filter
        $categoriesQuery = Category::orderBy('category', 'asc');

        // If BAC Category filter is active, only show categories for that BAC type
        if ($this->bacCategoryFilter) {
            $categoriesQuery->where('bac_type_id', $this->bacCategoryFilter);
        }

        $allCategories = $categoriesQuery->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->category
            ];
        });

        return view('livewire.mode-of-procurement.mode-of-procurement-index-page', [
            'procurements' => $procurements,
            'groupedData' => $groupedData,
            'bacCategories' => $bacCategories,
            'allCategories' => $allCategories,
            'ibNumbers' => $this->ibNumbers,
        ]);
    }
}
