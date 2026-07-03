# Arbeitspaket — Phase 0/1: Fundament Buchhaltung/FiBu/DATEV (sichere Etappe ohne Steuerberater)

**Stand:** 2026-07-02 · **Planungsdokument — noch KEIN Code, keine bestehende Datei geändert, nichts aus playground importiert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle (Fachlogik/Struktur), **nie** Optik/Werte.
**Abgrenzung:** Dieses Paket ist unabhängig von den aktuellen Planner-/Kanban-Änderungen im Repo — es fasst diese Dateien nicht an und hängt nicht von ihnen ab.
**Vorlage:** siehe `docs/uebernahme/buchhaltung-datev-integrationsplan.md` (Gesamtplan, Punkt 6/7).

**Ziel der Phase:** Technisches Fundament legen — **ohne** Live-Daten zu verändern, **ohne** steuerliche Aussage zu treffen, **ohne** echte Buchung oder DATEV-Export. Am Ende steht eine additive, vollständig zurückrollbare `accounting_*`-Grundschicht, ein wirksames Default-Deny-Gate (alles ROT), ein lückensicherer Nummernkreis, eine Löschsperre-/Audit-Infrastruktur und die nötigen nullable Ankerfelder an `invoices`/`invoice_items`.

---

## 0. Ist-Schema (verifiziert, damit nichts gedoppelt wird)

**`invoices` hat BEREITS:** `account_id, customer_id`(→new_leads)`, object_id`(→lead_alternative_adds)`, deal_id, offer_detail_id, invoice_no, type, status, issue_date, due_date, service_from, service_to, currency, subtotal, tax_rate, tax_amount, total_amount, notes, created_by, updated_by, timestamps, softDeletes, paid_amount, paid_at, payment_note, deal_limit_amount, deal_remaining_before/after, source_offer_detail_id, source_offer_items_hash, source_offer_synced_at, source_offer_updated_at`.
→ **NICHT erneut anlegen:** `deal_id`, `offer_detail_id`, `service_from/to` (kein separates `service_date` nötig), `paid_amount/paid_at`.

**`invoice_items` hat BEREITS:** `invoice_id, product_id, title, description, qty, unit, unit_price, tax_rate`(pro Position!)`, line_total, sort_order, timestamps, article_product_id, component_id, distributor_id, distributor_price_id, distributor_article_no, source_item_type, source_item_id, source_payload, print_hidden, group_title`.
→ `tax_rate` je Position existiert bereits; neu kommt nur die **Konto-/Steuerschlüssel-Referenz** und die Abschlags-Flags.

**Existierende FK-Ziele (dürfen in Phase 1 referenziert werden):** `departments`, `projects`, `invoices` (self). **Noch nicht existierend (FK erst in späterer Phase):** `debtors`, `cost_centers`, `cost_objects`, `open_items`, `posting_suggestions`, `accounts`, `tax_codes`.

---

## 1. Migrationsliste

### 1a) Phase 0 — NEUE, leere, steuer-neutrale Struktur-Tabellen (echte Laravel-Migrationen, KEIN playground-SQL-Dump)

**M0-1 `create_accounting_settings_table`** — Singleton-Firmenstamm (ersetzt playground-Mandantentabelle):
| Feld | Typ | Bemerkung |
|---|---|---|
| id | id | genau **eine** Zeile (App-Guard erzwingt Singleton) |
| company_legal_name | string, nullable | leer bis Befüllung |
| tax_number | string, nullable | Steuernummer — leer |
| vat_id | string, nullable | USt-IdNr — leer |
| chart_of_account_code | string(10), nullable | **leer!** SKR03/04 erst nach StB (B1); keine Vorbelegung |
| fiscal_year_start_month | tinyint, default 1 | technischer Default, keine Steuerwahrheit |
| default_currency | string(3), default 'EUR' | technisch, Einzelmandant DE |
| timestamps | | |

**M0-2 `create_accounting_number_ranges_table`** — Nummernkreis-Register:
`id · key string (unique) · prefix string nullable · current_number unsignedBigInteger default 0 · padding unsignedTinyInteger default 4 · year_reset boolean default false · period_year smallint nullable · note string nullable · timestamps`. **Unique(`key`,`period_year`).** Startzeilen (leer/0): `invoice`, `debtor`, `creditor`, `journal` — nur Struktur, keine scharfe Vergabe.

**M0-3 `create_accounting_gate_releases_table`** — Gate-/Freigabe-Status (Default-Deny-Persistenz):
`id · gate_key string (unique) · status string default 'red' · required_approval_role string nullable · released_by unsignedBigInteger nullable (→employees, nullOnDelete) · released_at timestamp nullable · release_note text nullable · evidence_ref string nullable · timestamps`. **Abwesenheit einer Zeile = deny** (fail-closed).

**M0-4 `create_accounting_audit_log_table`** — append-only Protokoll:
`id · actor_employee_id unsignedBigInteger nullable · action string · entity_type string nullable · entity_id unsignedBigInteger nullable · payload json nullable · prev_hash char(64) nullable · entry_hash char(64) · iso_ts string (ms-genau) · created_at timestamp` — **kein `updated_at`, kein softDeletes**, keine Update/Delete-Pfade in der App.

**M0-5 `create_accounting_entity_changes_table`** — Änderungs-/Storno-Protokoll (§146):
`id · entity_type string · entity_id unsignedBigInteger · action string (change|storno|correction) · field string nullable · old_value json nullable · new_value json nullable · reason text nullable · actor_employee_id unsignedBigInteger nullable · created_at timestamp` — append-only.

> **Bewusst NICHT in Phase 0:** `accounting_journal_entries`/`_lines`. Grund: Sie referenzieren `accounts`/`tax_codes`/`cost_centers`, die es erst ab Phase 3 gibt. Ein leeres Journal ohne FK-Ziele wäre toter Ballast und würde „keine scharfe Buchung" nur verschleiern. → Journal wird in Phase 3/5 mit vorhandenen FK-Zielen angelegt.

### 1b) Phase 1 — bestehende Tabellen NUR nullable erweitern (nichts bricht)

**M1-1 `add_accounting_anchor_columns_to_invoices`** (alle nullable, `after(...)` sauber platziert):
| Feld | Typ | FK jetzt? |
|---|---|---|
| department_id | unsignedBigInteger, nullable, index | **FK → departments** (nullOnDelete) — Ziel existiert |
| project_id | unsignedBigInteger, nullable, index | **FK → projects** (nullOnDelete) — Ziel existiert |
| original_invoice_id | unsignedBigInteger, nullable, index | **FK → invoices** self (nullOnDelete) — Storno-Referenz |
| is_reversed | boolean, default false | — |
| debtor_id | unsignedBigInteger, nullable, index | FK **später** (Phase 3, Ziel `debtors`) |
| cost_center_id | unsignedBigInteger, nullable, index | FK später (Phase 2/3) |
| cost_object_id | unsignedBigInteger, nullable, index | FK später (Phase 2/3) |
| open_item_id | unsignedBigInteger, nullable, index | FK später (Phase 5, Ziel `open_items`) |
| posting_suggestion_id | unsignedBigInteger, nullable, index | FK später (Phase 4) |
| posting_status | string(20), default 'none', index | Werte: none/suggested/posted/reversed — reiner Status, keine Buchung |

**M1-2 `add_accounting_anchor_columns_to_invoice_items`** (alle nullable):
| Feld | Typ | FK jetzt? |
|---|---|---|
| tax_code_id | unsignedBigInteger, nullable, index | FK später (Phase 3, Ziel `tax_codes`) |
| revenue_account_id | unsignedBigInteger, nullable, index | FK später (Phase 3, Ziel `accounts`) |
| cost_center_id | unsignedBigInteger, nullable, index | FK später |
| cost_object_id | unsignedBigInteger, nullable, index | FK später |
| is_deposit | boolean, default false | Abschlag |
| is_final | boolean, default false | Schlussrechnung |
| is_supplement | boolean, default false | Nachtrag |

> **Wichtig:** Kein Feld wird in Phase 1 zur Pflicht (kein NOT NULL, kein Enforcement). `department_id`-Pflicht ist **Phase 2**. Kein Kopf-`tax_code` an `invoices` — Steuer bleibt an der Position und ist in Phase 0/1 ohne Wahrheit.

---

## 2. Welche bestehenden Tabellen NUR nullable erweitert werden
Ausschließlich **`invoices`** (M1-1) und **`invoice_items`** (M1-2). **Keine** andere Live-Tabelle wird angefasst — nicht `deals`, nicht `deal_invoices` (bleibt unberührt und wird später verworfen, nicht in dieser Phase), nicht `departments`/`branches`/`projects` (nur als FK-Ziel referenziert, nicht verändert).

---

## 3. Services (in dieser Phase zu bauen bzw. später nötig)

**In Phase 0/1 zu bauen (steuer-neutral, keine Buchung):**
- **`NumberRangeService`** — atomare, lückensichere Vergabe (`SELECT … FOR UPDATE` / DB-Transaktion), kein `max()+1`, kein Lücken-Reset. Wird in Phase 0 nur als Utility gebaut/getestet, **noch nicht** an den scharfen Rechnungslauf gehängt.
- **`GateService` / `GateReadinessService`** + **`FinanzGateMiddleware`** — liest `accounting_gate_releases` + Config; **fail-closed** (unbekannt/fehlend = deny, HTTP 423). Portiert das Konzept aus playground `finanz_safety.php` + `datev_release_checklist.php`, verdrahtet auf ticket-Auth (`is_admin`/`user_rolls`).
- **`AuditLogService`** — schreibt append-only in `accounting_audit_log` mit `entry_hash`-Verkettung (`prev_hash` → `entry_hash`), keine Update/Delete-Methoden.
- **`DeletionGuard`** (Model-Observer/Trait) — verweigert `delete`/`update` an als „festgeschrieben" markierten Datensätzen; in Phase 0/1 nur als Infrastruktur + Config-Platzhalter (scharfer Stichtag kommt aus A6, Steuerberater/Yama).

**Erst SPÄTER nötig (NICHT in Phase 0/1 bauen):**
- Phase 3: `AccountResolutionService` (mapping_key → Konto/Steuerschlüssel).
- Phase 4: `PostingSuggestionService` (read-only Vorschläge).
- Phase 5: `JournalService` (bookFromSuggestion/storno), `FestschreibungsService` (§146, Vier-Augen), `OpenItemService`.
- Phase 6: `DatevPreparationService` (EXTF-v700 + validateExtf).

---

## 4. Gates, die standardmäßig ROT bleiben (Default-Deny)

Alle folgenden `gate_key` starten mit `status = 'red'` und werden in Phase 0/1 **nicht** freigeschaltet:
- **Aktions-Gates:** `finanz.master` (Hauptschalter), `finanz.buchen` (scharfe Buchung), `finanz.festschreiben` (§146), `finanz.export_datev`, `finanz.loeschsperre_scharf`.
- **Blocker-Gates (warten auf Steuerberater/Yama):** `stb.skr_gewaehlt` (B1), `stb.kontenplan_freigegeben` (B2), `stb.steuerschluessel_freigegeben` (B3), `stb.nummernsystematik_freigegeben` (B4), `stb.kanzlei_importtest_bestanden`, `yama.datev_zielbild` (A1), `yama.unveraenderbarkeit_ebene` (A4), `yama.loeschsperre_stichtag` (A6).

Solange ein Gate ROT ist, liefert die Middleware für die zugehörige Aktion hart `deny` (423). **Gesamtampel Phase-0/1-Ende = ROT** — gewollt.

---

## 5. Tests / Prüfpunkte je Migration

| Migration | Prüfpunkte |
|---|---|
| M0-1 settings | `up`/`down` sauber; Singleton-Guard verhindert 2. Zeile; `chart_of_account_code` bleibt NULL (keine SKR-Vorbelegung) |
| M0-2 number_ranges | Unique(`key`,`period_year`) greift; **Concurrency-Test**: N parallele `NumberRangeService`-Aufrufe → lückenlose, kollisionsfreie, aufsteigende Nummern (kein Duplikat, keine Lücke) |
| M0-3 gate_releases | fehlende/rote Zeile ⇒ Middleware `deny` (423); Freischalten einer Zeile ⇒ nur dieses Gate grün; unbekannter `gate_key` ⇒ fail-closed |
| M0-4 audit_log | Insert erzeugt korrekte `entry_hash`-Kette (`prev_hash` verknüpft); Update/Delete über App **nicht möglich**; Manipulation bricht Kettenprüfung |
| M0-5 entity_changes | append-only; Storno-Eintrag referenziert Ur-Entität; kein Update-Pfad |
| M1-1 invoices | **Kompletter bestehender Rechnungs-Flow** (`InvoiceController` + `InvoiceCanvasController`: anlegen/bearbeiten/anzeigen/Status) unverändert grün mit neuen NULL-Spalten; Live-Zeilen unverändert (Row-Count/Checksumme vor/nach identisch); FK department/project/self greifen bei gültigen IDs, erlauben NULL; `down()` entfernt Spalten sauber |
| M1-2 invoice_items | bestehender Positions-/Canvas-Flow grün; Flags default false; `down()` sauber |

**Querschnitt-Prüfpunkte:** (a) `php artisan migrate` **und** `migrate:rollback` laufen auf einer **Kopie der Live-DB** vollständig durch; (b) vor/nach der Migration identische Datensatzzahl in `invoices`/`invoice_items`; (c) kein Steuer-/Kontowert im System; (d) Gesamt-Gate-Ampel ROT.

---

## 6. Rollback-/Migrationssicherheit

- **Rein additiv:** nur neue Tabellen (M0-*) + nullable Spalten (M1-*). Keine Änderung/Löschung bestehender Spalten, keine Datenmigration, kein Backfill in dieser Phase.
- **Voll reversibel:** jede Migration mit sauberer `down()`. M0-* droppt leere Tabellen; M1-* droppt nur die neu hinzugefügten Spalten (FKs vorher lösen). Phase 0/1 ist zu 100 % zurückrollbar, da keine festgeschriebenen Daten entstehen.
- **Keine FK auf noch nicht existierende Ziele** (nur `departments`/`projects`/`invoices`-self werden jetzt verdrahtet) → keine „FK-to-nothing"-Fehler.
- **Getrennt testbar:** M0 und M1 sind unabhängig; M1 setzt M0 nicht voraus (Anker-Spalten sind zunächst FK-los gegenüber accounting-Zielen).

---

## 7. Was ausdrücklich NICHT in Phase 0/1 gebaut wird

- **Keine scharfe Buchung / kein Journal-Schreibpfad** (Journal-Tabellen entstehen erst Phase 3/5).
- **Keine Steuerlogik als Wahrheit:** kein Kontenrahmen mit Werten, keine `accounts`/`tax_codes`/`account_mappings` befüllt, kein SKR gesetzt. playground-Steuer-Stammdaten werden **nicht** übernommen.
- **Keine DATEV-Export-Aktivierung** (kein EXTF-Generator, kein `accounting_datev_exports`).
- **Keine offenen Posten / kein Mahnwesen / keine Debitoren-Stammdaten** (Tabellen erst Phase 3/5).
- **Keine Pflichtfelder / kein Enforcement / kein Backfill** an `invoices` (das ist Phase 2).
- **Keine Buchhaltungs-UI** außer optional einer **read-only Gate-Status-/Ampel-Seite** — und die **nur im ticket-Design** (Vuexy/Bootstrap-Blade, ticket-Sidebar, vorhandene Cards/Badges). Keine playground-Optik, kein Tailwind/Alpine.
- **`deal_invoices` wird nicht angefasst** (Verwerfen erst in späterer Phase).
- **Kein Anfassen der Planner-/Kanban-Dateien** oder anderer unrelated Repo-Änderungen.

---

## 8. Definition of Done

1. M0-1…M0-5 und M1-1…M1-2 existieren als echte, reversible Migrationen; `migrate` + `migrate:rollback` laufen auf Live-DB-Kopie sauber durch.
2. Live-Daten unverändert (identische Datensatzzahl in `invoices`/`invoice_items` vor/nach).
3. `NumberRangeService` nachweislich lückensicher (Concurrency-Test grün) — aber **nicht** an den Live-Rechnungslauf gehängt.
4. Gate-Infrastruktur wirksam: alle Gates ROT, Middleware liefert `deny` (423) für scharfe Aktionen; fail-closed bei unbekanntem Gate.
5. `AuditLogService` schreibt verkettet und ist nicht mutierbar; `DeletionGuard` verweigert Löschung markierter Datensätze (Config-Platzhalter).
6. Alle bestehenden Rechnungs-Flows unverändert grün.
7. **Kein** Steuer-/Kontowert, **keine** Buchung, **kein** Export im System — Gesamtampel ROT.

---

## 9. Risiken & Guards

| Risiko | Guard |
|---|---|
| NOT-NULL/Default bricht bestehende Inserts | strikt **nullable**, keine NOT-NULL, keine App-Logik-Änderung an bestehenden Controllern |
| Versehentliche Kopplung, die den Live-Flow ändert | Phase 1 fügt nur Spalten hinzu; bestehende Schreibpfade/Validierungen bleiben unberührt; Regressionstest des Rechnungs-Flows als DoD |
| FK auf noch nicht existierende Tabelle | in Phase 1 nur FK zu vorhandenen Zielen (departments/projects/invoices-self); alle accounting-Ziele FK-los, FK folgt in Zielphase |
| „aus Versehen scharf" (Buchung/Export) | kein Journal-Schreibpfad und kein Export-Service in dieser Phase; Gates ROT + fail-closed Middleware |
| playground-Steuerwerte werden als Wahrheit übernommen | in Phase 0/1 werden **gar keine** Stammdaten befüllt; Werte erst Phase 3 nach StB-Freigabe über `mapping_key` |
| Nummernkreis-Race in Zukunft | `NumberRangeService` jetzt schon atomar/transaktional gebaut und getestet, bevor er später scharf geschaltet wird |
| Audit-Log manipulierbar | append-only Design + `entry_hash`-Kette; DB-Grant-/Trigger-Härtung ist A4-Entscheidung (spätere Phase), hier App-seitig vorbereitet |
| Rollback-Schaden | rein additiv, jede Migration mit `down()`; keine festgeschriebenen Daten in dieser Phase |

---

### Portier-Referenzen (playground = nur Konzept/Struktur, NICHT Optik, NICHT Werte)
`config/finanz_safety.php` · `config/datev_release_checklist.php` · `app/Http/Middleware/FinanzGateMiddleware.php` · `app/Services/Accounting/{GateReadinessService,NumberRangeService}.php` · Schema-Paten `database/sql/crm_erp_accounting_foundation_schema.sql` (für `accounting_settings`←accounting_clients, `accounting_number_ranges`, audit/entity_changes). **Alle als echte ticket-Migrationen neu schreiben, `accounting_client_id` weglassen (Einzelmandant).**
