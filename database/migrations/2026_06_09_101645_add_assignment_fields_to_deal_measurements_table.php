<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('deal_measurements', function (Blueprint $table) {
            if (!Schema::hasColumn('deal_measurements', 'appointment_id')) {
                $table->unsignedBigInteger('appointment_id')->nullable()->after('note')->index();
            }

            if (!Schema::hasColumn('deal_measurements', 'personal_task_id')) {
                $table->unsignedBigInteger('personal_task_id')->nullable()->after('appointment_id')->index();
            }

            if (!Schema::hasColumn('deal_measurements', 'responsible_employee_id')) {
                $table->unsignedBigInteger('responsible_employee_id')->nullable()->after('personal_task_id')->index();
            }

            if (!Schema::hasColumn('deal_measurements', 'scheduled_start_date')) {
                $table->date('scheduled_start_date')->nullable()->after('responsible_employee_id');
            }

            if (!Schema::hasColumn('deal_measurements', 'scheduled_end_date')) {
                $table->date('scheduled_end_date')->nullable()->after('scheduled_start_date');
            }

            if (!Schema::hasColumn('deal_measurements', 'scheduled_start_time')) {
                $table->time('scheduled_start_time')->nullable()->after('scheduled_end_date');
            }

            if (!Schema::hasColumn('deal_measurements', 'scheduled_end_time')) {
                $table->time('scheduled_end_time')->nullable()->after('scheduled_start_time');
            }

            if (!Schema::hasColumn('deal_measurements', 'assignment_description')) {
                $table->longText('assignment_description')->nullable()->after('scheduled_end_time');
            }

            if (!Schema::hasColumn('deal_measurements', 'assignment_status')) {
                $table->string('assignment_status', 50)->nullable()->default('not_assigned')->after('assignment_description')->index();
            }

            if (!Schema::hasColumn('deal_measurements', 'assigned_at')) {
                $table->timestamp('assigned_at')->nullable()->after('assignment_status');
            }

            if (!Schema::hasColumn('deal_measurements', 'assigned_by')) {
                $table->unsignedBigInteger('assigned_by')->nullable()->after('assigned_at')->index();
            }
        });
    }

    public function down(): void
    {
        Schema::table('deal_measurements', function (Blueprint $table) {
            $columns = [
                'assigned_by',
                'assigned_at',
                'assignment_status',
                'assignment_description',
                'scheduled_end_time',
                'scheduled_start_time',
                'scheduled_end_date',
                'scheduled_start_date',
                'responsible_employee_id',
                'personal_task_id',
                'appointment_id',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('deal_measurements', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
