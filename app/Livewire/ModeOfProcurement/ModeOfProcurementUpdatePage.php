<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\BidSchedule;
use App\Models\Category;
use App\Models\ModeOfProcurement;
use App\Models\NtfBidSchedule;
use App\Models\PostProcurement;
use App\Models\PrSvp;
use App\Models\Supplier;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\MopItem;
use App\Models\MopLot;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Title;

#[Title('Mode of Procurement | PMIS')]
class ModeOfProcurementUpdatePage extends Component
{
    public Procurement $procurement;
    public $form = [];
    public $modeOfProcurements = [];
    protected ?Category $categoryCache = null;
    public $showTable = true;
    public $textareaRows = 1;
    public $page = 1;
    public $perPage = 10;
    public string $procID = '';
    public int $activeTab = 1;
    public bool $showHistory = false;

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
        $procurement->load('pr_items', 'mopLots.modeOfProcurement', 'mopItems.modeOfProcurement', 'mopItems.item');
        $this->procurement = $procurement;
        $this->procID = $procurement->procID ?? '';

        $this->form = $procurement->toArray();

        $this->form['approved_ppmp'] = (bool) ($this->form['approved_ppmp'] ?? false);
        $this->form['app_updated'] = (bool) ($this->form['app_updated'] ?? false);
        $this->form['early_procurement'] = (bool) ($this->form['early_procurement'] ?? false);

        // Normalize procurement_type default
        if (!in_array($this->form['procurement_type'] ?? null, ['perItem', 'perLot'])) {
            $this->form['procurement_type'] = 'perLot';
        }

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

        // Load modes of procurement
        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();

        $this->suppliers = Supplier::all();

        // Load items based on procurement type
        if ($this->form['procurement_type'] === 'perItem') {
            $this->loadPerItemData($procurement);
        } else {
            $this->loadPerLotData($procurement);
        }

        // Handle procurement program project textarea
        if ($procurement) {
            $this->form['procurement_program_project'] = $procurement->procurement_program_project;
            $this->procID = $procurement->procID;

            // Dynamically calculate rows based on text length or line breaks
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
        // 1. Get MOP Items linked to these PR Items
        $mopItems = $procurement->mopItems()
            ->with(['item', 'modeOfProcurement'])
            ->get()
            ->keyBy('prItemID');

        // 2. Get all UIDs to fetch schedules
        $uids = $mopItems->pluck('uid')->filter()->toArray();

        // 3. Fetch Schedules
        $bidSchedules = BidSchedule::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');
        $ntfSchedules = NtfBidSchedule::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');
        $prSvps = PrSvp::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');

        // 4. Map PR Items to Form Rows
        $this->form['items'] = $procurement->pr_items
            ->sortBy('prItemID') // Sort by your actual Primary Key
            ->map(function ($item) use ($mopItems, $bidSchedules, $ntfSchedules, $prSvps) {

                $prItemID = $item->prItemID; // Correct Primary Key
    
                $mopItem = $mopItems->get($prItemID);
                $uid = $mopItem?->uid;

                // Resolvers
                $bid = $uid ? $bidSchedules->get($uid) : null;
                $ntf = $uid ? $ntfSchedules->get($uid) : null;
                $svp = $uid ? $prSvps->get($uid) : null;

                $getVal = fn($key) => $bid?->$key ?? $ntf?->$key ?? $svp?->$key ?? null;

                return [
                    'prItemID' => $prItemID,
                    'item_no' => $item->item_no,
                    'description' => $item->description,
                    'amount' => number_format((float) $item->amount, 2, '.', ''),
                    'mode_of_procurement_id' => $mopItem?->mode_of_procurement_id,

                    // FORCE UNIQUE UID: Even if $uid is null, generate a unique one using the Item ID
                    'uid' => $uid ?? 'row_' . $prItemID,
                    'mode_order' => $mopItem?->mode_order,

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
            })
            ->values()
            ->toArray();

        // Safety check
        if (empty($this->form['items'])) {
            $this->form['items'] = [];
        }
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

        // 3. Fetch Schedules
        // Added strict check for ref_id = procID
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
        $uniqueId = 'new_' . md5(microtime(true) . mt_rand());

        if ($this->form['procurement_type'] === 'perItem') {
            $newItem = [
                'uid' => $uniqueId,
                'item_no' => '',
                'description' => '',
                'amount' => '0.00',
                'mode_of_procurement_id' => null,
                'mode_order' => null,
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
        } else {
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
        }

        // Add the new item to the END of the array
        $this->form['items'][] = $newItem;

        // Re-index mode_order if necessary (for perLot)
        if ($this->form['procurement_type'] === 'perLot') {
            foreach ($this->form['items'] as $index => &$item) {
                $item['mode_order'] = $index + 1;
            }
        }

        $this->showHistory = false;
    }
    public function toggleHistory()
    {
        $this->showHistory = !$this->showHistory;
    }

    public function removeItem($uid): void
    {
        // Filter purely by the UID
        $this->form['items'] = array_filter($this->form['items'], function ($item) use ($uid) {
            return $item['uid'] !== $uid;
        });

        $this->form['items'] = array_values($this->form['items']);

        if ($this->form['procurement_type'] === 'perLot') {
            foreach ($this->form['items'] as $index => &$item) {
                $item['mode_order'] = $index + 1;
            }
        }
    }

    protected function getItemIdentifier(array $item): ?string
    {
        if ($this->form['procurement_type'] === 'perItem') {
            return $item['prItemID'] ?? null;
        } else {
            return $item['uid'] ?? null;
        }
    }

    public function setStep(int $step): void
    {
        $this->activeTab = $step;
    }

    public function saveTab1()
    {
        // ==========================================
        // 1. BUILD VALIDATION RULES DYNAMICALLY
        // ==========================================
        $rules = [
            'form.items.*.mode_of_procurement_id' => 'required|integer',
        ];

        $attributes = [];

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            if (!$modeId)
                continue;

            // -------------------------------------------------------
            // [NEW] VALIDATION FOR MODE ID 4 (NTF / Negotiated 2 Failed)
            // -------------------------------------------------------
            // if ($modeId == 4) {
            //     // Check if user started filling any NTF fields
            //     $hasNtfData = !empty($item['ntf_no']) ||
            //         !empty($item['ntf_bidding_date']) ||
            //         !empty($item['ntf_bidding_result']) ||
            //         !empty($item['ib_number']); // IB might be required for NTF too

            //     if ($hasNtfData) {
            //         $rules["form.items.{$index}.ntf_no"] = 'required|string';
            //         $rules["form.items.{$index}.ntf_bidding_date"] = 'required|date';
            //         $rules["form.items.{$index}.ntf_bidding_result"] = 'required|string';
            //         // $rules["form.items.{$index}.ib_number"]       = 'required|string'; // Uncomment if IB is strict for NTF

            //         // Friendly Names
            //         $attributes["form.items.{$index}.ntf_no"] = "NTF No.";
            //         $attributes["form.items.{$index}.ntf_bidding_date"] = "NTF Date";
            //         $attributes["form.items.{$index}.ntf_bidding_result"] = "NTF Result";
            //         $attributes["form.items.{$index}.ib_number"] = "IB No.";
            //     }
            // }

            // -------------------------------------------------------
            // VALIDATION FOR MODE ID 5 (SVP / Small Value Procurement)
            // -------------------------------------------------------
            if ($modeId == 5) {
                $hasSvpData = !empty($item['rfq_no']) ||
                    !empty($item['canvass_date']) ||
                    !empty($item['date_returned_of_canvass']) ||
                    !empty($item['abstract_of_canvass_date']) ||
                    !empty($item['resolution_number']);

                if ($hasSvpData) {
                    $rules["form.items.{$index}.rfq_no"] = 'required|string';
                    $rules["form.items.{$index}.canvass_date"] = 'required|date';
                    $rules["form.items.{$index}.date_returned_of_canvass"] = 'required|date|after_or_equal:form.items.' . $index . '.canvass_date';
                    $rules["form.items.{$index}.abstract_of_canvass_date"] = 'required|date|after_or_equal:form.items.' . $index . '.date_returned_of_canvass';
                    $rules["form.items.{$index}.resolution_number"] = 'required|string';

                    $attributes["form.items.{$index}.rfq_no"] = "RFQ No.";
                    $attributes["form.items.{$index}.canvass_date"] = "Canvass Date";
                    $attributes["form.items.{$index}.date_returned_of_canvass"] = "Return of Canvass";
                    $attributes["form.items.{$index}.abstract_of_canvass_date"] = "Abstract Date";
                    $attributes["form.items.{$index}.resolution_number"] = "Resolution No.";
                }
            }
        }

        // ==========================================
        // 2. THE GATEKEEPER (Validation)
        // ==========================================
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

        // ==========================================
        // 3. SAVE TO DATABASE
        // ==========================================
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
                    $modeOrder = ($this->form['procurement_type'] === 'perLot') ? ($index + 1) : 1;
                    $generatedUid = "MOP-{$modeId}-{$modeOrder}";
                } else {
                    $generatedUid = $item['uid'];
                    $modeOrder = $item['mode_order'];
                }

                $savedParentModel = null;

                // Save Parent (MopItem/MopLot)
                if ($this->form['procurement_type'] === 'perItem') {
                    $savedParentModel = MopItem::updateOrCreate(
                        ['procID' => $this->procID, 'prItemID' => $item['prItemID']],
                        ['uid' => $generatedUid, 'mode_of_procurement_id' => $modeId, 'mode_order' => $modeOrder]
                    );
                    $processedIds[] = $savedParentModel->id;
                } else {
                    $matchCriteria = ['procID' => $this->procID];
                    if ($isUiNew)
                        $matchCriteria['mode_order'] = $modeOrder;
                    else
                        $matchCriteria['uid'] = $item['uid'];

                    $savedParentModel = MopLot::updateOrCreate(
                        $matchCriteria,
                        ['uid' => $generatedUid, 'mode_of_procurement_id' => $modeId, 'mode_order' => $modeOrder]
                    );
                    $processedIds[] = $savedParentModel->id;
                }

                if ($savedParentModel->wasRecentlyCreated) {
                    $isMopAdded = true;
                } elseif ($savedParentModel->wasChanged()) {
                    $isMopUpdated = true;
                }

                if ($savedParentModel) {
                    $this->saveRelatedSchedules(
                        $savedParentModel,
                        $item,
                        $this->form['procurement_type'],
                        $isScheduleAdded,
                        $isScheduleUpdated
                    );
                }
            }

            $deletedCount = 0;
            if ($this->form['procurement_type'] === 'perItem') {
                $deletedCount = MopItem::where('procID', $this->procID)->whereNotIn('id', $processedIds)->delete();
            } else {
                $deletedCount = MopLot::where('procID', $this->procID)->whereNotIn('id', $processedIds)->delete();
            }

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
        string $type,
        bool &$isScheduleAdded,
        bool &$isScheduleUpdated
    ): void {
        $modeId = $itemData['mode_of_procurement_id'];

        // 1. Define Keys based on Parent Model
        // Requirement: mopLot uses procID -> ref_id, and uid -> mop_uid
        if ($parentModel instanceof MopLot) {
            $refId = $this->procID;
            $parentUid = $parentModel->uid;
        } else {
            // Fallback for MopItem
            $refId = $itemData['prItemID'] ?? $parentModel->prItemID;
            $parentUid = $parentModel->uid;
        }

        // 2. Match Criteria (These are the keys used to find/link the record)
        $matchCriteria = [
            'ref_id' => $refId,
            'mop_uid' => $parentUid
        ];

        // 3. Identity Helper
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

        // =========================================================
        // 1. BID SCHEDULE (Standard Modes)
        // =========================================================
        if (!in_array($modeId, [1, 4, 5])) {
            if (!empty($itemData['ib_number']) && !empty($itemData['bidding_number'])) {

                $identity = $getIdentity(BidSchedule::class);

                $model = BidSchedule::updateOrCreate(
                    $matchCriteria, // Uses procID and uid
                    [
                        'uid' => $identity['uid'],
                        'ref_id' => $refId,      // Explicitly save ref_id
                        'mop_uid' => $parentUid, // Explicitly save mop_uid

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

        // =========================================================
        // 2. NTF BID SCHEDULE (Mode 4)
        // =========================================================
        if ($modeId == 4) {
            if (!empty($itemData['ntf_no'])) {

                $identity = $getIdentity(NtfBidSchedule::class);

                $model = NtfBidSchedule::updateOrCreate(
                    $matchCriteria, // Uses procID and uid
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

        // =========================================================
        // 3. PR SVP (Mode 5)
        // =========================================================
        if ($modeId == 5) {
            if (!empty($itemData['rfq_no'])) {

                $identity = $getIdentity(PrSvp::class);

                $model = PrSvp::updateOrCreate(
                    $matchCriteria, // Uses procID and uid
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
        // 1. Validation Rules
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

        // 2. Save to Database (PostProcurement model)
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

            // Use the new ref_id column for matching
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

    // Update the main save() to conditionally call savePost()
    public function save()
    {
        if ($this->activeTab == 2) {
            $this->savePost();
        } else {
            $this->saveTab1();
        }

        // Re-run mount to reload data and reset state
        $this->mount($this->procurement);

        // Redirect logic from original save
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

        return view('livewire.mode-of-procurement.mode-of-procurement-update-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'suppliers' => $this->suppliers,
        ]);
    }
}
