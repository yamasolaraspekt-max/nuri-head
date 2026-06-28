<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;

class WeatherClient
{
    public function current(float $lat, float $lon): array
    {
        // Open-Meteo (no API key)
        $url = 'https://api.open-meteo.com/v1/forecast';
        $res = Http::get($url, [
            'latitude'  => $lat,
            'longitude' => $lon,
            'current'   => 'temperature_2m,apparent_temperature,weather_code,wind_speed_10m,precipitation',
            'timezone'  => 'auto',
        ])->throw()->json();

        $c = $res['current'] ?? [];
        return [
            'temperature_c'   => $c['temperature_2m'] ?? null,
            'apparent_c'      => $c['apparent_temperature'] ?? null,
            'wind_speed_ms'   => $c['wind_speed_10m'] ?? null,
            'precip_mm'       => $c['precipitation'] ?? null,
            'weather_code'    => $c['weather_code'] ?? null,
            'as_of'           => $c['time'] ?? null,
        ];
    }
}
