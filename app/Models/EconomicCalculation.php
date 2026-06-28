<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EconomicCalculation extends Model
{
    protected $fillable = [
        'lead_id', 'alternative_id', 'product_id', 'soalr_total_cost', 'solar_savings', 'solar_roi_year', 'wp_total_cost', 'wp_savings', 'wp_roi_year', 'combined_savings', 'combined_roi'
    ];
}
