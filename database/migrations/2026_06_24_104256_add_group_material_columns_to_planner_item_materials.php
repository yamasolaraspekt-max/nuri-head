<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('planner_item_materials')) {
            return;
        }

        Schema::table('planner_item_materials', function (Blueprint $table) {
            if (!Schema::hasColumn('planner_item_materials', 'material_group_uuid')) {
                $table->uuid('material_group_uuid')->nullable()->after('planner_item_id');
            }

            if (!Schema::hasColumn('planner_item_materials', 'material_group_name')) {
                $table->string('material_group_name')->nullable()->after('material_group_uuid');
            }

            if (!Schema::hasColumn('planner_item_materials', 'material_scope')) {
                $table->string('material_scope')->nullable()->after('material_group_name');
            }

            if (!Schema::hasColumn('planner_item_materials', 'material_scope_employee_id')) {
                $table->unsignedBigInteger('material_scope_employee_id')->nullable()->after('material_scope');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('planner_item_materials')) {
            return;
        }

        Schema::table('planner_item_materials', function (Blueprint $table) {
            foreach (['material_group_uuid', 'material_group_name', 'material_scope', 'material_scope_employee_id'] as $column) {
                if (Schema::hasColumn('planner_item_materials', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
