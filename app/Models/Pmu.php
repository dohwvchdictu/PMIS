<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Pmu extends Model implements Auditable
{
    use HasFactory, SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'notice_of_award_number',
        'date_forwarded',
    ];

    protected $auditInclude = [
        'notice_of_award_number',
        'date_forwarded',
    ];

    protected $auditTimestamps = true;

    protected $auditStrict = false;

    protected $casts = [
        'date_forwarded' => 'date',
    ];

    public function generateTags(): array
    {
        return ['pmu', $this->notice_of_award_number];
    }

    // Get all procurements with this notice of award
    public function procurements()
    {
        return $this->hasMany(PostProcurement::class, 'notice_of_award_number', 'notice_of_award_number');
    }

    public function pmuPos()
    {
        return $this->hasMany(PmuPo::class, 'ref_id');
    }
}
