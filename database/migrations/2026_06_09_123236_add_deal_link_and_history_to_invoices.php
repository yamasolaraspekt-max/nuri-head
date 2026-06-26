<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                if (!Schema::hasColumn('invoices', 'deal_id')) {
                    $table->unsignedBigInteger('deal_id')->nullable()->after('object_id')->index();
                }
                if (!Schema::hasColumn('invoices', 'offer_detail_id')) {
                    $table->unsignedBigInteger('offer_detail_id')->nullable()->after('deal_id')->index();
                }
                if (!Schema::hasColumn('invoices', 'deal_limit_amount')) {
                    $table->decimal('deal_limit_amount', 12, 2)->nullable()->after('paid_at');
                }
                if (!Schema::hasColumn('invoices', 'deal_remaining_before')) {
                    $table->decimal('deal_remaining_before', 12, 2)->nullable()->after('deal_limit_amount');
                }
                if (!Schema::hasColumn('invoices', 'deal_remaining_after')) {
                    $table->decimal('deal_remaining_after', 12, 2)->nullable()->after('deal_remaining_before');
                }
            });
        }

        if (!Schema::hasTable('invoice_histories')) {
            Schema::create('invoice_histories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('invoice_id')->index();
                $table->unsignedBigInteger('employee_id')->nullable()->index();
                $table->string('event_type', 80)->index();
                $table->string('old_status', 30)->nullable();
                $table->string('new_status', 30)->nullable();
                $table->json('old_values')->nullable();
                $table->json('new_values')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('invoice_histories')) {
            Schema::dropIfExists('invoice_histories');
        }

        if (Schema::hasTable('invoices')) {
            Schema::table('invoices', function (Blueprint $table) {
                foreach (['deal_remaining_after', 'deal_remaining_before', 'deal_limit_amount', 'offer_detail_id', 'deal_id'] as $column) {
                    if (Schema::hasColumn('invoices', $column)) {
                        try {
                            $table->dropColumn($column);
                        } catch (Throwable $e) {
                            // keep migration rollback tolerant for older MySQL setups
                        }
                    }
                }
            });
        }
    }
};
