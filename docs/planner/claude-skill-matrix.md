# Skill-Matrix — welcher Skill für welchen Bedarf

| Slice-Bedarf | Zuerst | Dann |
| --- | --- | --- |
| Beliebiger Planner-Slice | planner-slice-orchestrator | ticket-code-reuse |
| Bestand verstehen | planner-repository-audit | planner-architecture |
| Neues Datenmodell / Persistenz | ticket-code-reuse | building-document |
| Insel ins CRM einbinden | ticket-code-reuse | laravel-planner-integration |
| UI/Oberfläche | ticket-code-reuse (Designsystem!) | laravel-planner-integration |
| Rechte/Upload/Org | ticket-code-reuse | planner-security-review |
| Abnahme | planner-verification | ticket-reuse-reviewer + security/test-reviewer |

Regel: UI-Bedarf startet IMMER mit ticket-code-reuse (Styleguide/`--sa-`/`x-page-head`/Sidebar),
nie mit einer neuen Komponente.
