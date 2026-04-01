<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('invoice_id')->index();

            // Your products table = article_groups
            $table->unsignedBigInteger('product_id')->nullable()->index();

            $table->string('title', 255);
            $table->text('description')->nullable();

            $table->decimal('qty', 10, 2)->default(1);
            $table->string('unit', 30)->nullable();

            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('tax_rate', 6, 3)->default(0);
            $table->decimal('line_total', 12, 2)->default(0);

            $table->unsignedInteger('sort_order')->default(0);

            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
