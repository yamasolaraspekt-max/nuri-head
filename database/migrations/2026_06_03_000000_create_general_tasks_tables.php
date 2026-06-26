<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('general_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->longText('description')->nullable();
            $table->enum('status', ['open','in_progress','review','done','archived'])->default('open')->index();
            $table->enum('priority', ['low','normal','important','urgent'])->default('normal')->index();
            $table->enum('visibility', ['all','department','specific'])->default('all')->index();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('claimed_by')->nullable()->index();
            $table->dateTime('due_at')->nullable();
            $table->decimal('planned_hours_today', 8, 2)->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->dateTime('archived_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('general_task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_task_id')->constrained('general_tasks')->cascadeOnDelete();
            $table->unsignedBigInteger('employee_id')->index();
            $table->timestamps();
            $table->unique(['general_task_id','employee_id']);
        });

        Schema::create('general_task_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('general_task_id')->constrained('general_tasks')->cascadeOnDelete();
            $table->unsignedBigInteger('employee_id')->index();
            $table->enum('type', ['comment','report'])->default('comment');
            $table->longText('body');
            $table->decimal('hours', 8, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('general_task_reports');
        Schema::dropIfExists('general_task_assignees');
        Schema::dropIfExists('general_tasks');
    }
};
