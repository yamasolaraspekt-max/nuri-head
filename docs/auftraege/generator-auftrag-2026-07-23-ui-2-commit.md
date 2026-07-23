# Generator-Auftrag — UI-2 festschreiben (2 Commits)

**Rolle:** Generator (nur). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.

**Vorbedingung (erfüllt):** Evaluator hat UI-2 (Tool-Registry + Activation-Engine + geteilter
UI-State) am 2026-07-23 mit **FREIGABE** abgenommen — Belege: `tsc:hausplaner` Exit 0,
`schema:hausplaner:check` Exit 0, `test:hausplaner` 306/306, die 3 neuen Testdateien isoliert 19/19,
Gegen-Beweis „eine Wahrheit" per grep (altes lokales `useState` fürs aktive Werkzeug in
`HausplanerApp.tsx:141-142` entfernt, UI-State ist einzige Quelle).

**Auftrag:** Den bereits umgesetzten, abgenommenen Arbeitsstand in **zwei getrennten Commits**
festschreiben — **auf Yamas ausdrückliches Wort** (Commit ist kein Selbstläufer; kein Push ohne Wort).

## Schritt 1 — Code-Slice (ein Commit)
Enthält: `app/tools/{toolTypes.ts, toolRegistry.ts, …}`, `app/state/uiState.ts`, die 3
`__tests__`-Dateien, und die Verdrahtung in `app/HausplanerApp.tsx` (lokaler Werkzeug-`useState`
entfernt → Bezug aus `uiState`). **Nicht** in diesem Commit: der gebaute `public/…js`-Bundle.

Vor dem Commit lokal selbst verifizieren (nicht dem Gedächtnis vertrauen):
`npm run tsc:hausplaner` (0) · `npm run schema:hausplaner:check` (0) · `npm run test:hausplaner` (306/306).

Commit-Message (Vorschlag): `UI-2: Tool-Registry + Activation-Engine + geteilter UI-State (eine Wahrheit fürs aktive Werkzeug)`

## Schritt 2 — Bundle-Rebuild (eigener Commit)
`public/…js` über das **Gate** bauen: `npm run build:hausplaner` (**nicht** `npx vite build`).
ARM-Rollup-Optional-Dep-Bug → im x64-Container bauen; der dort gebaute Bundle ist kanonisch.
Getrennt halten, damit Code-Review und Bundle-Diff sich nicht vermischen.

Commit-Message (Vorschlag): `UI-2: Hausplaner-Bundle-Rebuild (Tool-Registry/UI-State)`

## Evaluator-Notizen als Mitgabe (nicht blockierend, NICHT in diesen Commits nachbessern)
1. **Bericht-Genauigkeit:** frühere „5 Zeilen" waren real wenige Code- + 3 Kommentarzeilen. Nur Präzision.
2. **`activeToolId: string` Grenz-Cast** (`HausplanerApp.tsx:141`): sicher, weil Setter typisiert;
   darf **später** enger gefasst werden (Union statt `string`). Keine Änderung jetzt.
3. **Store-Remount:** globaler Store setzt bei Remount `activeWorkspace` mit zurück → 1-Frame-Unterschied
   bei künftigem Remount von `HausplanerApp`. **Planner-Entscheidung:** akzeptabel so, kein Nachbessern;
   falls Workspace-Persistenz über Remount gewünscht wird, ist das ein **eigener UI-3-Punkt**. Keine Änderung jetzt.

## Grenzen
Nur `ticket`. Nur diese zwei Commits, kein Scope-Zuwachs. Kein Push ohne Yamas Wort. Danach Meldung
**„umgesetzt"** an Planner (mit den Exit-Codes) und Ledger aktualisieren → Ball zurück zum Planner,
der W-1 scharfschaltet.
