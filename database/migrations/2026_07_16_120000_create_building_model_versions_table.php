<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * G1b-1 — additive, unveränderliche Ablage abgeleiteter kanonischer Gebäudemodell-Versionen.
 *
 * `building_model_versions` ist eine ABGELEITETE Projektion (derived_only): jede Zeile ist ein Snapshot
 * eines aus der weiterhin kanonischen Quelle `anforderungsprofile.gebaeude_geometrie` projizierten,
 * bereits validierten kanonischen Modell-Payloads (G1a-Schema). Es ist KEINE neue Geometrie-Schreibwahrheit,
 * kein Zweit-Writer, kein Ersatz für `gebaeude_geometrie`. Append-only, unveränderlich (Immutability im Model).
 *
 * Idempotenzschlüssel: (source_profile_id, source_version, schema_version, projection_version) — dieselbe
 * Quellversion unter derselben Schema-/Projektionsversion ergibt genau eine Zeile; ein abweichender Payload
 * unter demselben Schlüssel wird durch das Unique-Constraint (letzte Schutzlinie) abgewiesen.
 *
 * Rein additiv: legt nur diese Tabelle an, verändert keine Bestandstabelle. `down()` entfernt nur diese Tabelle.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('building_model_versions', function (Blueprint $table) {
            // Identität (unveränderliche Revision).
            $table->uuid('id')->primary();                 // surrogater Zeilen-PK (Store-generiert)
            $table->uuid('revision_id');                   // Revisions-ID aus dem kanonischen Payload
            $table->uuid('model_id');
            $table->uuid('parent_revision_id')->nullable();
            $table->unsignedBigInteger('object_id');       // Objektbezug (lead_alternative_add / verankerbar)

            // Source-Nachweis (feste Quelle: gebaeude_geometrie).
            $table->string('source_type', 64)->default('gebaeude_geometrie');
            $table->unsignedBigInteger('source_profile_id'); // anforderungsprofile.id (Quellversion-Zeile)
            $table->unsignedInteger('source_version');       // anforderungsprofile.version
            $table->char('source_hash', 64);                 // Hash der Quellgeometrie
            $table->timestamp('source_captured_at')->nullable();

            // Vertragsnachweis.
            $table->string('schema_version', 32);
            $table->string('projection_version', 32);
            $table->json('payload');                         // vollständiger kanonischer Modell-Payload
            $table->char('payload_hash', 64);                // normalisierter Payload-Hash
            $table->string('validation_status', 32)->default('valid');
            $table->json('warnings')->nullable();
            $table->timestamp('projected_at');

            // Rollenkennzeichnung — festgeschrieben derived_only (kein aktiver fachlicher Writer).
            $table->string('projection_role', 32)->default('derived_only');

            // Nur created_at (append-only, kein updated_at).
            $table->timestamp('created_at')->nullable();

            // Indizes.
            $table->index('revision_id');
            $table->index('model_id');
            $table->index('object_id');

            // Idempotenz-/Eindeutigkeitsschlüssel (letzte Schutzlinie).
            $table->unique(
                ['source_profile_id', 'source_version', 'schema_version', 'projection_version'],
                'bmv_source_schema_projection_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('building_model_versions');
    }
};
