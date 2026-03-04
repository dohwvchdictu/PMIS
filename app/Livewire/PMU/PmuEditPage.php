<?php

namespace App\Livewire\PMU;

use App\Models\Pmu;
use App\Models\PmuPo;
use App\Models\Procurement;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PmuEditPage extends Component
{
    private const DATE_FORMAT = 'Y-m-d';

    public string $noticeOfAwardNumber = '';
    public ?string $noticeOfAward = null;

    // Linked PRs pagination
    public int $editPage = 1;
    public int $editPerPage = 10;

    // Bulk edit selection
    public array $selectedItems = [];
    public bool $selectAll = false;
    public bool $showBulkEditModal = false;

    // Form fields
    public string $date_forwarded = '';
    public string $po_date = '';
    public ?string $po_date_deadline_display = null; // Earliest PO Date Deadline among selected items (read-only, for warning)
    public string $contract_amount = '';
    public string $po_contract_number = '';
    public string $po_contract_number_link = '';
    public string $ntp_link = '';
    public string $contract_signing_date = '';
    public string $notice_to_proceed_date = '';
    public string $remarks = '';

    private \Illuminate\Support\Collection|null $combinedRowsCache = null;

    public function mount(string $id): void
    {
        $this->noticeOfAwardNumber = $id;

        $record = Pmu::where('notice_of_award_number', $id)->firstOrFail();

        $this->noticeOfAward = DB::table('post_procurements')
            ->where('notice_of_award_number', $id)
            ->value('notice_of_award');

        $this->date_forwarded = $record->date_forwarded ? $record->date_forwarded->format(self::DATE_FORMAT) : '';
    }

    public function update(): void
    {
        $this->validate([
            'po_date' => 'nullable|date',
            'contract_amount' => 'nullable|numeric|min:0',
            'po_contract_number' => 'required|string|max:255',
            'po_contract_number_link' => 'nullable|url|max:2048',
            'ntp_link' => 'nullable|url|max:2048',
            'contract_signing_date' => 'nullable|date',
            'notice_to_proceed_date' => 'nullable|date',
            'remarks' => 'nullable|string',
        ], [
            'po_date.date' => 'PO Date must be a valid date.',
            'contract_amount.numeric' => 'Contract amount must be a valid number.',
            'contract_amount.min' => 'Contract amount must be 0 or greater.',
            'po_contract_number.required' => 'PO / Contract number is required.',
            'contract_signing_date.date' => 'Contract signing date must be a valid date.',
            'notice_to_proceed_date.date' => 'Notice to proceed date must be a valid date.',
        ]);

        if (empty($this->selectedItems)) {
            LivewireAlert::title('No items selected')
                ->warning()
                ->text('Please select at least one item.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        try {
            DB::beginTransaction();

            $pmu = Pmu::where('notice_of_award_number', $this->noticeOfAwardNumber)->firstOrFail();

            $data = [
                'po_date' => ($this->po_contract_number && $this->po_date) ? $this->po_date : null,
                'contract_amount' => $this->contract_amount !== '' ? $this->contract_amount : null,
                'po_contract_number' => $this->po_contract_number ?: null,
                'po_contract_number_link' => $this->po_contract_number_link ?: null,
                'ntp_link' => $this->ntp_link ?: null,
                'contract_signing_date' => $this->contract_signing_date ?: null,
                'notice_to_proceed_date' => $this->notice_to_proceed_date ?: null,
                'remarks' => $this->remarks ?: null,
            ];

            foreach ($this->selectedItems as $rowKey) {
                $pmuPo = PmuPo::where('ref_id', $rowKey)
                    ->where('pmu_id', $pmu->id)
                    ->first();

                if ($pmuPo) {
                    // Update existing record to trigger audit events
                    $pmuPo->update($data);
                } else {
                    // Create new record to trigger audit events
                    PmuPo::create([
                        'ref_id' => $rowKey,
                        'pmu_id' => $pmu->id,
                        ...$data,
                    ]);
                }
            }

            DB::commit();

            $this->closeBulkEditModal();
            $this->clearSelections();

            LivewireAlert::title('Saved!')
                ->success()
                ->text('PO / Contract details saved for ' . count($this->selectedItems) . ' item(s).')
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('PmuEditPage: Failed to update PO records', [
                'pmu_id' => $this->noticeOfAwardNumber,
                'error' => $e->getMessage(),
            ]);
            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to save records. Please try again or contact support.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function save()
    {
        try {
            $this->validateOnly('date_forwarded', [
                'date_forwarded' => 'nullable|date',
            ]);
        } catch (ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();
            LivewireAlert::title('Validation Error')
                ->warning()
                ->text($firstError)
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        try {
            DB::beginTransaction();

            $record = Pmu::where('notice_of_award_number', $this->noticeOfAwardNumber)->firstOrFail();
            $record->update([
                'date_forwarded' => $this->date_forwarded ?: null,
            ]);

            DB::commit();

            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Updated!',
                'message' => 'PMU record updated successfully.',
            ]);

            return redirect()->route('pmu.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('PmuEditPage: Failed to update PMU record', [
                'pmu_id' => $this->noticeOfAwardNumber,
                'error' => $e->getMessage(),
            ]);
            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to update record. Please try again or contact support.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function cancel()
    {
        return redirect()->route('pmu.index');
    }

    public function updatedSelectAll(bool $value): void
    {
        $pageIds = $this->buildEditPaginator()->items();
        $pageIds = collect($pageIds)
            ->pluck('rowKey')
            ->unique()->values()->map(fn($id) => (string) $id)->toArray();

        if ($value) {
            $this->selectedItems = array_values(array_unique(
                array_merge($this->selectedItems, $pageIds)
            ));
        } else {
            $this->selectedItems = array_values(
                array_diff($this->selectedItems, $pageIds)
            );
        }
    }

    public function updatedSelectedItems(): void
    {
        $pageIds = $this->buildEditPaginator()->items();
        $pageIds = collect($pageIds)
            ->pluck('rowKey')
            ->unique()->values()->map(fn($id) => (string) $id)->toArray();
        $this->selectAll = !empty($pageIds) &&
            empty(array_diff($pageIds, array_unique($this->selectedItems)));
    }

    public function clearSelections(): void
    {
        $this->selectedItems = [];
        $this->selectAll = false;
    }

    public function openBulkEditModal(): void
    {
        if (empty($this->selectedItems)) {
            LivewireAlert::title('No items selected')
                ->warning()
                ->text('Please select at least one item.')
                ->toast()
                ->position('top-end')
                ->show();
            return;
        }

        $pmu = Pmu::where('notice_of_award_number', $this->noticeOfAwardNumber)->firstOrFail();

        // Fetch existing pmu_po rows for all selected rowKeys
        $existing = PmuPo::where('pmu_id', $pmu->id)
            ->whereIn('ref_id', $this->selectedItems)
            ->get()
            ->keyBy('ref_id');

        // Compute po_date_deadline_display: earliest PO Date Deadline among selected items (most restrictive constraint)
        $this->po_date_deadline_display = collect($this->selectedItems)
            ->map(fn($rowKey) => $existing->get($rowKey)?->po_date_deadline?->format(self::DATE_FORMAT))
            ->filter()
            ->sort()
            ->first();

        // Build a normalised snapshot for each selected item (null if no record)
        $snapshots = collect($this->selectedItems)->map(function ($rowKey) use ($existing) {
            $row = $existing->get($rowKey);
            return [
                'po_date' => $row && $row->po_date ? $row->po_date->format(self::DATE_FORMAT) : '',
                'contract_amount' => $row ? (string) $row->contract_amount : '',
                'po_contract_number' => $row ? ($row->po_contract_number ?? '') : '',
                'po_contract_number_link' => $row ? ($row->po_contract_number_link ?? '') : '',
                'ntp_link' => $row ? ($row->ntp_link ?? '') : '',
                'contract_signing_date' => $row && $row->contract_signing_date ? $row->contract_signing_date->format(self::DATE_FORMAT) : '',
                'notice_to_proceed_date' => $row && $row->notice_to_proceed_date ? $row->notice_to_proceed_date->format(self::DATE_FORMAT) : '',
                'remarks' => $row ? ($row->remarks ?? '') : '',
            ];
        })->values();

        // If multiple items selected, ensure all share identical values
        if ($snapshots->unique()->count() > 1) {
            LivewireAlert::title('Conflicting Data')
                ->error()
                ->text('The selected items have different PO / Contract details. Please select items with identical data or edit them one at a time.')
                ->toast()
                ->timer(5000)
                ->position('top-end')
                ->show();
            return;
        }

        // Pre-fill form with common data; po_date falls back to PO Date Deadline if not yet set
        $data = $snapshots->first();
        $this->po_date = $data['po_date'] ?: ($this->po_date_deadline_display ?? '');
        $this->contract_amount = $data['contract_amount'];
        $this->po_contract_number = $data['po_contract_number'];
        $this->po_contract_number_link = $data['po_contract_number_link'];
        $this->ntp_link = $data['ntp_link'];
        $this->contract_signing_date = $data['contract_signing_date'];
        $this->notice_to_proceed_date = $data['notice_to_proceed_date'];
        $this->remarks = $data['remarks'];

        $this->showBulkEditModal = true;
    }

    public function closeBulkEditModal(): void
    {
        $this->showBulkEditModal = false;
        $this->po_date = '';
        $this->po_date_deadline_display = null;
        $this->contract_amount = '';
        $this->po_contract_number = '';
        $this->po_contract_number_link = '';
        $this->ntp_link = '';
        $this->contract_signing_date = '';
        $this->notice_to_proceed_date = '';
        $this->remarks = '';
        $this->resetErrorBag();
    }

    public function setEditPage(int $page): void
    {
        $this->editPage = $page;
        $this->clearSelections();
    }

    public function updatingEditPerPage(): void
    {
        $this->editPage = 1;
        $this->clearSelections();
    }

    private function fetchCombinedRows(): \Illuminate\Support\Collection
    {
        // Return cached results if already fetched in this request cycle
        if ($this->combinedRowsCache !== null) {
            return $this->combinedRowsCache;
        }

        $id = $this->noticeOfAwardNumber;

        $lots = Procurement::query()
            ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
            ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->where('post_procurements.notice_of_award_number', $id)
            ->whereNull('pmus.deleted_at')
            ->whereHas('prLotPrstages', fn($q) => $q->where('pr_stage_id', 7))
            ->select(
                'procurements.procID',
                'procurements.pr_number',
                'procurements.procurement_program_project',
                'procurements.abc',
                'post_procurements.awarded_amount',
                'post_procurements.date_receipt_of_supplier_noa',
                'suppliers.name as supplier_name'
            )
            ->get()
            ->map(fn($p) => (object) [
                'rowKey' => $p->procID,
                'rowType' => 'lot',
                'procID' => $p->procID,
                'prItemID' => null,
                'pr_number' => $p->pr_number,
                'description' => $p->procurement_program_project,
                'abc' => $p->abc,
                'awarded_amount' => $p->awarded_amount,
                'supplier_name' => $p->supplier_name,
                'date_receipt_of_supplier_noa' => $p->date_receipt_of_supplier_noa,
            ])->toBase();

        // Cache the combined result before returning
        $items = DB::table('pr_items')
            ->join('procurements', 'procurements.procID', '=', 'pr_items.procID')
            ->join('post_procurements', 'post_procurements.ref_id', '=', 'pr_items.prItemID')
            ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->whereExists(fn($q) => $q->select(DB::raw(1))
                ->from('pr_item_prstage')
                ->whereColumn('pr_item_prstage.prItemID', 'pr_items.prItemID')
                ->where('pr_item_prstage.pr_stage_id', 7))
            ->where('post_procurements.notice_of_award_number', $id)
            ->whereNull('pmus.deleted_at')
            ->select(
                'procurements.procID',
                'procurements.pr_number',
                'pr_items.prItemID',
                'pr_items.description',
                'pr_items.amount',
                'post_procurements.awarded_amount',
                'post_procurements.date_receipt_of_supplier_noa',
                'suppliers.name as supplier_name'
            )
            ->orderBy('procurements.pr_number')
            ->orderBy('pr_items.item_no')
            ->get()
            ->map(fn($r) => (object) [
                'rowKey' => $r->prItemID,
                'rowType' => 'item',
                'procID' => $r->procID,
                'prItemID' => $r->prItemID,
                'pr_number' => $r->pr_number,
                'description' => $r->description,
                'abc' => $r->amount,
                'awarded_amount' => $r->awarded_amount,
                'supplier_name' => $r->supplier_name,
                'date_receipt_of_supplier_noa' => $r->date_receipt_of_supplier_noa,
            ]);

        return $this->combinedRowsCache = $lots->merge($items);
    }

    private function buildEditPaginator(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $combined = $this->fetchCombinedRows();
        $total = $combined->count();
        $perPage = max(1, (int) $this->editPerPage);
        $page = max(1, (int) $this->editPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $combined->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['pageName' => 'edit_page']
        );
    }

    public function render()
    {
        $pmuRecord = Pmu::where('notice_of_award_number', $this->noticeOfAwardNumber)->first();

        // Key pmu_po records by ref_id (procID) for fast per-row lookup in the blade
        $pmuPoByProcId = $pmuRecord
            ? PmuPo::where('pmu_id', $pmuRecord->id)->get()->keyBy('ref_id')
            : collect();

        return view('livewire.pmu.pmu-edit-page', [
            'noticeOfAwardNumber' => $this->noticeOfAwardNumber,
            'pmuRecord' => $pmuRecord,
            'pmuPoByProcId' => $pmuPoByProcId,
            'noticeOfAward' => $this->noticeOfAward,
            'editPaginator' => $this->buildEditPaginator(),
        ])->layout('components.layouts.app');
    }
}
