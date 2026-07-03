# Arbeitspaket — Sprint 1: Rechnungsschiene härten für Kanzlei-Übergabe (A1 = Option 1)

**Stand:** 2026-07-02 · **Planungsdokument — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — UI später nur im ticket-Design (Vuexy/Bootstrap-Blade, ticket-Sidebar, Cards/Tabellen/Modals/Badges, Select2/Toastr). Planner-/Kanban-Änderungen unberührt.
**Grundlage:** A1-Entscheidung Option 1 (Kanzlei führt FiBu; ticket liefert Belege + offene Posten + Buchungsvorschläge). Siehe `entscheidungsvorlage-A1-datev-zielbild.md`, `architekturbewertung-buchhaltung-datev.md`.

**Ziel:** NICHT Buchhaltung bauen. Rechnungen so **stabil und revisionstauglich** machen, dass sie als **Beleg + offener Posten + Zahlungsstatus** sauber an die Kanzlei übergeben werden können — und später optional auf Journal/DATEV erweiterbar bleiben.

---

## 1. Ist-Befund Rechnungsschiene (verifiziert)

**Real nutzbar ist allein die `invoices`-Schiene** (`InvoiceController` + `InvoiceCanvasController`, Sidebar-verlinkt). `deal_invoices` ist tote Alt-Schiene (Stub-Controller) und bleibt außen vor.

Direkt am Code bestätigt (`app/Models/Invoice.php`):
- **SoftDeletes aktiv** → Rechnungen löschbar; `InvoiceController@destroy` (≈Z. 271–286) ruft `delete()` ohne Status-/Bezahlt-Prüfung und entfernt angehängte Dateien **physisch** aus dem Storage.
- **Nummernkreis `makeInvoiceNo()` (Z. 148–164):** `RE-yy` + letzte Ziffern `+1`, `str_pad(4)`. **Kein Lock/keine Transaktion** (Race Condition bei parallelem `store`), Auswahl per `orderByDesc('id')` statt Nummer, und die Nummer wird **schon beim Draft** vergeben (auch Canvas-Draft) → **Lücken** bei gelöschten Drafts. Zweite, parallele Implementierung in `InvoiceCanvasController::makeInvoiceNo()` (Divergenzrisiko).
- **Zahlung:** `paid_amount`/`paid_at` vorhanden, `open_amount = max(0, total − paid)` als berechnetes Attribut da — **aber** `applyStatusAccounting()` (≈Z. 1178–1203) kennt nur Alles-oder-Nichts (`paid` → `paid_amount = total`, sonst 0; Code-Kommentar „no partial-payment input"). → **Teilzahlung / echte offene Posten nicht abbildbar.**
- **Storno/Gutschrift:** nur `type`-Strings (Gutschrift/Stornorechnung); **kein** Referenzfeld zur Ur-Rechnung im `$fillable`, kein Storno-Workflow, keine Sperre der Originalrechnung.
- **PDF:** **existiert nicht.** Trotz installiertem `barryvdh/laravel-dompdf` kein `Pdf::loadView`. „Download" betrifft nur **manuell hochgeladene** Dateien (`invoice_files`). → Beleg muss extern erzeugt werden.
- **Vorbereitungsfelder:** `deal_id`, `offer_detail_id` vorhanden; **`project_id`/`department_id`/`cost_center_id` noch NICHT** (kämen aus Phase 1). `invoice_histories` existiert (Audit-Log, ohne FK/Schutz).

## 2. Größte Risiken für die Kanzlei-Übergabe

| # | Risiko | Schwere |
|---|---|---|
| R1 | **Löschbare (auch bezahlte) Rechnungen** → GoBD-Verstoß; Belege verschwinden vor Übergabe | 🔴 |
| R2 | **Nummernkreis nicht lückenlos/fortlaufend** (Race + Vergabe bei Draft + Draft-Löschung) → formaler GoBD-Mangel, Nummern-Doubletten/Lücken | 🔴 |
| R3 | **Keine Teilzahlung / keine echten offenen Posten** → OP-Liste an Kanzlei unvollständig/falsch | 🔴 |
| R4 | **Kein Beleg-PDF** → keine revisionsfeste Belegkopie zur Übergabe | 🟠 |
| R5 | **Storno/Gutschrift ohne Referenz** → Korrekturen nicht nachvollziehbar, Originalrechnung bleibt editierbar | 🟠 |
| R6 | **Zwei Nummernkreis-Implementierungen** (Controller vs. Model) → Divergenz | 🟠 |
| R7 | **Rechnung nach `sent`/`paid` voll editierbar** → nachträgliche stille Änderung von Belegen | 🟠 |

## 3. Was Sprint 1 bauen soll (Scope)

1. **Nummernkreis härten:** transaktionaler, lückensicherer Sequenz-Service; **finale Nummer erst beim Übergang `draft → sent`** vergeben (Drafts tragen keine verbrauchte Nummer → keine Lücken). Eine einzige Vergabestelle (Controller-Doppelimplementierung entfernen).
2. **Löschsperre + Storno-statt-Löschen:** nur Drafts **ohne** vergebene Nummer löschbar; `sent`/`paid`/nummerierte Rechnungen **nicht** löschbar, sondern nur **stornierbar**. Storno/Gutschrift erzeugt ein **verknüpftes** Dokument (`original_invoice_id`) und **sperrt** die Ur-Rechnung gegen Änderung.
3. **Teilzahlung + echte offene Posten:** eigene Zahlungserfassung (mehrere Teilzahlungen je Rechnung); `paid_amount`/`open_amount` daraus abgeleitet; **`payment_status`** (offen/teilbezahlt/bezahlt/überfällig) statt Alles-oder-Nichts.
4. **Beleg-PDF:** Rechnung als PDF rendern (dompdf) und **unveränderlich** ablegen (Belegarchiv, mit Hash + Erzeugungszeit); ab `sent` ist das PDF der maßgebliche Beleg.
5. **Editiersperre ab `sent`:** nach Versand nur noch definierte Felder (z. B. Zahlungsdaten) änderbar; inhaltliche Änderung nur über Storno + Neuausstellung.
6. **Übergabe-/OP-Liste für die Kanzlei (leicht):** OP-Liste (offene/überfällige Rechnungen) + Beleg-Export (PDF/CSV der Rechnungs-/Zahlungsdaten). **Ohne** Kontierung/Steuerschlüssel (das kommt erst mit B1–B4 als echter Buchungsvorschlag).

## 4. Was Sprint 1 ausdrücklich NICHT baut
- **Kein** scharfes Journal, **keine** §146-Festschreibung, **keine** Audit-Hash-Kette.
- **Kein** DATEV/EXTF-Export, **keine** UStVA/BWA/Bilanz (Kanzlei).
- **Kein** Kontenrahmen/Kontenwerte, **keine** `tax_codes`/Steuerschlüssel, **kein** kontierter Buchungsvorschlag (wartet auf B1–B4).
- **Keine** Kostenstellen-Pflicht/Backfill (separater Strang; hier höchstens die vorhandenen Felder anzeigen).
- **Keine** automatische Umlage, **keine** playground-Optik.

## 5. Migrationsvorschläge (nur Konzept, additiv/nullable)

- **`invoice_payments`** (neu): `id · invoice_id(FK) · amount decimal(12,2) · paid_on date · method string nullable · reference string nullable · note text nullable · created_by nullable · timestamps`. Quelle der Teilzahlungen/OP.
- **`invoices`** erweitern (nullable): `original_invoice_id`(self-FK, Storno-Bezug) · `is_cancelled` bool default false · `cancelled_at` timestamp · `cancel_reason` string · `payment_status` string(16) default 'open' · `finalized_at` timestamp (Zeitpunkt `sent`/Belegfixierung) · `pdf_file_id` (Verweis auf Beleg-PDF). *(Überlappt bewusst mit den Phase-1-Ankern `original_invoice_id`/`is_reversed` — hier zusammenführen, nicht doppeln.)*
- **Beleg-PDF-Ablage:** entweder `invoice_files` um `kind` (`uploaded`|`generated`) + `sha256` + `generated_at` erweitern, **oder** dedizierte `invoice_documents`-Tabelle. Empfehlung: `invoice_files` erweitern (kleiner Eingriff).
- **Nummernkreis:** dedizierte `invoice_number_sequences` (`year unique · last_number`) **oder** Wiederverwendung der Phase-0-`accounting_number_ranges`. Empfehlung: falls Phase 0 noch nicht gebaut, schlanke eigene Sequenz-Tabelle; sonst `accounting_number_ranges` nutzen (kein Doppelmechanismus).
- **Nichts** an bestehenden Spalten löschen/umtypen; alles additiv, reversibel.

## 6. Service-/Controller-Anpassungen (Konzept)

- **`InvoiceNumberService`**: `nextForYear()` transaktional (`SELECT … FOR UPDATE`/atomarer Increment), lückenlos; aufgerufen **nur** beim `draft → sent`-Übergang. Ersetzt beide `makeInvoiceNo()`-Stellen.
- **`InvoiceController@destroy`**: hart einschränken — nur Draft ohne Nummer löschbar; sonst 422 + Hinweis „Stornieren statt Löschen"; keine physische Dateilöschung bei nummerierten Rechnungen.
- **`InvoiceStornoService`**: erzeugt verknüpfte Storno-/Gutschrift-Rechnung (`original_invoice_id`), setzt `is_cancelled`/`cancelled_at` an der Ur-Rechnung, sperrt sie gegen Edit/Delete.
- **`InvoicePaymentService`**: Teilzahlung erfassen → `paid_amount = sum(invoice_payments.amount)`, `open_amount` abgeleitet, `payment_status` neu berechnet (inkl. `overdue` via `due_date`). Überzahlung/Neg­ativ-Guard.
- **`InvoicePdfService`**: `Pdf::loadView(...)` (dompdf) → PDF rendern, in Belegablage speichern (Hash), `pdf_file_id` setzen; ab `sent` regenerierungsfest (neuer Storno statt Überschreiben).
- **`InvoiceController@update` / Canvas `save`**: Editiersperre ab `sent` (nur erlaubte Felder); Canvas-„löscht+neu"-Verhalten für nummerierte Rechnungen unterbinden.
- **`KanzleiExportController`** (leicht): OP-Liste + Belege/CSV-Export (ohne Kontierung).
- Alle Änderungen schreiben weiter in `invoice_histories` (Nachvollzug).

## 7. UI-Auswirkungen (nur ticket-Design)

- **Rechnungsliste:** `payment_status`-Badges (offen/teilbezahlt/bezahlt/überfällig) im ticket-Badge-Stil; Filter danach.
- **Zahlung erfassen:** Modal „Teilzahlung" (Betrag, Datum, Methode) — Toastr-Bestätigung, offene Posten aktualisiert.
- **Löschen → Stornieren:** Delete-Button für nummerierte Rechnungen ersetzt durch **„Stornieren/Gutschrift"**-Aktion (Modal mit Grund); Original wird als „storniert" markiert (Badge + Verweis auf Storno-Beleg).
- **PDF:** Button „Beleg-PDF erzeugen/herunterladen"; ab `sent` read-only Beleg.
- **OP-/Kanzlei-Ansicht:** Liste offener/überfälliger Rechnungen + „Kanzlei-Export" (PDF/CSV) — neuer Sidebar-Punkt unter Rechnungen/Finanzen.
- **Editiersperre:** ab `sent` sind Positionen read-only, Hinweisleiste „Rechnung versendet — Korrektur nur per Storno".

## 8. Tests / Prüfpunkte

- **Nummernkreis:** Concurrency-Test (N parallele `sent`-Übergänge → lückenlos, kollisionsfrei, aufsteigend); gelöschter Draft erzeugt **keine** Lücke (Nummer erst bei `sent`); nur eine Vergabestelle aktiv.
- **Löschsperre:** `sent`/`paid`/nummerierte Rechnung nicht löschbar (422); Draft ohne Nummer löschbar; keine physische Belegfile-Löschung bei nummerierten.
- **Storno:** erzeugt verknüpftes Dokument mit `original_invoice_id`; Ur-Rechnung danach nicht editier-/löschbar; Vorzeichen/Beträge korrekt.
- **Teilzahlung/OP:** mehrere Zahlungen summieren zu `paid_amount`; `open_amount`/`payment_status` korrekt; `overdue` bei Fälligkeitsüberschreitung; Überzahlung abgewiesen.
- **PDF:** wird erzeugt, gespeichert, Hash stabil; nach `sent` unveränderlich (Korrektur nur via Storno); Wiederholung erzeugt kein abweichendes „Original".
- **Editiersperre:** Positionsänderung nach `sent` blockiert; Canvas-„löscht+neu" unterbunden.
- **Kanzlei-Export:** OP-Liste zählt korrekt (offen/überfällig); Export enthält Belegdaten; keine Kontierung/Steuerschlüssel (Scope-Grenze).
- **Regression:** bestehender Draft-Flow (anlegen/bearbeiten) unverändert grün; Live-Daten unangetastet.

## 9. Definition of Done
1. Rechnungsnummern lückenlos + transaktional, erst ab `sent`, eine Vergabestelle; Concurrency-Test grün.
2. Bezahlte/versendete/nummerierte Rechnungen nicht löschbar; Storno-statt-Löschen mit Referenz + Ur-Rechnungssperre funktioniert.
3. Teilzahlungen erfassbar; `open_amount`/`payment_status` korrekt; überfällige Posten erkannt.
4. Beleg-PDF erzeugt, gespeichert, unveränderlich; ab `sent` maßgeblich.
5. Editiersperre ab `sent` wirksam; Änderungen protokolliert.
6. OP-/Kanzlei-Übergabeliste (Belege + offene Posten, ohne Kontierung) exportierbar; alles im ticket-Design.
7. Kein Journal/DATEV/UStVA/Kontierung; additive, reversible Migrationen; Live-Daten unverändert.

## 10. Reihenfolge der Umsetzung
1. **Nummernkreis-Service + Vergabe erst bei `sent`** (Fundament, behebt R2/R6).
2. **Löschsperre + Editiersperre ab `sent`** (behebt R1/R7).
3. **Storno-/Gutschrift-Service + Referenz/Sperre** (behebt R5).
4. **Teilzahlung/OP (`invoice_payments` + PaymentService + `payment_status`)** (behebt R3).
5. **Beleg-PDF (PdfService + Ablage)** (behebt R4).
6. **OP-/Kanzlei-Übergabeliste + Export** (Ziel-Nutzen: saubere Übergabe).
7. **UI-Feinschliff** (Badges, Modals, Ansichten) im ticket-Design.

---
**Ein-Satz-Fazit:** Sprint 1 macht die Rechnung revisionstauglich (lückenlose Nummern, Löschsperre, Storno-Bezug, Teilzahlung/OP, Beleg-PDF) und liefert eine saubere Kanzlei-Übergabeliste — ohne eine einzige buchhalterische/steuerliche Aussage; Journal/DATEV/Kontierung bleiben bewusst außen vor und additiv nachrüstbar.
