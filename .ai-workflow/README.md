# Kontrollierter KI-Workflow für `ticket`

Dieser Ordner definiert einen prüfbaren Vier-Rollen-Prozess für Änderungen am Repository. Er verändert keine Produktlogik und ersetzt keine fachliche Freigabe.

## Erkannter Projektbestand

- Laravel 11 / PHP ab 8.2, PHPUnit und Laravel Pint
- React 19, Vue 3, TypeScript 5.8 und Vite 6
- vorhandene Hausplaner-Gates: `test:hausplaner`, `test:hausplaner:dom`, `tsc:hausplaner`, `schema:hausplaner:check`
- keine eingecheckte CI-Konfiguration unter `.github/`
- mehrere bereits vorhandene Worktrees; diese Workflow-Skripte greifen sie nicht an

## Rollen und Grenzen

1. **Planner** untersucht Bestand und Risiken und schreibt eine ausführbare Spezifikation. Er ändert keinen Produktivcode.
2. **Generator** implementiert ausschließlich die freigegebene Spezifikation und dokumentiert jeden Akzeptanzpunkt.
3. **Evaluator** prüft unabhängig, sucht Gegenbeispiele und ändert keinen Produktivcode.
4. **Repair-Generator** behebt ausschließlich bestätigte Findings. Neue Funktionalität erfordert eine neue Planung.

Die verbindlichen Einzelregeln stehen unter [`roles/`](roles/).

## Sicherheitsregeln

1. Niemals mehrere schreibende Claude-Code-Sessions im selben Worktree.
2. Der Planner ändert keinen Produktionscode.
3. Der Evaluator repariert keinen Produktionscode.
4. Der Generator entscheidet keine offenen Anforderungen selbst.
5. Änderungen werden nur über Commits zwischen den Rollen übergeben.
6. Uncommittete Änderungen gelten nicht als offizielle Übergabe.
7. Der Evaluator prüft einen konkreten Commit.
8. Chat-Nachrichten außerhalb der versionierten Task-Dateien gelten nicht als verbindliche Spezifikationsänderung.
9. Jede Scope-Erweiterung muss vom Planner in der Spezifikation nachgetragen werden.
10. Bei P0- oder P1-Befunden ist ein GREEN-Votum verboten.
11. Fehlende Belege gelten als nicht geprüft.
12. Tests des Generators ersetzen keine eigenen Gegenbeweise des Evaluators.

Zusätzlich gelten die Repository-Schutzregeln: Vor jeder Arbeit Branch, `HEAD` und Arbeitsbaum prüfen; fremde Änderungen nicht bereinigen; nie `git add -A`; Commit und Push getrennt freigeben; kein Force-Push, Hard-Reset, pauschales Lock-Löschen oder `rm -rf` für Worktrees.

## Branches und Worktrees

Für `TASK-123` werden neben dem Repository vier Verzeichnisse geplant:

| Rolle | Branch | Worktree |
|---|---|---|
| Planner | `planning/TASK-123` | `../ticket-planner-TASK-123` |
| Generator | `task/TASK-123` | `../ticket-generator-TASK-123` |
| Evaluator | `evaluation/TASK-123` | `../ticket-evaluator-TASK-123` |
| Repair | `repair/TASK-123` | `../ticket-repair-TASK-123` |

Alle Branches beginnen am beim Erstellen erkannten Basisstand. Spätere Übergaben werden als explizite Commits übernommen, beispielsweise per gezieltem `git cherry-pick <hash>` oder einem überprüften Merge. Die Skripte führen diese Übergaben nicht automatisch aus.

Vorschau:

```bash
.ai-workflow/scripts/create-task-worktrees.sh --dry-run TASK-123
```

Erstellen:

```bash
.ai-workflow/scripts/create-task-worktrees.sh TASK-123
```

Öffnen in VS Code:

```bash
code ../ticket-planner-TASK-123
code ../ticket-generator-TASK-123
code ../ticket-evaluator-TASK-123
code ../ticket-repair-TASK-123
```

Die Verzeichnisse sind getrennt, gehören aber zum selben Git-Repository. Ein lokaler, nicht eingecheckter Rollenmarker verhindert versehentliche Rollenverwechslungen.

## Ablauf

1. Task-ID festlegen.
2. Planner erstellt Inventar und Spezifikation.
3. Planner validiert seine Übergabe.
4. Generator liest ausschließlich die freigegebene Spezifikation.
5. Generator implementiert und testet.
6. Generator erstellt nach Freigabe einen Commit und Implementierungsbericht.
7. Generator validiert seine Übergabe.
8. Evaluator übernimmt den konkreten Generator-Commit.
9. Evaluator rekonstruiert zunächst selbst das Soll.
10. Evaluator prüft alle Kriterien und entwickelt eigene Gegenbeweise.
11. Evaluator erstellt Einzelvotum und Gesamtvotum.
12. Bei RED gehen die Befunde an den Planner.
13. Planner bestätigt oder präzisiert den Reparaturscope.
14. Repair-Generator behebt ausschließlich bestätigte Befunde.
15. Evaluator prüft Reparatur und Gesamtregression erneut.
16. Merge erst bei belastbarem GREEN.

Vor Schritt 2 werden Repository-Status und `HEAD` festgehalten, der Worktree-Plan per `--dry-run` geprüft und die getrennten Rollenverzeichnisse eingerichtet.

## Task-Artefakte

Jeder Task liegt unter `.ai-workflow/tasks/<TASK-ID>/`:

- `00-request.md`
- `01-inventory.md`
- `02-planner-spec.md`
- `03-generator-report.md`
- `04-evaluator-report.md`
- optional `findings/EV-001.md`
- Quality-Gate-Logs mit Zeitstempel

Vorlagen liegen unter [`templates/`](templates/). Übergaben werden geprüft mit:

```bash
.ai-workflow/scripts/validate-task-handover.sh TASK-123 planner
.ai-workflow/scripts/validate-task-handover.sh TASK-123 generator
.ai-workflow/scripts/validate-task-handover.sh TASK-123 evaluator
```

## Quality Gates

Eine Vorschau der im aktuellen Checkout verfügbaren Gates:

```bash
.ai-workflow/scripts/run-quality-gates.sh --dry-run TASK-123
```

Die echte Ausführung protokolliert Befehle, Ergebnisse und übersprungene Gates. Artefakt-Builds, die eingecheckte Bundles verändern können, laufen nur mit:

```bash
AI_WORKFLOW_INCLUDE_ARTIFACT_BUILDS=1 .ai-workflow/scripts/run-quality-gates.sh TASK-123
```

## Kontrolliertes Entfernen

```bash
.ai-workflow/scripts/remove-task-worktrees.sh --dry-run TASK-123
.ai-workflow/scripts/remove-task-worktrees.sh TASK-123
```

Das Skript bricht bei Änderungen in einem Ziel-Worktree ab, löscht keine Branches und führt `git worktree prune` nur mit der zusätzlichen Option `--prune` aus.
