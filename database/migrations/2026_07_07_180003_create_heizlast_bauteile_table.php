<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hüllbauteil eines Raums mit U-Wert-Eingabestrategie (A/B/C) und Quelle/Konfidenz.
 * Transplantat aus wberechnung; create + add_azimut + add_konstruktion_id konsolidiert.
 * konstruktion_id referenziert die bereits in ticket vorhandene Tabelle `konstruktionen`
 * (nullOnDelete). Additiv.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heizlast_bauteile', function (Blueprint $table) {
            $table->id();
            $table->foreignId('heizlast_raum_id')->constrained('heizlast_raeume')->cascadeOnDelete();
            $table->string('typ');                              // wand|dach|decke|boden|fenster|tuer
            $table->string('grenzflaeche')->default('aussen');  // aussen|unbeheizt|erdreich|beheizt
            $table->float('azimut_grad')->nullable();           // 0=N,90=O,180=S,270=W
            $table->decimal('flaeche_m2', 7, 2);
            $table->string('u_strategie')->default('C');        // A bekannt | B Schichten | C Baualter | typ
            $table->decimal('u_wert', 6, 3)->nullable();        // direkt (A) oder aufgelöst
            $table->json('schichten')->nullable();              // Strategie B
            $table->foreignId('konstruktion_id')->nullable()->constrained('konstruktionen')->nullOnDelete();
            $table->string('fenster_typ')->nullable();          // Fenster/Tür-Typ
            $table->string('konfidenz')->nullable();            // hoch|mittel|niedrig
            $table->string('quelle')->nullable();
            $table->unsignedSmallInteger('reihenfolge')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heizlast_bauteile');
    }
};
