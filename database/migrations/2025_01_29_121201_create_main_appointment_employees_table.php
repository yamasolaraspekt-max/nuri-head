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
        Schema::create('main_appointment_employees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('appointment_id'); 
            $table->string('status')->default('send');
            $table->string('reason')->nullable(); 
            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
            $table->foreign('appointment_id')->references('id')->on('main_appointments')->onDelete('cascade');   
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('main_appointment_employees');
    }
};
