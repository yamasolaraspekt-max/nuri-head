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
        Schema::create('reminder_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reminder_id');

            $table->string('event', 30); // created|snoozed|reminded|completed|canceled|reopened|note
            $table->dateTime('old_next_remind_at')->nullable();
            $table->dateTime('new_next_remind_at')->nullable();
            $table->text('note')->nullable();

            $table->unsignedBigInteger('actor_employee_id')->nullable();

            $table->timestamps();

            $table->index(['reminder_id','created_at'], 'reminder_events_idx');
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reminder_events');
    }
};
