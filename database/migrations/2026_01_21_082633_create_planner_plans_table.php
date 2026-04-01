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
        Schema::create('planner_plans', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('account_id')->nullable()->index();

            // ✅ correct
            $table->unsignedBigInteger('customer_id')->index();  // -> new_leads.id
            $table->unsignedBigInteger('project_id')->nullable()->index(); // -> lead_product_lists.id

            $table->string('stage', 80)->index(); // montage | inbetriebnahme | ...
            $table->string('title')->nullable();
            $table->string('status', 30)->default('draft')->index(); // draft|published|archived
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->timestamp('published_at')->nullable();
            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['customer_id','project_id','stage','status'], 'planner_plans_lookup_idx');

            // ✅ foreign keys
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('lead_product_lists')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planner_plans');
    }
};
