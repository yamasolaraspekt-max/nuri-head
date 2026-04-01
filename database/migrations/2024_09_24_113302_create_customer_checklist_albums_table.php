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
        Schema::create('customer_checklist_albums', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('checklist_id');
            $table->string('image_title')->nullable();
            $table->string('image_type'); //Inside Unit //outside Unit
            $table->string('image_stage')->default('before'); // Before Montage and After Montage
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('cascade'); 
            $table->foreign('checklist_id')->references('id')->on('checklists')->onDelete('cascade'); 

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_checklist_albums');
    }
};
