<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClimateSolarMonthlyData extends Model
{
    protected $table = 'climate_solar_monthly_data';

    protected $fillable = [
        'climate_station_id',
        'dataset_scope',
        'dataset_label',
        'year',
        'month_num',
        'month',
        'surface_type',
        'tilt_angle',
        'orientation',
        'value_kwh_m2',
        'row_kind',
    ];

    protected $casts = [
        'climate_station_id' => 'integer',
        'year' => 'integer',
        'month_num' => 'integer',
        'tilt_angle' => 'decimal:2',
        'value_kwh_m2' => 'decimal:2',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(ClimateStation::class, 'climate_station_id');
    }
}