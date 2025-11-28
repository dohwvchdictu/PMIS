<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrLotRemark extends Model
{
    use HasFactory;

    protected $table = 'pr_lot_remark';

    protected $fillable = [
        'procID',
        'remarks_id',
        'notes',
        'remark_history',
    ];

    protected $casts = [
        'remark_history' => 'datetime',
    ];

    public function procurement()
    {
        return $this->belongsTo(Procurement::class, 'procID', 'procID');
    }

    public function remark()
    {
        return $this->belongsTo(Remarks::class, 'remarks_id', 'id');
    }
}
