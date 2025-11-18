<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\BidSchedule;
use App\Models\Category;
use App\Models\ModeOfProcurement;
use App\Models\NtfBidSchedule;
use App\Models\PrSvp;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\MopItem;
use App\Models\MopLot;
use Illuminate\Support\Facades\DB;
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

        // Load modes of procurement
        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();

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

    protected function loadPerItemData(Procurement $procurement)
    {
        // 1. Get MOP Items
        $mopItems = $procurement->mopItems()
            ->with(['item', 'modeOfProcurement'])
            ->get()
            ->keyBy('prItemID');

        // 2. Get all UIDs
        $uids = $mopItems->pluck('uid')->filter()->toArray();

        // 3. Fetch Schedules
        $bidSchedules = BidSchedule::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');
        $ntfSchedules = NtfBidSchedule::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');
        $prSvps = PrSvp::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');

        $this->form['items'] = $procurement->pr_items
            ->sortByDesc('id')
            ->map(function ($item) use ($mopItems, $bidSchedules, $ntfSchedules, $prSvps) {
                $mopItem = $mopItems->get($item->prItemID);
                $uid = $mopItem?->uid;

                // Find matching schedules
                $bid = $uid ? $bidSchedules->get($uid) : null;
                $ntf = $uid ? $ntfSchedules->get($uid) : null;
                $svp = $uid ? $prSvps->get($uid) : null;

                // --- DATA RESOLVER HELPER ---
                // This checks all 3 models for a value.
                // Priority: Bid -> NTF -> SVP (or whichever exists)
                // This ensures if 'rfq_no' is saved in NTF table, we still find it.
                $getVal = fn($key) => $bid?->$key ?? $ntf?->$key ?? $svp?->$key ?? null;

                return [
                    'prItemID' => $item->prItemID,
                    'item_no' => $item->item_no,
                    'description' => $item->description,
                    'amount' => number_format((float) $item->amount, 2, '.', ''),
                    'mode_of_procurement_id' => $mopItem?->mode_of_procurement_id,
                    'uid' => $uid ?? 'row_' . $item->prItemID,
                    'mode_order' => $mopItem?->mode_order,

                    // --- SHARED FIELDS (Check ALL models) ---
                    'ib_number' => $getVal('ib_number'),
                    'pre_proc_conference' => $getVal('pre_proc_conference'),
                    'ads_post_ib' => $getVal('ads_post_ib'),
                    'pre_bid_conf' => $getVal('pre_bid_conf'),
                    'eligibility_check' => $getVal('eligibility_check'),
                    'sub_open_bids' => $getVal('sub_open_bids'),

                    // --- BIDDING SPECIFIC (Check Bid OR NTF) ---
                    // NtfBidSchedule model has 'bidding_number', so we must check $ntf too
                    'bidding_number' => $bid?->bidding_number ?? $ntf?->bidding_number ?? null,
                    'bidding_date' => $bid?->bidding_date ?? $ntf?->bidding_date ?? null,
                    'bidding_result' => $bid?->bidding_result ?? $ntf?->bidding_result ?? null,

                    // --- NTF SPECIFIC (Check NTF only) ---
                    'ntf_no' => $ntf?->ntf_no,
                    'ntf_bidding_date' => $ntf?->ntf_bidding_date,
                    'ntf_bidding_result' => $ntf?->ntf_bidding_result,

                    // --- SVP SPECIFIC (Check SVP OR NTF) ---
                    // NtfBidSchedule model has 'rfq_no' etc, so we must check $ntf too
                    'rfq_no' => $svp?->rfq_no ?? $ntf?->rfq_no ?? null,
                    'canvass_date' => $svp?->canvass_date ?? $ntf?->canvass_date ?? null,
                    'date_returned_of_canvass' => $svp?->date_returned_of_canvass ?? $ntf?->date_returned_of_canvass ?? null,
                    'abstract_of_canvass_date' => $svp?->abstract_of_canvass_date ?? $ntf?->abstract_of_canvass_date ?? null,
                    // Resolution is usually unique to SVP, but if NTF has it, add $ntf?->resolution_number
                    'resolution_number' => $svp?->resolution_number ?? null,
                ];
            })
            ->values()
            ->toArray();

        if (empty($this->form['items'])) {
            $this->addItem();
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

        // 3. Fetch Schedules
        $bidSchedules = BidSchedule::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');
        $ntfSchedules = NtfBidSchedule::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');
        $prSvps = PrSvp::whereIn('mop_uid', $uids)->get()->keyBy('mop_uid');

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

                // --- SHARED FIELDS (Check ALL) ---
                'ib_number' => $getVal('ib_number'),
                'pre_proc_conference' => $getVal('pre_proc_conference'),
                'ads_post_ib' => $getVal('ads_post_ib'),
                'pre_bid_conf' => $getVal('pre_bid_conf'),
                'eligibility_check' => $getVal('eligibility_check'),
                'sub_open_bids' => $getVal('sub_open_bids'),

                // --- BIDDING SPECIFIC (Check Bid OR NTF) ---
                'bidding_number' => $bid?->bidding_number ?? $ntf?->bidding_number ?? null,
                'bidding_date' => $bid?->bidding_date ?? $ntf?->bidding_date ?? null,
                'bidding_result' => $bid?->bidding_result ?? $ntf?->bidding_result ?? null,

                // --- NTF SPECIFIC ---
                'ntf_no' => $ntf?->ntf_no,
                'ntf_bidding_date' => $ntf?->ntf_bidding_date,
                'ntf_bidding_result' => $ntf?->ntf_bidding_result,

                // --- SVP SPECIFIC (Check SVP OR NTF) ---
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

    public function save()
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
            if ($modeId == 4) {
                // Check if user started filling any NTF fields
                $hasNtfData = !empty($item['ntf_no']) ||
                    !empty($item['ntf_bidding_date']) ||
                    !empty($item['ntf_bidding_result']) ||
                    !empty($item['ib_number']); // IB might be required for NTF too

                if ($hasNtfData) {
                    $rules["form.items.{$index}.ntf_no"] = 'required|string';
                    $rules["form.items.{$index}.ntf_bidding_date"] = 'required|date';
                    $rules["form.items.{$index}.ntf_bidding_result"] = 'required|string';
                    // $rules["form.items.{$index}.ib_number"]       = 'required|string'; // Uncomment if IB is strict for NTF

                    // Friendly Names
                    $attributes["form.items.{$index}.ntf_no"] = "NTF No.";
                    $attributes["form.items.{$index}.ntf_bidding_date"] = "NTF Date";
                    $attributes["form.items.{$index}.ntf_bidding_result"] = "NTF Result";
                    $attributes["form.items.{$index}.ib_number"] = "IB No.";
                }
            }

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

            $errorString = '<ul class="text-left list-disc pl-4 mt-2">';
            foreach ($errorMessages as $msg) {
                $errorString .= "<li>{$msg}</li>";
            }
            $errorString .= '</ul>';

            LivewireAlert::title('Validation Failed')
                ->error()
                ->html(true)
                ->text('Please check the following errors:' . $errorString)
                ->toast()->position('top-end')->timer(7000)->show();

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
    }
    protected function saveRelatedSchedules(
        $parentModel,
        array $itemData,
        string $type,
        bool &$isScheduleAdded,
        bool &$isScheduleUpdated
    ): void {
        $modeId = $itemData['mode_of_procurement_id'];
        $parentUid = $parentModel->uid;

        // 1. Determine ref_id
        $refId = ($type === 'perLot') ? $this->procID : ($itemData['prItemID'] ?? $parentModel->prItemID);

        // 2. Match Criteria
        $matchCriteria = ['ref_id' => $refId, 'mop_uid' => $parentUid];

        // 3. Identity Helper (FIXED)
        // We removed 'number' from the return. We only care about the UID.
        $getIdentity = function ($modelClass) use ($matchCriteria, $parentUid) {
            $existing = $modelClass::where($matchCriteria)->first();

            if ($existing) {
                // Return existing UID only
                return ['uid' => $existing->uid];
            } else {
                // Calculate new UID for new records
                $count = $modelClass::where('mop_uid', $parentUid)->count();
                $nextNum = $count + 1;
                return ['uid' => $parentUid . '-' . $nextNum];
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
                    $matchCriteria,
                    [
                        'uid' => $identity['uid'],
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,

                        // CRITICAL FIX: Use user input ($itemData), NOT database value
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
            // We trust the save() validation. Just check if main identifier exists.
            if (!empty($itemData['ntf_no'])) {

                $identity = $getIdentity(NtfBidSchedule::class);

                $model = NtfBidSchedule::updateOrCreate(
                    $matchCriteria,
                    [
                        'uid' => $identity['uid'],
                        'ref_id' => $refId,
                        'mop_uid' => $parentUid,

                        // Ensure these match your Model $fillable exactly
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
            // FIX: Removed the strict && check.
            // If RFQ exists, we attempt to save whatever fields are present.
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
    private function nullableDate($value)
    {
        return empty($value) ? null : $value;
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-update-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
        ]);
    }
}
