<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\PVRoof;

class PVChecklist extends Model
{
   use HasFactory, SoftDeletes;

    protected $fillable = [
        'customer_id',
        'alternative_id',
        'intention',
        'roof_type',
        'number_we',
        'number_stories',
        'number_of_meters',
        'annual_consumption',
        'electric_car',
        'electric_car_plan',
        'wallbox_desired',
        'number_of_wallboxes',
        'meter_cabinet',
        'meter_cabinet_company',
        'cabinet_size',
        'cabinet_size_sonstiges',
        'meter_adapter_plate',
        'ac_surge_protection',
        'sls_switch',
        'apz_field',
        'disconnect_relay',
        'equipotential_bonding',
        'desired_size',
        'pv_rafters',
        'evu_max_size',
        'roof_dimensions',
        'rafter_left_overhang',
        'roof_covering_width',
        'roof_covering_height',
        'rafter_right_overhang',
        'rafter_thickness',
        'rafter_reinforcement_needed',
        'statics_available',
        'conduit_available',
        'cable_routing_through',
        'lightning_protection',
        'roof_renovation_needed',
        'planned_date',
        'scaffolding_usage',
        'roofer',
        'duration',
        'location',
        'solar_tile_desired',
        'contact_person',
        'supplied_by',
        'roof_structures',
        'planned_action',
        'planned_note',
        'house_year',
        'number_of_modules',
        'module_manufacturer',
        'type_designation',
        'kwp_size',
        'inverter',
        'system_conversion',
        'damage_defect',
        'complete_dismantling',
        'insurance_damage',
        'customer_keeps_modules',
        'customer_keeps_inverter',
        'note',
    ];

    protected $dates = ['deleted_at'];

    public function customer()
    {
        return $this->belongsTo(NewLeads::class);
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }
    
    public function roofs()
    {
        return $this->hasMany(PVRoof::class, 'pv_id');
    }
}
