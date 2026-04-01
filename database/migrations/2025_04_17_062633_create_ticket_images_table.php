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
        Schema::create('ticket_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->string('stage'); 
            $table->string('image_name')->nullable();
            $table->string('image'); 
            $table->string('file_type')->nullable();  
            $table->string('status')->nullable();  
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('problems')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_images');
    }
};
