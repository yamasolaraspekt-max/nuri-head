<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('main_appointment_reminder_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('employee_id');

            $table->timestamp('reminder_at')->nullable();
            $table->timestamp('seen_at')->nullable();

            $table->timestamps();

            $table->unique(['appointment_id', 'employee_id'], 'main_appointment_reminder_unique');

            $table->index('appointment_id');
            $table->index('employee_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('main_appointment_reminder_logs');
    }
};