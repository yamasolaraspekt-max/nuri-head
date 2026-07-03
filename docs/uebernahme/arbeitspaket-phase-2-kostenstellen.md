# Arbeitspaket — Phase 2: Kostenstellen-Fundament + Pflichtfelder + Auftrag→Rechnungskette

**Stand:** 2026-07-02 · **Planungsdokument — noch KEIN Code, keine bestehende Datei geändert, nichts aus playground importiert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle. **UI-Regel:** alle Views nur im ticket-Design (Vuexy/Bootstrap-Blade, ticket-Sidebar, Cards/Tabellen/Modals/Badges, Select2/Toastr) — keine playground-Optik.
**Setzt voraus:** Phase 0/1 (`docs/uebernahme/arbeitspaket-phase-0-1-buchhaltung.md`) — `accounting_settings`, Gate-Registry, `invoices.department_id`/`cost_center_id` (nullable) existieren bereits.

## Getroffene Yama-Entscheidungen (Grundlage dieses Pakets)
1. **Kostenstelle = Abteilung je Filiale** (nicht `departments.id` allein).
2. **Kontenrahmen wählbar: SKR03 oder SKR04** — über `chart_of_accounts` + `accounts` + `account_mappings`.
3. **Keine harten Kontonummern im Code** — immer über `account_mappings.mapping_key` (z. B. `sales_revenue_19`), der je aktivem Rahmen auf ein SKR03-/SKR04-Konto zeigt.

**Ziel Phase 2:** Kostenstellen-Dimension „Abteilung je Filiale" sauber einführen, `cost_center_id` an `invoices` ableiten + pflichtig machen (zum richtigen Zeitpunkt), die Auftrag→Rechnungskette als Ableitungsquelle nutzbar machen — und die **leere** SKR-Struktur (chart/accounts/mappings + Resolver) vorbereiten. **Keine Steuerwerte, keine echten Konten, keine Buchung.**

---

## 0. Verifizierte Schema-Randbedingung (entscheidet die Ableitung)

- **`departments`**: `id, department_name, parent_id`(self)`, branch_id`(→branches, **nullable**)`, department_head, status, softDeletes`.
- **`deals`**: `department_id`(→departments) — **kein `branch_id`**.
- **`projects`**: `department_id`(→departments) — **kein `branch_id`**.
- **`employee_departments`**: `employee_id, department_id` (n:m).
- **`invoices`** (nach Phase 1): `department_id`(→departments, nullable)`, cost_center_id`(nullable, noch FK-los)`, deal_id, project_id, created_by`(→employees).

**Folge:** Der einzige durchgängige Weg zur Filiale führt über **`department → departments.branch_id`**. `deals`/`projects` liefern nur die Abteilung. Die Kostenstelle wird daher **department-zentriert** aufgelöst.

### ⚠ Offene Datenfrage (VOR Backfill zu klären — Yama/Datenprüfung, kein Steuerberater)
Sind die Abteilungen **filialspezifisch** (jede `departments`-Zeile hat ein gesetztes `branch_id`, „Montage Berlin" und „Montage Hamburg" sind **zwei** Zeilen) **oder generisch** (eine Zeile „Montage" mit `branch_id = NULL`, Filiale variiert je Auftrag)?

- **Fall A – filialspezifisch (branch_id gesetzt):** `department_id` bestimmt bereits eindeutig die Filiale → Kostenstelle ist **1:1 zur Abteilung**; `unique(branch_id, department_id)` ist praktisch `unique(department_id)`. Ableitung deterministisch.
- **Fall B – generisch (branch_id NULL):** `branch` ist aus der Abteilung **nicht** ableitbar → es braucht eine zweite Filial-Quelle (Ersteller-/Auftrags-Filiale) oder manuelle Wahl. Kostenstelle = echte (branch, department)-Kombination.

**Empfehlung:** Zuerst per Read-only-Analyse zählen, wie viele `departments.branch_id` NULL sind und ob Abteilungsnamen filialübergreifend doppeln. Das Modell unten funktioniert in **beiden** Fällen; nur die Ableitungsquelle für `branch` unterscheidet sich.

---

## 1. Tabellen-/Feldvorschlag

### 1a) NEU: `cost_centers` (Kostenstellen — Abteilung je Filiale)
| Feld | Typ | Bemerkung |
|---|---|---|
| id | id | |
| code | string(20) | DATEV-KOST1-Code, z. B. `1010`; von Yama vergebene Systematik |
| name | string | z. B. „Montage Berlin" |
| branch_id | unsignedBigInteger, nullable | FK → branches (nullOnDelete) |
| department_id | unsignedBigInteger, nullable | FK → departments (nullOnDelete) |
| parent_id | unsignedBigInteger, nullable | FK → cost_centers self (optional Hierarchie) |
| status | string(20), default 'active' | active/inactive |
| timestamps, softDeletes | | |

**Constraints:** `unique(code)` · `unique(branch_id, department_id)` (verhindert doppelte Kostenstelle je Filiale-Abteilung). Hinweis: Bei `branch_id`/`department_id = NULL` greift die MySQL-Unique-Regel nicht — daher zusätzlich **App-seitige** Eindeutigkeitsprüfung.

### 1b) NEU (leere Struktur, Vorbereitung SKR — KEINE Werte/Konten)
**`chart_of_accounts`** — Kontenrahmen: `id · code string(10) (SKR03|SKR04) · name · is_active boolean default false · timestamps`. Mehrere Zeilen erlaubt, **aktiv wird erst nach StB-Entscheidung (B1)** gesetzt.

**`accounts`** — Sachkontenstamm: `id · chart_of_account_id`(FK)`· account_number string · name · type string · normal_balance string(soll|haben) · is_active boolean default true · timestamps`. **In Phase 2: 0 Zeilen** (Befüllung erst Phase 3 nach StB).

**`account_mappings`** — semantische Zuordnung: `id · chart_of_account_id`(FK)`· mapping_key string (z. B. sales_revenue_19) · account_id`(FK→accounts, nullable)`· valid_from date nullable · valid_until date nullable · timestamps`. `unique(chart_of_account_id, mapping_key, valid_from)`. **In Phase 2: 0 Zeilen.**

### 1c) ERWEITERN (nullable): `accounting_settings`
- `active_chart_of_account_id` unsignedBigInteger, nullable, FK → chart_of_accounts (nullOnDelete). **Bleibt NULL bis B1.** (Ersetzt inhaltlich den Phase-0-Platzhalter `chart_of_account_code`; letzterer kann bestehen bleiben oder in dieser Migration entfernt werden — Details im Migrationsschritt.)

### 1d) ERWEITERN (nachgezogene FK): `invoices` / `invoice_items`
- `invoices.cost_center_id` → **jetzt FK auf `cost_centers`** (nullOnDelete); `invoices.department_id` FK besteht bereits.
- `invoice_items.cost_center_id`/`cost_object_id` → FK auf `cost_centers` (nullOnDelete). *(`cost_objects`/Kostenträger bleibt Phase 3 — hier nicht.)*

> **Nicht in Phase 2:** `debtors`, `tax_codes`, `open_items`, Journal, DATEV. Steuer-/Kontowerte bleiben leer.

---

## 2. Ableitungsregeln `cost_center_id` (Auftrag→Rechnung→Kostenstelle)

**Schritt 1 – Abteilung bestimmen** (erste nicht-leere Quelle gewinnt):
1. explizit am Rechnungskopf gesetzte `invoices.department_id` (manuelle Wahl hat Vorrang),
2. `invoices.deal_id → deals.department_id`,
3. `invoices.project_id → projects.department_id`,
4. `invoices.offer_detail_id → offer → department` (falls Angebot eine Abteilung trägt),
5. Fallback: Ersteller `invoices.created_by → employee_departments` (nur wenn **genau eine** Abteilung; sonst → Fehlerfall „mehrdeutig").

**Schritt 2 – Filiale bestimmen:**
- **Fall A:** `branch_id = departments.branch_id` (deterministisch).
- **Fall B:** `branch_id` aus Ersteller-/Auftrags-Filiale (sofern vorhanden) — sonst **kein Auto-Wert**, manuelle Wahl nötig.

**Schritt 3 – Kostenstelle auflösen:** `cost_centers` per `(branch_id, department_id)`; genau ein aktiver Treffer → `cost_center_id` setzen. 0 Treffer → Fehlerfall (siehe §5). >1 → App-Eindeutigkeitsverletzung (Datenfehler).

**Prinzipien:**
- Ableitung erzeugt einen **Vorschlag**, der im UI **überschreibbar** ist (Select2). Keine stille Falschzuordnung.
- Kein Rateweg über `created_by`, wenn mehrere Abteilungen → lieber leer + Pflichthinweis.
- `department_id` wird bei Ableitung gleich mitgeschrieben (Konsistenz Rechnung↔Kostenstelle).

---

## 3. Pflichtfeldregeln

- **`invoices.department_id` und `invoices.cost_center_id` sind Pflicht — aber erst beim Statuswechsel zu `sent`** (Festschreibung der Rechnung nach außen), **nicht** im `draft`. Drafts bleiben flexibel/leer.
- Durchsetzung zunächst **App-seitig** (FormRequest/Service-Validierung), **kein** DB-`NOT NULL` in Phase 2 (Altbestand!). DB-`NOT NULL` frühestens nach vollständigem Backfill und nur für neu erzeugte Zeilen abgesichert — optional spätere Mini-Migration.
- `cost_centers.status = inactive` darf **nicht** neu zugewiesen werden (nur Bestandsbezug bleibt gültig).
- **Keine** Pflicht auf Steuer-/Kontofelder (die sind leer/Phase 3).

---

## 4. UI-Auswirkungen (nur ticket-Design)

- **Kostenstellen-Stammdaten**: neuer Menüpunkt in bestehender ticket-Sidebar (unter Controlling/Stammdaten), CRUD-Liste + Modal — ticket-Tabellen/Badges/Buttons. Felder: code, name, Filiale (Select2), Abteilung (Select2), Status. Anlege-Assistent „Kostenstellen aus Filiale×Abteilung generieren".
- **Rechnungsformular** (`InvoiceController`/`InvoiceCanvasController`-Views): read-only angezeigter **abgeleiteter** Kostenstellen-Wert + Select2-Override, plus Abteilungsfeld. Validierungs-Toastr beim Speichern als `sent`, wenn leer/mehrdeutig.
- **Status-Badge** „Kostenstelle offen" auf Rechnungen ohne Zuordnung (für Altbestand/manuelle Nachpflege).
- **Keine** Buchhaltungs-/Konten-UI in Phase 2 (chart/accounts/mappings bleiben leer, keine Pflege-Views nötig; optional read-only „Kontenrahmen: noch nicht freigegeben"-Hinweis).

---

## 5. Migration / Backfill-Strategie

**Migrationen (additiv):** `create_cost_centers_table` · `create_chart_of_accounts_table` · `create_accounts_table` · `create_account_mappings_table` · `add_active_chart_to_accounting_settings` · `add_cost_center_fk_to_invoices` · `add_cost_center_fk_to_invoice_items`.

**Backfill (idempotent, dry-run-fähig, separates Artisan-Command — kein Auto-Run):**
1. **Kostenstellen erzeugen:** alle real vorkommenden `(branch, department)`-Kombinationen aus `departments` (+ ggf. aus `deals`/`projects`-Abteilungen) sammeln → je Kombination eine `cost_centers`-Zeile mit generiertem `code` und Name „<Abteilung> <Filiale>". Vorschlags-`code`-Schema von Yama bestätigen lassen.
2. **Rechnungen zuordnen:** für bestehende `invoices` `cost_center_id` per Ableitungsregeln (§2) setzen, **nur wo eindeutig**. Nicht eindeutige/leere bleiben NULL + Badge „Kostenstelle offen".
3. **Report:** Zuordnungsquote, Liste der nicht zuordenbaren Rechnungen, Liste der Abteilungen ohne `branch_id` (Fall B).
4. **Grandfathering:** Altbestand ohne Kostenstelle bleibt **valide** (keine rückwirkende Pflicht); Nachpflege manuell über UI.

**Reihenfolge:** erst Dry-Run + Report prüfen → Kostenstellen anlegen → Rechnungs-Backfill → dann App-Pflicht für **neue** `sent`-Rechnungen aktivieren.

---

## 6. Fehlerfälle

| Fall | Verhalten |
|---|---|
| Abteilung ohne `branch_id` (Fall B) | keine Auto-Filiale → Kostenstelle bleibt leer, UI verlangt manuelle Wahl; im Report gelistet |
| Keine Abteilung ableitbar (alle Quellen leer) | `cost_center_id` NULL; Speichern als `sent` blockiert mit Toastr „Kostenstelle/Abteilung erforderlich" |
| `created_by` in mehreren Abteilungen | mehrdeutig → **kein** Rateweg, leer + Pflichthinweis |
| `(branch, department)` hat keine Kostenstelle | Fehler „Keine Kostenstelle für diese Filiale/Abteilung" + Angebot, sie anzulegen |
| Deal-Abteilung ≠ Kopf-Abteilung | Kopf-Wahl gewinnt; Hinweis-Badge „Abweichung Auftrag/Rechnung" |
| Kostenstelle inaktiv | nicht neu zuweisbar; bestehende Zuordnung bleibt |
| Doppelte `(branch, department)` beim Anlegen | App-Unique-Verletzung → Fehlermeldung, kein Insert |

---

## 7. Vorbereitung SKR03/SKR04 (Struktur, keine Wahrheit)

- `chart_of_accounts`/`accounts`/`account_mappings` als **leere** Tabellen anlegen.
- **`AccountResolutionService`** (Skelett) bauen: nimmt `mapping_key` + aktiven Kontenrahmen (`accounting_settings.active_chart_of_account_id`), sucht in `account_mappings` das gültige Konto. In Phase 2 gibt es **keine Mappings** → Service liefert kontrolliert **`unmapped`/deny** und wirft nie ein hartkodiertes Konto.
- Damit ist der Weg „Code fragt `mapping_key`, nie eine Kontonummer" **erzwungen**, bevor überhaupt Werte existieren.
- **Kein** Seeder mit Konten/Steuerwerten; `active_chart_of_account_id` bleibt NULL bis B1.

---

## 8. Tests / Prüfpunkte

- **Ableitung (Unit):** je Präzedenz-Stufe (Kopf > deal > project > offer > creator) korrektes Ergebnis; mehrdeutiger creator → leer; Fall A deterministisch, Fall B → manuelle Wahl.
- **Eindeutigkeit:** `unique(code)` + `unique(branch_id, department_id)` greifen (DB **und** App-Prüfung inkl. NULL-Fälle).
- **Pflichtfeld:** `draft` speicherbar ohne Kostenstelle; `sent` blockiert ohne `department_id`+`cost_center_id`; bestehende Draft-Flows unverändert grün.
- **Backfill:** Dry-Run verändert nichts; Report korrekt; idempotent (zweiter Lauf = keine Dubletten); Altbestand bleibt valide; Live-Row-Count `invoices` unverändert.
- **FK-Migration:** `add_cost_center_fk_to_invoices` sauber `up`/`down`; gültige IDs referenzierbar, NULL erlaubt.
- **SKR-Vorbereitung:** `AccountResolutionService` liefert bei fehlendem Mapping `unmapped` (nie ein Konto); keine `accounts`/Steuerwerte im System; `active_chart_of_account_id` NULL.
- **Gate:** alle scharfen Finanz-Gates weiterhin ROT (Phase 2 schaltet nichts frei).
- **Regression:** kompletter Rechnungs-Flow (anlegen/bearbeiten/anzeigen/Status) grün.

---

## 9. Was weiterhin Steuerberater-blockiert bleibt

- **B1 – welcher Kontenrahmen aktiv** (SKR03/04): `active_chart_of_account_id` bleibt NULL; Architektur unterstützt beide, die Wahl fehlt.
- **B2 – Kontenplan/Sachkonten**: `accounts` bleibt leer.
- **B3 – Steuerschlüssel/BU** (19/7/0 % PV/§13b/ig/steuerfrei): keine `tax_codes` in Phase 2.
- **B4 – Debitoren-/Kreditoren-Nummernsystematik + Sachkontenlänge**: betrifft Phase 3.
- **`account_mappings`-Inhalte** (welcher `mapping_key` → welches Konto) werden **erst nach B1+B2** befüllt.

> **Nicht** StB-blockiert (jetzt baubar): die **Kostenstellen-Struktur** und die Rechnungs-Zuordnung — das ist die getroffene Yama-Entscheidung. Nur die **KOST1-Code-Systematik** sollte mit dem Steuerberater/DATEV kurz auf Kompatibilität abgeglichen werden (weich, kein Blocker).

---

## 10. Definition of Done
1. `cost_centers` + leere `chart_of_accounts`/`accounts`/`account_mappings` als reversible Migrationen; `accounting_settings.active_chart_of_account_id` (NULL); `invoices`/`invoice_items` cost_center-FK gezogen.
2. Kostenstellen für alle real vorkommenden Filiale×Abteilung-Kombinationen erzeugt (per Backfill-Command, Report geprüft).
3. Neue `sent`-Rechnungen erzwingen Abteilung + Kostenstelle (App-Validierung); Drafts frei; Altbestand valide (Grandfathering).
4. Ableitungslogik deterministisch + überschreibbar; Fehlerfälle (§6) sauber behandelt.
5. `AccountResolutionService` erzwingt `mapping_key`-Weg, liefert ohne Mapping `unmapped` — **kein** hartkodiertes Konto, **keine** Steuerwerte.
6. Alle Finanz-Gates ROT; kein Export, keine Buchung; Live-Daten unverändert; Rechnungs-Flow grün.

---

## 11. Risiken & Guards
| Risiko | Guard |
|---|---|
| Abteilungen ohne `branch_id` (Fall B) verhindern Auto-Kostenstelle | Vorab-Datenanalyse; UI-Pflichtwahl + Report; Modell funktioniert in beiden Fällen |
| Falsche stille Kostenstellen-Zuordnung | Ableitung nur als überschreibbarer Vorschlag; bei Mehrdeutigkeit leer statt raten |
| NOT-NULL bricht Altbestand | keine DB-NOT-NULL in Phase 2; App-Pflicht nur für neue `sent`-Rechnungen; Grandfathering |
| Hartkodierte Konten schleichen ein | `AccountResolutionService` als einziger Konto-Zugang; Phase 2 hat 0 Konten → jeder Direktzugriff fällt sofort auf |
| KOST1-Code passt nicht zu DATEV | Code-Systematik weich mit StB abgleichen (kein Blocker) |
| Backfill-Doppelläufe | idempotentes Command + Dry-Run + Report; `unique`-Constraints |
| Scope-Creep in Steuer/Buchung | Phase 2 legt nur Struktur + Kostenstellen an; Gates bleiben ROT; Journal/Steuer erst Phase 3+ |
