<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'address',
        'tax_preference',
        'business_type',
        'gstin',
        'place_of_supply',
        'tax_exempt_reason',
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }
}

