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
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->string('stage');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('version')->nullable();
            $table->string('status')->default('Published');
            $table->integer('sort_order')->nullable();
            $table->string('default')->nullable();
            $table->softDeletes();
            $table->timestamps(); 
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('set null');   
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stages');
    }
};
