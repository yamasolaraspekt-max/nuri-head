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
        Schema::create('offer_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the template
            $table->unsignedBigInteger('created_by'); // Employee ID
            
            // Reusable Document Data
            $table->string('brand_color')->nullable();
            $table->string('brand_mode')->default('text');
            $table->string('brand_logo_url')->nullable();
            $table->string('company_name')->nullable();
            
            $table->longText('cover_text_html')->nullable();
            $table->json('sections')->nullable();
            $table->json('placed_images')->nullable();

            $table->foreign('created_by')->references('id')->on('employees')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_templates');
    }
};
