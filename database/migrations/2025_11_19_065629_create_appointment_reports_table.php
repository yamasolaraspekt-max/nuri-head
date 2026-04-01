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
        Schema::create('appointment_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->unsignedBigInteger('appointment_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->string('type')->nullable(); 
            $table->longText('report')->nullable();
            $table->date('report_date')->nullable();
             $table->longText('next_step')->nullable();
            $table->date('due_date')->nullable();
            $table->unsignedBigInteger('report_by')->nullable();
            $table->json('comments')->nullable();
            $table->unsignedInteger('likes')->default(0);
            $table->unsignedInteger('dislikes')->default(0);
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('employees')->onDelete('cascade');
            $table->foreign('appointment_id')->references('id')->on('main_appointments')->onDelete('cascade');
            $table->foreign('task_id')->references('id')->on('personal_tasks')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_reports');
    }
};
