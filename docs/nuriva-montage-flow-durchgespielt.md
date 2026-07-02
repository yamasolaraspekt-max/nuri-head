# Nuriva-Montage-Flow — lokal durchgespielt (Runtime-Verifikation)

> **Analyse + temporärer Seeder (nur lokal), kein Produktiv-Bau am Rückfluss.** Verifiziert die strukturelle Inferenz aus `nuriva-montage-endpunkt-geklaert.md` (ae00b03) mit einem echten lokalen Durchlauf: Ist **Weg 2 (phase_activity)** der einzige aktionierbare Montage-Weg, oder gibt es auch für **Weg 1 (kanban_task)** einen Lade+Abschluss-Weg? Stand 2026-07-02, Belege aus realem Durchlauf.

> **⚠️ ERGEBNIS: BESTÄTIGT — Weg 2 (phase_activity) ist der EINZIGE aktionierbare Montage-Weg. Runtime-belegt.** Sogar stärker als vermutet: Weg 1 ist nicht nur „ohne Aktions-Endpunkt", sondern **die Lieferung selbst crasht** (kanban_tasks-Projektion → HTTP 500, `Unknown column 'laa.name'`). Ein Monteur kann Montage-Karten über die Nuriva-API **weder laden noch abschließen**. → **Rückfluss-Stufe 1 ist die GROSSE Variante** (Option C: Link + Eindeutigkeit). Details unten.

---

## Setup — was geseedet wurde (echte Code-Pfade, kein Fake)

Basis + Templates existierten bereits (13 article_groups, 13 task_phases, 49 phase_activities, 52 Gewerke); nur `planner_items`/`kanban_lead_tasks` waren leer. Verwendet: **Gewerk 53** (customer 105, alternative 103, **product_id 2**), Phase **2 „Umsetzung"**, Aktivitäten **2/3/4**, Mitarbeiter **122** (= User 126 `markus.hoffmann`, `name="122"`; `authEmployeeId()` = `(int) auth()->user()->name`).

Geseedet über die **echten Controller-Methoden** (Tinker, `auth()->loginUsingId(126)`):
- **`PlannerPlanController::syncAndLoad(customer_id=105, project_id=53)`** → HTTP 200, Plan id=2. Erzeugte planner_items: **`{master_set: 1, phase_activity: 3}`** → phase_activity-Items **7,8,9** (source_id 2,3,4). **NULL kanban_task** — bestätigt: der Sync erzeugt phase_activity, nie kanban_task.
- **`KanbanLeadTaskController::storeFromTemplate(lead_product_list_id=53, task_phase_id=2, phase_activity_id=4)`** → HTTP 200, **Karte id=1** (`status='open'`, title „Montage").
- Der Sync wies die Items real **Mitarbeiter 2** zu (aus Aktivitäts-/Pivot-Daten). Für den my-work-Filter (User 126 = Emp 122) habe ich **Emp 122 zusätzlich zugeordnet** — **Test-Fixture**, ehrlich vermerkt; ändert die source_type-Aussage nicht (alle Items sind phase_activity, unabhängig vom Mitarbeiter).

---

## SCHRITT 2 — Montage-Ladung (`GET /api/planner/my-work`, echter Sanctum-Token)

Token via `POST /api/planner/auth/token` (markus/demo1234, `ok:true`, token_len 50). Dann `my-work?include_unscheduled=1&include_done=1&mode=week`:
```
ok: true
source_type phase_activity : 12 Vorkommen (= Items 7,8,9 in items[]/byDate[]/projects[])
source_type kanban_task    : 0
source_type master_set     : 0   (master_set-Item war Emp 122 nicht zugeordnet -> gefiltert)
gelieferte planner_item_id: 7 (source_id 2), 8 (source_id 3), 9 (source_id 4)
```
→ **`my-work` liefert Montage AUSSCHLIESSLICH als `phase_activity`.** Die Büro-Karte (id=1) **erscheint NICHT** (sie ist ein `kanban_lead_tasks`-Zeile, kein planner_item; `loadEmployeeItems` liest nur planner_items).

---

## SCHRITT 3 — Abschluss, beide Wege probiert

### Versuch A — phase_activity abschließen (Weg 2) → GELINGT
`PATCH /api/planner/items/7/complete-report` (report + next_step + due_date):
```
Response: ok:true  "Aufgabe wurde mit Bericht abgeschlossen."
planner_item 7:            open  -> done
customer_histories(105/2/act2): 0 -> 1       <-- Writeback-Ziel: KUNDENHISTORIE
kanban card 1:             open  -> open      <-- UNBERÜHRT
```
→ Der phase_activity-Abschluss (`markPlannerSourceDone`) schreibt in die **Kundenhistorie**, **nicht** in die Büro-Karte. Bestätigt: **kein Karten-Rückfluss** auf diesem Weg.

### Versuch B — die kanban_task-Karte über die Nuriva-API laden/abschließen (Weg 1) → UNMÖGLICH
- **B1 — Abschluss per Karten-ID:** `PATCH /api/planner/items/1/complete-report` → **HTTP 404** „Planner Aufgabe wurde nicht gefunden oder ist diesem Mitarbeiter nicht zugeordnet." (Es existiert **kein planner_item id=1**; die Karten-ID lebt in einem anderen ID-Raum.)
- **B2 — gibt es überhaupt einen Karten-Endpunkt?** `route:list | grep api/(planner|mobile) … kanban|card` → **0 Treffer.** Keine App-API-Route lädt/aktioniert eine `kanban_lead_task` per Karten-ID.
- **B3 — die Weg-1-Lieferung selbst:** `POST /api/planner/auth/token {include_kanban_tasks:true}` → **HTTP 500**, `SQLSTATE[42S22]: Unknown column 'laa.name'` (die `kanbanTasksPayload`-Projektion joint `lead_alternative_adds as laa` und selektiert ein nicht-existentes `laa.name`). **Die kanban_task-Projektion crasht — sie liefert dem Monteur gar keine Karten.**
- **Karte 1 blieb über alle Versuche `open`** — über die Nuriva-API nie verändert.

---

## SCHRITT 4 — Fazit

**Ist Weg 2 (phase_activity) der EINZIGE aktionierbare Montage-Weg für Nuriva? — JA, runtime-belegt.**

| | Weg 2 (phase_activity) | Weg 1 (kanban_task) |
|---|---|---|
| **Ladung** (`my-work`) | ✅ liefert die Items (7,8,9) | ❌ nicht in my-work; Bundle-Projektion **crasht (HTTP 500)** |
| **Abschluss** | ✅ `complete-report` → `done` | ❌ **kein Endpunkt** (Karten-ID → 404) |
| **Writeback-Ziel** | Kundenhistorie (nicht die Karte) | — (nie erreicht) |

Die strukturelle Inferenz aus ae00b03 ist damit **runtime-bestätigt und sogar verschärft**: Weg 1 ist doppelt tot (keine Aktion **und** kaputte Lieferung). **Logisch zwingend:** Da die App Montage-Karten über die Nuriva-API weder laden (500) noch abschließen (404, kein Endpunkt) kann, **MUSS** Montage über Weg 2 (phase_activity) laufen — unabhängig vom (nicht vorliegenden) App-Code.

**→ Rückfluss-Stufe 1 = GROSSE Variante** (`rueckfluss-stufe1-link-vorbereitung.md`, Option C): der Karten-Link für die phase_activity-Repräsentation fehlt real und muss geschaffen werden — neue Spalte `planner_items.kanban_lead_task_id` (additiv) + Eindeutigkeit (Unique-Index, heute billig weil 0 Zeilen). NICHT die kleine „nur Zwischenstatus"-Variante. **Bau erst nach separatem Pflicht-Stopp.**

**Zusatz-Funde:**
1. **`kanbanTasksPayload` ist SQL-kaputt** (`laa.name`) — eigener Bug; falls Weg 1 je genutzt werden sollte, müsste erst dieser Query repariert werden. (Eigener Fix-Kandidat, out of scope.)
2. **`complete-report` akzeptiert bereits `next_step` + `due_date`** und schrieb sie mit — direkte Brücke zum **Follow-up-Konzept** (`follow-up-bestandsaufnahme.md`).
3. Der Sync weist Mitarbeiter real zu (Emp 2 aus Aktivitäts-Daten) — die Zuordnungs-Mechanik funktioniert.

**Aufräumen:** Alle Seed-Daten wieder entfernt — Baseline wiederhergestellt (`planner_plans/items/employees/kanban_lead_tasks` = 0, Test-`customer_histories` + Token gelöscht). Seeder-/Cleanup-Skripte liegen nur im **Scratchpad**, **nicht** in der produktiven Seeder-Kette (nicht committet).

---

## Gelesen / NICHT gelesen (ehrlich)
**Real ausgeführt/geprüft:** Seed via echtem `syncAndLoad` (200, planner_items {master_set:1, phase_activity:3}, keine kanban_task) + echtem `storeFromTemplate` (200, Karte id=1); Live-Token `POST /api/planner/auth/token` (200 ohne / **500 mit** include_kanban_tasks); `GET /api/planner/my-work` (nur phase_activity, Karte fehlt); `PATCH .../items/7/complete-report` (200, item→done, customer_histories 0→1, Karte unverändert); `PATCH .../items/1/complete-report` (Karten-ID → 404); `route:list` (0 kanban/card-API-Routen); Baseline-Wiederherstellung verifiziert (alle 0).

**NICHT verifiziert / Vorbehalte:**
- **Der echte Nuriva-App-Client** wurde nicht ausgeführt — die Aussage ruht auf dem **logisch zwingenden** Backend-Verhalten (Weg 1 nicht ladbar/abschließbar), nicht auf einem App-Trace. Falls die App einen mir unbekannten Endpunkt/Weg nutzt, müsste er außerhalb der gefundenen `/planner`+`/mobile`-Routen liegen (keine mit kanban/card).
- **Fixture-Zuordnung Emp 122**: der Sync wies Emp 2 zu; ich ergänzte 122 für den my-work-Filter. Beeinflusst die source_type-Aussage nicht, ist aber keine „natürliche" Zuweisung an genau meinen Test-User.
- **Der `laa.name`-Crash** ist gegen mein lokales Schema reproduziert; Produktion könnte ein anderes `lead_alternative_adds`-Schema haben (dann evtl. kein Crash). Der Code ist aber identisch.
- `pmoUpdatePhaseActivityStatus`/`storePlannerSourceReport` im Detail nicht zeilenweise gelesen — der Effekt (customer_histories +1, Karte unberührt) ist aber live belegt.

## Selbstkritik / Risiken
- **Leere Ausgangs-DB = Chance und Vorbehalt.** Der Durchlauf beweist das **Verhalten der Endpunkte** hart, aber nicht, wie eine *produktiv bespielte* Nuriva-Instanz aussieht. Da beide Wege dieselbe Code-Basis nutzen, ist der Schluss dennoch belastbar.
- **Der stärkste Beleg ist ein negativer** (Weg 1 crasht/hat keinen Endpunkt). Negative Belege sind robust gegen „vielleicht nutzt die App es doch" — man kann einen 500/404-Endpunkt nicht sinnvoll nutzen. Das ist genau die vom Auftrag gesuchte „logisch zwingende" Aussage.
- **Der kanban_tasks-Crash** könnte bedeuten, dass Weg 1 mal geplant, aber nie fertig/repariert wurde — konsistent mit dem „halbfertig"-Verdacht aus ae00b03. Es bleibt denkbar, dass jemand Weg 1 reaktivieren *wollte*; der aktuelle Stand ist aber eindeutig Weg 2.
- **Kein Produktiv-Bau erfolgt** — nur Analyse + temporärer Seed, danach Baseline wiederhergestellt. Stufe-1-Bau erst nach Pflicht-Stopp.

---

*Runtime-Verifikation — Seed temporär, Baseline wiederhergestellt, kein produktiver Code geändert. Querverweise: `nuriva-montage-endpunkt-geklaert.md` (Inferenz, hier bestätigt), `rueckfluss-stufe1-link-vorbereitung.md` (Option C), `follow-up-bestandsaufnahme.md` (next_step/due_date-Brücke), `fahrplan-ticket-crm.md` (Ebene 1.1).*
