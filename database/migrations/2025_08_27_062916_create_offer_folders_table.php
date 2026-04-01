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
        Schema::create('offer_folders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('new_leads')->cascadeOnDelete();
            $table->foreignId('alternative_id')->constrained('lead_alternative_adds')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('article_groups')->cascadeOnDelete();
            $table->foreignId('created_by')->constrained('employees')->cascadeOnDelete();

            $table->string('name')->nullable();
            $table->string('color');
            $table->longText('history')->nullable();
            $table->string('status')->nullable();

            $table->softDeletes();
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offer_folders');
    }
};
