# Generator-Auftrag — Batch-0-Fix: Tokenisierung (T) + echter Guard-Test + Cleanup

**Rolle:** Generator (Claude Code in VS Code). **Heimat-App:** `ticket`. **Ausgestellt von:** Planner, 2026-07-23.
**Basis:** `auto/hausplaner-navi-batch0` @ `c553fbc`. **Grund:** Evaluator-Logik-FREIGABE steht; drei UI-Punkte
offen. Dieser Fix schließt die tokenisier- und test-beweisbaren, **bevor** die Browser-Runde läuft (sonst
prüft der Evaluator eine Optik, die sich gleich ändert).

## Planner-Entscheidung (Grundlage: `docs/architektur/react-hausplaner-token-scope.md`)
React-Hausplaner = Ausnahme-Scope, Token-Wahrheit = **`T`** (`studioDaten.ts`). Jede React-Datei außer
`studioDaten.ts` referenziert **nur `T.*`**, kein Hex.

## Arbeitspakete
1. **Tokenisieren `FaehigkeitenNavi.tsx`:** alle hartkodierten Hex durch `T.*` ersetzen —
   Text/Ink → `T.ink/T.muted/T.faint`; **Marken-Grün/„aktiv" → `T.brand` (NICHT `#93c21c`)**; Zustände
   → `T.ok/T.warn/T.err` (grün/amber/rot); Flächen/Linien → `T.surface/T.surface2/T.bg/T.hair`.
   **Beweisziel:** `grep -nE "#[0-9a-fA-F]{3,6}|rgba\(" resources/planner/hausplaner/app/FaehigkeitenNavi.tsx`
   → **0 Treffer** (Hex lebt nur in `studioDaten.ts`).
2. **Echter Guard-Test (AP-E):** der bestehende Test prüft `engineModul` nur auf Präfix `geometry/`. Ersetzen/
   ergänzen durch einen Test, der **jedes** `engineModul` der Registry **dynamisch importiert** (`await import`)
   und den **in der Registry deklarierten Export** prüft (Export ≠ Modulname, z. B. `pvBelegung` →
   `pvSchnellBelegung`). Rot, sobald Modul **oder** dieser Export fehlt → verriegelt die „echte Engines"-Zusage
   per Beweis (heute nur report-belegt).
3. **Cleanup:** ungenutzte `fachIcon()` entfernen (tsc unused-sauber).

## Gate (Generator selbst)
`tsc:hausplaner` 0 · `schema:hausplaner:check` 0 · `test:hausplaner` (≥ vorher + Guard-Test verriegelt) ·
`build:hausplaner` (nativ/x64).

## Abnahme (Evaluator)
1. **0 Hex** in `FaehigkeitenNavi.tsx` (grep-Beweis); nur `T.*`; Marken-Grün == `T.brand` (nicht `#93c21c`).
2. **Guard-Test importiert real**; Gegenbeweis: ein `engineModul`/Export verfälschen ⇒ Test **rot**.
3. `fachIcon()` weg; tsc unused-sauber.
4. **A11y-Zusage bleibt:** Zustand als **Farbe UND Text** (ZustandBadge unverändert in der Aussage).
5. **Keine Regression** der +7 Verhaltens-Tests; eine Registry, keine zweite Werkzeug-Wahrheit.
6. Additiv, nur `auto/hausplaner-navi-batch0`, **kein Push/Merge**.

## Danach
Neuer Tip → Evaluator liest den Fix (Logik) → **dann** nativer Build + Browser-Runde (3 Viewports +
vier Fachagenten) am neuen Tip (siehe `evaluator-auftrag-faehigkeiten-navi-optik.md` + `…-sicht-runde-bauen.md`).
Meldung „umgesetzt" (4 Exit-Codes + grep-0-Beleg) → Evaluator, Pflicht-Stopp.
