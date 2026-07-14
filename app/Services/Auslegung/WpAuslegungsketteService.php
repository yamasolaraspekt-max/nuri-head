<?php

namespace App\Services\Auslegung;

use App\Repositories\CatalogDeviceRepository;
use App\Services\Heizlast\BivalenzService;
use App\Services\Heizlast\WaermepumpenMatchService;

/**
 * WP Stufe 3 — Auslegungsketten-Orchestrator (ticket-eigen, führende Schiene).
 *
 * Verdrahtet die vorhandenen Kern-Services in Soll-Reihenfolge und hängt die brachliegende Krone
 * `BivalenzService` JE KANDIDAT (N×) ein → Ranking (E-WP4). **Reuse, keine Parallelberechnung:**
 * die Kern-Services (`WaermepumpenMatchService`, `BivalenzService`, `CatalogDeviceRepository`)
 * werden NUR aufgerufen, nie verändert (Mirror-Analogie).
 *
 * Read-Model (Stufe 3a): liefert ein Ranking-DTO on-the-fly, schreibt NICHTS (keine Persistenz,
 * keine Migration). Die Verankerung/Persistenz am versionierten Anforderungsprofil + Controller-
 * Umstellung ist Stufe 3b.
 *
 * Operanden-Gate (nie raten): fehlender Pflicht-Operand → definierter Gate-Fehlerzustand.
 * E-WP4-Label: jedes Ergebnis ist „informativ, nicht verbindlich" (Stufe 3a immer).
 * E-WP5/Preis-Frage: Investkosten/Verfügbarkeit haben in ticket keine belegbare Quelle → Gewichte
 * werden NICHT angewandt (renormalisiert über die vorhandenen Kriterien), Marker `quelle_fehlt`.
 */
class WpAuslegungsketteService
{
    public function __construct(
        private WaermepumpenMatchService $match,
        private BivalenzService $bivalenz,
        private CatalogDeviceRepository $repo = new CatalogDeviceRepository,
    ) {}

    /** @return array<string,mixed> */
    public function rankeKandidaten(WpAuslegungsEingabe $e): array
    {
        $gates = $this->pruefeGates($e);
        if ($gates !== []) {
            return $this->gateOffen($gates);
        }

        $match = $this->match->kandidaten($e->phiHlKw, (string) $e->wpTyp, (string) $e->heizsystem, (float) $e->vorlaufC, $e->limit);
        $kandidaten = $match['geraete'] ?? [];
        if ($kandidaten === []) {
            return $this->ergebnis([], ['Keine passenden Geräte gefunden — Gerätetyp/Heizsystem/Katalog prüfen.'.($match['hinweis'] ? ' ('.$match['hinweis'].')' : '')]);
        }

        $kennlinien = $this->repo->heatPumps()->keyBy('id');
        $bewertet = [];
        $hinweise = [];
        foreach ($kandidaten as $k) {
            $kl = $kennlinien->get($k['id']);
            if ($kl === null) {
                $hinweise[] = 'Kennlinie für Gerät #'.$k['id'].' nicht auflösbar — übersprungen.';

                continue;
            }
            $biv = $this->bivalenz->berechne($kl, (float) $e->phiHlKw, (float) $e->qHeizKwh, (float) $e->qWwKwh, (bool) $e->wwMitWp, (float) $e->vorlaufC, $e->plz, $e->raumtempC, $e->lat, $e->lon);
            $bewertet[] = $this->bewerteKandidat($k, $biv, (float) $e->vorlaufC);
        }

        return $this->ergebnis($this->sortiere($bewertet), $hinweise);
    }

    /** G1–G5: fehlende Pflicht-Operanden → rule_keys (nie raten). @return array<int,string> */
    private function pruefeGates(WpAuslegungsEingabe $e): array
    {
        $g = [];
        if ($e->phiHlKw === null || $e->phiHlKw <= 0) {
            $g[] = 'bedarf_fehlt';
        }
        if ($e->qHeizKwh === null || $e->qHeizKwh <= 0) {
            $g[] = 'energie_fehlt';           // G2 (E-WP2: Verbrauch vorrangig, Rückfall Heizlast — dokumentiert)
        }
        if ($e->wwMitWp === null) {
            $g[] = 'ww_entscheidung_fehlt';   // G3
        }
        if ($e->vorlaufC === null || $e->vorlaufC <= 0) {
            $g[] = 'vorlauf_fehlt';           // G4
        }
        if ($e->plz === null || $e->plz === '') {
            $g[] = 'plz_fehlt';
        }
        if ($e->wpTyp === null || $e->wpTyp === '' || $e->heizsystem === null || $e->heizsystem === '') {
            $g[] = 'geraetetyp_fehlt';        // E-WP5/G1 (kein hartkodierter luft_wasser/heizkoerper-Default)
        }

        return $g;
    }

    /** @return array<string,mixed> */
    private function bewerteKandidat(array $k, array $biv, float $vorlaufC): array
    {
        // Stufe A — hartes Eignungs-Gate.
        $geeignet = true;
        $grund = null;
        $maxVorlauf = $k['max_vorlauf_c'] ?? null;
        $deckung = (float) ($biv['deckung_ne_pct'] ?? 0);
        $minDeckung = (float) config('wp_auslegung.gate_a.min_deckung_ne_pct', 90);

        if ($maxVorlauf !== null && $vorlaufC > (float) $maxVorlauf) {
            $geeignet = false;
            $grund = 'vorlauf_ueber_grenze';
        } elseif ($deckung < $minDeckung) {
            $geeignet = false;
            $grund = 'deckung_zu_gering';
        }

        $nibe = strcasecmp((string) ($k['hersteller'] ?? ''), (string) config('wp_auslegung.nibe_hersteller', 'NIBE')) === 0;

        return [
            'id' => $k['id'],
            'hersteller' => $k['hersteller'] ?? null,
            'modell' => $k['modell'] ?? null,
            'nibe' => $nibe,                       // gekennzeichnet, NICHT geboostet
            'geeignet' => $geeignet,
            'ausschluss_grund' => $grund,
            'bivalenz' => [
                'bivalenzpunkt_c' => $biv['bivalenzpunkt_c'] ?? null,
                'deckung_ne_pct' => $biv['deckung_ne_pct'] ?? null,
                'jaz' => $biv['jaz'] ?? null,
                'estab_waerme_anteil_pct' => $biv['estab_waerme_anteil_pct'] ?? null,
                'laufstunden_h' => $biv['laufstunden_h'] ?? null,
                'status' => $biv['bivalenz_status'] ?? null,
            ],
            'kriterien' => ['jaz' => (float) ($biv['jaz'] ?? 0)],
            'invest_quelle_fehlt' => ! (bool) config('wp_auslegung.quelle_vorhanden.investitionskosten', false),
            'verfuegbarkeit_quelle_fehlt' => ! (bool) config('wp_auslegung.quelle_vorhanden.verfuegbarkeit', false),
            'score' => null, // in sortiere() gesetzt
        ];
    }

    /**
     * Stufe B — Sortierung der Geeigneten über die Kriterien MIT belegbarer Quelle (E-WP5).
     * Gewichte renormalisiert; ungeeignete Kandidaten ans Ende (mit Grund).
     *
     * @param  array<int,array<string,mixed>>  $bewertet
     * @return array<int,array<string,mixed>>
     */
    private function sortiere(array $bewertet): array
    {
        $gewichte = (array) config('wp_auslegung.gewichte', []);
        $quelle = (array) config('wp_auslegung.quelle_vorhanden', []);
        $verfuegbar = array_filter(['jaz' => 'jaz', 'investitionskosten' => 'investitionskosten', 'verfuegbarkeit' => 'verfuegbarkeit'], fn ($kkey) => (bool) ($quelle[$kkey] ?? false));
        $gewichtSum = array_sum(array_map(fn ($kkey) => (float) ($gewichte[$kkey] ?? 0), $verfuegbar)) ?: 1.0;

        // Normierungsbasis je Kriterium (max über geeignete Kandidaten).
        $geeignete = array_filter($bewertet, fn ($b) => $b['geeignet']);
        $maxJaz = max(array_map(fn ($b) => (float) ($b['kriterien']['jaz'] ?? 0), $geeignete ?: [['kriterien' => ['jaz' => 1]]])) ?: 1.0;

        foreach ($bewertet as &$b) {
            $score = 0.0;
            if (in_array('jaz', $verfuegbar, true)) {
                $jazNorm = (float) ($b['kriterien']['jaz'] ?? 0) / $maxJaz;
                $score += $jazNorm * ((float) ($gewichte['jaz'] ?? 0) / $gewichtSum);
            }
            // investitionskosten/verfuegbarkeit: keine Quelle → nicht angewandt (kein erfundener Wert).
            $b['score'] = round($score, 4);
        }
        unset($b);

        usort($bewertet, function ($a, $c) {
            if ($a['geeignet'] !== $c['geeignet']) {
                return $a['geeignet'] ? -1 : 1; // Geeignete zuerst
            }

            return $c['score'] <=> $a['score']; // dann Score absteigend
        });

        return array_values($bewertet);
    }

    /**
     * @param  array<int,array<string,mixed>>  $kandidaten
     * @param  array<int,string>  $hinweise
     * @return array<string,mixed>
     */
    private function ergebnis(array $kandidaten, array $hinweise): array
    {
        $quelle = (array) config('wp_auslegung.quelle_vorhanden', []);
        $ausgesetzt = array_keys(array_filter(['investitionskosten' => 'i', 'verfuegbarkeit' => 'v'], fn ($k) => ! (bool) ($quelle[$k] ?? false), ARRAY_FILTER_USE_KEY));
        if ($ausgesetzt !== []) {
            $hinweise[] = 'Ranking-Kriterien ohne Datenquelle (Gewicht nicht angewandt): '.implode(', ', $ausgesetzt).' — Preisquelle: Yama-Entscheidung offen (E-WP5).';
        }

        return [
            'anwendbar' => true,
            'label' => (string) config('wp_auslegung.label_informativ', 'informativ, nicht verbindlich'),
            'verbindlich' => false, // Stufe 3a: immer informativ (bis Yama-Freigabe UND AP-1-Belastbarkeit)
            'gewichte' => (array) config('wp_auslegung.gewichte', []),
            'gewichte_ausgesetzt' => $ausgesetzt,
            'kandidaten' => $kandidaten,
            'hinweise' => $hinweise,
        ];
    }

    /** @param array<int,string> $gates @return array<string,mixed> */
    private function gateOffen(array $gates): array
    {
        return [
            'anwendbar' => false,
            'label' => (string) config('wp_auslegung.label_informativ', 'informativ, nicht verbindlich'),
            'verbindlich' => false,
            'gates_offen' => $gates,
            'kandidaten' => [],
            'hinweise' => ['Auslegung nicht möglich — fehlende Operanden (Vorschlag+Bestätigung nötig): '.implode(', ', $gates)],
        ];
    }
}
