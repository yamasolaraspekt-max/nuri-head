<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Rueckfluss Stufe 1b: Link von einem planner_item (source_type='phase_activity')
 * auf die zugehoerige Buero-Karte (kanban_lead_tasks).
 *
 * Bewusst OHNE Fremdschluessel (lose Kopplung): der Sync haelt den Link frisch
 * (setzt/nullt ihn je nach lebender Karte), verwaiste ids sind harmlos. Additiv,
 * Nuriva liest die Spalte nicht (my-work/itemPayload geben sie nicht aus).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('planner_items') && !Schema::hasColumn('planner_items', 'kanban_lead_task_id')) {
            Schema::table('planner_items', function (Blueprint $table) {
                $table->unsignedBigInteger('kanban_lead_task_id')->nullable()->after('source_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('planner_items') && Schema::hasColumn('planner_items', 'kanban_lead_task_id')) {
            Schema::table('planner_items', function (Blueprint $table) {
                $table->dropColumn('kanban_lead_task_id'); // Index faellt mit der Spalte weg
            });
        }
    }
};
