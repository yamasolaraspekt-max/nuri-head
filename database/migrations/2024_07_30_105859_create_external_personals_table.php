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
        Schema::create('external_personals', function (Blueprint $table) {
            $table->id();
            $table->string('company_name');
            $table->string('price_type');
            $table->decimal('price', 10, 2);
            $table->string('tax_id');
            $table->unsignedBigInteger('product_id');
            $table->string('admin_name')->nullable();
            $table->string('email')->nullable(); // Corrected
            $table->string('phone')->nullable(); // Corrected
            $table->string('status')->nullable();
            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('article_groups')
                ->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('external_personals');
    }
};
