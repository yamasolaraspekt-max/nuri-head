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
       Schema::create('task_to_dos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative');
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('activities_id');
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->unsignedBigInteger('project_id')->nullable(); 
            $table->unsignedBigInteger('contact_person')->nullable();
            $table->unsignedBigInteger('responsible_person')->nullable();
            $table->unsignedBigInteger('outside_service')->nullable();
            $table->unsignedBigInteger('outside_company')->nullable(); 
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
            $table->timestamps();
            $table->softDeletes(); // Add soft deletes

            // Foreign Key Constraints
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');
            $table->foreign('activities_id')->references('id')->on('phase_activities')->onDelete('cascade');
            $table->foreign('contact_person')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('responsible_person')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('outside_service')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade'); 
            $table->foreign('outside_company')->references('id')->on('external_personals')->onDelete('cascade'); 
            $table->foreign('alternative')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade'); 

            // Indexes
            $table->index('customer_id');
            $table->index('phase_id');
            $table->index('activities_id');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_to_dos');
    }
};
