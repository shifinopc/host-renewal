<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Server extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'provider',
        'ip_address',
        'type',
        'notes',
    ];

    public function domains()
    {
        return $this->hasMany(Domain::class);
    }
}

