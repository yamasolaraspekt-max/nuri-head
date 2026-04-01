<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('planner_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('plan_id')->index();
            $table->string('client_uid')->nullable();

            // links back to original modules (phase activity / appointment / manual / ticket task / personal task)
            $table->string('source_type', 60)->nullable()->index(); // phase_activity|appointment|manual|ticket_task|personal_task
            $table->unsignedBigInteger('source_id')->nullable()->index();

            $table->string('title');
            $table->string('category', 60)->nullable()->index();
            $table->text('description')->nullable();

            $table->unsignedInteger('duration_minutes')->default(60);

            $table->string('status', 30)->default('open')->index(); // open|scheduled|in_progress|paused|done
            $table->timestamp('planned_start_at')->nullable()->index();
            $table->timestamp('planned_end_at')->nullable()->index();

            $table->unsignedInteger('sort_order')->default(0)->index();

            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Enforce no duplicates per plan for linked records:
            $table->unique(['plan_id', 'source_type', 'source_id'], 'planner_items_unique_source_per_plan');

            $table->foreign('plan_id')->references('id')->on('planner_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planner_items');
    }
};
