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
        Schema::create('employees_personal_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('task_id'); 
            $table->string('status')->default('send');
            $table->string('reason')->nullable(); 
            $table->longText('note')->nullable();
            $table->date('change_date')->nullable();
            $table->unsignedBigInteger('changed_by')->nullable(); 
            $table->longText('change_reason')->nullable();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
             $table->foreign('task_id')->references('id')->on('personal_tasks')->onDelete('cascade');  
            $table->foreign('changed_by')->references('id')->on('employees')->onDelete('cascade');  
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees_personal_tasks');
    }
};
