<?php

namespace App\Services\BuildingModel;

use App\Services\Geometrie\TopologieGate;
use App\Support\GeometrieToleranz;

/**
 * CanonicalBuildingModelValidator (G1a / Alias 3D-1A) — reine, nebenwirkungsfreie semantische Validierung
 * eines kanonischen Gebäudemodells gegen den Vertrag v1 (resources/schemas/building-model/v1.schema.json)
 * nach Masterprompt V3.3 und den ratifizierten CAD-Auflagen A1–A6 / E1–E3.
 *
 * GRENZEN (bewusst, Startblock §13): kein DB-/Request-/Session-Zugriff, kein Speichern, keine Provider-/
 * Datei-Aufrufe. KEINE stillen Korrekturen — kein Knoten-Merge, kein Polygon-Schließen, keine Defaults,
 * keine ID-Erzeugung, keine automatische Richtungsumkehr, keine Reparatur ungültiger Geometrie. Der
 * Validator liest ein Array (dekodiertes JSON) und sammelt Fehler mit stabilem Code, Pfad und Severity.
 *
 * TOLERANZVERTRAG (CAD-A6, Startblock §11): Es gibt KEIN zweites Toleranzregime. Alle geometrischen
 * Toleranz-/Topologie-Entscheidungen (Mindestsegment, Punktgleichheit, Selbstschnitt, Selbstberührung,
 * Degeneriertheit, zulässige numerische Abweichung) laufen über den zentralen Vertrag
 * (config/geometrie.php via App\Support\GeometrieToleranz) bzw. werden für Raum-Boundary-Polygone an den
 * bestehenden App\Services\Geometrie\TopologieGate delegiert. Kein hartkodiertes Epsilon.
 *
 * Millimeter sind INTEGER (kein Float im Vertrag). Winkel Dezimalgrad, 0 ≤ azimut < 360 (0=Nord).
 * Höhendatum: OKFF Erdgeschoss = 0 mm; alle Modellhöhen beziehen sich darauf.
 * Fachliche Schreibwahrheit bleibt gebaeude_geometrie (3D-0B Variante C); dieser Vertrag ist nachgelagert.
 */
final class CanonicalBuildingModelValidator
{
    private const SUPPORTED_MAJOR = 1;

    public function __construct(private readonly TopologieGate $topologieGate = new TopologieGate) {}

    /** @param array<string,mixed> $model */
    public function validate(array $model): CanonicalModelValidationResult
    {
        $r = new CanonicalModelValidationResult;

        // 1. Schema-Version: unbekannte Major-Version → Abbruch (keine stille Interpretation).
        $ver = $model['schema_version'] ?? null;
        if (! is_string($ver) || ! preg_match('/^(\d+)\.\d+\.\d+$/', $ver, $m)) {
            $r->add('schema.invalid_version', 'schema_version', 'schema_version fehlt oder ist kein SemVer.');

            return $r;
        }
        if ((int) $m[1] !== self::SUPPORTED_MAJOR) {
            $r->add('schema.unsupported_major_version', 'schema_version', "Nicht unterstützte Major-Version: {$ver} (unterstützt: ".self::SUPPORTED_MAJOR.').');

            return $r;
        }

        // 2. Nordbezug — unbekannt darf nicht still zu 0° werden (B2/B12).
        $northSrc = $model['north_source'] ?? null;
        $northDeg = $model['north_angle_deg'] ?? null;
        if ($northSrc === 'unknown' && $northDeg !== null) {
            $r->add('north.unknown_with_value', 'north_angle_deg', 'north_source=unknown darf nicht still als Winkel geführt werden — north_angle_deg muss null sein.');
        }
        if ($northDeg !== null && (! is_numeric($northDeg) || $northDeg < 0 || $northDeg >= 360)) {
            $r->add('north.invalid_range', 'north_angle_deg', 'north_angle_deg muss 0 ≤ x < 360 sein.');
        }

        // 3. Herkunft/Status — Scan-/Providerquelle nicht automatisch bestätigt (B17/B19, CAD-E-Herkunft).
        if (($model['status'] ?? null) === 'confirmed' && in_array($model['source'] ?? null, ['scan_proposal', 'external_provider'], true)) {
            $r->add('source.unverified_confirmed', 'status', "Status 'confirmed' ist aus Quelle '".($model['source'])."' nicht ohne Nutzerprüfung zulässig.");
        }

        // 4. Globale ID-Eindeutigkeit über alle fachlichen Elemente.
        $this->pruefeIdEindeutigkeit($model, $r);

        // 5. Globaler Wand→Geschoss-Index (für geschossübergreifende Boundary-Erkennung, CAD-A5).
        $wallStorey = $this->wandGeschossIndex($model);

        // 6. Gebäude / Geschosse / Geometrie.
        foreach (($model['buildings'] ?? []) as $bi => $building) {
            $bp = "buildings.$bi";
            $orders = [];
            $sorted = [];
            foreach (($building['storeys'] ?? []) as $si => $storey) {
                $sp = "$bp.storeys.$si";

                $ord = $storey['order'] ?? null;
                if (is_int($ord)) {
                    if (isset($orders[$ord])) {
                        $r->add('storey.duplicate_order', "$sp.order", "Doppelte Geschossreihenfolge $ord innerhalb desselben Gebäudes.");
                    }
                    $orders[$ord] = true;
                    $sorted[] = ['ord' => $ord, 'storey' => $storey, 'path' => $sp];
                }

                $this->pruefeGeschoss($storey, $sp, $r, "$bi.$si", $wallStorey);
            }

            // 7. Höhenvertrag (CAD-A2): geschossübergreifende Konsistenz der geführten Höhen.
            $this->pruefeHoehen($sorted, $r);
        }

        return $r;
    }

    /**
     * Wand-ID → Geschoss-Schlüssel ("bi.si"), damit eine Raum-Boundary eine Wand aus einem ANDEREN
     * Geschoss als geschossübergreifend (statt „fehlend") erkennen kann.
     *
     * @param  array<string,mixed>  $model
     * @return array<string,string>
     */
    private function wandGeschossIndex(array $model): array
    {
        $idx = [];
        foreach (($model['buildings'] ?? []) as $bi => $b) {
            foreach (($b['storeys'] ?? []) as $si => $s) {
                foreach (($s['walls'] ?? []) as $w) {
                    if (is_string($w['id'] ?? null)) {
                        $idx[$w['id']] = "$bi.$si";
                    }
                }
            }
        }

        return $idx;
    }

    /** @param array<string,mixed> $model */
    private function pruefeIdEindeutigkeit(array $model, CanonicalModelValidationResult $r): void
    {
        $seen = [];
        $note = function (?string $id, string $path) use (&$seen, $r): void {
            if (! is_string($id) || $id === '') {
                return;
            }
            if (isset($seen[$id])) {
                $r->add('id.duplicate', $path, "Doppelte ID '$id' (bereits als {$seen[$id]}).");
            } else {
                $seen[$id] = $path;
            }
        };
        $note($model['model_id'] ?? null, 'model_id');
        $note($model['revision_id'] ?? null, 'revision_id');
        foreach (($model['buildings'] ?? []) as $bi => $b) {
            $note($b['id'] ?? null, "buildings.$bi.id");
            foreach (($b['storeys'] ?? []) as $si => $s) {
                $bp = "buildings.$bi.storeys.$si";
                $note($s['id'] ?? null, "$bp.id");
                foreach (['nodes', 'walls', 'openings', 'rooms', 'slabs'] as $coll) {
                    foreach (($s[$coll] ?? []) as $ei => $el) {
                        $note($el['id'] ?? null, "$bp.$coll.$ei.id");
                    }
                }
            }
        }
    }

    /**
     * @param  array<string,mixed>  $storey
     * @param  array<string,string>  $wallStorey
     */
    private function pruefeGeschoss(array $storey, string $sp, CanonicalModelValidationResult $r, string $storeyKey, array $wallStorey): void
    {
        $nodes = $this->indexNodes($storey, $sp, $r);
        $walls = $this->pruefeWaende($storey, $sp, $r, $nodes);
        $this->pruefeOeffnungen($storey, $sp, $r, $walls);
        $this->pruefeRaeume($storey, $sp, $r, $walls, $storeyKey, $wallStorey);
        $this->pruefeSlabs($storey, $sp, $r, $nodes);
    }

    /**
     * @param  array<string,mixed>  $storey
     * @return array<string,array{x:int,y:int}>
     */
    private function indexNodes(array $storey, string $sp, CanonicalModelValidationResult $r): array
    {
        $nodes = [];
        foreach (($storey['nodes'] ?? []) as $ni => $node) {
            $np = "$sp.nodes.$ni";
            foreach (['x_mm', 'y_mm'] as $f) {
                if (array_key_exists($f, $node) && ! is_int($node[$f])) {
                    $r->add('unit.float_length', "$np.$f", "Koordinate $f muss Millimeter-Integer sein (kein Float).");
                }
            }
            if (is_string($node['id'] ?? null) && is_int($node['x_mm'] ?? null) && is_int($node['y_mm'] ?? null)) {
                $nodes[$node['id']] = ['x' => $node['x_mm'], 'y' => $node['y_mm']];
            }
        }

        return $nodes;
    }

    /**
     * @param  array<string,mixed>  $storey
     * @param  array<string,array{x:int,y:int}>  $nodes
     * @return array<string,array{segNodes:array<int,array{0:string,1:string}>,segLens:array<int,float>,height:?int,bottom:int,type:?string}>
     */
    private function pruefeWaende(array $storey, string $sp, CanonicalModelValidationResult $r, array $nodes): array
    {
        $minSeg = GeometrieToleranz::minSegmentMm();
        $walls = [];
        foreach (($storey['walls'] ?? []) as $wi => $wall) {
            $wp = "$sp.walls.$wi";

            // mm-Integer.
            foreach (['thickness_mm', 'bottom_offset_mm', 'height_mm'] as $f) {
                if (array_key_exists($f, $wall) && ! is_int($wall[$f])) {
                    $r->add('unit.float_length', "$wp.$f", "Längenfeld $f muss Millimeter-Integer sein (kein Float).");
                }
            }

            // CAD-A1 — Wandreferenz Pflicht.
            $rlt = $wall['reference_line_type'] ?? null;
            if (! in_array($rlt, ['axis', 'inner', 'outer'], true)) {
                $r->add('wall.missing_reference_line_type', "$wp.reference_line_type", 'reference_line_type fehlt oder ist ungültig (axis|inner|outer) — Wandreferenz ist Pflicht.');
            }
            // CAD-E1 — Fixkante bei Dickenänderung.
            if (array_key_exists('thickness_change_anchor', $wall)
                && ! in_array($wall['thickness_change_anchor'], ['reference_line', 'inner_face', 'outer_face'], true)) {
                $r->add('wall.invalid_thickness_anchor', "$wp.thickness_change_anchor", 'thickness_change_anchor ist ungültig (reference_line|inner_face|outer_face).');
            }

            // Referenzpfad: ≥2 Knoten, alle vorhanden, keine identischen Nachbarn, Mindestsegmentlänge.
            $pathIds = $wall['reference_path']['node_ids'] ?? null;
            $segNodes = [];
            $segLens = [];
            if (! is_array($pathIds) || count($pathIds) < 2) {
                $r->add('wall.missing_node', "$wp.reference_path", 'reference_path braucht mindestens zwei Knoten.');
            } else {
                $missing = false;
                foreach ($pathIds as $nid) {
                    if (! is_string($nid) || ! isset($nodes[$nid])) {
                        $missing = true;
                    }
                }
                if ($missing) {
                    $r->add('wall.missing_node', $wp, 'Wand referenziert einen im Geschoss nicht vorhandenen Knoten (kein stiller Ersatzknoten).');
                } else {
                    $count = count($pathIds);
                    for ($i = 0; $i < $count - 1; $i++) {
                        $a = $pathIds[$i];
                        $b = $pathIds[$i + 1];
                        if ($a === $b) {
                            $r->add('wall.degenerate', "$wp.reference_path", 'Aufeinanderfolgende Referenzknoten sind identisch (entartetes Segment).');

                            continue;
                        }
                        $len = $this->distanz($nodes[$a], $nodes[$b]);
                        if ($len < $minSeg) {
                            $r->add('wall.segment_too_short', "$wp.reference_path", "Wandsegment kürzer als die zentrale Mindestsegmentlänge ({$minSeg} mm).");
                        }
                        $segNodes[] = [$a, $b];
                        $segLens[] = $len;
                    }
                }
            }

            // Positive Maße + Höhenmodus.
            if (isset($wall['thickness_mm']) && is_int($wall['thickness_mm']) && $wall['thickness_mm'] <= 0) {
                $r->add('wall.nonpositive_thickness', "$wp.thickness_mm", 'Wanddicke muss > 0 sein.');
            }
            $mode = $wall['height_mode'] ?? null;
            if ($mode === 'uniform') {
                if (! isset($wall['height_mm']) || ! is_int($wall['height_mm']) || $wall['height_mm'] <= 0) {
                    $r->add('wall.nonpositive_height', "$wp.height_mm", 'height_mode=uniform verlangt height_mm > 0.');
                }
            }

            // exterior_side ↔ wall_type (CAD-A4-Vorbereitung: Innen/Außen darf nicht erraten werden).
            $type = $wall['wall_type'] ?? null;
            $ext = $wall['exterior_side'] ?? null;
            if ($type === 'internal' && $ext !== 'not_applicable') {
                $r->add('wall.exterior_side_conflict', "$wp.exterior_side", 'Innenwand muss exterior_side=not_applicable führen.');
            }
            if ($type === 'external' && ! in_array($ext, ['left', 'right', 'unknown'], true)) {
                $r->add('wall.exterior_side_conflict', "$wp.exterior_side", 'Außenwand braucht exterior_side ∈ {left,right,unknown}.');
            }

            if (is_string($wall['id'] ?? null)) {
                $walls[$wall['id']] = [
                    'segNodes' => $segNodes,
                    'segLens' => $segLens,
                    'height' => (isset($wall['height_mm']) && is_int($wall['height_mm'])) ? $wall['height_mm'] : null,
                    'bottom' => (isset($wall['bottom_offset_mm']) && is_int($wall['bottom_offset_mm'])) ? $wall['bottom_offset_mm'] : 0,
                    'type' => is_string($type) ? $type : null,
                    'mode' => is_string($mode) ? $mode : null,
                ];
            }
        }

        return $walls;
    }

    /**
     * Öffnungsvertrag (CAD-A3): wand-/segmentgebunden, Station ab Segmentstart, innerhalb Segmentlänge
     * und Wandhöhe, Fertigmaß überschreitet Rohbaumaß nicht, keine Überlappung je (Wand, Segment).
     *
     * @param  array<string,mixed>  $storey
     * @param  array<string,array<string,mixed>>  $walls
     */
    private function pruefeOeffnungen(array $storey, string $sp, CanonicalModelValidationResult $r, array $walls): void
    {
        $relTol = GeometrieToleranz::recalcAbweichungRel();
        $byHost = [];
        foreach (($storey['openings'] ?? []) as $oi => $op) {
            $opp = "$sp.openings.$oi";
            foreach (['station_mm', 'rough_width_mm', 'rough_height_mm', 'sill_height_mm', 'lintel_height_mm', 'finished_width_mm', 'finished_height_mm'] as $f) {
                if (array_key_exists($f, $op) && $op[$f] !== null && ! is_int($op[$f])) {
                    $r->add('unit.float_length', "$opp.$f", "Längenfeld $f muss Millimeter-Integer sein (kein Float).");
                }
            }
            $wid = $op['wall_id'] ?? null;
            if (! is_string($wid) || ! isset($walls[$wid])) {
                $r->add('opening.missing_wall', $opp, 'Öffnung ist keiner im Geschoss vorhandenen Wand zugeordnet (keine frei liegende Öffnung).');

                continue;
            }
            $wall = $walls[$wid];
            $hsi = $op['host_segment_index'] ?? null;
            if (! is_int($hsi) || $hsi < 0 || $hsi >= count($wall['segLens'])) {
                $r->add('opening.invalid_host_segment', "$opp.host_segment_index", 'host_segment_index verweist auf kein vorhandenes Wandsegment.');

                continue;
            }
            $station = $op['station_mm'] ?? null;
            $rw = $op['rough_width_mm'] ?? null;
            $rh = $op['rough_height_mm'] ?? null;
            $sill = $op['sill_height_mm'] ?? null;

            if (is_int($station) && $station < 0) {
                $r->add('opening.negative_station', "$opp.station_mm", 'station_mm darf nicht negativ sein.');
            }
            if (is_int($rw) && $rw <= 0) {
                $r->add('opening.nonpositive_dimension', "$opp.rough_width_mm", 'rough_width_mm muss > 0 sein.');
            }
            if (is_int($rh) && $rh <= 0) {
                $r->add('opening.nonpositive_dimension', "$opp.rough_height_mm", 'rough_height_mm muss > 0 sein.');
            }

            // Horizontal: Station + Rohbaubreite innerhalb der Segmentlänge (zentrale numerische Toleranz).
            $segLen = $wall['segLens'][$hsi];
            if (is_int($station) && is_int($rw) && $rw > 0) {
                $eps = $relTol * max(1.0, $segLen);
                if (($station + $rw) > $segLen + $eps) {
                    $r->add('opening.exceeds_wall_length', $opp, 'Öffnung überschreitet die Länge des Hostsegments (keine stille Verschiebung).');
                }
            }

            // Vertikal (A-3): Öffnung innerhalb des Wandkörpers (bottom_offset .. bottom_offset+height).
            // Bei height_mode=profile ist die lokale Wandhöhe an der Station nicht auflösbar → NICHT still als
            // uniforme Höhe prüfen; stattdessen ausdrücklich als „nicht entscheidbar" markieren (Warncode),
            // keine Öffnung durch einen erfundenen Default akzeptieren.
            $wh = $wall['height'];
            $bottom = $wall['bottom'];
            $mode = $wall['mode'] ?? null;
            if (is_int($sill) && is_int($rh)) {
                if ($mode === 'profile') {
                    // Untere Grenze ist auch ohne Höhenprofil entscheidbar → hart prüfen.
                    if ($sill < $bottom) {
                        $r->add('opening.exceeds_wall_height', $opp, 'Öffnungsunterkante liegt unter der Wandunterkante.');
                    }
                    // Nur die OBERE Grenze braucht die lokale Wandhöhe an der Station → nicht entscheidbar.
                    $r->add('opening.profile_height_unresolved', $opp, 'Obere vertikale Grenze bei height_mode=profile nicht entscheidbar (lokale Wandhöhe an der Station fehlt) — keine Annahme einer uniformen Höhe.', CanonicalModelValidationResult::SEVERITY_WARNING);
                } elseif ($wh !== null && ($sill < $bottom || ($sill + $rh) > ($bottom + $wh))) {
                    $r->add('opening.exceeds_wall_height', $opp, 'Öffnung liegt vertikal außerhalb der Wand.');
                }
            }

            // Sturzkonsistenz (A-3): lintel_height_mm == sill_height_mm + rough_height_mm (rough_top_mm).
            // Abweichung = Messkonflikt, keine stille Priorisierung, keine automatische Korrektur.
            $lintel = $op['lintel_height_mm'] ?? null;
            if (is_int($lintel) && is_int($sill) && is_int($rh) && $lintel !== ($sill + $rh)) {
                $r->add('opening.lintel_conflict', "$opp.lintel_height_mm", 'lintel_height_mm weicht von sill_height_mm + rough_height_mm (rough_top_mm) ab — Messkonflikt, keine stille Priorisierung.');
            }

            // Fertigmaß darf Rohbauausschnitt nicht überstimmen.
            $fw = $op['finished_width_mm'] ?? null;
            $fh = $op['finished_height_mm'] ?? null;
            if (is_int($fw) && is_int($rw) && $fw > $rw) {
                $r->add('opening.finished_exceeds_rough', "$opp.finished_width_mm", 'Fertigbreite darf den Rohbauausschnitt nicht überschreiten.');
            }
            if (is_int($fh) && is_int($rh) && $fh > $rh) {
                $r->add('opening.finished_exceeds_rough', "$opp.finished_height_mm", 'Fertighöhe darf den Rohbauausschnitt nicht überschreiten.');
            }

            if (is_int($station) && is_int($rw) && $rw > 0) {
                $byHost["$wid#$hsi"][] = ['from' => $station, 'to' => $station + $rw, 'path' => $opp];
            }
        }
        foreach ($byHost as $ivs) {
            usort($ivs, fn ($a, $b) => $a['from'] <=> $b['from']);
            for ($i = 1; $i < count($ivs); $i++) {
                if ($ivs[$i]['from'] < $ivs[$i - 1]['to']) {
                    $r->add('opening.overlap', $ivs[$i]['path'], 'Öffnung überlappt eine andere Öffnung desselben Wandsegments.');
                }
            }
        }
    }

    /**
     * Raum-Boundary-Vertrag (CAD-A5) + Umlaufsinn (CAD-A4) + Topologie-Parität (CAD-A6).
     * Die Boundary referenziert Wände/Segmente (keine freie Knotenliste). Aus den gerichteten
     * Segmenten wird der geschlossene Knotenring gebildet und für die Topologie an den zentralen
     * TopologieGate delegiert (dieselbe Wahrheit wie bestehende Geometriepfade).
     *
     * @param  array<string,mixed>  $storey
     * @param  array<string,array<string,mixed>>  $walls
     * @param  array<string,string>  $wallStorey
     */
    private function pruefeRaeume(array $storey, string $sp, CanonicalModelValidationResult $r, array $walls, string $storeyKey, array $wallStorey): void
    {
        foreach (($storey['rooms'] ?? []) as $ri => $room) {
            $rp = "$sp.rooms.$ri";
            $edges = $room['boundary_edges'] ?? [];

            // Cache-Kennzeichnung.
            if (isset($room['polygon_cache_mm']) && is_array($room['polygon_cache_mm'])
                && ($room['polygon_cache_mm']['is_cache'] ?? null) !== true) {
                $r->add('room.polygon_not_marked_cache', "$rp.polygon_cache_mm", 'Ein gespeichertes Raum-Polygon ist nur abgeleiteter Cache und muss is_cache=true tragen (keine zweite Geometriewahrheit).');
            }

            // Manuelle Räume brauchen eine Begründung; abgeleitet und manuell nie gleichzeitig führend (B9.3).
            if (($room['geometry_source'] ?? null) === 'manual') {
                $reason = $room['manual_reason'] ?? null;
                if (! is_string($reason) || trim($reason) === '') {
                    $r->add('room.manual_without_reason', $rp, 'geometry_source=manual verlangt eine manual_reason.');
                }
            }

            if (! is_array($edges) || count($edges) < 3) {
                $r->add('room.boundary_not_closed', $rp, 'Raum-Boundary braucht mindestens drei Kanten.');

                continue;
            }

            // Kanten auflösen: Wand vorhanden (im Geschoss), Segment gültig, gerichteten Ring bilden.
            $ring = [];      // Knotenkoordinaten in Laufrichtung
            $fromIds = [];
            $toIds = [];
            $broken = false;
            foreach ($edges as $ei => $edge) {
                $wid = $edge['wall_id'] ?? null;
                if (! is_string($wid) || ! isset($walls[$wid])) {
                    if (is_string($wid) && isset($wallStorey[$wid]) && $wallStorey[$wid] !== $storeyKey) {
                        $r->add('room.boundary_cross_storey', "$rp.boundary_edges.$ei", 'Raum-Boundary referenziert eine Wand aus einem anderen Geschoss.');
                    } else {
                        $r->add('room.boundary_missing_wall', "$rp.boundary_edges.$ei", 'Raum-Boundary referenziert eine im Geschoss nicht vorhandene Wand.');
                    }
                    $broken = true;

                    continue;
                }
                $wall = $walls[$wid];
                $hsi = $edge['host_segment_index'] ?? null;
                if (! is_int($hsi) || $hsi < 0 || $hsi >= count($wall['segNodes'])) {
                    $r->add('room.boundary_invalid_host_segment', "$rp.boundary_edges.$ei", 'Boundary-Kante verweist auf kein vorhandenes Wandsegment.');
                    $broken = true;

                    continue;
                }
                [$a, $b] = $wall['segNodes'][$hsi];
                $fromId = ($edge['traversal'] ?? null) === 'backward' ? $b : $a;
                $toId = ($edge['traversal'] ?? null) === 'backward' ? $a : $b;
                $fromIds[] = $fromId;
                $toIds[] = $toId;
            }

            if ($broken) {
                continue;
            }

            // Konnektivität + Geschlossenheit: to[k] == from[k+1], to[last] == from[0].
            $n = count($fromIds);
            $closed = true;
            for ($k = 0; $k < $n; $k++) {
                if ($toIds[$k] !== $fromIds[($k + 1) % $n]) {
                    $closed = false;
                    break;
                }
            }
            if (! $closed) {
                $r->add('room.boundary_not_closed', $rp, 'Raum-Boundary bildet keinen zusammenhängenden geschlossenen Ring (keine stille Reparatur).');

                continue;
            }

            // Ring-Koordinaten (Startknoten je Kante).
            $nodesById = $this->geschossKnoten($storey);
            foreach ($fromIds as $id) {
                $ring[] = $nodesById[$id];
            }

            // Topologie-Parität: gleiche Regeln/Toleranzen wie bestehende Geometriepfade.
            $topo = $this->topologieGate->pruefePolygon($ring);
            if (! $topo->valid) {
                if (in_array('selbstschnitt', $topo->ruleKeys(), true)) {
                    $r->add('room.boundary_self_intersect', $rp, 'Raum-Boundary schneidet sich selbst.');
                } else {
                    $r->add('room.boundary_invalid_topology', $rp, 'Raum-Boundary verletzt den zentralen Topologievertrag: '.implode(',', $topo->ruleKeys()).'.');
                }

                continue;
            }

            // Umlaufsinn (CAD-A4): Boundary im Uhrzeigersinn, Raum liegt rechts → signierte Fläche < 0.
            if ($this->signierteFlaeche2($ring) >= 0) {
                $r->add('room.wrong_winding', $rp, 'Raum-Boundary läuft nicht im Uhrzeigersinn (Raum muss rechts der gerichteten Boundary liegen).');
            }
        }
    }

    /**
     * @param  array<string,mixed>  $storey
     * @return array<string,array{x:int,y:int}>
     */
    private function geschossKnoten(array $storey): array
    {
        $nodes = [];
        foreach (($storey['nodes'] ?? []) as $node) {
            if (is_string($node['id'] ?? null) && is_int($node['x_mm'] ?? null) && is_int($node['y_mm'] ?? null)) {
                $nodes[$node['id']] = ['x' => $node['x_mm'], 'y' => $node['y_mm']];
            }
        }

        return $nodes;
    }

    /**
     * @param  array<string,mixed>  $storey
     * @param  array<string,array{x:int,y:int}>  $nodes
     */
    private function pruefeSlabs(array $storey, string $sp, CanonicalModelValidationResult $r, array $nodes): void
    {
        foreach (($storey['slabs'] ?? []) as $li => $slab) {
            $lp = "$sp.slabs.$li";
            foreach (($slab['boundary_node_ids'] ?? []) as $nid) {
                if (! is_string($nid) || ! isset($nodes[$nid])) {
                    $r->add('slab.missing_node_ref', $lp, 'Decken-/Bodenplatten-Boundary referenziert einen nicht vorhandenen Knoten.');
                    break;
                }
            }
        }
    }

    /**
     * Höhenvertrag (CAD-A2): geführte Höhen dürfen sich nicht still widersprechen. Nachfußboden (raw)
     * darf nicht über dem Fertigfußboden (finished) liegen; eine geführte Geschosshöhe muss zur OKFF-
     * Differenz zum darüberliegenden Geschoss passen. Kein stilles Überschreiben, kein Standardwert.
     *
     * @param  array<int,array{ord:int,storey:array<string,mixed>,path:string}>  $sorted
     */
    private function pruefeHoehen(array $sorted, CanonicalModelValidationResult $r): void
    {
        foreach ($sorted as $entry) {
            $s = $entry['storey'];
            $fin = $s['finished_floor_level_mm'] ?? null;
            $raw = $s['raw_floor_level_mm'] ?? null;
            if (is_int($fin) && is_int($raw) && $raw > $fin) {
                $r->add('height.conflict', "{$entry['path']}.raw_floor_level_mm", 'Roh-Fußbodenhöhe liegt über der Fertig-Fußbodenhöhe (widersprüchliche Höhen, keine stille Korrektur).');
            }
        }

        usort($sorted, fn ($a, $b) => $a['ord'] <=> $b['ord']);
        for ($i = 0; $i < count($sorted) - 1; $i++) {
            $lo = $sorted[$i]['storey'];
            $hi = $sorted[$i + 1]['storey'];
            $finLo = $lo['finished_floor_level_mm'] ?? null;
            $finHi = $hi['finished_floor_level_mm'] ?? null;
            $sh = $lo['storey_height_mm'] ?? null;
            if (is_int($finLo) && is_int($finHi) && is_int($sh) && ($finHi - $finLo) !== $sh) {
                $r->add('height.conflict', "{$sorted[$i]['path']}.storey_height_mm", 'Geführte Geschosshöhe widerspricht der OKFF-Differenz zum darüberliegenden Geschoss.');
            }
        }
    }

    /** @param array{x:int,y:int} $a @param array{x:int,y:int} $b */
    private function distanz(array $a, array $b): float
    {
        return sqrt((($a['x'] - $b['x']) ** 2) + (($a['y'] - $b['y']) ** 2));
    }

    /**
     * Zweifache signierte Polygonfläche (Shoelace) über Integer-Koordinaten. < 0 = im Uhrzeigersinn
     * (bei x=Ost, y=Nord), > 0 = gegen den Uhrzeigersinn.
     *
     * @param  array<int,array{x:int,y:int}>  $ring
     */
    private function signierteFlaeche2(array $ring): int
    {
        $n = count($ring);
        $sum = 0;
        for ($i = 0; $i < $n; $i++) {
            $j = ($i + 1) % $n;
            $sum += $ring[$i]['x'] * $ring[$j]['y'] - $ring[$j]['x'] * $ring[$i]['y'];
        }

        return $sum;
    }
}
