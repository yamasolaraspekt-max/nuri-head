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
        Schema::create('project_timelines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('done_by')->nullable();
            $table->unsignedBigInteger('edit_by')->nullable();
            $table->date('start_date')->nullable();
            $table->date('due_date')->nullable();
            $table->string('is_done')->nullable(); // consider boolean or enum if strict
            $table->integer('done_range')->nullable(); // 0, 25, 45, 75, 95
            $table->date('done_date')->nullable();
            $table->integer('date_difference')->nullable(); // better as integer (in days) instead of date
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('cascade');
            $table->foreign('done_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('edit_by')->references('id')->on('employees')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_timelines');
    }
};
