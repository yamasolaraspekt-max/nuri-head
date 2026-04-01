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
         Schema::create('learning_topics', function (Blueprint $table) {
            $table->id();

            $table->string('title');
            $table->string('slug')->unique();
            $table->string('prompt_label')->nullable();   // Label shown as chip in chat, e.g. "How to create a customer?"
            $table->string('short_intro')->nullable();    // Short teaser sentence
            $table->longText('body')->nullable();         // Main article (Quill HTML)

            $table->unsignedTinyInteger('estimated_minutes')->nullable(); // e.g. 5, 10, 15
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');

            $table->enum('audience_scope', ['all', 'department', 'position', 'employee'])->default('all');
            $table->boolean('is_active')->default(true);

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_topics');
    }
};
