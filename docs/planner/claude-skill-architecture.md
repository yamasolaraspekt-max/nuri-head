# Claude-Skill-Architektur (Planner)

## Prinzip
Existing-Code-First: der Ticket-Bestand ist Fundament. Neu gebaut wird nur CAD-Spezifik.
Rollen-Trennung: Planner (Spec) → Generator (Bau, additiv) → Evaluator (unabhängige Abnahme).

## Skills
- `planner-slice-orchestrator` — Einstieg jedes Slices; erzwingt Reuse-Gate vor Neubau.
- `ticket-code-reuse` — Reuse-Prüfung + Matrix (R1–R5). Pflicht vor Produktivcode.
- `planner-repository-audit` — read-only Inventar (Planner + Ticket).
- `planner-architecture` — Zielbild + Schichten-Einordnung.
- `building-document` — versioniertes SceneDocument (mm, Revision/Checksum/409, additiv).
- `laravel-planner-integration` — Insel-Mount, Web-Routen/Rechte, isolierter Build.
- `planner-security-review` — Rechte/Org/Projekt/Upload/additiv-DB.
- `planner-verification` — Evaluator-Abnahme inkl. Reuse-Gate.

## Subagenten
- `ticket-reuse-reviewer`, `planner-architect`, `security-reviewer`, `test-reviewer` (alle read-only).

## Ablauf eines Slices
1. Orchestrator: Bedarf. 2. ticket-code-reuse: Matrix. 3. R1/R2/R4/R3 entscheiden.
4. Nur Fehlendes (CAD) neu (building-document / laravel-planner-integration).
5. Generator baut additiv. 6. Reviewer (reuse/security/test) + planner-verification: Abnahme.
