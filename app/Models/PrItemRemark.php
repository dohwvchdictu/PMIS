<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class PrItemRemark extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;

    protected $table = 'pr_item_remark';

    protected $fillable = [
        'procID',
        'prItemID',
        'remarks_id',
        'notes',
        'remark_history',
    ];

    protected $casts = [
        'remark_history' => 'datetime',
    ];
    protected $auditInclude = [
        'procID',
        'prItemID',
        'remarks_id',
        'notes',
        'remark_history',
    ];

    protected $auditTimestamps = true;

    protected $auditStrict = false;

    protected function resolveUser()
    {
        return \App\Models\User::resolveAuditUser();
    }
    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'procID', 'procID');
    }

    public function item()
    {
        return $this->belongsTo(PrItem::class, 'prItemID', 'prItemID');
    }

    public function remark()
    {
        return $this->belongsTo(Remarks::class, 'remarks_id', 'id');
    }
}
