<?php

namespace App\Services\Heizlast;

use App\Models\HeizlastProjekt;
use App\Models\Konstruktion;
use App\Models\SanierungsVariante;
use App\Models\WpAuslegung;

/**
 * Vorher/Nachher-Sanierungsrechner: rechnet ein Heizlast-Projekt einmal im IST und
 * einmal mit angewandten Maßnahmen (auf einer In-Memory-Kopie – echte Bauteile bleiben
 * unberührt) und stellt Spitzenlast Φ_HL (kW), Jahresheizwärmebedarf Q_a (kWh/a),
 * benötigte Vorlauftemperatur, BAFA-Hülle-Förderung und Wirtschaftlichkeit gegenüber.
 *
 * Wiederverwendete Bausteine (unverändert): HeizlastProjektService::fuerProjekt()
 * (Engine), KlimaBinService::profil() (Klima-Bins für die Gradstunden), FoerderungService.
 *
 * Fachhinweis: Φ_HL (kW, Spitze → WP-Größe/Vorlauf/Flächenheizung) und Q_a (kWh/a → €)
 * sind bewusst getrennt ausgewiesen – das ist der Kern der Sanierungsberatung.
 */
class SanierungsWirtschaftlichkeitService
{
    /** Vorlauftemperatur-Schwellen für die Machbarkeits-Einordnung (°C). */
    private const VL_FLAECHENHEIZUNG_C = 35.0;

    private const VL_WP_TAUGLICH_C = 50.0;

    public function __construct(
        private HeizlastProjektService $heizlastProjektService,
        private KlimaBinService $klimaBin,
        private FoerderungService $foerderung,
    ) {}

    /**
     * @param  array<string, mixed>  $annahmen  energiepreis_ct_kwh, erzeuger_faktor, energiepreissteigerung_pct, isfp, anzahl_we
     * @return array<string, mixed>
     */
    public function berechneVergleich(HeizlastProjekt $projekt, SanierungsVariante $variante, array $annahmen = []): array
    {
        $energiepreis = (float) ($annahmen['energiepreis_ct_kwh'] ?? 12.0);
        $steigerung = (float) ($annahmen['energiepreissteigerung_pct'] ?? 3.0);
        $erzeugerFaktor = isset($annahmen['erzeuger_faktor'])
            ? max(0.1, (float) $annahmen['erzeuger_faktor'])
            : $this->erzeugerFaktor($projekt);

        $massnahmen = $variante->massnahmen ?? [];
        $ist = $this->rechneZustand($projekt, []);
        $nachher = $this->rechneZustand($projekt, $massnahmen);

        $dPhi = $ist['phi_hl_kw'] - $nachher['phi_hl_kw'];
        $dQa = $ist['q_a_kwh'] - $nachher['q_a_kwh'];
        $dVl = $ist['vorlauf_c'] !== null && $nachher['vorlauf_c'] !== null
            ? $ist['vorlauf_c'] - $nachher['vorlauf_c'] : null;

        $raeume = [];
        foreach ($ist['raeume'] as $i => $r) {
            $n = $nachher['raeume'][$i] ?? null;
            $raeume[] = [
                'name' => $r['name'],
                'phi_hl_ist_w' => $r['phi_hl_w'],
                'phi_hl_nachher_w' => $n['phi_hl_w'] ?? null,
                'min_vorlauf_ist_c' => $r['min_vorlauf_c'],
                'min_vorlauf_nachher_c' => $n['min_vorlauf_c'] ?? null,
            ];
        }

        $foerderung = $this->bafaHuelle($massnahmen, $annahmen);

        $einsparungA = $erzeugerFaktor > 0 ? $dQa * ($energiepreis / 100.0) / $erzeugerFaktor : 0.0;
        $investition = $this->summeKosten($massnahmen);
        $nettoInvest = max(0.0, $investition - $foerderung['bafa_huelle_eur']);
        $amort = $einsparungA > 0 ? round($nettoInvest / $einsparungA, 1) : null;
        $gewinn30 = $this->projektion30($einsparungA, $nettoInvest, $steigerung);

        $ergebnis = [
            'annahmen' => [
                'energiepreis_ct_kwh' => $energiepreis,
                'erzeuger_faktor' => round($erzeugerFaktor, 2),
                'energiepreissteigerung_pct' => $steigerung,
                'hinweis' => 'Q_a über Gradstundenmethode (KlimaBinService-Bins, Heizgrenze/θint nach Norm). '
                    .'erzeuger_faktor = JAZ der verknüpften WP-Auslegung, sonst 1,0 (fossil-äquivalent).',
            ],
            'ist' => $this->zustandBlock($ist),
            'nachher' => $this->zustandBlock($nachher),
            'delta' => [
                'phi_hl_kw' => round($dPhi, 2),
                'phi_hl_pct' => $ist['phi_hl_kw'] > 0 ? round($dPhi / $ist['phi_hl_kw'] * 100, 1) : 0.0,
                'q_a_kwh' => (int) round($dQa),
                'q_a_pct' => $ist['q_a_kwh'] > 0 ? round($dQa / $ist['q_a_kwh'] * 100, 1) : 0.0,
                'vorlauf_k' => $dVl !== null ? round($dVl, 1) : null,
            ],
            'raeume' => $raeume,
            'foerderung' => $foerderung,
            'wirtschaftlichkeit' => [
                'investition_eur' => round($investition),
                'foerderung_eur' => $foerderung['bafa_huelle_eur'],
                'netto_investition_eur' => round($nettoInvest),
                'einsparung_eur_a' => round($einsparungA),
                'amortisation_jahre' => $amort,
                'gewinn_30j_eur' => round($gewinn30),
            ],
        ];

        if ($variante->exists) {
            $variante->update(['ergebnis_snapshot' => $ergebnis]); // advisory
        }

        return $ergebnis;
    }

    /**
     * Rechnet einen Zustand (IST oder mit Maßnahmen) über die bestehende Engine.
     *
     * @param  array<int, array<string, mixed>>  $massnahmen
     * @return array{phi_hl_kw: float, standardheizlast_kw: float, q_a_kwh: int, vorlauf_c: ?float, h_w_k: float, raeume: array<int, array{name: string, phi_hl_w: int, min_vorlauf_c: ?float}>}
     */
    private function rechneZustand(HeizlastProjekt $projekt, array $massnahmen): array
    {
        // Frische In-Memory-Kopie – Mutationen werden nie gespeichert.
        $kopie = HeizlastProjekt::with('raeume.bauteile')->findOrFail($projekt->getKey());
        $this->applyMassnahmen($kopie, $massnahmen);

        $r = $this->heizlastProjektService->fuerProjekt($kopie);

        $h = 0.0;
        $raeume = [];
        foreach ($r['raeume'] ?? [] as $raum) {
            $h += (float) ($raum['h_t_w_k'] ?? 0) + (float) ($raum['h_v_w_k'] ?? 0);
            $raeume[] = [
                'name' => (string) ($raum['name'] ?? ''),
                'phi_hl_w' => (int) ($raum['auslegungsheizlast_w'] ?? 0),
                'min_vorlauf_c' => $raum['min_vorlauf_c'] ?? null,
            ];
        }

        $qa = $h * $this->gradstundenKh($kopie->standort_plz) / 1000.0;

        return [
            'phi_hl_kw' => (float) ($r['gebaeude']['auslegungsheizlast_kw'] ?? 0),
            'standardheizlast_kw' => (float) ($r['gebaeude']['standardheizlast_kw'] ?? 0),
            'q_a_kwh' => (int) round($qa),
            'vorlauf_c' => $r['vorlauf']['benoetigte_max_vorlauftemp_c'] ?? null,
            'h_w_k' => round($h, 1),
            'raeume' => $raeume,
        ];
    }

    /**
     * Wendet die Maßnahmen auf die In-Memory-Projektkopie an (setzt U-Werte / ΔU_WB-Modus).
     *
     * @param  array<int, array<string, mixed>>  $massnahmen
     */
    private function applyMassnahmen(HeizlastProjekt $kopie, array $massnahmen): void
    {
        foreach ($massnahmen as $m) {
            if (! empty($m['delta_uwb_modus'])) {
                $kopie->waermebruecken = $m['delta_uwb_modus'] === 'nachweis' ? 'pauschal_nachweis' : 'pauschal';
            }

            $zielTyp = $this->normTyp($m['ziel']['typ'] ?? null);
            $u = $this->resolveU($m);
            if ($zielTyp === null || $u === null) {
                continue;
            }
            $zielGrenzflaeche = $m['ziel']['grenzflaeche'] ?? null;

            foreach ($kopie->raeume as $raum) {
                foreach ($raum->bauteile as $bauteil) {
                    if ($bauteil->typ !== $zielTyp) {
                        continue;
                    }
                    if ($zielGrenzflaeche !== null && $bauteil->grenzflaeche !== $zielGrenzflaeche) {
                        continue;
                    }
                    // Strategie A trägt den exakten U_w – auch für Fenster/Tür (uWert() löst A direkt auf).
                    $bauteil->u_strategie = 'A';
                    $bauteil->u_wert = $u;
                }
            }
        }
    }

    /**
     * U-Wert einer Maßnahme: u_neu > konstruktion_id (u_wert_berechnet).
     *
     * @param  array<string, mixed>  $m
     */
    private function resolveU(array $m): ?float
    {
        if (isset($m['u_neu']) && $m['u_neu'] !== null && $m['u_neu'] !== '') {
            return (float) $m['u_neu'];
        }
        if (! empty($m['konstruktion_id'])) {
            $k = Konstruktion::find($m['konstruktion_id']);
            if ($k?->u_wert_berechnet !== null) {
                return (float) $k->u_wert_berechnet;
            }
        }

        return null;
    }

    /** Bauteil-Zieltyp normalisieren (Konstruktions-Vokabular → Bauteil-Typ). */
    private function normTyp(?string $typ): ?string
    {
        return match ($typ) {
            null => null,
            'aussenwand', 'innenwand', 'fassade_wdvs' => 'wand',
            default => $typ,
        };
    }

    /** Jahres-Heizgradstunden K·h aus den Klima-Bins (Gradstundenmethode, Heizgrenze/θint nach Norm). */
    private function gradstundenKh(?string $plz): float
    {
        $profil = $this->klimaBin->profil($plz);
        $thetaInt = HeizlastNormwerte::THETA_INT_DEFAULT;
        $heizgrenze = (float) $profil['heizgrenze_c'];

        $summe = 0.0;
        foreach ($profil['bins'] as $bin) {
            if ($bin['t'] < $heizgrenze) {
                $summe += ($thetaInt - $bin['t']) * $bin['h'];
            }
        }

        return $summe;
    }

    private function erzeugerFaktor(HeizlastProjekt $projekt): float
    {
        if ($projekt->energiekonzept_id === null) {
            return 1.0;
        }
        $wp = WpAuslegung::where('energiekonzept_id', $projekt->energiekonzept_id)->latest()->first();
        $jaz = $wp !== null
            ? (data_get($wp->ergebnis, 'wp.jaz') ?? data_get($wp->ergebnis, 'bivalenz.jaz') ?? data_get($wp->ergebnis, 'jaz'))
            : null;

        return is_numeric($jaz) && (float) $jaz > 0 ? (float) $jaz : 1.0;
    }

    /**
     * @param  array<int, array<string, mixed>>  $massnahmen
     * @param  array<string, mixed>  $annahmen
     * @return array{bafa_huelle_eur: float, quote_pct: float, foerderfaehig_eur: float, deckel_eur: float, deckel_hinweis: ?string}
     */
    private function bafaHuelle(array $massnahmen, array $annahmen): array
    {
        $kosten = $this->summeKosten($massnahmen);
        $r = $this->foerderung->huelle([
            'huelle_kosten' => $kosten,
            'isfp' => (bool) ($annahmen['isfp'] ?? false),
            'anzahl_we' => (int) ($annahmen['anzahl_we'] ?? 1),
        ]);

        return [
            'bafa_huelle_eur' => (float) $r['zuschuss'],
            'quote_pct' => (float) $r['satz_pct'],
            'foerderfaehig_eur' => (float) $r['foerderfaehig'],
            'deckel_eur' => (float) $r['deckel'],
            'deckel_hinweis' => $kosten > (float) $r['deckel'] ? 'Förderfähige Kosten auf den Deckel gekappt.' : null,
        ];
    }

    /**
     * @param  array<int, array<string, mixed>>  $massnahmen
     */
    private function summeKosten(array $massnahmen): float
    {
        return (float) array_sum(array_map(fn ($m) => (float) ($m['kosten_brutto'] ?? 0), $massnahmen));
    }

    private function projektion30(float $einsparungA, float $nettoInvest, float $steigerungPct): float
    {
        $p = $steigerungPct / 100.0;
        $summe = 0.0;
        for ($i = 1; $i <= 30; $i++) {
            $summe += $einsparungA * (1 + $p) ** ($i - 1);
        }

        return $summe - $nettoInvest;
    }

    /**
     * @param  array{phi_hl_kw: float, q_a_kwh: int, vorlauf_c: ?float, h_w_k: float, standardheizlast_kw: float}  $z
     * @return array<string, mixed>
     */
    private function zustandBlock(array $z): array
    {
        $vl = $z['vorlauf_c'];

        return [
            'phi_hl_kw' => round($z['phi_hl_kw'], 2),
            'standardheizlast_kw' => round($z['standardheizlast_kw'], 2),
            'q_a_kwh' => $z['q_a_kwh'],
            'vorlauf_c' => $vl,
            'h_w_k' => $z['h_w_k'],
            // Machbarkeit aus der benötigten Vorlauftemperatur (nur Interpretation der Engine-Ausgabe).
            'flaechenheizung_machbar' => $vl !== null ? $vl <= self::VL_FLAECHENHEIZUNG_C : null,
            'wp_tauglich' => $vl !== null ? $vl <= self::VL_WP_TAUGLICH_C : null,
            'vorlauf_hinweis' => $vl === null
                ? 'Keine Heizflächen am Projekt erfasst – Vorlauf/Machbarkeit erst nach Erfassung von Heizkörpern/FBH.'
                : null,
        ];
    }
}
