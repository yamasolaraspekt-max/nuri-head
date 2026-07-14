<?php

namespace App\Services\Auslegung;

/**
 * WP-Kosten-Ergebnis (Stufe 3b / P1b-1) — verhaltensgleiche Kapselung der bisher in
 * EnergieAuslegungController::wpErgebnis und EnergiekonzeptController::baueWp doppelt vorhandenen
 * WP-Kostenwerte. Rohwerte (ungerundet); die Ausgabe-Rundung von stromkostenJahr bleibt bewusst im
 * Controller-View-Model (round(...)) wie im Ist. Keine neue Kostenart, keine Umbenennung.
 */
final class WpKostenErgebnis
{
    public function __construct(
        /** Netto-Investitionssumme (= KostenService summe_netto), bereits kaufmännisch gerundet vom KostenService. */
        public readonly float $investitionNetto,
        /** Jährliche Stromkosten roh (stromKwh × strompreis); Ausgabe-Rundung erfolgt im Controller wie bisher. */
        public readonly float $stromkostenJahr,
    ) {}
}
