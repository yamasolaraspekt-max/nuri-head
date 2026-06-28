<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClimateMonthlyData extends Model
{
    protected $table = 'climate_monthly_data';

    protected $fillable = [
        'climate_station_id',
        'dataset_scope',
        'dataset_label',
        'year',
        'month_num',
        'month',
        'days_count',
        'degree_days',
        'heating_days',
        'cooling_days',
        'avg_temp',
        'avg_temp_heating_days',
    ];

    protected $casts = [
        'climate_station_id' => 'integer',
        'year' => 'integer',
        'month_num' => 'integer',
        'days_count' => 'integer',
        'degree_days' => 'decimal:2',
        'heating_days' => 'decimal:2',
        'cooling_days' => 'decimal:2',
        'avg_temp' => 'decimal:2',
        'avg_temp_heating_days' => 'decimal:2',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(ClimateStation::class, 'climate_station_id');
    }
}