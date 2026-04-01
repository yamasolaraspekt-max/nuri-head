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
        Schema::create('checklist_rooms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('story_id');
            $table->string('unit');
            $table->decimal('room_size', 8, 2);  // Room size with precision            
            $table->string('heating_type');
            $table->timestamps();

            $table->foreign('story_id')->references('id')->on('checklist_apartments')->onDelete('cascade'); 
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 


        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checklist_rooms');
    }
};
