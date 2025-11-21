<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\BidSchedule;
use App\Models\ModeOfProcurement;
use App\Models\NtfBidSchedule;
use App\Models\PostProcurement;
use App\Models\PrSvp;
use App\Models\Supplier;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\MopLot;
use Illuminate\Support\Facades\DB;

class ModeOfProcurementPerLotPage extends Component
{
    public Procurement $procurement;
    public $form = [];
    public $modeOfProcurements = [];
    public $textareaRows = 1;
    public string $procID = '';
    public int $activeTab = 1;
    public bool $showHistory = false;

    // Post-Procurement Tab Fields
    public $resolutionNumber = null;
    public $bidEvaluationDate = null;
    public $postQualDate = null;
    public $noticeOfAward = null;
    public $recommendingForAward = null;
    public $awardedAmount = null;
    public $philgepsReferenceNo = null;
    public $awardNoticeNumber = null;
    public $dateOfPostingOfAwardOnPhilGEPS = null;
    public $supplier_id = null;
    public $suppliers = [];

    public function mount(Procurement $procurement)
    {
        $procurement->load('mopLots.modeOfProcurement');
        $this->procurement = $procurement;
        $this->procID = $procurement->procID ?? '';

        $this->form = $procurement->toArray();
        $this->form['approved_ppmp'] = (bool) ($this->form['approved_ppmp'] ?? false);
        $this->form['app_updated'] = (bool) ($this->form['app_updated'] ?? false);
        $this->form['early_procurement'] = (bool) ($this->form['early_procurement'] ?? false);

        // Load post-procurement data if exists
        $post = PostProcurement::where('ref_id', $this->procID)->first();
        if ($post) {
            $this->resolutionNumber = $post->resolution_number;
            $this->bidEvaluationDate = $post->bid_evaluation_date;
            $this->postQualDate = $post->post_qual_date;
            $this->noticeOfAward = $post->notice_of_award;
            $this->awardedAmount = $post->awarded_amount;
            $this->philgepsReferenceNo = $post->philgeps_reference_no;
            $this->awardNoticeNumber = $post->award_notice_no;
            $this->dateOfPostingOfAwardOnPhilGEPS = $post->date_of_posting_of_award_on_philgeps;
            $this->supplier_id = $post->supplier_id;
        }

        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();
        $this->suppliers = Supplier::all();

        $this->loadPerLotData($procurement);

        // Handle procurement program project textarea
        if ($procurement) {
            $this->form['procurement_program_project'] = $procurement->procurement_program_project;
            $this->procID = $procurement->procID;

            $text = trim($procurement->procurement_program_project ?? '');
            $lineCount = substr_count($text, "\n") + 1;
            $approxExtraLines = ceil(strlen($text) / 150);
            $this->textareaRows = max($lineCount, $approxExtraLines, 1);
        } else {
            $this->form['procurement_program_project'] = '';
            $this->procID = null;
            $this->textareaRows = 1;
        }
    }

    public function getIsPostAvailableProperty(): bool
    {
        foreach ($this->form['items'] as $item) {
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

    protected function loadPerLotData(Procurement $procurement)
    {
        // 1. Get MOP Lots
        $mopLots = $procurement->mopLots()
            ->with('modeOfProcurement')
            ->orderBy('mode_order')
            ->get();

        // 2. Get all UIDs
        $uids = $mopLots->pluck('uid')->filter()->toArray();
        $procID = $procurement->procID;

        // 3. Fetch Schedules with strict ref_id check
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

        $this->form['items'] = $mopLots->map(function ($mopLot, $index) use ($bidSchedules, $ntfSchedules, $prSvps) {
            $uid = $mopLot->uid;

            $bid = $uid ? $bidSchedules->get($uid) : null;
            $ntf = $uid ? $ntfSchedules->get($uid) : null;
            $svp = $uid ? $prSvps->get($uid) : null;

            // Helper to resolve value from any model
            $getVal = fn($key) => $bid?->$key ?? $ntf?->$key ?? $svp?->$key ?? null;

            return [
                'id' => $mopLot->id ?? null,
                'uid' => $uid ?? 'temp_' . uniqid(),
                'mode_of_procurement_id' => $mopLot->mode_of_procurement_id,
                'mode_order' => $mopLot->mode_order ?? ($index + 1),

                // --- SHARED FIELDS ---
                'ib_number' => $getVal('ib_number'),
                'pre_proc_conference' => $getVal('pre_proc_conference'),
                'ads_post_ib' => $getVal('ads_post_ib'),
                'pre_bid_conf' => $getVal('pre_bid_conf'),
                'eligibility_check' => $getVal('eligibility_check'),
                'sub_open_bids' => $getVal('sub_open_bids'),

                // --- BIDDING SPECIFIC ---
                'bidding_number' => $bid?->bidding_number ?? $ntf?->bidding_number ?? null,
                'bidding_date' => $bid?->bidding_date ?? $ntf?->bidding_date ?? null,
                'bidding_result' => $bid?->bidding_result ?? $ntf?->bidding_result ?? null,

                // --- NTF SPECIFIC ---
                'ntf_no' => $ntf?->ntf_no,
                'ntf_bidding_date' => $ntf?->ntf_bidding_date,
                'ntf_bidding_result' => $ntf?->ntf_bidding_result,

                // --- SVP SPECIFIC ---
                'rfq_no' => $svp?->rfq_no ?? $ntf?->rfq_no ?? null,
                'canvass_date' => $svp?->canvass_date ?? $ntf?->canvass_date ?? null,
                'date_returned_of_canvass' => $svp?->date_returned_of_canvass ?? $ntf?->date_returned_of_canvass ?? null,
                'abstract_of_canvass_date' => $svp?->abstract_of_canvass_date ?? $ntf?->abstract_of_canvass_date ?? null,
                'resolution_number' => $svp?->resolution_number ?? null,
            ];
        })->toArray();

        if (empty($this->form['items'])) {
            $this->form['items'] = [];
        }

        if ($this->isPostAvailable) {
            $this->activeTab = 2;
        }
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

        // Re-index mode_order
        foreach ($this->form['items'] as $index => &$item) {
            $item['mode_order'] = $index + 1;
        }

        $this->showHistory = false;
    }

    public function toggleHistory()
    {
        $this->showHistory = !$this->showHistory;
    }

    public function removeItem($uid): void
    {
        $this->form['items'] = array_filter($this->form['items'], function ($item) use ($uid) {
            return $item['uid'] !== $uid;
        });

        $this->form['items'] = array_values($this->form['items']);

        // Re-index mode_order
        foreach ($this->form['items'] as $index => &$item) {
            $item['mode_order'] = $index + 1;
        }
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

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            if (!$modeId)
                continue;

            // Check if Mode is NOT 1, 4, or 5 (Regular Bidding Mode)
            if (!in_array($modeId, [1, 4, 5])) {

                // 1. Check if ANY field relevant to Regular Bidding is filled out
                // We use OR (||) here. If they typed an IB Number OR a Bidding Number,
                // we assume they intended to add a schedule.
                $hasRegularBiddingData = !empty($item['ib_number']) ||
                    !empty($item['bidding_number']);

                // 2. If partial data exists, enforce the rules
                if ($hasRegularBiddingData) {
                    $rules["form.items.{$index}.ib_number"] = 'required|string|max:255';
                    $rules["form.items.{$index}.bidding_number"] = 'required|string|max:255';

                    // Add friendly names for the "Toast" or Error Message
                    $attributes["form.items.{$index}.ib_number"] = "IB No.";
                    $attributes["form.items.{$index}.bidding_number"] = "Bidding No.";
                }
            }


            if ($modeId == 4) { // NTF Mode
                // Check if ANY schedule field relevant to NTF/Bidding is filled out
                $hasNtfOrBiddingData = !empty($item['ib_number']) ||
                    !empty($item['bidding_number']);

                if ($hasNtfOrBiddingData) {
                    // These rules are triggered IF the user started filling data
                    $rules["form.items.{$index}.ib_number"] = 'required|string|max:255';
                    $rules["form.items.{$index}.bidding_number"] = 'required|string|max:255';

                    // Add friendly names for required fields
                    $attributes["form.items.{$index}.ib_number"] = " IB No.";
                    $attributes["form.items.{$index}.bidding_number"] = " Bidding No.";
                }
            }

            if ($modeId == 5) {
                $hasSvpData = !empty($item['rfq_no']) ||
                    !empty($item['canvass_date']) ||
                    !empty($item['date_returned_of_canvass']) ||
                    !empty($item['abstract_of_canvass_date']) ||
                    !empty($item['resolution_number']);

                if ($hasSvpData) {
                    $rules["form.items.{$index}.rfq_no"] = 'required|string';
                    $rules["form.items.{$index}.canvass_date"] = 'required|date';
                    $rules["form.items.{$index}.date_returned_of_canvass"] = 'required|date:form.items.' . $index . '.canvass_date';
                    $rules["form.items.{$index}.abstract_of_canvass_date"] = 'required|date:form.items.' . $index . '.date_returned_of_canvass';
                    $rules["form.items.{$index}.resolution_number"] = 'required|string';

                    $attributes["form.items.{$index}.rfq_no"] = "RFQ No.";
                    $attributes["form.items.{$index}.canvass_date"] = "Canvass Date";
                    $attributes["form.items.{$index}.date_returned_of_canvass"] = "Return of Canvass";
                    $attributes["form.items.{$index}.abstract_of_canvass_date"] = "Abstract Date";
                    $attributes["form.items.{$index}.resolution_number"] = "Resolution No.";
                }
            }
        }

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

        $isMopAdded = false;
        $isMopUpdated = false;
        $isScheduleAdded = false;
        $isScheduleUpdated = false;
        $isDeleted = false;

        DB::transaction(function () use (&$isMopAdded, &$isMopUpdated, &$isScheduleAdded, &$isScheduleUpdated, &$isDeleted) {
            $processedIds = [];

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
                    ['uid' => $generatedUid, 'mode_of_procurement_id' => $modeId, 'mode_order' => $modeOrder]
                );
                $processedIds[] = $savedParentModel->id;

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

            $deletedCount = MopLot::where('procID', $this->procID)->whereNotIn('id', $processedIds)->delete();
            if ($deletedCount > 0)
                $isDeleted = true;
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

        if ($this->isPostAvailable) {
            $this->activeTab = 2;
        }
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

        $getIdentity = function ($modelClass) use ($matchCriteria, $parentUid, $refId, $modeId) {
            $existing = $modelClass::where($matchCriteria)->first();
            if ($existing) {
                return ['uid' => $existing->uid];
            } else {
                // Find existing MopLots associated with the same Procurement ID ($refId)
                // and the same Mode ID ($modeId).
                $relatedMopUids = MopLot::where('procID', $refId)
                    ->where('mode_of_procurement_id', $modeId)
                    ->pluck('uid');

                // Count existing schedules of this type ($modelClass) that belong to
                // any of the MopLots identified above.
                $count = $modelClass::where('ref_id', $refId)
                    ->whereIn('mop_uid', $relatedMopUids) // <--- THIS LINE IS THE CORE CHANGE
                    ->count();

                // The new sequential number is based on the total count of schedules
                // for this mode/procurement, plus one.
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

        // BID SCHEDULE (Standard Modes)
        if (!in_array($modeId, [1, 4, 5])) {
            if (!empty($itemData['ib_number']) && !empty($itemData['bidding_number'])) {
                $identity = $getIdentity(BidSchedule::class);

                $model = BidSchedule::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $identity['uid'],
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,
                        'bidding_number' => $itemData['bidding_number'],
                        'ib_number' => $itemData['ib_number'],
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
            if (!empty($itemData['ib_number']) && !empty($itemData['bidding_number'])) {
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
                        'ntf_no' => $itemData['ntf_no'],
                        'ntf_bidding_date' => $this->nullableDate($itemData['ntf_bidding_date'] ?? null),
                        'ntf_bidding_result' => $itemData['ntf_bidding_result'] ?? null,
                        'rfq_no' => $itemData['rfq_no'],
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
            if (!empty($itemData['rfq_no'])) {
                $identity = $getIdentity(PrSvp::class);

                $model = PrSvp::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $identity['uid'],
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,
                        'rfq_no' => $itemData['rfq_no'],
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

        if ($this->isPostAvailable && $this->activeTab == 1) {
            $this->activeTab = 2;
        }
    }

    private function nullableDate($value)
    {
        return empty($value) ? null : $value;
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-per-lot-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'suppliers' => $this->suppliers,
        ]);
    }
}
