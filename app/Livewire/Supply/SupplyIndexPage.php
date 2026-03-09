<?php

namespace App\Livewire\Supply;

use App\Models\Supply;
use App\Models\SupplyPo;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

#[Title('Supply | PMIS')]
class SupplyIndexPage extends Component
{
    use WithPagination;

    private const TIMEZONE = 'Asia/Manila';
    private const DATE_FORMAT = 'Y-m-d';
    private const ALERT_TYPES = ['success', 'error', 'warning', 'info'];

    protected $paginationTheme = 'tailwind';

    // Pagination
    public int $pendingPerPage = 10;
    public int $receivedPerPage = 10;
    public int $pendingPage = 1;
    public int $receivedPage = 1;
    public int $expandedPage = 1;
    public int $expandedPerPage = 10;

    // Search
    public string $search = '';

    // Expand/collapse
    public ?string $expandedPoNumber = null;

    // Receive Modal
    public bool $showReceiveModal = false;
    public ?int $receivingSupplyId = null;
    public ?string $receiveDate = null;
    public ?string $receiveRemarks = null;

    // Bulk Receive
    public array $selectedSupplyIds = [];
    public bool $showBulkReceiveModal = false;
    public ?string $bulkReceiveDate = null;
    public ?string $bulkReceiveRemarks = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'pendingPerPage' => ['except' => 10],
        'receivedPerPage' => ['except' => 10],
    ];

    public function mount(): void
    {
        if (session('alert')) {
            $alert = session('alert');
            $alertType = $alert['type'] ?? 'info';

            if (!in_array($alertType, self::ALERT_TYPES)) {
                $alertType = 'info';
            }

            LivewireAlert::title($alert['title'] ?? 'Alert')
                ->{$alertType}()
                    ->text($alert['text'] ?? '')
                    ->toast()
                    ->position('top-end')
                    ->show();

            session()->forget('alert');
        }
    }

    public function updatingSearch(): void
    {
        $this->pendingPage = 1;
        $this->receivedPage = 1;
    }

    public function updatingPendingPerPage(): void
    {
        $this->pendingPage = 1;
    }

    public function updatingReceivedPerPage(): void
    {
        $this->receivedPage = 1;
    }

    public function toggleExpand(string $poNumber): void
    {
        if ($this->expandedPoNumber === $poNumber) {
            $this->expandedPoNumber = null;
        } else {
            $this->expandedPoNumber = $poNumber;
            $this->expandedPage = 1;
        }
    }

    public function setExpandedPage(int $page): void
    {
        $this->expandedPage = $page;
    }

    public function updatingExpandedPerPage(): void
    {
        $this->expandedPage = 1;
    }

    public function setPendingPage(int $page): void
    {
        $this->pendingPage = $page;
    }

    public function setReceivedPage(int $page): void
    {
        $this->receivedPage = $page;
    }

    public function render()
    {
        $baseQuery = Supply::query()
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('pmu_po')
                    ->whereColumn('pmu_po.po_contract_number', 'supplies.po_contract_number')
                    ->whereNull('pmu_po.deleted_at')
                    ->where(function ($q2) {
                        // Lot-based: pr_lot_prstage has stage 14
                        $q2->whereExists(function ($q3) {
                            $q3->select(\DB::raw(1))
                                ->from('pr_lot_prstage')
                                ->whereColumn('pr_lot_prstage.procID', 'pmu_po.ref_id')
                                ->where('pr_lot_prstage.pr_stage_id', 14);
                        })
                            // Item-based: pr_item_prstage has stage 14
                            ->orWhereExists(function ($q3) {
                            $q3->select(\DB::raw(1))
                                ->from('pr_item_prstage')
                                ->whereColumn('pr_item_prstage.prItemID', 'pmu_po.ref_id')
                                ->where('pr_item_prstage.pr_stage_id', 14);
                        });
                    });
            })
            ->when(!empty($this->search), function ($q) {
                $s = '%' . $this->search . '%';
                $q->where(function ($q2) use ($s) {
                    $q2->where('po_contract_number', 'like', $s)
                        ->orWhere('remarks', 'like', $s);
                });
            });

        $pendingItems = (clone $baseQuery)
            ->whereNull('date_received')
            ->orderByDesc('date_forwarded')
            ->orderByDesc('id')
            ->paginate($this->pendingPerPage, ['*'], 'pending_page', $this->pendingPage);

        $receivedItems = (clone $baseQuery)
            ->whereNotNull('date_received')
            ->orderByDesc('date_received')
            ->orderByDesc('id')
            ->paginate($this->receivedPerPage, ['*'], 'received_page', $this->receivedPage);

        $supplyPoByRefId = collect();
        if ($this->expandedPoNumber) {
            $expandedSupply = Supply::where('po_contract_number', $this->expandedPoNumber)->first();
            if ($expandedSupply) {
                $supplyPoByRefId = SupplyPo::where('supply_id', $expandedSupply->id)
                    ->get()
                    ->keyBy('ref_id');
            }
        }

        return view('livewire.supply.supply-index-page', [
            'pendingItems' => $pendingItems,
            'receivedItems' => $receivedItems,
            'expandedPaginator' => $this->buildExpandedRows(),
            'supplyPoByRefId' => $supplyPoByRefId,
        ]);
    }

    // ─── Expanded Procurement Details ───────────────────────────────────────

    private function buildExpandedRows(): \Illuminate\Pagination\LengthAwarePaginator
    {
        if (!$this->expandedPoNumber) {
            return new \Illuminate\Pagination\LengthAwarePaginator([], 0, $this->expandedPerPage, $this->expandedPage);
        }

        // Lot-based rows: pmu_po.ref_id = procurements.procID
        $lots = \Illuminate\Support\Facades\DB::table('pmu_po')
            ->join('procurements', 'procurements.procID', '=', 'pmu_po.ref_id')
            ->leftJoin('post_procurements', 'post_procurements.ref_id', '=', 'pmu_po.ref_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->whereNotExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('pr_items')
                    ->whereColumn('pr_items.prItemID', 'pmu_po.ref_id');
            })
            ->where('pmu_po.po_contract_number', $this->expandedPoNumber)
            ->whereNull('pmu_po.deleted_at')
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('pr_lot_prstage')
                    ->whereColumn('pr_lot_prstage.procID', 'pmu_po.ref_id')
                    ->where('pr_lot_prstage.pr_stage_id', 14);
            })
            ->select(
                'pmu_po.ref_id as rowKey',
                'procurements.procID',
                'procurements.pr_number',
                \DB::raw('procurements.procurement_program_project as description'),
                'suppliers.name as supplier_name',
                'pmu_po.po_date',
                'pmu_po.po_contract_number',
                'pmu_po.contract_amount',
            )
            ->get();

        // Item-based rows: pmu_po.ref_id = pr_items.prItemID
        $items = \Illuminate\Support\Facades\DB::table('pmu_po')
            ->join('pr_items', 'pr_items.prItemID', '=', 'pmu_po.ref_id')
            ->join('procurements', 'procurements.procID', '=', 'pr_items.procID')
            ->leftJoin('post_procurements', 'post_procurements.ref_id', '=', 'pmu_po.ref_id')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->where('pmu_po.po_contract_number', $this->expandedPoNumber)
            ->whereNull('pmu_po.deleted_at')
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('pr_item_prstage')
                    ->whereColumn('pr_item_prstage.prItemID', 'pmu_po.ref_id')
                    ->where('pr_item_prstage.pr_stage_id', 14);
            })
            ->select(
                'procurements.procID',
                'procurements.pr_number',
                'pr_items.description',
                'suppliers.name as supplier_name',
                'pmu_po.po_date',
                'pmu_po.ref_id as rowKey',
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

    // ─── Single Receive Modal ────────────────────────────────────────────────

    public function openReceiveModal(int $id): void
    {
        $supply = Supply::findOrFail($id);
        $this->receivingSupplyId = $id;
        $this->receiveDate = $supply->date_received
            ? $supply->date_received->format(self::DATE_FORMAT)
            : now(self::TIMEZONE)->format(self::DATE_FORMAT);
        $this->receiveRemarks = $supply->remarks ?? '';
        $this->showReceiveModal = true;
    }

    public function confirmReceive(): void
    {
        $this->validate([
            'receiveDate' => 'required|date',
            'receiveRemarks' => 'nullable|string|max:1000',
        ], [
            'receiveDate.required' => 'Received date is required.',
            'receiveDate.date' => 'Please enter a valid date.',
        ]);

        try {
            $supply = Supply::findOrFail($this->receivingSupplyId);

            $supply->update([
                'date_received' => $this->receiveDate,
                'remarks' => $this->receiveRemarks ?: null,
            ]);

            $this->closeReceiveModal();

            LivewireAlert::title('Marked as Received!')
                ->success()
                ->text('PO/Contract No. ' . $supply->po_contract_number . ' has been marked as received.')
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SupplyIndexPage: Failed to mark supply as received', [
                'supply_id' => $this->receivingSupplyId,
                'error' => $e->getMessage(),
            ]);

            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to mark the record as received. Please try again.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function closeReceiveModal(): void
    {
        $this->showReceiveModal = false;
        $this->receivingSupplyId = null;
        $this->receiveDate = null;
        $this->receiveRemarks = null;
        $this->resetValidation(['receiveDate', 'receiveRemarks']);
    }

    // ─── Bulk Receive ────────────────────────────────────────────────────────

    public function toggleSupplySelection(int $id): void
    {
        if (in_array($id, $this->selectedSupplyIds)) {
            $this->selectedSupplyIds = array_values(
                array_filter($this->selectedSupplyIds, fn($v) => $v !== $id)
            );
        } else {
            $this->selectedSupplyIds[] = $id;
        }
    }

    public function clearSelection(): void
    {
        $this->selectedSupplyIds = [];
    }

    public function openBulkReceiveModal(): void
    {
        if (empty($this->selectedSupplyIds)) {
            return;
        }
        $this->bulkReceiveDate = now(self::TIMEZONE)->format(self::DATE_FORMAT);
        $this->bulkReceiveRemarks = null;
        $this->showBulkReceiveModal = true;
    }

    public function confirmBulkReceive(): void
    {
        $this->validate([
            'bulkReceiveDate' => 'required|date',
            'bulkReceiveRemarks' => 'nullable|string|max:1000',
        ], [
            'bulkReceiveDate.required' => 'Received date is required.',
            'bulkReceiveDate.date' => 'Please enter a valid date.',
        ]);

        try {
            Supply::whereIn('id', $this->selectedSupplyIds)->update([
                'date_received' => $this->bulkReceiveDate,
                'remarks' => $this->bulkReceiveRemarks ?: null,
            ]);

            $count = count($this->selectedSupplyIds);

            $this->closeBulkReceiveModal();
            $this->selectedSupplyIds = [];

            LivewireAlert::title('Bulk Receive Successful!')
                ->success()
                ->text("{$count} record(s) have been marked as received.")
                ->toast()
                ->position('top-end')
                ->show();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('SupplyIndexPage: Failed to bulk receive supplies', [
                'supply_ids' => $this->selectedSupplyIds,
                'error' => $e->getMessage(),
            ]);

            LivewireAlert::title('Error')
                ->error()
                ->text('Failed to bulk mark records as received. Please try again.')
                ->toast()
                ->position('top-end')
                ->show();
        }
    }

    public function closeBulkReceiveModal(): void
    {
        $this->showBulkReceiveModal = false;
        $this->bulkReceiveDate = null;
        $this->bulkReceiveRemarks = null;
        $this->resetValidation(['bulkReceiveDate', 'bulkReceiveRemarks']);
    }
}
