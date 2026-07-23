# Korrigiertes Routing — Push + Dach-UI-Abnahme (2026-07-23)

**Korrektur (Evaluator-Einwand, berechtigt):** Push/checkout/build sind KEINE Evaluator-Aufgaben. Der
Abnehmende darf den Stand nicht selbst herstellen (sonst Generator+Evaluator in einem = blinder Fleck).
Drei getrennte Bahnen:

## Bahn 1 — PUSH → Yama (stehende Regel: pushen macht ausschließlich Yama)
Ziel-Tip **`cff1fe5`** (rect-Fix, volle FREIGABE, enthält e9334bb; 4b8eb04 bewusst draußen wegen offener
U-Optik). Befehle liegen in `generator-auftrag-repo-konsolidierung.md` (UPDATE-Block). Nur Yama führt aus.

## Bahn 2 — SETUP (checkout + build) → Generator (Executor-Rolle)
```
git checkout auto/hausplaner-dach-ui        # Tip 1d8c735
npm run build:hausplaner                     # nativ, Exit 0 (x64/Mac)
```
Danach Meldung an den Evaluator: „dach-ui @ 1d8c735 gebaut, Studio offen".

## Bahn 3 — ABNAHME → Evaluator (read-only, NACHDEM Generator gebaut hat)
1. Selbst verifizieren: Branch==`auto/hausplaner-dach-ui`, HEAD==`1d8c735`.
2. Read-only-Gates selbst: `tsc:hausplaner` / `schema:hausplaner:check` / `test:hausplaner` (erwartet 638/638).
3. Statik Dach-UI: 8 Formen im `<select>` == `RoofShape`; konditionale Anbau-Felder (u/l/t → length/width;
   l/t → +lengthB/widthB; rect/Altformen keins); an `aktualisiereDach` verdrahtet; rein UI/Command
   (Modell/Schema/`dachRoh` unverändert); 0 roher Hex.
4. U-Optik im Browser: u-shape + Anbau → U-Dach sichtbar → Lage/Orientierung in 3 Viewports
   (Generator-Flag „Schwerpunkt-Näherung, evtl. versetzt").
5. Votum je Slice (Dach-UI + U-Optik). Grün → Planner (Freigabe L/T-Faces Teil 3 / Batch 1); rot → Generator.

**Ballbesitz:** Yama (Push) · Generator (Bahn 2) · dann Evaluator (Bahn 3).
