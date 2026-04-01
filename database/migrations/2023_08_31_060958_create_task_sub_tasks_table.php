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
        Schema::create('task_sub_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phase_id'); 
            $table->unsignedBigInteger('task_id');
            $table->string('task_title')->nullable();
            $table->integer('duration')->nullable();
            $table->string('duration_type')->nullable();
            $table->longText('description'); 
            $table->string('status')->default('Published');
            $table->string('photo')->nullable();
            $table->string('image')->nullable();
            $table->integer('answered_by')->default(2);
            $table->timestamps();
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade'); 
            $table->foreign('task_id')->references('id')->on('phase_activities')->onDelete('cascade');  

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_sub_tasks');
    }
};
