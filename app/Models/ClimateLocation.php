<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClimateLocation extends Model
{
    protected $fillable = [
        'location_mapping_id',
        'postcode',
        'country_code',
        'location_type',
        'mapping_version',
        'mapping_version_name',
        'mapping_version_description',
        'name',
        'lat',
        'lon',
        'elevation',
        'station_01_id',
        'station_02_id',
        'station_03_id',
        'distance_station_01',
        'distance_station_02',
        'distance_station_03',
    ];

    protected $casts = [
        'mapping_version' => 'integer',
        'lat' => 'decimal:6',
        'lon' => 'decimal:6',
        'elevation' => 'decimal:2',
        'distance_station_01' => 'decimal:2',
        'distance_station_02' => 'decimal:2',
        'distance_station_03' => 'decimal:2',
    ];

    public function primaryStation()
    {
        return $this->belongsTo(ClimateStation::class, 'station_01_id', 'station_id');
    }

    public function secondaryStation()
    {
        return $this->belongsTo(ClimateStation::class, 'station_02_id', 'station_id');
    }

    public function tertiaryStation()
    {
        return $this->belongsTo(ClimateStation::class, 'station_03_id', 'station_id');
    }
}