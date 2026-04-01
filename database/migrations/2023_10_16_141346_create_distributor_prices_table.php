<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('distributor_prices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('distributor_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('discount_group_id')->nullable();

            $table->string('article_no')->nullable();

            // money & percent as DECIMAL
            $table->decimal('discount_price', 10, 2)->nullable();      // Rabatt in €
            $table->decimal('discount_percent', 5, 2)->nullable();      // Rabatt in %
            $table->decimal('price', 10, 2)->nullable();                // UVP
            $table->decimal('purchase_price', 10, 2)->nullable();       // EK

            $table->date('price_date')->nullable();
            $table->string('availability')->nullable();
            $table->string('status')->default('Published');

            $table->timestamps();

            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade'); 
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); 
            $table->foreign('discount_group_id')->references('id')->on('discount_groups')->onDelete('cascade'); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_prices');
    }
};
