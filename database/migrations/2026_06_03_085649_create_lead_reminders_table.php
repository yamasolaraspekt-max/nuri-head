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
        Schema::create('lead_reminders', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_product_list_id')->nullable()->constrained('lead_product_lists')->nullOnDelete();

            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();

            $table->unsignedBigInteger('department_id')->nullable();
            $table->unsignedBigInteger('responsible_employee_id')->nullable();

            $table->string('title');
            $table->text('description')->nullable();

            $table->date('reminder_date');
            $table->time('reminder_time')->nullable();

            $table->enum('priority', ['normal', 'important', 'critical'])->default('normal');
            $table->enum('status', ['open', 'done', 'cancelled'])->default('open');

            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            $table->index(['customer_id', 'alternative_id', 'product_id'], 'lr_lead_object_product_idx');
            $table->index(['responsible_employee_id', 'reminder_date', 'reminder_time'], 'lr_emp_due_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_reminders');
    }
};
