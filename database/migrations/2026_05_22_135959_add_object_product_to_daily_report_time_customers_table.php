<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('daily_report_time_customers')) {
            return;
        }

        Schema::table('daily_report_time_customers', function (Blueprint $table) {
            if (!Schema::hasColumn('daily_report_time_customers', 'alternative_id')) {
                $table->unsignedBigInteger('alternative_id')->nullable()->after('customer_id');
            }

            if (!Schema::hasColumn('daily_report_time_customers', 'lead_product_list_id')) {
                $table->unsignedBigInteger('lead_product_list_id')->nullable()->after('alternative_id');
            }

            if (!Schema::hasColumn('daily_report_time_customers', 'product_id')) {
                $table->unsignedBigInteger('product_id')->nullable()->after('lead_product_list_id');
            }

            if (!Schema::hasColumn('daily_report_time_customers', 'share_start_time')) {
                $table->time('share_start_time')->nullable()->after('share_percent');
            }

            if (!Schema::hasColumn('daily_report_time_customers', 'share_end_time')) {
                $table->time('share_end_time')->nullable()->after('share_start_time');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('daily_report_time_customers')) {
            return;
        }

        Schema::table('daily_report_time_customers', function (Blueprint $table) {
            foreach ([
                'product_id',
                'lead_product_list_id',
                'alternative_id',
                'share_end_time',
                'share_start_time',
            ] as $column) {
                if (Schema::hasColumn('daily_report_time_customers', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};