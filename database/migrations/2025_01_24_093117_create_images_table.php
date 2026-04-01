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
         Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('article_group')->nullable();
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('sub_task_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('update_by')->nullable();
            $table->string('stage'); 
            $table->string('image_name')->nullable();
            $table->string('image'); 
            $table->string('file_type')->nullable();  
            $table->string('status')->nullable();  
            $table->timestamps(); 
            $table->softDeletes(); 
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade'); 
            $table->foreign('article_group')->references('id')->on('article_groups')->onDelete('cascade'); 
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade'); 
            $table->foreign('task_id')->references('id')->on('phase_activities')->onDelete('cascade'); 
            $table->foreign('sub_task_id')->references('id')->on('task_sub_tasks')->onDelete('cascade'); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('update_by')->references('id')->on('employees')->onDelete('cascade'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
