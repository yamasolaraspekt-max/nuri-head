<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('master_set_components', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('master_set_id');

            // main/sub hierarchy
            $table->unsignedBigInteger('parent_id')->nullable();

            $table->unsignedBigInteger('product_id');

            // selected supplier for this product
            $table->unsignedBigInteger('distributor_id')->nullable();
            $table->unsignedBigInteger('distributor_price_id')->nullable();

            // snapshot price at time of saving
            $table->decimal('unit_price', 10, 2)->nullable();

            $table->decimal('qty', 10, 2)->default(1);
            $table->longText('description')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->foreign('master_set_id')
                ->references('id')->on('master_sets')
                ->onDelete('cascade');

            $table->foreign('parent_id')
                ->references('id')->on('master_set_components')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');

            $table->foreign('distributor_id')
                ->references('id')->on('distributors')
                ->nullOnDelete();

            $table->foreign('distributor_price_id')
                ->references('id')->on('distributor_prices')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_set_components');
    }
};
