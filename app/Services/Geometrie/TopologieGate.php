<?php

namespace App\Services\Geometrie;

use App\Services\Heizlast\GeometrieAbleitungService;
use App\Support\GeometrieToleranz;

/**
 * G0b / AP-4b — Topologie-Gate (vorgelagerter, ticket-eigener Pflichtdurchgang).
 *
 * Validiert Polygon-Geometrie VOR jeder Flächen-/Volumenberechnung und -Persistenz.
 * Ungültige Geometrie wird ABGELEHNT (Blocker), niemals still gerechnet, niemals still repariert.
 *
 * WICHTIG (Mirror-Erhalt, G0a-Beweis): Dieser Service fasst den gespiegelten Kern NICHT an.
 * `GeometrieAbleitungService`/`RaumHuelleService` bleiben byte-unverändert; die Flächenmathematik
 * wird nur hinter bestandenem Gate aufgerufen (Berechnungsfassade). Toleranzen ausschließlich aus
 * dem Toleranzvertrag (config/geometrie.php via GeometrieToleranz).
 *
 * Regeln (Startblock §2.2): 1 Mindestpunktzahl · 2 geschlossener Ring (Konvention, s. u.) ·
 * 3 Mindestsegmentlänge · 4 kein Selbstschnitt · 5 keine Selbstberührung · 6 keine Degeneriertheit ·
 * 7 Öffnungsplausibilität.
 *
 * Zu Regel 2: Die bestehende Engine schließt den Ring implizit (Shoelace mit Modulo-Umlauf).
 * „Geschlossen" ist damit Konvention; ein explizit doppelter Schlusspunkt wird dedupliziert, und
 * nicht-umschließende Eingaben fallen über Regel 6 (Fläche < Mindestfläche). Ein zusammenfallender
 * NICHT-benachbarter Eckpunkt (Ring-Einschnürung) wird über Regel 5 (Selbstberührung) abgelehnt.
 */
class TopologieGate
{
    public function __construct(private GeometrieAbleitungService $ableitung = new GeometrieAbleitungService) {}

    /**
     * Prüft ein Polygon (Punktliste [{x,y}] in mm) gegen die Regeln 1–6.
     *
     * @param  array<int,array{x:int|float,y:int|float}>  $punkte
     */
    public function pruefePolygon(array $punkte): TopologieErgebnis
    {
        $blocker = [];
        $pts = $this->dedupliziere($punkte);

        // Regel 1 — Mindestpunktzahl nach Deduplizierung.
        if (count($pts) < 3) {
            $blocker[] = ['rule_key' => 'min_punkte', 'message' => 'Weniger als 3 unterscheidbare Punkte.', 'indices' => []];

            return TopologieErgebnis::abgelehnt($blocker); // ohne ≥3 Punkte sind die übrigen Regeln nicht sinnvoll
        }

        // Regel 3 — Mindestsegmentlänge (inkl. Schlusskante).
        $minSeg = GeometrieToleranz::minSegmentMm();
        $n = count($pts);
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            if ($this->distanz($pts[$i], $pts[$j]) < $minSeg) {
                $blocker[] = ['rule_key' => 'min_segment', 'message' => "Segment kürzer als {$minSeg} mm.", 'indices' => [$i, $j]];
            }
        }

        // Regel 5 — Selbstberührung: zusammenfallende NICHT-benachbarte Eckpunkte.
        $tol = GeometrieToleranz::punktgleichheitMm();
        for ($i = 0; $i < $n; $i++) {
            for ($k = $i + 1; $k < $n; $k++) {
                if ($this->benachbart($i, $k, $n)) {
                    continue;
                }
                if ($this->distanz($pts[$i], $pts[$k]) <= $tol) {
                    $blocker[] = ['rule_key' => 'selbstberuehrung', 'message' => 'Ring berührt sich in zusammenfallenden Ecken.', 'indices' => [$i, $k]];
                }
            }
        }

        // Regel 4 — Selbstschnitt: kreuzende, nicht-benachbarte Kanten.
        for ($i = 0; $i < $n; $i++) {
            for ($k = $i + 1; $k < $n; $k++) {
                if ($this->kantenBenachbart($i, $k, $n)) {
                    continue;
                }
                if ($this->segmenteSchneiden($pts[$i], $pts[($i + 1) % $n], $pts[$k], $pts[($k + 1) % $n])) {
                    $blocker[] = ['rule_key' => 'selbstschnitt', 'message' => 'Kanten schneiden sich (selbstschneidendes Polygon).', 'indices' => [$i, $k]];
                }
            }
        }

        // Regel 6 — Degeneriertheit: Rohfläche unter Mindestfläche (deckt kollinear/Nullfläche ab).
        if ($this->rohFlaecheMm2($pts) < GeometrieToleranz::minPolygonFlaecheMm2()) {
            $blocker[] = ['rule_key' => 'degeneriert', 'message' => 'Polygonfläche zu klein oder Punkte kollinear.', 'indices' => []];
        }

        return $blocker === [] ? TopologieErgebnis::ok() : TopologieErgebnis::abgelehnt($blocker);
    }

    /**
     * Regel 7 — Öffnungsplausibilität: Σ Außen-Öffnungsfläche ≤ Σ Außen-Wandfläche.
     * Arbeitet auf den bereits abgeleiteten Bauteilen (gleiche Form wie RaumHuelleService), keine neue Formel.
     *
     * @param  array<int,array<string,mixed>>  $bauteile
     */
    public function pruefeOeffnungen(array $bauteile): TopologieErgebnis
    {
        $wand = 0.0;
        $oeffnung = 0.0;
        foreach ($bauteile as $b) {
            if (($b['grenzflaeche'] ?? 'aussen') !== 'aussen') {
                continue;
            }
            $typ = $b['typ'] ?? '';
            $a = (float) ($b['flaeche_m2'] ?? 0);
            if ($typ === 'wand') {
                $wand += $a;
            } elseif (in_array($typ, ['fenster', 'tuer'], true)) {
                $oeffnung += $a;
            }
        }

        // Gleichheit ist zulässig; nur Überschreitung ist Blocker (Toleranz recalc_abweichung_rel).
        $tol = GeometrieToleranz::recalcAbweichungRel() * max(1.0, $wand);
        if ($oeffnung > $wand + $tol) {
            return TopologieErgebnis::abgelehnt([[
                'rule_key' => 'oeffnung_groesser_wand',
                'message' => 'Summe der Öffnungsflächen überschreitet die Außenwandfläche.',
                'indices' => [],
            ]]);
        }

        return TopologieErgebnis::ok();
    }

    /**
     * Berechnungsfassade: liefert die Polygonfläche NUR bei gültiger Topologie, sonst Exception.
     * Für gültige Geometrie identisch zu GeometrieAbleitungService::polygonFlaecheM2 (Golden-Unverändert).
     *
     * @param  array<int,array{x:int|float,y:int|float}>  $punkte
     */
    public function flaecheOderException(array $punkte): float
    {
        $ergebnis = $this->pruefePolygon($punkte);
        if (! $ergebnis->valid) {
            throw new GeometrieUngueltigException($ergebnis);
        }

        return $this->ableitung->polygonFlaecheM2($punkte);
    }

    // ---- Geometrie-Helfer (rein, keine Persistenz) ----

    /**
     * @param  array<int,array{x:int|float,y:int|float}>  $punkte
     * @return array<int,array{x:float,y:float}>
     */
    private function dedupliziere(array $punkte): array
    {
        $tol = GeometrieToleranz::punktgleichheitMm();
        $out = [];
        foreach ($punkte as $p) {
            $q = ['x' => (float) ($p['x'] ?? 0), 'y' => (float) ($p['y'] ?? 0)];
            if ($out !== [] && $this->distanz(end($out), $q) <= $tol) {
                continue;
            }
            $out[] = $q;
        }
        // expliziten Schlusspunkt (== Startpunkt) entfernen
        if (count($out) > 1 && $this->distanz($out[0], end($out)) <= $tol) {
            array_pop($out);
        }

        return array_values($out);
    }

    /** @param array{x:float,y:float} $a @param array{x:float,y:float} $b */
    private function distanz(array $a, array $b): float
    {
        return hypot($a['x'] - $b['x'], $a['y'] - $b['y']);
    }

    private function benachbart(int $i, int $k, int $n): bool
    {
        $d = abs($i - $k);

        return $d === 1 || $d === $n - 1; // Nachbarn im Ring
    }

    /** Zwei KANTEN i (i→i+1) und k (k→k+1) sind benachbart, wenn sie einen Eckpunkt teilen. */
    private function kantenBenachbart(int $i, int $k, int $n): bool
    {
        $iNext = ($i + 1) % $n;
        $kNext = ($k + 1) % $n;

        return $i === $k || $i === $kNext || $iNext === $k || $iNext === $kNext;
    }

    /** @param array<int,array{x:float,y:float}> $pts */
    private function rohFlaecheMm2(array $pts): float
    {
        $n = count($pts);
        $summe = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $summe += $pts[$i]['x'] * $pts[$j]['y'] - $pts[$j]['x'] * $pts[$i]['y'];
        }

        return abs($summe) / 2.0;
    }

    /**
     * Echter Segmentschnitt-Test (Orientierungsverfahren).
     *
     * @param  array{x:float,y:float}  $p1 @param array{x:float,y:float} $p2
     * @param  array{x:float,y:float}  $p3 @param array{x:float,y:float} $p4
     */
    private function segmenteSchneiden(array $p1, array $p2, array $p3, array $p4): bool
    {
        $d1 = $this->orientierung($p3, $p4, $p1);
        $d2 = $this->orientierung($p3, $p4, $p2);
        $d3 = $this->orientierung($p1, $p2, $p3);
        $d4 = $this->orientierung($p1, $p2, $p4);

        if ((($d1 > 0 && $d2 < 0) || ($d1 < 0 && $d2 > 0))
            && (($d3 > 0 && $d4 < 0) || ($d3 < 0 && $d4 > 0))) {
            return true; // echter Kreuzungsschnitt
        }

        return false;
    }

    /** > 0 = gegen Uhrzeigersinn, < 0 = im Uhrzeigersinn, 0 = kollinear. */
    private function orientierung(array $a, array $b, array $c): float
    {
        return ($b['x'] - $a['x']) * ($c['y'] - $a['y']) - ($b['y'] - $a['y']) * ($c['x'] - $a['x']);
    }
}
