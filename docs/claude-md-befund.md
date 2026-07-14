# Befund — uncommittete CLAUDE.md-Drift (Rang-2, Yama-Entscheidung)

**Stand:** 2026-07-14 · **read-only Befund.** `CLAUDE.md` ist Rang-2-Autorität (nur Yama ändert) und liegt **außerhalb** der delegierten Rolle. Diese Datei ist der schriftliche Befund; `CLAUDE.md` selbst bleibt **unangetastet** und wird in **keinem** path-scoped Commit mitgeführt.

## Stand
`git diff --stat CLAUDE.md` → **1 file changed, 12 insertions(+)**, 0 Löschungen — rein **additiv**. Datiert **2026-07-10 / 2026-07-11**, also **nicht** in den WP-/G0-Arbeiten dieser Session (14.07.) erzeugt → vorbestehende lokale Drift im Arbeitsbaum.

## Wörtlicher Diff (12 hinzugefügte Zeilen = 6 Governance-Direktiven + je Leerzeile)

```diff
+> **⛔ CLAUDE-CODE-STARTPFLICHT: Agenten sofort aktivieren (dauerhaft, ab 2026-07-10).** Bei JEDER neuen Claude-Code-Arbeit in diesem Repository gilt automatisch [`docs/agents/04-claude-code-startanweisung.md`]… Der Standardmodus ist **Planner zuerst**…
+
+> **🎯 PFLICHT-FACHAGENTEN für Produkt/Frontend/Workflow/Architektur (dauerhaft, ab 2026-07-10).** Bei Aufgaben zu Frontend, Design, Layout, UX, Wizard, App-Konzept, Workflow, Prozess oder Architektur gilt zusätzlich [`docs/agents/05-fachagenten-produkt-architektur-frontend.md`]…
+
+> **🧭 ARBEITSKOMPASS / FAHRPLAN-PFLICHT (dauerhaft, ab 2026-07-10).** Bei jeder größeren Aufgabe zuerst [`docs/arbeitskompass-ticket.md`] prüfen…
+
+> **⛔ SYSTEMWEITE OPTIMIERUNGS-REIHENFOLGE (dauerhaft, ab 2026-07-11).** ① konzeptionell optimieren → ② Workflow bestimmen → ③ vorhandene Bausteine verknüpfen → ④ erst dann automatisieren…
+
+> **📘 KAPITEL-FAHRPLAN FÜR OPTIMIERUNGEN:** Die konkrete Schrittfolge steht in [`docs/systemoptimierung-fahrplan.md`]…
+
+> **⛔ KAPITEL-STARTBLOCK-PFLICHT:** Vor JEDEM Kapitel aus `docs/systemoptimierung-fahrplan.md` muss Claude Code zuerst einen Startblock liefern…
```

*(Blockinhalte gekürzt dargestellt; der vollständige Wortlaut steht im Arbeitsbaum-`CLAUDE.md`. Diff ist rein additiv, 12 Zeilen.)*

## Bewertung
- Inhaltlich **ohne Bezug** zur WP-Stufe-3a-/G0-Arbeit; reine Governance-Regeln, additiv, keine Löschung.
- **Nicht** in der delegierten Yama-Rolle änderbar (Rang 2). Daher: `CLAUDE.md` bleibt uncommitted im Arbeitsbaum; **Yama entscheidet** über einen separaten Governance-Commit.
- Alle Arbeits-Commits dieser Session (`865e230`, `6a45985`, WP Stufe 3a) sind **path-scoped ohne CLAUDE.md**.
