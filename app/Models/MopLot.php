<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MopLot extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'mop_lot';

    protected $fillable = [
        'procID',
        'uid',
        'mode_of_procurement_id',
        'mode_order',
    ];

    protected $auditInclude = [
        'procID',
        'uid',
        'mode_of_procurement_id',
        'mode_order',
    ];
    protected $auditTimestamps = true;

    protected $auditStrict = false;

    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'procID', 'procID');
    }

    public function modeOfProcurement()
    {
        return $this->belongsTo(ModeOfProcurement::class, 'mode_of_procurement_id');
    }
    public function bidSchedules()
    {
        return $this->hasMany(BidSchedule::class, 'procID', 'procID');
    }

    public function ntfBidSchedules()
    {

        return $this->hasMany(NtfBidSchedule::class, 'procID', 'procID');
    }

    public function prSvp()
    {
        return $this->hasOne(PrSvp::class, 'procID', 'procID');
    }
}
