<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('climate_evaluation_rows', function (Blueprint $table) {
            $table->id();

            $table->string('evaluation_id')->nullable();
            $table->string('evaluation_name')->nullable();

            $table->string('postcode', 20)->nullable();
            $table->string('country_code', 10)->nullable();
            $table->string('location_name')->nullable();
            $table->string('location_station_mapping_id')->nullable();

            $table->string('station_id')->nullable();

            $table->string('quantity_code')->nullable();       // Code_Quantity
            $table->string('data_type_code')->nullable();      // Code_DataType

            $table->string('orientation_code', 10)->nullable();
            $table->string('orientation_name')->nullable();
            $table->decimal('orientation_degree', 8, 2)->nullable();
            $table->decimal('inclination_degree', 8, 2)->nullable();

            $table->json('lta_values')->nullable();    // M_LTA_01..12
            $table->json('period_values')->nullable(); // M_2024_06.. etc
            $table->json('meta')->nullable();          // full source row

            $table->timestamps();

            $table->index(['postcode', 'station_id']);
            $table->index(['evaluation_id', 'quantity_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('climate_evaluation_rows');
    }
};