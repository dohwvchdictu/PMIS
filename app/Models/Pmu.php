<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Pmu extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'notice_of_award_number',
        'date_forwarded',
        'contract_amount',
        'po_contract_number',
        'po_contract_number_link',
        'contract_signing_date',
        'notice_to_proceed_date',
        'remarks',
    ];

    protected $auditInclude = [
        'notice_of_award_number',
        'date_forwarded',
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
        'date_forwarded' => 'date',
        'contract_signing_date' => 'date',
        'notice_to_proceed_date' => 'date',
        'contract_amount' => 'decimal:2',
    ];

    public function generateTags(): array
    {
        return ['pmu', $this->notice_of_award_number];
    }

    // Get all procurements with this notice of award
    public function procurements()
    {
        return $this->hasMany(PostProcurement::class, 'notice_of_award_number', 'notice_of_award_number');
    }
}
