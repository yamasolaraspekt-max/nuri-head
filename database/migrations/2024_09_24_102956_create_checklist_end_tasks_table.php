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
        Schema::create('checklist_end_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('checklist_id');
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->date('done_date')->nullable(); 
            $table->string('checked')->nullable();
            $table->date('checked_date')->nullable();
            $table->unsignedBigInteger('checked_by')->nullable(); 
            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade'); 
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade'); 
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('cascade'); 
            $table->foreign('checked_by')->references('id')->on('employees')->onDelete('cascade'); 


            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_end_tasks');
    }
};
