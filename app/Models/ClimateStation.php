<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClimateStation extends Model
{
    protected $fillable = [
        'station_id',
        'name',
        'region',
        'country_code',
        'lat',
        'lon',
        'elevation',
        'mapped_postcodes',
    ];

    protected $casts = [
        'lat' => 'decimal:6',
        'lon' => 'decimal:6',
        'elevation' => 'decimal:2',
        'mapped_postcodes' => 'array',
    ];

    public function monthlyData(): HasMany
    {
        return $this->hasMany(ClimateMonthlyData::class);
    }

    public function solarMonthlyData(): HasMany
    {
        return $this->hasMany(ClimateSolarMonthlyData::class);
    }
}