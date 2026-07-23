# Generator-Auftrag — W-3b Stufe 2a Teil 2: L/T/U-Dach-Rendering (additiver Modell-Slice + Port-Verdrahtung)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Basis-Branch:** der freigegebene Tip mit rect-Fix (`cff1fe5`, FREIGABE). **Geparkt** bis Yamas „go".
Kein Browser, keine UI — rein logik-/test-beweisbar. (Der Vorlagen-Picker bleibt Stufe 2b.)

## Warum dieser Auftrag (der gemessene Befund)
rect-Fix steht (FREIGABE `cff1fe5`, test 632/632). L/T/U rendern trotzdem **nicht**, und zwar aus einem
klar benannten Grund, den ich read-only bestätigt habe: die reinen Engines sind da und getestet
(`geometry/dachVerschneidung.ts` für l/t, `geometry/dachUForm.ts` für u), **aber das Modell trägt ihre
Eingaben nicht**. `RoofNode` hat heute `polygon, roofType, neigungGrad, firstAzimutGrad, aufbauten?` —
**nicht** die Anbau-/Schenkel-Maße, die `VerschneidungEingabe { length, width, lengthB, widthB }` bzw.
`UFormEingabe` brauchen. Deshalb kann `dachRoh` für die Verschneidungsformen nichts rechnen und der
Guard liefert `flaechen: []`. Die Lücke ist **Modell**, nicht Geometrie.

## Ziel & Entscheidung
Ein **additiver Modell-Parameter-Slice**, der die L/T/U-Anbau-Maße ans `RoofNode` bringt, und die
Verdrahtung dieser Maße durch `dachRoh` in die **schon getesteten** Verschneidungs-Engines — nur
aufgerufen, nie geändert (Byte-Treue W-1/W-2). Entscheidungen, verbindlich:

1. **Optionaler Sub-Objekt-Slot, additiv:** `RoofNode` bekommt ein **optionales** Feld
   `anbau?: RoofAnbauMasse` mit `{ length: number; width: number; lengthB?: number; widthB?: number }`
   (mm, wie die übrigen Maße). **Optional** ist Pflicht: Bestandsdaten und alle rechteckigen Formen
   (`sattel/walm/pult/flach/rect`) tragen es nicht → **keine neue 422-Fläche**, kein Migrations-Zwang.
2. **Eine Wahrheit fürs Mapping:** genau **eine** kleine Abbildungs-Funktion `anbauZuEingabe(node)` (in
   `dachMesh.ts`, neben `dachRoh`) übersetzt `node.anbau` → `VerschneidungEingabe` (l/t) bzw.
   `UFormEingabe` (u). Kein zweiter Ort, der dieselbe Übersetzung nochmal macht.
3. **U darf zuerst gehen:** `dachUForm` ist self-contained (nur `length/width` + Neigung). Wenn L/T
   an einer Kante hakt, ist ein Zwischenstand „**u rendert, l/t noch []**" ausdrücklich erlaubt und
   abnehmbar — nicht alles-oder-nichts. l/t (`dachVerschneidung`, braucht `lengthB/widthB`) danach.
4. **SSOT `dachRoh` bleibt die eine Quelle:** die neuen Flächen entstehen **in** `dachRoh`; der
   `istVerschneidungsForm(...) → flaechen:[]`-Guard wird durch die echten Verschneidungsflächen
   ersetzt. `dachMeshWelt`/`dachflaechen` konsumieren weiter **nur** `dachRoh`; `const rad =` bleibt 1×.

## Nahtstellen (WO)
- `domain/scene.types.ts`: `RoofNode` um `anbau?: RoofAnbauMasse` + Typ `RoofAnbauMasse` (additiv).
- `validation.ts` (Zod): `anbau` als **optionales** Objekt-Schema ergänzen → danach **zwingend**
  `npm run schema:hausplaner` (Schema-Regen!) — sonst 422/RED (die 970f0cc→aecc517-Lektion).
- `renderers/three-d/dachMesh.ts`: `anbauZuEingabe(node)` + `dachRoh`-Zweige für l-/t-/u-shape.
- **Nicht anfassen** (nur importieren/aufrufen): `geometry/dachVerschneidung.ts`, `geometry/dachUForm.ts`,
  `geometry/gaubeGeometrie.ts`, `geometry/dachformVorlagen.ts`. `roofShape.ts` unverändert (Enum steht).

## Kantenliste (wo es erfahrungsgemäß bricht)
- `node.anbau` **fehlt** bei Verschneidungsform → Marker/leer, **kein** Wurf (Alt-/Teildaten).
- `lengthB/widthB` fehlen bei l/t (nur u-taugliche Maße gesetzt) → l/t leer + Marker, nicht crashen.
- Degenerierte Maße (0 / negativ / Anbau ≥ Hauptbau) → `dachVerschneidung`/`dachUForm` sauber leer
  behandeln (Sinn der geöffneten `pruefeRechteckigeKontur`), kein NaN in die Triangulierung.
- Additiv-Beweis: eine Bestands-`RoofNode` **ohne** `anbau` validiert unverändert (kein 422).
- Schema-Drift: Zod geändert ohne `schema:hausplaner` → `schema:hausplaner:check` MUSS rot werden
  (dieser rote Test ist der Beweis, dass das Gate greift — vor dem Regen einmal sehen, dann grün).

## Gate (Generator selbst)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 (nach Regen) · `test:hausplaner` (≥ 632 + neue) ·
`build:hausplaner` (nativ/x64; ARM-Geräte-VM bekannt nicht baubar).

## Abnahmekriterien (Evaluator misst selbst nach)
1. **Additiv/keine 422:** Bestands-`RoofNode` ohne `anbau` validiert unverändert; `anbau` ist optional.
2. **Schema-Gate geschlossen:** `schema:hausplaner:check` grün **weil** neu generiert — Gegen-Beweis:
   Zod-Feld temporär ändern ohne Regen ⇒ check wird rot (Gate greift nachweislich).
3. **U rendert real:** `dachMeshWelt`/`dachflaechen` für u-shape mit gesetztem `anbau` liefern echte
   Flächen (nicht `[]`), belegt per verhaltensprüfendem Test gegen `dachUForm`-Erwartung.
4. **L/T rendern real ODER begründet geparkt:** l/t liefern echte Verschneidungsflächen; falls in
   diesem Slice noch nicht, ist der Zwischenstand „u grün, l/t []" **im Test dokumentiert** (kein
   stiller Wegfall, kein Crash).
5. **SSOT intakt:** Flächen aus `dachRoh`; Guard→[] ersetzt; kein zweiter Rechenweg; `const rad =` 1×;
   `anbauZuEingabe` genau einmal.
6. **Ports nur aufgerufen:** `dachVerschneidung`/`dachUForm`/`gaubeGeometrie`/`dachformVorlagen` **nicht**
   im git-Diff (Byte-Treue).
7. **B1-Compile-Beweis intakt** (`EngineRoofShape extends RoofShape`).
8. **Kein Crash an Kanten** (fehlendes/degeneriertes `anbau` → Marker/leer, nie Wurf).
9. **Gate:** tsc 0 · schema:check 0 · test ≥ 632 (+ neue) · build x64-grün.
10. **Scope/kein Beifang:** nur die deklarierten Dateien; geprüfte Basis `cff1fe5`.

## Guardrails
Additiv (`anbau` optional); eine Wahrheit (`dachRoh` SSOT, `anbauZuEingabe` einmal); portierte
`geometry/*` unverändert; Zod-Änderung → **immer** `schema:hausplaner` mitlaufen; kein Beifang
(`git reset -q HEAD -- .`, gezielt adden); nur `auto/`-Branch, **KEIN main-Merge/Push/Deploy** ohne
Yamas Wort. Meldung „umgesetzt" (4 Exit-Codes) → zurück an den Evaluator, Pflicht-Stopp.
