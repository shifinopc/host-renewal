<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'amount',
        'payment_date',
        'method',
        'reference_no',
        'invoice_number',
        'type',
        'status',
        'is_taxable',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'is_taxable' => 'boolean',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

    public function domainRenewal()
    {
        return $this->hasOne(DomainRenewal::class);
    }

    public function getInvoiceNumberAttribute(): string
    {
        $stored = $this->attributes['invoice_number'] ?? null;
        if (! empty($stored)) {
            return $stored;
        }

        $length = (int) (Setting::get('invoice_number_length', '6') ?? 6);
        $prefix = Setting::get('invoice_prefix', 'INV-');

        return $prefix . str_pad((string) $this->id, $length, '0', STR_PAD_LEFT);
    }

    public static function generateNextInvoiceNumber(): string
    {
        $prefix = Setting::get('invoice_prefix', 'INV-');
        $length = (int) (Setting::get('invoice_number_length', '6') ?? 6);
        $next = (int) (Setting::get('invoice_next_number', '1') ?? 1);

        $number = $prefix . str_pad((string) $next, $length, '0', STR_PAD_LEFT);

        Setting::set('invoice_next_number', (string) ($next + 1));

        return $number;
    }
}

