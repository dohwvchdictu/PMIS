<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\BidSchedule;
use App\Models\ModeOfProcurement;
use App\Models\NtfBidSchedule;
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
    public ?string $resolutionNumber = null;
    public ?string $bidEvaluationDate = null;
    public ?string $postQualDate = null;
    public ?string $noticeOfAward = null;
    public ?string $recommendingForAward = null;
    public ?float $awardedAmount = null;
    public ?string $philgepsReferenceNo = null;
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

    private function loadPostProcurementData(Procurement $procurement): void
    {
        // Initialize postItems array for each eligible item
        $this->postItems = [];

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            $prItemID = $item['prItemID'] ?? null;

            // Check if item is eligible for post-procurement
            $isEligible = false;

            if (in_array($modeId, [2, 3, 4])) {
                $bidResult = $item['bidding_result'] ?? '';
                $ntfResult = $item['ntf_bidding_result'] ?? '';

                if ($bidResult === 'SUCCESSFUL' || $ntfResult === 'SUCCESSFUL') {
                    $isEligible = true;
                }
            }

            if ($modeId == 5) {
                if (
                    !empty($item['rfq_no']) &&
                    !empty($item['canvass_date']) &&
                    !empty($item['date_returned_of_canvass']) &&
                    !empty($item['abstract_of_canvass_date']) &&
                    !empty($item['resolution_number'])
                ) {
                    $isEligible = true;
                }
            }

            if ($isEligible && $prItemID) {
                // Load post-procurement data for this prItemID (stored as ref_id)
                $post = PostProcurement::where('ref_id', $prItemID)->first();

                $this->postItems[$index] = [
                    'resolutionNumber' => $post?->resolution_number ?? null,
                    'bidEvaluationDate' => $post?->bid_evaluation_date ?? null,
                    'postQualDate' => $post?->post_qual_date ?? null,
                    'noticeOfAward' => $post?->notice_of_award ?? null,
                    'recommendingForAward' => $post?->recommending_for_award ?? null,
                    'awardedAmount' => $post?->awarded_amount ?? null,
                    'philgepsReferenceNo' => $post?->philgeps_reference_no ?? null,
                    'awardNoticeNumber' => $post?->award_notice_no ?? null,
                    'dateOfPostingOfAwardOnPhilGEPS' => $post?->date_of_posting_of_award_on_philgeps ?? null,
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

            if (in_array($modeId, [2, 3, 4])) {
                $bidResult = $item['bidding_result'] ?? '';
                $ntfResult = $item['ntf_bidding_result'] ?? '';

                if ($bidResult === 'SUCCESSFUL' || $ntfResult === 'SUCCESSFUL') {
                    return true;
                }
            }

            // Check SVP for all required fields
            if ($modeId == 5) {
                if (
                    !empty($item['rfq_no']) &&
                    !empty($item['canvass_date']) &&
                    !empty($item['date_returned_of_canvass']) &&
                    !empty($item['abstract_of_canvass_date']) &&
                    !empty($item['resolution_number'])
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
        $ntfSchedules = NtfBidSchedule::whereIn('ref_id', $prItemIds)->get();
        $prSvps = PrSvp::whereIn('ref_id', $prItemIds)->get();

        // Build unified schedule map keyed by prItemID and mop_uid
        $scheduleMap = $this->buildScheduleMap($bidSchedules, $ntfSchedules, $prSvps);

        $this->form['items'] = [];

        // Loop through PR Items
        $sortedPrItems = $procurement->pr_items->sortBy('prItemID');

        foreach ($sortedPrItems as $prItem) {
            $prItemID = $prItem->prItemID;
            $relatedMops = $mopItemsGrouped->get($prItemID);

            if ($relatedMops && $relatedMops->count() > 0) {
                foreach ($relatedMops as $mopItem) {
                    $uid = $mopItem->uid;
                    // FIX: Check if prItemID exists in scheduleMap and get the schedule
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

    private function buildScheduleMap(
        Collection $bidSchedules,
        Collection $ntfSchedules,
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
                'pre_proc_conference' => $schedule->pre_proc_conference,
                'ads_post_ib' => $schedule->ads_post_ib,
                'pre_bid_conf' => $schedule->pre_bid_conf,
                'eligibility_check' => $schedule->eligibility_check,
                'sub_open_bids' => $schedule->sub_open_bids,
                'bidding_number' => $schedule->bidding_number,
                'bidding_date' => $schedule->bidding_date,
                'bidding_result' => $schedule->bidding_result,
            ];
        }

        // Merge NtfBidSchedule data
        foreach ($ntfSchedules as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $mopUid = $schedule->mop_uid;
            $existing = $map[$refId]->get($mopUid, []);

            $map[$refId][$mopUid] = array_merge($existing, [
                'mop_uid' => $schedule->mop_uid,
                'ib_number' => $schedule->ib_number ?? $existing['ib_number'] ?? null,
                'pre_proc_conference' => $schedule->pre_proc_conference ?? $existing['pre_proc_conference'] ?? null,
                'ads_post_ib' => $schedule->ads_post_ib ?? $existing['ads_post_ib'] ?? null,
                'pre_bid_conf' => $schedule->pre_bid_conf ?? $existing['pre_bid_conf'] ?? null,
                'eligibility_check' => $schedule->eligibility_check ?? $existing['eligibility_check'] ?? null,
                'sub_open_bids' => $schedule->sub_open_bids ?? $existing['sub_open_bids'] ?? null,
                'bidding_number' => $schedule->bidding_number ?? $existing['bidding_number'] ?? null,
                'ntf_no' => $schedule->ntf_no,
                'ntf_bidding_date' => $schedule->ntf_bidding_date,
                'ntf_bidding_result' => $schedule->ntf_bidding_result,
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
            ]);
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
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
                'resolution_number' => $schedule->resolution_number,
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

            // All schedule fields from unified map
            'ib_number' => $schedule['ib_number'] ?? null,
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
            'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
            'eligibility_check' => $schedule['eligibility_check'] ?? null,
            'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
            'bidding_number' => $schedule['bidding_number'] ?? null,
            'bidding_date' => $schedule['bidding_date'] ?? null,
            'bidding_result' => $schedule['bidding_result'] ?? null,
            'ntf_no' => $schedule['ntf_no'] ?? null,
            'ntf_bidding_date' => $schedule['ntf_bidding_date'] ?? null,
            'ntf_bidding_result' => $schedule['ntf_bidding_result'] ?? null,
            'rfq_no' => $schedule['rfq_no'] ?? null,
            'canvass_date' => $schedule['canvass_date'] ?? null,
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
            'resolution_number' => $schedule['resolution_number'] ?? null,
        ];
    }

    public function addItem($index): void
    {
        // 1. Identify the context (Which PR Item are we adding to?)
        $referenceItem = $this->form['items'][$index] ?? null;

        if (!$referenceItem)
            return;

        $uniqueId = 'new_' . md5(microtime(true) . mt_rand());

        // 2. Create new item (Copy details from reference, but clear MOP data)
        $newItem = [
            'id' => null,  // ✅ ADD THIS - Must be null for new items
            'prItemID' => $referenceItem['prItemID'], // KEEP THIS LINK
            'item_no' => $referenceItem['item_no'],
            'description' => $referenceItem['description'],
            'amount' => $referenceItem['amount'],
            'uid' => $uniqueId,

            // Reset MOP fields
            'mode_of_procurement_id' => null,
            'mode_order' => ($referenceItem['mode_order'] ?? 0) + 1, // Increment order

            // ✅ ADD ALL THESE FIELDS AS NULL
            // --- SHARED ---
            'ib_number' => null,
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,

            // --- BID ---
            'bidding_number' => null,
            'bidding_date' => null,
            'bidding_result' => null,

            // --- NTF ---
            'ntf_no' => null,
            'ntf_bidding_date' => null,
            'ntf_bidding_result' => null,

            // --- SVP ---
            'rfq_no' => null,
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
            'resolution_number' => null,
        ];

        // 3. Insert ABOVE the clicked index
        array_splice($this->form['items'], $index, 0, [$newItem]);

        // 4. Ensure history is hidden so the UI doesn't look cluttered
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
                ->text('Unable to update record.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Update the item in the form array
        $this->form['items'][$this->editingIndex] = $this->editingItem;

        // Save to database
        $this->saveTab1();

        // Close modal
        $this->closeEditModal();

        LivewireAlert::title('History Updated')
            ->success()
            ->text('History record has been updated successfully.')
            ->toast()
            ->position('top-end')
            ->show();
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

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            if (!$modeId)
                continue;

            $prItemID = $item['prItemID'];
            $itemNumber = $item['item_no'] ?? ($index + 1);

            $matchCriteria = [
                'ref_id' => $prItemID,
                'mop_uid' => $item['uid']
            ];

            // Only validate if this item has been interacted with (has existing record or has data)
            $hasAnyData = false;

            // Check if item has any filled fields
            foreach ($item as $key => $value) {
                if (
                    !in_array($key, ['id', 'uid', 'mode_of_procurement_id', 'mode_order', 'prItemID', 'item_no', 'description', 'amount']) &&
                    !is_null($value) && trim($value) !== ''
                ) {
                    $hasAnyData = true;
                    break;
                }
            }

            // Skip validation for empty new items
            if (!$hasAnyData && str_starts_with($item['uid'], 'new_')) {
                continue;
            }

            // Validate Bidding Schedule (Modes 2, 3, 4)
            if (!in_array($modeId, [1, 5])) {
                $existingBidSchedule = BidSchedule::where($matchCriteria)->first();

                $hasBiddingData = !empty($item['ib_number']) ||
                    !empty($item['bidding_number']) ||
                    !empty($item['pre_proc_conference']) ||
                    !empty($item['ads_post_ib']) ||
                    !empty($item['pre_bid_conf']) ||
                    !empty($item['eligibility_check']) ||
                    !empty($item['sub_open_bids']) ||
                    !empty($item['bidding_date']) ||
                    !empty($item['bidding_result']);

                // Only validate existing records
                if ($existingBidSchedule && !$hasBiddingData) {
                    $this->scheduleValidationErrors[] = "At least one bidding schedule field must be filled.";
                    $isValid = false;
                }

                // Validate Bidding Result dependencies - only if bidding_result is actually selected
                $biddingResult = $item['bidding_result'] ?? null;
                if (!is_null($biddingResult) && trim($biddingResult) !== '') {
                    $missingFields = [];

                    if (empty($item['bidding_number']) || trim($item['bidding_number']) === '') {
                        $missingFields[] = 'Bidding #';
                    }
                    if (empty($item['ib_number']) || trim($item['ib_number']) === '') {
                        $missingFields[] = 'IB No.';
                    }
                    if (empty($item['bidding_date']) || trim($item['bidding_date']) === '') {
                        $missingFields[] = 'Bidding Date';
                    }

                    if (!empty($missingFields)) {
                        $fieldsList = implode(', ', $missingFields);
                        $this->scheduleValidationErrors[] = "Cannot set Bidding Result without {$fieldsList}.";
                        $isValid = false;
                    }
                }
            }

            // Validate NTF Schedule (Mode 4)
            if ($modeId == 4) {
                $existingNtfSchedule = NtfBidSchedule::where($matchCriteria)->first();

                $hasNtfData = !empty($item['ib_number']) ||
                    !empty($item['bidding_number']) ||
                    !empty($item['pre_proc_conference']) ||
                    !empty($item['ads_post_ib']) ||
                    !empty($item['pre_bid_conf']) ||
                    !empty($item['eligibility_check']) ||
                    !empty($item['sub_open_bids']) ||
                    !empty($item['ntf_no']) ||
                    !empty($item['ntf_bidding_date']) ||
                    !empty($item['ntf_bidding_result']) ||
                    !empty($item['rfq_no']) ||
                    !empty($item['canvass_date']) ||
                    !empty($item['date_returned_of_canvass']) ||
                    !empty($item['abstract_of_canvass_date']);

                if ($existingNtfSchedule && !$hasNtfData) {
                    $this->scheduleValidationErrors[] = "At least one NTF schedule field must be filled.";
                    $isValid = false;
                }

                // Validate NTF Bidding Result dependencies
                $ntfBiddingResult = $item['ntf_bidding_result'] ?? null;
                if (!is_null($ntfBiddingResult) && trim($ntfBiddingResult) !== '') {
                    $missingNtfFields = [];

                    if (empty($item['bidding_number']) || trim($item['bidding_number']) === '') {
                        $missingNtfFields[] = 'Bidding #';
                    }
                    if (empty($item['ib_number']) || trim($item['ib_number']) === '') {
                        $missingNtfFields[] = 'IB No.';
                    }
                    if (empty($item['ntf_bidding_date']) || trim($item['ntf_bidding_date']) === '') {
                        $missingNtfFields[] = 'NTF Bidding Date';
                    }

                    if (!empty($missingNtfFields)) {
                        $fieldsList = implode(', ', $missingNtfFields);
                        $this->scheduleValidationErrors[] = "Cannot set NTF Bidding Result without {$fieldsList}.";
                        $isValid = false;
                    }
                }
            }

            // Validate SVP (Mode 5)
            if ($modeId == 5) {
                $existingSvp = PrSvp::where($matchCriteria)->first();

                $hasSvpData = !empty($item['rfq_no']) ||
                    !empty($item['canvass_date']) ||
                    !empty($item['date_returned_of_canvass']) ||
                    !empty($item['abstract_of_canvass_date']) ||
                    !empty($item['resolution_number']);

                if ($existingSvp && !$hasSvpData) {
                    $this->scheduleValidationErrors[] = "At least one SVP field must be filled.";
                    $isValid = false;
                }
            }
        }

        return $isValid;
    }
    public function saveTab1()
    {
        $rules = [
            'form.items.*.mode_of_procurement_id' => 'required|integer',
        ];

        $attributes = [];

        try {
            $this->validate($rules, [], $attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();
            $errorString = ' ';
            foreach ($errorMessages as $msg) {
                $errorString .= "{$msg}";
            }

            LivewireAlert::title('Validation Failed')
                ->error()
                ->text('Please check the following errors:' . $errorString)
                ->toast()->position('top-end')->show();

            throw $e;
        }

        // Validate schedules before saving
        $this->scheduleValidationErrors = [];
        if (!$this->validateSchedules()) {
            $errorMessage = implode(' ', $this->scheduleValidationErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()->position('top-end')->show();
            return;
        }

        $isMopAdded = false;
        $isMopUpdated = false;
        $isScheduleAdded = false;
        $isScheduleUpdated = false;
        $isDeleted = false;

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
        } elseif ($isMopUpdated || $isScheduleUpdated || $isDeleted) {
            LivewireAlert::title('Updates Saved!')->success()->text('Changes have been saved successfully.')->toast()->position('top-end')->show();
        } else {
            LivewireAlert::title('No Changes')->info()->text('No changes were detected.')->toast()->position('top-end')->show();
        }

        $this->mount($this->procurement);
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

        $getIdentity = function ($modelClass) use ($matchCriteria, $parentUid, $refId, $modeId) {
            $existing = $modelClass::where($matchCriteria)->first();
            if ($existing) {
                return ['uid' => $existing->uid];
            } else {
                $relatedMopUids = MopItem::where('prItemID', $refId)
                    ->where('mode_of_procurement_id', $modeId)
                    ->pluck('uid');

                $count = $modelClass::where('ref_id', $refId)
                    ->whereIn('mop_uid', $relatedMopUids)
                    ->count();

                return ['uid' => $parentUid . '-' . ($count + 1)];
            }
        };

        $checkStatus = function ($model) use (&$isScheduleAdded, &$isScheduleUpdated) {
            if ($model->wasRecentlyCreated) {
                $isScheduleAdded = true;
            } elseif ($model->wasChanged()) {
                $isScheduleUpdated = true;
            }
        };

        // BIDDING SCHEDULE (Modes 2, 3, 4)
        if (!in_array($modeId, [1, 5])) {
            $hasBiddingData = !empty($itemData['ib_number']) ||
                !empty($itemData['bidding_number']) ||
                !empty($itemData['pre_proc_conference']) ||
                !empty($itemData['ads_post_ib']) ||
                !empty($itemData['pre_bid_conf']) ||
                !empty($itemData['eligibility_check']) ||
                !empty($itemData['sub_open_bids']) ||
                !empty($itemData['bidding_date']) ||
                !empty($itemData['bidding_result']);

            $existingBidSchedule = BidSchedule::where($matchCriteria)->first();

            if ($hasBiddingData || $existingBidSchedule) {
                // Skip if all fields are empty
                if (!$hasBiddingData && $existingBidSchedule) {
                    return;
                }

                $identity = $getIdentity(BidSchedule::class);

                $model = BidSchedule::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $identity['uid'],
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,
                        'bidding_number' => $itemData['bidding_number'] ?? null,
                        'ib_number' => $itemData['ib_number'] ?? null,
                        'pre_proc_conference' => $this->nullableDate($itemData['pre_proc_conference'] ?? null),
                        'ads_post_ib' => $this->nullableDate($itemData['ads_post_ib'] ?? null),
                        'pre_bid_conf' => $this->nullableDate($itemData['pre_bid_conf'] ?? null),
                        'eligibility_check' => $this->nullableDate($itemData['eligibility_check'] ?? null),
                        'sub_open_bids' => $this->nullableDate($itemData['sub_open_bids'] ?? null),
                        'bidding_date' => $this->nullableDate($itemData['bidding_date'] ?? null),
                        'bidding_result' => $itemData['bidding_result'] ?? null,
                    ]
                );
                $checkStatus($model);
            }
        }

        // NTF BID SCHEDULE (Mode 4)
        if ($modeId == 4) {
            $hasNtfData = !empty($itemData['ib_number']) ||
                !empty($itemData['bidding_number']) ||
                !empty($itemData['pre_proc_conference']) ||
                !empty($itemData['ads_post_ib']) ||
                !empty($itemData['pre_bid_conf']) ||
                !empty($itemData['eligibility_check']) ||
                !empty($itemData['sub_open_bids']) ||
                !empty($itemData['ntf_no']) ||
                !empty($itemData['ntf_bidding_date']) ||
                !empty($itemData['ntf_bidding_result']) ||
                !empty($itemData['rfq_no']) ||
                !empty($itemData['canvass_date']) ||
                !empty($itemData['date_returned_of_canvass']) ||
                !empty($itemData['abstract_of_canvass_date']);

            $existingNtfSchedule = NtfBidSchedule::where($matchCriteria)->first();

            if ($hasNtfData || $existingNtfSchedule) {
                if (!$hasNtfData && $existingNtfSchedule) {
                    return;
                }

                $identity = $getIdentity(NtfBidSchedule::class);

                $model = NtfBidSchedule::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $identity['uid'],
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,
                        'ib_number' => $itemData['ib_number'] ?? null,
                        'pre_proc_conference' => $this->nullableDate($itemData['pre_proc_conference'] ?? null),
                        'ads_post_ib' => $this->nullableDate($itemData['ads_post_ib'] ?? null),
                        'pre_bid_conf' => $this->nullableDate($itemData['pre_bid_conf'] ?? null),
                        'eligibility_check' => $this->nullableDate($itemData['eligibility_check'] ?? null),
                        'sub_open_bids' => $this->nullableDate($itemData['sub_open_bids'] ?? null),
                        'bidding_number' => $itemData['bidding_number'] ?? null,
                        'ntf_no' => $itemData['ntf_no'] ?? null,
                        'ntf_bidding_date' => $this->nullableDate($itemData['ntf_bidding_date'] ?? null),
                        'ntf_bidding_result' => $itemData['ntf_bidding_result'] ?? null,
                        'rfq_no' => $itemData['rfq_no'] ?? null,
                        'canvass_date' => $this->nullableDate($itemData['canvass_date'] ?? null),
                        'date_returned_of_canvass' => $this->nullableDate($itemData['date_returned_of_canvass'] ?? null),
                        'abstract_of_canvass_date' => $this->nullableDate($itemData['abstract_of_canvass_date'] ?? null),
                    ]
                );
                $checkStatus($model);
            }
        }

        // PR SVP (Mode 5)
        if ($modeId == 5) {
            $hasSvpData = !empty($itemData['rfq_no']) ||
                !empty($itemData['canvass_date']) ||
                !empty($itemData['date_returned_of_canvass']) ||
                !empty($itemData['abstract_of_canvass_date']) ||
                !empty($itemData['resolution_number']);

            $existingSvp = PrSvp::where($matchCriteria)->first();

            if ($hasSvpData || $existingSvp) {
                if (!$hasSvpData && $existingSvp) {
                    return;
                }

                $identity = $getIdentity(PrSvp::class);

                $model = PrSvp::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $identity['uid'],
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,
                        'rfq_no' => $itemData['rfq_no'] ?? null,
                        'canvass_date' => $this->nullableDate($itemData['canvass_date'] ?? null),
                        'date_returned_of_canvass' => $this->nullableDate($itemData['date_returned_of_canvass'] ?? null),
                        'abstract_of_canvass_date' => $this->nullableDate($itemData['abstract_of_canvass_date'] ?? null),
                        'resolution_number' => $itemData['resolution_number'] ?? null,
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

            if (in_array($modeId, [2, 3, 4])) {
                $bidResult = $item['bidding_result'] ?? '';
                $ntfResult = $item['ntf_bidding_result'] ?? '';

                if ($bidResult === 'SUCCESSFUL' || $ntfResult === 'SUCCESSFUL') {
                    return true;
                }
            }

            if ($modeId == 5) {
                if (
                    !empty($item['rfq_no']) &&
                    !empty($item['canvass_date']) &&
                    !empty($item['date_returned_of_canvass']) &&
                    !empty($item['abstract_of_canvass_date']) &&
                    !empty($item['resolution_number'])
                ) {
                    return true;
                }
            }

            return false;
        });

        if (empty($postAvailableItems)) {
            LivewireAlert::title('No Items Available')
                ->info()
                ->text('No items are eligible for post-procurement entry.')
                ->toast()->position('top-end')->show();
            return;
        }

        // Build validation rules - only validate non-empty items
        $rules = [];
        $attributes = [];

        foreach ($this->postItems as $index => $postItem) {
            $item = $this->form['items'][$index] ?? null;

            if (!$item) {
                continue;
            }

            // Check if this item has any data
            $hasData = !empty($postItem['resolutionNumber']) ||
                !empty($postItem['bidEvaluationDate']) ||
                !empty($postItem['postQualDate']) ||
                !empty($postItem['recommendingForAward']) ||
                !empty($postItem['noticeOfAward']) ||
                !empty($postItem['awardedAmount']) ||
                !empty($postItem['philgepsReferenceNo']) ||
                !empty($postItem['awardNoticeNumber']) ||
                !empty($postItem['dateOfPostingOfAwardOnPhilGEPS']) ||
                !empty($postItem['supplier_id']);

            // Only validate if this item has data
            if ($hasData) {
                $rules["postItems.{$index}.resolutionNumber"] = 'required|string|max:255';
                $rules["postItems.{$index}.bidEvaluationDate"] = 'nullable|date';
                $rules["postItems.{$index}.postQualDate"] = 'nullable|date';
                $rules["postItems.{$index}.recommendingForAward"] = 'nullable|date';
                $rules["postItems.{$index}.noticeOfAward"] = 'nullable|date';
                $rules["postItems.{$index}.awardedAmount"] = 'nullable|numeric';
                $rules["postItems.{$index}.philgepsReferenceNo"] = 'nullable|string|max:255';
                $rules["postItems.{$index}.awardNoticeNumber"] = 'nullable|string|max:255';
                $rules["postItems.{$index}.dateOfPostingOfAwardOnPhilGEPS"] = 'nullable|date';
                $rules["postItems.{$index}.supplier_id"] = 'nullable|integer|exists:suppliers,id';

                $attributes["postItems.{$index}.resolutionNumber"] = "Item {$this->form['items'][$index]['item_no']} - Resolution #";
                $attributes["postItems.{$index}.bidEvaluationDate"] = "Item {$this->form['items'][$index]['item_no']} - Bid Evaluation Date";
                $attributes["postItems.{$index}.postQualDate"] = "Item {$this->form['items'][$index]['item_no']} - Post Qual Date";
                $attributes["postItems.{$index}.recommendingForAward"] = "Item {$this->form['items'][$index]['item_no']} - Recommending For Award";
                $attributes["postItems.{$index}.noticeOfAward"] = "Item {$this->form['items'][$index]['item_no']} - Notice of Award";
                $attributes["postItems.{$index}.awardedAmount"] = "Item {$this->form['items'][$index]['item_no']} - Awarded Amount";
                $attributes["postItems.{$index}.philgepsReferenceNo"] = "Item {$this->form['items'][$index]['item_no']} - PhilGEPS Reference #";
                $attributes["postItems.{$index}.awardNoticeNumber"] = "Item {$this->form['items'][$index]['item_no']} - Award Notice #";
                $attributes["postItems.{$index}.dateOfPostingOfAwardOnPhilGEPS"] = "Item {$this->form['items'][$index]['item_no']} - Posting of Award Date";
                $attributes["postItems.{$index}.supplier_id"] = "Item {$this->form['items'][$index]['item_no']} - Supplier";
            }
        }

        // If no rules were added, no data to save
        if (empty($rules)) {
            LivewireAlert::title('No Changes')
                ->info()
                ->text('No post-procurement data to save.')
                ->toast()->position('top-end')->show();
            return;
        }

        try {
            $this->validate($rules, [], $attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();
            $errorString = ' ' . implode(' ', $errorMessages);

            LivewireAlert::title('Validation Failed')
                ->error()
                ->text('Please check the following errors:' . $errorString)
                ->toast()->position('top-end')->show();

            throw $e;
        }

        $isAdded = false;
        $isUpdated = false;

        DB::transaction(function () use (&$isAdded, &$isUpdated) {
            foreach ($this->postItems as $index => $postItem) {
                $item = $this->form['items'][$index] ?? null;

                // Skip if item doesn't exist or doesn't have a mode
                if (!$item || empty($item['mode_of_procurement_id'])) {
                    continue;
                }

                $prItemID = $item['prItemID'] ?? null;

                // Skip if no prItemID
                if (!$prItemID) {
                    continue;
                }

                // Check if post item has any data
                $hasData = !empty($postItem['resolutionNumber']) ||
                    !empty($postItem['bidEvaluationDate']) ||
                    !empty($postItem['postQualDate']) ||
                    !empty($postItem['recommendingForAward']) ||
                    !empty($postItem['noticeOfAward']) ||
                    !empty($postItem['awardedAmount']) ||
                    !empty($postItem['philgepsReferenceNo']) ||
                    !empty($postItem['awardNoticeNumber']) ||
                    !empty($postItem['dateOfPostingOfAwardOnPhilGEPS']) ||
                    !empty($postItem['supplier_id']);

                if (!$hasData) {
                    continue;
                }

                $data = [
                    'ref_id' => $prItemID,
                    'resolution_number' => $postItem['resolutionNumber'],
                    'bid_evaluation_date' => $this->nullableDate($postItem['bidEvaluationDate'] ?? null),
                    'post_qual_date' => $this->nullableDate($postItem['postQualDate'] ?? null),
                    'recommending_for_award' => $this->nullableDate($postItem['recommendingForAward'] ?? null),
                    'notice_of_award' => $this->nullableDate($postItem['noticeOfAward'] ?? null),
                    'awarded_amount' => $postItem['awardedAmount'] ?? null,
                    'philgeps_reference_no' => $postItem['philgepsReferenceNo'] ?? null,
                    'award_notice_no' => $postItem['awardNoticeNumber'] ?? null,
                    'date_of_posting_of_award_on_philgeps' => $this->nullableDate($postItem['dateOfPostingOfAwardOnPhilGEPS'] ?? null),
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
