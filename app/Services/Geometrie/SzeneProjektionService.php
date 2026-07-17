<?php

namespace App\Services\Geometrie;

/**
 * SzeneProjektionService (P2-1a) — REINE, UNVERDRAHTETE Projektion Hausplaner-Szene → gebaeude_geometrie.
 *
 * Grundlage: docs/planner-spec-szene-projektion.md; eingefrorener Vertrag (nur als Referenz gelesen,
 * kein Code kopiert) playground src/hausplaner/projection/raumProjektion.ts + geometry/wallGeometry.ts.
 *
 * Übersetzt scene_json (levels[], nodes[]: WallNode/OpeningNode) je Geschoss in die BESTEHENDE
 * RaumGeometrie-Struktur (polygon, wand_segmente, oeffnungen) — das Format, das
 * GeometrieAbleitungService::ausGeometrie erwartet. Jedes Raum-Polygon wird VOR der Ausgabe am
 * bestehenden TopologieGate geprüft; ungültige Geometrie wird abgelehnt (GeometrieUngueltigException),
 * nie still projiziert.
 *
 * P2-1a-Umfang (bewusst schmal): EIN geschlossener Wand-Umlauf je Geschoss, alle Wände 'aussen'.
 * Innen/aussen bei geteilten Kanten (Mehrraum) = P2-1b; Verdrahtung/Schreiben nach gebaeude_geometrie = P2-2.
 * Diese Klasse schreibt NICHTS und wird von KEINEM Produktivpfad aufgerufen.
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
            $raum = $this->projiziereGeschoss($levelWalls, $oeffnungen, $level);
            if ($raum !== null) {
                $raeume[] = $raum;
            }
        }

        return $raeume;
    }

    /**
     * @param  array<int,array<string,mixed>>  $walls
     * @param  array<int,array<string,mixed>>  $oeffnungen
     * @param  array<string,mixed>  $level
     * @return array<string,mixed>|null
     */
    private function projiziereGeschoss(array $walls, array $oeffnungen, array $level): ?array
    {
        $corners = $this->kette($walls);
        if ($corners === null) {
            return null; // kein geschlossener Umlauf — ehrlich nichts, keine erfundene Geometrie
        }

        $polygon = array_map(fn ($p) => ['x' => (int) $p['x'], 'y' => (int) $p['y']], $corners);

        $pruef = $this->gate->pruefePolygon($polygon);
        if (! $pruef->valid) {
            throw new GeometrieUngueltigException($pruef);
        }

        if ($this->flaecheDoppelt($polygon) < 0) {
            $polygon = array_values(array_reverse($polygon)); // CCW erzwingen (Außennormale = rechte Normale)
        }

        $n = count($polygon);
        $segmente = [];
        for ($i = 0; $i < $n; $i++) {
            $von = $polygon[$i];
            $bis = $polygon[($i + 1) % $n];
            $wall = $this->wandFuerKante($walls, $von, $bis);
            $segmente[] = [
                'von' => $von,
                'bis' => $bis,
                'grenzflaeche' => 'aussen',
                'azimut_grad' => $this->azimutRechteNormale($von, $bis),
                'bauteil_typ' => 'wand',
                'oeffnungen' => $wall === null ? [] : $this->oeffnungenDerWand($wall, $oeffnungen),
            ];
        }

        return [
            'geschoss' => (int) ($level['sortOrder'] ?? 0),
            'polygon' => $polygon,
            'hoehe_mm' => $this->hoehe($level, $walls),
            'wand_segmente' => $segmente,
            'decke' => null, // ehrlich unbestimmt (Operanden-Gate) — kein erfundener bauteil_typ
            'boden' => null,
        ];
    }

    /**
     * Ordnet die Wände zu EINER geschlossenen Eckenfolge; null, wenn die Kette nicht (sauber) schließt.
     *
     * @param  array<int,array<string,mixed>>  $walls
     * @return array<int,array{x:int,y:int}>|null
     */
    private function kette(array $walls): ?array
    {
        $rest = $walls;
        $first = array_shift($rest);
        $start = ['x' => (int) $first['start']['x'], 'y' => (int) $first['start']['y']];
        $ende = ['x' => (int) $first['end']['x'], 'y' => (int) $first['end']['y']];
        $corners = [$start, $ende];
        $current = $ende;

        while (count($rest) > 0) {
            $found = null;
            $next = null;
            foreach ($rest as $idx => $w) {
                $ws = ['x' => (int) $w['start']['x'], 'y' => (int) $w['start']['y']];
                $we = ['x' => (int) $w['end']['x'], 'y' => (int) $w['end']['y']];
                if ($this->samePoint($ws, $current)) {
                    $next = $we;
                    $found = $idx;
                    break;
                }
                if ($this->samePoint($we, $current)) {
                    $next = $ws;
                    $found = $idx;
                    break;
                }
            }
            if ($found === null) {
                return null; // Kette bricht ab
            }
            array_splice($rest, $found, 1);
            if ($this->samePoint($next, $corners[0])) {
                return count($rest) === 0 ? $corners : null; // frühzeitiger Schluss ⇒ mehrere Schleifen (P2-1a: nur eine)
            }
            $corners[] = $next;
            $current = $next;
        }

        return null; // nie zu corners[0] zurückgekehrt
    }

    /**
     * @param  array<int,array<string,mixed>>  $walls
     * @param  array{x:int,y:int}  $von
     * @param  array{x:int,y:int}  $bis
     * @return array<string,mixed>|null
     */
    private function wandFuerKante(array $walls, array $von, array $bis): ?array
    {
        foreach ($walls as $w) {
            $ws = ['x' => (int) $w['start']['x'], 'y' => (int) $w['start']['y']];
            $we = ['x' => (int) $w['end']['x'], 'y' => (int) $w['end']['y']];
            if (($this->samePoint($ws, $von) && $this->samePoint($we, $bis))
                || ($this->samePoint($ws, $bis) && $this->samePoint($we, $von))) {
                return $w;
            }
        }

        return null;
    }

    /**
     * @param  array<string,mixed>  $wall
     * @param  array<int,array<string,mixed>>  $oeffnungen
     * @return array<int,array<string,mixed>>
     */
    private function oeffnungenDerWand(array $wall, array $oeffnungen): array
    {
        $typMap = ['window' => 'fenster', 'door' => 'tuer', 'opening' => 'oeffnung'];
        $out = [];
        foreach ($oeffnungen as $o) {
            if (($o['hostWallId'] ?? null) !== ($wall['id'] ?? null)) {
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

    /**
     * Azimut der rechten (bei CCW: äußeren) Normalen — Nord = +y = 0°, Ost = 90° (eingefrorene Konvention).
     *
     * @param  array{x:int,y:int}  $von
     * @param  array{x:int,y:int}  $bis
     */
    private function azimutRechteNormale(array $von, array $bis): int
    {
        $dx = $bis['x'] - $von['x'];
        $dy = $bis['y'] - $von['y'];
        $nx = $dy;   // rechte Normale
        $ny = -$dx;
        $grad = (int) round(rad2deg(atan2($nx, $ny)));

        return (($grad % 360) + 360) % 360;
    }

    /**
     * @param  array<int,array{x:int,y:int}>  $poly
     */
    private function flaecheDoppelt(array $poly): float
    {
        $n = count($poly);
        $s = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $s += $poly[$i]['x'] * $poly[$j]['y'] - $poly[$j]['x'] * $poly[$i]['y'];
        }

        return $s;
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

    /**
     * @param  array{x:int,y:int}  $a
     * @param  array{x:int,y:int}  $b
     */
    private function samePoint(array $a, array $b): bool
    {
        return (int) $a['x'] === (int) $b['x'] && (int) $a['y'] === (int) $b['y'];
    }
}
