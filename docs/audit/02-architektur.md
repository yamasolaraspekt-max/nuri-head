# AUDIT 02 — Architektur (Phase 1B)

> **Status: FERTIG (Lese-Audit).** Rein lesend. Belege wörtlich (Datei:Zeile, DB-Zahlen). DB: 442 Tabellen, live. Weichen = Gesetz. Tiefe der Hotspot-Controller NICHT gelesen (Komplexität über Größe geschätzt).

## 1. Doppel-Wahrheiten-Register

### 1a — Status/Phase je Gewerk: 12 Spalten in EINER Zeile (P1-Architektur)
`lead_product_lists` trägt **12 konkurrierende status/stage-Spalten**: `status`, `stage`, `old_stage`, `stage_mode`, `product_stage_id`, `product_task_phase_id`, `work_status`, `stage_history`(json), `product_stage_history`(json), `lead_stage_id`, `lead_stage_sub_stage_id`, `offer_acceptance_status`. Dazu `deals` 5 (`deal_status/status/measurement_status/status_msg/project_status`), `new_leads` 3, `offers` 2. **≥6 konkurrierende Phasen-Repräsentationen in einer Zeile**, kein Constraint hält sie synchron. → deckt Weiche 1 (Statusquelle) empirisch. **Pfad:** `lead_stage_id`/`_sub_stage_id` als einzige Wahrheit; Rest belegt stilllegen (Drop je eigener Posten).

### 1b — Stage-Tabellen-Wildwuchs: 13 Tabellen, EINE echte Live-Doppelung
| Tabelle | Rows | Live-Refs | Status |
|---|--:|--:|---|
| `lead_stages` | 10 | 403 | **AKTIV = Weiche** |
| `offer_kanban_stages` | 30 | 16 | **AKTIV, live parallel** ⚠️ |
| `stages` (Alt-Templates) | 0 | 225 | Code aktiv auf leerer Tabelle |
| `phase_stages`/`customer_stages`/`customer_phase_stages`/`custom_process_stages`/`lead_stage_sub_stages` | 0 | 7–16 | dormant |
| `task_phases`/`phase_sections`/`phase_activities` | 13/13/49 | — | aktiv (Template-Ebene) |

**Kernbefund:** `offer_kanban_stages` (30 Zeilen, befüllt) läuft **live parallel** zu `lead_stages` — die einzige *tatsächlich doppelt-produktive* Stage-Quelle. `stages` (0 Zeilen) hat 225 Code-Refs — lebender Code auf leerer Alt-Tabelle (`StageController::create` schreibt aktiv, `Product/Stage/StageController.php:191`). **Pfad:** offer_kanban_stages → Sicht auf lead_stages; Stage-Model-Pfad auf lead_stages migrieren.

### 1c — Rechnung invoices vs deal_invoices: **Weiche eingehalten** ✅
`invoices` 11 Zeilen / ~453 Refs · `deal_invoices` 0 Zeilen / **nur 4 Kommentar-Zeilen** (LeadOverviewController:734, DealController:188/1129/2805). `dealInvoiceBalance()` liest `invoices.deal_id`, nicht die Alt-Tabelle. Rest: physischer `DROP deal_invoices` steht aus (CLAUDE.md).

### 1d — Kunde new_leads vs customers: **Weiche eingehalten**, Schatten latent
`new_leads` 52 / 1010 Refs · `customers` **0 Zeilen, 74 Spalten** / 98 Refs. **Kein** `Customer::create/insert` im Code — nur Leser, überwiegend `Old/`. 2 Nicht-Old-Leser: `CustomerHeatingCircuitController.php:87`, `ChecklistRoomController.php:138`. Latente 74-Spalten-Doppelung (Customer-Model-Falle, Glossar §5). **Pfad:** 2 Leser umhängen, `customers` belegt droppen.

### 1e — `projects` als parallele Ebene (Konzept-Verstoß, dormant) — s. §5.

## 2. Gott-Klassen / Hotspots (Zeilenzahl; Tiefe nicht gelesen)
**Controller (Top-10 >2400 Z.):** `NewLeadsController` **14.054** · `PlannerPlanController` **11.097** · `LeadOverviewController` **7.075** · `PersonalTaskController` **6.570** · `OverdueCenterController` 4.618 · `DailyReportController` 4.001 · `DealController` 3.952 · `OfferFolderController` 3.810 · `EmployeeController` 3.523 · `MainAppointmentController` 3.428. (Top-20 alle >2000.)
**Blades (Top >12k Z.):** `offer/configuration/offer/config.blade.php` **25.064** · `customer_profile.blade.php` **19.727** · `new_leads/customer_profile.blade.php` **19.338** · `master_sets/index.blade.php` 15.270 · `offer/folder-show.blade.php` 14.480 · `profile.blade.php` 12.394.
**Models:** unkritisch (größtes Employee.php 485 Z.; keins >500 außer diesem). **Fette Logik liegt in Controllern + Blades, nicht in Models.** Gesamt app+views = **918.555 Zeilen**.

## 3. Schichten-Disziplin — Logik/RBAC in Blades (P1-Architektur)
| Muster | Live-Blades (ohne old/copy) |
|---|--:|
| raw `DB::` in View | **81** |
| `->where(` Query-Builder in View | **120** |
| `@php`-Blöcke (gesamt) | 377 |

Wörtlich: `customer_profile.blade.php:2506` (DB::table employees), `offer/view/offer_view.blade.php:495` (**RBAC-Check `user_rolls` direkt in der View**), `deal/customer_view.blade.php:2417-2449` (3× inline DB), `offer/folder-show.blade.php:64` (`DB::raw` in View). → Geschäftslogik, N+1-Lookups und **Berechtigungsprüfungen** in Views verdrahtet.

## 4. Datenmodell-Smells
- **Status als freies varchar:** **139** `status`-Spalten varchar/char/text vs. 11 enum; **192** `%status%`-Spalten varchar gesamt. Keine DB-Wertebereichs-Absicherung → stille Divergenz bei Tippfehler. **Pfad:** kanonische Enums/Referenztabellen; Phasen → `lead_stages`.
- **Fehlbenannt:** `radiators` = **Wechselrichter-Altlast bestätigt** (Spalten `dc_nennleistung_kw`, `ac_nennspannung_v`, `anzahl_phasen`, `trafo` …; Tippfehler `dc_nennspanuung_v`, `standby_verbruahe_w`). Deckt CLAUDE.md-Warnung. Kein erschöpfender Namens-Audit über 442 Tabellen.
- **Fehlende FK-Indizes (Stichprobe 6):** `lead_product_lists.accepted_offer_folder_id`, `invoices.source_offer_detail_id`, `new_leads.moser_id` unindiziert. Kern-Tabellen sonst sauber. NICHT verallgemeinerbar (nur 6/442).

## 5. Konzept-Treue — Code gegen die Weichen
| Weiche | Verstoß | Beleg | Schwere |
|---|---|---|---|
| Objekt klammert (kein Projekt-Level) | `projects` als eigene Ebene: 31 Zeilen, `Project::customer()`→NewLeads, `::alternative()`→LeadAlternativeAdd (Kunde×Objekt×Produkt parallel zu `lead_product_lists`) | `app/Models/Project.php:41-53`; projects=31 | Mittel — **dormant im Schreibpfad** (kein `Project::create`, nur `Old/`-Leser) |
| Phase = `lead_stages` | Alt-`Stage` 225 Refs aktiv (`StageController::create` schreibt); `offer_kanban_stages` 30 Zeilen live parallel | `Product/Stage/StageController.php:191` | Mittel — echte Parallelquelle |
| Kunde = `new_leads` | `customers`-Schatten + 2 Nicht-Old-Leser | grep | Niedrig (0 Zeilen, keine Schreiber) |
| Rechnung = `invoices` | **kein Verstoß** | §1c | ✅ konform |

**Positiv/schützen:** invoices-Schiene sauber · keine customers/projects-Schreiber · deal_invoices sauber stillgelegt.

## Grenzen/Selbstkritik
Hotspot-Controller nur nach Zeilenzahl (nicht tief gelesen). Divergenz-Risiken latent (viele Dup-Tabellen 0 Zeilen) — **einzige live-doppelt-produktive Fälle: `offer_kanban_stages`↔`lead_stages` + 12 Status-Spalten in `lead_product_lists`**. FK-Index nur 6/442. Fehlbenannt nur `radiators` verifiziert. TABU-Bereiche nicht bewertet.
