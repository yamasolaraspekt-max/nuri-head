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
        Schema::create('task_phases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('section_id')->nullable(); 
            $table->string('section_name')->nullable();
            $table->string('phase_name'); 
            $table->string('stage')->default('project');
            $table->unsignedBigInteger('stage_id')->nullable();
            $table->string('version')->nullable();
            $table->string('status')->nullable();
            $table->integer('count')->nullable();
            $table->integer('order')->nullable();   
            $table->timestamps();
            $table->softDeletes();   
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');  
            $table->foreign('section_id')->references('id')->on('phase_sections')->onDelete('cascade');
            $table->foreign('stage_id')->references('id')->on('stages')->onDelete('set null');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_phases');
    }
};
