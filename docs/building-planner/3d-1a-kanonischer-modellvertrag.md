# G1a / Alias 3D-1A — Kanonischer Modellvertrag v1

**Welle:** G1a (kanonischer Schema- und Modellvertrag) · **Heimat-App:** `ticket` · **Stand:** 2026-07-15
**Artefakte:** `resources/schemas/building-model/v1.schema.json` · `app/Services/BuildingModel/CanonicalBuildingModelValidator.php` · `app/Services/BuildingModel/CanonicalModelValidationResult.php`
**Tests:** `tests/Unit/BuildingModel/*` (40 Tests) · **Fixtures:** `tests/Fixtures/building-model/{valid,invalid}/*`
**Grundlage:** Masterprompt V3.3 (B2/B9/B10/B12/B19, Teil C), CAD-Fachprüfung + ADR `docs/building-planner/adr/ADR-0001-kanonischer-cad-modellvertrag.md`.

> **Einordnung (3D-0B Variante C).** Dieser Vertrag ist **nachgelagert**. Die fachliche Schreibwahrheit bleibt `anforderungsprofile.gebaeude_geometrie` (versioniert, objektgebunden). G1a liefert **nur** Schema + reinen semantischen Validator — **keine** Persistenz, Migration, UI, Three.js, LiDAR. Es entsteht **keine zweite Geometrie-Wahrheit**.

---

## 1. Einheiten, Koordinaten, Datum

- **Längen: Millimeter-Integer.** Kein Float in der Persistenz (Validator: `unit.float_length`).
- **Koordinaten:** 2D je Geschoss, X = Ost, Y = Nord. **Winkel** Dezimalgrad, 0 = Nord, im Uhrzeigersinn (`0 ≤ north_angle_deg < 360`).
- **Höhendatum:** `height_datum = "okff_eg_zero"` — OKFF Erdgeschoss = 0 mm; alle Modellhöhen beziehen sich darauf.
- **Unbekannter Nordbezug** wird nicht still zu 0°: `north_source = unknown` verlangt `north_angle_deg = null` (Validator: `north.unknown_with_value`).

## 2. Hierarchie

```
Modell (object_id, model_id, revision_id, status, source, north, height_datum)
└─ buildings[]  (id, name)
   └─ storeys[] (id, order, finished_floor_level_mm, …)
      ├─ nodes[]     (id, x_mm, y_mm)
      ├─ walls[]     (reference_path, reference_line_type, …)
      ├─ openings[]  (wall_id, host_segment_index, station_mm, …)
      ├─ rooms[]     (geometry_source, boundary_edges[], …)
      └─ slabs[]     (kind, elevation_mm, boundary_node_ids[])
```

## 3. Bauteile und Vertragsregeln

### 3.1 Wand (CAD-E1/A1)
- `reference_path.node_ids` — gerichtete Punktfolge, **≥ 2 Knoten** (Polylinie möglich; Segment `i` = `node_ids[i] → node_ids[i+1]`).
- `reference_line_type ∈ {axis, inner, outer}` — **Pflicht**; `axis` ist Aufnahme-Default. Innen-/Außenkante deterministisch abgeleitet, nicht zweitpersistiert.
- `thickness_change_anchor ∈ {reference_line, inner_face, outer_face}` (Default `reference_line`) — Fixkante bei Dickenänderung.
- `thickness_mm ≥ 1`, `bottom_offset_mm` (relativ OKFF Geschoss), `height_mode ∈ {uniform, profile}` + `height_mm ≥ 1`. `profile` ist strukturell offen für Kniestock/Abseite/DG-Schräge (Ausbau spätere Welle).
- `wall_type ∈ {external, internal, unknown}`; `exterior_side ∈ {left, right, unknown, not_applicable}` — Innenwand ⇒ `not_applicable`, Außenwand ⇒ `left|right|unknown` (Innen/Außen darf nicht erraten werden).
- Regeln: keine identischen Nachbarknoten (`wall.degenerate`), jedes Segment ≥ zentraler Mindestsegmentlänge (`wall.segment_too_short`), Knoten existieren (`wall.missing_node`).

### 3.2 Öffnung (CAD-A3)
- **Parametrisch an Wand/Segment**: `wall_id` + `host_segment_index`.
- `station_mm` = Abstand **entlang der Referenzlinie ab Start des gerichteten Hostsegments** (Achsmaß, nicht Wandflächenmaß) bis zur näheren Rohbaukante; die Öffnung belegt `[station_mm, station_mm + rough_width_mm]`. `rough_width_mm`/`rough_height_mm` führen den **Wandausschnitt**; `sill_height_mm`/`lintel_height_mm` beziehen sich auf OKFF (Höhendatum); `finished_*` optional, **≤ Rohbaumaß**.
- **Auflage A-3 (G1b, aus CAD-Re-Evaluation):** `lintel_height_mm` wird in v1 gespeichert, aber nicht gegen `sill_height_mm + rough_height_mm` kreuzgeprüft; das Datum von `sill_height_mm` wird wie `bottom_offset_mm` als OKFF-Bezug behandelt. Beides ist in G1b zu kreuzprüfen bzw. verbindlich zu dokumentieren.
- Absolute XY-Koordinaten sind nur abgeleitete Renderdaten (nicht im Vertrag).
- Regeln: Wand vorhanden (`opening.missing_wall`), Segment gültig (`opening.invalid_host_segment`), `station + rough_width ≤ Segmentlänge` (`opening.exceeds_wall_length`), vertikal im Wandkörper (`opening.exceeds_wall_height`), Fertig ≤ Rohbau (`opening.finished_exceeds_rough`), keine Überlappung je (Wand, Segment) (`opening.overlap`). Keine stille Verschiebung.

### 3.3 Raum (CAD-A5/A4)
- `geometry_source ∈ {derived_topology, manual}`; manuell verlangt `manual_reason` (`room.manual_without_reason`). Abgeleitet und manuell nie gleichzeitig führend.
- `boundary_edges[]` (≥ 3): je Kante `wall_id` + `host_segment_index` + `wall_side ∈ {left,right}` + `traversal ∈ {forward,backward}` — **keine freie Knotenliste** als Zweitwahrheit.
- **Auflage A-1 (G2c, aus CAD-Re-Evaluation):** `wall_side` wird in G1a strukturell geführt, aber **nicht** ausgewertet (der Umlaufsinn wird unabhängig auf dem Achsring erzwungen). In G2c ist `wall_side` entweder aus `traversal` + Umlaufsinn **abzuleiten** (nicht als Schreibwahrheit zu halten) **oder** gegen beide zu **validieren** — sonst latente zweite Wahrheit.
- Der Validator bildet aus den gerichteten Segmenten den geschlossenen Knotenring: Konnektivität + Geschlossenheit (`room.boundary_not_closed`), Wand im Geschoss (`room.boundary_missing_wall`) bzw. aus anderem Geschoss (`room.boundary_cross_storey`).
- **Topologie-Parität**: der Ring wird an den zentralen `TopologieGate` delegiert → `room.boundary_self_intersect` / `room.boundary_invalid_topology`.
- **Umlaufsinn (CAD-A4)**: Boundary im **Uhrzeigersinn**, Raum liegt **rechts** der gerichteten Boundary (signierte Fläche < 0) → sonst `room.wrong_winding`.
- `polygon_cache_mm` ist ausschließlich abgeleiteter Cache (`is_cache = true`, sonst `room.polygon_not_marked_cache`); trägt `derived_from_revision`/`topology_signature`.

### 3.4 Decke / Bodenplatte
- `kind ∈ {ceiling, floor_slab}`, `elevation_mm`, `boundary_node_ids` (Knoten existieren, sonst `slab.missing_node_ref`), `is_cache` optional.

### 3.5 Höhen (CAD-A2)
- Geführt je Geschoss: `finished_floor_level_mm` (OKFF), `raw_floor_level_mm?`, `default_clear_height_mm?`, `slab_thickness_mm?`, `storey_height_mm?`.
- Konflikte werden **markiert, nicht repariert**: `raw > finished` und geführte Geschosshöhe ≠ OKFF-Differenz zum darüberliegenden Geschoss → `height.conflict`. Kein stiller Standardwert für das oberste Geschoss.

## 4. Zentraler Toleranzvertrag (CAD-A6)

Es gibt **kein zweites Toleranzregime**. Alle geometrischen Toleranz-/Topologie-Entscheidungen kommen aus `config/geometrie.php` über `App\Support\GeometrieToleranz` bzw. werden für Raum-Boundary-Polygone an `App\Services\Geometrie\TopologieGate` delegiert. Kein hartkodiertes Epsilon; numerische Vergleichstoleranz = `recalc_abweichung_rel` (relativ). Der Paritätstest (`BuildingModelTopologieParityTest`) beweist gleiches Urteil in TopologieGate und Validator für denselben Grenzfall.

## 5. Stabile Fehlercodes (Auszug)

`schema.unsupported_major_version` · `north.unknown_with_value` · `north.invalid_range` · `source.unverified_confirmed` · `id.duplicate` · `storey.duplicate_order` · `height.conflict` · `unit.float_length` · `wall.missing_reference_line_type` · `wall.invalid_thickness_anchor` · `wall.missing_node` · `wall.degenerate` · `wall.segment_too_short` · `wall.nonpositive_thickness` · `wall.nonpositive_height` · `wall.exterior_side_conflict` · `opening.missing_wall` · `opening.invalid_host_segment` · `opening.exceeds_wall_length` · `opening.exceeds_wall_height` · `opening.finished_exceeds_rough` · `opening.overlap` · `opening.negative_station` · `opening.nonpositive_dimension` · `room.boundary_missing_wall` · `room.boundary_cross_storey` · `room.boundary_invalid_host_segment` · `room.boundary_not_closed` · `room.boundary_self_intersect` · `room.boundary_invalid_topology` · `room.wrong_winding` · `room.manual_without_reason` · `room.polygon_not_marked_cache` · `slab.missing_node_ref`.

## 6. Grenzen des Validators (bewusst)

Nebenwirkungsfrei: kein DB-/Request-/Session-/Provider-/Datei-Zugriff (nur Lesen des Toleranzvertrags via `config`), kein Speichern. **Keine** stillen Korrekturen: kein Knoten-Merge, kein Polygon-Schließen, keine Defaults, keine ID-Erzeugung, keine Richtungsumkehr, keine Geometrie-Reparatur. Mehrere Fehler werden gesammelt (Code, Pfad, Meldung, Severity).
