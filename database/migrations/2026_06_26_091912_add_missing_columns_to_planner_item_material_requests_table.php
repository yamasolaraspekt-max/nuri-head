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
            if (!Schema::hasColumn('planner_item_material_requests', 'article_no')) {
                $table->string('article_no')->nullable()->after('article_name');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'accepted_at')) {
                $table->timestamp('accepted_at')->nullable()->after('status');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'accepted_by_employee_id')) {
                $table->unsignedBigInteger('accepted_by_employee_id')->nullable()->after('accepted_at');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'rejected_at')) {
                $table->timestamp('rejected_at')->nullable()->after('accepted_by_employee_id');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'rejected_by_employee_id')) {
                $table->unsignedBigInteger('rejected_by_employee_id')->nullable()->after('rejected_at');
            }

            if (!Schema::hasColumn('planner_item_material_requests', 'rejection_note')) {
                $table->text('rejection_note')->nullable()->after('rejected_by_employee_id');
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
                'article_no',
                'accepted_at',
                'accepted_by_employee_id',
                'rejected_at',
                'rejected_by_employee_id',
                'rejection_note',
            ] as $column) {
                if (Schema::hasColumn('planner_item_material_requests', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};