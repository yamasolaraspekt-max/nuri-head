# EXPERTEN-INVENTUR 09 — AUSWERTUNG (Controlling / Aftersales)

> **Rolle:** Auswertungs-Experte (Controlling / Aftersales). **Modus:** rein lesend.
> **Stand:** 2026-07-10. Repo `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Baut auf:** `docs/audit/code-audit.md`, `docs/audit/intelligenz-audit.md`, `docs/audit/automatisierungs-hebel.md`, `docs/glossar.md`.
> **Mein Bereich:** Nachkalkulation (Soll-Ist) · Deckungsbeitrag · Kennzahlen/Cockpit · Controlling über Projekte · Aftersales/Wartung · Archivierung.
> **TABU (nur an Nähten betrachtet):** Nuriva, Video/Jitsi, Invoice-Zone-Interna, Legacy Bitrix/NIBE/IMAP.
> **Beleg-Disziplin:** Jede Aussage trägt Datei:Zeile, SQL-Messung oder Zählung. Unbelegtes ist **NICHT-VERIFIZIERT**. Datenbasis-Warnung s.u. gilt für alle „Größen".

**Glossar (verbindlich):** Kunde=`new_leads`, Objekt=`lead_alternative_adds` (`alternative_id`), Gewerk=`lead_product_lists`, Angebot=`offers`, Auftrag=`deals`, Rechnung=`invoices` (führend).

**Datenbasis-Warnung:** Dev-Restore, mein Bereich ist fast leer: `costing_sets` 0, `costing_set_roles` 0, `time_summaries` 0, `daily_reports` 0, `daily_report_times` 0, alle `*_reports` 0, `maintenance_*` 0 — **nur `customer_maintenance_contracts` 10** und `daily_report_work_places` 3. Alle Aussagen sind **strukturell** (Code + Schema), nicht volumengemessen.

---

## 0. KERN-BEFUND (die drei Leitfragen des Auftrags)

| Leitfrage | Antwort | Kern-Beleg |
|---|---|---|
| **Gibt es echte Nachkalkulation (Ist-Kosten vs. Angebot)?** | **NEIN.** Es existiert **ausschließlich Vorkalkulation** (Angebots-/Vorlagen-Preisbildung). Kein Ist-Kosten-Rücklauf, kein Soll-Ist in Geld, kein persistierter Plan-DB als Controlling-Größe. | Negativ-Suche `nachkalk`/`ist_kosten`/`actual_cost`/`db_percent` = **0 Treffer** in `app/`. `wage_cost_per_hour × Ist-Stunden` existiert **nirgends** (nur × Plan-Stunden in MasterSet). |
| **Wie tragfähig ist die Kennzahlen-Basis (Status-Zoo-Wackeligkeit)?** | **Wackelig.** Das Cockpit zählt/aggregiert über genau die Freitext-Status-Felder, die das code-audit als „Status-Zoo" belegt (`invoices.status`, `deals.status`, `lead_product_lists.status`). „Umsatz" hängt an `invoices.status='paid'` + `created_at`-Periodenbezug — beides fragil. | `DashboardCompanyController.php:121-133,199-211`; Status-Zoo: code-audit 1D-B (150 `status`-Spalten, 139 varchar), intelligenz-audit C1/C4. |
| **Wartungs-Wiedervorlage automatisch?** | **NEIN.** `next_service_date` wird einmalig gesetzt und in Read-only-Feeds angezeigt; **kein** Scheduler scannt Fälligkeiten, **kein** Folge-Termin/Task/Notification wird erzeugt, das Datum wird nach erledigter Wartung **nicht** fortgeschrieben. | Kein Maintenance-Command in `app/Console/Kernel.php:19-35`; `CustomerMaintenanceContractController` erzeugt bei `store` nur ein `completed`-Protokoll, keine Folge. |

**Ein-Satz-Urteil:** ticket ist im Auswertungs-/Controlling-Bereich eine **Umsatz- und Aktivitäts-Anzeige**, kein Controlling-System: es zeigt *wie viel* und *wie viele*, aber nicht *ob es sich gelohnt hat* — und Aftersales/Wartung ist ein Erfassungs-Skelett ohne den automatischen Wiedervorlage-Zyklus, der sein eigentlicher Wert wäre.

---

## 1. IST-FUNKTIONEN (mit Beleg)

### A. Vorkalkulation / Kostenschema (`CostingSet`) — **gebaut, aber nur an Vorlagen gebunden**
- `CostingSetController.php` — CRUD des Kostenschemas: `aw_minutes`, `material_overhead_percent`, `labor_overhead_percent`, `site_overhead_percent`, `risk_percent`, `profit_percent`, `commission_mode` (`revenue_percent`/`fixed`/`db_percent`), `rounding_mode`. **Finance-Gate vorhanden** (`:15-17` `permission:Finance,update|delete` — MASTER-01 P1-IDOR-Fix bereits angewandt; positiv, s. §5).
- Rollen-Matrix `CostingSetRole`: pro Qualifikation `wage_cost_per_hour → full_cost_rate_per_hour (+NK+GK) → sell_rate_per_hour (+Markup)` (`CostingSetController.php:296-306,418-449`). Das ist ein sauberes **Stundensatz-Kalkulationsschema**.
- **Konsumiert wird es ausschließlich in Produkt-Vorlagen** (`MasterSet`): `MasterSetController.php:2221,2284,2330` nutzt `role->wage_cost_per_hour` zur **Baustein**-Preisbildung; `MasterSet.php:33` `costing_set_id`; Bindung per Migration `…add_costing_set_to_master_sets_table`. **Kein `costing_set_id` auf `offers`/`deals`/`lead_product_lists`.**
- **Folge:** Das Schema preist wiederverwendbare Produkt-Vorlagen. Es wird **nie** je Angebot/Auftrag als geplante Kosten festgeschrieben. → reine **Vorkalkulation**, transient.

### B. Angebots-Deckungsbeitrag (Vorkalkulation-DB) — **live gerechnet, NICHT persistiert**
- `OfferController::calculateOfferSections()` `:1951-2046`: rechnet `total_ek`, `total_db = total_net − total_ek`, `markup_percent`, `margin_percent`, je Sektion `db` — aus den **Angebots-eigenen** EK-Feldern (`offerNodeEkPrice():1776`, `offerLineTotals():1847`).
- **Persistenz-Lücke:** `offer_details`-„Totals Snapshot" (Migration `…create_offer_details_table:28-31`) speichert **nur `total_net`, `tax_rate`, `total_gross`**. `total_ek`/`total_db`/`margin_percent` gehen nur in die JSON-Antwort (`:2097`), **keine Spalte**. Beim Template-Save (`:2090`) nur `total_net` als `leistung`. → **Der geplante DB wird nicht dauerhaft als Controlling-Kennzahl festgehalten** (nur aus `sections`-JSON nachrechenbar).
- Es ist der **Deckungsbeitrag des Angebots gegen seinen eigenen kalkulierten EK** — nicht gegen Ist-Kosten.

### C. Zeit-Soll-Ist (`TimeSummaryService`) — **Minuten, nie Geld**
- `TimeSummaryService::recompute()` `:13-70` + `TimeSummary.php:14-19`: `plan_minutes` vs `actual_minutes` vs `diff_minutes` je `customer_id/alternative_id/product_id/section_id`, aus `CustomerHistory.plan_time` vs `is_time` (Aktivitäts-Zeitstrings; Halb-Erledigt aus `done_reason.percent` abgeleitet, `:47-53`). **Kein Stundensatz, keine Kosten, kein Angebots-Bezug.**
- Das ist ein **Aufgaben-Fortschritts-Tracker** (Zeit), keine Kosten-Nachkalkulation. `time_summaries` = 0 Zeilen.
- Weitere Zeit-only-Soll-Ist: `general_tasks` `planned_hours` vs `actual_hours`, `gantt-view.blade.php:6` („Soll/Ist und Prozent"). Alle rein zeitlich.

### D. Anwesenheits-/Stunden-Controlling (`DailyReportController`) — **Personalzeit, nicht Projektkosten**
- `DailyReportController` (4001 LOC) = Zeiterfassung (Anwesenheit, Start/Ende, QR, Nachtrag). `monthAnalytics()` `:90-203`: pro Monat `worked_hours` (SUM `hours_spent`) vs `expected_hours` (`expectedHoursForDay`), `missing_days`, `coverage_percent`. `weeklyReport()` `:1770` analog.
- `DailyReportTime` trägt `hours_spent`, aber **keine Satz-/Kosten-Spalte** — Stunden werden **nie** mit einem Lohnsatz multipliziert (`:102,2314` nur Roh-Stunden). → **Zeitabdeckungs-KPI** (melden die Leute ihre Stunden?), nicht Projekt-Rentabilität.

### E. Cockpit / Kennzahlen (`DashboardCompanyController`) — **Zähl- + Umsatz-Cockpit**
- `overview()` `:28-160`: KPIs = Counts (`employees`, `departments`, `leads`=alle `new_leads`, `new_leads` periodisch, `offers`, `deals`, `tickets`, `appointments`, `tasks`, `invoices`, `paid_invoices`) **+ `invoice_total`** (SUM `total_amount` bei `status='paid'`, `:125-127`) **+ `invoice_open`** (offene Forderung `SUM GREATEST(total-paid,0)`, `:129-133`).
- Charts: `monthlyRevenue()` `:199-232` (SUM paid `total_amount` je Monat), `itemsByType()`, `invoiceStatus()`, `departmentPerformance()` `:` (Counts Leads/Angebote/Aufträge **je Abteilung**, **N+1** — 3 Queries × N Abteilungen).
- **Keine Kosten-/Gewinn-Seite.** „Department performance" = Aktivitäts-Counts, nicht Profit. `DashboardDepartmentController::invoiceByStatus` `:225,1184-1188` ebenfalls nur `SUM total_amount`.
- **KPI-Wackeligkeit (belegt):** `monthlyRevenue`/`invoice_total` filtern `status='paid'` (Umsatzdefinition führt über `invoices`, aber Status-Freitext bleibt) und binden die Periode an `invoices.created_at` (`:206-207`) statt Ausstellungs-/Zahldatum → **schwacher Periodenbezug**. „Aufträge"-Count zählt `deals` über `created_at`, während `deals.status` (active/deal) laut intelligenz-audit C1/C2 fünf parallele Status-Felder trägt und „active trotz voll bezahlt" vorkommt → **gewonnene/abgeschlossene Aufträge sind nicht sauber zählbar**.

### F. Persönliches Cockpit (`EmployeeDashboardController`, 2420 LOC)
- `miniAnalyticsChart()` `:2064-2166`: Tages-Counts offener Aufgaben/Termine/Tickets/Ticket-Tasks/Leads/Anfragen (je 1 `DB::table`-Count). `personalHoursChart()` `:1995-2062`: Wochen-Bürostunden aus `TimeManagementEntry` — **`montage` hartkodiert 0** (`:2036`). `getMyData()` `:1878-1988`: „Meine Kunden / Meine Projekte" als Listen (verschachteltes `foreach`, **N+1** `:1908-1935`), zeigt `status/stage/stage_history` (Status-Zoo). `hrWidget()` `:2168` HR-Kennzahlen (Urlaub etc.).
- → Persönliches Cockpit = **Arbeitsvorrat-Anzeige**, keine Profitabilitäts-KPI.

### G. Überfälligkeits-/Wiedervorlage-Center (`OverdueCenterController`, 4618 LOC)
- **Pull-Report** (`index()` `:648` rendert Partial; kein Auto-Push): merged 5 Typen (`inquiry/task/appointment/ticket/lead`) älter als **48 h** (`self::HOURS=48`) in-memory, mit Bericht-/Skip-/Reminder-Funktionen.
- **Manuelle Wiedervorlage:** `reminderUpsert()` `:3750` / `reminderBulkUpsert()` `:3817` schreiben in Tabelle `reminders` (`entity_type/entity_id/next_remind_at/status`); `computeNextRemindAt()` `:3900` = now+Minuten / explizites Datum / Fallback +2 h. `applyReminderHiding()` `:663-719` blendet Items mit **künftigem** `next_remind_at` aus (Snooze).
- **Keine Auto-Eskalation:** **Kein** Scheduler scannt `reminders.next_remind_at` (`app/Console/Kernel.php:19-35` kennt keinen solchen Command; `next_remind_at` wird nur in OverdueCenter + `EmployeeController` als Sende-Log geschrieben, nicht als Fälligkeits-Scan gelesen). Deckt sich mit intelligenz-audit R3 („OverdueCenter ist Pull, eskaliert nicht von selbst").
- `recentReportsEmployeeSummary()` `:80-154`: **N+1-Schuld** — iteriert alle aktiven Mitarbeiter und feuert je Mitarbeiter **5** getrennte Count-Queries (`remaining*CountForEmployee` `:4386-4613`) → bei 51 Mitarbeitern **~255 Queries** für eine Übersicht.

### H. Aftersales / Wartungsverträge (`CustomerMaintenanceContract`)
- **Datenmodell trägt den Wiedervorlage-Anker:** `CustomerMaintenanceContract.php:14-66` hat `next_service_date`, `interval_type`, `interval_months`, `recommended_interval_months`, `contract_duration_months`, `termination_notice_days`, `price`, FKs `lead_id→new_leads`, `alternative_id→lead_alternative_adds`, `asset_id→maintenance_assets`, `nextPlannedProtocol()`. Kette: **Kunde → Gewerk (`lead_product_list`) → `MaintenanceAsset` → Vertrag**.
- **Funktion (Explore-Befund, file:line):** `CustomerMaintenanceContractController` (1694 Z., 1 Fett-Controller): Liste mit Filter/Sort inkl. `next_service_asc`, Stat `upcoming` = fällig in 30 Tagen (`:101-115`); Intake-**Wizard** (`create:158-288`); `store():552-959` legt in einer Transaktion **Asset + Vertrag + ein `MaintenanceProtocol` (status `completed`, :852)** an; Read-only-Feeds `incoming/calendarFeed/kanbanFeed` (`:1551-1637`) zeigen fällige Verträge. Viele `Schema::hasColumn`-Guards + Debug-`Log::info` → **defensives, unfertiges Skelett**.
- **Automatische Wiedervorlage = ABWESEND:** (1) **kein Scheduler-Command** scannt Verträge; (2) `next_service_date` wird bei `store` **einmalig** gesetzt und **nach erledigter Wartung nicht fortgeschrieben**; (3) **kein** `FollowUpCreator`/`personal_tasks`/`main_appointments`-Aufruf im Maintenance-Bereich (grep leer) → der Servicetermin landet **nicht** im Termin-/Task-System. Rescheduling ist vollständig manuell (Mensch liest Liste/Kalender).
- **Toter Ballast:** `WartungContractNotification`/`WartungOrderNotification`/`WartungProfileNotification` type-hinten **nicht existierende** Models (`App\Models\WartungContract` etc. fehlen) → würden beim Instanziieren fatalen; **0 Dispatch**. `MaintenanceContract.php` = leerer Stub. Inkonsistente FK-Spalten (`maintenance_contract_id` vs `customer_maintenance_contract_id`) zwischen Relationen.
- **Reife-Indiz Daten:** `customer_maintenance_contracts` 10, alle anderen `maintenance_*` 0 → „Vertrag + 1 Protokoll erfassen"-Stand, Lebenszyklus-Hälfte nie gebaut.

### I. Archivierung
- Lebenszyklus-Endstufen existieren in `lead_stages` (SQL): `Abnahme(abnahme,70)` · `Abschluss(completed,80)` · `Archive(archive,90)` · `Junk(junk,100)`. Status `archive` wird in Listen gefiltert (`LeadOverviewController.php:684,928` `whereNotIn ['archive','archiv','junk','ticket']`).
- → Archivierung ist ein **Status-/Stufen-Zustand** (aus dem Kanban-Fluss), **kein** eigener Auswertungs-/Aufbewahrungs-/GoBD-Prozess in meinem Bereich. Keine automatische Projekt-Archivierung nach Abschluss, keine Nachkalkulations-Ablage. **Tippfehler-Divergenz** `archiv` vs `archive` bereits im code-audit 1.4 belegt.

---

## 2. STÄRKEN

1. **Sauberes Vorkalkulations-Fundament da.** `CostingSet`/`CostingSetRole` ist ein ordentliches Stundensatz-Schema (Lohn→Vollkosten→VK mit NK/GK/Markup, `:296-306`), transaktional, mit Default-Schutz (`:233,248`). Der Rechenkern für **Nachkalkulation ist damit halb vorhanden** — es fehlt nur der Ist-Kosten-Rücklauf und die Bindung an Auftrag/Zeit.
2. **Angebots-DB wird server-autoritativ gerechnet** (`OfferController:1951-2046`, EK/DB/Marge) — die *Vorkalkulations*-Intelligenz ist da; sie müsste nur (a) persistiert und (b) gegen Ist gestellt werden.
3. **Umsatz-Cockpit vorhanden und an die führende Schiene gebunden** (`invoices`, Umsatzdefinition CLAUDE.md): `invoice_total`/`invoice_open`/`monthlyRevenue` liefern echte Umsatz-/Forderungs-Sicht (`DashboardCompanyController:125-133,199-232`).
4. **Finance-IDOR bereits geschlossen** im Kosten-Bereich: `CostingSetController` trägt `permission:Finance`-Gate (`:15-17`) — eine der **5 gegateten Write-Routen** aus code-audit 2.2a. Positiv gegen den sonst dormanten Rechte-Bestand.
5. **Wiedervorlage-Datenmodell ist vollständig** (`reminders` mit `next_remind_at`/Event-Log; Vertrag mit `next_service_date`/`interval_months`) — der Anker für Automatik liegt bereit, muss nur gescannt werden.
6. **Wartungs-Kette ist FK-sauber** (Kunde→Gewerk→Asset→Vertrag→Protokoll) und dockt am Glossar-Kern an (`new_leads`/`lead_alternative_adds`).

## 3. SCHWÄCHEN

1. **[Kern] Keine Nachkalkulation.** Ist-Kosten (gearbeitete Stunden × Satz, tatsächlich bezahltes Material) werden **nie** gegen die Angebots-/Auftragswerte gestellt. `wage_cost_per_hour × Ist-Stunden` existiert nicht (nur × Plan-Stunden in MasterSet). → Kein Soll-Ist, kein realer DB je Auftrag/Gewerk, keine Lern-Schleife „stimmt unsere Kalkulation?".
2. **[Kern] Kein Cross-Projekt-Controlling.** Kein Report für Profitabilität je Gewerk-Typ/Abteilung/Filiale (`Report/*` = nur `DailyReport*`; keine `controlling`/`auswertung`/`nachkalk`-Route). Muster „welches Gewerk verdient" ist nicht abbildbar.
3. **[Kern] Wartungs-Wiedervorlage nicht automatisiert** (§1-H): manueller Pull, kein Scheduler, keine Folge-Erzeugung, `next_service_date` friert ein. Das eigentliche Aftersales-Wertversprechen (wiederkehrender Umsatz durch automatische Terminierung) ist nicht realisiert.
4. **[hoch] KPI-Basis auf dem Status-Zoo.** Cockpit-Aussagen hängen an Freitext-Status (`invoices.status`, `deals.status`) — code-audit 1D-B/intelligenz-audit C1/C2/C4: 139 varchar-`status`-Spalten, „active trotz bezahlt", `new_leads.status`-Mischwerte. → **jede Gruppierung/Filterung nach Status ist so verlässlich wie der Freitext.**
5. **[hoch] Umsatz-Periodenbezug schwach.** `monthlyRevenue` periodisiert über `invoices.created_at` (`:206-207`), nicht Ausstellungs-/Zahldatum → Monatsumsatz kann von der Anlage-, nicht der Leistungs-/Zahllogik abhängen.
6. **[hoch] N+1-Schuld in den Summaries.** `recentReportsEmployeeSummary` (5×N Queries, §1-G), `departmentPerformance` (3×N), `getMyData` (verschachtelt) — bei Prod-Skala (~3000 Kunden, 51 MA) spürbar. Deckt code-audit 2.2d (Query-Last) ab.
7. **[mittel] Fett-Controller ohne Tests.** `OverdueCenterController` 4618 Z., `DailyReportController` 4001 Z. — beide Report-Domäne im code-audit 1.7 als **Test=NULL** gelistet. Auswertungs-Logik (Schwellen, Zeit-Parsing `TimeSummaryService:31-38`, Coverage-Rechnung) ist ungetestet.
8. **[mittel] Toter Aftersales-Ballast:** 3 `Wartung*Notification` gegen fehlende Models, leerer `MaintenanceContract`-Stub, inkonsistente FK-Namen — Rauschen + Fatale-Falle.
9. **[niedrig] Geplanter DB nicht persistiert** (§1-B) — selbst die *Vorkalkulation* wird nicht als Controlling-Größe abgelegt (nur aus JSON rekonstruierbar), also auch kein „Plan-DB vs. später"-Vergleich möglich.

## 4. REIFE je Funktion

Skala 1 (stumme Erfassung) … 5 (mitdenkendes Assistenzsystem), konsistent mit intelligenz-audit „Automatisierungsgrad".

| Funktion | Reife | Begründung (Beleg) |
|---|---:|---|
| Vorkalkulation Kostenschema (`CostingSet`) | **3** | Rechenschema sauber, aber nur an Vorlagen gebunden, nicht an Auftrag/Zeit; kein Snapshot |
| Angebots-DB (Vorkalkulation) | **3** | live korrekt gerechnet, aber nicht persistiert, nicht gegen Ist |
| **Nachkalkulation (Ist vs. Angebot)** | **0** | existiert nicht |
| Zeit-Soll-Ist (`TimeSummary`) | **2** | Minuten-Plan/Ist je Aktivität, nie Geld, Tabelle leer |
| Anwesenheits-/Stunden-Controlling | **2** | Coverage/Fehlstunden je MA; reine Personalzeit |
| Umsatz-Cockpit (Company/Department) | **2** | echte Umsatz-/Forderungs-Zahlen, aber Status-Zoo-Basis, kein Kosten-/Gewinn-Seite, N+1 |
| Persönliches Cockpit (Arbeitsvorrat) | **2** | Counts + „meine" Listen, keine Kennzahl, N+1 |
| Überfälligkeits-/Wiedervorlage-Center | **2** | strukturierter Pull + manuelle Snooze-Reminder; keine Auto-Eskalation |
| Aftersales/Wartungsverträge | **1–2** | Erfassung + Read-only-Fälligkeits-Feeds; kein Auto-Wiedervorlage-Zyklus |
| Archivierung | **1** | Stufen-Zustand aus Kanban; kein eigener Prozess |

## 5. AUTOMATISIERUNGS-REIFE gesamt (Bereich Controlling/Aftersales)

**Gesamt ~1,5–2 von 5 — eine Umsatz- und Aktivitäts-Anzeige, kein Controlling-/Aftersales-System.**

- **Controlling/Auswertung: Grad ~1–2.** Das System **misst Umsatz und zählt Vorgänge**, aber es **kalkuliert nichts nach** und **wertet Profitabilität nicht aus**. Die Ist-Seite (Kosten, gearbeitete Stunden × Satz) existiert als Datenpunkt (`DailyReportTime.hours_spent`, `CostingSetRole.wage_cost_per_hour`), wird aber **nie zusammengeführt**. Das ist die größte Einzel-Lücke meines Bereichs — und zugleich die **billigste Reserve**, weil beide Operanden vorhanden sind (Stunden + Sätze) und nur die Multiplikation + Gegenüberstellung fehlt.
- **Aftersales/Wartung: Grad ~1–2.** Erfassung steht (Vertrag, Asset, Protokoll, Fälligkeitsdatum), aber der **Automatik-Kern (Fälligkeits-Scan → Wiedervorlage → Termin/Task/Benachrichtigung)** fehlt vollständig. Passt exakt zu intelligenz-audit K6 (FollowUpCreator deckt Wartung nicht ab) + R3 (kein Push).
- **Konsistenz mit Nachbar-Audits:** deckt intelligenz-audit „Alt-Kern Grad ~2" und automatisierungs-hebel „Verdrahten statt Neubauen"; die Rechen-Zone Grad 4 (Heizlast/Form) liegt **außerhalb** meines Auswertungs-Bereichs.

**Die drei wirkungsvollsten Hebel in meinem Bereich (Wirkung ÷ Aufwand; als Landkarte, NICHT jetzt bauen):**
1. **Nachkalkulation-Naht (M):** `DailyReportTime.hours_spent × costing_set_roles.sell/full_cost_rate` je Auftrag summieren + gegen `offer total_ek/total_db` stellen → erster echter Soll-Ist-DB je Auftrag. Beide Operanden existieren. **(b)** — Fachfreigabe (Satz-Zuordnung) nötig, daher Vorschlag, nicht Vollautomat.
2. **Wartungs-Wiedervorlage (S–M):** ein Scheduler-Command scannt `customer_maintenance_contracts.next_service_date`, erzeugt bei Fälligkeit (minus `termination_notice_days`) eine Aufgabe/Termin via `FollowUpCreator`/`personal_tasks` und schreibt nach erledigtem Protokoll `next_service_date += interval_months` fort. Anker vollständig vorhanden. **(a)** für die Wiedervorlage-Erzeugung.
3. **KPI-Basis härten (M, Strangler):** Cockpit-Gruppierungen auf einen Status-/Werte-Kontrakt statt Freitext ziehen + Umsatz-Periode auf Ausstell-/Zahldatum statt `created_at` — hängt an Weiche 1 (führende Status-Wahrheit).

---

## Gelesen / Nicht-gelesen (Bilanz)

**Firsthand gelesen/gemessen:** `CostingSetController.php` (voll), `EmployeeDashboardController` (`miniAnalyticsChart`, `personalHoursChart`, `getMyData`, Methoden-Landkarte), `OverdueCenterController` (Methoden-Landkarte + `index`/`applyReminderHiding`/`reminderUpsert`/`reminderBulkUpsert`/`computeNextRemindAt`/`recentReportsEmployeeSummary`), `DailyReportController` (`monthAnalytics` + Header/Methoden-Landkarte), `TimeSummaryService` + `TimeSummary`, `DashboardCompanyController::overview`+`monthlyRevenue`+`departmentPerformance`, `CustomerMaintenanceContract`-Model; `app/Console/Kernel.php` + `routes/console.php` (Scheduler-Vollzählung); Routen `web.php` (Report/Dashboard/CostingSet/Maintenance-Gruppen); SQL: `lead_stages` (Lebenszyklus), Tabellen-Zeilenzahlen meines Bereichs. **Via Explore-Agenten (belegt file:line):** (1) Nachkalkulation/DB/Cross-Projekt-Vollrecherche über CostingSet-Konsum, Soll-Ist-Suche, Offer-DB-Persistenz, Umsatz-Aggregation, Zeit→Kosten; (2) Wartungsverträge-Vollrecherche (Controller, Scheduler, Notifications, FollowUpCreator-Anbindung, Reichweite).

**Nicht/oberflächlich gelesen:** die großen Methodenkörper von `OverdueCenterController` (`recentReportsFetch` `:2484-3266`, Export) und `DailyReportController` (`store`/`completeAndExport`/`EmployeeList*`) im Volltext; die Report-Blades/Charts-Frontend; `MasterSetController`-Preisbildung im Detail (nur die CostingSet-Konsumzeilen belegt); `DashboardDepartmentController` (1615 Z.) außer `invoiceByStatus`; die Maintenance-Wizard-Blades.

## NICHT-VERIFIZIERT

- **„Keine Auto-Eskalation/Auto-Wiedervorlage" = statischer grep** über `app/Console`, Jobs, Scheduler. Stark, aber kein Beweis gegen Reflection/String-Dispatch oder einen extern (Cron außerhalb `Kernel::schedule`) getriggerten Command.
- **Clientseitige Verkettung nicht geprüft:** Ob ein Blade/JS im Cockpit Kosten/DB clientseitig rechnet oder Wartungs-Fälligkeiten clientseitig eskaliert — **serverseitig widerlegt, clientseitig NICHT geprüft**.
- **Zeilenzahlen** aus Dev-Restore (Bereich ~leer) — keine Aussage über Prod-Verhalten/-Volumen; N+1-Schwere ist strukturell abgeleitet, nicht laufzeit-gemessen.
- **`reminders`-Scan durch `EmployeeController`** nur als „Sende-Log-Insert" gesichtet (`:2296-2386`), nicht der volle Controller — ein dortiger Fälligkeits-Push ist unwahrscheinlich, aber nicht 100 % ausgeschlossen.
- **Invoice-Zone (TABU):** `total_amount`/`status='paid'` als Umsatz-Quelle nur an der Naht genommen, die interne Rechnungs-/Zahllogik nicht bewertet.

## Selbstkritik

- Der stärkste Befund (keine Nachkalkulation) ist ein **Negativ-Beweis** — durch breite Suche gestützt, aber Negativ-Beweise bleiben prinzipiell schwächer als Positiv-Belege; ein exotisch benannter Controlling-Pfad könnte übersehen sein.
- Mein Bereich ist in der Dev-Restore fast vollständig leer — die **Funktions**-Aussagen (Code) sind belastbar, die **Nutzungs**-Aussagen („wird real genutzt") sind es nicht.
- Ich habe die zwei größten Report-Controller nach Methoden-Landkarte + Schlüsselmethoden bewertet, nicht Zeile für Zeile; einzelne Auswertungs-Feinheiten in den ungelesenen Methodenkörpern können die Reife-Note punktuell verschieben.
- TABU (Nuriva/Video/Invoice-Interna/Legacy) respektiert — nur an Nähten betrachtet.
