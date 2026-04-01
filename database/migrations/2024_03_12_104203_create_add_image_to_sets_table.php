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
        Schema::create('add_image_to_sets', function (Blueprint $table) {
            $table->id(); 

            $table->unsignedBigInteger('master_set_id');
            $table->unsignedBigInteger('product_id');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade');
            $table->string('name');
            $table->string('image');
            $table->string('status')->default('Published');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('add_image_to_sets');
    }
};
