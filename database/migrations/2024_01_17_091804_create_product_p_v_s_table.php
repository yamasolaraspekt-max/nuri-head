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
        Schema::create('product_p_v_s', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('article_group_id');

            $table->string('cell_type')->nullable();
            $table->string('half_cell_module')->nullable();
            $table->integer('num_cells')->nullable();
            $table->integer('num_bypass_diodes')->nullable();
            $table->float('voltage_loss_per_bypass_diode')->nullable();
            $table->string('integrated_power_optimizer')->nullable();
            $table->string('trafo_inverter_only')->nullable();
            $table->string('cell_strands_vertical')->nullable();

            // UI Kennwerte bei STC
            $table->float('mpp_voltage')->nullable();
            $table->float('mpp_current')->nullable();
            $table->float('open_circuit_voltage')->nullable();
            $table->float('short_circuit_current')->nullable();
            $table->float('voltage_increase_before_stabilization')->nullable();
            $table->float('nominal_power')->nullable();
            $table->float('fill_factor')->nullable();
            $table->float('efficiency')->nullable();

            // UI Kennwerte bei Schwachlicht
            $table->string('low_light_model')->nullable();
            $table->float('irradiance')->nullable();
            $table->float('mpp_voltage_low_light')->nullable();
            $table->float('mpp_current_low_light')->nullable();
            $table->float('open_circuit_voltage_low_light')->nullable();
            $table->float('short_circuit_current_low_light')->nullable();
            $table->float('fill_factor_low_light')->nullable();
            $table->float('efficiency_low_light')->nullable();
            $table->string('standard_low_light_behavior')->nullable();

            // Weitere Parameter
            $table->float('temperature_coefficient_voc')->nullable();
            $table->float('temperature_coefficient_voc_pct')->nullable();
            $table->float('temperature_coefficient_isc')->nullable();
            $table->float('temperature_coefficient_isc_pct')->nullable();
            $table->float('temperature_coefficient_pmax')->nullable();
            $table->float('angle_correction_factor')->nullable();
            $table->float('max_system_voltage')->nullable();
            $table->float('bifaciality_factor')->nullable();

            // Abmessungen
            $table->float('width')->nullable();
            $table->float('height')->nullable();
            $table->float('area')->nullable();
            $table->float('depth')->nullable();
            $table->float('frame_width')->nullable();
            $table->float('weight')->nullable();

            $table->string('status')->nullable();


            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('article_group_id')->references('id')->on('article_groups')->onDelete('cascade');



            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_p_v_s');
    }
};
