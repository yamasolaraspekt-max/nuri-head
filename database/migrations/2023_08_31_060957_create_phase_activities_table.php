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
        Schema::create('phase_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('phase_id');
            $table->unsignedBigInteger('product_id'); 
            $table->unsignedBigInteger('section_id');
            $table->unsignedBigInteger('parent_id')->nullable(); 
            $table->unsignedBigInteger('copy_from')->nullable(); 
            $table->unsignedBigInteger('stage_id')->nullable(); 
            $table->string('version')->nullable(); 
            $table->string('section_name')->nullable();
            $table->string('initial')->nullable();
            $table->string('title');
            $table->time('duration')->nullable();
            $table->string('duration_type')->nullable();
            $table->string('description')->nullable();
            $table->longText('notes')->nullable();
            $table->string('status')->nullable();
            $table->string('photo')->nullable();  
            $table->string('link')->nullable();  
            $table->string('priority')->nullable();  
            $table->decimal('percent', 10,2)->nullable();  
            $table->integer('usage_count')->nullable();  
            $table->integer('rating')->nullable();  
            $table->integer('answered_by')->default(2);  
            $table->integer('sort_order')->nullable();
            $table->integer('copy_count')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreign('phase_id')->references('id')->on('task_phases')->onDelete('cascade');  
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');  
            $table->foreign('section_id')->references('id')->on('phase_sections')->onDelete('cascade');  
            $table->foreign('parent_id')->references('id')->on('phase_activities')->onDelete('cascade'); 
            $table->foreign('copy_from')->references('id')->on('phase_activities')->onDelete('cascade'); 
            $table->foreign('stage_id')->references('id')->on('stages')->onDelete('cascade'); 

        });
    } 

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('phase_activities');
    }
};
