<?php

namespace App\Livewire\PMU;

use App\Models\Procurement;
use App\Models\PostProcurement;
use Livewire\Component;
use Livewire\WithPagination;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Support\Facades\DB;

class PmuIndexPage extends Component
{
    use WithPagination;

    protected $paginationTheme = 'tailwind';

    // Pagination
    public $perPage = 10;

    // Search
    public $search = '';

    // Filters
    public $divisionFilter = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'divisionFilter' => ['except' => null],
        'perPage' => ['except' => 10],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingDivisionFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'divisionFilter']);
        $this->resetPage();
        LivewireAlert::success('Filters Reset', 'All filters have been cleared.');
    }

    public function render()
    {
        // Get procurements with stage 7 (Forwarded to PMU) grouped by notice_of_award_number
        $groupedItems = Procurement::query()
            ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
            ->leftJoin('divisions', 'procurements.divisions_id', '=', 'divisions.id')
            ->where(function ($q) {
                // Check if procurement has lot stage or item stage with pr_stage_id = 7
                $q->whereHas('prLotPrstages', function ($query) {
                    $query->where('pr_stage_id', 7);
                })
                    ->orWhereHas('prItemPrstages', function ($query) {
                    $query->where('pr_stage_id', 7);
                });
            })
            ->when($this->search, function ($q) {
                $q->where(function ($query) {
                    $query->where('procurements.pr_number', 'like', '%' . $this->search . '%')
                        ->orWhere('post_procurements.notice_of_award_number', 'like', '%' . $this->search . '%')
                        ->orWhere('divisions.divisions', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->divisionFilter, function ($q) {
                $q->where('procurements.divisions_id', $this->divisionFilter);
            })
            ->select(
                'post_procurements.notice_of_award_number',
                DB::raw('COUNT(DISTINCT procurements.procID) as procurement_count'),
                DB::raw('SUM(procurements.abc) as total_abc'),
                DB::raw('MIN(procurements.date_receipt) as earliest_date'),
                DB::raw('MAX(procurements.date_receipt) as latest_date')
            )
            ->whereNotNull('post_procurements.notice_of_award_number')
            ->groupBy('post_procurements.notice_of_award_number')
            ->orderBy('post_procurements.notice_of_award_number', 'desc')
            ->paginate($this->perPage);

        // Get divisions for filter
        $divisions = DB::table('divisions')->orderBy('divisions')->get();

        return view('livewire.pmu.pmu-index-page', [
            'groupedItems' => $groupedItems,
            'divisions' => $divisions,
        ])->layout('components.layouts.app');
    }
}
