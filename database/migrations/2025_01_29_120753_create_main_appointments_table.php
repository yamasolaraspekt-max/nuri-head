<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('main_appointments', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('created_by');
            $table->string('name');
            $table->string('execution_type')->nullable();
            $table->string('appointment_type')->nullable();

            // FIX: nullable('new') is invalid
            $table->string('contact_mode')->nullable()->default('new');

            $table->longText('note')->nullable();
            $table->string('color')->nullable();

            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('total_time', 10, 2)->nullable();

            $table->string('date_type')->nullable();
            $table->string('from_day')->nullable();
            $table->string('to_day')->nullable();
            $table->string('from_month')->nullable();
            $table->string('to_month')->nullable();
            $table->string('repeat')->nullable();

            $table->date('reminder_date')->nullable();
            $table->time('reminder_time')->nullable();

            $table->json('report_responsible')->nullable();
            $table->string('next_step')->nullable();

            $table->date('change_date')->nullable();
            $table->longText('change_reason')->nullable();

            $table->string('full_address')->nullable();
            $table->string('street')->nullable();
            $table->decimal('latitude', 35, 30)->nullable();
            $table->decimal('longitude', 35, 30)->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();

            $table->string('phone')->nullable();
            $table->string('email')->nullable();

            $table->string('status')->nullable();
            $table->string('link')->nullable();
            $table->string('priority')->nullable();

            $table->string('public')->nullable();

            // cleaner: no nullable needed when default exists
            $table->boolean('is_notified')->default(false);

            $table->string('type')->default('appointment');

            $table->unsignedBigInteger('branch_id')->nullable();
            $table->unsignedBigInteger('branch_address_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();

            $table->json('products')->nullable();
            $table->json('product_inquiry')->nullable();

            $table->integer('other_id')->nullable();

            // New Columns
            $table->unsignedBigInteger('contact_id')->nullable();
            $table->unsignedBigInteger('task_id')->nullable();
            $table->unsignedBigInteger('problem_id')->nullable();
            $table->unsignedBigInteger('problem_task_id')->nullable(); // FK added later (cycle)
            $table->string('contact_type')->nullable();
            $table->string('is_contact')->nullable();
            $table->string('pre_type')->nullable();
            $table->string('source')->nullable();

            // Reporting
            $table->string('is_report')->default('false');
            $table->longText('report')->nullable();
            $table->date('report_date')->nullable();
            $table->unsignedBigInteger('report_by')->nullable();

            $table->unsignedBigInteger('changed_by')->nullable();

            // Indexes (good practice)
            $table->index('created_by');
            $table->index('task_id');
            $table->index('changed_by');
            $table->index('customer_id');
            $table->index('branch_id');
            $table->index('branch_address_id');
            $table->index('report_by');
            $table->index('problem_id');
            $table->index('problem_task_id');

            // Foreign keys (NON-CYCLIC only)
            $table->foreign('created_by')->references('id')->on('employees')->cascadeOnDelete();

            // nullable -> prefer nullOnDelete (avoid unexpected deletions)
            $table->foreign('task_id')->references('id')->on('personal_tasks')->nullOnDelete();
            $table->foreign('changed_by')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('new_leads')->nullOnDelete();
            $table->foreign('branch_id')->references('id')->on('branches')->nullOnDelete();
            $table->foreign('branch_address_id')->references('id')->on('branch_addresses')->nullOnDelete();
            $table->foreign('report_by')->references('id')->on('employees')->nullOnDelete();
            $table->foreign('problem_id')->references('id')->on('problems')->nullOnDelete();

            // IMPORTANT: DO NOT add FK to ticket_tasks here (problem_task_id) -> added later

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('main_appointments');
    }
};
