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
        Schema::create('learning_topic_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('learning_topic_id')
                ->constrained('learning_topics')
                ->onDelete('cascade');

            $table->enum('media_type', ['image', 'video', 'audio', 'file']);
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->string('file_path');     // e.g. learning_media/abc123.mp4
            $table->string('mime_type')->nullable();
            $table->integer('sort_order')->default(0);

            $table->json('meta')->nullable(); // e.g. { "duration": 120, "poster": "…" }

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_topic_media');
    }
};
