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
    Schema::create('chats', function (Blueprint $table) {
        $table->id();
        $table->string('slug')->nullable();

        // 🔗 Sender and recipient (1:1 chat)
        $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('cascade');
        $table->foreignId('to_user_id')->nullable()->constrained('users')->onDelete('cascade');
 
        // 🏷️ Group chat
        $table->foreignId('group_id')->nullable()->constrained('chat_groups')->onDelete('cascade');

        // 📝 Message content
        $table->text('message')->nullable();
        $table->string('file_path')->nullable();

        // 🎯 Type & status
        $table->string('type')->default('text');
        $table->enum('status', ['sent', 'delivered', 'read'])->default('sent');
        $table->boolean('is_read')->default(false);

        // 🕓 Read timestamp
        $table->timestamp('read_at')->nullable();

        // 🔁 Replies
        $table->foreignId('reply_to_id')->nullable()->constrained('chats')->onDelete('set null');
        $table->foreignId('shared_from_id')->nullable()->constrained('chats')->nullOnDelete();

        // 📅 Created/Updated/Deleted timestamps
        $table->timestamps();      // created_at and updated_at
        $table->softDeletes();     // deleted_at
    });




    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
