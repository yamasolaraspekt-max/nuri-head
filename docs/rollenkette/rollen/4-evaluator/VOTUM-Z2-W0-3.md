# VOTUM Z2-W0-3 — Attendance: `employee_id` aus der Sitzung

**evaluator · 22.08.2026 · Auftrag `ABNAHME-evaluator-Z2-W0-3` gen 14 · Lease-Token 1**
**Bau `69c85d01` · Integrationsstand `767fb730`**

## Ergebnis: ABGENOMMEN — 4 von 4 Kriterien erfüllt

| # | Verlangt | Beleg (selbst gemessen) |
|---|---|---|
| **A** | fremde `employee_id` in `location` → 403 (oder überschrieben) | **403** „Fremde Mitarbeiter-Kennung."; Mutation trifft den Test |
| **B** | `day`/`report` mit fremder `employee_id` liefern keine fremden Daten | `report` grün + Mutation; `day` selbst geprobt → **keine fremden Daten** |
| **C** | eigener Mitarbeiter → unverändert 200 (kein Verlust) | grün, und in **beiden** Mutationsrichtungen unberührt |
| **D** | `grep -n employee_id` zeigt keine Request-Übernahme ohne Abgleich | Rohausgabe unten |

## D — Rot gegen Grün, mit dem Messbefehl des Blatts

```
ROT   69c85d01^:52   $employeeId = (int) $request->input('employee_id', $request->query('employee_id', 0));
GRÜN  Integrationsstand — diese Zeile existiert nicht mehr.
      :73  private function resolveEmployeeId(Request $request): int
           $employeeId = (int) ($this->authEmployeeId() ?? 0);
           abort_if($employeeId <= 0, 422, …);
      :383 abort_unless((int) $data['employee_id'] === $employeeId, 403, 'Fremde Mitarbeiter-Kennung.');
```

Der Bau wählt die dichtere der beiden vom Blatt zugelassenen Formen: **eine** Auflösestelle statt
neun `abort_unless`. **Ich habe nachgezählt statt geglaubt:** `resolveEmployeeId()` hat **neun**
Aufrufstellen (`:303, :321, :340, :382, :434, :453, :468, :486, :504, :532`) — alle Lese- und
Schreibpfade gehen darüber. Für den Schreibpfad `location()` kommt die ausdrückliche 403 hinzu.

## Gegen-Beweis: die Zusagen hängen an der Bindung

Mutation im Wegwerf-Klon — `resolveEmployeeId` nimmt wieder den Request-Wert:

```
⨯ location mit fremder employee id wird abgewiesen
✓ location mit eigener employee id geht durch
⨯ report mit fremder employee id liefert die eigenen daten
```

Genau die zwei Fremd-Tests fallen, der Eigen-Test bleibt grün. Klon zurückgesetzt, am Bestand
nichts verändert.

**Ein erster Mutationsversuch griff nicht** — mein Muster suchte `protected`, die Methode ist
`private`; die Tests blieben grün, weil der Code unverändert war. Ein Gegen-Beweis, der nichts
verändert, belegt nichts: erst der zweite Lauf zählt.

## `day` — was ich belegt habe und was nicht

Kriterium B nennt `day` **und** `report`; der mitgelieferte Test deckt nur `report`. Ich habe `day`
selbst geprobt:

```
GET /planner/plans/{plan}/attendance/day?employee_id=<FREMD>
-> {"ok":true,"data":{"date":"2026-08-22","by_employee":[]}}
```

**Keine fremden Daten — die Aussage des Kriteriums ist erfüllt.** Die Gegenrichtung („der eigene
kommt") konnte ich **nicht** herstellen: auch mit eigener Kennung ist die Liste leer, weil mein
Testaufbau für `day` keine Attendance-Zeile anlegt. Das ist **mein** Aufbau, kein nachgewiesener
Funktionsverlust — ich sage es so, statt aus einer leeren Antwort einen Mangel zu machen.
Strukturell trägt `day` dieselbe Bindung: `:303` ruft `resolveEmployeeId()`.

## Grundmenge

`route:list` → **20** Routen auf Attendance-Controllern, davon **11** auf dem
`PlannerAttendanceController` — dem einzigen im Scope. Alle elf laufen über `resolveEmployeeId()`;
keine Route ohne Messung.

## Befund ohne Kriterienwirkung — dieselbe Klasse außerhalb des Scope

```
Admin/AttendanceAnalyticsController.php:32   $employeeId = $request->get('employee_id');
Api/MobileAttendanceController.php:374       $requestEmployeeId = $request->input('employee_id');
```

Beide **ohne** Abgleich mit der Sitzung in der Datei (`authEmployeeId|Auth::id|user()->employee` →
je **0**), Middleware nur `Authenticate` bzw. `Authenticate:sanctum` — also authentifiziert, aber
ohne Ownership.

**Kein Mangel an diesem Auftrag:** das Blatt begrenzt den Scope ausdrücklich auf
`PlannerAttendanceController` und nennt `api/*` unter Nicht-Zielen (Folgelauf Z2c). Ich schreibe
kein Kriterium nach. Aber es ist genau die Lücke, die hier geschlossen wurde — an zwei anderen
Türen. **Was ich nicht gemessen habe:** ob eine Prüfung tiefer im Aufruf greift; ich habe die
Datei und die Middleware gemessen, nicht den ganzen Pfad.

## Ball

**Dirigent** — Z2-W0-3 abgenommen; die zwei Fundstellen gehören in die Z2c-Folge.
