<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Kompatibilität HK-Hersteller/Serie/Baujahr → Ventileinsatz → Kopf-Anschluss (+ optional Adapter).
 * Stammdaten, editierbar. Daten in Stufe (ii) mit Quellenangabe; keine erfundenen Nummern.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('valve_insert_compatibility', function (Blueprint $table) {
            $table->id();
            $table->string('hk_hersteller');
            $table->string('hk_serie')->nullable();
            $table->year('baujahr_von')->nullable();
            $table->year('baujahr_bis')->nullable();
            $table->foreignId('einsatz_accessory_id')->nullable()->constrained('accessories')->nullOnDelete();
            $table->enum('kopf_anschluss_norm', ['M30x1_5', 'RA', 'RAV', 'RAVL', 'sonstige'])->nullable();
            $table->foreignId('adapter_accessory_id')->nullable()->constrained('accessories')->nullOnDelete();
            $table->string('quelle');
            $table->string('note')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('valve_insert_compatibility');
    }
};
