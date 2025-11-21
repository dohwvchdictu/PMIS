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
use App\Models\MopItem;
use Illuminate\Support\Facades\DB;

class ModeOfProcurementPerItemPage extends Component
{
    public Procurement $procurement;
    public $form = [];
    public $modeOfProcurements = [];
    public $textareaRows = 1;
    public string $procID = '';
    public int $activeTab = 1;
    public bool $showHistory = false;
    public ?string $historyForUid = null;

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
        $procurement->load('pr_items', 'mopItems.modeOfProcurement', 'mopItems.item');
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

        $this->loadPerItemData($procurement);

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

    protected function loadPerItemData(Procurement $procurement)
    {
        // 1. Load MOP Items grouped strictly by PR Item ID
        // Ensure we sort by mode_order DESC so the newest version is always first
        $mopItemsGrouped = $procurement->mopItems()
            ->with(['item', 'modeOfProcurement'])
            ->orderBy('mode_order', 'desc')
            ->get()
            ->groupBy('prItemID'); // Make sure this matches your DB column (prItemID or pr_item_id)

        // 2. Get UIDs for schedules
        $uids = $procurement->mopItems->pluck('uid')->filter()->toArray();

        // 3. Fetch Schedules
        $bidSchedules = BidSchedule::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');
        $ntfSchedules = NtfBidSchedule::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');
        $prSvps = PrSvp::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');

        $this->form['items'] = [];

        // 4. Loop through PR Items (Sorted by ID to keep them consistent)
        // This ensures Item 1 is processed fully before Item 2 starts
        $sortedPrItems = $procurement->pr_items->sortBy('prItemID');

        foreach ($sortedPrItems as $prItem) {
            $prItemID = $prItem->prItemID;

            // Get all MOP history for THIS specific PR Item
            $relatedMops = $mopItemsGrouped->get($prItemID);

            if ($relatedMops && $relatedMops->count() > 0) {
                foreach ($relatedMops as $mopItem) {
                    $this->form['items'][] = $this->mapItemToRow($prItem, $mopItem, $bidSchedules, $ntfSchedules, $prSvps);
                }
            } else {
                $this->form['items'][] = $this->mapItemToRow($prItem, null, $bidSchedules, $ntfSchedules, $prSvps);
            }
        }
    }

    // Helper to keep the loop clean
    private function mapItemToRow($prItem, $mopItem, $bidSchedules, $ntfSchedules, $prSvps)
    {
        $uid = $mopItem?->uid;

        // Resolvers
        $bid = $uid ? $bidSchedules->get($uid) : null;
        $ntf = $uid ? $ntfSchedules->get($uid) : null;
        $svp = $uid ? $prSvps->get($uid) : null;

        $getVal = fn($key) => $bid?->$key ?? $ntf?->$key ?? $svp?->$key ?? null;

        return [
            'prItemID' => $prItem->prItemID, // Crucial for grouping
            'item_no' => $prItem->item_no,
            'description' => $prItem->description,
            'amount' => number_format((float) $prItem->amount, 2, '.', ''),
            'mode_of_procurement_id' => $mopItem?->mode_of_procurement_id,
            'uid' => $uid ?? 'new_' . uniqid(),
            'mode_order' => $mopItem?->mode_order ?? 1,

            // --- SHARED ---
            'ib_number' => $getVal('ib_number'),
            'pre_proc_conference' => $getVal('pre_proc_conference'),
            'ads_post_ib' => $getVal('ads_post_ib'),
            'pre_bid_conf' => $getVal('pre_bid_conf'),
            'eligibility_check' => $getVal('eligibility_check'),
            'sub_open_bids' => $getVal('sub_open_bids'),

            // --- BID ---
            'bidding_number' => $bid?->bidding_number ?? $ntf?->bidding_number ?? null,
            'bidding_date' => $bid?->bidding_date ?? $ntf?->bidding_date ?? null,
            'bidding_result' => $bid?->bidding_result ?? $ntf?->bidding_result ?? null,

            // --- NTF ---
            'ntf_no' => $ntf?->ntf_no,
            'ntf_bidding_date' => $ntf?->ntf_bidding_date,
            'ntf_bidding_result' => $ntf?->ntf_bidding_result,

            // --- SVP ---
            'rfq_no' => $svp?->rfq_no ?? $ntf?->rfq_no ?? null,
            'canvass_date' => $svp?->canvass_date ?? $ntf?->canvass_date ?? null,
            'date_returned_of_canvass' => $svp?->date_returned_of_canvass ?? $ntf?->date_returned_of_canvass ?? null,
            'abstract_of_canvass_date' => $svp?->abstract_of_canvass_date ?? $ntf?->abstract_of_canvass_date ?? null,
            'resolution_number' => $svp?->resolution_number ?? null,
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
            'prItemID' => $referenceItem['prItemID'], // KEEP THIS LINK
            'item_no' => $referenceItem['item_no'],
            'description' => $referenceItem['description'],
            'amount' => $referenceItem['amount'],
            'uid' => $uniqueId,

            // Reset MOP fields
            'mode_of_procurement_id' => null,
            'mode_order' => ($referenceItem['mode_order'] ?? 0) + 1, // Increment order
            'bidding_number' => null,
            'ib_number' => null,
            // ... set all other schedule fields to null ...
        ];

        // 3. Insert ABOVE the clicked index
        array_splice($this->form['items'], $index, 0, [$newItem]);

        // 4. Ensure history is hidden so the UI doesn't look cluttered
        $this->showHistory = false;
    }
    public function toggleHistory(string $uid)
    {
        if ($this->historyForUid === $uid && $this->showHistory) {
            // If clicking the same item, just toggle off
            $this->showHistory = false;
            $this->historyForUid = null;
        } else {
            // Show history for this specific item
            $this->showHistory = true;
            $this->historyForUid = $uid;
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
                $isUiNew = isset($item['uid']) && (str_starts_with($item['uid'], 'new_') || str_starts_with($item['uid'], 'temp_'));

                if ($isUiNew) {
                    $generatedUid = "MOP-{$modeId}-1";
                } else {
                    $generatedUid = $item['uid'];
                }

                $savedParentModel = MopItem::updateOrCreate(
                    ['procID' => $this->procID, 'prItemID' => $item['prItemID']],
                    ['uid' => $generatedUid, 'mode_of_procurement_id' => $modeId, 'mode_order' => 1]
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

            $deletedCount = MopItem::where('procID', $this->procID)->whereNotIn('id', $processedIds)->delete();
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
        $refId = $itemData['prItemID'] ?? $parentModel->prItemID;
        $parentUid = $parentModel->uid;

        $matchCriteria = [
            'ref_id' => $refId,
            'mop_uid' => $parentUid
        ];

        $getIdentity = function ($modelClass) use ($matchCriteria, $parentUid) {
            $existing = $modelClass::where($matchCriteria)->first();
            if ($existing) {
                return ['uid' => $existing->uid];
            } else {
                $count = $modelClass::where('mop_uid', $parentUid)->count();
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
            if (!empty($itemData['ntf_no'])) {
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
        return view('livewire.mode-of-procurement.mode-of-procurement-per-item-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'suppliers' => $this->suppliers,
        ]);
    }
}
