<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('climate_locations', function (Blueprint $table) {
            $table->id();

            $table->string('location_mapping_id')->nullable();
            $table->string('postcode', 20);
            $table->string('country_code', 10)->nullable();
            $table->string('location_type', 50)->nullable();

            $table->unsignedBigInteger('mapping_version')->nullable();
            $table->string('mapping_version_name')->nullable();
            $table->text('mapping_version_description')->nullable();

            $table->string('name')->nullable();
            $table->decimal('lat', 10, 6)->nullable();
            $table->decimal('lon', 10, 6)->nullable();
            $table->decimal('elevation', 10, 2)->nullable();

            $table->string('station_01_id')->nullable();
            $table->string('station_02_id')->nullable();
            $table->string('station_03_id')->nullable();

            $table->decimal('distance_station_01', 10, 2)->nullable();
            $table->decimal('distance_station_02', 10, 2)->nullable();
            $table->decimal('distance_station_03', 10, 2)->nullable();

            $table->timestamps();

            $table->index(['postcode', 'country_code'], 'cl_postcode_country_idx');

            $table->index(
                ['station_01_id', 'station_02_id', 'station_03_id'],
                'cl_station_ids_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('climate_locations');
    }
};