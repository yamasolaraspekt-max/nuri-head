<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Hausplaner-Transplantation nach ticket — Foundation-Tabellen (Spec: docs/planner-spec-hausplaner-transplantation.md).
 *
 * STRENG ADDITIV (DAUERDIREKTIVE): drei NEUE Tabellen, KEIN Eingriff in Bestandstabellen,
 * keine Kette (Angebot→Auftrag→invoices) berührt. MySQL-Semantik (json, keine ->>-Raw-Queries),
 * bigint-IDs. Portiert aus playground (2026_07_16_200001), EINZIGE Änderung ▲T1: der Anker
 * wechselt von project_id auf alternative_id (Objekt = lead_alternative_adds, kanonische
 * Gebäudeakte) — genau EIN Plan je Objekt (unique). FK auf lead_alternative_adds nur, wenn die
 * Tabelle existiert (defensiv, ▲T2). Kein tenant_id (spätere additive Nachrüstung möglich).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('hausplaner_documents')) {
            return; // idempotent — additiver Schutz gegen Doppel-Migration
        }

        // Aktueller Plan je OBJEKT — genau EINER (alternative_id unique). Szene = einzige Zeichen-Wahrheit.
        Schema::create('hausplaner_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alternative_id');             // ▲T1: Anker = Objekt (lead_alternative_adds)
            $table->unsignedInteger('schema_version')->default(1);
            $table->unsignedBigInteger('revision')->default(1);       // base_revision-Prüfung → 409
            $table->json('scene_json');                               // SceneDocument (mm-Integer)
            $table->string('checksum', 64)->nullable();               // sha256 über kanonisches scene_json
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->unique('alternative_id');
        });

        // FK nur wenn das Objekt-Ziel existiert (defensiv — bricht keine Umgebung ohne die Tabelle).
        if (Schema::hasTable('lead_alternative_adds')) {
            Schema::table('hausplaner_documents', function (Blueprint $table) {
                $table->foreign('alternative_id')
                    ->references('id')->on('lead_alternative_adds')
                    ->cascadeOnDelete();
            });
        }

        // Historische Planstände — append-only (Wiederherstellen = neue Revision am Dokument).
        Schema::create('hausplaner_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hausplaner_document_id')->constrained('hausplaner_documents')->cascadeOnDelete();
            $table->unsignedBigInteger('revision');
            $table->json('scene_json');
            $table->string('label')->nullable();
            $table->string('reason')->nullable();                     // 'manuell' | 'vor_wiederherstellung'
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->index(['hausplaner_document_id', 'revision']);
        });

        // Katalog — NUR Planer-Zusätze; Herstellerdaten leben im Spec-Standard (spec_ref = Naht).
        Schema::create('hausplaner_catalog_items', function (Blueprint $table) {
            $table->id();
            $table->string('category', 40);                           // window | door | radiator | heat_pump | ...
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->json('dimensions');                               // {width,height,depth} in mm
            $table->json('representation');                           // {symbol2d?, model3dUrl?, previewImageUrl?}
            $table->json('placement');                                // {mode, allowRotation, allowScaling}
            $table->string('spec_ref')->nullable();                   // opaker Verweis auf Spec-Standard
            $table->json('technical_data')->nullable();               // NUR planer-spezifisch
            $table->boolean('aktiv')->default(true);
            $table->timestamps();

            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hausplaner_snapshots');
        Schema::dropIfExists('hausplaner_catalog_items');
        Schema::dropIfExists('hausplaner_documents');
    }
};
