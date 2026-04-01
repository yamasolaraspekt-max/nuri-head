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
        Schema::create('lead_product_lists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id'); 
            $table->unsignedBigInteger('service_id')->nullable(); 
            $table->unsignedBigInteger('department_id')->nullable(); 
            $table->unsignedBigInteger('employee_id')->nullable(); //innendienst
            $table->unsignedBigInteger('field_employee')->nullable(); //Außendienst
            $table->json('teams')->nullable(); // New Field
            $table->string('service')->default('complete'); 
            $table->string('status')->default('open');
            $table->string('work_status', 16)->default('playing');
            $table->index('work_status');
            $table->string('interest')->default('intent');
            $table->string('realization_time')->nullable(); // Near as posible, / 3 Month , 6 Month, Sonstiges, (Others)
            $table->json('stage_history')->nullable();
            $table->string('stage')->nullable();
            $table->string('old_stage')->nullable();
            $table->decimal('price', 10,2)->nullable();
            $table->date('price_latest')->nullable();
            $table->integer('project_minutes')->nullable();
            $table->json('price_history')->nullable();
            $table->softDeletes();  
            $table->timestamps();  
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade'); 
            $table->foreign('alternative_id') ->references('id')   ->on('lead_alternative_adds')  ->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('field_employee')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('service_id')->references('id')->on('phase_sections')->onDelete('cascade'); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    { 
        //
    }
};
