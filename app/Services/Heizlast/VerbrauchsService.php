<?php

namespace App\Services\Heizlast;

/**
 * Methode V – Verbrauchsmethode: Heizlast aus bisherigem Energieverbrauch.
 * Spiegelt reales Nutzungsverhalten (liegt oft unter der Norm-Auslegungsheizlast).
 */
class VerbrauchsService
{
    public function __construct(private WarmwasserService $ww) {}

    /**
     * @return array<string, mixed>|null null, wenn kein Verbrauch angegeben
     */
    public function berechne(HeizlastEingabe $e): ?array
    {
        if (! $e->verbrauch_menge || $e->verbrauch_menge <= 0) {
            return null;
        }

        $medium = $this->mediumKey($e->aktuelles_heizmedium);
        $jahre = max(1, $e->verbrauch_zeitraum_jahre);
        $jahresmenge = $e->verbrauch_menge / $jahre;

        $faktor = HeizlastKonstanten::heizmediumFaktor($medium, $e->verbrauch_einheit);
        $endenergie = $jahresmenge * $faktor;

        $eta = HeizlastKonstanten::nutzungsgrad($e->erzeuger_typ);
        $qNutz = $endenergie * $eta;

        $qWw = $e->enthaelt_warmwasser ? $this->ww->qWwKwh($e) : 0.0;
        $qHeiz = max(0.0, $qNutz - $qWw);

        $bVh = $e->b_vh ?: HeizlastKonstanten::B_VH_DEFAULT;
        $phiHl = $qHeiz / $bVh; // kWh / h = kW

        return [
            'methode' => 'verbrauch',
            'endenergie_kwh' => round($endenergie),
            'nutzungsgrad' => $eta,
            'q_nutz_kwh' => round($qNutz),
            'q_ww_kwh' => round($qWw),
            'q_heiz_kwh' => round($qHeiz),
            'b_vh' => $bVh,
            'phi_hl_kw' => round($phiHl, 1),
            'heizmedium_faktor' => $faktor,
            'annahmen' => [
                'nutzungsgrad' => $eta,
                'b_vh' => $bVh,
                'heizmedium' => $medium.' ('.$e->verbrauch_einheit.', ×'.$faktor.')',
                'ww_herausgerechnet' => $e->enthaelt_warmwasser,
            ],
        ];
    }

    private function mediumKey(string $medium): string
    {
        return match ($medium) {
            'strom_direkt', 'nachtspeicher', 'strom' => 'strom',
            'heizoel', 'oel' => 'heizoel',
            default => $medium,
        };
    }
}
