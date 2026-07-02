# Kanban ↔ Phase ↔ Aufgabe ↔ Arbeitsschritt — Bestandsaufnahme (vor dem Bau)

> **Reine Analyse (nur Lesen), kein Code/Migration/Datei geändert.** Diese Datei ist das einzige Schreibprodukt.
> **Ziel:** kartieren, wie die vier Ebenen **Kanban → Phase → Aufgabe → Arbeitsschritt** heute im Code zusammenhängen, **bevor** daran gebaut wird — mit wörtlichen Belegen, Live-Zeilenzahlen, ehrlicher Gelesen/Nicht-gelesen-Liste und klarer Benennung konkurrierender „Wahrheiten".
> **Begriffe (`glossar.md`):** Kunde=`new_leads`, Objekt=`lead_alternative_adds` (`alternative_id`), Gewerk=`lead_product_lists` (Kunde×Produkt×Objekt), Angebot=`offers`, Auftrag=`deals`.
> **Verbindliche Phasen (Weiche 1):** Lead → Angebot → Auftrag → Montage → Abnahme → Abschluss.
> **Stand-Hinweis:** Zwischen den Vorkontext-Befunden (`kanban-ebenen-montage-planner-nuriva-befund.md`, `planner-kanban-zuordnung-hart-geprueft.md`) und heute wurde der **Rückfluss-Link neu gebaut** (Migration `2026_07_02_120000`). Dieses Doc bildet den **aktuellen** Stand ab und markiert, wo es von den älteren Docs abweicht.

---

## 0. Kurzfazit (der Kernbefund vorweg)

**Die vier Ebenen sind NICHT sauber verkettet — die Kette bricht bereits zwischen Ebene 1 (Phase) und Ebene 2 (Aufgabe), und Ebene 4 (Arbeitsschritt) existiert nur als ungenutzte Anlage.** Konkret:

1. **PHASE ist DREIFACH modelliert** und die Instanzen sind untereinander **nicht per FK verbunden**: `lead_stages` (9 Zeilen, live, das eigentliche Board), `task_phases` (13 Zeilen, das Template — mit *leeren* Brücken-Spalten zur Phase), und `offer_kanban_stages` (30 Zeilen, gefüllt, eigenes Angebots-/Auftrags-Kanban mit ganz anderer Granularität).
2. **Der Träger der Phase am Gewerk ist ein STRING, kein FK:** `lead_product_lists` hat **keine** `lead_stage_id`-Spalte — die Board-Zuordnung läuft über den Text `lead_product_lists.status` → `normalizeStage()` → `lead_stages.key`. Die Vorkontext-Doc-Behauptung „positioniert über `lead_product_lists.lead_stage_id`" ist damit **falsch/veraltet** (siehe §2).
3. **Phase → Aufgabe ist real über PRODUKT verkettet, nicht über Phase:** Das Template (`task_phases`) hängt sauber am Produkt (`product_id`), aber seine Phase-Brücken `lead_stage_id`/`stage_id` sind **live 0 von 13 gefüllt** → die Aufgaben-Vorlage kennt ihre Phase faktisch nicht.
4. **Arbeitsschritt (Ebene 4) ist angelegt, aber tot:** `phase_activities.parent_id` (Selbst-Referenz) ist der vorgesehene Arbeitsschritt-Mechanismus, aber **live 0 von 49** genutzt — alle Aufgaben sind flach. Eine zweite, halb-deprecatete Repräsentation (`task_sub_tasks`) ist **leer (0)**.
5. **Kanban ist keine eigene Tabelle, sondern eine SICHT** — ein Board-Rendering (`admin/kanban/kanban.blade.php`, 5.071 Z.) über `lead_stages`-Spalten mit `lead_product_lists` als Karten; die per-Gewerk-Aufgaben (`kanban_lead_tasks`) sind ein *separates* Drawer-System, **live leer (0)**.

**Kleinster sinnvoller erster Baustein (nur benannt, NICHT gebaut):** die eine bereits vorhandene, aber ungenutzte FK-Brücke **`task_phases.lead_stage_id`** (Spalte existiert, Model-Relation existiert, live 0 gefüllt) tatsächlich befüllen/nutzen — das schließt den einzigen Bruch, der die Vorlage an die Phasen-Achse hängt, ohne Schema-Neubau. Details §6.

---

## 1. DIE VIER EBENEN — Tabellen (DESCRIBE, Live-Zeilen, Zweck)

Live-Existenz + Zeilenzahl aller Kandidaten (lokale DB, `php artisan tinker`, 2026-07-02):

| Tabelle | existiert | Live-Zeilen | Rolle-Kandidat |
|---|---|---:|---|
| `lead_stages` | JA | **9** | PHASE (Board-Spalten) — **live** |
| `lead_stage_sub_stages` | JA | **0** | UNTER-PHASE (Sub-Spalten) — Struktur da, **leer** |
| `task_phases` | JA | **13** | PHASE im Template — **live** |
| `phase_activities` | JA | **49** | AUFGABE im Template — **live** |
| `phase_sections` | JA | **13** | Prozess-Variante über Phase — **live** |
| `task_sub_tasks` | JA | **0** | ARBEITSSCHRITT (alt) — **leer** |
| `stages` | JA | **0** | Stage-Legacy — **leer/dormant** |
| `phase_stages` | JA | **0** | Stage-Legacy — **leer/dormant** |
| `customer_stages` | JA | **0** | Stage-Legacy — **leer/dormant** |
| `offer_kanban_stages` | JA | **30** | PHASE (Angebot/Auftrag-Kanban) — **live, konkurrierend** |
| `kanban_lead_tasks` | JA | **0** | AUFGABE-Instanz je Gewerk (Büro) — Struktur da, **leer** |
| `planner_items` | JA | **0** | AUFGABE/Schritt-Instanz (Feld) — Struktur da, **leer** |
| `planner_plans` | JA | **0** | Planner-Container — **leer** |
| **`customer_phase_lists`** | **NEIN (MISSING)** | — | frühere Fortschrittsliste — **gedroppt** (bestätigt) |

> **`customer_phase_lists` bestätigt tot:** `Schema::hasTable('customer_phase_lists')` = **false**. Alle Referenzen in Code sind Legacy/tot (deckt sich mit dem Glossar-Hinweis „kürzlich gedroppt"). Wird in diesem Doc nicht mehr als Kandidat geführt.

### 1.1 PHASE-Ebene

**`lead_stages`** (Board-Spalten) — `DESCRIBE`:
```
id (PK) | key (UNIQUE) | name | color | icon | sort_order | is_default | is_protected | is_closed | is_active | timestamps | deleted_at
```
**Live-Inhalt (alle 9, nach sort_order):**
```
10 lead       Lead        prot=1 closed=0
20 offer      Angebot     prot=1 closed=0
30 follow_up  Nachfassen  prot=1 closed=0
40 accepted   Annehmen    prot=1 closed=0
50 deal       Auftrag     prot=1 closed=0
60 project    Montage     prot=1 closed=0
70 completed  Abschluss   prot=1 closed=1
80 archive    Archive     prot=1 closed=1
90 junk       Junk        prot=1 closed=1
```
**Zweck:** dynamisch pflegbare Spalten des Lead-Kanbans. **Befund gegen Weiche 1:** Das sind **9** Stufen, NICHT die 6 verbindlichen Phasen. Abweichungen: `follow_up`/`accepted` sind Zwischenstufen (in Weiche 1 bewusst *Übergänge*, keine Phasen); **`Montage` heißt hier key=`project`**; **die Phase „Abnahme" FEHLT ganz**; `archive`/`junk` sind Zustände, keine Phasen (Weiche 1 sagt „Archiviert ist ein Zustand, keine Phase"). → **`lead_stages` bildet die 6 Weiche-1-Phasen heute NICHT ab.** *(Der Seeder `LeadOverviewController` um Z. 2990 setzt genau diese 9 Keys als Default.)*

**`lead_stage_sub_stages`** (Unter-Phasen) — `DESCRIBE`:
```
id (PK) | lead_stage_id (MUL) | key | name | color | icon | sort_order | is_default | is_active | timestamps | deleted_at
```
**Zweck:** je Hauptphase eigene Unterspalten (`lead_stage_id` = FK zur Hauptphase). **Live: 0 Zeilen** → die zweite Kanban-Ebene ist **strukturell angelegt, aber unbespielt**. FK `lead_stage_sub_stages.lead_stage_id → lead_stages.id` verifiziert (information_schema).

**`task_phases`** (PHASE im Template) — relevante Spalten aus `DESCRIBE`:
```
id | product_id (MUL) | section_id (MUL) | phase_name | description | sort_order
| stage (varchar, DEFAULT 'project') | stage_id (MUL) | lead_stage_id (MUL) | lead_sub_stage_id (MUL)
| version | status | order | timestamps | deleted_at
```
**Zweck:** die *definitorische* Prozess-Vorlage je Produkt („aus welchen Phasen besteht der Ablauf für Produkt X"). Menü Konfiguration → „Arbeitsschritte" (`Phase/TaskPhaseController`). **Kritischer Live-Befund (§2/§3):** von 13 Zeilen haben **13× `stage='project'`**, **0× `lead_stage_id`**, **0× `lead_sub_stage_id`**, **0× `stage_id` (alle NULL)** → die drei Brücken-Spalten zur Phasen-Achse sind komplett leer.

### 1.2 AUFGABE-Ebene

**`phase_activities`** (AUFGABE im Template) — relevante Spalten:
```
id | phase_id (MUL, →task_phases) | product_id | section_id | lead_stage_id | lead_sub_stage_id
| parent_id (MUL, →self) | copy_from | stage_id | title | duration (TIME) | duration_type
| description | notes | status | photo | priority | percent | sort_order | timestamps | deleted_at
```
**Zweck:** die Aufgaben je Phase des Templates (`phase_id → task_phases`). **Live: 49 Zeilen.** Arbeitsschritt-Mechanismus `parent_id` (Selbst-FK) vorhanden. **Live-Befund:** **0 von 49** haben `parent_id` gesetzt, **0 von 49** haben `lead_stage_id` → alle Aufgaben sind flach und phasen-achsen-los.

**`kanban_lead_tasks`** (AUFGABE-Instanz je Gewerk, Büro) — relevante Spalten:
```
id | lead_product_list_id | customer_id | alternative_id | product_id
| lead_stage_id | lead_sub_stage_id | task_phase_id | phase_activity_id
| title | description | internal_note | is_manual | is_scheduled | photo_required
| status (DEFAULT 'open') | estimated_minutes | planned_start_at | planned_end_at | done_at
| created_by_employee_id | performer_employee_id | scheduled_by_employee_id | done_by_employee_id
| meta (json) | timestamps | deleted_at
```
**Zweck:** die konkrete, verfolgbare Büro-Aufgabe je Gewerk (Kanban-Drawer im Kundenprofil), erzeugt aus dem Template. **Live: 0 Zeilen.** Trägt gleichzeitig `task_phase_id` + `phase_activity_id` (Herkunft aus Template) **und** `lead_stage_id`/`lead_sub_stage_id` (Phasen-Position) — siehe §2 zur Frage, wie diese zusammenspielen.

**`planner_items`** (AUFGABE/Schritt-Instanz, Feld/Montage) — relevante Spalten (inkl. der NEUEN):
```
id | plan_id (→planner_plans) | client_uid | source_type (MUL) | source_id (MUL)
| kanban_lead_task_id (MUL)  ← NEU (Migration 2026_07_02_120000)
| title | category | description | duration_minutes (DEFAULT 60) | status (DEFAULT 'open')
| done_at | done_by_employee_id | planned_start_at | planned_end_at | sort_order | meta (json)
| started_at | paused_at | stopped_at | last_status_* | timestamps | deleted_at
```
**Zweck:** die Feld-Ausführungs-Liste (Nuriva-angebunden), Aggregat aus `source_type` = `phase_activity | task_phase | appointment | ticket_task | personal_task | master_set | manual`. **Live: 0 Zeilen.** **Container** `planner_plans` (0 Zeilen): trägt `stage` (varchar 80, z. B. `montage`/`inbetriebnahme`), `customer_id → new_leads`, `project_id → lead_product_lists`.

### 1.3 ARBEITSSCHRITT-Ebene

Zwei konkurrierende Repräsentationen, **beide live leer**:
- **`phase_activities.parent_id`** (Selbst-FK, Model-Relationen `parent()`/`children()` vorhanden in `app/Models/PhaseActivities.php:69–76`) — der *vorgesehene* Weg. **Live 0/49 genutzt.**
- **`task_sub_tasks`** (`…060958`, halb-deprecated) — **Live 0 Zeilen.**

→ **Die Arbeitsschritt-Ebene existiert heute nur als Anlage, nicht als gelebte Struktur.** „Aufgabe hat Arbeitsschritte" ist im Datenmodell möglich (`parent_id`), wird aber faktisch nicht praktiziert.

### 1.4 Wo ist „Kanban"?

**Kanban ist keine eigene Tabelle, sondern eine SICHT (Board-Rendering).**
- **Board-Controller/View:** `Customer/Kanban/LeadOverviewController::kanban` (`:219`) rendert `return view('admin.kanban.kanban', …)` (`:415`, im `sed`-Ausschnitt Z. 197). Die Blade `admin/kanban/kanban.blade.php` ist **5.071 Zeilen**. Daten-Feed per AJAX: `LeadOverviewController::kanbanFeed` (`:444`, Route `GET /lead/kanban/feed`).
- Es gibt **kein** `kanban_boards`/`kanban_columns`-Tabellenpaar. Die „Spalten" *sind* `lead_stages`; die „Karten" *sind* `lead_product_lists`. Der Name `kanban_lead_tasks` bezeichnet **nicht** das Board, sondern das per-Gewerk-Aufgaben-Drawer.

---

## 2. DIE BEZIEHUNGEN — FKs / *_id-Spalten, die Verkettung wörtlich

**FK-Belege (information_schema, live):**

`kanban_lead_tasks` FKs:
```
lead_product_list_id -> lead_product_lists(id)     task_phase_id     -> task_phases(id)
customer_id          -> new_leads(id)              phase_activity_id -> phase_activities(id)
alternative_id       -> lead_alternative_adds(id)  lead_stage_id     -> lead_stages(id)
product_id           -> article_groups(id)         lead_sub_stage_id -> lead_stage_sub_stages(id)
+ 4 employee-FKs
```
`planner_items` FKs (nur):
```
plan_id -> planner_plans(id)     last_status_changed_by_employee_id -> employees(id)
```
→ **`planner_items.kanban_lead_task_id` hat KEINEN echten FK-Constraint** (nur `->index()`, Migration `2026_07_02_120000` Z. 21). Die Spalte existiert und wird gefüllt (siehe §3), ist aber referenz-integritätslos.

### 2.1 Die Verkettung Phase → Aufgabe → Arbeitsschritt (wörtlich)

**Template-Kette (sauber, aber produktbasiert):**
- `phase_activities.phase_id → task_phases.id` (FK verifiziert; Model `TaskPhase::activities()` = `hasMany(PhaseActivities, 'phase_id')`, `app/Models/TaskPhase.php:41`).
- `phase_activities.parent_id → phase_activities.id` (Arbeitsschritt; Model `parent()`/`children()`).
→ **task_phases (Phase) → phase_activities (Aufgabe) → phase_activities.children (Arbeitsschritt)** ist als Hierarchie **modellierbar und im Model verdrahtet**. ABER: die Aufgabe kennt ihre **Phasen-Achse** (die 6/9 Lead-Phasen) nur über `task_phases.lead_stage_id` — und das ist **live leer**.

**BRUCH 1 — Phase-Achse ↔ Template:** `task_phases.lead_stage_id` = 0/13 gefüllt, `task_phases.stage_id` = 0/13 (alle NULL), `task_phases.stage` = 13× `'project'` (String-Default). Das Template hängt real **nur über `product_id`** an einem Gewerk. Beleg im Board-Code: `LeadOverviewController::kanbanFeed` matcht Template-Rows über `whereIn('tp.product_id', $productIds)` (`:1538`) und filtert *nur wenn gefüllt* auf Phase: `if (!empty($row->lead_stage_id) && (int)$row->lead_stage_id !== (int)$stageId) return false;` (`:1574`) — bei leerem `lead_stage_id` greift der Phasen-Filter also **nie**, jede Template-Phase des Produkts wird als Kandidat behandelt. Dasselbe im Kanban-Drawer: `KanbanLeadTaskController::loadTemplateTasks` matcht `->where('product_id', …)` + `->where(lead_stage_id = X OR stage_id = X)` (`:651–656`) — mit beiden Spalten leer trägt dieser OR-Zweig live nicht.

**BRUCH 2 — Phase-Achse ↔ Gewerk (die Karte):** `lead_product_lists` hat **keine `lead_stage_id`-Spalte** (verifiziert: `Schema::hasColumn('lead_product_lists','lead_stage_id')` = false). Die Karte trägt ihre Hauptphase als **String** in `lead_product_lists.status` (Live-Verteilung: `lead`×23, `offer`×10, `deal`×8, `follow_up`×5, `accepted`×4, `project`×2). Die Board-Zuordnung übersetzt diesen String: `normalizeStage($lead->stage ?? $lead->status)` → `stageMap()` → `lead_stages.key` (`LeadOverviewController:1561–1562`, `stageMap()` bei `:2xxx`; u. a. `'montage'|'projekt'|'project' => 'project'`, `'auftrag'|'deal' => 'deal'`). Die **Unter**-Phase dagegen läuft per FK: `lead_product_lists.lead_stage_sub_stage_id → lead_stage_sub_stages.id` (Spalte existiert). → **Asymmetrie: Hauptphase = String-Key, Unter-Phase = FK.** Damit ist die im Vorkontext (`kanban-ebenen-montage-planner-nuriva-befund.md` §1) behauptete Positionierung „über `lead_product_lists.lead_stage_id`" für die **Hauptphase falsch** — es gibt keine solche Spalte; nur die Sub-Phase ist FK-verankert.

**Aufgaben-Instanz ↔ Phase (kanban_lead_tasks):** Die Büro-Karte kopiert beim Anlegen die Phasen-Position hinein: `KanbanLeadTaskController::storeFromTemplate` setzt `'lead_stage_id' => resolveLeadStageId($leadProduct)` + `'lead_sub_stage_id' => $leadProduct->lead_stage_sub_stage_id` + `'task_phase_id' => $phase->id` + `'phase_activity_id' => $activity?->id` (`:499–502`). `resolveLeadStageId` (`:983`) übersetzt wieder den **String** `status/stage` → `lead_stages.key` → id (Fallback: `is_default`-Stage). → Die Karte trägt also **redundant beide Sichten** (Phase-FK + Template-FK), abgeleitet aus dem String-Status des Gewerks. **Wie sie zusammenspielen:** `task_phase_id`/`phase_activity_id` = *Herkunft* (welche Template-Vorlage), `lead_stage_id`/`lead_sub_stage_id` = *Position* (in welcher Pipeline-Phase) — zwei unabhängige Achsen, per Copy zum Anlege-Zeitpunkt gefüllt, danach **nicht synchron gehalten** (kein Trigger, der `lead_stage_id` der Karte nachzieht, wenn das Gewerk die Phase wechselt — **NICHT VERIFIZIERT** ob es einen gibt; kein solcher gefunden).

**BRUCH 3 — Arbeitsschritt:** live nicht existent (§1.3). „Aufgabe hasMany Arbeitsschritte" ist im Model da, aber 0/49 genutzt → in der Praxis ist die Hierarchie **flach: Phase → Aufgabe**, ohne dritte Ebene.

### 2.2 Antwort auf die Leitfrage der Beziehungen

- **Phase hasMany Aufgaben?** Im Template JA (`task_phases hasMany phase_activities`), aber die Phase des Templates ist **nicht** an die Board-Phase (`lead_stages`) gebunden (leere Brücke). Am Gewerk läuft die Phase über String-Status, nicht FK.
- **Aufgabe hasMany Arbeitsschritte?** Modellierbar (`phase_activities.parent_id`), **live 0×** → praktisch **flach**.
- **Insgesamt:** eher **flach + produktbasiert** als sauber phasen-hierarchisch verkettet.

---

## 3. MEHRERE WAHRHEITEN — konkurrierende Modellierungen je Ebene

### 3.1 PHASE — dreifach modelliert

| # | System | Live-Zeilen | geroutet? | Zweck | Bindung zur Phase-Achse |
|---|---|---:|---|---|---|
| A | **`lead_stages`** (+`lead_stage_sub_stages`) | 9 (+0) | JA (`/lead/kanban*`) | die 9 Board-Spalten (Lead-Kanban) | Gewerk via **String** `status` |
| B | **`task_phases`** | 13 | JA (`task_phase.index`, Planner-Sync) | Phasen im Produkt-Template | `lead_stage_id`/`stage_id` **live leer** |
| C | **`offer_kanban_stages`** | 30 | JA (`/…/kanban-stages`) | Angebots-/Auftrags-Kanban (eigene Feinstufen) | `document_status` = `offer`/`deal`; **kein Träger** auf `offers`/`deals` gefunden |

- **A live, geroutet** = die verbindliche Board-Phase (Yama hat `lead_stages` als Phasen-System bestätigt, Weiche 1). Aber 9 statt 6, „Abnahme" fehlt, „Montage"=key `project` (§1.1).
- **B live, geroutet**, aber als *Phasen-Achse* faktisch **entkoppelt** (Brücken 0/13). B trägt real die **Aufgaben-Vorlage**, nicht die Pipeline-Position.
- **C live, gefüllt (30), geroutet**, aber **schwebend**: `offer_kanban_stages` hat `document_status` in {`offer`×13, `deal`×17} und liefert Feinstufen wie `lead_anfrage`/`erstkontakt`/`beratung_geplant`/`beratung_durchgefuehrt`/`technische_pruefung`. **Weder `offers` noch `deals` haben eine `*_stage`/`*_kanban`-Spalte** (verifiziert: „KEINE stage/kanban-Spalte"), die diese Keys trägt. → C ist ein **eigener Stage-Katalog** (CRUD-Controller vorhanden), dessen **Anwendung auf echte Dokumente im Backend nicht belegt** ist (evtl. rein clientseitig genutzt — **NICHT VERIFIZIERT**). C ist eine **konkurrierende, feinere Phasenzählung** für Angebot/Auftrag, parallel zu A's grober Stufe `offer`/`deal`.

> **Dormant/tot (Phase-Legacy):** `stages` (0), `phase_stages` (0), `customer_stages` (0) — leer, keine Live-Rolle. `customer_phase_lists` — **gedroppt (Tabelle fehlt)**. `TaskPhase::stage()` = `belongsTo(Stage::class,'stage_id')` zeigt auf die **leere** `stages`-Tabelle → tote Model-Relation.

### 3.2 AUFGABE — dreifach modelliert (aber alle live leer)

Dieselbe `phase_activity` wird in drei Instanz-Systemen zu „Status/Fortschritt" gemacht:
- **`kanban_lead_tasks`** (Büro) via `phase_activity_id` — **0 Zeilen.**
- **`planner_items`** (Feld/Nuriva) via `source_type='phase_activity'`+`source_id` — **0 Zeilen.**
- (früher `customer_phase_lists` via `activities_id`) — **Tabelle gedroppt.**

→ Die im Vorkontext beschriebene **Dreifach-Instanz ist auf zwei geschrumpft** (customer_phase_lists weg). Beide verbleibenden sind **strukturell da, live unbespielt** — die „mehreren Wahrheiten" der Aufgabe sind heute ein **Schema-Risiko, kein Daten-Risiko** (0 Zeilen → nichts kann divergieren, solange leer).

**NEU seit den Vorkontext-Docs — die Aufgaben-Instanzen wurden VERBUNDEN:** `planner_items.kanban_lead_task_id` (Migration `2026_07_02_120000`, heute) schafft den direkten Link Büro-Karte ↔ Feld-Item, den `planner-kanban-zuordnung-hart-geprueft.md` noch als **fehlend** dokumentierte. Zwei Schreibpfade gefunden:
1. **Beim Sync (Feld←Büro):** `PlannerPlanController` baut `cardMap = kanban_lead_tasks[lead_product_list_id].pluck('id','phase_activity_id')` (`:494–500`) und schreibt `'kanban_lead_task_id' => $cardMap[$activityId] ?? null` in jedes `phase_activity`-planner_item (`:565`).
2. **Beim Karten-Anlegen (Büro→Feld, best-effort):** `KanbanLeadTaskController::storeFromTemplate` verlinkt nach dem Anlegen die passenden planner_items zurück: `DB::table('planner_items')->where('source_type','phase_activity')->where('source_id', $task->phase_activity_id)->whereIn('plan_id', planner_plans[project_id=lead_product_list_id])->update(['kanban_lead_task_id' => $task->id])` (`:536–541`).

> **Wichtige Einschränkung (die alte Warnung gilt weiter):** Der Link nutzt weiterhin das Paar `(lead_product_list_id, phase_activity_id)` als Brücke, und **es gibt weiterhin keinen Unique-Constraint** darauf (verifiziert: Indizes auf `kanban_lead_tasks` sind nur `PRIMARY` + zwei FK-`index()` auf `task_phase_id`/`phase_activity_id`, **kein unique**) und `storeFromTemplate` dedupliziert nicht (`create()`, `:494`). → `cardMap` ist ein `pluck('id','phase_activity_id')`, das bei **mehreren Karten je phase_activity nur die letzte** behält (stiller Datenverlust der Mehrfachheit). Der Link ist gebaut, aber die **Eindeutigkeits-Voraussetzung ist NICHT geschaffen** — das ist genau der Punkt, den `architektur-entscheidungen.md` (Weiche 6, „Rück-Richtung") als Vorbedingung nannte. *(Live 0 Zeilen → heute kein Schaden; das Risiko ist latent.)*

### 3.3 ARBEITSSCHRITT — doppelt modelliert
`phase_activities.parent_id` **vs.** `task_sub_tasks` — beide leer (§1.3). Keine live Wahrheit; reine Anlage-Doppelung.

---

## 4. KANBAN-DARSTELLUNG — welcher Controller/View, wie das Mapping läuft

**Board (Ebene 1+2):**
- **Controller:** `Customer/Kanban/LeadOverviewController::kanban` (`:219`) → View `admin.kanban.kanban` (`:415`). Daten-Feed: `kanbanFeed` (`:444`, Route `GET /lead/kanban/feed`).
- **Spalten-Aufbau (`:230–272`):** `leadStagesForUi()` lädt `lead_stages` (mit eingebetteten `subStages`) → `stageMeta[key] = { id,key,name,color,icon,sort_order,…, sub_stages:[…] }`. Für das Board werden `junk`+`ticket` per `reject()` ausgeblendet (`:266–272`). → **Spalten = `lead_stages`, Unter-Spalten = `lead_stage_sub_stages` eingebettet.**
- **Karten → Spalte (`kanbanFeed`, :1561–1562):** je Gewerk (`lead_product_lists`) `stageKey = normalizeStage($lead->stage ?? $lead->status ?? 'lead')`; `stageId = $stageIdsByKey[$stageKey]`. → **String-Zuordnung**, nicht FK (§2).
- **Aufgaben je Karte (`kanbanFeed`, :1506–1601):** Die Karte zeigt eine „aktuelle/nächste Aufgabe": gespeicherte `kanban_lead_tasks` (`savedByLeadProduct`) → offene/erledigte gesplittet (`:1552–1553`); wenn keine offene existiert, wird eine **Template**-Aufgabe berechnet (`templateRows` aus `task_phases LEFT JOIN phase_activities`, `:1506–1545`), gefiltert je `product_id` (+ Phase *nur wenn `lead_stage_id` gefüllt*, was live nie). → **Das Board mappt real: Spalte=Lead-Phase (String), Karte=Gewerk, „Aufgabe"=nächste offene kanban_lead_task ODER berechnete Template-Aufgabe je Produkt.**

**Aufgaben-Drawer (Ebene 2+3, im Kundenprofil):**
- **Controller:** `Customer/Kanban/KanbanLeadTaskController` (`context`:29, `storeFromTemplate`:427, `storeManual`, `updateStatus`, `destroy` — Routen `web.php:971–1009`).
- `context` (`:29`) liefert pro Gewerk: `templates` (aus `loadTemplateTasks`, §2), `tasks` (gespeicherte `kanban_lead_tasks`), `field_progress` (aus `planner_items`, s. u.), `employees`.
- **Arbeitsschritt im Drawer:** wird als flache Aktivitätsliste je Phase gezeigt (`loadTemplateTasks` gibt `phase → activities[]`, keine `children`-Ebene) → **die dritte Ebene erscheint im UI nicht** (weil `parent_id` live leer).

**Feld-Ausführung → Board-Rückkopplung:**
- `KanbanLeadTaskController::montageFieldProgress` (`:116`) rechnet den **Montage-Fortschritt aus `planner_items`** (Plan `stage='montage'`, `source_type='phase_activity'`, nach Anzahl) — bewusst NICHT aus `kanban_lead_tasks` (Kommentar `:110–115`, deckt sich mit `architektur-entscheidungen.md` „Progressbar Weg A"). → Das ist die eine Stelle, an der die **Feld-Wahrheit (`planner_items`)** in die Büro-Sicht (Drawer) zurückgespiegelt wird.

**Angebots-/Auftrags-Kanban (separat):**
- `Customer/Offer/OfferKanbanStageController` (`index`/`store`/`update`/`destroy`/`reorder`, Routen `web.php:3403–3417`) verwaltet die `offer_kanban_stages` je `document_status` (`offer`/`deal`). Eigenes Board, eigene Feinstufen (§3.1-C).

---

## 5. VERHÄLTNIS ZUM QUALIFIKATIONS-STRANG (B1) — reine Beobachtung

> Der Parallel-Agent baut `phase_activities.required_qualification_id` (eine Mindest-Qualifikation je Arbeitsschritt, FK auf `position_qualifications`). **Ich habe die Kollisionsdateien NICHT editiert.**

**Beobachtungen (nur notiert, für spätere Zusammenführung):**
1. **B1 ist heute NOCH NICHT in der DB:** `Schema::hasColumn('phase_activities','required_qualification_id')` = **false**. Die Ziel-Tabelle `position_qualifications` **existiert** (Migration `2026_02_12_133229`). → B1 ist im Bau, aber die Spalte ist zum Analyse-Zeitpunkt nicht vorhanden.
2. **Berührungspunkt = `phase_activities` (Ebene AUFGABE/ARBEITSSCHRITT):** B1 hängt genau an der Tabelle, die in dieser Kanban-Kette die **Aufgabe** trägt (und via `parent_id` den Arbeitsschritt tragen *würde*). B1 qualifiziert also die **Aufgabe-Zeile**, nicht das Kanban/die Phase.
3. **Interaktion mit der Kanban-Struktur:** gering, aber vorhanden. Die Kanban-Ebenen lesen `phase_activities` an mehreren Stellen (`kanbanFeed` Template-JOIN `:1509`; `KanbanLeadTaskController::loadTemplateTasks` `:638`; Planner-Sync `pmoUpsertTemplatePlannerItem` je `phase_activity`). Wenn B1 eine neue Spalte `required_qualification_id` ergänzt, ist das **additiv** — die bestehenden `SELECT`/`with()` brechen nicht. **ABER:** sobald der Arbeitsschritt-Mechanismus (`parent_id`) real genutzt würde (heute 0/49), stellt sich die Frage, ob die Qualifikation auf **Aufgabe** oder **Arbeitsschritt** (Kind) gehört — das ist dieselbe offene Ebenen-Frage wie hier. → **Für die Zusammenführung vormerken:** B1 (Qualifikation je phase_activity) und die hier befundete flache/ungenutzte Arbeitsschritt-Ebene betreffen **dieselbe Tabelle** und sollten konsistent entschieden werden (Qualifikation je Aufgabe vs. je Arbeitsschritt).
4. **Kein Konflikt mit dem Rückfluss-Link:** B1 fasst `planner_items`/`kanban_lead_tasks` nicht an; der heute gebaute `kanban_lead_task_id`-Link und B1 sind **orthogonal**.

---

## 6. URTEIL + kleinster erster Baustein

### 6.1 Ist Phase → Aufgabe → Arbeitsschritt sauber modelliert?

**NEIN — gebrochen und mehrfach, in dieser Reihenfolge der Schwere:**

1. **PHASE ist die am stärksten zersplitterte Ebene:** drei live/geroutete Systeme (`lead_stages` 9, `task_phases` 13, `offer_kanban_stages` 30) **ohne FK-Verbindung untereinander**, plus 3 leere Legacy-Stage-Tabellen. Die eine „Board-Wahrheit" (`lead_stages`) bildet die 6 Weiche-1-Phasen **nicht** ab (9 Stufen, „Abnahme" fehlt, Montage=key `project`).
2. **Phase → Aufgabe ist NICHT phasen-verkettet, sondern produkt-verkettet:** Template hängt an `product_id`; die Phase-Brücken `task_phases.lead_stage_id`/`stage_id` sind **live 0/13**. Der Gewerk-Träger der Hauptphase ist ein **String** (`lead_product_lists.status`), kein FK (keine `lead_stage_id`-Spalte).
3. **Aufgabe → Arbeitsschritt ist tot:** `phase_activities.parent_id` live 0/49; `task_sub_tasks` leer. Die dritte Ebene ist Anlage, keine Praxis.
4. **Aufgaben-Instanzen (Büro/Feld) sind strukturell da, live leer** — und seit heute per `kanban_lead_task_id` verbunden, **aber ohne Eindeutigkeits-Garantie** (kein Unique auf `(lead_product_list_id, phase_activity_id)`, kein Dedup).

**Was FUNKTIONIERT (fairerweise):** Die *Template-interne* Kette `task_phases → phase_activities (→ parent_id)` ist sauber per FK und Model-Relation modelliert; das Board-Rendering ist robust (defensive `Schema::has*`-Guards überall); der Rückfluss-Link (Feld→Büro) ist frisch verdrahtet. Das Fundament ist da — die **Klammer zwischen Phase-Achse (lead_stages) und Template (task_phases) fehlt gefüllt**, und die Karte hängt per String statt FK an der Phase.

### 6.2 Kleinster sinnvoller erster Baustein (nur benannt, NICHT gebaut)

**Kandidat 1 (kleinster, schließt Bruch 1): `task_phases.lead_stage_id` befüllen/nutzen.** Die Spalte existiert, die Model-Relation `TaskPhase::leadStage()` existiert (`app/Models/TaskPhase.php:79`), der Board-/Drawer-Code liest sie bereits defensiv (`:1517`, `:654`) — nur die **Daten fehlen** (0/13). Wird sie je Template-Phase gesetzt, hängt die Aufgaben-Vorlage sauber an der 9-Phasen-Achse, **ohne Schema-Änderung, ohne neuen Code-Pfad** (die Filter greifen dann automatisch). Risiko minimal (additive Datenpflege). — *Voraussetzung/Abhängigkeit: hängt an Weiche 1 (welche Phasen gelten) — solange `lead_stages` 9 statt 6 Stufen führt und „Abnahme" fehlt, würde man die Vorlage an eine noch nicht Weiche-1-konforme Achse binden.*

**Kandidat 2 (strukturell sauberer, größer): den Gewerk-Phasenträger von String auf FK heben** — eine `lead_product_lists.lead_stage_id`-Spalte einführen und den String-`status` daraus ableiten. Das würde Bruch 2 schließen (Karte per FK an Phase), berührt aber viele Schreibpfade (jeder `status`-Setzer) → **kein „kleinster" Schritt**, gehört in den Weiche-1-Umbau (Etappe 4).

**Empfehlung zur Reihenfolge (ohne Fakten zu erzwingen):** Der ehrlich *kleinste* konsolidierende Schritt ist **Kandidat 1**, aber er ist **erst sinnvoll, nachdem Weiche 1 die Phasenliste `lead_stages` auf die 6 verbindlichen Phasen gebracht hat** (sonst befüllt man die Brücke gegen eine falsche Achse). → **Reihenfolge: (0) `lead_stages` an Weiche 1 angleichen [6 Phasen, „Abnahme" ergänzen, Montage-Key klären] → (1) `task_phases.lead_stage_id` befüllen → später (2) Karte-Phase auf FK.** Der Schritt „(0)" ist eine Weiche-1-Bau-Entscheidung, kein reiner Baustein.

---

## 7. Gelesen / NICHT gelesen (ehrlich)

**Live abgefragt (DESCRIBE + Counts + FKs, `php artisan tinker`, information_schema):**
- Existenz/Zeilen aller 14 Kandidaten-Tabellen; `DESCRIBE` für `lead_stages`, `lead_stage_sub_stages`, `task_phases`, `phase_activities`, `phase_sections`, `kanban_lead_tasks`, `planner_items`, `planner_plans`, `offer_kanban_stages`.
- Live-Inhalte: `lead_stages` (alle 9), `task_phases.stage`/`lead_stage_id`/`lead_sub_stage_id`/`stage_id`-Verteilung, `phase_activities.parent_id`/`lead_stage_id`-Zählung, `lead_product_lists.status`-Verteilung, `offer_kanban_stages.document_status`-Verteilung + Beispielzeilen.
- FKs auf `kanban_lead_tasks` + `planner_items`; Unique-Index-Check `kanban_lead_tasks`.
- `Schema::hasColumn` für `lead_product_lists.lead_stage_id`/`status`/`stage`/`lead_stage_sub_stage_id`, `phase_activities.required_qualification_id`, `offers`/`deals` stage/kanban-Spalten.

**Code vollständig/ausschnittsweise gelesen:**
- `app/Models/KanbanLeadTask.php` (komplett), `app/Models/TaskPhase.php` (Relationen), `app/Models/PhaseActivities.php` (Relationen inkl. `parent`/`children`).
- `KanbanLeadTaskController.php`: `context` (:29–107), `montageFieldProgress` (:116+), `storeFromTemplate` (:490–549), `loadTemplateTasks` (:632–692), `resolveLeadStageId`/`stageLabel` (:983–1011).
- `LeadOverviewController.php`: `kanban` (:219–288, return :415), `kanbanFeed` Template-JOIN + Karten-Mapping (:1505–1601), `normalizeStage`/`stageMap` (:2757+ / stageMap-Body), Seeder-Defaults (:2990).
- `PlannerPlanController.php`: Sync-Kern `pmoSyncTemplateItems` (:470–568) inkl. `cardMap` (:494–500) + `kanban_lead_task_id`-Write (:565).
- `OfferKanbanStageController.php` (:1–60), Routen `web.php` (886–894, 971–1009, 2139, 3403–3417).
- Migrationen (Existenz/Kern-Zeilen): `2026_07_02_120000_add_kanban_lead_task_id_to_planner_items` (:19–30), `2026_06_05_222249_create_kanban_lead_tasks_tables` (Grep FK/unique).

**NICHT (vollständig) gelesen — mögliche Lücken:**
- `admin/kanban/kanban.blade.php` (**5.071 Z.**) nur per Grep nach Strukturmarkern — die **exakte visuelle Zuordnung Sub-Spalten/Drawer im JS** ist nicht Zeile-für-Zeile belegt. Wie das Frontend `sub_stages` rendert (Swimlane vs. Drill-down) = **NICHT VERIFIZIERT**.
- `LeadOverviewController.php` (**286 KB**) nur gezielt gegrept/ausschnittsweise — ein abweichender Phasen-Zuordnungspfad an anderer Stelle ist **nicht ausgeschlossen**.
- `PlannerPlanController.php` (~11k Z.) nur um den Sync-Kern + Grep — vollständige Kette nicht end-to-end.
- **`offer_kanban_stages`-Anwendung:** ob/wo die Feinstufen einem echten `offer`/`deal`-Datensatz zugeordnet werden (evtl. clientseitig, evtl. eigene Zuordnungstabelle) = **NICHT VERIFIZIERT** (keine Träger-Spalte auf offers/deals gefunden; JSON-`meta`/Frontend nicht inspiziert).
- **JSON-`meta`** von `kanban_lead_tasks`/`planner_items` nicht inspiziert (0 Zeilen → ohnehin leer).
- **Trigger-Frage:** ob beim Phasenwechsel eines Gewerks die `lead_stage_id` bereits gespeicherter `kanban_lead_tasks` nachgezogen wird = **kein Pfad gefunden**, aber nicht erschöpfend widerlegt.

## 8. Selbstkritik (kein „passt")

- **Alle Live-Zahlen stammen aus der lokalen DB**, die bei den Instanz-Tabellen (`kanban_lead_tasks`, `planner_items`, `planner_plans`, `lead_stage_sub_stages`) **0 Zeilen** hat. „Strukturell da, live leer" ist ein Schema-Befund; wie es sich **mit echten Daten** verhält (z. B. ob Mehrfach-Karten je phase_activity real entstehen), ist **nicht gemessen**. Die Schema-Brüche (kein Unique, String-statt-FK-Phase, leere Template-Brücken) sind dagegen **hart** (DESCRIBE/Index/Count).
- **„B fehlt in der DB" (§5)** ist ein Zeitpunkt-Befund — der Parallel-Agent kann die Spalte während/nach dieser Analyse hinzufügen. Der Befund gilt für 2026-07-02, Analyse-Zeitpunkt.
- **Die Aussage „C schwebend" (§3.1)** beruht auf „keine Träger-Spalte auf offers/deals" + CRUD-Controller — eine clientseitige oder meta-basierte Zuordnung habe ich **nicht ausgeschlossen**. Ich habe C bewusst als „konkurrierend, Anwendung unbelegt" markiert, nicht als „tot".
- **Der „kleinste Baustein" (§6.2)** ist meine **Bewertung** unter der ausdrücklichen Abhängigkeit von Weiche 1 — keine Bau-Empfehlung, und ich habe die Vorbedingung offen benannt statt sie zu überspringen.
- **Widerspruch zu einem Vorkontext-Doc offen benannt:** `kanban-ebenen-montage-planner-nuriva-befund.md` schreibt „Karten positioniert über `lead_product_lists.lead_stage_id`" — das ist nach heutigem Schema **falsch** (keine solche Spalte; Hauptphase = String). Ich stelle den aktuellen Befund über die ältere Doc-Aussage, weil er live verifiziert ist.

---

*Reine Analyse — nichts am Code/Schema geändert; einziges Schreibprodukt ist dieses Doc. Wörtliche Belege: DB-Live (DESCRIBE/Count/FK/Index, information_schema) 2026-07-02; Controller `Customer/Kanban/LeadOverviewController` (kanban:219/415, kanbanFeed:444/1505–1601, normalizeStage:2757, stageMap, Seeder:2990), `Customer/Kanban/KanbanLeadTaskController` (context:29, montageFieldProgress:116, storeFromTemplate:490–549, loadTemplateTasks:632–692, resolveLeadStageId:983), `Planner/PlannerPlanController` (pmoSyncTemplateItems:470–568, cardMap:494–500, kanban_lead_task_id:565), `Customer/Offer/OfferKanbanStageController` (:1–60); Models `KanbanLeadTask`, `TaskPhase` (activities:41, stage:54, leadStage:79), `PhaseActivities` (parent/children:69–76); Migrationen `add_kanban_lead_task_id_to_planner_items`(2026_07_02_120000), `create_kanban_lead_tasks_tables`(2026_06_05_222249); Routen web.php 886–894/971–1009/3403–3417. Querverweise: `architektur-entscheidungen.md` (Weiche 1/6), `kanban-ebenen-montage-planner-nuriva-befund.md`, `struktur-systeme-verhaeltnis-befund.md`, `planner-kanban-zuordnung-hart-geprueft.md`, `glossar.md`.*
