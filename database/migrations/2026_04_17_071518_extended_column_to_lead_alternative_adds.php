<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lead_alternative_adds', function (Blueprint $table) {
            if (!Schema::hasColumn('lead_alternative_adds', 'appointment_confirmed')) {
                $table->text('appointment_confirmed')->nullable()->after('appointment');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'heating_energy_unit')) {
                $table->text('heating_energy_unit')->nullable()->after('annual_heating_energy_consumption');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'electricity_price')) {
                $table->decimal('electricity_price', 10, 2)->nullable()->after('total_electricity_consumption');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'feed_in_tariff')) {
                $table->decimal('feed_in_tariff', 10, 2)->nullable()->after('electricity_price');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'old_heating_price')) {
                $table->decimal('old_heating_price', 10, 2)->nullable()->after('feed_in_tariff');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'building_type')) {
                $table->text('building_type')->nullable()->after('usage_type');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'owner_occupied_units')) {
                $table->unsignedInteger('owner_occupied_units')->nullable()->after('owner_count');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'rented_units')) {
                $table->unsignedInteger('rented_units')->nullable()->after('owner_occupied_units');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'owners_below_40k')) {
                $table->unsignedInteger('owners_below_40k')->nullable()->after('rented_units');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'owners_above_40k')) {
                $table->unsignedInteger('owners_above_40k')->nullable()->after('owners_below_40k');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'owner_occupied_below_40k')) {
                $table->unsignedInteger('owner_occupied_below_40k')->nullable()->after('owners_above_40k');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'owner_occupied_above_40k')) {
                $table->unsignedInteger('owner_occupied_above_40k')->nullable()->after('owner_occupied_below_40k');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'rented_below_40k')) {
                $table->unsignedInteger('rented_below_40k')->nullable()->after('owner_occupied_above_40k');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'rented_above_40k')) {
                $table->unsignedInteger('rented_above_40k')->nullable()->after('rented_below_40k');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'building_length')) {
                $table->decimal('building_length', 10, 2)->nullable()->after('living_space');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'building_width')) {
                $table->decimal('building_width', 10, 2)->nullable()->after('building_length');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'facade_height')) {
                $table->decimal('facade_height', 10, 2)->nullable()->after('building_width');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'total_window_area')) {
                $table->decimal('total_window_area', 10, 2)->nullable()->after('facade_height');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'masonry_thickness')) {
                $table->decimal('masonry_thickness', 10, 2)->nullable()->after('masonry');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'roof_insulation_type')) {
                $table->text('roof_insulation_type')->nullable()->after('insolation_age');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'roof_insulation_thickness')) {
                $table->decimal('roof_insulation_thickness', 10, 2)->nullable()->after('roof_insulation_type');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'roof_insulation_year')) {
                $table->text('roof_insulation_year')->nullable()->after('roof_insulation_thickness');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'basement_insulation_type')) {
                $table->text('basement_insulation_type')->nullable()->after('roof_insulation_year');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'basement_insulation_thickness')) {
                $table->decimal('basement_insulation_thickness', 10, 2)->nullable()->after('basement_insulation_type');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'basement_insulation_year')) {
                $table->text('basement_insulation_year')->nullable()->after('basement_insulation_thickness');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'ventilation_type')) {
                $table->text('ventilation_type')->nullable()->after('window_year');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'old_heating_power')) {
                $table->decimal('old_heating_power', 10, 2)->nullable()->after('heating_system_type');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'heat_distribution')) {
                $table->text('heat_distribution')->nullable()->after('old_heating_power');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'flow_temperature')) {
                $table->decimal('flow_temperature', 10, 2)->nullable()->after('heat_distribution');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'hot_water_tank_liters')) {
                $table->unsignedInteger('hot_water_tank_liters')->nullable()->after('hot_water_generation');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'heat_pump_pipe_length')) {
                $table->decimal('heat_pump_pipe_length', 10, 2)->nullable()->after('hot_water_tank_liters');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'basement_ceiling_height')) {
                $table->decimal('basement_ceiling_height', 10, 2)->nullable()->after('heat_pump_pipe_length');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'door_width_for_installation')) {
                $table->decimal('door_width_for_installation', 10, 2)->nullable()->after('basement_ceiling_height');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'heat_pump_investment_costs')) {
                $table->decimal('heat_pump_investment_costs', 12, 2)->nullable()->after('door_width_for_installation');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'heat_pump_subsidy_percent')) {
                $table->decimal('heat_pump_subsidy_percent', 5, 2)->nullable()->after('heat_pump_investment_costs');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'circulation_line')) {
                $table->text('circulation_line')->nullable()->after('pipe_system_material');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'heating_pipe_dimension')) {
                $table->text('heating_pipe_dimension')->nullable()->after('circulation_line');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'water_pipe_dimension')) {
                $table->text('water_pipe_dimension')->nullable()->after('heating_pipe_dimension');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'circulation_pipe_dimension')) {
                $table->text('circulation_pipe_dimension')->nullable()->after('water_pipe_dimension');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'meter_cabinet_action')) {
                $table->text('meter_cabinet_action')->nullable()->after('meter_cabinet');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'sls_switch')) {
                $table->text('sls_switch')->nullable()->after('meter_cabinet_action');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'apz_field')) {
                $table->text('apz_field')->nullable()->after('sls_switch');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'ac_surge_protection')) {
                $table->text('ac_surge_protection')->nullable()->after('apz_field');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'enwg_14a_ready')) {
                $table->text('enwg_14a_ready')->nullable()->after('ac_surge_protection');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'grid_reserve')) {
                $table->text('grid_reserve')->nullable()->after('enwg_14a_ready');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'load_management')) {
                $table->boolean('load_management')->default(false)->after('tenant_model');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'charging_power')) {
                $table->text('charging_power')->nullable()->after('wallbox_count');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'access_control')) {
                $table->text('access_control')->nullable()->after('charging_power');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'documents_invoices')) {
                $table->boolean('documents_invoices')->default(false)->after('note');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'documents_roof_images')) {
                $table->boolean('documents_roof_images')->default(false)->after('documents_invoices');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'documents_meter_images')) {
                $table->boolean('documents_meter_images')->default(false)->after('documents_roof_images');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'documents_window_images')) {
                $table->boolean('documents_window_images')->default(false)->after('documents_meter_images');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'documents_heating_images')) {
                $table->boolean('documents_heating_images')->default(false)->after('documents_window_images');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'documents_facade_images')) {
                $table->boolean('documents_facade_images')->default(false)->after('documents_heating_images');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'site_visit_needed')) {
                $table->boolean('site_visit_needed')->default(false)->after('documents_facade_images');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'ready_for_offer')) {
                $table->boolean('ready_for_offer')->default(false)->after('site_visit_needed');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'signature_confirm')) {
                $table->boolean('signature_confirm')->default(false)->after('ready_for_offer');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'signature_name')) {
                $table->text('signature_name')->nullable()->after('signature_confirm');
            }

            if (!Schema::hasColumn('lead_alternative_adds', 'signature_date')) {
                $table->dateTime('signature_date')->nullable()->after('signature_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('lead_alternative_adds', function (Blueprint $table) {
            $table->dropColumn([
                'appointment_confirmed',
                'heating_energy_unit',
                'electricity_price',
                'feed_in_tariff',
                'old_heating_price',
                'building_type',
                'owner_occupied_units',
                'rented_units',
                'owners_below_40k',
                'owners_above_40k',
                'owner_occupied_below_40k',
                'owner_occupied_above_40k',
                'rented_below_40k',
                'rented_above_40k',
                'building_length',
                'building_width',
                'facade_height',
                'total_window_area',
                'masonry_thickness',
                'roof_insulation_type',
                'roof_insulation_thickness',
                'roof_insulation_year',
                'basement_insulation_type',
                'basement_insulation_thickness',
                'basement_insulation_year',
                'ventilation_type',
                'old_heating_power',
                'heat_distribution',
                'flow_temperature',
                'hot_water_tank_liters',
                'heat_pump_pipe_length',
                'basement_ceiling_height',
                'door_width_for_installation',
                'heat_pump_investment_costs',
                'heat_pump_subsidy_percent',
                'circulation_line',
                'heating_pipe_dimension',
                'water_pipe_dimension',
                'circulation_pipe_dimension',
                'meter_cabinet_action',
                'sls_switch',
                'apz_field',
                'ac_surge_protection',
                'enwg_14a_ready',
                'grid_reserve',
                'load_management',
                'charging_power',
                'access_control',
                'documents_invoices',
                'documents_roof_images',
                'documents_meter_images',
                'documents_window_images',
                'documents_heating_images',
                'documents_facade_images',
                'site_visit_needed',
                'ready_for_offer',
                'signature_confirm',
                'signature_name',
                'signature_date',
            ]);
        });
    }
};