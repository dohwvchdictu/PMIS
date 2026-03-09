<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplyDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref_id',
        'batch_no',
        'delivery_completion',
        'date_received_from_end_user',
        'soa_amount',
        'date_forwarded_to_budget',
    ];

    protected $casts = [
        'delivery_completion' => 'date',
        'date_received_from_end_user' => 'datetime',
        'soa_amount' => 'decimal:2',
        'date_forwarded_to_budget' => 'datetime',
    ];

    public function supply()
    {
        return $this->belongsTo(Supply::class, 'ref_id');
    }
}
