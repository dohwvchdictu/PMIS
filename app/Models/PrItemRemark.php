<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrItemRemark extends Model
{
    use HasFactory;

    protected $table = 'pr_item_remark';

    protected $fillable = [
        'procID',
        'prItemID',
        'remarks_id',
        'remark_history',
    ];

    protected $casts = [
        'remark_history' => 'datetime',
    ];

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
