<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('planner_item_comments')) {
            Schema::create('planner_item_comments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('planner_item_id')->index();
                $table->unsignedBigInteger('plan_id')->nullable()->index();
                $table->unsignedBigInteger('planner_plan_id')->nullable()->index();
                $table->unsignedBigInteger('lead_product_list_id')->nullable()->index();
                $table->unsignedBigInteger('project_id')->nullable()->index();
                $table->unsignedBigInteger('customer_id')->nullable()->index();
                $table->unsignedBigInteger('alternative_id')->nullable()->index();
                $table->unsignedBigInteger('product_id')->nullable()->index();
                $table->unsignedBigInteger('article_group')->nullable()->index();
                $table->string('source_type')->nullable();
                $table->unsignedBigInteger('source_id')->nullable();
                $table->string('title')->nullable();
                $table->string('subject')->nullable();
                $table->longText('body')->nullable();
                $table->longText('comment')->nullable();
                $table->longText('description')->nullable();
                $table->string('author_name')->nullable();
                $table->unsignedBigInteger('created_by_employee_id')->nullable()->index();
                $table->unsignedBigInteger('created_by')->nullable()->index();
                $table->softDeletes();
                $table->timestamps();
            });
        }

        if (Schema::hasTable('images')) {
            Schema::table('images', function (Blueprint $table) {
                if (!Schema::hasColumn('images', 'lead_product_list_id')) {
                    $table->unsignedBigInteger('lead_product_list_id')->nullable()->index()->after('customer_id');
                }
                if (!Schema::hasColumn('images', 'planner_item_id')) {
                    $table->unsignedBigInteger('planner_item_id')->nullable()->index()->after('lead_product_list_id');
                }
            });
        }

        if (Schema::hasTable('planner_item_materials')) {
            Schema::table('planner_item_materials', function (Blueprint $table) {
                if (!Schema::hasColumn('planner_item_materials', 'plan_id')) {
                    $table->unsignedBigInteger('plan_id')->nullable()->index()->after('planner_item_id');
                }
                if (!Schema::hasColumn('planner_item_materials', 'lead_product_list_id')) {
                    $table->unsignedBigInteger('lead_product_list_id')->nullable()->index()->after('plan_id');
                }
                if (!Schema::hasColumn('planner_item_materials', 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable()->index()->after('lead_product_list_id');
                }
                if (!Schema::hasColumn('planner_item_materials', 'alternative_id')) {
                    $table->unsignedBigInteger('alternative_id')->nullable()->index()->after('customer_id');
                }
                if (!Schema::hasColumn('planner_item_materials', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->index()->after('alternative_id');
                }
                if (!Schema::hasColumn('planner_item_materials', 'article_group')) {
                    $table->unsignedBigInteger('article_group')->nullable()->index()->after('product_id');
                }
            });
        }

        if (Schema::hasTable('planner_item_steps')) {
            Schema::table('planner_item_steps', function (Blueprint $table) {
                if (!Schema::hasColumn('planner_item_steps', 'plan_id')) {
                    $table->unsignedBigInteger('plan_id')->nullable()->index()->after('planner_item_id');
                }
                if (!Schema::hasColumn('planner_item_steps', 'lead_product_list_id')) {
                    $table->unsignedBigInteger('lead_product_list_id')->nullable()->index()->after('plan_id');
                }
                if (!Schema::hasColumn('planner_item_steps', 'customer_id')) {
                    $table->unsignedBigInteger('customer_id')->nullable()->index()->after('lead_product_list_id');
                }
                if (!Schema::hasColumn('planner_item_steps', 'alternative_id')) {
                    $table->unsignedBigInteger('alternative_id')->nullable()->index()->after('customer_id');
                }
                if (!Schema::hasColumn('planner_item_steps', 'product_id')) {
                    $table->unsignedBigInteger('product_id')->nullable()->index()->after('alternative_id');
                }
                if (!Schema::hasColumn('planner_item_steps', 'article_group')) {
                    $table->unsignedBigInteger('article_group')->nullable()->index()->after('product_id');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('planner_item_comments');
    }
};
