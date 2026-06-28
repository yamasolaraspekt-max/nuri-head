<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitabilityData extends Model
{
    protected $table = 'profitability_data';

    protected $fillable = [
        'p_id',
        'customer_id',
        'alternative_id',
        'product_id',
        'pv_module_area',
        'pv_power_kwp',
        'pv_self_use',
        'battery_capacity',
        'autarky_level',
        'jaz_value',
        'wp_consumption',
        'ev_count',
        'ev_consumption',
        'household_power',
        'heating_energy',
        'building_type',
        'kfw_subsidy',
        'other_subsidy',
        'pv_energy_yield',
        'pv_irradiation',
        'pv_total_loss',
        'electricity_price',
        'oil_price',
        'gas_price',
        'fuel_price',
        'district_heating_price',
        'energy_inflation',
        'co2_emission_saved',
        'co2_factor_electricity',
        'co2_factor_oil',
        'co2_factor_gas',
        'co2_factor_district',
    ];

    // 🔁 Relationships

    public function profitabilityCalculation()
    {
        return $this->belongsTo(ProfitabilityCalculation::class, 'p_id');
    }

    public function customer()
    {
        return $this->belongsTo(NewLeads::class, 'customer_id');
    }

    public function alternative()
    {
        return $this->belongsTo(LeadAlternativeAdd::class, 'alternative_id');
    }

    public function product()
    {
        return $this->belongsTo(ArticleGroup::class, 'product_id');
    }
}
