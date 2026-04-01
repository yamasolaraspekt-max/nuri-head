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
        Schema::create('lead_product_checklist_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_product_list_id');
            $table->unsignedBigInteger('product_formula_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('alternative_id');
            $table->unsignedBigInteger('product_id');
            $table->string('section_name');
            $table->json('filled_values');
            $table->json('formula_snapshot'); // 💾 stores field structure as it was when filled
            $table->integer('formula_version'); // 🧠 store version used
            $table->timestamps();
        
            $table->foreign('lead_product_list_id')->references('id')->on('lead_product_lists')->onDelete('cascade');
            $table->foreign('product_formula_id')->references('id')->on('product_formulas')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_product_checklist_values');
    }
};
