<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B2a-1 Referenz-Katalog: typische Bauteil-U-Werte je Epoche/Sanierungsstufe (Strategie C, Schätzung).
 * Richtwerte IWU-Gebäudetypologie (TABULA-DE) — für Nachweis durch bauteilgenaue Werte ersetzen.
 * W-B2a-4: `verifikations_status` je Zeile (Default 'tabula_richtwert'); Epochen-Stichproben gegen TABULA
 * werden auf 'stichprobe_verifiziert' + Quelle/Datum gehoben. Attribut wird bis ins Ergebnis durchgereicht.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baualtersklassen', function (Blueprint $table) {
            $table->id();
            $table->smallInteger('von_jahr');
            $table->smallInteger('bis_jahr');
            $table->string('sanierungsstufe')->default('unsaniert')->comment('unsaniert|teilsaniert|saniert');
            $table->decimal('u_wand', 5, 3)->comment('[W/m²K]');
            $table->decimal('u_dach', 5, 3)->comment('[W/m²K]');
            $table->decimal('u_boden', 5, 3)->comment('[W/m²K]');
            $table->decimal('u_fenster', 5, 3)->comment('[W/m²K]');
            $table->decimal('u_tuer', 5, 3)->nullable()->comment('[W/m²K]');
            $table->string('quelle')->default('IWU/TABULA (Richtwert)');
            $table->string('verifikations_status')->nullable()->comment('tabula_richtwert | stichprobe_verifiziert | importiert_ungeprueft');
            $table->string('verifikations_quelle')->nullable()->comment('bei Stichprobe: TABULA-Referenz');
            $table->date('verifikations_datum')->nullable();
            $table->string('imported_from')->nullable();
            $table->timestamps();

            $table->index(['von_jahr', 'bis_jahr', 'sanierungsstufe']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baualtersklassen');
    }
};
