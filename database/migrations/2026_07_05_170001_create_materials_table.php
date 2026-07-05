<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B2a-1 Referenz-Katalog: Baustoff-/Dämmstoff-λ für die U-Wert-Berechnung (Strategie B, Schichtaufbau).
 * Bemessungswerte DIN 4108-4 / DIN EN ISO 10456. Additiv, lokal-first (main = Yamas lokaler Hauptstand).
 * `verifikations_status` je Zeile wird bis ins Heizlast-Ergebnis durchgereicht (W-B2a-4).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('kategorie')->index()->comment('mauerwerk|beton|holz|putz|estrich|daemmung|sonstiges');
            $table->decimal('lambda_w_mk', 6, 4)->comment('[W/mK] Bemessungswert Wärmeleitfähigkeit');
            $table->unsignedInteger('rohdichte_kg_m3')->nullable()->comment('[kg/m³]');
            $table->string('quelle')->default('DIN 4108-4 / ISO 10456');
            $table->string('verifikations_status')->nullable()->comment('din_belegt | importiert_ungeprueft');
            $table->string('imported_from')->nullable()->comment("Herkunfts-Marker, z.B. 'wberechnung'");
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};
