<?php

namespace App\Livewire\Supply;

use App\Models\Supply;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

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

    // Search
    public string $search = '';

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

        return view('livewire.supply.supply-index-page', [
            'pendingItems' => $pendingItems,
            'receivedItems' => $receivedItems,
        ]);
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
