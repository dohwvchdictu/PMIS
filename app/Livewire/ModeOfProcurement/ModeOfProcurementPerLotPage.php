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
    public $queryParams = [];
    public bool $showModal = false;
    public ?array $editingItem = null;
    public ?int $editingIndex = null;
    public array $scheduleValidationErrors = [];

    public function mount(Procurement $procurement): void
    {
        $this->queryParams = request()->query();

        $procurement->load('mopLots.modeOfProcurement');
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

        // Load post-procurement data
        $this->loadPostProcurementData($procurement);

        // Load mode of procurements as collection
        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();
        $this->suppliers = Supplier::all();

        // Load per-lot data
        $this->loadPerLotData($procurement);

        // Calculate textarea rows
        $this->calculateTextareaRows($procurement->procurement_program_project ?? '');
    }

    private function loadPostProcurementData(Procurement $procurement): void
    {
        $post = PostProcurement::where('ref_id', $this->procID)->first();

        if ($post) {
            $this->resolutionNumber = $post->resolution_number;
            $this->bidEvaluationDate = $post->bid_evaluation_date;
            $this->postQualDate = $post->post_qual_date;
            $this->noticeOfAward = $post->notice_of_award;
            $this->recommendingForAward = $post->recommending_for_award;
            $this->awardedAmount = $post->awarded_amount;
            $this->philgepsReferenceNo = $post->philgeps_reference_no;
            $this->awardNoticeNumber = $post->award_notice_no;
            $this->dateOfPostingOfAwardOnPhilGEPS = $post->date_of_posting_of_award_on_philgeps;
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

            // Check Bidding/NTF for "SUCCESSFUL" result
            if (in_array($modeId, [1, 2, 3, 4])) {
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

    protected function loadPerLotData(Procurement $procurement): void
    {
        // Get MOP Lots
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

        // Fetch all schedules in batch queries
        $bidSchedules = BidSchedule::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        $ntfSchedules = NtfBidSchedule::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        $prSvps = PrSvp::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        // Build unified schedule map
        $scheduleMap = $this->buildScheduleMap($bidSchedules, $ntfSchedules, $prSvps);

        // Map items
        $this->form['items'] = $mopLots->map(function ($mopLot, $index) use ($scheduleMap) {
            $uid = $mopLot->uid;
            $schedule = $uid ? $scheduleMap->get($uid, []) : [];

            return $this->buildItemArray($mopLot, $index, $schedule);
        })->toArray();

        // if ($this->isPostAvailable) {
        //     $this->activeTab = 2;
        // }
    }

    private function buildItemArray($mopLot, int $index, array $schedule): array
    {
        return [
            'id' => $mopLot->id ?? null,
            'uid' => $mopLot->uid ?? 'temp_' . uniqid(),
            'mode_of_procurement_id' => $mopLot->mode_of_procurement_id,
            'mode_order' => $mopLot->mode_order ?? ($index + 1),

            // Shared fields
            'ib_number' => $schedule['ib_number'] ?? null,
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
            'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
            'eligibility_check' => $schedule['eligibility_check'] ?? null,
            'sub_open_bids' => $schedule['sub_open_bids'] ?? null,

            // Bidding specific
            'bidding_number' => $schedule['bidding_number'] ?? null,
            'bidding_date' => $schedule['bidding_date'] ?? null,
            'bidding_result' => $schedule['bidding_result'] ?? null,

            // NTF specific
            'ntf_no' => $schedule['ntf_no'] ?? null,
            'ntf_bidding_date' => $schedule['ntf_bidding_date'] ?? null,
            'ntf_bidding_result' => $schedule['ntf_bidding_result'] ?? null,

            // SVP specific
            'rfq_no' => $schedule['rfq_no'] ?? null,
            'canvass_date' => $schedule['canvass_date'] ?? null,
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
            'resolution_number' => $schedule['resolution_number'] ?? null,
        ];
    }
    private function buildScheduleMap(Collection $bidSchedules, Collection $ntfSchedules, Collection $prSvps): Collection
    {
        $map = collect();

        foreach ($bidSchedules as $uid => $schedule) {
            $map[$uid] = [
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

        foreach ($ntfSchedules as $uid => $schedule) {
            $existing = $map->get($uid, []);
            $map[$uid] = array_merge($existing, [
                'ib_number' => $schedule->ib_number,
                'pre_proc_conference' => $schedule->pre_proc_conference,
                'ads_post_ib' => $schedule->ads_post_ib,
                'pre_bid_conf' => $schedule->pre_bid_conf,
                'eligibility_check' => $schedule->eligibility_check,
                'sub_open_bids' => $schedule->sub_open_bids,
                'bidding_number' => $schedule->bidding_number,
                'ntf_no' => $schedule->ntf_no,
                'ntf_bidding_date' => $schedule->ntf_bidding_date,
                'ntf_bidding_result' => $schedule->ntf_bidding_result,
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
            ]);
        }

        foreach ($prSvps as $uid => $schedule) {
            $existing = $map->get($uid, []);
            $map[$uid] = array_merge($existing, [
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
                'resolution_number' => $schedule->resolution_number,
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
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bidding_date' => null,
            'bidding_result' => null,
            'ntf_bidding_date' => null,
            'ntf_bidding_result' => null,
            'ntf_no' => null,
            'rfq_no' => null,
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
            'resolution_number' => null,
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
        $this->activeTab = $step;
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

        DB::transaction(function () use (&$isMopAdded, &$isMopUpdated, &$isScheduleAdded, &$isScheduleUpdated, &$isDeleted) {

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
    private function validateSchedules(): bool
    {
        $isValid = true;

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            if (!$modeId)
                continue;

            $itemNumber = $index + 1;
            $modeName = $this->modeOfProcurements->firstWhere('id', $modeId)?->name ?? "Item {$itemNumber}";

            $matchCriteria = [
                'ref_id' => $this->procID,
                'mop_uid' => $item['uid']
            ];

            // Only validate if this item has been interacted with (has existing record or has data)
            $hasAnyData = false;

            // Check if item has any filled fields
            foreach ($item as $key => $value) {
                if (
                    !in_array($key, ['id', 'uid', 'mode_of_procurement_id', 'mode_order']) &&
                    !is_null($value) && trim($value) !== ''
                ) {
                    $hasAnyData = true;
                    break;
                }
            }

            // Skip validation for empty new items
            if (!$hasAnyData && str_starts_with($item['uid'], 'temp_')) {
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

                    // Check if Pre-Proc Conference is filled
                    $hasPreProcConference = !empty($item['pre_proc_conference']) && trim($item['pre_proc_conference']) !== '';

                    if (!$hasPreProcConference) {
                        // If Pre-Proc Conference is not filled, require all three fields
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
                            $this->scheduleValidationErrors[] = "Cannot set Bidding Result without {$fieldsList} or Pre-Proc Conference.";
                            $isValid = false;
                        }
                    }
                    // If Pre-Proc Conference is filled, allow bidding result (no validation errors)
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

                    // Check if Pre-Proc Conference is filled
                    $hasPreProcConference = !empty($item['pre_proc_conference']) && trim($item['pre_proc_conference']) !== '';

                    if (!$hasPreProcConference) {
                        // If Pre-Proc Conference is not filled, require all three fields
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
                            $this->scheduleValidationErrors[] = "Cannot set NTF Bidding Result without {$fieldsList} or Pre-Proc Conference.";
                            $isValid = false;
                        }
                    }
                    // If Pre-Proc Conference is filled, allow NTF bidding result (no validation errors)
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

                if (!$existingNtfSchedule) {
                    $relatedMopUids = MopLot::where('procID', $refId)
                        ->where('mode_of_procurement_id', $modeId)
                        ->pluck('uid');

                    $count = NtfBidSchedule::where('ref_id', $refId)
                        ->whereIn('mop_uid', $relatedMopUids)
                        ->count();

                    $uid = $parentUid . '-' . ($count + 1);
                } else {
                    $uid = $existingNtfSchedule->uid;
                }

                $model = NtfBidSchedule::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $uid,
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

    public function savePost()
    {
        $rules = [
            'resolutionNumber' => 'required|string|max:255',
            'bidEvaluationDate' => 'nullable|date',
            'postQualDate' => 'nullable|date',
            'recommendingForAward' => 'nullable|date',
            'noticeOfAward' => 'nullable|date',
            'awardedAmount' => 'nullable|numeric',
            'philgepsReferenceNo' => 'nullable|string|max:255',
            'awardNoticeNumber' => 'nullable|string|max:255',
            'dateOfPostingOfAwardOnPhilGEPS' => 'nullable|date',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
        ];

        $attributes = [
            'resolutionNumber' => 'Resolution #',
            'bidEvaluationDate' => 'Bid Evaluation Date',
            'postQualDate' => 'Post Qual Date',
            'recommendingForAward' => 'Recommending For Award',
            'noticeOfAward' => 'Notice of Award Date',
            'awardedAmount' => 'Awarded Amount',
            'philgepsReferenceNo' => 'PhilGEPS Reference #',
            'awardNoticeNumber' => 'Award Notice #',
            'dateOfPostingOfAwardOnPhilGEPS' => 'Posting of Award Date',
            'supplier_id' => 'Supplier',
        ];

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
            $data = [
                'ref_id' => $this->procID,
                'resolution_number' => $this->resolutionNumber,
                'bid_evaluation_date' => $this->nullableDate($this->bidEvaluationDate),
                'post_qual_date' => $this->nullableDate($this->postQualDate),
                'recommending_for_award' => $this->nullableDate($this->recommendingForAward),
                'notice_of_award' => $this->nullableDate($this->noticeOfAward),
                'awarded_amount' => $this->awardedAmount,
                'philgeps_reference_no' => $this->philgepsReferenceNo,
                'award_notice_no' => $this->awardNoticeNumber,
                'date_of_posting_of_award_on_philgeps' => $this->nullableDate($this->dateOfPostingOfAwardOnPhilGEPS),
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
            LivewireAlert::title('Post-Procurement Added!')->success()->text('The procurement award details have been saved.')->toast()->position('top-end')->show();
        } elseif ($isUpdated) {
            LivewireAlert::title('Post-Procurement Updated!')->success()->text('The procurement award details have been updated.')->toast()->position('top-end')->show();
        } else {
            LivewireAlert::title('No Changes')->info()->text('Post-procurement details remain unchanged.')->toast()->position('top-end')->show();
        }
    }

    public function save()
    {
        if ($this->activeTab == 2) {
            $this->savePost();
        } else {
            $this->saveTab1();
        }

        $this->mount($this->procurement);

        // if ($this->isPostAvailable && $this->activeTab == 1) {
        //     $this->activeTab = 2;
        // }
    }

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
        $this->showModal = true;  // Changed from showEditModal
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
        $this->showModal = false;  // Changed from showEditModal
        $this->editingItem = null;
        $this->editingIndex = null;
    }
    private function nullableDate($value)
    {
        return empty($value) ? null : $value;
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
