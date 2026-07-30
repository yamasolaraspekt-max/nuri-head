<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * AUF-88-P1 / K-02 — der Hausplaner-Projektbezug eines Plan-Uploads.
     *
     * Additiv: eine nullable Fremdschlüssel-Spalte, keine Bestandsdaten berührt
     * (Daten-/Ketten-Schutz, CLAUDE.md). Der „Hausplaner-Projekt"-Begriff aus dem Auftrag
     * ist in diesem Bestand `LeadAlternativeAdd` (dieselbe Wahrheit wie die Route
     * `hausplaner.objekt.*` — `HausplanerController::seite(LeadAlternativeAdd $objekt)`).
     * Kein neues Ablagemodell (nicht_ziel des Auftrags).
     */
    public function up(): void
    {
        Schema::table('plan_uploads', function (Blueprint $table) {
            $table->foreignId('lead_alternative_add_id')->nullable()
                ->after('heizlast_projekt_id')
                ->constrained('lead_alternative_adds')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plan_uploads', function (Blueprint $table) {
            $table->dropConstrainedForeignId('lead_alternative_add_id');
        });
    }
};
