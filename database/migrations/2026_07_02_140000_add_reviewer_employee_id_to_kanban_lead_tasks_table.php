<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Qualifikations-Fundament B3: Pruefer der Buero-Karte.
 *
 * kanban_lead_tasks.reviewer_employee_id -> employees. Wird beim Monteur-Abschluss
 * gesetzt, wenn die ausfuehrende Person die Mindest-Qualifikation (B1) der Taetigkeit
 * NICHT erfuellt: die Karte geht auf 'reported' (zu pruefen) statt 'done', und der
 * ermittelte Pruefer (naechsthoeher qualifizierter Vorgesetzter) wird hier hinterlegt.
 *
 * nullable = 'reported' ohne Pruefer moeglich (kein Supervisor). FK nullOnDelete,
 * stilkonform zu den bestehenden employee-FKs von kanban_lead_tasks.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kanban_lead_tasks') && !Schema::hasColumn('kanban_lead_tasks', 'reviewer_employee_id')) {
            Schema::table('kanban_lead_tasks', function (Blueprint $table) {
                $table->unsignedBigInteger('reviewer_employee_id')->nullable()->after('done_by_employee_id');
                $table->foreign('reviewer_employee_id')
                      ->references('id')->on('employees')
                      ->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('kanban_lead_tasks') && Schema::hasColumn('kanban_lead_tasks', 'reviewer_employee_id')) {
            Schema::table('kanban_lead_tasks', function (Blueprint $table) {
                $table->dropForeign(['reviewer_employee_id']);
                $table->dropColumn('reviewer_employee_id');
            });
        }
    }
};
