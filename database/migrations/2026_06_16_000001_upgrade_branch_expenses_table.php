<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('branch_expenses')) {
            Schema::create('branch_expenses', function (Blueprint $table) {
                $table->id();
                $table->foreignId('branch_id')->constrained('branches')->cascadeOnDelete();
                $table->unsignedSmallInteger('year')->index();
                $table->date('period_start')->nullable()->index();
                $table->date('period_end')->nullable()->index();
                $table->decimal('rent_total', 14, 2)->default(0);
                $table->decimal('insurance_total', 14, 2)->default(0);
                $table->decimal('employee_total', 14, 2)->default(0);
                $table->decimal('asset_total', 14, 2)->default(0);
                $table->decimal('machine_total', 14, 2)->default(0);
                $table->decimal('installment_total', 14, 2)->default(0);
                $table->decimal('other_total', 14, 2)->default(0);
                $table->decimal('total', 14, 2)->default(0);
                $table->string('status')->default('active')->index();
                $table->text('notes')->nullable();
                $table->unsignedBigInteger('created_by')->nullable();
                $table->unsignedBigInteger('updated_by')->nullable();
                $table->timestamps();
                $table->index(['branch_id', 'year']);
            });

            return;
        }

        Schema::table('branch_expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('branch_expenses', 'period_start')) {
                $table->date('period_start')->nullable()->after('year')->index();
            }
            if (!Schema::hasColumn('branch_expenses', 'period_end')) {
                $table->date('period_end')->nullable()->after('period_start')->index();
            }
            foreach (['rent_total', 'insurance_total', 'employee_total', 'asset_total', 'machine_total', 'installment_total', 'other_total'] as $column) {
                if (!Schema::hasColumn('branch_expenses', $column)) {
                    $table->decimal($column, 14, 2)->default(0)->after('period_end');
                }
            }
            if (!Schema::hasColumn('branch_expenses', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            if (!Schema::hasColumn('branch_expenses', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('notes');
            }
            if (!Schema::hasColumn('branch_expenses', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }
            if (!Schema::hasColumn('branch_expenses', 'created_at')) {
                $table->timestamps();
            }
        });
    }

    public function down(): void
    {
        // Safe upgrade migration: no destructive rollback.
    }
};
