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
        Schema::create('product_sub_sets', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('master_set_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('main_product');
            $table->integer('product_count');
            $table->integer('measure_unit');
            $table->integer('distributor_id');
            $table->double('retail_price', 10.2)->nullable();
            $table->integer('discount_group')->nullable();
            $table->double('purchase_price', 10.2)->nullable();
            $table->double('total', 10.2)->nullable();
            $table->string('status')->nullable();
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade'); 
            $table->foreign('main_product')->references('id')->on('add_product_to_sets')->onDelete('cascade'); 
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_sub_sets');
    }
};
