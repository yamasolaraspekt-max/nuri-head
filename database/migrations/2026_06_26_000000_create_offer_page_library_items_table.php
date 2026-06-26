<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offer_page_library_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('article_group_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();

            $table->string('title')->nullable();
            $table->text('description')->nullable();

            $table->string('file_disk')->default('public');
            $table->string('file_path');
            $table->string('file_url')->nullable();
            $table->string('original_name')->nullable();
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['article_group_id', 'product_id', 'is_active'], 'opl_items_scope_idx');

            $table->foreign('article_group_id')
                ->references('id')
                ->on('article_groups')
                ->nullOnDelete();

            $table->foreign('product_id')
                ->references('id')
                ->on('products')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_page_library_items');
    }
};
