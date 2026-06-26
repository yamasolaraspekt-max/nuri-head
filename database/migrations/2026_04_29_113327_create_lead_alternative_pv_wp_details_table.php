<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_alternative_pv_wp_details', function (Blueprint $table) {
            $table->id();

            // Link to lead_alternative_adds
            $table->foreignId('lead_alternative_add_id')
                ->unique()
                ->constrained('lead_alternative_adds')
                ->cascadeOnDelete();

            // PV Formular: Allgemein & Zubehör
            $table->boolean('cables_sufficient')->nullable();
            $table->text('dismantling_remain_at_customer')->nullable();

            $table->text('battery_type')->nullable();
            $table->text('battery_size')->nullable();
            $table->text('battery_location')->nullable();
            $table->decimal('battery_dist_inverter_meter', 8, 2)->nullable();
            $table->decimal('battery_dist_battery_inverter', 8, 2)->nullable();

            $table->boolean('wp_integration')->nullable();
            $table->text('wp_type')->nullable();
            $table->text('wp_status')->nullable();
            $table->boolean('wp_heating_rod')->nullable();

            $table->decimal('wallbox_distance_meter', 8, 2)->nullable();
            $table->boolean('wallbox_core_drilling')->nullable();
            $table->boolean('earthworks_required')->nullable();
            $table->decimal('earthworks_length', 8, 2)->nullable();
            $table->text('earthworks_by')->nullable();
            $table->text('other_customer_wishes')->nullable();

            $table->boolean('meter_cabinet_old_to_subdistribution')->nullable();
            $table->boolean('meter_cabinet_additional_subdistribution')->nullable();
            $table->boolean('meter_cabinet_submeter_required')->nullable();
            $table->integer('meter_cabinet_submeter_count')->nullable();
            $table->boolean('meter_cabinet_wp_submeter_required')->nullable();
            $table->integer('meter_cabinet_wp_submeter_count')->nullable();

            $table->boolean('internet_repeater_required')->nullable();
            $table->boolean('internet_socket_required')->nullable();
            $table->text('internet_socket_distance')->nullable();

            // WP Formular: Gebäude & Etagen
            $table->boolean('two_units_present')->nullable();
            $table->boolean('has_bathtub')->nullable();
            $table->text('bathtub_dimensions')->nullable();
            $table->boolean('has_pool')->nullable();
            $table->decimal('pool_volume', 8, 2)->nullable();

            $table->text('installation_floor')->nullable();
            $table->text('heating_specialties')->nullable();
            $table->boolean('single_pipe_system')->nullable();
            $table->boolean('solar_thermal_keep')->nullable();
            $table->integer('solar_thermal_modules')->nullable();

            $table->boolean('dhw_electric_dle')->nullable();
            $table->boolean('dhw_electric_boiler')->nullable();
            $table->boolean('dhw_electric_ut')->nullable();

            // Etagen
            $table->text('kg_heated')->nullable();
            $table->text('eg_heated')->nullable();
            $table->text('og_heated')->nullable();
            $table->text('dg_heated')->nullable();

            $table->boolean('kg_underfloor')->nullable();
            $table->boolean('eg_underfloor')->nullable();
            $table->boolean('og_underfloor')->nullable();
            $table->boolean('dg_underfloor')->nullable();

            $table->boolean('kg_radiator')->nullable();
            $table->boolean('eg_radiator')->nullable();
            $table->boolean('og_radiator')->nullable();
            $table->boolean('dg_radiator')->nullable();

            // Heizkreise
            $table->decimal('hk1_flow_temp', 5, 2)->nullable();
            $table->decimal('hk1_return_temp', 5, 2)->nullable();
            $table->decimal('hk2_flow_temp', 5, 2)->nullable();
            $table->decimal('hk2_return_temp', 5, 2)->nullable();

            // Regler & Abgleich
            $table->boolean('controller_cooling_suitable')->nullable();
            $table->boolean('hkv_balancing_suitable')->nullable();
            $table->text('hkv_balancing_reason')->nullable();
            $table->boolean('actuator_balancing_suitable')->nullable();
            $table->text('actuator_balancing_reason')->nullable();

            // Anlage & Aufstellung
            $table->boolean('passive_cooling_interest')->nullable();
            $table->boolean('space_vvm500')->nullable();
            $table->boolean('space_wm320')->nullable();
            $table->boolean('individual_components_required')->nullable();

            // Einbringmaße Haupt
            $table->text('access_heating_room')->nullable();
            $table->decimal('access_width', 8, 2)->nullable();
            $table->decimal('access_height', 8, 2)->nullable();
            $table->decimal('door1_width', 8, 2)->nullable();
            $table->decimal('door1_height', 8, 2)->nullable();
            $table->decimal('door2_width', 8, 2)->nullable();
            $table->decimal('door2_height', 8, 2)->nullable();
            $table->decimal('door3_width', 8, 2)->nullable();
            $table->decimal('door3_height', 8, 2)->nullable();
            $table->decimal('door4_width', 8, 2)->nullable();
            $table->decimal('door4_height', 8, 2)->nullable();
            $table->boolean('stairs_present')->nullable();
            $table->text('stairs_type')->nullable();
            $table->decimal('stairs_width', 8, 2)->nullable();

            // Wege und Alternative
            $table->text('outdoor_unit_connection')->nullable();
            $table->decimal('outdoor_connection_length', 8, 2)->nullable();
            $table->decimal('indoor_connection_length', 8, 2)->nullable();

            $table->boolean('alternative_placement_possible')->nullable();
            $table->decimal('alt_access_width', 8, 2)->nullable();
            $table->decimal('alt_access_height', 8, 2)->nullable();
            $table->decimal('alt_door1_width', 8, 2)->nullable();
            $table->decimal('alt_door1_height', 8, 2)->nullable();
            $table->decimal('alt_door2_width', 8, 2)->nullable();
            $table->decimal('alt_door2_height', 8, 2)->nullable();
            $table->boolean('alt_stairs_present')->nullable();
            $table->text('alt_stairs_type')->nullable();
            $table->decimal('alt_stairs_width', 8, 2)->nullable();

            // Elektro WP
            $table->decimal('length_ae_zs', 8, 2)->nullable();
            $table->decimal('length_ae_ie', 8, 2)->nullable();
            $table->decimal('length_ie_zs', 8, 2)->nullable();
            $table->boolean('wp_meter_present')->nullable();
            $table->boolean('wp_tariff_planned')->nullable();
            $table->time('lockout_time_1_start')->nullable();
            $table->time('lockout_time_1_end')->nullable();
            $table->time('lockout_time_2_start')->nullable();
            $table->time('lockout_time_2_end')->nullable();

            // Sonstiges WP
            $table->text('drip_line_ie')->nullable();
            $table->text('condensate_ae')->nullable();
            $table->decimal('trace_heating_cable_length', 8, 2)->nullable();
            $table->text('foundation_by')->nullable();
            $table->text('earthworks_by_wp')->nullable();
            $table->text('soakaway_by')->nullable();

            $table->text('element_buffer')->nullable();
            $table->text('element_dhw')->nullable();
            $table->text('element_hkv')->nullable();
            $table->text('element_circulation')->nullable();

            // Schallberechnung
            $table->text('noise_area')->nullable();
            $table->text('noise_location')->nullable();
            $table->text('noise_shielding')->nullable();
            $table->decimal('noise_immission_distance', 8, 2)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_alternative_pv_wp_details');
    }
};