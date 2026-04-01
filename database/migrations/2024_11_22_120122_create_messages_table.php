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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
                $table->unsignedBigInteger('message_id')->nullable();
            $table->unsignedBigInteger('chat_id')->nullable();
            $table->unsignedBigInteger('author_id')->nullable();
            $table->text('text')->nullable();
            $table->timestamp('date')->nullable();
            $table->boolean('unread')->default(false);
            $table->string('uuid')->nullable();
            $table->json('replaces')->nullable();
            $table->json('params')->nullable();
            $table->string('disappearing_date')->nullable();

            // User data
            $table->string('user_name')->nullable();
            $table->string('user_first_name')->nullable();
            $table->string('user_last_name')->nullable();
            $table->string('user_work_position')->nullable();
            $table->string('user_avatar')->nullable();
            $table->string('user_status')->nullable();
            $table->json('user_departments')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
