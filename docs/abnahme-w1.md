# Abnahme W-1 — Dach-Fundament (Werte + Verschneidung)

> **Rolle:** Evaluator (bewusst getrennter Durchgang; Code nicht selbst gebaut). **Stand:** 2026-07-23.
> **Gegenstand:** Branch `auto/hausplaner-w1`, Commit `588283d`. Port der reinen Playground-Dach-Logik
> (Werte + Verschneidung) in den ticket-Hausplaner. **Kein main-Merge, kein Push, kein Deploy.**

## Umfang (laut Auftrag, planner-geschärft auf Abhängigkeiten)

Portiert (reine Reuse) aus `Playground/src/utils/` → `ticket/resources/planner/hausplaner/geometry/`:
`dachWerte.ts` (Klemm-/Umrechnungslogik), `dachVerschneidung.ts` (Kehl-/Gratlinien L/T), `dachUForm.ts`
(geneigte U-Form) — je mit portiertem Test in `__tests__/`. **Bewusst NICHT** in W-1: `dachformVorlagen.ts`
(zieht `polygonFlaeche`/`aufbauPlatzierung`/`linienBauteile` aus späteren Wellen — wäre „Springen").
Persistiertes Zod-/Roof-Schema **unverändert** (additiv, schema-neutral).

## Selbst gemessen (nicht dem Generator geglaubt)

| Kriterium | Ergebnis |
|---|---|
| `npm run tsc:hausplaner` | **Exit 0** |
| `npm run schema:hausplaner:check` (Teil von test) | **Exit 0** |
| `npm run test:hausplaner` | **338 / 338, fail 0** (vorher 307 → +31 aus den 3 neuen Tests) |

## Gegen-Beweise

1. **Reine Reuse belegt:** `diff` Playground-Quelle ↔ portierte Module → **byte-identisch** für alle drei
   (dachWerte/dachVerschneidung/dachUForm). Keine Logik-Drift.
2. **Schema-/Framework-neutral:** die drei Module sind **importfrei** (kein `import`/`require`) — sie
   berühren weder Zod noch three.js noch die persistierte Szene.
3. **Persistenz unangetastet:** `roofType: z.enum(['sattel','walm','pult','flach'])` in `validation.ts`
   **unverändert** → kein 422-Risiko, Bestands-Szenen bleiben valide.
4. **Scope sauber, kein Beifang:** Commit `588283d` = exakt 6 Dateien, 737 Insertions; `szene.ts`
   (out-of-scope, uncommittet) ist **nicht** enthalten.

## Achsen

- **Richtigkeit:** Port faithful (Identität belegt), Gate grün. ✓
- **Bauordnung:** eine Wahrheit gewahrt (kein zweiter Store/Schema-Ort), additiv, keine Live-Regression. ✓
- **Grundvoraussetzungen:** keine destruktiven Ops, kein Push/Merge/Deploy, Rollentrennung eingehalten. ✓

## Votum: **GRÜN**

W-1 ist abgenommen. Nicht-blockierende Notiz: `dachformVorlagen` + die Aufbauten-/Holz-/Linien-Module
folgen in W-2..W-4, sobald ihre Abhängigkeiten (`polygonFlaeche`, `aufbauPlatzierung`, `linienBauteile`)
portiert sind. Die eigentliche 3D-Verdrahtung + Schema-Konsolidierung (RoofShape L/T/U in die persistierte
Szene) bleibt bewusst W-3 — dort wird der `pruefeRechteckigeKontur`-Wurf geöffnet.
