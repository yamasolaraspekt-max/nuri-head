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
        Schema::create('building_types', function (Blueprint $table) {
            $table->id();
            $table->string('building_type');
            $table->string('status')->nullable();
            $table->integer('start_year')->nullable();
            $table->integer('end_year')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_types');
    }
};
