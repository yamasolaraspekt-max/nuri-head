<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_phases', function (Blueprint $table) {
            if (!Schema::hasColumn('task_phases', 'lead_stage_id')) {
                $table->unsignedBigInteger('lead_stage_id')->nullable()->index();
            }

            if (!Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
                $table->unsignedBigInteger('lead_sub_stage_id')->nullable()->index();
            }
        });

        Schema::table('phase_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('phase_activities', 'lead_stage_id')) {
                $table->unsignedBigInteger('lead_stage_id')->nullable()->index();
            }

            if (!Schema::hasColumn('phase_activities', 'lead_sub_stage_id')) {
                $table->unsignedBigInteger('lead_sub_stage_id')->nullable()->index();
            }
        });

        Schema::table('master_set_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('master_set_tasks', 'lead_stage_id')) {
                $table->unsignedBigInteger('lead_stage_id')->nullable()->index();
            }

            if (!Schema::hasColumn('master_set_tasks', 'lead_sub_stage_id')) {
                $table->unsignedBigInteger('lead_sub_stage_id')->nullable()->index();
            }
        });

        /*
         * No legacy `stages` table is required here.
         * Existing empty/null workflow rows are assigned to your default LeadStage,
         * only so the Aufgabe picker does not stay empty after the migration.
         */
        $defaultLeadStageId = DB::table('lead_stages')
            ->whereNull('deleted_at')
            ->where('is_active', 1)
            ->orderByDesc('is_default')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->value('id');

        if ($defaultLeadStageId) {
            DB::table('task_phases')
                ->whereNull('lead_stage_id')
                ->update(['lead_stage_id' => $defaultLeadStageId]);

            DB::table('phase_activities')
                ->whereNull('lead_stage_id')
                ->update(['lead_stage_id' => $defaultLeadStageId]);

            DB::table('master_set_tasks')
                ->whereNull('lead_stage_id')
                ->update(['lead_stage_id' => $defaultLeadStageId]);
        }
    }

    public function down(): void
    {
        Schema::table('master_set_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('master_set_tasks', 'lead_sub_stage_id')) {
                $table->dropColumn('lead_sub_stage_id');
            }

            if (Schema::hasColumn('master_set_tasks', 'lead_stage_id')) {
                $table->dropColumn('lead_stage_id');
            }
        });

        Schema::table('phase_activities', function (Blueprint $table) {
            if (Schema::hasColumn('phase_activities', 'lead_sub_stage_id')) {
                $table->dropColumn('lead_sub_stage_id');
            }

            if (Schema::hasColumn('phase_activities', 'lead_stage_id')) {
                $table->dropColumn('lead_stage_id');
            }
        });

        Schema::table('task_phases', function (Blueprint $table) {
            if (Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
                $table->dropColumn('lead_sub_stage_id');
            }

            if (Schema::hasColumn('task_phases', 'lead_stage_id')) {
                $table->dropColumn('lead_stage_id');
            }
        });
    }
};
