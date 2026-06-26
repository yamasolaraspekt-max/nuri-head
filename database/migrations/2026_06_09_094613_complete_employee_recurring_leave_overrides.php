<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employee_recurring_leave_overrides', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_recurring_leave_overrides', 'new_duration_days')) {
                $table->unsignedInteger('new_duration_days')->nullable()->after('new_end_time');
            }

            if (!Schema::hasColumn('employee_recurring_leave_overrides', 'new_title')) {
                $table->string('new_title')->nullable()->after('new_duration_days');
            }

            if (!Schema::hasColumn('employee_recurring_leave_overrides', 'new_description')) {
                $table->text('new_description')->nullable()->after('new_title');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_recurring_leave_overrides', function (Blueprint $table) {
            foreach (['new_description', 'new_title', 'new_duration_days'] as $column) {
                if (Schema::hasColumn('employee_recurring_leave_overrides', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
