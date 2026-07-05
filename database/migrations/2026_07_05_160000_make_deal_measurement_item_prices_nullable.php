<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * S-3: unit_price/purchase_price/total_price auf deal_measurement_items nullable machen,
 * damit „kein Preis" ehrlich als NULL gespeichert wird (statt 0,00 = falscher Wert) — Motivation
 * aus M4-a v-c-2 (HK-Regel-Kandidaten ohne SKU/Preis). testing; main = M5-Fenster.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deal_measurement_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 4)->nullable()->default(null)->change();
            $table->decimal('purchase_price', 15, 4)->nullable()->default(null)->change();
            $table->decimal('total_price', 15, 4)->nullable()->default(null)->change();
        });
    }

    /**
     * ACHTUNG: nur waisenfrei sicher — bestehende NULL-Preise würden den NOT-NULL-Wechsel brechen.
     * Vor Rollback sicherstellen, dass keine NULL-Preise existieren
     * (z. B. UPDATE deal_measurement_items SET unit_price=0 WHERE unit_price IS NULL; analog für die anderen).
     */
    public function down(): void
    {
        Schema::table('deal_measurement_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 4)->default(0)->change();
            $table->decimal('purchase_price', 15, 4)->default(0)->change();
            $table->decimal('total_price', 15, 4)->default(0)->change();
        });
    }
};
