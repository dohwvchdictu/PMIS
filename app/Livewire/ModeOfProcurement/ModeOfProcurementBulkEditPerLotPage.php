<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\BidSchedule;
use App\Models\ModeOfProcurement;
use App\Models\PrSvp;
use Illuminate\Support\Collection;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\MopItem;
use App\Models\MopLot;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModeOfProcurementBulkEditPerLotPage extends Component
{
    public array $items = [];
    public Collection $modeOfProcurements;
    public array $procurementIds = [];
    public bool $showHistory = false;
    public ?string $historyForKey = null;
    public array $bulkEdit = [];
    public array $scheduleValidationErrors = [];
    public int $activeTab = 1;

    public function mount(): void
    {
        $this->procurementIds = request()->query('items', []);

        if (empty($this->procurementIds)) {
            session()->flash('alert', [
                'type' => 'warning',
                'title' => 'No Items Selected',
                'message' => 'Please select procurement items to edit.'
            ]);
            $this->redirect(route('mode-of-procurement.index'));
            return;
        }

        $this->modeOfProcurements = ModeOfProcurement::orderBy('id', 'asc')->get();
        $this->loadProcurementData();
        $this->populateBulkEditData();
    }

    private function hasValue($value): bool
    {
        return !empty($value) && $value !== null && $value !== '';
    }

    private function hasAnyValue(array $fields): bool
    {
        foreach ($fields as $field) {
            if ($this->hasValue($this->bulkEdit[$field] ?? '')) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if item has any schedule data
     */
    private function itemHasSchedule(array $item): bool
    {
        $scheduleFields = [
            'bidding_number',
            'ib_number',
            'philgeps_posting_ref_no',
            'ads_post_ib',
            'pre_proc_conference',
            'pre_bid_conf',
            'eligibility_check',
            'sub_open_bids',
            'bid_evaluation_date',
            'post_qualification_date',
            'bidding_result',
            'resolution_number_mop',
            'rfq_no',
            'canvass_date',
            'date_returned_of_canvass',
            'abstract_of_canvass_date'
        ];

        foreach ($scheduleFields as $field) {
            if ($this->hasValue($item[$field] ?? '')) {
                return true;
            }
        }

        return false;
    }

    private function validateBulkSchedules(): bool
    {
        $this->scheduleValidationErrors = [];
        $modeId = $this->bulkEdit['mode_of_procurement_id'] ?? null;

        if (!$modeId) {
            $this->scheduleValidationErrors[] = 'Mode of Procurement is required.';
            return false;
        }

        $biddingResult = $this->bulkEdit['bidding_result'] ?? '';

        // Only validate for competitive bidding modes (2-6)
        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            if ($biddingResult === 'SUCCESSFUL') {
                // All fields required for SUCCESSFUL
                $requiredFields = [
                    'ads_post_ib' => 'Ads/Post IB',
                    'pre_proc_conference' => 'Pre-Proc Conference',
                    'list_invited_observers' => 'List of Invited Observers',
                    'obsrvr_prebid_conf' => 'Observers (Pre-Bid)',
                    'obsrvr_eligibility' => 'Observers (Eligibility)',
                    'obsrvr_sub_open_of_bid' => 'Observers (Sub/Open)',
                    'obsrvr_bid' => 'Observers (Bid)',
                    'obsrvr_post_qual' => 'Observers (Post Qual)',
                    'pre_bid_conf' => 'Pre-Bid Conference',
                    'eligibility_check' => 'Eligibility Check',
                    'sub_open_bids' => 'Sub/Open of Bids',
                    'bid_evaluation_date' => 'Bid Evaluation Date',
                    'post_qualification_date' => 'Post Qualification Date',
                ];

                foreach ($requiredFields as $field => $label) {
                    if (!$this->hasValue($this->bulkEdit[$field] ?? '')) {
                        $this->scheduleValidationErrors[] = "For SUCCESSFUL bidding: {$label} is required.";
                    }
                }
            } elseif (in_array($biddingResult, ['UNSUCCESSFUL', 'FAILED REBIDDING'])) {
                // Limited fields required for UNSUCCESSFUL/FAILED REBIDDING
                $requiredFields = [
                    'ads_post_ib' => 'Ads/Post IB',
                    'pre_bid_conf' => 'Pre-Bid Conference',
                    'eligibility_check' => 'Eligibility Check',
                    'sub_open_bids' => 'Sub/Open of Bids',
                ];

                foreach ($requiredFields as $field => $label) {
                    if (!$this->hasValue($this->bulkEdit[$field] ?? '')) {
                        $this->scheduleValidationErrors[] = "For {$biddingResult}: {$label} is required.";
                    }
                }
            }
        }

        return empty($this->scheduleValidationErrors);
    }

    private function validateExistingScheduleDeletion(): bool
    {
        // Check if user is trying to clear existing schedules without providing new data
        $currentItems = $this->getCurrentItems();

        foreach ($currentItems as $item) {
            $modeId = $item['mode_of_procurement_id'];

            // For competitive bidding
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $existingFields = [
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

                $hasExistingData = false;
                foreach ($existingFields as $field) {
                    if ($this->hasValue($item[$field] ?? '')) {
                        $hasExistingData = true;
                        break;
                    }
                }

                if ($hasExistingData && !$this->hasAnyValue($existingFields)) {
                    $this->scheduleValidationErrors[] = "Cannot clear all existing bidding schedule data for PR #{$item['pr_number']}.";
                    return false;
                }
            }

            // For SVP/Alternative modes
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                $existingFields = [
                    'resolution_number_mop',
                    'rfq_no',
                    'canvass_date',
                    'date_returned_of_canvass',
                    'abstract_of_canvass_date'
                ];

                $hasExistingData = false;
                foreach ($existingFields as $field) {
                    if ($this->hasValue($item[$field] ?? '')) {
                        $hasExistingData = true;
                        break;
                    }
                }

                if ($hasExistingData && !$this->hasAnyValue($existingFields)) {
                    $this->scheduleValidationErrors[] = "Cannot clear all existing SVP schedule data for PR #{$item['pr_number']}.";
                    return false;
                }
            }
        }

        return true;
    }

    private function canModifySuccessfulBids(): bool
    {
        // Check if user has permission to modify items with post-procurement data
        if (!auth()->user()->can('edit_mode::of::procurement')) {
            $currentItems = $this->getCurrentItems();

            foreach ($currentItems as $item) {
                $biddingResult = $item['bidding_result'] ?? '';

                if ($biddingResult === 'SUCCESSFUL') {
                    $refId = $item['procurement_type'] === 'perLot'
                        ? $item['procID']
                        : $item['prItemID'];

                    $hasPostData = \App\Models\PostProcurement::where('ref_id', $refId)->exists();

                    if ($hasPostData) {
                        $this->scheduleValidationErrors[] = "Cannot modify PR #{$item['pr_number']} - it has a SUCCESSFUL bidding result with post-procurement data. This requires 'Edit Mode of Procurement' permission.";
                        return false;
                    }
                }
            }
        }

        return true;
    }

    private function getCurrentItems(): array
    {
        // Group items by procurement and get the one with highest mode_order (current mode)
        $groupedByProc = [];

        foreach ($this->items as $item) {
            $key = $item['procurement_type'] === 'perLot' ?
                'lot_' . $item['procID'] :
                'item_' . $item['prItemID'];

            if (!isset($groupedByProc[$key]) || $item['mode_order'] > $groupedByProc[$key]['mode_order']) {
                $groupedByProc[$key] = $item;
            }
        }

        return array_values($groupedByProc);
    }

    private function populateBulkEditData(): void
    {
        // Get all current items (highest mode_order) from all selected PRs
        if (empty($this->items)) {
            return;
        }

        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            \Log::warning('No current items found', ['total_items' => count($this->items)]);
            return;
        }

        \Log::info('Found current items', ['count' => count($currentItems)]);

        // Fields to check for identical values
        $fields = [
            'mode_of_procurement_id',
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
            'resolution_number_mop',
            'resolution_number',
            'rfq_no',
            'canvass_date',
            'date_returned_of_canvass',
            'abstract_of_canvass_date'
        ];

        // For each field, check if all items have the same value
        foreach ($fields as $field) {
            $values = array_map(fn($item) => $item[$field] ?? '', $currentItems);
            $uniqueValues = array_unique($values);

            // If all values are identical, use that value; otherwise leave empty
            if (count($uniqueValues) === 1) {
                $this->bulkEdit[$field] = reset($values);
            } else {
                $this->bulkEdit[$field] = '';
            }
        }

        \Log::info('Bulk edit populated', [
            'mode_id' => $this->bulkEdit['mode_of_procurement_id'],
            'bidding_number' => $this->bulkEdit['bidding_number']
        ]);
    }

    private function loadProcurementData(): void
    {
        $procurements = Procurement::with(['pr_items', 'mopLots', 'mopItems'])
            ->whereIn('procID', $this->procurementIds)
            ->orderBy('pr_number')
            ->get();

        // Get all prItemIDs and procIDs for schedules
        $prItemIds = [];
        $procIds = [];

        foreach ($procurements as $procurement) {
            $procIds[] = $procurement->procID;
            if ($procurement->procurement_type === 'perItem') {
                $prItemIds = array_merge($prItemIds, $procurement->pr_items->pluck('prItemID')->toArray());
            }
        }

        // Fetch all schedules at once
        $bidSchedules = BidSchedule::whereIn('ref_id', array_merge($prItemIds, $procIds))->get();
        $prSvps = PrSvp::whereIn('ref_id', array_merge($prItemIds, $procIds))->get();

        // Build schedule maps
        $scheduleMap = $this->buildScheduleMap($bidSchedules, $prSvps);

        $this->items = [];

        foreach ($procurements as $procurement) {
            if ($procurement->procurement_type === 'perLot') {
                // Load all modes for perLot, not just the latest
                $mopLots = $procurement->mopLots()
                    ->with('modeOfProcurement')
                    ->orderBy('mode_order', 'desc')
                    ->get();

                foreach ($mopLots as $mopLot) {
                    $this->items[] = $this->buildPerLotRowFromMop($procurement, $mopLot, $scheduleMap);
                }
            } else {
                // For perItem, load all modes for each item
                foreach ($procurement->pr_items->sortBy('prItemID') as $prItem) {
                    $mopItems = MopItem::where('prItemID', $prItem->prItemID)
                        ->with('modeOfProcurement')
                        ->orderBy('mode_order', 'desc')
                        ->get();

                    foreach ($mopItems as $mopItem) {
                        $this->items[] = $this->buildPerItemRowFromMop($procurement, $prItem, $mopItem, $scheduleMap);
                    }
                }
            }
        }
    }

    private function buildScheduleMap(Collection $bidSchedules, Collection $prSvps): Collection
    {
        $map = collect();

        foreach ($bidSchedules as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $map[$refId][$schedule->mop_uid] = [
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

        foreach ($prSvps as $schedule) {
            $refId = $schedule->ref_id;
            if (!$map->has($refId)) {
                $map[$refId] = collect();
            }
            $mopUid = $schedule->mop_uid;
            $existing = $map[$refId]->get($mopUid, []);

            $map[$refId][$mopUid] = array_merge($existing, [
                'resolution_number' => $schedule->resolution_number,
                'rfq_no' => $schedule->rfq_no,
                'canvass_date' => $schedule->canvass_date,
                'date_returned_of_canvass' => $schedule->date_returned_of_canvass,
                'abstract_of_canvass_date' => $schedule->abstract_of_canvass_date,
            ]);
        }

        return $map;
    }

    private function buildPerLotRowFromMop(Procurement $procurement, $mopLot, Collection $scheduleMap): array
    {
        $schedule = [];
        if ($mopLot && $scheduleMap->has($procurement->procID)) {
            $schedule = $scheduleMap[$procurement->procID][$mopLot->uid] ?? [];
        }

        return [
            'procID' => $procurement->procID,
            'prItemID' => null,
            'pr_number' => $procurement->pr_number,
            'procurement_program_project' => $procurement->procurement_program_project,
            'procurement_type' => 'perLot',
            'mop_id' => $mopLot?->id,
            'mop_uid' => $mopLot?->uid,
            'mode_of_procurement_id' => $mopLot?->mode_of_procurement_id,
            'mode_order' => $mopLot?->mode_order ?? 0,

            // Schedule fields
            'bidding_number' => $schedule['bidding_number'] ?? '',
            'ib_number' => $schedule['ib_number'] ?? '',
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? '',
            'ads_post_ib' => $schedule['ads_post_ib'] ?? '',
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? '',
            'list_invited_observers' => $schedule['list_invited_observers'] ?? '',
            'obsrvr_prebid_conf' => $schedule['obsrvr_prebid_conf'] ?? '',
            'obsrvr_eligibility' => $schedule['obsrvr_eligibility'] ?? '',
            'obsrvr_sub_open_of_bid' => $schedule['obsrvr_sub_open_of_bid'] ?? '',
            'obsrvr_bid' => $schedule['obsrvr_bid'] ?? '',
            'obsrvr_post_qual' => $schedule['obsrvr_post_qual'] ?? '',
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? '',
            'eligibility_check' => $schedule['eligibility_check'] ?? '',
            'sub_open_bids' => $schedule['sub_open_bids'] ?? '',
            'bid_evaluation_date' => $schedule['bid_evaluation_date'] ?? '',
            'post_qualification_date' => $schedule['post_qualification_date'] ?? '',
            'bidding_result' => $schedule['bidding_result'] ?? '',
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? '',
            'resolution_number' => $schedule['resolution_number'] ?? '',
            'rfq_no' => $schedule['rfq_no'] ?? '',
            'canvass_date' => $schedule['canvass_date'] ?? '',
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? '',
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? '',
        ];
    }

    private function buildPerItemRowFromMop(Procurement $procurement, $prItem, $mopItem, Collection $scheduleMap): array
    {
        $schedule = [];
        if ($mopItem && $scheduleMap->has($prItem->prItemID)) {
            $schedule = $scheduleMap[$prItem->prItemID][$mopItem->uid] ?? [];
        }

        return [
            'procID' => $procurement->procID,
            'prItemID' => $prItem->prItemID,
            'pr_number' => $procurement->pr_number,
            'procurement_program_project' => $prItem->description ?? $procurement->procurement_program_project,
            'procurement_type' => 'perItem',
            'mop_id' => $mopItem?->id,
            'mop_uid' => $mopItem?->uid,
            'mode_of_procurement_id' => $mopItem?->mode_of_procurement_id,
            'mode_order' => $mopItem?->mode_order ?? 0,

            // Schedule fields
            'bidding_number' => $schedule['bidding_number'] ?? '',
            'ib_number' => $schedule['ib_number'] ?? '',
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? '',
            'ads_post_ib' => $schedule['ads_post_ib'] ?? '',
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? '',
            'list_invited_observers' => $schedule['list_invited_observers'] ?? '',
            'obsrvr_prebid_conf' => $schedule['obsrvr_prebid_conf'] ?? '',
            'obsrvr_eligibility' => $schedule['obsrvr_eligibility'] ?? '',
            'obsrvr_sub_open_of_bid' => $schedule['obsrvr_sub_open_of_bid'] ?? '',
            'obsrvr_bid' => $schedule['obsrvr_bid'] ?? '',
            'obsrvr_post_qual' => $schedule['obsrvr_post_qual'] ?? '',
            'pre_bid_conf' => $schedule['pre_bid_conf'] ?? '',
            'eligibility_check' => $schedule['eligibility_check'] ?? '',
            'sub_open_bids' => $schedule['sub_open_bids'] ?? '',
            'bid_evaluation_date' => $schedule['bid_evaluation_date'] ?? '',
            'post_qualification_date' => $schedule['post_qualification_date'] ?? '',
            'bidding_result' => $schedule['bidding_result'] ?? '',
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? '',
            'resolution_number' => $schedule['resolution_number'] ?? '',
            'rfq_no' => $schedule['rfq_no'] ?? '',
            'canvass_date' => $schedule['canvass_date'] ?? '',
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? '',
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? '',
        ];
    }

    private function buildPerLotRow(Procurement $procurement, Collection $scheduleMap): array
    {
        $latestMop = $procurement->mopLots()
            ->with('modeOfProcurement')
            ->orderBy('mode_order', 'desc')
            ->first();

        $schedule = [];
        if ($latestMop && $scheduleMap->has($procurement->procID)) {
            $schedule = $scheduleMap[$procurement->procID][$latestMop->uid] ?? [];
        }

        return [
            'procID' => $procurement->procID,
            'prItemID' => null,
            'pr_number' => $procurement->pr_number,
            'procurement_program_project' => $procurement->procurement_program_project,
            'procurement_type' => 'perLot',
            'mop_id' => $latestMop?->id,
            'mop_uid' => $latestMop?->uid,
            'mode_of_procurement_id' => $latestMop?->mode_of_procurement_id,
            'mode_order' => $latestMop?->mode_order ?? 0,

            // Schedule fields
            'bidding_number' => $schedule['bidding_number'] ?? '',
            'ib_number' => $schedule['ib_number'] ?? '',
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? '',
            'ads_post_ib' => $schedule['ads_post_ib'] ?? '',
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? '',
            'list_of_invited_observers' => $schedule['list_of_invited_observers'] ?? '',
            'observer_1' => $schedule['observer_1'] ?? '',
            'observer_2' => $schedule['observer_2'] ?? '',
            'observer_3' => $schedule['observer_3'] ?? '',
            'observer_4' => $schedule['observer_4'] ?? '',
            'observer_5' => $schedule['observer_5'] ?? '',
            'pre_bid_conference' => $schedule['pre_bid_conference'] ?? '',
            'eligibility_check' => $schedule['eligibility_check'] ?? '',
            'submission_opening_of_bids' => $schedule['submission_opening_of_bids'] ?? '',
            'bid_evaluation' => $schedule['bid_evaluation'] ?? '',
            'post_qualification' => $schedule['post_qualification'] ?? '',
            'bidding_result' => $schedule['bidding_result'] ?? '',
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? '',
            'resolution_number' => $schedule['resolution_number'] ?? '',
            'rfq_no' => $schedule['rfq_no'] ?? '',
            'canvass_date' => $schedule['canvass_date'] ?? '',
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? '',
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? '',
        ];
    }

    private function buildPerItemRow(Procurement $procurement, $prItem, Collection $scheduleMap): array
    {
        $latestMop = MopItem::where('prItemID', $prItem->prItemID)
            ->with('modeOfProcurement')
            ->orderBy('mode_order', 'desc')
            ->first();

        $schedule = [];
        if ($latestMop && $scheduleMap->has($prItem->prItemID)) {
            $schedule = $scheduleMap[$prItem->prItemID][$latestMop->uid] ?? [];
        }

        return [
            'procID' => $procurement->procID,
            'prItemID' => $prItem->prItemID,
            'pr_number' => $procurement->pr_number,
            'procurement_program_project' => $prItem->description ?? $procurement->procurement_program_project,
            'procurement_type' => 'perItem',
            'mop_id' => $latestMop?->id,
            'mop_uid' => $latestMop?->uid,
            'mode_of_procurement_id' => $latestMop?->mode_of_procurement_id,
            'mode_order' => $latestMop?->mode_order ?? 0,

            // Schedule fields
            'bidding_number' => $schedule['bidding_number'] ?? '',
            'ib_number' => $schedule['ib_number'] ?? '',
            'philgeps_posting_ref_no' => $schedule['philgeps_posting_ref_no'] ?? '',
            'ads_post_ib' => $schedule['ads_post_ib'] ?? '',
            'pre_proc_conference' => $schedule['pre_proc_conference'] ?? '',
            'list_of_invited_observers' => $schedule['list_of_invited_observers'] ?? '',
            'observer_1' => $schedule['observer_1'] ?? '',
            'observer_2' => $schedule['observer_2'] ?? '',
            'observer_3' => $schedule['observer_3'] ?? '',
            'observer_4' => $schedule['observer_4'] ?? '',
            'observer_5' => $schedule['observer_5'] ?? '',
            'pre_bid_conference' => $schedule['pre_bid_conference'] ?? '',
            'eligibility_check' => $schedule['eligibility_check'] ?? '',
            'submission_opening_of_bids' => $schedule['submission_opening_of_bids'] ?? '',
            'bid_evaluation' => $schedule['bid_evaluation'] ?? '',
            'post_qualification' => $schedule['post_qualification'] ?? '',
            'bidding_result' => $schedule['bidding_result'] ?? '',
            'resolution_number_mop' => $schedule['resolution_number_mop'] ?? '',
            'resolution_number' => $schedule['resolution_number'] ?? '',
            'rfq_no' => $schedule['rfq_no'] ?? '',
            'canvass_date' => $schedule['canvass_date'] ?? '',
            'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? '',
            'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? '',
        ];
    }

    private function validateAbcThreshold(): bool
    {
        // Get all procurements
        $procurements = Procurement::whereIn('procID', $this->procurementIds)->get();

        $hasBelow200k = false;
        $hasAbove200k = false;
        $differentPRs = [];

        foreach ($procurements as $procurement) {
            $abcAmount = $procurement->abc ?? 0;
            
            if ($abcAmount < 200000) {
                $hasBelow200k = true;
                if ($hasAbove200k) {
                    $differentPRs[] = $procurement->pr_number;
                }
            } else {
                $hasAbove200k = true;
                if ($hasBelow200k) {
                    $differentPRs[] = $procurement->pr_number;
                }
            }
        }

        // If mismatch found
        if ($hasBelow200k && $hasAbove200k) {
            $prList = implode(', ', $differentPRs);
            LivewireAlert::title('ABC Threshold Mismatch')
                ->warning()
                ->text("PR {$prList} " . (count($differentPRs) > 1 ? 'have' : 'has') . " different ABC threshold. All PRs must be below ₱200K or ₱200K and above.")
                ->toast()
                ->position('top-end')
                ->show();
            return false;
        }

        return true;
    }

    public function save()
    {
        // Validate ABC threshold first
        if (!$this->validateAbcThreshold()) {
            return;
        }

        // Validate before starting transaction
        if (!$this->validateBulkSchedules()) {
            foreach ($this->scheduleValidationErrors as $error) {
                LivewireAlert::title('Validation Error')
                    ->warning()
                    ->text($error)
                    ->toast()
                    ->position('top-end')
                    ->show();
            }
            return;
        }

        // Check for permission to modify successful bids
        if (!$this->canModifySuccessfulBids()) {
            foreach ($this->scheduleValidationErrors as $error) {
                LivewireAlert::title('Permission Denied')
                    ->warning()
                    ->text($error)
                    ->toast()
                    ->position('top-end')
                    ->show();
            }
            return;
        }

        // Validate against clearing existing data
        if (!$this->validateExistingScheduleDeletion()) {
            foreach ($this->scheduleValidationErrors as $error) {
                LivewireAlert::title('Validation Error')
                    ->warning()
                    ->text($error)
                    ->toast()
                    ->position('top-end')
                    ->show();
            }
            return;
        }

        DB::transaction(function () {
            // Only save current items (highest mode_order), not history
            $currentItems = $this->getCurrentItems();
            foreach ($currentItems as $item) {
                $this->updateItem($item);
            }
        });

        LivewireAlert::title('Bulk Update Successful!')
            ->success()
            ->text('All items have been updated.')
            ->toast()
            ->position('top-end')
            ->show();

        return $this->redirect(route('mode-of-procurement.index'));
    }

    private function updateItem(array $item): void
    {
        if ($item['procurement_type'] === 'perLot') {
            $this->updatePerLotSchedules($item);
        } else {
            $this->updatePerItemSchedules($item);
        }
    }

    private function updatePerLotSchedules(array $item): void
    {
        if (!$item['mop_uid']) {
            return;
        }

        $modeId = $item['mode_of_procurement_id'];
        $refId = $item['procID'];
        $mopUid = $item['mop_uid'];

        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            BidSchedule::updateOrCreate(
                ['mop_uid' => $mopUid, 'ref_id' => $refId],
                [
                    'bidding_number' => $this->nullableValue($this->bulkEdit['bidding_number'] ?? ''),
                    'ib_number' => $this->nullableValue($this->bulkEdit['ib_number'] ?? ''),
                    'philgeps_posting_ref_no' => $this->nullableValue($this->bulkEdit['philgeps_posting_ref_no'] ?? ''),
                    'ads_post_ib' => $this->nullableDate($this->bulkEdit['ads_post_ib'] ?? ''),
                    'pre_proc_conference' => $this->nullableDate($this->bulkEdit['pre_proc_conference'] ?? ''),
                    'list_invited_observers' => $this->nullableDate($this->bulkEdit['list_invited_observers'] ?? ''),
                    'obsrvr_prebid_conf' => $this->nullableDate($this->bulkEdit['obsrvr_prebid_conf'] ?? ''),
                    'obsrvr_eligibility' => $this->nullableDate($this->bulkEdit['obsrvr_eligibility'] ?? ''),
                    'obsrvr_sub_open_of_bid' => $this->nullableDate($this->bulkEdit['obsrvr_sub_open_of_bid'] ?? ''),
                    'obsrvr_bid' => $this->nullableDate($this->bulkEdit['obsrvr_bid'] ?? ''),
                    'obsrvr_post_qual' => $this->nullableDate($this->bulkEdit['obsrvr_post_qual'] ?? ''),
                    'pre_bid_conf' => $this->nullableDate($this->bulkEdit['pre_bid_conf'] ?? ''),
                    'eligibility_check' => $this->nullableDate($this->bulkEdit['eligibility_check'] ?? ''),
                    'sub_open_bids' => $this->nullableDate($this->bulkEdit['sub_open_bids'] ?? ''),
                    'bid_evaluation_date' => $this->nullableDate($this->bulkEdit['bid_evaluation_date'] ?? ''),
                    'post_qualification_date' => $this->nullableDate($this->bulkEdit['post_qualification_date'] ?? ''),
                    'bidding_result' => $this->nullableValue($this->bulkEdit['bidding_result'] ?? ''),
                    'resolution_number_mop' => $this->nullableValue($this->bulkEdit['resolution_number_mop'] ?? ''),
                ]
            );
        }

        if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
            PrSvp::updateOrCreate(
                ['mop_uid' => $mopUid, 'ref_id' => $refId],
                [
                    'resolution_number' => $this->nullableValue($this->bulkEdit['resolution_number'] ?? ''),
                    'rfq_no' => $this->nullableValue($this->bulkEdit['rfq_no'] ?? ''),
                    'canvass_date' => $this->nullableDate($this->bulkEdit['canvass_date'] ?? ''),
                    'date_returned_of_canvass' => $this->nullableDate($this->bulkEdit['date_returned_of_canvass'] ?? ''),
                    'abstract_of_canvass_date' => $this->nullableDate($this->bulkEdit['abstract_of_canvass_date'] ?? ''),
                ]
            );
        }
    }

    private function updatePerItemSchedules(array $item): void
    {
        if (!$item['mop_uid'] || !$item['prItemID']) {
            return;
        }

        $modeId = $item['mode_of_procurement_id'];
        $refId = $item['prItemID'];
        $mopUid = $item['mop_uid'];

        if (in_array($modeId, [2, 3, 4, 5, 6])) {
            BidSchedule::updateOrCreate(
                ['mop_uid' => $mopUid, 'ref_id' => $refId],
                [
                    'bidding_number' => $this->nullableValue($this->bulkEdit['bidding_number'] ?? ''),
                    'ib_number' => $this->nullableValue($this->bulkEdit['ib_number'] ?? ''),
                    'philgeps_posting_ref_no' => $this->nullableValue($this->bulkEdit['philgeps_posting_ref_no'] ?? ''),
                    'ads_post_ib' => $this->nullableDate($this->bulkEdit['ads_post_ib'] ?? ''),
                    'pre_proc_conference' => $this->nullableDate($this->bulkEdit['pre_proc_conference'] ?? ''),
                    'list_invited_observers' => $this->nullableDate($this->bulkEdit['list_invited_observers'] ?? ''),
                    'obsrvr_prebid_conf' => $this->nullableDate($this->bulkEdit['obsrvr_prebid_conf'] ?? ''),
                    'obsrvr_eligibility' => $this->nullableDate($this->bulkEdit['obsrvr_eligibility'] ?? ''),
                    'obsrvr_sub_open_of_bid' => $this->nullableDate($this->bulkEdit['obsrvr_sub_open_of_bid'] ?? ''),
                    'obsrvr_bid' => $this->nullableDate($this->bulkEdit['obsrvr_bid'] ?? ''),
                    'obsrvr_post_qual' => $this->nullableDate($this->bulkEdit['obsrvr_post_qual'] ?? ''),
                    'pre_bid_conf' => $this->nullableDate($this->bulkEdit['pre_bid_conf'] ?? ''),
                    'eligibility_check' => $this->nullableDate($this->bulkEdit['eligibility_check'] ?? ''),
                    'sub_open_bids' => $this->nullableDate($this->bulkEdit['sub_open_bids'] ?? ''),
                    'bid_evaluation_date' => $this->nullableDate($this->bulkEdit['bid_evaluation_date'] ?? ''),
                    'post_qualification_date' => $this->nullableDate($this->bulkEdit['post_qualification_date'] ?? ''),
                    'bidding_result' => $this->nullableValue($this->bulkEdit['bidding_result'] ?? ''),
                    'resolution_number_mop' => $this->nullableValue($this->bulkEdit['resolution_number_mop'] ?? ''),
                ]
            );
        }

        if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
            PrSvp::updateOrCreate(
                ['mop_uid' => $mopUid, 'ref_id' => $refId],
                [
                    'resolution_number' => $this->nullableValue($this->bulkEdit['resolution_number'] ?? ''),
                    'rfq_no' => $this->nullableValue($this->bulkEdit['rfq_no'] ?? ''),
                    'canvass_date' => $this->nullableDate($this->bulkEdit['canvass_date'] ?? ''),
                    'date_returned_of_canvass' => $this->nullableDate($this->bulkEdit['date_returned_of_canvass'] ?? ''),
                    'abstract_of_canvass_date' => $this->nullableDate($this->bulkEdit['abstract_of_canvass_date'] ?? ''),
                ]
            );
        }
    }

    private function nullableValue($value)
    {
        return empty($value) ? null : $value;
    }

    private function nullableDate($value): ?string
    {
        return empty($value) ? null : $value;
    }

    public function toggleHistory(string $key)
    {
        if ($this->historyForKey === $key && $this->showHistory) {
            $this->showHistory = false;
            $this->historyForKey = null;
        } else {
            $this->showHistory = true;
            $this->historyForKey = $key;
        }
    }

    public function getHistoryItemsProperty()
    {
        if (!$this->showHistory || !$this->historyForKey) {
            return collect();
        }

        // Parse the key to get procID or prItemID
        $parts = explode('_', $this->historyForKey);
        $type = $parts[0]; // 'lot' or 'item'
        $id = $parts[1];

        // Get all items for this PR except the first (current) one
        return collect($this->items)
            ->filter(function ($item) use ($type, $id) {
                if ($type === 'lot') {
                    return $item['procurement_type'] === 'perLot' && $item['procID'] == $id;
                } else {
                    return $item['procurement_type'] === 'perItem' && $item['prItemID'] == $id;
                }
            })
            ->skip(1) // Skip the current/first mode
            ->values();
    }

    private function formatDate($date): string
    {
        if (empty($date)) {
            return '-';
        }

        try {
            return Carbon::parse($date)->format('m/d/Y');
        } catch (\Exception $e) {
            return $date;
        }
    }

    public function setStep(int $step): void
    {
        if ($step == 2 && !$this->isPostAvailable) {
            LivewireAlert::title('Cannot Access Post Tab')
                ->warning()
                ->text('Post-procurement tab is not yet available. Please complete the bulk edit first.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $this->activeTab = $step;
    }

    public function getIsPostAvailableProperty(): bool
    {
        // Post tab will be available after successful bulk edit save
        // For now, it's disabled during bulk edit
        return false;
    }

    public function getDisableModeSelectProperty(): bool
    {
        // Disable mode selection if ANY item has schedule data
        $currentItems = $this->getCurrentItems();

        foreach ($currentItems as $item) {
            if ($this->itemHasSchedule($item)) {
                return true;
            }
        }

        return false;
    }

    public function getShowAddModeButtonProperty(): bool
    {
        // Show Add Mode button if all PRs have mode_id = 1 (no mode selected)
        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            return false;
        }

        foreach ($currentItems as $item) {
            $modeId = $item['mode_of_procurement_id'] ?? null;
            // If any item has a mode other than 1 (or has schedule data), don't show Add button
            if ($modeId != 1) {
                return false;
            }
        }

        return true;
    }

    public function getShowBiddingFieldsProperty(): bool
    {
        $modeId = $this->bulkEdit['mode_of_procurement_id'] ?? null;
        return in_array($modeId, [2, 3, 4, 5, 6]);
    }

    public function getShowSvpFieldsProperty(): bool
    {
        $modeId = $this->bulkEdit['mode_of_procurement_id'] ?? null;
        return in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24]);
    }

    public function getDisableInputsProperty(): bool
    {
        // Disable inputs if ALL selected PRs have:
        // 1. SUCCESSFUL bidding result
        // 2. Post-procurement data exists
        // 3. User doesn't have edit permission

        $canEditMop = auth()->user()->can('edit_mode::of::procurement');

        \Log::info('Bulk Edit - Checking disable inputs', [
            'user_has_permission' => $canEditMop,
            'user_id' => auth()->id(),
        ]);

        if ($canEditMop) {
            \Log::info('Bulk Edit - Not disabling: User has permission');
            return false; // User has permission, don't disable
        }

        $currentItems = $this->getCurrentItems();

        if (empty($currentItems)) {
            \Log::info('Bulk Edit - Not disabling: No current items found');
            return false;
        }

        \Log::info('Bulk Edit - Checking items', ['count' => count($currentItems)]);

        // Check if ALL items meet the disable criteria
        foreach ($currentItems as $index => $item) {
            $biddingResult = $item['bidding_result'] ?? '';
            $refId = $item['procurement_type'] === 'perLot' ? $item['procID'] : $item['prItemID'];

            $hasPostData = \App\Models\PostProcurement::where('ref_id', $refId)->exists();
            $isSuccessful = $biddingResult === 'SUCCESSFUL';

            \Log::info("Bulk Edit - Item $index check", [
                'pr_number' => $item['pr_number'] ?? 'N/A',
                'procurement_type' => $item['procurement_type'],
                'ref_id' => $refId,
                'bidding_result' => $biddingResult,
                'is_successful' => $isSuccessful,
                'has_post_data' => $hasPostData,
                'meets_criteria' => $isSuccessful && $hasPostData,
            ]);

            // If ANY item doesn't meet criteria, don't disable
            if (!($isSuccessful && $hasPostData)) {
                \Log::info("Bulk Edit - Not disabling: Item $index doesn't meet criteria");
                return false;
            }
        }

        // All items are successful with post data and user lacks permission
        \Log::info('Bulk Edit - DISABLING: All items meet criteria and user lacks permission');
        return true;
    }

    public function getAbcThresholdCategoryProperty(): string
    {
        $procurements = Procurement::whereIn('procID', $this->procurementIds)->get();

        if ($procurements->isEmpty()) {
            return 'N/A';
        }

        // Check the first procurement's ABC to determine category
        $firstAbc = $procurements->first()->abc ?? 0;

        if ($firstAbc < 200000) {
            return 'Below ₱200,000.00';
        } else {
            return '₱200,000.00 and Above';
        }
    }

    public function cancel()
    {
        return redirect()->route('mode-of-procurement.index');
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-bulk-edit-per-lot-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
            'isPostAvailable' => $this->isPostAvailable,
            'disableInputs' => $this->disableInputs,
            'disableModeSelect' => $this->disableModeSelect,
            'showBiddingFields' => $this->showBiddingFields,
            'showSvpFields' => $this->showSvpFields,
            'abcThresholdCategory' => $this->abcThresholdCategory,
            'showAddModeButton' => $this->showAddModeButton,
        ]);
    }
}
