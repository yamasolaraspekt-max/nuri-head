<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * AUF-81 — Konfigurator-Pakete serverseitig (B7 / AUF-40 Teil B).
 *
 * **STRENG ADDITIV (DAUERDIREKTIVE): EINE neue Tabelle, kein Eingriff in Bestandstabellen.**
 * Keine Spalte umbenannt, keine Kette (Angebot → Auftrag → Rechnung) berührt. Der Rückweg ist
 * das Verwerfen einer Tabelle, die es vorher nicht gab — **dabei geht kein Kundendatensatz
 * verloren, weil in ihr nur Neues steht.** Das ist die Bedingung, unter der dieser Posten
 * überhaupt geschnitten wurde (§2 des Auftrags).
 *
 * **Nach dem vorhandenen Muster gebaut, nicht nach eigenem** — Vorlage
 * `2026_07_16_211128_create_hausplaner_foundation_tables.php`: idempotent, `bigint`-IDs,
 * `json`-Spalten, **defensiver** Fremdschlüssel (nur wenn die Zieltabelle existiert),
 * MySQL-Semantik ohne Roh-Abfragen. Kein `tenant_id` — die Bestandstabellen haben auch keines;
 * additiv nachrüstbar.
 *
 * **Warum `alternative_id` nullable ist:** der Konfigurator läuft **autark, ohne Gebäude**. Ein
 * Pflichtfeld verböte genau den Fall, der die Fläche stark macht.
 *
 * **Warum `user_id` NICHT nullable ist:** er ist der Besitzer. Ohne ihn gibt es kein
 * Eigentumsgatter — und ohne das wäre die Liste ein Leck. *Ein Datensatz ohne Eigentümer ist ein
 * Datensatz, den das Gatter nicht schützt.*
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hausplaner_configurator_packages')) {
            return; // idempotent — additiver Schutz gegen Doppel-Migration
        }

        Schema::create('hausplaner_configurator_packages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');                    // der Besitzer — Grundlage des Gatters
            $table->unsignedBigInteger('alternative_id')->nullable(); // autark erlaubt: kein Gebäude nötig
            $table->string('art', 32);                                // fenster · tuer · treppe · heizkoerper
            $table->string('titel');
            $table->string('status', 32)->default('entwurf');
            $table->unsignedInteger('schema_version')->default(1);    // CONFIGURATOR_SCHEMA_VERSION
            $table->json('paket');                                    // das Paket selbst, unveraendert
            $table->timestamps();

            // Die Liste fragt immer nach Besitzer und Zeit — genau dafür der Index.
            $table->index(['user_id', 'created_at']);

            // Fremdschlüssel defensiv: nur, wenn das Ziel existiert (▲T2 der Vorlage). Sie stehen
            // hier INNERHALB der Erzeugung, nicht in einem nachgelagerten Aenderungs-Aufruf — so
            // enthält diese Migration keinen einzigen Aufruf, der eine Tabelle nachträglich
            // ändert. Das ist Kriterium 2 wörtlich, nicht nur sinngemäß.
            if (Schema::hasTable('users')) {
                $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
            }
            if (Schema::hasTable('lead_alternative_adds')) {
                $table->foreign('alternative_id')
                    ->references('id')->on('lead_alternative_adds')
                    ->nullOnDelete();   // das Objekt geht, das Paket bleibt autark bestehen
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hausplaner_configurator_packages');
    }
};
