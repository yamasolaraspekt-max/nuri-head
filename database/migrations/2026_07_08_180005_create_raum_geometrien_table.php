<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Grundriss-Geometrie eines Heizlast-Raums (Polygon + Wandsegmente + Decke/Boden).
 * Quelle für die Flächenableitung (GeometrieAbleitungService → Bauteile → Heizlast).
 * Mirror aus wberechnung; additiv, 1:1 an heizlast_raeume.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('raum_geometrien', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heizlast_raum_id')->constrained('heizlast_raeume')->cascadeOnDelete();
            $table->integer('geschoss')->default(0);
            $table->json('polygon')->nullable();
            $table->integer('hoehe_mm')->nullable();
            $table->json('wand_segmente')->nullable();
            $table->json('decke')->nullable();
            $table->json('boden')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('raum_geometrien');
    }
};
