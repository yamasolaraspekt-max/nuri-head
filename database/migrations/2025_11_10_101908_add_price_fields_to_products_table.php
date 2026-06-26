<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'retail_price')) {
                $table->decimal('retail_price', 12, 2)->nullable()->after('noise_level_db');
            }

            if (!Schema::hasColumn('products', 'purchase_price')) {
                $table->decimal('purchase_price', 12, 2)->nullable()->after('retail_price');
            }

            if (!Schema::hasColumn('products', 'vat_percent')) {
                $table->decimal('vat_percent', 5, 2)->default(19)->after('purchase_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'vat_percent')) {
                $table->dropColumn('vat_percent');
            }

            if (Schema::hasColumn('products', 'purchase_price')) {
                $table->dropColumn('purchase_price');
            }

            if (Schema::hasColumn('products', 'retail_price')) {
                $table->dropColumn('retail_price');
            }
        });
    }
};