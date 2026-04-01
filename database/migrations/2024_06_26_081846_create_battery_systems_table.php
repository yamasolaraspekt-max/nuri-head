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
        Schema::create('battery_systems', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('article_group_id');

            $table->string('ess_company')->nullable();
            $table->string('ess_name')->nullable();
            $table->text('ess_description')->nullable();
            $table->boolean('ess_available')->default(false);
            $table->string('ess_version')->nullable(); 
            $table->string('ess_user_id')->nullable();

            // Inverter Data
            $table->float('nominal_power')->nullable();
            $table->float('max_charge_power')->nullable();
            $table->float('max_discharge_power')->nullable();
            $table->string('coupling_type')->nullable();

            // Efficiency
            $table->float('ess_efficiency_0')->nullable();
            $table->float('ess_efficiency_5')->nullable();
            $table->float('ess_efficiency_10')->nullable();
            $table->float('ess_efficiency_20')->nullable();
            $table->float('ess_efficiency_30')->nullable();
            $table->float('ess_efficiency_50')->nullable();
            $table->float('ess_efficiency_75')->nullable();
            $table->float('ess_efficiency_100')->nullable();

            // Charging Strategy
            $table->float('ess_equalization_charge')->nullable();
            $table->float('ess_equalization_charge_end')->nullable();
            $table->float('ess_equalization_charge_duration')->nullable();
            $table->float('ess_equalization_charge_cycle')->nullable();
            $table->float('ess_full_charge')->nullable();
            $table->float('ess_full_charge_end')->nullable();
            $table->float('ess_full_charge_duration')->nullable();
            $table->float('ess_full_charge_cycle')->nullable();
            $table->float('ess_maintenance_charge')->nullable();
            $table->float('ess_uo_charge')->nullable();
            $table->float('ess_uo_charge_end')->nullable();
            $table->float('ess_uo_charge_duration')->nullable();
            $table->float('ess_i_charge')->nullable();
            $table->float('ess_i_charge_end')->nullable();

            // Battery
            $table->string('ess_battery')->nullable();
            $table->integer('ess_num_batteries_per_string')->nullable();
            $table->integer('ess_num_battery_strings')->nullable();
            $table->float('ess_system_voltage')->nullable();
            $table->float('ess_usable_energy')->nullable();
            $table->float('ess_capacity_c10')->nullable();
            $table->timestamps();

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('article_group_id')->references('id')->on('article_groups')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battery_systems');
    }
};
