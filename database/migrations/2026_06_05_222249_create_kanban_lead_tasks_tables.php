<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('kanban_lead_tasks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_product_list_id')
                ->constrained('lead_product_lists')
                ->cascadeOnDelete();

            $table->foreignId('customer_id')
                ->constrained('new_leads')
                ->cascadeOnDelete();

            $table->foreignId('alternative_id')
                ->nullable()
                ->constrained('lead_alternative_adds')
                ->nullOnDelete();

            $table->foreignId('product_id')
                ->constrained('article_groups')
                ->cascadeOnDelete();

            $table->foreignId('lead_stage_id')
                ->nullable()
                ->constrained('lead_stages')
                ->nullOnDelete();

            $table->foreignId('lead_sub_stage_id')
                ->nullable()
                ->constrained('lead_stage_sub_stages')
                ->nullOnDelete();

            $table->foreignId('task_phase_id')
                ->nullable()
                ->constrained('task_phases')
                ->nullOnDelete();

            $table->foreignId('phase_activity_id')
                ->nullable()
                ->constrained('phase_activities')
                ->nullOnDelete();

            $table->string('title');
            $table->longText('description')->nullable();
            $table->longText('internal_note')->nullable();

            $table->boolean('is_manual')->default(false);
            $table->boolean('is_scheduled')->default(false);
            $table->boolean('photo_required')->default(false);

            $table->string('status')->default('open');
            // open, scheduled, in_progress, done, cancelled

            $table->unsignedInteger('estimated_minutes')->nullable();

            $table->dateTime('planned_start_at')->nullable();
            $table->dateTime('planned_end_at')->nullable();
            $table->dateTime('done_at')->nullable();

            /*
             * IMPORTANT:
             * In your system auth()->user()->name = employees.id
             */
            $table->unsignedBigInteger('created_by_employee_id')->nullable();
            $table->unsignedBigInteger('performer_employee_id')->nullable();
            $table->unsignedBigInteger('scheduled_by_employee_id')->nullable();
            $table->unsignedBigInteger('done_by_employee_id')->nullable();

            $table->foreign('created_by_employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            $table->foreign('performer_employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            $table->foreign('scheduled_by_employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            $table->foreign('done_by_employee_id')
                ->references('id')
                ->on('employees')
                ->nullOnDelete();

            $table->json('meta')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index([
                'lead_product_list_id',
                'lead_stage_id',
                'lead_sub_stage_id',
                'status',
            ], 'klt_context_status_idx');

            $table->index([
                'customer_id',
                'alternative_id',
                'product_id',
            ], 'klt_lead_product_context_idx');
        });

        Schema::create('kanban_lead_task_employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kanban_lead_task_id')
                ->constrained('kanban_lead_tasks')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('employee_id');

            $table->foreign('employee_id')
                ->references('id')
                ->on('employees')
                ->cascadeOnDelete();

            $table->string('role')->default('scheduled_candidate');
            // performer, scheduled_candidate, helper

            $table->string('status')->default('open');
            // open, accepted, declined, done

            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('done_at')->nullable();

            $table->timestamps();

            $table->unique(
                ['kanban_lead_task_id', 'employee_id', 'role'],
                'klt_employee_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kanban_lead_task_employees');
        Schema::dropIfExists('kanban_lead_tasks');
    }
};