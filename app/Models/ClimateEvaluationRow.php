<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClimateEvaluationRow extends Model
{
    protected $fillable = [
        'evaluation_id',
        'evaluation_name',
        'postcode',
        'country_code',
        'location_name',
        'location_station_mapping_id',
        'station_id',
        'quantity_code',
        'data_type_code',
        'orientation_code',
        'orientation_name',
        'orientation_degree',
        'inclination_degree',
        'lta_values',
        'period_values',
        'meta',
    ];

    protected $casts = [
        'orientation_degree' => 'decimal:2',
        'inclination_degree' => 'decimal:2',
        'lta_values' => 'array',
        'period_values' => 'array',
        'meta' => 'array',
    ];
}