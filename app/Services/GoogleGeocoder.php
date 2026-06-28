<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GoogleGeocoder {
    public function geocode(string $address): ?array {
        $key = config('services.google.maps_key');
        if (!$key || !$address) return null;

        $res = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $address,
            'key'     => $key,
        ])->json();

        $loc = data_get($res, 'results.0.geometry.location');
        return $loc ? ['lat'=>$loc['lat'], 'lon'=>$loc['lng']] : null;
    }
}