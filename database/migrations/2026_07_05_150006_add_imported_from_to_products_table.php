<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Cut-over Stufe 2: Herkunfts-Marker für importierte Katalog-Zeilen (additiv, nullable).
 * 'wberechnung' = via WberechnungImportSeeder eingefroren; null = Bestand/manuell.
 * Trägt zeilengenaues, reversibles Rollback (WberechnungImportTeardownSeeder) — auch bei
 * Marken, die schon existieren (z.B. LONGi mit anderen Serien), ohne Bestand zu berühren.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('imported_from')->nullable()->after('status')
                ->comment("Herkunfts-Marker importierter Katalog-Zeilen, z.B. 'wberechnung'; null = Bestand/manuell");
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('imported_from');
        });
    }
};
