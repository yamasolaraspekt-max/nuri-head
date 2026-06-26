<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('planner_items')) {
            Schema::table('planner_items', function (Blueprint $table) {
                if (!Schema::hasColumn('planner_items', 'done_at')) {
                    $table->timestamp('done_at')->nullable()->after('status');
                }

                if (!Schema::hasColumn('planner_items', 'done_by_employee_id')) {
                    $table->unsignedBigInteger('done_by_employee_id')->nullable()->after('done_at')->index();
                }
            });
        }

        if (!Schema::hasTable('planner_item_status_histories')) {
            Schema::create('planner_item_status_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('planner_item_id')->index();
                $table->unsignedBigInteger('planner_plan_id')->nullable()->index();
                $table->unsignedBigInteger('project_id')->nullable()->index();
                $table->string('source_type')->nullable()->index();
                $table->unsignedBigInteger('source_id')->nullable()->index();
                $table->string('old_status')->nullable()->index();
                $table->string('new_status')->index();
                $table->string('status_label')->nullable();
                $table->unsignedBigInteger('changed_by_employee_id')->nullable()->index();
                $table->timestamp('changed_at')->nullable()->index();
                $table->text('note')->nullable();
                $table->json('meta')->nullable();
                $table->timestamps();

                $table->index(['planner_plan_id', 'changed_at'], 'pish_plan_changed_idx');
                $table->index(['planner_item_id', 'changed_at'], 'pish_item_changed_idx');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('planner_item_status_histories')) {
            Schema::dropIfExists('planner_item_status_histories');
        }

        if (Schema::hasTable('planner_items')) {
            Schema::table('planner_items', function (Blueprint $table) {
                if (Schema::hasColumn('planner_items', 'done_by_employee_id')) {
                    $table->dropColumn('done_by_employee_id');
                }

                if (Schema::hasColumn('planner_items', 'done_at')) {
                    $table->dropColumn('done_at');
                }
            });
        }
    }
};
