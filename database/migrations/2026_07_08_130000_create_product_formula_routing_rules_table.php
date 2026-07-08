<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * FS-05 — Smartrouting: deklarative Regeln (Anker*) → Formular. NULL-Anker = Wildcard.
 * Phase-1-Anker (§6): article_group, lead_product_list (Gewerk/Produkt-Zeile), object_type.
 *
 * Rein additiv (neue Tabelle) — kein Bestandseingriff (DAUERDIREKTIVE/G3). Prod-Lauf = Tag-X.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_formula_routing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_formula_id')->constrained('product_formulas')->cascadeOnDelete();

            // Anker (nullable = Wildcard).
            $table->unsignedBigInteger('article_group_id')->nullable()->index();   // article_groups
            $table->unsignedBigInteger('lead_product_list_id')->nullable()->index(); // Gewerk/Produkt-Zeile am Lead
            $table->string('object_type')->nullable()->index();                     // lead_alternative_adds.object_type

            $table->unsignedInteger('priority')->default(100); // höher = bevorzugt bei Gleich-Spezifität
            $table->boolean('is_active')->default(true)->index();
            $table->string('imported_from')->nullable(); // Herkunfts-Marker (FS-06)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_formula_routing_rules');
    }
};
