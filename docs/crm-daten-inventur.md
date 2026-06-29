# CRM-Daten-Inventur — wo Seeder das CRM testbar machen

**Read-only Bestandsaufnahme der Datenmengen je CRM-Bereich.** Stand 2026-06-29 · Branch `private/app-code-backup`.
Ziel: zeigen, welche Bereiche genug Daten haben, welche **leer/dünn** sind, und **wo ein Seeder lohnt**, damit das ganze CRM zum Prüfen/Testen ausreichend gefüllt ist. Legacy (Bitrix/NIBE/IMAP) ausgeklammert.

---

## 1. Ist-Stand je Bereich (Zeilen aktuell)

| Bereich | Tabelle | Zeilen | Status |
|---|---|--:|---|
| **Anfragen** | inquiries / inquiry_types | 40 / 8 | ✅ ok |
| | inquiry_product_lists | **0** | ⬜ leer |
| **Kunden/Leads** | new_leads / lead_alternative_adds / lead_product_lists | 52 / 71 / 52 | ✅ ok |
| | new_lead_responsibilities | **0** | ⬜ leer |
| **Lead-Aktivität** | customer_histories / customer_notes / main_appointments | 92 / 76 / 80 | ✅ ok |
| | main_appointment_employees (Termin↔MA) | **0** | ⬜ leer |
| **Pipeline/Kanban** | lead_stages | 9 | ✅ ok |
| **Angebote** | offers / offer_details / offer_folders / offer_templates | **0 / 0 / 0 / 0** | 🟥 komplett leer |
| **Aufträge** | deals / deal_notes | **0 / 0** | 🟥 komplett leer |
| **Rechnungen** | invoices / invoice_files | **0 / 0** | 🟥 komplett leer |
| **Projekte** | projects | 31 | ✅ ok |
| **Wartung** | customer_maintenance_contracts / maintenance_checklists | **0 / 0** | ⬜ leer |
| **Aufgaben** | general_tasks / personal_tasks | **0 / 0** | 🟥 leer (Aufgaben-Board ist leer!) |
| **Support/Tickets** | problems | **0** | ⬜ leer |
| **Partner/Kontakte** | brands / distributors / products / distributor_prices | 45 / 9 / 44 / 88 | ✅ ok |
| | external_personals (Externe Firmen) | **0** | ⬜ leer |
| **Termine (Alt)** | appointments | **0** | ⬜ leer (evtl. Legacy — modern = main_appointments) |
| **Stammdaten** | departments / employees / positions | 16 / 51 / 24 | ✅ ok |

---

## 2. Wo ein Seeder lohnt — priorisiert

### 🟥 Gruppe 1 — höchster Testwert, jetzt komplett leer
Diese Bereiche fehlen ganz und blockieren das Testen der „hinteren" CRM-Kette **und** das Cockpit (Umsatz/Auslastung). Die **Anlage-UI ist teils defekt** (Aufträge P1-16, Angebote P1-18) — **deshalb ist ein Seeder hier genau das richtige Werkzeug**: er füllt die Daten direkt und umgeht die kaputten Workflows.

1. **Aufgaben** — `general_tasks` (+ `general_task_assignees`, `general_task_steps`). Das Aufgaben-/Kanban-Board (das ich repariert habe) ist **datenleer**. Seedbar: Aufgaben je Abteilung/Mitarbeiter mit Status, Fälligkeit, `soll_minutes`/`ist_minutes`, Zuweisungen. → macht das Aufgaben-Modul **und** die Produktivitäts-Datenbasis testbar.
2. **Aufträge** — `deals` (+ `deal_notes`). Felder da (`department_id, customer_id, product_id, price, deal_status, project_status, sign_date, confirmed_at`). Seedbar aus den vorhandenen Leads/Projekten. → Kanban-Spalte „Auftrag", **Umsatz/Auftragsvolumen je Abteilung** (Cockpit!).
3. **Angebote** — `offers` (+ `offer_details` als Positionen, `offer_folders`). `offers.department_id` da. Seedbar je Lead/Produkt mit Status + Positionssummen. → Pipeline-Stufe „Angebot", Angebotsvolumen.
4. **Rechnungen** — `invoices` (+ Positionen/Beträge). Seedbar je Auftrag (`deal_id`). → **fakturierter Umsatz** als Testbasis (Achtung: `invoices` hat kein `department_id` → Umsatz→Abteilung nur über deal; siehe cockpit-inventur.md).

### ⬜ Gruppe 2 — füllt sichtbare Lücken, einfache Inserts
5. **inquiry_product_lists** — Produktzeilen je Anfrage (Innendienst/Außendienst auf Anfrage-Ebene). → Anfrage-Verifizierungs-Flow + Innen/Außendienst-Auswahl testbar.
6. **new_lead_responsibilities** — zuständige Mitarbeiter je Lead/Produkt.
7. **main_appointment_employees** — Pivot, der die 80 vorhandenen Termine den Mitarbeitern zuweist. → „Außendienst-Kalender" / „meine Termine" zeigt Personen.
8. **external_personals** — Externe Firmen/Subunternehmer für die Kontaktliste (Reiter „Externe Firmen").
9. **Support/Tickets** — `problems`: ein paar Tickets je Kunde/Objekt. → Support-Modul testbar.
10. **Wartung** — `customer_maintenance_contracts` + `maintenance_checklists`: Wartungsverträge je Objekt. → Wartungs-Modul testbar.

### ⚪ Optional / später
- `lead_stage_sub_stages` (Kanban-Unterstufen) — eher Konfiguration als Testdaten.
- `appointments` (Alt-Termintabelle) — vor dem Seeden klären, ob Legacy (modern = `main_appointments`); sonst nicht anfassen.

---

## 3. Was NICHT geseedet wird
- **Legacy:** Bitrix-Chat-Import, NIBE, IMAP — ausgeklammert (eigene Memo „Legacy ignorieren").
- **Bereits ausreichend:** Stammdaten, Partner/Artikel, Anfragen, Leads, Lead-Aktivität, Projekte — hier kein Seeder nötig.

---

## 4. Empfehlung — ein gebündelter „CRM-Operativ-Voll"-Seeder
Ein zusätzlicher idempotenter Seeder (z. B. `DemoCrmPipelineSeeder`), aufgesetzt auf die vorhandenen Demo-Kunden/Leads/Projekte, der **die hintere Kette + offene Listen** füllt:

- je Lead-Position ggf. **Angebot → Auftrag → Rechnung** erzeugen (mit Status-Mix offen/angenommen/fakturiert), damit die **Pipeline durchgängig** ist und das Cockpit echten Umsatz/Auftragseingang sieht;
- **Aufgaben** (general_tasks) je Abteilung/Mitarbeiter mit Soll/Ist-Minuten;
- **Termine↔Mitarbeiter** (main_appointment_employees) verknüpfen;
- **Tickets** (problems) + **Wartungsverträge** je Objekt;
- **Externe Firmen** für die Kontaktliste.

**Reihenfolge im Seeder:** Angebote → Aufträge (aus angenommenen Angeboten) → Rechnungen (aus Aufträgen) — damit die Verknüpfungen (`offer_id`, `deal_id`) stimmen. Mengen moderat (z. B. ~30 Angebote, ~20 Aufträge, ~15 Rechnungen, ~40 Aufgaben, ~15 Tickets, ~10 Wartungsverträge, ~8 externe Firmen).

> **Wichtig (ehrlich):** Der Seeder liefert **Testdaten zum Prüfen der Listen/Kanban/Cockpit-Ansichten** — er ersetzt **nicht** die Reparatur der defekten Anlage-Workflows (Auftrag/Angebot-Crashes, P1-16/18). Beides ist nötig: Seeder zum Sehen/Testen jetzt, Workflow-Fix, damit echte Daten später normal entstehen.

---

## 5. Fazit
**Vorne voll, hinten leer.** Anfragen/Leads/Aktivität/Projekte/Partner sind gut befüllt; **Angebote, Aufträge, Rechnungen, Aufgaben, Tickets, Wartung sind 0**. Ein gebündelter CRM-Pipeline-Seeder schließt diese Lücken und macht **die gesamte CRM-Kette + das Cockpit** prüfbar — ohne auf die Reparatur der kaputten Anlage-Masken zu warten. Größter Einzelgewinn: die durchgängige **Angebot→Auftrag→Rechnung-Kette** (Umsatz) + **Aufgaben** (leeres Board).
