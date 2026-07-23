# 3D-Playground-Capability-Matrix

> **Rolle:** PLANNER (read-only). **Stand:** 2026-07-23. **Scope:** Was der **Playground
> „Dach-&-PV-Planer" (Dach-Insel, keine Produktion)** heute kann — belegt an `src/utils/`,
> `src/stores/`, `DachplanerProPage.tsx` und den 18 Test-Dateien.
> Legende: ✅ vorhanden+getestet · ◑ vorhanden, an die 3786-Z.-Seite gebunden · ❌ fehlt.

## A. Dachformen

| Fähigkeit | Status | Beleg |
|---|:--:|---|
| flach / pult / sattel / walm | ✅ | `roofTypes.RoofShape`, `dachformVorlagen.ts` |
| `rect` (rechteckige Basis) | ✅ | `RoofShape='rect'` |
| **L-Form / T-Form** (geneigt, mit Verschneidung) | ✅ | `RoofShape='l-shape'|'t-shape'`, `dachVerschneidung.ts` (Kehl-/Gratlinien, SSOT, Regressionsschloss) |
| **U-Form** (geneigt) | ✅ | `dachUForm.ts` (+ Test) |
| Dachform-Vorlagenbibliothek (parametrisch) | ✅ | `dachformVorlagen.ts` (2399 LOC, + Test) |

## B. Gauben / Dachaufbauten

| Fähigkeit | Status | Beleg |
|---|:--:|---|
| Gauben-Geometrie inkl. Anschluss ans Hauptdach | ✅ | `gaubeGeometrie.ts` (498 LOC, + Test); EA17 Anschlussfix, EA25 Fußabdruck-Polygon |
| 5 Gaubentypen: Schlepp/Trapez/Flach/Giebel/Spitz | ✅ | `ObstacleType` = schleppgaube/trapezgaube/flachgaube/giebelgaube/spitzgaube |
| Dachfenster als Dachöffnung/-aufbau | ✅ | `ObstacleType='window'`; `dachOeffnung.ts`, `dachAusschnitt.ts` |
| Kamin / Lüfter / Sat / Lichtkuppel | ✅ | `ObstacleType` chimney/vent/sat/lichtkuppel |
| Maßhaltige Dachöffnung (Loch-Polygon) | ✅ | `dachAusschnitt.ts` (510 LOC, + Test) |
| Platzierung/Orientierung/Status der Aufbauten | ✅ | `aufbauPlatzierung.ts`, `aufbauOrientierung.ts`, `aufbautenStatus.ts` (alle + Test) |

## C. Zimmerer-Handwerk / Stückliste

| Fähigkeit | Status | Beleg |
|---|:--:|---|
| Sparren-Trennung an Öffnung | ✅ | `sparrenTrennung.ts` (+ Test) |
| Auswechslungen (Wechselhölzer) | ✅ | `auswechslung.ts` (174 LOC, + Test) |
| Schifterschnitt-Stückliste | ✅ | `schifterListe.ts` (+ Test) |
| Holzmengen / Holz-Bauteile (Pfetten, Grat-/Kehlsparren) | ✅ | `holzMengen.ts`, `holzBauteile.ts` (beide + Test) |
| Linien-Bauteile (Schneefang o. ä.) | ✅ | `linienBauteile.ts` |
| Klemm-/Umrechnungslogik (Grad/Neigung/Maß) | ✅ | `dachWerte.ts` (+ Test) |

## D. Material / Produkt / PV

| Fähigkeit | Status | Beleg |
|---|:--:|---|
| Eindeckung/Ziegel-Produkt aus DB | ✅ | `dachproduktService.ts` (+ Test), `roofConfiguratorService.ts` |
| Material-/Montage-Panels (UI) | ◑ | `components/energie/CoveringMaterialPanel`, `EindeckungMaterialPanel`, `MontagesystemPanel` |
| PV-Modul-Belegung | ◑ | `ModuleData` in `roofTypes.ts`; Belegung in `DachplanerProPage.tsx` |

## E. 3D-Darstellung

| Fähigkeit | Status | Beleg |
|---|:--:|---|
| three.js-3D-Rendering des Dachs inkl. Gauben/Aufbauten | ◑ | `DachplanerProPage.tsx` (3786 LOC) — Render + UI + Logik **vermischt** im Seiten-Monolith |
| Reine Geometrie ohne three.js-Kopplung | ✅ | gesamte `src/utils/`-Engine ist framework-frei |

## F. Qualität / Absicherung

| Aspekt | Status | Beleg |
|---|:--:|---|
| Test-Abdeckung Engine | ✅ | 18 Test-Dateien; Kern-Logik als „REINE, testbare" Funktionen |
| Regressionsschlösser (eingefrorene Fixtures) | ✅ | u. a. `dachVerschneidung` SSOT + Regressionsschloss |
| Reifegrad | ✅ | ~28 Iterationen (EA11–EA28, Reparatur 3–10) |

## Kernaussage (Playground)

Die Playground-Insel deckt **genau die Lücken der ticket-Produktion** (Doc 2, Abschnitte B/C/D)
bereits ab — Gauben, Dachfenster/Aufbauten, L-/T-/U-Verschneidung, Holz-/Sparren-Stückliste,
Material/PV — und zwar **als reine, getestete Logik** (`src/utils/`), sauber getrennt von der 3D-Seite.
Die einzige echte Kopplung liegt in `DachplanerProPage.tsx` (3786 Z.), wo Rendering, UI und Zustand
vermischt sind (◑). **Reuse-Strategie (Doc 5):** die reine `src/utils/`-Engine wird gebrückt/übernommen;
der Seiten-Monolith wird **nicht** übernommen, nur seine Renderlogik als Vorlage extrahiert.
