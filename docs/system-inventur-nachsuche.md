# System-Inventur — Nachsuche (Vollständigkeitsprüfung zu `docs/system-inventur.md`)

**Stand:** 2026-07-11 · **Rolle:** Koordinator + 3 read-only Explore-Experten · **Modus:** nur tiefer suchen + absichern — **keine Bewertung-als-Lösung, keine Optimierung, kein Bau.**
**Methode:** drei parallele Grep/Find/Read-Sweeps (alte Module/tote Views · unverdrahtete Services/Automatik · Daten/Models/Tabellen/Docs). Alle Funde mit Datei:Zeile-Beleg. Kennzeichnung **sicher** / **Stichprobe** / **zu verifizieren**.

---

## 1. Was wurde zusätzlich gefunden?

### 1.1 Toter/versteckter Modul- und View-Bestand (Kapitel A/N)
- **`app/Http/Controllers/Old/` — 37 Dateien, sicher tot:** alle deklarieren intern `namespace App\Http\Controllers;` (nicht `…\Old`) → **PSR-4-nicht-ladbar** (`composer.json:37-42` reines PSR-4). **0** Route-Referenzen auf `Controllers\Old`. Größte: `oldMainAppointment.php` (86 KB), `CustomerController.php` (34 KB), `OverdueCenterController copy.php` (104 KB).
- **`RealtimeNotificationDebugController` — toter Import:** `routes/web.php:361` `use …RealtimeNotificationDebugController;`, aber die Klasse existiert nur in `Old/` (nicht ladbar). Kein `Route::` nutzt sie. **(sicher)**
- **~29 verwaiste Root-Controller** (0 Refs in routes/, app/, views/): u. a. `HeatpumpController`, `HeatpumpChecklistController`, `CustomerOfferWPController`, `CustomerWPCableController`, `EconomicAssumptionController`, `DealNoteController`, `SolarSystemController`, `AttendanceController`, `HomeController`, `PaymentMethodController`, `LabelController`, `TaskDocumentController` u. a. **(sicher, Stichprobe: vor Löschung final gegenprüfen wg. evtl. dynamischer Registrierung)**.
- **60+ `.blade copy*.php`-Dateien + 26 „Old Code"-View-Verzeichnisse** — strukturell **nie** via `view()` ladbar (Leerzeichen/`copy` im Namen). Gigantisch: `offer/configuration/offer/config.blade copy.php` **841 KB**, `kanban/kanban.blade copy.php` **910 KB**, `dashboard/employee/mobile.blade copy.php` 416 KB. „Tarn"-Namen: `customer_view.bladessdf copy.php`, `customer_profile.blade copy22.php`. Größte Alt-Ordner: `checklist/…/Old Code` (2,4 MB), `dashboard/old codes` (1,4 MB), `layouts/OLD CODE` (1,2 MB). **(sicher)**
- **Literale Doppel-Route:** `web.php:3477` **und** `:3479` registrieren beide `folders.material-change` (Namenskollision). **(sicher)**
- **Versteckte Dev-/Test-Routen** (nicht in Sidebar): `/test`, `/testnav`, `/testnav2`, `/test_dashboard`, `/test-notification`, `daily_report.prototype.{test1,blackgrading}`; Test-Views `layouts/{test,test2}.blade.php`, `test/test.blade.php` (refs=0). **(sicher)**
- **Versteckte Tool-/Import-/Wizard-Controller mit aktiver Route, aber nicht in der (einzigen) Sidebar:** `ToolsController`, `PVToolsController`, `LeadImportController`, `ProductImportController`, `ProductCsvImportController`, `ClimateImportController`, `CustomerHistoryImportController`, `MoserWpImportController`, `MoserWpInvoiceImportController`, `TaskWizardController`, `EmailConfigurationController`, `OfferWizardController` (durch `wizard-smart` ersetzt, `sidebar:406`). **(zu verifizieren: Erreichbarkeit über Unterseiten/Buttons)**

### 1.2 Unverdrahtete Services + Automatik (Kapitel D/K/N)
- **14 ISOLIERTE Services (0 Produktiv-Aufrufer, höchstens Tests):**
  `GoogleGeocoder`, `DashboardLiveActivityService`, `PvProjektService`, `HeizlastService`, `SupplierProductImportService`, `PlausibilityService`, **`EingangsBelegflussService`**, **`DatevExtfExportService`**, **`AuswertungsService`**, `SmartroutingService`, **`BivalenzService`**, `AnforderungsprofilHeizlastAdapter`, `SeedOrphanCleanupService`, `OmdClient`. **Transitiv tot** (nur von isolierten gerufen): `BelegflussService`, `BuchungsEngine`, `OmdAuthService`. **(sicher)**
  → **Die komplette FiBu/DATEV-Rechenkette ist isoliert** (`EingangsBelegfluss→Belegfluss/BuchungsEngine`, `Datev`, `Auswertung`) — nur über Tests erreicht.
- **3 verwaiste Jobs** (0 Dispatcher): `EmbedMessage`, `UpdateMemory`, `FusionFormEntryJob` (produktiv läuft `ProcessFusionEntry`). **(sicher)**
- **3 verwaiste Events + 1 tote Listener-Kette:** `DashboardEmployeeStatusUpdated`, `LeadSidebarCountsUpdated`, `SolarNewsPushed` (0 Dispatcher); **`LeadRecordChanged` ist registriert (Listener `StoreLeadActivity`), wird aber nirgends gefeuert** → Lead-Aktivitäts-Logging läuft faktisch nicht. **(sicher)**
- **Scheduler-Auffälligkeiten:** `ProcessPersonalTaskScheduler` ist **doppelt geplant** (`Kernel.php:28` + `routes/console.php:22`); `leaves:update-status` + `job_representatives:update-status` sind **auskommentiert** (nicht geplant). **(sicher)**
- **Keine Observer-Klassen** im Projekt; Automatik läuft ausschließlich über **13 Model-`booted()`-Hooks** (Nummernkreise Offer/Deal/Measurement, Invoice due_date A3 + Löschschutz, LeadProductList Status→FK-Fold, ArticleGroup 8 Default-Phasen, Employee Default-Wochenplan u. a.). **(sicher)**

### 1.3 Daten/Import, modellose Tabellen, tote Models (Kapitel D/F/K + Querschnitt)
- **Eingefrorene Referenz-Kataloge unter `database/data/`** (in der ersten Inventur nur als Tabelle `klima_plz` erwähnt): `klima_plz.csv` (**8.169 Zeilen** PLZ→NAT/HGT/Vollbenutzungsstd./Höhe), `b2a_referenz.php` (DIN-4108-4/ISO-6946/IWU-TABULA), `wberechnung_import.php` (19 WP + 5 PV, datenblatt-verifiziert), `wberechnung_wp_kurven.json` (WP-Kennlinien je Modell/VL-Temp). **(sicher)**
- **Import-Rohdaten** unter `storage/app/temp/csv-imports/` (DATANORM-artige GH-Artikel, „NIBE S2125-16", ein File mit `#NAME?`-Excel-Artefakt). **(Stichprobe)**
- **44 Tabellen OHNE Eloquent-Model** (eine Model-basierte Inventur übersieht sie), u. a.: **komplette FiBu** (`accounts`, `account_mappings`, `accounting_documents/journal_entries/journal_lines/fiscal_years/clients`, `chart_of_accounts`, `tax_codes`, `invoice_number_ranges`) → nur `Services/Accounting/*` via `DB::table`; **Spec-Import** (`spec_import_batches`, `product_heat_pump_specs`, `product_pv_module_specs`, `product_formula_routing_rules`); dormant (`deal_invoices`, `building_data`, `heat_pumps`, `email_open_events`, `assets_handover`, `error_problem`); viele Pivot-/Prozess-Tabellen (`general_task_*`, `planner_item_*`, `lead_activity_log_reads`). **(sicher)**
- **~27 Models ohne externe Referenz** (Tot-Kandidaten): u. a. `MasterSetGroupSet`, `CustomerProduct`, `TaskTags`, `DistributorMaintenanceChecklist`, `Nat`, `PhaseStage`, `OfferReport`, `LeadActivityLogs`, `CustomerActivity`, `TempLeadEmployee`, `PersonalTaskProgress`, `OfferTemplateFavorite`. **(Stichprobe — dynamische Nutzung möglich)**
- **Naming-Mismatch Model↔Tabelle** (mögliche tote Relationen): `Heatpump→heat_pumps`, `CustomerProduct→customer_product` (Singular), `BrandMaintenanceChecklist`/`DistributorMaintenanceChecklist` (Singular-Tabelle), `MasterSetGroupSet→master_set_group_master_set`, u. a. **(zu verifizieren)**

---

## 2. Was war in der ersten Inventur unterrepräsentiert?

1. **Toter Ballast** war nur grob erwähnt (`Old/`, „copy") — real: **37 Old-Controller + ~29 verwaiste Root-Controller + 60+ Copy-Blades + 26 Old-View-Ordner + Dev-/Test-Routen + Doppel-Route**. Deutlich größer als dargestellt.
2. **Die isolierten Bausteine** waren auf `BivalenzService`/`SmartroutingService` verkürzt — real **14 isolierte Services**, inkl. der **kompletten FiBu-Kette**, `PlausibilityService`, `PvProjektService`, `HeizlastService`, `AnforderungsprofilHeizlastAdapter`.
3. **Modellose Tabellen** (44) fehlten ganz — v. a. die **gesamte FiBu-Datenschicht** und die **Spec-Import-/Geräte-Spec-Tabellen** sind nur über Services erreichbar und in einer Model-Inventur unsichtbar.
4. **Referenz-/Import-Datenschicht** (`database/data/*`, Cut-over-Seeder) war unterrepräsentiert — das ist die **Datengrundlage** für WP/PV/Heizlast/FiBu.
5. **Automatik-Randfälle**: verwaiste Jobs/Events, tote Listener-Kette, Scheduler-Dublette, auskommentierte Schedules — in Inventur 1 nicht erfasst.
6. **Seeder-Verdrahtung**: die meisten Referenz-/Cut-over-Seeder sind **nicht** im `DatabaseSeeder` (nur manuell) — wichtig fürs Verständnis, wie Daten real in die DB kommen.

---

## 3. Wertvolle, aber unverdrahtete Bausteine (gebaut, 0/​wenige Aufrufer)

| Baustein | Wert | Belegter Zustand |
|---|---|---|
| **FiBu/DATEV-Kette** (`EingangsBelegfluss`, `Belegfluss`, `BuchungsEngine`, `DatevExtfExport`, `Auswertung`) + 10 `accounting_*`-Tabellen + `KontenrahmenSeeder` | GoBD-Buchung/DATEV-Export komplett gebaut | **0 Produktiv-Aufrufer** (nur Tests); Seeder nicht im DatabaseSeeder; „0 Buchungen" |
| **WP-Auslegungs-Kette** (`BivalenzService` + `WpKennlinieService` + `HeizlastService` + Referenzdaten `klima_plz.csv`/`wp_kurven.json`) | normbasiertes WP-Ranking (VDI 4645/4650) | Ranking-Stufe **0 Aufrufer**; Kette nur bis „Kandidaten" verdrahtet |
| **`SmartroutingService`** | Formular-/Checklisten-Routing | 0 Regeln, 0 Aufrufer |
| **`PlausibilityService`** (+`UnitConversionService`) | Eingabe-Plausibilisierung | 0 Aufrufer |
| **`PvProjektService`** | PV-Projekt-Orchestrierung | 0 Aufrufer (nutzt Sizing-Contracts) |
| **`AnforderungsprofilHeizlastAdapter`** | Brücke Anforderungsprofil→Heizlast | 0 Aufrufer |
| **`SupplierProductImportService` / `OmdClient`** | Lieferanten-/OMD-Import | 0 Aufrufer |
| **`DashboardLiveActivityService`** (+ Event `DashboardLiveActivityCreated`) | Live-Aktivitäts-Broadcast | 0 Aufrufer, dispatcht toten Event |
| **`position_qualification_hierarchies`** (+ `PositionController::hierarchyCheck`) | Autoritäts-/Rang-Logik | dormant (0 Zeilen), Methode ungeroutet (Doku-Beleg) |
| **Formular-Fill-Engine** (`LeadProductChecklistValue`, `VisibleIfService`) | dynamische Checklisten-Formulare | nicht ins UI verdrahtet (FS-07 offen) |

---

## 4. Altlasten, die trotzdem produktiv relevant sein könnten (nicht blind löschen)

- **`customers` (Model `Customer` live, 4 Controller-Refs)** — Namenskollision mit der führenden `new_leads`, aber **möglicherweise noch produktiv**. **Vor jeder Ablösung Lebendprüfung.** **(zu verifizieren)**
- **`leads` (Model `Leads`)** — laut Glossar **E-Mail-Tabelle, KEIN Kunde**; 11 Referenzen (v. a. Old). „Voraussichtlich entfernbar nach Bestätigung" — aber Namensfalle. **(zu verifizieren)**
- **`deal_invoices`** — dormant/stillgelegt, aber noch von `DealController`/`LeadOverviewController` referenziert; Drop ausstehend. **Umsatz-Doppelschiene — Löschung nur als eigener, belegter Posten.**
- **Projekt-Altdomäne** (`projects`, `project_tasks`, `project_timelines`, `project_awards`) — durch Planner abgelöst, Models dormant, aber Datenbestand unklar. **(zu verifizieren)**
- **Spec-Import-Tabellen** (`product_heat_pump_specs`, `product_pv_module_specs`) — modellos, aber **aktive Datengrundlage** für WP/PV-Katalog (`CatalogDeviceRepository`). **Nicht als „modellos = tot" missdeuten.**
- **`invoice_number_ranges`** — modellos, aber **produktiv** (race-safe Nummernkreis über `InvoiceNumberService`, Model-Hook). **Kritisch — nicht anfassen.**
- **`assets_handover` / `handovers`** — Lager-Asset-Transfer (Namensfalle: **nicht** Kunden-Abnahme).

---

## 5. Risiken, wenn übersehen

1. **FiBu-Fehlschluss:** Wer nur Controller/Models liest, hält die FiBu für „nicht vorhanden" — real ist sie **komplett gebaut, nur unverdrahtet** (Services + 10 Tabellen). Ein Neubau wäre eine verbotene zweite Wahrheit.
2. **WP-Substanzverlust:** dieselbe Falle bei der Auslegungs-Kette (`BivalenzService`) — genau der bereits benannte Kern.
3. **Stilles Nicht-Logging:** `LeadRecordChanged`→`StoreLeadActivity` feuert nie → Lead-Aktivitäten werden evtl. nicht protokolliert, obwohl der Code „da" ist. **(fachlich zu prüfen)**
4. **Scheduler-Dublette:** `ProcessPersonalTaskScheduler` läuft doppelt → mögliche **Doppel-Erinnerungen/Doppel-Broadcasts** je Minute.
5. **Lösch-Unfälle:** Wer „modellose Tabelle = tot" oder „Model ohne Referenz = tot" annimmt, löscht evtl. **produktive** FiBu-/Spec-/Nummernkreis-Strukturen. → Lebendprüfung (Row-Count) Pflicht.
6. **Naming-Fallen:** `customers`/`leads`/`handovers`/`Heatpump→heat_pumps` — falsche Domänen-Zuordnung oder falsche Löschung.
7. **Massiver toter Ballast** verschleiert die echte Struktur (851 Views, aber ein großer Teil Copy/Old) und verzerrt Größen-/Aufwandsschätzungen.
8. **Verwaiste Import-Rohdaten** in `storage/app/temp/` (mit kaputten Excel-Zellen) — falls versehentlich re-importiert, Datenqualitätsrisiko.

---

## 6. Kapitel, die wegen der Nachsuche ergänzt werden müssen

- **Kapitel A/N (Querschnitt):** großer Abschnitt „Toter Ballast & Dev-/Test-Routen" (Old/, 29 Root-Controller, 60+ Copy-Blades, 26 Old-Dirs, Doppel-Route, versteckte Routen); „Automatik-Landschaft" (13 Model-Hooks, verwaiste Jobs/Events, Scheduler-Dublette, keine Observer).
- **Kapitel K (Rechnung/FiBu):** neuer Kernbefund — **komplette FiBu/DATEV-Datenschicht (10 modellose Tabellen) + Services isoliert**, Kontenrahmen-Seeder manuell. K ist damit „Beleg reif / **Buchung gebaut aber 0-verdrahtet**".
- **Kapitel D (Angebot/Auslegung):** ergänzen um **Referenz-/Import-Datenschicht** (`database/data/*`, Cut-over-Seeder), **Spec-Import-Tabellen** (modellos), Formular-Fill-Engine dormant, `PlausibilityService`/`PvProjektService`/`AnforderungsprofilHeizlastAdapter` isoliert.
- **Kapitel F (Katalog/Preise):** Lieferanten-Import (OMD/DATANORM) teils isoliert; Import-Rohdaten in `storage/app/temp`.
- **Kapitel B/C (Eingang/CRM):** tote Listener-Kette `LeadRecordChanged`; `leads`/`customers`-Namensfallen; `kanban_lead_tasks`/Arbeitsschritt-Ebene 0-Zeilen.
- **Kapitel H (Disposition/HR):** `position_qualification_hierarchies` dormant; Attendance/Recurring-Leave/Filialkosten „gebaut, 0 Zeilen".
- **Kapitel L (Controlling):** DATEV/Controlling-Auswertung isoliert.
- **Kapitel M (Service):** Serviceaufträge dormant (Model, keine Route/Nav).

---

## 7. Offene Suchlücken (bewusst nicht abschließend geklärt)

1. **Live-Row-Counts fehlen:** „dormant/0 Zeilen"-Aussagen stammen aus vorhandener Doku, **nicht neu an der Live-DB gemessen**. Für echte „tot vs. produktiv"-Entscheidung nötig.
2. **Dynamische Registrierung:** `Route::resource`/String-basierte Controller-Bindung, Reflection- oder `::class`-Aufrufe könnten „verwaiste" Controller/Models/Services doch nutzen — Stichproben, kein vollständiger Trace.
3. **Blade-Dynamik:** `@include($var)`/`view($dynamic)` könnte „ungenutzte" Views doch rendern.
4. **Route-Dubletten-Kandidaten** (`/customers/search` 3×, `/personal_task_*` 2×, `/getEmployees` 2×) — evtl. verschiedene Prefix-Gruppen, nicht abschließend geprüft.
5. **~27 Tot-Model-Kandidaten + Naming-Mismatch-Tabellen** brauchen finale Einzelprüfung vor jeder Ablösung.
6. **Rand/Legacy** (Chat/AI/Bitrix/Nuriva/Video, `docker/`, S3/SES, Redis) nur oberflächlich berührt — außerhalb der Prozesskette, TABU.
7. **API-Routen** (`routes/api.php`, 365 Z.) + Mobile/Nuriva-Endpunkte nicht kapitelweise inventarisiert.
8. **Vollständige Copy-Blade-/Old-Dir-Liste** liegt in den Sweep-Logs; hier nur die größten zitiert.

---

## Evaluator-Notiz (Selbstprüfung dieser Nachsuche)
- **Belegt (firsthand grep/find/ls durch 3 Experten):** Old/-Untotladbarkeit + 0 Route-Refs · 14 isolierte Services + transitive · verwaiste Jobs/Events + tote Listener-Kette · Scheduler-Dublette · `database/data/*` + Seeder-Verdrahtung · 44 modellose Tabellen · Copy-/Old-View-Bestand.
- **Stichprobe / zu verifizieren:** exakte Zahl verwaister Root-Controller (dynamische Registrierung) · ~27 Tot-Models · Naming-Mismatch-Tabellen · Live-Row-Counts (aus Doku, nicht neu gemessen).
- **Nicht gemacht (korrekt):** keine Bewertung-als-Lösung, keine Löschung, keine Verdrahtung, keine Optimierung, kein Bau, kein Commit.
- **Konsequenz für die Systemkarte:** `docs/system-inventur.md` ist **grob korrekt, aber im toten Ballast, in der FiBu-Isolation und in der modellosen Datenschicht unterrepräsentiert** — diese Nachsuche schließt das. **Empfehlung unverändert:** erstes Tiefenkapitel **D** (Substanz nutzbar machen) oder **K** (FiBu-Isolation ist jetzt der zweite große, klar umrissene Fund).
