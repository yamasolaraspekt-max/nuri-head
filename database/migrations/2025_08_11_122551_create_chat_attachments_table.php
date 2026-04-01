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
        Schema::create('chat_attachments', function (Blueprint $t) {
            $t->id();
            $t->unsignedBigInteger('chat_id');
            $t->string('disk')->default('public');      // or 'private'
            $t->string('path');                         // stored path
            $t->string('original_name');
            $t->string('mime')->nullable();
            $t->unsignedBigInteger('size')->nullable();
            $t->boolean('is_image')->default(false);
            $t->timestamps();

            $t->foreign('chat_id')->references('id')->on('chats')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_attachments');
    }
};
