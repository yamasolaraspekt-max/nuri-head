<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {

            // =========================================================
            // 1) Productivity / EK-hour inputs (snapshot + audit)
            // =========================================================

            // snapshot factor (use your controller constant as default)
            $table->decimal('weeks_per_year', 8, 4)->default(52.1429)->after('working_days_per_week');

            // part-time support / scaling
            $table->decimal('fte_pct', 6, 2)->default(100.00)->after('weeks_per_year');

            // the "20% unproductive time" etc.
            $table->decimal('unproductive_pct', 6, 3)->default(20.000)->after('public_holidays');

            // optional: distinguish actual vs assumed absences
            $table->enum('absence_mode', ['assumed','actual'])->default('assumed')->after('unproductive_pct');

            // computed hours for THIS period (month) – consistent with period_year/month
            $table->decimal('hours_per_day', 8, 3)->nullable()->after('absence_mode');           // derived: hpw/wdpw
            $table->decimal('planned_hours_period', 10, 3)->nullable()->after('hours_per_day'); // hpw * avg_weeks_per_month
            $table->decimal('absence_hours_period', 10, 3)->nullable()->after('planned_hours_period');
            $table->decimal('effective_hours_period', 10, 3)->nullable()->after('absence_hours_period');
            $table->decimal('productive_hours_period', 10, 3)->nullable()->after('effective_hours_period');

            // keep your yearly field, but add consistent computed yearly fields (optional)
            $table->decimal('effective_hours_year', 12, 3)->nullable()->after('productive_hours_year');

            // =========================================================
            // 2) Overhead / Gemeinkosten (fully loaded cost)
            // =========================================================

            // percent GK, plus computed amount + fully-loaded totals
            $table->decimal('overhead_rate_pct', 6, 3)->default(0.000)->after('employer_total_monthly');
            $table->decimal('overhead_amount_monthly', 12, 2)->nullable()->after('overhead_rate_pct');

            // fully-loaded = employer_total_monthly + overhead_amount_monthly
            $table->decimal('fully_loaded_total_monthly', 12, 2)->nullable()->after('overhead_amount_monthly');

            // EK €/productive hour (derived KPI)
            $table->decimal('ek_productive_hourly', 12, 4)->nullable()->after('fully_loaded_total_monthly');

            // =========================================================
            // 3) Totals for fast reporting (components JSON stays detailed)
            // =========================================================
            $table->decimal('allowances_monthly', 12, 2)->nullable()->after('base_yearly');
            $table->decimal('bonuses_monthly', 12, 2)->nullable()->after('allowances_monthly');
            $table->decimal('deductions_other_monthly', 12, 2)->nullable()->after('bonuses_monthly');
            $table->decimal('benefits_employer_monthly', 12, 2)->nullable()->after('deductions_other_monthly');

            // =========================================================
            // 4) Normalize duplicate column (optional)
            // =========================================================
            // You currently have BOTH:
            // - employer_total_monthly
            // - total_monthly_salary
            //
            // If "total_monthly_salary" is redundant in your production data,
            // consider dropping it in a later migration after you backfill/verify.
            // (Do NOT drop automatically here.)
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->dropColumn([
                'weeks_per_year',
                'fte_pct',
                'unproductive_pct',
                'absence_mode',
                'hours_per_day',
                'planned_hours_period',
                'absence_hours_period',
                'effective_hours_period',
                'productive_hours_period',
                'effective_hours_year',
                'overhead_rate_pct',
                'overhead_amount_monthly',
                'fully_loaded_total_monthly',
                'ek_productive_hourly',
                'allowances_monthly',
                'bonuses_monthly',
                'deductions_other_monthly',
                'benefits_employer_monthly',
            ]);
        });
    }
};
