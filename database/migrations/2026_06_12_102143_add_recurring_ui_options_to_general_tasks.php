<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('general_tasks', 'show_due_datetime')) {
                $table->boolean('show_due_datetime')->default(true)->after('planned_hours_today');
            }

            if (!Schema::hasColumn('general_tasks', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('show_due_datetime');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_frequency')) {
                $table->string('recurrence_frequency')->nullable()->after('is_recurring');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_weekday')) {
                $table->unsignedTinyInteger('recurrence_weekday')->nullable()->after('recurrence_frequency');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_ends_at')) {
                $table->dateTime('recurrence_ends_at')->nullable()->after('recurrence_weekday');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_parent_id')) {
                $table->unsignedBigInteger('recurrence_parent_id')->nullable()->after('recurrence_ends_at');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_generated_from_id')) {
                $table->unsignedBigInteger('recurrence_generated_from_id')->nullable()->after('recurrence_parent_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            $columns = [
                'recurrence_generated_from_id',
                'recurrence_parent_id',
                'recurrence_ends_at',
                'recurrence_weekday',
                'recurrence_frequency',
                'is_recurring',
                'show_due_datetime',
            ];
 
            foreach ($columns as $column) {
                if (Schema::hasColumn('general_tasks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
