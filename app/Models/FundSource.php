<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundSource extends Model
{
    use HasFactory;

    protected $fillable = ['fundsources', 'fund_source_group_id', 'slug', 'is_active'];

    public function fundSourceGroup()
    {
        return $this->belongsTo(FundSourceGroup::class);
    }

    public function procurements()
    {
        return $this->hasMany(Procurement::class);
    }

}
