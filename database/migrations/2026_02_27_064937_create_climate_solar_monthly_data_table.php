<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('climate_solar_monthly_data', function (Blueprint $table) {
            $table->id();

            $table->foreignId('climate_station_id')
                ->constrained('climate_stations')
                ->cascadeOnDelete();

            $table->enum('dataset_scope', ['period', 'lta']);
            $table->string('dataset_label')->nullable();
            $table->integer('year')->nullable();

            $table->tinyInteger('month_num')->nullable(); // null allowed for totals if needed
            $table->string('month', 3)->nullable();

            $table->enum('surface_type', ['horizontal', 'vertical', 'tilted']);
            $table->decimal('tilt_angle', 6, 2)->nullable(); // e.g. 45
            $table->string('orientation', 10)->nullable();   // Hor, O, SO, S, SW, W, NW, N, NO

            $table->decimal('value_kwh_m2', 10, 2)->nullable();

            $table->enum('row_kind', ['month', 'year_sum', 'heating_sum'])->default('month');

            $table->timestamps();

            $table->index([
                'climate_station_id',
                'dataset_scope',
                'surface_type',
                'orientation',
                'month_num',
            ], 'climate_solar_lookup_idx');

            $table->unique(
                [
                    'climate_station_id',
                    'dataset_scope',
                    'dataset_label',
                    'year',
                    'month_num',
                    'surface_type',
                    'tilt_angle',
                    'orientation',
                    'row_kind',
                ],
                'climate_solar_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('climate_solar_monthly_data');
    }
};