# Generator-Auftrag — Sicherungs-Push (lokaler Hausplaner-Stand → Remote)

**Rolle:** Generator (Claude Code in VS Code, native git). **Heimat-App:** `ticket`.
**Ausgestellt von:** Planner (Cowork), 2026-07-23. **Dringlichkeit: HOCH** — der Stand lebt nur lokal.

## Warum
Der komplette neueste Hausplaner-Fortschritt (UI-2 + W-1 + W-2 + UI-3a + UI-3b + Decken, Integrations-Tip
`auto/hausplaner-ui-3b` @ `590700c`) liegt **auf keinem Remote** — nur lokal. Einziger Zweck dieses
Auftrags: diesen Stand **sichern**. **KEIN Merge, KEIN main, KEIN force, KEIN Push zu origin.**

## Fakten (gemessen, read-only)
- Kanonisches Ziel-Repo: **`fork` = github.com/yamasolaraspekt-max/nuri-head** (hat schon die Hauptlinie).
- `590700c` ist 4 Commits vor `fork/auto/hausplaner-ui-3a`; der 1 Commit, den fork zusätzlich hat
  (`f3e38d6`), ist die **alte Beifang-Version von UI-3a** und wird von der lokalen sauberen Aufteilung
  (`6569cc1`) ersetzt — nicht anfassen, kein Verlust.
- Alle Wellen-Branches (ui-2/w1/w2/ui-3a) sind Vorfahren von `590700c` → ein Push von `auto/hausplaner-ui-3b`
  sichert ihre Commits mit.

## Schritte
1. Lokal prüfen: `git rev-parse --short auto/hausplaner-ui-3b` → muss `590700c` sein.
   Arbeitsbaum sauber halten (`git status`); die 2 offenen Doc-Dateien vorher committen ODER stashen
   (kein Beifang in den Push).
2. **Sicherungs-Push** (neuer Remote-Branch, berührt nichts Bestehendes):
   `git push fork auto/hausplaner-ui-3b`
3. Verifizieren: `git ls-remote fork auto/hausplaner-ui-3b` zeigt `590700c...`.
4. Zur Vollständigkeit optional die einzelnen Wellen-Branches mitsichern (jeweils ohne force):
   `git push fork auto/hausplaner-ui-2 auto/hausplaner-w1 auto/hausplaner-w2 auto/hausplaner-ui-3a`
   (nur falls lokal vorhanden; reine Zusatz-Sicherung, kein Muss).

## Verboten (harte Guardrails)
- **KEIN** `--force` / `--force-with-lease` (nichts überschreiben).
- **KEIN** Push nach `origin` (raminsadid2021) — das Trunk-Ziel ist noch offen (Yama entscheidet separat).
- **KEIN** Push/Merge nach `main` oder `fork/auto/hausplaner-ui-3a`.
- **`nurihead` (backup-private) NICHT anfassen/löschen** — dort liegen die einzigen Kopien von
  `strang/accounting` (`28b074b`) und `strang/formulare` (`f796b42`); die werden in einem SEPARATEN
  Konsolidierungs-Schritt gesichert, nicht hier.

## Abnahme (Evaluator in VS Code)
1. `git ls-remote fork auto/hausplaner-ui-3b` == `590700c` (Push angekommen).
2. `git log -1 fork/main` unverändert (Initial-Commit) — main nicht berührt.
3. `fork/auto/hausplaner-ui-3a` unverändert (`f3e38d6` noch da) — kein force, nichts überschrieben.
4. `origin` unverändert — kein Push zu raminsadid2021.

## Danach (Planner, separat)
Nach erfolgreicher Sicherung: Konsolidierungs-Auftrag (die 2 einzigartigen Stränge accounting/formulare
ins kanonische Repo holen) + die Trunk-Frage (main-Ziel; hängt an „wer ist raminsadid2021").
