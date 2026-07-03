# Sprint 1 — Ticketliste: Rechnungsschiene härten für Kanzlei-Übergabe

**Stand:** 2026-07-02 · **Planungsdokument — KEIN Code, KEINE Migration, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — UI strikt im ticket-Design (Vuexy/Bootstrap-Blade, ticket-Sidebar, Cards/Tabellen/Modals/Badges, Select2/Toastr). Planner-/Kanban-Änderungen unberührt.
**Grundlage:** `arbeitspaket-sprint-1-rechnungsschiene.md` · A1 = Option 1 (Kanzlei führt FiBu). Sprint 1 baut **keine** Buchhaltung.

**Verifizierte Kern-Dateien** (Ausgangspunkt für alle Tickets):
`app/Models/Invoice.php` · `app/Models/InvoiceItem.php` · `app/Models/InvoiceFile.php` · `app/Models/InvoiceHistory.php` · `app/Http/Controllers/Invoice/InvoiceController.php` · `app/Http/Controllers/Invoice/InvoiceCanvasController.php` · `routes/web.php` (Bereich `/invoices`, `/invoices/canvas`) · Migrationen `2023_07_19_100437_create_invoices_table.php` (+ Payment-/Deal-/Auftrag-Sync-Ergänzungen) · `2026_01_19_201601_create_invoice_items_table.php` · Views unter `resources/views/admin/…` (exakte Blade-Pfade beim Umsetzen lokalisieren; **nicht** playground-Optik). `barryvdh/laravel-dompdf` ist installiert.

> **Konvention:** „Migration als Konzept" = additiv, nullable, reversibel; keine bestehende Spalte löschen/umtypen. Jedes Ticket schreibt Änderungen weiter in `invoice_histories`.

---

## Priorisierung & Abhängigkeitskette
```
P0  S1-01 Nummernkreis ──► S1-02 Löschsperre
                 │
P1               ├─► S1-03 Editiersperre ab sent
                 ├─► S1-04 Storno/Gutschrift (braucht S1-01,S1-02)
                 ├─► S1-05 Teilzahlung invoice_payments ──► S1-06 payment_status
                 └─► S1-07 Beleg-PDF (braucht S1-03)
P2  S1-08 OP-/Kanzlei-Übergabeliste (braucht S1-06) · S1-09 UI-Konsolidierung
P3  S1-10 Cleanup/Legacy · S1-11 Regressionssuite
```

---

## P0 — muss vor allem anderen

### S1-01 · Nummernkreis transaktional & lückenlos, Vergabe erst bei `draft → sent`, eine Vergabestelle
- **Ziel:** Rechnungsnummern fortlaufend, lückenlos, kollisionsfrei; finale Nummer erst beim Versand.
- **Warum wichtig:** R2/R6 — Race Condition + Nummernvergabe beim Draft + doppelte Implementierung = GoBD-Mangel (Lücken/Doubletten).
- **Betroffene Dateien:** `app/Models/Invoice.php` (`makeInvoiceNo()` Z. 148–164), `app/Http/Controllers/Invoice/InvoiceCanvasController.php` (zweite `makeInvoiceNo()`), `InvoiceController.php` (`store`, `updateStatus`/`applyStatusAccounting`), `routes/web.php`.
- **Migration/Konzept:** neue `invoice_number_sequences` (`year` unique · `last_number` unsignedBigInteger) **oder** Wiederverwendung `accounting_number_ranges` (falls Phase 0 gebaut). Empfehlung: eigene schlanke Sequenz, falls Phase 0 noch nicht existiert.
- **Service/Controller:** neuer `InvoiceNumberService::nextForYear()` — transaktional (`DB::transaction` + `lockForUpdate()`/atomarer Increment). **Draft** erhält **keine** finale Nummer (nur temporäre Kennung/`NULL`); finale Vergabe **einmalig** beim Statuswechsel zu `sent`. Beide bisherigen `makeInvoiceNo()` entfernen und durch den Service ersetzen.
- **UI:** Draft zeigt „Entwurf – Nummer bei Versand"; Nummer erscheint erst nach `sent`. ticket-Design.
- **Abhängigkeiten:** keine (Fundament).
- **Akzeptanzkriterien:** (a) parallele `sent`-Übergänge erzeugen nie doppelte/übersprungene Nummern; (b) gelöschter Draft erzeugt **keine** Lücke; (c) genau eine Vergabestelle im Code; (d) Format `RE-yy####` bleibt.
- **Tests:** Concurrency-Test (viele parallele `sent`), Draft-Löschung → keine Lücke, Jahreswechsel-Reset, kein Draft mit finaler Nummer.
- **Risiken/Guards:** Deadlock bei Lock → kurze Transaktion, nur Sequenzzeile sperren. Backfill-frei (bestehende Nummern unangetastet).
- **Nicht im Scope:** Debitoren-/DATEV-Nummernkreise, Storno-Nummernlogik (→ S1-04 nutzt denselben Service).

### S1-02 · Löschsperre (kein Löschen nummerierter/versendeter/bezahlter Rechnungen; keine physische Belegfile-Löschung)
- **Ziel:** Belege bleiben erhalten; Löschen nur für unnummerierte Drafts.
- **Warum wichtig:** R1 — löschbare (auch bezahlte) Rechnungen + physische Dateilöschung = akuter GoBD-Verstoß.
- **Betroffene Dateien:** `InvoiceController.php` (`destroy` ≈Z. 271–286, `uploadFiles`/Datei-Löschung ≈Z. 480–547), `Invoice.php` (SoftDeletes), `InvoiceCanvasController.php`.
- **Migration/Konzept:** keine zwingend; optional Model-Observer/Policy. (Storno-Felder kommen in S1-04.)
- **Service/Controller:** `destroy` hart einschränken — nur `status='draft'` **ohne** `invoice_no` löschbar; sonst 422 + Hinweis „Stornieren statt Löschen". Physische Belegfile-Löschung bei nummerierten Rechnungen unterbinden. Zentraler Guard (Model `deleting`-Event) als Sicherheitsnetz.
- **UI:** Delete-Button für nummerierte Rechnungen ausblenden/deaktivieren; stattdessen Verweis auf Storno (S1-04).
- **Abhängigkeiten:** S1-01 (Nummernstatus als Kriterium).
- **Akzeptanzkriterien:** `sent`/`paid`/nummerierte Rechnung nicht löschbar; Draft ohne Nummer löschbar; Belegdateien nummerierter Rechnungen werden nicht physisch entfernt.
- **Tests:** Löschversuch je Status; Datei-Erhalt; Guard greift auch bei direktem `delete()`.
- **Risiken/Guards:** versehentliche Sperre valider Draft-Löschung → klare Statusbedingung; Guard + Route-Ebene doppelt.
- **Nicht im Scope:** Storno-Workflow selbst (S1-04), Editiersperre (S1-03).

---

## P1 — Kernfunktion

### S1-03 · Editiersperre ab `sent`
- **Ziel:** Nach Versand keine stille inhaltliche Änderung mehr; Korrektur nur über Storno.
- **Warum wichtig:** R7 — nach `sent`/`paid` voll editierbar (inkl. Canvas „löscht+neu alle Items").
- **Betroffene Dateien:** `InvoiceController.php` (`update` ≈Z. 236), `InvoiceCanvasController.php` (`save` ≈Z. 186/381), FormRequests.
- **Migration/Konzept:** `invoices.finalized_at` (nullable timestamp, gesetzt bei `sent`).
- **Service/Controller:** ab `finalized_at`/`status='sent'` nur definierte Felder änderbar (Zahlungsdaten, interne Notiz); Positions-/Betrags-Änderung blockiert (422). Canvas-„delete+recreate" für nummerierte Rechnungen unterbinden.
- **UI:** Positionsbereich read-only ab `sent`; Hinweisleiste „Rechnung versendet — Korrektur nur per Storno". ticket-Design.
- **Abhängigkeiten:** S1-01.
- **Akzeptanzkriterien:** Positions-/Summenänderung nach `sent` blockiert; erlaubte Felder weiter änderbar; Draft-Edit unverändert.
- **Tests:** Edit-Versuch nach `sent`; Canvas-Save nach `sent`; erlaubte vs. gesperrte Felder.
- **Risiken/Guards:** zu strenge Sperre bremst Alltag → erlaubte Felder klar definieren.
- **Nicht im Scope:** Storno-Erstellung (S1-04).

### S1-04 · Storno/Gutschrift mit Referenz + Ur-Rechnungssperre
- **Ziel:** Korrektur revisionssicher: verknüpftes Storno-/Gutschrift-Dokument statt Änderung/Löschung.
- **Warum wichtig:** R5 — `type`-Strings ohne Referenz; Originalrechnung bleibt offen/editierbar.
- **Betroffene Dateien:** `InvoiceController.php`, `Invoice.php` (Relationen), Views.
- **Migration/Konzept:** `invoices` erweitern (nullable): `original_invoice_id` (self-FK), `is_cancelled` (bool default false), `cancelled_at`, `cancel_reason`. *(Bewusste Zusammenführung mit Phase-1-Ankern `original_invoice_id`/`is_reversed` — nicht doppeln.)*
- **Service/Controller:** `InvoiceStornoService`: erzeugt Storno-/Gutschrift-Rechnung (eigene Nummer via `InvoiceNumberService`, Vorzeichen/Beträge gespiegelt, `original_invoice_id` gesetzt); markiert Ur-Rechnung `is_cancelled`+`cancelled_at`; sperrt sie gegen Edit/Delete.
- **UI:** „Stornieren/Gutschrift"-Aktion (Modal mit Grund); Ur-Rechnung erhält Badge „storniert" + Verweis auf Storno-Beleg; Storno verweist zurück. ticket-Design.
- **Abhängigkeiten:** S1-01, S1-02, S1-03.
- **Akzeptanzkriterien:** Storno erzeugt verknüpftes Dokument mit `original_invoice_id`; Ur-Rechnung danach gesperrt; Beträge korrekt gespiegelt; Deal-Deckelung (`signedInvoiceAmount`) konsistent.
- **Tests:** Storno-Erzeugung, Verknüpfung beidseitig, Ur-Rechnung nicht mehr editier-/löschbar, Vorzeichen, Nummernvergabe.
- **Risiken/Guards:** doppeltes Stornieren → Idempotenz-Guard (`is_cancelled` prüfen).
- **Nicht im Scope:** buchhalterische Verbuchung des Stornos (Kanzlei).

### S1-05 · Teilzahlung — `invoice_payments` + PaymentService
- **Ziel:** Mehrere Teilzahlungen je Rechnung erfassbar; `paid_amount` daraus abgeleitet.
- **Warum wichtig:** R3 — heute nur alles-oder-nichts; echte OP unmöglich.
- **Betroffene Dateien:** `InvoiceController.php` (`applyStatusAccounting` ≈Z. 1178–1203), `Invoice.php`.
- **Migration/Konzept:** neue `invoice_payments` (`id · invoice_id(FK) · amount decimal(12,2) · paid_on date · method string nullable · reference string nullable · note text nullable · created_by nullable · timestamps`).
- **Service/Controller:** `InvoicePaymentService::record()` — Zahlung anlegen, `paid_amount = sum(amount)`, `paid_at` = letzte Zahlung; Überzahlungs-/Negativ-Guard. `applyStatusAccounting` auf zahlungsgetrieben umstellen (kein `paid_amount=total`-Automatismus mehr).
- **UI:** Modal „Zahlung erfassen" (Betrag/Datum/Methode); Zahlungshistorie je Rechnung. ticket-Design.
- **Abhängigkeiten:** S1-01.
- **Akzeptanzkriterien:** mehrere Zahlungen summieren korrekt; `open_amount` (bestehendes Attribut) stimmt; Überzahlung abgewiesen.
- **Tests:** Teilzahlungssummen, Überzahlung, `paid_at`, Rundung.
- **Risiken/Guards:** Race bei parallelen Zahlungen → Transaktion; Rundungsdifferenzen → decimal(12,2).
- **Nicht im Scope:** `payment_status`-Ableitung (S1-06), Skonto/Verzugszins (Kanzlei/Steuerberater).

### S1-06 · `payment_status` (offen / teilbezahlt / bezahlt / überfällig)
- **Ziel:** Klarer Zahlungsstatus je Rechnung für OP-Liste und Kanzlei-Übergabe.
- **Warum wichtig:** R3 — Übergabeliste braucht belastbaren Status.
- **Betroffene Dateien:** `Invoice.php` (abgeleitetes Attribut/Scope), `InvoiceController.php`.
- **Migration/Konzept:** `invoices.payment_status` (string(16) default 'open') **oder** rein berechnet. Empfehlung: gespeicherte Spalte, aktualisiert durch PaymentService + einen Fälligkeits-Recompute.
- **Service/Controller:** Ableitung: `open` (paid=0), `partial` (0<paid<total), `paid` (paid≥total), `overdue` (open/partial **und** `due_date < heute`). Recompute bei Zahlung + tägliche/Login-getriggerte Überfälligkeitsprüfung.
- **UI:** Status-Badges + Filter in der Rechnungsliste. ticket-Design.
- **Abhängigkeiten:** S1-05.
- **Akzeptanzkriterien:** Status je Konstellation korrekt; `overdue` nur bei offener/teilbezahlter Rechnung nach Fälligkeit; konsistent mit `open_amount`.
- **Tests:** Statusmatrix (paid=0/teil/voll × fällig/nicht fällig); Übergang partial→paid; overdue-Grenze.
- **Risiken/Guards:** Zeitzonen/Fälligkeit → `due_date`-Vergleich mit App-Zeit; Status nur ableiten, nie manuell frei setzen.
- **Nicht im Scope:** Mahnwesen/Mahnstufen (später, ggf. Kanzlei).

### S1-07 · Beleg-PDF unveränderlich mit Hash
- **Ziel:** Revisionsfeste PDF-Belegkopie je Rechnung ab `sent`.
- **Warum wichtig:** R4 — kein PDF trotz installiertem dompdf; Kanzlei braucht Belege.
- **Betroffene Dateien:** neuer `InvoicePdfService`, `InvoiceController.php`, `InvoiceFile.php`, neue Blade-PDF-Vorlage (ticket-Design, **nicht** playground).
- **Migration/Konzept:** `invoice_files` erweitern: `kind` (`uploaded`|`generated`), `sha256`, `generated_at`; optional `invoices.pdf_file_id`. (Alternativ dedizierte `invoice_documents`.)
- **Service/Controller:** `Pdf::loadView()` (dompdf) rendert Rechnung → speichert Beleg-PDF (mit `sha256`), verknüpft `pdf_file_id`. Ab `sent` regenerierungsfest (inhaltliche Korrektur nur via Storno, nicht Überschreiben).
- **UI:** Button „Beleg-PDF erzeugen/herunterladen"; ab `sent` read-only Beleg. ticket-Design.
- **Abhängigkeiten:** S1-03 (Editiersperre, damit PDF stabil).
- **Akzeptanzkriterien:** PDF erzeugt+gespeichert; `sha256` stabil bei gleichem Inhalt; nach `sent` kein abweichendes „Original"; Download funktioniert.
- **Tests:** Erzeugung, Hash-Stabilität, Immutabilität nach `sent`, Storno erzeugt eigenes PDF.
- **Risiken/Guards:** dompdf-Layout/Assets → einfache, ticket-konforme Vorlage; große PDFs → Storage-Pfad wie bestehende `invoice_files`.
- **Nicht im Scope:** ZUGFeRD/XRechnung/E-Rechnung-Format (später separat prüfen).

---

## P2 — danach

### S1-08 · OP-/Kanzlei-Übergabeliste + Export (ohne Kontierung)
- **Ziel:** Offene-Posten-Liste + Belegexport als saubere Kanzlei-Übergabe.
- **Warum wichtig:** Ziel-Nutzen von Sprint 1 (A1 Option 1): ticket liefert Belege + OP.
- **Betroffene Dateien:** neuer `KanzleiExportController`, Views, `routes/web.php`.
- **Migration/Konzept:** keine (liest bestehende Daten).
- **Service/Controller:** OP-Liste (offene/überfällige Rechnungen mit `open_amount`, Fälligkeit, Kunde); Export als CSV + Beleg-PDF-Sammlung. **Keine** Konten/Steuerschlüssel/Kontierung.
- **UI:** neuer Sidebar-Punkt „Offene Posten / Kanzlei-Export" unter Rechnungen/Finanzen; Liste + Filter + Export-Button. ticket-Design.
- **Abhängigkeiten:** S1-06 (payment_status), S1-07 (PDF).
- **Akzeptanzkriterien:** OP-Liste zählt offen/überfällig korrekt; Export enthält Belegdaten + PDFs; keine Kontierung enthalten.
- **Tests:** OP-Summen, Filter, Exportinhalt, Scope-Grenze (keine Kontierung).
- **Risiken/Guards:** Scope-Creep Richtung Buchungsvorschlag → bewusst ohne mapping_key/Konten (wartet auf B1–B4).
- **Nicht im Scope:** kontierter DATEV-Buchungsvorschlag, EXTF.

### S1-09 · UI-Konsolidierung im ticket-Design
- **Ziel:** Einheitliche, ticket-konforme Bedienung aller neuen Funktionen.
- **Warum wichtig:** Konsistenz; keine playground-Optik; verstreute Mini-UIs zusammenführen.
- **Betroffene Dateien:** Rechnungs-Views (`resources/views/admin/…` lokalisieren), `sidebar.blade.php`.
- **UI:** payment_status-Badges, Zahlungs-Modal, Storno-Aktion, PDF-Button, OP-Ansicht — durchgängig mit bestehenden ticket-Komponenten (Bootstrap/Vuexy, Select2, Toastr, bestehende Badge-/Modal-Klassen).
- **Abhängigkeiten:** S1-04…S1-08.
- **Akzeptanzkriterien:** alle neuen Elemente im ticket-Stil; keine Tailwind/Alpine-Reste; Navigation in bestehender Sidebar-Struktur.
- **Tests:** visuelle/manuelle Prüfung gegen bestehende ticket-Seiten; Responsive wie Restsystem.
- **Risiken/Guards:** Design-Drift → bestehende Klassen wiederverwenden, nichts aus playground kopieren.
- **Nicht im Scope:** neues Designsystem.

---

## P3 — optional / Feinschliff

### S1-10 · Cleanup & Legacy-Stilllegung
- **Ziel:** Doppelstrukturen/Altpfade entschärfen.
- **Betroffene Dateien:** `InvoiceCanvasController.php` (zweite `makeInvoiceNo` — bereits in S1-01 entfernt, hier verifizieren), `deal_invoices`-Direktroute (`routes/web.php` ≈Z. 4332), `app/Http/Controllers/Old/NewLeadsInvoiceController.php`, `invoice_histories` (FK/Härtung).
- **Konzept:** `deal_invoices`-Direktroute stilllegen/verstecken (nicht löschen, um Live-Daten nicht zu berühren); Legacy-Invoice-Controller prüfen; `invoice_histories` optional FK auf `invoices` + Schutz.
- **Akzeptanzkriterien:** keine zweite Nummernvergabe mehr erreichbar; tote Alt-Schiene nicht mehr im Nav.
- **Risiken/Guards:** Live-Daten in `deal_invoices` nicht anfassen — nur Route/Nav.
- **Nicht im Scope:** endgültiges Löschen von `deal_invoices` (spätere Migrationsentscheidung).

### S1-11 · Regressionssuite Rechnungsschiene
- **Ziel:** Bestehende Flows abgesichert, neue Funktionen dauerhaft geschützt.
- **Betroffene Dateien:** `tests/…` (neu).
- **Konzept:** Feature-Tests: Draft-Anlage/-Edit unverändert; Nummernkreis-Concurrency; Lösch-/Editiersperre; Storno-Verknüpfung; Teilzahlung/`payment_status`; PDF-Immutabilität; OP-Export. Live-Datenschutz (Row-Count vor/nach Migrationen).
- **Akzeptanzkriterien:** grüne Suite; bestehende Rechnungs-Flows nachweislich unverändert.
- **Risiken/Guards:** ticket hat „kaum Tests" (Bestandsaufnahme) → hier Grundstock schaffen.
- **Nicht im Scope:** vollständige App-Testabdeckung.

---

## Scope-Grenze Sprint 1 (gilt für ALLE Tickets)
**NICHT gebaut:** Journal/Festschreibung/Audit-Hash · DATEV/EXTF · UStVA/BWA/Bilanz · Kontenrahmen/`tax_codes`/Steuerschlüssel · kontierter Buchungsvorschlag · Kostenstellen-Pflicht/Backfill · automatische Umlage · playground-Optik. Alle Migrationen additiv/nullable/reversibel; Live-Daten unangetastet; später additiv auf Journal/DATEV (Option 2) erweiterbar.

**Reihenfolge kompakt:** S1-01 → S1-02 → (S1-03, S1-05→S1-06, S1-07, S1-04) → S1-08 → S1-09 → S1-10/S1-11.
