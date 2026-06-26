<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('supplier_connection_mappings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('supplier_connection_id')
                ->constrained('supplier_connections')
                ->cascadeOnDelete();

            $table->string('source_field');

            $table->string('target_table')->default('products');
            // products, brands, distributors, distributor_prices

            $table->string('target_field');

            $table->string('transformer')->nullable();
            // text, decimal, integer, html_strip, uppercase, lowercase, boolean

            $table->text('default_value')->nullable();

            $table->boolean('is_required')->default(false);
            $table->boolean('is_active')->default(true);

            $table->integer('sort_order')->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('supplier_connection_mappings');
    }
};