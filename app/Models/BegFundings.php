<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BegFundings extends Model
{
    protected $table = 'beg_fundings';

    protected $fillable = [
        'lead_id',
        'alternative_id',
        'product_id',
        'house_type',
        'is_owner',
        'is_living_inside',
        'heating_age', 
        'income', 
        'living_space',
        'number_we',
        'installation_year',
        'applies_efficiency_bonus',
        'applies_speed_bonus',
        'applies_income_bonus',
        'base_percentage',
        'efficiency_percentage',
        'speed_percentage',
        'income_percentage',
        'total_percentage',
        'max_funding_amount',
        'status',
        'notes',
    ];

    // 🧩 Relationships
    public function lead()
    {
        return $this->belongsTo(\App\Models\NewLead::class, 'lead_id');
    }

    public function alternative()
    {
        return $this->belongsTo(\App\Models\LeadAlternativeAdd::class, 'alternative_id');
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\ArticleGroup::class, 'product_id');
    }

}
