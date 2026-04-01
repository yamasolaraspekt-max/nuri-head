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
        Schema::create('profitability_data', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('p_id')->nullable();        
            $table->unsignedBigInteger('customer_id')->nullable();        
            $table->unsignedBigInteger('alternative_id')->nullable();     
            $table->unsignedBigInteger('product_id')->nullable();     

            // 🔆 PV-Anlage
            $table->float('pv_module_area')->nullable();
            $table->float('pv_power_kwp')->nullable();
            $table->integer('pv_self_use')->nullable();

            // 🔋 Batteriespeicher
            $table->float('battery_capacity')->nullable();
            $table->integer('autarky_level')->nullable();

            // ♨ Wärmepumpe
            $table->float('jaz_value')->nullable();
            $table->integer('wp_consumption')->nullable();

            // 🚗 Wallbox / E-Mobilität
            $table->tinyInteger('ev_count')->nullable();
            $table->integer('ev_consumption')->nullable();

            // ⚡ Gebäude & Verbrauch
            $table->integer('household_power')->nullable();
            $table->integer('heating_energy')->nullable();
            $table->string('building_type')->nullable(); // EFH / MFH / Gewerbe

            // 💶 Förderung
            $table->decimal('kfw_subsidy', 10, 2)->nullable();
            $table->decimal('other_subsidy', 10, 2)->nullable();

            $table->float('pv_energy_yield')->nullable(); // E_y
            $table->float('pv_irradiation')->nullable();  // H(i)_y
            $table->float('pv_total_loss')->nullable();   // l_total

            $table->decimal('oil_price', 5, 2)->nullable();
            $table->decimal('gas_price', 5, 2)->nullable();
            $table->decimal('district_heating_price', 5, 2)->nullable();
            $table->decimal('energy_inflation', 4, 1)->nullable();
            $table->decimal('fuel_price', 5, 2)->nullable();
            $table->decimal('electricity_price', 5, 2)->nullable();

            $table->decimal('co2_emission_saved', 8, 2)->nullable(); // e.g., annual CO₂ savings in kg or tons
            $table->decimal('co2_factor_electricity', 5, 3)->nullable(); // e.g., kg CO₂/kWh
            $table->decimal('co2_factor_oil', 5, 3)->nullable();
            $table->decimal('co2_factor_gas', 5, 3)->nullable();
            $table->decimal('co2_factor_district', 5, 3)->nullable();



            $table->timestamps();

            $table->foreign('p_id')->references('id')->on('profitability_calculations')->onDelete('cascade'); 
            $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade'); 
            $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade'); 
            $table->foreign('product_id')->references('id')->on('article_groups')->onDelete('set null'); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profitability_data');
    }
};
