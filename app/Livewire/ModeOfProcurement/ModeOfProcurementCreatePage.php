<?php

namespace App\Livewire\ModeOfProcurement;

use App\Models\Mop;
use App\Models\MopGroup;
use App\Models\PrItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Livewire\Component;
use App\Models\Procurement;
use App\Models\ModeOfProcurement;
use App\Models\MopLot;
use App\Models\BidSchedule;
use App\Models\NtfBidSchedule;
use App\Models\PrSvp;
use Livewire\WithPagination;

class ModeOfProcurementCreatePage extends Component
{
    use WithPagination;
    public $procurement = null;
    public ?string $ref_number = null;
    public string $procurementType = '';
    public int $activeTab = 1; // Default to Tab 1
    public array $selectedProcurements = [];
    public int $perPage = 10;
    public $selectedPRPage = 1;
    public ?int $mopGroupId = null;
    public $form = [
        'modes' => [],
    ];
    public array $selectedLots = [];
    public array $selectedItemGroups = [];
    protected $listeners = ['procurementsSelected'];
    public $isEditing = false;
    public bool $viewOnlyTab1 = false;
    public bool $viewOnlyTab2 = false;
    public bool $viewOnlyTab3 = false;

    public function mount($ref_number = null)
    {
        $this->activeTab = (int) request()->query('tab', 1);

        if (session('alert')) {
            $alert = session('alert');
            LivewireAlert::title($alert['title'])
                ->{$alert['type']}()
                    ->text($alert['message'])
                    ->toast()
                    ->position('top-end')
                    ->show();
        }

        if ($this->mopGroupId || $this->procurementType) {
            return;
        }

        $this->ref_number = $ref_number;

        if ($this->ref_number) {
            session()->forget(['selected_procurements', 'form_state']);
            $this->resetForm();
            // $this->activeTab = 2; // Removed: Default is now 1

            $mopGroup = MopGroup::with([
                'procurements.mops.modeDetails',
                'procurements.mops.bidSchedules',
                'procurements.mops.ntfBidSchedules',
                'procurements.mops.svpDetails',
                'prItems.procurement',
                'prItems.mops.modeDetails',
                'prItems.mops.bidSchedules',
                'prItems.mops.ntfBidSchedules',
                'prItems.mops.svpDetails',
            ])
                ->where('ref_number', $this->ref_number)
                ->firstOrFail();

            $this->mopGroupId = $mopGroup->id;
            $this->procurementType = $mopGroup->procurable_type;

            if ($this->procurementType === 'perLot') {
                $this->selectedProcurements = $mopGroup->procurements->map(function ($proc) {
                    return [
                        'id' => $proc->procID,
                        'pr_number' => $proc->pr_number,
                        'procurement_program_project' => $proc->procurement_program_project,
                        'items' => null,
                    ];
                })->toArray();
            } else {
                $this->selectedProcurements = $mopGroup->prItems
                    ->groupBy('procID')
                    ->map(function ($items) {
                        $proc = $items->first()->procurement;
                        if (!$proc)
                            return null;
                        return [
                            'id' => $proc->procID,
                            'pr_number' => $proc->pr_number,
                            'procurement_program_project' => $proc->procurement_program_project,
                            'items' => $items->map(function ($item) {
                                return [
                                    'id' => $item->prItemID,
                                    'description' => $item->description,
                                    'amount' => $item->amount,
                                ];
                            })->values()->all(),
                        ];
                    })
                    ->filter()
                    ->values()
                    ->toArray();
            }

            $firstItem = ($this->procurementType === 'perLot')
                ? $mopGroup->procurements->first()
                : $mopGroup->prItems->first();

            if ($firstItem && $firstItem->mops->isNotEmpty()) {
                $this->form['modes'] = $firstItem->mops->sortByDesc('mode_order')->map(function ($mop) {
                    $loadedSchedulesData = [];
                    $modeId = $mop->mode_of_procurement_id;

                    if ($modeId == 5) {
                        $svpDetail = $mop->svpDetails()->first();
                        if ($svpDetail) {
                            $loadedSchedulesData = [$svpDetail->toArray()];
                        }
                    } elseif ($modeId == 4) {
                        $ntfSchedulesCollection = $mop->ntfBidSchedules;
                        if ($ntfSchedulesCollection) {
                            $loadedSchedulesData = $ntfSchedulesCollection->sortBy('bidding_number')->toArray();
                        }
                    } else {
                        $bidSchedulesCollection = $mop->bidSchedules;
                        if ($bidSchedulesCollection) {
                            $loadedSchedulesData = $bidSchedulesCollection->sortBy('bidding_number')->toArray();
                        }
                    }

                    return [
                        'id' => $mop->id,
                        'uid' => $mop->uid,
                        'mode_of_procurement_id' => $mop->mode_of_procurement_id,
                        'mode_order' => $mop->mode_order,
                        'bid_schedules' => $loadedSchedulesData,
                    ];
                })->values()->all();
            } else {
                Log::warning("No initial MOP found for MopGroup ref: {$this->ref_number}, loading default.");
                $this->form['modes'] = []; // Ensure it's empty or load default if needed
            }

        } else {
            $this->activeTab = 1; // Explicitly set for create mode
            $this->procurementType = request()->query('type', 'perLot');

            if (session()->has('selected_procurements')) {
                $this->selectedProcurements = session('selected_procurements');
            }
            if (session()->has('form_state')) {
                $this->form = session('form_state', ['modes' => []]); // Ensure modes exists
                session()->forget('form_state');
            } else {
                $this->resetForm(); // Ensure clean state on create
            }
        }

        $this->ensureDefaultBidSchedules();
    }
    public function updatedFormModes($value, $key)
    {
        // Example: key = "0.mode_of_procurement_id"
        if (str_ends_with($key, 'mode_of_procurement_id')) {
            $index = explode('.', $key)[0] ?? null;

            if (is_numeric($index) && isset($this->form['modes'][$index])) {

                // ✅ RESET the bid schedule when the mode changes
                // This clears out all old data from the previous mode
                $this->form['modes'][$index]['bid_schedules'] = [
                    [
                        'uid' => 'TEMP-' . uniqid(), // Add a new UID
                        'bidding_number' => 1,
                        'ib_number' => '',
                        'pre_proc_conference' => null,
                        'ads_post_ib' => null,
                        'pre_bid_conf' => null,
                        'eligibility_check' => null,
                        'sub_open_bids' => null,
                        'bidding_date' => null,
                        'bidding_result' => '',
                        'ntf_no' => '',
                        'ntf_bidding_date' => null,
                        'ntf_bidding_result' => '',
                        'rfq_no' => '',
                        'canvass_date' => null,
                        'date_returned_of_canvass' => null,
                        'abstract_of_canvass_date' => null,
                        'resolution_number' => '',
                    ]
                ];
            }
        }
    }

    private function ensureDefaultBidSchedules()
    {
        if (!isset($this->form['modes']) || !is_array($this->form['modes'])) {
            $this->form['modes'] = [];
        }

        foreach ($this->form['modes'] as &$mode) {
            if (empty($mode['bid_schedules']) || !is_array($mode['bid_schedules'])) {
                $mode['bid_schedules'] = [
                    [
                        'uid' => 'TEMP-' . uniqid(),
                        'bidding_number' => 1,
                        'ib_number' => '',
                        'bidding_date' => null,
                        'bidding_result' => '',
                        'ntf_bidding_result' => '',
                    ]
                ];
            }
        }
    }

    private function persistFormState(): void
    {
        session([
            'form_state' => $this->form,
            'selected_procurements' => $this->selectedProcurements,
        ]);
    }

    public function openSelectionModal()
    {
        $this->persistFormState();

        $existingLotIds = collect($this->selectedProcurements)
            ->filter(fn($proc) => empty($proc['items']))
            ->pluck('id')
            ->toArray();

        $existingItemIds = collect($this->selectedProcurements)
            ->filter(fn($proc) => !empty($proc['items']))
            ->flatMap(fn($proc) => collect($proc['items'])->pluck('id'))
            ->toArray();

        $this->dispatch('open-mode-modal', existingLotIds: $existingLotIds, existingItemIds: $existingItemIds);
    }

    public function procurementsSelected(array $selectedData): void
    {
        $this->selectedProcurements = $selectedData;
    }

    public function onProcurementSelected(array $selections): void
    {
        $this->selectedProcurements = $selections;
    }

    public function hydrateForm()
    {
        if (!$this->procurement) {
            return;
        }
    }

    public function getSelectedPRProperty()
    {
        $items = collect($this->selectedProcurements)
            ->flatMap(function ($proc) {
                if (!empty($proc['items'])) {
                    return collect($proc['items'])->map(function ($item) use ($proc) {
                        $item['pr_number'] = $proc['pr_number'];
                        $item['is_item'] = true;
                        $item['unique_key'] = 'item_' . $item['id'];
                        return $item;
                    });
                } else {
                    $proc['is_item'] = false;
                    $proc['unique_key'] = 'lot_' . $proc['id'];
                    return [$proc];
                }
            });

        return $this->paginateCollection($items, $this->perPage, 'selectedPRPage');
    }

    public function removeSelectedPR(string $uniqueKey): void
    {
        [$type, $id] = explode('_', $uniqueKey);
        $id = (int) $id;

        if ($type === 'lot') {
            $this->selectedProcurements = collect($this->selectedProcurements)
                ->filter(fn($proc) => !empty($proc['items']) || (empty($proc['items']) && $proc['id'] !== $id))
                ->values()
                ->all();
        } else {
            foreach ($this->selectedProcurements as $procIndex => &$proc) {
                if (!empty($proc['items'])) {
                    $proc['items'] = collect($proc['items'])
                        ->filter(fn($item) => $item['id'] !== $id)
                        ->values()
                        ->all();
                    if (empty($proc['items'])) {
                        unset($this->selectedProcurements[$procIndex]);
                    }
                }
            }
            $this->selectedProcurements = array_values($this->selectedProcurements);
        }

        if ($this->SelectedPR->isEmpty() && $this->selectedPRPage > 1) {
            $this->selectedPRPage--;
        }
    }

    private function paginateCollection($collection, $perPage, $pageName)
    {
        $page = $this->$pageName ?? 1;
        return new LengthAwarePaginator(
            $collection->forPage($page, $perPage),
            $collection->count(),
            $perPage,
            $page,
            ['path' => request()->url(), 'pageName' => $pageName]
        );
    }

    public function nextCustomPage(string $pageName)
    {
        if (property_exists($this, $pageName)) {
            $this->$pageName++;
        }
    }

    public function previousCustomPage(string $pageName)
    {
        if (property_exists($this, $pageName) && $this->$pageName > 1) {
            $this->$pageName--;
        }
    }

    public function getShowAddModeButtonProperty()
    {
        $modes = collect($this->form['modes'] ?? []);
        $hasDefaultMode = $modes->contains('mode_of_procurement_id', 1);
        $hasPendingOrEmptySchedule = $modes->contains(function ($mode) {
            $schedules = collect($mode['bid_schedules'] ?? []);
            return $schedules->isEmpty() ||
                $schedules->contains(fn($s) => empty($s['bidding_result']) && empty($s['ntf_bidding_result']));
        });
        return !$hasDefaultMode && !$hasPendingOrEmptySchedule;
    }

    public function addMode()
    {
        // Define a template for an empty bid schedule
        $emptyBidSchedule = [
            'bidding_number' => 1, // Start with 1 for the first bid
            'ib_number' => '',
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bidding_date' => null,
            'bidding_result' => '',
            'ntf_no' => '',
            'ntf_bidding_date' => null,
            'ntf_bidding_result' => '',
            'rfq_no' => '',
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
            'resolution_number' => '',
        ];

        $newMode = [
            'uid' => 'TEMP-' . uniqid(),
            'mode_of_procurement_id' => '',
            'mode_order' => count($this->form['modes'] ?? []) + 1,
            'bid_schedules' => [$emptyBidSchedule],
        ];

        // Add the new mode to the beginning of the array
        array_unshift($this->form['modes'], $newMode);
    }

    public function addBidSchedule($modeIndex)
    {
        $existingSchedules = $this->form['modes'][$modeIndex]['bid_schedules'] ?? [];
        $newBiddingNumber = count($existingSchedules) + 1;

        $newBidSchedule = [
            'bidding_number' => $newBiddingNumber,
            'ib_number' => '',
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bidding_date' => null,
            'bidding_result' => '',
            'ntf_no' => '',
            'ntf_bidding_date' => null,
            'ntf_bidding_result' => '',
            'rfq_no' => '',
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
            'resolution_number' => '',
        ];
        array_unshift($this->form['modes'][$modeIndex]['bid_schedules'], $newBidSchedule);
    }

    private function validateData()
    {
        $this->validate([
            'form.modes' => 'required|array',
            'form.modes.*.mode_of_procurement_id' => 'required|exists:mode_of_procurements,id',
            'form.modes.*.bid_schedules' => 'nullable|array',
            'form.modes.*.bid_schedules.*.ib_number' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.pre_proc_conference' => 'nullable|date',
            'form.modes.*.bid_schedules.*.ads_post_ib' => 'nullable|date',
            'form.modes.*.bid_schedules.*.pre_bid_conf' => 'nullable|date',
            'form.modes.*.bid_schedules.*.eligibility_check' => 'nullable|date',
            'form.modes.*.bid_schedules.*.sub_open_bids' => 'nullable|date',
            'form.modes.*.bid_schedules.*.bidding_number' => 'nullable|integer|min:0|max:255',
            'form.modes.*.bid_schedules.*.bidding_date' => 'nullable|date',
            'form.modes.*.bid_schedules.*.bidding_result' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.ntf_no' => 'nullable|string|max:255',
            'form.modes.*.bid_schedules.*.ntf_bidding_date' => 'nullable|date',
            'form.modes.*.bid_schedules.*.ntf_bidding_result' => 'nullable|string|max:255',
        ]);

        foreach ($this->form['modes'] as $modeIndex => $mode) {
            if ($mode['mode_of_procurement_id'] == 5) {
                if (empty($mode['bid_schedules']) || !is_array($mode['bid_schedules'])) {
                    throw ValidationException::withMessages([
                        "form.modes.$modeIndex.bid_schedules" => 'Mode 5 requires at least one bid schedule.',
                    ]);
                }
                $isPlaceholder = count($mode['bid_schedules']) === 1 &&
                    empty($mode['bid_schedules'][0]['resolution_number']) &&
                    empty($mode['bid_schedules'][0]['rfq_no']) &&
                    empty($mode['bid_schedules'][0]['canvass_date']) &&
                    empty($mode['bid_schedules'][0]['date_returned_of_canvass']) &&
                    empty($mode['bid_schedules'][0]['abstract_of_canvass_date']);
                if (!$isPlaceholder) {
                    foreach ($mode['bid_schedules'] as $bidIndex => $schedule) {
                        $this->validate([
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.resolution_number" => 'required|string|max:255',
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.rfq_no" => 'required|string|max:255',
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.canvass_date" => 'required|date',
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.date_returned_of_canvass" => 'required|date',
                            "form.modes.$modeIndex.bid_schedules.$bidIndex.abstract_of_canvass_date" => 'required|date',
                        ]);
                    }
                }
            }
        }
    }

    private function prepareModes()
    {
        $modes = $this->form['modes'];
        usort($modes, fn($a, $b) => ($a['mode_order'] ?? 0) <=> ($b['mode_order'] ?? 0));
        return $modes;
    }

    public function setStep($step)
    {
        $this->activeTab = $step;
    }

    public function save()
    {
        if ($this->activeTab === 1) {
            $this->saveTab1();
        } elseif ($this->activeTab === 2) {
            $this->saveTab2();
        }
    }

    public function saveTab1()
    {
        if (empty($this->selectedProcurements)) {
            LivewireAlert::title('Selection Required')
                ->error()
                ->text('Please select at least one PR Lot or Item.')
                ->toast()->position('top-end')->show();
            return;
        }

        try {
            $group = DB::transaction(function () {
                $year = now()->format('Y');
                $lastNumber = MopGroup::whereYear('created_at', $year)
                    ->where('ref_number', 'like', "MOP-$year-%")
                    ->orderByDesc('id')
                    ->value('ref_number');

                $nextNumber = 1;
                if ($lastNumber) {
                    $lastNum = (int) Str::afterLast($lastNumber, '-');
                    $nextNumber = $lastNum + 1;
                }

                $refNumber = sprintf('MOP-%s-%04d', $year, $nextNumber);

                $mopGroup = MopGroup::create([
                    'ref_number' => $refNumber,
                    'status' => 'draft',
                    'procurable_type' => $this->procurementType,
                ]);

                // Separate by type
                $lotIDs = collect($this->selectedProcurements)
                    ->filter(fn($proc) => empty($proc['items']))
                    ->pluck('procID');

                $itemIDs = collect($this->selectedProcurements)
                    ->filter(fn($proc) => !empty($proc['items']))
                    ->pluck('items.*.prItemID')
                    ->flatten();

                $uidBase = $mopGroup->ref_number;

                // Attach lots
                if ($lotIDs->isNotEmpty()) {
                    $mopGroup->procurements()->attach($lotIDs);

                    $attachedLots = Procurement::whereIn('procID', $lotIDs)->get();
                    foreach ($attachedLots as $lot) {
                        $existingMop = $lot->mops()->first();
                        if (!$existingMop) {
                            $uid = "{$uidBase}-1-1";
                            $lot->mops()->create([
                                'mop_group_ref' => $refNumber,
                                'mode_of_procurement_id' => 1,
                                'original_mode_of_procurement_id' => 1,
                                'current_mode_of_procurement_id' => 1,
                                'mode_order' => 1,
                                'uid' => $uid,
                            ]);
                        }
                    }
                }

                // Attach items
                if ($itemIDs->isNotEmpty()) {
                    $mopGroup->prItems()->attach($itemIDs);

                    $attachedItems = PrItem::whereIn('prItemID', $itemIDs)->get();
                    foreach ($attachedItems as $item) {
                        $existingMop = $item->mops()->first();
                        if (!$existingMop) {
                            // 👇 Generate proper UID (Option B pattern)
                            $uid = "{$uidBase}-1-1";
                            $item->mops()->create([
                                'mode_of_procurement_id' => 1,
                                'original_mode_of_procurement_id' => 1,
                                'current_mode_of_procurement_id' => 1,
                                'mode_order' => 1,
                                'uid' => $uid,
                            ]);
                        }
                    }
                }

                return $mopGroup;
            });

            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Saved!',
                'message' => 'Your Procurement has been created successfully.',
            ]);

            return redirect()->route('mode-of-procurement.update', [
                'ref_number' => $group->ref_number,
                'tab' => 2,
            ]);
        } catch (\Exception $e) {
            Log::error('Error saving MOP Details selections: ' . $e->getMessage());

            LivewireAlert::title('Error Saving Selections')
                ->error()
                ->text('An error occurred: ' . $e->getMessage())
                ->toast()->position('top-end')->show();
        }
    }


    private function syncMopFor($item, $modeData)
    {
        $modeId = $modeData['mode_of_procurement_id'] ?? null;
        $modeOrder = $modeData['mode_order'] ?? 1;

        $existingMops = $item->mops()->get();
        $mop = $existingMops->firstWhere('mode_order', $modeOrder);

        if (!$mop) {
            // Create new MOP (original = current)
            $item->mops()->create([
                'original_mode_of_procurement_id' => $modeId,
                'current_mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
                'mop_group_id' => $this->mopGroupId,
            ]);
            return;
        }

        // If existing MOP, check if the mode changed
        if ($mop->current_mode_of_procurement_id !== $modeId) {
            $mop->update([
                'current_mode_of_procurement_id' => $modeId,
            ]);
        }
    }

    public function saveTab2()
    {
        if (!$this->mopGroupId) {
            LivewireAlert::title('Error')
                ->error()
                ->text('Cannot save. Group ID not found. Please re-select procurements.')
                ->toast()->position('top-end')->show();
            return;
        }

        try {
            $this->validate([
                'form.modes' => 'required|array|min:1',
                'form.modes.*.mode_of_procurement_id' => 'required|exists:mode_of_procurements,id',
            ]);
        } catch (ValidationException $e) {
            Log::error('Validation error in saveTab2: ' . $e->getMessage(), $e->errors());
            LivewireAlert::title('Validation Error')
                ->error()
                ->text('Please ensure all required fields in the Mode of Procurement section are correctly filled.')
                ->toast()->position('top-end')->show();
            throw $e;
        }

        $schedulesWereSaved = false;

        try {
            DB::transaction(function () use (&$schedulesWereSaved) {
                $mopGroup = MopGroup::with(['procurements.mops', 'prItems.mops'])
                    ->findOrFail($this->mopGroupId);

                $itemsToUpdate = ($mopGroup->procurable_type === 'perLot')
                    ? $mopGroup->procurements
                    : $mopGroup->prItems;

                $sortedModes = collect($this->form['modes'])
                    ->sortBy('mode_order')
                    ->values()
                    ->all();

                foreach ($itemsToUpdate as $item) {
                    $existingMops = $item->mops()->get();
                    $existingOrders = $existingMops->pluck('mode_order')->toArray();
                    $submittedOrders = collect($sortedModes)->pluck('mode_order')->toArray();

                    // Delete modes not in form
                    $ordersToDelete = array_diff($existingOrders, $submittedOrders);
                    $existingMops->whereIn('mode_order', $ordersToDelete)->each->delete();

                    foreach ($sortedModes as $modeData) {
                        $modeId = $modeData['mode_of_procurement_id'];
                        $modeOrder = $modeData['mode_order'];
                        $bidSchedules = $modeData['bid_schedules'] ?? [];

                        // find or create mop for this item+modeOrder
                        $mop = $existingMops->firstWhere('mode_order', $modeOrder);

                        if (!$mop) {
                            $uid = "{$mopGroup->ref_number}-{$modeId}-{$modeOrder}";
                            $mop = $item->mops()->create([
                                'uid' => $uid,
                                'mode_of_procurement_id' => $modeId,
                                'original_mode_of_procurement_id' => $modeId,
                                'current_mode_of_procurement_id' => $modeId,
                                'mode_order' => $modeOrder,
                                'mop_group_id' => $this->mopGroupId,
                                'mop_group_ref' => $mopGroup->ref_number,
                            ]);
                        } else {
                            $mop->update([
                                'mode_of_procurement_id' => $modeId,
                                'current_mode_of_procurement_id' => $modeId,
                                'mop_group_ref' => $mopGroup->ref_number,
                            ]);
                        }

                        // Save schedules (explicit upsert logic)
                        foreach ($bidSchedules as $schedule) {
                            // fields to check for "empty placeholder"
                            $allDataFields = [
                                'ib_number',
                                'pre_proc_conference',
                                'ads_post_ib',
                                'pre_bid_conf',
                                'eligibility_check',
                                'sub_open_bids',
                                'bidding_date',
                                'bidding_result',
                                'ntf_no',
                                'ntf_bidding_date',
                                'ntf_bidding_result',
                                'rfq_no',
                                'canvass_date',
                                'date_returned_of_canvass',
                                'abstract_of_canvass_date',
                                'resolution_number',
                            ];

                            $hasAnyData = false;
                            foreach ($allDataFields as $field) {
                                if (isset($schedule[$field]) && !is_null($schedule[$field]) && $schedule[$field] !== '') {
                                    $hasAnyData = true;
                                    break;
                                }
                            }

                            // skip placeholder rows with no data
                            if (!$hasAnyData) {
                                continue;
                            }

                            // Mark that we saved something (will be committed later)
                            $schedulesWereSaved = true;

                            // SVP validation (unchanged)
                            if ($modeId == 5) {
                                $requiredFields = [
                                    'rfq_no' => 'RFQ No.',
                                    'resolution_number' => 'Resolution Number',
                                    'canvass_date' => 'Canvass Date',
                                    'date_returned_of_canvass' => 'Returned of Canvass',
                                    'abstract_of_canvass_date' => 'Abstract of Canvass',
                                ];
                                $missing = collect($requiredFields)
                                    ->filter(fn($label, $field) => empty($schedule[$field]))
                                    ->values()
                                    ->all();

                                if (!empty($missing)) {
                                    throw ValidationException::withMessages([
                                        'svp_fields' => 'Missing required field(s): ' . implode(', ', $missing),
                                    ]);
                                }
                            } else {
                                $otherDataFields = [
                                    'pre_proc_conference',
                                    'ads_post_ib',
                                    'pre_bid_conf',
                                    'eligibility_check',
                                    'sub_open_bids',
                                    'bidding_date',
                                    'bidding_result',
                                    'ntf_no',
                                    'ntf_bidding_date',
                                    'ntf_bidding_result',
                                    'rfq_no',
                                    'canvass_date',
                                    'date_returned_of_canvass',
                                    'abstract_of_canvass_date',
                                    'resolution_number',
                                ];

                                $hasOtherValues = false;
                                foreach ($otherDataFields as $field) {
                                    if (isset($schedule[$field]) && !is_null($schedule[$field]) && $schedule[$field] !== '') {
                                        $hasOtherValues = true;
                                        break;
                                    }
                                }

                                if ($hasOtherValues && empty($schedule['ib_number'])) {
                                    throw ValidationException::withMessages([
                                        'required_field' => 'IB Number is required',
                                    ]);
                                }
                            }

                            // build schedule UID key (mop.uid + optional bidding_number)
                            $scheduleUid = $mop->uid;
                            if (!empty($schedule['bidding_number'])) {
                                $scheduleUid .= '-' . $schedule['bidding_number'];
                            }

                            // Decide Model & relation
                            if ($modeId == 4) {
                                // Negotiated/NTF
                                $ScheduleModel = NtfBidSchedule::class;
                                $relation = $mop->ntfBidSchedules();
                            } elseif ($modeId == 5) {
                                $ScheduleModel = PrSvp::class;
                                $relation = $mop->svpDetails();
                            } else {
                                // default -> competitive bidding
                                $ScheduleModel = BidSchedule::class;
                                $relation = $mop->bidSchedules();
                            }

                            // Try to find existing record using UID (preferred) OR id fallback OR bidding_number
                            $existing = null;

                            if (!empty($schedule['uid'])) {
                                $existing = $ScheduleModel::where('uid', $schedule['uid'])->first();
                            }

                            // fallback: if id present try id
                            if (!$existing && !empty($schedule['id'])) {
                                $existing = $ScheduleModel::find($schedule['id']);
                            }

                            // fallback: scoped to mop uid + bidding_number (if provided)
                            if (!$existing && !empty($schedule['bidding_number'])) {
                                $existing = $ScheduleModel::where('uid', $mop->uid)
                                    ->where('bidding_number', $schedule['bidding_number'])
                                    ->first();
                            }

                            // final fallback (for NTF where ntf_no uniquely identifies)
                            if (!$existing && $modeId == 4 && !empty($schedule['ntf_no'])) {
                                $existing = $ScheduleModel::where('ntf_no', $schedule['ntf_no'])->first();
                            }

                            // Update existing
                            if ($existing) {
                                $existing->update([
                                    'mop_group_ref' => $mopGroup->ref_number,
                                    'uid' => $schedule['uid'] ?? $scheduleUid,
                                    'modeproc' => $mop->current_mode_of_procurement_id,
                                    'ib_number' => $schedule['ib_number'] ?? null,
                                    'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
                                    'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
                                    'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
                                    'eligibility_check' => $schedule['eligibility_check'] ?? null,
                                    'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
                                    'bidding_number' => $schedule['bidding_number'] ?? null,
                                    'bidding_date' => $schedule['bidding_date'] ?? null,
                                    'bidding_result' => $schedule['bidding_result'] ?? null,
                                    'ntf_no' => $schedule['ntf_no'] ?? null,
                                    'ntf_bidding_date' => $schedule['ntf_bidding_date'] ?? null,
                                    'ntf_bidding_result' => $schedule['ntf_bidding_result'] ?? null,
                                    'rfq_no' => $schedule['rfq_no'] ?? null,
                                    'canvass_date' => $schedule['canvass_date'] ?? null,
                                    'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
                                    'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
                                    'resolution_number' => $schedule['resolution_number'] ?? null,
                                ]);
                            } elseif (!empty($schedule['ib_number']) || (!empty($schedule['ntf_no']) && $modeId == 4) || $modeId == 5) {
                                // Create new only when IB/NTF/RFQ required data exists (ib_number or ntf_no or SVP fields)
                                $relation->create([
                                    'mop_group_ref' => $mopGroup->ref_number,
                                    'uid' => $schedule['uid'] ?? $scheduleUid,
                                    'modeproc' => $mop->current_mode_of_procurement_id,
                                    'ib_number' => $schedule['ib_number'] ?? null,
                                    'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
                                    'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
                                    'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
                                    'eligibility_check' => $schedule['eligibility_check'] ?? null,
                                    'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
                                    'bidding_number' => $schedule['bidding_number'] ?? null,
                                    'bidding_date' => $schedule['bidding_date'] ?? null,
                                    'bidding_result' => $schedule['bidding_result'] ?? null,
                                    'ntf_no' => $schedule['ntf_no'] ?? null,
                                    'ntf_bidding_date' => $schedule['ntf_bidding_date'] ?? null,
                                    'ntf_bidding_result' => $schedule['ntf_bidding_result'] ?? null,
                                    'rfq_no' => $schedule['rfq_no'] ?? null,
                                    'canvass_date' => $schedule['canvass_date'] ?? null,
                                    'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
                                    'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
                                    'resolution_number' => $schedule['resolution_number'] ?? null,
                                ]);
                            }
                        } // end schedules loop
                    } // end modes loop
                } // end items loop
            }); // end transaction

            // Alerts
            if ($schedulesWereSaved) {
                LivewireAlert::title('Modes Saved!')
                    ->success()
                    ->text('The Modes of Procurement and their schedules have been saved successfully.')
                    ->toast()->position('top-end')->show();
            } else {
                LivewireAlert::title('Mode Saved!')
                    ->success()
                    ->text('The Mode of Procurement has been saved successfully.')
                    ->toast()->position('top-end')->show();
            }

            // reload and set step
            $this->mount($this->ref_number);
            $this->setStep(2);

        } catch (\Exception $e) {
            Log::error('Error saving MOP tab 2: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            LivewireAlert::title('Error Saving MOP')
                ->error()
                ->text('An error occurred while saving: ' . $e->getMessage())
                ->toast()->position('top-end')->show();
            // rethrow if you want higher-level catching
            // throw $e;
        }
    }

    public function resetForm(): void
    {
        $this->selectedProcurements = [];
        $this->form = [
            'modes' => [],
        ];
        $this->resetValidation();
    }

    public function render()
    {
        $modes = ModeOfProcurement::where('is_active', true)
            ->orderBy('id')
            ->get();

        $existingLotIds = [];
        $existingItemIds = [];

        foreach ($this->selectedProcurements as $proc) {
            if (empty($proc['items'])) {
                $existingLotIds[] = $proc['id'];
            } else {
                foreach ($proc['items'] as $item) {
                    $existingItemIds[] = $item['id'];
                }
            }
        }

        $this->selectedLots = collect($this->selectedProcurements)
            ->filter(fn($proc) => empty($proc['items']))
            ->all();

        $this->selectedItemGroups = collect($this->selectedProcurements)
            ->filter(fn($proc) => !empty($proc['items']))
            ->all();

        return view('livewire.mode-of-procurement.mode-of-procurement-create-page', [
            'modeOfProcurements' => $modes,
            'existingLotIds' => $existingLotIds,
            'existingItemIds' => $existingItemIds,
        ]);
    }
}
