<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'tin',
        'address',
        'mobile',
        'telephone',
        'email',
        'contact_person',
        'remarks',
        'is_active',
    ];

    public function procurements()
    {
        return $this->hasMany(Procurement::class);
    }

}
