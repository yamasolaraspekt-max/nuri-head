<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            if (!Schema::hasColumn('stages', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('status');
            }
        });

        Schema::table('task_phases', function (Blueprint $table) {
            if (!Schema::hasColumn('task_phases', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('phase_name');
            }
        });

        Schema::table('lead_product_lists', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_product_lists', 'stage_mode')) {
                $table->string('stage_mode')->default('company')->after('status');
            }

            if (!Schema::hasColumn('lead_product_lists', 'product_stage_id')) {
                $table->unsignedBigInteger('product_stage_id')->nullable()->after('stage_mode');
            }

            if (!Schema::hasColumn('lead_product_lists', 'product_task_phase_id')) {
                $table->unsignedBigInteger('product_task_phase_id')->nullable()->after('product_stage_id');
            }

            if (!Schema::hasColumn('lead_product_lists', 'product_stage_history')) {
                $table->json('product_stage_history')->nullable()->after('stage_history');
            }

            if (!Schema::hasColumn('lead_product_lists', 'workflow_updated_at')) {
                $table->timestamp('workflow_updated_at')->nullable()->after('updated_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lead_product_lists', function (Blueprint $table) {
            foreach (['workflow_updated_at', 'product_stage_history', 'product_task_phase_id', 'product_stage_id', 'stage_mode'] as $column) {
                if (Schema::hasColumn('lead_product_lists', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('task_phases', function (Blueprint $table) {
            if (Schema::hasColumn('task_phases', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });

        Schema::table('stages', function (Blueprint $table) {
            if (Schema::hasColumn('stages', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
