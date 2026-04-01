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
        Schema::create('offer_reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('subject_id');
            $table->unsignedBigInteger('ending_id');
            $table->unsignedBigInteger('cover_id');
            $table->unsignedBigInteger('master_set_id');
            $table->unsignedBigInteger('article_group');
            $table->unsignedBigInteger('sub_article');

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('offer_greetings')->onDelete('cascade');
            $table->foreign('ending_id')->references('id')->on('offer_greetings')->onDelete('cascade');
            $table->foreign('cover_id')->references('id')->on('offer_covers')->onDelete('cascade');
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade');
            $table->foreign('article_group')->references('id')->on('article_groups')->onDelete('cascade');
            $table->foreign('sub_article')->references('id')->on('sub_article_groups')->onDelete('cascade');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_reports');
    }
};
