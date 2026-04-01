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
        Schema::create('heatpump_checklists', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id'); 
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade'); 

            $table->string('construction_year')->nullable();  // Baujahr
            $table->integer('persons_per_household')->nullable();  // Personen pro Haushalt
            $table->float('total_living_area')->nullable();  // Wohnfläche Insgesamt m2
       
              // Mehrfamilienhaus Wohneinheiten - Doppelhaushälfte
             $table->integer('building_type_id')->nullable();  // Einfamilienhaus -Reihenmittelhaus -Mehrfamilienhaus -Doppelhaushälfte
             $table->boolean('two_separated_units')->nullable();  // 2 abgetrennte Wohneinheiten vorhanden
             $table->string('construction_type')->nullable(); //Massivbau Holzfertigteilbau Niedrigenergiehaus Passivhaus

             $table->string('wall_material')->nullable();  // Mauermaterial Poroton - Kalksandstein
             $table->integer('wall_thickness')->nullable(); // Mauerstärke cm
             $table->float('heat_demand_per_m2')->nullable();  // Wärmebedarf pro m2

             $table->float('constantly_heated_area')->nullable();  // Ständig beheizte Fläche m2
             $table->integer('number_of_bathrooms')->nullable();  // Anzahl Bäder
             $table->boolean('bathtub')->nullable();  // Badewanne
             $table->boolean('swimming_pool')->nullable();  // Schwimmbad
             $table->float('swimming_pool_size')->nullable();
             $table->boolean('rain_shower')->nullable();  // Regendusche
             $table->float('rain_shower_size')->nullable(); 
             $table->boolean('whirlpool')->nullable();  // Whirlpool 
             $table->float('whirlpool_size')->nullable();  

             // Fenster
            $table->string('window_glazing')->nullable();  // Verglasung der Fenster 1-fach 2-fach 3-fach

            $table->string('planned_renovation')->nullable();  // Geplante energetische Sanierungsmaßnahmen   1.KFW 2.BAFA 

            $table->boolean('window_replacement')->nullable();  // Fenstertausch
            $table->boolean('roof_insulation')->nullable();  // Dachdämmung
            $table->boolean('facade_insulation')->nullable();  // Fassadendämmung

            // aktuelle Heizung
            $table->boolean('fireplace')->nullable();  // Kamin vorhanden? 
            $table->float('wood_consumption')->nullable(); // Verbrauch: Holz m3 pro Jahr
            $table->string('heating_manufacturer')->nullable();  // Hersteller & Typ Heizung
            $table->float('power_kw')->nullable();  // Leistung: kW   
            
            // Art & Verbrauch im Vorjahr
            $table->string('heating_medium')->nullable();  // Öl / Gas / Pellets / Wärmepumpe
            $table->string('heating_medium_value')->nullable(); // Öl / Gas / Pellets / Wärmepumpe

            $table->string('installation_location'); // Aufstellort
            $table->string('floor_number')->nullable();  // Geschoss: 
            $table->string('installed_pipes')->nullable();  // Welche Leitungen sind verlegt? -Kupfer -Kunststoff -Stahl
            $table->boolean('solar_system')->nullable();  // Thermische Solaranlage vorhanden?
            $table->boolean('flat_collector')->nullable();  // Flachkollektor 
            $table->boolean('tube_collector')->nullable();  // Röhrenkollektor
            $table->float('collector_area')->nullable();  // Kollektorfläche m2
            $table->boolean('electricity_meter')->nullable();  // Stromzähler vorhanden?
            $table->boolean('heat_pump_tariff')->nullable();  // Wärmepumpentarif mit Sperrzeit geplant? 
            
            // Warmwasser
            $table->float('storage_capacity_l')->nullable(); // Aufbereitung Fassungsvermögen in l 
            $table->boolean('hot_water_storage')->nullable();  // vorhandener Speicher für Wärmepumpe geeignet?
         

            //Heizungen
            $table->boolean('basement')->nullable();  // Kellergeschoss beheizt - nicht beheizt - Fussbodenheizung - Heizkörper - Stromleitung:
            $table->boolean('basement_electric_heating')->nullable();  // HK Stromleitung vorhanden?
            $table->float('basement_heated_area')->nullable();  // beheizte Fläche m2

            $table->boolean('ground_floor')->nullable();  // Erdgeschoss beheizt - nicht beheizt - Fussbodenheizung - Heizkörper - Stromleitung:
            $table->boolean('ground_floor_electric_heating')->nullable();  // HK Stromleitung vorhanden?
            $table->float('ground_floor_heated_area')->nullable();  // beheizte Fläche m2

            $table->boolean('upper_floor')->nullable();  // Obergeschoss beheizt  - nicht beheizt - Fussbodenheizung - Heizkörper - Stromleitung:
            $table->boolean('upper_floor_electric_heating')->nullable();  // HK Stromleitung vorhanden?
            $table->float('upper_floor_heated_area')->nullable();  // beheizte Fläche m2


            $table->boolean('attic')->nullable(); // Dachgeschoss beheizt nicht beheizt - Fussbodenheizung - Heizkörper - Stromleitung:
            $table->boolean('attic_electric_heating')->nullable();  // HK Stromleitung vorhanden?
            $table->float('attic_heated_area')->nullable();  // beheizte Fläche m2 
            $table->string('flush_heating_circuits')->nullable();  // Müssen Heizkreise gespült / erneuert werden? 

            $table->string('heat_source')->nullable();  // Wärmequelle: Luft-Wasser Wärmepumpe - Sole-Wasser Wärmepumpe (Air-water heat pump - brine-water heat pump)
            $table->boolean('cooling_function')->nullable();  // mit Kühlfunktion?

            $table->string('ventilation')->nullable();  // Lüftung : vorhanden geplant zentral dezentral
            
            $table->boolean('internet_connection')->nullable();  // Internetanschluss vorhanden?
            $table->string('internet_type')->nullable(); //WAN - LAN

            $table->boolean('underfloor_heating')->nullable();  // Fußbodenheizung
            $table->boolean('radiators')->nullable();  // Heizkörper
  
            
            $table->float('room_height')->nullable();  // Raumhöhe
            $table->boolean('installation_suitable')->nullable(); //für Aufstellung geeignet

            $table->string('indoor_unit')->nullable();  // Ort Innengerät 
            $table->string('outdoor_unit')->nullable();  // Ort Außengerät
            $table->boolean('crane_required')->nullable();  // Kran notwendig?

            //Einbringmaße Zuwegung Heizraum / Türmaße:
            $table->float('entry_width1')->nullable(); // Einbringmaße Breite
            $table->float('entry_height2')->nullable(); // Einbringmaße Höhe
             $table->float('entry_width3')->nullable(); // Einbringmaße Breite
            $table->float('entry_height4')->nullable(); // Einbringmaße Höhe
            
            $table->float('new_heating_area')->nullable(); // Fläche neue Heizung
            $table->float('new_heating_width')->nullable(); // Breite neue Heizung
            $table->float('new_heating_height')->nullable(); // Höhe neue Heizung
   
        
           // Anschluss Außeneinheit zur Innenseite
            $table->boolean('connection_type')->nullable(); // Lösungsweg über Wand /Boden - Wall - Floor 
            $table->float('connection_size')->nullable();
 
         
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('heatpump_checklists');
    }
};
