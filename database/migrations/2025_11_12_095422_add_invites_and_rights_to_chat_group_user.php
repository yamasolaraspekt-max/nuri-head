<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('chat_group_user', function (Blueprint $table) {
            // invite + status
            $table->enum('status', ['pending', 'accepted', 'declined'])
                  ->default('pending')
                  ->after('role');

            $table->foreignId('invited_by')
                  ->nullable()
                  ->after('user_id')
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamp('joined_at')->nullable()->after('status');

            // rights
            $table->enum('history_visibility', ['all', 'from_join'])
                  ->default('all')
                  ->after('joined_at');

            $table->boolean('can_write')
                  ->default(true)
                  ->after('history_visibility');
        });
    }

    public function down(): void
    {
        Schema::table('chat_group_user', function (Blueprint $table) {
            $table->dropColumn(['status', 'invited_by', 'joined_at', 'history_visibility', 'can_write']);
        });
    }
};