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
        Schema::create('job_representatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('representer_id');
            $table->unsignedBigInteger('current_representer');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->longText('description')->nullable();
            $table->string('status')->default('Published');
            $table->timestamps();
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('cascade'); 
            $table->foreign('position_id')->references('id')->on('positions')->onDelete('cascade'); 
            $table->foreign('representer_id')->references('id')->on('employees')->onDelete('cascade');  
            $table->foreign('current_representer')->references('id')->on('employees')->onDelete('cascade');  

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_representatives');
    }
};
