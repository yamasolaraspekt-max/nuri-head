<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WeatherStation extends Model
{
    use HasFactory;

        protected $fillable = ['icao', 'name', 'latitude', 'longitude', 'elevation'];

}
