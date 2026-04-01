<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('costing_sets', function (Blueprint $table) {
            if (!Schema::hasColumn('costing_sets', 'default_payroll_overhead_percent')) {
                $table->decimal('default_payroll_overhead_percent', 6, 2)->default(0)->after('aw_minutes');
            }
            if (!Schema::hasColumn('costing_sets', 'default_company_overhead_percent')) {
                $table->decimal('default_company_overhead_percent', 6, 2)->default(0)->after('default_payroll_overhead_percent');
            }

            // Optional: auto VK €/h = Vollkost * (1 + markup%)
            if (!Schema::hasColumn('costing_sets', 'default_sell_markup_percent')) {
                $table->decimal('default_sell_markup_percent', 6, 2)->default(0)->after('default_company_overhead_percent');
            }
        });
    }

    public function down(): void
    {
        Schema::table('costing_sets', function (Blueprint $table) {
            if (Schema::hasColumn('costing_sets', 'default_payroll_overhead_percent')) {
                $table->dropColumn('default_payroll_overhead_percent');
            }
            if (Schema::hasColumn('costing_sets', 'default_company_overhead_percent')) {
                $table->dropColumn('default_company_overhead_percent');
            }
            if (Schema::hasColumn('costing_sets', 'default_sell_markup_percent')) {
                $table->dropColumn('default_sell_markup_percent');
            }
        });
    }
};