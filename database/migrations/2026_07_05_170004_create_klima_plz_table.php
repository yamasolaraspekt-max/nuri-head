<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B2a-1 Klima-Referenz (offline, W-B2a-3): Norm-Außentemperatur + Bezugshöhe je deutscher PLZ
 * (DWD-CDC + IWU-Gewichtung). Speist KlimaPlzService (B2a-3-Port). API-Anreicherung ist ein
 * späterer optionaler Posten. Additiv, lokal-first.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klima_plz', function (Blueprint $table) {
            $table->id();
            $table->string('plz', 5)->index();
            $table->string('ort');
            $table->decimal('lat', 8, 5);
            $table->decimal('lon', 8, 5);
            $table->decimal('nat_c', 5, 2)->comment('[°C] Norm-Außentemperatur (DWD)');
            $table->decimal('temp_mittel_c', 7, 4)->comment('[°C] Jahresmittel');
            $table->integer('hgt15_kd')->nullable()->comment('[Kd] Heizgradtage Basis 15°C');
            $table->decimal('vollbenutz_h', 8, 2)->nullable()->comment('[h] Vollbenutzungsstunden');
            $table->integer('hoehe_m')->nullable()->comment('[m] Bezugshöhe (Höhenkorrektur)');
            $table->string('imported_from')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klima_plz');
    }
};
