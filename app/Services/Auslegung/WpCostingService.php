<?php

namespace App\Services\Auslegung;

use App\Services\Energie\KostenService;

/**
 * WpCostingService (Stufe 3b / P1b-1) — verhaltensgleiche Extraktion der bestehenden WP-Kostenlogik aus
 * EnergieAuslegungController::wpErgebnis und EnergiekonzeptController::baueWp (Duplikat A2). Kapselt
 * AUSSCHLIESSLICH die heutige Kostenrechnung:
 *   - Investitionssumme über den unveränderten KostenService (summe_netto),
 *   - jährliche Stromkosten = Stromverbrauch × Strompreis.
 *
 * Bewusst NICHT enthalten (bleibt beim Aufrufer / anderen Diensten): JAZ/Stromverbrauch-Berechnung,
 * Warmwasser, Förderung, Dokument, Produktwahl, Ranking, Bivalenz. Das Ist-Verhalten A1
 * (Geräte-Unabhängigkeit der Kosten) bleibt erhalten — der Service kennt kein Gerät.
 */
final class WpCostingService
{
    public function __construct(
        private KostenService $kosten = new KostenService,
    ) {}

    /**
     * @param  float  $investition  förderfähige Investitionssumme (Formularwert, kein Gerätepreis)
     * @param  float  $stromKwh     Jahres-Stromverbrauch der WP (vom Aufrufer via JazService berechnet)
     * @param  float  $strompreis   €/kWh
     */
    public function berechne(float $investition, float $stromKwh, float $strompreis): WpKostenErgebnis
    {
        $investitionNetto = $this->kosten->berechne([], null, null, $investition)['summe_netto'];
        $stromkostenJahr = $stromKwh * $strompreis;

        return new WpKostenErgebnis($investitionNetto, $stromkostenJahr);
    }
}
