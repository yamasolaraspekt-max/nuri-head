<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('offer_folder_library_pages', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('offer_id')->nullable()->index();
            $table->unsignedBigInteger('offer_folder_id')->nullable()->index();
            $table->unsignedBigInteger('offer_detail_id')->nullable()->index();
            $table->unsignedBigInteger('offer_page_library_item_id')->nullable()->index();

            $table->unsignedBigInteger('article_group_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('created_by')->nullable()->index();

            $table->string('title')->nullable();
            $table->string('file_url')->nullable();

            // after_cover, after_roof, before_positions, after_positions, before_final, end
            $table->string('page_position', 50)->default('after_roof')->index();
            $table->unsignedInteger('sort_order')->default(0)->index();
            $table->boolean('is_enabled')->default(true)->index();

            $table->json('meta')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['offer_folder_id', 'page_position', 'sort_order'], 'oflp_order_idx');

            $table->foreign('offer_id')
                ->references('id')
                ->on('offers')
                ->cascadeOnDelete();

            $table->foreign('offer_folder_id')
                ->references('id')
                ->on('offer_folders')
                ->cascadeOnDelete();

            $table->foreign('offer_detail_id')
                ->references('id')
                ->on('offer_details')
                ->nullOnDelete();

            $table->foreign('offer_page_library_item_id')
                ->references('id')
                ->on('offer_page_library_items')
                ->nullOnDelete();

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
        Schema::dropIfExists('offer_folder_library_pages');
    }
};
