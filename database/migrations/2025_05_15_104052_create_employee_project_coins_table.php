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
        Schema::create('employee_project_coins', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('project_task_id')->nullable();
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('award_id')->nullable();
            $table->unsignedBigInteger('feedback_id')->nullable();
            $table->unsignedBigInteger('employee_id')->nullable(); 

            $table->timestamps();


            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('award_id')->references('id')->on('project_awards')->onDelete('cascade'); 
             $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('cascade');
            $table->foreign('feedback_id')->references('id')->on('project_feedback')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('project_task_id')->references('id')->on('project_tasks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_project_coins');
    }
};
