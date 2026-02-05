<?php

namespace App\Livewire\Reports;

use App\Models\BacType;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Procurement;
use App\Exports\BacPrsReceivedExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\MopLot;
use App\Models\BidSchedule;
use App\Models\PrSvp;
use App\Models\ModeOfProcurement;

class BacPrsReceivedPage extends Component
{
    use WithPagination;

    // Pagination
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
        'startDate' => ['except' => ''],
        'endDate' => ['except' => ''],
        'currentModeFilter' => ['except' => null],
    ];
    protected $paginationTheme = 'tailwind';

    // Search
    public $search = '';

    // Filters
    public $startDate = '';
    public $endDate = '';
    public $currentModeFilter = null;

    public function mount()
    {
    }

    /**
     * Load filter options
     */

    /**
     * Reset pagination when search or filters change.
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedStartDate()
    {
        $this->resetPage();
    }

    public function updatedEndDate()
    {
        $this->resetPage();
    }

    public function updatedCurrentModeFilter()
    {
        $this->resetPage();
    }

    /**
     * Clear all filters
     */
    public function clearFilters()
    {
        $this->search = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->currentModeFilter = null;
        $this->resetPage();
    }

    /**
     * Export to Excel
     */
    public function exportToExcel()
    {
        $dateRange = '';
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $dateRange = '_' . $this->startDate . '_to_' . $this->endDate;
        } elseif (!empty($this->startDate)) {
            $dateRange = '_from_' . $this->startDate;
        } elseif (!empty($this->endDate)) {
            $dateRange = '_to_' . $this->endDate;
        } else {
            $dateRange = '_' . now()->format('Y-m-d');
        }

        $fileName = 'BAC_PRs_Received_Category_A' . $dateRange . '.xlsx';

        return Excel::download(
            new BacPrsReceivedExport(
                $this->search,
                $this->startDate,
                $this->endDate,
                $this->currentModeFilter
            ),
            $fileName
        );
    }

    public function render()
    {
        $query = Procurement::query()
            ->with([
                'currentPrStage.procurementStage',
                'division',
                'clusterCommittee',
                'category.bacType',
                'fundSource',
                'mopLots.modeOfProcurement',
                'pr_items.mopItems.modeOfProcurement'
            ])
            ->whereHas('category', function ($q) {
                $q->where('bac_type_id', 1);
            })
            ->latest('date_receipt');

        // Apply search filter
        if (!empty($this->search)) {
            $searchTerm = '%' . $this->search . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('pr_number', 'like', $searchTerm)
                    ->orWhere('procurement_program_project', 'like', $searchTerm);
            });
        }

        // Apply period covered filter
        if (!empty($this->startDate) && !empty($this->endDate)) {
            $query->whereBetween('date_receipt', [$this->startDate, $this->endDate]);
        } elseif (!empty($this->startDate)) {
            $query->whereDate('date_receipt', '>=', $this->startDate);
        } elseif (!empty($this->endDate)) {
            $query->whereDate('date_receipt', '<=', $this->endDate);
        }

        // Current Mode filter
        if ($this->currentModeFilter) {
            $query->where(function ($q) {
                // Per-lot procurements
                $q->where('procurement_type', 'perLot')
                    ->whereHas('mopLots', function ($subQ) {
                        $subQ->where('mode_of_procurement_id', $this->currentModeFilter)
                            ->whereRaw('mode_order = (SELECT MAX(mode_order) FROM mop_lot WHERE procID = procurements.procID)');
                    });
            });
        }

        $procurements = $query->paginate($this->perPage);

        // Add current mode and status to each procurement
        foreach ($procurements as $procurement) {
            $modeStatus = $this->getCurrentModeAndStatus($procurement);
            $procurement->currentMode = $modeStatus['mode'];
            $procurement->currentStatus = $modeStatus['status'];
        }

        return view('livewire.reports.bac-prs-received-page', [
            'procurements' => $procurements,
            'modes' => $this->modes,
        ]);
    }

    public function getModesProperty()
    {
        return ModeOfProcurement::orderBy('id', 'asc')
            ->get()
            ->map(function ($mode) {
                return [
                    'id' => $mode->id,
                    'name' => $mode->modeofprocurements,
                ];
            });
    }

    /**
     * Get the current mode of procurement and status for a procurement
     */
    private function getCurrentModeAndStatus($procurement)
    {
        if ($procurement->procurement_type === 'perLot') {
            $latestMop = $procurement->mopLots()
                ->orderBy('mode_order', 'desc')
                ->first();

            if (!$latestMop) {
                return ['mode' => null, 'status' => null];
            }

            $status = null;
            $modeId = $latestMop->mode_of_procurement_id;

            // Mode 1 - No schedule needed
            if ($modeId == 1) {
                return [
                    'mode' => $latestMop->modeOfProcurement,
                    'status' => 'No Schedule'
                ];
            }

            // Check bidding modes (2-6)
            if (in_array($modeId, [2, 3, 4, 5, 6])) {
                $bidSchedule = BidSchedule::where('mop_uid', $latestMop->uid)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($bidSchedule && $bidSchedule->bidding_result) {
                    $status = 'Completed';
                }
            }

            // Check SVP modes (7-24)
            if (in_array($modeId, [7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24])) {
                $prSvp = PrSvp::where('mop_uid', $latestMop->uid)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($prSvp && $prSvp->resolution_number) {
                    $status = 'Completed';
                }
            }

            return [
                'mode' => $latestMop->modeOfProcurement,
                'status' => $status
            ];
        } else {
            return ['mode' => null, 'status' => 'Multiple'];
        }
    }
}
