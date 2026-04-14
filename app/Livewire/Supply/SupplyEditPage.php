<?php

namespace App\Livewire\Supply;

use App\Models\Supply;
use App\Models\SupplyPo;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

#[Title('Supply | PMIS')]
class SupplyEditPage extends Component
{
    use WithPagination;

    private const TIMEZONE = 'Asia/Manila';
    private const DATE_FORMAT = 'Y-m-d';

    public int $supplyId;

    // Supply-level editable fields
    public string $date_forwarded = '';
    public string $date_received = '';
    public string $remarks = '';

    // Expanded procurement rows
    public int $expandedPage = 1;
    public int $expandedPerPage = 10;

    // ─── Bulk selection ───────────────────────────────────────────────────────
    public array $selectedItems = [];
    public bool $selectAll = false;

    // ─── Bulk edit modal ─────────────────────────────────────────────────────
    public bool $showBulkEditModal = false;
    public string $bulk_description = '';
    public string $bulk_deadline = '';
    public string $bulk_date_of_delivery = '';
    public string $bulk_date_of_acceptance = '';
    public string $bulk_delivery_completion = '';
    public string $bulk_date_received_from_end_user = '';
    public string $bulk_date_forwarded_to_budget = '';

    // ─── Edit Remarks modal ───────────────────────────────────────────────────
    public bool $showEditRemarksModal = false;

    public string $editRemarksValue = '';

    public function mount(int $id): void
    {
        $supply = Supply::findOrFail($id);
        $this->supplyId = $supply->id;
        $this->date_forwarded = $supply->date_forwarded ? $supply->date_forwarded->format(self::DATE_FORMAT) : '';
        $this->date_received = $supply->date_received ? $supply->date_received->format(self::DATE_FORMAT) : '';
        $this->remarks = $supply->remarks ?? '';
    }

    // ─── Save supply header ───────────────────────────────────────────────────

    public function save(): mixed
    {
        try {
            $this->validate([
                'date_forwarded' => 'nullable|date',
                'date_received' => 'nullable|date',
                'remarks' => 'nullable|string|max:1000',
            ], [
                'date_forwarded.date' => 'Date Forwarded must be a valid date.',
                'date_received.date' => 'Date Received must be a valid date.',
            ]);
        } catch (ValidationException $e) {
            LivewireAlert::title('Validation Error')
                ->error()
                ->text(collect($e->errors())->flatten()->first())
                ->toast()
                ->position('top-end')
                ->show();
            return null;
        }

        try {
            DB::beginTransaction();

            $supply = Supply::findOrFail($this->supplyId);
            $supply->update([
                'date_forwarded' => $this->date_forwarded ?: null,
                'date_received' => $this->date_received ?: null,
                'remarks' => $this->remarks ?: null,
            ]);

            DB::commit();

            session()->flash('alert', [
                'type' => 'success',
                'title' => 'Saved!',
                'text' => 'Supply record updated successfully.',
            ]);

            return redirect()->route('supply.index');
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('SupplyEditPage: Failed to update supply', [
                'supply_id' => $this->supplyId,
                'error' => $e->getMessage(),
            ]);
            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to update record. Please try again.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function cancel(): mixed
    {
        return redirect()->route('supply.index');
    }

    // ─── Bulk selection ───────────────────────────────────────────────────────

    public function updatedSelectAll(bool $value): void
    {
        $pageIds = collect($this->buildExpandedRows()->items())
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
        $pageIds = collect($this->buildExpandedRows()->items())
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

    // ─── Bulk edit modal ─────────────────────────────────────────────────────

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

        $this->resetBulkFields();

        // Pre-fill from existing supply_po records
        $existing = SupplyPo::where('supply_id', $this->supplyId)
            ->whereIn('ref_id', $this->selectedItems)
            ->get()
            ->keyBy('ref_id');

        $snapshots = collect($this->selectedItems)->map(function ($rowKey) use ($existing) {
            $row = $existing->get($rowKey);
            return [
                'description' => $row?->description ?? '',
                'deadline' => $row && $row->deadline ? $row->deadline->format('Y-m-d') : '',
                'date_of_delivery' => $row && $row->date_of_delivery ? $row->date_of_delivery->format('Y-m-d') : '',
                'date_of_acceptance' => $row && $row->date_of_acceptance ? $row->date_of_acceptance->format('Y-m-d') : '',
                'delivery_completion' => $row && $row->delivery_completion ? $row->delivery_completion->format('Y-m-d') : '',
                'date_received_from_end_user' => $row && $row->date_received_from_end_user ? $row->date_received_from_end_user->format('Y-m-d') : '',
            ];
        })->values();

        // Block if selected items have conflicting data
        if ($snapshots->unique()->count() > 1) {
            LivewireAlert::title('Conflicting Data')
                ->error()
                ->text('The selected items have different supply details. Please select items with identical data or edit them one at a time.')
                ->toast()
                ->timer(5000)
                ->position('top-end')
                ->show();
            return;
        }

        // Pre-fill form with common data
        $data = $snapshots->first() ?? [];
        $this->bulk_description = $data['description'];
        $this->bulk_deadline = $data['deadline'];
        $this->bulk_date_of_delivery = $data['date_of_delivery'];
        $this->bulk_date_of_acceptance = $data['date_of_acceptance'];
        $this->bulk_delivery_completion = $data['delivery_completion'];
        $this->bulk_date_received_from_end_user = $data['date_received_from_end_user'];

        $this->showBulkEditModal = true;
    }

    public function closeBulkEditModal(): void
    {
        $this->showBulkEditModal = false;
        $this->resetBulkFields();
        $this->resetValidation([
            'bulk_description',
            'bulk_deadline',
            'bulk_date_of_delivery',
            'bulk_date_of_acceptance',
            'bulk_delivery_completion',
            'bulk_date_received_from_end_user',
            'bulk_date_forwarded_to_budget',
        ]);
    }

    public function saveBulkEdit(): void
    {
        $this->validate([
            'bulk_description' => 'nullable|string|max:1000',
            'bulk_deadline' => 'nullable|date',
            'bulk_date_of_delivery' => 'nullable|date',
            'bulk_date_of_acceptance' => 'nullable|date',
            'bulk_delivery_completion' => 'nullable|date',
            'bulk_date_received_from_end_user' => 'nullable|date',
            'bulk_date_forwarded_to_budget' => 'nullable|date',
        ], [
            'bulk_deadline.date' => 'Deadline must be a valid date.',
            'bulk_date_of_delivery.date' => 'Date of Delivery must be a valid date.',
            'bulk_date_of_acceptance.date' => 'Date of Acceptance must be a valid date.',
            'bulk_delivery_completion.date' => 'Delivery Completion must be a valid date.',
            'bulk_date_received_from_end_user.date' => 'Date Received from End User must be a valid date.',
            'bulk_date_forwarded_to_budget.date' => 'Date Forwarded to Budget must be a valid date.',
        ]);

        try {
            DB::beginTransaction();

            $count = count($this->selectedItems);

            foreach ($this->selectedItems as $rowKey) {
                SupplyPo::updateOrCreate(
                    ['supply_id' => $this->supplyId, 'ref_id' => $rowKey],
                    [
                        'description' => $this->bulk_description ?: null,
                        'deadline' => $this->bulk_deadline ?: null,
                        'date_of_delivery' => $this->bulk_date_of_delivery ?: null,
                        'date_of_acceptance' => $this->bulk_date_of_acceptance ?: null,
                        'delivery_completion' => $this->bulk_delivery_completion ?: null,
                        'date_received_from_end_user' => $this->bulk_date_received_from_end_user ?: null,
                        'date_forwarded_to_budget' => $this->bulk_date_forwarded_to_budget ?: null,
                    ]
                );
            }

            DB::commit();

            $this->closeBulkEditModal();
            $this->clearSelections();

            LivewireAlert::title('Saved!')
                ->success()
                ->text($count . ' item(s) updated successfully.')
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('SupplyEditPage: Failed to bulk save', [
                'supply_id' => $this->supplyId,
                'error' => $e->getMessage(),
            ]);
            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to save records. Please try again.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    private function resetBulkFields(): void
    {
        $this->bulk_description = '';
        $this->bulk_deadline = '';
        $this->bulk_date_of_delivery = '';
        $this->bulk_date_of_acceptance = '';
        $this->bulk_delivery_completion = '';
        $this->bulk_date_received_from_end_user = '';
        $this->bulk_date_forwarded_to_budget = '';
    }

    // ─── Edit Remarks modal ───────────────────────────────────────────────────

    public function openEditRemarksModal(): void
    {
        $this->editRemarksValue = $this->remarks;
        $this->showEditRemarksModal = true;
    }

    public function confirmEditRemarks(): void
    {
        $this->validate(['editRemarksValue' => 'nullable|string|max:1000']);

        try {
            $supply = Supply::findOrFail($this->supplyId);
            $supply->update(['remarks' => $this->editRemarksValue ?: null]);
            $this->remarks = $this->editRemarksValue;
            $this->showEditRemarksModal = false;

            LivewireAlert::title('Saved!')
                ->success()
                ->text('Remarks updated successfully.')
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SupplyEditPage: Failed to update remarks', [
                'error' => $e->getMessage(),
            ]);
            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to update remarks. Please try again.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function closeEditRemarksModal(): void
    {
        $this->showEditRemarksModal = false;
        $this->editRemarksValue = '';
        $this->resetValidation(['editRemarksValue']);
    }

    // ─── Expanded procurement rows (same logic as index page) ─────────────────

    public function setExpandedPage(int $page): void
    {
        $this->expandedPage = $page;
    }

    public function updatingExpandedPerPage(): void
    {
        $this->expandedPage = 1;
    }

    private function buildExpandedRows(): \Illuminate\Pagination\LengthAwarePaginator
    {
        $supply = Supply::find($this->supplyId);
        $poNumber = $supply?->po_contract_number;

        if (!$poNumber) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->expandedPerPage, $this->expandedPage);
        }

        // Lot-based rows
        $lots = DB::table('pmu_po')
            ->join('procurements', 'procurements.procID', '=', 'pmu_po.ref_id')
            ->leftJoin('post_procurements', 'post_procurements.ref_id', '=', 'pmu_po.ref_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->leftJoin('end_users', 'end_users.id', '=', 'procurements.end_users_id')
            ->whereNotExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pr_items')
                    ->whereColumn('pr_items.prItemID', 'pmu_po.ref_id');
            })
            ->where('pmu_po.po_contract_number', $poNumber)
            ->whereNull('pmu_po.deleted_at')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pr_lot_prstage')
                    ->whereColumn('pr_lot_prstage.procID', 'pmu_po.ref_id')
                    ->where('pr_lot_prstage.pr_stage_id', 14);
            })
            ->select(
                'pmu_po.ref_id as rowKey',
                'procurements.procID',
                'procurements.pr_number',
                DB::raw('procurements.procurement_program_project as description'),
                'suppliers.name as supplier_name',
                'end_users.endusers as end_user_name',
                'pmu_po.date_po_receipt_by_supplier',
                'pmu_po.date_coa_stamped_received',
                'pmu_po.contract_amount',
            )
            ->get();

        // Item-based rows
        $items = DB::table('pmu_po')
            ->join('pr_items', 'pr_items.prItemID', '=', 'pmu_po.ref_id')
            ->join('procurements', 'procurements.procID', '=', 'pr_items.procID')
            ->leftJoin('post_procurements', 'post_procurements.ref_id', '=', 'pmu_po.ref_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->leftJoin('end_users', 'end_users.id', '=', 'procurements.end_users_id')
            ->where('pmu_po.po_contract_number', $poNumber)
            ->whereNull('pmu_po.deleted_at')
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pr_item_prstage')
                    ->whereColumn('pr_item_prstage.prItemID', 'pmu_po.ref_id')
                    ->where('pr_item_prstage.pr_stage_id', 14);
            })
            ->select(
                'pmu_po.ref_id as rowKey',
                'procurements.procID',
                'procurements.pr_number',
                'pr_items.description',
                'suppliers.name as supplier_name',
                'end_users.endusers as end_user_name',
                'pmu_po.date_po_receipt_by_supplier',
                'pmu_po.date_coa_stamped_received',
                'pmu_po.contract_amount',
            )
            ->get();

        $combined = $lots->merge($items);
        $total = $combined->count();
        $perPage = max(1, (int) $this->expandedPerPage);
        $page = max(1, (int) $this->expandedPage);
        $sliced = $combined->forPage($page, $perPage)->values();

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $sliced,
            $total,
            $perPage,
            $page,
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        $supply = Supply::findOrFail($this->supplyId);

        $supplyPoByRefId = SupplyPo::where('supply_id', $this->supplyId)
            ->get()
            ->keyBy('ref_id');

        return view('livewire.supply.supply-edit-page', [
            'supply' => $supply,
            'expandedPaginator' => $this->buildExpandedRows(),
            'supplyPoByRefId' => $supplyPoByRefId,
        ])->layout('components.layouts.app');
    }
}
