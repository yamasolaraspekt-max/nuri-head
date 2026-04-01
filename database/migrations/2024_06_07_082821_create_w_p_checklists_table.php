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
     Schema::create('w_p_checklists', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('customer_id');
        $table->integer('postcode');
        
        // Intention fields
        $table->string('wp_intention')->nullable(); // Interesse, vorhanden, Erweiterung, später
        $table->string('wp_objective')->nullable(); // EFH, MFH, Neubau, Sanierung, Einzelmaßnahmen
        $table->string('wp_object')->nullable(); // EFH, MFH, Neubau, Sanierung, Einzelmaßnahmen
        $table->string('wp_heating_type')->nullable(); // EFH, MFH, Neubau, Sanierung, Einzelmaßnahmen
        
        // House and heating system age
        $table->integer('construction_year')->nullable(); // Alter des Hauses

        // Space-related fields
        $table->integer('living_space')->nullable(); // Beheizte Wohnfläche (kWh)
        $table->integer('unusable_space')->nullable(); // Nutzfläche (kWh)
        $table->integer('number_people')->nullable(); // Anzahl Personen
        $table->integer('wp_number_we')->nullable(); // Anzahl WE
        $table->integer('wp_number_stories')->nullable(); // Anzahl Stockwerke
        
        // Glass and window margin
        $table->string('1-glass')->nullable(); // Fensterverglasung (1-fach, 2-fach, 3-fach)
        $table->string('2-glass')->nullable(); // Fensterverglasung (1-fach, 2-fach, 3-fach)
        $table->string('3-glass')->nullable(); // Fensterverglasung (1-fach, 2-fach, 3-fach)
        $table->string('window_margin')->nullable(); // Fensterrahmen (Alu, Kunststoff, Holz)
        
        // Insulation and wall-related fields
        $table->integer('insulation_thickness')->nullable(); // Außendämmung Stärke
        $table->string('wall_type')->nullable(); // Mauerart (Mauerwerk, Holz, Kunststoff)
        $table->integer('wall_thickness')->nullable(); // Mauer-Stärke
        $table->char('wp_insulation')->nullable();
        $table->integer('wp_insulation_strength')->nullable(); // Dämmung Stärke
        $table->char('wp_rafter')->nullable(); 
        $table->integer('wp_rafter_strength')->nullable(); // Zwischensparrendämmung Stärke

        // Bathroom-related fields
        $table->integer('wp_bathrooms')->nullable(); // Anzahl Badezimmer
        $table->string('wp_bathtub')->nullable(); // Badewanne vorhanden (Ja/Nein)
        $table->integer('wp_bathtub_count')->nullable(); // Anzahl Badewannen
        $table->string('wp_bathtub_measure')->nullable(); // Maß der Badewanne
        $table->string('wp_swimming_pool')->nullable(); // Swimmingpool vorhanden (Ja/Nein)
        $table->integer('wp_swimming_pool_count')->nullable(); // Anzahl Swimmingpools
        
        // Solar and chimney related fields
        $table->string('solor')->nullable(); // Solarthermie vorhanden (Ja/Nein)
        $table->integer('number_collector')->nullable(); // Anzahl Solarthermiekollektoren (New column)
        $table->string('chimney')->nullable(); // Kamin vorhanden (Ja/Nein)
        $table->decimal('chimney_usage')->nullable(); // Kamin vorhanden (Ja/Nein)

        // Heating load calculation
        $table->string('hlb_calc')->nullable(); // Heizlastberechnung vorhanden (Ja/Nein)
        
        // Energy consumption fields
        $table->decimal('energy_first_year_consumption', 10,2)->nullable(); // Consumption Year 1
        $table->decimal('energy_second_year_consumption', 10,2)->nullable(); // Consumption Year 2
        $table->decimal('energy_third_year_consumption', 10,2)->nullable(); // Consumption Year 3
        $table->string('energy_consumption_type')->nullable(); // Energieverbrauchseinheit
        $table->decimal('energy_total_year_consumption', 10,2)->nullable(); // Total Consumption
        $table->decimal('energy_avg_year_consumption', 10,2)->nullable(); // Average Consumption

        // Energy cost fields
        $table->decimal('energy_first_year_cost', 10,2)->nullable(); // Consumption Year 1
        $table->decimal('energy_second_year_cost')->nullable(); // Consumption Year 2
        $table->decimal('energy_third_year_cost', 10,2)->nullable(); // Consumption Year 3
        $table->decimal('energy_total_year_cost', 10,2)->nullable(); // Total Cost
        $table->decimal('energy_avg_year_cost', 10,2)->nullable(); // Average Cost

        // Heating system fields
        $table->integer('heating_manufacture_year')->nullable(); // Alter der Heizung
        $table->string('heating_type')->nullable(); // Heizungsart
        $table->integer('system_performance')->nullable(); // Leistung der Anlage (kWh)
        $table->string('heating_company')->nullable(); // Heizungsfirma (New column)
        $table->string('type_designation')->nullable(); // Typenbezeichnung (New column)
        $table->string('hot_water_preparation')->nullable(); // Warmwasserbereitung
        $table->integer('number_hotWaterConsumptionPerPerson')->nullable(); // Warmwasserverbrauch pro Person

        // General heating system
        $table->string('general_heating_system')->nullable(); // Allgemeine Heizungsanlage
        $table->string('pipe_system')->nullable(); // Rohrsystem
        $table->string('heating_circuit_distributor')->nullable(); // Heizkreisverteiler
        $table->string('actuators')->nullable(); // Stellantriebe
        $table->string('suitable_cooling_system')->nullable(); // Geeignetes Kühlsystem
        $table->string('radiator')->nullable(); // Heizkörper
        $table->string('thermostats')->nullable(); // Thermostate
        $table->string('thermostatic_valves')->nullable(); // Thermostatventile
        $table->string('radiator_cooling_system')->nullable(); // Heizkörper-Kühlsystem
        $table->string('radiator_note')->nullable(); // Heizkörper Hinweise
        
        // Meter cabinet details
        $table->string('meter_cabinet')->nullable(); // Zählerschrank
        $table->string('cabinet_size')->nullable(); // Zählerschrankgröße
        $table->string('meter_cabinet_company')->nullable(); // Zählerschrankfirma
        
        // Ventilation fields
        $table->string('ventilation')->nullable(); // Lüftung vorhanden (Ja/Nein)
        $table->string('ventilation_system')->nullable(); // Lüftungssystem
        $table->string('ventilation_company')->nullable(); // Lüftungsfirma
        $table->string('ventilation_type')->nullable(); // Lüftungstyp
        $table->string('heat_recovery')->nullable(); // Wärmerückgewinnung
        
        // Exhibition and heat pump fields
        $table->string('heatpump')->nullable(); // Heatpump
        $table->string('exhibition_location')->nullable(); // Ausstellungsort (New column)
        $table->string('exhibation_location_note')->nullable(); // Ausstellungsort Hinweis (New column)
        
        $table->timestamps();
        $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
    });



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('w_p_checklists');
    }
};
