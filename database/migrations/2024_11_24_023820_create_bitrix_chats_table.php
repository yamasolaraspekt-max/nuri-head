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
        Schema::create('bitrix_chats', function (Blueprint $table) {
           $table->id();
        $table->unsignedBigInteger('parent_chat_id')->default(0);
        $table->unsignedBigInteger('parent_message_id')->default(0);
        $table->string('name')->nullable();
        $table->text('description')->nullable();
        $table->unsignedBigInteger('owner')->nullable();
        $table->boolean('extranet')->default(false);
        $table->string('avatar')->nullable();
        $table->string('color', 7)->nullable();
        $table->string('type')->nullable();
        $table->unsignedInteger('counter')->default(0);
        $table->unsignedInteger('user_counter')->default(0);
        $table->unsignedInteger('message_count')->default(0);
        $table->unsignedBigInteger('unread_id')->default(0);
        $table->unsignedBigInteger('last_message_id')->default(0);
        $table->unsignedBigInteger('last_id')->default(0);
        $table->unsignedBigInteger('marked_id')->default(0);
        $table->unsignedBigInteger('disk_folder_id')->default(0);
        $table->string('entity_type')->nullable();
        $table->string('entity_id')->nullable();
        $table->string('entity_data_1')->nullable();
        $table->string('entity_data_2')->nullable();
        $table->string('entity_data_3')->nullable();
        $table->text('restrictions')->nullable(); // JSON data
        $table->text('mute_list')->nullable(); // JSON data
        $table->timestamp('date_create')->nullable();
        $table->string('message_type')->nullable();
        $table->unsignedInteger('disappearing_time')->default(0);
        $table->string('public')->nullable();
        $table->string('role')->nullable();
        $table->text('entity_link')->nullable(); // JSON data
        $table->text('permissions')->nullable(); // JSON data
        $table->boolean('is_new')->default(false);
        $table->text('readed_list')->nullable(); // JSON data
        $table->text('manager_list')->nullable(); // JSON data
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bitrix_chats');
    }
};
