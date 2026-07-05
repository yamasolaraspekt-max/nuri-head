<?php

namespace App\Services\Heizlast;

/**
 * Echte Klimadaten je deutscher PLZ aus aus DWD-Stationsdaten abgeleiteten Werten
 * (Norm-Außentemperatur, Jahresmitteltemperatur, Heizgradtage, Koordinaten).
 *
 * Quelle: aus DWD-CDC-Stationsdaten gewichtete PLZ-Tabelle (IWU-/DWD-basiert),
 * abgelegt unter database/data/klima_plz.csv. Einmal je Prozess geladen und gecacht.
 */
class KlimaPlzService
{
    /** @var array<string, array{ort: string, lat: float, lon: float, nat_c: ?float, temp_mittel_c: ?float, hgt15_kd: ?int, vollbenutz_h: ?float, hoehe_m: ?int}>|null */
    private static ?array $daten = null;

    /**
     * @return array{ort: string, lat: float, lon: float, nat_c: ?float, temp_mittel_c: ?float, hgt15_kd: ?int, vollbenutz_h: ?float, hoehe_m: ?int}|null
     */
    public function lookup(?string $plz): ?array
    {
        $p = $this->normalize($plz);

        return $p !== null ? ($this->daten()[$p] ?? null) : null;
    }

    /**
     * Offline-Koordinaten der PLZ (Ortsmittelpunkt) – ohne externe Geocoding-API.
     *
     * @return array{lat: float, lon: float, name: string}|null
     */
    public function koordinaten(?string $plz): ?array
    {
        $r = $this->lookup($plz);

        return $r ? ['lat' => $r['lat'], 'lon' => $r['lon'], 'name' => trim(($r['ort'] ?? '').', '.$this->normalize($plz))] : null;
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function daten(): array
    {
        if (self::$daten !== null) {
            return self::$daten;
        }

        $map = [];
        $pfad = database_path('data/klima_plz.csv');
        if (is_readable($pfad) && ($fh = fopen($pfad, 'r')) !== false) {
            fgetcsv($fh, escape: ''); // Kopfzeile
            while (($r = fgetcsv($fh, escape: '')) !== false) {
                if (count($r) < 8 || ! ctype_digit((string) $r[0])) {
                    continue;
                }
                $map[$r[0]] = [
                    'ort' => (string) $r[1],
                    'lat' => (float) $r[2],
                    'lon' => (float) $r[3],
                    'nat_c' => $r[4] !== '' ? (float) $r[4] : null,
                    'temp_mittel_c' => $r[5] !== '' ? (float) $r[5] : null,
                    'hgt15_kd' => $r[6] !== '' ? (int) $r[6] : null,
                    'vollbenutz_h' => $r[7] !== '' ? (float) $r[7] : null,
                    'hoehe_m' => isset($r[8]) && $r[8] !== '' ? (int) $r[8] : null,
                ];
            }
            fclose($fh);
        }

        return self::$daten = $map;
    }

    private function normalize(?string $plz): ?string
    {
        $p = preg_replace('/\D/', '', (string) $plz);
        if ($p === '' || strlen($p) > 5 || strlen($p) < 4) {
            return null;
        }

        return str_pad($p, 5, '0', STR_PAD_LEFT);
    }
}
