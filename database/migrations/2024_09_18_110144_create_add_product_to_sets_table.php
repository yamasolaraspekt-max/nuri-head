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
        Schema::create('add_product_to_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('master_set_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('distributor_id');

            
            $table->integer('product_count');
            $table->integer('measure_unit');
            $table->double('retail_price', 10.2)->nullable();
            $table->integer('discount_group')->nullable();
            $table->double('purchase_price', 10.2)->nullable();
            $table->double('total', 10.2)->nullable();
        
            $table->timestamps();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade');
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_product_to_sets');
    }
};

