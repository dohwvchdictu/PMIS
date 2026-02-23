<?php

namespace App\Livewire\PMU;

use App\Models\Procurement;
use App\Models\PostProcurement;
use App\Models\Pmu;
use App\Models\Supplier;
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

    // Expanded row pagination
    public $expandedPage = 1;
    public $expandedPerPage = 10;

    // Modal linked-PRs pagination
    public $modalPage = 1;
    public $modalPerPage = 10;

    // Search
    public $search = '';

    // Collapsible functionality
    public $expandedNoaNumber = null;

    // View Modal
    public bool $showViewModal = false;
    public ?string $viewNoaNumber = null;
    public $viewPmuRecord = null;
    public $viewPostProcurement = null;
    public $viewProcurements = null;
    public $viewItemRows = null;
    public float $viewTotalAbc = 0;
    public $viewSupplier = null;

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

    public function setModalPage(int $page): void
    {
        $this->modalPage = $page;
    }

    public function updatingModalPerPage(): void
    {
        $this->modalPage = 1;
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
                    ->orWhere('procurements.procurement_program_project', 'like', '%' . $this->search . '%');
            });
            $itemQuery->where(function ($q) {
                $q->where('procurements.pr_number', 'like', '%' . $this->search . '%')
                    ->orWhere('post_procurements.notice_of_award_number', 'like', '%' . $this->search . '%')
                    ->orWhere('pr_items.description', 'like', '%' . $this->search . '%');
            });
        }

        $unionQuery = $lotQuery
            ->select(
                'post_procurements.notice_of_award_number',
                'pmus.date_forwarded',
                'post_procurements.notice_of_award'
            )
            ->unionAll(
                $itemQuery->select(
                    'post_procurements.notice_of_award_number',
                    'pmus.date_forwarded',
                    'post_procurements.notice_of_award'
                )
            );

        $groupedItems = \DB::table(\DB::raw('(' . $unionQuery->toSql() . ') as combined'))
            ->mergeBindings($unionQuery)
            ->select(
                'notice_of_award_number',
                'date_forwarded',
                'notice_of_award',
                \DB::raw('COUNT(*) as procurement_count')
            )
            ->groupBy(
                'notice_of_award_number',
                'date_forwarded',
                'notice_of_award'
            )
            ->orderBy('date_forwarded', 'desc')
            ->paginate($this->perPage);

        // If an NOA is expanded, load a unified paginated list (per-lot + per-item)
        $expandedPaginator = null;

        if ($this->expandedNoaNumber) {
            $expandedPmu = Pmu::where('notice_of_award_number', $this->expandedNoaNumber)->first();
            $expandedPmuId = $expandedPmu?->id;

            // Per-lot procurements
            $lots = \DB::table('procurements')
                ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
                ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
                ->leftJoin('pmu_po', function ($join) use ($expandedPmuId) {
                    $join->on('pmu_po.ref_id', '=', 'procurements.procID')
                        ->where('pmu_po.pmu_id', '=', $expandedPmuId)
                        ->whereNull('pmu_po.deleted_at');
                })
                ->where('post_procurements.notice_of_award_number', $this->expandedNoaNumber)
                ->whereNull('pmus.deleted_at')
                ->whereExists(function ($q) {
                    $q->select(\DB::raw(1))
                        ->from('pr_lot_prstage')
                        ->whereColumn('pr_lot_prstage.procID', 'procurements.procID')
                        ->where('pr_lot_prstage.pr_stage_id', 7);
                })
                ->select(
                    'procurements.procID',
                    'procurements.pr_number',
                    \DB::raw('procurements.procurement_program_project as description'),
                    'procurements.abc',
                    \DB::raw("'lot' as row_type"),
                    'post_procurements.resolution_award_number',
                    'post_procurements.resolution_award_date',
                    'post_procurements.awarded_amount',
                    'post_procurements.date_receipt_of_supplier_noa',
                    'suppliers.name as supplier_name',
                    'pmu_po.po_date',
                    'pmu_po.po_contract_number',
                    'pmu_po.po_contract_number_link',
                    'pmu_po.contract_amount as pmu_contract_amount',
                    'pmu_po.contract_signing_date as pmu_contract_signing_date',
                    'pmu_po.notice_to_proceed_date as pmu_notice_to_proceed_date',
                    'pmu_po.remarks as pmu_remarks'
                )
                ->get();

            // Per-item rows
            $items = \DB::table('pr_items')
                ->join('procurements', 'procurements.procID', '=', 'pr_items.procID')
                ->join('post_procurements', 'post_procurements.ref_id', '=', 'pr_items.prItemID')
                ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
                ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
                ->leftJoin('pmu_po', function ($join) use ($expandedPmuId) {
                    $join->on('pmu_po.ref_id', '=', 'pr_items.prItemID')
                        ->where('pmu_po.pmu_id', '=', $expandedPmuId)
                        ->whereNull('pmu_po.deleted_at');
                })
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
                    'pr_items.description',
                    \DB::raw('pr_items.amount as abc'),
                    \DB::raw("'item' as row_type"),
                    'post_procurements.resolution_award_number',
                    'post_procurements.resolution_award_date',
                    'post_procurements.awarded_amount',
                    'post_procurements.date_receipt_of_supplier_noa',
                    'suppliers.name as supplier_name',
                    'pmu_po.po_date',
                    'pmu_po.po_contract_number',
                    'pmu_po.po_contract_number_link',
                    'pmu_po.contract_amount as pmu_contract_amount',
                    'pmu_po.contract_signing_date as pmu_contract_signing_date',
                    'pmu_po.notice_to_proceed_date as pmu_notice_to_proceed_date',
                    'pmu_po.remarks as pmu_remarks'
                )
                ->orderBy('procurements.pr_number')
                ->orderBy('pr_items.item_no')
                ->get();

            $combined = $lots->merge($items);
            $total = $combined->count();
            $perPage = max(1, (int) $this->expandedPerPage);
            $page = max(1, (int) $this->expandedPage);
            $sliced = $combined->forPage($page, $perPage)->values();

            $expandedPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                $sliced,
                $total,
                $perPage,
                $page,
                ['pageName' => 'expanded_page']
            );
        }

        return view('livewire.pmu.pmu-index-page', [
            'groupedItems' => $groupedItems,
            'expandedPaginator' => $expandedPaginator,
            'modalPaginator' => $this->buildModalPaginator(),
        ])->layout('components.layouts.app');
    }

    private function buildModalPaginator(): ?\Illuminate\Pagination\LengthAwarePaginator
    {
        if (!$this->showViewModal) {
            return null;
        }

        $lots = collect($this->viewProcurements ?? [])->map(fn($p) => (object) [
            'procID' => $p->procID,
            'pr_number' => $p->pr_number,
            'description' => $p->procurement_program_project,
            'abc' => $p->abc,
            'resolution_award_number' => $p->resolution_award_number ?? null,
            'resolution_award_date' => $p->resolution_award_date ?? null,
            'awarded_amount' => $p->awarded_amount ?? null,
            'date_receipt_of_supplier_noa' => $p->date_receipt_of_supplier_noa ?? null,
            'supplier_name' => $p->supplier_name ?? null,
            'po_date' => $p->po_date ?? null,
            'po_contract_number' => $p->po_contract_number ?? null,
            'po_contract_number_link' => $p->po_contract_number_link ?? null,
            'contract_amount' => $p->pmu_contract_amount ?? null,
            'contract_signing_date' => $p->pmu_contract_signing_date ?? null,
            'notice_to_proceed_date' => $p->pmu_notice_to_proceed_date ?? null,
            'remarks' => $p->pmu_remarks ?? null,
        ]);

        $items = collect($this->viewItemRows ?? [])->map(fn($r) => (object) [
            'procID' => $r->procID,
            'pr_number' => $r->pr_number,
            'description' => $r->description,
            'abc' => $r->amount,
            'resolution_award_number' => $r->resolution_award_number ?? null,
            'resolution_award_date' => $r->resolution_award_date ?? null,
            'awarded_amount' => $r->awarded_amount ?? null,
            'date_receipt_of_supplier_noa' => $r->date_receipt_of_supplier_noa ?? null,
            'supplier_name' => $r->supplier_name ?? null,
            'po_date' => $r->po_date ?? null,
            'po_contract_number' => $r->po_contract_number ?? null,
            'po_contract_number_link' => $r->po_contract_number_link ?? null,
            'contract_amount' => $r->pmu_contract_amount ?? null,
            'contract_signing_date' => $r->pmu_contract_signing_date ?? null,
            'notice_to_proceed_date' => $r->pmu_notice_to_proceed_date ?? null,
            'remarks' => $r->pmu_remarks ?? null,
        ]);

        $combined = $lots->merge($items);
        $total = $combined->count();
        $perPage = max(1, (int) $this->modalPerPage);
        $page = max(1, (int) $this->modalPage);

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $combined->forPage($page, $perPage)->values(),
            $total,
            $perPage,
            $page,
            ['pageName' => 'modal_page']
        );
    }

    public function openViewModal(string $noaNumber): void
    {
        $this->viewNoaNumber = $noaNumber;

        $this->viewPmuRecord = Pmu::where('notice_of_award_number', $noaNumber)
            ->whereNull('deleted_at')
            ->first();

        $this->viewPostProcurement = PostProcurement::where('notice_of_award_number', $noaNumber)->first();

        $this->viewProcurements = Procurement::query()
            ->join('post_procurements', 'procurements.procID', '=', 'post_procurements.ref_id')
            ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->leftJoin('pmu_po', function ($join) {
                $join->on('pmu_po.ref_id', '=', 'procurements.procID')
                    ->on('pmu_po.pmu_id', '=', 'pmus.id')
                    ->whereNull('pmu_po.deleted_at');
            })
            ->where('post_procurements.notice_of_award_number', $noaNumber)
            ->whereNull('pmus.deleted_at')
            ->whereHas('prLotPrstages', fn($q) => $q->where('pr_stage_id', 7))
            ->select(
                'procurements.procID',
                'procurements.pr_number',
                'procurements.procurement_program_project',
                'procurements.abc',
                'post_procurements.resolution_award_number',
                'post_procurements.resolution_award_date',
                'post_procurements.awarded_amount',
                'post_procurements.date_receipt_of_supplier_noa',
                'suppliers.name as supplier_name',
                'pmu_po.po_date',
                'pmu_po.po_contract_number',
                'pmu_po.po_contract_number_link',
                'pmu_po.contract_amount as pmu_contract_amount',
                'pmu_po.contract_signing_date as pmu_contract_signing_date',
                'pmu_po.notice_to_proceed_date as pmu_notice_to_proceed_date',
                'pmu_po.remarks as pmu_remarks'
            )
            ->get();

        $this->viewItemRows = DB::table('pr_items')
            ->join('procurements', 'procurements.procID', '=', 'pr_items.procID')
            ->join('post_procurements', 'post_procurements.ref_id', '=', 'pr_items.prItemID')
            ->join('pmus', 'post_procurements.notice_of_award_number', '=', 'pmus.notice_of_award_number')
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->leftJoin('pmu_po', function ($join) {
                $join->on('pmu_po.ref_id', '=', 'pr_items.prItemID')
                    ->on('pmu_po.pmu_id', '=', 'pmus.id')
                    ->whereNull('pmu_po.deleted_at');
            })
            ->whereExists(function ($q) {
                $q->select(DB::raw(1))
                    ->from('pr_item_prstage')
                    ->whereColumn('pr_item_prstage.prItemID', 'pr_items.prItemID')
                    ->where('pr_item_prstage.pr_stage_id', 7);
            })
            ->where('post_procurements.notice_of_award_number', $noaNumber)
            ->whereNull('pmus.deleted_at')
            ->select(
                'procurements.procID',
                'procurements.pr_number',
                'pr_items.prItemID',
                'pr_items.item_no',
                'pr_items.description',
                'pr_items.amount',
                'post_procurements.resolution_award_number',
                'post_procurements.resolution_award_date',
                'post_procurements.awarded_amount',
                'post_procurements.date_receipt_of_supplier_noa',
                'suppliers.name as supplier_name',
                'pmu_po.po_date',
                'pmu_po.po_contract_number',
                'pmu_po.po_contract_number_link',
                'pmu_po.contract_amount as pmu_contract_amount',
                'pmu_po.contract_signing_date as pmu_contract_signing_date',
                'pmu_po.notice_to_proceed_date as pmu_notice_to_proceed_date',
                'pmu_po.remarks as pmu_remarks'
            )
            ->orderBy('procurements.pr_number')
            ->orderBy('pr_items.item_no')
            ->get();

        $this->viewTotalAbc = (float) $this->viewProcurements->sum('abc');

        if ($this->viewPostProcurement?->supplier_id) {
            $this->viewSupplier = Supplier::find($this->viewPostProcurement->supplier_id);
        } else {
            $this->viewSupplier = null;
        }

        $this->modalPage = 1;
        $this->showViewModal = true;
    }

    public function closeViewModal(): void
    {
        $this->showViewModal = false;
        $this->viewNoaNumber = null;
        $this->viewPmuRecord = null;
        $this->viewPostProcurement = null;
        $this->viewProcurements = null;
        $this->viewItemRows = null;
        $this->viewTotalAbc = 0;
        $this->viewSupplier = null;
        $this->modalPage = 1;
    }
}
