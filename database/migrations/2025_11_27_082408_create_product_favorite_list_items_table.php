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
        Schema::create('product_favorite_list_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_favorite_list_id')
                ->constrained('product_favorite_lists')
                ->onDelete('cascade');

            $table->foreignId('product_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('employee_id')
                ->nullable()
                ->constrained('employees')
                ->onDelete('set null'); // who added

            $table->string('note')->nullable();

            $table->timestamps();

            // use a short explicit index name
            $table->unique(
                ['product_favorite_list_id', 'product_id'],
                'pfl_item_prod_unique'
            );
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_favorite_list_items');
    }
};
