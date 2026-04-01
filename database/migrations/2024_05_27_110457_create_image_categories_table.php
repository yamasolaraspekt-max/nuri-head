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
        Schema::create('image_categories', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->unsignedBigInteger('article_group_id');
            $table->foreign('article_group_id')->references('id')->on('article_groups')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('image_categories');
    }
};
