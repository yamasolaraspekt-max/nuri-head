# Subagent-Matrix

| Subagent | Rolle | Tools | Verändert Code? |
| --- | --- | --- | --- |
| ticket-reuse-reviewer | Reuse ausreichend gesucht/genutzt? Doppelbau/2. Designsystem? | Glob, Grep, Read | nein |
| planner-architect | Schichten-Einordnung, Reuse-vs-Neu | Glob, Grep, Read | nein |
| security-reviewer | Rechte/Org/Projekt/Upload/additiv-DB | Glob, Grep, Read, Bash(lesend) | nein |
| test-reviewer | Regressions-/Testabdeckung | Glob, Grep, Read, Bash(lesend) | nein |

Einsatz vor jeder Abnahme; Urteil grün/rot mit Beleg. Ohne Reuse-Matrix → ticket-reuse-reviewer ROT.
