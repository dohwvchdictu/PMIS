<?php

namespace App\Livewire\Reports;

use App\Models\BacType;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Procurement;
use App\Exports\BacPrsReceivedExport;
use Maatwebsite\Excel\Facades\Excel;

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
        'bacCategoryFilter' => ['except' => ''],
    ];
    protected $paginationTheme = 'tailwind';

    // Search
    public $search = '';

    // Filters
    public $startDate = '';
    public $endDate = '';
    public $bacCategoryFilter = '';

    // Reference Data
    public $bacCategories = [];

    public function mount()
    {
        $this->loadFilterOptions();
    }

    /**
     * Load filter options
     */
    public function loadFilterOptions()
    {
        $this->bacCategories = BacType::orderBy('name')->get();
    }

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

    public function updatedBacCategoryFilter()
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
        $this->bacCategoryFilter = '';
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

        $fileName = 'BAC_PRs_Received' . $dateRange . '.xlsx';

        return Excel::download(
            new BacPrsReceivedExport(
                $this->search,
                $this->startDate,
                $this->endDate,
                $this->bacCategoryFilter
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
                'fundSource'
            ])
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

        // Apply BAC category filter
        if (!empty($this->bacCategoryFilter)) {
            $query->whereHas('category', function ($q) {
                $q->where('bac_type_id', $this->bacCategoryFilter);
            });
        }

        $procurements = $query->paginate($this->perPage);

        return view('livewire.reports.bac-prs-received-page', [
            'procurements' => $procurements,
        ]);
    }
}
