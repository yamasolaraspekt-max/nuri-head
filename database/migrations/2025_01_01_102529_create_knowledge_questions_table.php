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
        Schema::create('knowledge_questions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('knowledge_id');
            $table->string('question');
            $table->longText('description')->nullable();
            $table->string('video')->nullable();
            $table->integer('created_by')->nullable();
            $table->timestamps();

            $table->foreign('knowledge_id')->references('id')->on('knowledge_categories')->onDelete('cascade');  

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_questions');
    }
};
