<?php

namespace App\Services\Geometrie;

/**
 * SzeneProjektionService — Projektion Hausplaner-Szene → gebaeude_geometrie. **VERDRAHTET seit P2-2.**
 *
 * Grundlage: docs/planner-spec-szene-projektion.md; eingefrorener Vertrag (nur als Referenz gelesen,
 * kein Code kopiert) playground src/hausplaner/geometry/roomDetection.ts + geometry/wallGeometry.ts.
 *
 * Übersetzt scene_json (levels[], nodes[]: WallNode/OpeningNode) je Geschoss in die BESTEHENDE
 * RaumGeometrie-Struktur (polygon, wand_segmente, oeffnungen) — das Format, das
 * GeometrieAbleitungService::ausGeometrie erwartet. Jedes Raum-Polygon wird VOR der Ausgabe am
 * bestehenden TopologieGate geprüft; ungültige Geometrie wird abgelehnt (GeometrieUngueltigException),
 * nie still projiziert.
 *
 * P2-1b: planare Raumerkennung (Wandachsen-Graph, T-Punkt-Teilung, Halbkanten-Umläufe, Innenflächen =
 * positive Shoelace). MEHRRAUM. innen/aussen: eine Kante, deren BEIDE Halbkanten in Innenräumen liegen,
 * ist 'innen' (kein Azimut); sonst 'aussen' (Azimut aus rechter Normale, Nord=+y). decke/boden ehrlich null.
 * Verdrahtung/Schreiben nach gebaeude_geometrie war P2-2 (Yama-Go) — **sie ist erfolgt.**
 *
 * **BERICHTIGT 20.08.: hier stand bis heute "schreibt NICHTS und wird von KEINEM Produktivpfad
 * aufgerufen". Beide Sätze sind seit der P2-2-Verdrahtung falsch.** Die Kette, Glied für Glied
 * nachgemessen:
 *
 *   routes/web.php:4999                     POST /objekt/{objekt}/uebernehmen
 *     -> HausplanerController::uebernehmen   (:228  app(UebernehmeSzeneInAuslegung::class))
 *       -> UebernehmeSzeneInAuslegung        (:43  Konstruktor-Injektion, :61  ->projiziere())
 *         -> DIESE Klasse
 *   Feature-Tests: tests/Feature/Hausplaner/UebernehmeSzeneInAuslegungTest.php · UebernahmeKnopfTest.php
 *
 * Richtig bleibt: **diese Klasse selbst schreibt nichts** — sie projiziert, das Schreiben besorgt
 * UebernehmeSzeneInAuslegung über den Versionspfad. Ein Kopf, der seinen eigenen Zustand falsch
 * angibt, ist gefährlicher als gar keiner: er lädt dazu ein, hier ohne Rücksicht zu ändern.
 */
class SzeneProjektionService
{
    public function __construct(private TopologieGate $gate = new TopologieGate) {}

    /**
     * @param  array<string,mixed>  $scene  dekodiertes scene_json
     * @return array<int, array<string,mixed>>  Liste RaumGeometrieProjektion-förmiger Arrays
     */
    public function projiziere(array $scene): array
    {
        $nodes = $scene['nodes'] ?? [];
        $walls = array_values(array_filter($nodes, fn ($n) => ($n['type'] ?? '') === 'wall'));
        $oeffnungen = array_values(array_filter(
            $nodes,
            fn ($n) => in_array($n['type'] ?? '', ['window', 'door', 'opening'], true)
        ));

        $raeume = [];
        foreach ($scene['levels'] ?? [] as $level) {
            $levelWalls = array_values(array_filter(
                $walls,
                fn ($w) => ($w['levelId'] ?? null) === ($level['id'] ?? null)
            ));
            if (count($levelWalls) < 3) {
                continue;
            }
            foreach ($this->projiziereGeschoss($levelWalls, $oeffnungen, $level) as $raum) {
                $raeume[] = $raum;
            }
        }

        return $raeume;
    }

    /**
     * @param  array<int,array<string,mixed>>  $walls
     * @param  array<int,array<string,mixed>>  $oeffnungen
     * @param  array<string,mixed>  $level
     * @return array<int,array<string,mixed>>
     */
    private function projiziereGeschoss(array $walls, array $oeffnungen, array $level): array
    {
        // 1) Kanten (an T-Punkten geteilt) sammeln.
        $endpunkte = [];
        foreach ($walls as $w) {
            $endpunkte[] = $this->pt($w['start']);
            $endpunkte[] = $this->pt($w['end']);
        }

        $kanten = []; // [a,b,wallId,offVon,offBis]
        foreach ($walls as $w) {
            $s = $this->pt($w['start']);
            $e = $this->pt($w['end']);
            $laenge = hypot($e['x'] - $s['x'], $e['y'] - $s['y']);
            if ($laenge == 0.0) {
                continue;
            }
            // Split-Punkte als Liste {off,p} (KEINE Float-Array-Keys — PHP würde die zu int casten).
            $paare = [['off' => 0.0, 'p' => $s], ['off' => $laenge, 'p' => $e]];
            foreach ($endpunkte as $p) {
                if ($this->liegtAufStrecke($p, $s, $e)) {
                    $paare[] = ['off' => hypot($p['x'] - $s['x'], $p['y'] - $s['y']), 'p' => $p];
                }
            }
            usort($paare, fn ($l, $r) => $l['off'] <=> $r['off']);
            $punkte = [];
            $gesehen = [];
            foreach ($paare as $pr) {
                $k = $this->key($pr['p']);
                if (! isset($gesehen[$k])) {
                    $gesehen[$k] = true;
                    $punkte[] = $pr;
                }
            }
            for ($i = 0; $i < count($punkte) - 1; $i++) {
                $kanten[] = [
                    'a' => $this->key($punkte[$i]['p']), 'b' => $this->key($punkte[$i + 1]['p']),
                    'wallId' => $w['id'] ?? null, 'offVon' => $punkte[$i]['off'], 'offBis' => $punkte[$i + 1]['off'],
                ];
            }
        }

        // 2) Halbkanten (beide Richtungen) + Winkel; ausgehend je Knoten nach Winkel sortiert.
        $H = []; // Halbkanten (Index-basiert, mutierbar)
        $kanteHk = []; // kanteId => [hkIdx1, hkIdx2]
        foreach ($kanten as $kid => $k) {
            $pa = $this->parse($k['a']);
            $pb = $this->parse($k['b']);
            $i1 = count($H);
            $H[] = ['von' => $k['a'], 'zu' => $k['b'], 'kanteId' => $kid,
                'winkel' => atan2($pb['y'] - $pa['y'], $pb['x'] - $pa['x']), 'flaecheId' => -1,
                'wallId' => $k['wallId'], 'offVon' => $k['offVon'], 'offBis' => $k['offBis']];
            $i2 = count($H);
            $H[] = ['von' => $k['b'], 'zu' => $k['a'], 'kanteId' => $kid,
                'winkel' => atan2($pa['y'] - $pb['y'], $pa['x'] - $pb['x']), 'flaecheId' => -1,
                'wallId' => $k['wallId'], 'offVon' => $k['offVon'], 'offBis' => $k['offBis']];
            $kanteHk[$kid] = [$i1, $i2];
        }

        $ausgehend = []; // node => list of hkIdx
        foreach ($H as $i => $h) {
            $ausgehend[$h['von']][] = $i;
        }
        foreach ($ausgehend as $node => $liste) {
            usort($liste, fn ($l, $r) => $H[$l]['winkel'] <=> $H[$r]['winkel']);
            $ausgehend[$node] = $liste;
        }

        // 3) Flächen-Umläufe einsammeln (jede Halbkante genau einmal ⇒ terminiert immer).
        $faceInterior = [];   // flaecheId => bool (positive Shoelace = Innenraum)
        $faceUmlauf = [];     // flaecheId => list of hkIdx
        $zaehler = 0;
        foreach (array_keys($H) as $startIdx) {
            if ($H[$startIdx]['flaecheId'] !== -1) {
                continue;
            }
            $umlauf = [];
            $akt = $startIdx;
            while ($H[$akt]['flaecheId'] === -1) {
                $H[$akt]['flaecheId'] = $zaehler;
                $umlauf[] = $akt;
                $akt = $this->naechste($akt, $H, $ausgehend);
            }
            $polygon = array_map(fn ($i) => $this->parse($H[$i]['von']), $umlauf);
            $flaeche = count($polygon) >= 3 ? $this->signierteFlaeche($polygon) : 0.0;
            $faceInterior[$zaehler] = $flaeche > 0;
            $faceUmlauf[$zaehler] = $umlauf;
            $zaehler++;
        }

        // 4) Innenräume projizieren.
        $raeume = [];
        foreach ($faceUmlauf as $fid => $umlauf) {
            if (! $faceInterior[$fid]) {
                continue; // Außenumlauf/entartet
            }
            $polygon = array_map(fn ($i) => ['x' => (int) $this->parse($H[$i]['von'])['x'], 'y' => (int) $this->parse($H[$i]['von'])['y']], $umlauf);

            $pruef = $this->gate->pruefePolygon($polygon);
            if (! $pruef->valid) {
                throw new GeometrieUngueltigException($pruef);
            }

            $segmente = [];
            foreach ($umlauf as $hk) {
                $von = ['x' => (int) $this->parse($H[$hk]['von'])['x'], 'y' => (int) $this->parse($H[$hk]['von'])['y']];
                $bis = ['x' => (int) $this->parse($H[$hk]['zu'])['x'], 'y' => (int) $this->parse($H[$hk]['zu'])['y']];
                $gegen = $this->gegenHalbkante($hk, $H, $kanteHk);
                $istInnen = $gegen !== null && ($faceInterior[$H[$gegen]['flaecheId']] ?? false);
                $segmente[] = [
                    'von' => $von,
                    'bis' => $bis,
                    'grenzflaeche' => $istInnen ? 'innen' : 'aussen',
                    'azimut_grad' => $istInnen ? null : $this->azimutRechteNormale($von, $bis),
                    'bauteil_typ' => 'wand',
                    'oeffnungen' => $this->oeffnungenDerKante($H[$hk], $oeffnungen),
                ];
            }

            $raeume[] = [
                'geschoss' => (int) ($level['sortOrder'] ?? 0),
                'polygon' => $polygon,
                'hoehe_mm' => $this->hoehe($level, $walls),
                'wand_segmente' => $segmente,
                'decke' => null, // ehrlich unbestimmt (Operanden-Gate)
                'boden' => null,
            ];
        }

        return $raeume;
    }

    /** Nächste Halbkante des Umlaufs: an h.zu die im Uhrzeigersinn nächste NACH der Gegenkante. */
    private function naechste(int $i, array $H, array $ausgehend): int
    {
        $liste = $ausgehend[$H[$i]['zu']];
        $pos = -1;
        foreach ($liste as $p => $j) {
            if ($H[$j]['zu'] === $H[$i]['von'] && $H[$j]['kanteId'] === $H[$i]['kanteId']) {
                $pos = $p;
                break;
            }
        }
        if ($pos === -1) {
            foreach ($liste as $p => $j) {
                if ($H[$j]['zu'] === $H[$i]['von']) {
                    $pos = $p;
                    break;
                }
            }
        }
        $n = count($liste);
        $naechstePos = ($pos - 1 + $n) % $n;

        return $liste[$naechstePos];
    }

    /** Die Gegen-Halbkante (andere Richtung derselben Kante). */
    private function gegenHalbkante(int $i, array $H, array $kanteHk): ?int
    {
        [$a, $b] = $kanteHk[$H[$i]['kanteId']];

        return $i === $a ? $b : $a;
    }

    /**
     * @param  array<string,mixed>  $hk  Halbkante (trägt wallId, offVon, offBis)
     * @param  array<int,array<string,mixed>>  $oeffnungen
     * @return array<int,array<string,mixed>>
     */
    private function oeffnungenDerKante(array $hk, array $oeffnungen): array
    {
        $typMap = ['window' => 'fenster', 'door' => 'tuer', 'opening' => 'oeffnung'];
        $out = [];
        foreach ($oeffnungen as $o) {
            if (($o['hostWallId'] ?? null) !== ($hk['wallId'] ?? null)) {
                continue;
            }
            // Öffnung liegt im Offset-Fenster dieser (ggf. an T-Punkten geteilten) Kante.
            $off = (float) ($o['offsetFromWallStart'] ?? 0);
            if ($off < $hk['offVon'] || $off >= $hk['offBis']) {
                continue;
            }
            $out[] = [
                'typ' => $typMap[$o['type'] ?? 'opening'] ?? 'oeffnung',
                'breite_mm' => (int) ($o['width'] ?? 0),
                'hoehe_mm' => (int) ($o['height'] ?? 0),
                'bruestung_mm' => (int) ($o['sillHeight'] ?? 0),
            ];
        }

        return $out;
    }

    /** Azimut der rechten (bei CCW-Innenraum: äußeren) Normalen — Nord = +y = 0°, Ost = 90°. */
    private function azimutRechteNormale(array $von, array $bis): int
    {
        $dx = $bis['x'] - $von['x'];
        $dy = $bis['y'] - $von['y'];
        $grad = (int) round(rad2deg(atan2($dy, -$dx)));

        return (($grad % 360) + 360) % 360;
    }

    /** Liegt p exakt (mm-Integer, kollinear) auf der OFFENEN Strecke a–b? */
    private function liegtAufStrecke(array $p, array $a, array $b): bool
    {
        $kreuz = ($b['x'] - $a['x']) * ($p['y'] - $a['y']) - ($b['y'] - $a['y']) * ($p['x'] - $a['x']);
        if ($kreuz != 0.0) {
            return false;
        }
        $skalar = ($p['x'] - $a['x']) * ($b['x'] - $a['x']) + ($p['y'] - $a['y']) * ($b['y'] - $a['y']);
        $laengeQ = ($b['x'] - $a['x']) ** 2 + ($b['y'] - $a['y']) ** 2;

        return $skalar > 0 && $skalar < $laengeQ;
    }

    /** @param array<int,array{x:float|int,y:float|int}> $polygon */
    private function signierteFlaeche(array $polygon): float
    {
        $summe = 0.0;
        $n = count($polygon);
        for ($i = 0; $i < $n; $i++) {
            $p = $polygon[$i];
            $q = $polygon[($i + 1) % $n];
            $summe += $p['x'] * $q['y'] - $q['x'] * $p['y'];
        }

        return $summe / 2.0;
    }

    /**
     * @param  array<string,mixed>  $level
     * @param  array<int,array<string,mixed>>  $walls
     */
    private function hoehe(array $level, array $walls): ?int
    {
        if (isset($level['defaultWallHeight'])) {
            return (int) $level['defaultWallHeight'];
        }
        if (isset($walls[0]['height'])) {
            return (int) $walls[0]['height'];
        }

        return null;
    }

    /** @param array<string,mixed> $p @return array{x:int,y:int} */
    private function pt(array $p): array
    {
        return ['x' => (int) ($p['x'] ?? 0), 'y' => (int) ($p['y'] ?? 0)];
    }

    /** @param array{x:int,y:int} $p */
    private function key(array $p): string
    {
        return $p['x'].','.$p['y'];
    }

    /** @return array{x:int,y:int} */
    private function parse(string $k): array
    {
        [$x, $y] = explode(',', $k);

        return ['x' => (int) $x, 'y' => (int) $y];
    }
}
