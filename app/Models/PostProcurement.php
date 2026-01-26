<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PostProcurement extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'ref_id',
        'resolution_award_number',
        'resolution_award_date',
        'notice_of_award_number',
        'notice_of_award',
        'philgeps_notice_of_award_no',
        'awarded_amount',
        'philgeps_posting_of_award',
        'supplier_id',
    ];

    protected $auditInclude = [
        'ref_id',
        'resolution_award_number',
        'resolution_award_date',
        'notice_of_award_number',
        'notice_of_award',
        'philgeps_notice_of_award_no',
        'awarded_amount',
        'philgeps_posting_of_award',
        'supplier_id',
    ];

    protected $auditTimestamps = true;

    protected $auditStrict = false;

    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'ref_id', 'procID');
    }
}
