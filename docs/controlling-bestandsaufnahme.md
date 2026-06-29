# Controlling-Bestandsaufnahme — Kostenstellen-/Kostenträgerrechnung

**Read-only Analyse am Code/Datenmodell. Es wird nichts gebaut, nichts entschieden.**
Stand: 2026-06-29 · Branch: `private/app-code-backup` · Repo: `/Users/yamanuri/Documents/ticket`
Schema-Wahrheit = Migrationen in `database/migrations/`. Wo unsicher: „unklar/nicht gefunden" statt Vermutung.

> Zweck: erfassen, welche Bausteine für eine künftige Kostenstellen-/Kostenträgerrechnung (Controlling)
> bereits existieren, was nutzbar ist und welche **fachlichen** Lücken der Geschäftsführung / dem
> Steuerberater zur Klärung vorzulegen sind. Das Modul wird **nicht jetzt** gebaut.

---

## Kernbefund (Zusammenfassung)

1. **Es gibt nirgends ein Feld „Kostenstelle"/`cost_center`/`kostenstelle`.** Voll-Projekt-grep über
   `*.php`/`*.blade.php` (ohne vendor/node_modules) liefert **0 Treffer**. → Eine Kostenstellen-Dimension
   existiert im ticket-Datenmodell schlicht nicht.
2. **Struktur ist da:** Branch → Department (hierarchisch, `parent_id`) → Team, plus Mitarbeiter-Zuordnung
   über `employee_departments`. Tragfähige Basis für „Kostenstelle = Abteilung/Filiale".
3. **Umsatz lässt sich an die Abteilung hängen** — aber nur über die **Auftragsseite** (`deals.department_id`,
   `lead_product_lists.department_id`, `offers.department_id`, `projects.department_id`). Die **generische
   Rechnungstabelle `invoices` hat KEIN `department_id`**; dort wird Umsatz→Abteilung nur **heuristisch** über
   Kunde/Objekt/Ersteller zugeordnet.
4. **Kosten werden je Filiale (Branch) erfasst/aggregiert, NICHT je Abteilung verteilt.** Personal-, Miet-,
   Versicherungs-, sonstige Gemein- und Asset-Kosten hängen an `branch_id` bzw. an `branch_expenses`.
   Eine Verteilung auf Abteilungen/Projekte (Umlage, Deckungsbeitrag) existiert **nicht**.
5. **Die vorhandenen „Kalkulations"-Module sind kein Controlling:** `costing_sets`/`costing_set_roles` =
   Angebots-**Vor**kalkulation (Zuschläge); `economic_calculations`/`profitability_calculations` =
   **kundenseitige** Wirtschaftlichkeits-/ROI-Rechnung (Amortisation, CO₂). Keine **Ist**-Kostenrechnung.
6. **Keine Buchhaltung/DATEV in ticket** (reine Rechnungsstellung, keine Konten/Buchungssätze/Export).
   Die echte FiBu inkl. `cost_centers`, Kostenstellen- und Projektrechnung liegt laut vorhandenen
   Übernahme-Docs im **playground**-Prototyp (siehe Teil C).

Belegte Fundstellen in diesem Dokument: ~40 (Datei:Zeile bzw. Tabelle.Spalte).

---

## TEIL A — Technische Bestandsaufnahme (mit Belegen)

### A0. Gibt es ein „Kostenstelle"-Feld? — Nein
- Voll-Projekt-grep `cost[_-]?center|kostenstelle|kostentraeger|kostenträger|kostenrechnung|controlling`
  über alle `*.php`/`*.blade.php` (ohne vendor/node_modules): **keine Treffer**.
- Auch in `database/migrations/` (553 Migrationen) und `app/Models/` (≈300 Models): kein `cost_center`.
- → **Befund: keine Kostenstellen-Dimension im Schema.**

### A1. Abteilungen / Kostenstellen-Struktur — vorhanden und brauchbar
- **Branch (Filiale):** `branches` — `branch`, `employee_count`, `total_expense`
  (`2023_06_13_100801_create_branches_table.php:16,25,26`). `total_expense` ist ein **Aggregatfeld** je Filiale.
- **Department (Abteilung):** `departments` — `department_name`, `parent_id` (Hierarchie/Selbst-FK),
  `branch_id`, `department_head`, `order`
  (`2024_07_04_095539_create_departments_table.php:16-21,27-30`). → Filiale↔Abteilung verknüpft, Abteilungen
  hierarchisch.
- **Mitarbeiter→Abteilung:** `employee_departments` — `employee_id`, `department_id`
  (`2024_07_04_131852_create_employee_departments_table.php:16-17`). n:m-Zuordnung vorhanden.
- **Team:** `teams` + `team_members` (`2025_10_17_093731_create_teams_table.php`,
  `2025_10_17_093758_create_team_members_table.php`).
- **Position/Qualifikation:** `positions`, `department_positions`, `product_positions`,
  `position_qualifications` — Rollen/Qualifikationen je Abteilung.
- **Fazit A1:** Die Organisations-Struktur (Filiale/Abteilung/Team + Mitarbeiterzuordnung) ist sauber
  modelliert und wäre die natürliche Kostenstellen-Achse. **Was fehlt: ein expliziter Kostenstellen-Code/
  -Schlüssel** (heute nur die Department-/Branch-ID).

### A2. Umsätze je Einheit — vorhanden, aber zwei getrennte Erlös-Schienen
- **Auftrag/Deal (mit Abteilungsbezug):** `deals` — `price`, **`department_id`** (FK → departments),
  `order_number`, `customer_id`, `product_id`
  (`2025_02_05_125814_create_deals_table.php:20,24,28,47`). → **Umsatz je Abteilung ist über den Deal direkt
  zuordenbar.**
- **Ist-Rechnungsbeträge je Deal:** `deal_invoices` — `deal_id`, `invoice_amount`, `paid_amount`,
  `open_amount`, `invoice_type` (Abschlag/Schluss)
  (`2025_06_23_053704_create_deal_invoices_table.php:16,21-25`). Erlös→Abteilung-Pfad:
  `deal_invoices.deal_id → deals.department_id`.
- **Generische Rechnung (OHNE Abteilungsbezug):** `invoices` — `customer_id`, `object_id`, `invoice_no`,
  `subtotal`, `total_amount`, `tax_amount` — **kein `department_id`**
  (`2023_07_19_100437_create_invoices_table.php:17-20,33-36`); Positionen in `invoice_items`
  (`product_id`, `unit_price`, `line_total`, `2026_01_19_201601_create_invoice_items_table.php:16,24,26`).
- **Angebot:** `offers` trägt `department_id` (alter/extend-Migrationen; vom Dashboard genutzt, s. u.);
  Arbeits-/Material-Positionen in `offer_employee_lists` (`role`, `rate` €/h, `hours_total`, `sum_total` —
  `2025_08_28_071357_create_offer_employee_lists_table.php:21-26`) und `offer_product_lists`,
  `offer_asset_lists`. Das ist **Angebots-Kalkulation**, kein realisierter Umsatz.
- **„Gewerk"/Auftragsposition:** `lead_product_lists` trägt **`department_id`** und ist im Dashboard die
  primäre Abteilungs-Verankerung (s. A4).
- **Heuristik im Dashboard:** Für die generischen `invoices` (kein FK auf Abteilung) wird der Umsatz je
  Abteilung über Kunde/Objekt/Ersteller **erschlossen**, nicht hart verknüpft
  (`app/Http/Controllers/Dashboard/DashboardDepartmentController.php:638-642`,
  Summierung `SUM(total_amount)` in `invoiceByStatus()` `:1184-1188`).
- **Fazit A2:** Umsatz je Abteilung ist über die **Auftragsschiene** sauber abbildbar
  (`deals`/`deal_invoices`/`lead_product_lists`). Über die **generische Rechnungsschiene** nur unsicher
  (heuristisch). → Doppelte Erlös-Schienen = Mehrdeutigkeit beim „Umsatz je Kostenstelle".

### A3. Kosten je Einheit — je Kostenart erfasst, aber **filialbezogen**, nicht abteilungsbezogen

**a) Personalkosten / Löhne**
- Stammlohn am Mitarbeiter: `employees.salary_per_hour`, `working_hour`
  (`2023_06_13_090239_create_employees_table.php:24,44`).
- Monats-Lohnabrechnung (modern, „Vollkosten"): `salaries` — `emp_id`, `period_year/month`,
  `gross_monthly`, `employer_contrib_monthly`, `employer_total_monthly`, `total_monthly_salary`,
  `productive_hours_year` (`2024_01_08_150312_create_salaries_table.php:18,21-22,45,50-52,58`).
- Stundensatz-/Kalkulationsblatt (älter): `salary_sheets` — `labor_cost_hour`, `productive_hour`,
  `total_monthly_salary` (`2024_01_10_125544_create_salary_sheets_table.php:31,34,41`).
- **Zuordnung:** Lohnkosten hängen am **Mitarbeiter** (`emp_id`), **nicht an Abteilung/Projekt**. Eine
  Abteilungszuordnung ginge nur indirekt über `employee_departments`; eine **projekt-/auftragsscharfe**
  Verteilung der Lohnkosten gibt es nicht (Zeitbuchungen tragen keine Kosten — s. A4 Zeitwirtschaft).

**b) Material / Einkauf**
- Wareneingang: `goods_receipts` — `purchase_price`, `qty`, **`department_id`**, `article_group_id`,
  plus Auftrags-/Objektbezug `customer_id`/`object_id`/`lead_product_list_id`
  (`2026_02_26_070038_create_goods_receipts_table.php:18-23,46-48,68`). → **Material ist sowohl je Abteilung
  als auch je Projekt/Objekt zuordenbar** (einzige Kostenart mit beidem).
- Beschaffungsantrag: `purchase_requests` — `purchase_price`, `retail_price`, `quantity`, `customer_id`,
  `employee_id` (`2023_08_28_072139_create_purchase_requests_table.php:52,56,61,67,78`); **kein
  `department_id`**.
- Bestand/Bewegungen: `inventory`, `inventory_histories`, `inventory_request_outs` (vorhanden, nicht
  kostenstellen-getaggt).

**c) Fuhrpark / Maschinen / Betriebsmittel (Abschreibung/Finanzierung/Raten)**
- Anlagen/Betriebsmittel: `assets` — `purchase_price`, `purchase_type`, `leasing_*`, `branch_id`,
  `parent_id` (Set), `used_for`→article_groups
  (`2023_08_30_084628_create_assets_table.php:28,30,32-35,52,24,55`). **Kein `department_id`, keine
  Abschreibungs-/AfA-Felder** (nur Kauf/Leasing-Preise).
- Finanzierung/Ratenzahlung: `asset_installments` — `price_per_month`, `installment_duration`, `total`,
  `fines`, `insurance_amount`, `asset_id`, `branch_id`
  (`2024_03_28_092548_create_asset_installments_table.php:20-23,33,37,40`). Raten je **Asset+Filiale**, nicht
  je Abteilung.
- Maschinen: `machines` — `purchase_price`, `leasing_*`, `mileage`, `branch_id`
  (`2024_04_01_010402_create_machines_table.php:24,26-29,21,34`). **Kein `department_id`, keine AfA.**
- Service/Wartung: `machine_services`, `maintenance_assets` (Kostenpositionen vorhanden, filial-/anlagebezogen).
- **Hinweis Abgrenzung:** `electric_vehicles` ist ein **Produktkatalog** (verkaufbare E-Fahrzeuge,
  FK → `products`/`article_groups`, `2024_06_26_100120_create_electric_vehicles_table.php:16-17,37-38`) —
  **nicht** der interne Fuhrpark. Interner Fuhrpark = `assets`/`machines` (Kategorie Fahrzeug).
- **Fazit c:** Anschaffungs-/Leasing-/Ratenkosten sind erfasst, aber **nur Filial-, nicht Abteilungsebene**;
  **keine Abschreibung/AfA** im ticket-Schema; **keine km-/Nutzungs-Erfassung** zur verursachungsgerechten
  Fuhrpark-Verteilung.

**d) Miete / Versicherung / sonstige Gemeinkosten (je Filiale)**
- Container je Filiale/Jahr: `branch_expenses` — `branch_id`, `total`, `year`
  (`2024_10_09_150105_create_branch_expenses_table.php:16-18`).
- Miete: `branch_rents` (`rent_cost`, `extra_cost`, `total`,
  `2024_10_09_150106_create_branch_rents_table.php:18-20`), `rent_properties`
  (`living_space` = **m²/Wohnfläche vorhanden!**, `cold_rent`, `extra_cost`,
  `2024_10_11_095958_create_rent_properties_table.php:18,26-27`), `branch_rent_infos`
  (`cold_rent`, `electricity_cost`, `heating_cost`, `repair_cost`,
  `2024_10_11_095961_...:29-34`), `rent_extra_costs` (`title`, `cost`,
  `2024_10_11_124840_create_rent_extra_costs.php:17-18`).
  → m²-Wert je Mietobjekt **ist da** (`living_space`), aber **keine m²-Zuteilung je Abteilung** und kein
  Verteilungsmechanismus.
- Versicherung: `branch_insurances` — `insurance_for`, `monthly_payable`, `coverage_amount`,
  `branch_expenses_id` (`2024_10_11_095960_create_branch_insurances_table.php:17,20-21,24`). Je Filiale,
  keine Abteilungszuordnung.
- Sonstige Gemeinkosten: `branch_expense_other_costs` — `branch_id`, `category`, `amount`, `payment_cycle`,
  `vendor`, `invoice_no` (`2026_06_16_000005_create_branch_expense_other_costs_table.php:17-28`). Je Filiale,
  mit freiem `category`, **kein `department_id`**.
- **Fazit d:** Sämtliche Gemeinkosten hängen an der **Filiale** (`branch_id`/`branch_expenses`). **Keine
  Spalte und keine Logik, um sie auf Abteilungen herunterzubrechen.**

### A4. Zuordnungs-/Umlage-Logik — keine echte Umlage; nur Vorkalkulation + Aggregation
- **Keine Kostenumlage auf Abteilungen/Projekte.** Es gibt keinen Code, der `branch_expenses`, Löhne,
  Asset-/Mietkosten auf Abteilungen oder Aufträge verteilt; **kein Deckungsbeitrag/Marge je Einheit**.
- **Was es gibt — Vorkalkulation (Angebot):**
  - `costing_sets` — globale Zuschläge: `material_overhead_percent`, `labor_overhead_percent`,
    `site_overhead_percent/_fixed`, `risk_percent`, `profit_percent`, `commission_*`
    (`2026_03_05_112752_create_costing_sets_table.php:21-41`).
  - `costing_set_roles` — je Qualifikation: `wage_cost_per_hour`, `payroll_overhead_percent`,
    `company_overhead_percent`, `full_cost_rate_per_hour`, `sell_rate_per_hour`
    (`2026_03_05_112847_create_costing_set_roles_table.php:17-23`).
  - Anwendung: `app/Models/CostingSetRole.php:29-35` (`full = wage*(1+payroll%+company%)`),
    `app/Http/Controllers/CostingSetController.php:279-309`, MasterSet-Materialzuschläge
    `app/Http/Controllers/Product/MasterSet/MasterSetController.php:446-449`
    (`global_gemeinkosten`/`global_wagnis`/`global_mat_margin`).
  - → Das ist **Angebots-Preisbildung** (Zuschläge auf Positionen), **keine Ist-Kostenrechnung**.
- **Was es gibt — Aggregation (Reporting), keine Verteilung:**
  - `app/Http/Controllers/BranchExpenseController.php:421-426` summiert je **Filiale**:
    `employees->sum('salary')`, `asset_installments.sum('total')`, Maschinen-`purchase_price`.
  - `app/Http/Controllers/Dashboard/DashboardDepartmentController.php` aggregiert **je Abteilung**, wobei
    die Abteilung über `lead_product_lists.department_id` / `offers.department_id` / `deals.department_id`
    verankert wird (`:285,297,511,530,649,1236`); Umsatz via `SUM(total_amount)` (`:1184-1188,422-428`).
    → **Umsatz-Summierung je Abteilung, aber keine Kosten-Gegenrechnung** (kein DB/GuV je Abteilung).
- **Fazit A4:** Verteilungs-/Umlagelogik = **nicht vorhanden**. Vorhanden sind (1) Angebots-Vorkalkulation
  und (2) Umsatz-Aggregation je Abteilung. Die Brücke „Kosten je Kostenstelle/Kostenträger" fehlt komplett.
- **Zeitwirtschaft als mögliche Umlagebasis (Stunden):**
  - `daily_report_time_customers` — `share_hours`, `share_percent`, `customer_id`, `alternative_id`,
    `product_id` (auftragsscharfe Stundenanteile; `2026_05_22_140100_...`). **Beste Umlagebasis**, aber
    **kein `department_id`** und **keine Kosten** (nur Stunden).
  - `attendances` — `employee_id`, `customer_id`/`alternative_id`/`product_id`, `work_total_seconds`,
    `travel_total_seconds` (`2026_06_24_110816_add_planner_attendance_tracking.php`). Ist-Arbeits-/Reisezeit
    je Auftrag, **kein `department_id`**.
  - `daily_report_times` (`employee_id`, `customer_id`, `hours_spent`, polymorph `reportable_*`),
    `time_summaries` (Aggregat ohne `employee_id`), `time_management_plans/_entries` (nur Soll-Budget),
    `project_time_requests` (nur Zeit-**Anträge**). → Stunden sind teils auftragsscharf, aber **nirgends mit
    €-Kosten oder `department_id` verknüpft**; eine Stunden→Kosten→Abteilung-Kette müsste neu gebaut werden.

### A5. Buchhaltung / DATEV — nicht vorhanden (isoliert)
- **Keine FiBu-Schicht in ticket:** keine Konten/Kontenrahmen (SKR03/04), keine Buchungssätze/Journal,
  kein DATEV-Export, keine Sachkonten. Voll-Projekt-grep (`datev|buchhaltung|fibu|kontenrahmen|skr0|
  sachkonto|ledger|journal|lexware|sevdesk|lexoffice`) ohne vendor/node_modules: **keine substanzielle
  Treffer** — nur Fehlalarme (z. B. `datevId = substr(customerNo,-5)` als Logging in
  `app/Http/Controllers/Customer/NewLeadsController.php:610-612`; `'Buchhaltung'` als Demo-Abteilungsname im
  Seeder; `applyStatusAccounting()` = Zahlungsstatus, keine Buchung).
- ticket kann **Rechnungen** (`invoices`/`invoice_items`, `deal_invoices`), aber **ohne**
  Buchhaltungsanschluss/Konten/Kostenarten. → **Isolierte Rechnungsstellung, keine Buchhaltung.**

---

## TEIL B — Fachliche Lücken: Entscheidungen VOR dem Bau

> Diese Punkte sind **fachlich zu klären — NICHT vom System zu raten — durch Geschäftsführung /
> Steuerberater.** Das System liefert hierfür heute **keine** Defaults.

1. **Kostenstellen-Definition.** Ist „Kostenstelle" = Filiale (`branches`), = Abteilung (`departments`),
   = Abteilung je Filiale, oder eine **eigene** Kostenstellen-Systematik (ggf. an DATEV-Kostenstellen
   angelehnt)? → *fachlich zu klären — NICHT vom System zu raten — Geschäftsführung/Steuerberater.*
2. **Umlageschlüssel Verwaltungs-/GF-/Overhead-Kosten.** Verteilung nach Mitarbeiterzahl, Umsatzanteil,
   geleisteten Stunden oder festem Schlüssel? (System hätte Bausteine für alle Varianten, aber keine
   Vorgabe.) → *fachlich zu klären …*
3. **Fuhrpark-Verteilung.** Nach km/Nutzung (heute **nicht** erfasst), nach fester Zuordnung Fahrzeug→
   Abteilung, oder pauschal? → *fachlich zu klären …*
4. **Maschinen/Betriebsmittel — Abschreibung vs. Zahlung.** Sollen **AfA/Abschreibung** kalkulatorisch in
   die Kostenrechnung (heute nur Kauf-/Leasing-/Ratenbeträge, **keine AfA-Felder**) oder
   Zahlungsströme (Raten)? Nutzungsdauern/Methode? → *fachlich zu klären …*
5. **Miete nach m² je Abteilung?** `living_space` (m²) liegt je Mietobjekt vor, aber **keine m²-Zuteilung
   je Abteilung**. Verteilung nach m², Kopfzahl oder pauschal? → *fachlich zu klären …*
6. **Versicherung — Verteilung.** Je Police direkt zugeordnet, nach Anlagewert, nach Kopf/Umsatz oder
   pauschal? → *fachlich zu klären …*
7. **Personalkosten — Bezugsbasis.** Vollkosten (`salaries.employer_total_monthly`) oder Brutto? Verteilung
   nach Stunden je Auftrag (Zeitwirtschaft vorhanden, aber ohne €), nach Hauptabteilung oder anteilig bei
   abteilungsübergreifendem Einsatz? → *fachlich zu klären …*
8. **Erlös-Schiene.** Welche gilt als „Umsatz je Kostenstelle" — Auftrag/`deal_invoices` (mit `department_id`)
   oder generische `invoices` (ohne)? Doppelerfassung vermeiden. → *fachlich zu klären …*
9. **Werden DATEV-Kostenstellen bereits extern (Steuerberater/FiBu) geführt?** Falls ja: Übernahme/Mapping
   statt Eigenbau; das ticket-System müsste sich dann an die externe Kostenstellen-Systematik anlehnen.
   → *fachlich zu klären — NICHT vom System zu raten — Geschäftsführung/Steuerberater.*
10. **Kostenträger-Definition.** Kostenträger = Auftrag/Deal, Projekt, Gewerk (`lead_product_list`) oder
    Objekt? Granularität der Nachkalkulation? → *fachlich zu klären …*

---

## TEIL C — Einschätzung (sofort nutzbar / unvollständig / fehlt)

### Sofort nutzbar (existiert sauber)
- **Organisations-Achse:** `branches`, `departments` (hierarchisch), `teams`, `employee_departments`,
  `positions`/`qualifications` — tragfähige Kostenstellen-Struktur.
- **Umsatz je Auftrag/Abteilung über die Auftragsschiene:** `deals.department_id` + `price`,
  `deal_invoices` (Ist-Beträge), `lead_product_lists.department_id`.
- **Material verursachungsgerecht:** `goods_receipts` (Preis + `department_id` + Projekt/Objekt) — einzige
  Kostenart, die heute schon Abteilung **und** Kostenträger trägt.
- **Vorkalkulation/Stundensätze:** `costing_sets`/`costing_set_roles` (Vollkostensätze je Qualifikation) —
  als **Kostensatz-Quelle** für eine spätere Umlage wiederverwendbar.

### Existiert, aber unvollständig/unzuverlässig
- **Umsatz über die generische `invoices`-Schiene:** kein `department_id`; Abteilungs-Zuordnung nur
  heuristisch (DashboardDepartmentController) → für Controlling **unzuverlässig**.
- **Projekt-/Auftragsdaten generell** unterliegen der laufenden **P1/P2-Stabilisierung** (vgl.
  `docs/stabilitaet-fixliste.md`, `docs/stabilitaet-p1-arbeitsliste.md`,
  `docs/stabilitaet-routing-workflow.md`): solange Auftrags-/Rechnungsketten nicht stabil sind, ist jede
  darauf aufbauende Nachkalkulation nur so gut wie die Datenbasis. **Empfehlung: Controlling erst nach
  Stabilisierung von Auftrag→Rechnung.**
- **Zeitwirtschaft als Umlagebasis:** auftragsscharfe Stunden vorhanden
  (`daily_report_time_customers`, `attendances`), aber **ohne €-Kosten und ohne `department_id`** — Kette
  Stunden→Kosten→Kostenstelle fehlt.
- **Kalkulationsfelder mehrfach/uneinheitlich:** `salary_sheets` (alt) vs. `salaries` (neu) — Stundensatz-
  Definition uneinheitlich; vor Nutzung als Kostensatz konsolidieren.

### Fehlt komplett (müsste neu gebaut werden)
- Kostenstellen-/Kostenträger-**Dimension** (kein `cost_center`-Feld irgendwo).
- **Umlage-/Verteilungslogik** (Gemeinkosten → Abteilung/Projekt) und **Deckungsbeitrag/Abteilungs-GuV**.
- **Abschreibung/AfA** für Anlagen/Maschinen.
- **Buchhaltung/DATEV**-Anbindung (Konten, Kostenarten, Export).
- Gemeinkosten unterhalb der **Filial**-Ebene (Miete/Versicherung/Sonstiges tragen kein `department_id`).

### Verweis: Liegt Relevantes evtl. im „playground" statt in ticket?
Ja — laut vorhandenen Übernahme-Docs (nicht selbst nachgeprüft, nur referenziert):
- `docs/uebernahme/inventar-playground.md:54-56` listet im playground-Prototyp eine **Buchhaltungs-Suite mit
  ~30 Submodulen** inkl. ausdrücklich **`-kostenstellenrechnung`**, **`-projektrechnung`**, **`-dimensionen`**
  und einer **`cost_centers`-Tabelle** (plus accounting_journal/konten/DATEV-Export).
- `docs/uebernahme/inventar-playground.md:74-76` nennt zusätzlich **Controlling-Module**
  (`controlling-kpi`, `ziele`/OKR, **`abteilungs-guv`**).
- `docs/uebernahme/uebernahme-empfehlung.md:32,34,43` und `docs/uebernahme/vergleich-crm.md:16,35,45` werten
  **FiBu/DATEV/Controlling als die größte ticket-Lücke** und ordnen playground als Quelle ein — jedoch
  **Prototyp** (GoBD/DATEV-Test „nicht bestanden"), und `inventar-playground.md:23` vermerkt, dass die
  **„Kostenstellen-Dimension bei der Angebot→Auftrag-Umwandlung verloren geht"**.
- `docs/audit-playground.md:41,57-58` bestätigt: DATEV-EXTF/SKR03/UStVA fest verdrahtet, DATEV-Export in Prod
  bis Steuerberater-Freigabe gesperrt.
- **Einordnung:** Die fachliche Substanz einer Kostenstellen-/Kostenträgerrechnung existiert **konzeptionell
  im playground**, nicht in ticket. Ob übernehmen/neu bauen/extern (DATEV beim Steuerberater) ist eine
  strategische Entscheidung (Teil B, Punkt 9) — und kein technischer Schalter.

---

*Ende der read-only Bestandsaufnahme. Keine Code-/Schema-Änderung, keine fachliche Entscheidung getroffen.*
