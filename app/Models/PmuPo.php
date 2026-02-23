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
        'contract_amount',
        'po_contract_number',
        'po_contract_number_link',
        'contract_signing_date',
        'notice_to_proceed_date',
        'remarks',
    ];

    protected $auditInclude = [
        'pmu_id',
        'ref_id',
        'contract_amount',
        'po_contract_number',
        'po_contract_number_link',
        'contract_signing_date',
        'notice_to_proceed_date',
        'remarks',
    ];

    protected $auditTimestamps = true;

    protected $auditStrict = false;

    protected $casts = [
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
