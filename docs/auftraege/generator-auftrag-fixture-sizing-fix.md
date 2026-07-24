# Generator-Auftrag — Fixture-Sizing-Fix: 3D-Snapshot darf nicht 2×2 sein

**Rolle:** Generator (VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Basis:** Fixture-Stand `105f8c3`. **Grund:** Evaluator-Votum NACHBESSERN — belegter Sicht-Infrastruktur-Bug.
**Frontend-Linse angewandt.**

## Befund (Evaluator, live gemessen + Planner am Code bestätigt)
`?fixture=u-dach&capture=1` → `window.__hausplanerSnapshot3d()` liefert einen **158-Zeichen-PNG (leer)**.
Ursache: der three.js-Canvas mountet mit **2×2 px**, obwohl der Container 907×584 ist. Am Code: `szene.ts:97`
`renderer.setSize(container.clientWidth || 1, container.clientHeight || 1)` — im Fixture-Fluss ist der
Container zur Mount-Zeit **0** (Expertenmodus, 3D-Default, Layout noch nicht gelegt) ⇒ 1×1 ×pixelRatio = 2×2.
Der vorhandene `ResizeObserver`/`groesseAnpassen()` (Z.170/240-244) korrigiert es im Fixture-Fluss nicht
rechtzeitig (Evaluator: weder echtes Fenster-Resize noch 2D↔3D-Toggle skalierten den Renderer nach).
**Nicht** die Szene ist kaputt — der U-Render funktioniert (Evaluator sah einen echten 782-KB-Frame im
fixture-freien Fluss). Es ist reine **Sizing-/Mount-Reihenfolge**.

## Fix (Frontend-Linse: three.js imperativ — vor Capture korrekt dimensionieren + Frame erzwingen)
1. **`snapshot()` robust machen** (`renderers/three-d/szene.ts`): **vor** `toDataURL()`
   erst `this.groesseAnpassen()` (setzt `setSize` + Kamera-`aspect` auf aktuelle `clientWidth/Height`) **und**
   `this.renderer.render(this.szene, this.kamera)` erzwingen. So spiegelt der Snapshot immer die **aktuelle
   Container-Größe** + einen frischen Frame — auch wenn der Erst-Mount 2×2 war.
2. **Ehrlicher Leer-Fall:** ist die Container-Größe zur Capture-Zeit real 0 (3D-Ansicht nicht aktiv), **kein
   stiller leerer PNG** — stattdessen die 3D-Ansicht vorher aktivieren/sizen ODER einen klaren Marker/Fehler
   zurückgeben („3D-Ansicht nicht aktiv"), damit „leer" nicht wie „ok" aussieht (Beweis statt Schein).
3. Optional robuster: im **Fixture-Modus** die 3D-Ansicht erst rendern, wenn ihr Container echte Größe hat
   (ein Frame nach Layout / `requestAnimationFrame`), statt bei 0 zu mounten.

## Nahtstellen
`renderers/three-d/szene.ts` (`snapshot()` + ggf. Fixture-Mount). `capture.ts`/Fixture-Statik **unverändert**
(flag-gegated, Modellpfad, keine Persistenz — vom Evaluator als sauber bestätigt). Kein `dachRoh`/Modell/Schema.

## Abnahme (Evaluator)
1. `?fixture=u-dach&capture=1` → `window.__hausplanerSnapshot3d()` liefert einen **nicht-leeren** PNG; der
   three.js-Canvas hat **Container-Größe** (nicht 2×2). Die U ist deterministisch sichtbar (Lage/Orientierung).
2. Gleiches trägt für die **Decke-Slab-Sicht** (dieselbe Fixture/Capture) und künftige 3D-Slices.
3. Ohne Flag: Normalbetrieb unverändert (`preserveDrawingBuffer` flag-gated, keine Perf-Regression).
4. Gate: tsc 0 · schema 0 · test (≥ aktuell) · build 0. Additiv, nur `auto/`-Branch, kein Push.

## Guardrails
Rein Render-Infrastruktur; Szene/Fixture-Logik unberührt; three.js sauber (dispose/kein Leak); Meldung
„umgesetzt" → Evaluator, der dann U-Optik + Decke-Slab deterministisch durchfährt.
