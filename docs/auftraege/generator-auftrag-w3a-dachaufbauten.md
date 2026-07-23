# Generator-Auftrag — W-3a: Dachaufbauten (Gauben/Dachfenster/Kamin) im 3D, additiv

**Rolle:** Generator (Claude Code in VS Code, native git/node). **Heimat-App:** `ticket`. Du setzt um
und meldest „umgesetzt" (kein Selbst-„grün"). Danach prüft der **Evaluator** (VS Code) unabhängig.
**Ausgestellt von:** Planner (Cowork), 2026-07-23.

## Basis
Reine Dach-Engine ist bereits portiert und grün (W-1/W-2). Integrations-Tip: Branch
`auto/hausplaner-ui-3b` (Commit `590700c`) — enthält UI-2 + W-1 + W-2 + UI-3a + UI-3b + Decken.
Arbeite auf einem NEUEN Branch `auto/hausplaner-w3a` aus diesem Tip.

## Ziel & Entscheidung (die eine Festlegung)
Dachaufbauten — **Gauben (5 Typen), Dachfenster, Kamin, Lüfter/Sat, Lichtkuppel** — als **ADDITIVES**
Feld am `RoofNode` einführen und im 3D rendern, über die schon portierte Engine
(`gaubeGeometrie`, `dachOeffnung`, `dachAusschnitt`, `aufbauPlatzierung`).
**BEWUSST NICHT in W-3a:** die `roofType`-Enum ändern (sattel/walm/pult/flach bleibt) und
`pruefeRechteckigeKontur` öffnen — die nicht-rechteckigen L/T/U-Formen sind **W-3b** (separat, mit
Yama-Review). W-3a bleibt damit **additiv ⇒ kein 422**, Bestands-Szenen bleiben valide.

## Umsetzung
1. **Datenmodell additiv** (`domain/scene.types.ts`): `RoofNode.aufbauten?: RoofAufbau[]` (OPTIONAL).
   `RoofAufbau = { id: string; typ: ObstacleType; x: number; y: number; breiteMm: number; hoeheMm: number;
   tiefeMm: number; rotationGrad?: number; neigungGrad?: number }`. `x`,`y` = relative Lage 0..1 auf der
   Dachfläche. `ObstacleType = 'chimney'|'window'|'vent'|'sat'|'lichtkuppel'|'schleppgaube'|'trapezgaube'|
   'flachgaube'|'giebelgaube'|'spitzgaube'` (aus dem Playground übernehmen).
2. **Zod additiv** (`domain/validation.ts`): `roofNodeSchema` um `aufbauten: z.array(aufbauSchema).optional()`
   erweitern (OPTIONAL — additiv). Danach **zwingend** `npm run schema:hausplaner` und das regenerierte
   `domain/scene-document-v2.schema.json` MITCOMMITTEN (Lehre 970f0cc→aecc517: Zod ohne Schema-Regen = 422/ROT).
3. **Commands** (`commands.types.ts` + `commands/applyCommand.ts`): `ADD_ROOF_AUFBAU`,
   `REMOVE_ROOF_AUFBAU`, `UPDATE_ROOF_AUFBAU` (undo-fähig, Reducer analog zu ADD_ROOF/UPDATE_ROOF).
4. **3D-Render** (`renderers/three-d/szene.ts`): je `roof.aufbauten` die Aufbauten rendern — Gaubenkörper
   über `gaubeGeometrie` (Fußabdruck/Anschluss), Dachöffnung/Loch über `dachAusschnitt`/`dachOeffnung`
   (masshaltig), Platzierung über `aufbauPlatzierung`. Dachfläche schneidet das Loch aus. Ungültige Lage
   (Aufbau außerhalb Fläche) ⇒ überspringen + sichtbar markieren (bestehendes Kante-Muster), **kein Crash**.
   Nur im aktiven Geschoss (bestehende Regel).
5. **Tests** (`__tests__/`): (a) `ADD_ROOF_AUFBAU`/`REMOVE_ROOF_AUFBAU` setzen/entfernen korrekt;
   (b) **Additiv-Beweis:** ein Roof OHNE `aufbauten` validiert weiter gegen das Schema (kein 422);
   (c) eine Schleppgaube erzeugt über die Engine gültige Geometrie (nicht-leeres Loch/Fußabdruck).

## Gate (selbst ausführen, VOR „umgesetzt")
`npm run tsc:hausplaner` (0) · `npm run schema:hausplaner:check` (0) · `npm run test:hausplaner`
(Anzahl ≥ vorher) · `npm run build:hausplaner`. Bei rot: Ursache nennen, NICHT „umgesetzt" melden.

## Kantenliste
Schema-Desync (erst Zod → `schema:hausplaner` → regeneriertes JSON einchecken); Loch nicht masshaltig;
Gaubenanschluss über First (EA17-Fix steckt schon in `gaubeGeometrie` — nicht neu erfinden); Aufbau-Lage
außerhalb Dachfläche (überspringen, markieren); nur aktives Geschoss rendern.

## Abnahmekriterien (für den Evaluator in VS Code)
1. Bestands-Szene ohne `aufbauten` lädt + validiert weiter → **kein 422** (additiv belegt).
2. Ein Roof mit 1 Schleppgaube: im 3D erscheint die Gaube **und** das ausgeschnittene Loch in der Dachfläche.
3. `schema:hausplaner:check` Exit 0, `test:hausplaner` grün (Anzahl ≥ vorher), **`roofType`-Enum unverändert**.
4. Commit-Scope ohne Beifang (nur die beabsichtigten Dateien; `git status` prüfen).
5. Byte-/Logik-Treue der Engine gewahrt (keine Änderung an den portierten `geometry/`-Modulen nötig — sie
   werden nur AUFGERUFEN).

## Bauordnung / Guardrails
Additiv; `roofType`-Enum NICHT ändern; `pruefeRechteckigeKontur` NICHT öffnen (das ist W-3b).
Eine Modellwahrheit (`hausplanerStore`, typed Command + inverse-patch); 2D/3D read-only.
Nur `auto/`-Branch, **KEIN main-Merge, KEIN Push, KEIN Deploy** ohne Yamas ausdrückliches Wort.
Meldung am Ende: „umgesetzt" mit den vier Exit-Codes an Yama/Evaluator.
