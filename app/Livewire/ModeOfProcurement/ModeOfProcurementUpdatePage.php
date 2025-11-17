<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\Category;
use App\Models\ModeOfProcurement;
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
        $this->modeOfProcurements = ModeOfProcurement::orderBy('modeofprocurements')->get();

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
        // Get mop_item records with their relationships
        $mopItems = $procurement->mopItems()
            ->with(['item', 'modeOfProcurement'])
            ->get()
            ->keyBy('prItemID');

        $this->form['items'] = $procurement->pr_items
            ->sortByDesc('id')
            ->map(function ($item) use ($mopItems) {
                $mopItem = $mopItems->get($item->prItemID);

                return [
                    'prItemID' => $item->prItemID,
                    'item_no' => $item->item_no,
                    'description' => $item->description,
                    'amount' => number_format((float) $item->amount, 2, '.', ''),
                    'mode_of_procurement_id' => $mopItem?->mode_of_procurement_id,
                    'uid' => $mopItem?->uid ?? 'row_' . $item->prItemID,
                    'mode_order' => $mopItem?->mode_order,
                    // Add other fields from bid_schedule or ntf_bid_schedule as needed
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
            })
            ->values()
            ->toArray();

        if (empty($this->form['items'])) {
            $this->addItem();
        }
    }

    protected function loadPerLotData(Procurement $procurement)
    {
        // Get mop_lot records
        $mopLots = $procurement->mopLots()
            ->with('modeOfProcurement')
            ->orderBy('mode_order')
            ->get();

        $this->form['items'] = $mopLots->map(function ($mopLot, $index) {
            return [
                'id' => $mopLot->id ?? null,
                'uid' => $mopLot->uid ?? 'temp_' . uniqid(),
                'mode_of_procurement_id' => $mopLot->mode_of_procurement_id,
                'mode_order' => $mopLot->mode_order ?? ($index + 1),
                // Add other fields from bid_schedule or ntf_bid_schedule as needed
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
        $this->validate([
            'form.items.*.mode_of_procurement_id' => 'required|integer',
            // Add other validations for dates/fields if needed
        ]);

        $isNewRecord = false; // Flag to track if any new records were added

        DB::transaction(function () use (&$isNewRecord) {
            $processedIds = [];

            foreach ($this->form['items'] as $index => $item) {
                // Skip rows where no mode is selected
                if (empty($item['mode_of_procurement_id'])) {
                    continue;
                }

                $modeId = $item['mode_of_procurement_id'];

                // Check if this is a new record by looking at the UID
                $isNew = isset($item['uid']) && (
                    str_starts_with($item['uid'], 'new_') ||
                    str_starts_with($item['uid'], 'temp_')
                );

                // Handle UID and mode_order based on whether it's new or existing
                if ($isNew) {
                    // For new records: generate new UID and determine mode_order
                    $modeOrder = ($this->form['procurement_type'] === 'perLot')
                        ? ($index + 1)
                        : 1;
                    $generatedUid = "MOP-{$modeId}-{$modeOrder}";
                    $isNewRecord = true;
                } else {
                    // For existing records: keep existing UID and mode_order
                    $generatedUid = $item['uid'];
                    $modeOrder = $item['mode_order']; // Keep existing mode_order
                }

                if ($this->form['procurement_type'] === 'perItem') {
                    // --- SAVE TO MOP_ITEM ---

                    $mopItem = MopItem::updateOrCreate(
                        [
                            'procID' => $this->procID,
                            'prItemID' => $item['prItemID'],
                        ],
                        [
                            'uid' => $generatedUid,
                            'mode_of_procurement_id' => $modeId,
                            'mode_order' => $modeOrder,
                        ]
                    );

                    $processedIds[] = $mopItem->id;

                } else {
                    // --- SAVE TO MOP_LOT ---

                    $matchCriteria = ['procID' => $this->procID];

                    if ($isNew) {
                        $matchCriteria['mode_order'] = $modeOrder;
                    } else {
                        // For existing records, match by ID if available
                        if (isset($item['id']) && is_numeric($item['id'])) {
                            $matchCriteria['id'] = $item['id'];
                        } else {
                            // Fallback: match by UID if no ID
                            $matchCriteria['uid'] = $item['uid'];
                        }
                    }

                    $mopLot = MopLot::updateOrCreate(
                        $matchCriteria,
                        [
                            'uid' => $generatedUid,
                            'mode_of_procurement_id' => $modeId,
                            'mode_order' => $modeOrder,
                        ]
                    );

                    $processedIds[] = $mopLot->id;
                }
            }

            // --- CLEANUP: Delete removed records ---
            if ($this->form['procurement_type'] === 'perItem') {
                MopItem::where('procID', $this->procID)
                    ->whereNotIn('id', $processedIds)
                    ->delete();
            } else {
                MopLot::where('procID', $this->procID)
                    ->whereNotIn('id', $processedIds)
                    ->delete();
            }
        });

        // Show appropriate alert based on whether new records were added
        if ($isNewRecord) {
            LivewireAlert::title('Mode Added Successfully!')
                ->success()
                ->text('The mode of procurement has been added.')
                ->toast()
                ->position('top-end')
                ->show();
        } else {
            LivewireAlert::title('Mode Updated Successfully!')
                ->success()
                ->text('The mode of procurement has been updated.')
                ->toast()
                ->position('top-end')
                ->show();
        }

        // Reload the procurement data to refresh the form with saved data
        $this->mount($this->procurement);
    }

    public function render()
    {
        return view('livewire.mode-of-procurement.mode-of-procurement-update-page', [
            'modeOfProcurements' => $this->modeOfProcurements,
        ]);
    }
}
