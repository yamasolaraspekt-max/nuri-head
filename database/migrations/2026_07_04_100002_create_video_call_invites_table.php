<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Phase 3b — Gast-Einladungen per E-Mail zu einem Videocall.
 * sent_at bleibt null, wenn der (synchrone) Mailversand fehlschlägt (siehe F3).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_call_invites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_call_id')->constrained('video_calls')->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('email');
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('video_call_invites');
    }
};
