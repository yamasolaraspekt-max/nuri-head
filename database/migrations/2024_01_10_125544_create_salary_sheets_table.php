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
        Schema::create('salary_sheets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('emp_id');
            $table->integer('per_week');
            $table->integer('per_day');
            $table->integer('per_year');
            $table->integer('holiday');
            $table->integer('holiday_hour');
            $table->integer('sick_leave');
            $table->integer('sick_leave_hour');
            $table->float('health_insurance',2);
            $table->float('shared_wage',2);
            $table->integer('public_holiday');
            $table->integer('public_holiday_hour');
            $table->integer('remaining_working_hour');
            $table->integer('unproductive_working_day');
            $table->integer('unproductive_working_hour');
            $table->integer('productive_hour');
            $table->float('wege_per_hour',2);
            $table->float('monthly_salary', 2);
            $table->float('labor_cost_hour', 2);
            $table->float('additional_cost', 2);
            $table->float('additional_cost_monthly', 2);
            $table->float('additional_cost_yearly', 2);
            $table->float('plus_additional_wage_cost', 2);
            $table->float('gross_salary', 2);
            $table->float('productive_hour_wege', 2);
            $table->float('total_monthly_salary', 2);
            $table->string('file')->nullable();
            $table->date('salary_date')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();


            $table->foreign('emp_id')->references('id')->on('employees')->onDelete('cascade')->onUpdate('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_sheets');
    }
};
