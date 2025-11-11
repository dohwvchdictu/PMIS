<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = [
        'divisions',
        'abbreviation',
        'slug',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get all procurements for this division
     */
    public function procurements()
    {
        return $this->hasMany(Procurement::class, 'divisions_id');
    }

    /**
     * Get count of active procurements
     */
    public function activeProcurements()
    {
        return $this->procurements()
            ->whereHas('prLotPrstages', function ($q) {
                $q->whereHas('procurementStage', function ($sq) {
                    $sq->where('procurementstage', 'not like', '%completed%')
                        ->where('procurementstage', 'not like', '%delivered%')
                        ->where('procurementstage', 'not like', '%closed%');
                });
            });
    }

    /**
     * Scope to get only active divisions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
