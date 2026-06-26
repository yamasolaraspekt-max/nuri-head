<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('branch_insurances')) {
            Schema::create('branch_insurances', function (Blueprint $table) {
                $table->id();

                $table->foreignId('branch_expense_id')
                    ->nullable()
                    ->constrained('branch_expenses')
                    ->nullOnDelete();

                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('branches')
                    ->nullOnDelete();

                $table->unsignedBigInteger('expense_types_id')->nullable();

                $table->string('insurance_for')->nullable();
                $table->string('policy_number')->nullable();
                $table->string('provider')->nullable();

                $table->decimal('coverage_amount', 14, 2)->default(0);
                $table->decimal('monthly_payable', 14, 2)->default(0);

                $table->string('payment_cycle')->nullable();
                $table->unsignedTinyInteger('due_day')->nullable();

                $table->date('next_due_date')->nullable()->index();
                $table->date('payment_date')->nullable()->index();

                $table->date('start_date')->nullable();
                $table->date('end_date')->nullable()->index();

                $table->string('status')->default('active')->index();
                $table->text('notes')->nullable();
                $table->string('document')->nullable();

                $table->timestamps();
            });

            return;
        }

        Schema::table('branch_insurances', function (Blueprint $table) {
            if (!Schema::hasColumn('branch_insurances', 'branch_expense_id')) {
                $table->foreignId('branch_expense_id')
                    ->nullable()
                    ->constrained('branch_expenses')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('branch_insurances', 'branch_id')) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('branches')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('branch_insurances', 'payment_cycle')) {
                $table->string('payment_cycle')->nullable();
            }

            if (!Schema::hasColumn('branch_insurances', 'due_day')) {
                $table->unsignedTinyInteger('due_day')->nullable();
            }

            if (!Schema::hasColumn('branch_insurances', 'next_due_date')) {
                $table->date('next_due_date')->nullable()->index();
            }

            if (!Schema::hasColumn('branch_insurances', 'payment_date')) {
                $table->date('payment_date')->nullable()->index();
            }

            if (!Schema::hasColumn('branch_insurances', 'status')) {
                $table->string('status')->default('active')->index();
            }

            if (!Schema::hasColumn('branch_insurances', 'notes')) {
                $table->text('notes')->nullable();
            }

            if (!Schema::hasColumn('branch_insurances', 'document')) {
                $table->string('document')->nullable();
            }

            if (!Schema::hasColumn('branch_insurances', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        // Safe upgrade migration: no destructive rollback.
    }
};