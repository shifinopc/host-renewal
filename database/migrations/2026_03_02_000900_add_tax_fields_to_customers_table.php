<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->string('tax_preference', 20)
                ->default('taxable')
                ->after('address');
            $table->string('business_type')->nullable()->after('tax_preference');
            $table->string('gstin')->nullable()->after('business_type');
            $table->string('place_of_supply')->nullable()->after('gstin');
            $table->text('tax_exempt_reason')->nullable()->after('place_of_supply');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'tax_preference',
                'business_type',
                'gstin',
                'place_of_supply',
                'tax_exempt_reason',
            ]);
        });
    }
};

