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
       if (!Schema::hasTable('product_types')) {
            Schema::create('product_types', function (Blueprint $table) {
                $table->id();
                $table->integer('product_id');
                $table->integer('distributor_id')->nullable();
                $table->string('article');
                $table->integer('ean')->nullable();
                $table->integer('serial')->nullable();
                $table->string('type');
                $table->string('available');
                $table->string('return');
                $table->string('return_fee');
                $table->longText('description')->nullable();
                $table->integer('payment_method_id')->nullable();
                $table->integer('payment_method_price')->nullable();
                $table->integer('purchase_price')->nullable();
                $table->string('price_unit')->nullable();
                $table->string('quantity_unit')->nullable();
                $table->string('package_unit')->nullable();
                $table->string('price_type')->nullable();
                $table->integer('plus_price')->nullable();
                $table->integer('tax')->nullable();
                $table->integer('installation_id')->nullable();
                $table->string('image')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_types');
    }
};
