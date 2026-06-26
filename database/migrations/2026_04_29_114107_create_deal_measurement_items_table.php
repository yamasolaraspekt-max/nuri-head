<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_measurement_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('deal_measurement_id')
                ->constrained('deal_measurements')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('deal_id')->nullable()->index();
            $table->unsignedBigInteger('offer_id')->nullable()->index();
            $table->unsignedBigInteger('offer_detail_id')->nullable()->index();

            $table->string('section_title')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedInteger('depth')->default(0);

            $table->string('item_type')->nullable();
            $table->string('kind')->nullable();

            $table->unsignedBigInteger('master_set_id')->nullable()->index();
            $table->unsignedBigInteger('master_set_component_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('component_id')->nullable()->index();

            $table->string('article_no')->nullable();
            $table->string('distributor_article_no')->nullable();
            $table->unsignedBigInteger('distributor_id')->nullable()->index();
            $table->string('distributor_name')->nullable();

            $table->string('name')->nullable();
            $table->longText('description')->nullable();
            $table->string('image')->nullable();

            $table->decimal('qty_offer', 15, 4)->default(0);
            $table->decimal('qty_measurement', 15, 4)->default(0);
            $table->decimal('qty_final', 15, 4)->default(0);

            $table->string('unit')->nullable();
            $table->string('measure')->nullable();

            $table->decimal('unit_price', 15, 4)->default(0);
            $table->decimal('purchase_price', 15, 4)->default(0);
            $table->decimal('total_price', 15, 4)->default(0);

            $table->string('order_status')->nullable();
            $table->json('stock_allocation')->nullable();
            $table->json('raw_snapshot')->nullable();

            $table->boolean('is_checked')->default(false);
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['deal_measurement_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_measurement_items');
    }
};