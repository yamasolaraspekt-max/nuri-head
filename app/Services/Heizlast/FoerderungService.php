<?php

namespace App\Services\Heizlast;

/**
 * BEG-EM / KfW-458-Heizungsförderung für Wärmepumpen (Stand 2024/2025).
 *
 * Fördersätze: Grundförderung 30 %, Effizienzbonus +5 % (nat. Kältemittel/Erd-/
 * Wasserquelle), Klimageschwindigkeitsbonus +20 % (Selbstnutzer, Austausch alter
 * fossiler Heizung), Einkommensbonus +30 % (Selbstnutzer, zvE ≤ 40.000 €).
 * Maximaler Fördersatz 70 %. Förderfähige Kosten gedeckelt je Wohneinheit.
 *
 * Spiegelt bewusst die Logik des Energiekonzept-Simulators, damit eine Übergabe
 * der Eingaben dort dieselbe Förderung ergibt (keine Doppelrechnung).
 */
class FoerderungService
{
    private const GRUND = 30;

    private const EFFIZIENZ = 5;

    private const KLIMA = 20;

    private const EINKOMMEN = 30;

    private const MAX = 70;

    /** BAFA-Einzelmaßnahmen Gebäudehülle: Grundförderung + iSFP-Bonus. */
    private const HUELLE_GRUND = 15;

    private const ISFP = 5;

    /** Heizungsarten, deren Austausch grundsätzlich klimageschwindigkeitsbonus-berechtigt ist (Simulator-Vokabular). */
    private const FOSSIL_SOFORT = ['oel', 'kohle', 'nacht'];

    /** Diese gelten nur ab 20 Jahren Alter als berechtigt. */
    private const FOSSIL_AB_20J = ['gas', 'fluessiggas', 'holz', 'fernwaerme'];

    /**
     * Maximal förderfähige Kosten je Gebäude in Abhängigkeit der Wohneinheiten.
     */
    public function deckel(int $wohneinheiten): int
    {
        $n = max(1, $wohneinheiten);
        if ($n === 1) {
            return 30000;
        }
        if ($n <= 6) {
            return 30000 + ($n - 1) * 15000;
        }

        return 30000 + 5 * 15000 + ($n - 6) * 8000;
    }

    /**
     * @param  array<string, mixed>  $in
     * @return array<string, mixed>
     */
    public function berechne(array $in): array
    {
        $kosten = max(0.0, (float) ($in['foerderfaehige_kosten'] ?? 0));
        $rabatt = max(0.0, (float) ($in['rabatt'] ?? 0));
        $zusatz = max(0.0, (float) ($in['zusatz'] ?? 0));

        $n = max(1, (int) ($in['anzahl_we'] ?? 1));
        $selbst = min($n, max(0, (int) ($in['selbst_bewohnte_we'] ?? 1)));
        $unter40k = min($selbst, max(0, (int) ($in['we_unter_40k'] ?? 0)));

        $effizienz = (bool) ($in['effizienzbonus'] ?? false);
        $klimaBerechtigt = $this->klimaBerechtigt($in['heizungsart'] ?? null, (int) ($in['heizung_alter'] ?? 0));

        $deckel = $this->deckel($n);
        $nachRabatt = max(0.0, $kosten - $rabatt);
        $foerderfaehig = min($nachRabatt, $deckel);
        $proWe = $foerderfaehig / $n;

        $vermietet = $n - $selbst;
        $selbstOhne = $selbst - $unter40k;
        $selbstMit = $unter40k;

        $basis = self::GRUND + ($effizienz ? self::EFFIZIENZ : 0);
        $klima = $klimaBerechtigt ? self::KLIMA : 0;

        $satzVermietet = min(self::MAX, $basis);                              // nur Grund + Effizienz
        $satzSelbstOhne = min(self::MAX, $basis + $klima);                    // + Klimabonus
        $satzSelbstMit = min(self::MAX, $basis + $klima + self::EINKOMMEN);   // + Einkommensbonus

        $zuschuss = round($proWe * ($vermietet * $satzVermietet + $selbstOhne * $satzSelbstOhne + $selbstMit * $satzSelbstMit) / 100);
        $effSatz = $foerderfaehig > 0 ? $zuschuss / $foerderfaehig : 0.0;
        $gesamt = $zuschuss + $zusatz;
        $netto = max(0.0, $nachRabatt - $zuschuss - $zusatz);

        return [
            'deckel' => $deckel,
            'foerderfaehig' => round($foerderfaehig, 2),
            'saetze' => [
                'grund' => self::GRUND,
                'effizienz' => $effizienz ? self::EFFIZIENZ : 0,
                'klima' => $klima,
                'einkommen' => $selbstMit > 0 ? self::EINKOMMEN : 0,
                'vermietet_pct' => $satzVermietet,
                'selbst_ohne_pct' => $satzSelbstOhne,
                'selbst_mit_pct' => $satzSelbstMit,
            ],
            'klima_berechtigt' => $klimaBerechtigt,
            'einheiten' => ['gesamt' => $n, 'vermietet' => $vermietet, 'selbst_ohne_bonus' => $selbstOhne, 'selbst_mit_bonus' => $selbstMit],
            'zuschuss' => $zuschuss,
            'zusatz' => round($zusatz, 2),
            'gesamt_foerderung' => round($gesamt, 2),
            'effektiver_satz_pct' => round($effSatz * 100, 1),
            'netto_investition' => round($netto, 2),
        ];
    }

    /**
     * BAFA-Einzelmaßnahmen (BEG EM) für Gebäudehülle/Sanierung (Dämmung, Fenster …).
     * Grundförderung 15 %, iSFP-Bonus +5 %. Höchstgrenze förderfähiger Kosten je WE:
     * 30.000 € (ohne iSFP) bzw. 60.000 € (mit iSFP). Eigener Topf — NICHT für die WP.
     *
     * @param  array<string, mixed>  $in
     * @return array<string, mixed>
     */
    public function huelle(array $in): array
    {
        $kosten = max(0.0, (float) ($in['huelle_kosten'] ?? 0));
        $isfp = (bool) ($in['isfp'] ?? false);
        $n = max(1, (int) ($in['anzahl_we'] ?? 1));

        $deckelProWe = $isfp ? 60000 : 30000;
        $deckel = $deckelProWe * $n;
        $foerderfaehig = min($kosten, $deckel);
        $satz = self::HUELLE_GRUND + ($isfp ? self::ISFP : 0);
        $zuschuss = round($foerderfaehig * $satz / 100);

        return [
            'isfp' => $isfp,
            'deckel' => $deckel,
            'foerderfaehig' => round($foerderfaehig, 2),
            'satz_pct' => $satz,
            'zuschuss' => $zuschuss,
            'netto_investition' => round(max(0.0, $kosten - $zuschuss), 2),
        ];
    }

    private function klimaBerechtigt(?string $heizungsart, int $alter): bool
    {
        if ($heizungsart === null) {
            return false;
        }
        if (in_array($heizungsart, self::FOSSIL_SOFORT, true)) {
            return true;
        }

        return in_array($heizungsart, self::FOSSIL_AB_20J, true) && $alter >= 20;
    }
}
