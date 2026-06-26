<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_notes', 'destination_type')) {
                $table->enum('destination_type', ['warehouse', 'customer'])
                    ->default('warehouse')
                    ->after('delivered_from');
            }

            if (!Schema::hasColumn('delivery_notes', 'customer_id')) {
                $table->unsignedBigInteger('customer_id')->nullable()->after('branch_id');
                $table->foreign('customer_id')->references('id')->on('new_leads')->nullOnDelete();
            }

            if (!Schema::hasColumn('delivery_notes', 'alternative_id')) {
                $table->unsignedBigInteger('alternative_id')->nullable()->after('customer_id');
                $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->nullOnDelete();
            }

            if (!Schema::hasColumn('delivery_notes', 'lead_product_list_id')) {
                $table->unsignedBigInteger('lead_product_list_id')->nullable()->after('alternative_id');
                $table->foreign('lead_product_list_id')->references('id')->on('lead_product_lists')->nullOnDelete();
            }

            if (!Schema::hasColumn('delivery_notes', 'deal_id')) {
                $table->unsignedBigInteger('deal_id')->nullable()->after('lead_product_list_id');
                $table->foreign('deal_id')->references('id')->on('deals')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_notes', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_notes', 'deal_id')) {
                $table->dropForeign(['deal_id']);
                $table->dropColumn('deal_id');
            }

            if (Schema::hasColumn('delivery_notes', 'lead_product_list_id')) {
                $table->dropForeign(['lead_product_list_id']);
                $table->dropColumn('lead_product_list_id');
            }

            if (Schema::hasColumn('delivery_notes', 'alternative_id')) {
                $table->dropForeign(['alternative_id']);
                $table->dropColumn('alternative_id');
            }

            if (Schema::hasColumn('delivery_notes', 'customer_id')) {
                $table->dropForeign(['customer_id']);
                $table->dropColumn('customer_id');
            }

            if (Schema::hasColumn('delivery_notes', 'destination_type')) {
                $table->dropColumn('destination_type');
            }
        });
    }
};