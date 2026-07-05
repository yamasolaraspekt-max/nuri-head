# NAV-01 · QUELLE 5 — Unverlinkt-Befund (Einarbeitung in die Schicksals-Tabelle)

> **Additives Begleitdokument** zu `ticket-NAV-01-finale-navi-liste.md` (Master). Das Master-Dok wird gerade von der Parallel-Instanz aktiv editiert — daher hier separat abgelegt (kein Clobbering), **bereit zum Einmergen** als §6 bzw. in §2. **Reine Analyse — kein Code/Nav/Route geändert.**
>
> **Quelle 5** = Route↔Sidebar-Diff (route:list 2026-07-03): **850** GET-Seiten-Routen · **138** verlinkt (Sidebar + Topbar/Quick + Dashboard) · **712** unverlinkt → **77** belastbar (echte Blade-Vollseiten ohne `{id}`). Ergänzt §2 (Schicksals-Tabelle). Diese Quelle war im Master **noch nicht** enthalten (nur „Agent-Nav-Audit, 116 Items").

## 6a · A-Kandidaten (~22) → Schicksal „war unsichtbar → neu verortet"
| Seite (Route) | heute | Schicksal (Zielbereich) | Form |
|---|---|---|---|
| Meine Leads (`my.leads` /my_leads) | unverlinkt | → **2** Anfragen & Leads | **Tab in Leadliste** |
| Anfragen-Gesamt (`inquiry.view` /inquiry_view) | unverlinkt | → **2** | **Tab „Alle" bei Anfragen** |
| Rechnungsübersicht (`admin.invoices.index` /admin/invoices) | **toter Menülink** (6e) | → **5** produktiv / Weiche-3 | Punkt „Rechnungen" (Pflicht-Stopp 6e) |
| Deal-Rechnungen (`deal.invoice` /deal/invoices) | Alt-Schiene | → **nur-URL** (Weiche-3) | s. 6e |
| Wareneingang (`admin.goods-receipts.index`) | unverlinkt | → **7** Lager | **Tab bei Bestellungen** (oder eigener Sub) |
| Import-Utilities (`datanorm.form`, `admin.products.csv-import.index`, `…images.csv-import.index`) | unverlinkt | → **KEINE Sidebar-Punkte** | **Aktionen** in Artikel-Fläche (7) bzw. Verwaltung (11) |
| Tagesbericht-Übersicht (`daily.report`) | unverlinkt | → **1/6** Berichte | Tab/Ansicht |
| Projekt-Cockpit (`planner.projects`), Planner-Cockpit (`planner.cockpit`) | unverlinkt (ggf. indirekt) | → **6** Montage | Ansicht/Tab (prüfen ob = `planner.index`-Landing) |
| Mitarbeiter-Verfügbarkeit (`employee.availability`) | unverlinkt | → **10** Personal | Tab bei „Zeit" |
| Krankmeldungen (`employee.sick.index`) | **halbfertig (View fehlt)** | → **10** (Tab Urlaub & Abwesenheit) | **geplant** (View nachziehen) |
| Neue Benutzerverwaltung (`admin.users.page` /admin/users) | unverlinkt (Menü hat alte `/user`,`/admin_user`,`/limit_user`) | → **11** Benutzer & Rechte | **konsolidieren** (welche Benutzer-UI führt? offen) |
| Stammdaten-5 (`building.type.view`,`heating.type.view`,`tiles.view`,`temp.view`,`inquiry.type.info`) | unverlinkt | → **11** (Zugang & Stammdaten) | **Tabs** (Geschwister von `country.info`/`tax.info`/…) |
| Wirtschaftlichkeit (`economic_calculations.index`) | unverlinkt (deckt IA-3) | → **8** Energie (oder Vertrieb-Aktion) | prüfen |
| Moser-Import (`admin.leads.moser_wp.index`, `…moser_wp_invoice.index`) | unverlinkt, **nischig/kundenspezifisch** | → **11** (Utility) | Aktion/nur-URL; **prüfen ob noch nötig** |
| Projekt-Timeline (`timeline`) | unverlinkt, **Zweck unklar** | → **6**? | **prüfen** (nicht raten) |

## 6b · B-Kandidaten (~35) → „bewusst nur-URL/Kontext" (NICHT verlinken, aber dokumentiert)
- **`.create`-Formulare** aus Elternlisten: `branch.create`, `teams.create`, `lead-email-accounts.create`, `admin.supplier-connectors.create`, `admin.maintenance.contracts.create`, `task_phase_create`, `purchase_request_create`, `request_out_create` → kein Menüpunkt.
- **Kundenakte-/Kontext-Tabs:** `deal.details` (`deal_details`), `plan.details` (`plan_details`) → nur mit Kunde/Deal.
- **Dubletten zu bereits verlinkten Punkten (Beleg → eine Wahrheit behalten, Rest nur-URL):** `knowledge.index`↔`knowledge.base` · `task.phase.index`↔`task_phase` · `products.favorite-lists`↔`product.favorites.index` · `new.leads`↔`new_lead_view` · `deal.measurements.index`↔`deal.measurements.kanban`.
- **Fragmente ohne Layout (X):** `customer-reviews.index`, `main.appointment` (`appointment_table`), Kanban-Fragmente (`kanban.archive/junk/tickets`, `lead.kanban.investment`) → Partials, **ignorieren**.

## 6c · C (Legacy) → Raus-Kandidaten mit Beleg
- **NIBE** `Api\ApiLinkController` (`nibe/devices`, `nibe/auth`, `nibe/refresh`) → Legacy, ignorieren (Memory: Bitrix/NIBE). `Old/`-Controller (37) ohnehin nie in Nav.

## 6d · D (Utility/Kiosk/Mobile/Dev) → nur listen, keine Hauptnavi
- `terminal` (Stempel-Kiosk, Fullscreen), `employee_dashboard/mobile`, `mobile/mobile-calendar`, `qrcode_details` + `employee/qr/create`, `notAdmin`/`notweb` (Berechtigungs-Fehlerseiten).

## 6e · ⚠️ PFLICHT-STOPP: Rechnungen (Weiche-3-Sperre)
- **Verifiziert (route:list 2026-07-03):** `route('invoices.index')` — im Master **§1/Bereich 5** referenziert — **existiert NICHT**; der Sidebar-Link fällt auf `#` (`$safeRoute`). Reale Seite = **`admin.invoices.index`** (`/admin/invoices`). Alt-Schiene **`deal.invoice`** (`/deal/invoices`) lebt ebenfalls.
- **Offene Frage (Pflicht-Stopp, nicht raten):** Warum ist `invoices.index` tot — Route auf `admin.`-Prefix umbenannt und Sidebar nicht nachgezogen?
- **Die Nav-Fusion entscheidet NICHT, welche Schiene führt** (`invoices` vs. `deal_invoices`) — das ist **Weiche 3** (Steuerberater/Accounting-Instanz).
- **Vorgehen (nur Navi, NICHTS an Invoice-Flächen/Routen ändern):** in der Zielnavi **EIN** Punkt „Rechnungen"; sein **Ziel** ist **OFFENE YAMA-FRAGE** (→ Master §4 Frage 6). Interim: auf das **heute erreichbare** Ziel zeigen (`admin.invoices.index`); Alt-Schiene `deal.invoice` **nur-URL** (nicht als 2. Menüpunkt).
- **Master-Korrektur (Vorschlag, NICHT im Master überschrieben):** §1/Bereich 5 „Rechnungen → `invoices.index`" auf **`admin.invoices.index`** korrigieren, sobald Weiche 3 das führende Ziel bestätigt.

## 6f · Noch offen (sobald vollständig)
- Feinzuordnung Projekte/Reporting (`daily.report` · `planner.projects` · `timeline`) + finale Einordnung der D-Utilities — nach Bestätigung nachtragen.

---
*Reine Analyse (Quelle 5, route:list 2026-07-03). Zum Einmergen in `ticket-NAV-01-finale-navi-liste.md` §2/§6, sobald die Parallel-Instanz das Master-Dok nicht mehr aktiv editiert.*
