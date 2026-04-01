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
        Schema::create('customer_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('alternative_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('section_id')->nullable();
            $table->unsignedBigInteger('done_by')->nullable();
            $table->unsignedBigInteger('marked_by')->nullable();
            
            $table->string('is_done')->nullable();
            $table->json('done_reason')->nullable();
            $table->time('plan_time')->nullable();
            $table->time('is_time')->nullable();
            $table->time('d_time')->nullable();
            $table->date('done_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('has_document')->nullable();
            $table->json('done_history')->nullable();
            $table->string('old_stage')->nullable();
            
            $table->timestamps();

            // Foreign Keys
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('set null');
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('set null');
            $table->foreign('section_id')->references('id')->on('phase_sections')->onDelete('set null');
            $table->foreign('done_by')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('marked_by')->references('id')->on('employees')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('set null');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('set null');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('set null');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_histories');
    }
};
