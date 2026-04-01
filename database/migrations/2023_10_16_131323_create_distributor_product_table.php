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
        Schema::create('distributor_product', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('distributor_id')->unsigned();
            $table->foreign('distributor_id')->references('id')->on('distributors')->onDelete('cascade')->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**0
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('distributor_product');
    }
};
