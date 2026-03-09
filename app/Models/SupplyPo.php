<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyPo extends Model
{
    protected $table = 'supply_po';

    protected $fillable = [
        'supply_id',
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
        'date_forwarded_to_budget' => 'datetime',
        'soa_amount' => 'decimal:2',
    ];

    public function supply(): BelongsTo
    {
        return $this->belongsTo(Supply::class);
    }
}
