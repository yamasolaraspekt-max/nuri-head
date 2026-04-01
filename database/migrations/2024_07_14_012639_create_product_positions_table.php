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
       Schema::create('product_positions', function (Blueprint $table) {
            $table->id();
            $table->string('stage')->nullable();
            $table->unsignedBigInteger('article_group_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();  
            $table->unsignedBigInteger('department_id')->nullable();
            $table->json('position_ids')->nullable();
            $table->timestamps();
            
            $table->foreign('article_group_id')->references('id')->on('article_groups')->onDelete('set null');
            $table->foreign('department_id')->references('id')->on('departments')->onDelete('set null');
            $table->foreign('service_id')->references('id')->on('phase_sections')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_positions');
    }
};
