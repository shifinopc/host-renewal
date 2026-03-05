<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->index('customer_id');
            $table->index('server_id');
            $table->index('expiry_date');
            $table->unique(['customer_id', 'domain_name']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index('domain_id');
            $table->index('payment_date');
        });

        Schema::table('reminders', function (Blueprint $table) {
            $table->index(['domain_id', 'reminder_type']);
            $table->index('sent_at');
        });
    }

    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropUnique(['customer_id', 'domain_name']);
            $table->dropIndex(['domains_customer_id_index']);
            $table->dropIndex(['domains_server_id_index']);
            $table->dropIndex(['domains_expiry_date_index']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payments_domain_id_index']);
            $table->dropIndex(['payments_payment_date_index']);
        });

        Schema::table('reminders', function (Blueprint $table) {
            $table->dropIndex(['reminders_domain_id_reminder_type_index']);
            $table->dropIndex(['reminders_sent_at_index']);
        });
    }
};

