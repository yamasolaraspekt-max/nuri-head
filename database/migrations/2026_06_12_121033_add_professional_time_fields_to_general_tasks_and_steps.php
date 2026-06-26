<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('general_tasks', 'planned_minutes')) {
                $table->unsignedInteger('planned_minutes')->default(0)->after('progress_percent');
            }
            if (!Schema::hasColumn('general_tasks', 'actual_minutes')) {
                $table->unsignedInteger('actual_minutes')->default(0)->after('planned_minutes');
            }
        });

        Schema::table('general_task_steps', function (Blueprint $table) {
            if (!Schema::hasColumn('general_task_steps', 'planned_minutes')) {
                $table->unsignedInteger('planned_minutes')->default(0)->after('sort_order');
            }
            if (!Schema::hasColumn('general_task_steps', 'actual_minutes')) {
                $table->unsignedInteger('actual_minutes')->default(0)->after('planned_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_task_steps', function (Blueprint $table) {
            if (Schema::hasColumn('general_task_steps', 'actual_minutes')) {
                $table->dropColumn('actual_minutes');
            }
            if (Schema::hasColumn('general_task_steps', 'planned_minutes')) {
                $table->dropColumn('planned_minutes');
            }
        });

        Schema::table('general_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('general_tasks', 'actual_minutes')) {
                $table->dropColumn('actual_minutes');
            }
            if (Schema::hasColumn('general_tasks', 'planned_minutes')) {
                $table->dropColumn('planned_minutes');
            }
        });
    }
};
