<?php

namespace App\Livewire\PMU;

use App\Models\Procurement;
use App\Models\PostProcurement;
use App\Models\Pmu;
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
        // Build a union of:
        //   (A) per-lot: post_procurements.ref_id = procurements.procID  AND  pr_lot_prstage has stage 7
        //   (B) per-item: post_procurements.ref_id = pr_items.prItemID   AND  pr_item_prstage has stage 7

        $lotQuery = \DB::table('procurements')
            ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
            ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
            ->leftJoin('divisions', 'procurements.divisions_id', '=', 'divisions.id')
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('pr_lot_prstage')
                    ->whereColumn('pr_lot_prstage.procID', 'procurements.procID')
                    ->where('pr_lot_prstage.pr_stage_id', 7);
            })
            ->whereNotNull('post_procurements.notice_of_award_number')
            ->whereNull('pmus.deleted_at');

        $itemQuery = \DB::table('procurements')
            ->join('pr_items', 'pr_items.procID', '=', 'procurements.procID')
            ->join('post_procurements', 'post_procurements.ref_id', '=', 'pr_items.prItemID')
            ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
            ->leftJoin('divisions', 'procurements.divisions_id', '=', 'divisions.id')
            ->whereExists(function ($q) {
                $q->select(\DB::raw(1))
                    ->from('pr_item_prstage')
                    ->whereColumn('pr_item_prstage.prItemID', 'pr_items.prItemID')
                    ->where('pr_item_prstage.pr_stage_id', 7);
            })
            ->whereNotNull('post_procurements.notice_of_award_number')
            ->whereNull('pmus.deleted_at');

        // Apply search to both
        if (!empty($this->search)) {
            $lotQuery->where(function ($q) {
                $q->where('procurements.pr_number', 'like', '%' . $this->search . '%')
                    ->orWhere('post_procurements.notice_of_award_number', 'like', '%' . $this->search . '%')
                    ->orWhere('divisions.divisions', 'like', '%' . $this->search . '%');
            });
            $itemQuery->where(function ($q) {
                $q->where('procurements.pr_number', 'like', '%' . $this->search . '%')
                    ->orWhere('post_procurements.notice_of_award_number', 'like', '%' . $this->search . '%')
                    ->orWhere('divisions.divisions', 'like', '%' . $this->search . '%');
            });
        }

        $unionQuery = $lotQuery
            ->select(
                'post_procurements.notice_of_award_number',
                'pmus.date_forwarded',
                'pmus.contract_amount',
                'pmus.po_contract_number',
                'pmus.contract_signing_date',
                'pmus.notice_to_proceed_date',
                'pmus.remarks'
            )
            ->unionAll(
                $itemQuery->select(
                    'post_procurements.notice_of_award_number',
                    'pmus.date_forwarded',
                    'pmus.contract_amount',
                    'pmus.po_contract_number',
                    'pmus.contract_signing_date',
                    'pmus.notice_to_proceed_date',
                    'pmus.remarks'
                )
            );

        $groupedItems = \DB::table(\DB::raw('(' . $unionQuery->toSql() . ') as combined'))
            ->mergeBindings($unionQuery)
            ->select(
                'notice_of_award_number',
                'date_forwarded',
                'contract_amount',
                'po_contract_number',
                'contract_signing_date',
                'notice_to_proceed_date',
                'remarks',
                \DB::raw('COUNT(*) as procurement_count')
            )
            ->groupBy(
                'notice_of_award_number',
                'date_forwarded',
                'contract_amount',
                'po_contract_number',
                'contract_signing_date',
                'notice_to_proceed_date',
                'remarks'
            )
            ->orderBy('date_forwarded', 'desc')
            ->paginate($this->perPage);

        // If an NOA is expanded, load its per-lot and per-item entries separately
        $expandedProcurements = null;
        $expandedItemRows = null;

        if ($this->expandedNoaNumber) {
            // Per-lot procurements
            $expandedProcurements = Procurement::query()
                ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
                ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
                ->where('post_procurements.notice_of_award_number', $this->expandedNoaNumber)
                ->whereNull('pmus.deleted_at')
                ->whereHas('prLotPrstages', function ($q) {
                    $q->where('pr_stage_id', 7);
                })
                ->select('procurements.*', 'post_procurements.notice_of_award_number')
                ->get();

            // Per-item rows: each forwarded pr_item with matching post_procurement
            $expandedItemRows = \DB::table('pr_items')
                ->join('procurements', 'procurements.procID', '=', 'pr_items.procID')
                ->join('post_procurements', 'post_procurements.ref_id', '=', 'pr_items.prItemID')
                ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
                ->whereExists(function ($q) {
                    $q->select(\DB::raw(1))
                        ->from('pr_item_prstage')
                        ->whereColumn('pr_item_prstage.prItemID', 'pr_items.prItemID')
                        ->where('pr_item_prstage.pr_stage_id', 7);
                })
                ->where('post_procurements.notice_of_award_number', $this->expandedNoaNumber)
                ->whereNull('pmus.deleted_at')
                ->select(
                    'procurements.procID',
                    'procurements.pr_number',
                    'pr_items.prItemID',
                    'pr_items.item_no',
                    'pr_items.description',
                    'pr_items.amount'
                )
                ->orderBy('procurements.pr_number')
                ->orderBy('pr_items.item_no')
                ->get();
        }

        return view('livewire.pmu.pmu-index-page', [
            'groupedItems' => $groupedItems,
            'expandedProcurements' => $expandedProcurements,
            'expandedItemRows' => $expandedItemRows,
        ])->layout('components.layouts.app');
    }
}
