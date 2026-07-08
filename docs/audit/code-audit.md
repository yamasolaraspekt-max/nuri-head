# CODE-AUDIT-01 — Reine Code-Analyse (ticket CRM/ERP)

> **Status:** READ-ONLY-Audit, Stand 2026-07-08. Repo `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Maßstab (fair):** gewachsenes Live-System (Laravel 11, ~3000 Kunden, ~40 tägl. Nutzer), kein Greenfield. Bewertet gegen „wartbar, sicher, erweiterbar für 3 Jahre", nicht Lehrbuch. Haus-Qualitätsmaßstab = die frisch gebauten Zonen (FK-Kanban-Kette, Cut-over-Migrationen, FollowUpCreator, Anforderungsprofile, Test-Harness, `app/Services/Accounting|Form|Heizkoerper`).
> **TABU (nur gelesen, nicht bewertet-zum-Ändern):** Nuriva-APIs, Video/Jitsi, Invoice-Zone, Legacy Bitrix/NIBE/IMAP.
> **Kollisionshinweis:** Dieses Dokument gehört zum Audit CODE-AUDIT-01. Die Dateien `00-index.md`, `01-fehler-inventur.md`, `02-architektur.md`, `03-swot.md`, `stopp-1.md` gehören einem **parallelen** Audit und werden hier nicht angefasst.

**Beleg-Disziplin:** Jede Bewertung trägt Datei:Zeile, Messung oder Zählung. Stichproben sind als solche markiert, Hochrechnungen mit *(Hochrechnung)*, Unbelegtes mit **NICHT-VERIFIZIERT**.

---

# TEIL 1 — STRUKTUR-BESTANDSAUFNAHME (gemessen)

## 1.1 Landkarte in Zahlen

Exakte Zählungen (`find … | wc -l`, `wc -l`), Stand 2026-07-08:

| Kategorie | Dateien | LOC gesamt |
|---|---:|---:|
| Controller (`app/Http/Controllers/**`) | 387 | **201.821** |
| Models (`app/Models/**`) | 410 | 21.983 |
| Services (`app/Services/**`) | 83 | 13.946 |
| Blade-Views (`resources/views/**`) | 851 | **706.670** |
| JS (`public/js` + `resources/js`, ohne min/vendor) | ~254 | — |
| Routen `web.php` / `api.php` | — | 5.532 / 365 |
| Tests (`tests/**/*Test.php`) | 52 | — |

**Kern-Verhältnis:** 202k Controller-LOC gegen 14k Service-LOC = **~14:1**. Die Geschäftslogik lebt überwältigend in Controllern, nicht in einer Service-Schicht. Die Service-Schicht (83 Dateien) ist fast vollständig **jung** und **transplantat-gebunden** (`Accounting`, `Form`, `Heizkoerper`, `Heizlast`, `Energie`, `Spec`, `Suppliers`, `Anforderungsprofil`, `FollowUp`) — der Alt-Kern (Kunde/Kanban/Angebot/Auftrag/Termin) hat praktisch keine Services.

**Top-20 Controller nach LOC** (Auszug der schwersten):

| LOC | Datei |
|---:|---|
| 14.054 | `Customer/NewLeadsController.php` |
| 11.097 | `Planner/PlannerPlanController.php` |
| 7.075 | `Customer/Kanban/LeadOverviewController.php` |
| 6.570 | `Task/PersonalTaskController.php` |
| 4.618 | `Report/OverdueCenterController.php` |
| 4.001 | `Report/DailyReportController.php` |
| 3.952 | `Customer/Deal/DealController.php` |
| 3.810 | `Customer/Offer/OfferFolderController.php` |
| 3.523 | `Employee/EmployeeController.php` |
| 3.428 | `Appointment/MainAppointmentController.php` |
| 3.152 | `Task/PersonalTaskBoardController.php` |
| 2.952 | `Inquiry/InquiryController.php` |
| 2.951 | `Customer/Offer/OfferController.php` |
| 2.864 | `Ticket/ProblemController.php` |
| 2.758 | `Customer/Offer/DealMaterialListController.php` |
| 2.464 | `Product/MasterSet/MasterSetController.php` |
| 2.429 | `Customer/Deal/DealMeasurementController.php` |
| 2.420 | `EmployeeDashboardController.php` |
| 2.375 | `Planner/PlannerEmployeeApiController.php` |
| 2.289 | `Old/OverdueCenterController copy.php` *(toter Ballast, s. 1.5)* |

**Top-Models:** `Employee.php` (485), `LeadAlternativeAdd.php` (442), `Inquiry.php` (398), `NewLeads.php` (343), `LeadProductList.php` (315), `Offer.php` (310). — Models sind schlank (Ø ~54 Z.); die Masse liegt in Controllern und Views.

**Top-Views:** `offer/configuration/offer/config.blade.php` (25.064), `offer/old/config.blade.php` (21.284), `admin/customer_profile.blade.php` (19.727), `new_leads/customer_profile.blade.php` (19.338), `master_sets/index.blade.php` (15.270). Diese Views sind größtenteils **Inline-JS-Anwendungen** (s. 1.6).

**Stichprobe Fett-Controller vs. Service-Schicht (30-Methoden-Prüfung → stattdessen aussagekräftigere Ganzdatei-Messung):**
`NewLeadsController.php` hat **121 public-Methoden**, **267 `DB::table`-Aufrufe** und **102 direkte Model-Queries** (`::where/::find/::create/::update`) in **einer** Datei. Das ist der Prototyp „Query + Business-Logik + Response in einer Methode". Zum Kontrast: der **neuere** `LeadOverviewController.php` deklariert typisierte `: JsonResponse`-Rückgaben (z.B. `:4622 updateStage`, `:4902 moveStageWorkflow`) und delegiert Stufenlogik an den Model-Hook — sichtbar sauberer, aber weiterhin fett (7k Z.).
Gesamtzählung als Proxy fürs Mischverhältnis: **888** `validate()/Validator::make`-Aufrufe verteilt auf **3.374** public-Controller-Methoden. **0** `FormRequest`-Klassen (`app/Http/Requests` leer). Validierung ist damit inline und lückenhaft; die Trennung „Request-Validierung ↔ Handler" fehlt strukturell.

**Fazit 1.1:** Zwei Welten in einem Repo. Der junge Rand (Services, typisierte Controller, Tests) folgt dem Hausmaßstab; der alte, umsatztragende Kern ist Controller-lastig ohne Service-Schicht.

## 1.2 Architektur-Muster-Befund

| Muster | Real vorhanden? | Wo genutzt | Wo hätte genutzt gehört |
|---|---|---|---|
| **Services** | Ja, aber jung | `app/Services/*` (83 Dateien, fast alle Transplantate) | Der gesamte Alt-Kern (Lead/Kanban/Angebot/Auftrag) hat keine |
| **Actions** | **Nein** | — | — |
| **Repositories** | Fast nicht | `app/Repositories/CatalogDeviceRepository.php` (Einzelstück) | — |
| **Events/Listeners** | Ja | `app/Events/*` (24, v.a. Realtime: Chat, Dashboard-Live, EmployeeLocation) / `app/Listeners/*` (3) | Konsistent nur im Realtime-Bereich |
| **Jobs** | Ja | `app/Jobs/*` (10: Import, AI-Embedding, Weather, Reminder) | Konsistent im Async-Bereich |
| **Observers** | **Nein** als Klasse (0 `->observe()` in Providern); stattdessen **Model-`booted()`-Hooks** | z.B. `LeadProductList::booted()` `:112`, `NewLeads::booted()` `:16` | Bewusste, kohärente Wahl — kein Mangel |
| **FormRequest** | **Nein** (0 Dateien) | — | Jede store/update-Route |
| **Policies** | Minimal (5 Dateien) | v.a. DealMeasurement/Heizkoerper-Zone | Der gesamte Alt-Kern |
| **Global Helpers** | **Nein** (kein `app/Helpers`, kein composer-`files`-Autoload) — *sauber* | — | — |

**Generationen (datiert nach Code-Stil, nicht git — die Historie ist auf 2026-04-01 gestaucht):**
- **Alt-Stil (Gen 1):** `DB::table(...)`-lastige Fett-Controller, Inline-Validierung, Feld-für-Feld-`save()`, Magic-String-Status, riesige Blade+Inline-JS. Prototyp: `NewLeadsController`, `PlannerPlanController`.
- **Neu-Stil (Gen 2, „Hausmaßstab"):** typisierte `JsonResponse`-Controller die an Model-Hooks/Services delegieren, dedizierte Service-Klassen mit Konstruktor-DI (`CompatibilityService.php:17`), typisierte Registries als Vertrag, Migrations mit echtem `down()`, Marker-Seeder, Feature+Unit-Tests. Prototyp: `app/Services/Heizkoerper|Accounting|Form`, `LeadProductList`-Hook.

**Zwei Konventionen laufen nebeneinander.** Das ist der zentrale Architektur-Befund: es gibt keine EINE Konvention, sondern eine klar bessere junge und eine dominante alte. Der Weg ist Strangler (alte Zone stückweise in Neu-Stil ziehen), nicht Rewrite.

## 1.3 Abhängigkeits-Analyse

- **Domänen-übergreifende Schreiber:** `DB::table(...)` in **160** von 387 Controllern; **267** allein in `NewLeadsController`. Weil viele Schreibpfade an Eloquent vorbei über `DB::table` gehen, umgehen sie Model-Hooks (die Autoren wissen das — `LeadProductList.php:107-110` dokumentiert genau diese Umgehung und fängt die eine Batch-INSERT gezielt ab).
- **„Jeder-darf-alles"-Models (Kopplungsgrad = nutzende Controller):**
  - `Employee` — **90** Controller. Das ist das mega-gekoppelte Zentral-Model.
  - `NewLeads` — **39** · `LeadProductList` — **34** · `LeadAlternativeAdd` — **25**.
  Diese vier sind die Klammer-Modelle des Systems; jede Schema-Änderung an ihnen hat breite Reichweite (s. Teil 1D-D Flaschenhals).
- **Zirkuläre Abhängigkeiten:** keine PHP-Zyklen im klassischen Sinn nachgewiesen (**NICHT-VERIFIZIERT** als vollständige Zyklen-Analyse); die faktische Verflechtung läuft über die geteilten Klammer-Models, nicht über `use`-Zyklen.
- **Globale Helfer/Streuung:** kein globaler Helfer-Namespace (positiv). Streuung erfolgt stattdessen über **inline duplizierte Logik** in Controllern (s. 1.4).

## 1.4 Duplikation

**Bestätigter Kern-Fund — Stage-Ableitung existiert in ≥3 Varianten mit ECHTEM Fold-Unterschied:**
- **Kanon:** `LeadProductList::deriveLeadStageId()` `app/Models/LeadProductList.php:144-175` — Synonym-Map + **Fold** `follow_up→offer`, `accepted→deal` (`:164`).
- **Inline-Reimplementierung:** `NewLeadsController::normalizeCompanyStage()` `:12977-13004` — **dieselbe Alias-Map**, aber **ohne Fold** (`:13003 return $aliases[$s] ?? $s;`). → Für denselben Status liefern beide Wege **unterschiedliche Stufen-Keys**; der Controller kann `follow_up`/`accepted` erzeugen, die der Hook nie erzeugt. **Konkretes Divergenz-Risiko für `lead_stage_id`-Konsistenz.**
- **Dritte Variante:** `NewLeadsController.php:9791-9805` (`match($stage)`) mit Tippfehler-Key `archiv` vs. Kanon `archive`.

**Drei Speichern-Muster für dieselbe Entität `lead_product_lists` (Gewerk):**
- Pattern A `create([...])` mit `'status'=>'Lead'` — `NewLeadsController.php:1391-1403` (+ Klone `InquiryController.php:2752`, `InquiryVerificationController.php:554`).
- Pattern B `updateOrCreate([...])` mit `'status'=>'open'` — `NewLeadsController.php:1636-1652`.
- Pattern C `new LeadProductList()` + Feld-für-Feld + `save()` mit `'status'=>'archive'` — `MassManagerController.php:106-121` (umgeht Fillable).
→ Gleiche Tabelle, **drei verschiedene Default-Status**. Klassischer „Status-Zoo an der Quelle".

**Weitere 10+-Zeilen-Klone (Stichprobe):**
- Adress-Zusammenbau `trim(street+postcode+city)` 4× inline in `InquiryController.php:1061,:1353,:2442,:2710` (mit Divergenz Konkatenation vs. Interpolation).
- Stufen-FK-Filter „NULL or 0 or =id" doppelt in `PlannerPlanController.php:669-672` ↔ `:6442-6444`.
- FK-Umschreibung beim Stage-Merge/Dedupe `LeadOverviewController.php:2957-2993`.

*(Hochrechnung: über die drei Groß-Controller hinaus existieren weitere Adress-/Stage-Klone; Umfang nicht vollständig vermessen.)*

## 1.5 Toter Code

- **`app/Http/Controllers/Old/` — 37 Dateien, zu 100 % tot.** 0 Referenzen in `routes/`. Zusätzlich deklarieren die Old-Dateien intern `namespace App\Http\Controllers;` (nicht `…\Old`) → per PSR-4 aus `Old/` **gar nicht autoladbar**. Darunter selbst „copy"-Leichen: `Old/OverdueCenterController copy.php` (2.289 Z.), `Old/oldMainAppointment.php` (2.058 Z.).
- **„copy/backup/Old Code"-Ballast:** ~**110** Dateien mit `copy`/`backup` im Namen unter `app/` + `resources/views`, plus ~28 „Old Code/oldcode"-**Verzeichnisse** in `resources/views`. Größte: `admin/layouts/app.blade copy 2.php` (~10.952 Z.), `master_sets/index.blade copy 3.php` (~6.702 Z.).
- **Verwaiste Views (bestätigt):** `admin/offer/old/folder-show.blade copy.php`, `admin/kanban/oldcode/*` (`kanba 2026.php`, `caban.blade.php`), `admin/new_leads/old code/*` (`customer_view.bladessdf copy.php`) — 0 `view()/@include/@extends`-Referenzen.
- **Auskommentierte Blöcke >20 Z.:** in den Groß-Controllern (`NewLeadsController`, `PlannerPlanController`, `LeadOverviewController`) **keine** langen Kommentar-Leichen gefunden — dieser Dead-Code-Typ ist hier **nicht** das Problem; der tote Code liegt in ganzen Dateien/Ordnern.

## 1.6 JS-Schicht

- **`public/js/kanban.js` (17.204 Z.) — flacher Vanilla-JS-Funktionszoo, nicht modular.** 0 `import/export`, 0 `class`, 0 top-level `function`; stattdessen **342** Arrow-Funktionen und IIFE-Verschachtelung. Namensraum über **`window`-Globals** (`window.kbGlobalCardId`, `window.KANBAN_BOOT`). **192** `addEventListener`, nur **4** jQuery-`$(`, **0** Alpine. Der Datei-Header dokumentiert selbst: mechanisch aus Inline-`<script>`-Blöcken einer Blade extrahiert und konkateniert. → größte einzelne Frontend-Wartungslast.
- **Inline-JS-Anteil der Top-10-Views** (Zeilen in `<script>`-Blöcken / Gesamt):

| View (unter `resources/views/admin/`) | Gesamt | Script | % JS |
|---|---:|---:|---:|
| new_leads/customer_profile.blade.php | 19.338 | 17.698 | **91,5 %** |
| customer_profile.blade.php | 19.727 | 15.736 | **79,8 %** |
| master_sets/index.blade.php | 15.271 | 11.599 | 76,0 % |
| offer/configuration/offer/config.blade.php | 25.064 | 18.677 | 74,5 % |
| todo/personal/calendar.blade.php | 12.385 | 7.963 | 64,3 % |
| layouts/app.blade.php | 11.188 | 5.705 | 51,0 % |
| offer/folder-show.blade.php | 14.481 | 7.630 | 52,7 % |

  → Diese 10 Views enthalten **~101.000 Zeilen Inline-`<script>`** *(Hochrechnung Summe)*. Die großen Kern-Views sind faktisch JavaScript-Anwendungen in Blade-Dateien.
- **jQuery/Alpine-Mischgrad:** jQuery ist Hausstandard (**347** View-Dateien mit `$(`). Alpine `x-data` nur in **10** Dateien. Laut CLAUDE.md ist Alpine nur in `heizkoerper/**` und im Formulare-Rendering erlaubt. **Genau eine LIVE-Verletzung:** `resources/views/admin/planner/list.blade.php:225` (`x-data="planItemsLoader(…)"`, Alpine per CDN inline `:477`, `Alpine.data` `:480`) — geroutet via `PlannerPlanController.php:2898`. Der Rest der `x-data`-Treffer sind tote Backups (`…copy`, `Old/`) oder ein **konformer Kommentar** (`energie/grundriss_editor.blade.php:7` „KEIN Alpine …"). Regel hält bis auf die Planner-Liste.

## 1.7 Test-Realität

**52 Test-Dateien, ~337 Testmethoden** (208 Feature + 129 Unit).

| Domäne | Files | Testmethoden | Kommentar |
|---|---:|---:|---|
| Form (Feature+Unit) | 6 | 57 | Neu, stark |
| Heizlast (Feature+Unit) | 9 | 52 | Neu, physikalisch |
| Energie (Unit) | 5 | 35 | Neu |
| Heizkoerper | 7 | 34 | Neu |
| Suppliers | 3 | 23 | Neu |
| Spec | 2 | 22 | Neu |
| Invoice | 2 | 21 | *(TABU-Zone, nur gezählt)* |
| Accounting | 5 | 20 | Neu, GoBD-Invarianten |
| Security | 3 | 20 | DealMeasurement/Policy |
| Planner | 1 | 14 | dünn (nur API-Contract) |
| Anforderungsprofil | 2 | 10 | Neu |
| Catalog | 2 | 10 | Marker-Teardown |
| DealMeasurement | 1 | 4 | Teil-Domäne |

**Getestet vs. NULL — die 9 größten Alt-Domänen:**

| Groß-Domäne | Controller-Gewicht | Test |
|---|---|---|
| Customer/NewLeads | 14k Z. | **NULL** |
| Kanban/LeadOverview | 7k Z. + kanban.js 17k | **NULL** |
| Task/PersonalTask | 6,5k Z. | **NULL** |
| Offer | config.blade 25k | **NULL** |
| Appointment | 3,4k Z. | **NULL** |
| Employee | 3,5k Z. | **NULL** |
| Report | 4,6k Z. | **NULL** |
| Planner | 11k Z. | **DÜNN** (API-Contract, nicht Verhalten) |
| Deal | 3,9k Z. | **PARTIELL** (Measurement + Policy) |

→ **Testabdeckung ist invers zur Controller-Größe:** die größten, ältesten, umsatztragenden Flächen sind ungeschützt; die kleinen, jungen Service-Domänen gut abgedeckt.

**Test-Qualität (Stichprobe — echtes Verhalten, kein „200 OK"):**
- Accounting `GobdBuchungsEngineTest.php:109-111` prüft lückenlosen Nummernkreis `2026-000001…000003`; `:123` fängt `Maker-Checker`-Exception; `:161-163` Storno-Neutralität Soll=Haben, Originalzeile unverändert `:144`.
- Security `DealMeasurementPolicyTest.php:65` Portal-Nutzer hart verweigert; `:79` Orphan-Zähler `assertSame(1, Cache::get(ORPHAN_COUNTER))`; `:116` echter HTTP `403` auf `heizkoerper.stueckliste.uebernehmen`.
- Heizlast `KlimaBinServiceTest.php:17` Bins decken 8760 h; `:26` Saison-Gewichte summieren 1.
- Form `FormulaEvaluateEndpointTest.php:40` berechneter Wert, `:52` unsichtbare Berechnung nicht ausgeführt, `:68` Schema-Verletzung → 422.

**Harness (vorbildlich, s. 2.1):** Test-DB per `phpunit.xml:24-28` `force="true"` auf `ticket_testing` genagelt (kann strukturell nie die Dev-DB treffen); Teardown-auf-Null über `RefreshDatabase`/`DatabaseTransactions`; Marker-Seeding + reversibler, mehrbesitz-sicherer Teardown mit Idempotenz-Assertions (`FoxEssLongiTeardownTest.php:41,:47`, `WberechnungImportTest.php:117,:126`).

---

# TEIL 1D — DATENSTRUKTUR-AUDIT (Schema + Daten)

> **Datenbasis-Warnung (wichtig):** Die Zeilenzahlen stammen aus einer **lokalen Dev-Restore** von `ticket` (442 Tabellen). **363 der 442 Tabellen sind leer (0 Zeilen)**, die größte Tabelle ist `klima_plz` (8.162). Das ist **nicht produktionsrepräsentativ** (Prod ~3000 Kunden). Alle Aussagen zu „Größe/Wachstum" sind daher **strukturell** (aus Schema + Code-Query-Mustern), nicht aus gemessenem Volumen. Zeilenzahlen dienen nur als Lebendig/Tot/Zombie-Indiz.

## Schema-Landkarte (Kern + Verdacht)

| Tabelle | ~Zeilen (dev) | Spalten | FK | Idx | Domäne | Status |
|---|---:|---:|---:|---:|---|---|
| `new_leads` (Kunde) | 52 | 53 | 2 | 3 | Kern/Kunde | lebendig |
| `lead_alternative_adds` (Objekt) | 71 | **193** | 1 | 2 | Kern/Objekt | lebendig, **God-Table** |
| `lead_alternative_pv_wp_details` | — | 118 | — | — | Objekt-Detail | lebendig, breit (34 TEXT-Spalten) |
| `lead_product_lists` (Gewerk) | 52 | 43 | 11 | 13 | Kern/Kanban | lebendig, gut indiziert |
| `offers` (Angebot) | 29 | 15 | 7 | 9 | Kern/Angebot | lebendig |
| `deals` (Auftrag) | 14 | 28 | 10 | 11 | Kern/Auftrag | lebendig |
| `invoices` (Rechnung, führend) | 11 | 34 | 4 | 17 | Kern/FiBu | lebendig, gut indiziert |
| `deal_invoices` | 0 | — | — | — | FiBu-Alt | **schlafend (bestätigt 0)** |
| `lead_stages` | 10 | 13 | 0 | 2 | Kanban | lebendig, **kein FK auf sich referenziert** |
| `customers` | **0** | 74 | — | — | Kunde-Alt | **ZOMBIE** (19 FKs zeigen darauf, 0 Zeilen) |
| `products` | 94 | 36 | 4 | 6 | Katalog | lebendig |
| `employees` | 51 | 66 | — | — | Personal | lebendig, breit |
| `salaries` | 100 | 54 | — | — | Personal/Lohn | lebendig, **sensibel** |

**Lebendige Datentiefe (dev):** `klima_plz` 8.162, `salaries` 100, `customer_histories` 95, `products` 94, `main_appointments` 80, `lead_alternative_adds` 71.

## (A) SICHERHEIT

- **Sensible Daten lokalisiert:** Lohn/Steuer `salaries` (54 Sp., 100 Z.), `salary_sheets`; Urlaub/Krankheit `leave_days`, `employee_sicks`, `employee_short_leaves`, `employee_recurring_leaves`; Kundendaten `new_leads`/`lead_alternative_adds`; Bankverbindungen in FiBu/Branch-Tabellen. **Zugangsdaten/API-Keys im Klartext: KEINE gefunden** — grep über `config/` nach `key/secret/token/password => '…'` ohne `env()` = 0; keine `sk_live/AKIA/AIza/ghp_`-Muster in `app/`+`config/`. `.env.example` nutzt Platzhalter. **Das ist gut.**
- **Passwort-/Token-Hygiene:** Standard-Laravel-`Hash` (bcrypt) für User-Passwörter (**NICHT-VERIFIZIERT** als vollständiger Plaintext-Spalten-Scan über alle 442 Tabellen). PII-in-Logs: die `Log::warning('lead_stage_id derive-miss', ['status'=>…])`-Hooks loggen nur Status-Strings, keine PII — sauber.
- **Lösch-Realität (DSGVO):** **92 von 442 Tabellen** haben `deleted_at` (SoftDeletes), **59 Models** nutzen `use SoftDeletes`. Es gibt einen `SoftDeletedGarbageCollector`-Service (`app/Services/SoftDeletedGarbageCollector.php`, 183 Z.). **Aber:** ein echter, kaskadierender **Kunden-Löschpfad** über alle ~80 `customer_id`-Tabellen ist **NICHT-VERIFIZIERT** — SoftDelete auf `new_leads` lässt die Kind-Zeilen als Waisen zurück (Waisen-Risiko real, weil `customer_id` teils ohne FK-Constraint, s. B).
- **Zeilen-Ebenen-Trennung:** Es gibt Branch/Filial-Struktur (`branches`, `brand_departments`), aber die **Autorisierung erzwingt sie fast nirgends** (s. Teil 2.2a): geschriebene Routen sind nur `auth`-gegated → im Kern **sieht/schreibt jeder eingeloggte Mitarbeiter alles**, inkl. fremder Gehälter (IDOR, s. 2.2a).

## (B) WARTBARKEIT

- **Namens-Konventionen — DE/EN gemischt (belegt):** Tabellen mit deutschen Namen (`anforderungsprofil_werte`, `baualtersklassen`, `konstruktionen`, `foerderungen`) neben englischen (`products`, `offers`, `deals`). Spalten gemischt: `u_wert`, `betrag`, `anzahl`, `verifikations_datum` (DE) neben `status`, `created_at` (EN). Die **neuen** Transplantat-Domänen sind bewusst deutsch (Fachsprache), der Alt-Kern englisch — eine erklärbare, aber uneinheitliche Zweiteilung.
- **Timestamp/SoftDelete-Verbreitung:** `created_at` in **438/442** Tabellen (nahezu vollständig — gut). `deleted_at` in 92.
- **Dieselbe Fach-Spalte unter verschiedenen Namen (belegt, gravierend):** Der Kunden-FK heißt **`customer_id` (81 Tabellen)** vs. `lead_id` (10) vs. `new_lead_id` (1). Schlimmer: **`customer_id` referenziert ZWEI verschiedene Tabellen** — `new_leads` (47 FK-Constraints) UND die Zombie-Tabelle `customers` (19 FK-Constraints). Wer `customer_id` liest, muss die Tabelle kennen, um zu wissen, ob Kunde=`new_leads` oder =`customers` gemeint ist. **Semantik-Falle ersten Ranges.**
- **FK-Constraint-Deckung (überraschend gut):** **921 FK-Constraints**, **330/442 Tabellen** haben mindestens einen FK. Kern-Tabellen solide (`lead_product_lists` 11 FK, `deals` 10, `offers` 7). **Lücken:** die God-Table `lead_alternative_adds` hat bei 193 Spalten nur **1 FK** (App-Disziplin statt DB-Constraint), `new_leads` nur 2, `lead_stages` 0. **SoftDelete-FK-Falle:** FK-Constraints greifen nicht auf `deleted_at` — soft-gelöschte Eltern lassen FK-gültige, aber fachlich verwaiste Kinder zurück.
- **Typen-Hygiene — Status-Zoo (belegt, groß):** **150** Spalten heißen `status`, davon **139 `varchar`** (Freitext) vs. nur **11 `enum`**. `new_leads.status` enthält gemischte Freitext-Werte: `QUALIFIZIERT`, `Lead`, `Active`, `Von Junk wiederhergestellt` (DE/EN, uneinheitliche Groß-/Kleinschreibung). `deals.status`: `active`, `deal`. `invoices.status`: `open`, `paid` (sauberer). Im Controller-Code **202** hartkodierte Status-Literale. → Werte-Kontrakt fehlt fast überall.
- **JSON ohne Doku:** breite JSON-Nutzung, u.a. `deal_measurement_details` mit `form_data/roof_data/pv_data/wp_data/raw_snapshot`, `anforderungsprofile.gebaeude_geometrie`, `customer_notes.history`. Die **neuen** JSON-Felder haben Registry/Schema-Verträge (`FormSchemaValidator`, `SpecSchema`) — die **alten** (`raw_snapshot`) sind undokumentierte Blobs. Einheiten-Ambiguität kW/W: in der neuen Heizkoerper/Energie-Zone per `private const` + Einheiten-Kommentar adressiert (`UWertService`), im Alt-Katalog **NICHT-VERIFIZIERT** dokumentiert.
- **Fehlbenannte Tabellen (radiators-Muster — weitere):** `radiators` = Wechselrichter-Altlast (per CLAUDE.md dokumentiert, nicht für Heizkörper). Weitere Zombie/Fehlbenennung: **`customers` (0 Zeilen, aber 19 FK-Ziele)** ist der schwerwiegendste Fall — eine ganze superseded Kunden-Tabelle steht als FK-Ziel im Weg.

## (C) OPTIMIERUNG

- **Index-Deckung (auf Kern gut, Rand-Lücken):** Kern-Tabellen sind ordentlich indiziert (`invoices` 17 Idx, `lead_product_lists` 13). Fehlende Leit-Indizes auf abfragbaren `_id`-Spalten: `main_appointments.contact_id`, `main_appointments.other_id`, `personal_tasks.controller_id`, `personal_tasks.source_id`, `personal_tasks.task_id`, `new_leads.moser_id`, `lead_product_lists.accepted_offer_folder_id`. Besonders die polymorph wirkenden `personal_tasks.(source_id, controller_id, task_id)` ohne Index sind bei Wachstum ein Scan-Risiko (Betroffenheit: Task-Board-Queries filtern darüber).
- **Größen-Treiber (strukturell):** Die Breite treibt, nicht die Zeilen: `lead_alternative_adds` (193 Sp., 30 TEXT-Spalten), `lead_alternative_pv_wp_details` (118 Sp., 34 TEXT), `heatpump_checklists` (78), `w_p_checklists` (76). **TEXT/BLOB in heißer Tabelle:** `lead_alternative_adds` (der Objekt-Kern, in 25 Controllern geladen) trägt 30 TEXT-Spalten → jedes `SELECT *` zieht viel toten Ballast. **Log-Tabellen ohne Rotation:** `notifications`, `lead_activity_logs`, diverse `*_histories` existieren (in dev leer); strukturell unbegrenzt, keine Rotation nachgewiesen (**bei 10k Kunden P1**).
- **Query-Lastbild:** Eager-Loading wird breit genutzt (**934** `->with(`-Aufrufe in Controllern) — die bekannten N+1 sind also teils bewusst gemildert. Aber `NewLeadsController` mit 267 `DB::table`-Aufrufen umgeht Eager-Loading; Full-Table-Scan-Verdacht dort, wo God-Table `lead_alternative_adds` ohne WHERE-Index gefiltert wird. `SELECT *` auf die 193-Spalten-Tabelle ist das breiteste heiße Muster.
- **Redundanz (berechnet + gespeichert):** Snapshot-Muster verbreitet (`deal_measurements.material_summary`, `*.raw_snapshot`, `sections_snapshot`, 10+ Models mit „snapshot"). Snapshots sind bei Belegen legitim (eingefrorener Zustand), aber ohne dokumentierte Invalidierung besteht Divergenz-Risiko zwischen Snapshot und Quelle (**NICHT-VERIFIZIERT** je einzelnem Snapshot, ob gewollt eingefroren oder stale-Cache).

## (D) ERWEITERBARKEIT

- **Andock-Fähigkeit kommender Module:** Die **neuen** Nähte sind stabil und vorbildlich (Registry-als-Vertrag, additive Migrationen, FK-reich): Formular-Engine (`ProductFormula`, `FormSchemaValidator`), Accounting (`accounting_*`-Tabellen dockt an `invoices`), Anforderungsprofil (EAV mit `SchluesselRegistry`-Guard). Kundendienst/Lohn docken an `new_leads`/`employees` — dort ist die Naht wegen fehlender FK-Constraints und `customer_id`-Doppeldeutigkeit **weniger stabil** (fehlender Anker: es fehlt eine eindeutige, FK-erzwungene Kunden-Identität).
- **Flaschenhals-Tabellen:** **`lead_alternative_adds` (193 Sp.)** ist der klare Zerlegungs-Kandidat — sie vermischt Objekt-Stammdaten mit PV-/WP-/Dach-Detailfeldern. Additiver Strangler-Pfad: neue Detail-Tabellen (nach Muster des bereits ausgelagerten `lead_alternative_pv_wp_details`) anlegen, Lese-/Schreibpfade einzeln umziehen, alte Spalten zuletzt belegt stilllegen. **`products`** ist weniger kritisch (36 Sp., gut indiziert, EIN Katalog etabliert).
- **Migrations-Fähigkeit:** Die junge Zone zeigt saubere **additive Evolution mit echtem `down()`** (s. 2.1) — das ist der Standard, den der Rest erreichen muss. Downtime-kritische Umbauten nur dort, wo die God-Table zerlegt wird (Datenmigration großer TEXT-Spalten).
- **Skalierungs-Reife (10k Kunden / 2. Filiale / API):** Auto-Increment-PKs durchgängig (bei 2. Filiale/Mandant kein ID-Konflikt, solange EINE DB). **Fehlende Mandanten-/Filial-Spalte** auf vielen Kern-Tabellen: die Zeilen-Trennung nach Filiale ist im Schema **nicht** verankert (nur teils über `branch`-Relationen) → 2. Filiale bräuchte additive `branch_id`-Spalten + durchgesetzte Scopes. Harte Annahme „ein eingeloggter Nutzer darf alles" (s. 2.2a) skaliert organisatorisch nicht auf mehrere Filialen.

---

# TEIL 2 — QUALITÄTS-BEWERTUNG

## 2.1 ✅ GUT (schützen + Vorbild)

Belegt gelobt — jeder Punkt nennt das Prinzip, aus dem die Bauordnung (Teil 3) abgeleitet ist.

1. **FK-Kanban-Kette: Hook + Fold + Fallback + Wächter** — `LeadProductList.php:112-175`, `LeadStage.php`, `NewLeads.php:16`.
   - Hook: `booted()` hängt `creating`/`updating` ein → deckt ALLE Eloquent-Schreiber zentral.
   - Fold: `deriveLeadStageId()` normalisiert Synonyme und foldet Kind-Phasen auf Eltern (`follow_up→offer`, `accepted→deal`, `:164`), key→id per Request gecacht.
   - Fallback: leerer/`open`-Status → `lead`; unbekannter Key → `null` + `Log::warning` statt Crash (`:120,:134`).
   - Wächter: Stale-FK-Schutz (`:128` — Status dirty, FK nicht → FK neu ableiten); Key-Immutabilität bei Rename (`LeadStage.php`), Lösch-Wächter `InvoiceDeletionBlockedException` (`NewLeads.php:23`). Selbst die Raw-INSERT-Umgehung ist bewusst adressiert (`:107-110`).
   - **PRINZIP:** Eine abgeleitete FK-Wahrheit gehört in EINEN Model-Hook mit deterministischem Fold + benanntem Fallback + Guard gegen stale Overwrites — nie in verstreute Controller-Zuweisungen.

2. **Test-Harness** — `phpunit.xml:24-28` (`DB_DATABASE=ticket_testing force="true"`), `database/seeders/Testing/HarnessSupport.php` (`TAG='[TEST-HARNESS]'` `:23`, `upsertId()` `:56`, `guardLocal()` `:30`), `HarnessTeardownSeeder.php` (FK-sichere Reihenfolge, Leeres-Array-Schutz `:39`, `remnants()`-Zählung wirft `:126`).
   - **PRINZIP:** Testbestand markiert sich selbst (Marker im Textfeld), seedet idempotent per Upsert, räumt sich beweisbar auf Null (Rest-Zählung wirft), mit Umgebungs-Guard davor und Test-DB strukturell gepinnt.

3. **FollowUpCreator — EINE Erzeugungs-Stelle** — `app/Services/FollowUp/FollowUpCreator.php`: `sync()` `:50` einziger Erzeugungspunkt, Upsert per `(source_type, source_id)` `:79-88`, Herkunfts-Whitelist `SOURCE_TYPES` `:23`.
   - **PRINZIP:** Für einen Datensatz-Typ genau ein Service-Erzeugungspunkt mit Upsert-Schlüssel + Herkunfts-Whitelist — kein Copy-Paste-Insert je Controller.

4. **Seeder Marker-/Teardown-Disziplin** — `database/seeders/FoxEssLongiCatalogSeeder.php` (`const MARKER` `:23`, schreibt `products.imported_from`) + `…TeardownSeeder.php:24-40` (löscht nur eigene Marker; geteilte Stammdaten nur wenn Rest=0, sonst stehen lassen + melden). Test-gesichert.
   - **PRINZIP:** Seed-Daten tragen einen Herkunfts-Marker (Konstante); Teardown löscht nur eigene Marker-Zeilen, lässt mehrbesitzte Stammdaten stehen (Rest-Zählung entscheidet) — reversibel ohne Beifang.

5. **Registry als Vertrag** — `Anforderungsprofil/SchluesselRegistry.php` (`SCHLUESSEL`-Map `{einheit,cast,pflicht}`, `definition()` wirft bei unbekanntem Key `:53`, erzwungen via `AnforderungsprofilWert::saving`); `Spec/SpecSchema.php` (EINE Regeldefinition je Gerätetyp); `Form/FormSchemaValidator.php` (`VERSION=2`, Typ-Whitelist, reine `validate()`).
   - **PRINZIP:** Erlaubte Keys/Typen leben in EINER typisierten Registry-Konstante, gegen die beim Schreiben validiert wird (Exception statt stille Zeile).

6. **Migration up/down-Disziplin** — Stichprobe 8 jüngere Migrationen, alle mit echtem `down()`: `…create_accounting_foundation_tables.php:119` (6 `dropIfExists` in FK-sicherer Umkehr), `…add_imported_from_to_products_table.php:23` (`dropColumn`), `…activate_abnahme_lead_stage.php:18` (Daten-Migration reversiert genau die eine Zeile). Kein leeres `down()`.
   - **PRINZIP:** Jede Migration — auch Daten-Migrationen — hat ein echtes `down()`, das ihren Effekt in FK-sicherer Umkehr-Reihenfolge präzise rückgängig macht.

7. **Rechte-Fundament** — `CheckUserPermission.php` (parametrisiert `item`/`action`), `User::hasPermission()` `:56` (SuperAdmin-Bypass, Action→CRUD-Flag-`match`, `user_rolls`-Query), Rang-Basis `PositionQualification`/`DepartmentPosition`/`PositionQualificationHierarchy`.
   - **PRINZIP:** Rechte werden an EINER Stelle geprüft (`hasPermission` hinter Route-Middleware) mit Action→CRUD-Flag-Mapping. *(Das Fundament ist gut — es wird nur kaum aufgerufen, s. 2.2a.)*

8. **Saubere Services mit DI** — `Heizkoerper/CompatibilityService.php:17` (Konstruktor-DI), `RadiatorPerformanceService` (reine EN-442-Rechenmethoden, DB-frei), `Accounting/BuchungsEngine::festschreiben()` (Transaktion + `lockForUpdate` + Maker-Checker + Balance-Gate), `Heizlast/UWertService` (Normwerte als `private const`).
   - **PRINZIP:** Ein Service = eine fachliche Verantwortung; Kollaboratoren per Konstruktor injiziert, Konstanten/Normwerte als `private const`, Rechenkerne DB-frei und testbar.

## 2.2 ❌ SCHLECHT (belegt, nach Schaden)

### (a) Sicherheit

- **[P0/P1] Autorisierung praktisch abwesend.** Von **1.211** Write-Routen (POST/PUT/PATCH/DELETE) in `routes/web.php` sind nur **5** mit `permission:`-Middleware gegated (alle im Users-Bereich, `routes/web.php:2242-2246`). Die `super`-Middleware (CheckSuperUser) wird **0×** genutzt, `is_Admin` nur ~10×. Der Rest ist nur `auth`-gegated → **jeder eingeloggte Mitarbeiter darf systemweit schreiben/löschen**, unabhängig von Rolle/Rang. Das gute Rechte-Fundament (2.1-7) ist **dormant**.
- **[P0] Unauthentifizierte Write/Delete-Routen.** Dies ist Laravel 11 mit noch aktivem Legacy `app/Http/Kernel.php` (gebunden in `bootstrap/app.php`); die `web`-Middleware-Gruppe enthält **kein `auth`** (`Kernel.php:35-43`). **35 Route-Gruppen laufen mit `['middleware' => 'web']` allein** → Schreib-/Löschrouten ohne Login. Verifiziert: `routes/web.php:695` `POST /branch_save`, `:1698` `DELETE /holiday_destroy/{id}`, `:1699` `POST /holiday_create`, `:1709` `DELETE /leave_day_destroy/{id}`. *(Mildernd: CSRF-Token via `VerifyCsrfToken` in der `web`-Gruppe vorhanden; App laut Projektnotiz nur lokal, nicht deployed — senkt akute Ausnutzbarkeit, ändert die Code-Klasse nicht.)*
- **[P1] IDOR auf Gehalt/Personaldaten.** Salary/Leave/Sick-Routen nur `auth` (`routes/web.php:1682-1692`); `SalaryController`, `SalarySheetController`, `LeaveController`, `EmployeeSickController` enthalten **keine** `authorize/Gate/can/is_admin`-Prüfung. `GET /salary_sheet/{id}` (`:1684`), `GET /employees/{id}` (`:1689`) → jeder Nutzer kann fremde Gehälter per `{id}` durchzählen und lesen/schreiben.
- **[P1] Write ohne Server-Validierung.** `CustomerMeasureController.php:31` (`create($request->all())` ohne `validate`), `Product/PV/RadiatorController.php:31`, `Report/DailyReportWorkPlaceController.php:48`.
- **[P2] Mass-Assignment.** 5 Models mit `guarded=[]` (`PlannerItemChecklist.php:10`, `TimeSummary.php:11`, `MasterSetTaskLabor.php:10`, `KlimaPlz.php:11`, `Asset.php:11` — keine Auth-/Credential-Felder, daher P2); `Salary.php:20` `guarded=['id']` (alle Gehaltsspalten außer id massen-zuweisbar). **22** Controller-Stellen mit `$request->all()` direkt in `create/update` (Liste: `HandoverController.php:148`, `Product/ProductWPController.php:77`, `Inventory/AssetInstallmentController.php:99,:180` (Modell `Asset`=guarded[]), u.a.).
- **[sauber] Raw-SQL.** Kein SQL-Injection-Vektor gefunden: geprüfte `whereRaw/DB::raw`-Stellen sind parametrisiert oder gegen interne Spalten-Whitelist/Backtick-Strip abgesichert (`LeadOverviewController.php:3908` `whereRaw('… >= ?', [...])`; `PlannerPlanController.php:6497` `str_replace('` + "`" + `','',$column)`). **Keine hartkodierten Secrets.** Das ist gut.

### (b) Daten-Risiko

- **[P1] Fehlende Transaktionen bei Mehr-Tabellen-Schreiben.** Nur **96** von 387 Controllern nutzen `beginTransaction`/`DB::transaction`. Die drei Speichern-Muster für `lead_product_lists` (1.4) schreiben teils über mehrere Tabellen ohne Klammer → Teil-Schreibungen bei Fehler möglich. *(Gegenbeispiel korrekt: `BuchungsEngine::festschreiben()` klammert sauber.)*
- **[P1] Fehlende FK auf God-Table.** `lead_alternative_adds` (193 Sp.) hat 1 FK; Kunden-Kinder mit `customer_id` ohne Constraint → Waisen bei Löschung. **SoftDelete-FK-Falle:** FK-Constraints ignorieren `deleted_at`.
- **[P1] `customer_id`-Doppeldeutigkeit + Zombie `customers`.** `customer_id` zeigt in 47 Tabellen auf `new_leads`, in 19 auf die 0-Zeilen-Tabelle `customers`. Race/Fehl-Join-Verdacht bei jedem generischen `customer_id`-Zugriff.
- **[Race-Verdacht] Stufen-Move.** Der Kanban-Move ist per Hook gut abgesichert; parallele Moves derselben Karte ohne `lockForUpdate` außerhalb der FiBu **NICHT-VERIFIZIERT** als getestet.

### (c) Wartbarkeit

- **[P1] Gott-Klassen.** `NewLeadsController.php` (14.054 Z., 121 Methoden, 267 `DB::table`), `PlannerPlanController.php` (11.097 Z.). Jede Änderung dort ist hochriskant und untestbar (keine Tests, 1.7).
- **[P1] Duplikation mit Divergenz.** `deriveLeadStageId` vs. `normalizeCompanyStage` mit echtem Fold-Unterschied (1.4) — der gefährlichste Wartungs-Fund, weil er inkonsistente `lead_stage_id` je Schreibweg erzeugen kann.
- **[P1] Logik in Blades.** ~101k Zeilen Inline-JS in den Top-10-Views (1.6); `kanban.js` 17k Z. Funktionszoo. Fachlogik (Stufen-Übergänge, Preisberechnung) teils clientseitig.
- **[P2] Magic Strings / Status-Zoo.** 202 hartkodierte Status-Literale in Controllern; 139 varchar-`status`-Spalten ohne Enum/Werteliste.
- **[P2] Toter Ballast.** 37 nicht-autoladbare `Old/`-Controller, ~110 copy/backup-Dateien, ~28 „Old Code"-View-Ordner (1.5).

### (d) Performance

- **[P1] Fehlende Indizes** auf abfragbaren `_id`-Spalten (`personal_tasks.source_id/controller_id/task_id`, `main_appointments.contact_id`) — Scan-Risiko bei Wachstum (1D-C).
- **[P1] `SELECT *` auf God-Table.** `lead_alternative_adds` (193 Sp., 30 TEXT) in 25 Controllern geladen; breite heiße Leselast.
- **[P2] Log-/History-Tabellen ohne Rotation.** `notifications`, `lead_activity_logs`, `*_histories` strukturell unbegrenzt.

## 2.3 🔧 BESSER MACHEN (realistisch, inkrementell)

| # | Ist → Soll | Aufwand | Nutzen | Risiko live | Art |
|---|---|---|---|---|---|
| 1 | Ungegate Write-Routen → `permission:`-Middleware Zug um Zug je Domäne anhängen (Fundament existiert) | M | Sicherheit ↑↑ | mittel (Rollen müssen befüllt sein) | Strangler |
| 2 | `web`-only-Gruppen → in `auth`-Gruppe verschieben (35 Gruppen) | S | Sicherheit ↑↑ | niedrig | inkrementell |
| 3 | Gehalt/Lohn/Urlaub → `is_admin`/HR-Gate + Owner-Check | S | DSGVO/Vertrauen | niedrig | Punkt-Fix |
| 4 | `normalizeCompanyStage` löschen, überall `LeadProductList::deriveLeadStageId` nutzen | S | Konsistenz `lead_stage_id` | niedrig | Punkt-Fix |
| 5 | Status-Freitext → `enum`/Werteliste + Konstanten je Kern-Tabelle | M | Wartbarkeit | mittel (Datenbereinigung) | Strangler |
| 6 | Mehr-Tabellen-Schreiber in `DB::transaction` klammern | M | Datenintegrität | niedrig | inkrementell |
| 7 | God-Table `lead_alternative_adds` additiv zerlegen (Detail-Tabellen nach `pv_wp_details`-Muster) | L | Performance/Erweiterbarkeit | mittel | Strangler |
| 8 | Zombie `customers` belegt stilllegen, 19 FKs auf `new_leads` umhängen (eigener beauftragter Posten) | M | Semantik-Falle weg | mittel | eigener Posten |
| 9 | `Old/`-Ordner + copy/backup-Views löschen (nach Referenz-Beweis) | S | Rauschen ↓ | niedrig | Aufräumen |
| 10 | Inline-JS der Top-Views schrittweise extrahieren (wie kanban.js, dann modularisieren) | L | Wartbarkeit | mittel | Strangler |

**Keine Rewrites.** Der Kern bleibt; er wird strangweise in den bewiesenen Neu-Stil gezogen.

## 2.4 🚨 DRINGEND

### P0 — sofort (Sicherheit/Datenverlust)

**P0-1: `web`-only Write/Delete-Routen ohne `auth`.**
- Mini-Fix: Die 35 `Route::group(['middleware' => 'web'], …)` mit Schreibrouten auf `['web','auth']` heben (oder alternativ `auth` global in die `web`-Gruppe in `app/Http/Kernel.php:35-43` aufnehmen — Achtung: dann müssen Login/öffentliche Routen explizit ausgenommen werden).
- Verifikation: `php artisan route:list --json | jq` — jede POST/PUT/PATCH/DELETE-Route trägt `auth`; Smoke-Test: ausgeloggter `POST /holiday_create` → 302/401 statt 200.

**P0-2: Gehalt/Lohn/Urlaub/Krankheit ungegated (IDOR).**
- Mini-Fix: HR-Routen-Gruppe (`routes/web.php:1682-1692` + Salary/Leave/Sick) mit `is_admin` bzw. `permission:hr,*` versehen; im Controller Owner-/Rollen-Check (`abort_unless($user->isAdmin() || $user->id === $employee->user_id, 403)`).
- Verifikation: als Nicht-Admin `GET /salary_sheet/{fremde_id}` → 403; Feature-Test analog `DealMeasurementPolicyTest`.

### P1 — vor Wachstum (mit Mess-Beleg)

- **P1-1 Autorisierung flächig nachrüsten** — nur 5/1211 Write-Routen gegated. Bei 2. Filiale/mehr Nutzern nicht tragbar. Domänenweise `permission:`-Middleware + `user_rolls` befüllen.
- **P1-2 `deriveLeadStageId`-Duplikat entfernen** — Fold-Divergenz erzeugt inkonsistente `lead_stage_id`. Mess-Beleg: Wege liefern für `nachfassen`/`annehmen` unterschiedliche Keys.
- **P1-3 God-Table `lead_alternative_adds`** — 193 Sp./30 TEXT, `SELECT *` in 25 Controllern; bei 10k Kunden Lese-Flaschenhals. Additiv zerlegen.
- **P1-4 Fehlende Indizes** (`personal_tasks.source_id/controller_id/task_id`, `main_appointments.contact_id`) — bei 200+ Karten/wachsenden Tasks Scan.
- **P1-5 Transaktions-Klammer** für Mehr-Tabellen-Schreiber (nur 96/387 Controller mit Transaktion).

### P2 — im Zuge laufender Stränge

- Status-Freitext → Enum/Werteliste (mit den ohnehin laufenden Domänen-Umbauten).
- Zombie `customers` stilllegen (eigener beauftragter Drop-Posten, Trail erhalten).
- `Old/`-Ordner + copy/backup-Ballast löschen.
- Inline-JS-Extraktion der Top-Views.
- Alpine-Verletzung `planner/list.blade.php` auf jQuery zurückbauen oder Ausnahme dokumentieren.

---

## Selbstkritik

- **Datenbasis dünn:** Die Dev-Restore ist zu 82 % leer (363/442 Tabellen 0 Zeilen). Alle Größen-/Wachstums-Aussagen sind strukturell, nicht gemessen — an echten Prod-Daten könnten Index-/Scan-Befunde schärfer oder milder ausfallen.
- **Stichproben statt Vollzählung:** Die Query+Logik-Mischung ist an `NewLeadsController` und wenigen Groß-Controllern belegt, nicht über alle 387 Controller quantifiziert (die 14:1-LOC-Ratio stützt die Verallgemeinerung, ersetzt aber keine Vollzählung).
- **Nicht tief geprüft (NICHT-VERIFIZIERT):** vollständiger Plaintext-Spalten-Scan über 442 Tabellen; vollständige Zyklen-Analyse; Snapshot-Invalidierungs-Logik je Feld; echte Runtime-N+1-Messung (nur statisch aus `->with(`/`DB::table` abgeleitet); vollständige Prüfung, ob die Qualifikations-Hierarchie irgendwo erzwungen wird.
- **TABU respektiert:** Nuriva, Video/Jitsi, Invoice-Zone, Legacy Bitrix/NIBE/IMAP nur gezählt, nicht bewertet.

## Gelesen / Nicht-gelesen (Bilanz)

**Gelesen/gemessen:** komplette Datei-/LOC-Landkarte (Controller/Models/Services/Views/JS/Routen/Tests exakt); Schema aller 442 Tabellen via `information_schema` (Spalten, FK=921, Indizes, Zeilen, Status-Zoo=150, JSON, Namens-DE/EN, `customer_id`-Doppelziel); `LeadProductList`-Hook + `deriveLeadStageId` (firsthand); `CheckUserPermission`/`Kernel.php`/`bootstrap/app.php` (firsthand); Route-Gating-Zählung (firsthand `web.php`); `guarded=[]`-Models + Mass-Assign-Stellen (firsthand); Test-Suite-Inventur + Qualitäts-Stichprobe; kanban.js-Struktur + Inline-JS-Anteile; Duplikations- und Dead-Code-Inventur; die 8 Gold-Zonen (Hook, Harness, FollowUpCreator, Seeder-Marker, Registry, Migration-down, Rechte-Fundament, Services).

**Nicht gelesen / nur oberflächlich:** die einzelnen Methodenkörper der meisten 387 Controller (nur Groß-Controller im Detail); TABU-Zonen inhaltlich; die 706k Zeilen Blade im Detail (nur Top-10 vermessen); jede der 52 Test-Dateien im Volltext (Stichprobe 4).
