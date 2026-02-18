<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PrLotPrstage extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'pr_lot_prstage';

    protected $fillable = [
        'procID',
        'pr_stage_id',
        'stage_history',
        'actual_date_forwarded',
    ];
    protected $auditInclude = [
        'procID',
        'pr_stage_id',
        'stage_history',
        'actual_date_forwarded',
    ];

    protected $auditTimestamps = true;

    protected $auditStrict = false;

    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'procID', 'procID');
    }

    // Change this to SINGULAR to match your blade
    public function procurementStage()
    {
        return $this->belongsTo(ProcurementStage::class, 'pr_stage_id', 'id');
    }
}
