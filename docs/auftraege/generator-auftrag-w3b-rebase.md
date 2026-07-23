# Generator-Auftrag — W-3b auf gefixtes W-3a rebasen (Block korrekt geräumt)

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.

## Governance-Klärung (wichtig)
W-3b (`57e1913`) wurde auf der **Vor-Fix-Basis `b7f83f0`** gebaut und ist damit über den offenen
M1-Block gerückt — ein Prozessverstoß, den der Evaluator zu Recht angesagt hat. **Der Block wird NICHT
per Zuruf aufgehoben.** Er ist inzwischen **korrekt geräumt**: W-3a-fix (`0c33755`) hat M1 gefixt
(geteilte Quelle `dachRoh`, Verriegelungs-Test) — Evaluator-FREIGABE + Live-Gate 624/624 (tsc0) bestätigt.
Deshalb ist die saubere Korrektur: **W-3b auf `0c33755` neu aufsetzen.** W-3b-Fachinhalt bleibt vollständig
erhalten (kein Wegwerfen).

## Ziel
Die guten W-3b-Bausteine **behalten** und auf den gefixten Stand verpflanzen:
- `domain/roofShape.ts` (eine `RoofShape`-Wahrheit), additive Enum-Erweiterung (rect/l/t/u),
- der **B1-Compile-Beweis** (`type _… = EngineRoofShape extends RoofShape ? true : never`),
- der Kontur-Guard `istVerschneidungsForm(...)` (render-neutral leer statt Crash), Schema-Regen.

## Schritte
1. **Rebase** der W-3b-Commits von der alten Basis auf den Fix:
   `git rebase --onto auto/hausplaner-w3a b7f83f0 auto/hausplaner-w3b`
   (verpflanzt `57e1913` & Folgende auf `0c33755`). Alternativ Merge, falls für euch sauberer — Ergebnis zählt.
2. **Konflikt in `dachMesh.ts` bewusst auflösen — und dabei besser machen:** die W-3b-Guards sollen NICHT
   mehr in **beide** Funktionen (die es nach dem Fix nicht mehr doppelt gibt), sondern **an die EINE
   geteilte Quelle** `dachRoh()`. Der `istVerschneidungsForm`-Guard gehört **einmal** in `dachRoh()` —
   dann wirkt er automatisch für `dachMeshWelt` (Triangulierung) und `dachflaechen` (Filter). Die
   Doppelpflege, die M1 erzeugte, **entfällt** damit dank SSOT (der Guard wird sogar billiger als in W-3b).
3. **Nicht aufweichen:** der Verriegelungs-Test (`dachflaechen`-Ecken auf `dachMeshWelt`-Fläche) bleibt grün;
   der B1-Compile-Beweis bleibt; `dachRoh` bleibt einzige Herleitung (`const rad =` weiterhin genau 1×).
4. **Schema:** falls die Enum-Erweiterung berührt wird, `npm run schema:hausplaner` und das regenerierte
   `scene-document-v2.schema.json` **mitcommitten** (additiv/optional — Bestands-4-Formen gültig, kein 422).

## Gate (Generator selbst, am w3b-Tip)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 · `test:hausplaner` (≥ 624 + W-3b-Tests) · `build:hausplaner` 0.
Testzahl selbst am Tip belegen.

## Guardrails
Additiv; portierte `geometry/*`-Module unverändert (nur `renderers/three-d/dachMesh.ts` + `domain/*`);
SSOT `dachRoh` + B1-Compile-Beweis + Verriegelungs-Test **nicht** aufweichen; kein Beifang
(`git reset -q HEAD -- .`, gezielt adden); nur `auto/`-Branch, **KEIN main-Merge/Push/Deploy** ohne Yamas Wort.
Meldung „umgesetzt" (4 Exit-Codes) → **zurück an den Evaluator**, Pflicht-Stopp.

## Abnahmekriterien (Evaluator)
1. W-3b sitzt auf `0c33755` (Basis = gefixtes W-3a).
2. `dachMesh.ts`: der L/T/U-Guard steht **an einer Stelle** (`dachRoh`), keine Doppelpflege; `const rad =` genau 1×.
3. Verriegelungs-Test grün; B1-Compile-Beweis vorhanden; `RoofShape` eine Wahrheit.
4. `l-shape` validiert additiv (kein 422); Bestands-4-Formen unverändert gültig.
5. Volle Suite selbst am Tip gemessen; kein Beifang; `geometry/*` unberührt.

## Hinweis Stufe 2 (später, nicht dieser Auftrag)
Das eigentliche **L/T/U-Rendering** (Kehl-/Gratlinien aus `dachVerschneidung`/`dachUForm`) und der
**Vorlagen-Picker** (187 `dachformVorlagen`) bleiben Stufe 2 von W-3b — erst nach grüner Re-Abnahme dieses
Rebase-Stands, auf dein „go".
