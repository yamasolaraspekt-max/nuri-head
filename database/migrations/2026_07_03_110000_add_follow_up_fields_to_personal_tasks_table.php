<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Follow-up F1 — Traeger-Erweiterung auf personal_tasks (additiv, kein Verhaltenswechsel).
 *
 * personal_tasks ist der bestaetigte Follow-up-Traeger (Termin-Muster verallgemeinern):
 * naechster Schritt (personal_task_keys.task), Zuweisung (employees_personal_tasks-Pivot),
 * Faelligkeit (due_date/reminder_date), /home-Sichtbarkeit (Focus-Today-Widget via Pivot)
 * bestehen bereits. F1 ergaenzt nur die Follow-up-MARKER + Herkunft:
 *
 * - follow_up_art  nachfass | wiederaufnahme (App-validiert). "vollstaendig erledigt" = KEINE
 *                  Zeile, daher genuegt art; kein separates outcome-Feld.
 * - source_type    Herkunft, feste Liste: customer_note | kanban_lead_task | customer_report
 *                  | lead_product_list | appointment_report.
 * - source_id      id in der jeweiligen Quell-Tabelle.
 *
 * Konvention (KEINE Migration): type='follow_up' markiert Follow-up-Zeilen (bestehende Spalte,
 * live leer, Default 'personal_task' unberuehrt) -> eigene "Meine Follow-ups"-Sektion.
 *
 * Stufe-1 dashboard-only (kein Mail/Push): Follow-ups setzen bei der Erzeugung (F2)
 * next_reminder_at NICHT -> ProcessPersonalTaskScheduler ueberspringt sie. (F1 = nur Spalten.)
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('personal_tasks')) {
            return;
        }

        Schema::table('personal_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('personal_tasks', 'follow_up_art')) {
                $table->string('follow_up_art', 20)->nullable()->after('type');
            }
            if (!Schema::hasColumn('personal_tasks', 'source_type')) {
                $table->string('source_type', 40)->nullable()->after('follow_up_art');
            }
            if (!Schema::hasColumn('personal_tasks', 'source_id')) {
                $table->unsignedBigInteger('source_id')->nullable()->after('source_type');
            }
        });

        // Index separat (idempotent-safe): nur anlegen, wenn beide Spalten frisch da sind.
        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->index(['source_type', 'source_id'], 'personal_tasks_source_index');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('personal_tasks')) {
            return;
        }

        Schema::table('personal_tasks', function (Blueprint $table) {
            $table->dropIndex('personal_tasks_source_index');
        });

        Schema::table('personal_tasks', function (Blueprint $table) {
            foreach (['follow_up_art', 'source_type', 'source_id'] as $col) {
                if (Schema::hasColumn('personal_tasks', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
