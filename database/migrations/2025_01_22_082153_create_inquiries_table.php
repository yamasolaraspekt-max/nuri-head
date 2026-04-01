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
        Schema::create('inquiries', function (Blueprint $table) {
            $table->id();
            $table->string('pre_type')->nullable();
            $table->string('source')->nullable();
            $table->string('title')->nullable();
            $table->string('type')->nullable();
            $table->string('type_extra')->nullable();
            $table->string('firma')->nullable();
            $table->string('lastname')->nullable();
            $table->string('name')->nullable();
            $table->string('street')->nullable();
            $table->decimal('latitude', 35, 30)->nullable();
            $table->decimal('longitude', 35, 30)->nullable();  
            $table->decimal('elevation', 35, 30)->nullable();  
            $table->string('postcode')->nullable();
            $table->string('full_address')->nullable();
            $table->string('city')->nullable();
            $table->string('phone')->nullable();
            $table->string('telephone')->nullable();
            $table->string('email')->nullable();
            $table->longText('note')->nullable();
            $table->string('reason')->nullable();
            $table->string('status')->nullable();
            $table->string('periority')->nullable();
            $table->string('next_step')->nullable(); 
            $table->unsignedBigInteger('personal_task_id')->nullable();
            $table->unsignedBigInteger('branch_id');
            $table->unsignedBigInteger('contact_person');  
            $table->unsignedBigInteger('verify_by')->nullable(); 
            $table->unsignedBigInteger('department_id')->nullable(); 
            $table->unsignedBigInteger('direct_to')->nullable(); 
            $table->date('verify_date')->nullable(); 
            $table->softDeletes();  
            $table->timestamps();  
            $table->foreign('branch_id')->references('id')->on('branches')->onDelete('cascade');
            $table->foreign('contact_person')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('verify_by')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('direct_to')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade');
            $table->foreign('personal_task_id')->references('id')->on('personal_tasks')->onDelete('set null');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiries');
    }
};
