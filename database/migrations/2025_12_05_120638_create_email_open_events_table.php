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
        Schema::create('email_open_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('campaign')->nullable();
            $table->string('email')->nullable();

            $table->string('ip', 45)->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->string('status', 20)->default('created');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_open_events');
    }
};
