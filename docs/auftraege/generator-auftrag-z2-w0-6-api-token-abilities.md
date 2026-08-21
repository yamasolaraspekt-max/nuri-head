# Z2-W0-6 · Nuriva-API: die vier Token-Abilities werden durchgesetzt — oder sie verschwinden

```yaml
zustand: ENTWURF
welle: 0 (Sicherheit, niedrigere Dringlichkeit als W0-5; läuft NACH W0-5)
basis_sha: cb500067
herkunft: Befund A-5 (docs/backlog/inventur-2026-08-21-z2-folge.md)
spur: A — Autorisierung
entscheidung_dirigent: DURCHSETZEN (least privilege); alle heute vergebenen Tokens tragen alle vier Abilities (routes/api.php:75-80), also bricht nichts
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
```

## Ziel
Lese-Routen hinter `ability:planner:read`, Schreib-Routen hinter `ability:planner:write`,
Attendance-nahe hinter `ability:planner:attendance`, Kanban hinter `ability:planner:kanban` —
Code und Token-Zusage stimmen überein.

## Ist-Beleg
`PlannerApiAuthController@token` vergibt vier Abilities (`routes/api.php:75-80`); `grep tokenCan|
ability:|abilities` → 0 Treffer in Routen, Planner-Controllern, Middleware; Gruppe nur `auth:sanctum` (`:253`).

## Scope · Dateien
`routes/api.php` (Untergruppen mit `ability:`-Middleware), ggf. Kernel-Alias für Sanctum
`CheckAbilities`/`CheckForAnyAbility`; Test: Token mit nur `planner:read` → Schreib-Route 403,
Lese-Route 200; Volltoken → alles 200. **Nur `ticket_testing`.**
**Nicht-Ziele:** keine Änderung der Ability-Vergabe; keine Ownership-Logik (W0-5); keine
Antwortformate.

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: read-only-Token → Schreib-Route 403, Lese-Route 200 | Gate | *n.U.* | Testnamen |
| B: Vollton (vier Abilities) → alle bisher grünen Contract-Tests grün | Schutz | *n.U.* | Zähler |
| C: `route:list --path=api/planner` zeigt je Route die Ability-Middleware (Rohausgabe) | Beleg | *n.U.* | route:list |

**P1-Kriterium A ist vor dem Bau wirksam rot** (0 Treffer für ability-Prüfung).

## Rückweg
Ein Commit, zurückdrehbar.
