<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            // Monetary fields
            $table->decimal('net_amount', 12, 2)->nullable()->after('price');     // Netto
            $table->decimal('gross_amount', 12, 2)->nullable()->after('net_amount'); // Brutto
            $table->decimal('tax_amount', 12, 2)->nullable()->after('gross_amount'); // Steuer

            // Receipt fields
            $table->date('receipt_date')->nullable()->after('tax_amount');          // Belegdatum
            $table->string('receipt_reference', 191)->nullable()->after('receipt_date'); // Beleg (number/reference)
        });
    }

    public function down(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            $table->dropColumn([
                'net_amount',
                'gross_amount',
                'tax_amount',
                'receipt_date',
                'receipt_reference',
            ]);
        });
    }
};
