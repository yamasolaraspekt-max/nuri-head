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
        Schema::create('beg_fundings', function (Blueprint $table) {
            $table->id();

            // Optional: link to user
            $table->unsignedBigInteger('lead_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');

            // Customer information 
            $table->string('house_type'); 
            $table->boolean('is_owner');
            $table->boolean('is_living_inside');
            $table->integer('heating_age');   
            $table->decimal('income', 10, 2); 
            $table->integer('living_space');
            $table->integer('number_wp');
            $table->integer('installation_year');

            // Automatically evaluated flags
            $table->boolean('applies_efficiency_bonus')->default(false);
            $table->boolean('applies_speed_bonus')->default(false);
            $table->boolean('applies_income_bonus')->default(false);

            // Calculated funding percentages
            $table->decimal('base_percentage', 5, 2)->default(30.00);
            $table->decimal('efficiency_percentage', 5, 2)->default(0.00);
            $table->decimal('speed_percentage', 5, 2)->default(0.00);
            $table->decimal('income_percentage', 5, 2)->default(0.00);
            $table->decimal('total_percentage', 5, 2)->default(0.00); // Max: 70%

            // Optional funding ceiling
            $table->integer('max_funding_amount')->nullable();

            // Admin notes
            $table->string('status')->nullable(); // e.g., calculated, submitted
            $table->string('notes')->nullable();

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
        Schema::dropIfExists('beg_fundings');
    }
};
