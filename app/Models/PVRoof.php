<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AuditableLead;

class PVRoof extends Model
{
    use HasFactory;
    use AuditableLead;

    protected $fillable = [
        'customer_id',
        'alternative_id',

        'designation',
        'type',
        'roof_type',
        'roof',
        'roof_pitch',
        'roof_orientation',
        'roof_azimuth',
        'roof_area',
        'roof_height',
        'roof_age',

        'roof_covering',
        'roof_covering_name',
        'roof_covering_company',
        'roof_covering_model',
        'roof_covering_dimensions_cm',

        'roof_insulation',
        'thickness_roof_insulation',
        'between_rafter_insulation',
        'thickness_between_rafter',
        'insulation_material',

        'asbestos',
        'roof_renovation',
        'structural_analysis_available',
        'rafter_overhang_left',
        'rafter_overhang_right',
        'rafter_thickness',
        'rafter_reinforcement_needed',
        'scaffold_usage',
        'roof_structures',
        'roofer',

        'intention',
        'objective',
        'notes',
        'delivered_by',

        'number_we',
        'number_of_meters',
        'electricity_consumption',
        'construction_fluid',

        'electric_car',
        'number_of_electric_cars',
        'wallbox_desired',
        'number_of_wallboxes',

        'meter_cabinet',
        'meter_cabinet_company',
        'cabinet_size',
        'cabinet_size_sonstiges',

        'solar_holding_tile_desired',
        'kwp_size',
        'pv_existing',
        'construction_year',
        'module_count',
        'module_power',

        // extended wizard / migration fields
        'shading',
        'dc_cable_route',
        'storage_preference',
        'backup_power',
        'pv_investment_costs',
    ];

    protected $casts = [
        'asbestos' => 'boolean',
        'roof_renovation' => 'boolean',
        'structural_analysis_available' => 'boolean',
        'rafter_reinforcement_needed' => 'boolean',
        'scaffold_usage' => 'boolean',
        'roofer' => 'boolean',
        'electric_car' => 'boolean',
        'wallbox_desired' => 'boolean',
        'solar_holding_tile_desired' => 'boolean',
        'pv_existing' => 'boolean',

        'thickness_roof_insulation' => 'decimal:2',
        'thickness_between_rafter' => 'decimal:2',
        'roof_area' => 'decimal:2',
        'roof_age' => 'integer',
        'rafter_overhang_left' => 'decimal:2',
        'rafter_overhang_right' => 'decimal:2',
        'rafter_thickness' => 'decimal:2',
        'number_of_meters' => 'decimal:2',
        'electricity_consumption' => 'decimal:2',
        'number_of_electric_cars' => 'integer',
        'number_of_wallboxes' => 'integer',
        'kwp_size' => 'decimal:2',
        'module_count' => 'integer',
        'module_power' => 'decimal:2',
        'roof_pitch' => 'decimal:2',
        'roof_azimuth' => 'decimal:2',
        'roof_height' => 'decimal:2',
        'pv_investment_costs' => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function products()
    {
        return $this->belongsTo(Products::class, 'roof_covering');
    }

    public function roofCoveringProduct()
    {
        return $this->belongsTo(Product::class, 'roof_covering');
    }
}