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
        Schema::create('product_set_descriptions', function (Blueprint $table) {
            $table->id();   
            $table->unsignedBigInteger('master_set_id'); 
            $table->string('title')->nullable();
            $table->string('value')->nullable();
            $table->timestamps();
         
            $table->foreign('master_set_id')->references('id')->on('product_master_sets')->onDelete('cascade');  
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_set_descriptions');
    }
};
