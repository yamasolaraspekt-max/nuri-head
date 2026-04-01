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
        Schema::create('building_type_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('building_type_id');
            $table->foreign('building_type_id')->references('id')->on('building_types')->onDelete('cascade');
            $table->integer('size')->nullable();
            $table->integer('value')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_type_values');
    }
};
