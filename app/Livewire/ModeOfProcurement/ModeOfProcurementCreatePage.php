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

        // Default structure matches validation rules exactly (snake_case keys)
        $newBidSchedule = [
            'ib_number' => '',
            'pre_proc_conference' => null,
            'ads_post_ib' => null,
            'pre_bid_conf' => null,
            'eligibility_check' => null,
            'sub_open_bids' => null,
            'bidding_date' => null,
            'bidding_result' => '',
            'bidding_number' => $newBiddingNumber,
            'ntf_no' => '',
            'ntf_bidding_date' => null,
            'ntf_bidding_result' => '',
            'rfq_no' => '',
            'canvass_date' => null,
            'date_returned_of_canvass' => null,
            'abstract_of_canvass_date' => null,
        ];

        // If this is mode 5, ensure at least one schedule exists right away
        if (
            isset($this->form['modes'][$modeIndex]['mode_of_procurement_id']) &&
            $this->form['modes'][$modeIndex]['mode_of_procurement_id'] == 5 &&
            empty($existingSchedules)
        ) {
            $this->form['modes'][$modeIndex]['bid_schedules'][] = $newBidSchedule;
            return;
        }

        // Otherwise, prepend new schedule to the list
        $this->form['modes'][$modeIndex]['bid_schedules'] = array_merge(
            [$newBidSchedule],
            $existingSchedules
        );
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
                                'procurable_type' => $this->procurementType,
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
                                'mop_group_ref' => $refNumber,
                                'mode_of_procurement_id' => 1,
                                'original_mode_of_procurement_id' => 1,
                                'current_mode_of_procurement_id' => 1,
                                'mode_order' => 1,
                                'uid' => $uid,
                                'procurable_type' => $this->procurementType,
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
                'procurable_type' => $this->procurementType,
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
        try {
            // Ensure Mode 5 always has at least one schedule before validating
            foreach ($this->form['modes'] as $modeIndex => $mode) {
                if (
                    isset($mode['mode_of_procurement_id']) &&
                    $mode['mode_of_procurement_id'] == 5 &&
                    (empty($mode['bid_schedules']) || !is_array($mode['bid_schedules']))
                ) {
                    $this->addBidSchedule($modeIndex);
                }
            }

            $this->validateTab2();

            $modesForProcessing = $this->prepareModes();

            foreach ($modesForProcessing as $modeIndex => $mode) {
                $this->processMode($mode, $modeIndex);
            }

            LivewireAlert::title('Saved Successfully!')
                ->success()->toast()->position('top-end')->show();

            $this->checkSuccessfulBidOrNtf();
            $this->checkSuccessfulSvp();

            if ($this->hasSuccessfulBidOrNtf || $this->hasSuccessfulSvp) {
                $this->activeTab = 3;
            }

        } catch (ValidationException $e) {
            LivewireAlert::title('Validation Failed!')
                ->error()->text($e->getMessage())->toast()->position('top-end')->show();
        } catch (\Exception $e) {
            \Log::error('Error Saving Data: ' . $e->getMessage());
            LivewireAlert::title('Error Saving Data!')
                ->error()->text($e->getMessage())->toast()->position('top-end')->show();
        }
    }
    private function validateTab2()
    {
        // Base rules for all modes and shared fields
        $this->validate([
            'form.modes' => 'required|array',
            'form.modes.*.mode_of_procurement_id' => 'required|exists:mode_of_procurements,id',
            'form.modes.*.bid_schedules' => 'nullable|array',

            // Common optional fields
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

        // Mode 5 specific: require at least one schedule only if user actually filled something
        foreach ($this->form['modes'] as $modeIndex => $mode) {
            if ($mode['mode_of_procurement_id'] == 5) {

                // Ensure at least one bid schedule exists
                if (empty($mode['bid_schedules']) || !is_array($mode['bid_schedules'])) {
                    throw ValidationException::withMessages([
                        "form.modes.$modeIndex.bid_schedules" => 'Mode 5 requires at least one bid schedule.',
                    ]);
                }

                // Check if this is just the auto-generated placeholder
                $isPlaceholder = count($mode['bid_schedules']) === 1 &&
                    empty($mode['bid_schedules'][0]['resolution_number']) &&
                    empty($mode['bid_schedules'][0]['rfq_no']) &&
                    empty($mode['bid_schedules'][0]['canvass_date']) &&
                    empty($mode['bid_schedules'][0]['date_returned_of_canvass']) &&
                    empty($mode['bid_schedules'][0]['abstract_of_canvass_date']);

                if (!$isPlaceholder) {
                    // Validate every filled-in schedule entry
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
    private function processMode(array $mode, int $modeIndex)
    {
        Log::info("Processing Mode {$modeIndex}:", $mode);
        $modeId = $mode['mode_of_procurement_id'];
        $modeOrder = $mode['mode_order'] ?? ($modeIndex + 1);

        $this->preventDuplicateMode($modeId, $mode);

        // Get the MopGroup to access its related procurements/items
        $mopGroup = MopGroup::where('ref_number', $this->ref_number)->first();

        if (!$mopGroup) {
            throw new \Exception("MopGroup not found for ref: {$this->ref_number}");
        }

        // Process for each procurement or item
        if ($this->procurementType === 'perLot') {
            foreach ($mopGroup->procurements as $procurement) {
                $existingMode = $this->updateOrCreateBidModeForProcurable(
                    $mode,
                    $modeId,
                    $modeOrder,
                    $procurement->procID,
                    'App\Models\Procurement'
                );

                if (!empty($mode['bid_schedules'])) {
                    if ($modeId == 5) {
                        $this->processPrSvp($mode['bid_schedules'], $existingMode->uid);
                    } else {
                        $this->processSchedules($mode['bid_schedules'], $existingMode->uid, $modeId);
                    }
                }
            }
        } else {
            foreach ($mopGroup->prItems as $item) {
                $existingMode = $this->updateOrCreateBidModeForProcurable(
                    $mode,
                    $modeId,
                    $modeOrder,
                    $item->prItemID,
                    'App\Models\PrItem'
                );

                if (!empty($mode['bid_schedules'])) {
                    if ($modeId == 5) {
                        $this->processPrSvp($mode['bid_schedules'], $existingMode->uid);
                    } else {
                        $this->processSchedules($mode['bid_schedules'], $existingMode->uid, $modeId);
                    }
                }
            }
        }

        // Sync UID back to form (use the last created mode's UID)
        if (isset($existingMode)) {
            $this->syncModeUidToForm($mode, $existingMode->uid, $modeOrder);
        }
    }
    private function updateOrCreateBidModeForProcurable($mode, $modeId, $modeOrder, $procurableId, $procurableType)
    {
        // Try to find existing mode by UID if it's not temporary
        $existingMode = !empty($mode['uid']) && !str_starts_with($mode['uid'], 'TEMP-')
            ? Mop::where('uid', $mode['uid'])
                ->where('procurable_id', $procurableId)
                ->where('procurable_type', $procurableType)
                ->first()
            : null;

        // If existing record is mode_id == 1 and incoming is not 1, create new
        if ($existingMode && $existingMode->mode_of_procurement_id == 1 && $modeId != 1) {
            $newOrder = Mop::where('mop_group_ref', $this->ref_number)
                ->where('procurable_id', $procurableId)
                ->where('procurable_type', $procurableType)
                ->max('mode_order') + 1;

            $uid = "{$this->ref_number}-{$modeId}-{$newOrder}";

            return Mop::create([
                'mop_group_ref' => $this->ref_number,
                'uid' => $uid,
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $newOrder,
                'procurable_id' => $procurableId,
                'procurable_type' => $procurableType,
            ]);
        }

        // Standard update or create
        if ($existingMode) {
            $existingMode->update([
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
            ]);
        } else {
            $uid = "{$this->ref_number}-{$modeId}-{$modeOrder}";
            $existingMode = Mop::create([
                'mop_group_ref' => $this->ref_number,
                'uid' => $uid,
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
                'procurable_id' => $procurableId,
                'procurable_type' => $procurableType,
            ]);
        }

        return $existingMode;
    }
    private function preventDuplicateMode($modeId, $mode)
    {
        if ($modeId == 1) {
            $exists = Mop::where('mop_group_ref', $this->ref_number)
                ->where('mode_of_procurement_id', 1)
                ->exists();

            if ($exists && (empty($mode['uid']) || str_starts_with($mode['uid'], 'TEMP-'))) {
                throw new \Exception('Mode of procurement ID 1 is already added.');
            }
        }
    }
    private function updateOrCreateBidMode($mode, $modeId, $modeOrder)
    {
        $existingMode = !empty($mode['uid']) && !str_starts_with($mode['uid'], 'TEMP-')
            ? Mop::where('uid', $mode['uid'])->first()
            : null;

        // If existing record is mode_id == 1 and incoming is not 1, create new
        if ($existingMode && $existingMode->mode_of_procurement_id == 1 && $modeId != 1) {
            // Determine the next mode_order for this procID
            $newOrder = Mop::where('mop_group_ref', $this->ref_number)->max('mode_order') + 1;

            // Generate a clean uid (not temporary)
            $uid = "MOP{$modeId}-{$newOrder}";

            return Mop::create([
                'mop_group_ref' => $this->ref_number,
                'uid' => $uid,
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $newOrder,
                'procurable_type' => $this->procurementType,
            ]);
        }

        // Standard update or create
        if ($existingMode) {
            $update = [
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
            ];

            $existingMode->update($update);
        } else {
            $uid = "MOP{$modeId}-{$modeOrder}";
            $existingMode = Mop::create([
                'mop_group_ref' => $this->ref_number,
                'uid' => $uid,
                'mode_of_procurement_id' => $modeId,
                'mode_order' => $modeOrder,
                'procurable_type' => $this->procurementType,
            ]);
        }

        return $existingMode;
    }
    private function processSchedules(array $schedules, string $uid, int $modeId)
    {
        $reorderedSchedules = array_reverse($schedules);

        foreach ($reorderedSchedules as $i => $schedule) {
            $biddingNumber = $i + 1;
            $scheduleUid = "{$uid}-{$biddingNumber}";

            \Log::info("Processing Schedule for UID: {$scheduleUid}", $schedule);

            $baseData = [
                'mop_group_ref' => $this->ref_number,
                'uid' => $scheduleUid,
                'ib_number' => $schedule['ib_number'] ?? null,
                'pre_proc_conference' => $schedule['pre_proc_conference'] ?? null,
                'ads_post_ib' => $schedule['ads_post_ib'] ?? null,
                'pre_bid_conf' => $schedule['pre_bid_conf'] ?? null,
                'eligibility_check' => $schedule['eligibility_check'] ?? null,
                'sub_open_bids' => $schedule['sub_open_bids'] ?? null,
                'bidding_number' => $biddingNumber,
            ];

            if ($modeId == 4) {
                $ntfData = array_merge($baseData, [
                    'ntf_no' => $schedule['ntf_no'] ?? null,
                    'ntf_bidding_date' => $schedule['ntf_bidding_date'] ?? null,
                    'ntf_bidding_result' => $schedule['ntf_bidding_result'] ?? null,
                    'rfq_no' => $schedule['rfq_no'] ?? null,
                    'canvass_date' => $schedule['canvass_date'] ?? null,
                    'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
                    'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
                ]);

                NtfBidSchedule::updateOrCreate(
                    ['mop_group_ref' => $this->ref_number, 'uid' => $scheduleUid],
                    $ntfData
                );

            } else {
                $bidData = array_merge($baseData, [
                    'bidding_date' => $schedule['bidding_date'] ?? null,
                    'bidding_result' => $schedule['bidding_result'] ?? null,
                ]);

                BidSchedule::updateOrCreate(
                    ['mop_group_ref' => $this->ref_number, 'uid' => $scheduleUid],
                    $bidData
                );
            }
        }
    }
    private function processPrSvp(array $schedules, string $uid)
    {
        $schedule = $schedules[0] ?? null;

        if (!$schedule)
            return;

        PrSvp::updateOrCreate(
            ['mop_group_ref' => $this->ref_number, 'uid' => $uid],
            [
                'resolution_number' => $schedule['resolution_number'] ?? null,
                'rfq_no' => $schedule['rfq_no'] ?? null,
                'canvass_date' => $schedule['canvass_date'] ?? null,
                'date_returned_of_canvass' => $schedule['date_returned_of_canvass'] ?? null,
                'abstract_of_canvass_date' => $schedule['abstract_of_canvass_date'] ?? null,
            ]
        );
    }
    private function syncModeUidToForm($mode, $uid, $modeOrder)
    {
        foreach ($this->form['modes'] as &$formMode) {
            if (
                (!empty($formMode['uid']) && $formMode['uid'] === $mode['uid']) ||
                (empty($formMode['uid']) && $formMode['mode_of_procurement_id'] === $mode['mode_of_procurement_id'])
            ) {
                $formMode['uid'] = $uid;
                $formMode['mode_order'] = $modeOrder;
                break;
            }
        }
    }
    public function checkSuccessfulBidOrNtf()
    {
        if (empty($this->ref_number)) {
            $this->hasSuccessfulBidOrNtf = false;
            return;
        }

        $this->hasSuccessfulBidOrNtf = BidSchedule::where('mop_group_ref', $this->ref_number)
            ->where('bidding_result', 'SUCCESSFUL')
            ->exists() || NtfBidSchedule::where('mop_group_ref', $this->ref_number)
                ->where('ntf_bidding_result', 'SUCCESSFUL')
                ->exists();
    }
    public function checkSuccessfulSvp()
    {
        if (empty($this->ref_number)) {
            $this->hasSuccessfulSvp = false;
            return;
        }

        // Adjust the field below if you have a different indicator for SVP success
        $this->hasSuccessfulSvp = PrSvp::where('mop_group_ref', $this->ref_number)
            ->whereNotNull('resolution_number')
            ->whereNotNull('rfq_no')
            ->whereNotNull('canvass_date')
            ->whereNotNull('date_returned_of_canvass')
            ->whereNotNull('abstract_of_canvass_date')
            ->exists();
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
