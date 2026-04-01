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
        Schema::create('lead_alternative_adds', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_id');
            $table->longText('full_address')->nullable();
            $table->string('street')->nullable();
            $table->string('postcode')->nullable();
            $table->string('city')->nullable();
            $table->decimal('lat', 9, 6)->nullable(); 
            $table->decimal('lon', 9, 6)->nullable();
            $table->float('elevation')->nullable(); 
            $table->integer('main')->nullable(); 
            $table->integer('address_no')->nullable(); 
            $table->string('object_name')->nullable();  
            $table->timestamp('request_date')->nullable(); // Corrected
            $table->string('periority')->default('Normal');
            $table->string('document')->nullable(); 
            $table->text('note')->nullable();
            $table->date('appointment')->nullable();
            $table->string('appointment_by')->nullable(); 
            $table->string('objective')->nullable();
            $table->integer('living_space')->nullable(); // Beheizte Wohnfläche (kWh)
            $table->integer('unusable_space')->nullable(); // Nutzfläche (kWh)
            $table->integer('number_people')->nullable(); // Anzahl Personen
            $table->integer('number_we')->nullable(); // Anzahl WE
            $table->integer('number_stories')->nullable(); // Anzahl Stockwerke
            $table->string('installation_location')->nullable();
            $table->string('installation_location_extra')->nullable(); 
            $table->integer('annual_consumption')->nullable();
            $table->string('tile_name')->nullable(); 
            $table->string('roof_type')->nullable();
            $table->integer('roof_age')->nullable();
            $table->integer('house_year')->nullable();
            $table->integer('heating_system_age')->nullable();
            $table->integer('heating_system_year')->nullable();
            $table->string('heating_type')->nullable();
            $table->string('heating_system_type')->nullable();
            $table->integer('annual_heating_energy_consumption')->nullable();
            $table->integer('annual_heating_energy_consumption_kwh')->nullable();
            $table->string('electric_car')->nullable();
            $table->string('electric_car_plan')->nullable(); 
            $table->string('status')->default('Published'); 
            $table->integer('total_number')->nullable();
            $table->integer('answered_number')->nullable();
            $table->string('roof_covering')->nullable();
            $table->string('roof_pitch')->nullable();
            $table->string('roof_direction')->nullable(); 
            $table->string('fireplace')->nullable(); 
            $table->string('wood_consumption')->nullable(); 
            $table->decimal('fireplace_value')->nullable(); 
            $table->decimal('car_kilo')->nullable(); 
            $table->string('stage')->default('lead'); 
            $table->date('project_date')->nullable(); 
             $table->longtext('object_remark')->nullable();
            $table->longtext('heating_remark')->nullable(); 
            $table->longtext('roof_remark')->nullable();
            $table->longtext('energy_remark')->nullable();
            $table->longtext('car_remark')->nullable();
            $table->string('wallbox_location')->nullable();
            $table->string('is_owner')->default('Ja');
            $table->string('is_living_inside')->default('Ja');
            $table->decimal('income', 10,2)->default(40000);
            $table->integer('insolation')->default(0);
            $table->integer('insolation_thickness')->nullable();
            $table->string('insolation_type')->nullable(); //Auf dach, Swichen SPARUN, BIDES
            $table->string('insolation_matarial')->nullable(); //Auf dach, Swichen SPARUN, BIDES
            $table->integer('insolation_age')->nullable();  

            // New Columns 
            $table->string('object_type')->nullable();
            $table->string('building_condition')->nullable();
            $table->string('owner_count')->nullable();
            $table->integer('person_count')->nullable();
            $table->integer('building_year')->nullable();
            $table->integer('story_count')->nullable();
            $table->integer('heated_area')->nullable();
            $table->string('external_insulation_thickness')->nullable();
            $table->string('masonry')->nullable();
            $table->string('window_glazing')->nullable();
            $table->string('window_frame')->nullable();
            $table->integer('window_year')->nullable();
            $table->integer('door_year')->nullable();
            $table->string('door_condition')->nullable();
            $table->string('chimney')->nullable();
            $table->integer('heating_circuits_count')->nullable();
            $table->integer('pipe_system_count')->nullable();
            $table->string('pipe_system_material')->nullable();
            $table->string('quantity')->nullable();
            $table->string('consumption')->nullable();
            $table->integer('bathroom_count')->nullable();
            $table->string('hot_water_generation')->nullable();
            $table->integer('bathtub_count')->nullable();
            $table->string('income_level')->nullable();
            $table->decimal('total_heat_consumption', 10, 2)->nullable();
            $table->decimal('total_electricity_consumption', 10, 2)->nullable();
            $table->decimal('heating_load_calculation', 10, 2)->nullable();
            $table->integer('electric_car_count')->nullable();
            $table->integer('wallbox_count')->nullable();
            $table->string('heavy_current_cable')->nullable();
            $table->string('network_cable')->nullable();
            $table->string('groundwork')->nullable();
            $table->string('company_vehicle')->nullable();
            $table->string('bidirectional_car')->nullable();
            $table->decimal('power_household', 10, 2)->nullable();
            $table->decimal('power_heatpump', 10, 2)->nullable();
            $table->decimal('power_electric_car', 10, 2)->nullable();
            $table->decimal('power_other', 10, 2)->nullable();
            $table->decimal('power_total', 10, 2)->nullable();
            $table->string('meter_cabinet')->nullable();
            $table->integer('meter_count')->nullable();
            $table->string('tenant_model')->nullable();
            $table->string('installation_location_power')->nullable();
            $table->string('network_wlan')->nullable();

            // 🆕 Additional BEG Calculator Fields
            $table->string('usage_type')->nullable(); // selbstgenutzt, vermietet, gemischt
            $table->decimal('income_taxed', 10, 2)->nullable(); // Jahreseinkommen
            $table->string('heating_age_group')->nullable(); // jünger als 20 Jahre / älter
            $table->boolean('natural_refrigerant')->nullable(); // WP mit natürlichem Kältemittel
            $table->decimal('investment_costs', 10, 2)->nullable(); // Investitionskosten brutto
            $table->decimal('calculated_subsidy', 10, 2)->nullable(); // Zuschuss
            $table->decimal('calculated_credit_need', 10, 2)->nullable(); // Kreditbedarf
            $table->decimal('calculated_rate', 10, 2)->nullable(); // Monatliche Rate
            $table->string('recommended_program')->nullable(); // KfW 358 / 359
            $table->decimal('subsidy_quote', 5, 2)->nullable(); // % Quote 
            $table->integer('number_self_used')->nullable(); // also missing in your old schema
            

            $table->decimal('solar_module_kwp')->nullable();
            $table->decimal('solar_tile_kwp')->nullable();
            $table->decimal('battery_kwh')->nullable();
            $table->integer('balcony_modules')->nullable(); // Anzahl Module
            $table->boolean('has_pump_upgrade')->nullable();
            $table->boolean('hydraulic_only')->nullable(); // Nur hydraulischer Abgleich ohne WP
            $table->boolean('solar_thermal')->nullable(); // Für Solarthermie
            $table->decimal('solar_thermal_area')->nullable();
            $table->boolean('solar_thermal_simulation')->nullable();

            $table->softDeletes();
            $table->timestamps();
            $table->foreign('lead_id')->references('id')->on('new_leads')->onDelete('cascade'); 

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_alternative_adds');
    }
};
 