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
        Schema::create('daily_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            
            $table->date('start_date');
            $table->date('end_date')->nullable(); 
            $table->string('address')->nullable();
            $table->decimal('lat', 9, 6)->nullable(); 
            $table->decimal('lon', 9, 6)->nullable();
            $table->string('ip')->nullable();
            $table->string('status')->nullable();                                                                                                                                                                                                                            
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade'); 
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daily_reports');
    }
};
