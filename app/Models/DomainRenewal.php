<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DomainRenewal extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'start_date',
        'end_date',
        'payment_id',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }
}
