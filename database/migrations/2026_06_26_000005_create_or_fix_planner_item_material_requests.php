<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('planner_item_material_requests')) {
            Schema::create('planner_item_material_requests', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('planner_item_id');
                $table->unsignedBigInteger('planner_plan_id')->nullable();
                $table->unsignedBigInteger('lead_product_list_id')->nullable();
                $table->unsignedBigInteger('customer_id')->nullable();
                $table->unsignedBigInteger('alternative_id')->nullable();
                $table->unsignedBigInteger('product_id')->nullable();
                $table->unsignedBigInteger('requested_by_employee_id')->nullable();
                $table->string('name')->nullable();
                $table->string('article_name')->nullable();
                $table->string('article_no')->nullable();
                $table->text('description')->nullable();
                $table->decimal('quantity', 12, 3)->default(1);
                $table->string('unit', 50)->nullable();
                $table->string('priority', 50)->default('normal');
                $table->date('needed_at')->nullable();
                $table->text('note')->nullable();
                $table->string('status', 50)->default('requested');
                $table->json('meta')->nullable();
                $table->timestamps();
                $table->softDeletes();
                $table->index('planner_item_id');
                $table->index('planner_plan_id');
                $table->index('lead_product_list_id');
                $table->index('customer_id');
                $table->index('status');
            });

            return;
        }

        Schema::table('planner_item_material_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('planner_item_material_requests', 'planner_plan_id')) {
                $table->unsignedBigInteger('planner_plan_id')->nullable()->after('planner_item_id');
            }
            if (!Schema::hasColumn('planner_item_material_requests', 'lead_product_list_id')) {
                $table->unsignedBigInteger('lead_product_list_id')->nullable()->after('planner_plan_id');
            }
            if (!Schema::hasColumn('planner_item_material_requests', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('lead_product_list_id');
            }
            if (!Schema::hasColumn('planner_item_material_requests', 'alternative_id')) {
                $table->unsignedBigInteger('alternative_id')->nullable()->after('customer_id');
            }
            if (!Schema::hasColumn('planner_item_material_requests', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('alternative_id');
            }
            if (!Schema::hasColumn('planner_item_material_requests', 'requested_by_employee_id')) {
                $table->unsignedBigInteger('requested_by_employee_id')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('planner_item_material_requests', 'article_no')) {
                $table->string('article_no')->nullable()->after('article_name');
            }
            if (!Schema::hasColumn('planner_item_material_requests', 'meta')) {
                $table->json('meta')->nullable()->after('status');
            }
            if (!Schema::hasColumn('planner_item_material_requests', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planner_item_material_requests');
    }
};
