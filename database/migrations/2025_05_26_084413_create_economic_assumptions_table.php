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
        Schema::create('economic_assumptions', function (Blueprint $table) {
            $table->id();
            $table->decimal('electricity_price', 5, 2); // Strompreis in €/kWh
            $table->decimal('gas_price', 5, 2)->nullable(); // Gaspreis in €/kWh
            $table->decimal('price_increase_rate', 5, 2); // Strompreissteigerung pro Jahr in %
            $table->decimal('inflation_rate', 5, 2); // Inflation für Rückrechnung
            $table->integer('lifespan'); // Betrachtungszeitraum in Jahren
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('economic_assumptions');
    }
};
