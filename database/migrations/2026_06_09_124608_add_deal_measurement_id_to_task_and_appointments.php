<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('personal_tasks') && !Schema::hasColumn('personal_tasks', 'deal_measurement_id')) {
            Schema::table('personal_tasks', function (Blueprint $table) {
                $table->unsignedBigInteger('deal_measurement_id')->nullable()->after('task_id')->index();
            });
        }

        if (Schema::hasTable('main_appointments') && !Schema::hasColumn('main_appointments', 'deal_measurement_id')) {
            Schema::table('main_appointments', function (Blueprint $table) {
                $table->unsignedBigInteger('deal_measurement_id')->nullable()->after('task_id')->index();
            });
        }

        $this->backfillMainAppointments();
        $this->backfillPersonalTasks();
    }

    public function down(): void
    {
        if (Schema::hasTable('personal_tasks') && Schema::hasColumn('personal_tasks', 'deal_measurement_id')) {
            Schema::table('personal_tasks', function (Blueprint $table) {
                $table->dropIndex(['deal_measurement_id']);
                $table->dropColumn('deal_measurement_id');
            });
        }

        if (Schema::hasTable('main_appointments') && Schema::hasColumn('main_appointments', 'deal_measurement_id')) {
            Schema::table('main_appointments', function (Blueprint $table) {
                $table->dropIndex(['deal_measurement_id']);
                $table->dropColumn('deal_measurement_id');
            });
        }
    }

    private function backfillMainAppointments(): void
    {
        if (!Schema::hasTable('main_appointments') || !Schema::hasTable('deal_measurements')) {
            return;
        }

        if (!Schema::hasColumn('main_appointments', 'deal_measurement_id')) {
            return;
        }

        if (Schema::hasColumn('main_appointments', 'source') && Schema::hasColumn('main_appointments', 'other_id')) {
            DB::statement("\n                UPDATE main_appointments ma\n                INNER JOIN deal_measurements dm ON dm.id = ma.other_id\n                SET ma.deal_measurement_id = dm.id\n                WHERE ma.deal_measurement_id IS NULL\n                  AND ma.source = 'deal_measurement'\n                  AND ma.other_id IS NOT NULL\n            ");
        }

        if (Schema::hasColumn('deal_measurements', 'appointment_id')) {
            DB::statement("\n                UPDATE main_appointments ma\n                INNER JOIN deal_measurements dm ON dm.appointment_id = ma.id\n                SET ma.deal_measurement_id = dm.id\n                WHERE ma.deal_measurement_id IS NULL\n            ");
        }
    }

    private function backfillPersonalTasks(): void
    {
        if (!Schema::hasTable('personal_tasks') || !Schema::hasTable('deal_measurements')) {
            return;
        }

        if (!Schema::hasColumn('personal_tasks', 'deal_measurement_id')) {
            return;
        }

        if (Schema::hasColumn('deal_measurements', 'personal_task_id')) {
            DB::statement("\n                UPDATE personal_tasks pt\n                INNER JOIN deal_measurements dm ON dm.personal_task_id = pt.id\n                SET pt.deal_measurement_id = dm.id\n                WHERE pt.deal_measurement_id IS NULL\n            ");
        }

        if (Schema::hasTable('main_appointments') && Schema::hasColumn('main_appointments', 'deal_measurement_id') && Schema::hasColumn('main_appointments', 'task_id')) {
            DB::statement("\n                UPDATE personal_tasks pt\n                INNER JOIN main_appointments ma ON ma.task_id = pt.id\n                SET pt.deal_measurement_id = ma.deal_measurement_id\n                WHERE pt.deal_measurement_id IS NULL\n                  AND ma.deal_measurement_id IS NOT NULL\n            ");
        }
    }
};
