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

    // Bulk Edit
    public $selectedItems = [];
    public $selectAll = false;

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

    public function updatedSelectAll($value)
    {
        if ($value) {
            // Build the same query as in render()
            $query = Procurement::query()
                ->with([
                    'currentPrStage.procurementStage',
                    'mopLots.modeOfProcurement',
                    'pr_items',
                    'category.bacType'
                ])
                ->latest();

            // Apply same filters as render()
            if (!empty($this->search)) {
                $searchTerm = '%' . $this->search . '%';
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('pr_number', 'like', $searchTerm)
                        ->orWhere('procurement_program_project', 'like', $searchTerm)
                        // Search by IB Number for perLot procurements (current mode only)
                        ->orWhere(function ($subQ) use ($searchTerm) {
                            $subQ->where('procurement_type', 'perLot')
                                ->whereExists(function ($exists) use ($searchTerm) {
                                    $exists->select(\DB::raw(1))
                                        ->from('mop_lot')
                                        ->whereColumn('mop_lot.procID', 'procurements.procID')
                                        ->whereNotExists(function ($notExists) {
                                            $notExists->select(\DB::raw(1))
                                                ->from('mop_lot as ml2')
                                                ->whereColumn('ml2.procID', 'mop_lot.procID')
                                                ->whereColumn('ml2.mode_order', '>', 'mop_lot.mode_order');
                                        })
                                        ->whereExists(function ($bidExists) use ($searchTerm) {
                                            $bidExists->select(\DB::raw(1))
                                                ->from('bid_schedules')
                                                ->whereColumn('bid_schedules.ref_id', 'mop_lot.procID')
                                                ->whereColumn('bid_schedules.mop_uid', 'mop_lot.uid')
                                                ->where('bid_schedules.ib_number', 'like', $searchTerm);
                                        });
                                });
                        })
                        // Search by IB Number for perItem procurements (current mode only)
                        ->orWhere(function ($subQ) use ($searchTerm) {
                            $subQ->where('procurement_type', 'perItem')
                                ->whereExists(function ($exists) use ($searchTerm) {
                                    $exists->select(\DB::raw(1))
                                        ->from('pr_items')
                                        ->whereColumn('pr_items.procID', 'procurements.procID')
                                        ->whereExists(function ($mopExists) use ($searchTerm) {
                                            $mopExists->select(\DB::raw(1))
                                                ->from('mop_item')
                                                ->whereColumn('mop_item.prItemID', 'pr_items.prItemID')
                                                ->whereNotExists(function ($notExists) {
                                                    $notExists->select(\DB::raw(1))
                                                        ->from('mop_item as mi2')
                                                        ->whereColumn('mi2.prItemID', 'mop_item.prItemID')
                                                        ->whereColumn('mi2.mode_order', '>', 'mop_item.mode_order');
                                                })
                                                ->whereExists(function ($bidExists) use ($searchTerm) {
                                                    $bidExists->select(\DB::raw(1))
                                                        ->from('bid_schedules')
                                                        ->whereColumn('bid_schedules.ref_id', 'mop_item.prItemID')
                                                        ->whereColumn('bid_schedules.mop_uid', 'mop_item.uid')
                                                        ->where('bid_schedules.ib_number', 'like', $searchTerm);
                                                });
                                        });
                                });
                        });
                });
            }

            if ($this->bacCategoryFilter) {
                $query->whereHas('category', function ($q) {
                    $q->where('bac_type_id', $this->bacCategoryFilter);
                });
            }

            if ($this->categoryFilter) {
                $query->where('category_id', $this->categoryFilter);
            }

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

            if ($this->ibNumberFilter) {
                $query->where(function ($q) {
                    // For perLot procurements - match by procID and current mode
                    $q->where(function ($subQ) {
                        $subQ->where('procurement_type', 'perLot')
                            ->whereExists(function ($exists) {
                                $exists->select(\DB::raw(1))
                                    ->from('mop_lot')
                                    ->whereColumn('mop_lot.procID', 'procurements.procID')
                                    ->whereNotExists(function ($notExists) {
                                        $notExists->select(\DB::raw(1))
                                            ->from('mop_lot as ml2')
                                            ->whereColumn('ml2.procID', 'mop_lot.procID')
                                            ->whereColumn('ml2.mode_order', '>', 'mop_lot.mode_order');
                                    })
                                    ->whereExists(function ($bidExists) {
                                        $bidExists->select(\DB::raw(1))
                                            ->from('bid_schedules')
                                            ->whereColumn('bid_schedules.ref_id', 'mop_lot.procID')
                                            ->whereColumn('bid_schedules.mop_uid', 'mop_lot.uid')
                                            ->where('bid_schedules.ib_number', $this->ibNumberFilter);
                                    });
                            });
                    })
                        // For perItem procurements - match through pr_items and current mode
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
                                                ->whereNotExists(function ($notExists) {
                                                    $notExists->select(\DB::raw(1))
                                                        ->from('mop_item as mi2')
                                                        ->whereColumn('mi2.prItemID', 'mop_item.prItemID')
                                                        ->whereColumn('mi2.mode_order', '>', 'mop_item.mode_order');
                                                })
                                                ->whereExists(function ($bidExists) {
                                                    $bidExists->select(\DB::raw(1))
                                                        ->from('bid_schedules')
                                                        ->whereColumn('bid_schedules.ref_id', 'mop_item.prItemID')
                                                        ->whereColumn('bid_schedules.mop_uid', 'mop_item.uid')
                                                        ->where('bid_schedules.ib_number', $this->ibNumberFilter);
                                                });
                                        });
                                });
                        });
                });
            }

            // Only select perLot procurements on the current page
            $procurements = $query->paginate($this->perPage);
            $this->selectedItems = $procurements
                ->filter(fn($p) => $p->procurement_type === 'perLot')
                ->pluck('procID')
                ->toArray();
        } else {
            $this->selectedItems = [];
        }
    }

    public function bulkEdit()
    {
        if (empty($this->selectedItems)) {
            LivewireAlert::title('No Items Selected')
                ->warning()
                ->text('Please select at least one procurement to edit.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Get all selected procurements with their current modes and schedules
        $procurements = Procurement::whereIn('procID', $this->selectedItems)
            ->with(['mopLots'])
            ->get();

        $modeGroups = [];
        $scheduleData = [];

        foreach ($procurements as $procurement) {
            // Get the latest mode for this procurement
            $latestMop = $procurement->mopLots()
                ->orderBy('mode_order', 'desc')
                ->first();

            $modeId = $latestMop?->mode_of_procurement_id;

            if (!isset($modeGroups[$modeId])) {
                $modeGroups[$modeId] = [];
            }

            $modeGroups[$modeId][] = $procurement->pr_number;

            // Get schedule data for this procurement - ALWAYS add to array
            $schedule = [];

            if ($latestMop) {
                if (in_array($modeId, [2, 3, 4, 5, 6])) {
                    $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
                        ->where('ref_id', $procurement->procID)
                        ->first();

                    if ($bidSchedule) {
                        $schedule = [
                            'bidding_number' => $bidSchedule->bidding_number,
                            'ib_number' => $bidSchedule->ib_number,
                            'philgeps_posting_ref_no' => $bidSchedule->philgeps_posting_ref_no,
                            'ads_post_ib' => $bidSchedule->ads_post_ib,
                            'pre_proc_conference' => $bidSchedule->pre_proc_conference,
                            'list_invited_observers' => $bidSchedule->list_invited_observers,
                            'obsrvr_prebid_conf' => $bidSchedule->obsrvr_prebid_conf,
                            'obsrvr_eligibility' => $bidSchedule->obsrvr_eligibility,
                            'obsrvr_sub_open_of_bid' => $bidSchedule->obsrvr_sub_open_of_bid,
                            'obsrvr_bid' => $bidSchedule->obsrvr_bid,
                            'obsrvr_post_qual' => $bidSchedule->obsrvr_post_qual,
                            'pre_bid_conf' => $bidSchedule->pre_bid_conf,
                            'eligibility_check' => $bidSchedule->eligibility_check,
                            'sub_open_bids' => $bidSchedule->sub_open_bids,
                            'bid_evaluation_date' => $bidSchedule->bid_evaluation_date,
                            'post_qualification_date' => $bidSchedule->post_qualification_date,
                            'bidding_result' => $bidSchedule->bidding_result,
                            'resolution_number_mop' => $bidSchedule->resolution_number_mop,
                        ];
                    }
                } elseif (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                    $prSvp = PrSvp::where('mop_uid', $latestMop->uid)
                        ->where('ref_id', $procurement->procID)
                        ->first();

                    if ($prSvp) {
                        $schedule = [
                            'resolution_number' => $prSvp->resolution_number,
                            'rfq_no' => $prSvp->rfq_no,
                            'canvass_date' => $prSvp->canvass_date,
                            'date_returned_of_canvass' => $prSvp->date_returned_of_canvass,
                            'abstract_of_canvass_date' => $prSvp->abstract_of_canvass_date,
                        ];
                    }
                }
            }

            // Always add schedule data for all procurements
            $scheduleData[$procurement->procID] = $schedule;
        }

        // Check if any PR has no mode
        if (isset($modeGroups[null])) {
            LivewireAlert::title('Mode Required')
                ->warning()
                ->text('Please select PRs with a mode of procurement')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Check if all PRs have the same mode
        if (count($modeGroups) > 1) {
            LivewireAlert::title('Mode Mismatch')
                ->warning()
                ->text('All selected PRs must have the same mode of procurement')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Check if all PRs have identical schedule data
        $scheduleHashes = [];
        $prNumbersByHash = [];

        foreach ($procurements as $procurement) {
            $hash = md5(json_encode($scheduleData[$procurement->procID]));
            $scheduleHashes[$procurement->procID] = $hash;

            if (!isset($prNumbersByHash[$hash])) {
                $prNumbersByHash[$hash] = [];
            }
            $prNumbersByHash[$hash][] = $procurement->pr_number;
        }

        $uniqueHashes = array_unique($scheduleHashes);

        if (count($uniqueHashes) > 1) {
            // Find the minority group (PRs with different data)
            $hashCounts = array_count_values($scheduleHashes);
            arsort($hashCounts);
            $majorityHash = array_key_first($hashCounts);

            $differentPRs = [];
            foreach ($scheduleHashes as $procID => $hash) {
                if ($hash !== $majorityHash) {
                    $pr = $procurements->firstWhere('procID', $procID);
                    if ($pr) {
                        $differentPRs[] = $pr->pr_number;
                    }
                }
            }

            $prList = implode(', ', $differentPRs);

            LivewireAlert::title('Field Mismatch')
                ->warning()
                ->text("PR {$prList} " . (count($differentPRs) > 1 ? 'have' : 'has') . " different field values from the others")
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Check if all PRs have the same ABC threshold (< 200K or >= 200K)
        $hasBelow200k = false;
        $hasAbove200k = false;

        foreach ($procurements as $procurement) {
            $abcAmount = $procurement->abc ?? 0;

            if ($abcAmount < 200000) {
                $hasBelow200k = true;
            } else {
                $hasAbove200k = true;
            }

            // Early exit if mismatch found
            if ($hasBelow200k && $hasAbove200k) {
                LivewireAlert::title('ABC Threshold Mismatch')
                    ->warning()
                    ->text('All selected PRs must have the same ABC threshold (below ₱200K or ₱200K and above)')
                    ->toast()
                    ->position('top-end')
                    ->show();
                return;
            }
        }

        return $this->redirect(route('mode-of-procurement.bulk-edit', ['items' => $this->selectedItems]));
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
        $ibNumbers = BidSchedule::select('ib_number')
            ->whereNotNull('ib_number')
            ->where('ib_number', '!=', '')
            ->distinct()
            ->get()
            ->sortBy(function ($item) {
                // Natural sorting: extract numbers from IB format (e.g., "IB-123")
                preg_match('/\d+/', $item->ib_number, $matches);
                return isset($matches[0]) ? (int) $matches[0] : 0;
            })
            ->values()
            ->map(function ($item) {
                return [
                    'id' => $item->ib_number,
                    'name' => $item->ib_number
                ];
            });

        return $ibNumbers;
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
                    ->orWhere('procurement_program_project', 'like', $searchTerm)
                    // Search by IB Number for perLot procurements (current mode only)
                    ->orWhere(function ($subQ) use ($searchTerm) {
                        $subQ->where('procurement_type', 'perLot')
                            ->whereExists(function ($exists) use ($searchTerm) {
                                $exists->select(\DB::raw(1))
                                    ->from('mop_lot')
                                    ->whereColumn('mop_lot.procID', 'procurements.procID')
                                    ->whereNotExists(function ($notExists) {
                                        $notExists->select(\DB::raw(1))
                                            ->from('mop_lot as ml2')
                                            ->whereColumn('ml2.procID', 'mop_lot.procID')
                                            ->whereColumn('ml2.mode_order', '>', 'mop_lot.mode_order');
                                    })
                                    ->whereExists(function ($bidExists) use ($searchTerm) {
                                        $bidExists->select(\DB::raw(1))
                                            ->from('bid_schedules')
                                            ->whereColumn('bid_schedules.ref_id', 'mop_lot.procID')
                                            ->whereColumn('bid_schedules.mop_uid', 'mop_lot.uid')
                                            ->where('bid_schedules.ib_number', 'like', $searchTerm);
                                    });
                            });
                    })
                    // Search by IB Number for perItem procurements (current mode only)
                    ->orWhere(function ($subQ) use ($searchTerm) {
                        $subQ->where('procurement_type', 'perItem')
                            ->whereExists(function ($exists) use ($searchTerm) {
                                $exists->select(\DB::raw(1))
                                    ->from('pr_items')
                                    ->whereColumn('pr_items.procID', 'procurements.procID')
                                    ->whereExists(function ($mopExists) use ($searchTerm) {
                                        $mopExists->select(\DB::raw(1))
                                            ->from('mop_item')
                                            ->whereColumn('mop_item.prItemID', 'pr_items.prItemID')
                                            ->whereNotExists(function ($notExists) {
                                                $notExists->select(\DB::raw(1))
                                                    ->from('mop_item as mi2')
                                                    ->whereColumn('mi2.prItemID', 'mop_item.prItemID')
                                                    ->whereColumn('mi2.mode_order', '>', 'mop_item.mode_order');
                                            })
                                            ->whereExists(function ($bidExists) use ($searchTerm) {
                                                $bidExists->select(\DB::raw(1))
                                                    ->from('bid_schedules')
                                                    ->whereColumn('bid_schedules.ref_id', 'mop_item.prItemID')
                                                    ->whereColumn('bid_schedules.mop_uid', 'mop_item.uid')
                                                    ->where('bid_schedules.ib_number', 'like', $searchTerm);
                                            });
                                    });
                            });
                    });
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

        // IB Number filter (current mode only)
        if ($this->ibNumberFilter) {
            $query->where(function ($q) {
                // For perLot procurements - match by procID and current mode
                $q->where(function ($subQ) {
                    $subQ->where('procurement_type', 'perLot')
                        ->whereExists(function ($exists) {
                            $exists->select(\DB::raw(1))
                                ->from('mop_lot')
                                ->whereColumn('mop_lot.procID', 'procurements.procID')
                                ->whereNotExists(function ($notExists) {
                                    $notExists->select(\DB::raw(1))
                                        ->from('mop_lot as ml2')
                                        ->whereColumn('ml2.procID', 'mop_lot.procID')
                                        ->whereColumn('ml2.mode_order', '>', 'mop_lot.mode_order');
                                })
                                ->whereExists(function ($bidExists) {
                                    $bidExists->select(\DB::raw(1))
                                        ->from('bid_schedules')
                                        ->whereColumn('bid_schedules.ref_id', 'mop_lot.procID')
                                        ->whereColumn('bid_schedules.mop_uid', 'mop_lot.uid')
                                        ->where('bid_schedules.ib_number', $this->ibNumberFilter);
                                });
                        });
                })
                    // For perItem procurements - match through pr_items and current mode
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
                                            ->whereNotExists(function ($notExists) {
                                                $notExists->select(\DB::raw(1))
                                                    ->from('mop_item as mi2')
                                                    ->whereColumn('mi2.prItemID', 'mop_item.prItemID')
                                                    ->whereColumn('mi2.mode_order', '>', 'mop_item.mode_order');
                                            })
                                            ->whereExists(function ($bidExists) {
                                                $bidExists->select(\DB::raw(1))
                                                    ->from('bid_schedules')
                                                    ->whereColumn('bid_schedules.ref_id', 'mop_item.prItemID')
                                                    ->whereColumn('bid_schedules.mop_uid', 'mop_item.uid')
                                                    ->where('bid_schedules.ib_number', $this->ibNumberFilter);
                                            });
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
