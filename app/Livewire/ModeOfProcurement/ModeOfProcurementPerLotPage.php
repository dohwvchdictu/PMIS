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
use App\Models\MopLot;
use Illuminate\Support\Facades\DB;

class ModeOfProcurementPerLotPage extends Component
{
    public Procurement $procurement;
    public array $form = [];

    public Collection $modeOfProcurements;
    public int $textareaRows = 1;
    public string $procID = '';
    public int $activeTab = 1;
    public bool $showHistory = false;

    // Post-Procurement Tab Fields
    public ?string $resolutionAwardNumber = null;
    public ?string $noticeOfAwardNumber = null;
    public ?string $noticeOfAward = null;
    public ?string $resolutionAwardDate = null;
    public ?float $awardedAmount = null;
    public ?string $philgepsNoticeOfAwardNo = null;
    public ?string $philgepsPostingOfAward = null;
    public ?int $supplier_id = null;

    public Collection $suppliers;
    public $queryParams = [];
    public bool $showModal = false;
    public ?float $abc = null;
    public ?array $editingItem = null;
    public ?int $editingIndex = null;
    public array $scheduleValidationErrors = [];

    public function mount(Procurement $procurement): void
    {
        $this->queryParams = request()->query();

        $procurement->load('mopLots.modeOfProcurement');
        $this->procurement = $procurement;
        $this->procID = $procurement->procID ?? '';

        $this->form = [
            'pr_number' => $procurement->pr_number ?? '',
            'procurement_program_project' => $procurement->procurement_program_project ?? '',
            'approved_ppmp' => (bool) ($procurement->approved_ppmp ?? false),
            'app_updated' => (bool) ($procurement->app_updated ?? false),
            'early_procurement' => (bool) ($procurement->early_procurement ?? false),
            'items' => [],
        ];

        $this->abc = $procurement->abc;

        $this->loadPostProcurementData($procurement);
        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();
        $this->suppliers = Supplier::all();
        $this->loadPerLotData($procurement);
        $this->calculateTextareaRows($procurement->procurement_program_project ?? '');
    }

    // ============================================================================
    // FIX #5: CONSISTENT EMPTY CHECK PATTERN
    // ============================================================================
    /**
     * Standardized method to check if a value is considered "filled"
     * Handles: null, empty string, whitespace-only strings
     * Returns true if value has meaningful content
     */
    private function hasValue($value): bool
    {
        // null check first
        if (is_null($value)) {
            return false;
        }

        // Convert to string and trim
        $stringValue = trim((string) $value);

        // Check if empty after trimming
        return $stringValue !== '';
    }

    /**
     * Check if ANY of the provided fields have values
     */
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
        $post = PostProcurement::where('ref_id', $this->procID)->first();

        if ($post) {
            $this->resolutionAwardNumber = $post->resolution_award_number;
            $this->noticeOfAwardNumber = $post->notice_of_award_number;
            $this->noticeOfAward = $post->notice_of_award;
            $this->resolutionAwardDate = $post->resolution_award_date;
            $this->awardedAmount = $post->awarded_amount;
            $this->philgepsNoticeOfAwardNo = $post->philgeps_notice_of_award_no;
            $this->philgepsPostingOfAward = $post->philgeps_posting_of_award;
            $this->supplier_id = $post->supplier_id;
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
        foreach ($this->form['items'] ?? [] as $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;

            // COMPETITIVE BIDDING MODES (2, 3, 4, 5, 6)
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
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

                // If ABC is 200k or above, also require philgeps_posting_ref_no and ads_post_ib
                if ($this->abc >= 200000) {
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

    protected function loadPerLotData(Procurement $procurement): void
    {
        $mopLots = $procurement->mopLots()
            ->with('modeOfProcurement')
            ->orderBy('mode_order')
            ->get();

        $uids = $mopLots->pluck('uid')->filter()->toArray();
        $procID = $procurement->procID;

        if (empty($uids)) {
            $this->form['items'] = [];
            return;
        }

        $bidSchedules = BidSchedule::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        $prSvps = PrSvp::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        $scheduleMap = $this->buildScheduleMap($bidSchedules, $prSvps);

        $this->form['items'] = $mopLots->map(function ($mopLot, $index) use ($scheduleMap) {
            $uid = $mopLot->uid;
            $schedule = $uid ? $scheduleMap->get($uid, []) : [];
            return $this->buildItemArray($mopLot, $index, $schedule);
        })->toArray();
    }

    private function buildItemArray($mopLot, int $index, array $schedule): array
    {
        return [
            'id' => $mopLot->id ?? null,
            'uid' => $mopLot->uid ?? 'temp_' . uniqid(),
            'mode_of_procurement_id' => $mopLot->mode_of_procurement_id,
            'mode_order' => $mopLot->mode_order ?? ($index + 1),
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

    private function buildScheduleMap(Collection $bidSchedules, Collection $prSvps): Collection
    {
        $map = collect();

        foreach ($bidSchedules as $uid => $schedule) {
            $map[$uid] = [
                'bidding_number' => $schedule->bidding_number,
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
                'bidding_result' => $schedule->bidding_result,
                'resolution_number_mop' => $schedule->resolution_number_mop,
            ];
        }

        foreach ($prSvps as $uid => $schedule) {
            $existing = $map->get($uid, []);
            $map[$uid] = array_merge($existing, [
                'philgeps_posting_ref_no' => $schedule->philgeps_posting_ref_no ?? $existing['philgeps_posting_ref_no'] ?? null,
                'ads_post_ib' => $schedule->ads_post_ib ?? $existing['ads_post_ib'] ?? null,
                'resolution_number_mop' => $schedule->resolution_number_mop,
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
            ]);
        }

        return $map;
    }

    public function addItem(): void
    {
        $newItem = [
            'id' => null,
            'uid' => 'temp_' . uniqid(),
            'mode_of_procurement_id' => null,
            'mode_order' => 1,
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
            'resolution_number_mop' => null,
            'rfq_no' => null,
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
        ];

        $this->form['items'][] = $newItem;
        $this->reindexModeOrder();
        $this->showHistory = false;
    }

    private function reindexModeOrder(): void
    {
        foreach ($this->form['items'] as $index => &$item) {
            $item['mode_order'] = $index + 1;
        }
    }

    public function toggleHistory()
    {
        $this->showHistory = !$this->showHistory;
    }

    public function removeItem(string $uid): void
    {
        $this->form['items'] = array_filter($this->form['items'], function ($item) use ($uid) {
            return $item['uid'] !== $uid;
        });

        $this->form['items'] = array_values($this->form['items']);
        $this->reindexModeOrder();
    }

    public function setStep(int $step): void
    {
        if ($step == 2 && !$this->isPostAvailable) {
            LivewireAlert::title('Cannot Access Post Tab')
                ->warning()
                ->text('Please complete the Mode of Procurement details first. A SUCCESSFUL bidding result or complete SVP data is required.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $this->activeTab = $step;
    }

    // ============================================================================
    // FIX #6: TRANSACTION ROLLBACK - VALIDATE BEFORE TRANSACTION
    // ============================================================================
    public function saveTab1()
    {
        // STEP 1: Validate form rules FIRST
        $rules = [
            'form.items.*.mode_of_procurement_id' => 'required|integer',
        ];

        $attributes = [];

        try {
            $this->validate($rules, [], $attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();
            $errorString = implode(' ', $errorMessages);

            LivewireAlert::title('Validation Failed')
                ->error()
                ->text('Please check the following errors: ' . $errorString)
                ->toast()->position('top-end')->show();

            // Exit early - don't proceed to schedule validation or DB transaction
            return;
        }

        // STEP 2: Validate schedules BEFORE starting transaction
        $this->scheduleValidationErrors = [];
        if (!$this->validateSchedules()) {
            $errorMessage = implode(' ', $this->scheduleValidationErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()->position('top-end')->show();

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
                $modeOrder = $index + 1;
                $isUiNew = isset($item['uid']) && (str_starts_with($item['uid'], 'new_') || str_starts_with($item['uid'], 'temp_'));

                if ($isUiNew) {
                    $generatedUid = "MOP-{$modeId}-{$modeOrder}";
                } else {
                    $generatedUid = $item['uid'];
                }

                $matchCriteria = ['procID' => $this->procID];
                if ($isUiNew) {
                    $matchCriteria['mode_order'] = $modeOrder;
                } else {
                    $matchCriteria['uid'] = $item['uid'];
                }

                $savedParentModel = MopLot::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $generatedUid,
                        'mode_of_procurement_id' => $modeId,
                        'mode_order' => $modeOrder
                    ]
                );

                if ($savedParentModel->wasRecentlyCreated) {
                    $isMopAdded = true;
                } elseif ($savedParentModel->wasChanged()) {
                    $isMopUpdated = true;
                }

                if ($savedParentModel) {
                    $this->saveRelatedSchedules(
                        $savedParentModel,
                        $item,
                        $isScheduleAdded,
                        $isScheduleUpdated
                    );
                }
            }
        });

        // Show appropriate success message
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

    private function validateSchedules(): bool
    {
        $isValid = true;

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            if (!$modeId)
                continue;

            $itemNumber = $index + 1;
            $modeName = $this->modeOfProcurements->firstWhere('id', $modeId)?->modeofprocurements ?? "Item {$itemNumber}";

            $matchCriteria = [
                'ref_id' => $this->procID,
                'mop_uid' => $item['uid']
            ];

            // FIX #5: Using hasAnyValue() for consistent checking
            $biddingFields = [
                $item['bidding_number'] ?? null,
                $item['ib_number'] ?? null,
                $item['philgeps_posting_ref_no'] ?? null,
                $item['ads_post_ib'] ?? null,
                $item['pre_proc_conference'] ?? null,
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
            if (!$hasAnyData && str_starts_with($item['uid'], 'temp_')) {
                continue;
            }

            // COMPETITIVE BIDDING MODES (2, 3, 4, 5, 6)
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $existingBidSchedule = BidSchedule::where($matchCriteria)->first();

                $hasBiddingData = $this->hasAnyValue($biddingFields);

                if ($existingBidSchedule && !$hasBiddingData) {
                    $this->scheduleValidationErrors[] = "{$modeName}: At least one bidding schedule field must be filled.";
                    $isValid = false;
                }

                // Validate Bidding Result dependencies
                $biddingResult = $item['bidding_result'] ?? null;

                // FIX #5: Using hasValue() for consistent checking
                if ($this->hasValue($biddingResult)) {
                    $missingFields = [];
                    $hasPreProcConference = $this->hasValue($item['pre_proc_conference']);

                    if (!$hasPreProcConference) {
                        if (!$this->hasValue($item['bidding_number'])) {
                            $missingFields[] = 'Bidding #';
                        }
                        if (!$this->hasValue($item['ib_number'])) {
                            $missingFields[] = 'IB No.';
                        }
                        if (!$this->hasValue($item['sub_open_bids'])) {
                            $missingFields[] = 'Submission & Opening of Bids';
                        }

                        if (!empty($missingFields)) {
                            $fieldsList = implode(', ', $missingFields);
                            $this->scheduleValidationErrors[] = "{$modeName}: Cannot set Bidding Result without {$fieldsList} or Pre-Proc Conference.";
                            $isValid = false;
                        }
                    }

                    if ($biddingResult === 'SUCCESSFUL') {
                        $successMissingFields = [];

                        if (!$this->hasValue($item['bid_evaluation_date'])) {
                            $successMissingFields[] = 'Bid Evaluation Date';
                        }
                        if (!$this->hasValue($item['post_qualification_date'])) {
                            $successMissingFields[] = 'Post Qualification Date';
                        }

                        if (!empty($successMissingFields)) {
                            $fieldsList = implode(', ', $successMissingFields);
                            $this->scheduleValidationErrors[] = "{$modeName}: {$fieldsList} required for SUCCESSFUL bidding result.";
                            $isValid = false;
                        }
                    }
                }
            }

            // SVP/ALTERNATIVE MODES (7-24)
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                $existingSvp = PrSvp::where($matchCriteria)->first();

                // FIX #5: Using hasAnyValue() for consistent checking
                $svpFields = [
                    $item['rfq_no'] ?? null,
                    $item['canvass_date'] ?? null,
                    $item['date_returned_of_canvass'] ?? null,
                    $item['abstract_of_canvass_date'] ?? null,
                ];

                $hasSvpData = $this->hasAnyValue($svpFields);

                if ($existingSvp && !$hasSvpData) {
                    $this->scheduleValidationErrors[] = "{$modeName}: At least one SVP field must be filled.";
                    $isValid = false;
                }

                // No required field validation for SVP modes during save - all fields are optional
            }
        }

        return $isValid;
    }

    protected function saveRelatedSchedules(
        $parentModel,
        array $itemData,
        bool &$isScheduleAdded,
        bool &$isScheduleUpdated
    ): void {
        $modeId = $itemData['mode_of_procurement_id'];
        $refId = $this->procID;
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
                $itemData['bidding_number'] ?? null,
                $itemData['ib_number'] ?? null,
                $itemData['philgeps_posting_ref_no'] ?? null,
                $itemData['ads_post_ib'] ?? null,
                $itemData['pre_proc_conference'] ?? null,
                $itemData['pre_bid_conf'] ?? null,
                $itemData['eligibility_check'] ?? null,
                $itemData['sub_open_bids'] ?? null,
                $itemData['bid_evaluation_date'] ?? null,
                $itemData['post_qualification_date'] ?? null,
                $itemData['bidding_date'] ?? null,
                $itemData['bidding_result'] ?? null,
            ];

            $hasBiddingData = $this->hasAnyValue($biddingFields);
            $existingBidSchedule = BidSchedule::where($matchCriteria)->first();

            if ($hasBiddingData || $existingBidSchedule) {
                if (!$hasBiddingData && $existingBidSchedule) {
                    return;
                }

                if (!$existingBidSchedule) {
                    $relatedMopUids = MopLot::where('procID', $refId)
                        ->where('mode_of_procurement_id', $modeId)
                        ->pluck('uid');

                    $count = BidSchedule::where('ref_id', $refId)
                        ->whereIn('mop_uid', $relatedMopUids)
                        ->count();

                    $uid = $parentUid . '-' . ($count + 1);
                } else {
                    $uid = $existingBidSchedule->uid;
                }

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
            // Save SVP fields to PrSvp for modes 7-24
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
                    $relatedMopUids = MopLot::where('procID', $refId)
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

    // ============================================================================
    // FIX #9: IMPROVED POST-PROCUREMENT SAVE LOGIC WITH CLEAR VALIDATION
    // ============================================================================
    public function savePost()
    {
        // First check if Post tab should be accessible
        if (!$this->isPostAvailable) {
            LivewireAlert::title('Invalid Action')
                ->error()
                ->text('Cannot save post-procurement data. Prerequisites not met.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $postFields = [
            $this->resolutionAwardNumber,
            $this->resolutionAwardDate,
            $this->noticeOfAwardNumber,
            $this->noticeOfAward,
            $this->awardedAmount,
            $this->philgepsNoticeOfAwardNo,
            $this->philgepsPostingOfAward,
            $this->supplier_id,
        ];


        $hasAnyPostData = $this->hasAnyValue($postFields);

        // Define base validation rules (all nullable by default)
        $rules = [
            'resolutionAwardNumber' => 'nullable|string|max:255',
            'resolutionAwardDate' => 'nullable|date',
            'noticeOfAwardNumber' => 'nullable|string|max:255',
            'noticeOfAward' => 'nullable|date',
            'awardedAmount' => 'nullable|numeric|min:0',
            'philgepsNoticeOfAwardNo' => 'nullable|string|max:255',
            'philgepsPostingOfAward' => 'nullable|date',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
        ];

        // FIX #9: If ANY field has data, make Resolution Award Number and Date REQUIRED
        if ($hasAnyPostData) {
            $rules['resolutionAwardNumber'] = 'required|string|max:255';
            $rules['resolutionAwardDate'] = 'required|date';
        }

        $attributes = [
            'resolutionAwardNumber' => 'Resolution Award Number',
            'resolutionAwardDate' => 'Resolution Award Date',
            'noticeOfAwardNumber' => 'Notice of Award Number',
            'noticeOfAward' => 'Notice of Award Date',
            'awardedAmount' => 'Awarded Amount',
            'philgepsNoticeOfAwardNo' => 'PhilGEPS Notice of Award Number',
            'philgepsPostingOfAward' => 'PhilGEPS Posting of Award',
            'supplier_id' => 'Supplier',
        ];

        // FIX #9: Custom error messages for better UX
        $messages = [
            'resolutionAwardNumber.required' => 'Resolution Award Number is required when entering post-procurement data.',
            'resolutionAwardDate.required' => 'Resolution Award Date is required when entering post-procurement data.',
        ];

        try {
            $this->validate($rules, $messages, $attributes);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();
            $errorString = implode(' ', $errorMessages);

            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorString)
                ->toast()->position('top-end')->show();

            return; // Exit early on validation failure
        }

        $isAdded = false;
        $isUpdated = false;

        DB::transaction(function () use (&$isAdded, &$isUpdated) {
            $data = [
                'ref_id' => $this->procID,
                'resolution_award_number' => $this->resolutionAwardNumber,
                'resolution_award_date' => $this->resolutionAwardDate,
                'notice_of_award_number' => $this->noticeOfAwardNumber,
                'notice_of_award' => $this->noticeOfAward,
                'awarded_amount' => $this->awardedAmount,
                'philgeps_notice_of_award_no' => $this->philgepsNoticeOfAwardNo,
                'philgeps_posting_of_award' => $this->nullableDate($this->philgepsPostingOfAward),
                'supplier_id' => $this->supplier_id,
            ];

            $postModel = PostProcurement::updateOrCreate(
                ['ref_id' => $this->procID],
                $data
            );

            if ($postModel->wasRecentlyCreated) {
                $isAdded = true;
            } elseif ($postModel->wasChanged()) {
                $isUpdated = true;
            }
        });

        if ($isAdded) {
            LivewireAlert::title('Post-Procurement Added!')
                ->success()
                ->text('The procurement award details have been saved.')
                ->toast()
                ->position('top-end')
                ->show();
        } elseif ($isUpdated) {
            LivewireAlert::title('Post-Procurement Updated!')
                ->success()
                ->text('The procurement award details have been updated.')
                ->toast()
                ->position('top-end')
                ->show();
        } else {
            LivewireAlert::title('No Changes')
                ->info()
                ->text('Post-procurement details remain unchanged.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function save()
    {
        if ($this->activeTab == 2) {
            if (!$this->isPostAvailable) {
                LivewireAlert::title('Cannot Save')
                    ->error()
                    ->text('Complete Mode of Procurement details in Tab 1 first.')
                    ->toast()
                    ->position('top-end')
                    ->show();
                $this->activeTab = 1;
                return;
            }
            $this->savePost();
        } else {
            $this->saveTab1();
        }

        $this->mount($this->procurement);
    }

    public function editHistoryItem($index): void
    {
        $totalItems = count($this->form['items']);
        $originalIndex = $totalItems - 1 - $index;

        if (!isset($this->form['items'][$originalIndex])) {
            LivewireAlert::title('Error')
                ->error()
                ->text('History record not found.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $item = $this->form['items'][$originalIndex];
        $biddingResult = $item['bidding_result'] ?? null;
        $hasPostData = PostProcurement::where('ref_id', $this->procID)->exists();
        $canEditMop = auth()->user()->can('edit_mode::of::procurement');

        if ($biddingResult === 'SUCCESSFUL' && $hasPostData && !$canEditMop) {
            LivewireAlert::title('Permission Denied')
                ->error()
                ->text('Cannot edit SUCCESSFUL bidding records when post-procurement data exists. This requires special permission.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $this->editingIndex = $originalIndex;
        $this->editingItem = $this->form['items'][$originalIndex];
        $this->showModal = true;
    }

    public function getIsPostActiveProperty(): bool
    {
        $post = PostProcurement::where('ref_id', $this->procID)->first();
        return $post !== null;
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

        $this->form['items'][$this->editingIndex] = $this->editingItem;

        $this->scheduleValidationErrors = [];
        if (!$this->validateSchedules()) {
            $errorMessage = implode(' ', $this->scheduleValidationErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()
                ->position('top-end')
                ->show();

            $this->mount($this->procurement);
            return;
        }

        $this->saveTab1();
        $this->closeModal();
    }
    public function itemHasSchedule(array $item): bool
    {
        $scheduleFields = [
            // Bidding fields
            $item['bidding_number'] ?? null,
            $item['ib_number'] ?? null,
            $item['philgeps_posting_ref_no'] ?? null,
            $item['pre_proc_conference'] ?? null,
            $item['ads_post_ib'] ?? null,
            $item['pre_bid_conf'] ?? null,
            $item['eligibility_check'] ?? null,
            $item['sub_open_bids'] ?? null,
            $item['bid_evaluation_date'] ?? null,
            $item['post_qualification_date'] ?? null,
            $item['bidding_date'] ?? null,
            $item['bidding_result'] ?? null,
            // Other Modes
            $item['resolution_number_mop'] ?? null,
            $item['rfq_no'] ?? null,
            $item['canvass_date'] ?? null,
            $item['date_returned_of_canvass'] ?? null,
            $item['abstract_of_canvass_date'] ?? null,
        ];

        return $this->hasAnyValue($scheduleFields);
    }
    public function canAddRebidForItem(array $item, ?int $modeId): bool
    {
        if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
            return false;
        }

        $bidResult = $item['bidding_result'] ?? '';

        $hasBiddingData = $this->hasValue($item['ib_number']) &&
            $this->hasValue($item['bidding_number']) &&
            $this->hasValue($item['sub_open_bids']);

        $hasPreProcConference = $this->hasValue($item['pre_proc_conference']);

        return $modeId == 1 ||
            (($hasBiddingData || $hasPreProcConference) &&
                $bidResult === 'UNSUCCESSFUL' &&
                !$this->isPostAvailable);
    }
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->editingItem = null;
        $this->editingIndex = null;
    }

    private function nullableDate($value)
    {
        return $this->hasValue($value) ? $value : null;
    }

    public function cancel()
    {
        return redirect()->route('mode-of-procurement.index', $this->queryParams);
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-per-lot-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'suppliers' => $this->suppliers,
            'isPostActive' => $this->isPostActive,
        ]);
    }
}
