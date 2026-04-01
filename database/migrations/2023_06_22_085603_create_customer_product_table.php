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
       if (!Schema::hasTable('customer_product')) {
                Schema::create('customer_product', function (Blueprint $table) {
                    $table->id();
                    $table->bigInteger('customer_id')->unsigned();
                    $table->foreign('customer_id')
                        ->references('id')
                        ->on('customers')
                        ->onDelete('cascade')
                        ->onUpdate('cascade');

                    $table->bigInteger('product_id')->unsigned();
                    $table->foreign('product_id')
                        ->references('id')
                        ->on('products')
                        ->onDelete('cascade')
                        ->onUpdate('cascade');

                    $table->timestamps();
                });
            }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_product');
    }
};
