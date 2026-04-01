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
        Schema::create('building_data', function (Blueprint $table) {
            $table->id();
             $table->year('year');
            $table->decimal('u_wand', 3, 1);
            $table->decimal('u_wand_gut', 3, 1)->nullable();
            $table->decimal('u_wand_ad', 3, 2)->nullable();
            $table->decimal('u_wand_id', 3, 2)->nullable();
            $table->decimal('u_boden', 2, 1)->nullable();
            $table->decimal('u_boden_dae', 3, 2)->nullable();
            $table->decimal('u_kellerdecke', 2, 1)->nullable();
            $table->decimal('u_kg_decke_d', 3, 2)->nullable();
            $table->decimal('u_dach', 2, 1)->nullable();
            $table->decimal('u_fenster', 3, 1)->nullable(); 
            $table->decimal('u_tuer', 2, 1)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('building_data');
    }
};
