# G1b-0 — Dry-Run-Mapping (Quelle → G1a-Kanonik)

**Welle:** G1b-0 · **Basis:** `b31f451` · **Stand:** 2026-07-16 · **read-only, kein Schreiben.**
**Zweck:** vollständige Feld-Abbildung der bestehenden Geometriequellen auf den kanonischen G1a-Vertrag (`resources/schemas/building-model/v1.schema.json`), mit Verlustfreiheit-/Konflikt-Klassifikation. **Keine Daten werden geschrieben; dies ist der Bauplan für den späteren Projektions-Adapter (P-B), kein Import.**

## 0. Klassifikation
`direkt_abbildbar` · `adapter_erforderlich` · `mehrdeutig` · `nicht_abbildbar` · `datenqualitaetsfehler` · `nutzerentscheidung`

## 1. Quellen
- **Q1 = `anforderungsprofile.gebaeude_geometrie`** (kanonische Wahrheit, Heizlast-`raeume[]`-Format) — führende Quelle des Mappings.
- **Q2 = `raum_geometrien`** (Legacy 2D: `polygon`, `wand_segmente[]` inkl. `oeffnungen`, `azimut_grad`, `hoehe_mm`, `geschoss`, `decke/boden`) — reichere Rohtopologie, aber Alt-/Transientpfad; nur als **Ergänzungsquelle** für Felder, die Q1 nicht führt (Wandsegmente/Öffnungen), streng objektgebunden.
- **Bindung:** `anforderungsprofile.verankerbar` → `LeadAlternativeAdd` = Objekt.

## 2. Mapping-Matrix

| # | Quelle | Quellfeld | G1a-Ziel | Ziel­feld | Transformation | Verlustfrei | Klasse | Konflikt/Entscheidung |
|---|--------|-----------|----------|-----------|----------------|-------------|--------|-----------------------|
| M1 | Q1-Anker | `verankerbar_id` (LeadAlternativeAdd) | Modell | `object_id` | direkt (int) | ja | direkt_abbildbar | 1 aktives Modell je Objekt |
| M2 | Q1 | `anforderungsprofile.id` | Modell | `model_id` | int→UUID-Ableitung (stabil je Objekt) | nein | adapter_erforderlich | UUID deterministisch vergeben, Herkunft mitführen |
| M3 | Q1 | `version` | Modell | `revision_id` / `parent_revision_id` | Versionskette → Revisions-UUIDs | nein | adapter_erforderlich | append-only Kette 1:1 übernehmen |
| M4 | Q1 | `status` (entwurf/aktiv/abgeloest) | Modell | `status` (draft/validated/confirmed/superseded) | Mapping entwurf→draft, aktiv→confirmed, abgeloest→superseded | teilw. | adapter_erforderlich | `validated` existiert im Alt-Status nicht → nur aus Gate-Durchlauf ableitbar |
| M5 | — | (kein Gebäudecontainer) | Gebäude | `buildings[].id` | 1 Default-Gebäude je Objekt | nein | adapter_erforderlich | Mehr-Gebäude im Alt-Format nicht vorgesehen → 1 Gebäude |
| M6 | Q1 | `raeume[].? / geschoss` (Q2 `geschoss`-Skalar) | Geschoss | `storeys[].order/id` | Skalar→Storey-Entität | teilw. | mehrdeutig | Q1 führt oft nur 1 Geschoss; Mehrgeschoss aus Q2 `geschoss`-Index |
| M7 | Q2 | `hoehe_mm` (Raumhöhe) | Geschoss/Wand | `finished_floor_level_mm` / `height_mm` | Raumhöhe → Wandhöhe; OKFF aus Geschossindex | nein | nutzerentscheidung | OKFF/Geschosshöhe fehlt → **kein stiller Default**, markieren |
| M8 | Q2 | `polygon` (mm) | Knoten/Raum | `nodes[]` + `rooms[].boundary_edges[]` | Polygon-Ecken → nodes; Kanten → Wände→boundary_edges | teilw. | adapter_erforderlich | Umlaufsinn auf CW normieren (protokolliert), Topologie-Gate |
| M9 | Q2 | `wand_segmente[]` | Wand | `walls[].reference_path` | Segment (Start/End) → reference_path (2 Knoten) | teilw. | adapter_erforderlich | Polylinienwände aus zusammenhängenden Segmenten bündeln |
| M10 | Q2 | `wand_segmente.dicke?` | Wand | `thickness_mm` | mm→mm | teilw. | mehrdeutig | fehlt oft → `nutzerentscheidung` (kein Default) |
| M11 | — | (keine Bezugslinie im Alt-Format) | Wand | `reference_line_type` | Bezugslinie ableiten, nicht still setzen (CAD-E1/**A3**) | nein | **nutzerentscheidung** | Alt-`raum_geometrien`-Polygon ist **meist Innenkante** → `reference_line_type` **nicht still `axis`**: wenn Innenkante erkennbar → `inner` mit `data_quality`-Kennzeichnung, sonst als Nutzerentscheidung markieren (axis/inner verschiebt die Wand um `thickness/2` → Raumfläche/Hülle) |
| M12 | — | (kein Anschlag-Anker) | Wand | `thickness_change_anchor` | Default `reference_line` (CAD-E1) | nein | direkt_abbildbar | fester Default |
| M13 | Q2 | Wandtyp (außen/innen aus Hülle) | Wand | `wall_type` / `exterior_side` | Hüllen-Zugehörigkeit → external/internal; Umlaufsinn → exterior_side | teilw. | adapter_erforderlich | uneindeutig → `wall_type=unknown`, `exterior_side=unknown` (kein Raten) |
| M14 | Q2 | `wand_segmente.oeffnungen[]` | Öffnung | `openings[]` | Öffnung an Wand, XY→**station_mm** entlang Referenzlinie | teilw. | adapter_erforderlich | Station ab Segmentstart bis nähere Rohbaukante; überlappend→Konflikt |
| M15 | Q2 | Öffnung Breite/Höhe | Öffnung | `rough_width_mm/rough_height_mm` | mm→mm (Rohbau führt) | teilw. | mehrdeutig | Rohbau vs. Fertig im Alt-Format nicht getrennt → als Rohbau, Fertig `null` |
| M16 | Q2 | Öffnung Brüstung | Öffnung | `sill_height_mm` | mm ab OKFF | teilw. | nutzerentscheidung | fehlt → markieren (kein Default) |
| M17 | Q2 | `azimut_grad` | (abgeleitet) | — | Nord=0-Konvention; PVGIS Süd=0 nur per Adapter | teilw. | adapter_erforderlich | **keine** stille 180°/Vorzeichen-Korrektur; Wand-Azimut wird in G1a aus Umlaufsinn abgeleitet, nicht gespeichert |
| M18 | — | Gebäude-Nordwinkel | Modell | `north_angle_deg`/`north_source` | aus Georef/Import/Nutzer | nein | nutzerentscheidung | fehlt → `north_source=unknown`, `north_angle_deg=null` (kein Raten) |
| M19 | intern mm | Einheit | alle Längen | `*_mm` (Integer) | Meter→mm falls nötig | ja (mm-Quelle) | datenqualitaetsfehler bei Float | Meter/Float-Eingabe wird abgelehnt (Gate/Validator) |
| M20 | Q2 | `decke`/`boden` | Decke/Boden | `slabs[]` (ceiling/floor_slab) | Hüllbauteil → slab + boundary_node_ids | teilw. | adapter_erforderlich | fehlend → Slab optional (kein Zwang) |
| M21 | — | Herkunft | Modell | `source`/`data_quality` | setzen (migrated_existing_geometry) | ja | direkt_abbildbar | Scan/Playground nie automatisch führend |
| M22 | Q2 opak | playground `geometry_json` | — | — | erst nach Schema-Rekonstruktion | nein | nicht_abbildbar | ohne Objektbezug **nicht** migrieren (K3) |

## 3. Verlustfreiheit — Zusammenfassung
- **Direkt/verlustfrei:** object_id, Einheit (mm-Quelle), Herkunft.
- **Adapter (deterministisch, dokumentiert):** IDs→UUID, Versionskette→Revisionen, Status-Mapping, Polygon→nodes/boundary_edges, Segmente→reference_path, Umlaufsinn-Normierung, Station-Ableitung, Default reference_line_type=axis.
- **Nutzerentscheidung (kein Default):** OKFF/Geschosshöhe, Wanddicke (fehlend), Brüstung (fehlend), Gebäude-Nordwinkel, Wandtyp/Innen-Außen (uneindeutig).
- **Datenqualitätsfehler:** Float-mm/Meter, selbstschneidende Polygone, Σ Öffnung > Σ Wand → Topologie-Gate blockt, Konfliktliste, **kein** Import.
- **Nicht abbildbar:** objektlose playground-Geometrie.

## 4. Adapter-Bauplan (für spätere P-B-Projektion, nicht jetzt bauen)
`ProfilGeometrieZuKanonik`-Transform: liest Q1 (+ Q2 objektgebunden) → erzeugt `CanonicalBuildingModel`-JSON (v1-Schema) → validiert mit `CanonicalBuildingModelValidator` → schreibt **derived** Snapshot. Fehlende Pflicht-Operanden erzeugen **Konfliktliste** (Klasse `nutzerentscheidung`), niemals erfundene Werte. Der Adapter ist reine Projektion; er ändert `gebaeude_geometrie` nie.

*Lokal wird 0 Bestandsgeometrie erwartet (Hetzner erst am Deploy-Tag). Das Mapping ist damit primär Vertrag/Bauplan, kein Massendaten-Import.*
