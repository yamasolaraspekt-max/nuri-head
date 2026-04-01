<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('customer_product_info_media', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('customer_product_info_id');

            $table->string('disk')->default('public');
            $table->string('path'); // e.g. customer-products/123/uuid.pdf
            $table->string('stored_name')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime')->nullable();
            $table->unsignedBigInteger('size')->nullable();

            $table->string('type', 20)->default('file')->index(); // image|pdf|file
            $table->unsignedBigInteger('uploaded_by')->nullable();
            $table->integer('sort_order')->default(0);

            $table->timestamps();

            $table->foreign('customer_product_info_id')
                ->references('id')
                ->on('customer_product_infos')
                ->onDelete('cascade');

            $table->index(['customer_product_info_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customer_product_info_media');
    }
};
