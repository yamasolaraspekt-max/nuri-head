<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * B2a-2 — Anforderungsprofil (Framework Phase 1): das führende, versionierte Bedarfsobjekt der Auslegung.
 * Polymorph verankert (Whitelist auf Modellebene: lead_alternative_adds = Objekt/kanonisch,
 * lead_product_lists = Gewerk/Deal). Append-only versioniert; genau EINE aktive Version je Verankerung
 * (App-Invariante + Test — die DB erzwingt sie nicht, weil der Aktiv-Zustand pro Verankerung gilt).
 *
 * KEIN Nachbau von wb-heizlast_projekte: nur Bedarf + Verankerung + Version; Rechenparameter/Roh-Ergebnis
 * bleiben draußen (Abgleich-Tabelle im B2a-2-Befund). Werte liegen in anforderungsprofil_werte (170006).
 * NUR lokal (Lokal-First; main = Yamas lokaler Hauptstand).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anforderungsprofile', function (Blueprint $table) {
            $table->id();
            $table->morphs('verankerbar'); // verankerbar_type (FQCN) + verankerbar_id (+ Index) — Whitelist im Model
            $table->unsignedInteger('version')->default(1);
            $table->string('status')->default('entwurf'); // entwurf | aktiv | abgeloest
            $table->foreignId('abgeloest_durch_id')->nullable()
                ->constrained('anforderungsprofile')->nullOnDelete(); // Nachfolge-Version
            $table->string('bezeichnung')->nullable(); // z. B. "Bestand" / "nach Sanierung"
            // erfasst_von: Nachbar-Konvention created_by -> employees (5:2 in bestehenden Migrationen;
            // die handelnde Fach-Identität der Auslegung ist der Planer = employee).
            $table->foreignId('created_by')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamps();

            $table->index(['verankerbar_type', 'verankerbar_id', 'status']); // Ein-aktiv-Query je Verankerung
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anforderungsprofile');
    }
};
