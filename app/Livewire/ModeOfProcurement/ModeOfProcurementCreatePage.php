<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\Mop;
use App\Models\MopGroup;
use App\Models\PostProcurement;
use App\Models\PrItem;
use App\Models\ProcurementStage;
use App\Models\Remarks;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Attributes\Title;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\ModeOfProcurement;
use App\Models\MopLot;
use App\Models\BidSchedule;
use App\Models\NtfBidSchedule;
use App\Models\PrSvp;
use Livewire\WithPagination;

#[Title('Create Mode of Procurement | PMIS')]
class ModeOfProcurementCreatePage extends Component
{
    use WithPagination;
    public $procurement = null;
    public ?string $ref_number = null;
    public string $procurementType = '';
    public int $activeTab = 1; // Default to Tab 1
    public array $selectedProcurements = [];
    public int $perPage = 10;
    public $selectedPRPage = 1;
    public ?int $mopGroupId = null;
    public $form = [
        'modes' => [],
    ];
    public array $selectedLots = [];
    public array $selectedItemGroups = [];
    protected $listeners = ['procurementsSelected'];
    public $isEditing = false;
    public bool $viewOnlyTab1 = false;
    public bool $viewOnlyTab2 = false;
    public bool $justAddedNewMode = false;
    public bool $justAddedNewSchedule = false;
    public bool $changesMade = false;

    public bool $viewOnlyTab3 = false;

    public function mount($ref_number = null)
    {
        $this->activeTab = (int) request()->query('tab', 1);

        if (session('alert')) {
            $alert = session('alert');
            LivewireAlert::title($alert['title'])
                ->{$alert['type']}()
                    ->text($alert['message'])
                    ->toast()
                    ->position('top-end')
                    ->show();
        }

        if ($this->mopGroupId || $this->procurementType) {
            return;
        }

        $this->ref_number = $ref_number;

        if ($this->ref_number) {
            session()->forget(['selected_procurements', 'form_state']);
            $this->resetForm();

            $mopGroup = MopGroup::with([
                'procurements.mops.modeDetails',
                'procurements.mops.bidSchedules',
                'procurements.mops.ntfBidSchedules',
                'procurements.mops.svpDetails',
                'prItems.procurement',
                'prItems.mops.modeDetails',
                'prItems.mops.bidSchedules',
                'prItems.mops.ntfBidSchedules',
                'prItems.mops.svpDetails',
            ])
                ->where('ref_number', $this->ref_number)
                ->firstOrFail();

            $this->mopGroupId = $mopGroup->id;
            $this->procurementType = $mopGroup->procurable_type;

            if ($this->procurementType === 'perLot') {
                $this->selectedProcurements = $mopGroup->procurements->map(function ($proc) {
                    return [
                        'id' => $proc->procID,
                        'pr_number' => $proc->pr_number,
                        'procurement_program_project' => $proc->procurement_program_project,
                        'items' => null,
                    ];
                })->toArray();
            } else {
                $this->selectedProcurements = $mopGroup->prItems
                    ->groupBy('procID')
                    ->map(function ($items) {
                        $proc = $items->first()->procurement;
                        if (!$proc)
                            return null;
                        return [
                            'id' => $proc->procID,
                            'pr_number' => $proc->pr_number,
                            'procurement_program_project' => $proc->procurement_program_project,
                            'items' => $items->map(function ($item) {
                                return [
                                    'id' => $item->prItemID,
                                    'description' => $item->description,
                                    'amount' => $item->amount,
                                ];
                            })->values()->all(),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray();
            }

            $firstItem = ($this->procurementType === 'perLot')
                ? $mopGroup->procurements->first()
                : $mopGroup->prItems->first();

            if ($firstItem && $firstItem->mops->isNotEmpty()) {
                $this->form['modes'] = $firstItem->mops
                    ->sortByDesc('mode_order')
                    ->map(function ($mop) {
                        $loadedSchedulesData = [];
                        $modeId = $mop->mode_of_procurement_id;

                        if ($modeId == 5) {
                            $svpDetail = $mop->svpDetails()->first();
                            if ($svpDetail) {
                                $loadedSchedulesData = [
                                    [
                                        'uid' => $svpDetail->uid ?? null,
                                        'resolution_number' => $svpDetail->resolution_number ?? '',
                                        'rfq_no' => $svpDetail->rfq_no ?? '',
                                        'canvass_date' => $svpDetail->canvass_date ?? null,
                                        'date_returned_of_canvass' => $svpDetail->date_returned_of_canvass ?? null,
                                        'abstract_of_canvass_date' => $svpDetail->abstract_of_canvass_date ?? null,
                                    ]
                                ];
                            }
                        } elseif ($modeId == 4) {
                            $ntfSchedulesCollection = $mop->ntfBidSchedules;
                            if ($ntfSchedulesCollection && $ntfSchedulesCollection->isNotEmpty()) {
                                $loadedSchedulesData = $ntfSchedulesCollection
                                    ->sortByDesc('bidding_number')
                                    ->map(function ($schedule) {
                                        return [
                                            'uid' => $schedule->uid ?? null,
                                            'bidding_number' => $schedule->bidding_number ?? 1,
                                            'ib_number' => $schedule->ib_number ?? '',
                                            'pre_proc_conference' => $schedule->pre_proc_conference ?? null,
                                            'ads_post_ib' => $schedule->ads_post_ib ?? null,
                                            'pre_bid_conf' => $schedule->pre_bid_conf ?? null,
                                            'eligibility_check' => $schedule->eligibility_check ?? null,
                                            'sub_open_bids' => $schedule->sub_open_bids ?? null,
                                            'ntf_no' => $schedule->ntf_no ?? '',
                                            'ntf_bidding_date' => $schedule->ntf_bidding_date ?? null,
                                            'ntf_bidding_result' => $schedule->ntf_bidding_result ?? '',
                                            'rfq_no' => $schedule->rfq_no ?? '',
                                            'canvass_date' => $schedule->canvass_date ?? null,
                                            'date_returned_of_canvass' => $schedule->date_returned_of_canvass ?? null,
                                            'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date ?? null,
                                        ];
                                    })->values()->all();
                            }
                        } else {
                            $bidSchedulesCollection = $mop->bidSchedules;
                            if ($bidSchedulesCollection && $bidSchedulesCollection->isNotEmpty()) {
                                $loadedSchedulesData = $bidSchedulesCollection
                                    ->sortByDesc('bidding_number')
                                    ->map(function ($schedule) {
                                        return [
                                            'uid' => $schedule->uid ?? null,
                                            'bidding_number' => $schedule->bidding_number ?? 1,
                                            'ib_number' => $schedule->ib_number ?? '',
                                            'pre_proc_conference' => $schedule->pre_proc_conference ?? null,
                                            'ads_post_ib' => $schedule->ads_post_ib ?? null,
                                            'pre_bid_conf' => $schedule->pre_bid_conf ?? null,
                                            'eligibility_check' => $schedule->eligibility_check ?? null,
                                            'sub_open_bids' => $schedule->sub_open_bids ?? null,
                                            'bidding_date' => $schedule->bidding_date ?? null,
                                            'bidding_result' => $schedule->bidding_result ?? '',
                                        ];
                                    })->values()->all();
                            }
                        }

                        return [
                            'id' => $mop->id,
                            'uid' => $mop->uid,
                            'mode_of_procurement_id' => $mop->mode_of_procurement_id,
                            'mode_order' => $mop->mode_order,
                            'bid_schedules' => $loadedSchedulesData,
                        ];
                    })->values()->all();
            } else {
                Log::warning("No initial MOP found for MopGroup ref: {$this->ref_number}, loading default.");
                $this->form['modes'] = [];
            }

            // Load Tab 3 data (Post Procurement)
            $postProcurement = PostProcurement::where('mop_group_ref', $this->ref_number)->first();

            if ($postProcurement) {
                $this->form['bidEvaluationDate'] = $postProcurement->bid_evaluation_date;
                $this->form['postQualDate'] = $postProcurement->post_qual_date;
                $this->form['resolutionNumber'] = $postProcurement->resolution_number;
                $this->form['recommendingForAward'] = $postProcurement->recommending_for_award;
                $this->form['noticeOfAward'] = $postProcurement->notice_of_award;
                $this->form['awardedAmount'] = $postProcurement->awarded_amount;
                $this->form['dateOfPostingOfAwardOnPhilGEPS'] = $postProcurement->date_of_posting_of_award_on_philgeps;
                $this->form['philgepsReferenceNo'] = $postProcurement->philgeps_reference_no;
                $this->form['awardNoticeNumber'] = $postProcurement->award_notice_no;
                $this->form['supplier_id'] = $postProcurement->supplier_id;
            }

        } else {
            $this->activeTab = 1;
            $this->procurementType = request()->query('type', 'perLot');

            if (session()->has('selected_procurements')) {
                $this->selectedProcurements = session('selected_procurements');
            }
            if (session()->has('form_state')) {
                $this->form = session('form_state', ['modes' => []]);
                session()->forget('form_state');
            } else {
                $this->resetForm();
            }
        }

        $this->ensureDefaultBidSchedules();
    }

    public function updatedFormModes($value, $key)
    {
        if (str_ends_with($key, 'mode_of_procurement_id')) {
            $index = explode('.', $key)[0] ?? null;

            if (is_numeric($index) && isset($this->form['modes'][$index])) {
                $newModeId = $this->form['modes'][$index]['mode_of_procurement_id'];

                // ✅ If changing TO a non-default mode (not Mode 1), create one empty schedule
                if ($newModeId && $newModeId != 1) {
                    $this->form['modes'][$index]['bid_schedules'] = [
                        [
                            'uid' => 'TEMP-' . uniqid(),
                            'bidding_number' => 1, // ✅ Set to 1
                            'ib_number' => '',
                            'pre_proc_conference' => null,
                            'ads_post_ib' => null,
                            'pre_bid_conf' => null,
                            'eligibility_check' => null,
                            'sub_open_bids' => null,
                            'bidding_date' => null,
                            'bidding_result' => '',
                            'ntf_no' => '',
                            'ntf_bidding_date' => null,
                            'ntf_bidding_result' => '',
                            'rfq_no' => '',
                            'canvass_date' => null,
                            'date_returned_of_canvass' => null,
                            'abstract_of_canvass_date' => null,
                            'resolution_number' => '',
                        ]
                    ];
                } else {
                    // ✅ If changing back to Mode 1 or null, clear schedules
                    $this->form['modes'][$index]['bid_schedules'] = [];
                }
            }
        }
    }

    private function ensureDefaultBidSchedules()
    {
        if (!isset($this->form['modes']) || !is_array($this->form['modes'])) {
            $this->form['modes'] = [];
        }

        // Define the one, complete, correct empty schedule template
        $emptyBidSchedule = [
            'uid' => 'TEMP-' . uniqid(),
            'bidding_number' => 1,
            'ib_number' => '',
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bidding_date' => null,
            'bidding_result' => '',
            'ntf_no' => '',
            'ntf_bidding_date' => null,
            'ntf_bidding_result' => '',
            'rfq_no' => '',
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
            'resolution_number' => '',
        ];

        foreach ($this->form['modes'] as $modeIndex => &$mode) {
            $modeId = $mode['mode_of_procurement_id'] ?? null;

            if (empty($mode['bid_schedules']) && $modeId && $modeId != 1) {
                $this->form['modes'][$modeIndex]['bid_schedules'] = [$emptyBidSchedule];
            }
        }
    }

    private function persistFormState(): void
    {
        session([
            'form_state' => $this->form,
            'selected_procurements' => $this->selectedProcurements,
        ]);
    }

    public function openSelectionModal()
    {
        $this->persistFormState();

        $existingLotIds = collect($this->selectedProcurements)
            ->filter(fn($proc) => empty($proc['items']))
            ->pluck('id')
            ->toArray();

        $existingItemIds = collect($this->selectedProcurements)
            ->filter(fn($proc) => !empty($proc['items']))
            ->flatMap(fn($proc) => collect($proc['items'])->pluck('id'))
            ->toArray();

        $this->dispatch('open-mode-modal', existingLotIds: $existingLotIds, existingItemIds: $existingItemIds);
    }

    public function procurementsSelected(array $selectedData): void
    {
        $this->selectedProcurements = $selectedData;
    }

    public function onProcurementSelected(array $selections): void
    {
        $this->selectedProcurements = $selections;
    }

    public function hydrateForm()
    {
        if (!$this->procurement) {
            return;
        }
    }

    public function getSelectedPRProperty()
    {
        $items = collect($this->selectedProcurements)
            ->flatMap(function ($proc) {
                if (!empty($proc['items'])) {
                    return collect($proc['items'])->map(function ($item) use ($proc) {
                        $item['pr_number'] = $proc['pr_number'];
                        $item['is_item'] = true;
                        $item['unique_key'] = 'item_' . $item['id'];
                        return $item;
                    });
                } else {
                    $proc['is_item'] = false;
                    $proc['unique_key'] = 'lot_' . $proc['id'];
                    return [$proc];
                }
            });

        return $this->paginateCollection($items, $this->perPage, 'selectedPRPage');
    }

    public function removeSelectedPR(string $uniqueKey): void
    {
        [$type, $id] = explode('_', $uniqueKey);
        $id = (int) $id;

        if ($type === 'lot') {
            $this->selectedProcurements = collect($this->selectedProcurements)
                ->filter(fn($proc) => !empty($proc['items']) || (empty($proc['items']) && $proc['id'] !== $id))
                ->values()
                ->all();
        } else {
            foreach ($this->selectedProcurements as $procIndex => &$proc) {
                if (!empty($proc['items'])) {
                    $proc['items'] = collect($proc['items'])
                        ->filter(fn($item) => $item['id'] !== $id)
                        ->values()
                        ->all();
                    if (empty($proc['items'])) {
                        unset($this->selectedProcurements[$procIndex]);
                    }
                }
            }
            $this->selectedProcurements = array_values($this->selectedProcurements);
        }

        if ($this->SelectedPR->isEmpty() && $this->selectedPRPage > 1) {
            $this->selectedPRPage--;
        }
    }

    private function paginateCollection($collection, $perPage, $pageName)
    {
        $page = $this->$pageName ?? 1;
        return new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => $pageName]
        );
    }

    public function nextCustomPage(string $pageName)
    {
        if (property_exists($this, $pageName)) {
            $this->$pageName++;
        }
    }

    public function previousCustomPage(string $pageName)
    {
        if (property_exists($this, $pageName) && $this->$pageName > 1) {
            $this->$pageName--;
        }
    }

    public function getShowAddModeButtonProperty()
    {
        // Check database state instead of form state
        if (empty($this->ref_number)) {
            return false;
        }

        // Get the latest mode from the database
        $latestModeFromDb = Mop::where('mop_group_ref', $this->ref_number)
            ->orderByDesc('mode_order')
            ->first();

        if (!$latestModeFromDb) {
            return false;
        }

        $modeId = $latestModeFromDb->mode_of_procurement_id;

        // Don't show button if latest mode is Mode 1
        if ($modeId == 1) {
            return false;
        }

        // Check if schedules exist AND have bidding results in the database
        if ($modeId == 4) {
            // For NTF mode, check if schedules exist AND all have ntf_bidding_result
            $schedulesExist = NtfBidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $latestModeFromDb->uid)
                ->exists();

            if (!$schedulesExist) {
                return false; // No schedules saved yet
            }

            $hasUnfinishedSchedule = NtfBidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $latestModeFromDb->uid)
                ->where(function ($query) {
                    $query->whereNull('ntf_bidding_result')
                        ->orWhere('ntf_bidding_result', '');
                })
                ->exists();

            return !$hasUnfinishedSchedule;
        } elseif ($modeId == 5) {
            // For SVP mode, check if schedule exists AND has required fields
            $scheduleExists = PrSvp::where('mop_group_ref', $this->ref_number)
                ->where('uid', $latestModeFromDb->uid)
                ->exists();

            if (!$scheduleExists) {
                return false; // No schedule saved yet
            }

            $hasUnfinishedSchedule = PrSvp::where('mop_group_ref', $this->ref_number)
                ->where('uid', $latestModeFromDb->uid)
                ->where(function ($query) {
                    $query->whereNull('resolution_number')
                        ->orWhere('resolution_number', '')
                        ->orWhereNull('rfq_no')
                        ->orWhere('rfq_no', '')
                        ->orWhereNull('canvass_date');
                })
                ->exists();

            return !$hasUnfinishedSchedule;
        } else {
            // For regular modes, check if schedules exist AND all have bidding_result
            $schedulesExist = BidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $latestModeFromDb->uid)
                ->exists();

            if (!$schedulesExist) {
                return false; // No schedules saved yet
            }

            $hasUnfinishedSchedule = BidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $latestModeFromDb->uid)
                ->where(function ($query) {
                    $query->whereNull('bidding_result')
                        ->orWhere('bidding_result', '');
                })
                ->exists();

            return !$hasUnfinishedSchedule;
        }
    }

    public function canShowAddBidButton($modeUid, $modeId)
    {
        if (empty($this->ref_number) || empty($modeUid)) {
            return false;
        }

        // Don't show button if the mode UID is temporary (not yet saved)
        if (str_starts_with($modeUid, 'TEMP-')) {
            return false;
        }

        // Get the latest mode from database
        $latestModeFromDb = Mop::where('mop_group_ref', $this->ref_number)
            ->orderByDesc('mode_order')
            ->first();

        if (!$latestModeFromDb || $latestModeFromDb->uid !== $modeUid) {
            return false; // Only show Add Bid for the latest mode
        }

        // Mode 2 can only have max 2 bids
        if ($modeId == 2) {
            $bidCount = BidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $modeUid)
                ->count();

            if ($bidCount >= 2) {
                return false;
            }
        }

        // Check if schedules exist AND have bidding results in the database
        if ($modeId == 4) {
            $schedulesExist = NtfBidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $modeUid)
                ->exists();

            if (!$schedulesExist) {
                return false;
            }

            $hasUnfinishedSchedule = NtfBidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $modeUid)
                ->where(function ($query) {
                    $query->whereNull('ntf_bidding_result')
                        ->orWhere('ntf_bidding_result', '');
                })
                ->exists();

            return !$hasUnfinishedSchedule;
        } elseif ($modeId == 5) {
            // SVP mode doesn't support multiple bids
            return false;
        } else {
            $schedulesExist = BidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $modeUid)
                ->exists();

            if (!$schedulesExist) {
                return false;
            }

            $hasUnfinishedSchedule = BidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('mop_uid', $modeUid)
                ->where(function ($query) {
                    $query->whereNull('bidding_result')
                        ->orWhere('bidding_result', '');
                })
                ->exists();

            return !$hasUnfinishedSchedule;
        }
    }

    public function addMode()
    {
        // Define a template for an empty bid schedule
        $emptyBidSchedule = [
            'bidding_number' => 1,
            'ib_number' => '',
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bidding_date' => null,
            'bidding_result' => '',
            'ntf_no' => '',
            'ntf_bidding_date' => null,
            'ntf_bidding_result' => '',
            'rfq_no' => '',
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
            'resolution_number' => '',
        ];

        // ✅ Calculate next mode_order from DATABASE, not form
        $maxModeOrderInForm = collect($this->form['modes'])
            ->max('mode_order') ?? 0;

        $newModeOrder = $maxModeOrderInForm + 1;

        $newMode = [
            'uid' => 'TEMP-' . uniqid(),
            'mode_of_procurement_id' => '',
            'mode_order' => $newModeOrder, // This is now correct
            'bid_schedules' => [$emptyBidSchedule],
        ];

        // Add the new mode to the beginning of the array (for display)
        array_unshift($this->form['modes'], $newMode);
    }

    public function addBidSchedule($modeIndex)
    {
        $existingSchedules = $this->form['modes'][$modeIndex]['bid_schedules'] ?? [];

        $maxBiddingNumber = collect($existingSchedules)->max('bidding_number') ?? 0;
        $newBiddingNumber = $maxBiddingNumber + 1;

        // Default structure matches validation rules exactly (snake_case keys)
        $newBidSchedule = [
            'uid' => 'TEMP-' . uniqid(),
            'ib_number' => '',
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bidding_date' => null,
            'bidding_result' => '',
            'bidding_number' => $newBiddingNumber,
            'ntf_no' => '',
            'ntf_bidding_date' => null,
            'ntf_bidding_result' => '',
            'rfq_no' => '',
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
        ];

        // If this is mode 5, ensure at least one schedule exists right away
        if (
            isset($this->form['modes'][$modeIndex]['mode_of_procurement_id']) &&
            $this->form['modes'][$modeIndex]['mode_of_procurement_id'] == 5 &&
            empty($existingSchedules)
        ) {
            $this->form['modes'][$modeIndex]['bid_schedules'][] = $newBidSchedule;
            return;
        }

        // Otherwise, prepend new schedule to the list
        $this->form['modes'][$modeIndex]['bid_schedules'] = array_merge(
            [$newBidSchedule],
            $existingSchedules
        );
    }

    private function validateData()
    {
        $this->validate([
            'form.modes' => 'required|array',
            'form.modes.*.mode_of_procurement_id' => 'required|exists:mode_of_procurements,id',
            'form.modes.*.bid_schedules' => 'nullable|array',
            'form.modes.*.bid_schedules.*.ib_number' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.pre_proc_conference' => 'nullable|date',
            'form.modes.*.bid_schedules.*.ads_post_ib' => 'nullable|date',
            'form.modes.*.bid_schedules.*.pre_bid_conf' => 'nullable|date',
            'form.modes.*.bid_schedules.*.eligibility_check' => 'nullable|date',
            'form.modes.*.bid_schedules.*.sub_open_bids' => 'nullable|date',
            'form.modes.*.bid_schedules.*.bidding_number' => 'nullable|integer|min:0|max:255',
            'form.modes.*.bid_schedules.*.bidding_date' => 'nullable|date',
            'form.modes.*.bid_schedules.*.bidding_result' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.ntf_no' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.ntf_bidding_date' => 'nullable|date',
            'form.modes.*.bid_schedules.*.ntf_bidding_result' => 'nullable|string|max:255',
        ]);

        foreach ($this->form['modes'] as $modeIndex => $mode) {
            if ($mode['mode_of_procurement_id'] == 5) {
                if (empty($mode['bid_schedules']) || !is_array($mode['bid_schedules'])) {
                    throw ValidationException::withMessages([
                        "form.modes.$modeIndex.bid_schedules" => 'Mode 5 requires at least one bid schedule.',
                    ]);
                }
                $isPlaceholder = count($mode['bid_schedules']) === 1 &&
                    empty($mode['bid_schedules'][0]['resolution_number']) &&
                    empty($mode['bid_schedules'][0]['rfq_no']) &&
                    empty($mode['bid_schedules'][0]['canvass_date']) &&
                    empty($mode['bid_schedules'][0]['date_returned_of_canvass']) &&
                    empty($mode['bid_schedules'][0]['abstract_of_canvass_date']);
                if (!$isPlaceholder) {
                    foreach ($mode['bid_schedules'] as $bidIndex => $schedule) {
                        $this->validate([
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.resolution_number" => 'required|string|max:255',
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.rfq_no" => 'required|string|max:255',
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.canvass_date" => 'required|date',
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.date_returned_of_canvass" => 'required|date',
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.abstract_of_canvass_date" => 'required|date',
                        ]);
                    }
                }
            }
        }
    }

    private function prepareModes()
    {
        $modes = $this->form['modes'];
        usort($modes, fn($a, $b) => ($a['mode_order'] ?? 0) <=> ($b['mode_order'] ?? 0));
        return $modes;
    }

    public function setStep($step)
    {
        $this->activeTab = $step;
    }

    public function save()
    {
        if ($this->activeTab === 1) {
            $this->saveTab1();
        } elseif ($this->activeTab === 2) {
            $this->saveTab2();
        } elseif ($this->activeTab === 3) {
            $this->saveTab3();
        }
    }

    public function saveTab1()
    {
        if (empty($this->selectedProcurements)) {
            LivewireAlert::title('Selection Required')
                ->error()
                ->text('Please select at least one PR Lot or Item.')
                ->toast()->position('top-end')->show();
            return;
        }

        try {
            $group = DB::transaction(function () {
                $year = now()->format('Y');
                $lastNumber = MopGroup::whereYear('created_at', $year)
                    ->where('ref_number', 'like', "MOP-$year-%")
                    ->orderByDesc('id')
                    ->value('ref_number');

                $nextNumber = 1;
                if ($lastNumber) {
                    $lastNum = (int) Str::afterLast($lastNumber, '-');
                    $nextNumber = $lastNum + 1;
                }

                $refNumber = sprintf('MOP-%s-%04d', $year, $nextNumber);

                $mopGroup = MopGroup::create([
                    'ref_number' => $refNumber,
                    'status' => 'draft',
                    'procurable_type' => $this->procurementType,
                ]);

                // Separate by type
                $lotIDs = collect($this->selectedProcurements)
                    ->filter(fn($proc) => empty($proc['items']))
                    ->pluck('procID');

                $itemIDs = collect($this->selectedProcurements)
                    ->filter(fn($proc) => !empty($proc['items']))
                    ->pluck('items.*.prItemID')
                    ->flatten();

                $uidBase = $mopGroup->ref_number;

                // Attach lots
                if ($lotIDs->isNotEmpty()) {
                    $mopGroup->procurements()->attach($lotIDs);

                    $attachedLots = Procurement::whereIn('procID', $lotIDs)->get();
                    foreach ($attachedLots as $lot) {
                        $existingMop = $lot->mops()->first();
                        if (!$existingMop) {
                            $uid = "{$uidBase}-1-1";
                            $lot->mops()->create([
                                'mop_group_ref' => $refNumber,
                                'mode_of_procurement_id' => 1,
                                'original_mode_of_procurement_id' => 1,
                                'current_mode_of_procurement_id' => 1,
                                'mode_order' => 1,
                                'uid' => $uid,
                                'procurable_type' => $this->procurementType,
                            ]);
                        }
                    }
                }

                // Attach items
                if ($itemIDs->isNotEmpty()) {
                    $mopGroup->prItems()->attach($itemIDs);

                    $attachedItems = PrItem::whereIn('prItemID', $itemIDs)->get();
                    foreach ($attachedItems as $item) {
                        $existingMop = $item->mops()->first();
                        if (!$existingMop) {
                            // 👇 Generate proper UID (Option B pattern)
                            $uid = "{$uidBase}-1-1";
                            $item->mops()->create([
                                'mop_group_ref' => $refNumber,
                                'mode_of_procurement_id' => 1,
                                'original_mode_of_procurement_id' => 1,
                                'current_mode_of_procurement_id' => 1,
                                'mode_order' => 1,
                                'uid' => $uid,
                                'procurable_type' => $this->procurementType,
                            ]);
                        }
                    }
                }

                return $mopGroup;
            });

            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Saved!',
                'message' => 'Your Procurement has been created successfully.',
            ]);

            return redirect()->route('mode-of-procurement.update', [
                'ref_number' => $group->ref_number,
                'tab' => 2,
            ]);

        } catch (\Exception $e) {
            Log::error('Error saving MOP Details selections: ' . $e->getMessage());

            LivewireAlert::title('Error Saving Selections')
                ->error()
                ->text('An error occurred: ' . $e->getMessage())
                ->toast()->position('top-end')->show();
        }
    }

    private function syncMopFor($item, $modeData)
    {
        $modeId = $modeData['mode_of_procurement_id'] ?? null;
        $modeOrder = $modeData['mode_order'] ?? 1;

        $existingMops = $item->mops()->get();
        $mop = $existingMops->firstWhere('mode_order', $modeOrder);

        if (!$mop) {
            // Create new MOP (original = current)
            $item->mops()->create([
                'original_mode_of_procurement_id' => $modeId,
                'current_mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
                'mop_group_id' => $this->mopGroupId,
                'procurable_type' => $this->procurementType,
            ]);
            return;
        }

        // If existing MOP, check if the mode changed
        if ($mop->current_mode_of_procurement_id !== $modeId) {
            $mop->update([
                'current_mode_of_procurement_id' => $modeId,
            ]);
        }
    }
    private function isDirtyArray(array $original, array $updated): bool
    {
        foreach ($updated as $key => $value) {
            $origValue = array_key_exists($key, $original) ? $original[$key] : null;

            if ($origValue instanceof \DateTimeInterface) {
                $origValue = $origValue->format('Y-m-d H:i:s');
            }

            // loose compare to avoid trivial type mismatches blocking updates
            if ($origValue != $value) {
                return true;
            }
        }

        return false;
    }

    private function modelDataChanged($model, array $data): bool
    {
        // $model is an Eloquent model
        // compare only keys present in $data
        return $this->isDirtyArray($model->getAttributes(), $data);
    }
    private function upsertBidScheduleIfChanged(string $scheduleUid, int $modeId, array $data): bool
    {
        $modelClass = ($modeId === 4) ? NtfBidSchedule::class : BidSchedule::class;

        $existing = $modelClass::where('mop_group_ref', $this->ref_number)
            ->where('uid', $scheduleUid)
            ->first();

        if ($existing) {
            if ($this->modelDataChanged($existing, $data)) {
                $existing->update($data);
                return true;
            }
            return false;
        }

        // create
        $modelClass::create(array_merge(['mop_group_ref' => $this->ref_number, 'uid' => $scheduleUid], $data));
        return true;
    }


    private function upsertPrSvpIfChanged(string $uid, array $data): bool
    {
        $existing = PrSvp::where('mop_group_ref', $this->ref_number)
            ->where('uid', $uid)
            ->first();

        if ($existing) {
            if ($this->modelDataChanged($existing, $data)) {
                $existing->update($data);
                return true;
            }
            return false;
        }

        PrSvp::create(array_merge(['mop_group_ref' => $this->ref_number, 'uid' => $uid], $data));
        return true;
    }
    private function syncModeUidToForm(array &$mode, string $uid, int $modeOrder): void
    {
        $mode['uid'] = $uid;
        $mode['mode_order'] = $modeOrder;
    }

    public function saveTab2()
    {
        try {
            $this->justAddedNewMode = false;
            $this->justAddedNewSchedule = false;
            $this->changesMade = false;
            $hasModesToSave = false;
            $hasSchedulesToSave = false;
            $hasNonDefaultMode = false;
            $hasNewSchedule = false;

            // 1. Check state (using isScheduleEmpty)
            foreach ($this->form['modes'] as $modeIndex => $mode) {
                $modeId = $mode['mode_of_procurement_id'] ?? null;
                if (empty($modeId)) {
                    continue;
                }
                $hasModesToSave = true;

                if ($modeId && $modeId != 1) {
                    $hasNonDefaultMode = true;
                }

                if (!empty($mode['bid_schedules'])) {
                    foreach ($mode['bid_schedules'] as $schedule) {
                        if (empty($schedule['uid']) || (isset($schedule['uid']) && str_starts_with($schedule['uid'], 'TEMP-'))) {
                            $hasNewSchedule = true;
                        }

                        if (!$this->isScheduleEmpty($schedule, $modeId)) {
                            $hasSchedulesToSave = true;
                            break 2;
                        }
                    }
                }
            }

            if ($hasNewSchedule) {
                $hasSchedulesToSave = true;
                $this->validateTab2();
            }

            // 2. Quick UX checks
            if (!$hasNonDefaultMode && !$hasSchedulesToSave) {
                LivewireAlert::title('No Changes to Save')
                    ->info()->text('For BAC Decision is already set as default.')
                    ->toast()->position('top-end')->show();
                return;
            }

            if (!$hasModesToSave) {
                LivewireAlert::title('No Changes to Save')
                    ->info()->text('Please select a mode of procurement first.')
                    ->toast()->position('top-end')->show();
                return;
            }

            if ($hasSchedulesToSave && !$hasNewSchedule) {
                $this->validateTab2();
            }

            // 3. Process everything inside a transaction
            DB::transaction(function () use ($hasSchedulesToSave) {
                $modesForProcessing = $this->prepareModes();

                foreach ($modesForProcessing as $modeIndex => $mode) {
                    if (empty($mode['mode_of_procurement_id'])) {
                        continue;
                    }
                    $this->processMode($mode, $modeIndex);
                }

                if (!$this->justAddedNewMode && $hasSchedulesToSave === false) {
                    foreach ($this->form['modes'] as $modeIndex => $mode) {
                        $modeId = $mode['mode_of_procurement_id'] ?? null;
                        if ($modeId && $modeId != 1 && $modeId != 5) {
                            $allSchedulesAreEmpty = true;
                            if (!empty($mode['bid_schedules'])) {
                                $allSchedulesAreEmpty = collect($mode['bid_schedules'])->every(fn($s) => $this->isScheduleEmpty($s, $modeId));
                            }
                            if ($allSchedulesAreEmpty) {
                                $firstScheduleKey = "form.modes.$modeIndex.bid_schedules.0.ib_number";
                                throw ValidationException::withMessages([
                                    $firstScheduleKey => 'The IB No. is required for this mode.',
                                ]);
                            }
                        }
                    }
                }
            });

            // 4. Repopulate placeholder
            foreach ($this->form['modes'] as $modeIndex => &$mode) {
                $modeId = $mode['mode_of_procurement_id'] ?? null;
                if ($modeId && $modeId != 1 && empty($mode['bid_schedules'])) {
                    $mode['bid_schedules'] = [
                        [
                            'uid' => 'TEMP-' . uniqid(),
                            'bidding_number' => 1,
                            'ib_number' => '',
                            'pre_proc_conference' => null,
                            'ads_post_ib' => null,
                            'pre_bid_conf' => null,
                            'eligibility_check' => null,
                            'sub_open_bids' => null,
                            'bidding_date' => null,
                            'bidding_result' => '',
                            'ntf_no' => '',
                            'ntf_bidding_date' => null,
                            'ntf_bidding_result' => null,
                            'rfq_no' => '',
                            'canvass_date' => null,
                            'date_returned_of_canvass' => null,
                            'abstract_of_canvass_date' => null,
                            'resolution_number' => '',
                        ]
                    ];
                }
            }
            unset($mode);

            // 5. Show toast based on result
            if ($this->justAddedNewMode && $this->justAddedNewSchedule) {
                LivewireAlert::title('Mode and Bid Schedule Added Successfully!')
                    ->success()->toast()->position('top-end')->show();
            } elseif ($this->justAddedNewMode) {
                LivewireAlert::title('Mode Added Successfully!')
                    ->success()->toast()->position('top-end')->show();
            } elseif ($this->justAddedNewSchedule) {
                LivewireAlert::title('Bid Schedule Added Successfully!')
                    ->success()->toast()->position('top-end')->show();
            } elseif ($this->changesMade) {
                LivewireAlert::title('Updated Successfully!')
                    ->success()->toast()->position('top-end')->show();
            } else {
                LivewireAlert::title('No changes — nothing to save.')
                    ->info()->toast()->position('top-end')->show();
            }

            $this->checkSuccessfulBidOrNtf();
            $this->checkSuccessfulSvp();

            if ($this->hasSuccessfulBidOrNtf || $this->hasSuccessfulSvp) {
                $this->activeTab = 3;
            }
        } catch (ValidationException $e) {
            LivewireAlert::title('Validation Failed!')
                ->error()->text($e->getMessage())->toast()->position('top-end')->show();
        } catch (\Exception $e) {
            \Log::error('Error Saving Data: ' . $e->getMessage());
            LivewireAlert::title('Error Saving Data!')
                ->error()->text($e->getMessage())->toast()->position('top-end')->show();
        }
    }


    private function isScheduleEmpty(array $schedule, int $modeId): bool
    {


        if ($modeId == 5) {
            // Mode 5 (SVP)
            return empty($schedule['resolution_number'])
                && empty($schedule['rfq_no'])
                && empty($schedule['canvass_date'])
                && empty($schedule['date_returned_of_canvass'])
                && empty($schedule['abstract_of_canvass_date']);
        }

        // For all other modes
        return empty($schedule['ib_number'])
            && empty($schedule['pre_proc_conference'])
            && empty($schedule['ads_post_ib'])
            && empty($schedule['pre_bid_conf'])
            && empty($schedule['eligibility_check'])
            && empty($schedule['sub_open_bids'])
            && empty($schedule['bidding_date'])
            && empty($schedule['bidding_result'])
            && empty($schedule['ntf_no'])
            && empty($schedule['ntf_bidding_date'])
            && empty($schedule['ntf_bidding_result'])
            && empty($schedule['rfq_no'])
            && empty($schedule['canvass_date'])
            && empty($schedule['date_returned_of_canvass'])
            && empty($schedule['abstract_of_canvass_date']);
    }

    private function validateTab2()
    {
        // Base rules (unchanged)
        $this->validate([
            'form.modes' => 'required|array',
            'form.modes.*.mode_of_procurement_id' => 'required|exists:mode_of_procurements,id',
            'form.modes.*.bid_schedules' => 'nullable|array',
            'form.modes.*.bid_schedules.*.ib_number' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.pre_proc_conference' => 'nullable|date',
            'form.modes.*.bid_schedules.*.ads_post_ib' => 'nullable|date',
            'form.modes.*.bid_schedules.*.pre_bid_conf' => 'nullable|date',
            'form.modes.*.bid_schedules.*.eligibility_check' => 'nullable|date',
            'form.modes.*.bid_schedules.*.sub_open_bids' => 'nullable|date',
            'form.modes.*.bid_schedules.*.bidding_number' => 'nullable|integer|min:0|max:255', // Keep nullable here
            'form.modes.*.bid_schedules.*.bidding_date' => 'nullable|date',
            'form.modes.*.bid_schedules.*.bidding_result' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.ntf_no' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.ntf_bidding_date' => 'nullable|date',
            'form.modes.*.bid_schedules.*.ntf_bidding_result' => 'nullable|string|max:255',
        ]);

        // We loop through all schedules and *only* validate the ones that are NOT empty.
        foreach ($this->form['modes'] as $modeIndex => $mode) {
            $modeId = $mode['mode_of_procurement_id'] ?? null;
            if (!$modeId || empty($mode['bid_schedules'])) {
                continue;
            }

            foreach ($mode['bid_schedules'] as $bidIndex => $schedule) {
                // Check if the schedule is NOT empty (i.e., user tried to fill it)
                $isTemp = !empty($schedule['uid']) && str_starts_with($schedule['uid'], 'TEMP-');

                if (!$this->isScheduleEmpty($schedule, $modeId) || $isTemp) {

                    // If it's not empty, validate it based on its type.
                    if ($modeId == 5) {
                        // Mode 5 (SVP) Validation
                        $this->validate(
                            [
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.resolution_number" => 'required|string|max:255',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.rfq_no" => 'required|string|max:255',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.canvass_date" => 'required|date',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.date_returned_of_canvass" => 'required|date',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.abstract_of_canvass_date" => 'required|date',
                            ],
                            [
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.resolution_number.required" =>
                                    'The Resolution Number is required.',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.rfq_no.required" =>
                                    'The RFQ Number is required.',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.canvass_date.required" =>
                                    'The Canvass Date is required.',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.date_returned_of_canvass.required" =>
                                    'The Returned of Canvass is required.',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.abstract_of_canvass_date.required" =>
                                    'The  Abstract of Canvass is required.',
                            ]
                        );
                    } else {
                        // [!!! THE FIX IS HERE !!!]
                        // All other modes: IB Number AND Bidding Number are required
                        $this->validate(
                            [
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.ib_number" => 'required|string|max:255',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.bidding_number" => 'required|integer|min:1', // Must be at least 1
                            ],
                            [
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.ib_number.required" =>
                                    'The IB No. is required since other fields are filled.',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.bidding_number.required" =>
                                    'The Bidding # is required since other fields are filled.',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.bidding_number.integer" =>
                                    'The Bidding # must be a number.',
                                "form.modes.$modeIndex.bid_schedules.$bidIndex.bidding_number.min" =>
                                    'The Bidding # must be at least 1.',
                            ]
                        );
                    }
                }
            }
        }

        // Duplicate combination check (This is still correct and needed)
        foreach ($this->form['modes'] as $modeIndex => $mode) {
            if (empty($mode['bid_schedules'])) {
                continue;
            }
            $combinations = [];
            foreach ($mode['bid_schedules'] as $bidIndex => $schedule) {
                $biddingNumber = $schedule['bidding_number'] ?? null;
                $ibNumber = $schedule['ib_number'] ?? null;
                if (empty($biddingNumber) || empty($ibNumber)) {
                    continue;
                }
                $key = "{$biddingNumber}|{$ibNumber}";
                if (isset($combinations[$key])) {
                    throw ValidationException::withMessages([
                        "form.modes.$modeIndex.bid_schedules.$bidIndex.ib_number" =>
                            "This combination of Bidding Number ({$biddingNumber}) and IB Number ({$ibNumber}) already exists in this mode.",
                    ]);
                }
                $combinations[$key] = true;
            }
        }
    }
    private function processMode(array $mode, int $modeIndex)
    {
        Log::info("Processing Mode {$modeIndex}:", $mode);
        $modeId = $mode['mode_of_procurement_id'];
        $modeOrder = $mode['mode_order'] ?? 1;

        $this->preventDuplicateMode($modeId, $mode);

        $mopGroup = MopGroup::where('ref_number', $this->ref_number)->first();

        if (!$mopGroup) {
            throw new \Exception("MopGroup not found for ref: {$this->ref_number}");
        }

        $createdMode = null;
        $originalUid = $mode['uid'] ?? null;

        if ($this->procurementType === 'perLot') {
            foreach ($mopGroup->procurements as $procurement) {
                $createdMode = $this->updateOrCreateBidModeForProcurable(
                    $mode,
                    $modeId,
                    $modeOrder,
                    $procurement->procID,
                    'App\Models\Procurement'
                );
            }
        } else {
            foreach ($mopGroup->prItems as $item) {
                $createdMode = $this->updateOrCreateBidModeForProcurable(
                    $mode,
                    $modeId,
                    $modeOrder,
                    $item->prItemID,
                    'App\Models\PrItem'
                );
            }
        }

        // Process schedules
        if ($createdMode && !empty($mode['bid_schedules'])) {
            if ($modeId == 5) {
                $madeChange = $this->processPrSvp($mode['bid_schedules'], $createdMode->uid);
                if ($madeChange)
                    $this->changesMade = true;
            } else {
                $madeChangeCount = $this->processSchedules($mode['bid_schedules'], $createdMode->uid, $modeId);
                if ($madeChangeCount > 0) {
                    $this->changesMade = true;
                }
            }

            // Update schedule UIDs in form after saving
            $this->updateScheduleUidsInForm($mode['bid_schedules'], $createdMode->uid, $modeId, $originalUid);
        }

        // Update mode UID in form
        if ($createdMode && $originalUid) {
            foreach ($this->form['modes'] as $index => &$formMode) {
                if (($formMode['uid'] ?? null) === $originalUid) {
                    $formMode['uid'] = $createdMode->uid;
                    $formMode['mode_order'] = $createdMode->mode_order;
                    break;
                }
            }
            unset($formMode);
        }
    }

    private function updateOrCreateBidModeForProcurable($mode, $modeId, $modeOrder, $procurableId, $procurableType)
    {
        $existingMode = !empty($mode['uid']) && !str_starts_with($mode['uid'], 'TEMP-')
            ? Mop::where('uid', $mode['uid'])
                ->where('procurable_id', $procurableId)
                ->where('procurable_type', $procurableType)
                ->first()
            : null;

        // If existing record is mode_id == 1 and incoming is not 1, create new
        if ($existingMode && $existingMode->mode_of_procurement_id == 1 && $modeId != 1) {
            $newOrder = Mop::where('mop_group_ref', $this->ref_number)
                ->where('procurable_id', $procurableId)
                ->where('procurable_type', $procurableType)
                ->max('mode_order') + 1;

            $uid = "{$this->ref_number}-{$modeId}-{$newOrder}";

            $this->justAddedNewMode = true;
            $this->changesMade = true;

            return Mop::create([
                'mop_group_ref' => $this->ref_number,
                'uid' => $uid,
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $newOrder,
                'procurable_id' => $procurableId,
                'procurable_type' => $procurableType,
            ]);
        }

        // Standard update or create
        if ($existingMode) {
            $update = [
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
            ];

            if ($this->modelDataChanged($existingMode, $update)) {
                $existingMode->update($update);
                $this->changesMade = true;
            }
        } else {
            $actualMaxOrder = Mop::where('mop_group_ref', $this->ref_number)
                ->where('procurable_id', $procurableId)
                ->where('procurable_type', $procurableType)
                ->max('mode_order') ?? 0;

            $newOrder = $actualMaxOrder + 1;
            $uid = "{$this->ref_number}-{$modeId}-{$newOrder}";

            $this->justAddedNewMode = true;
            $this->changesMade = true;

            $existingMode = Mop::create([
                'mop_group_ref' => $this->ref_number,
                'uid' => $uid,
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $newOrder,
                'procurable_id' => $procurableId,
                'procurable_type' => $procurableType,
            ]);
        }

        return $existingMode;
    }

    private function preventDuplicateMode($modeId, $mode)
    {
        if ($modeId == 1) {
            $exists = Mop::where('mop_group_ref', $this->ref_number)
                ->where('mode_of_procurement_id', 1)
                ->exists();

            if ($exists && (empty($mode['uid']) || str_starts_with($mode['uid'], 'TEMP-'))) {
                throw new \Exception('Mode of procurement ID 1 is already added.');
            }
        }
    }
    private function updateOrCreateBidMode($mode, $modeId, $modeOrder)
    {
        $existingMode = !empty($mode['uid']) && !str_starts_with($mode['uid'], 'TEMP-')
            ? Mop::where('uid', $mode['uid'])->first()
            : null;

        // If existing record is mode_id == 1 and incoming is not 1, create new
        if ($existingMode && $existingMode->mode_of_procurement_id == 1 && $modeId != 1) {
            // Determine the next mode_order for this procID
            $newOrder = Mop::where('mop_group_ref', $this->ref_number)->max('mode_order') + 1;

            // Generate a clean uid (not temporary)
            $uid = "MOP{$modeId}-{$newOrder}";

            return Mop::create([
                'mop_group_ref' => $this->ref_number,
                'uid' => $uid,
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $newOrder,
                'procurable_type' => $this->procurementType,
            ]);
        }

        // Standard update or create
        if ($existingMode) {
            $update = [
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
            ];

            $existingMode->update($update);
        } else {
            $uid = "MOP{$modeId}-{$modeOrder}";
            $existingMode = Mop::create([
                'mop_group_ref' => $this->ref_number,
                'uid' => $uid,
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
                'procurable_type' => $this->procurementType,
            ]);
        }

        return $existingMode;
    }
    private function processSchedules(array $schedules, string $uid, int $modeId): int
    {
        $changedCount = 0;
        $reorderedSchedules = array_reverse($schedules);

        foreach ($reorderedSchedules as $i => $schedule) {
            if ($this->isScheduleEmpty($schedule, $modeId) && !(isset($schedule['uid']) && str_starts_with($schedule['uid'], 'TEMP-'))) {
                continue;
            }

            $biddingNumber = !empty($schedule['bidding_number']) ? $schedule['bidding_number'] : ($i + 1);
            $scheduleUid = "{$uid}-{$biddingNumber}";

            // Check if this schedule actually exists in the database
            $modelClass = ($modeId === 4) ? NtfBidSchedule::class : BidSchedule::class;
            $existsInDb = $modelClass::where('mop_group_ref', $this->ref_number)
                ->where('uid', $scheduleUid)
                ->exists();

            // Only mark as "new" if it has TEMP uid AND doesn't exist in database
            if (!empty($schedule['uid']) && str_starts_with($schedule['uid'], 'TEMP-') && !$existsInDb) {
                $this->justAddedNewSchedule = true;
            }

            $baseData = [
                'mop_group_ref' => $this->ref_number,
                'mop_uid' => $uid,
                'uid' => $scheduleUid,
                'ib_number' => $schedule['ib_number'] ?? null,
                'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
                'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
                'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
                'eligibility_check' => $schedule['eligibility_check'] ?? null,
                'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
                'bidding_number' => $biddingNumber,
            ];

            if ($modeId == 4) {
                $ntfData = array_merge($baseData, [
                    'ntf_no' => $schedule['ntf_no'] ?? null,
                    'ntf_bidding_date' => $schedule['ntf_bidding_date'] ?? null,
                    'ntf_bidding_result' => $schedule['ntf_bidding_result'] ?? null,
                    'rfq_no' => $schedule['rfq_no'] ?? null,
                    'canvass_date' => $schedule['canvass_date'] ?? null,
                    'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
                    'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
                ]);

                if ($this->upsertBidScheduleIfChanged($scheduleUid, $modeId, $ntfData)) {
                    $changedCount++;
                }
            } else {
                $bidData = array_merge($baseData, [
                    'bidding_date' => $schedule['bidding_date'] ?? null,
                    'bidding_result' => $schedule['bidding_result'] ?? null,
                ]);

                if ($this->upsertBidScheduleIfChanged($scheduleUid, $modeId, $bidData)) {
                    $changedCount++;
                }
            }
        }

        return $changedCount;
    }

    private function processPrSvp(array $schedules, string $uid): bool
    {
        $schedule = $schedules[0] ?? null;
        if (!$schedule) {
            return false;
        }

        if ($this->isScheduleEmpty($schedule, 5) && !(isset($schedule['uid']) && str_starts_with($schedule['uid'], 'TEMP-'))) {
            return false;
        }

        // Check if this SVP schedule actually exists in the database
        $existsInDb = PrSvp::where('mop_group_ref', $this->ref_number)
            ->where('uid', $uid)
            ->exists();

        // Only mark as "new" if it has TEMP uid AND doesn't exist in database
        if (!empty($schedule['uid']) && str_starts_with($schedule['uid'], 'TEMP-') && !$existsInDb) {
            $this->justAddedNewSchedule = true;
        }

        $data = [
            'mop_uid' => $uid,
            'resolution_number' => $schedule['resolution_number'] ?? null,
            'rfq_no' => $schedule['rfq_no'] ?? null,
            'canvass_date' => $schedule['canvass_date'] ?? null,
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
        ];

        return $this->upsertPrSvpIfChanged($uid, $data);
    }
    private function updateScheduleUidsInForm(array $schedules, string $mopUid, int $modeId, ?string $modeUidInForm)
    {
        // Find the mode in the form that matches
        foreach ($this->form['modes'] as $modeIndex => &$formMode) {
            if (($formMode['uid'] ?? null) !== $modeUidInForm) {
                continue;
            }

            // Update each schedule's UID
            foreach ($formMode['bid_schedules'] as $schedIndex => &$formSchedule) {
                $originalScheduleUid = $formSchedule['uid'] ?? null;

                // Only update if it's a TEMP uid
                if ($originalScheduleUid && str_starts_with($originalScheduleUid, 'TEMP-')) {
                    $biddingNumber = $formSchedule['bidding_number'] ?? ($schedIndex + 1);
                    $realScheduleUid = "{$mopUid}-{$biddingNumber}";

                    // Update the UID in the form
                    $formSchedule['uid'] = $realScheduleUid;
                }
            }
            unset($formSchedule);

            break; // Found the mode, no need to continue
        }
        unset($formMode);
    }

    public function checkSuccessfulBidOrNtf()
    {
        if (empty($this->ref_number)) {
            $this->hasSuccessfulBidOrNtf = false;
            return;
        }

        $this->hasSuccessfulBidOrNtf = BidSchedule::where('mop_group_ref', $this->ref_number)
            ->where('bidding_result', 'SUCCESSFUL')
            ->exists() || NtfBidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('ntf_bidding_result', 'SUCCESSFUL')
                ->exists();
    }
    public function checkSuccessfulSvp()
    {
        if (empty($this->ref_number)) {
            $this->hasSuccessfulSvp = false;
            return;
        }

        // Adjust the field below if you have a different indicator for SVP success
        $this->hasSuccessfulSvp = PrSvp::where('mop_group_ref', $this->ref_number)
            ->whereNotNull('resolution_number')
            ->whereNotNull('rfq_no')
            ->whereNotNull('canvass_date')
            ->whereNotNull('date_returned_of_canvass')
            ->whereNotNull('abstract_of_canvass_date')
            ->exists();
    }
    public function saveTab3()
    {
        try {
            // Validate input
            $this->validate([
                'form.bidEvaluationDate' => 'nullable|date',
                'form.postQualDate' => 'nullable|date',
                'form.resolutionNumber' => 'nullable|string|max:255',
                'form.recommendingForAward' => 'nullable|date',
                'form.noticeOfAward' => 'nullable|date',
                'form.awardedAmount' => 'nullable|numeric|min:0',
                'form.dateOfPostingOfAwardOnPhilGEPS' => 'nullable|date',
                'form.philgepsReferenceNo' => 'nullable|string|max:255',
                'form.awardNoticeNumber' => 'nullable|string|max:255',
                'form.supplier_id' => 'nullable|exists:suppliers,id',
            ]);

            // Normalize awardedAmount
            if (!empty($this->form['awardedAmount'])) {
                $this->form['awardedAmount'] = floatval(preg_replace('/[^0-9.]/', '', $this->form['awardedAmount']));
            }

            // Prepare data
            $data = [
                'bid_evaluation_date' => $this->form['bidEvaluationDate'] ?? null,
                'post_qual_date' => $this->form['postQualDate'] ?? null,
                'resolution_number' => $this->form['resolutionNumber'] ?? null,
                'recommending_for_award' => $this->form['recommendingForAward'] ?? null,
                'notice_of_award' => $this->form['noticeOfAward'] ?? null,
                'awarded_amount' => $this->form['awardedAmount'] ?? null,
                'date_of_posting_of_award_on_philgeps' => $this->form['dateOfPostingOfAwardOnPhilGEPS'] ?? null,
                'philgeps_reference_no' => $this->form['philgepsReferenceNo'] ?? null,
                'award_notice_no' => $this->form['awardNoticeNumber'] ?? null,
                'supplier_id' => $this->form['supplier_id'] ?? null,
            ];

            // Check if there are any changes to save
            $hasChanges = false;

            DB::transaction(function () use ($data, &$hasChanges) {
                if (empty($this->ref_number)) {
                    throw new \Exception("Reference number is required.");
                }

                // Find existing PostProcurement record by mop_group_ref
                $existingRecord = PostProcurement::where('mop_group_ref', $this->ref_number)->first();

                if ($existingRecord) {
                    // Check if data has changed
                    if ($this->modelDataChanged($existingRecord, $data)) {
                        $existingRecord->update($data);
                        $hasChanges = 'updated';
                    }
                } else {
                    // Create new record with mop_group_ref
                    PostProcurement::create(array_merge(['mop_group_ref' => $this->ref_number], $data));
                    $hasChanges = 'created';
                }
            });

            // Show appropriate message
            if ($hasChanges === 'created') {
                LivewireAlert::title('Post Procurement Saved Successfully!')
                    ->success()
                    ->toast()
                    ->position('top-end')
                    ->show();
            } elseif ($hasChanges === 'updated') {
                LivewireAlert::title('Post Procurement Updated Successfully!')
                    ->success()
                    ->toast()
                    ->position('top-end')
                    ->show();
            } else {
                LivewireAlert::title('No changes to save')
                    ->info()
                    ->toast()
                    ->position('top-end')
                    ->show();
            }

        } catch (ValidationException $e) {
            LivewireAlert::title('Validation Failed!')
                ->error()
                ->text($e->getMessage())
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            \Log::error('Error saving PostProcurement: ' . $e->getMessage());

            LivewireAlert::title('Save Failed')
                ->error()
                ->text('An error occurred while saving: ' . $e->getMessage())
                ->toast()
                ->position('top-end')
                ->show();
        }
    }
    public function resetForm(): void
    {
        $this->selectedProcurements = [];
        $this->form = [
            'modes' => [],
        ];
        $this->resetValidation();
    }

    public function render()
    {
        $modes = ModeOfProcurement::where('is_active', true)
            ->orderBy('id')
            ->get();

        $existingLotIds = [];
        $existingItemIds = [];

        foreach ($this->selectedProcurements as $proc) {
            if (empty($proc['items'])) {
                $existingLotIds[] = $proc['id'];
            } else {
                foreach ($proc['items'] as $item) {
                    $existingItemIds[] = $item['id'];
                }
            }
        }

        $this->selectedLots = collect($this->selectedProcurements)
            ->filter(fn($proc) => empty($proc['items']))
            ->all();

        $this->selectedItemGroups = collect($this->selectedProcurements)
            ->filter(fn($proc) => !empty($proc['items']))
            ->all();

        $suppliers = Supplier::all();
        $procurementStages = ProcurementStage::all();
        $remarks = Remarks::all();

        return view('livewire.mode-of-procurement.mode-of-procurement-create-page', [
            'modeOfProcurements' => $modes,
            'existingLotIds' => $existingLotIds,
            'existingItemIds' => $existingItemIds,
            'suppliers' => $suppliers,
            'procurementStages' => $procurementStages,
            'remarks' => $remarks,
        ]);
    }
}
