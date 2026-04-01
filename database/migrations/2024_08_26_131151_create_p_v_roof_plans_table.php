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
        Schema::create('p_v_roof_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('roof_id')->nullable(); 
            $table->string('roof_structures')->nullable();
            $table->string('planned_action')->nullable();
            $table->text('planned_note')->nullable();
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');  
            $table->foreign('roof_id')->references('id')->on('p_v_long_roofs')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_v_roof_plans');
    }
};
