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
        Schema::create('chat_mentions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('chat_id')->constrained('chats')->cascadeOnDelete();
            $table->foreignId('mentioned_user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('mentioned_by_user_id')->constrained('users')->cascadeOnDelete();

            $table->foreignId('group_id')->nullable()->constrained('chat_groups')->nullOnDelete();

            $table->timestamp('read_at')->nullable();

            $table->timestamps();

            $table->unique(['chat_id', 'mentioned_user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_mentions');
    }
};
