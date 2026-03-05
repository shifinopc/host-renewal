<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('server_id')->nullable()->constrained()->nullOnDelete();
            $table->string('domain_name');
            $table->string('plan_name')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->date('start_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->enum('status', ['Active', 'Expiring', 'Expired'])->default('Active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('domains');
    }
};

