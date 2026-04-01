<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('inventory_id');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->unsignedBigInteger('used_by')->nullable();

            $table->string('type')->default('used'); // used, created, updated, manual_adjustment
            $table->decimal('quantity_before', 12, 2)->default(0);
            $table->decimal('quantity_used', 12, 2)->default(0);
            $table->decimal('quantity_after', 12, 2)->default(0);

            $table->string('usage_location')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->nullOnDelete();
            $table->foreign('customer_id')->references('id')->on('new_leads')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_histories');
    }
};