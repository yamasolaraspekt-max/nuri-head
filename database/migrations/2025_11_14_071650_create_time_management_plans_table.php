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
        Schema::create('time_management_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('employee_id');
            $table->integer('year');
            $table->integer('month'); // 1..12

            // snapshot values from employees at the moment of planning
            $table->string('working_type')->nullable();         // from employees.working_type
            $table->decimal('hourly_rate', 10, 2)->default(0);  // from employees.salary_per_hour
            $table->decimal('target_hours', 8, 2)->default(0);  // default from employees.working_hour
            $table->decimal('scheduled_hours', 8, 2)->default(0);

            $table->enum('status', ['draft', 'pending', 'approved', 'rejected'])->default('draft');

            $table->unsignedBigInteger('approved_by')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->text('comment')->nullable();

            $table->timestamps();

            $table->foreign('employee_id')
                ->references('id')->on('employees')
                ->onDelete('cascade');

            $table->foreign('approved_by')
                ->references('id')->on('users')
                ->onDelete('set null');

            $table->unique(['employee_id', 'year', 'month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('time_management_plans');
    }
};
