# Audit: `playground` (Laravel-Backend) — unabhängige Reifeprüfung

**Stand:** 2026-06-28 · **NUR LESEN** — in beiden Projekten nichts geändert/exportiert.
**Frage:** Ist playgrounds Fundament tragfähig genug, um **zukünftig das Basissystem** zu werden (mehrere Standorte, später international), auf das tickets CI, Layout und Spezial-Features (Kanban, Kundenprofil) aufgesetzt würden?
**Methode:** 5 parallele Agenten, **am Code verifiziert** (Datei:Zeile), playgrounds Selbsteinschätzung (`BEFUND.md`) NICHT als Wahrheit übernommen — wie beim ticket-Audit.

---

## ⭐ GESAMTURTEIL: **JA — MIT VORBEHALTEN**

playgrounds Fundament ist **echt und gut gebaut** — kein Demo-Gerüst, sondern reale Fachlogik quer durch alle Domänen, mit einer saubereren Architektur und einem saubereren Datenmodell als ticket. Als **Basissystem grundsätzlich geeignet**, und tickets wertvolle Features (Kanban, Kundenprofil) passen konzeptionell gut auf playgrounds Datenmodell.

**ABER:** Genau die zwei Eigenschaften, die für die Zukunft entscheidend sind, sind **noch nicht wirklich gebaut**, sondern nur „vorbereitet":
1. 🔴 **Mandantenfähigkeit ist NICHT echt umgesetzt** — faktisch Einzelmandant. Für „mehrere Standorte" der **kritischste** Punkt.
2. 🔴 **Keine Internationalisierung** — Deutsch/EUR/DE-Steuerrecht fest verdrahtet. „Später international" = eigenes großes Projekt.

Beides ist **gezielte Nacharbeit auf solider Basis (kein Neubau)** — deshalb „JA mit Vorbehalten", nicht „NEIN". Vor Produktivbetrieb sind zusätzlich konkrete **Sicherheitslücken** zu schließen.

---

## Die 6 Prüfdimensionen (code-verifiziert)

### 1. Struktur & Skalierbarkeit — 🟡 tragfähig mit Nacharbeit
- **Service-Layer: echt, aber ungleich.** Buchhaltung = saubere Constructor-Injection-Services (`OutgoingInvoiceController.php:17`, `JournalController.php:19`). Außerhalb: **156/279 Controller importieren keinen Service**, ~130–140 validieren inline, 154 enthalten Inline-Eloquent-Queries. Keine reine Fassade, aber Konsistenz-/Wartbarkeitsthema.
- **Pagination = der reale Engpass.** Nur **40/279 Controller** nutzen `paginate()`; **86 `index()`-Methoden** geben `->get()` ohne Limit zurück (z. B. `CustomerController.php:27` = alle Kunden, `BusinessContactController.php:42`). Nur 58/279 nutzen Eager Loading (`->with()`). Bestätigtes N+1: `BusinessContactController.php:643`. → **Hauptbremse bei vielen Nutzern/Daten** — aber gezielt behebbar.
- **Mega-Dateien:** eine Handvoll Hotspots (`OfferController` 840 Z., `FormBuilderController` 815, `FormulaEvaluationService` 902) — kein flächiges Problem; Models gesund (max 289).
- **Modular, aber im Übergang:** `web.php` ist ein 1577-Zeilen-Monolith (753 Inline-Routen) **parallel** zu 120 `routes/modules/*.php`. Saubere Schichtung Api/Modules/Pilot, React-SPA wurde abgerissen, Blade-Migration läuft.
- **Indizes:** Kerntabellen (im Roh-SQL-Dump) gut indiziert (374 Keys); einzelne neuere Migrationen ohne FK-Index (`departments.branch_id`).

### 2. Mandantenfähigkeit — 🔴 nur vorbereitet, faktisch Einzelmandant (ENTSCHEIDEND)
- Von **173 Tabellen** tragen nur ~12–13 eine Mandantenspalte (`accounting_client_id`) — **fast ausschließlich Buchhaltung**. **Kern-Tabellen (`customers`, `projects`, `articles`, `employee_profiles`, `angebot_offers`) haben KEINE** Mandanten-/Standortspalte.
- **0 globale Scopes** im gesamten Code (`grep addGlobalScope app/` → kein Treffer). Kein `BelongsToTenant`-Trait.
- Die einzige Tenant-Middleware (`EnsureAccountingClientScope.php`) ist **per Design inert** („kein einziger gebundener Nutzer"); `users.accounting_client_id` ist überall `null`. Zusätzlich stiller Default `client_id = 1` in den Accounting-Controllern (`AccountController.php:20` u. v. m.) → **IDOR** selbst dort.
- **Leck-Test bestätigt:** `Customer::...->get()`, `Project::with(...)->get()`, `Article`, `EmployeeProfile`, `Offer` liefern **alle** Datensätze ohne Standortfilter. Trennung ist mangels Spalte technisch unmöglich.
- `branches`/`departments` = Stammdaten ohne Scoping-Wirkung; `User` hat **kein** `branch_id`.
- **Fazit:** „Mandantenfähig" steht in Schema-Kommentaren, ist aber nur für Buchhaltung *angelegt* und **nirgends durchgesetzt**. Standort A könnte Daten von Standort B sehen.

### 3. Internationalisierung — 🔴 fest verdrahtet DE/EUR
- **Keine Mehrsprachigkeit:** kein `lang/`/`resources/lang/`, keine `__()`-Infrastruktur; Texte/Fehlermeldungen deutsch hartkodiert.
- **Keine Mehrwährung:** `currency`-Spalten existieren, aber **immer** `->default('EUR')`; **keine** FX-/Umrechnungslogik (kein `exchange_rate`/`convertCurrency`). EUR hart in `Support/Format.php:23,29`.
- **DE-Recht fest:** UStVA, DATEV-EXTF-v700, SKR03-only-Bilanz, §-Bezüge (§146/§147 AO) im Code. → Auslandseinsatz = großes Projekt, kein Schalter.

### 4. Sicherheit — 🟠 behebbar, aber echte offene Stellen
- **Korrektur zur BEFUND.md:** Die `/app/*`-Bridge-Routen sind **doch** auth-geschützt — der Loader (`web.php:153-161`) umschließt alle 120 Modul-Dateien zentral mit `auth`. Die Behauptung „teils offene Auth auf Bridge-Routen" ist dafür **veraltet/falsch**. Aber: „fail-open"-Design (gesamter Schutz hängt an einer Schleife).
- 🔴 **HIGH — Unauthentifizierte Datenpreisgabe:** `/pilot/*/daten` (12 JSON-Endpunkte, `web.php:165-187`) geben Geschäftsdaten **ohne Login** aus — verifiziert: `/pilot/konten/daten` liefert den **Kontenplan**; ebenso Lieferanten, Mitarbeiter, Objekte, Produkte.
- 🔴 **HIGH — Klartext-Secrets:** `.env:20` `DB_PASSWORD=crm2024`, `.env:4` statischer Master-`API_TOKEN` (umgeht die Token-Middleware als „dev/master"-Pfad).
- 🔴 **HIGH — Local-Bypass:** `if (!env('API_TOKEN') && app()->environment('local')) return $next()` in `PermissionMiddleware`/`RoleMiddleware`/`ApiTokenMiddleware` → in `local` mit leerem Token sind **alle** Auth/Rollen/Rechte-Checks aus. Derzeit durch gesetzten Token entschärft, aber ein-Variablen-fragil.
- 🟠 **MEDIUM:** Klartext-DB-Credential im **git-getrackten** `docker-compose.yml:38`; `APP_DEBUG=true`; mehrere `api.token`-Routen ohne Feinrecht.
- ✅ Sauber: keine hartkodierten Secrets in `app/`/`config/` (alles via `env()`), keine Artisan/eval-Web-Closures, CSRF korrekt, kein ausnutzbares Mass-Assignment.

### 5. Wie echt sind die Funktionen — 🟢 überwiegend echt (kein Gerüst), aber halbe Ketten
- **Stichprobe 14 Module quer durch alle Domänen: ~11 FERTIG : 3 TEILWEISE : 0 GERÜST.** Kein Stub-Controller, kein leeres `return view()`, keine Platzhalter-Blade gefunden. (Die `Modules/*`-Controller sind bewusst dünne Bridges auf reale `Api/*`-Controller — Designmuster, kein Stub.)
- **Echte Fach-Mathematik:** PV-Stringauslegung mit IEC-62548/VDE-Normbezug (`InverterSizingService.php:56-141`), echte Doppik mit Balance-Prüfung/GoBD-Storno (`JournalService.php:36-177`), Heizlast-Tabellen, UStVA-Netting.
- **Die echten Schwächen sind unterbrochene/halbautomatische Ketten, nicht „Gerüst":**
  - ✅ Angebot→Auftrag: vollständig (`AuftragsUmwandlungsDienst.php`, atomar, idempotent).
  - 🔴 **Auftrag→Rechnung: Bruch** — kein direkter Pfad; `InvoiceController` hat kein `store()`/keinen `order_id`; Rechnung entsteht nur aus der Auftragsbestätigung.
  - 🔴 **Zwei Rechnungsschienen:** Schiene A (`angebot_invoices`, „Legacy-Marker", **ohne** Buchhaltungsanschluss) vs. Schiene B (`accounting_outgoing_invoices`, kanonisch). Nur durch `DoppelfakturaGuard` gegeneinander gesperrt.
  - 🟠 **Rechnung→Journal: nur Buchungs­vorschlag**, keine automatische Verbuchung; DATEV-Export in Prod hart gesperrt (423) bis Steuerberater-Freigabe.
  - 🟠 **UStVA-Kennzahlen nicht geseedet, Bilanz SKR03-only** — fachlich noch nicht steuer-validiert (Code labelt selbst „PRÜFPFLICHTIG").

### 6. Aufwand: tickets CI/Layout/Kanban/Kundenprofil in playground — 🟢 machbar, Muster passen
- **CI/Layout:** Stacks **gegensätzlich** — playground = Tailwind-CDN + Alpine + **1** Master-Layout (137 Views, eigenes `x-ui.*`-Design-System); ticket = Bootstrap-5/Vuexy-Theme + jQuery. tickets CI **wörtlich** durchzusetzen = **GROSS** (Rewrite, Konflikte). **Empfehlung: playgrounds (modernere, konsistentere) CI behalten**, optische Anleihen additiv über `x-ui.*` → praktisch **klein**.
- **Kanban: MITTEL** — playground hat **bereits** ein Alpine-Kanban (`modules/anfragen`, 12 `Inquiry`-Stages) inkl. Move-API; es fehlen Drag&Drop + konfigurierbare Stage-Tabelle (ticket hat editierbare `lead_stages`/Sub-Stages/Teams). tickets 793-KB-Engine **nicht** portieren — auf playgrounds Stack neu bauen.
- **Kundenprofil: MITTEL** — playground hat **bereits** eine Kundenakte (`modules/kundenakte`, KPI/Tabs/Timeline-Komponenten) und die Daten-Relationen (`Project` → offers/orders/invoices/appointments/tasks/timeline). Saubere Hierarchie **Kunde→Objekt→Projekt** statt tickets „alles an `new_leads`". tickets fehlende Tabs/Feeds nachbauen + über die Hierarchie aggregieren; tickets 23k-Zeilen-Blade **nicht** portierbar.
- **Kernaussage:** tickets Wert sind die **fachlichen Muster** (Stage-Workflow mit Audit; Akte mit Multi-Quellen-Timeline), nicht der Code/CI. Sie mappen sauber auf playgrounds Datenmodell.

---

## Voraussetzungen, damit playground Basissystem werden kann (priorisiert)
1. 🔴 **Mandantenfähigkeit nachbauen** (Pflicht für mehrere Standorte): `branch_id`/`tenant_id` in alle Kern-Tabellen, **erzwungene** Trennung via Global Scope/Trait + Pflicht-Middleware, Nutzer↔Standort-Bindung. Den stillen `client_id=1`-Default beseitigen.
2. 🔴 **Sicherheit härten** (Pflicht vor Produktiv): `/pilot/*/daten` schützen oder entfernen; Secrets aus `.env`/`docker-compose.yml` rotieren & aus Git nehmen; den `local`-Bypass entschärfen; `APP_DEBUG=false`.
3. 🟠 **Pagination** auf den großen Listen-Endpunkten — größter Skalierungs-Hebel.
4. 🟠 **Geschäftsketten schließen:** Auftrag→Rechnung-Pfad, die zwei Rechnungsschienen konsolidieren, Rechnung→Journal-Automatik, UStVA-Kennzahlen seeden.
5. 🟢 **(Wenn international:)** i18n-Layer + Mehrwährung + länderspezifische Steuer-Adapter — eigenes Projekt, nicht nebenbei.
6. 🟢 **Feature-Übernahme aus ticket** (Kanban, Kundenprofil) als Neubau auf playgrounds Stack — Muster passen, mittel.

## Einordnung gegen ticket
playground ist **strukturell und fachlich das stärkere Fundament** (modular, sauberes Datenmodell Kunde→Objekt→Projekt, echte Buchhaltung/HR/Energie, Tests) — ticket ist dafür das **produktiv-erprobte** System mit gereiften UX-Features. Für „Basissystem der Zukunft" spricht die Substanz von playground; die Vorbehalte (Mandanten, Security, i18n, halbe Ketten) sind die Hausaufgaben, die vor diesem Schritt erledigt sein müssen.
