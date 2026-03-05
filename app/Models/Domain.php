<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'server_id',
        'domain_name',
        'plan_name',
        'price',
        'start_date',
        'expiry_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_date' => 'date',
        'expiry_date' => 'date',
        'price' => 'decimal:2',
    ];

    public function getFaviconUrlAttribute(): ?string
    {
        $host = $this->getHostFromDomainName();
        if (! $host) {
            return null;
        }

        return 'https://www.google.com/s2/favicons?domain=' . urlencode($host) . '&sz=32';
    }

    public function getSiteUrlAttribute(): ?string
    {
        $host = $this->getHostFromDomainName();
        if (! $host) {
            return null;
        }

        return 'https://' . $host;
    }

    public function getDaysUntilExpiryAttribute(): ?int
    {
        if (! $this->expiry_date) {
            return null;
        }

        return Carbon::today()->diffInDays($this->expiry_date, false);
    }

    protected function getHostFromDomainName(): ?string
    {
        if (! $this->domain_name) {
            return null;
        }

        $host = preg_replace('#^https?://#', '', trim($this->domain_name));
        $host = strtok($host, '/');

        return $host ?: null;
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function renewals()
    {
        return $this->hasMany(DomainRenewal::class)->orderByDesc('end_date');
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function getExpiryStatusAttribute(): string
    {
        if (! $this->expiry_date) {
            return 'Active';
        }

        $today = Carbon::today();

        if ($this->expiry_date->lt($today)) {
            return 'Expired';
        }

        if ($this->expiry_date->lte($today->copy()->addDays(30))) {
            return 'Expiring';
        }

        return 'Active';
    }

    public function scopeExpiringInDays($query, int $days)
    {
        $today = Carbon::today();

        return $query->whereBetween('expiry_date', [$today, $today->copy()->addDays($days)]);
    }

    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::today());
    }
}

