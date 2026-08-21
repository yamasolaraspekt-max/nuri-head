# Z2-W0-3 · Planner-Attendance: `employee_id` kommt aus der Sitzung, nicht aus dem Request

```yaml
zustand: ENTWURF
welle: 0 (Sicherheit, LIVE) — der Teil von S-1, der OHNE den offenen Operanden (Permission-Item) geht
basis_sha: 7a82ecfb
herkunft: Befund S-1 (docs/backlog/inventur-2026-08-21-z2.md), BESTÄTIGT + VERSCHÄRFT durch security-reviewer 21.08.
spur: A — Datenschutz (Standortdaten von Mitarbeitern), Betriebsrats-relevant
baut: generator (Agent backend-entwickler)
nimmt_ab: evaluator — nie der Bauende
fachliche_gegenprobe: security-reviewer (Meldung)
status_steht_in: docs/STATUS.md — Integrator-Lauf erforderlich
abgrenzung: das Routen-Permission-Gate für /planner/* (61 Routen) ist Z2-W0-5 und wartet auf Y-6 (Permission-Item)
```

## Ziel
Kein Attendance-Schreib- oder Lesepfad akzeptiert eine fremde `employee_id` aus Request/Query:
der Mitarbeiter ist der authentifizierte (`authEmployeeId()`), sonst 403.

## Ist-Beleg (Gegenprobe)
`PlannerAttendanceController.php:346-359` `location()`: `employee_id` aus `$request->validate(...)`,
`(int) $data['employee_id']` **ohne Abgleich** mit `authEmployeeId()`; `resolveEmployeeId()` (`:50-61`)
nimmt `employee_id` aus Request/Query, fällt nur bei `<= 0` auf den eigenen zurück; betrifft
`location`, `day`, `report` u.a. Repo-weit in `app/Http/Controllers/Planner/` (16.403 Z., 8 Controller):
**0 Treffer** für authoriz/gate/haspermission/->can(/permission.
**Reproduktion:** eingeloggter Nutzer ohne Planner-Bezug → `POST /planner/plans/{fremderPlan}/attendance/location`
mit `employee_id=<fremd>&lat=0&lng=0` → 200, Zeile in `attendance_locations`.

## Scope · Dateien
- `PlannerAttendanceController.php`: `resolveEmployeeId()` liefert ausschließlich `authEmployeeId()`
  (oder `abort_unless($employeeId === $this->authEmployeeId(), 403)` an jedem Schreib-/Lesepfad —
  eine der beiden Formen, im Baubericht begründet; die erste ist dichter).
- `tests/Feature/Planner/PlannerAttendanceBindungTest.php` (neu): fremde `employee_id` → 403 bzw.
  ignoriert zugunsten der eigenen; eigene → 200. **Nur `ticket_testing`.**
**Nicht-Ziele:** kein Permission-Item, keine Routen-Middleware (Z2-W0-5/Y-6); keine Änderung an
Plan-/Item-Logik; keine Migration; `api/planner/*` (Sanctum) ist Gegenstand des Folgelaufs Z2c.

## Kanten
Gibt es einen legitimen Vorgesetzten-Fall (Teamleiter trägt für Mitarbeiter ein)? **Dann ist das
ein Operand** — im Baubericht als Rückfrage ausweisen, nicht still erlauben; bis dahin gilt
„nur eigener Mitarbeiter".

## Nachvollzugs-Matrix (Fassung 1.7, §5)
| Kriterium | Arbeitspaket | Commit-SHA | Testbeleg |
|---|---|---|---|
| A: fremde `employee_id` in `location` → 403 (oder überschrieben durch eigene — eine Form, belegt) | Bindung | *n.U.* | Testname |
| B: `day`/`report` mit fremder `employee_id` liefern keine fremden Daten | Bindung | *n.U.* | Testname |
| C: eigener Mitarbeiter → unverändert 200 (kein Verlust) | Schutz | *n.U.* | Testname |
| D: `grep -n "employee_id" PlannerAttendanceController.php` zeigt keine Request-Übernahme ohne Abgleich mehr (Rohausgabe) | Grenze | *n.U.* | grep |

**P1-Kriterium A ist vor dem Bau wirksam rot** (Reproduktion 200).

## Rückweg
Ein Commit, zurückdrehbar; kein Schema, keine Daten.
