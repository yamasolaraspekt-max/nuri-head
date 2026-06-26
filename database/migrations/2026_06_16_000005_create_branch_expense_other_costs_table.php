<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('branch_expense_other_costs')) {
            return;
        }

        Schema::create('branch_expense_other_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_expense_id')->constrained('branch_expenses')->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->unsignedBigInteger('expense_type_id')->nullable()->index();
            $table->string('title');
            $table->string('category')->default('other')->index();
            $table->decimal('amount', 14, 2)->default(0);
            $table->string('payment_cycle')->nullable();
            $table->date('payment_date')->nullable()->index();
            $table->date('due_date')->nullable()->index();
            $table->string('vendor')->nullable();
            $table->string('invoice_no')->nullable();
            $table->string('status')->default('open')->index();
            $table->text('notes')->nullable();
            $table->string('document')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branch_expense_other_costs');
    }
};
