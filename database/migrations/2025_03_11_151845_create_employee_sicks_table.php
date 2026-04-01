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
        Schema::create('employee_sicks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emp_id');
            $table->unsignedBigInteger('created_by');
            $table->date('start_date');
            $table->date('end_date')->nullable();  
            $table->integer('total_days')->nullable();  
            $table->integer('total_hours')->nullable(); 
            $table->integer('year')->nullable();
            $table->string('status')->nullable();
            $table->string('status_msg')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
            $table->foreign('emp_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('employees')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_sicks');
    }
};
