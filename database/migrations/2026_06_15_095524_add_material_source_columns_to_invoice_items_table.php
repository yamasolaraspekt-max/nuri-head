<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            if (!Schema::hasColumn('invoice_items', 'article_product_id')) {
                $table->unsignedBigInteger('article_product_id')->nullable()->after('product_id');
            }

            if (!Schema::hasColumn('invoice_items', 'component_id')) {
                $table->unsignedBigInteger('component_id')->nullable()->after('article_product_id');
            }

            if (!Schema::hasColumn('invoice_items', 'distributor_id')) {
                $table->unsignedBigInteger('distributor_id')->nullable()->after('component_id');
            }

            if (!Schema::hasColumn('invoice_items', 'distributor_price_id')) {
                $table->unsignedBigInteger('distributor_price_id')->nullable()->after('distributor_id');
            }

            if (!Schema::hasColumn('invoice_items', 'distributor_article_no')) {
                $table->string('distributor_article_no')->nullable()->after('distributor_price_id');
            }

            if (!Schema::hasColumn('invoice_items', 'source_item_type')) {
                $table->string('source_item_type')->nullable()->after('distributor_article_no');
            }

            if (!Schema::hasColumn('invoice_items', 'source_item_id')) {
                $table->string('source_item_id')->nullable()->after('source_item_type');
            }

            if (!Schema::hasColumn('invoice_items', 'source_payload')) {
                $table->json('source_payload')->nullable()->after('source_item_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoice_items', function (Blueprint $table) {
            foreach ([
                'article_product_id',
                'component_id',
                'distributor_id',
                'distributor_price_id',
                'distributor_article_no',
                'source_item_type',
                'source_item_id',
                'source_payload',
            ] as $column) {
                if (Schema::hasColumn('invoice_items', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};