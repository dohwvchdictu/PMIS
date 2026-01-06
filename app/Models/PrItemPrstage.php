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
    ];

    protected $casts = [
        'stage_history' => 'datetime',
    ];

    protected $auditInclude = [
        'procID',
        'prItemID',
        'pr_stage_id',
        'stage_history',
    ];
    protected $auditTimestamps = true;

    protected $auditStrict = false;

    protected function resolveUser()
    {
        return \App\Models\User::resolveAuditUser();
    }
    public function item()
    {
        return $this->belongsTo(PrItem::class, 'prItemID', 'prItemID');
    }

    public function stage()
    {
        return $this->belongsTo(ProcurementStage::class, 'pr_stage_id', 'id');
    }
}

