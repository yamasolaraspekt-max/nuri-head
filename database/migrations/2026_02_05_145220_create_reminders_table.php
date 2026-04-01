<?php

// database/migrations/2026_02_05_000001_create_reminders_tables.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void
  {
    Schema::create('reminders', function (Blueprint $table) {
      $table->id();

      $table->unsignedBigInteger('employee_id'); // assignee/owner of reminder
      $table->string('entity_type', 30);         // inquiry|task|appointment|ticket|lead
      $table->unsignedBigInteger('entity_id');

      $table->string('status', 20)->default('active'); // active|snoozed|done|canceled
      $table->dateTime('next_remind_at')->nullable();
      $table->dateTime('last_reminded_at')->nullable();

      $table->unsignedBigInteger('created_by')->nullable();
      $table->json('meta')->nullable();

      $table->timestamps();
      $table->softDeletes();

      $table->unique(['employee_id','entity_type','entity_id'], 'reminders_owner_entity_unique');
      $table->index(['entity_type','entity_id'], 'reminders_entity_idx');
      $table->index(['employee_id','next_remind_at'], 'reminders_next_idx');
    });

    
  }

  public function down(): void
  { 
    Schema::dropIfExists('reminders');
  }
};
