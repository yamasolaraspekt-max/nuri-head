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
        Schema::create('customer_phase_lists', function (Blueprint $table) {
            $table->id();
      
            $table->unsignedBigInteger('project_id')->nullable(); 
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('checklist_id')->nullable();
            $table->unsignedBigInteger('phase_id')->nullable();
            $table->unsignedBigInteger('activities_id');
            $table->unsignedBigInteger('product_id'); 
            $table->unsignedBigInteger('service_id')->nullable(); 
            $table->unsignedBigInteger('department_id')->nullable(); 
            $table->string('service')->nullable(); 
            $table->unsignedBigInteger('alternative_id'); 
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->unsignedBigInteger('contact_person')->nullable();
            $table->unsignedBigInteger('responsible_person')->nullable();
            $table->unsignedBigInteger('outside_service')->nullable();
            $table->unsignedBigInteger('outside_company')->nullable(); 

 
            $table->string('color')->nullable();
            $table->string('active_by')->nullable();
            $table->string('jump_steps')->nullable();
            $table->string('jump_steps_by')->nullable();
   
       
            $table->enum('done', ['true', 'false']); // Constraint on done values
            $table->enum('type', ['main', 'sub']); // Constraint on type values
            $table->integer('main_id')->nullable();
            $table->string('outside_type')->default('internal');
            $table->date('done_date')->nullable();  
            $table->longText('reason')->nullable();
            $table->string('done_status')->nullable(); 
            $table->string('status')->nullable(); 
            $table->integer('work_progress')->nullable();
            $table->time('more_time')->nullable();
            $table->decimal('total_time', 10, 2)->nullable();

 


                // Foreign Key Constraints

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade'); 
            $table->foreign('checklist_id')->references('id')->on('project_montage_checklists')->onDelete('cascade');  
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade'); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
            $table->foreign('activities_id')->references('id')->on('phase_activities')->onDelete('cascade');
            $table->foreign('contact_person')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('responsible_person')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('outside_service')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade'); 
            $table->foreign('outside_company')->references('id')->on('external_personals')->onDelete('cascade'); 
            $table->foreign('service_id')->references('id')->on('phase_sections')->onDelete('cascade'); 
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade'); 
 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_phase_lists');
    }
};
