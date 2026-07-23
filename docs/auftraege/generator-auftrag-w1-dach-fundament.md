# Generator-Auftrag — Welle W-1 „Dach-Fundament" (reine Logik + Schema, kein 3D-Render)

**Rolle:** Generator (nur). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.

**Sperre (Vorbedingung):** Beginne **erst**, wenn der UI-2-Commit steht (sauberer Baum) — siehe
`generator-auftrag-2026-07-23-ui-2-commit.md`. Der Schema-Gleichstand ist bereits belegt
(`schema:hausplaner:check` Exit 0 im UI-2-Lauf), daher **keine** separate S0-Zeremonie mehr nötig.

**Ziel & Entscheidung:** Übernimm die gereifte, framework-freie Dach-Logik aus dem Playground
(„Dach-&-PV-Planer", `src/utils/`) als reine TS-Module in den ticket-Hausplaner und verankere die
erweiterten Dach-Typen an **einer** Modellwahrheit. **Kein 3D-Rendering in dieser Welle** (das ist W-3).
Reihenfolge: **erst Typen/Schema, dann Logik** (Lehre aus `970f0cc`: Zod ohne Schema-Regen = 422/ROT).

## Umsetzung (genau diese vier Punkte)
1. **Typen + Schema zuerst (Consolidate).** `RoofShape` um `'l-shape' | 't-shape'` erweitern;
   `ObstacleType` (chimney/window/vent/sat/lichtkuppel + schleppgaube/trapezgaube/flachgaube/
   giebelgaube/spitzgaube) aufnehmen. Vorlage: `Playground/src/stores/roofTypes.ts`. Einbau in
   `domain/scene.types.ts` **und** Zod-`validation.ts`. **Kein** zweiter Store, **kein** Import von
   `roofConfigStore.ts` — einzige Modellwahrheit bleibt `hausplanerStore.ts`. Danach zwingend
   `npm run schema:hausplaner` und das regenerierte `scene-document-v2.schema.json` einchecken. Neue
   Felder **additiv/optional** (Bestands-Szenen bleiben valide).
2. **`dachWerte` (Reuse).** Port `src/utils/dachWerte.ts` → `geometry/dachWerte.ts` + Test aus
   `src/services/__tests__/dachWerte.test.ts`. Unverändert, nur Importpfade.
3. **`dachformVorlagen` L/T/U (Adapt).** Port `src/utils/dachformVorlagen.ts` →
   `geometry/dachformVorlagen.ts` + Test. An die ticket-Typen (Punkt 1) anpassen. Abnahme: je Form
   entsteht eine **gültige Kontur**.
4. **Verschneidung (Reuse).** Port `src/utils/dachVerschneidung.ts` (Kehl-/Gratlinien L/T, SSOT +
   Regressionsschloss) → `geometry/dachVerschneidung.ts` **und** `src/utils/dachUForm.ts` →
   `geometry/dachUForm.ts`, jeweils mit Tests. Eingefrorene Fixtures **unverändert** mitnehmen.

**Bewusst NICHT in W-1:** Gauben-Geometrie, Dachöffnungen/Aufbauten, Holz-/Sparren-Stückliste,
three.js-Anschluss, Material/PV. Kommt W-2..W-5.

## Bauordnung
Eine Modellwahrheit (`hausplanerStore.ts`, typed Command + Immer inverse-patch); 2D/3D read-only.
Schema zuerst regenerieren + einchecken. Build über Gate `npm run build:hausplaner`. Den bestehenden
`pruefeRechteckigeKontur`-Wurf in `dachGeometrie.ts`/`dachMeshWelt` in W-1 **nicht** aufbrechen (das
gehört zu W-3), sonst wirft der 3D-Kern bei L/T/U.

## Kantenliste
Typ-Dublette RoofShape/ObstacleType (→ ein Ort); Schema-Desync (→ regenerieren); nicht-rechteckige
Kontur (Wurf bleibt in W-1 unangetastet); eingefrorene Fixtures nicht verändern.

## Meldung am Ende
„umgesetzt" + geänderte/neue Dateien + hinzugefügte/portierte Tests + Bestätigung
`schema:hausplaner:check` Exit 0. **Kein** Selbst-„grün". Ledger aktualisieren → Ball an Evaluator.
