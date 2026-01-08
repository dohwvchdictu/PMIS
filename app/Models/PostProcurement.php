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
        'resolution_number',
        'bid_evaluation_date',
        'post_qual_date',
        'recommending_for_award',
        'notice_of_award',
        'philgeps_reference_no',
        'award_notice_no',
        'awarded_amount',
        'date_of_posting_of_award_on_philgeps',
        'supplier_id',
    ];
    protected $auditInclude = [
        'ref_id',
        'resolution_number',
        'bid_evaluation_date',
        'post_qual_date',
        'recommending_for_award',
        'notice_of_award',
        'philgeps_reference_no',
        'award_notice_no',
        'awarded_amount',
        'date_of_posting_of_award_on_philgeps',
        'supplier_id',
    ];
    protected $auditTimestamps = true;

    protected $auditStrict = false;

    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'ref_id', 'procID');
    }

}
