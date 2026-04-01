<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_set_cart_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained('master_set_carts')
                ->cascadeOnDelete();

            $table->foreignId('section_id')
                ->nullable()
                ->constrained('master_set_cart_sections')
                ->nullOnDelete();

            $table->foreignId('parent_id')
                ->nullable()
                ->constrained('master_set_cart_items')
                ->nullOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            $table->foreignId('distributor_id')
                ->nullable()
                ->constrained('distributors')
                ->nullOnDelete();

            $table->foreignId('distributor_price_id')
                ->nullable()
                ->constrained('distributor_prices')
                ->nullOnDelete();

            $table->string('source_type', 30)->default('product')->index();
            // product | manual | copied_component

            $table->string('node_type', 20)->default('main')->index();
            // main | sub

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->string('article_no')->nullable();
            $table->string('measure')->nullable();
            $table->string('vpe')->nullable();
            $table->string('price_unit')->nullable();

            $table->decimal('qty', 12, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('purchase_price', 12, 2)->default(0);
            $table->decimal('margin', 12, 2)->default(0);
            $table->decimal('skonto', 12, 2)->default(0);

            $table->string('payment_terms')->nullable();
            $table->string('availability')->nullable();

            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->unsignedInteger('depth')->default(0);

            $table->boolean('is_stammartikel')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_collapsed')->default(false);

            $table->timestamps();

            $table->index(['cart_id', 'section_id']);
            $table->index(['cart_id', 'parent_id']);
            $table->index(['cart_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_set_cart_items');
    }
};