<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('task_phases', function (Blueprint $table) {
            if (!Schema::hasColumn('task_phases', 'lead_stage_id')) {
                $table->unsignedBigInteger('lead_stage_id')->nullable()->after('stage_id')->index();
            }

            if (!Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
                $table->unsignedBigInteger('lead_sub_stage_id')->nullable()->after('lead_stage_id')->index();
            }

            if (!Schema::hasColumn('task_phases', 'description')) {
                $table->text('description')->nullable()->after('phase_name');
            }
        });

        Schema::table('phase_activities', function (Blueprint $table) {
            if (!Schema::hasColumn('phase_activities', 'lead_stage_id')) {
                $table->unsignedBigInteger('lead_stage_id')->nullable()->after('section_id')->index();
            }

            if (!Schema::hasColumn('phase_activities', 'lead_sub_stage_id')) {
                $table->unsignedBigInteger('lead_sub_stage_id')->nullable()->after('lead_stage_id')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('phase_activities', function (Blueprint $table) {
            if (Schema::hasColumn('phase_activities', 'lead_sub_stage_id')) {
                $table->dropColumn('lead_sub_stage_id');
            }

            if (Schema::hasColumn('phase_activities', 'lead_stage_id')) {
                $table->dropColumn('lead_stage_id');
            }
        });

        Schema::table('task_phases', function (Blueprint $table) {
            if (Schema::hasColumn('task_phases', 'description')) {
                $table->dropColumn('description');
            }

            if (Schema::hasColumn('task_phases', 'lead_sub_stage_id')) {
                $table->dropColumn('lead_sub_stage_id');
            }

            if (Schema::hasColumn('task_phases', 'lead_stage_id')) {
                $table->dropColumn('lead_stage_id');
            }
        });
    }
};
