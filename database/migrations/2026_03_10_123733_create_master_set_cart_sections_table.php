<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('master_set_cart_sections', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cart_id')
                ->constrained('master_set_carts')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('color', 20)->nullable();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_collapsed')->default(false);

            $table->timestamps();

            $table->index(['cart_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('master_set_cart_sections');
    }
};