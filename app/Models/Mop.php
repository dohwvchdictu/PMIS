<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Mop extends Model
{
    use HasFactory;

    protected $fillable = [
        'mop_group_ref',
        'procurable_type',
        'procurable_id',
        'mode_of_procurement_id',
        'original_mode_of_procurement_id',
        'current_mode_of_procurement_id',
        'mode_order',
        'uid',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($mop) {
            // Step 1: Default original_mode_of_procurement_id
            if (is_null($mop->original_mode_of_procurement_id)) {
                $mop->original_mode_of_procurement_id = $mop->mode_of_procurement_id
                    ?? $mop->current_mode_of_procurement_id;
            }

            // Step 2: Default current_mode_of_procurement_id
            if (is_null($mop->current_mode_of_procurement_id)) {
                $mop->current_mode_of_procurement_id = $mop->mode_of_procurement_id
                    ?? $mop->original_mode_of_procurement_id;
            }

            // Step 3: Generate stable UID (Option B)
            if (empty($mop->uid)) {
                $modeId = $mop->original_mode_of_procurement_id ?? '0';
                $order = $mop->mode_order ?? (static::count() + 1);
                $mop->uid = sprintf('MOP-%s-%s', $modeId, $order);
            }
        });

        static::updating(function ($mop) {
            // Detect mode change
            if ($mop->isDirty('current_mode_of_procurement_id')) {
                $old = $mop->getOriginal('current_mode_of_procurement_id');
                $new = $mop->current_mode_of_procurement_id;

                // Optional: Log to mode change table
                if (class_exists(MopModeChange::class)) {
                    MopModeChange::create([
                        'mop_id' => $mop->id,
                        'old_mode_id' => $old,
                        'new_mode_id' => $new,
                        'changed_by' => auth()->id(),
                    ]);
                }
            }
        });
    }
    protected $casts = [
        'mode_order' => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Polymorphic link to Procurement or PrItem
    public function procurable(): MorphTo
    {
        return $this->morphTo();
    }

    // Mode details
    public function modeDetails(): BelongsTo
    {
        return $this->belongsTo(ModeOfProcurement::class, 'mode_of_procurement_id');
    }

    // Original mode
    public function originalMode(): BelongsTo
    {
        return $this->belongsTo(ModeOfProcurement::class, 'original_mode_of_procurement_id');
    }

    // Current mode
    public function currentMode(): BelongsTo
    {
        return $this->belongsTo(ModeOfProcurement::class, 'current_mode_of_procurement_id');
    }

    public function bidSchedules(): HasMany
    {
        return $this->hasMany(BidSchedule::class, 'mop_uid', 'uid');
    }

    public function ntfBidSchedules(): HasMany
    {
        return $this->hasMany(NtfBidSchedule::class, 'mop_uid', 'uid');
    }

    public function svpDetails(): HasMany
    {
        return $this->hasMany(PrSvp::class, 'mop_uid', 'uid');
    }


}
