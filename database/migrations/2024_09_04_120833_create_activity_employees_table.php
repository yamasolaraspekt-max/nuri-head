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
        Schema::create('activity_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('activity_id');
            $table->unsignedBigInteger('sub_task_id');
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('appointment_id'); 
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade'); 
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('cascade'); 
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('appointment_id')->references('id')->on('appointments')->onDelete('cascade'); 
            $table->foreign('sub_task_id')->references('id')->on('task_sub_tasks')->onDelete('cascade'); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_employees');
    }
};
