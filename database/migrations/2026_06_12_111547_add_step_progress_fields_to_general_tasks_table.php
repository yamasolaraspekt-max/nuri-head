<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('general_tasks', 'task_mode')) {
                $table->string('task_mode', 20)->default('single')->after('visibility');
            }
            if (!Schema::hasColumn('general_tasks', 'progress_percent')) {
                $table->unsignedTinyInteger('progress_percent')->default(0)->after('planned_hours_today');
            }
            if (!Schema::hasColumn('general_tasks', 'soll_minutes')) {
                $table->unsignedInteger('soll_minutes')->default(0)->after('progress_percent');
            }
            if (!Schema::hasColumn('general_tasks', 'ist_minutes')) {
                $table->unsignedInteger('ist_minutes')->default(0)->after('soll_minutes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            foreach (['task_mode', 'progress_percent', 'soll_minutes', 'ist_minutes'] as $column) {
                if (Schema::hasColumn('general_tasks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
