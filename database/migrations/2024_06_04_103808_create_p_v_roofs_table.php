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
    Schema::create('p_v_roofs', function (Blueprint $table) {
        $table->id(); 
        $table->unsignedBigInteger('customer_id');
        $table->unsignedBigInteger('alternative_id'); 
        $table->unsignedBigInteger('roof_covering')->nullable();  
        $table->string('roof_covering_name')->nullable();  
        $table->string('designation')->nullable();
        $table->string('roof')->nullable();
        $table->string('roof_insulation')->nullable();
        $table->string('construction_fluid')->nullable();
        $table->integer('roof_age')->nullable();
        $table->string('thickness_roof_insulation')->nullable();
        $table->string('between_rafter_insulation')->nullable();
        $table->string('thickness_between_rafter')->nullable();  
        $table->enum('intention', ['Interesse', 'vorhanden', 'Erweiterung', 'später'])->default('Interesse');
        $table->enum('objective', ['EFH', 'MFH', 'Neubau', 'Sanierung', 'Einzelmaßnahmen'])->default('EFH');
        $table->integer('number_we')->nullable();
        $table->integer('number_of_meters')->nullable();
        $table->float('electricity_consumption')->nullable();  // kWh
        $table->string('electric_car')->nullable();
        $table->integer('number_of_electric_cars')->nullable();
        $table->string('wallbox_desired')->nullable();
        $table->integer('number_of_wallboxes')->nullable();
        // Adding checkbox fields
        $table->string('meter_cabinet')->nullable();
        $table->integer('meter_cabinet_company')->nullable();
        $table->string('cabinet_size')->nullable();
        $table->string('cabinet_size_sonstiges')->nullable();
        // Adding new component fields 
        $table->string('roof_renovation')->nullable(); 
        $table->string('asbestos')->nullable(); 
        $table->string('tilt')->nullable(); 

        $table->string('roof_area')->nullable(); // Maße Dachfläche
        $table->integer('rafter_overhang_left')->nullable(); // Dachüberstand Sparren links
        $table->integer('rafter_overhang_right')->nullable(); // Dachüberstand Sparren rechts
        $table->integer('rafter_thickness')->nullable(); // Sparrenstärke
        $table->boolean('rafter_reinforcement_needed')->default(false); // Sparrenverstärkung notwendig
        $table->string('roof_covering_dimensions_cm')->nullable(); // Eindeckmaß in cm
        $table->boolean('structural_analysis_available')->default(false); // Statik vorhanden
        $table->string('roofer')->nullable(); // Dachdecker
        $table->boolean('scaffold_usage')->default(false); // Gerüstnutzung
        $table->text('roof_structures')->nullable(); // Dachaufbauten
        $table->text('notes')->nullable(); // Notiz
        $table->string('delivered_by')->nullable(); 
        $table->boolean('solar_holding_tile_desired')->default(false);  
        $table->string('pv_existing')->nullable(); 
        $table->integer('construction_year')->nullable(); 
        $table->integer('module_count')->nullable(); 
        $table->decimal('module_power', 10,2)->nullable(); 
        $table->decimal('kwp_size', 10,2)->nullable(); 

        $table->float('roof_pitch')->nullable(); // in degrees, 30° for example
        $table->float('roof_azimuth')->nullable(); // 0=N, 90=E, 180=S, 270=W
        $table->string('mouting_type')->nullable(); //  e.g. Senkrecht, Waagerecht, Südaufst

        $table->string('roof_form')->nullable(); // Form: Flachdach, Schrägdach
        $table->string('roof_type')->nullable(); // Typ: Satteldach etc.
        $table->string('roof_covering_company')->nullable(); // Hersteller
        $table->string('roof_covering_model')->nullable(); // Model
        $table->string('roof_orientation')->nullable(); // Ausrichtung: Süd-West etc.
        $table->float('roof_height')->nullable(); // Traufhöhe
        $table->string('insulation_material')->nullable(); // Dämmmaterial

        

        $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
        $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');
        $table->foreign('roof_covering')->references('id')->on('products')->onDelete('cascade');
        
        $table->timestamps();
        $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_v_roofs');
    }
};
