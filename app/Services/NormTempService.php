<?php
namespace App\Services;

use App\Services\Klima\KlimaPlzService;

class NormTempService
{
    public function __construct(private ?KlimaPlzService $klima = null) {}

    /**
     * DIN/EN design outdoor temperature (Norm-Außentemperatur).
     *
     * Leading source is the `klima_plz` table (DWD/DIN 12831, 8168 PLZ) via KlimaPlzService —
     * one truth, shared with AnforderungsprofilService. If a 5-digit PLZ is derivable from the
     * address and found in the table, that value wins. The crude city/elevation heuristic below
     * remains ONLY as a fallback when no PLZ is available or the table has no match.
     */
    public function estimate(?array $identity, ?array $tech, ?array $geo): float
    {
        $address = (string) data_get($identity, 'address', '');

        // Leading source: klima_plz lookup by PLZ extracted from the address.
        if (preg_match('/\b(\d{5})\b/', $address, $m)) {
            $klima = $this->klima ?? app(KlimaPlzService::class);
            $nat = $klima->getNormAussentempForPlz($m[1]);
            if ($nat !== null) {
                return (float) $nat;
            }
        }

        return $this->heuristic($identity, $geo, $address);
    }

    /** Fallback estimator (kept from the pre-klima_plz era; used only when no table hit). */
    private function heuristic(?array $identity, ?array $geo, string $address): float
    {
        $elev = (float) ($geo['elevation'] ?? data_get($identity, 'geo.elevation') ?? 0);
        $city = mb_strtolower($address, 'UTF-8');

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
