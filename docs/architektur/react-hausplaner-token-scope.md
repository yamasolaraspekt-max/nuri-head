# Entscheidung — Token-Scope der React-Hausplaner-Insel (Planner, 2026-07-23)

## Frage (Evaluator-Befund Batch 0)
`FaehigkeitenNavi.tsx` nutzt hartkodierten Hex (`#1f2937`, `#93c21c`, `#3f6212`, `rgba(147,194,28,…)`).
CLAUDE.md/`ui-bauordnung.md`: „Farbwerte nur über sa-ui-Tokens, kein Hex in Views außer Token-Dateien".
Ist die React-Insel ein Ausnahme-Scope oder muss sie auf `--sa-*` tokenisiert werden?

## Entscheidung (verbindlich)
Der Hausplaner ist eine **eigenständige React-SPA** (eigener Build `vite.hausplaner.config.ts`), in eine
Blade-Schale (`admin/hausplaner/studio.blade.php`) montiert. Er ist ein **deklarierter Ausnahme-Scope** mit
**einer** eigenen Token-Wahrheit: **`T` in `resources/planner/hausplaner/app/studioDaten.ts`** (die
v9-Synthese). `studioDaten.ts` ist damit die **Token-Datei** der Insel (das Analogon zur Blade-Token-Datei),
und die **einzige** Stelle, an der in der React-Insel Hex stehen darf.

Regel für die Insel (deckt die CLAUDE.md-Intention „eine Token-Wahrheit, kein verstreuter Hex" ab):
- **Jede** React-Datei außer `studioDaten.ts` referenziert nur `T.*` — **kein** hartkodierter Hex, **kein**
  loses `--sa-*`.
- `#93c21c`/`#1f2937` in `FaehigkeitenNavi.tsx` sind doppelt falsch: hartkodiert **und** off-palette
  (Marken-Grün ist v9 `T.brand = #7fae1c`, nicht das Blade-`--sa-accent #93c21c`).

## Backlog (nicht dieser Slice)
Ob das Marken-Grün über **beide** Inseln vereinheitlicht wird (v9 `#7fae1c` vs. Blade `--sa-accent #93c21c`)
ist eine eigene, größere CI-Frage — später als eigener Vorgang, nicht in einem Feature-Slice.
