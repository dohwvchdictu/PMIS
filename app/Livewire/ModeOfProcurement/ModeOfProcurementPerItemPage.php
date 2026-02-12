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
    // Constants for mode types
    const BIDDING_MODES = [2, 3, 4, 5, 6];
    const SVP_MODES = [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24];
    const ABC_THRESHOLD = 200000;
    const MODE_PENDING = 1;

    // Helper methods to eliminate magic numbers
    public function isCompetitiveBidding(?int $modeId): bool
    {
        return $modeId && in_array($modeId, self::BIDDING_MODES);
    }

    public function isSvpMode(?int $modeId): bool
    {
        return $modeId && in_array($modeId, self::SVP_MODES);
    }

    public function isPendingMode(?int $modeId): bool
    {
        return $modeId === self::MODE_PENDING;
    }

    public function meetsAbcThreshold(?float $amount): bool
    {
        return $amount && $amount >= self::ABC_THRESHOLD;
    }

    public function requiresPhilgeps(?int $modeId, ?float $amount): bool
    {
        return $this->isSvpMode($modeId) && $this->meetsAbcThreshold($amount);
    }

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

    // Bulk Edit Properties
    public bool $showBulkEditModal = false;
    public array $selectedItems = [];
    public array $bulkEditData = [];
    public array $bulkEditErrors = [];

    // Post Procurement Bulk Edit Properties
    public bool $showPostBulkEditModal = false;
    public array $selectedPostItems = [];
    public array $postBulkEditData = [];
    public array $postBulkEditErrors = [];

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

            if ($this->isCompetitiveBidding($modeId)) {
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

            // COMPETITIVE BIDDING MODES
            if ($this->isCompetitiveBidding($modeId)) {
                // Check all required bidding fields are filled
                $allBiddingFieldsFilled =
                    $this->hasValue($item['bidding_number']) &&
                    $this->hasValue($item['ib_number']) &&
                    $this->hasValue($item['philgeps_posting_ref_no']) &&
                    $this->hasValue($item['ads_post_ib']) &&
                    $this->hasValue($item['pre_proc_conference']) &&
                    $this->hasValue($item['list_invited_observers']) &&
                    $this->hasValue($item['obsrvr_prebid_conf']) &&
                    $this->hasValue($item['obsrvr_eligibility']) &&
                    $this->hasValue($item['obsrvr_sub_open_of_bid']) &&
                    $this->hasValue($item['obsrvr_bid']) &&
                    $this->hasValue($item['obsrvr_post_qual']) &&
                    $this->hasValue($item['pre_bid_conf']) &&
                    $this->hasValue($item['eligibility_check']) &&
                    $this->hasValue($item['sub_open_bids']) &&
                    $this->hasValue($item['bid_evaluation_date']) &&
                    $this->hasValue($item['post_qualification_date']) &&
                    $this->hasValue($item['sub_open_bids']) &&
                    $this->hasValue($item['bidding_result']) &&
                    ($item['bidding_result'] === 'SUCCESSFUL');

                // For modes 2-6, also require resolution_number_mop
                if (in_array($modeId, [2, 3, 4, 5, 6])) {
                    $allBiddingFieldsFilled = $allBiddingFieldsFilled && $this->hasValue($item['resolution_number_mop']);
                }

                if ($allBiddingFieldsFilled) {
                    return true;
                }
            }

            // SVP/ALTERNATIVE MODES (7-24)
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                // Base required SVP fields
                $allSvpFieldsFilled =
                    $this->hasValue($item['resolution_number_mop']) &&
                    $this->hasValue($item['rfq_no']) &&
                    $this->hasValue($item['canvass_date']) &&
                    $this->hasValue($item['date_returned_of_canvass']) &&
                    $this->hasValue($item['abstract_of_canvass_date']);

                // If amount >= 200k, also require philgeps_posting_ref_no and ads_post_ib
                $amount = (float) ($item['amount'] ?? 0);
                if ($amount >= 200000) {
                    $allSvpFieldsFilled = $allSvpFieldsFilled &&
                        $this->hasValue($item['philgeps_posting_ref_no']) &&
                        $this->hasValue($item['ads_post_ib']);
                }

                if ($allSvpFieldsFilled) {
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
                'ads_post_ib' => $schedule->ads_post_ib,
                'pre_proc_conference' => $schedule->pre_proc_conference,
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
            'bidding_number' => $schedule['bidding_number'] ?? null,
            'ib_number' => $schedule['ib_number'] ?? null,
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? null,
            'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
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
            'ads_post_ib' => null,
            'pre_proc_conference' => null,
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

            // Check if this item has any schedule data entered
            $biddingFields = [
                $item['ib_number'] ?? null,
                $item['philgeps_posting_ref_no'] ?? null,
                $item['ads_post_ib'] ?? null,
                $item['bidding_number'] ?? null,
                $item['pre_proc_conference'] ?? null,
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
                $item['bidding_result'] ?? null,
            ];

            $svpFields = [
                $item['rfq_no'] ?? null,
                $item['canvass_date'] ?? null,
                $item['date_returned_of_canvass'] ?? null,
                $item['abstract_of_canvass_date'] ?? null,
            ];

            $hasAnyBiddingData = $this->hasAnyValue($biddingFields);
            $hasAnySvpData = $this->hasAnyValue($svpFields);
            $hasResolutionNumber = $this->hasValue($item['resolution_number_mop'] ?? null);

            // Check if there are existing database records for this item
            $existingBidSchedule = null;
            $existingSvp = null;

            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $existingBidSchedule = BidSchedule::where($matchCriteria)->first();
            }

            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                $existingSvp = PrSvp::where($matchCriteria)->first();
            }

            // Skip validation if:
            // 1. No data has been entered for this item
            // 2. AND no existing database record exists
            $hasAnyData = $hasAnyBiddingData || $hasAnySvpData || $hasResolutionNumber;
            $hasExistingRecord = $existingBidSchedule || $existingSvp;

            if (!$hasAnyData && !$hasExistingRecord) {
                continue;
            }

            // COMPETITIVE BIDDING MODES (2, 3, 4, 5, 6)
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                // If there's an existing record but all data was removed, show error
                if ($existingBidSchedule && !$hasAnyBiddingData && !$hasResolutionNumber) {
                    $this->scheduleValidationErrors[] = sprintf(
                        "<strong>Item %s</strong> (%s): At least one bidding schedule field must be filled.",
                        $itemNumber,
                        $shortDesc
                    );
                    $isValid = false;
                    continue; // Skip further validation for this item
                }

                // Validate Bidding Result dependencies (only if bidding result is set)
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
                        if (!$this->hasValue($item['sub_open_bids'])) {
                            $missingFields[] = '<strong>Submission & Opening of Bids</strong>';
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
                // If there's an existing record but all data was removed, show error
                if ($existingSvp && !$hasAnySvpData && !$hasResolutionNumber) {
                    $this->scheduleValidationErrors[] = sprintf(
                        "<strong>Item %s</strong> (%s): At least one SVP field must be filled.",
                        $itemNumber,
                        $shortDesc
                    );
                    $isValid = false;
                    continue; // Skip further validation for this item
                }

                // No required field validation for SVP modes during save - all fields are optional
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
                        // MODE CHANGED: Update the existing record instead of creating new one for bulk edits
                        $existingRecord->update([
                            'mode_of_procurement_id' => $modeId,
                            // Update UID to reflect new mode
                            'uid' => "MOP-{$modeId}-{$existingRecord->mode_order}",
                        ]);

                        $isMopUpdated = true;

                        // Save related schedules for updated record
                        $this->saveRelatedSchedules(
                            $existingRecord,
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
            $biddingFields = [
                $itemData['ib_number'] ?? null,
                $itemData['philgeps_posting_ref_no'] ?? null,
                $itemData['ads_post_ib'] ?? null,
                $itemData['bidding_number'] ?? null,
                $itemData['pre_proc_conference'] ?? null,
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
                $itemData['bidding_result'] ?? null,
            ];

            $hasBiddingData = $this->hasAnyValue($biddingFields);

            // Add resolution_number_mop check for modes 2-6
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
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
                        'ads_post_ib' => $this->nullableDate($itemData['ads_post_ib'] ?? null),
                        'pre_proc_conference' => $this->nullableDate($itemData['pre_proc_conference'] ?? null),
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

    public function canAddNewModeForItem(array $item, ?int $modeId): bool
    {
        // SVP/Alternative modes cannot add new modes
        if ($this->isSvpMode($modeId)) {
            return false;
        }

        $bidResult = $item['bidding_result'] ?? '';

        $hasBiddingData = $this->hasValue($item['ib_number']) &&
            $this->hasValue($item['bidding_number']) &&
            $this->hasValue($item['sub_open_bids']);

        $hasPreProcConference = $this->hasValue($item['pre_proc_conference']);

        // Check if post data exists for THIS SPECIFIC item
        $prItemID = $item['prItemID'] ?? null;
        $hasPostDataForThisItem = $prItemID && isset($this->postItems[$prItemID]) &&
            !empty(array_filter($this->postItems[$prItemID] ?? []));

        return $this->isPendingMode($modeId) ||
            (($hasBiddingData || $hasPreProcConference) &&
                $bidResult === 'UNSUCCESSFUL' &&
                !$hasPostDataForThisItem);
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

    public function toggleItemSelection($index): void
    {
        if (in_array($index, $this->selectedItems)) {
            $this->selectedItems = array_values(array_diff($this->selectedItems, [$index]));
        } else {
            $this->selectedItems[] = $index;
        }
    }

    public function selectAll(): void
    {
        $this->selectedItems = [];
        foreach ($this->form['items'] as $index => $item) {
            // Only select items that are head records (not history)
            $currentPrID = $item['prItemID'] ?? null;
            $prevPrID = $this->form['items'][$index - 1]['prItemID'] ?? null;
            $isHead = $index === 0 || $currentPrID !== $prevPrID;

            if ($isHead) {
                $this->selectedItems[] = $index;
            }
        }
    }

    public function deselectAll(): void
    {
        $this->selectedItems = [];
    }

    public function openBulkEditModal(): void
    {
        if (empty($this->selectedItems)) {
            LivewireAlert::title('No Items Selected')
                ->warning()
                ->text('Please select at least one item to edit.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Validate selected items
        $validation = $this->validateBulkEditSelection();

        if (!$validation['valid']) {
            $this->bulkEditErrors = $validation['errors'];
            $errorMessage = implode(' ', $validation['errors']);
            LivewireAlert::title('Bulk Edit Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Initialize bulk edit data based on the common mode
        $firstItem = $this->form['items'][$this->selectedItems[0]];
        $modeId = $validation['commonMode'] ?? $firstItem['mode_of_procurement_id'] ?? 1;
        $amountThreshold = $validation['amountThreshold'];

        $this->bulkEditData = [
            'mode_of_procurement_id' => $modeId,
            'amount_threshold' => $amountThreshold,
            'items_count' => count($this->selectedItems),
            'item_numbers' => $validation['itemNumbers'],
        ];

        // Initialize fields based on mode type with existing data
        $this->initializeBulkEditFields($modeId, $firstItem, $amountThreshold);

        $this->bulkEditErrors = [];
        $this->showBulkEditModal = true;
    }

    public function updatedBulkEditDataModeOfProcurementId()
    {
        // When mode changes, reinitialize fields based on new mode
        $modeId = $this->bulkEditData['mode_of_procurement_id'];
        $firstItem = $this->form['items'][$this->selectedItems[0]];
        $amountThreshold = $this->bulkEditData['amount_threshold'];

        $this->initializeBulkEditFields($modeId, $firstItem, $amountThreshold);
    }

    private function initializeBulkEditFields($modeId, $firstItem, $amountThreshold)
    {
        // Clear existing mode-specific fields
        $this->bulkEditData = array_intersect_key($this->bulkEditData, array_flip([
            'mode_of_procurement_id',
            'amount_threshold',
            'items_count',
            'item_numbers'
        ]));

        // Initialize fields based on mode type with existing data
        if ($this->isCompetitiveBidding($modeId)) {
            $this->bulkEditData = array_merge($this->bulkEditData, [
                'bidding_number' => $firstItem['bidding_number'] ?? '',
                'ib_number' => $firstItem['ib_number'] ?? '',
                'philgeps_posting_ref_no' => $firstItem['philgeps_posting_ref_no'] ?? '',
                'ads_post_ib' => $firstItem['ads_post_ib'] ?? '',
                'pre_proc_conference' => $firstItem['pre_proc_conference'] ?? '',
                'list_invited_observers' => $firstItem['list_invited_observers'] ?? '',
                'obsrvr_prebid_conf' => $firstItem['obsrvr_prebid_conf'] ?? '',
                'obsrvr_eligibility' => $firstItem['obsrvr_eligibility'] ?? '',
                'obsrvr_sub_open_of_bid' => $firstItem['obsrvr_sub_open_of_bid'] ?? '',
                'obsrvr_bid' => $firstItem['obsrvr_bid'] ?? '',
                'obsrvr_post_qual' => $firstItem['obsrvr_post_qual'] ?? '',
                'pre_bid_conf' => $firstItem['pre_bid_conf'] ?? '',
                'eligibility_check' => $firstItem['eligibility_check'] ?? '',
                'sub_open_bids' => $firstItem['sub_open_bids'] ?? '',
                'bid_evaluation_date' => $firstItem['bid_evaluation_date'] ?? '',
                'post_qualification_date' => $firstItem['post_qualification_date'] ?? '',
                'bidding_result' => $firstItem['bidding_result'] ?? '',
                'resolution_number_mop' => $firstItem['resolution_number_mop'] ?? '',
            ]);
        } elseif ($this->isSvpMode($modeId)) {
            $this->bulkEditData = array_merge($this->bulkEditData, [
                'rfq_no' => $firstItem['rfq_no'] ?? '',
                'canvass_date' => $firstItem['canvass_date'] ?? '',
                'date_returned_of_canvass' => $firstItem['date_returned_of_canvass'] ?? '',
                'abstract_of_canvass_date' => $firstItem['abstract_of_canvass_date'] ?? '',
                'resolution_number_mop' => $firstItem['resolution_number_mop'] ?? '',
            ]);

            // Add PhilGEPS fields if threshold is >= 200k
            if ($amountThreshold === '>=200k') {
                $this->bulkEditData['philgeps_posting_ref_no'] = $firstItem['philgeps_posting_ref_no'] ?? '';
                $this->bulkEditData['ads_post_ib'] = $firstItem['ads_post_ib'] ?? '';
            }
        }
    }

    private function validateBulkEditSelection(): array
    {
        $errors = [];
        $itemNumbers = [];
        $modes = [];
        $amounts = [];
        $scheduleData = [];

        foreach ($this->selectedItems as $index) {
            $item = $this->form['items'][$index];
            $itemNumber = $item['item_no'];
            $modeId = $item['mode_of_procurement_id'] ?? null;
            $amount = (float) ($item['amount'] ?? 0);

            $itemNumbers[] = $itemNumber;

            if ($modeId) {
                $modes[$modeId][] = $itemNumber;
            }

            $amounts[$itemNumber] = $amount;

            // Collect schedule data for consistency check
            $schedule = [];
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                // Bidding schedule fields
                $schedule = [
                    'bidding_number' => $item['bidding_number'] ?? null,
                    'ib_number' => $item['ib_number'] ?? null,
                    'philgeps_posting_ref_no' => $item['philgeps_posting_ref_no'] ?? null,
                    'ads_post_ib' => $item['ads_post_ib'] ?? null,
                    'pre_proc_conference' => $item['pre_proc_conference'] ?? null,
                    'list_invited_observers' => $item['list_invited_observers'] ?? null,
                    'obsrvr_prebid_conf' => $item['obsrvr_prebid_conf'] ?? null,
                    'obsrvr_eligibility' => $item['obsrvr_eligibility'] ?? null,
                    'obsrvr_sub_open_of_bid' => $item['obsrvr_sub_open_of_bid'] ?? null,
                    'obsrvr_bid' => $item['obsrvr_bid'] ?? null,
                    'obsrvr_post_qual' => $item['obsrvr_post_qual'] ?? null,
                    'pre_bid_conf' => $item['pre_bid_conf'] ?? null,
                    'eligibility_check' => $item['eligibility_check'] ?? null,
                    'sub_open_bids' => $item['sub_open_bids'] ?? null,
                    'bid_evaluation_date' => $item['bid_evaluation_date'] ?? null,
                    'post_qualification_date' => $item['post_qualification_date'] ?? null,
                    'bidding_result' => $item['bidding_result'] ?? null,
                    'resolution_number_mop' => $item['resolution_number_mop'] ?? null,
                ];
            } elseif (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                // SVP schedule fields
                $schedule = [
                    'rfq_no' => $item['rfq_no'] ?? null,
                    'canvass_date' => $item['canvass_date'] ?? null,
                    'date_returned_of_canvass' => $item['date_returned_of_canvass'] ?? null,
                    'abstract_of_canvass_date' => $item['abstract_of_canvass_date'] ?? null,
                    'resolution_number_mop' => $item['resolution_number_mop'] ?? null,
                ];
            }

            $scheduleData[$index] = $schedule;
        }

        // Check if all items have the same mode
        if (count($modes) > 1) {
            $modeDetails = [];
            foreach ($modes as $modeId => $items) {
                $modeName = $this->modeOfProcurements->firstWhere('id', $modeId)?->modeofprocurements ?? 'Unknown';
                $modeDetails[] = "{$modeName}: Items " . implode(', ', $items);
            }
            $errors[] = "Items have different modes: " . implode('; ', $modeDetails) . ". Bulk edit requires all selected items to have the same mode of procurement.";
        }

        if (empty($modes)) {
            $errors[] = "No valid mode selected for items: " . implode(', ', $itemNumbers);
        }

        // Check if all items have identical schedule data (only when all have same mode)
        if (!empty($modes) && count($modes) === 1) {
            $scheduleHashes = [];
            $itemNumbersByHash = [];

            foreach ($this->selectedItems as $index) {
                $hash = md5(json_encode($scheduleData[$index]));
                $scheduleHashes[$index] = $hash;

                $itemNumber = $this->form['items'][$index]['item_no'];
                if (!isset($itemNumbersByHash[$hash])) {
                    $itemNumbersByHash[$hash] = [];
                }
                $itemNumbersByHash[$hash][] = $itemNumber;
            }

            $uniqueHashes = array_unique($scheduleHashes);

            if (count($uniqueHashes) > 1) {
                // Find the minority group (items with different data)
                $hashCounts = array_count_values($scheduleHashes);
                arsort($hashCounts);
                $majorityHash = array_key_first($hashCounts);

                $differentItems = [];
                foreach ($scheduleHashes as $index => $hash) {
                    if ($hash !== $majorityHash) {
                        $itemNumber = $this->form['items'][$index]['item_no'];
                        $differentItems[] = $itemNumber;
                    }
                }

                $itemList = implode(', ', $differentItems);
                $errors[] = "Field mismatch: Item" . (count($differentItems) > 1 ? 's' : '') . " {$itemList} " .
                    (count($differentItems) > 1 ? 'have' : 'has') . " different field values from the others. Bulk edit requires all selected items to have identical field values.";
            }
        }

        // Check amount threshold consistency
        $below200k = [];
        $above200k = [];

        foreach ($amounts as $itemNum => $amount) {
            if ($amount < 200000) {
                $below200k[] = $itemNum;
            } else {
                $above200k[] = $itemNum;
            }
        }

        $amountThreshold = null;
        if (!empty($below200k) && !empty($above200k)) {
            $errors[] = "Mixed amount thresholds: Below ₱200,000: Items " . implode(', ', $below200k) .
                "; ₱200,000 and above: Items " . implode(', ', $above200k) . ". Bulk edit requires all items to have the same amount threshold.";
        } elseif (!empty($below200k)) {
            $amountThreshold = '<200k';
        } elseif (!empty($above200k)) {
            $amountThreshold = '>=200k';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'itemNumbers' => $itemNumbers,
            'amountThreshold' => $amountThreshold,
            'commonMode' => empty($modes) ? null : array_key_first($modes),
        ];
    }

    private function pluralize(int $count): string
    {
        return $count > 1 ? 's' : '';
    }

    public function applyBulkEdit(): void
    {
        if (empty($this->selectedItems)) {
            $this->closeBulkEditModal();
            return;
        }

        // Validate bulk edit data before applying
        if (!$this->validateBulkEditData()) {
            $errorMessage = implode(' ', $this->bulkEditErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Validate ABC threshold consistency (MISSING from per-item!)
        if (!$this->validateBulkEditAbcThreshold()) {
            return;
        }

        // Check permissions for modifying successful bids (MISSING from per-item!)
        if (!$this->canModifySuccessfulBidsInBulk()) {
            return;
        }

        // Validate against clearing existing schedule data (MISSING from per-item!)
        if (!$this->validateBulkEditScheduleDeletion()) {
            return;
        }

        // Validate that at least one field is filled
        $modeId = $this->bulkEditData['mode_of_procurement_id'];
        $hasData = false;

        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            $biddingFields = [
                'bidding_number',
                'ib_number',
                'philgeps_posting_ref_no',
                'ads_post_ib',
                'pre_proc_conference',
                'list_invited_observers',
                'obsrvr_prebid_conf',
                'obsrvr_eligibility',
                'obsrvr_sub_open_of_bid',
                'obsrvr_bid',
                'obsrvr_post_qual',
                'pre_bid_conf',
                'eligibility_check',
                'sub_open_bids',
                'bid_evaluation_date',
                'post_qualification_date',
                'bidding_result',
                'resolution_number_mop'
            ];

            foreach ($biddingFields as $field) {
                if (!empty($this->bulkEditData[$field])) {
                    $hasData = true;
                    break;
                }
            }
        } elseif (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
            $svpFields = [
                'rfq_no',
                'canvass_date',
                'date_returned_of_canvass',
                'abstract_of_canvass_date',
                'resolution_number_mop'
            ];

            if ($this->bulkEditData['amount_threshold'] === '>=200k') {
                $svpFields[] = 'philgeps_posting_ref_no';
                $svpFields[] = 'ads_post_ib';
            }

            foreach ($svpFields as $field) {
                if (!empty($this->bulkEditData[$field])) {
                    $hasData = true;
                    break;
                }
            }
        }

        if (!$hasData) {
            LivewireAlert::title('No Changes')
                ->warning()
                ->text('Please fill in at least one field to update.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Apply changes to selected items
        foreach ($this->selectedItems as $index) {
            // Update mode if changed
            $originalMode = $this->form['items'][$index]['mode_of_procurement_id'];
            if ($originalMode != $modeId) {
                $this->form['items'][$index]['mode_of_procurement_id'] = $modeId;
                // Clear fields from old mode when mode changes
                $this->clearFieldsForModeChange($index, $originalMode, $modeId);
            }

            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                // Update bidding fields
                $this->updateBiddingFields($index);
            } elseif (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                // Update SVP fields
                $this->updateSvpFields($index);
            }
        }

        // SAVE TO DATABASE - This was missing!
        $this->saveTab1();

        LivewireAlert::title('Bulk Edit Applied')
            ->success()
            ->text(count($this->selectedItems) . ' items updated successfully.')
            ->toast()
            ->position('top-end')
            ->show();

        $this->closeBulkEditModal();
    }

    private function clearFieldsForModeChange($index, $oldMode, $newMode)
    {
        // Clear bidding fields if switching from bidding to SVP
        if ($this->isCompetitiveBidding($oldMode) && $this->isSvpMode($newMode)) {
            $biddingFields = [
                'bidding_number',
                'ib_number',
                'philgeps_posting_ref_no',
                'ads_post_ib',
                'pre_proc_conference',
                'list_invited_observers',
                'obsrvr_prebid_conf',
                'obsrvr_eligibility',
                'obsrvr_sub_open_of_bid',
                'obsrvr_bid',
                'obsrvr_post_qual',
                'pre_bid_conf',
                'eligibility_check',
                'sub_open_bids',
                'bid_evaluation_date',
                'post_qualification_date',
                'bidding_result',
                'resolution_number_mop'
            ];
            foreach ($biddingFields as $field) {
                $this->form['items'][$index][$field] = '';
            }
        }

        // Clear SVP fields if switching from SVP to bidding
        if ($this->isSvpMode($oldMode) && $this->isCompetitiveBidding($newMode)) {
            $svpFields = [
                'rfq_no',
                'canvass_date',
                'date_returned_of_canvass',
                'abstract_of_canvass_date',
                'resolution_number_mop',
                'philgeps_posting_ref_no',
                'ads_post_ib'
            ];
            foreach ($svpFields as $field) {
                $this->form['items'][$index][$field] = '';
            }
        }
    }

    private function validateBulkEditData(): bool
    {
        $this->bulkEditErrors = [];
        $modeId = $this->bulkEditData['mode_of_procurement_id'] ?? null;

        if (!$modeId) {
            $this->bulkEditErrors[] = 'Mode of Procurement is required.';
            return false;
        }

        // COMPETITIVE BIDDING MODES
        if ($this->isCompetitiveBidding($modeId)) {
            // Validate Bidding Result dependencies
            $biddingResult = $this->bulkEditData['bidding_result'] ?? null;

            if ($this->hasValue($biddingResult)) {
                $missingFields = [];
                $hasPreProcConference = $this->hasValue($this->bulkEditData['pre_proc_conference'] ?? '');

                if (!$hasPreProcConference) {
                    if (!$this->hasValue($this->bulkEditData['bidding_number'] ?? '')) {
                        $missingFields[] = 'Bidding #';
                    }
                    if (!$this->hasValue($this->bulkEditData['ib_number'] ?? '')) {
                        $missingFields[] = 'IB No.';
                    }
                    if (!$this->hasValue($this->bulkEditData['sub_open_bids'] ?? '')) {
                        $missingFields[] = 'Submission & Opening of Bids';
                    }

                    if (!empty($missingFields)) {
                        $fieldsList = implode(', ', $missingFields);
                        $this->bulkEditErrors[] = "Competitive Bidding: Cannot set Bidding Result without {$fieldsList} or Pre-Proc Conference.";
                    }
                }

                if ($biddingResult === 'SUCCESSFUL') {
                    $successMissingFields = [];

                    if (!$this->hasValue($this->bulkEditData['bid_evaluation_date'] ?? '')) {
                        $successMissingFields[] = 'Bid Evaluation Date';
                    }
                    if (!$this->hasValue($this->bulkEditData['post_qualification_date'] ?? '')) {
                        $successMissingFields[] = 'Post Qualification Date';
                    }

                    if (!empty($successMissingFields)) {
                        $fieldsList = implode(', ', $successMissingFields);
                        $this->bulkEditErrors[] = "Competitive Bidding: {$fieldsList} required for SUCCESSFUL bidding result.";
                    }
                }

                // Validate Resolution Number for Bidding Result
                if (!$this->hasValue($this->bulkEditData['resolution_number_mop'] ?? '')) {
                    $this->bulkEditErrors[] = 'Competitive Bidding: Resolution Number is required when Bidding Result is set.';
                }
            }
        }

        // SVP/ALTERNATIVE MODES
        if ($this->isSvpMode($modeId)) {
            // Validate PhilGEPS requirements based on amount threshold
            if ($this->bulkEditData['amount_threshold'] === '>=200k') {
                $missingPhilgepsFields = [];

                if (!$this->hasValue($this->bulkEditData['philgeps_posting_ref_no'] ?? '')) {
                    $missingPhilgepsFields[] = 'PhilGEPS Posting Ref No';
                }
                if (!$this->hasValue($this->bulkEditData['ads_post_ib'] ?? '')) {
                    $missingPhilgepsFields[] = 'Advertisement/Posting of IB/REI';
                }

                if (!empty($missingPhilgepsFields)) {
                    $fieldsList = implode(', ', $missingPhilgepsFields);
                    $this->bulkEditErrors[] = "SVP Mode: {$fieldsList} required for items with ABC ≥ ₱200,000.";
                }
            }
        }

        return empty($this->bulkEditErrors);
    }

    private function validateBulkEditAbcThreshold(): bool
    {
        // Check if all selected items have consistent ABC thresholds
        $below200k = [];
        $above200k = [];

        foreach ($this->selectedItems as $index) {
            $item = $this->form['items'][$index];
            $amount = (float) ($item['amount'] ?? 0);

            if ($amount < 200000) {
                $below200k[] = $item['item_no'] ?? ($index + 1);
            } else {
                $above200k[] = $item['item_no'] ?? ($index + 1);
            }
        }

        // If we have both below and above 200k, it's inconsistent
        if (!empty($below200k) && !empty($above200k)) {
            $belowList = implode(', ', $below200k);
            $aboveList = implode(', ', $above200k);

            LivewireAlert::title('Inconsistent ABC Thresholds')
                ->error()
                ->text("Cannot bulk edit items with different ABC thresholds. Items {$belowList} are below ₱200,000, while items {$aboveList} are ₱200,000 and above.")
                ->toast()
                ->position('top-end')
                ->show();
            return false;
        }

        return true;
    }

    private function canModifySuccessfulBidsInBulk(): bool
    {
        // Check if user has permission to modify items with post-procurement data
        if (!auth()->user()->can('edit_mode::of::procurement')) {
            foreach ($this->selectedItems as $index) {
                $item = $this->form['items'][$index];
                $biddingResult = $item['bidding_result'] ?? '';

                if ($biddingResult === 'SUCCESSFUL') {
                    $prItemID = $item['prItemID'];

                    $hasPostData = \App\Models\PostProcurement::where('ref_id', $prItemID)->exists();

                    if ($hasPostData) {
                        $itemNo = $item['item_no'] ?? ($index + 1);
                        LivewireAlert::title('Permission Required')
                            ->error()
                            ->text("Cannot modify Item #{$itemNo} - it has a SUCCESSFUL bidding result with post-procurement data. This requires 'Edit Mode of Procurement' permission.")
                            ->toast()
                            ->position('top-end')
                            ->show();
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function validateBulkEditScheduleDeletion(): bool
    {
        // Check if we're trying to clear existing schedules without providing new data
        $modeId = $this->bulkEditData['mode_of_procurement_id'];

        foreach ($this->selectedItems as $index) {
            $item = $this->form['items'][$index];
            $currentModeId = $item['mode_of_procurement_id'] ?? null;

            // Only check if mode is staying the same (not adding new mode)
            if ($currentModeId == $modeId) {
                // For competitive bidding
                if (in_array($modeId, [2, 3, 4, 5, 6])) {
                    $hasExistingBiddingData = $this->hasValue($item['bidding_number']) ||
                        $this->hasValue($item['ib_number']) ||
                        $this->hasValue($item['sub_open_bids']);

                    $clearingBiddingData = !$this->hasValue($this->bulkEditData['bidding_number'] ?? '') &&
                        !$this->hasValue($this->bulkEditData['ib_number'] ?? '') &&
                        !$this->hasValue($this->bulkEditData['sub_open_bids'] ?? '') &&
                        !$this->hasValue($this->bulkEditData['pre_proc_conference'] ?? '');

                    if ($hasExistingBiddingData && $clearingBiddingData) {
                        $itemNo = $item['item_no'] ?? ($index + 1);
                        LivewireAlert::title('Cannot Clear Existing Data')
                            ->error()
                            ->text("Cannot clear existing bidding data for Item #{$itemNo} without providing replacement data or Pre-Proc Conference.")
                            ->toast()
                            ->position('top-end')
                            ->show();
                        return false;
                    }
                }

                // For SVP/Alternative modes
                if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                    $hasExistingSvpData = $this->hasValue($item['rfq_no']) ||
                        $this->hasValue($item['canvass_date']) ||
                        $this->hasValue($item['abstract_of_canvass_date']);

                    $clearingSvpData = !$this->hasValue($this->bulkEditData['rfq_no'] ?? '') &&
                        !$this->hasValue($this->bulkEditData['canvass_date'] ?? '') &&
                        !$this->hasValue($this->bulkEditData['abstract_of_canvass_date'] ?? '');

                    if ($hasExistingSvpData && $clearingSvpData) {
                        $itemNo = $item['item_no'] ?? ($index + 1);
                        LivewireAlert::title('Cannot Clear Existing Data')
                            ->error()
                            ->text("Cannot clear existing SVP data for Item #{$itemNo} without providing replacement data.")
                            ->toast()
                            ->position('top-end')
                            ->show();
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function updateBiddingFields($index): void
    {
        $fields = [
            'bidding_number',
            'ib_number',
            'philgeps_posting_ref_no',
            'ads_post_ib',
            'pre_proc_conference',
            'list_invited_observers',
            'obsrvr_prebid_conf',
            'obsrvr_eligibility',
            'obsrvr_sub_open_of_bid',
            'obsrvr_bid',
            'obsrvr_post_qual',
            'pre_bid_conf',
            'eligibility_check',
            'sub_open_bids',
            'bid_evaluation_date',
            'post_qualification_date',
            'bidding_result',
            'resolution_number_mop'
        ];

        foreach ($fields as $field) {
            if (!empty($this->bulkEditData[$field])) {
                $this->form['items'][$index][$field] = $this->bulkEditData[$field];
            }
        }
    }

    private function updateSvpFields($index): void
    {
        $fields = [
            'rfq_no',
            'canvass_date',
            'date_returned_of_canvass',
            'abstract_of_canvass_date',
            'resolution_number_mop'
        ];

        if ($this->bulkEditData['amount_threshold'] === '>=200k') {
            $fields[] = 'philgeps_posting_ref_no';
            $fields[] = 'ads_post_ib';
        }

        foreach ($fields as $field) {
            if (!empty($this->bulkEditData[$field])) {
                $this->form['items'][$index][$field] = $this->bulkEditData[$field];
            }
        }
    }
    public function bulkAddMode(): void
    {
        if (empty($this->selectedItems)) {
            return;
        }

        // Validate ABC threshold consistency (MISSING!)
        if (!$this->validateBulkEditAbcThreshold()) {
            return;
        }

        // Check permissions for modifying successful bids (MISSING!)
        if (!$this->canModifySuccessfulBidsInBulk()) {
            return;
        }

        // Get the mode ID from bulk edit data
        $modeId = $this->bulkEditData['mode_of_procurement_id'] ?? null;

        // Check if mode allows adding
        $canAdd = $this->isPendingMode($modeId) ||
            (in_array($modeId, [2, 3, 4, 5, 6]) &&
                ($this->bulkEditData['bidding_result'] ?? '') === 'UNSUCCESSFUL');

        if (!$canAdd) {
            LivewireAlert::title('Cannot Add Mode')
                ->warning()
                ->text('New modes can only be added for Shopping mode or UNSUCCESSFUL competitive bidding.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // First, apply the current bulk edit changes
        $this->applyBulkEdit();

        // Then add new mode to each selected item
        foreach ($this->selectedItems as $index) {
            $this->addItem($index);
        }

        // SAVE THE NEW MODES TO DATABASE - This was missing!
        $this->saveTab1();

        LivewireAlert::title('Modes Added Successfully')
            ->success()
            ->text('New modes added to ' . count($this->selectedItems) . ' items.')
            ->toast()
            ->position('top-end')
            ->show();

        // Reload data
        $this->mount($this->procurement);

        // Close modal and clear selection
        $this->closeBulkEditModal();
    }
    public function applyBulkEditAndAddMode(): void
    {
        // Validate ABC threshold consistency (MISSING!)
        if (!$this->validateBulkEditAbcThreshold()) {
            return;
        }

        // Check permissions for modifying successful bids (MISSING!)
        if (!$this->canModifySuccessfulBidsInBulk()) {
            return;
        }

        // First apply the bulk edit
        $this->applyBulkEdit();

        // Then add new mode to all selected items
        foreach ($this->selectedItems as $index) {
            $this->addItem($index);
        }

        // SAVE EVERYTHING TO DATABASE - This was missing!
        $this->saveTab1();

        LivewireAlert::title('Changes Applied & Modes Added')
            ->success()
            ->text('Bulk edit applied and new modes added to ' . count($this->selectedItems) . ' items.')
            ->toast()
            ->position('top-end')
            ->show();

        $this->closeBulkEditModal();
    }
    public function closeBulkEditModal(): void
    {
        $this->showBulkEditModal = false;
        $this->bulkEditData = [];
        $this->bulkEditErrors = [];
        $this->deselectAll();
    }

    // Post Procurement Bulk Edit Methods
    public function openPostBulkEditModal(): void
    {
        if (empty($this->selectedPostItems)) {
            LivewireAlert::title('No Items Selected')
                ->warning()
                ->text('Please select at least one item to edit.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Get item numbers for display
        $itemNumbers = [];
        foreach ($this->selectedPostItems as $prItemID) {
            foreach ($this->form['items'] as $item) {
                if (($item['prItemID'] ?? null) === $prItemID) {
                    $itemNumbers[] = $item['item_no'] ?? $prItemID;
                    break;
                }
            }
        }

        // Initialize post bulk edit data with single set of fields (like Tab 1 bulk edit)
        $this->postBulkEditData = [
            'items_count' => count($this->selectedPostItems),
            'item_numbers' => $itemNumbers,
            'selected_items' => [], // Will hold item info for display
            'resolutionAwardNumber' => '',
            'noticeOfAwardNumber' => '',
            'noticeOfAward' => '',
            'resolutionAwardDate' => '',
            'awardedAmount' => '',
            'philgepsNoticeOfAwardNo' => '',
            'philgepsPostingOfAward' => '',
            'supplier_id' => '',
        ];

        // Populate selected_items for display in the table
        foreach ($this->selectedPostItems as $prItemID) {
            foreach ($this->form['items'] as $item) {
                if (($item['prItemID'] ?? null) === $prItemID) {
                    $this->postBulkEditData['selected_items'][] = [
                        'prItemID' => $prItemID,
                        'item_no' => $item['item_no'] ?? '',
                        'description' => $item['description'] ?? '',
                    ];
                    break;
                }
            }
        }

        $this->postBulkEditErrors = [];
        $this->showPostBulkEditModal = true;
    }

    public function applyPostBulkEdit(): void
    {
        if (empty($this->selectedPostItems)) {
            $this->closePostBulkEditModal();
            return;
        }

        // Validate post bulk edit data (like Tab 1 bulk edit)
        $this->postBulkEditErrors = [];
        $hasData = false;

        $fields = [
            'resolutionAwardNumber',
            'noticeOfAwardNumber',
            'noticeOfAward',
            'resolutionAwardDate',
            'awardedAmount',
            'philgepsNoticeOfAwardNo',
            'philgepsPostingOfAward',
            'supplier_id'
        ];

        foreach ($fields as $field) {
            if (!empty($this->postBulkEditData[$field])) {
                $hasData = true;
                break;
            }
        }

        if (!$hasData) {
            LivewireAlert::title('No Changes')
                ->warning()
                ->text('Please fill in at least one field to update.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        // Apply changes to selected post items (like Tab 1 bulk edit)
        DB::transaction(function () {
            foreach ($this->selectedPostItems as $prItemID) {
                $postData = [
                    'ref_id' => $prItemID,
                    'resolution_award_number' => $this->postBulkEditData['resolutionAwardNumber'] ?: null,
                    'notice_of_award_number' => $this->postBulkEditData['noticeOfAwardNumber'] ?: null,
                    'notice_of_award' => $this->postBulkEditData['noticeOfAward'] ?: null,
                    'resolution_award_date' => $this->nullableDate($this->postBulkEditData['resolutionAwardDate']),
                    'awarded_amount' => $this->postBulkEditData['awardedAmount'] ?: null,
                    'philgeps_notice_of_award_no' => $this->postBulkEditData['philgepsNoticeOfAwardNo'] ?: null,
                    'philgeps_posting_of_award' => $this->nullableDate($this->postBulkEditData['philgepsPostingOfAward']),
                    'supplier_id' => $this->postBulkEditData['supplier_id'] ?: null,
                ];

                PostProcurement::updateOrCreate(
                    ['ref_id' => $prItemID],
                    $postData
                );
            }
        });

        // Reload post procurement data
        $this->loadPostProcurementData($this->procurement);

        LivewireAlert::title('Post Procurement Bulk Edit Applied')
            ->success()
            ->text(count($this->selectedPostItems) . ' items updated successfully.')
            ->toast()
            ->position('top-end')
            ->show();

        $this->closePostBulkEditModal();
    }

    public function closePostBulkEditModal(): void
    {
        $this->showPostBulkEditModal = false;
        $this->postBulkEditData = [];
        $this->postBulkEditErrors = [];
        $this->deselectAllPostItems();
    }

    public function toggleAllPostItems(): void
    {
        $availablePrItemIDs = [];
        foreach ($this->form['items'] ?? [] as $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            $prItemID = $item['prItemID'] ?? null;

            if (!$prItemID)
                continue;

            // Check for SUCCESSFUL bidding in competitive modes
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $bidResult = $item['bidding_result'] ?? '';
                if ($bidResult === 'SUCCESSFUL') {
                    $availablePrItemIDs[] = $prItemID;
                    continue;
                }
            }

            // Check for complete SVP data in alternative modes
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                if (
                    !empty($item['resolution_number_mop']) &&
                    !empty($item['rfq_no']) &&
                    !empty($item['canvass_date']) &&
                    !empty($item['date_returned_of_canvass']) &&
                    !empty($item['abstract_of_canvass_date'])
                ) {
                    $availablePrItemIDs[] = $prItemID;
                }
            }
        }

        if (count($this->selectedPostItems) === count($availablePrItemIDs)) {
            // All are selected, deselect all
            $this->selectedPostItems = [];
        } else {
            // Select all available items
            $this->selectedPostItems = $availablePrItemIDs;
        }
    }

    public function togglePostItemSelection($prItemID): void
    {
        if (in_array($prItemID, $this->selectedPostItems)) {
            $this->selectedPostItems = array_diff($this->selectedPostItems, [$prItemID]);
        } else {
            $this->selectedPostItems[] = $prItemID;
        }
    }

    public function deselectAllPostItems(): void
    {
        $this->selectedPostItems = [];
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-per-item-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'suppliers' => $this->suppliers,
        ]);
    }
}
