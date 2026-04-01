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
        Schema::create('economic_calculations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');
            $table->decimal('solar_total_cost', 10, 2)->nullable();
            $table->decimal('solar_savings', 10, 2)->nullable();
            $table->decimal('solar_roi_years', 5, 2)->nullable();
            $table->decimal('wp_total_cost', 10, 2)->nullable();
            $table->decimal('wp_savings', 10, 2)->nullable();
            $table->decimal('wp_roi_years', 5, 2)->nullable();
            $table->decimal('combined_savings', 10, 2)->nullable();
            $table->decimal('combined_roi', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('lead_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_calculations');
    }
};
