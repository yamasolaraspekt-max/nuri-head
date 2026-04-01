<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('offer_product_lists', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('offer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('offer_folder_id')->nullable()->constrained('offer_folders')->cascadeOnDelete();
            $table->foreignId('master_set_id')->nullable()->constrained('product_master_sets')->nullOnDelete();

            // Item basics
            $table->string('name');                  // "VVM500 (Copy): Material"
            $table->string('type')->nullable(); // Material / Lohn / Tools
            $table->string('sku')->nullable();       // "SKU SET-18-MAT"
            $table->text('notes')->nullable();       // textarea Notizen

            // Quantities & prices
            $table->integer('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);  // Einzel €
            $table->decimal('line_net', 12, 2)->default(0);    // Zeile Netto €
            $table->decimal('cost', 12, 2)->default(0);        // Einkaufskosten

            $table->string('measure_unit')->nullable(); // cm, m, mm, stuck

            // Discounts / adjustments
            $table->decimal('discount_pct', 5, 2)->default(0);  // per-line Rabatt in %
            $table->decimal('discount_abs', 12, 2)->default(0); // absoluter Rabatt €

            // VAT and totals
            $table->decimal('vat_pct', 5, 2)->default(19);
            $table->decimal('total_price', 12, 2)->default(0);  // Netto nach Rabatt
            $table->decimal('gross_price', 12, 2)->default(0);  // Brutto inkl. MwSt

            // Position/order of line
            $table->integer('sort_order')->default(0);
            $table->string('image_name');


            // Global settings snapshot (per offer, but stored here for consistency)
            $table->decimal('global_discount_pct', 5, 2)->default(0);
            $table->decimal('global_markup_pct', 5, 2)->default(0);
            $table->decimal('shipping_net_eur', 12, 2)->default(0);
            $table->boolean('apply_global_to_shipping')->default(false);

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_product_lists');
    }
};
