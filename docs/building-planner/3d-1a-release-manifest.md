# RELEASE-MANIFEST — G1a / Alias 3D-1A (kanonischer CAD-Modellvertrag)

**Welle:** G1a (kanonischer Schema- und Modellvertrag) · **Heimat-App:** `ticket` · **Datum:** 2026-07-15
**Branch:** `private/app-code-backup` · **HEAD bei Bau:** `975f44d` (bereits gepusht, `@{upstream}...HEAD = 0 0`)
**Status:** UMGESETZT (Generator) — nicht „grün". Abnahme durch unabhängigen technischen Evaluator + CAD-Re-Evaluator + Yama ausstehend. **Kein Commit, kein Push.**

## 1. Scope

Umgesetzt wurde ausschließlich G1a: JSON-Schema, kanonischer Modellvertrag, reiner semantischer Validator, Contract-/Parität-Tests, anonymisierte Fixtures, ADR und Dokumente. **Nicht** enthalten (Pflicht-Stopp §21): Persistenz/Migration (G1b), Editor/FG-1 (G2), Three.js (G6), LiDAR (G4L), Importadapter, jede Änderung an `gebaeude_geometrie`, P1b-2.

## 2. Geänderte/erzeugte Dateien (pfadgenau, alle untracked)

**Produktivcode (Services):**
- `app/Services/BuildingModel/CanonicalBuildingModelValidator.php` (umgebaut auf V3.3 + CAD-A1..A6)
- `app/Services/BuildingModel/CanonicalModelValidationResult.php` (Wertträger, unverändert übernommen)

**Vertrag/Schema:**
- `resources/schemas/building-model/v1.schema.json` (umgebaut)

**Tests:**
- `tests/Unit/BuildingModel/CanonicalBuildingModelValidatorTest.php`
- `tests/Unit/BuildingModel/BuildingModelSchemaContractTest.php`
- `tests/Unit/BuildingModel/BuildingModelTopologieParityTest.php` (neu, CAD-A6-Parität)

**Fixtures:** `tests/Fixtures/building-model/valid/*` (10) · `tests/Fixtures/building-model/invalid/*` (17)

**Dokumente/ADR:**
- `docs/building-planner/3d-1a-kanonischer-modellvertrag.md`
- `docs/building-planner/3d-1a-cad-fachpruefung.md`
- `docs/building-planner/3d-1a-release-manifest.md` (dieses Dokument)
- `docs/building-planner/adr/ADR-0001-kanonischer-cad-modellvertrag.md`
- `docs/cad-fachpruefung-modellvertrag.md` (Gate-Fachprüfung, vorheriger Auftrag)

**Nicht angefasst (fremder Scope, bewusst unberührt):** ` M resources/views/admin/layouts/sidebar.blade.php`, `resources/views/admin/layouts/partials/` (Navi-Slice) sowie diverse `docs/ap2…/ap3…/ap4…/bereich2…/wp-stufe…` (andere Stränge).

## 3. Vertrags-Kennzahlen

- **Entitäten:** 7 (`building`, `storey`, `node`, `wall`, `opening`, `room`, `slab`) + `uuid`-Typ.
- **Stabile Fehlercodes:** 34 (siehe Modellvertrag §5).
- **CAD-Auflagen umgesetzt:** A1, A2, A3, A4, A5, A6 · **Entscheidungen ratifiziert:** E1, E2, E3 (ADR-0001).

## 4. Prüfbefehle (reproduzierbar)

```bash
php artisan test tests/Unit/BuildingModel/            # 40 Tests, 93 Assertions — grün
php artisan test tests/Unit/Geometrie tests/Unit/Heizlast   # 64 Tests — grün (keine Regression)
php artisan test                                       # 675 grün, 1 rot (E4-Baseline, s.u.)
```

## 5. Testergebnis

- **BuildingModel-Suite:** 40 passed (93 assertions).
- **Geometrie/Heizlast-Regression:** 64 passed (222 assertions) — Kern unverändert.
- **Gesamtsuite:** 675 passed, **1 failed** = `Tests\Feature\Invoice\InvoiceDeletionGuardTest` (BroadcastException `localhost:6001`, Reverb E4-Baseline) — **vorbestehend, nicht G1a**.

## 6. Gegenbeweise (§15.3)

7 Mutationen, jede macht die BuildingModel-Suite rot; Validator danach md5-identisch (`578d8e9e218c5919eda4849023fc7c86`): reference_line_type optional · Öffnungsstation ignorieren · falscher Umlaufsinn · fehlende Wandreferenz · hartkodiertes Epsilon (Paritätstest) · Scan auto-confirm · Wandrichtung umkehren.

## 7. Bekannte Grenzen (bewusst, Folgewellen)

- **Höhenprofil / DG-Schräge:** `height_mode=profile` ist strukturell vorgesehen, aber in v1 nicht ausmodelliert (Ausbau G1b/G7).
- **Wandseite ↔ Umlaufsinn-Konsistenz:** `wall_side` je Boundary-Kante wird strukturell geführt, aber die vollständige Innen/Außen-Ableitung entsteht mit der Raumtopologie in G2c.
- **Segmentlängen** auf schrägen Wänden sind irrational; Öffnungs-Längenprüfung nutzt die zentrale relative Vergleichstoleranz (`recalc_abweichung_rel`).
- Schema-Struktur wird durch Contract-Tests geprüft; eine JSON-Schema-Validator-Bibliothek wird bewusst **nicht** eingeführt (Startblock §12).

## 8a. Unabhängige Prüfung (getrennte Instanzen, §18/§19)

- **Technischer Evaluator: GRÜN.** Selbst ausgeführt: BuildingModel 40 passed, Geometrie/Heizlast 64 passed; 14 Punkte belegt (Datei:Zeile). Kein rohes Epsilon (nur `GeometrieToleranz`/`recalc_abweichung_rel`); Validator nebenwirkungsfrei; Gegenbeweise belastbar. Hinweise (nicht blockierend): Fixture 14/15 tragen Zusatzcodes (optionale Hygiene); `wall_side` ungenutzt (Forward-Note G2c); fremde Working-Tree-Views beim Commit isolieren.
- **CAD-Re-Evaluator: FACHLICH_GRÜN_MIT_AUFLAGEN.** Alle 12 Fachfragen JA (schräger Raum 08, Umlaufsinn 11, Toleranz 15 nachgerechnet). Keine harten Einwände, kein ROT.

**Ratifizierte Folgewellen-Auflagen (kein Bau in G1a):**
- **A-1 (G2c):** `wall_side` je Boundary-Kante ableiten oder gegen `traversal`+Umlaufsinn validieren (latente Zweitwahrheit). Dokumentiert im Modellvertrag §3.3.
- **A-2 (G1a-Doku, erledigt):** Stationsdatum präzisiert auf „entlang der Referenzlinie ab Segmentstart" (Modellvertrag §3.2).
- **A-3 (G1b):** `lintel_height_mm` gegen `sill+rough_height` kreuzprüfen; `sill_height_mm`-Datum verbindlich dokumentieren (Modellvertrag §3.2).

## 8. Bestätigungen (§20)

`gebaeude_geometrie` unverändert · kein neuer Geometrie-Store · keine Migration · kein Controller · keine Route · keine View (fremde Sidebar-Änderung nicht Teil dieses Slices) · kein Package · kein Editor · kein Three.js · kein LiDAR · **kein Commit · kein Push**.
