# Cockpit-Inventur — Machbarkeit „fraktales Profit-Center-Cockpit"

**Reine Lese-Analyse am Datenmodell. Nichts gebaut, nichts migriert, nichts entschieden.**
Stand: 2026-06-29 · Branch `private/app-code-backup` · Repo `/Users/yamanuri/Documents/ticket`
Schema-Wahrheit = `database/migrations/` + Live-DB. Wo unsicher: ausdrücklich benannt, nicht vermutet.
Kostenseite (Deckungsbeitrag/Gemeinkosten) im Detail: siehe [controlling-bestandsaufnahme.md](controlling-bestandsaufnahme.md) — hier nur referenziert, nicht gedoppelt.

> Zielmodell: jede **Abteilung** als eigenständige Wirtschaftseinheit (Umsatz, Kosten, Personal, Auslastung) —
> drei Maßstäbe derselben Struktur (Unternehmen → Abteilung → Mitarbeiter), mit **normierten** Kennzahlen
> (pro Kopf, pro Stunde, in %). Kernfrage überall: **„Ist es sauber einer ABTEILUNG zuordenbar?"**

---

## Kernbefund in einem Satz

Die **Abteilungs-Dimension ist strukturell vorhanden** (department_id auf fast allen Transaktions­tabellen, `employee_departments` sauber 1:1) — aber die **Fakten, die das Cockpit füllen müssten, fehlen fast vollständig**: kein realer Umsatz (invoices/offers/deals leer, Auftragsanlage defekt), keine geleisteten/Anwesenheits-Stunden (alle Zeittabellen leer), keine Kostenverteilung je Abteilung, kein Gewerks-Nachfragewert, keine Überstunden. **Das Skelett steht, das Fleisch fehlt.**

---

## TEIL A — WAS IST DA (sauber nutzbar)

### A1. Abteilungs-Zuordnung — das Fundament steht strukturell
| Beziehung | Beleg | Status |
|---|---|---|
| **Mitarbeiter → Abteilung** | `employee_departments(employee_id, department_id)` — 50/50 Demo-MA zugeordnet, **0 Mehrfachzuordnungen** (sauber 1:1) | ✅ solide |
| **Projekt → Abteilung** | `projects.department_id` (FK→departments) — 31/31 gesetzt | ✅ Feld da |
| **Auftrag → Abteilung** | `deals.department_id` (FK) vorhanden | ⚠️ Feld da, Daten leer (Teil B) |
| **Angebot → Abteilung** | `offers.department_id` vorhanden | ⚠️ Feld da, Daten leer (Teil B) |
| **Anfrage → Abteilung** | `inquiries.department_id` — 39/40 gesetzt | ✅ Feld da |
| **Lead-Position → Abteilung** | `lead_product_lists.department_id` — 52/52 gesetzt | ✅ Feld da |
| **Struktur Branch→Department→Team** | hierarchisch (`departments.parent_id`), 16 Abteilungen | ✅ tragfähig |

→ **Die Abteilung als Aggregations-Achse ist anschlussfähig.** Genau das ist die wichtigste Vorbedingung, und sie ist (auf Schema-Ebene) erfüllt.

### A2. Mitarbeiter-Sicht („meine X") — filterbar
Nutzer→Mitarbeiter-Mapping über `users.name = employees.id`. Damit je Nutzer filterbar:
- **Projekte:** `projects.employee_id`, `projects.project_leader`
- **Aufträge:** `deals.employee_id` (+ `checked_by`, `reviewer_id`)
- **Termine:** `main_appointments.created_by` + Pivot `main_appointment_employees(employee_id, appointment_id)`
- **Aufgaben:** `general_tasks.created_by` / `claimed_by` + Pivot `general_task_assignees`
- **Anfragen:** `inquiries.contact_person`
- **Innen-/Außendienst am Lead:** `lead_product_lists.employee_id` (Innendienst) / `field_employee` (Außendienst)

→ Die **„meine Projekte/Aufträge/Termine/Aufgaben"-Sicht ist strukturell sauber abbildbar.** ✅

### A3. Normierungs-Basis (teilweise)
- **Mitarbeiterzahl je Abteilung:** über `employee_departments` ✅ → „pro Kopf" rechenbar.
- **Personalkostensatz:** `employees.salary_per_hour` (50/51 gesetzt) ✅ → Personalkosten-Basis vorhanden.
- **Soll-Arbeitszeit:** `employees.working_hour` ✅, Urlaub `remaining_day`/`leave`, Krank `sick_leave`/`sick_leave_remaining` ✅ (je MA, via `employee_departments` je Abteilung aggregierbar).

---

## TEIL B — WAS IST UNZUVERLÄSSIG (Feld existiert, aber Zuordnung lückenhaft / keine Daten / hängt an Defektem)

### B1. Umsatz — Feld-/Datenlage bricht genau dort, wo es zählt
- **`invoices` hat KEIN `department_id`.** Spalten u.a.: `deal_id, customer_id, status`. Umsatz→Abteilung also nur **indirekt** über `invoices.deal_id → deals.department_id`. Laut Controlling-Inventur (Kernbefund 3) wird das sonst nur **heuristisch** (Kunde/Objekt/Ersteller) zugeordnet → unzuverlässig.
- **`invoices` ist leer (0 Zeilen)** und enthält keine offensichtliche Betragsspalte (kein `total/net_amount/gross_amount/amount` gefunden) — Rechnungssumme liegt vermutlich in Positionen/anderem Feld, **ungeprüft, weil keine Daten**.
- **„Fakturiert vs. Auftragseingang" unterscheidbar?** Nur theoretisch: `deals.deal_status`/`confirmed_at`/`sign_date` vs. `invoices.status`. Ohne Daten nicht belegbar.

### B2. Auftrags-/Angebotsvolumen — Struktur da, Kette tot
- **`deals` (Aufträge): 0 Zeilen.** Felder vorhanden (`department_id, price, deal_status, project_status, sign_date, confirmed_at`), aber **Auftragsanlage ist defekt** (P1-16: 9 DealController-Methoden fehlen → jeder Speichern-POST 500). → Auftragsvolumen je Abteilung aktuell **nicht erfassbar**, weil keine Aufträge entstehen.
- **`offers` (Angebote): 0 Zeilen.** `offers.department_id` da; Betrag liegt in Angebotspositionen (`offer_details`), nicht als Kopf-Summe geprüft. Angebots-`show`/`generate-pdf` ebenfalls als P1-Crash gelistet.

### B3. Auslastung / Produktivität — Struktur ja, Daten nein
- **Geplante Stunden:** `general_tasks.soll_minutes` + `main_appointments`-Zeiten (start/end/total_time) vorhanden — Termine sind befüllt (Demo), `general_tasks` aber praktisch leer.
- **Geleistete Stunden / Anwesenheit:** **alle Quelltabellen leer** — `attendances` (0), `daily_reports` (0), `time_management_plans` (0), `employee_time_schedules` (0). → „verfügbare Mannstunden aus Anwesenheit" und „geleistete Stunden" haben **keine Datenbasis**.
- Damit ist **Auslastung (verplant/verfügbar)** und **Produktivität (geleistet/geplant)** heute **nicht berechenbar** — es fehlt die Ist-Stundenerfassung mit Daten.

### B4. Krank/Urlaub vorhanden, aber nur als Stand, nicht als Verlauf
`employees.sick_leave/remaining_day/leave` sind **Restwerte je MA** (Demo gesetzt). Für Cockpit-Trends („Krankenstand je Abteilung über Zeit") fehlt die **Buchungs-/Verlaufsebene** (genommene Tage mit Datum) — `time_management_plans`/Anwesenheit leer.

### B5. Reliabilitäts-Vorbehalt zur Abteilungs-Zuordnung
Alle `department_id`-Felder sind **nullable**; die guten Füllraten oben stammen **aus dem Demo-Seeder**, nicht aus erzwungenen App-Workflows. Ob bei normaler Nutzung (Projekt/Auftrag/Anfrage anlegen) die Abteilung **immer** gesetzt wird, ist **nicht garantiert** — vor dem Cockpit zu härten (Pflichtfeld/Default).

---

## TEIL C — WAS FEHLT KOMPLETT (müsste neu gebaut werden, bevor echte Zahlen erscheinen)

1. **Umsatz je Abteilung als belastbare Größe.** `invoices` braucht eine **direkte Abteilungs-Dimension** (oder eine saubere, erzwungene Ableitung über deal→department) **und** eine eindeutige Betrags-/Status-Logik (fakturiert vs. offen). Heute: nicht vorhanden/leer.
2. **Kostenrechnung je Abteilung / Deckungsbeitrag.** Laut Controlling-Inventur: **kein `cost_center`**, Kosten hängen an **Branch**, **keine Umlage** auf Abteilungen, `costing_sets` (Vorkalkulation) leer, **keine Ist-Kostenrechnung, keine FiBu/DATEV**. → Gewinn/DB je Abteilung ist **komplett ungebaut**.
3. **Ist-Stundenerfassung (Anwesenheit + geleistete Zeit).** Tabellen existieren, sind aber leer und ohne erzwungenen Erfassungs-Workflow. Ohne sie: **keine Auslastung, keine Produktivität, keine „pro Stunde"-Normierung.**
4. **Überstunden.** **Kein Feld, keine Tabelle** (`employees` hat nichts mit over/extra). Müsste komplett neu modelliert werden.
5. **Gewerks-Nachfragewert.** `article_groups.min_value/max_value` existiert, ist aber **0.00** (kein Durchschnittswert je Gewerk). Zudem hat **`inquiries` kein `product_id`** — Gewerk je Anfrage nur über `department_id`/Titel. → „Summe offener Anfragen × Gewerks-Durchschnitt" ist heute **nicht rechenbar**.
6. **Aggregations-/Kennzahlenschicht selbst.** Es gibt keine Sicht/Service, die je Abteilung Umsatz/Kosten/Auslastung normiert zusammenführt — das Cockpit (inkl. „pro Kopf/pro Stunde/%") ist als Schicht **nicht vorhanden**.

---

## FAZIT — ehrlich

**Wie weit ist ticket entfernt?** Das **Fundament (Abteilungs-Achse + Mitarbeiter-Zuordnung + „meine"-Filter)** steht erstaunlich gut — das ist die halbe Miete und oft das Schwierigste. **Aber:** von den **fünf Cockpit-Eingangsgrößen** (Umsatz, Kosten, geleistete Stunden, Nachfragewert, Überstunden) ist **keine einzige heute belastbar** — drei sind leer/defekt (Umsatz, Stunden, Aufträge), zwei fehlen ganz (Kostenumlage, Überstunden/Nachfragewert). Ein Cockpit darauf würde **plausibel aussehen und falsche Zahlen zeigen** — gefährlicher als kein Cockpit.

**Größte Lücke:** nicht die Abteilungs-Zuordnung (die ist da), sondern die **Faktenbasis Umsatz + Ist-Kosten + Ist-Stunden je Abteilung**. Konkret am schwersten: **Umsatz↔Abteilung an `invoices`** (kein department_id) und die **komplett fehlende Ist-Kostenrechnung** (Controlling-Inventur).

**Zwingende Reihenfolge der Vorarbeiten (Cockpit zuletzt):**
1. **Abteilungs-Zuordnung härten** — `department_id` auf Projekt/Auftrag/Angebot/Anfrage zum **Pflichtfeld** machen (erzwungen im Workflow, nicht nur nullable). Billigster, höchster Hebel; das Skelett tragfähig machen.
2. **Auftrags→Rechnungs-Kette reparieren** (P1-16 DealController + Angebots-Crashes), damit überhaupt **Umsatz/Auftragsvolumen** entsteht — **und `invoices` eine Abteilungs-Dimension geben**.
3. **Ist-Stundenerfassung aktivieren** (Anwesenheit + geleistete Zeit mit Daten) → Auslastung/Produktivität/„pro Stunde".
4. **Kostenrechnung je Abteilung** (Kostenstelle, Umlage, DB) — eigenes, großes Vorhaben; siehe Controlling-Inventur.
5. **Nachfragewert** (Gewerks-Durchschnitt + Produkt-Bezug an Anfragen) + **Überstunden-Modell**.
6. **Erst dann** die Aggregations-/Kennzahlenschicht (das Cockpit selbst) mit normierten Kennzahlen.

> Kurz: Das Cockpit ist keine Anzeige-Aufgabe, sondern eine **Datenerfassungs-Aufgabe**. Erst müssen Umsatz, Kosten und Stunden je Abteilung **zuverlässig entstehen** — dann ist das Cockpit fast geschenkt.
