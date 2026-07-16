# G1a / Alias 3D-1A — CAD-Fachprüfung: Umsetzung der Auflagen im Vertrag

**Welle:** G1a · **Stand:** 2026-07-15 · **Heimat-App:** `ticket`
**Bezug:** Gate-Fachprüfung `docs/cad-fachpruefung-modellvertrag.md` (Urteil „freigabefähig_mit_auflagen") · ADR `docs/building-planner/adr/ADR-0001-kanonischer-cad-modellvertrag.md` · Startblock G1a.

> Dieses Dokument belegt nachvollziehbar, **wie** die ratifizierten CAD-Auflagen (A1–A6) und Entscheidungen (E1–E3) im gebauten Vertrag v1 umgesetzt und geprüft wurden. Es ist Prüfgrundlage für den unabhängigen CAD-Re-Evaluator (Startblock §19) und ersetzt weder den technischen Evaluator noch die Yama-Freigabe.

## 1. Auflagen-/Entscheidungs-Matrix

| ID | Anforderung | Umsetzung im Vertrag v1 | Beleg |
|---|---|---|---|
| **CAD-A1 / E1** | Wandreferenz Pflicht, `axis` Default, Fixkante, gerichtete Bezugslinie, keine Kanten-Zweitwahrheit | `reference_line_type` (required, enum axis/inner/outer), `thickness_change_anchor` (default reference_line), `reference_path.node_ids` (≥2, gerichtet), Innen/Außen abgeleitet | Schema `$defs.wall`; Validator `wall.missing_reference_line_type`, `wall.invalid_thickness_anchor`, `wall.missing_node/degenerate/segment_too_short`; Test `test_cad_a1_*`, invalid 01/02 |
| **CAD-A2** | OKFF/Rohdecke/lichte/Geschosshöhe getrennt geführt; Konflikt markiert statt repariert; ±0,00 Datum | Getrennte `*_floor_level_mm`/`*_height_mm`-Felder; `height_datum=okff_eg_zero`; `height.conflict` bei raw>finished und Geschosshöhe≠OKFF-Differenz | Schema `$defs.storey`, `height_datum`; Validator `pruefeHoehen`; invalid 13; valid 05 |
| **CAD-A3** | Öffnung stationbasiert ab Segmentstart, Rohbau≠Fertig, Bezug OKFF, keine XY | `host_segment_index` + `station_mm` + `rough_*`/`finished_*` + `sill/lintel`; keine XY-Felder | Schema `$defs.opening`; Validator `opening.invalid_host_segment/exceeds_wall_length/exceeds_wall_height/finished_exceeds_rough/overlap`; Test `test_cad_a3_*`, invalid 05/06/07/08; valid 04/07 |
| **CAD-A4** | Umlaufsinn kanonisch, Innen/Außen deterministisch, Richtung nicht still umkehren | Boundary CW (Raum rechts) → `room.wrong_winding`; `exterior_side` mit `wall.exterior_side_conflict`; Richtungssensitivität per Gegenbeweis M7 | Validator `signierteFlaeche2`, `pruefeRaeume`; invalid 11; Gegenbeweis M7 |
| **CAD-A5** | Raum-Boundary referenziert Wände/Kanten, kein freier Knoten, Cache nur abgeleitet | `boundary_edges[]` (wall_id+segment+side+traversal), `boundary_node_ids` entfernt; `polygon_cache_mm.is_cache=true` | Schema `$defs.room`; Validator `room.boundary_missing_wall/cross_storey/not_closed/polygon_not_marked_cache`; Test `test_cad_a5_*`, invalid 09/10 |
| **CAD-A6** | Ein Toleranzvertrag, kein hartkodiertes Epsilon, Parität mit TopologieGate | Delegation an `TopologieGate`/`GeometrieToleranz`; kein lokales `1e-6` für Toleranzentscheidungen | Validator `pruefeRaeume`/`pruefeWaende`; Paritätstest `BuildingModelTopologieParityTest`; invalid 15; Gegenbeweis M5 |
| **CAD-E2** | Wandanschlüsse abgeleitet, keine Junction-Geometrie in v1 | Keine `building_wall_junctions`/Anschlussgeometrie im Schema; nur Referenzlinien + Dicke | ADR §CAD-E2; Schema (keine Junction-Entität) |
| **CAD-E3** | Nur neutrale Geometrie, keine Wohnfläche/keine DIN-WoFlV-Mischung | Keine Flächen-/`living_area`-Felder im Vertrag; nur Geometrie-Operanden | ADR §CAD-E3; Schema (keine Flächenfelder) |

## 2. Gegenbeweise (Startblock §15.3)

Sieben Mutationen, jede macht die BuildingModel-Suite rot; Validator danach md5-identisch wiederhergestellt (`578d8e9e218c5919eda4849023fc7c86`):

1. `reference_line_type` optional → rot ✓
2. Öffnungsstation ignorieren (absolute XY) → rot ✓
3. falschen Umlaufsinn akzeptieren → rot ✓
4. fehlende Wandreferenz akzeptieren → rot ✓
5. hartkodiertes Epsilon statt TopologieGate → **Paritätstest** rot ✓
6. Scanvorschlag auto-confirm → rot ✓
7. Wandrichtung still umkehren → rot ✓

## 3. Bestandsaufmaß-Prüffragen (§19-Vorlage, Selbsteinschätzung)

1. Bezugslinie eindeutig? **Ja** — reference_line_type Pflicht, axis Default. 2. Dickenänderung ohne ungewollte Verschiebung? **Ja** — thickness_change_anchor Fixkante. 3. Öffnungsstation aus Aufmaßpraxis eindeutig? **Ja** — ab Segmentstart, nähere Rohbaukante. 4. Rohbau/Fertig getrennt? **Ja** — `rough_*`/`finished_*`, Fertig ≤ Rohbau. 5. OKFF/Rohdecke/lichte/Geschosshöhe getrennt? **Ja** — eigene Felder, `height_datum`. 6. Schiefe Bestandsräume möglich? **Ja** — freie mm-Knoten (valid 08). 7. Umlaufsinn eindeutig? **Ja** — CW, Raum rechts. 8. Raumflächen aus Wandtopologie ableitbar? **Ja** — boundary_edges → Ring → TopologieGate. 9. Offen für DG-Schrägen? **Ja** — `height_mode=profile` strukturell vorgesehen. 10. 2D/3D-Parität strukturell? **Ja** — ein Elementsatz, 3D abgeleitet. 11. IFC nicht blockiert? **Ja** — Achse (IfcWallStandardCase) + boundary_edges (IfcRelSpaceBoundary-fähig). 12. Kein unnötiges Voll-BIM? **Ja** — keine Junction-/Flächen-/Statik-Entitäten.

**Selbst-Urteil (nicht bindend, unabhängige Re-Evaluation folgt):** FACHLICH_GRÜN_MIT_AUFLAGEN — Restpunkte sind Folgewellen-Ausbau (Höhenprofil G1b, Raum-Wand-Seiten-Konsistenz G2c), keine v1-Grundmängel.
