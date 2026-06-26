<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('product_histories', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('product_id')->index();
            $table->unsignedBigInteger('changed_by')->nullable()->index();

            $table->string('action')->default('updated');
            // created, updated, deleted

            $table->json('changed_fields')->nullable();
            $table->json('old_values')->nullable();
            $table->json('new_values')->nullable();

            $table->timestamps();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_histories');
    }
};