<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('deal_notes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deal_id')->index();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('alternative_id')->nullable()->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('parent_id')->nullable()->index();
            $table->longText('description');
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->unsignedBigInteger('updated_by')->nullable()->index();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('deal_id')->references('id')->on('deals')->onDelete('cascade');
            $table->foreign('parent_id')->references('id')->on('deal_notes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('deal_notes');
    }
};