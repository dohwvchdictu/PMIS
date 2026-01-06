<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class BidSchedule extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'ref_id',
        'mop_uid',
        'uid',
        'modeproc',
        'ib_number',
        'pre_proc_conference',
        'ads_post_ib',
        'pre_bid_conf',
        'eligibility_check',
        'sub_open_bids',
        'bidding_number',
        'bidding_date',
        'bidding_result',
    ];

    protected $auditInclude = [
        'ref_id',
        'mop_uid',
        'uid',
        'modeproc',
        'ib_number',
        'pre_proc_conference',
        'ads_post_ib',
        'pre_bid_conf',
        'eligibility_check',
        'sub_open_bids',
        'bidding_number',
        'bidding_date',
        'bidding_result',
    ];
    protected $auditTimestamps = true;

    protected $auditStrict = false;

    protected function resolveUser()
    {
        return \App\Models\User::resolveAuditUser();
    }
    // If procID is related to the Procurement model (as foreign key), you can define a relationship:
    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'ref_id', 'procID');
    }


}
