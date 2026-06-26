<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('branch_rents')) {
            Schema::create('branch_rents', function (Blueprint $table) {
                $table->id();

                $table->foreignId('expense_details_id')
                    ->nullable()
                    ->constrained('branch_expenses')
                    ->nullOnDelete();

                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('branches')
                    ->nullOnDelete();

                $table->string('object_name');
                $table->string('object_type')->nullable();

                $table->decimal('extra_cost', 14, 2)->default(0);
                $table->decimal('rent_cost', 14, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);

                $table->string('city')->nullable();
                $table->string('street')->nullable();
                $table->string('house_no')->nullable();
                $table->string('postcode')->nullable();

                $table->string('landlord_name')->nullable();
                $table->string('landlord_contact')->nullable();

                $table->date('contract_start')->nullable();
                $table->date('contract_end')->nullable();

                $table->string('payment_cycle')->nullable();
                $table->unsignedTinyInteger('due_day')->nullable();
                $table->date('next_due_date')->nullable()->index();

                $table->string('status')->default('active')->index();
                $table->text('notes')->nullable();

                $table->timestamps();
            });

            return;
        }

        Schema::table('branch_rents', function (Blueprint $table) {
            if (!Schema::hasColumn('branch_rents', 'branch_id')) {
                $table->foreignId('branch_id')
                    ->nullable()
                    ->constrained('branches')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('branch_rents', 'object_type')) {
                $table->string('object_type')->nullable();
            }

            if (!Schema::hasColumn('branch_rents', 'landlord_name')) {
                $table->string('landlord_name')->nullable();
            }

            if (!Schema::hasColumn('branch_rents', 'landlord_contact')) {
                $table->string('landlord_contact')->nullable();
            }

            if (!Schema::hasColumn('branch_rents', 'contract_start')) {
                $table->date('contract_start')->nullable();
            }

            if (!Schema::hasColumn('branch_rents', 'contract_end')) {
                $table->date('contract_end')->nullable();
            }

            if (!Schema::hasColumn('branch_rents', 'payment_cycle')) {
                $table->string('payment_cycle')->nullable();
            }

            if (!Schema::hasColumn('branch_rents', 'due_day')) {
                $table->unsignedTinyInteger('due_day')->nullable();
            }

            if (!Schema::hasColumn('branch_rents', 'next_due_date')) {
                $table->date('next_due_date')->nullable()->index();
            }

            if (!Schema::hasColumn('branch_rents', 'status')) {
                $table->string('status')->default('active')->index();
            }

            if (!Schema::hasColumn('branch_rents', 'notes')) {
                $table->text('notes')->nullable();
            }

            if (!Schema::hasColumn('branch_rents', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        // Safe upgrade migration: no destructive rollback.
    }
};