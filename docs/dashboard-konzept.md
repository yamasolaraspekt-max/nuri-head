# Dashboard-Analyse & Konzept (Read-Only)

> Stand der Analyse: 2026-06-29 · Branch `private/app-code-backup` (zum Analysezeitpunkt aktiv von anderen Prozessen verändert, HEAD bewegte sich von `82ecff6` → `ee0a309`).
> Dieses Dokument ist eine reine **Bestandsaufnahme + Konzept**. Es wurde **kein Anwendungscode geändert**.
> Alle Belege als `Datei:Zeile`. Drei Blickwinkel je Dashboard: **[A] Architekt** (Technik), **[P] Personal** (Alltag), **[GF] Geschäftsführung** (Steuerung).

---

## Teil 1 — Inventar: Welche Dashboards/Übersichten gibt es?

Es gibt **nicht nur ein Dashboard**, sondern ein zusammengesetztes System. Das sichtbare „Mein Dashboard" ist eine einzige große Blade-Datei, die per AJAX viele eigenständige Controller als Widgets nachlädt. Daneben existieren mehrere Vollseiten-Übersichten.

| # | Dashboard / Übersicht | Für wen | Route(n) | Controller | View / Ausgabe |
|---|---|---|---|---|---|
| 1 | **„Mein Dashboard"** (Hauptseite, Widget-Raster, pro Nutzer anpassbar) | Mitarbeiter (alle) | `/`, `/home`, `/employee_dashboard`, `/employee_dashboard/mobile` (`routes/web.php:606,607,680,681`) | `EmployeeDashboardController@index` / `@mobile` (`:60`, `:235`) | `resources/views/admin/dashboard/employee/mobile.blade.php` (**13.425 Z**) |
| 2 | **Fokus Heute / „Mein Arbeitstag"** (Widget) | Mitarbeiter | `/my/due-today` (`routes/web.php:610`) | `EmployeeDashboardController@getDueToday` (`:751`) | Partial `partials/focus_today.blade.php` (1.087 Z) |
| 3 | **Abteilungs-Dashboard** (Widgets „Abteilungsübersicht/-analyse/Team") | Mitarbeiter/Teamleitung | `dashboard.department.departments` / `.overview` (`routes/web.php:479,480`) | `Dashboard\DashboardDepartmentController` (**1.615 Z**) | reine **JSON-API**, gerendert in `mobile.blade.php:6803` |
| 4 | **Firmen-Cockpit** (GF-KPIs) | Geschäftsführung | `dashboard.company.overview` (`routes/web.php:455`) | `Dashboard\DashboardCompanyController` (464 Z) | JSON + Partial `partials/company-cockpit.blade.php` (324 Z), eingebunden `mobile.blade.php:6680` |
| 5 | **Mitarbeiter-Status** (Aktiv/Krank/Urlaub-Pillen oben) | alle | `dashboard.employee-status.index` (`routes/web.php:459`) | `Dashboard\DashboardEmployeeStatusController` (416 Z) | JSON, Frontend in `mobile.blade.php:5887ff` |
| 6 | **Live-Inbox** (Benachrichtigungs-Feed) | Mitarbeiter | `dashboard/live-inbox/*` (`routes/web.php:462-466`) | `Dashboard\DashboardLiveInboxController` (117 Z) + `Services\DashboardLiveActivityService` | JSON |
| 7 | **Kalender-Widget** | Mitarbeiter | `dashboard.calendar.*` (`routes/web.php:469-472`) | `Dashboard\DashboardCalendarWidgetController` (489 Z) | JSON |
| 8 | **Abwesenheits-Antrag** (Widget+Modal) | Mitarbeiter | `dashboard.absence-request.*` (`routes/web.php:475,476`) | `Dashboard\DashboardAbsenceRequestController` (159 Z) | JSON; Partials `absence-request-widget/-modal.blade.php` |
| 9 | **Chart-Widgets** (Arbeitsstunden, Mini-Analyse, HR) | Mitarbeiter | `employee.dashboard.personal_hours_chart` / `.mini_analytics_chart` / `.hr_widget` (`routes/web.php:619-624,616`) | `EmployeeDashboardController@personalHoursChart`/`@miniAnalyticsChart`/`@hrWidget` (`:1908,:1977,:2081`) | JSON |
| 10 | **Overdue-/Berichts-Center** (Vollseite + Dashboard-Widget „48h überfällig") | Teamleitung/GF + Mitarbeiter | `/admin/overdue-center`, `/admin/overdue-center/recent`, `/admin/reports` (`routes/web.php:634-642`) | `Report\OverdueCenterController` (**4.618 Z**) | Vollseite `recent_reports_center.blade.php` (**8.108 Z**); Widget `partials/overdue48h.blade.php` (596 Z) |
| 11 | **Lead-/Vertriebs-Kanban** (Pipeline-Übersicht) | Vertrieb/Vertriebsleitung | `/lead/kanban` u.v.m. (`routes/web.php:888ff`) | `Customer\Kanban\LeadOverviewController` (**7.002 Z**) | `resources/views/admin/kanban/kanban.blade.php` (**5.071 Z**) |
| 12 | **Anpassungs-System** (Widget-Registry, Schnellzugriffe, Icons) | alle | `dashboard.widgets.*`, `dashboard.shortcuts.*`, `dashboard.saveOrder` (`routes/web.php:443-452,683`) | `Dashboard\DashboardWidgetController` (466 Z), `UserDashboardShortcutController` (362 Z), `DashboardIconController` (93 Z) | JSON; Registry aus `database/seeders/DashboardWidgetSeeder.php` |

**Karteileichen / Altlasten (nicht aktiv eingebunden):**
- `resources/views/admin/dashboard/employee/dashboard.blade.php` (180 Z) — eigene „Übersicht Dashboard"-Variante mit Tabs (Persönlich/Abteilung/Benutzerdefiniert), wird von **keinem** Controller gerendert (toter Code); enthält hartcodierte Begrüßung „Willkommen zurück, **Torsten**" (`:11`).
- `resources/views/admin/dashboard/employee/mobile.blade copy.php`, `…/Old/…`, `…/old codes/…` (8+ Kopien), `admin/dashboard/test.blade.php` (Route `routes/web.php:4666`).
- `app/Http/Controllers/Old/OverdueCenterController copy.php`.

**Das Widget-Inventar des Haupt-Dashboards** (23 Keys, `DashboardWidgetSeeder.php:28-470`):
- *Persönlich (13):* `feed` (Geburtstage/Feiertage), `clock`, `hr` (Zeiten & Abwesenheit), `absenceRequest`, `focus`, `myLiveInbox`, `personalChart`, `notes`, `employeeCalendar`, `shortcuts`, `miniChart`, `todayWeather`, `empty`.
- *Abteilung (8):* `deptOverview`, `deptTeam`, `deptCharts`, `deptRecent`, `deptChanges`, `deptPie`, `deptBar`, `deptHistory`.
- *Firma (7):* `companyOverview`, `companyMainArea`, `companyRevenue`, `companyTypes`, `companyDepartmentPerformance`, `companyDepartmentList`, `companyHistory`.

---

## Teil 2 — Bewertung je Dashboard

### 2.1 „Mein Dashboard" (Hauptseite) — `mobile.blade.php`

| Kriterium | Bewertung |
|---|---|
| **Inhalt** | Ambitioniert: Uhrzeit, Wetter, Zeiten/Abwesenheit, Fokus Heute, Arbeitsstunden, Notizen, Schnellzugriffe, Abteilungs- und Firmen-Cockpit in einem. Inhaltlich grundsätzlich passend für einen Mitarbeiter-Einstieg. Aber: mischt Mitarbeiter-, Abteilungs- und GF-Inhalte in **einer** Ansicht ohne Rollentrennung. |
| **Design** | Optisch modern (Karten, Lucide-Icons, Inter-Font). Aber alles **inline**: 8 `<style>`- und 10 `<script>`-Blöcke in einer 13.425-Zeilen-Datei. CSS-Variablen werden in mehreren Partials erneut definiert (z.B. `overdue48h.blade.php:2-12`) → Stil-Duplikate. |
| **Usability** | Drag/Drop, Resize, Ein-/Ausblenden pro Nutzer (gespeichert) ist gut gedacht. Aber: kein echter Empty-State; viele Widgets zeigen „–" bis AJAX antwortet; bei API-Ausfall (Wetter) bleibt das Widget hängen. |
| **Sinnhaftigkeit** | Der Anspruch „ein Dashboard für alle Rollen, frei konfigurierbar" ist überladen. Für die meisten Mitarbeiter sind Firmen-/Abteilungs-KPIs irrelevant und nur unnötige Komplexität. |
| **Verlinkungen** | 27 `route()`-Aufrufe laden Widgets per AJAX (`:6763-6818`). Die Detail-Links entstehen erst in den JS-Templates (`href="${…}"`, `:7241,:11810,:13117`) — also nicht statisch prüfbar, fehleranfällig. |

**Belege & Befunde [A]:**
- **`index()` und `mobile()` sind ~95 % identischer Code** (`:60-232` vs. `:235-367`), beide rendern dieselbe View `admin.dashboard.employee.mobile`. Einziger Unterschied: `index()` liefert zusätzlich `activeDepartments`, `myCustomerCount`, `myProjectCount` (`:178-230`); `mobile()` nicht. ~300 Zeilen Duplikat. **`/employee_dashboard/mobile` liefert also eine inhaltlich ärmere Variante derselben Seite.**
- **Tabs „projects" und „offers" stürzen ab:** `loadTabContent()` ruft `$this->getProjects(...)` (`:421`) und `$this->getOffers(...)` (`:423`) auf — **diese Methoden existieren nicht** (im ganzen Controller nicht definiert) → `Call to undefined method`.
- **`getTabCounts()` ist nicht lauffähig:** nutzt `Task::count()` und `Appointment::count()` (`:683,:684`), ohne `use App\Models\Task`/`Appointment`. `App\Models\Task` existiert gar nicht; `Appointment` ist nicht importiert → Klassen werden als `App\Http\Controllers\Task/Appointment` aufgelöst → Fatal Error. Route ungeschützt: `routes/web.php:609` (kein `auth`).
- **Tote Demo-Daten in der Live-View:** `const focusData = [{… user:'Max Mustermann'}, {… user:'Torsten'}, …]` (`:6845-6854`) + `renderFocusList()` (`:8008`) werden bei Init aufgerufen (`:7283`). Wird aktuell nicht angezeigt, weil die HTML-Container `focusListContainer`/`focusSearch` **nicht existieren** (0 Treffer) → `renderFocusList()` bricht früh ab. Echte Daten kommen aus `focus_today.blade.php` via `route('my.due.today')`. → **Leftover-Demo-Code mit Fake-Namen** sollte raus.
- **Wetter ohne Cache/Fallback:** `getWeatherData()` → `getCurrentLocation()` ruft extern `https://ipinfo.io/json` (`:722`), dann Tomorrow.io (`:736`, Key `env('DASHBOARD_KEY')` via `config/services.php:52`). Bei Ausfall/Rate-Limit: Exception, kein Fallback, **kein Cache** (`grep Cache:: → keine Treffer`). Zwei externe Abhängigkeiten für ein dekoratives Widget.
- **„Montage"-Stunden sind immer 0:** `personalHoursChart()` setzt `$montageHours = 0;` hart (`:1949`) und summiert das in jeden Tag (`:1955`). Die Chart-Serie „Montage" ist also dauerhaft leer/irreführend.
- **N+1 / viele Einzelqueries:** `miniAnalyticsChart()` feuert 6 separate Roh-`DB::table()`-Count-Queries (`:1987-2048`) pro Aufruf.

**[P] Personal:** Funktional als Tagesstart brauchbar (Fokus Heute, Notizen, Schnellzugriffe). Stört: abstürzende Tabs, „leere" Widgets, Wetter das mal lädt/mal nicht. **[GF]:** Kein verlässliches Steuerungsbild — KPIs sind in dasselbe Mitarbeiter-Dashboard gequetscht.

---

### 2.2 Fokus Heute / „Mein Arbeitstag" — `focus_today.blade.php` + `getDueToday`

- **Inhalt:** Aggregiert heute fällige persönliche Aufgaben, Termine, Tickets, Ticket-Tasks, Leads, Anfragen (`EmployeeDashboardController@getDueToday`, `:751-1313`, sehr lange Methode mit vielen Roh-Queries). Inhaltlich das **sinnvollste** Mitarbeiter-Widget.
- **Usability:** Lädt eigenständig per `data-load-url="{{ route('my.due.today') }}"` (`focus_today.blade.php:1`), „erledigt"-Aktion über `markAsDone` (`:1314`). Gut.
- **[A]-Schwäche:** `getDueToday()` und `markAsDone()` sind je mehrere hundert Zeilen Roh-SQL im Controller — gehört in einen Service/Query-Builder. Mitarbeiter-ID über `auth()->user()->name` (`:753`) — projektweites Muster (s. Teil 3).
- **Bewertung:** [P] hoch, [GF] indirekt (zeigt Auslastung). Behalten, technisch sanieren.

---

### 2.3 Abteilungs-Dashboard — `DashboardDepartmentController` (1.615 Z)

- **Inhalt:** KPIs je Abteilung (Team-Größe, Leads, Kunden, Objekte, Angebote, Aufträge, Tickets, Termine, Aufgaben, Rechnungssummen offen/bezahlt; `:381-434`), 7 Charts (`:207-230`, u.a. `workload_by_employee`, `revenue_by_status`, `lead_pipeline`), je 8 „neuste" Items pro Typ. Inhaltlich reichhaltig und für Teamleitung relevant.
- **Verlinkungen — TOTE LINKS:** `recentLeads()`/`recentLeadProducts()`/`customerMini()` bauen `url('/customer/profile/'.$id)` (`:750,:798,:1367`) — **diese Route existiert nicht**; korrekt wäre `/new_lead_profile/{id}` (`routes/web.php:795`). → **3 defekte Klickziele.** `customer/appointments/{id}` (`:1031`) fraglich. Andere Links (`/deal/{id}/profile`, `/problem/profile/{id}`, `/personal-tasks/{id}/profile`) sind gültig.
- **[A]-Schwächen:** N+1 in `workloadByEmployee()` — Schleife über alle Mitarbeiter mit je 2 Roh-Queries (`:1149-1165`); 8 fast identische `recent*`-Methoden (~2.000 Z, `:646-1039`) → eine generische Methode; `Schema::hasColumn()` im Hot-Path (`:1328`); 3 separate Invoice-Klone statt 1 `selectRaw` (`:422,:426,:430`); reine `DB::table()`-Queries ohne Models.
- **Sicherheit:** `overview()` hat **keinen** Department-Zugriffscheck (`:78-122`) — ein Nicht-Admin kann via `?department_id=…` fremde Abteilungs-KPIs abfragen (nur `findOrFail`).
- **Bewertung:** [GF/Teamleitung] inhaltlich gut und gewünscht, aber tote Links + fehlende Berechtigung + Performance sind echte Mängel.

---

### 2.4 Firmen-Cockpit — `DashboardCompanyController` (464 Z) + `company-cockpit.blade.php`

- **Inhalt (GF-KPIs, `:86-134`):** Mitarbeiter-/Bereich-Anzahl, Leads gesamt/neu, Angebote, Aufträge, Rechnungen, **Umsatz (bezahlt), offene Forderungen**, Monatsumsatz-Chart, Vorgänge nach Typ, Rechnungs-Status, Bereich-Performance, Top-20 Aktivitätslog. **Das ist die richtige Stoßrichtung für die GF.**
- **[A]-Schwächen:** N+1 in `departmentPerformance()` — pro Abteilung `departmentLeadCountByProducts()` (`:315-316,:424-430`); Roh-`DB::table('lead_activity_logs')` (`:434`); ineffizientes `pluck→filter→unique→values→all` (`:42-47`); kein Empty-State (`:149`).
- **Sicherheit / Sinnhaftigkeit:** **Keinerlei Berechtigungsprüfung** — Umsatz, offene Forderungen und Aktivitätslog sind für **jeden** eingeloggten Nutzer als Widget verfügbar. Aus GF-Sicht ein Vertraulichkeits-Problem.
- **Verlinkungen:** Reine `data-widget-id`-Anker, **kein Drill-Down** (Bereichs-Tabelle zeigt nur IDs, keine Verlinkung zu Details).
- **Bewertung:** [GF] inhaltlich wertvoll, aber gehört **nicht** ins offene Mitarbeiter-Dashboard, sondern in eine geschützte GF-Ansicht mit Drill-Down.

---

### 2.5 Mitarbeiter-Status — `DashboardEmployeeStatusController` (416 Z)

- **Inhalt:** Zähler aktiv/inaktiv/krank/Urlaub/wiederkehrend/gesamt + Listen mit Abwesenheitsgrund/-zeitraum (`:68-101`). Sinnvoll für Teamleitung/Disposition.
- **[A]-Schwächen:** Hartcodierte Status-Strings statt Enum (`:31-34`); 3 separate Abwesenheits-Queries + `merge/unique` statt UNION (`:111-127`); **kein Berechtigungscheck**; `profile_url` hart als `url('employee_profile/'.id)` (`:385`).
- **Bewertung:** [P/Teamleitung] nützlich. Technik solide-genug, aber Status-Werte zentralisieren.

---

### 2.6 Live-Inbox — `DashboardLiveInboxController` (117 Z) + Service

- **Inhalt:** Ungelesen-Zähler + Feed (Typ, Aktion, Titel, Nachricht, URL, gelesen) (`:45-49`); read/unread/read-all.
- **[A]-Schwächen / Sicherheit:** Filtert nur über `employee_id` aus `auth()->user()->name` (`:95-99`) — keine harte Nutzer-Bindung. Ziel-URLs im Service **hartcodiert** (`/personal-tasks`, `/calendar`, `/new_lead_view/{id}`, `/offer/{id}` …) und inkonsistent (`/inquiry/` vs. `/inquiries/`, `:136-137`).
- **Bewertung:** [P] gutes Konzept (ein Posteingang für Vorgänge), aber Links über `route()` statt hartcodiert; Berechtigung absichern.

---

### 2.7 Kalender-Widget — `DashboardCalendarWidgetController` (489 Z)

- **Inhalt:** Mitarbeiter-Liste, Monats-/Tagesansicht mit Berichts-/Ticket-Markern, Termin-Detail (Kunde, Produkt, Ticket, Berichte) (`:24,:43,:117,:146`).
- **[A]-Schwächen:** N+1 durch 6× `orWhereHas()` mit Subqueries (`:238-255`); **keine Berechtigung** — `month(employee_id=X)` für beliebige Mitarbeiter abrufbar (`:45,:119`); `json_decode` ohne Fehlerbehandlung (`:290-293`).
- **Bewertung:** [P] nützlich. Berechtigung + Eager Loading nötig.

---

### 2.8 Abwesenheits-Antrag — `DashboardAbsenceRequestController` (159 Z)

- **Inhalt:** Antrag (Urlaub/Krank) direkt aus dem Dashboard, Liste möglicher Genehmiger (`:35-43,:64-102`).
- **[A]/Sicherheit:** **Dokumente in `public/images/employees/sick_documents/` ohne Zugriffsschutz** (`:110-124`) — Krankmeldungen öffentlich abrufbar (Datenschutz!). **Pfad-Bug:** Speicherung `sick_documents/`, Ausgabe-URL `sick_document/` (`:110` vs. `:143`). Keine Prüfung, ob `request_to` Genehmiger ist; kein Größenlimit; jeder kann Antrag für Beliebige stellen.
- **Bewertung:** [P] praktisch, aber Sicherheits-/Datenschutz-Mängel sind **dringend** (eigenes Sofort-Thema).

---

### 2.9 Overdue-/Berichts-Center — `OverdueCenterController` (4.618 Z)

- **Inhalt:** Vollseite `/admin/overdue-center` + Widget „48h überfällig". Zeigt >48h untätige Vorgänge in 5 Typen (Anfrage, Aufgabe, Termin, Ticket, Lead-Produkt) mit Überfälligkeits-Stunden, Priorität, Status, Verantwortlichen; Filter + Bulk-Berichtsspeicherung. **Inhaltlich ein echtes Steuerungsinstrument.**
- **[A]-Schwächen:** Monster-Controller — `recentReportsFetch()` **782 Zeilen** (`:2484`), `statusLabelDe()` 191 Z (`:3279`), `overdueInquiries()` 153 Z (`:1112`); 5 quasi-identische `overdue*()`- und `isEmployeeInvolvedIn*()`-Methoden; Schema-Checks zur Laufzeit (`:65,:1114`); View `recent_reports_center.blade.php` **8.108 Z** mit Inline-CSS/JS.
- **Verlinkungen:** Widget ist rein AJAX (keine href, `overdue48h.blade.php:309-596`); in der Vollseite `href="#"` als JS-Platzhalter (`recent_reports_center.blade.php:4703`) — bei JS-Fehler toter Link.
- **Sinnhaftigkeit:** [Teamleitung/GF] **ja** für Erkennung liegengebliebener Vorgänge. **Aber:** keine Team-/Bereichs-Aggregation (nur „alles oder nichts" via `canViewAll`, `:732-735`), keine Trend-/Historie, kein Eskalations-Workflow. `recent_reports_center` wirkt eher wie ein Entwickler-Debugger als ein Management-Tool.

---

### 2.10 Lead-/Vertriebs-Kanban — `LeadOverviewController` (7.002 Z)

- **Inhalt:** Pipeline-Board (Lead → Nachfassen → Angebot → Auftrag → Montage → Abschluss + Archiv/Junk), Live-Activity je Lead, Statistik-Cards (Mitarbeiter/Produkte/Kunden/Anfragen, Status-Breakdown), Filter, Stage-History (JSON), Team-Zuweisung je Stage, Ticketize, Termine. **Mächtigste Übersicht im System.**
- **[A]-Schwächen:** 7.002 Zeilen, 60+ private + 30+ public Methoden, ~276 `DB::table/DB::raw`-Zugriffe, 40+ Routen auf einen Controller; `changeStage()` 370+ Z mit großer Transaktion (`:1959-2300`); N+1-Risiko im `kanbanFeed()` (`:465-619`); View `kanban.blade.php` 5.071 Z. **Fehlende `index()`** trotz Route `/lead/overview` (`routes/web.php:888`) → potenzieller 404.
- **Sinnhaftigkeit:** [Vertrieb/Vertriebsleitung] **hoch** — vollständige Pipeline mit Live-Updates. Aber Performance- und Wartungsrisiko durch die Codegröße.

---

### 2.11 Anpassungs-System (Widgets / Schnellzugriffe / Icons)

- **Inhalt:** Widget-Registry (DB, `DashboardWidgetSeeder.php`), Schnellzugriffe (`shortcutRegistry()` 10 Ziele, `UserDashboardShortcutController:278-361`), Alt-System „Karten-Reihenfolge" (`DashboardIconController:20-30`, hartcodierte 9er-Liste; `DashboardIcon`-Model leer).
- **[A]-Schwäche — drei parallele Konzepte für „was sieht der Nutzer":** Widgets (DB), Shortcuts (halb-hartcodiert) und Icons/Karten (alt, hartcodiert) existieren nebeneinander. **Permission-Check nur bei Shortcuts** (`hasPermission()`, `:30-49`); die **Widget-Registry hat keine Berechtigungsprüfung** → alle 23 Widgets (inkl. Firmen-Umsatz) sind für jeden sichtbar.
- **Bewertung:** Auf **ein** System konsolidieren; Alt-Icons (`DashboardIconController`, Route `dashboard.saveOrder`) entfernen.

---

## Teil 3 — Übergreifende Schwächen (was sich durchzieht)

**A) Technik / Architektur**
1. **Riesige Dateien statt Komponenten:** `mobile.blade.php` 13.425 Z (8 `<style>`, 10 `<script>`), `recent_reports_center.blade.php` 8.108 Z, `kanban.blade.php` 5.071 Z; Controller mit 7.002 / 4.618 / 1.615 Zeilen. Inline-CSS/JS überall, CSS-Variablen mehrfach dupliziert.
2. **Code-Duplikate:** `index()`≈`mobile()` (~300 Z), 8× `recent*` im Department-Controller (~2.000 Z), 5× `overdue*`/`isEmployeeInvolved*` im Overdue-Center.
3. **Kaputte/halbfertige Stellen:** Tabs `projects`/`offers` ohne Methode (Crash), `getTabCounts()` mit nicht existenten Klassen (Crash), „Montage"-Stunden hart 0, Demo-Daten („Torsten"/„Max Mustermann") im Live-View, tote View `dashboard.blade.php`.
4. **Externe Abhängigkeit ungekapselt:** Wetter hängt an ipinfo.io + Tomorrow.io, ohne Cache/Fallback/Timeout-Strategie.
5. **Roh-SQL statt Eloquent + N+1:** durchgängig `DB::table()`/`DB::raw()`; N+1 in Department-, Company-, Calendar-, Kanban-Code; `Schema::hasColumn/hasTable` zur Laufzeit.
6. **Identitäts-Muster `auth()->user()->name` als Mitarbeiter-ID:** projektweit (z.B. `EmployeeDashboardController:62`, Company `:25`, Live-Inbox `:97`, Overdue `:2407`). Funktioniert nur, weil `users.name` die Employee-ID speichert — fragil, fehleranfällig, erschwert jede Umstellung. *(Hinweis: laut Projekt-Setup bewusst so; trotzdem Risiko.)*

**B) Sicherheit / Datenschutz**
7. **Fehlende Berechtigungsprüfungen** in Company-, Department-, Calendar-, Employee-Status-, Live-Inbox-Endpunkten → vertrauliche Daten (Umsatz, offene Forderungen, fremde Kalender/Status) sind für jeden Eingeloggten abrufbar.
8. **Krankmeldungs-Dokumente öffentlich** unter `public/…/sick_documents/` ohne Access-Control.
9. **Ungeschützte Routen:** `/dashboard/tab-counts` (`routes/web.php:609`) ohne `auth`.

**C) Verlinkung / Konsistenz**
10. **Tote/falsche Links:** `/customer/profile/{id}` (3×, Department), `href="#"`-Platzhalter (Overdue-Vollseite), inkonsistente Inbox-URLs (`/inquiry/` vs. `/inquiries/`), Abwesenheits-Doc-Pfad-Bug.
11. **Kein einheitliches Link-Konzept:** Detail-Ziele teils per `route()`, teils hartcodiert per `url('…')`, teils erst im JS-Template gebaut → nicht statisch prüfbar.

**D) Fehlende GF-Kennzahlen / Rollentrennung**
12. **Keine Rollentrennung:** Mitarbeiter-, Abteilungs- und Firmen-Inhalte liegen in **einer** Seite. Es gibt **kein** dediziertes, geschütztes GF-Dashboard.
13. **Fehlende Steuerungsgrößen für die GF:** keine Zeitreihen/Trends (Umsatzentwicklung, Pipeline-Velocity, Abschlussquote, Durchlaufzeit Lead→Auftrag), keine Ziel-/Ist-Vergleiche, keine Aggregation überfälliger Vorgänge nach Bereich/Team, kein Drill-Down von KPI → Liste → Datensatz.

---

## Teil 4 — Konzept-Vorschlag

Leitidee: **Drei klar getrennte, rollenbasierte Dashboards** statt einer überladenen Allzweck-Seite — gespeist aus denselben, aufgeräumten Daten-Services.

### 4.1 Zielbild je Rolle

**(1) Mitarbeiter-Dashboard „Mein Arbeitstag"** *(für alle)*
- Inhalt: Fokus Heute (Aufgaben/Termine/Tickets/Anfragen), Live-Inbox, persönliche Arbeitsstunden (Soll/Ist), Notizen, Schnellzugriffe, Abwesenheits-Antrag, Wetter/Uhr optional.
- **Kein** Firmen-Umsatz, **keine** fremden KPIs.
- Anpassbar (Widgets ein/aus) — aber nur aus dem für die Rolle freigegebenen Set.

**(2) Teamleitungs-Dashboard „Mein Team / Bereich"** *(Teamleitung)*
- Inhalt: Mitarbeiter-Status (anwesend/krank/Urlaub), Auslastung je Mitarbeiter, **überfällige Vorgänge aggregiert nach Person/Team**, Abteilungs-KPIs, neueste Vorgänge mit Drill-Down.
- Verlinkung: jede KPI klickbar → gefilterte Liste → Datensatz.

**(3) Geschäftsführungs-Cockpit** *(GF, geschützt)*
- Inhalt: Umsatz bezahlt/offen **als Zeitreihe**, Pipeline-Wert & -Velocity, Abschlussquote, Durchlaufzeit Lead→Auftrag, Auftragsbestand, Top/Flop-Bereiche, Rechnungs-Status. Ziel-/Ist wo Ziele gepflegt sind.
- Drill-Down von jeder Kennzahl bis auf Einzeldatensatz.
- **Welche KPIs final aufs GF-Cockpit gehören und welche Zielwerte gelten → vom Nutzer (GF) zu entscheiden.**

> Der bestehende **Vertriebs-Kanban** bleibt als eigenständige Pipeline-Ansicht (verlinkt aus Team-/GF-Dashboard), wird aber nicht ins Haupt-Dashboard gepresst.

### 4.2 Verlinkungs-Struktur (einheitlich)
- Jede Zahl/Karte ist klickbar und führt zu einer **gefilterten Liste**, von dort zum **Datensatz** (KPI → Liste → Detail).
- Alle Ziele über benannte Routen (`route(...)`), **keine** hartcodierten `url('…')`-Pfade im JS. Eine zentrale JS-Routen-Map statt verstreuter Strings.
- Tote Links sofort beheben (s. Sofort-Liste).

### 4.3 Technische Sanierung
- `mobile.blade.php` in Layout + Widget-Partials + ausgelagerte CSS/JS-Assets zerlegen.
- Daten-Logik aus Controllern in **Services/Query-Objekte** (Department-, Company-, Overdue-, Focus-Services); `recent*`/`overdue*` generisch zusammenfassen.
- **Berechtigungen** (Policies/Gates) für alle KPI-/Status-/Kalender-Endpunkte; Widget-Registry mit Permission-Filter.
- Externe Dienste (Wetter, Geo) in einen Service mit **Cache + Timeout + Fallback** kapseln.
- Auf **ein** Personalisierungs-System konsolidieren (Widget-Registry); Alt-Icons/`DashboardIconController` entfernen.

### 4.4 Priorisierte Maßnahmen

**SOFORT (Bugs/Sicherheit, klein & risikoarm)**
1. Abstürzende Tabs reparieren: `getProjects()/getOffers()` implementieren **oder** Tabs entfernen (`EmployeeDashboardController:421,423`).
2. `getTabCounts()` reparieren (richtige Models importieren) oder Route entfernen (`:683,684`; `routes/web.php:609` zusätzlich `auth`).
3. Tote Links `/customer/profile/{id}` → `/new_lead_profile/{id}` (`DashboardDepartmentController:750,798,1367`).
4. Krankmeldungs-Dokumente aus `public/` herausnehmen + geschützter Download; Pfad-Bug `sick_document(s)` (`DashboardAbsenceRequestController:110,143`).
5. Demo-Daten `focusData`/`renderFocusList` und tote View `dashboard.blade.php` (+ „Torsten") entfernen (`mobile.blade.php:6845-6854,8008`).
6. Berechtigungsprüfung auf Company-/Department-/Calendar-/Employee-Status-/Live-Inbox-Endpunkte.
7. „Montage"-Stunden korrekt berechnen oder Serie ausblenden (`:1949`).

**MITTEL (Konsolidierung & Struktur)**
8. `index()`/`mobile()` zusammenführen (ein Pfad, eine Datenquelle).
9. `mobile.blade.php` in Partials + ausgelagerte Assets zerlegen; CSS-Variablen zentralisieren.
10. Wetter/Geo in Service mit Cache/Fallback kapseln.
11. N+1 in Department/Company/Calendar/Kanban beheben (`withCount`, Eager Loading, Batch-Queries).
12. Personalisierungs-Systeme auf Widget-Registry konsolidieren; Alt-Icons entfernen.
13. Overdue-Center: Aggregation nach Team/Bereich ergänzen; `recentReportsFetch()` (782 Z) und `recent_reports_center.blade.php` (8.108 Z) aufteilen.

**SPÄTER (strategisch)**
14. Rollenbasierte Dashboards (Mitarbeiter / Teamleitung / GF) als getrennte, geschützte Seiten umsetzen.
15. GF-Cockpit mit Zeitreihen, Pipeline-/Vertriebskennzahlen, Ziel-/Ist und durchgängigem Drill-Down.
16. `LeadOverviewController` (7.002 Z) in Service-Layer + Query-Objekte refaktorieren; fehlende `index()` für `/lead/overview` klären.
17. Identitäts-Muster `auth()->user()->name` mittelfristig auf eine saubere `employee_id`-Beziehung umstellen *(großes, projektweites Thema)*.

### 4.5 Ausdrücklich „vom Nutzer (GF/Fachbereich) zu entscheiden"
- **Welche KPIs** final aufs GF-Cockpit gehören und welche **Zielwerte/Benchmarks** gelten.
- **Rollen-/Sichtbarkeits-Matrix:** Wer darf Umsatz, offene Forderungen, fremde Mitarbeiter-Status/Kalender sehen?
- Ob der **Vertriebs-Kanban** die führende Pipeline-Ansicht bleibt (Empfehlung: ja, separat) oder ins Team-Dashboard integriert wird.
- Welche der **Alt-/Karteileichen** (alte Views, `mobile.blade copy`, „old codes") archiviert vs. gelöscht werden dürfen.
- Eskalations-/Erinnerungs-Policy im Overdue-Center (ab wann, an wen, wie).
