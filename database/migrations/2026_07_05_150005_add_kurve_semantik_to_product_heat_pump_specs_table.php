<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cut-over Stufe 2: kurve_semantik als Semantik-Marker der WP-Leistungsdaten (additiv, nullable).
 * Spiegelt den wberechnung-Stand b4a9eda: leistungskurve=null + kurve_semantik='en14511_nenn'
 * => WpKennlinieService rechnet über den Spalten-Fallback (datenblatt-verifizierte Nennwerte).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_heat_pump_specs', function (Blueprint $table) {
            $table->string('kurve_semantik')->nullable()->after('leistungskurve')
                ->comment("Semantik der Leistungsdaten: 'en14511_nenn' (Spalten-Fallback) | 'volllast_max' | null");
        });
    }

    public function down(): void
    {
        Schema::table('product_heat_pump_specs', function (Blueprint $table) {
            $table->dropColumn('kurve_semantik');
        });
    }
};
