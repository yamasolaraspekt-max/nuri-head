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
        Schema::create('employee_time_schedules', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('employee_id');
            // 1 = Monday ... 7 = Sunday
            $table->unsignedTinyInteger('day_of_week');

            $table->boolean('is_working_day')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();

            // break and work in minutes (net = gross - break)
            $table->unsignedInteger('break_minutes')->default(0);
            $table->unsignedInteger('work_minutes')->default(0);

            $table->timestamps();

            $table->unique(['employee_id', 'day_of_week']);

            $table->foreign('employee_id')
                ->references('id')->on('employees')
                ->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employee_time_schedules');
    }
};
