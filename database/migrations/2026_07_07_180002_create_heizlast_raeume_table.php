<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Raum eines Heizlast-Projekts (Nutzung, Geometrie, Luftwechsel). Transplantat aus
 * wberechnung; create + add_heizkoerper + add_fussbodenheizung konsolidiert. Additiv.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heizlast_raeume', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heizlast_projekt_id')->constrained('heizlast_projekte')->cascadeOnDelete();
            $table->string('name');
            $table->string('nutzung')->default('wohnen'); // → θint
            $table->decimal('theta_int_c', 4, 1)->nullable();
            $table->decimal('grundflaeche_m2', 7, 2);
            $table->decimal('hoehe_m', 4, 2)->default(2.5);
            $table->decimal('luftwechsel_1h', 4, 2)->nullable();
            $table->unsignedSmallInteger('reihenfolge')->default(0);
            $table->json('heizkoerper')->nullable();       // [{heizkoerper_id, laenge_m, anzahl}]
            $table->json('fussbodenheizung')->nullable();  // EN 1264: rohr_aussen_mm, verlegeabstand_mm, ...
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heizlast_raeume');
    }
};
