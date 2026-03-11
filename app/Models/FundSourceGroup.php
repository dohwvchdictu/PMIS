<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FundSourceGroup extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'is_active'];

    public function fundSources()
    {
        return $this->hasMany(FundSource::class);
    }
}
