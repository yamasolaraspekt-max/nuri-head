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
       Schema::create('climate_stations', function (Blueprint $table) {
        $table->id();
        $table->string('station_id')->unique(); // DWD station id
        $table->string('name');
        $table->string('region')->nullable();
        $table->string('country_code', 10)->nullable();
        $table->decimal('lat', 10, 6)->nullable();
        $table->decimal('lon', 10, 6)->nullable();
        $table->decimal('elevation', 10, 2)->nullable();
        $table->json('mapped_postcodes')->nullable();
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('climate_stations');
    }
};
