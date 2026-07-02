# Rückfluss Stufe 1 — Link planner_item ↔ Büro-Karte: Vorbereitung (reine Analyse)

> **Nur Lesen/Analyse, KEINE Änderung.** Klärt, WO und WIE der fehlende Link zwischen einer Planner-Aufgabe und der Büro-Karte (`kanban_lead_tasks`) am sichersten gesetzt werden kann, als Fundament für den Monteur-Rückfluss mit Projektleiter-Prüfschritt (Weiche 6). Belege wörtlich, Stand 2026-07-02.

> **⚠️ KERNBEFUND, der die Frage neu rahmt (unbedingt zuerst lesen):** Der fehlende Link betrifft **nur die eine von zwei** Montage-Repräsentationen. Büro-Karten haben **bereits** einen vollständigen Rundlauf mit der Feld-App über den source_type **`kanban_task`** (`source_id = kanban_lead_tasks.id`, Status-Writeback verdrahtet). Was fehlt, ist die Verbindung zwischen den **Template-`phase_activity`-Items** (aus dem Projekt-Sync) und den Büro-Karten — zwei **redundante Repräsentationen derselben Montage-Arbeit**. Das verschiebt Stufe 1 von „Link bauen" hin zu einer **Design-Entscheidung: welche Repräsentation ist die Feld-Wahrheit?** (Details §2, §6.)

---

## 1. WO ENTSTEHEN DIE BEIDEN SEITEN? (wörtlich)

### 1a. Büro-Karte — `KanbanLeadTaskController::storeFromTemplate` (:427)
Karte aus einem Phasen-/Aktivitäts-Template (Büro klickt „übernehmen"). Kern (`:469-496`):
```php
$task = KanbanLeadTask::query()->create([
    'lead_product_list_id' => $leadProduct->id,        // Gewerk
    'customer_id'          => $leadProduct->customer_id,
    'alternative_id'       => $leadProduct->alternative_id,
    'product_id'           => $leadProduct->product_id,
    'task_phase_id'        => $phase->id,
    'phase_activity_id'    => $activity?->id,           // NULLABLE (Phase ohne Aktivität möglich)
    'title'                => $activity?->title ?: $phase->phase_name,
    'is_manual'            => false,
    'status'               => !empty($data['is_scheduled']) ? 'scheduled' : 'open',
    'meta' => [ 'source' => 'task_phase_template', 'phase_name' => ..., 'activity_title' => ... ],
]);
```
**Wichtig:** `create()` — **kein** `firstOrCreate`, **kein** Dedup. Zweiter Klick = zweite Karte. `meta` trägt KEINE planner-Referenz.

**Zusätzlich** `storeManual` (:359, `create()` :385): manuelle Karte mit `is_manual => true`, **`task_phase_id => null`, `phase_activity_id => null`** (:392-393 sinngemäß) — hängt an keiner Aktivität.

### 1b. Planner-Item — `pmoUpsertTemplatePlannerItem` via `syncPhaseActivities` (:545)
Der Projekt-Sync (`syncAndLoad` :402 → `syncPhaseActivities` :466) legt je Aktivität ein Item an (`:545`):
```php
$this->pmoUpsertTemplatePlannerItem($plan, 'phase_activity', $activityId, [ ... ]);
```
Upsert (`:768-812`):
```php
$item = PlannerItem::query()->firstOrNew([
    'plan_id'     => (int) $plan->id,
    'source_type' => $sourceType,   // 'phase_activity'
    'source_id'   => $sourceId,     // = phase_activity_id
]);
// ... setzt title/status/duration ... schreibt KEIN meta ...
$item->save();
```
**Wichtig:** `firstOrNew(plan_id, source_type, source_id)` — planner-seitig **eindeutig** pro (Plan, Aktivität). `meta` wird hier **nicht** angefasst.

### 1c. Gemeinsame Achse — ja, vorhanden
`plan` ist `firstOrCreate` auf `(customer_id, project_id)` mit **`project_id = lead_product_lists.id`** (:410-424). Also:

| | Gewerk-Bezug | Aktivitäts-Bezug |
|---|---|---|
| **Büro-Karte** | `lead_product_list_id` (Spalte) | `phase_activity_id` (Spalte, nullable) |
| **Planner-Item(phase_activity)** | `plan.project_id` (= lead_product_list_id) | `source_id` (= phase_activity_id) |

→ Der Verknüpfungs-Query lautet:
```sql
kanban_lead_tasks
  WHERE lead_product_list_id = plan.project_id
    AND phase_activity_id   = planner_item.source_id
```
Beide Werte liegen **zur Sync-Zeit** vor (im `phase_activity`-Zweig von `syncPhaseActivities`). Eindeutig ist der Query aber **nur**, wenn es je (lpl, activity) höchstens eine Karte gibt → §3.

---

## 2. DER KERNBEFUND: es gibt bereits einen `kanban_task`-Rundlauf

**Die Feld-App bekommt Büro-Karten direkt als Aufgaben** — Read-Projektion in `PlannerApiAuthController:481-490`:
```php
return [
    'id'                 => (int) $row->id,
    'kanban_lead_task_id'=> (int) $row->id,
    'source_type'        => 'kanban_task',
    'source_id'          => (int) $row->id,      // <-- source_id IST die Karten-ID
    'lead_product_list_id' => ...,
];
```
(Kein persistiertes planner_item — eine Live-Projektion von `kanban_lead_tasks` in das App-Aufgaben-Format.)

**Und der Status-Writeback ist verdrahtet** — zentraler Dispatch `pmoSyncPlannerItemStatusToSource:1761`:
```php
match ($sourceType) {
    'kanban_task'    => $this->pmoUpdateKanbanTaskSourceStatus($sourceId, $status, ...),  // -> kanban_lead_tasks WHERE id=$sourceId (:2353)
    'phase_activity' => $this->pmoUpdatePhaseActivityStatus($plan, $sourceId, ...),        // -> Kundenhistorie, NICHT die Karte
    // ...
};
```
**Konsequenz — zwei Repräsentationen derselben Montage-Arbeit, mit unterschiedlichem Rückfluss-Ziel:**
1. **`kanban_task`** (source_id = Karten-ID): Rundlauf **existiert** — App-Statusänderung schreibt **direkt in die Büro-Karte** (`:2305-2353`). Aber schreibt heute `done`/`scheduled` **direkt** (kein Zwischenschritt „gemeldet").
2. **`phase_activity`** (source_id = Aktivitäts-ID): aus dem Projekt-Sync; Statusänderung schreibt in die **Kundenhistorie** (`pmoUpdatePhaseActivityStatus`), **nicht** in die Büro-Karte. Hier fehlt der Karten-Link.

→ **Der „fehlende Link" ist real — aber nur für die `phase_activity`-Repräsentation.** Für Büro-Karten (`kanban_task`) besteht der Link + Writeback bereits. Das ist dasselbe „mehrere Wahrheiten"-Muster wie bei Weiche 6, eine Ebene tiefer.

*(NICHT VERIFIZIERT: **welche** Repräsentation die Nuriva-App dem Monteur für Montage tatsächlich anzeigt — `MobilePlannerApiController` [persistierte planner_items: phase_activity/appointment/…] vs. `PlannerApiAuthController` [kanban_task-Projektion]. Das sind zwei verschiedene Endpunkte; welchen die App für Montage nutzt, ist ohne App-Trace/Netzwerk-Mitschnitt nicht belegbar. **Das ist die zentrale offene Tatsache — sie entscheidet, ob Stufe 1 „Link bauen" oder „Zwischenstatus ergänzen" heißt.**)*

---

## 3. DAS EINDEUTIGKEITS-PROBLEM (kritisch)

`storeFromTemplate` dedupliziert nicht (`create()`, §1a). Also **mehrere Karten je (lead_product_list_id, phase_activity_id) möglich** → der Link-Query aus §1c wäre **mehrdeutig** (welche Karte bekommt den Rückfluss?).

**Live-Check (heute):** `kanban_lead_tasks` = **0 Zeilen** (Tabelle neu/ungenutzt). Also ist das Problem **heute rein theoretisch** — und der Zeitpunkt für einen Unique-Index ist **ideal** (keine Bestandsdaten zu deduplizieren, keine Dedup-Migration nötig).

**Ist Dedup Voraussetzung von Stufe 1?** — **Ja, wenn der Link über die Achse (lpl, activity) läuft** (Option B/C). Ohne Eindeutigkeit zeigt der Link evtl. auf die falsche Karte. Zwei Wege:
- **Unique-Index** `(lead_product_list_id, phase_activity_id)` + `firstOrCreate` in `storeFromTemplate`. **Caveat MySQL:** NULLs gelten als verschieden → mehrere `phase_activity_id = NULL`-Karten (Phase-/Manuell-Karten) bleiben erlaubt (gut). Aber der Index bindet auch **manuelle** Karten mit gesetzter `phase_activity_id` an dieselbe Eindeutigkeit — Kollision Template- vs. Manuell-Karte für dieselbe Aktivität ist ein Randfall (bewerten). Ein *gefilterter* Unique-Index (`WHERE is_manual=0`) geht in MySQL nicht nativ.
- **Deterministischer Tie-Break** ohne Index (z. B. „älteste/jüngste Karte") — schwächer, versteckt das Problem.

**Wenn der Link dagegen über `kanban_task` (source_id=Karten-ID) läuft (Option D), entfällt das Eindeutigkeits-Problem** — die Karten-ID ist per se eindeutig.

---

## 4. WAS BERÜHRT EIN LINK-BAU?

**Sync-Frequenz:** `syncPhaseActivities` läuft bei **jedem** `syncAndLoad`/`syncProjectScopedPlan` (Route `/plans/sync`, :5272; auch :4454-4458), also bei jedem Planner-Öffnen/Sync eines Projekts. Ein zusätzlicher Karten-Lookup **pro Aktivität** = N Queries/Sync → sollte **gebündelt** werden (ein Query lädt alle Karten des Projekts vorab, dann In-Memory-Map), sonst N+1.

**meta-Write (Option B) — RISIKO, aktiviert einen latenten Nuriva-Bug:**
- `PlannerItem` castet `meta` zu **`array`** (Model `:23`), aber `MobilePlannerApiController:194-197` behandelt es als String:
  ```php
  $meta = $item->meta ? json_decode($item->meta, true) : [];   // json_decode(array) -> TypeError in PHP 8
  $meta['mobile_report'] = ...; $meta['signature'] = ...;
  $item->meta = json_encode($meta);                            // String an array-cast -> Doppel-Encoding beim save
  ```
- **Heute dormant**, weil Template-Items `meta = NULL` haben (Sync setzt es nie) → Ternär nutzt `[]`, `json_decode` wird nicht erreicht.
- **Sobald der Sync `meta.kanban_lead_task_id` befüllt**, wird `$item->meta` ein nicht-leeres Array → `json_decode($array, true)` → **TypeError**, sobald ein Monteur zu diesem Item einen Report/Signatur sendet. → **Option B erzwingt zusätzlich einen Fix in `MobilePlannerApiController`** (manuelles json_decode/encode raus, Array-Cast direkt nutzen). Das vergrößert den Sprengradius von Option B in den Nuriva-Schreibpfad.

**Neue Spalte (Option C):** `planner_items.kanban_lead_task_id` (nullable, indexiert) — eine Migration. Idiomatischer hier (planner_items bekam bereits mehrere Add-Column-Migrationen: `status`, `done_at`, `done_by_employee_id`). **Additiv, berührt meta/Nuriva nicht**, kein TypeError-Risiko.

**Nuriva allgemein:** liest/schreibt `meta` (mobile_report/signature). Eine **neue Spalte** ist für Nuriva unsichtbar (Nuriva liest sie nicht). Ein **meta-Key** würde von Nurivas Merge-Logik zwar erhalten bleiben (`$meta['x']=...` überschreibt nur einzelne Keys) — aber der TypeError oben tritt vorher auf. → **Spalte ist für Nuriva das sicherere Vehikel.**

---

## 5. LINK-OPTIONEN — additiv vs. invasiv

| Option | Wo/Wie | additiv/riskant | Berührt |
|---|---|---|---|
| **A** — Link beim Karten-Anlegen (`storeFromTemplate`) setzen | Karte merkt sich planner_item | **Nicht tragfähig:** das `phase_activity`-planner_item existiert bei Karten-Anlage oft **noch nicht** (Karte = Büro-UI, Item = Planner-Sync, unabhängige Flows). | — |
| **B** — Beim Sync `planner_items.meta.kanban_lead_task_id` schreiben (Lookup §1c) | Planner-Seite, meta | **riskant:** aktiviert Nuriva-TypeError (§4) → erzwingt Nuriva-Fix; meta-Merge nötig. | Sync, MobilePlannerApiController |
| **C** — Beim Sync neue Spalte `planner_items.kanban_lead_task_id` schreiben (Lookup §1c) | Planner-Seite, neue Spalte | **additiv + sauber:** 1 Migration, kein meta/Nuriva-Eingriff, indexierbar. Braucht Eindeutigkeit (§3). | Sync (+ 1 Migration) |
| **D** — Büro-Karten als `kanban_task`-Repräsentation nutzen (existiert bereits, §2) | Karte projiziert sich schon als Aufgabe; Writeback verdrahtet | **am additivsten für den Link** (0 neuer Link — source_id IST die Karten-ID), **aber** verlangt die Design-Klärung „phase_activity vs. kanban_task als Feld-Wahrheit" und ggf. Zusammenführung der zwei Repräsentationen. | Design-Entscheidung, evtl. Sync-Logik |

---

## 6. FAZIT

**Ist Stufe 1 ein kleiner, sicherer additiver Schritt?** — **Es kommt auf die Design-Entscheidung an**, nicht primär auf die Speicher-Option:

- **Falls die Feld-Wahrheit die Büro-Karte ist** (App zeigt `kanban_task`-Aufgaben): Der Link + Writeback **existiert schon** (§2). Stufe 1 ist dann **kein Link-Bau**, sondern das Einfügen des **Zwischenstatus „gemeldet" ≠ „bestätigt"** in `pmoUpdateKanbanTaskSourceStatus` (schreibt heute `done` direkt) + Projektleiter-Prüfschritt. Klein bis mittel, kein Eindeutigkeits-Problem.
- **Falls die Feld-Wahrheit das `phase_activity`-Template ist**: Der Link fehlt. Dann ist **Option C** (neue Spalte, additiv, kein Nuriva-Risiko) der sauberste Weg — **aber** die **Eindeutigkeit muss zuerst** hergestellt werden (§3; jetzt billig, weil Tabelle leer).

**Empfehlung (mit Begründung):**
1. **ZUERST die offene Tatsache klären** (blockiert alles): Welchen API-Endpunkt/`source_type` nutzt die Nuriva-App für Montage-Aufgaben? Das ist **nicht** verifizierbar ohne App-Trace → **Yama/Nuriva-Seite muss das beantworten** (oder wir tracen die App-Requests live). Ohne das baut Stufe 1 evtl. einen Link, den es (als `kanban_task`) schon gibt.
2. **Falls `phase_activity` die Feld-Wahrheit ist → Option C**, nicht B (B aktiviert den Nuriva-TypeError). Minimale sichere Bau-Sequenz:
   - (i) **Eindeutigkeit**: Unique-Index `(lead_product_list_id, phase_activity_id)` + `firstOrCreate` in `storeFromTemplate` (Tabelle leer → gefahrlos), Randfall Manuell-vs-Template bewerten.
   - (ii) **Migration**: `planner_items.kanban_lead_task_id` (nullable, index).
   - (iii) **Sync**: in `syncPhaseActivities` **einen gebündelten** Karten-Lookup (alle Karten des Projekts als Map), pro Aktivität `kanban_lead_task_id` am Item setzen (additiv, kein N+1).
   - (iv) erst **danach** (Stufe 2) die Melde→Prüf→Bestätigt-Kette.
3. **Falls `kanban_task` die Feld-Wahrheit ist → Option D**: kein Link-Bau; Stufe 1 = Zwischenstatus „gemeldet" in `pmoUpdateKanbanTaskSourceStatus` + Prüfschritt. Offene Frage: Redundanz `phase_activity` ↔ `kanban_task` (zwei Aufgaben für dieselbe Arbeit) — braucht Konsolidierungs-Entscheidung.

**Design-Entscheidung, die Yama treffen muss:**
> **Was ist die Montage-Feld-Wahrheit, mit der der Monteur arbeitet — die Büro-Kanban-Karte (`kanban_task`, Link existiert) oder das `phase_activity`-Template-Item (Link fehlt)? Und sollen diese zwei redundanten Repräsentationen zusammengeführt werden?** Erst diese Antwort legt fest, ob Stufe 1 „Link + Eindeutigkeit bauen" (Option C) oder „Zwischenstatus ergänzen" (Option D) bedeutet.

---

## Gelesen / NICHT gelesen (ehrlich)

**Vollständig gelesen/geprüft (wörtlich):** `storeFromTemplate` (:427-514) + `storeManual` (:359-397, Felder); `syncAndLoad` (:402-463, Sync-Liste ohne kanban); `syncPhaseActivities` (:466-558); `pmoUpsertTemplatePlannerItem` (:768-818, `firstOrNew`, kein meta); `PlannerItem`-Casts (`meta => array`, :23); `planner_items`-Migration (`meta` json nullable, :36) + done-meta-Migration (Spalten, keine meta-Keys); `kanban_lead_tasks`-Migration (Spalten, **kein** Unique auf der Achse); `MobilePlannerApiController` meta-R/W (:194-197); `PlannerApiAuthController` kanban_task-Projektion (:481-490); Status-Dispatch `pmoSyncPlannerItemStatusToSource` (:1761-1780) inkl. kanban_task→Karte (:2305-2353) und phase_activity→Historie; Live-DB (`kanban_lead_tasks` 0 Zeilen, 0 Dupes).

**Nur gegrept / NICHT VERIFIZIERT:**
- **Welche Repräsentation die Nuriva-App für Montage nutzt** (kanban_task-Projektion vs. persistierte phase_activity-Items) — der entscheidende offene Punkt; nur aus dem Backend nicht belegbar.
- Ob `pmoUpdateKanbanTaskSourceStatus` **vom selben Flow** erreicht wird, den der Monteur auslöst (Dispatch bei :1775 ist verdrahtet; der genaue Mobile-Endpunkt → Dispatch-Pfad ist nicht durchtracet). `MobilePlannerApiController:183-197` schreibt `meta` direkt (report/signature) — ein *anderer* Update-Pfad als der Status-Dispatch; deren Zusammenspiel ungeprüft.
- Ob `pmoUpdatePhaseActivityStatus` außer der Kundenhistorie noch etwas berührt (nicht gelesen).
- Der genaue Randfall-Effekt eines Unique-Index auf **manuelle** Karten mit gesetzter `phase_activity_id` (nur konzeptionell bewertet).

## Selbstkritik / Risiken
- **Die Analyse dreht sich um eine unverifizierte Tatsache** (welche Repräsentation die App nutzt). Ich habe bewusst **keine** der drei Optionen als „die richtige" gesetzt, weil ohne diese Tatsache jede Wahl auf Sand steht. Das ist ehrlicher, aber es heißt: **Stufe 1 ist noch nicht startklar** — erst die Design-/App-Frage klären.
- **Der kanban_task-Fund korrigiert die Prämisse** des Auftrags („Link fehlt, muss zuerst geschaffen werden") teilweise: für Büro-Karten existiert er. Hätte ich nur nach `phase_activity` gegrept, wäre die Prämisse „bestätigt" gewesen — der Live-/Breitband-Check hat das Gegenteil gezeigt. („Grep = Verdacht, Live = Wahrheit.")
- **Die TypeError-Aussage (Option B)** beruht auf dem Zusammenspiel Array-Cast × manuelles json_decode; ich habe sie **nicht** live ausgelöst (kein Monteur-Report gegen ein meta-befülltes Item gefahren) → als starkes, aber **NICHT live-verifiziertes** Risiko markiert.
- **Tabelle leer (0 Zeilen)** ist Chance und Warnung zugleich: der Sync/Karten-Flow ist real womöglich noch kaum benutzt → „Feld-Wahrheit" könnte praktisch noch gar nicht etabliert sein, was die Design-Entscheidung umso wichtiger (und noch offen) macht.

---

*Reine Analyse — nichts geändert. Querverweise: `planner-kanban-zuordnung-hart-geprueft.md`, `planner-kanban-meta-daten-geprueft.md`, `monteur-rueckfluss-verknuepfungen-befund.md`, `architektur-entscheidungen.md` (Weiche 6), `fahrplan-ticket-crm.md` (Ebene 1.1).*
