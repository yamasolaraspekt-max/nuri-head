<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Heizlast-Projekt (Gebäude) als Container der raumweisen DIN-EN-12831-Berechnung.
 * Transplantat aus wberechnung; Spalten gespiegelt (create + spätere add-Migrationen
 * konsolidiert). Additiv, keine bestehende ticket-Tabelle wird berührt.
 *
 * Abweichung ggü. wberechnung: energiekonzept_id ist hier KEIN Fremdschlüssel
 * (nullable unsignedBigInteger ohne constrained), da ticket keine Tabelle
 * `energiekonzepte` besitzt. Rein additiv, kein Ziel-Constraint.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heizlast_projekte', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('standort_plz', 10)->nullable();
            $table->decimal('norm_aussentemp_c', 5, 1)->nullable(); // θe (aus PLZ-Tabelle oder manuell)
            $table->float('gelaendehoehe_m')->nullable();            // Standorthöhe ü. NN für Höhenkorrektur
            $table->smallInteger('baujahr')->nullable();
            $table->string('sanierungsstufe')->default('unsaniert'); // für Strategie C
            $table->string('waermebruecken')->default('pauschal');   // keine|pauschal|pauschal_nachweis
            $table->decimal('komfortzuschlag_k', 4, 1)->default(0);
            $table->boolean('intermittierend')->default(false);
            $table->unsignedBigInteger('energiekonzept_id')->nullable(); // KEIN FK — ticket hat kein energiekonzepte
            $table->json('ergebnis')->nullable();                    // zuletzt berechnetes Ergebnis (Cache/Übergabe)
            $table->decimal('ziel_vorlauf_c', 4, 1)->default(45);
            $table->decimal('spreizung_k', 4, 1)->default(7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heizlast_projekte');
    }
};
