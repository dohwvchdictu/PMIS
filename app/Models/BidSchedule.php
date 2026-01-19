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
        'ib_number',
        'philgeps_posting_ref_no',
        'pre_proc_conference',
        'ads_post_ib',
        'list_invited_observers',
        'obsrvr_prebid_conf',
        'obsrvr_eligibility',
        'obsrvr_sub_open_of_bid',
        'obsrvr_bid',
        'obsrvr_post_qual',
        'pre_bid_conf',
        'eligibility_check',
        'sub_open_bids',
        'bid_evaluation_date',
        'post_qualification_date',
        'bidding_number',
        'bidding_date',
        'bidding_result',
        'resolution_number_mop',
    ];

    protected $auditInclude = [
        'ref_id',
        'mop_uid',
        'uid',
        'ib_number',
        'philgeps_posting_ref_no',
        'pre_proc_conference',
        'ads_post_ib',
        'list_invited_observers',
        'obsrvr_prebid_conf',
        'obsrvr_eligibility',
        'obsrvr_sub_open_of_bid',
        'obsrvr_bid',
        'obsrvr_post_qual',
        'pre_bid_conf',
        'eligibility_check',
        'sub_open_bids',
        'bid_evaluation_date',
        'post_qualification_date',
        'bidding_number',
        'bidding_date',
        'bidding_result',
        'resolution_number_mop',
    ];

    protected $auditTimestamps = true;

    protected $auditStrict = false;

    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'ref_id', 'procID');
    }
}
