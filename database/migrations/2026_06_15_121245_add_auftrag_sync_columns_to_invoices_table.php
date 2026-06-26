<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (!Schema::hasColumn('invoices', 'source_offer_detail_id')) {
                $table->unsignedBigInteger('source_offer_detail_id')->nullable()->after('offer_detail_id');
            }

            if (!Schema::hasColumn('invoices', 'source_offer_items_hash')) {
                $table->string('source_offer_items_hash', 64)->nullable()->after('source_offer_detail_id');
            }

            if (!Schema::hasColumn('invoices', 'source_offer_synced_at')) {
                $table->timestamp('source_offer_synced_at')->nullable()->after('source_offer_items_hash');
            }

            if (!Schema::hasColumn('invoices', 'source_offer_updated_at')) {
                $table->timestamp('source_offer_updated_at')->nullable()->after('source_offer_synced_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            foreach ([
                'source_offer_detail_id',
                'source_offer_items_hash',
                'source_offer_synced_at',
                'source_offer_updated_at',
            ] as $column) {
                if (Schema::hasColumn('invoices', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};