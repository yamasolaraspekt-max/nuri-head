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
        Schema::create('asset_sets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBiginteger('asset_id');
            $table->unsignedBigInteger('master_id');
            $table->string('name')->nullable();
            $table->integer('count')->nullable();
            $table->integer('total_price')->nullable();

            $table->timestamps();

            $table->foreign('asset_id')->references('id')->on('assets')->cascadeOnDelete();
            $table->foreign('master_id')->references('id')->on('product_master_sets')->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asset_sets');
    }
};
