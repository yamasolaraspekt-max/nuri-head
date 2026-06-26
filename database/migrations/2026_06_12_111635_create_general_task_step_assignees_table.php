<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('general_task_step_assignees')) {
            Schema::create('general_task_step_assignees', function (Blueprint $table) {
                $table->id();
                $table->foreignId('general_task_step_id')->constrained('general_task_steps')->cascadeOnDelete();
                $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
                $table->timestamps();

                $table->unique(['general_task_step_id', 'employee_id'], 'gt_step_employee_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('general_task_step_assignees');
    }
};
