<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Förderprogramm-Verwaltung (Ersatz für das kaputte BEG-Förderungen-Modul).
 *
 * Übernommen aus dem playground-Modul „foerderungen", angepasst an ticket-Konventionen.
 * KfW-/BAFA-/Landes-/EU-Förderprogramme mit Antragsstatus-Workflow
 * (geplant→beantragt→bewilligt→ausgezahlt/abgelehnt), Frist und Förderbetrag.
 *
 * Eigenständige NEUE Tabelle — die bestehende `beg_fundings` (Lead-Förderrechner)
 * bleibt unberührt. Rein additiv, idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('foerderungen')) {
            return;
        }

        Schema::create('foerderungen', function (Blueprint $table) {
            $table->id();
            $table->string('antragsnummer')->unique();
            $table->string('bezeichnung');
            $table->string('foerdertyp', 30)->default('sonstiger'); // KfW|BAFA|Land|Kommune|EU|sonstiger
            $table->decimal('betrag', 14, 2)->default(0);
            $table->string('antragsstatus', 30)->default('geplant'); // geplant|beantragt|bewilligt|ausgezahlt|abgelehnt
            $table->date('deadline')->nullable();
            $table->string('projekt_ref')->nullable();
            $table->text('notiz')->nullable();
            $table->timestamp('archived_at')->nullable();
            $table->timestamps();

            $table->index('foerdertyp', 'idx_foerderungen_typ');
            $table->index('antragsstatus', 'idx_foerderungen_status');
            $table->index('deadline', 'idx_foerderungen_deadline');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foerderungen');
    }
};
