<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profitability_calculations', function (Blueprint $table) {
            if (Schema::hasColumn('profitability_calculations', 'service_id')) {
                $table->unsignedBigInteger('service_id')->nullable()->change();
            }

            if (! Schema::hasColumn('profitability_calculations', 'config_snapshot')) {
                $table->json('config_snapshot')->nullable()->after('electricity_price_note');
            }

            if (! Schema::hasColumn('profitability_calculations', 'computed_snapshot')) {
                $table->json('computed_snapshot')->nullable()->after('config_snapshot');
            }

            if (! Schema::hasColumn('profitability_calculations', 'customer_snapshot')) {
                $table->json('customer_snapshot')->nullable()->after('computed_snapshot');
            }
        });
    }

    public function down(): void
    {
        Schema::table('profitability_calculations', function (Blueprint $table) {
            if (Schema::hasColumn('profitability_calculations', 'config_snapshot')) {
                $table->dropColumn('config_snapshot');
            }

            if (Schema::hasColumn('profitability_calculations', 'computed_snapshot')) {
                $table->dropColumn('computed_snapshot');
            }

            if (Schema::hasColumn('profitability_calculations', 'customer_snapshot')) {
                $table->dropColumn('customer_snapshot');
            }
        });

        // nullable rollback for service_id is usually skipped in down for safety
    }
};