<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfitabilityCalculation extends Model
{
    protected $fillable = [
        'customer_id',
        'alternative_id',
        'product_id',
        'service_id',
        'employee_id',
        'title',
        'status',

        'current_electricity_cost',
        'current_heating_cost',
        'current_fuel_cost',
        'current_total_yearly_cost',
        'current_total_25y_cost',

        'future_electricity_cost',
        'future_heating_cost',
        'future_ev_cost',
        'future_total_yearly_cost',
        'future_total_25y_cost',

        'savings_per_year',
        'savings_over_25_years',

        'investment_cost',
        'amortisation_years',
        'roi_percent',

        'co2_emission_before',
        'co2_emission_after',
        'co2_saved_trees_equiv',

        'notes',
        'electricity_price_note',

        'config_snapshot',
        'computed_snapshot',
        'customer_snapshot',
    ];

    protected $casts = [
        'config_snapshot' => 'array',
        'computed_snapshot' => 'array',
        'customer_snapshot' => 'array',

        'current_electricity_cost' => 'decimal:2',
        'current_heating_cost' => 'decimal:2',
        'current_fuel_cost' => 'decimal:2',
        'current_total_yearly_cost' => 'decimal:2',
        'current_total_25y_cost' => 'decimal:2',

        'future_electricity_cost' => 'decimal:2',
        'future_heating_cost' => 'decimal:2',
        'future_ev_cost' => 'decimal:2',
        'future_total_yearly_cost' => 'decimal:2',
        'future_total_25y_cost' => 'decimal:2',

        'savings_per_year' => 'decimal:2',
        'savings_over_25_years' => 'decimal:2',

        'investment_cost' => 'decimal:2',
        'amortisation_years' => 'decimal:2',
        'roi_percent' => 'decimal:2',

        'co2_emission_before' => 'decimal:2',
        'co2_emission_after' => 'decimal:2',
    ];

    // 🔗 Relationships

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

    public function service()
    {
        return $this->belongsTo(PhaseSection::class, 'service_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    

}
