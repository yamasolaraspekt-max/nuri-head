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
        Schema::create('radiators', function (Blueprint $table) {
            $table->id();
            $table->integer('product_id');
            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            
            // Electric Daten
            $table->integer('dc_nennleistung_kw')->nullable();
            $table->integer('max_dc_leistung_kw')->nullable();
            $table->integer('dc_nennspanuung_v')->nullable();
            $table->integer('max_eingangsspannung_v')->nullable();
            $table->integer('max_eingangsstrom_a')->nullable();
            $table->integer('max_kurzschlussstrom_dc_a')->nullable();
            $table->integer('anzahl_dc_eingange')->nullable();

            //Electric daten AC
            $table->integer('ac_nennleistung_kw')->nullable();
            $table->integer('max_ac_leistung_kva')->nullable();
            $table->integer('ac_nennspannung_v')->nullable();
            $table->integer('anzahl_phasen')->nullable();
            $table->string('trafo')->nullable();

            //Electric Daten - Sonstinge

            $table->integer('anderung')->nullable();
            $table->integer('min_einspeiseleistung_w')->nullable();
            $table->integer('standby_verbruahe_w')->nullable();
            $table->integer('nachtverbrauch_w')->nullable();

            // MPP Tracker


            $table->integer('leistungs_lower_20')->nullable();
            $table->integer('leistungs_greater_20')->nullable();
            $table->integer('parallelbetrieb')->nullable();
            $table->integer('anzahl_mpp_tracker')->nullable();
            $table->string('identisch_electisch')->nullable();
            $table->integer('max_eingangsstrom_mpp_a')->nullable();
            $table->integer('max_kurzschlussstrom_mpp_a')->nullable();
            $table->integer('max_eingangsleistung_mpp_kw')->nullable();
            $table->integer('min_mpp_spannung_v')->nullable();
            $table->integer('max_mpp_spannung_v')->nullable();

   
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('radiators');
    }
};
