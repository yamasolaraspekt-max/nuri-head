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
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('alternative_id'); 
            $table->unsignedBigInteger('service_id')->nullable(); 
            $table->unsignedBigInteger('department_id')->nullable(); 
            $table->string('service');
            $table->unsignedBigInteger('employee_id');  
            $table->unsignedBigInteger('project_leader')->nullable();  
            $table->string('progress')->nullable();
            $table->date('project_start')->nullable();
            $table->date('montage_start')->nullable();
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('total_time', 10,2)->nullable();
            $table->string('color')->nullable();
             $table->string('project_status')->nullable();
             $table->string('priority')->nullable();
            $table->string('status')->nullable();
            $table->string('status_msg')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('project_leader')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');  
            $table->foreign('service_id')->references('id')->on('phase_sections')->onDelete('cascade');  
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');  
 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
