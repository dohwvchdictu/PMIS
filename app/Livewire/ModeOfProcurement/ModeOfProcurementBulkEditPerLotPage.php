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

    public function save()
    {
        DB::transaction(function () {
            foreach ($this->items as $item) {
                // Only save head items (current mode), not history
                if ($item['mode_order'] == 0) {
                    $this->updateItem($item);
                }
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

    public function cancel()
    {
        return redirect()->route('mode-of-procurement.index');
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-bulk-edit-per-lot-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
        ]);
    }
}
