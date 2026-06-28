# Inventar `playground` — NUR Laravel + Blade (Übernahme-Quelle)

**Stand:** 2026-06-28 · Reine Lese-Analyse, in beiden Projekten nichts geändert.
**Scope laut Vorgabe:** Es zählt **nur Laravel + Blade**. Der React/TypeScript-Teil von `playground` (SPA, 3D-Dachplaner mit Three.js, TS-Connector-Framework `integrations/*.ts`) ist **ausdrücklich ausgeschlossen** — stack-fremd, nicht nach `ticket` übernehmbar. Quelle ist daher **ausschließlich** `playground/backend-laravel/`.

> Hinweis am Rande: Das ist kein Verlust. `playground` migriert laut eigenem Plan (`ZIEL-STRUKTUR-UND-PLAN.md`) React → **Laravel-Blade + Alpine** selbst. Der Blade-Stand ist schon weit: **180 Blade-Dateien**, davon ein wachsender `views/modules/`-Baum mit **~84 Modulen**.

## Tech-Stack (Laravel-Seite)
| | `playground/backend-laravel` | Vergleich `ticket` |
|---|---|---|
| Framework | **Laravel ^11 / ^12** | Laravel 11 |
| PHP | ^8.2 | 8.4 |
| DB | MySQL `crm_erp` | MySQL `ticket` |
| UI | **Blade** (+ Alpine), API für die alte React-SPA | Blade (datengetriebene Sidebar) |
| Auth | **RBAC**: `roles` + `permissions` + Pivot, Middleware `role:*`/`permission:*`; Session **und** API-Token | `is_admin`-Bypass + `user_rolls`; `users.name = employees.id` |
| Realtime | `laravel/reverb` (WebSockets) | — (kein Reverb) |
| Excel | `phpoffice/phpspreadsheet` | teils vorhanden |
| Umfang | **279 Controller · 297 Models · 173 Migrationen · 166 Services · 120 Modul-Routendateien** | ~129 Menüpunkte, Blade-Monolith |
| Tests | PHPUnit, ~296 Testdateien | kaum |
| Architektur | **modular**: `routes/web.php` lädt 120 × `routes/modules/{modul}.php`; Service-Layer; Migrations-first | ein großes `web.php` |

## Reifegrad gesamt (aus playgrounds eigener `BEFUND.md` / Gesamtinventur)
**Fortgeschrittener, lauffähiger Prototyp / frühe Pilot-Reife** — sehr breit gebaut, >85 % datenbankecht, aber **nicht produktionsreif**: Auth auf neuen Bridge-Routen teils offen, einige End-to-End-Ketten unterbrochen (Angebot→Auftrag-Endpoint fehlt; Kostenstellen-Dimension geht bei Umwandlung verloren), Buchhaltung GoBD/DATEV-konzipiert aber mit offenen Lücken. **Merke: `ticket` ist das produktive Live-System, `playground` der ideenreiche Prototyp.** Übernommen werden **Datenmodelle + Geschäftslogik + Blade-Bausteine**, hart nachgezogen wird in `ticket`.

---

## Modul-Landkarte (Laravel/Blade) — gruppiert nach Domäne
Legende Blade-UI: **✅** = eigene `views/modules/`-UI vorhanden · ◌ = nur Route/Controller (API/Pilot)

### CRM / Kontakte / Kommunikation
`kunden`✅ · `kundenakte`✅ · `kontakte`✅ · `kontaktarten`✅ · `kontaktvorlagen`✅ · `kontakt-intelligenz` · `kontakt-resource` · `anfragen`✅ · `kommunikation`✅ · `benachrichtigungen`✅
Tabellen: customers, customer_contacts, customer_appointments, inquiries, objects, business_contacts, contact_types/templates, notification_reads. **Kommunikation via Reverb** (Chat/E-Mail-Events).

### Vertrieb: Angebote / Aufträge / Rechnungen / Verträge / Förderung
`angebote`✅ · `angebot-sets`✅ · `angebot-vorlagen` · `auftraege`✅ · `auftragsbestaetigungen`✅ · `rechnungen`✅ · `leistungen`✅ · `vertraege`✅ · `foerderungen`✅
Tabellen: angebot_offers / angebot_offer_items / offer_versions, orders / order_items / order_supplements / order_labor_lines / order_material_lines, angebot_invoices, vertraege, foerderungen/foerder_parameter. **Herzstück: „Angebotsampel" (Offer Traffic Light)** — Grün/Gelb/Rot blockiert Phasenwechsel bei fehlenden Pflichtdaten.

### Projekte / Montage / Aufmaß
`projekte`✅ · `projekt-akte`✅ · `projektprofile`✅ · `projekt-lohnkosten`✅ · `aufgaben`✅ · `aufgabenmaterial`✅ · `aufgabennachweise`✅ · `bautagesberichte`✅ · `feinaufmass`✅ · `montagevorbereitung`✅ · `innenauftraege`✅ · `entwicklungsberichte`✅
Tabellen: projects, project_profiles/_phases/_tasks, tasks, task_materials, task_proofs, project_daily_reports, feinaufmasse, project_documents/_images/_notes/_cost_records.

### Disposition / Planung / Kalender
`dispositionen`✅ · `termine`✅ · `planung-ansichten` · `kapazitaet-produktivitaet`✅ · `personalressourcen`✅ · `personalzuordnungen`✅
Tabellen: dispositionen, disposition_tasks, personnel_assignments, termine, planning_saved_views.

### Artikel / Lager / Einkauf
`artikel`✅ · `artikelgruppen`✅ · `artikel-stueckliste`✅ · `artikel-technische-daten`✅ · `artikelimport`✅ · `produktkatalog`✅ · `lieferanten`✅ · `bestellungen`✅ · `wareneingaenge`✅ · `materialentnahmen`✅ · `lagerorte`✅ · `inventur`✅
Tabellen: articles/article_groups/article_master_data/article_technical_specs/artikel_stueckliste, lieferanten/lieferanten_artikel, bestellungen/bestellpositionen, wareneingaenge/_positionen, materialentnahmen/_positionen, bestaende, lagerorte, stocktakes/_items. **Excel-/DATANORM-Import.**

### HR / Personal / Lohnvorbereitung / Organisation
`mitarbeiter`✅ · `arbeitsvertraege`✅ · `hr-prozesse`✅ · `lohnarten`✅ · `lohnvorbereitung`✅ · `personalnachweise`✅ · `zeiterfassung`✅ · `zeitauswertung` · `ueberstunden`✅ · `abteilungen`✅ · `abteilungsuebersicht`✅ · `abteilungs-guv`✅ · `teams`✅ · `niederlassungen`✅ · `gewerke`✅
Tabellen: employee_profiles, employment_contracts, hr_wage_types, hr_payroll_runs/_entries/_wage_lines/_approvals/_checks/_documents/_exports, hr_time_entries, hr_clock_events, hr_vacation_requests, hr_sick_leaves, hr_overtime_accounts, departments, branches, hr_teams, trades. **Vollständige Lohn-Vorbereitung mit Freigabe-Workflow.**

### Buchhaltung (DATEV / GoBD) — die größte eigenständige Suite
`buchhaltung`✅ + ~30 Submodule: `-journal` `-konten` `-kontenrahmen` `-bank` `-kasse` `-belege` `-rechnungseingang-ausgang` `-offene-posten` `-mahnwesen` `-nummernkreise` `-perioden` `-monatsabschluss` `-bilanz` `-bwa` `-susa` `-ustva` `-anlagen` `-dimensionen` `-kostenstellenrechnung` `-projektrechnung` `-buchungsvorschlaege` `-fristen` `-partner` `-steuerberater` `-pruefzentrum` `-gobd` `-gobd-protokoll` `-gate-protokoll`
Tabellen (60+): accounting_clients (Mandant), accounts/account_mappings, accounting_documents, accounting_outgoing_/incoming_invoices, accounting_journal_entries/_lines, accounting_bank_accounts/_cash_register, open_items, debtors/creditors, tax_codes/tax_rates, cost_centers, accounting_datev_exports, accounting_deadlines, accounting_dunning_runs/_items, payment_runs/_items. **Doppelte Buchführung, EXTF-/DATEV-Export, unveränderbares Journal, Festschreibung, UStVA/BWA/Bilanz/SuSa, Mahnwesen, AfA.** (Prototyp — DATEV-Testpaket noch „nicht bestanden", offene GoBD-Punkte.)

### Kundendienst
`tickets`✅ · `reklamationen`✅
Tabellen: tickets, ticket_nachrichten, ticket_notizen, serviceauftraege, reklamationen.

### Betriebsmittel / Fuhrpark
`betriebsmittel`✅ · `betriebsmittelarten`✅
Tabellen: betriebsmittel, betriebsmittel_arten/_kosten/_nutzungen/_reservierungen/_wartungsereignisse/_pruefplaene. **Fuhrpark/Maschinen mit Wartung, Reservierung, Prüfplänen.**

### Energie-Tools (NUR die Laravel-/Daten-Seite; 3D-Planer-UI ist React = ausgeschlossen)
`waermepumpe`✅ · `wp-auslegung`✅ · `wr-auslegung`✅ · `lastmanagement`✅ · `lastprofil`✅ · `konfigurator`✅ · `produktkatalog`✅ · `dachplaner-pro` (nur die Blade-Hülle; die 3D-Insel selbst ist React → raus)
Tabellen: auslegungen/_ergebnisse/_strings/_mppt, pv_modules/_specs, inverters/_specs, batteries/_specs, waermepumpe_specs, wp_kennfeld, lastprofil_*, roof_tiles/_templates/_coverings, mounting_components, solar_mounts. **Heizlast-/PV-/WP-Auslegungsrechner + Produktdatenbank.**

### Formulare (dynamische Abfrage-Engine)
`formulare`✅ · `formularbaukasten`✅ · `formular-antworten`✅ · `formular-berechnung`✅
Tabellen: dynamic_forms, form_sections, form_fields, form_field_options, form_answers, angebot_offer_item_calculations. **Produktabhängige Formulare + „Smartrouting" (FormRoutingService): Auswahl lädt automatisch Pflichtfelder/Dokumente/Aufgaben.**

### Controlling / Strategie
`controlling-kpi`✅ · `ziele` (OKR)✅ · `abteilungs-guv`✅
Tabellen: controlling_kpis, okrs, department_development_reports.

### Plattform / Auth / System / Sonstiges
`auth` · `rollen`✅ · `systemmodule`✅ · `einstellungen`✅ · `design-system`✅ · `lookup` · `uploads`✅ · `_uebersicht` · `veranstaltungen`✅ · `erkennung`✅ (Beleg-/Bild-OCR)
Tabellen: users, roles, permissions, role_user, permission_role, tenant_modules, file_uploads, history_entries (append-only Audit), media_assets, veranstaltungen, erkennung_*.

---

## Was bewusst NICHT im Inventar steht (ausgeschlossen)
- **React/TS-SPA** (`playground/src/**`) — ~218 Pages, React 19, Vite. Stack-fremd.
- **3D-Dachplaner** (`src/planer`, Three.js, ~260k Test-Zeilen) — beeindruckend, aber **React** → nicht nach `ticket` übernehmbar. Nur seine Laravel-Datenbasis (roof_tiles/templates, produktkatalog) ist relevant.
- **TS-Connector-Framework** (`integrations/*.ts`, Lieferanten-Connectoren IDS/OCI/UGL/DATANORM/BMEcat) — **TypeScript** → ausgeschlossen. Die Idee existiert zusätzlich als SQL-Schema + OpenAPI; eine Laravel-Neuimplementierung wäre ein Neubau, keine Übernahme.
- `app.jsx` (199 KB Legacy-Prototyp), Python-Cleanup-Skripte, JSON-Dumps — toter Root-Code.
