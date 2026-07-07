<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Sanierungs-Szenario zu einem Heizlast-Projekt (IST ↔ Maßnahmen). Arbeitet
 * ausschließlich auf der Variante; echte heizlast_bauteile bleiben unberührt.
 * Transplantat aus wberechnung, Spalten gespiegelt. Additiv.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sanierungs_varianten', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heizlast_projekt_id')->constrained('heizlast_projekte')->cascadeOnDelete();
            $table->string('label');
            $table->boolean('ist_basis')->default(false);
            $table->foreignId('basis_variante_id')->nullable()->constrained('sanierungs_varianten')->nullOnDelete();
            $table->json('massnahmen'); // [{bezeichnung, ziel:{typ,grenzflaeche?}, u_neu?, konstruktion_id?, ...}]
            $table->json('ergebnis_snapshot')->nullable(); // advisory-Cache des letzten Vergleichs
            $table->unsignedSmallInteger('reihenfolge')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sanierungs_varianten');
    }
};
