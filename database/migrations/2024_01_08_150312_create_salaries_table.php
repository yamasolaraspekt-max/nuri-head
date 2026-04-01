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
        Schema::create('salaries', function (Blueprint $table) {
             $table->id();

            // Who
            $table->foreignId('emp_id')->constrained('employees')->cascadeOnDelete();

            // Accounting Period (unique per employee + month)
            $table->unsignedSmallInteger('period_year');   // e.g. 2025
            $table->unsignedTinyInteger('period_month');   // 1..12

            // Contract + Working pattern
            $table->enum('contract_type', ['monthly','weekly','hourly','yearly'])->default('monthly');
            $table->unsignedSmallInteger('working_hours_per_week')->default(40);
            $table->unsignedTinyInteger('working_days_per_week')->default(5);

            // Base pay (exactly ONE is the authoritative input for this sheet;
            // others are derived; we still persist all for reporting)
            $table->decimal('base_hourly', 12, 2)->nullable();
            $table->decimal('base_weekly', 12, 2)->nullable();
            $table->decimal('base_monthly', 12, 2)->nullable();
            $table->decimal('base_yearly', 12, 2)->nullable();

            // Gross / Net (per month) — enterprise reporting
            $table->boolean('is_taxed')->default(true); // if false -> gross==net, tax=0
            $table->enum('tax_source', ['employee_profile','manual'])->default('employee_profile');
            $table->string('tax_class', 8)->nullable();           // mirror snapshot (DE: I–VI)
            $table->string('health_insurer')->nullable();         // snapshot for audit
            $table->decimal('income_tax_rate_pct', 6, 3)->nullable();    // e.g. 21.000 (%)
            $table->decimal('social_rate_employee_pct', 6, 3)->nullable(); // sum of employee-side rates
            $table->decimal('social_rate_employer_pct', 6, 3)->nullable(); // employer-side total

            $table->decimal('gross_monthly', 12, 2)->nullable();
            $table->decimal('employee_deductions_monthly', 12, 2)->nullable(); // tax+employee social
            $table->decimal('net_monthly', 12, 2)->nullable();

            // Employer cost (for “Vollkosten”)
            $table->decimal('employer_contrib_monthly', 12, 2)->nullable(); // employer social etc.
            $table->decimal('employer_total_monthly', 12, 2)->nullable();   // gross + employer_contrib
            $table->decimal('total_monthly_salary', 12, 2)->nullable();   // gross + employer_contrib

            // Absences & productivity (optional, but useful for €/productive-hour KPIs)
            $table->unsignedSmallInteger('holiday_days')->default(0);
            $table->unsignedSmallInteger('sick_days')->default(0);
            $table->unsignedSmallInteger('public_holidays')->default(0);
            $table->unsignedInteger('productive_hours_year')->nullable();

            // Free-form components (allowances, deductions, bonuses…), e.g.
            // [{ "type":"allowance","code":"MEAL","amount":50.00, "period":"monthly" }, ...]
            $table->json('components')->nullable();

            // Audit & meta
            $table->char('currency', 3)->default('EUR');
            $table->enum('status', ['draft','active','archived'])->default('active');
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->text('notes')->nullable();

            $table->timestamps();

            // Ensure a single sheet per employee per accounting month
            $table->unique(['emp_id', 'period_year', 'period_month'], 'uniq_emp_period');
            $table->index(['period_year', 'period_month']);
            

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
