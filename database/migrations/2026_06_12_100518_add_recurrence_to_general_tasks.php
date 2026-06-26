<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            if (!Schema::hasColumn('general_tasks', 'is_recurring')) {
                $table->boolean('is_recurring')->default(false)->after('archived_at');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_frequency')) {
                $table->string('recurrence_frequency', 30)->nullable()->after('is_recurring');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_weekday')) {
                $table->unsignedTinyInteger('recurrence_weekday')->nullable()->after('recurrence_frequency');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_ends_at')) {
                $table->dateTime('recurrence_ends_at')->nullable()->after('recurrence_weekday');
            }

            if (!Schema::hasColumn('general_tasks', 'next_recurring_at')) {
                $table->dateTime('next_recurring_at')->nullable()->after('recurrence_ends_at');
            }

            if (!Schema::hasColumn('general_tasks', 'last_recurring_at')) {
                $table->dateTime('last_recurring_at')->nullable()->after('next_recurring_at');
            }

            if (!Schema::hasColumn('general_tasks', 'recurring_parent_id')) {
                $table->unsignedBigInteger('recurring_parent_id')->nullable()->after('last_recurring_at');
                $table->index('recurring_parent_id');
            }

            if (!Schema::hasColumn('general_tasks', 'recurrence_frequency')) {
                return;
            }
        });
    }

    public function down(): void
    {
        Schema::table('general_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('general_tasks', 'recurring_parent_id')) {
                $table->dropIndex(['recurring_parent_id']);
            }

            $columns = [
                'recurring_parent_id',
                'last_recurring_at',
                'next_recurring_at',
                'recurrence_ends_at',
                'recurrence_weekday',
                'recurrence_frequency',
                'is_recurring',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('general_tasks', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
