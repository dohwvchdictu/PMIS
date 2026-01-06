<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class BACApprovedPR extends Model implements Auditable
{
    use HasFactory;
    use \OwenIt\Auditing\Auditable;
    protected $table = 'bac_approved_prs';
    protected $fillable = [
        'procID',
        'filepath',
        'remarks',
    ];
    protected $auditInclude = [
        'procID',
        'filepath',
        'remarks',
    ];
    protected $auditTimestamps = true;

    protected $auditStrict = false;

    protected function resolveUser()
    {
        return \App\Models\User::resolveAuditUser();
    }
    public function getRouteKeyName()
    {
        return 'procID';
    }
    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'procID', 'procID');
    }
}
