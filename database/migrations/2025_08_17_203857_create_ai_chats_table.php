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
        Schema::create('ai_chats', function (Blueprint $t) {
            $t->id();
            $t->foreignId('user_id')->constrained()->cascadeOnDelete();
            $t->foreignId('customer_id')->nullable()->constrained('new_leads')->nullOnDelete();
            $t->string('title')->nullable();
            $t->longText('memory_summary')->nullable();
            $t->timestamp('memory_updated_at')->nullable();
            $t->boolean('is_shared')->default(false);
            $t->string('share_token', 64)->nullable()->unique();
            $t->timestamp('last_activity_at')->nullable();
            $t->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_chats');
    }
};
