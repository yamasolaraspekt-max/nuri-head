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
        Schema::create('set_paragraphs', function (Blueprint $table) {
            $table->id(); 
            $table->unsignedBigInteger('master_id');  
            $table->foreign('master_id')->references('id')->on('product_master_sets')->onDelete('cascade');
            $table->longText('content');
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('set_paragraphs');
    }
};
