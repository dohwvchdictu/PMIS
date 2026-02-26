<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PrItemPrstage extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pr_item_prstage';
    protected $primaryKey = 'id'; // default, unless you named it differently

    protected $fillable = [
        'procID',
        'prItemID',
        'pr_stage_id',
        'stage_history',
        'actual_date_forwarded',
    ];

    protected $auditInclude = [
        'procID',
        'prItemID',
        'pr_stage_id',
        'stage_history',
        'actual_date_forwarded',
    ];
    protected $auditTimestamps = true;

    protected $auditStrict = false;

    public function item()
    {
        return $this->belongsTo(PrItem::class, 'prItemID', 'prItemID');
    }

    public function stage()
    {
        return $this->belongsTo(ProcurementStage::class, 'pr_stage_id', 'id');
    }
}

