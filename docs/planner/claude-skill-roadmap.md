# Skill-Roadmap / Status

## Fertig (in dieser Lieferung)
- CLAUDE.md-Regel „Existing-Code-First und Ticket-Wiederverwendung".
- Skills: ticket-code-reuse (+5 Referenzen), planner-slice-orchestrator, planner-repository-audit,
  planner-architecture, building-document, laravel-planner-integration, planner-security-review,
  planner-verification.
- Subagenten: ticket-reuse-reviewer, planner-architect, security-reviewer, test-reviewer.
- Docs: diese acht Dateien.

## Offen (braucht Mac-Datei-/Shell-Brücke)
- **Vollständiges** reales Repo-Inventar (Bootstrap Phase 2–4): alle Routen/Controller/Services/
  Views/Tests → `ticket-code-inventory.md` erweitern, `ticket-reuse-matrix.md` mit echten Zeilen füllen.
- Verifikation der „zu verifizierenden" Bereiche (Aufgaben, Kommentare, Aktivitäten,
  Benachrichtigungen, generische Tabellen/Filter, Testmuster) mit realen Pfaden.
- Einbau ins Repo (`.claude/`, `docs/planner/`, CLAUDE.md-Abschnitt) + settings/hooks, sobald die
  Brücke steht.

## Nächster fachlicher Anwendungsfall
Der Hausplaner-UI-Umbau läuft künftig durch ticket-code-reuse: `x-page-head`, `--sa-`-Tokens,
Sidebar-/Button-Muster wiederverwenden statt greenfield — behebt CI-/Konsistenzproblem an der Wurzel.
