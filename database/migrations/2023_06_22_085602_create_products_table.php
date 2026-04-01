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
       if (!Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->string('article_no')->nullable();
                $table->string('ean')->nullable();
                $table->unsignedBigInteger('discount_group')->nullable();
                $table->unsignedBigInteger('article_group')->nullable();
                $table->unsignedBigInteger('sub_article')->nullable();
                $table->unsignedBigInteger('measure_unit')->nullable();
                $table->unsignedBigInteger('brand_id')->nullable();
                $table->string('product');
                $table->string('model')->nullable();
                $table->string('category')->default('Produkt');
                $table->string('roof_type')->default('none');
                $table->string('color')->nullable();
                $table->string('price_unit')->nullable();
                $table->string('package_unit')->nullable();
                $table->longText('short_description')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();

                $table->foreign('brand_id')->references('id')->on('brands')->onDelete('cascade');
                $table->foreign('article_group')->references('id')->on('article_groups')->onDelete('cascade');
                $table->foreign('sub_article')->references('id')->on('sub_article_groups')->onDelete('cascade');
                $table->foreign('measure_unit')->references('id')->on('measures')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
