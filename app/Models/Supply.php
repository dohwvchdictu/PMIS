<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supply extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_contract_number',
        'date_forwarded',
        'date_received',
        'remarks',
    ];

    protected $casts = [
        'date_forwarded' => 'date',
        'date_received' => 'date',
    ];

    public function details()
    {
        return $this->hasMany(SupplyDetail::class, 'ref_id');
    }

    public function supplyPos()
    {
        return $this->hasMany(\App\Models\SupplyPo::class);
    }
}
