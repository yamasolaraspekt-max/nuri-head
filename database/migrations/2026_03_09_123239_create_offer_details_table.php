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
        Schema::create('offer_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            
            // Branding & Styling
            $table->string('brand_color')->nullable();
            $table->string('brand_mode')->default('text'); // text or image
            $table->string('company_name')->nullable();
            
            // The "Meat" of the Offer - Stored as JSON
            $table->longText('cover_text')->nullable();
            $table->json('sections'); // Full hierarchy of items, sets, and labor
            $table->json('canvas_images'); // Positions and sources of floating images
            
            // Totals Snapshot
            $table->decimal('total_net', 15, 2);
            $table->decimal('tax_rate', 5, 2);
            $table->decimal('total_gross', 15, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_details');
    }
};
