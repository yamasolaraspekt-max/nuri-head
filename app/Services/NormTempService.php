<?php
namespace App\Services;

class NormTempService
{
    /**
     * Very simple estimator for DIN/EN design outdoor temperature.
     * Replace with a real table (DWD/DIN 12831) when available.
     */
    public function estimate(?array $identity, ?array $tech, ?array $geo): float
    {
        $elev = (float) ($geo['elevation'] ?? data_get($identity, 'geo.elevation') ?? 0);
        $city = mb_strtolower((string) data_get($identity, 'address', ''), 'UTF-8');

        // crude regional hints
        if (str_contains($city, 'münchen') || str_contains($city, 'berchtesg')) return -16.0;
        if (str_contains($city, 'berlin') || str_contains($city, 'potsdam'))   return -12.0;
        if (str_contains($city, 'köln') || str_contains($city, 'düsseldorf'))  return -10.0;

        // elevation tweak: baseline -12°C plus ~ -1°C per +300 m
        $adj = -12.0 + (floor($elev / 300) * -1.0);
        // clamp to a sane range
        return max(-18.0, min(-8.0, $adj));
    }
}
