<?php

namespace App\Services\Energie;

/**
 * Investitionskosten der PV-Auslegung (Komponenten → Summe), die anschließend in die
 * Wirtschaftlichkeit (Energiekonzept-Simulator) übernommen werden. Preise auto aus
 * Artikel-Listenpreis vorbelegt, im Tool überschreibbar.
 */
class KostenService
{
    /**
     * @param  array<int, array{label:string, anzahl:int, einzelpreis:float}>  $modulPosten
     * @return array{positionen: array<int, array<string, mixed>>, modul_netto: float, summe_netto: float}
     */
    public function berechne(array $modulPosten, ?float $wrPreis, ?float $batteriePreis, float $sonstige = 0.0): array
    {
        $positionen = [];
        $modulNetto = 0.0;

        foreach ($modulPosten as $p) {
            $summe = (int) $p['anzahl'] * (float) $p['einzelpreis'];
            $modulNetto += $summe;
            $positionen[] = [
                'gruppe' => 'Module', 'label' => $p['label'], 'menge' => (int) $p['anzahl'],
                'einzelpreis' => round((float) $p['einzelpreis'], 2), 'summe' => round($summe, 2),
            ];
        }

        if ($wrPreis !== null) {
            $positionen[] = ['gruppe' => 'Wechselrichter', 'label' => 'Wechselrichter', 'menge' => 1, 'einzelpreis' => round($wrPreis, 2), 'summe' => round($wrPreis, 2)];
        }
        if ($batteriePreis !== null && $batteriePreis > 0) {
            $positionen[] = ['gruppe' => 'Batterie', 'label' => 'Batteriespeicher', 'menge' => 1, 'einzelpreis' => round($batteriePreis, 2), 'summe' => round($batteriePreis, 2)];
        }
        if ($sonstige > 0) {
            $positionen[] = ['gruppe' => 'Sonstiges', 'label' => 'Montage/Sonstiges', 'menge' => 1, 'einzelpreis' => round($sonstige, 2), 'summe' => round($sonstige, 2)];
        }

        $summe = array_sum(array_column($positionen, 'summe'));

        return [
            'positionen' => $positionen,
            'modul_netto' => round($modulNetto, 2),
            'summe_netto' => round($summe, 2),
        ];
    }
}
