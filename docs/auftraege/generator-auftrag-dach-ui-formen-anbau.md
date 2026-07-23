# Generator-Auftrag — Dach-UI: alle Formen + Anbau erreichbar (macht U-Render sichtbar)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Grundlage/Befund:** Evaluator hat am lebenden Objekt belegt — der Dachform-`<select>` bietet nur
`sattel/walm/pult/flach`; **kein rect/l/t/u-shape, kein Anbau-Feld**. Der gebaute U-Render (Teil 2, Code-
FREIGABE, 638 selbst gefahren) ist damit **für den Nutzer dunkel** und die U-Optik nicht prüfbar. Dieser
Slice holt die Formen an die Oberfläche.

## Ziel & Entscheidung
Die **eine `RoofShape`-Wahrheit** (8 Formen) im Dach-Panel erreichbar machen und die **Anbau-Maße** eingebbar
— rein UI/Command, **kein** Modell-/Schema-Eingriff (Enum + `RoofAnbauMasse` stehen seit W-3b Stufe 1/2a).

## Nahtstellen (WO — exakt)
- `app/HausplanerApp.tsx` **~Z.1078**: den roofType-`<select>` um die fehlenden Optionen ergänzen —
  `rect` („Rechteck-Fläche"), `l-shape` („L-Form"), `t-shape` („T-Form"), `u-shape` („U-Form"). Labels
  deutsch; Werte exakt die `RoofShape`-Strings.
- `app/HausplanerApp.tsx` **~Z.1085 ff.** (neben Neigung/First/Überstand): **Anbau-Eingabe**, konditional:
  - `u-shape`: Felder `length`, `width` (Hauptbau).
  - `l-shape`/`t-shape`: zusätzlich `lengthB`, `widthB` (Anbau).
  - `rect`/4 Altformen: **kein** Anbau-Feld.
  Verdrahtet an die vorhandene `aktualisiereDach(...)`-Command-Brücke (setzt `roofType` bzw. `anbau`).
- **Nicht anfassen:** `domain/roofShape.ts`, `domain/scene.types.ts`, `validation.ts`/Schema (stehen),
  `dachMesh.ts`/`dachRoh` (Render steht).

## Token-Disziplin
Nur die vorhandenen Style-Konstanten (`FARBEN`, `panelInput`) bzw. `T.*` verwenden — **kein roher Hex** in
den geänderten Zeilen (gleiche Regel wie der Navi-Fix; `docs/architektur/react-hausplaner-token-scope.md`).

## Kantenliste
- Formwechsel `u-shape → rect`: `anbau` bleibt/wird ignoriert (rect braucht keins), kein Crash.
- `l/t` gewählt, `lengthB/widthB` noch leer: Felder sichtbar, **Render bleibt leer bis Teil 3** (L/T-Faces) —
  kein stiller Wegfall, kein Wurf (Teil-2-Verhalten unverändert).
- `u-shape` mit `length/width`: **U rendert sichtbar** (das ist der Kern-Sichtbarkeitsgewinn).

## Gate (Generator selbst)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 (kein Schema-Eingriff) · `test:hausplaner` (+ ggf. UI-nahe
Tests) · `build:hausplaner` (nativ/x64).

## Abnahme (Evaluator — jetzt browserbar)
1. Der Dachform-`<select>` bietet **alle 8** Formen; Werte == `RoofShape`.
2. `u-shape` wählbar + `length/width` eingebbar ⇒ **U-Dach im 3D sichtbar** (Browser, 3 Viewports) —
   **damit ist die zuvor aufgeschobene U-Optik-Auflage (Schwerpunkt-Näherung) prüfbar** und wird hier
   mitgenommen (Lage/Orientierung).
3. `rect` erzeugt definiertes Verhalten (flache Fläche, rect-Fix); `l/t` wählbar (Flächen leer bis Teil 3,
   dokumentiert — kein Crash).
4. **Token-Disziplin:** kein roher Hex in den geänderten Zeilen.
5. Additiv (kein Modell/Schema-Diff); nur `auto/`-Branch, kein Push.

## Guardrails
Rein UI/Command; `RoofShape`/`RoofAnbauMasse`/Render unverändert (nur konsumiert); eine Wahrheit; kein
Beifang; Meldung „umgesetzt" (4 Exit-Codes) → Evaluator, Pflicht-Stopp.
