# Generator-Auftrag — Fix: u-shape braucht ALLE 4 Anbau-Felder in der UI (KORRIGIERT)

**Rolle:** Generator (VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Basis:** Dach-UI-Slice (`1d8c735`). **Ballbesitz: Generator** (rot → Fix). Rein UI/Command.

## KORREKTUR meiner früheren Vorgabe
Frühere Planner-Annahme „u-shape braucht nur length/width" war **falsch**. Der Evaluator hat gemessen:
`anbauZuEingabe` liefert `null`, solange **nicht alle vier** Maße > 0 sind (`length && width && lengthB &&
widthB`); `uFormFlaechen` definiert die U-Form aus **Außenrechteck (length/width) minus Kerbe/Innenhof
(lengthB/widthB)**. Der **Render ist korrekt** — vier Maße sind fachlich nötig. Der Fehler liegt in der
**UI-Feld-Konditionalität** (zeigt für u-shape nur 2 Felder). `dachMesh.ts`/`anbauZuEingabe`/`dachUForm`
bleiben **unverändert**.

## Umsetzen (rein UI, `HausplanerApp.tsx` Dach-Panel)
1. Für **u-shape** ALLE vier Anbau-Felder einblenden und an `anbau` verdrahten (wie bei l/t):
   - `length`/`width` → Label **„Außenmaß Länge/Breite"**.
   - `lengthB`/`widthB` → Label **„Innenhof/Kerbe Länge/Breite"** (die U-Aussparung).
2. Für **l/t-shape** bleiben die vier Felder wie sie sind, Label `lengthB`/`widthB` = **„Anbau Länge/Breite"**
   (Bedeutung unterscheidet sich vom Innenhof — Labels je Form).
3. `rect`/4 Altformen: unverändert kein Anbau-Feld.
4. Verdrahtung an die vorhandene `aktualisiereDach(...)`-Brücke (setzt `anbau.{length,width,lengthB,widthB}`).

## Abnahme (Evaluator)
1. u-shape zeigt **vier** Felder; mit vier Maßen > 0 → `anbauZuEingabe` liefert Eingabe → `uFormFlaechen`
   echte Flächen → **U-Dach rendert sichtbar** (dann U-Optik browsable: Lage/Orientierung, Schwerpunkt-Näherung).
2. `dachMesh.ts`/`dachUForm`/`anbauZuEingabe` NICHT im Diff (Render unverändert).
3. l/t weiter mit vier Feldern; Render bleibt geparkt ([]) bis Teil 3 (dokumentiert, kein Crash).
4. Rein UI/Command (kein Modell/Schema-Eingriff), 0 roher Hex, Gates grün. Additiv, nur `auto/`-Branch, kein Push.

## Danach
U ist damit über die UI erzeugbar und sichtbar → Evaluator schließt die U-Optik-Runde. L/T-Flächen bleiben
Teil 3 (`generator-auftrag-w3b-stufe2a-teil3-lt-flaechen.md`).
