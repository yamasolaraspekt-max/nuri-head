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
     Schema::create('customer_room_dimensions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); // Foreign key to customers table 
            $table->string('dimension_type');          // To store whether it’s Tür or Wand
            $table->integer('room_number');
            $table->decimal('width', 10, 2);            // Width of the room
            $table->decimal('height', 10, 2);           // Height of the room
            $table->decimal('ceiling_height', 10, 2)->nullable(); // Optional ceiling height
            $table->string('stair_form')->nullable();  // Optional stair form (nullable)
            $table->decimal('stair_width', 10, 2)->nullable(); // Optional stair width (nullable)
            $table->string('room_story')->nullable();  // Optional room story (nullable)
            $table->timestamps();                      // Timestamps for created_at and updated_at

            // Foreign keys with cascading delete
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_room_dimensions');
    }
};
