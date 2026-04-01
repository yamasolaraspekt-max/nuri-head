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
        Schema::create('imported_ids_items', function (Blueprint $table) {
            $table->id();
            $table->string('article_no')->nullable();
            $table->string('batch_id')->nullable();
            $table->string('ean')->nullable();
            $table->string('short_text')->nullable();
            $table->string('long_text')->nullable();
            $table->decimal('qty', 10, 3)->nullable();
            $table->string('unit')->nullable();
            $table->decimal('offer_price', 10, 2)->nullable();
            $table->decimal('net_price', 10, 2)->nullable();
            $table->decimal('vat', 5, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('imported_ids_items');
    }
};
