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
        Schema::create('employee_activity_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_set_id');
            $table->unsignedBigInteger('employee_set_id');
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('activity_id'); 
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade'); 
            $table->foreign('employee_set_id')->references('id')->on('employee_sets')->onDelete('cascade'); 
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade'); 
            $table->foreign('activity_id')->references('id')->on('phase_activities')->onDelete('cascade'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_activity_sets');
    }
};
