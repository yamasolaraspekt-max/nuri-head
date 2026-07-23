# Abnahme W-2 — reine Dach-Engine (Aufbauten, Holz, Öffnungen, Gauben, Vorlagen)

> **Rolle:** Evaluator (getrennter Durchgang). **Stand:** 2026-07-23. **Gegenstand:** Branch
> `auto/hausplaner-w2`, Commit `2d7d8b3`. **Kein main-Merge/Push/Deploy.**

## Umfang (topologisch portiert, faithful Reuse)
15 Module `Playground/src/utils/` → `ticket/.../geometry/`: `polygonFlaeche`, `aufbauOrientierung`,
`aufbautenStatus`, `sparrenTrennung`, `auswechslung`, `schifterListe`, `holzMengen`, `holzBauteile`,
`aufbauPlatzierung`, `dachOeffnung`, `linienBauteile`, `gaubeGeometrie`, `dachAusschnitt`,
`dachformVorlagen`, `grundriss` — + 10 portierte Tests. Alle Geschwister-/Deps innerhalb des Portsets
bzw. `dachWerte` (W-1). Persistiertes Schema unberührt.

## Selbst gemessen
| Kriterium | Ergebnis |
|---|---|
| `tsc:hausplaner` | **Exit 0** |
| `test:hausplaner` | **607 / 607, fail 0** (W-1 338 → +269) |

## Gegen-Beweise
1. **Byte-Identität:** `diff` Quelle↔Ziel → **15/15 identisch**. Keine Logik-Drift.
2. **Schema-neutral:** `roofType: z.enum(['sattel','walm','pult','flach'])` **unverändert** → kein 422.
3. **Scope:** Commit = 25 Dateien (7670 Ins.), ausschließlich `geometry/` + `__tests__/` — **kein Beifang**
   (keine `renderers/`, `domain/`, `app/`-Änderung).
4. **Zwischenfall sauber gelöst (nicht kaschiert):** 2 Tests brachen zunächst (`ERR_MODULE_NOT_FOUND: grundriss`)
   — Test-Helfer `grundriss` war übersehen (Dep-Scan sah nur Modul-, nicht Test-Importe). Nachgeportet
   (hängt nur an `polygonFlaeche`), danach grün. Lehre im Journal: Test-Importe mitscannen.

## Votum: **GRÜN**

Damit ist die **reine, testgestützte Dach-Engine vollständig im ticket** (W-1 + W-2). **STOPP-Punkt:**
der nächste Schritt (W-3) ist die 3D-Verdrahtung + Schema-Konsolidierung (RoofShape L/T/U in die
persistierte Szene, `pruefeRechteckigeKontur` öffnen, `roofType`-Enum erweitern) — das berührt Live-Daten
und braucht **Yama-Review**. Autonomer Loop endet hier; kein weiterer Mitwecker.
