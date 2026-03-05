<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PmuPo extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pmu_po';

    protected $fillable = [
        'pmu_id',
        'ref_id',
        'po_date_deadline',
        'po_date',
        'date_coa_stamped_received',
        'date_po_receipt_by_supplier',
        'contract_amount',
        'po_contract_number',
        'po_contract_number_link',
        'ntp_link',
        'contract_signing_date',
        'notice_to_proceed_date',
        'remarks',
        'manual_status',
    ];

    protected $auditInclude = [
        'pmu_id',
        'ref_id',
        'po_date_deadline',
        'po_date',
        'date_coa_stamped_received',
        'date_po_receipt_by_supplier',
        'contract_amount',
        'po_contract_number',
        'po_contract_number_link',
        'ntp_link',
        'contract_signing_date',
        'notice_to_proceed_date',
        'remarks',
        'manual_status',
    ];

    protected $auditTimestamps = true;

    protected $auditStrict = false;

    protected $casts = [
        'po_date_deadline' => 'date',
        'po_date' => 'date',
        'date_coa_stamped_received' => 'date',
        'date_po_receipt_by_supplier' => 'date',
        'contract_signing_date' => 'date',
        'notice_to_proceed_date' => 'date',
        'contract_amount' => 'decimal:2',
    ];

    public function generateTags(): array
    {
        return ['pmu_po', $this->ref_id];
    }

    public function pmu()
    {
        return $this->belongsTo(Pmu::class, 'pmu_id');
    }

    public function procurement()
    {
        return $this->belongsTo(\App\Models\Procurement::class, 'ref_id', 'procID');
    }
}
