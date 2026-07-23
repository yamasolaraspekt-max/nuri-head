# 3D-Code-Provenance

> **Rolle:** PLANNER (read-only). **Stand:** 2026-07-23. **Zweck:** Herkunftsnachweis — welcher
> Produktions-3D-/Dach-Code aus welcher Quelle/Commit-Historie stammt. Verhindert, dass „Neubau"
> beschlossen wird, wo in Wahrheit schon portierter, gereifter Code liegt.

## 1. Zwei Entwicklungslinien in der Playground-Historie

Die Playground-Git-Historie zeigt **zwei getrennte Dach-Stränge**:

**Strang A — „Dach-&-PV-Planer" (die reiche Insel).** Beginnt mit
`cf3eef3a feat(energie): vollständigen Dachplaner (Gemini-Prototyp) als eigene Seite integriert`
(`DachplanerProPage.tsx`). Danach die **EA-Serie** (Eingabeaufforderung) + Reparaturen, die die
`src/utils/`-Engine aufbauen, u. a.:
- `e1549947 fix(dachplaner): EA17 — Gaubenanschluss ans Hauptdach … + Geometrie-Pruefer` (erster
  Commit an `gaubeGeometrie.ts`)
- `ad29efee feat(dachplaner): EA25 — Gauben-Hauptdachloch als realer Fussabdruck-Polygon (Pentagon)`
- `1992072f feat(dachplaner): EA26 (Teilstufe 1) — Kehl-/Gratlinien geneigter L/T-Verschneidung als
  SSOT + Regressionsschloss + Overlay` (erster Commit an `dachVerschneidung.ts`)

**Strang B — „S1 D" Hausplaner-Dach (der schlanke Kern).** Eine bewusst rechteckige,
schema-/test-gegurtete Inkrement-Serie im **Hausplaner**:
- `2c9f7f8a S1 D-a: Dach-Datenmodell — RoofNode + schemaVersion 2 + Lade-Migration v1->v2 + Dach-Commands`
- `95bf4f0a S1 D-b: Dach-Geometrie — dachFlaechen() je Typ (flach/pult/sattel/walm) + Azimut-Ableitung (D4)`
- `72425d12 S1 D-d: Dach-Projektions-Vertrag dach_flaechen[] + eingefrorenes Fixture`
- `76234366 S1 D-c (3D-Kern): reines Dach-Mesh (dachMeshWelt) + three-Renderer-Wiring in szene.ts`
- `7df51388 S1 D-c (UI): Dach-Werkzeug + 2D-Symbol + Parameter-Panel`
- `8572da5c S1 D-c FIX (Evaluator ROT Krit.3): geteilte Kontur-Pruefung — dachMeshWelt wirft jetzt
  bei nicht-rechteckiger Kontur` (aktueller Playground-HEAD)

## 2. Herkunft der Produktions-Dateien (ticket)

| ticket-Produktions-Datei | Herkunft | Beleg |
|---|---|---|
| `geometry/dachGeometrie.ts` | **Strang B**, S1 D-b (`95bf4f0a`) | identisch zur Playground-Kopie `src/hausplaner/geometry/dachGeometrie.ts` (148 LOC, beide) |
| `renderers/three-d/dachMesh.ts` | **Strang B**, S1 D-c (`76234366`) + FIX `8572da5c` | identisch zur Kopie `src/hausplaner/renderers/three-d/dachMesh.ts` (108 LOC) |
| `projection/dachProjektion.ts` | **Strang B**, S1 D-d (`72425d12`) | identisch zur Kopie `src/hausplaner/projection/dachProjektion.ts` (43 LOC) |
| `geometry/dachVorlage.ts` | **Strang B** (P2b-6), Defaults-Tabelle | Datei-Kommentar „P2b-6" |
| `renderers/three-d/szene.ts` | Hausplaner-Kern; Dach-Wiring aus S1 D-c; Böden-Fix M0-Paket-1 (`741c3c9`) | — |

**→ Der rechteckige Produktions-Dachkern = portierter Strang B.** Er ist bereits Governance-geprüft
(Evaluator-ROT-Fix `8572da5c` eingearbeitet).

## 3. Die entscheidende Naht: Strang A ist NICHT in den Hausplaner gebrückt

`Playground/src/hausplaner/` (die Produktions-Spiegelung) importiert **keinerlei** `src/utils/`-Engine
aus Strang A — kein `utils/gaube*`, `utils/dachVerschneidung`, `utils/dachUForm`,
`utils/dachformVorlagen`, `utils/sparren*`, `utils/aufbau*`. Die reiche Engine hängt weiterhin **allein
an `DachplanerProPage.tsx`** (Strang A). Damit ist eindeutig:

- Was in Produktion fehlt (Gauben, L/T/U, Aufbauten, Holz) ist **in Strang A vorhanden und getestet**,
- aber **nie an den Hausplaner angeschlossen** worden.

Das ist keine Neubau-Situation, sondern eine **Anschluss-/Übernahme-Situation** (Doc 5).

## 4. Weitere Herkunfts-Anker

- ticket `2634caa` „Architektur-Zielbild EIN 3D-Hausplaner (Schichten S0–S4, Wellen W-A..W-F)" —
  Zielbild, das genau diese Konsolidierung vorsieht.
- `Playground/ZIEL-STRUKTUR-UND-PLAN.md` (2026-06-15): hält fest, dass **3D React 19 + three.js
  bleibt** und die Dach-Insel eine prinzipbedingte JS-Insel ist — d. h. die Engine wandert als
  TS-Modul, nicht als Blade-Migration.
- Aktive Feature-Branches im Playground (Auszug): `feat/wp-konfigurator-seeder`,
  `integrate/g-strang-react-blade-2026-07-05`, diverse `feat/*` — **kein** separater Dach-/Gauben-
  Prototyp außerhalb Strang A/B gefunden (die Engine ist die einzige reiche Quelle).

## 5. Konsequenz für den Reuse-Plan

Weil Strang A **reif + getestet + framework-frei** ist und Strang B **bereits erfolgreich portiert**
wurde, ist der bewährte Weg die Wiederholung genau dieses Musters: `src/utils/`-Engine als reine
TS-Module in den ticket-Hausplaner übernehmen (REUSE/ADAPT), nur die three.js-Renderteile aus dem
Seiten-Monolith extrahieren (EXTRACT) — **kein NEW**, solange die Engine trägt. Details: Doc 5.
