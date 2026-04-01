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
        Schema::create('customer_heating_circuits', function (Blueprint $table) {
            $table->id();
             $table->unsignedBigInteger('customer_id');  // Foreign key to the customers table 
            $table->integer('heating_circuit_number');
            $table->decimal('flow_temperature', 5, 2)->nullable();
            $table->decimal('return_flow_temperature', 5, 2)->nullable();
            $table->string('room_story')->nullable();  // Story: KG, EG, OG, DG, etc.
            $table->string('pipe_dimension')->nullable();  // Pipe dimension, e.g., 12, 14, etc.
            $table->string('pipe_material')->nullable();  // Pipe material, e.g., Kupfer, Kunststoff
            $table->timestamps();  // Timestamps for created_at and updated_at

            // Foreign key constraint
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 
       
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_heating_circuits');
    }
};
