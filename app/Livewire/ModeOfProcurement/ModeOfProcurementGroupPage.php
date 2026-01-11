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

class ModeOfProcurementGroupPage extends Component
{
    public Collection $procurements;
    public string $ibNumber = '';
    public array $form = [];
    public Collection $modeOfProcurements;
    public Collection $suppliers;
    public array $queryParams = [];
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

    public bool $showModal = false;
    public ?array $editingItem = null;
    public ?int $editingIndex = null;
    public array $scheduleValidationErrors = [];

    public function mount(Procurement $procurement): void
    {
        $this->queryParams = request()->query();

        // Load all procurements with the same IB number
        $this->loadGroupProcurements($procurement);

        // Initialize mode of procurements and suppliers
        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();
        $this->suppliers = Supplier::all();

        // Load the IB number from the first procurement
        $this->ibNumber = $this->getIBNumber($procurement);

        // Initialize form structure
        $this->initializeForm();

        // Load post-procurement data (shared across all PRs in group)
        $this->loadPostProcurementData();
    }

    private function loadGroupProcurements(Procurement $procurement): void
    {
        $ibNumber = $this->getIBNumber($procurement);

        if (!$ibNumber) {
            $this->procurements = collect([$procurement]);
            return;
        }

        // Get all procurements with the same IB number
        $procIDs = BidSchedule::where('ib_number', $ibNumber)
            ->distinct()
            ->pluck('ref_id');

        $this->procurements = Procurement::whereIn('procID', $procIDs)
            ->with(['mopLots.modeOfProcurement'])
            ->get();
    }

    private function getIBNumber(Procurement $procurement): ?string
    {
        $latestMop = $procurement->mopLots()
            ->orderBy('mode_order', 'desc')
            ->first();

        if (!$latestMop) {
            return null;
        }

        $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
            ->where('ref_id', $procurement->procID)
            ->first();

        return $bidSchedule ? $bidSchedule->ib_number : null;
    }

    private function initializeForm(): void
    {
        $this->form = [
            'ib_number' => $this->ibNumber,
            'procurements' => [],
            'items' => [], // Shared schedule history
        ];

        // Load each procurement's data
        foreach ($this->procurements as $procurement) {
            $this->form['procurements'][] = [
                'procID' => $procurement->procID,
                'pr_number' => $procurement->pr_number,
                'procurement_program_project' => $procurement->procurement_program_project,
                'abc' => $procurement->abc,
            ];
        }

        // Load shared schedule data from the first procurement
        if ($this->procurements->isNotEmpty()) {
            $firstProcurement = $this->procurements->first();
            $this->loadSharedScheduleData($firstProcurement);
        }
    }

    private function loadSharedScheduleData(Procurement $procurement): void
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

        // Fetch schedules
        $bidSchedules = BidSchedule::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        $prSvps = PrSvp::whereIn('mop_uid', $uids)
            ->where('ref_id', $procID)
            ->get()
            ->keyBy('mop_uid');

        // Build schedule map
        $scheduleMap = $this->buildScheduleMap($bidSchedules, $prSvps);

        // Map items
        $this->form['items'] = $mopLots->map(function ($mopLot, $index) use ($scheduleMap) {
            $uid = $mopLot->uid;
            $schedule = $uid ? $scheduleMap->get($uid, []) : [];

            return $this->buildItemArray($mopLot, $index, $schedule);
        })->toArray();
    }

    private function buildScheduleMap(Collection $bidSchedules, Collection $prSvps): Collection
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

    private function buildItemArray($mopLot, int $index, array $schedule): array
    {
        return [
            'id' => $mopLot->id ?? null,
            'uid' => $mopLot->uid ?? 'temp_' . uniqid(),
            'mode_of_procurement_id' => $mopLot->mode_of_procurement_id,
            'mode_order' => $mopLot->mode_order ?? ($index + 1),
            'ib_number' => $schedule['ib_number'] ?? null,
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
            'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
            'eligibility_check' => $schedule['eligibility_check'] ?? null,
            'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
            'bidding_number' => $schedule['bidding_number'] ?? null,
            'bidding_date' => $schedule['bidding_date'] ?? null,
            'bidding_result' => $schedule['bidding_result'] ?? null,
            'rfq_no' => $schedule['rfq_no'] ?? null,
            'canvass_date' => $schedule['canvass_date'] ?? null,
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
            'resolution_number' => $schedule['resolution_number'] ?? null,
        ];
    }

    private function loadPostProcurementData(): void
    {
        // Use the first procurement's post data as representative
        if ($this->procurements->isEmpty()) return;

        $firstProcID = $this->procurements->first()->procID;
        $post = PostProcurement::where('ref_id', $firstProcID)->first();

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

    public function addItem(): void
    {
        $newItem = [
            'id' => null,
            'uid' => 'temp_' . uniqid(),
            'mode_of_procurement_id' => null,
            'mode_order' => 1,
            'bidding_number' => null,
            'ib_number' => $this->ibNumber,
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bidding_date' => null,
            'bidding_result' => null,
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

    public function toggleHistory(): void
    {
        $this->showHistory = !$this->showHistory;
    }

    public function setStep(int $step): void
    {
        $this->activeTab = $step;
    }

    public function getIsPostAvailableProperty(): bool
    {
        foreach ($this->form['items'] ?? [] as $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;

            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $bidResult = $item['bidding_result'] ?? '';
                if ($bidResult === 'SUCCESSFUL') {
                    return true;
                }
            }

            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                if (!empty($item['rfq_no']) && !empty($item['canvass_date']) &&
                    !empty($item['date_returned_of_canvass']) &&
                    !empty($item['abstract_of_canvass_date']) &&
                    !empty($item['resolution_number'])) {
                    return true;
                }
            }
        }

        return false;
    }

    public function saveTab1(): void
    {
        $rules = [
            'form.items.*.mode_of_procurement_id' => 'required|integer',
        ];

        try {
            $this->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();
            $errorString = implode(' ', $errorMessages);

            LivewireAlert::title('Validation Failed')
                ->error()
                ->text('Please check the following errors: ' . $errorString)
                ->toast()->position('top-end')->show();

            throw $e;
        }

        // Validate schedules
        $this->scheduleValidationErrors = [];
        if (!$this->validateSchedules()) {
            $errorMessage = implode(' ', $this->scheduleValidationErrors);
            LivewireAlert::title('Validation Failed')
                ->error()
                ->text($errorMessage)
                ->toast()->position('top-end')->show();
            return;
        }

        DB::transaction(function () {
            // Update all procurements in the group with the same schedule
            foreach ($this->procurements as $procurement) {
                $this->updateProcurementSchedule($procurement);
            }
        });

        LivewireAlert::title('Group Updated Successfully!')
            ->success()
            ->text('All PRs in this IB group have been updated.')
            ->toast()->position('top-end')->show();

        // Reload data
        $this->mount($this->procurements->first());
    }

    private function updateProcurementSchedule(Procurement $procurement): void
    {
        foreach ($this->form['items'] as $index => $item) {
            if (empty($item['mode_of_procurement_id'])) continue;

            $modeId = $item['mode_of_procurement_id'];
            $modeOrder = $index + 1;
            $isUiNew = str_starts_with($item['uid'], 'temp_');

            $generatedUid = $isUiNew
                ? "MOP-{$modeId}-{$modeOrder}"
                : $item['uid'];

            $matchCriteria = ['procID' => $procurement->procID];
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

            if ($savedParentModel) {
                $this->saveRelatedSchedules($savedParentModel, $item, $procurement->procID);
            }
        }
    }

    private function saveRelatedSchedules($parentModel, array $itemData, string $refId): void
    {
        $modeId = $itemData['mode_of_procurement_id'];
        $parentUid = $parentModel->uid;

        $matchCriteria = [
            'ref_id' => $refId,
            'mop_uid' => $parentUid
        ];

        // Save bidding schedules for modes 2-6
        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            $hasBiddingData = !empty($itemData['ib_number']) ||
                !empty($itemData['bidding_number']) ||
                !empty($itemData['pre_proc_conference']) ||
                !empty($itemData['ads_post_ib']) ||
                !empty($itemData['pre_bid_conf']) ||
                !empty($itemData['eligibility_check']) ||
                !empty($itemData['sub_open_bids']) ||
                !empty($itemData['bidding_date']) ||
                !empty($itemData['bidding_result']);

            if ($hasBiddingData) {
                $existingBidSchedule = BidSchedule::where($matchCriteria)->first();

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

                BidSchedule::updateOrCreate(
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
            }
        }

        // Save SVP schedules for modes 7-24
        if (in_array($modeId, range(7, 24))) {
            $hasSvpData = !empty($itemData['rfq_no']) ||
                !empty($itemData['canvass_date']) ||
                !empty($itemData['date_returned_of_canvass']) ||
                !empty($itemData['abstract_of_canvass_date']) ||
                !empty($itemData['resolution_number']);

            if ($hasSvpData) {
                $existingSvp = PrSvp::where($matchCriteria)->first();

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

                PrSvp::updateOrCreate(
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
            }
        }
    }

    private function validateSchedules(): bool
    {
        $isValid = true;

        foreach ($this->form['items'] as $index => $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            if (!$modeId) continue;

            $hasAnyData = false;
            foreach ($item as $key => $value) {
                if (!in_array($key, ['id', 'uid', 'mode_of_procurement_id', 'mode_order']) &&
                    !is_null($value) && trim($value) !== '') {
                    $hasAnyData = true;
                    break;
                }
            }

            if (!$hasAnyData && str_starts_with($item['uid'], 'temp_')) {
                continue;
            }

            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $biddingResult = $item['bidding_result'] ?? null;
                if (!is_null($biddingResult) && trim($biddingResult) !== '') {
                    $hasPreProcConference = !empty($item['pre_proc_conference']) &&
                        trim($item['pre_proc_conference']) !== '';

                    if (!$hasPreProcConference) {
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
                            $this->scheduleValidationErrors[] = "Cannot set Bidding Result without {$fieldsList} or Pre-Proc Conference.";
                            $isValid = false;
                        }
                    }
                }
            }
        }

        return $isValid;
    }

    public function savePost(): void
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

        try {
            $this->validate($rules);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errorMessages = $e->validator->errors()->all();
            $errorString = implode(' ', $errorMessages);

            LivewireAlert::title('Validation Failed')
                ->error()
                ->text('Please check the following errors: ' . $errorString)
                ->toast()->position('top-end')->show();

            throw $e;
        }

        DB::transaction(function () {
            $data = [
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

            // Update post-procurement for all PRs in the group
            foreach ($this->procurements as $procurement) {
                PostProcurement::updateOrCreate(
                    ['ref_id' => $procurement->procID],
                    array_merge($data, ['ref_id' => $procurement->procID])
                );
            }
        });

        LivewireAlert::title('Post-Procurement Updated!')
            ->success()
            ->text('Award details have been saved for all PRs in this group.')
            ->toast()->position('top-end')->show();
    }

    public function save(): void
    {
        if ($this->activeTab == 2) {
            $this->savePost();
        } else {
            $this->saveTab1();
        }

        $this->mount($this->procurements->first());
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

        $this->editingIndex = $originalIndex;
        $this->editingItem = $this->form['items'][$originalIndex];
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

            $this->mount($this->procurements->first());
            return;
        }

        $this->saveTab1();
        $this->closeEditModal();
    }

    public function closeEditModal(): void
    {
        $this->showModal = false;
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
        return view('livewire.mode-of-procurement.mode-of-procurement-group-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'suppliers' => $this->suppliers,
        ]);
    }
}
