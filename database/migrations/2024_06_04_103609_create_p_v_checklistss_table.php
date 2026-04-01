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
Schema::create('p_v_checklists', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('customer_id');
    $table->unsignedBigInteger('alternative_id'); 
    $table->enum('intention', ['Interesse', 'vorhanden', 'Erweiterung', 'später'])->default('Interesse');
    $table->enum('roof_type', ['EFH', 'MFH', 'Neubau', 'Sanierung', 'Einzelmaßnahmen'])->default('EFH');
    $table->integer('number_we')->nullable();
    $table->integer('number_stories')->nullable();
    $table->integer('number_of_meters')->nullable();
    $table->decimal('annual_consumption',10,2)->nullable();  // kWh
    $table->string('electric_car')->nullable();
    $table->integer('electric_car_plan')->nullable();
    $table->string('wallbox_desired')->nullable();
    $table->integer('number_of_wallboxes')->nullable();
    // Adding checkbox fields
    $table->string('meter_cabinet')->nullable();
    $table->integer('meter_cabinet_company')->nullable();
    $table->string('cabinet_size')->nullable();
    $table->string('cabinet_size_sonstiges')->nullable();
    $table->string('position_hak')->nullable();
    $table->string('distance_inverter')->nullable();
    $table->string('distance_new_meter_cabinet')->nullable();
    // Adding new component fields
    $table->string('meter_adapter_plate')->nullable();
    $table->string('ac_surge_protection')->nullable();
    $table->string('sls_switch')->nullable();
    $table->string('apz_field')->nullable();
    $table->string('disconnect_relay')->nullable();
    $table->string('equipotential_bonding')->nullable();

// Adding new fields from the Blade template
    $table->string('desired_size')->nullable();
    $table->string('pv_rafters')->nullable();
    $table->string('evu_max_size')->nullable();
    $table->text('roof_dimensions')->nullable();
    $table->string('rafter_left_overhang')->nullable();
    $table->string('roof_covering_width')->nullable();
    $table->string('roof_covering_height')->nullable();
    $table->string('rafter_right_overhang')->nullable();
    $table->string('rafter_thickness')->nullable();
    $table->string('rafter_reinforcement_needed')->nullable();
    $table->string('statics_available')->nullable();
    $table->string('conduit_available')->nullable();
    $table->string('cable_routing_through')->nullable();
    $table->string('lightning_protection')->nullable();
    $table->string('roof_renovation_needed')->nullable();
    $table->string('planned_date')->nullable();
    $table->string('scaffolding_usage')->nullable();
    $table->string('roofer')->nullable();
    $table->string('duration')->nullable();
    $table->string('location')->nullable();
    $table->string('solar_tile_desired')->nullable();
    $table->string('contact_person')->nullable();
    $table->string('supplied_by')->nullable();
    $table->string('roof_structures')->nullable();
    $table->string('planned_action')->nullable();
    $table->text('planned_note')->nullable();
    $table->integer('house_year')->nullable();
    $table->integer('number_of_modules')->nullable();
    $table->string('module_manufacturer')->nullable();
    $table->string('type_designation')->nullable();
    $table->string('kwp_size')->nullable();
    $table->string('inverter')->nullable();
    $table->string('system_conversion')->nullable();
    $table->string('damage_defect')->nullable();
    $table->string('complete_dismantling')->nullable();
    $table->string('insurance_damage')->nullable();
    $table->string('customer_keeps_modules')->nullable();
    $table->string('customer_keeps_inverter')->nullable();
    $table->text('note')->nullable(); 

 
    $table->softDeletes();
    $table->timestamps();

    $table->foreign('customer_id')->references('id')->on('new_leads')->onDelete('cascade');
    $table->foreign('alternative_id')->references('id')->on('lead_alternative_adds')->onDelete('cascade');

});



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('p_v_checklists');
    }
};
