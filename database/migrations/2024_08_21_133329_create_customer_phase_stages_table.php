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
        Schema::create('customer_phase_stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('phase_id');
             $table->string('status')->nullable();
            $table->string('color')->nullable();
            $table->string('active_by')->nullable();
            $table->string('jump_steps')->nullable();
            $table->string('jump_steps_by')->nullable(); 
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_phase_stages');
    }
};
