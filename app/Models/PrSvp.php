<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class PrSvp extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;
    protected $fillable = [
        'ref_id',
        'mop_uid',
        'uid',
        'resolution_number',
        'rfq_no',
        'canvass_date',
        'date_returned_of_canvass',
        'abstract_of_canvass_date',
    ];

    protected $auditInclude = [
        'ref_id',
        'mop_uid',
        'uid',
        'resolution_number',
        'rfq_no',
        'canvass_date',
        'date_returned_of_canvass',
        'abstract_of_canvass_date',
    ];
    protected $auditTimestamps = true;

    protected $auditStrict = false;

    protected function resolveUser()
    {
        return \App\Models\User::resolveAuditUser();
    }
    // ✅ Relationships
    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'ref_id', 'procID');
    }


}
