<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('lead_alternative_adds')) {
            return;
        }

        Schema::table('lead_alternative_adds', function (Blueprint $table) {
            $addIfMissing = function (string $column, callable $callback): void {
                if (!Schema::hasColumn('lead_alternative_adds', $column)) {
                    $callback();
                }
            };

            // ids / relations / address
            $addIfMissing('lead_id', fn () => $table->unsignedBigInteger('lead_id')->nullable()->after('id'));
            $addIfMissing('street', fn () => $table->string('street')->nullable());
            $addIfMissing('postcode', fn () => $table->string('postcode')->nullable());
            $addIfMissing('city', fn () => $table->string('city')->nullable());
            $addIfMissing('main', fn () => $table->boolean('main')->nullable());
            $addIfMissing('lat', fn () => $table->decimal('lat', 10, 7)->nullable());
            $addIfMissing('lon', fn () => $table->decimal('lon', 10, 7)->nullable());
            $addIfMissing('elevation', fn () => $table->decimal('elevation', 10, 2)->nullable());
            $addIfMissing('address_no', fn () => $table->string('address_no')->nullable());
            $addIfMissing('object_name', fn () => $table->string('object_name')->nullable());
            $addIfMissing('periority', fn () => $table->string('periority')->nullable());
            $addIfMissing('document', fn () => $table->text('document')->nullable());
            $addIfMissing('note', fn () => $table->longText('note')->nullable());
            $addIfMissing('appointment', fn () => $table->date('appointment')->nullable());
            $addIfMissing('appointment_by', fn () => $table->string('appointment_by')->nullable());
            $addIfMissing('appointment_confirmed', fn () => $table->string('appointment_confirmed')->nullable());

            // consumption / prices
            $addIfMissing('annual_consumption', fn () => $table->decimal('annual_consumption', 12, 2)->nullable());
            $addIfMissing('annual_heating_energy_consumption', fn () => $table->decimal('annual_heating_energy_consumption', 12, 2)->nullable());
            $addIfMissing('annual_heating_energy_consumption_kwh', fn () => $table->decimal('annual_heating_energy_consumption_kwh', 12, 2)->nullable());
            $addIfMissing('heating_energy_unit', fn () => $table->string('heating_energy_unit')->nullable());
            $addIfMissing('total_heat_consumption', fn () => $table->decimal('total_heat_consumption', 12, 2)->nullable());
            $addIfMissing('total_electricity_consumption', fn () => $table->decimal('total_electricity_consumption', 12, 2)->nullable());
            $addIfMissing('electricity_price', fn () => $table->decimal('electricity_price', 12, 4)->nullable());
            $addIfMissing('feed_in_tariff', fn () => $table->decimal('feed_in_tariff', 12, 4)->nullable());
            $addIfMissing('old_heating_price', fn () => $table->decimal('old_heating_price', 12, 4)->nullable());

            // roof
            $addIfMissing('roof_type', fn () => $table->string('roof_type')->nullable());
            $addIfMissing('roof_age', fn () => $table->integer('roof_age')->nullable());
            $addIfMissing('roof_pitch', fn () => $table->decimal('roof_pitch', 8, 2)->nullable());
            $addIfMissing('roof_direction', fn () => $table->string('roof_direction')->nullable());
            $addIfMissing('roof_covering', fn () => $table->string('roof_covering')->nullable());
            $addIfMissing('roof_remark', fn () => $table->longText('roof_remark')->nullable());

            // building
            $addIfMissing('house_year', fn () => $table->integer('house_year')->nullable());
            $addIfMissing('building_type', fn () => $table->string('building_type')->nullable());
            $addIfMissing('building_condition', fn () => $table->string('building_condition')->nullable());
            $addIfMissing('building_length', fn () => $table->decimal('building_length', 12, 2)->nullable());
            $addIfMissing('building_width', fn () => $table->decimal('building_width', 12, 2)->nullable());
            $addIfMissing('facade_height', fn () => $table->decimal('facade_height', 12, 2)->nullable());
            $addIfMissing('total_window_area', fn () => $table->decimal('total_window_area', 12, 2)->nullable());

            // heating
            $addIfMissing('heating_system_age', fn () => $table->string('heating_system_age')->nullable());
            $addIfMissing('heating_system_year', fn () => $table->integer('heating_system_year')->nullable());
            $addIfMissing('heating_system_type', fn () => $table->string('heating_system_type')->nullable());
            $addIfMissing('heating_type', fn () => $table->string('heating_type')->nullable());
            $addIfMissing('heating_age_group', fn () => $table->string('heating_age_group')->nullable());
            $addIfMissing('old_heating_power', fn () => $table->decimal('old_heating_power', 12, 2)->nullable());
            $addIfMissing('heat_distribution', fn () => $table->string('heat_distribution')->nullable());
            $addIfMissing('flow_temperature', fn () => $table->decimal('flow_temperature', 12, 2)->nullable());
            $addIfMissing('heating_load_calculation', fn () => $table->string('heating_load_calculation')->nullable());
            $addIfMissing('heating_notes', fn () => $table->longText('heating_notes')->nullable());
            $addIfMissing('heating_remark', fn () => $table->longText('heating_remark')->nullable());

            // emobility
            $addIfMissing('electric_car', fn () => $table->string('electric_car')->nullable());
            $addIfMissing('electric_car_plan', fn () => $table->string('electric_car_plan')->nullable());
            $addIfMissing('electric_car_count', fn () => $table->integer('electric_car_count')->nullable());
            $addIfMissing('car_kilo', fn () => $table->decimal('car_kilo', 12, 2)->nullable());
            $addIfMissing('company_vehicle', fn () => $table->string('company_vehicle')->nullable());
            $addIfMissing('bidirectional_car', fn () => $table->boolean('bidirectional_car')->nullable());

            $addIfMissing('wallbox_count', fn () => $table->integer('wallbox_count')->nullable());
            $addIfMissing('wallbox_location', fn () => $table->string('wallbox_location')->nullable());
            $addIfMissing('charging_power', fn () => $table->string('charging_power')->nullable());
            $addIfMissing('access_control', fn () => $table->string('access_control')->nullable());

            // object data
            $addIfMissing('total_number', fn () => $table->integer('total_number')->nullable());
            $addIfMissing('objective', fn () => $table->string('objective')->nullable());
            $addIfMissing('number_we', fn () => $table->integer('number_we')->nullable());
            $addIfMissing('living_space', fn () => $table->decimal('living_space', 12, 2)->nullable());
            $addIfMissing('unusable_space', fn () => $table->decimal('unusable_space', 12, 2)->nullable());
            $addIfMissing('number_people', fn () => $table->integer('number_people')->nullable());
            $addIfMissing('number_stories', fn () => $table->integer('number_stories')->nullable());

            $addIfMissing('installation_location', fn () => $table->string('installation_location')->nullable());
            $addIfMissing('installation_location_extra', fn () => $table->string('installation_location_extra')->nullable());
            $addIfMissing('installation_location_power', fn () => $table->string('installation_location_power')->nullable());

            $addIfMissing('status', fn () => $table->string('status')->nullable());
            $addIfMissing('request_date', fn () => $table->date('request_date')->nullable());
            $addIfMissing('project_date', fn () => $table->date('project_date')->nullable());
            $addIfMissing('full_address', fn () => $table->string('full_address')->nullable());
            $addIfMissing('object_remark', fn () => $table->longText('object_remark')->nullable());
            $addIfMissing('energy_remark', fn () => $table->longText('energy_remark')->nullable());
            $addIfMissing('car_remark', fn () => $table->longText('car_remark')->nullable());
            $addIfMissing('stage', fn () => $table->string('stage')->nullable());

            // fireplace / ownership / funding
            $addIfMissing('fireplace', fn () => $table->string('fireplace')->nullable());
            $addIfMissing('wood_consumption', fn () => $table->decimal('wood_consumption', 12, 2)->nullable());
            $addIfMissing('fireplace_value', fn () => $table->decimal('fireplace_value', 12, 2)->nullable());

            $addIfMissing('is_owner', fn () => $table->boolean('is_owner')->nullable());
            $addIfMissing('owner_count', fn () => $table->integer('owner_count')->nullable());
            $addIfMissing('owner_occupied_units', fn () => $table->integer('owner_occupied_units')->nullable());
            $addIfMissing('rented_units', fn () => $table->integer('rented_units')->nullable());
            $addIfMissing('owners_below_40k', fn () => $table->integer('owners_below_40k')->nullable());
            $addIfMissing('owners_above_40k', fn () => $table->integer('owners_above_40k')->nullable());
            $addIfMissing('owner_occupied_below_40k', fn () => $table->integer('owner_occupied_below_40k')->nullable());
            $addIfMissing('owner_occupied_above_40k', fn () => $table->integer('owner_occupied_above_40k')->nullable());
            $addIfMissing('rented_below_40k', fn () => $table->integer('rented_below_40k')->nullable());
            $addIfMissing('rented_above_40k', fn () => $table->integer('rented_above_40k')->nullable());

            $addIfMissing('is_living_inside', fn () => $table->boolean('is_living_inside')->nullable());
            $addIfMissing('income', fn () => $table->decimal('income', 12, 2)->nullable());
            $addIfMissing('income_taxed', fn () => $table->decimal('income_taxed', 12, 2)->nullable());
            $addIfMissing('income_level', fn () => $table->string('income_level')->nullable());

            // insulation
            $addIfMissing('insolation', fn () => $table->string('insolation')->nullable());
            $addIfMissing('insolation_thickness', fn () => $table->decimal('insolation_thickness', 12, 2)->nullable());
            $addIfMissing('external_insulation_thickness', fn () => $table->decimal('external_insulation_thickness', 12, 2)->nullable());
            $addIfMissing('insolation_type', fn () => $table->string('insolation_type')->nullable());
            $addIfMissing('insolation_matarial', fn () => $table->string('insolation_matarial')->nullable());
            $addIfMissing('insolation_age', fn () => $table->integer('insolation_age')->nullable());

            $addIfMissing('masonry', fn () => $table->string('masonry')->nullable());
            $addIfMissing('masonry_thickness', fn () => $table->decimal('masonry_thickness', 12, 2)->nullable());

            $addIfMissing('roof_insulation_type', fn () => $table->string('roof_insulation_type')->nullable());
            $addIfMissing('roof_insulation_thickness', fn () => $table->decimal('roof_insulation_thickness', 12, 2)->nullable());
            $addIfMissing('roof_insulation_year', fn () => $table->integer('roof_insulation_year')->nullable());

            $addIfMissing('basement_insulation_type', fn () => $table->string('basement_insulation_type')->nullable());
            $addIfMissing('basement_insulation_thickness', fn () => $table->decimal('basement_insulation_thickness', 12, 2)->nullable());
            $addIfMissing('basement_insulation_year', fn () => $table->integer('basement_insulation_year')->nullable());

            $addIfMissing('window_glazing', fn () => $table->string('window_glazing')->nullable());
            $addIfMissing('window_frame', fn () => $table->string('window_frame')->nullable());
            $addIfMissing('window_year', fn () => $table->string('window_year')->nullable());
            $addIfMissing('door_year', fn () => $table->string('door_year')->nullable());
            $addIfMissing('door_condition', fn () => $table->string('door_condition')->nullable());
            $addIfMissing('ventilation_type', fn () => $table->string('ventilation_type')->nullable());

            // finance / technical
            $addIfMissing('usage_type', fn () => $table->string('usage_type')->nullable());
            $addIfMissing('natural_refrigerant', fn () => $table->boolean('natural_refrigerant')->nullable());
            $addIfMissing('investment_costs', fn () => $table->decimal('investment_costs', 14, 2)->nullable());
            $addIfMissing('calculated_subsidy', fn () => $table->decimal('calculated_subsidy', 14, 2)->nullable());
            $addIfMissing('calculated_credit_need', fn () => $table->decimal('calculated_credit_need', 14, 2)->nullable());
            $addIfMissing('calculated_rate', fn () => $table->decimal('calculated_rate', 14, 2)->nullable());
            $addIfMissing('recommended_program', fn () => $table->string('recommended_program')->nullable());
            $addIfMissing('subsidy_quote', fn () => $table->decimal('subsidy_quote', 14, 2)->nullable());

            $addIfMissing('solar_module_kwp', fn () => $table->decimal('solar_module_kwp', 12, 2)->nullable());
            $addIfMissing('has_pump_upgrade', fn () => $table->boolean('has_pump_upgrade')->nullable());
            $addIfMissing('balcony_modules', fn () => $table->integer('balcony_modules')->nullable());
            $addIfMissing('hydraulic_only', fn () => $table->boolean('hydraulic_only')->nullable());
            $addIfMissing('solar_thermal', fn () => $table->string('solar_thermal')->nullable());
            $addIfMissing('solar_thermal_area', fn () => $table->decimal('solar_thermal_area', 12, 2)->nullable());
            $addIfMissing('solar_thermal_simulation', fn () => $table->string('solar_thermal_simulation')->nullable());

            // pipes / water
            $addIfMissing('heating_circuits_count', fn () => $table->integer('heating_circuits_count')->nullable());
            $addIfMissing('pipe_system_count', fn () => $table->integer('pipe_system_count')->nullable());
            $addIfMissing('pipe_system_material', fn () => $table->string('pipe_system_material')->nullable());
            $addIfMissing('circulation_line', fn () => $table->string('circulation_line')->nullable());
            $addIfMissing('heating_pipe_dimension', fn () => $table->string('heating_pipe_dimension')->nullable());
            $addIfMissing('water_pipe_dimension', fn () => $table->string('water_pipe_dimension')->nullable());
            $addIfMissing('circulation_pipe_dimension', fn () => $table->string('circulation_pipe_dimension')->nullable());

            $addIfMissing('quantity', fn () => $table->decimal('quantity', 12, 2)->nullable());
            $addIfMissing('consumption', fn () => $table->decimal('consumption', 12, 2)->nullable());
            $addIfMissing('bathroom_count', fn () => $table->integer('bathroom_count')->nullable());
            $addIfMissing('bathtub_count', fn () => $table->integer('bathtub_count')->nullable());
            $addIfMissing('hot_water_generation', fn () => $table->string('hot_water_generation')->nullable());
            $addIfMissing('hot_water_tank_liters', fn () => $table->decimal('hot_water_tank_liters', 12, 2)->nullable());
            $addIfMissing('heat_pump_pipe_length', fn () => $table->decimal('heat_pump_pipe_length', 12, 2)->nullable());
            $addIfMissing('basement_ceiling_height', fn () => $table->decimal('basement_ceiling_height', 12, 2)->nullable());
            $addIfMissing('door_width_for_installation', fn () => $table->decimal('door_width_for_installation', 12, 2)->nullable());
            $addIfMissing('heat_pump_investment_costs', fn () => $table->decimal('heat_pump_investment_costs', 14, 2)->nullable());
            $addIfMissing('heat_pump_subsidy_percent', fn () => $table->decimal('heat_pump_subsidy_percent', 8, 2)->nullable());

            // power / cable
            $addIfMissing('heavy_current_cable', fn () => $table->string('heavy_current_cable')->nullable());
            $addIfMissing('network_cable', fn () => $table->string('network_cable')->nullable());
            $addIfMissing('groundwork', fn () => $table->string('groundwork')->nullable());

            $addIfMissing('power_household', fn () => $table->decimal('power_household', 12, 2)->nullable());
            $addIfMissing('power_heatpump', fn () => $table->decimal('power_heatpump', 12, 2)->nullable());
            $addIfMissing('power_electric_car', fn () => $table->decimal('power_electric_car', 12, 2)->nullable());
            $addIfMissing('power_other', fn () => $table->decimal('power_other', 12, 2)->nullable());
            $addIfMissing('power_total', fn () => $table->decimal('power_total', 12, 2)->nullable());

            // meter cabinet
            $addIfMissing('meter_cabinet', fn () => $table->string('meter_cabinet')->nullable());
            $addIfMissing('meter_cabinet_action', fn () => $table->string('meter_cabinet_action')->nullable());
            $addIfMissing('meter_count', fn () => $table->integer('meter_count')->nullable());
            $addIfMissing('sls_switch', fn () => $table->string('sls_switch')->nullable());
            $addIfMissing('apz_field', fn () => $table->string('apz_field')->nullable());
            $addIfMissing('ac_surge_protection', fn () => $table->string('ac_surge_protection')->nullable());
            $addIfMissing('enwg_14a_ready', fn () => $table->string('enwg_14a_ready')->nullable());
            $addIfMissing('grid_reserve', fn () => $table->string('grid_reserve')->nullable());
            $addIfMissing('cabinet_size', fn () => $table->string('cabinet_size')->nullable());

            // tenant / flags
            $addIfMissing('tenant_model', fn () => $table->boolean('tenant_model')->nullable());
            $addIfMissing('load_management', fn () => $table->boolean('load_management')->nullable());
            $addIfMissing('network_wlan', fn () => $table->string('network_wlan')->nullable());

            $addIfMissing('documents_invoices', fn () => $table->boolean('documents_invoices')->nullable());
            $addIfMissing('documents_roof_images', fn () => $table->boolean('documents_roof_images')->nullable());
            $addIfMissing('documents_meter_images', fn () => $table->boolean('documents_meter_images')->nullable());
            $addIfMissing('documents_window_images', fn () => $table->boolean('documents_window_images')->nullable());
            $addIfMissing('documents_heating_images', fn () => $table->boolean('documents_heating_images')->nullable());
            $addIfMissing('documents_facade_images', fn () => $table->boolean('documents_facade_images')->nullable());
            $addIfMissing('site_visit_needed', fn () => $table->boolean('site_visit_needed')->nullable());
            $addIfMissing('ready_for_offer', fn () => $table->boolean('ready_for_offer')->nullable());

            // signature / misc
            $addIfMissing('signature_confirm', fn () => $table->boolean('signature_confirm')->nullable());
            $addIfMissing('signature_name', fn () => $table->string('signature_name')->nullable());
            $addIfMissing('signature_date', fn () => $table->dateTime('signature_date')->nullable());
            $addIfMissing('tile_name', fn () => $table->string('tile_name')->nullable());
        });

        // add soft deletes if missing
        if (!Schema::hasColumn('lead_alternative_adds', 'deleted_at')) {
            Schema::table('lead_alternative_adds', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    public function down(): void
    {
        // Intentionally left empty because this is a defensive sync migration.
    }
};