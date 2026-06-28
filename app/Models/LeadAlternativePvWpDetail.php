<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;

class LeadAlternativePvWpDetail extends Model
{
    use HasFactory;
    use AuditableLead;

    protected $table = 'lead_alternative_pv_wp_details';

    protected $guarded = ['id'];

    protected $casts = [
        'cables_sufficient' => 'boolean',
        'wp_integration' => 'boolean',
        'wp_heating_rod' => 'boolean',
        'wallbox_core_drilling' => 'boolean',
        'earthworks_required' => 'boolean',

        'meter_cabinet_old_to_subdistribution' => 'boolean',
        'meter_cabinet_additional_subdistribution' => 'boolean',
        'meter_cabinet_submeter_required' => 'boolean',
        'meter_cabinet_wp_submeter_required' => 'boolean',

        'internet_repeater_required' => 'boolean',
        'internet_socket_required' => 'boolean',

        'two_units_present' => 'boolean',
        'has_bathtub' => 'boolean',
        'has_pool' => 'boolean',

        'single_pipe_system' => 'boolean',
        'solar_thermal_keep' => 'boolean',

        'dhw_electric_dle' => 'boolean',
        'dhw_electric_boiler' => 'boolean',
        'dhw_electric_ut' => 'boolean',

        'kg_underfloor' => 'boolean',
        'eg_underfloor' => 'boolean',
        'og_underfloor' => 'boolean',
        'dg_underfloor' => 'boolean',

        'kg_radiator' => 'boolean',
        'eg_radiator' => 'boolean',
        'og_radiator' => 'boolean',
        'dg_radiator' => 'boolean',

        'controller_cooling_suitable' => 'boolean',
        'hkv_balancing_suitable' => 'boolean',
        'actuator_balancing_suitable' => 'boolean',

        'passive_cooling_interest' => 'boolean',
        'space_vvm500' => 'boolean',
        'space_wm320' => 'boolean',
        'individual_components_required' => 'boolean',

        'stairs_present' => 'boolean',
        'alternative_placement_possible' => 'boolean',
        'alt_stairs_present' => 'boolean',

        'wp_meter_present' => 'boolean',
        'wp_tariff_planned' => 'boolean',

        'battery_dist_inverter_meter' => 'decimal:2',
        'battery_dist_battery_inverter' => 'decimal:2',
        'wallbox_distance_meter' => 'decimal:2',
        'earthworks_length' => 'decimal:2',

        'pool_volume' => 'decimal:2',
        'hk1_flow_temp' => 'decimal:2',
        'hk1_return_temp' => 'decimal:2',
        'hk2_flow_temp' => 'decimal:2',
        'hk2_return_temp' => 'decimal:2',

        'access_width' => 'decimal:2',
        'access_height' => 'decimal:2',
        'door1_width' => 'decimal:2',
        'door1_height' => 'decimal:2',
        'door2_width' => 'decimal:2',
        'door2_height' => 'decimal:2',
        'door3_width' => 'decimal:2',
        'door3_height' => 'decimal:2',
        'door4_width' => 'decimal:2',
        'door4_height' => 'decimal:2',
        'stairs_width' => 'decimal:2',

        'outdoor_connection_length' => 'decimal:2',
        'indoor_connection_length' => 'decimal:2',

        'alt_access_width' => 'decimal:2',
        'alt_access_height' => 'decimal:2',
        'alt_door1_width' => 'decimal:2',
        'alt_door1_height' => 'decimal:2',
        'alt_door2_width' => 'decimal:2',
        'alt_door2_height' => 'decimal:2',
        'alt_stairs_width' => 'decimal:2',

        'length_ae_zs' => 'decimal:2',
        'length_ae_ie' => 'decimal:2',
        'length_ie_zs' => 'decimal:2',

        'trace_heating_cable_length' => 'decimal:2',
        'noise_immission_distance' => 'decimal:2',
    ];

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'lead_alternative_add_id');
    }

    public function leadAlternativeAdd()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'lead_alternative_add_id');
    }
}