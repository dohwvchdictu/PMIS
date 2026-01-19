<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\BidSchedule;
use App\Models\ModeOfProcurement;
use App\Models\PostProcurement;
use App\Models\PrSvp;
use App\Models\Supplier;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\MopItem;
use Illuminate\Support\Facades\DB;

class ModeOfProcurementPerItemPage extends Component
{
    public Procurement $procurement;
    public array $form = [];
    public Collection $modeOfProcurements;
    public int $textareaRows = 1;
    public string $procID = '';
    public int $activeTab = 1;
    public bool $showHistory = false;
    public ?string $historyForPrItemId = null;

    // Post-Procurement Tab Fields
    public array $postItems = [];
    public ?string $resolutionAwardNumber = null;
    public ?string $noticeOfAward = null;
    public ?string $resolutionAwardDate = null;
    public ?float $awardedAmount = null;
    public ?string $awardNoticeNumber = null;
    public ?string $dateOfPostingOfAwardOnPhilGEPS = null;
    public ?int $supplier_id = null;
    public Collection $suppliers;

    // Edit History Modal Properties
    public bool $showModal = false;
    public ?array $editingItem = null;
    public ?int $editingIndex = null;
    public array $scheduleValidationErrors = [];
    public $queryParams = [];

    public function mount(Procurement $procurement): void
    {
        $this->queryParams = request()->query();

        $procurement->load('pr_items', 'mopItems.modeOfProcurement', 'mopItems.item');
        $this->procurement = $procurement;
        $this->procID = $procurement->procID ?? '';

        // Initialize form with typed structure
        $this->form = [
            'pr_number' => $procurement->pr_number ?? '',
            'procurement_program_project' => $procurement->procurement_program_project ?? '',
            'approved_ppmp' => (bool) ($procurement->approved_ppmp ?? false),
            'app_updated' => (bool) ($procurement->app_updated ?? false),
            'early_procurement' => (bool) ($procurement->early_procurement ?? false),
            'items' => [],
        ];

        // Delegate to helper methods
        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();
        $this->suppliers = Supplier::all();
        $this->loadPerItemData($procurement);
        $this->loadPostProcurementData($procurement);
        $this->calculateTextareaRows($procurement->procurement_program_project ?? '');
    }
    private function hasValue($value): bool
    {
        if (is_null($value)) {
            return false;
        }

        $stringValue = trim((string) $value);

        return $stringValue !== '';
    }
    private function hasAnyValue(array $fields): bool
    {
        foreach ($fields as $value) {
            if ($this->hasValue($value)) {
                return true;
            }
        }
        return false;
    }
    private function loadPostProcurementData(Procurement $procurement): void
    {
        // Initialize postItems array for each eligible item
        $this->postItems = [];

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            $prItemID = $item['prItemID'] ?? null;

            // Check if item is eligible for post-procurement
            $isEligible = false;

            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $bidResult = $item['bidding_result'] ?? '';

                if ($bidResult === 'SUCCESSFUL') {
                    $isEligible = true;
                }
            }

            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                if (
                    !empty($item['resolution_number_mop']) &&
                    !empty($item['rfq_no']) &&
                    !empty($item['canvass_date']) &&
                    !empty($item['date_returned_of_canvass']) &&
                    !empty($item['abstract_of_canvass_date'])
                ) {
                    $isEligible = true;
                }
            }

            if ($isEligible && $prItemID) {
                // Load post-procurement data for this prItemID (stored as ref_id)
                $post = PostProcurement::where('ref_id', $prItemID)->first();

                $this->postItems[$prItemID] = [
                    'resolutionAwardNumber' => $post?->resolution_award_number ?? null,
                    'noticeOfAwardNumber' => $post?->notice_of_award_number ?? null,
                    'noticeOfAward' => $post?->notice_of_award ?? null,
                    'resolutionAwardDate' => $post?->resolution_award_date ?? null,
                    'awardedAmount' => $post?->awarded_amount ?? null,
                    'philgepsNoticeOfAwardNo' => $post?->philgeps_notice_of_award_no ?? null,
                    'philgepsPostingOfAward' => $post?->philgeps_posting_of_award ?? null,
                    'supplier_id' => $post?->supplier_id ?? null,
                ];
            }
        }
    }
    private function calculateTextareaRows(string $text): void
    {
        $text = trim($text);
        $lineCount = substr_count($text, "\n") + 1;
        $approxExtraLines = ceil(strlen($text) / 150);
        $this->textareaRows = max($lineCount, $approxExtraLines, 1);
    }
    public function getIsPostAvailableProperty(): bool
    {
        foreach ($this->form['items'] as $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;

            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $bidResult = $item['bidding_result'] ?? null;
                if ($bidResult === 'SUCCESSFUL') {
                    return true;
                }
            }

            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                // Using hasValue() for consistent checking
                if (
                    $this->hasValue($item['resolution_number_mop']) &&
                    $this->hasValue($item['rfq_no']) &&
                    $this->hasValue($item['canvass_date']) &&
                    $this->hasValue($item['date_returned_of_canvass']) &&
                    $this->hasValue($item['abstract_of_canvass_date'])
                ) {
                    return true;
                }
            }
        }

        return false;
    }
    protected function loadPerItemData(Procurement $procurement): void
    {
        // Load MOP Items grouped strictly by PR Item ID
        $mopItemsGrouped = $procurement->mopItems()
            ->with(['item', 'modeOfProcurement'])
            ->orderBy('mode_order', 'desc')
            ->get()
            ->groupBy('prItemID');

        // Get prItemIDs for schedules
        $prItemIds = $procurement->pr_items->pluck('prItemID')->filter()->toArray();

        // Fetch Schedules by ref_id (prItemID)
        $bidSchedules = BidSchedule::whereIn('ref_id', $prItemIds)->get();
        $prSvps = PrSvp::whereIn('ref_id', $prItemIds)->get();

        // Build unified schedule map keyed by prItemID and mop_uid
        $scheduleMap = $this->buildScheduleMap($bidSchedules, $prSvps);

        $this->form['items'] = [];

        // Loop through PR Items
        $sortedPrItems = $procurement->pr_items->sortBy('prItemID');

        foreach ($sortedPrItems as $prItem) {
            $prItemID = $prItem->prItemID;
            $relatedMops = $mopItemsGrouped->get($prItemID);

            if ($relatedMops && $relatedMops->count() > 0) {
                foreach ($relatedMops as $mopItem) {
                    $uid = $mopItem->uid;
                    // Check if prItemID exists in scheduleMap and get the schedule
                    $schedule = [];
                    if ($uid && $scheduleMap->has($prItemID)) {
                        $prItemSchedules = $scheduleMap->get($prItemID);
                        $schedule = $prItemSchedules->get($uid, []);
                    }
                    $this->form['items'][] = $this->mapItemToRow($prItem, $mopItem, $schedule);
                }
            } else {
                $this->form['items'][] = $this->mapItemToRow($prItem, null, []);
            }
        }
    }

    // Replace the buildScheduleMap function in ModeOfProcurementPerItemPage.php

    private function buildScheduleMap(
        Collection $bidSchedules,
        Collection $prSvps
    ): Collection {
        $map = collect();

        // Initialize map for all schedules
        foreach ($bidSchedules as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $map[$refId][$schedule->mop_uid] = [
                'mop_uid' => $schedule->mop_uid,
                'ib_number' => $schedule->ib_number,
                'philgeps_posting_ref_no' => $schedule->philgeps_posting_ref_no,
                'pre_proc_conference' => $schedule->pre_proc_conference,
                'ads_post_ib' => $schedule->ads_post_ib,
                'list_invited_observers' => $schedule->list_invited_observers,
                'obsrvr_prebid_conf' => $schedule->obsrvr_prebid_conf,
                'obsrvr_eligibility' => $schedule->obsrvr_eligibility,
                'obsrvr_sub_open_of_bid' => $schedule->obsrvr_sub_open_of_bid,
                'obsrvr_bid' => $schedule->obsrvr_bid,
                'obsrvr_post_qual' => $schedule->obsrvr_post_qual,
                'pre_bid_conf' => $schedule->pre_bid_conf,
                'eligibility_check' => $schedule->eligibility_check,
                'sub_open_bids' => $schedule->sub_open_bids,
                'bid_evaluation_date' => $schedule->bid_evaluation_date,
                'post_qualification_date' => $schedule->post_qualification_date,
                'bidding_number' => $schedule->bidding_number,
                'bidding_date' => $schedule->bidding_date,
                'bidding_result' => $schedule->bidding_result,
                'resolution_number_mop' => $schedule->resolution_number_mop,
            ];
        }

        // Merge PrSvp data
        foreach ($prSvps as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $mopUid = $schedule->mop_uid;
            $existing = $map[$refId]->get($mopUid, []);

            $map[$refId][$mopUid] = array_merge($existing, [
                'mop_uid' => $schedule->mop_uid,
                'philgeps_posting_ref_no' => $schedule->philgeps_posting_ref_no ?? ($existing['philgeps_posting_ref_no'] ?? null),
                'ads_post_ib' => $schedule->ads_post_ib ?? ($existing['ads_post_ib'] ?? null),
                'resolution_number_mop' => $schedule->resolution_number_mop,
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
            ]);
        }

        return $map;
    }
    private function mapItemToRow($prItem, $mopItem, array $schedule): array
    {
        return [
            'id' => $mopItem?->id,
            'prItemID' => $prItem->prItemID,
            'item_no' => $prItem->item_no,
            'description' => $prItem->description,
            'amount' => number_format((float) $prItem->amount, 2, '.', ''),
            'mode_of_procurement_id' => $mopItem?->mode_of_procurement_id,
            'uid' => $mopItem?->uid ?? 'new_' . uniqid(),
            'mode_order' => $mopItem?->mode_order ?? 1,

            // Bidding schedule fields
            'ib_number' => $schedule['ib_number'] ?? null,
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? null,
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
            'ads_post_ib' => $schedule['ads_post_ib'] ?? null,

            // ADDED: Observer fields (matching Per-Lot)
            'list_invited_observers' => $schedule['list_invited_observers'] ?? null,
            'obsrvr_prebid_conf' => $schedule['obsrvr_prebid_conf'] ?? null,
            'obsrvr_eligibility' => $schedule['obsrvr_eligibility'] ?? null,
            'obsrvr_sub_open_of_bid' => $schedule['obsrvr_sub_open_of_bid'] ?? null,
            'obsrvr_bid' => $schedule['obsrvr_bid'] ?? null,
            'obsrvr_post_qual' => $schedule['obsrvr_post_qual'] ?? null,

            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
            'eligibility_check' => $schedule['eligibility_check'] ?? null,
            'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
            'bid_evaluation_date' => $schedule['bid_evaluation_date'] ?? null,
            'post_qualification_date' => $schedule['post_qualification_date'] ?? null,
            'bidding_number' => $schedule['bidding_number'] ?? null,
            'bidding_date' => $schedule['bidding_date'] ?? null,
            'bidding_result' => $schedule['bidding_result'] ?? null,
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? null,
            'rfq_no' => $schedule['rfq_no'] ?? null,
            'canvass_date' => $schedule['canvass_date'] ?? null,
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
        ];
    }

    public function addItem($index): void
    {
        $referenceItem = $this->form['items'][$index] ?? null;

        if (!$referenceItem)
            return;

        $uniqueId = 'new_' . md5(microtime(true) . mt_rand());

        $newItem = [
            'id' => null,
            'prItemID' => $referenceItem['prItemID'],
            'item_no' => $referenceItem['item_no'],
            'description' => $referenceItem['description'],
            'amount' => $referenceItem['amount'],
            'uid' => $uniqueId,

            // Reset MOP fields
            'mode_of_procurement_id' => null,
            'mode_order' => ($referenceItem['mode_order'] ?? 0) + 1,

            // --- COMPETITIVE BIDDING FIELDS ---
            'bidding_number' => null,
            'ib_number' => null,
            'philgeps_posting_ref_no' => null,
            'pre_proc_conference' => null,
            'ads_post_ib' => null,

            // ADDED: Observer fields (matching Per-Lot)
            'list_invited_observers' => null,
            'obsrvr_prebid_conf' => null,
            'obsrvr_eligibility' => null,
            'obsrvr_sub_open_of_bid' => null,
            'obsrvr_bid' => null,
            'obsrvr_post_qual' => null,

            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bid_evaluation_date' => null,
            'post_qualification_date' => null,
            'bidding_date' => null,
            'bidding_result' => null,

            // --- RESOLUTION NUMBER (MOP) ---
            'resolution_number_mop' => null,

            // --- SVP FIELDS ---
            'rfq_no' => null,
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
        ];

        array_splice($this->form['items'], $index, 0, [$newItem]);
        $this->showHistory = false;
    }
    public function toggleHistory(string $prItemID)
    {
        if ($this->historyForPrItemId === $prItemID && $this->showHistory) {
            // If clicking the same item, just toggle off
            $this->showHistory = false;
            $this->historyForPrItemId = null;
        } else {
            // Show history for this specific PR Item (all its modes)
            $this->showHistory = true;
            $this->historyForPrItemId = $prItemID;
        }
    }
    public function removeItem($uid): void
    {
        $this->form['items'] = array_filter($this->form['items'], function ($item) use ($uid) {
            return $item['uid'] !== $uid;
        });

        $this->form['items'] = array_values($this->form['items']);
    }

    public function setStep(int $step): void
    {
        $this->activeTab = $step;
    }

    // --- History Editing Methods ---

    public function editHistoryItem($index): void
    {
        if (!isset($this->form['items'][$index])) {
            LivewireAlert::title('Error')
                ->error()
                ->text('History record not found.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Store the index and copy the item data for editing
        $this->editingIndex = $index;
        $this->editingItem = $this->form['items'][$index];
        $this->showModal = true;
    }

    public function updateHistoryItem(): void
    {
        if ($this->editingIndex === null || !isset($this->form['items'][$this->editingIndex])) {
            LivewireAlert::title('Error')
                ->error()
                ->text('Unable to update record - item not found.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Update the item in the form array
        $this->form['items'][$this->editingIndex] = $this->editingItem;

        // Validate schedules before saving
        $this->scheduleValidationErrors = [];
        if (!$this->validateSchedules()) {
            $errorMessage = $this->formatValidationErrors($this->scheduleValidationErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()
                ->position('top-end')
                ->show();

            // Restore original value on validation failure
            $this->mount($this->procurement);
            return;
        }

        // Save to database
        $this->saveTab1();

        // Close modal only after successful save
        $this->closeEditModal();
    }

    public function closeEditModal(): void
    {
        $this->showModal = false;
        $this->editingItem = null;
        $this->editingIndex = null;
    }
    private function validateSchedules(): bool
    {
        $isValid = true;
        $this->scheduleValidationErrors = [];

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            if (!$modeId)
                continue;

            $prItemID = $item['prItemID'];
            $itemNumber = $item['item_no'] ?? ($index + 1);
            $itemDesc = $item['description'] ?? 'Unknown Item';
            $shortDesc = strlen($itemDesc) > 50 ? substr($itemDesc, 0, 50) . '...' : $itemDesc;

            $matchCriteria = [
                'ref_id' => $prItemID,
                'mop_uid' => $item['uid']
            ];

            // UPDATED: Include observer fields in biddingFields array
            $biddingFields = [
                $item['ib_number'] ?? null,
                $item['philgeps_posting_ref_no'] ?? null,
                $item['bidding_number'] ?? null,
                $item['pre_proc_conference'] ?? null,
                $item['ads_post_ib'] ?? null,
                $item['list_invited_observers'] ?? null,
                $item['obsrvr_prebid_conf'] ?? null,
                $item['obsrvr_eligibility'] ?? null,
                $item['obsrvr_sub_open_of_bid'] ?? null,
                $item['obsrvr_bid'] ?? null,
                $item['obsrvr_post_qual'] ?? null,
                $item['pre_bid_conf'] ?? null,
                $item['eligibility_check'] ?? null,
                $item['sub_open_bids'] ?? null,
                $item['bid_evaluation_date'] ?? null,
                $item['post_qualification_date'] ?? null,
                $item['bidding_date'] ?? null,
                $item['bidding_result'] ?? null,
            ];

            $hasAnyData = $this->hasAnyValue($biddingFields);

            // Skip validation for empty new items
            if (!$hasAnyData && str_starts_with($item['uid'], 'new_')) {
                continue;
            }

            // COMPETITIVE BIDDING MODES (2, 3, 4, 5, 6)
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $existingBidSchedule = BidSchedule::where($matchCriteria)->first();

                $hasBiddingData = $this->hasAnyValue($biddingFields);

                if ($existingBidSchedule && !$hasBiddingData) {
                    $this->scheduleValidationErrors[] = sprintf(
                        "<strong>Item %s</strong> (%s): At least one bidding schedule field must be filled.",
                        $itemNumber,
                        $shortDesc
                    );
                    $isValid = false;
                }

                // ADDED: Validate Resolution Number (MOP) for modes 3-6 (matching Per-Lot)
                if (in_array($modeId, [3, 4, 5, 6])) {
                    if ($hasBiddingData && !$this->hasValue($item['resolution_number_mop'])) {
                        $this->scheduleValidationErrors[] = sprintf(
                            "<strong>Item %s</strong> (%s): Resolution Number (MOP) is required for this procurement mode.",
                            $itemNumber,
                            $shortDesc
                        );
                        $isValid = false;
                    }
                }

                // Validate Bidding Result dependencies
                $biddingResult = $item['bidding_result'] ?? null;

                if ($this->hasValue($biddingResult)) {
                    $missingFields = [];
                    $hasPreProcConference = $this->hasValue($item['pre_proc_conference']);

                    if (!$hasPreProcConference) {
                        if (!$this->hasValue($item['bidding_number'])) {
                            $missingFields[] = '<strong>Bidding #</strong>';
                        }
                        if (!$this->hasValue($item['ib_number'])) {
                            $missingFields[] = '<strong>IB No.</strong>';
                        }
                        if (!$this->hasValue($item['bidding_date'])) {
                            $missingFields[] = '<strong>Bidding Date</strong>';
                        }

                        if (!empty($missingFields)) {
                            $fieldsList = implode(', ', $missingFields);
                            $this->scheduleValidationErrors[] = sprintf(
                                "<strong>Item %s</strong> (%s): Cannot set Bidding Result to '%s' without %s or <strong>Pre-Proc Conference</strong>.",
                                $itemNumber,
                                $shortDesc,
                                $biddingResult,
                                $fieldsList
                            );
                            $isValid = false;
                        }
                    }

                    if ($biddingResult === 'SUCCESSFUL') {
                        $successMissingFields = [];

                        if (!$this->hasValue($item['bid_evaluation_date'])) {
                            $successMissingFields[] = '<strong>Bid Evaluation Date</strong>';
                        }
                        if (!$this->hasValue($item['post_qualification_date'])) {
                            $successMissingFields[] = '<strong>Post Qualification Date</strong>';
                        }

                        if (!empty($successMissingFields)) {
                            $fieldsList = implode(', ', $successMissingFields);
                            $this->scheduleValidationErrors[] = sprintf(
                                "<strong>Item %s</strong> (%s): %s required for SUCCESSFUL bidding result.",
                                $itemNumber,
                                $shortDesc,
                                $fieldsList
                            );
                            $isValid = false;
                        }
                    }
                }
            }

            // SVP/ALTERNATIVE MODES (7-24)
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                $existingSvp = PrSvp::where($matchCriteria)->first();

                $svpFields = [
                    $item['resolution_number_mop'] ?? null,
                    $item['rfq_no'] ?? null,
                    $item['canvass_date'] ?? null,
                    $item['date_returned_of_canvass'] ?? null,
                    $item['abstract_of_canvass_date'] ?? null,
                ];

                $hasSvpData = $this->hasAnyValue($svpFields);

                if ($existingSvp && !$hasSvpData) {
                    $this->scheduleValidationErrors[] = sprintf(
                        "<strong>Item %s</strong> (%s): At least one SVP field must be filled.",
                        $itemNumber,
                        $shortDesc
                    );
                    $isValid = false;
                }

                if ($hasSvpData) {
                    $requiredSvpFields = [
                        'resolution_number_mop' => 'Resolution Number (MOP)',
                        'rfq_no' => 'RFQ No.',
                        'canvass_date' => 'Canvass Date',
                        'date_returned_of_canvass' => 'Returned of Canvass',
                        'abstract_of_canvass_date' => 'Abstract of Canvass'
                    ];

                    $missingSvpFields = [];
                    foreach ($requiredSvpFields as $field => $label) {
                        if (!$this->hasValue($item[$field] ?? null)) {
                            $missingSvpFields[] = "<strong>{$label}</strong>";
                        }
                    }

                    if (!empty($missingSvpFields)) {
                        $fieldsList = implode(', ', $missingSvpFields);
                        $this->scheduleValidationErrors[] = sprintf(
                            "<strong>Item %s</strong> (%s): All SVP fields are required. Missing: %s",
                            $itemNumber,
                            $shortDesc,
                            $fieldsList
                        );
                        $isValid = false;
                    }
                }
            }
        }

        return $isValid;
    }
    public function saveTab1()
    {
        // STEP 1: Validate form rules FIRST
        $rules = [
            'form.items.*.mode_of_procurement_id' => 'required|integer',
        ];

        $messages = [];
        $attributes = [];

        // Build custom messages for each item
        foreach ($this->form['items'] as $index => $item) {
            $itemNumber = $item['item_no'] ?? ($index + 1);

            $messages["form.items.{$index}.mode_of_procurement_id.required"] =
                "Item {$itemNumber}: Mode of Procurement is required.";
            $messages["form.items.{$index}.mode_of_procurement_id.integer"] =
                "Item {$itemNumber}: Invalid Mode of Procurement selected.";

            $attributes["form.items.{$index}.mode_of_procurement_id"] =
                "Item {$itemNumber} - Mode of Procurement";
        }

        try {
            $this->validate($rules, $messages, $attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();

            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($this->formatValidationErrors($errorMessages))
                ->toast()
                ->position('top-end')
                ->show();

            // Exit early - don't proceed to schedule validation or DB transaction
            return;
        }

        // STEP 2: Validate schedules BEFORE starting transaction
        $this->scheduleValidationErrors = [];
        if (!$this->validateSchedules()) {
            LivewireAlert::title('Schedule Validation Failed')
                ->error()
                ->text($this->formatValidationErrors($this->scheduleValidationErrors))
                ->toast()
                ->position('top-end')
                ->show();

            // Exit early - validation failed, don't touch database
            return;
        }

        // STEP 3: ALL validation passed - NOW start the database transaction
        $isMopAdded = false;
        $isMopUpdated = false;
        $isScheduleAdded = false;
        $isScheduleUpdated = false;

        DB::transaction(function () use (&$isMopAdded, &$isMopUpdated, &$isScheduleAdded, &$isScheduleUpdated) {

            foreach ($this->form['items'] as $index => $item) {
                if (empty($item['mode_of_procurement_id']))
                    continue;

                $modeId = $item['mode_of_procurement_id'];
                $prItemID = $item['prItemID'] ?? null;

                // Check if the item is newly added in the UI
                $isUiNew = isset($item['uid']) && (str_starts_with($item['uid'], 'new_') || str_starts_with($item['uid'], 'temp_'));

                // Check if this is an existing saved record
                $isSavedRecord = isset($item['id']) && is_numeric($item['id']);

                if ($isUiNew) {
                    // FOR NEW ITEMS: Calculate mode_order based on existing DB records
                    $maxModeOrder = MopItem::where('procID', $this->procID)
                        ->where('prItemID', $prItemID)
                        ->max('mode_order') ?? 0;

                    $modeOrder = $maxModeOrder + 1;

                    // Generate UID
                    $generatedUid = "MOP-{$modeId}-{$modeOrder}";

                    // CREATE new record
                    $savedParentModel = MopItem::create([
                        'uid' => $generatedUid,
                        'mode_of_procurement_id' => $modeId,
                        'mode_order' => $modeOrder,
                        'procID' => $this->procID,
                        'prItemID' => $prItemID,
                    ]);

                    $isMopAdded = true;

                    // Save related schedules for new item
                    $this->saveRelatedSchedules(
                        $savedParentModel,
                        $item,
                        $isScheduleAdded,
                        $isScheduleUpdated
                    );

                } elseif ($isSavedRecord) {
                    // FOR EXISTING RECORDS: Check if mode_id changed
                    $existingRecord = MopItem::find($item['id']);

                    if (!$existingRecord) {
                        continue;
                    }

                    // Check if the mode_of_procurement_id has changed
                    $modeChanged = $existingRecord->mode_of_procurement_id != $modeId;

                    if ($modeChanged) {
                        // MODE CHANGED: Create a NEW record (leave old one as history)
                        $maxModeOrder = MopItem::where('procID', $this->procID)
                            ->where('prItemID', $prItemID)
                            ->max('mode_order') ?? 0;

                        $newModeOrder = $maxModeOrder + 1;
                        $generatedUid = "MOP-{$modeId}-{$newModeOrder}";

                        $savedParentModel = MopItem::create([
                            'uid' => $generatedUid,
                            'mode_of_procurement_id' => $modeId,
                            'mode_order' => $newModeOrder,
                            'procID' => $this->procID,
                            'prItemID' => $prItemID,
                        ]);

                        $isMopAdded = true;

                        // Save related schedules for new record
                        $this->saveRelatedSchedules(
                            $savedParentModel,
                            $item,
                            $isScheduleAdded,
                            $isScheduleUpdated
                        );

                    } else {
                        // MODE NOT CHANGED: Only update schedules on existing record
                        $this->saveRelatedSchedules(
                            $existingRecord,
                            $item,
                            $isScheduleAdded,
                            $isScheduleUpdated
                        );
                    }
                }
            }

        });

        if ($isMopAdded) {
            LivewireAlert::title('Mode Added Successfully!')->success()->text('The mode of procurement has been added.')->toast()->position('top-end')->show();
        } elseif ($isScheduleAdded) {
            LivewireAlert::title('Schedule Added!')->success()->text('A new bidding schedule has been created.')->toast()->position('top-end')->show();
        } elseif ($isMopUpdated || $isScheduleUpdated) {
            LivewireAlert::title('Updates Saved!')->success()->text('Changes have been saved successfully.')->toast()->position('top-end')->show();
        } else {
            LivewireAlert::title('No Changes')->info()->text('No changes were detected.')->toast()->position('top-end')->show();
        }

        $this->mount($this->procurement);
    }

    private function formatValidationErrors(array $errors): string
    {
        if (empty($errors)) {
            return 'Unknown validation error occurred.';
        }

        if (count($errors) === 1) {
            return strip_tags($errors[0]);
        }

        // Create a simple numbered list
        $text = '';
        foreach ($errors as $index => $error) {
            $cleanError = strip_tags($error);
            $text .= "\n• " . $cleanError;
        }

        return $text;
    }

    protected function saveRelatedSchedules(
        $parentModel,
        array $itemData,
        bool &$isScheduleAdded,
        bool &$isScheduleUpdated
    ): void {
        $modeId = $itemData['mode_of_procurement_id'];
        $refId = $itemData['prItemID'] ?? $parentModel->prItemID;
        $parentUid = $parentModel->uid;

        $matchCriteria = [
            'ref_id' => $refId,
            'mop_uid' => $parentUid
        ];

        $checkStatus = function ($model) use (&$isScheduleAdded, &$isScheduleUpdated) {
            if ($model->wasRecentlyCreated) {
                $isScheduleAdded = true;
            } elseif ($model->wasChanged()) {
                $isScheduleUpdated = true;
            }
        };

        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            // UPDATED: Include observer fields in the check
            $biddingFields = [
                $itemData['ib_number'] ?? null,
                $itemData['philgeps_posting_ref_no'] ?? null,
                $itemData['bidding_number'] ?? null,
                $itemData['pre_proc_conference'] ?? null,
                $itemData['ads_post_ib'] ?? null,
                $itemData['list_invited_observers'] ?? null,
                $itemData['obsrvr_prebid_conf'] ?? null,
                $itemData['obsrvr_eligibility'] ?? null,
                $itemData['obsrvr_sub_open_of_bid'] ?? null,
                $itemData['obsrvr_bid'] ?? null,
                $itemData['obsrvr_post_qual'] ?? null,
                $itemData['pre_bid_conf'] ?? null,
                $itemData['eligibility_check'] ?? null,
                $itemData['sub_open_bids'] ?? null,
                $itemData['bid_evaluation_date'] ?? null,
                $itemData['post_qualification_date'] ?? null,
                $itemData['bidding_date'] ?? null,
                $itemData['bidding_result'] ?? null,
            ];

            $hasBiddingData = $this->hasAnyValue($biddingFields);

            // Add resolution_number_mop check for modes 3-6
            if (in_array($modeId, [3, 4, 5, 6])) {
                $hasBiddingData = $hasBiddingData || $this->hasValue($itemData['resolution_number_mop']);
            }

            $existingBidSchedule = BidSchedule::where($matchCriteria)->first();

            if ($hasBiddingData || $existingBidSchedule) {
                if (!$hasBiddingData && $existingBidSchedule) {
                    return;
                }

                if (!$existingBidSchedule) {
                    $relatedMopUids = MopItem::where('prItemID', $refId)
                        ->where('mode_of_procurement_id', $modeId)
                        ->pluck('uid');

                    $count = BidSchedule::where('ref_id', $refId)
                        ->whereIn('mop_uid', $relatedMopUids)
                        ->count();

                    $uid = $parentUid . '-' . ($count + 1);
                } else {
                    $uid = $existingBidSchedule->uid;
                }

                // UPDATED: Include all observer fields in the save
                $model = BidSchedule::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $uid,
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,
                        'bidding_number' => $itemData['bidding_number'] ?? null,
                        'ib_number' => $itemData['ib_number'] ?? null,
                        'philgeps_posting_ref_no' => $itemData['philgeps_posting_ref_no'] ?? null,
                        'pre_proc_conference' => $this->nullableDate($itemData['pre_proc_conference'] ?? null),
                        'ads_post_ib' => $this->nullableDate($itemData['ads_post_ib'] ?? null),
                        'list_invited_observers' => $itemData['list_invited_observers'] ?? null,
                        'obsrvr_prebid_conf' => $this->nullableDate($itemData['obsrvr_prebid_conf'] ?? null),
                        'obsrvr_eligibility' => $this->nullableDate($itemData['obsrvr_eligibility'] ?? null),
                        'obsrvr_sub_open_of_bid' => $this->nullableDate($itemData['obsrvr_sub_open_of_bid'] ?? null),
                        'obsrvr_bid' => $this->nullableDate($itemData['obsrvr_bid'] ?? null),
                        'obsrvr_post_qual' => $this->nullableDate($itemData['obsrvr_post_qual'] ?? null),
                        'pre_bid_conf' => $this->nullableDate($itemData['pre_bid_conf'] ?? null),
                        'eligibility_check' => $this->nullableDate($itemData['eligibility_check'] ?? null),
                        'sub_open_bids' => $this->nullableDate($itemData['sub_open_bids'] ?? null),
                        'bid_evaluation_date' => $this->nullableDate($itemData['bid_evaluation_date'] ?? null),
                        'post_qualification_date' => $this->nullableDate($itemData['post_qualification_date'] ?? null),
                        'bidding_date' => $this->nullableDate($itemData['bidding_date'] ?? null),
                        'bidding_result' => $itemData['bidding_result'] ?? null,
                        'resolution_number_mop' => $itemData['resolution_number_mop'] ?? null,
                    ]
                );
                $checkStatus($model);
            }
        }

        if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
            $svpFields = [
                $itemData['philgeps_posting_ref_no'] ?? null,
                $itemData['ads_post_ib'] ?? null,
                $itemData['resolution_number_mop'] ?? null,
                $itemData['rfq_no'] ?? null,
                $itemData['canvass_date'] ?? null,
                $itemData['date_returned_of_canvass'] ?? null,
                $itemData['abstract_of_canvass_date'] ?? null,
            ];

            $hasSvpData = $this->hasAnyValue($svpFields);
            $existingSvp = PrSvp::where($matchCriteria)->first();

            if ($hasSvpData || $existingSvp) {
                if (!$hasSvpData && $existingSvp) {
                    return;
                }

                if (!$existingSvp) {
                    $relatedMopUids = MopItem::where('prItemID', $refId)
                        ->where('mode_of_procurement_id', $modeId)
                        ->pluck('uid');

                    $count = PrSvp::where('ref_id', $refId)
                        ->whereIn('mop_uid', $relatedMopUids)
                        ->count();

                    $uid = $parentUid . '-' . ($count + 1);
                } else {
                    $uid = $existingSvp->uid;
                }

                $model = PrSvp::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $uid,
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,
                        'philgeps_posting_ref_no' => $itemData['philgeps_posting_ref_no'] ?? null,
                        'ads_post_ib' => $this->nullableDate($itemData['ads_post_ib'] ?? null),
                        'resolution_number_mop' => $itemData['resolution_number_mop'] ?? null,
                        'rfq_no' => $itemData['rfq_no'] ?? null,
                        'canvass_date' => $this->nullableDate($itemData['canvass_date'] ?? null),
                        'date_returned_of_canvass' => $this->nullableDate($itemData['date_returned_of_canvass'] ?? null),
                        'abstract_of_canvass_date' => $this->nullableDate($itemData['abstract_of_canvass_date'] ?? null),
                    ]
                );
                $checkStatus($model);
            }
        }
    }

    public function cancel()
    {
        return redirect()->route('mode-of-procurement.index', $this->queryParams);
    }
    public function getIsPostActiveProperty(): bool
    {
        // Check if any post-procurement records exist for the items
        $prItemIds = collect($this->form['items'])->pluck('prItemID')->filter()->unique();

        if ($prItemIds->isEmpty()) {
            return false;
        }

        return PostProcurement::whereIn('ref_id', $prItemIds)->exists();
    }
    public function savePost()
    {
        // Filter items that meet post-procurement criteria
        $postAvailableItems = array_filter($this->form['items'] ?? [], function ($item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;

            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $bidResult = $item['bidding_result'] ?? '';
                if ($bidResult === 'SUCCESSFUL') {
                    return true;
                }
            }

            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                if (
                    !empty($item['resolution_number_mop']) &&
                    !empty($item['rfq_no']) &&
                    !empty($item['canvass_date']) &&
                    !empty($item['date_returned_of_canvass']) &&
                    !empty($item['abstract_of_canvass_date'])
                ) {
                    return true;
                }
            }

            return false;
        });

        // Build validation rules with custom messages
        $rules = [];
        $messages = [];
        $attributes = [];

        foreach ($this->postItems as $prItemID => $postItem) {
            $item = collect($this->form['items'])->firstWhere('prItemID', $prItemID);

            if (!$item) {
                continue;
            }

            $itemNumber = $item['item_no'] ?? 'Unknown';
            $itemDesc = $item['description'] ?? '';
            $shortDesc = strlen($itemDesc) > 40 ? substr($itemDesc, 0, 40) . '...' : $itemDesc;

            // Check if this item has any data
            $postFields = [
                $postItem['resolutionAwardNumber'] ?? null,
                $postItem['resolutionAwardDate'] ?? null,
                $postItem['noticeOfAwardNumber'] ?? null,
                $postItem['noticeOfAward'] ?? null,
                $postItem['awardedAmount'] ?? null,
                $postItem['philgepsNoticeOfAwardNo'] ?? null,
                $postItem['philgepsPostingOfAward'] ?? null,
                $postItem['supplier_id'] ?? null,
            ];

            $hasData = $this->hasAnyValue($postFields);
            !empty($postItem['supplier_id']);

            if ($hasData) {
                $rules["postItems.{$prItemID}.resolutionAwardNumber"] = 'required|string|max:255';
                $rules["postItems.{$prItemID}.resolutionAwardDate"] = 'nullable|date';
                $rules["postItems.{$prItemID}.noticeOfAwardNumber"] = 'nullable|string|max:255';
                $rules["postItems.{$prItemID}.noticeOfAward"] = 'nullable|date';
                $rules["postItems.{$prItemID}.awardedAmount"] = 'nullable|numeric|min:0';
                $rules["postItems.{$prItemID}.philgepsNoticeOfAwardNo"] = 'nullable|string|max:255';
                $rules["postItems.{$prItemID}.philgepsPostingOfAward"] = 'nullable|date';
                $rules["postItems.{$prItemID}.supplier_id"] = 'nullable|integer|exists:suppliers,id';



                // Custom messages
                $messages["postItems.{$prItemID}.resolutionAwardNumber.required"] =
                    "<strong>Item {$itemNumber}</strong> ({$shortDesc}): Resolution Award Number is required.";
                $messages["postItems.{$prItemID}.awardedAmount.numeric"] =
                    "<strong>Item {$itemNumber}</strong> ({$shortDesc}): Awarded Amount must be a valid number.";
                $messages["postItems.{$prItemID}.awardedAmount.min"] =
                    "<strong>Item {$itemNumber}</strong> ({$shortDesc}): Awarded Amount cannot be negative.";
                $messages["postItems.{$prItemID}.supplier_id.exists"] =
                    "<strong>Item {$itemNumber}</strong> ({$shortDesc}): Selected supplier is invalid.";


                // Date validation messages
                foreach (['resolutionAwardDate', 'noticeOfAward', 'dateOfPostingOfAwardOnPhilGEPS'] as $dateField) {
                    $fieldLabel = ucwords(str_replace(['_', 'Date', 'Of'], [' ', '', 'of'], $dateField));
                    $messages["postItems.{$prItemID}.{$dateField}.date"] =
                        "<strong>Item {$itemNumber}</strong> ({$shortDesc}): {$fieldLabel} must be a valid date.";
                }

                // Attributes for better error display
                $attributes["postItems.{$prItemID}.resolutionAwardNumber"] = "Item {$itemNumber} - Resolution Award #";
                $attributes["postItems.{$prItemID}.awardedAmount"] = "Item {$itemNumber} - Awarded Amount";
                $attributes["postItems.{$prItemID}.supplier_id"] = "Item {$itemNumber} - Supplier";
            }
        }

        if (empty($rules)) {
            LivewireAlert::title('No Changes')
                ->info()
                ->text('No post-procurement data to save.')
                ->toast()->position('top-end')->show();
            return;
        }

        try {
            $this->validate($rules, $messages, $attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();

            LivewireAlert::title('Post-Procurement Validation Failed')
                ->error()
                ->text($this->formatValidationErrors($errorMessages))
                ->toast()
                ->position('top-end')
                ->show();

            throw $e;
        }

        $isAdded = false;
        $isUpdated = false;

        DB::transaction(function () use (&$isAdded, &$isUpdated) {
            // Loop through postItems (which contains the form data)
            foreach ($this->postItems as $prItemID => $postItem) {
                // Get the corresponding form item
                $item = collect($this->form['items'])->firstWhere('prItemID', $prItemID);

                // Skip if item doesn't exist or doesn't have a mode
                if (!$item || empty($item['mode_of_procurement_id'])) {
                    continue;
                }

                // Check if this item has any data
                $postFields = [
                    $postItem['resolutionAwardNumber'] ?? null,
                    $postItem['resolutionAwardDate'] ?? null,
                    $postItem['noticeOfAwardNumber'] ?? null,
                    $postItem['noticeOfAward'] ?? null,
                    $postItem['awardedAmount'] ?? null,
                    $postItem['awardNoticeNumber'] ?? null,
                    $postItem['dateOfPostingOfAwardOnPhilGEPS'] ?? null,
                    $postItem['supplier_id'] ?? null,
                ];

                $hasData = $this->hasAnyValue($postFields);

                if (!$hasData) {
                    continue;
                }

                $data = [
                    'ref_id' => $prItemID,
                    'resolution_award_number' => $postItem['resolutionAwardNumber'],
                    'resolution_award_date' => $this->nullableDate($postItem['resolutionAwardDate'] ?? null),
                    'notice_of_award_number' => $postItem['noticeOfAwardNumber'] ?? null,
                    'notice_of_award' => $this->nullableDate($postItem['noticeOfAward'] ?? null),
                    'awarded_amount' => $postItem['awardedAmount'] ?? null,
                    'philgeps_notice_of_award_no' => $postItem['philgepsNoticeOfAwardNo'] ?? null,
                    'philgeps_posting_of_award' => $this->nullableDate($postItem['philgepsPostingOfAward'] ?? null),
                    'supplier_id' => $postItem['supplier_id'] ?? null,
                ];

                // Use ref_id as prItemID (unique per item)
                $postModel = PostProcurement::updateOrCreate(
                    ['ref_id' => $prItemID],
                    $data
                );

                if ($postModel->wasRecentlyCreated) {
                    $isAdded = true;
                } elseif ($postModel->wasChanged()) {
                    $isUpdated = true;
                }
            }
        });

        if ($isAdded) {
            LivewireAlert::title('Post-Procurement Added!')
                ->success()
                ->text('The procurement award details have been saved.')
                ->toast()->position('top-end')->show();
        } elseif ($isUpdated) {
            LivewireAlert::title('Post-Procurement Updated!')
                ->success()
                ->text('The procurement award details have been updated.')
                ->toast()->position('top-end')->show();
        } else {
            LivewireAlert::title('No Changes')
                ->info()
                ->text('Post-procurement details remain unchanged.')
                ->toast()->position('top-end')->show();
        }

        // Reload data to refresh postItems
        $this->mount($this->procurement);
    }
    public function getHistoryItemsProperty()
    {
        if (!$this->showHistory || !$this->historyForPrItemId) {
            return collect();
        }

        // Get all items for this prItemID except the first (current) one
        return collect($this->form['items'])
            ->filter(function ($item) {
                return ($item['prItemID'] ?? null) === $this->historyForPrItemId;
            })
            ->skip(1); // Skip the first item (current record)
    }
    public function hasPostDataForItem($itemIndex): bool
    {
        $item = $this->form['items'][$itemIndex] ?? null;
        if (!$item)
            return false;

        $prItemID = $item['prItemID'] ?? null;
        if (!$prItemID)
            return false;

        // Check if post data exists in the postItems array for this prItemID
        return isset($this->postItems[$prItemID]) &&
            !empty(array_filter($this->postItems[$prItemID] ?? []));
    }

    public function save(): void
    {
        if ($this->activeTab == 2) {
            $this->savePost();
            return;
        } else {
            $this->saveTab1();
            $this->mount($this->procurement);
        }
    }

    private function nullableDate($value): ?string
    {
        return empty($value) ? null : $value;
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-per-item-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'suppliers' => $this->suppliers,
        ]);
    }
}
