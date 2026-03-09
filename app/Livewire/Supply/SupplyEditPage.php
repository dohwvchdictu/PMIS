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
    private const DT_FORMAT = 'Y-m-d\TH:i';

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
    public string $bulk_batch_no = '';
    public string $bulk_delivery_completion = '';
    public string $bulk_date_received_from_end_user = '';
    public string $bulk_soa_amount = '';
    public string $bulk_date_forwarded_to_budget = '';

    // ─── Detail modal ─────────────────────────────────────────────────────────
    public bool $showDetailModal = false;
    public ?int $editingDetailId = null;   // null = new row
    public string $batch_no = '';
    public string $delivery_completion = '';
    public string $date_received_from_end_user = '';
    public string $soa_amount = '';
    public string $date_forwarded_to_budget = '';

    // ─── Edit Remarks modal ───────────────────────────────────────────────────
    public bool $showEditRemarksModal = false;

    public string $editRemarksValue = '';

    // ─── Delete confirm ───────────────────────────────────────────────────────
    public bool $showDeleteConfirm = false;
    public ?int $deletingDetailId = null;

    // ─── Detail pagination ────────────────────────────────────────────────────
    public int $detailPage = 1;
    public int $detailPerPage = 10;

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
        $this->showBulkEditModal = true;
    }

    public function closeBulkEditModal(): void
    {
        $this->showBulkEditModal = false;
        $this->resetBulkFields();
        $this->resetValidation([
            'bulk_batch_no',
            'bulk_delivery_completion',
            'bulk_date_received_from_end_user',
            'bulk_soa_amount',
            'bulk_date_forwarded_to_budget',
        ]);
    }

    public function saveBulkEdit(): void
    {
        $this->validate([
            'bulk_batch_no' => 'nullable|string|max:255',
            'bulk_delivery_completion' => 'nullable|date',
            'bulk_date_received_from_end_user' => 'nullable|date',
            'bulk_soa_amount' => 'nullable|numeric|min:0',
            'bulk_date_forwarded_to_budget' => 'nullable|date',
        ], [
            'bulk_delivery_completion.date' => 'Delivery Completion must be a valid date.',
            'bulk_date_received_from_end_user.date' => 'Date Received from End User must be a valid date.',
            'bulk_soa_amount.numeric' => 'SOA Amount must be a valid number.',
            'bulk_soa_amount.min' => 'SOA Amount must be 0 or greater.',
            'bulk_date_forwarded_to_budget.date' => 'Date Forwarded to Budget must be a valid date.',
        ]);

        try {
            DB::beginTransaction();

            $count = count($this->selectedItems);

            foreach ($this->selectedItems as $rowKey) {
                SupplyPo::updateOrCreate(
                    ['supply_id' => $this->supplyId, 'ref_id' => $rowKey],
                    [
                        'batch_no' => $this->bulk_batch_no ?: null,
                        'delivery_completion' => $this->bulk_delivery_completion ?: null,
                        'date_received_from_end_user' => $this->bulk_date_received_from_end_user ?: null,
                        'soa_amount' => $this->bulk_soa_amount !== '' ? $this->bulk_soa_amount : null,
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
        $this->bulk_batch_no = '';
        $this->bulk_delivery_completion = '';
        $this->bulk_date_received_from_end_user = '';
        $this->bulk_soa_amount = '';
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

    // ─── Supply Detail CRUD ───────────────────────────────────────────────────

    public function openDetailModal(?int $detailId = null): void
    {
        $this->editingDetailId = $detailId;

        if ($detailId) {
            $detail = SupplyDetail::findOrFail($detailId);
            $this->batch_no = $detail->batch_no ?? '';
            $this->delivery_completion = $detail->delivery_completion ? $detail->delivery_completion->format(self::DATE_FORMAT) : '';
            $this->date_received_from_end_user = $detail->date_received_from_end_user ? $detail->date_received_from_end_user->format(self::DT_FORMAT) : '';
            $this->soa_amount = $detail->soa_amount !== null ? (string) $detail->soa_amount : '';
            $this->date_forwarded_to_budget = $detail->date_forwarded_to_budget ? $detail->date_forwarded_to_budget->format(self::DT_FORMAT) : '';
        } else {
            $this->resetDetailFields();
        }

        $this->showDetailModal = true;
    }

    public function saveDetail(): void
    {
        $this->validate([
            'batch_no' => 'nullable|string|max:255',
            'delivery_completion' => 'nullable|date',
            'date_received_from_end_user' => 'nullable|date',
            'soa_amount' => 'nullable|numeric|min:0',
            'date_forwarded_to_budget' => 'nullable|date',
        ], [
            'delivery_completion.date' => 'Delivery Completion must be a valid date.',
            'date_received_from_end_user.date' => 'Date Received from End User must be a valid date.',
            'soa_amount.numeric' => 'SOA Amount must be a valid number.',
            'soa_amount.min' => 'SOA Amount must be 0 or greater.',
            'date_forwarded_to_budget.date' => 'Date Forwarded to Budget must be a valid date.',
        ]);

        try {
            $data = [
                'ref_id' => $this->supplyId,
                'batch_no' => $this->batch_no ?: null,
                'delivery_completion' => $this->delivery_completion ?: null,
                'date_received_from_end_user' => $this->date_received_from_end_user ?: null,
                'soa_amount' => $this->soa_amount !== '' ? $this->soa_amount : null,
                'date_forwarded_to_budget' => $this->date_forwarded_to_budget ?: null,
            ];

            if ($this->editingDetailId) {
                SupplyDetail::findOrFail($this->editingDetailId)->update($data);
                $message = 'Supply detail updated successfully.';
            } else {
                SupplyDetail::create($data);
                $message = 'Supply detail added successfully.';
            }

            $this->closeDetailModal();

            LivewireAlert::title('Saved!')
                ->success()
                ->text($message)
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SupplyEditPage: Failed to save detail', [
                'error' => $e->getMessage(),
            ]);
            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to save supply detail. Please try again.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function closeDetailModal(): void
    {
        $this->showDetailModal = false;
        $this->editingDetailId = null;
        $this->resetDetailFields();
        $this->resetValidation([
            'batch_no',
            'delivery_completion',
            'date_received_from_end_user',
            'soa_amount',
            'date_forwarded_to_budget',
        ]);
    }

    public function confirmDeleteDetail(int $detailId): void
    {
        $this->deletingDetailId = $detailId;
        $this->showDeleteConfirm = true;
    }

    public function deleteDetail(): void
    {
        try {
            SupplyDetail::findOrFail($this->deletingDetailId)->delete();

            $this->showDeleteConfirm = false;
            $this->deletingDetailId = null;

            LivewireAlert::title('Deleted!')
                ->success()
                ->text('Supply detail removed.')
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SupplyEditPage: Failed to delete detail', [
                'error' => $e->getMessage(),
            ]);
            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to delete supply detail. Please try again.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deletingDetailId = null;
    }

    private function resetDetailFields(): void
    {
        $this->batch_no = '';
        $this->delivery_completion = '';
        $this->date_received_from_end_user = '';
        $this->soa_amount = '';
        $this->date_forwarded_to_budget = '';
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

    public function setDetailPage(int $page): void
    {
        $this->detailPage = $page;
    }

    public function updatingDetailPerPage(): void
    {
        $this->detailPage = 1;
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
                'pmu_po.po_date',
                'pmu_po.po_contract_number',
                'pmu_po.contract_amount',
            )
            ->get();

        // Item-based rows
        $items = DB::table('pmu_po')
            ->join('pr_items', 'pr_items.prItemID', '=', 'pmu_po.ref_id')
            ->join('procurements', 'procurements.procID', '=', 'pr_items.procID')
            ->leftJoin('post_procurements', 'post_procurements.ref_id', '=', 'pmu_po.ref_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
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
                'pmu_po.po_date',
                'pmu_po.po_contract_number',
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

        return view('livewire.supply.supply-edit-page', [
            'supply' => $supply,
            'expandedPaginator' => $this->buildExpandedRows(),
        ])->layout('components.layouts.app');
    }
}
