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
        Schema::create('group_set_product_master_set', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_master_set_id')->unsigned();
            $table->foreign('product_master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade')->onUpdate('cascade');
            $table->bigInteger('group_set_id')->unsigned();
            $table->foreign('group_set_id')->references('id')->on('group_sets')->onDelete('cascade')->onUpdate('cascade');;
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('group_set_product_master_set');
    }
};
