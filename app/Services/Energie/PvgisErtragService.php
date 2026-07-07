<?php

namespace App\Services\Energie;

use App\Models\KlimaPlz;
use Illuminate\Support\Facades\Http;

/**
 * PvgisErtragService — kanonischer PVGIS-Jahresertrags-Service (Strang Energie).
 *
 * EINE WAHRHEIT für den PVGIS-Ertrag: kapselt den v5_3-PVcalc-Aufruf nach dem
 * bewährten Muster in PVToolsController::fetchByPostcode (Endpoint
 * https://re.jrc.ec.europa.eu/api/v5_3/PVcalc, Query peakpower/loss/angle/aspect +
 * lat/lon; Jahresertrag = outputs.totals.fixed.E_y in kWh).
 *
 * KEIN zweites PVGIS-Tool: dies ist der Ort, an den sich neue Verbraucher (z. B.
 * das Energiekonzept) hängen. PVToolsController bleibt aus Bestandsschutz vorerst
 * unangetastet (Legacy lauffähig), SOLLTE aber langfristig auf diesen Service
 * delegieren, damit es genau eine PVGIS-Anbindung gibt (Grundsatz „eine Wahrheit").
 *
 * Geocoding: KEINE neue Geocoding-API. lat/lon kommen entweder direkt (optionale
 * Eingabe) oder aus der bestehenden Tabelle `klima_plz` (Spalten lat/lon). Fehlen
 * beide, wird KEIN PVGIS-Call gemacht — es greift der Fallback-Spezifischertrag.
 *
 * GRACEFUL FALLBACK: Bei Netz-/Timeout-/Antwortfehler crasht der Service nicht,
 * sondern liefert quelle='fallback' mit ~950 kWh/kWp Spezifischertrag.
 */
class PvgisErtragService
{
    /** PVGIS v5_3 PVcalc-Endpoint (identisch zu PVToolsController::fetchByPostcode). */
    private const PVGIS_URL = 'https://re.jrc.ec.europa.eu/api/v5_3/PVcalc';

    /** Fallback-Spezifischertrag (kWh je kWp und Jahr), grober Deutschland-Mittelwert. */
    private const FALLBACK_SPEZ_ERTRAG = 950.0;

    /**
     * Jahresertrag einer PV-Anlage. Ruft PVGIS live (mit lat/lon), fällt bei
     * fehlenden Koordinaten oder Fehler auf einen Spezifischertrag-Schätzwert zurück.
     *
     * @param  float  $angle  Neigung ° (0 = horizontal)
     * @param  float  $aspect  Azimut nach PVGIS-Konvention: 0 = Süd, -90 = Ost, 90 = West
     * @param  float  $loss  Systemverluste in %
     * @return array{jahresertrag_kwh: float, spezifischer_ertrag: float, quelle: string}
     */
    public function jahresertragKwh(
        float $lat,
        float $lon,
        float $kwp,
        float $angle = 30,
        float $aspect = 0,
        float $loss = 14
    ): array {
        $kwp = max(0.0, $kwp);

        try {
            $response = Http::timeout(15)->retry(1, 200)->get(self::PVGIS_URL, [
                'lat' => $lat,
                'lon' => $lon,
                'peakpower' => $kwp > 0 ? $kwp : 1.0,
                'loss' => $loss,
                'angle' => $angle,
                'aspect' => $aspect,
                'usehorizon' => 1,
                'outputformat' => 'json',
            ]);

            if ($response->successful()) {
                $eY = data_get($response->json(), 'outputs.totals.fixed.E_y');

                if (is_numeric($eY) && (float) $eY > 0) {
                    $jahresertrag = (float) $eY;

                    return [
                        'jahresertrag_kwh' => round($jahresertrag, 1),
                        'spezifischer_ertrag' => $kwp > 0 ? round($jahresertrag / $kwp, 1) : self::FALLBACK_SPEZ_ERTRAG,
                        'quelle' => 'pvgis',
                    ];
                }
            }
        } catch (\Throwable $e) {
            // Bewusst geschluckt: Netz/Timeout/Antwortfehler dürfen den Aufrufer
            // nicht crashen — der Fallback unten liefert einen belastbaren Schätzwert.
        }

        return $this->fallback($kwp);
    }

    /**
     * Jahresertrag aus einer PLZ (Geocoding über die bestehende Tabelle `klima_plz`),
     * optional mit direkt übergebenen Koordinaten (Vorrang). Ohne auflösbare
     * Koordinaten wird KEIN PVGIS-Call gemacht → reiner Fallback-Ertrag.
     *
     * @return array{jahresertrag_kwh: float, spezifischer_ertrag: float, quelle: string}
     */
    public function jahresertragFuerStandort(
        ?string $plz,
        ?float $lat,
        ?float $lon,
        float $kwp,
        float $angle = 30,
        float $aspect = 0,
        float $loss = 14
    ): array {
        if ($lat === null || $lon === null) {
            $plz = $plz !== null ? trim($plz) : '';

            if ($plz !== '') {
                $klima = KlimaPlz::where('plz', $plz)->first();

                if ($klima !== null) {
                    $lat = (float) $klima->lat;
                    $lon = (float) $klima->lon;
                }
            }
        }

        if ($lat === null || $lon === null) {
            return $this->fallback(max(0.0, $kwp));
        }

        return $this->jahresertragKwh($lat, $lon, $kwp, $angle, $aspect, $loss);
    }

    /**
     * Fallback-Ertrag ohne PVGIS: Spezifischertrag × kWp.
     *
     * @return array{jahresertrag_kwh: float, spezifischer_ertrag: float, quelle: string}
     */
    private function fallback(float $kwp): array
    {
        return [
            'jahresertrag_kwh' => round(self::FALLBACK_SPEZ_ERTRAG * $kwp, 1),
            'spezifischer_ertrag' => self::FALLBACK_SPEZ_ERTRAG,
            'quelle' => 'fallback',
        ];
    }
}
