<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('planner_group_materials')) {
            return;
        }

        Schema::create('planner_group_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id')->nullable()->index();
            $table->unsignedBigInteger('planner_plan_id')->nullable()->index();
            $table->unsignedBigInteger('lead_product_list_id')->nullable()->index();
            $table->unsignedBigInteger('project_id')->nullable()->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('alternative_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('article_group')->nullable()->index();

            $table->unsignedBigInteger('employee_id')->nullable()->index();
            $table->string('employee_name')->nullable();
            $table->json('item_ids')->nullable();
            $table->json('linked_item_ids')->nullable();

            $table->uuid('material_group_uuid')->nullable()->index();
            $table->uuid('group_uuid')->nullable()->index();
            $table->string('material_group_name')->nullable();
            $table->string('group_name')->nullable();
            $table->string('material_scope')->nullable()->index();
            $table->string('group_scope')->nullable()->index();
            $table->unsignedBigInteger('material_scope_employee_id')->nullable()->index();
            $table->unsignedBigInteger('group_employee_id')->nullable()->index();
            $table->date('scope_date_from')->nullable()->index();
            $table->date('scope_date_to')->nullable()->index();
            $table->string('scope_mode')->nullable();
            $table->string('period_label')->nullable();

            $table->string('source_type')->nullable()->index();
            $table->unsignedBigInteger('source_id')->nullable()->index();
            $table->string('origin_type')->nullable()->index();

            $table->string('name')->nullable();
            $table->string('title')->nullable();
            $table->string('material_name')->nullable();
            $table->string('article_no')->nullable()->index();
            $table->string('distributor_article_no')->nullable();
            $table->string('sku')->nullable()->index();
            $table->decimal('qty', 14, 3)->default(1);
            $table->decimal('quantity', 14, 3)->default(1);
            $table->string('unit')->nullable();
            $table->string('measure')->nullable();
            $table->string('measure_unit')->nullable();
            $table->decimal('unit_price', 14, 2)->default(0);
            $table->decimal('price', 14, 2)->default(0);
            $table->decimal('purchase_price', 14, 2)->default(0);
            $table->decimal('ek', 14, 2)->default(0);
            $table->decimal('total_price', 14, 2)->default(0);
            $table->unsignedBigInteger('distributor_id')->nullable()->index();
            $table->unsignedBigInteger('distributor_price_id')->nullable()->index();
            $table->string('distributor_name')->nullable();
            $table->string('supplier')->nullable();
            $table->text('image_url')->nullable();
            $table->text('img')->nullable();
            $table->boolean('active')->default(true)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedBigInteger('created_by_employee_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->json('source_payload')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planner_group_materials');
    }
};
