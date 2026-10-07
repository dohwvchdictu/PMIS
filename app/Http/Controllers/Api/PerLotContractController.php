<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PmuPo;

class PerLotContractController extends Controller
{
    /**
     * List Per Lot procurements with their PO / Contract Number and awarded Supplier.
     *
     * For Per Lot, both pmu_po and post_procurements are keyed on ref_id = procID,
     * mirroring how ProcurementViewPage resolves the PMU and supplier records.
     */
    public function __invoke()
    {
        $contracts = PmuPo::query()
            ->join('procurements', 'procurements.procID', '=', 'pmu_po.ref_id')
            ->join('pmus', 'pmus.id', '=', 'pmu_po.pmu_id')
            ->leftJoin('post_procurements', function ($join) {
                $join->on('post_procurements.ref_id', '=', 'pmu_po.ref_id')
                    ->whereNull('post_procurements.deleted_at');
            })
            ->leftJoin('suppliers', 'suppliers.id', '=', 'post_procurements.supplier_id')
            ->leftJoin('categories', 'categories.id', '=', 'procurements.category_id')
            ->where('procurements.procurement_type', 'perLot')
            ->whereNull('procurements.deleted_at')
            ->whereNull('pmus.deleted_at')
            ->whereNotNull('pmu_po.po_contract_number')
            ->where('pmu_po.po_contract_number', '!=', '')
            ->orderBy('procurements.pr_number')
            ->get([
                'procurements.pr_number',
                'procurements.procurement_program_project',
                'categories.category',
                'pmu_po.po_contract_number',
                'suppliers.name as supplier_name',
            ]);

        return response()->json(['data' => $contracts]);
    }
}
