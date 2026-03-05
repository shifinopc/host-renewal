<?php

use App\Models\Domain;
use App\Models\DomainRenewal;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        Domain::whereNotNull('start_date')
            ->whereNotNull('expiry_date')
            ->whereDoesntHave('renewals')
            ->each(function (Domain $domain) {
                DomainRenewal::create([
                    'domain_id' => $domain->id,
                    'start_date' => $domain->start_date,
                    'end_date' => $domain->expiry_date,
                    'payment_id' => null,
                ]);
            });
    }

    public function down(): void
    {
        // No rollback - would need to identify which renewals were created
    }
};
