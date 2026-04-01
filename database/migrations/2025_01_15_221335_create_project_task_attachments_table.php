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
        Schema::create('project_task_attachments', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->unsignedBigInteger('upload_by')->nullable();
            $table->string('image_name');
            $table->string('image');
            $table->string('file_type');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');  
            $table->foreign('upload_by')->references('id')->on('employees')->onDelete('cascade');  
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');  
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');  
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('cascade');  
  
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('project_task_attachments');
    }
};
