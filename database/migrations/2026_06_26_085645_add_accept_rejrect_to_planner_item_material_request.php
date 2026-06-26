<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('planner_item_material_requests')) {
            return;
        }

        Schema::table('planner_item_material_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('planner_item_material_requests', 'response_status')) {
                $table->string('response_status', 50)->nullable()->after('status');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'response_note')) {
                $table->text('response_note')->nullable()->after('response_status');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'responded_by_employee_id')) {
                $table->unsignedBigInteger('responded_by_employee_id')->nullable()->after('requested_by_employee_id');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'responded_at')) {
                $table->timestamp('responded_at')->nullable()->after('response_note');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'accepted_by_employee_id')) {
                $table->unsignedBigInteger('accepted_by_employee_id')->nullable()->after('responded_by_employee_id');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('responded_at');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'rejected_by_employee_id')) {
                $table->unsignedBigInteger('rejected_by_employee_id')->nullable()->after('accepted_by_employee_id');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('accepted_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('planner_item_material_requests')) {
            return;
        }

        Schema::table('planner_item_material_requests', function (Blueprint $table) {
            foreach ([
                'rejected_at',
                'rejected_by_employee_id',
                'accepted_at',
                'accepted_by_employee_id',
                'responded_at',
                'responded_by_employee_id',
                'response_note',
                'response_status',
            ] as $column) {
                if (Schema::hasColumn('planner_item_material_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
