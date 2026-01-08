<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class MopItem extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'mop_item';

    protected $fillable = [
        'procID',
        'prItemID',
        'uid',
        'mode_of_procurement_id',
        'mode_order',
    ];
    protected $auditInclude = [
        'procID',
        'prItemID',
        'uid',
        'mode_of_procurement_id',
        'mode_order',
    ];
    protected $auditTimestamps = true;

    protected $auditStrict = false;

    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'procID');
    }

    public function item()
    {
        return $this->belongsTo(PrItem::class, 'prItemID');
    }

    public function modeOfProcurement()
    {
        return $this->belongsTo(ModeOfProcurement::class, 'mode_of_procurement_id');
    }
}
