# Nuriva-Montage-Endpunkt — welcher Weg nutzt die App? (reine Analyse)

> **Nur Lesen/Analyse, keine Änderung.** Klärt die offene Frage aus `rueckfluss-stufe1-link-vorbereitung.md`: Nutzt die Nuriva-Monteur-App für Montage **kanban_task** (Büro-Karte, Rundlauf existiert = Weg 1) oder **phase_activity** (Template-Item, Karten-Link fehlt = Weg 2)? Das entscheidet, ob Rückfluss-Stufe 1 **klein** oder **groß** ist. Stand 2026-07-02, Belege wörtlich.

> **⚠️ ERGEBNIS: Mit hoher Sicherheit Weg 2 (phase_activity) — Stufe 1 ist die GROSSE Variante.** Begründung strukturell (nicht daten-belegt, weil lokal 0 Zeilen): Der gesamte aktionierbare Nuriva-`/planner`-Flow (Arbeit laden **und** abschließen) ist **planner_item-ID-zentriert**, und die einzigen planner_items, die der Sync erzeugt, sind **phase_activity** (nie kanban_task). Die kanban_task-Projektion ist eine **Anzeige-Beigabe ohne aktionierbaren Endpunkt**. **Aber:** kein Runtime-Beweis (0 Daten) → als starke *strukturelle* Schlussfolgerung markiert, endgültige Bestätigung via Request-Log/Ramin. Details unten.

---

## 0. Welche Oberfläche IST Nuriva? — code-belegt

Es gibt zwei mobile API-Flächen. Die Code-Kommentare weisen **`/planner/*`** eindeutig Nuriva zu:
- `routes/api.php:224` — Gruppen-Header **„Planner / Nuriva Integration API"**.
- `routes/api.php:291` „**Nuriva**: Complete Planner Item With Report", :304/:311 „**Nuriva** sends task/customer photos here."
- `PlannerEmployeeApiController` schreibt `'source' => 'nuriva_mobile'` (:1906/1959), `'type' => 'nuriva_mobile'` (:1951), `'title' => 'Nuriva Bericht'` (:1987); Kommentar :1779 „This is what **Nuriva** can read immediately when it reloads planner-work."

→ **Nuriva = `/planner/*`** (PlannerApiAuthController [auth] + PlannerEmployeeApiController [work/complete] + PlannerItemStateController [play/pause/stop] + PlannerMasterSet/ItemMaterial/MobileCustomerImage). Die `/mobile/*`-Fläche (MobileAuthController/MobilePlannerApiController) ist eine **separate** (ältere?) mobile Fläche, **nicht** als Nuriva gekennzeichnet. *(NICHT VERIFIZIERT: ob Nuriva zusätzlich `/mobile/*` nutzt — unwahrscheinlich laut Kommentaren, aber nicht ausgeschlossen.)*

---

## 1. Alle Monteur-Endpunkte (wörtlich, routes/api.php)

**Nuriva `/planner`-Gruppe** (`prefix('planner')`, :231; Auth via `auth:sanctum`, :253):
| Route | Controller@Methode | Liefert / tut |
|---|---|---|
| POST `/planner/auth/token` | `PlannerApiAuthController@token` | Login → Token **+ Bundle inkl. `kanban_tasks`** (Projektion, s. §2) |
| GET `/planner/auth/me` | `PlannerApiAuthController@me` | Profil **+ Bundle inkl. `kanban_tasks`** (:111) |
| GET `/planner/my-work` | `PlannerEmployeeApiController@myWork` | **persistierte planner_items** des Mitarbeiters (§2) |
| GET `/planner/my-day-report`, `/employees/{e}/work`, `/employees/{e}/day-report` | dito | dito (Tag/Report-Sichten über planner_items) |
| PATCH `/planner/items/{item}/complete-report` | `PlannerEmployeeApiController@completeItemWithReport` | **Abschluss eines planner_items** (Writeback, §3) |
| — `/planner/items/{item}/master-sets`, `/materials`, `/customer-images` | PlannerMasterSet/ItemMaterial/MobileCustomerImage | Material/Bilder je **planner_item-ID** |
| GET/POST `/planner/plans/{plan}/items/{item}` play/pause/stop | `PlannerItemStateController` (statusesByPlan/play/pause/stop) | Zeit/Status je **planner_item-ID** (plan+item) |

**`/mobile`-Gruppe** (separat, nicht Nuriva-gekennzeichnet): GET `/mobile/tasks` → `MobilePlannerApiController@index` (persistierte planner_items), POST `/mobile/tasks/sync` → `@sync` (Writeback in `meta`), + attendance/calendar/employees/customers.

---

## 2. Welcher liefert Montage-Aufgaben — und in welcher Repräsentation?

**Zwei Repräsentationen, unterschiedliche Endpunkte:**

**(a) Persistierte planner_items → `my-work`** (der aktionierbare Arbeits-Feed). `myWork` → `employeeWorkResponse` → `loadEmployeeItems` (:223) fragt **ausschließlich** `planner_items`:
```php
$q = DB::table('planner_items as pi')
    ->join('planner_plans as pp', 'pp.id', '=', 'pi.plan_id')
    ->whereExists( … planner_item_employees pie WHERE pie.employee_id = $employeeId );
```
**Kein Union mit `kanban_lead_tasks`.** Die source_type dieser Items bestimmt der **Sync** — und der Projekt-Sync erzeugt (belegt in `syncAndLoad:438-442` → `syncPhaseActivities:545`) **`phase_activity`** (+ task_phase/appointment/ticket/personal_task/master_set), **nie `kanban_task`**. → **`my-work` liefert Montage als `phase_activity` (Weg 2).**

**(b) kanban_task-Projektion → im Auth-Bundle.** `PlannerApiAuthController::kanbanTasksPayload` (privat, :209) liest `kanban_lead_tasks` **direkt** und projiziert (:481-490):
```php
return [ 'id' => (int)$row->id, 'kanban_lead_task_id' => (int)$row->id,
         'source_type' => 'kanban_task', 'source_id' => (int)$row->id, … ];
```
Aufgerufen **nur** in `token()` (:88) und `me()` (:111) → als **Beigabe** der Auth-Antwort. **Kein persistiertes planner_item**, `id` = **Karten-ID**. (Weg 1.)

---

## 3. Welcher schreibt zurück — und verbindet sich der Writeback mit derselben Repräsentation?

**Der Nuriva-Abschluss `completeItemWithReport(int $item)` (:1682) ist planner_item-ID-zentriert:**
```php
$plannerItem = DB::table('planner_items as pi')->join('planner_plans as pp', …)
    ->where('pi.id', $item)->whereExists(… pie.employee_id = $employeeId)->first();  // :1722-1741
// 1. planner_items.status='done' (+done_at/done_by)          :1758-1773
// 2. storePlannerItemReportMirror  (planner_item_comments)   :1783  „was Nuriva beim Reload liest"
// 3. storePlannerSourceReport      (Report in Quelltabelle)  :1798
// 4. markPlannerSourceDone(...)    source-status-Writeback   :1812
// 5. storePlannerItemStatusHistory                            :1825
```
`markPlannerSourceDone` (:2028) verzweigt nach dem **source_type des geladenen Items** (:2126): `if ($sourceType === 'kanban_task') { … update kanban_lead_tasks WHERE id=$sourceId … }`; für `phase_activity` geht es in die Kundenhistorie (`pmoUpdatePhaseActivityStatus`, vgl. Dispatch `pmoSyncPlannerItemStatusToSource:1775-1777`).

**Die Kette verbindet sich also mit dem source_type des planner_items** — aber:
- `completeItemWithReport` verlangt eine **planner_item-ID**. Die kanban_task-**Projektion** liefert `id = Karten-ID` (≠ planner_item-ID). Ein Abschluss der Projektions-Items über diesen Endpunkt träfe `planner_items WHERE id=Karten-ID` → **falsches Item / 404**.
- Es gibt **keinen** generischen „Status per (source_type, source_id)"-Endpunkt in `/planner` und **keinen** Aktions-Endpunkt per Karten-ID. `PlannerItemStateController` play/pause/stop nimmt `(planId, itemId)` = planner_item-ID.

→ **Der Karten-Writeback (kanban_task→Karte) feuert nur, wenn ein abgeschlossenes planner_item selbst source_type='kanban_task' hätte — solche erzeugt der Sync aber nicht.** Für die real existierenden (phase_activity-)Items schreibt der Abschluss in die **Kundenhistorie, nicht in die Büro-Karte.**

**Bonus-Fund (Querverbindung Follow-up):** `completeItemWithReport` validiert bereits **`next_step` + `due_date`** (:1696-1697) — exakt die Follow-up-Felder aus `follow-up-bestandsaufnahme.md`. Der Monteur-Abschluss trägt das Follow-up-Konzept also schon im Ansatz.

---

## 4. Indizien für reale Nutzung

- **Daten (entscheidend):** `planner_items` GROUP BY source_type → **0 Gruppen (Tabelle leer)**; `kanban_lead_tasks` → **0 Zeilen**. **Beide Montage-Tabellen sind lokal leer** → **aus den Daten ist KEIN Weg belegbar** (keiner kommt real vor). Das Subsystem ist lokal offenbar (noch) unbenutzt/pre-production.
- **Logging/Timestamps:** kein `last_used`/Zugriffszähler je Endpunkt gefunden (nur allgemeines `Log::info('Planner sync completed', …)` im Sync). Kein Beleg, welcher Endpunkt real getroffen wird.
- **Sanctum/Auth-Flow:** Nuriva authentifiziert über `/planner/auth/token` (throttle:10,1) → Token → `auth:sanctum`-Gruppe. Das Auth-Bundle liefert `kanban_tasks` mit; der aktionierbare Arbeits-Feed ist `my-work` (planner_items). Beide erreichbar — der Token-Flow entscheidet die Repräsentation **nicht**.

---

## 5. Fazit

**Lässt sich aus dem Backend eindeutig sagen, welchen Weg die App nutzt?**

**Struktur: JA, mit hoher Sicherheit → Weg 2 (phase_activity).** Runtime-Beweis: **NEIN** (0 Daten).

Die aktionierbare Nuriva-Maschinerie (laden via `my-work`/planner_items, abschließen via `completeItemWithReport`/planner_item-ID, Zeit via `PlannerItemStateController`/planner_item-ID) arbeitet **durchgängig über persistierte planner_items**, und die einzigen, die entstehen, sind **phase_activity**. Die `kanban_task`-Projektion ist eine **Anzeige-Beigabe im Auth-Bundle ohne aktionierbaren Endpunkt** (Karten-ID lässt sich über keinen `/planner`-Endpunkt abschließen/bespielen). Der bestehende kanban_task→Karte-Writeback ist zwar verdrahtet, **feuert aber nur für kanban_task-typisierte planner_items, die der Sync nie anlegt** → für Nuriva-Montage praktisch **toter Zweig**.

**Damit: Rückfluss-Stufe 1 ist die GROSSE Variante** — der Karten-Link für die phase_activity-Repräsentation **fehlt real** und muss geschaffen werden (Option C aus `rueckfluss-stufe1-link-vorbereitung.md`: neue Spalte `planner_items.kanban_lead_task_id`, additiv), **plus Eindeutigkeit** (Unique-Index, heute billig weil 0 Zeilen). NICHT die kleine „nur Zwischenstatus"-Variante.

**Was zur ENDGÜLTIGEN Klärung fehlt (Runtime, nicht Backend):**
1. **Request-Log** an den `/planner`-Endpunkten: Welche Liste lädt die App für Montage (`my-work` vs. das `kanban_tasks`-Bundle aus `auth/me`), und **welche `id`** postet sie an `/planner/items/{id}/complete-report`? (planner_item-ID ⇒ Weg 2 bestätigt; Karten-ID ⇒ Weg 1 im Spiel.) Minimal-invasiv: temporäres `Log::info` in `myWork` + `completeItemWithReport` mit der eingehenden id/route.
2. **ODER** eine Frage an Ramin/Nuriva-Dev: „Zeigt die App dem Monteur die Aufgaben aus `GET /planner/my-work` (planner_items) oder aus dem `kanban_tasks`-Feld der Auth-Antwort? Und welche id schickt sie an `complete-report`?"

Da die Backend-Struktur bereits stark auf Weg 2 zeigt und beide Tabellen leer sind, ist der **pragmatische Schluss**: Stufe 1 als GROSSE Variante planen (Option C + Eindeutigkeit); das Request-Log/​die Ramin-Frage dient nur der **Bestätigung**, bevor gebaut wird.

---

## Gelesen / NICHT gelesen (ehrlich)
**Geprüft (wörtlich/live):** `routes/api.php` (mobile + planner Gruppen, Nuriva-Kommentare); `MobilePlannerApiController` index (:52 planner_items) + sync (:171-197 meta-Writeback); `PlannerApiAuthController` kanbanTasksPayload (:209/:481-490) + Aufrufer token/me (:88/:111); `PlannerEmployeeApiController` myWork→employeeWorkResponse→loadEmployeeItems (:19/:67/:223, nur planner_items), completeItemWithReport (:1682-1857, planner_item-ID, next_step/due_date, markPlannerSourceDone), markPlannerSourceDone kanban_task-Zweig (:2028/:2126); Dispatch pmoSyncPlannerItemStatusToSource (:1771-1779); Sync erzeugt phase_activity (syncAndLoad:438-442, syncPhaseActivities:545); PlannerItemStateController (play/pause/stop by planId+itemId); Live-Datenzählung (planner_items 0, kanban_lead_tasks 0).

**NUR gegrept / NICHT VERIFIZIERT:**
- **Welchen Endpunkt die App real aufruft** — der Kern-Runtime-Punkt; ohne Request-Log/App-Trace nicht beweisbar. Struktur zeigt Weg 2, aber die App könnte das `kanban_tasks`-Bundle anzeigen und über einen mir unbekannten/zukünftigen Pfad aktionieren.
- Ob Nuriva **zusätzlich** die `/mobile/*`-Fläche nutzt (Kommentare sagen /planner=Nuriva; nicht 100 % ausgeschlossen).
- Ob **irgendein** Pfad kanban_task-**planner_items** (persistiert) erzeugt (der „Quick-Add" :4768 legt eine rohe Karte an, kein planner_item) — falls doch, wäre der kanban_task-Zweig doch lebend.
- `storePlannerSourceReport`/`pmoUpdatePhaseActivityStatus` im Detail (ob der phase_activity-Report doch irgendwo eine Karte berührt — unwahrscheinlich, ungelesen).

## Selbstkritik / Risiken
- **Die Aussage ist eine starke strukturelle Inferenz, kein Beweis.** Bei 0 Zeilen in beiden Tabellen ist das Subsystem lokal unbenutzt — es ist denkbar, dass die reale Nuriva-Instanz anders bespielt wird als der lokale Code nahelegt. Ich habe „Weg 2" daher als *hohe Sicherheit mit Runtime-Vorbehalt* formuliert, nicht als Faktum.
- **Das Nebeneinander von zwei Repräsentationen + verdrahtetem-aber-unerreichtem kanban_task-Zweig** riecht nach **halbfertigem/aspirationalem Code** (jemand wollte kanban_task aktionierbar machen, hat aber keinen App-Pfad fertiggebaut). Das ist selbst ein Befund: die „mehrere Wahrheiten"-Krankheit auch hier.
- **Empfehlung „große Variante bauen"** stützt sich darauf, dass der Abschluss planner_item-zentriert + phase_activity-only ist. Falls das Request-Log wider Erwarten Karten-IDs zeigt, kippt es auf Weg 1 (klein) — deshalb: **erst bestätigen (Log/Ramin), dann bauen.** Kein Bau ohne diese eine Runtime-Bestätigung.

---

*Reine Analyse — nichts geändert. Querverweise: `rueckfluss-stufe1-link-vorbereitung.md` (Optionen A–D), `architektur-entscheidungen.md` (Weiche 6), `planner-kanban-zuordnung-hart-geprueft.md`, `fahrplan-ticket-crm.md` (Ebene 1.1).*
