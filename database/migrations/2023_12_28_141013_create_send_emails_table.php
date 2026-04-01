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
        Schema::create('send_emails', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->longText('body')->nullable();
            $table->integer('attachment_count')->nullable();
            $table->string('sender_email')->nullable();
            $table->string('sender_name');
            $table->string('recipient_name')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('send_emails');
    }
};
