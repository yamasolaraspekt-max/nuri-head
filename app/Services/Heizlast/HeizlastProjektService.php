<?php

namespace App\Services\Heizlast;

use App\Models\HeizlastProjekt;
use App\Models\RadiatorSpec;
use App\Services\Heizkoerper\HydraulicService;
use App\Services\Heizkoerper\RadiatorPerformanceService;
use App\Services\Klima\KlimaPlzService;
use Illuminate\Support\Collection;

/**
 * Bindet die U-Wert-Strategien (UWertService) an den raumweisen Rechenkern
 * (HeizlastRechner): löst je Bauteil den U-Wert auf (A bekannt / B Schichten /
 * C Baualter / Fenster-/Tür-Typ), rechnet und reichert das Ergebnis um
 * Strategie/Konfidenz/Quelle je Bauteil, die Gesamt-Datenqualität sowie den
 * Wärmeübergabe-Abgleich (Heizkörper EN 442 / Fußbodenheizung EN 1264:
 * Mindest-Vorlauftemperatur je Raum, Gebäude-Maximum) an.
 */
class HeizlastProjektService
{
    private const KONFIDENZ_RANG = ['niedrig' => 1, 'mittel' => 2, 'hoch' => 3];

    public function __construct(
        private UWertService $u,
        private HeizlastRechner $rechner,
        private RadiatorPerformanceService $heizkoerper,
        private FussbodenheizungService $fussbodenheizung,
        private HydraulicService $hydraulik,
        private KlimaPlzService $klima,
        private HoehenkorrekturService $hoehenkorrektur,
    ) {}

    /**
     * @param  array<string, mixed>  $eingabe
     * @return array<string, mixed>
     */
    public function berechne(array $eingabe): array
    {
        $baujahr = (int) ($eingabe['baujahr'] ?? 1990);
        $sanierung = $eingabe['sanierungsstufe'] ?? 'unsaniert';

        $raeumeIn = [];
        $aufloesungen = [];
        foreach ($eingabe['raeume'] ?? [] as $ri => $raum) {
            $bauteile = [];
            $aufloesungen[$ri] = [];
            foreach ($raum['bauteile'] ?? [] as $ci => $b) {
                $res = $this->uWert($b, $baujahr, $sanierung);
                $aufloesungen[$ri][$ci] = $res;
                $bauteile[] = [
                    'typ' => $b['typ'] ?? 'wand',
                    'grenzflaeche' => $b['grenzflaeche'] ?? 'aussen',
                    'flaeche_m2' => (float) ($b['flaeche_m2'] ?? 0),
                    'u_wert' => $res['u_wert'],
                ];
            }
            $raeumeIn[] = [
                'name' => $raum['name'] ?? '',
                'nutzung' => $raum['nutzung'] ?? 'wohnen',
                'theta_int_c' => $raum['theta_int_c'] ?? null,
                'grundflaeche_m2' => $raum['grundflaeche_m2'] ?? 0,
                'hoehe_m' => $raum['hoehe_m'] ?? 2.5,
                'luftwechsel_1h' => $raum['luftwechsel_1h'] ?? null,
                'bauteile' => $bauteile,
            ];
        }

        // Höhenkorrektur der Norm-Außentemperatur nach DIN/TS 12831-1 (zentral, damit
        // Live-Berechnung und Nachweis dieselbe θe und denselben Kontext erhalten).
        $hoehenkorrektur = $this->loeseHoehenkorrektur($eingabe);

        $ergebnis = $this->rechner->berechne([
            'norm_aussentemp_c' => $hoehenkorrektur['norm_aussentemp_c'],
            'waermebruecken' => $eingabe['waermebruecken'] ?? 'pauschal',
            'komfortzuschlag_k' => $eingabe['komfortzuschlag_k'] ?? 0,
            'intermittierend' => $eingabe['intermittierend'] ?? false,
            'raeume' => $raeumeIn,
        ]);
        $ergebnis['hoehenkorrektur'] = $hoehenkorrektur;

        // Strategie/Konfidenz/Quelle je Bauteil ins Ergebnis weben + Gesamt-Datenqualität (schwächstes Glied).
        $minRang = 3;
        foreach ($ergebnis['raeume'] as $ri => &$raum) {
            foreach ($raum['bauteile'] as $ci => &$bt) {
                $res = $aufloesungen[$ri][$ci] ?? [];
                $bt['eingabe_strategie'] = $res['eingabe_strategie'] ?? null;
                $bt['konfidenz'] = $res['konfidenz'] ?? null;
                $bt['quelle'] = $res['quelle'] ?? null;
                $minRang = min($minRang, self::KONFIDENZ_RANG[$res['konfidenz'] ?? 'niedrig'] ?? 1);
            }
        }
        unset($raum, $bt);
        $ergebnis['datenqualitaet_konfidenz'] = array_search($minRang, self::KONFIDENZ_RANG, true) ?: 'niedrig';

        $this->emitterAbgleich($ergebnis, $eingabe);
        $ergebnis['hydraulik'] = $this->hydraulik->abgleich(
            $ergebnis['raeume'],
            (float) ($eingabe['spreizung_k'] ?? 7),
            (float) ($eingabe['dp_ventil_mbar'] ?? 100),
        );

        return $ergebnis;
    }

    /**
     * Wärmeübergabe-Abgleich je Raum: Heizkörper (EN 442) und/oder Fußbodenheizung
     * (EN 1264). Je Raum die Mindest-Vorlauftemperatur des günstigsten erfassten
     * Systems; gebäudeweit das Maximum (= benötigte Vorlauftemperatur der WP).
     *
     * @param  array<string, mixed>  $ergebnis
     * @param  array<string, mixed>  $eingabe
     */
    private function emitterAbgleich(array &$ergebnis, array $eingabe): void
    {
        $zielVl = (float) ($eingabe['ziel_vorlauf_c'] ?? 45);
        $spreizung = (float) ($eingabe['spreizung_k'] ?? 7);
        $raeumeIn = $eingabe['raeume'] ?? [];

        $ids = [];
        foreach ($raeumeIn as $raum) {
            foreach ($raum['heizkoerper'] ?? [] as $hk) {
                if (! empty($hk['heizkoerper_id'])) {
                    $ids[] = $hk['heizkoerper_id'];
                }
            }
        }
        $katalog = $ids ? RadiatorSpec::whereIn('id', array_unique($ids))->get()->keyBy('id') : collect();

        $maxMinVl = null;
        $unzureichend = false;
        $erfasst = false;
        foreach ($ergebnis['raeume'] as $i => &$raum) {
            $heizlast = (float) $raum['auslegungsheizlast_w'];
            $raumtemp = (float) $raum['theta_int_c'];

            $raumMinVl = null;       // beste (niedrigste) Vorlauftemp eines deckenden Systems
            $raumErfasst = false;
            $raumDeckt = false;

            // Heizkörper (EN 442)
            $raum['heizkoerper'] = $this->heizkoerperRaum($raeumeIn[$i]['heizkoerper'] ?? [], $katalog, $heizlast, $raumtemp, $zielVl, $spreizung);
            if ($raum['heizkoerper'] !== null) {
                $raumErfasst = true;
                $minVl = $raum['heizkoerper']['min_vorlauf_c'];
                if ($minVl !== null) {
                    $raumDeckt = true;
                    $raumMinVl = $raumMinVl === null ? $minVl : min($raumMinVl, $minVl);
                }
            }

            // Fußbodenheizung (EN 1264)
            $raum['fussbodenheizung'] = $this->fbhRaum($raeumeIn[$i]['fussbodenheizung'] ?? null, $raum, $heizlast, $raumtemp, $zielVl, $spreizung, (float) ($raeumeIn[$i]['hoehe_m'] ?? 2.5));
            if ($raum['fussbodenheizung'] !== null) {
                $raumErfasst = true;
                $minVl = $raum['fussbodenheizung']['min_vorlauf_c'];
                if ($minVl !== null) {
                    $raumDeckt = true;
                    $raumMinVl = $raumMinVl === null ? $minVl : min($raumMinVl, $minVl);
                }
            }

            $raum['min_vorlauf_c'] = $raumMinVl;
            $raum['emitter_status'] = ! $raumErfasst ? 'na' : ($raumDeckt ? 'gruen' : 'rot');

            if ($raumErfasst) {
                $erfasst = true;
                if (! $raumDeckt) {
                    $unzureichend = true;
                } elseif ($maxMinVl === null || $raumMinVl > $maxMinVl) {
                    $maxMinVl = $raumMinVl;
                }
            }
        }
        unset($raum);

        $vorlauf = [
            'ziel_vorlauf_c' => $zielVl,
            'spreizung_k' => $spreizung,
            'benoetigte_max_vorlauftemp_c' => $unzureichend ? null : $maxMinVl,
            'unzureichend' => $unzureichend,
            'erfasst' => $erfasst,
        ];
        $ergebnis['vorlauf'] = $vorlauf;
        $ergebnis['heizkoerper'] = $vorlauf; // Rückwärtskompatibilität (Controller/UI)
    }

    /**
     * @param  array<int, array<string, mixed>>  $hkInput
     * @return array<string, mixed>|null
     */
    private function heizkoerperRaum(array $hkInput, Collection $katalog, float $heizlast, float $raumtemp, float $zielVl, float $spreizung): ?array
    {
        if ($hkInput === []) {
            return null;
        }

        $resolved = [];
        $qNormSum = 0.0;
        foreach ($hkInput as $hk) {
            $kat = ! empty($hk['heizkoerper_id']) ? ($katalog[$hk['heizkoerper_id']] ?? null) : null;
            $wProM = (float) ($hk['q_norm_w_pro_m'] ?? $kat?->q_norm_w_pro_m ?? 0);
            $qNorm = $wProM * (float) ($hk['laenge_m'] ?? 0) * max(1, (int) ($hk['anzahl'] ?? 1));
            if ($qNorm <= 0) {
                continue;
            }
            $qNormSum += $qNorm;
            $resolved[] = [
                'q_norm_w' => $qNorm,
                'exponent_n' => (float) ($hk['exponent_n'] ?? $kat?->exponent_n ?? 1.30),
                'norm_bedingung' => $hk['norm_bedingung'] ?? $kat?->norm_bedingung ?? '75/65/20',
            ];
        }
        if ($resolved === []) {
            return null;
        }

        $qReal = $this->heizkoerper->qReal($resolved, $zielVl, $raumtemp, $spreizung);

        return [
            'q_norm_w' => (int) round($qNormSum),
            'q_real_w' => (int) round($qReal),
            'deckung_pct' => $heizlast > 0 ? (int) round($qReal / $heizlast * 100) : null,
            'status' => $this->heizkoerper->status($qReal, $heizlast),
            'min_vorlauf_c' => $this->heizkoerper->minVorlauf($resolved, $heizlast, $raumtemp, $spreizung),
            'leistungstabelle' => $this->heizkoerper->leistungstabelle($resolved, $raumtemp),
        ];
    }

    /**
     * @param  array<string, mixed>|null  $fbhInput
     * @param  array<string, mixed>  $raum
     * @return array<string, mixed>|null
     */
    private function fbhRaum(?array $fbhInput, array $raum, float $heizlast, float $raumtemp, float $zielVl, float $spreizung, float $hoehe = 2.5): ?array
    {
        if (empty($fbhInput) || empty($fbhInput['aktiv'] ?? true)) {
            return null;
        }
        // Heizfläche = beheizte Grundfläche des Raums (Richtwert).
        $flaeche = (float) ($fbhInput['flaeche_m2'] ?? $raum['grundflaeche_m2'] ?? 0);
        if ($flaeche <= 0) {
            return null;
        }
        // Zulässige Oberflächentemperatur: Bad 33 °C, sonst 29 °C (EN 1264).
        $maxOberflaeche = ($raum['nutzung'] ?? null) === 'bad' ? 33.0 : 29.0;

        // Randzone: fehlende Außenwandlänge aus den Außenwand-Bauteilen herleiten
        // (Netto-Wandfläche / Raumhöhe), sofern nicht explizit angegeben.
        if (! empty($fbhInput['randzone']) && (float) ($fbhInput['aussenwand_laenge_m'] ?? 0) <= 0 && $hoehe > 0) {
            $wandAussen = 0.0;
            foreach ($raum['bauteile'] ?? [] as $b) {
                if (($b['typ'] ?? null) === 'wand' && ($b['grenzflaeche'] ?? null) === 'aussen') {
                    $wandAussen += (float) ($b['flaeche_eff_m2'] ?? 0);
                }
            }
            if ($wandAussen > 0) {
                $fbhInput['aussenwand_laenge_m'] = round($wandAussen / $hoehe, 1);
            }
        }

        return $this->fussbodenheizung->analyse($fbhInput, $flaeche, $heizlast, $raumtemp, $zielVl, $spreizung, $maxOberflaeche);
    }

    /**
     * Löst die Höhenkorrektur der Norm-Außentemperatur: Basis-θe (PLZ/explizit),
     * Bezugshöhe aus der PLZ-Klimatabelle, Standorthöhe aus der Eingabe.
     *
     * @param  array<string, mixed>  $eingabe
     * @return array<string, mixed>
     */
    private function loeseHoehenkorrektur(array $eingabe): array
    {
        $basis = (float) ($eingabe['norm_aussentemp_c'] ?? -12.0);

        $bezugshoehe = null;
        $plz = $eingabe['standort_plz'] ?? null;
        if ($plz !== null && $plz !== '') {
            $bezugshoehe = $this->klima->findByPlz($plz)?->hoehe_m ?? null;
        }

        $standorthoehe = isset($eingabe['gelaendehoehe_m']) && $eingabe['gelaendehoehe_m'] !== '' && is_numeric($eingabe['gelaendehoehe_m'])
            ? (float) $eingabe['gelaendehoehe_m']
            : null;

        return $this->hoehenkorrektur->korrigiere(
            $basis,
            $standorthoehe,
            $bezugshoehe === null ? null : (float) $bezugshoehe,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function fuerProjekt(HeizlastProjekt $p): array
    {
        $p->loadMissing('raeume.bauteile');

        return $this->berechne([
            'norm_aussentemp_c' => $p->norm_aussentemp_c ?? -12.0,
            'standort_plz' => $p->standort_plz,
            'gelaendehoehe_m' => $p->gelaendehoehe_m,
            'waermebruecken' => $p->waermebruecken,
            'komfortzuschlag_k' => $p->komfortzuschlag_k,
            'intermittierend' => $p->intermittierend,
            'baujahr' => $p->baujahr,
            'sanierungsstufe' => $p->sanierungsstufe,
            'ziel_vorlauf_c' => $p->ziel_vorlauf_c,
            'spreizung_k' => $p->spreizung_k,
            'raeume' => $p->raeume->map(fn ($r) => [
                'name' => $r->name, 'nutzung' => $r->nutzung, 'theta_int_c' => $r->theta_int_c,
                'grundflaeche_m2' => $r->grundflaeche_m2, 'hoehe_m' => $r->hoehe_m, 'luftwechsel_1h' => $r->luftwechsel_1h,
                'heizkoerper' => $r->heizkoerper,
                'fussbodenheizung' => $r->fussbodenheizung,
                'bauteile' => $r->bauteile->map(fn ($b) => [
                    'typ' => $b->typ, 'grenzflaeche' => $b->grenzflaeche, 'flaeche_m2' => $b->flaeche_m2,
                    'u_strategie' => $b->u_strategie, 'u_wert' => $b->u_wert, 'schichten' => $b->schichten,
                    'fenster_typ' => $b->fenster_typ,
                ])->all(),
            ])->all(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $b
     * @return array<string, mixed>
     */
    private function uWert(array $b, int $baujahr, string $sanierung): array
    {
        $typ = $b['typ'] ?? 'wand';
        if (in_array($typ, ['fenster', 'tuer'], true)) {
            // Strategie A: freier U-Wert (Produkt/Override) hat Vorrang vor dem Typ-Schlüssel.
            if (($b['u_strategie'] ?? null) === 'A' && isset($b['u_wert']) && (float) $b['u_wert'] > 0) {
                return $this->u->fensterDirekt((float) $b['u_wert'], $b['quelle'] ?? null);
            }

            return $typ === 'fenster'
                ? $this->u->fenster($b['fenster_typ'] ?? '2fach')
                : $this->u->tuer($b['fenster_typ'] ?? 'standard');
        }

        return match ($b['u_strategie'] ?? 'C') {
            'A' => $this->u->bekannt((float) ($b['u_wert'] ?? 0), $b['quelle'] ?? null),
            'B' => $this->u->ausSchichten($b['schichten'] ?? [], $typ),
            default => $this->u->ausBaualter($baujahr, $typ, $sanierung)
                ?? $this->u->bekannt((float) ($b['u_wert'] ?? 1.0), 'Fallback (keine Baualtersklasse)'),
        };
    }
}
