# S1-01 — Transaktionaler, lückenarmer Rechnungsnummernkreis (Nummer erst bei `draft → sent`, eine Vergabestelle)

**Stand:** 2026-07-02 · **Detail-Ticket — KEIN Code, KEINE Migration geschrieben, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — UI später nur im ticket-Design. Planner-/Kanban-Änderungen unberührt.
**Priorität:** P0 (Fundament) · **Sprint:** 1 · **Grundlage:** `sprint-1-tickets-rechnungsschiene.md`, A1 = Option 1 (Kanzlei führt FiBu; ticket macht keine eigene Buchung).

---

## 1. Ist-Befund (betroffene Dateien/Methoden)

- **`app/Models/Invoice.php` → `makeInvoiceNo()` (Z. 148–164):** `prefix = 'RE-'.now()->format('y')`; sucht `invoice_no LIKE 'RE-yy%'`, `orderByDesc('id')`, nimmt letzte Ziffern `+1`, `str_pad(4)`. **Kein Lock, keine Transaktion.** Auswahl per `id` (nicht per Nummer). SoftDeletes aktiv → gelöschte Drafts können Nummern „verbrauchen".
- **Vergabezeitpunkt zu früh:** Nummer wird bei **Draft-Anlage** gesetzt — u. a. in `InvoiceController::store` (≈Z. 197 ff.) und im Canvas-Draft `InvoiceCanvasController::storeDraftFromOfferDetail` (≈Z. 33 ff., Nummer ≈Z. 64).
- **Zweite Vergabestelle:** `InvoiceCanvasController::makeInvoiceNo()` (≈Z. 712) — eigene, parallele Implementierung derselben Logik → Divergenzrisiko.
- **Status/Zahlung:** `InvoiceController::updateStatus` (≈Z. 29 ff.) und `applyStatusAccounting()` (≈Z. 1178–1203) steuern Statuswechsel.
- **Folgen:** Race Condition bei parallelem Anlegen/Versenden (gleiche Nummer möglich); Lücken durch gelöschte/verworfene Drafts; zwei Wahrheiten für dieselbe `invoices.invoice_no`.
- **`invoices.invoice_no`** ist `string(50)`, indexiert; Unique nur `(account_id, invoice_no)` — bei `account_id = NULL` erlaubt MySQL Duplikate.

## 2. Zielverhalten

- **Genau eine Vergabestelle** (`InvoiceNumberService`) für Rechnungsnummern im ganzen Code.
- **Reine Entwürfe (`draft`) erhalten KEINE finale Nummer** (`invoice_no = NULL`, UI zeigt Platzhalter).
- **Finale Nummer wird einmalig beim Übergang `draft → sent`** (bzw. beim ersten Eintritt in einen „ausgestellten" Status) vergeben.
- **Vergabe transaktional + zeilengesperrt** → keine Doubletten bei parallelen Requests.
- **Lückenarm:** die bekannten Lückenquellen (Draft-Verbrauch, Race) sind eliminiert; stornierte Rechnungen behalten ihre Nummer (Storno statt Löschen, S1-04) → keine neuen Lücken.
- **Bestehende `invoice_no` bleiben unverändert;** Startwert wird aus dem vorhandenen Bestand abgeleitet.
- **Kein** scharfes Journal, **keine** DATEV-Buchung.

## 3. Datenmodell-Konzept

**Empfehlung: dedizierte `invoice_number_ranges`** (nicht `accounting_number_ranges`). Begründung: Sprint 1 ist Rechnungsschienen-Härtung und soll **unabhängig** von der (gegateten, verschobenen) `accounting_*`-Schicht lauffähig sein. Die Tabelle ist so entworfen, dass sie später mit `accounting_number_ranges` **zusammengeführt** werden kann.

| Feld | Typ | Bemerkung |
|---|---|---|
| id | id | |
| type | string(20) | z. B. `invoice` (parametrierbar; Storno nutzt später denselben Service) |
| prefix | string(10) | z. B. `RE-` |
| year | smallint | Ausstellungsjahr (2-/4-stellig konsistent zu Format) |
| current_number | unsignedBigInteger, default 0 | **zuletzt vergebene** Nummer; nächste = `current_number + 1` |
| updated_by | unsignedBigInteger, nullable | FK → employees (nullOnDelete) |
| timestamps | | |

- **Kein `next_number`-Feld:** nur `current_number` speichern (redundanzfrei; `next = current+1`).
- **Kein `locked_at`-Feld:** die Nebenläufigkeit wird durch **pessimistisches Row-Locking innerhalb der Transaktion** (`lockForUpdate`) gelöst — eine persistierte Lock-Spalte wäre fehleranfällig (verwaiste Locks). *(Falls später ein anderer Prozess außerhalb DB-Transaktionen zugreift, kann eine Advisory-Lock ergänzt werden — nicht in diesem Ticket.)*
- **Constraints:** **`unique(type, year)`** (genau eine Sequenzzeile je Typ+Jahr). Zusätzlich empfohlen als Sicherheitsnetz: **`unique(invoice_no)` bzw. `unique(type_scope, invoice_no)` auf `invoices`** — siehe Risiken (die heutige `(account_id, invoice_no)`-Unique deckt `account_id = NULL` nicht ab).

## 4. Service-Design — `InvoiceNumberService`

**Öffentliche Methode (Konzept):** `assignFor(Invoice $invoice): string` — vergibt (oder liefert idempotent) die Nummer.

**Ablauf (innerhalb `DB::transaction`):**
1. **Idempotenz-Guard:** hat `$invoice->invoice_no` bereits einen Wert → **sofort zurückgeben**, nichts inkrementieren.
2. Ausstellungsjahr bestimmen (`year = now()->format('Y'|'y')`), `type = 'invoice'`, `prefix = 'RE-'`.
3. Sequenzzeile `(type, year)` **mit `lockForUpdate()`** laden; existiert keine → per `firstOrCreate` anlegen (Startwert siehe §6, Backfill-Ableitung).
4. `next = current_number + 1`; Nummer bilden: `prefix . yy . str_pad(next, 4, '0', LEFT)` (Format bleibt `RE-yy####`).
5. `current_number = next`, `updated_by` setzen, Sequenzzeile speichern.
6. `invoice->invoice_no = <Nummer>` setzen und speichern.
7. Commit → Lock freigegeben. Rückgabe der Nummer.

**Locking-Strategie:** `SELECT … FOR UPDATE` via Eloquent `lockForUpdate()` auf die **eine** Sequenzzeile; Transaktion kurz halten (nur Sequenz + Nummernzuweisung). Bei parallelem Zugriff serialisiert die DB die beiden Transaktionen → keine Doublette.

**Fehlerfälle:** DB-Deadlock/Lock-Timeout → Retry-Wrapper (kleine Anzahl, exponentiell) um die Transaktion. Wenn `invoice_no`-Unique-Verletzung trotz allem auftritt → als harter Fehler behandeln (nicht stillschweigend überschreiben).

## 5. Statuswechsel-Design

- **`draft → sent`:** ruft `InvoiceNumberService::assignFor()` **einmal**; setzt zusätzlich (aus S1-03) `finalized_at`. Ab hier hat die Rechnung eine finale Nummer.
- **Direktausstellung:** wird eine Rechnung nicht als `draft`, sondern direkt in einem ausgestellten Status angelegt, erfolgt die Vergabe beim **ersten Eintritt** in einen ausgestellten Status (`sent`/`paid`/`overdue`) — definiert über eine `isIssuedStatus()`-Menge. Primärpfad bleibt `draft → sent`.
- **`sent → paid | overdue | cancelled`:** Nummer **bleibt** unverändert (nur Idempotenz-Guard greift, keine Neuvergabe).
- **`sent → draft` ist gesperrt** (kein Rückweg ohne Sonderprozess): Rücknahme einer ausgestellten Rechnung nur über **Storno** (S1-04), nicht durch Status-Downgrade. `updateStatus` weist den Übergang `sent → draft` mit 422 ab.
- **Nur Rechnungs-Dokumente** (`type = Rechnung`; Gutschrift/Storno in S1-04 über denselben Service) erhalten `RE-`-Nummern. **Angebot/Auftrag** (falls über dieselbe Tabelle) sind **nicht** Teil dieses Nummernkreises (eigene Nummerierung, außerhalb Scope).

## 6. Umgang mit Altbestand

- **Bestehende `invoice_no` werden NICHT geändert.**
- **Startwert-Ableitung (einmaliger, idempotenter Seed / bei `firstOrCreate`):** pro Jahr `current_number = MAX(parsed number)` über bestehende `invoices.invoice_no`, die dem Muster **`RE-yy####`** entsprechen (Regex auf die letzten Ziffern). Gibt es keine passende Nummer für das Jahr → Start bei `0` (erste Vergabe = `0001`).
- **Testdaten mit Sondernummern** (z. B. `TST-OPEN-2337`) **matchen das `RE-`-Muster nicht** → werden bei der Startwert-Ableitung **ignoriert** und bleiben unverändert erhalten. Keine Zerstörung, keine Kollision.
- **Kein Rückwirken** auf bereits vergebene Nummern; die Sequenz zählt nur vorwärts ab dem abgeleiteten Maximum.
- Seed erfolgt **lazy** beim ersten `assignFor` je `(type, year)` (kein separater destruktiver Migrationsschritt nötig).

## 7. Controller-/Model-Auswirkungen

- **`Invoice::makeInvoiceNo()`:** intern auf `InvoiceNumberService` umleiten **oder** als `@deprecated` markieren und Aufrufer entfernen. Ziel: keine eigenständige Nummernlogik mehr im Model.
- **`InvoiceController::store`:** vergibt beim Draft **keine** Nummer mehr; `invoice_no` bleibt `NULL` bis `sent`.
- **`InvoiceController::updateStatus` / `applyStatusAccounting`:** beim Übergang in `sent` `assignFor()` aufrufen; `sent → draft` blockieren.
- **`InvoiceCanvasController::storeDraftFromOfferDetail`:** Nummernvergabe entfernen (Draft ohne Nummer); **`InvoiceCanvasController::makeInvoiceNo()` löschen** und alle Aufrufe auf den Service umstellen.
- **Nur eine Vergabestelle** verbleibt: `InvoiceNumberService`.

## 8. UI-Auswirkungen (nur ticket-Design)

- **Draft:** statt Nummer den Platzhalter „Nummer wird beim Senden vergeben" anzeigen (ticket-Text-/Badge-Stil).
- **Sent und später:** finale `RE-yy####`-Nummer anzeigen.
- Listen/Filter, die nach `invoice_no` suchen, müssen mit `NULL` bei Drafts umgehen (Draft z. B. über `id`/Entwurfskennung referenzieren).
- Keine neue Seite, keine playground-Optik.

## 9. Tests

- **Nebenläufigkeit:** zwei (mehrere) parallele `draft → sent`-Requests → **keine gleiche Nummer**, keine Lücke, aufsteigend.
- **Draft ohne Nummer:** neu angelegter Draft hat `invoice_no = NULL`.
- **Genau eine Nummer bei `sent`:** nach Versand ist genau eine Nummer gesetzt.
- **Idempotenz:** erneutes „Senden" / erneuter `assignFor`-Aufruf auf bereits nummerierter Rechnung ändert die Nummer **nicht**.
- **Altbestand stabil:** bestehende `RE-`-Nummern und `TST-OPEN-…`-Testnummern bleiben unverändert; Startwert = korrektes Maximum der `RE-yy####`.
- **Statuswechsel:** `sent → paid/overdue/cancelled` behält Nummer; `sent → draft` wird abgewiesen (422).
- **Format:** `RE-yy####` beibehalten; Jahreswechsel startet neue Sequenz bei `0001`.
- **Regression:** bestehender Draft-Anlage-/Bearbeitungs-Flow unverändert grün; Live-Daten-Row-Count unverändert.

## 10. Risiken & Guards

| Risiko | Guard |
|---|---|
| Race → doppelte Nummer | `DB::transaction` + `lockForUpdate` auf die eine Sequenzzeile; zusätzlich `unique`-Sicherheitsnetz auf `invoices.invoice_no` |
| `account_id = NULL` erlaubt heute Duplikate | Sicherheitsnetz-Unique so wählen, dass es auch bei `NULL` greift (z. B. eigener Unique-Index auf `invoice_no` für Rechnungs-Typ) |
| Deadlock/Lock-Timeout | kurze Transaktion + kleiner Retry-Wrapper |
| Falscher Startwert zerstört Sequenz | Startwert nur aus `RE-yy####`-Muster; Testnummern ignoriert; lazy `firstOrCreate` |
| Zweite Vergabestelle bleibt versehentlich | S1-10 verifiziert Entfernung; Test „nur eine Vergabestelle" |
| `sent → draft` öffnet Nummern-Rückweg | Übergang gesperrt (422); Rücknahme nur via Storno (S1-04) |
| Jahres-/Zeitzonenkante | Jahr aus App-Zeit konsistent bestimmen |

## 11. Definition of Done

1. Genau **eine** Vergabestelle (`InvoiceNumberService`); beide `makeInvoiceNo()`-Implementierungen entfernt/umgeleitet.
2. Drafts haben **keine** Nummer; Nummer wird **einmalig** bei `draft → sent` transaktional + gesperrt vergeben.
3. Nebenläufigkeits-Test grün (keine Doublette, keine Lücke); Idempotenz-Test grün.
4. Bestehende `RE-`- und Testnummern unverändert; Startwert korrekt abgeleitet.
5. `sent → draft` gesperrt; ausgestellte Rechnung behält Nummer über alle Folgestatus.
6. Migration additiv/reversibel (`invoice_number_ranges`, optional Unique-Sicherheitsnetz); Live-Daten unangetastet.
7. UI zeigt Draft-Platzhalter bzw. finale Nummer im ticket-Design.

## 12. Nicht im Scope

**PDF** (S1-07) · **Storno/Gutschrift** (S1-04) · **Teilzahlung/OP** (S1-05/06) · **Editiersperre** (S1-03) · **DATEV/EXTF** · **Journal/Festschreibung** · Debitoren-/Kostenstellen-/Kontierungslogik · Angebots-/Auftrags-Nummernkreise · playground-Optik.

---
**Ein-Satz-Fazit:** Eine einzige, transaktional gesperrte Vergabestelle vergibt die Rechnungsnummer erst beim Versand — Drafts bleiben nummernlos, Bestand und Testnummern bleiben unangetastet, Race und Draft-Lücken sind beseitigt; alles ohne jede buchhalterische Aussage.
