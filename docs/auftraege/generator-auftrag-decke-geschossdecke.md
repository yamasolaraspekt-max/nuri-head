# Generator-Auftrag — Decke (Geschossdecke) : der Etagen-Blocker

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Grundlage:** `docs/konzept/etagenbau-decken-giebel.md` (Feature A). **Basis:** aktueller grüner Dach-Tip.
**Warum zuerst:** ohne Decke kein Aufbau der nächsten Etage.

## Ziel & Entscheidung
Additiver **`CeilingNode` (type 'ceiling')** als **eigene Sammlung `ceilings: CeilingNode[]`** NEBEN `nodes[]`
— exakt das Muster von `roofs: RoofNode[]`, damit die Node-Union und alle Konsumenten (Raumerkennung/
Projektion/Wand-Renderer) **unberührt** bleiben. v1-Dokumente per Lade-Migration auf `ceilings: []`.

`CeilingNode`:
- `id`, `levelId`, `type:'ceiling'`, `visible`, `locked`, `tags`, `createdAt`, `updatedAt` (BaseNode-Form).
- `polygon: Array<{x,y}>` (Umriss in mm; Default = Gebäude-Umriss/`roomDetection` des Levels).
- `dickeMm: number` (Default = `level.floorThickness`).
- `oeffnungen?: Array<{ polygon: Punkt2D[] }>` (Durchbrüche, z. B. Treppe — Vorbild `dachAusschnitt`).
- `schichten?: Schicht[]` (Fußbodenaufbau — leer lassen, Feature B füllt/rechnet; Typ aus `wandaufbau.ts`).

## Nahtstellen (WO)
- `domain/scene.types.ts`: `CeilingNode` + `SceneDocument.ceilings: CeilingNode[]` (additiv).
- `domain/validation.ts` (Zod): `ceilings` als optionales Array, `CeilingNode`-Schema → **danach zwingend
  `npm run schema:hausplaner`** (Regen; sonst 422/RED).
- `domain/commands.types.ts` + `commands/applyCommand.ts`: `ADD_CEILING` (inkl. Variante „aus Grundriss":
  polygon aus Level-Umriss/`roomDetection`), `UPDATE_CEILING`, `REMOVE_CEILING`; **Regel:** je Level max. 1
  Decke (wie Dach). Treppen-Node im Level ⇒ automatisch Öffnung (Loch-Polygon) in die Decke stanzen.
- Lade-Migration (v1→v2): fehlendes `ceilings` ⇒ `[]`.
- `app/tools/*`: Werkzeug **„Decke"** mit eigenem Icon (24er-viewBox), in die Registry/Navi (Gruppe Bau/Hülle).
- `renderers/three-d/szene.ts` (+ ggf. neues `deckenMesh.ts`): Slab-Mesh aus `polygon` minus `oeffnungen`,
  Dicke `dickeMm`, auf **Wand-Oberkante des Levels** (`level.elevation + defaultWallHeight`). Eine Geometrie-
  Quelle je Decke.
- **Etagen-Stapel (eine Wahrheit):** die nächste Etage sitzt auf der Decke —
  `level[N+1].elevation = level[N].elevation + wandHöhe(N) + decke(N).dickeMm`. Wo diese Ableitung heute
  implizit ist, **eine** Stelle daraus machen (kein zweiter Rechenweg).

## Kantenliste
- Kein Umriss/leeres Level → keine Decke, kein Wurf.
- Öffnung größer als Decke / außerhalb → sauber begrenzt, kein NaN.
- Treppe ohne Decke / Decke ohne Treppe → beides gültig (Öffnung nur wenn Treppe da).
- Bestandsdokument ohne `ceilings` → lädt (Migration `[]`), validiert (kein 422).

## Gate (Generator selbst)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 (nach Regen) · `test:hausplaner` (+ neue) ·
`build:hausplaner` (nativ/x64).

## Abnahme (Evaluator)
1. **Additiv/kein 422:** Bestand ohne `ceilings` lädt+validiert; `ceilings` optional; Schema regeneriert
   (Gegenbeweis: Zod ohne Regen ⇒ `schema:check` rot).
2. **Decke real:** `ADD_CEILING`/„aus Grundriss" erzeugt Slab; 3D zeigt eine Decke auf Wand-Oberkante
   (Test auf Mesh/Fläche > 0).
3. **Treppendurchbruch:** Treppe im Level ⇒ Loch in der Decke (Test: Öffnungs-Polygon fehlt in der Slab-Fläche).
4. **Etagen-Stapel:** `level[N+1].elevation` folgt aus Decke(N)-Dicke — **eine** Ableitung (Test).
5. **Eine Wahrheit:** `ceilings` separate Sammlung (Node-Union unberührt); Öffnungslogik nutzt das
   `dachAusschnitt`-Muster (kein Parallel-Code); `wandaufbau` unverändert.
6. Werkzeug „Decke" + Icon in Navi sichtbar; A11y (Zustand Farbe+Text). Additiv, nur `auto/`-Branch, kein Push.

## Guardrails
Additiv; eine Wahrheit; Zod-Änderung → immer Schema-Regen; kein Beifang; Meldung „umgesetzt" (4 Exit-Codes)
→ Evaluator, Pflicht-Stopp. Feature B (Fußbodenaufbau-Panel) und C (Auto-Giebel) folgen als eigene Slices.
