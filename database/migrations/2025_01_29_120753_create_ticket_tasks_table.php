<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
       if (!Schema::hasTable('ticket_tasks')) {
            Schema::create('ticket_tasks', function (Blueprint $table) {
                $table->id();

                $table->unsignedBigInteger('ticket_id');
                $table->unsignedBigInteger('employee_id');
                $table->unsignedBigInteger('error_id')->nullable();
                $table->unsignedBigInteger('appointment_id')->nullable();

                $table->string('title')->nullable();
                $table->string('priority')->nullable();
                $table->string('status')->nullable();
                $table->date('start_date')->nullable();
                $table->date('due_date')->nullable();
                $table->integer('difference')->nullable();
                $table->longText('solution')->nullable();
                $table->boolean('is_done')->nullable();
                $table->json('teams')->nullable();

                $table->timestamps();

                $table->index('ticket_id');
                $table->index('employee_id');
                $table->index('error_id');
                $table->index('appointment_id');

                $table->foreign('ticket_id')->references('id')->on('problems')->cascadeOnDelete();
                $table->foreign('employee_id')->references('id')->on('employees')->cascadeOnDelete();
                $table->foreign('error_id')->references('id')->on('errors')->nullOnDelete();
                $table->foreign('appointment_id')->references('id')->on('main_appointments')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ticket_tasks');
    }
};
