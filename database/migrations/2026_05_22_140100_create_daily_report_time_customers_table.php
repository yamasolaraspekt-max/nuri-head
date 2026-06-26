<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('daily_report_time_customers')) {
            return;
        }

        // Base table for the DailyReportTimeCustomer model. The original
        // "create" migration was mislabeled (it modified the products table),
        // so this table was never created even though later migrations ALTER it.
        Schema::create('daily_report_time_customers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_time_id')->index();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('alternative_id')->nullable();
            $table->unsignedBigInteger('lead_product_list_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->decimal('share_hours', 10, 2)->nullable();
            $table->decimal('share_percent', 5, 2)->nullable();
            $table->time('share_start_time')->nullable();
            $table->time('share_end_time')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
        });

        // Note: the drtc_* composite indexes are added by the
        // 2026_05_22_140145 migration, so they are intentionally omitted here.
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_report_time_customers');
    }
};
