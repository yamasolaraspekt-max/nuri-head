<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('daily_report_time_customers')) {
            return;
        }

        if (!Schema::hasColumn('daily_report_time_customers', 'alternative_id')) {
            Schema::table('daily_report_time_customers', function (Blueprint $table) {
                $table->unsignedBigInteger('alternative_id')->nullable()->after('customer_id');
            });
        }

        if (!Schema::hasColumn('daily_report_time_customers', 'lead_product_list_id')) {
            Schema::table('daily_report_time_customers', function (Blueprint $table) {
                $table->unsignedBigInteger('lead_product_list_id')->nullable()->after('alternative_id');
            });
        }

        if (!Schema::hasColumn('daily_report_time_customers', 'product_id')) {
            Schema::table('daily_report_time_customers', function (Blueprint $table) {
                $table->unsignedBigInteger('product_id')->nullable()->after('lead_product_list_id');
            });
        }

        if (!Schema::hasColumn('daily_report_time_customers', 'share_start_time')) {
            Schema::table('daily_report_time_customers', function (Blueprint $table) {
                $table->time('share_start_time')->nullable()->after('share_percent');
            });
        }

        if (!Schema::hasColumn('daily_report_time_customers', 'share_end_time')) {
            Schema::table('daily_report_time_customers', function (Blueprint $table) {
                $table->time('share_end_time')->nullable()->after('share_start_time');
            });
        }

        if (!$this->indexExists('daily_report_time_customers', 'drtc_customer_object_idx')) {
            Schema::table('daily_report_time_customers', function (Blueprint $table) {
                $table->index(['customer_id', 'alternative_id'], 'drtc_customer_object_idx');
            });
        }

        if (!$this->indexExists('daily_report_time_customers', 'drtc_lpl_idx')) {
            Schema::table('daily_report_time_customers', function (Blueprint $table) {
                $table->index('lead_product_list_id', 'drtc_lpl_idx');
            });
        }

        if (!$this->indexExists('daily_report_time_customers', 'drtc_product_idx')) {
            Schema::table('daily_report_time_customers', function (Blueprint $table) {
                $table->index('product_id', 'drtc_product_idx');
            });
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('daily_report_time_customers')) {
            return;
        }

        foreach ([
            'drtc_customer_object_idx',
            'drtc_lpl_idx',
            'drtc_product_idx',
        ] as $indexName) {
            if ($this->indexExists('daily_report_time_customers', $indexName)) {
                Schema::table('daily_report_time_customers', function (Blueprint $table) use ($indexName) {
                    $table->dropIndex($indexName);
                });
            }
        }

        foreach ([
            'share_end_time',
            'share_start_time',
            'product_id',
            'lead_product_list_id',
            'alternative_id',
        ] as $column) {
            if (Schema::hasColumn('daily_report_time_customers', $column)) {
                Schema::table('daily_report_time_customers', function (Blueprint $table) use ($column) {
                    $table->dropColumn($column);
                });
            }
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', $table)
            ->where('index_name', $index)
            ->exists();
    }
};