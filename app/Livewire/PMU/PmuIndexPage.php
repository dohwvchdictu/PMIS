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
    public $itemsPerPage = 10;

    // Search
    public $search = '';

    // Collapsible functionality
    public $expandedNoaNumber = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'perPage' => ['except' => 10],
    ];

    public function mount()
    {
        if (session('alert')) {
            $alert = session('alert');

            LivewireAlert::title($alert['title'])
                ->{$alert['type']}()
                    ->text($alert['message'])
                    ->toast()
                    ->position('top-end')
                    ->show();
        }
    }

    /**
     * Toggle expanded/collapsed state for NOA items
     */
    public function toggle($property, $value)
    {
        $value = (string) $value;

        if ($this->$property === $value) {
            $this->$property = null;
        } else {
            $this->$property = $value;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Get procurements with stage 7 (Forwarded to PMU) grouped by notice_of_award_number
        $query = Procurement::query()
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
            ->whereNotNull('post_procurements.notice_of_award_number');

        // Apply search filter
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('procurements.pr_number', 'like', '%' . $this->search . '%')
                    ->orWhere('post_procurements.notice_of_award_number', 'like', '%' . $this->search . '%')
                    ->orWhere('divisions.divisions', 'like', '%' . $this->search . '%');
            });
        }

        $groupedItems = $query
            ->select(
                'post_procurements.notice_of_award_number',
                DB::raw('COUNT(DISTINCT procurements.procID) as procurement_count'),
                DB::raw('SUM(procurements.abc) as total_abc'),
                DB::raw('MIN(procurements.date_receipt) as earliest_date'),
                DB::raw('MAX(procurements.date_receipt) as latest_date')
            )
            ->groupBy('post_procurements.notice_of_award_number')
            ->orderBy('post_procurements.notice_of_award_number', 'desc')
            ->paginate($this->perPage);

        // If an NOA is expanded, load its procurements
        $expandedProcurements = null;
        if ($this->expandedNoaNumber) {
            $expandedProcurements = Procurement::query()
                ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
                ->where('post_procurements.notice_of_award_number', $this->expandedNoaNumber)
                ->where(function ($q) {
                    $q->whereHas('prLotPrstages', function ($query) {
                        $query->where('pr_stage_id', 7);
                    })
                        ->orWhereHas('prItemPrstages', function ($query) {
                            $query->where('pr_stage_id', 7);
                        });
                })
                ->with([
                    'division',
                    'category',
                    'fundSource',
                    'endUser',
                    'prLotPrstages.procurementStage',
                    'prItemPrstages.stage',
                    'pr_items' => function ($query) {
                        $query->with(['prstage.stage', 'currentItemRemark.remark']);
                    }
                ])
                ->select('procurements.*', 'post_procurements.notice_of_award_number')
                ->get();
        }

        return view('livewire.pmu.pmu-index-page', [
            'groupedItems' => $groupedItems,
            'expandedProcurements' => $expandedProcurements,
        ])->layout('components.layouts.app');
    }
}
