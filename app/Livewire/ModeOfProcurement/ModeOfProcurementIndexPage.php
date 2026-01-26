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
    public $currentModeFilter = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'bacCategoryFilter' => ['except' => null],
        'categoryFilter' => ['except' => null],
        'ibNumberFilter' => ['except' => null],
        'currentModeFilter' => ['except' => null],
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

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingIbNumberFilter()
    {
        $this->resetPage();
    }

    public function updatingCurrentModeFilter()
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

    public function getModesProperty()
    {
        return \App\Models\ModeOfProcurement::orderBy('id', 'asc')
            ->get()
            ->map(function ($mode) {
                return [
                    'id' => $mode->id,
                    'name' => $mode->modeofprocurements
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

        // Current Mode filter
        if ($this->currentModeFilter) {
            $query->where(function ($q) {
                // For perLot procurements
                $q->where(function ($subQ) {
                    $subQ->where('procurement_type', 'perLot')
                        ->whereExists(function ($exists) {
                            $exists->select(\DB::raw(1))
                                ->from('mop_lot')
                                ->whereColumn('mop_lot.procID', 'procurements.procID')
                                ->where('mop_lot.mode_of_procurement_id', $this->currentModeFilter)
                                ->whereNotExists(function ($notExists) {
                                    $notExists->select(\DB::raw(1))
                                        ->from('mop_lot as ml2')
                                        ->whereColumn('ml2.procID', 'mop_lot.procID')
                                        ->whereColumn('ml2.mode_order', '>', 'mop_lot.mode_order');
                                });
                        });
                })
                    // For perItem procurements
                    ->orWhere(function ($subQ) {
                        $subQ->where('procurement_type', 'perItem')
                            ->whereExists(function ($exists) {
                                $exists->select(\DB::raw(1))
                                    ->from('pr_items')
                                    ->whereColumn('pr_items.procID', 'procurements.procID')
                                    ->whereExists(function ($mopExists) {
                                        $mopExists->select(\DB::raw(1))
                                            ->from('mop_item')
                                            ->whereColumn('mop_item.prItemID', 'pr_items.prItemID')
                                            ->where('mop_item.mode_of_procurement_id', $this->currentModeFilter)
                                            ->whereNotExists(function ($notExists) {
                                                $notExists->select(\DB::raw(1))
                                                    ->from('mop_item as mi2')
                                                    ->whereColumn('mi2.prItemID', 'mop_item.prItemID')
                                                    ->whereColumn('mi2.mode_order', '>', 'mop_item.mode_order');
                                            });
                                    });
                            });
                    });
            });
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
            'bacCategories' => $bacCategories,
            'allCategories' => $allCategories,
            'ibNumbers' => $this->ibNumbers,
            'modes' => $this->modes,
        ]);
    }
}
