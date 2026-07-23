# Generator-Auftrag — 3D-Snapshot-Fähigkeit (macht WebGL-Optik abnehmbar)

**Rolle:** Generator (VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Befund (am Code gemessen):** `renderers/three-d/szene.ts:95` = `new THREE.WebGLRenderer({ antialias: true })`
— **kein `preserveDrawingBuffer`**. Folge: ein Screenshot des WebGL-Canvas (CDP `Page.captureScreenshot` bzw.
`canvas.toDataURL()`) liefert einen geleerten Puffer → CDP-Timeout. Das vorhandene `toDataURL`
(`HausplanerApp.tsx:260`) betrifft die **Konva-2D-Stage**, nicht das 3D-Canvas. → Die 3D-Optik ist heute
**nicht capturebar**, damit für den Evaluator visuell nicht prüfbar (U-Dach, künftige 3D-Slices).

## Ziel & Entscheidung
Eine **3D-Snapshot-Fähigkeit**, damit der Evaluator den WebGL-Frame als Standbild bekommt — **perf-schonend**,
also **nicht dauerhaft** `preserveDrawingBuffer` (das kostet Leistung im Normalbetrieb). Zwei gangbare Wege,
Generator wählt den saubereren:
- **A (bevorzugt):** eine `snapshot(): string`-Methode an der Szene, die den three.js-Frame synchron nach
  `renderer.render(...)` als DataURL greift. Dafür den Renderer mit `preserveDrawingBuffer: true` **nur dann**
  erzeugen, wenn ein **Capture-/Debug-Flag** aktiv ist (z. B. `?capture=1` in der Studio-URL oder ein
  Debug-Schalter) — im Normalbetrieb unverändert.
- **B:** Capture unmittelbar in der `requestAnimationFrame` **vor** dem Buffer-Swap (ohne preserveDrawingBuffer),
  falls sauber umsetzbar.

## Nahtstellen
`renderers/three-d/szene.ts` (Renderer-Optionen hinter Flag + `snapshot()`); ggf. Flag-Durchreichung in
`HausplanerApp`/`studio.blade.php`. **Kein** Eingriff in `dachRoh`/Modell/Schema.

## Abnahme (Evaluator)
1. Mit Capture-Flag liefert die Szene ein **nicht-leeres** 3D-Standbild (DataURL/Screenshot) — der Evaluator
   kann die U-Optik (Lage/Orientierung) endlich prüfen.
2. **Ohne** Flag: Normalbetrieb unverändert (kein Dauer-`preserveDrawingBuffer`, keine Perf-Regression).
3. Rein Frontend/Render-Infrastruktur, additiv; `dachRoh`/Modell/Schema unberührt; 0 roher Hex; kein Push.

## Guardrails
Additiv, hinter Flag (Perf); eine Wahrheit (nur szene.ts erzeugt den Renderer); Meldung „umgesetzt" → Evaluator.
**Reihenfolge:** kann zusammen mit dem u-shape-Feld-Fix laufen (beide Frontend) — dann macht der u-Fix die
U *erreichbar* und dieser Slice sie *sichtbar-prüfbar*.
