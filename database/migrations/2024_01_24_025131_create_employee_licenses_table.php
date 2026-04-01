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
        Schema::create('employee_licenses', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('emp_id');  
            $table->string('trailer')->nullable();
            $table->string('license_no')->nullable();
            $table->date('expiry_date')->nullable();
            $table->string('grade')->nullable(); 
            $table->string('status')->nullable();
            $table->date('suspend_date')->nullable();
            $table->string('duration')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('emp_id')->references('id')->on('employees')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_licenses');
    }
};
