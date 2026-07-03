# S1-07 — Unveränderlicher Rechnungs-/Storno-PDF-Beleg mit SHA-256-Hash

**Stand:** 2026-07-02 · **Detail-Ticket — KEIN Code, KEINE Migration geschrieben, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — PDF-Vorlage & UI im ticket-Design (kein playground-Layout). Planner-/Kanban-Änderungen unberührt.
**Priorität:** P1 · **Sprint:** 1 · **Grundlage:** S1-01 (Nummer bei `sent`), S1-02 (Datei-Löschsperre), S1-03 (Editiersperre), S1-04 (Storno), S1-05/06 (OP). A1 = Option 1. **Kein** DATEV/Journal.

---

## 1. Ist-Befund

- **`barryvdh/laravel-dompdf`** ist installiert, aber **ungenutzt** — kein `Pdf::loadView`/`loadHTML` in den Invoice-Controllern.
- **`app/Http/Controllers/Invoice/InvoiceController.php`:** „Download" betrifft nur **manuell hochgeladene** Dateien (`uploadFiles`/Datei-Routen ≈Z. 480–547); kein generierter Beleg.
- **`app/Http/Controllers/Invoice/InvoiceCanvasController.php`:** Rechnungsdarstellung als Canvas/HTML; kein finaler PDF-Beleg.
- **`app/Models/InvoiceFile.php` + `invoice_files` (Migration `2026_01_19_201639`):** speichert **hochgeladene** Dateien (Pfad/Original-Name/MIME/Größe/`created_by`); **kein** `sha256`/`file_role`/`immutable`/`generated_at`.
- **Views/Templates:** vorhandene Rechnungs-Blades (Index/Show/Canvas unter `resources/views/admin/…`) sind Bildschirm-/Canvas-Ansichten, **keine** dedizierte Druck-/PDF-Vorlage.
- **Folge (R4):** kein stabiler, unveränderlicher Beleg — die Kanzlei bekäme nur veränderbare HTML-/Canvas-Daten.

## 2. Datenmodell-Konzept

**Empfehlung: `invoice_files` erweitern (kein neues `invoice_documents` in Sprint 1).**
Begründung: `invoice_files` regelt bereits Storage + Download, und die **Löschsperre aus S1-02** greift schon auf diese Tabelle. Ein neuer Belegtyp braucht nur eine Rolle + Integritätsfelder. Eine eigene `invoice_documents`-Tabelle lohnt erst bei mehreren Belegarten (Mahnung/Lieferschein) → späteres Thema.

**`invoice_files` additiv erweitern (nullable):**
| Feld | Typ | Zweck |
|---|---|---|
| `file_role` | string(20), default `upload` | `upload` \| `final_pdf` |
| `sha256` | char(64), nullable | Integritäts-Hash des gespeicherten PDF |
| `generated_at` | timestamp, nullable | Erzeugungszeitpunkt |
| `generated_by` | unsignedBigInteger, nullable, FK employees | |
| `immutable` | boolean, default false | markiert den finalen Beleg als unveränderlich |

**`invoices` erweitern:**
| Feld | Typ | Zweck |
|---|---|---|
| `final_pdf_file_id` | unsignedBigInteger, nullable, FK invoice_files | O(1)-Zugriff + **Idempotenz** (gesetzt ⇒ Beleg existiert) |
| `pdf_failed_at` | timestamp, nullable | für Retry-Anzeige, falls Erzeugung fehlschlug |

*(Storno/Gutschrift sind eigene `Invoice`-Zeilen (S1-04) → ihr finaler Beleg ist ebenfalls `file_role='final_pdf'` an der Reversal-Rechnung; kein Extra-Typ nötig.)*

## 3. Service-Design — `InvoicePdfService`

- **`generateFinalPdf(Invoice $invoice): InvoiceFile`**
  1. **Idempotenz:** ist `invoice->final_pdf_file_id` gesetzt → **bestehende** Datei zurückgeben, **nicht** neu rendern.
  2. **Guards:** Rechnung muss ausgestellt sein (`invoice_no` vergeben, `status` ausgestellt) — sonst kein finaler Beleg.
  3. `renderBladeTemplate()` → HTML aus dedizierter PDF-Blade (ticket-Design) mit der **finalen** `invoice_no`.
  4. `Pdf::loadHTML(...)` (dompdf) → PDF-Binär.
  5. `storePdf()` → auf denselben Storage-Disk wie `invoice_files`, definierter Pfad (z. B. `invoices/{id}/RE-….pdf`).
  6. `calculateSha256()` über den **gespeicherten** Datei-Inhalt.
  7. `attachAsInvoiceFile()` → `invoice_files`-Zeile (`file_role='final_pdf'`, `sha256`, `generated_at/by`, `immutable=true`); `invoices.final_pdf_file_id` setzen; `pdf_failed_at = NULL`.
- **Kein Re-Render** für einen bereits vorhandenen finalen Beleg (weder bei Anzeige noch bei erneutem Senden).
- **Download** liefert **immer die gespeicherte Datei** (Stream aus Storage), nie ein Neu-Rendering.

## 4. Zeitpunkt & Fehlerbehandlung

- **Reihenfolge bei `draft → sent`:** (1) in DB-Transaktion Nummer vergeben (S1-01) + `status='sent'` + `finalized_at`; **commit** → (2) `generateFinalPdf()` mit der jetzt finalen Nummer.
  - Grund: PDF-Rendering ist I/O-lastig und gehört **nicht** in die kurze Nummern-Transaktion; und die finale Nummer muss im PDF stehen (deshalb Nummer zuerst).
- **Storno/Gutschrift:** identischer Fluss beim Senden des Reversal-Belegs (eigene Nummer S1-01 → eigenes PDF).
- **PDF-Erzeugung schlägt fehl — Empfehlung: KEIN Rollback des Statuswechsels.**
  - Begründung: Ein Rollback `sent → draft` würde die bereits **verbrauchte, lückenlose Nummer** (S1-01) zu einer **Lücke** machen — Lückenlosigkeit ist die härtere GoBD-Anforderung; ein fehlendes PDF ist **wiederherstellbar**, eine Nummernlücke nicht.
  - Verhalten: Rechnung bleibt `sent`, `final_pdf_file_id` bleibt NULL, `pdf_failed_at` gesetzt → **automatischer Retry** (Queued Job) **+ manueller „Beleg erneut erzeugen"-Button**. UI zeigt „Beleg wird erzeugt / fehlt — erneut versuchen".
  - `generateFinalPdf` ist idempotent → Retry erzeugt genau **einen** Beleg.

## 5. Beleg-Unveränderbarkeit

- **Kein Überschreiben:** ist `final_pdf_file_id` gesetzt, wird die Datei nie ersetzt.
- **Kein physisches Löschen:** `file_role='final_pdf'`/`immutable=true` + S1-02-Löschsperre (Belegdatei ausgestellter Rechnung) → Storage-Delete blockiert.
- **Kein Neu-Rendern** des finalen Belegs (Anzeige/Download = gespeicherte Datei).
- **Korrektur nur über Storno/Gutschrift** (S1-04) mit **eigenem** finalem PDF; das Original-PDF bleibt unangetastet.

## 6. PDF-Inhalt

- **Kopf:** Firma/Absender (aus bestehenden Stammdaten), Belegtitel „Rechnung" bzw. „Stornorechnung/Gutschrift".
- **Kunde/Objekt:** `customer` (new_leads), `object` (lead_alternative_adds).
- **`invoice_no`** (final), `issue_date`, `due_date`, Leistungszeitraum `service_from`–`service_to`.
- **Positionen:** Titel/Beschreibung, Menge/Einheit, Einzelpreis, Positionssumme.
- **Summen:** Netto (`subtotal`), USt (`tax_amount` bei `tax_rate`), Brutto (`total_amount`).
- **Zahlungsinformationen:** Zahlungsziel/`due_date` + Zahlungshinweis/Bankverbindung (statisch, wie **bei Ausstellung**).
  - **Wichtig (Bewertung):** der **eingefrorene Beleg enthält NICHT** den späteren `paid_amount`/OP-Stand — Zahlungen kommen erst nach Ausstellung und würden einen statischen Beleg verfälschen. Der dynamische Zahlungs-/OP-Stand lebt in der App/OP-Liste (S1-05/06/08), **nicht** im unveränderlichen PDF. *(Eine separate, nicht-immutable „Zahlungsübersicht" ist optional/später, nicht Teil des Belegs.)*
- **Storno/Gutschrift-PDF:** deutlicher Hinweis „Storno/Gutschrift zu Rechnung RE-… vom …" (Bezug auf Original via `original_invoice_id`), negative Summen.
- **QR/Hash sichtbar? — Empfehlung: NEIN.**
  - Der **SHA-256 der Datei** kann nicht auf der Datei selbst gedruckt werden (zirkulär: der Hash ändert sich durch das Drucken). Der Hash wird **nur in der DB** zur Integritätsprüfung gehalten.
  - Optional darf eine neutrale **Beleg-/Verifikations-ID** (nicht der Datei-Hash) gedruckt werden. **QR-Zahlcode ist ausdrücklich out of scope.**

## 7. Controller-/UI-Auswirkungen (nur ticket-Design)

- **„PDF anzeigen/Download"-Button** (liefert gespeicherte Datei, kein Re-Render).
- **Badge „Finaler Beleg erzeugt"** (grün) bzw. **„Beleg fehlt — erneut erzeugen"** (Warnung + Retry-Button), abgeleitet aus `final_pdf_file_id`/`pdf_failed_at`.
- **Keine Delete-Aktion** für `file_role='final_pdf'` (Icon entfällt; S1-02-Guard sekundär).
- Storno-Beleg erscheint mit eigenem PDF + Verweis auf Original.
- Bestehende ticket-Komponenten (Buttons/Badges/Toastr); **keine playground-Optik**; PDF-Blade schlicht im ticket-Stil.

## 8. Tests

- **`sent` erzeugt genau ein PDF:** nach Versand existiert 1 `final_pdf`, `final_pdf_file_id` gesetzt.
- **Finale Nummer im PDF:** gerendertes PDF enthält die vergebene `invoice_no`.
- **SHA-256 stimmt:** gespeicherter `sha256` = Hash des Datei-Inhalts.
- **Download rendert nicht neu:** wiederholter Download liefert byte-identische Datei (gleicher Hash), kein Re-Render.
- **Erneuter `send` erzeugt kein zweites PDF:** Idempotenz greift (`final_pdf_file_id` gesetzt).
- **Storno erzeugt eigenes PDF:** Reversal-Beleg hat eigenes `final_pdf` mit eigener Nummer + Original-Bezug.
- **PDF-Fehler:** Statuswechsel wird **nicht** zurückgerollt (Empfehlung); `pdf_failed_at` gesetzt, Retry erzeugt genau ein PDF; keine Nummernlücke.
- **Physisches Löschen blockiert:** `Storage::delete` auf `final_pdf` verweigert (S1-02).
- **Regression:** Upload-Dateien (`file_role='upload'`) weiter normal handhabbar; Live-Daten unangetastet.

## 9. Risiken & Guards

| Risiko | Guard |
|---|---|
| Nummernlücke bei PDF-Fehler+Rollback | **kein** Statusrollback; sent bleibt, PDF-Retry (Job + Button) |
| Doppeltes/erneutes Rendern verändert Beleg | Idempotenz via `final_pdf_file_id`; Download = gespeicherte Datei |
| Löschen/Überschreiben des Belegs | `immutable=true` + S1-02-Löschsperre; kein Overwrite-Pfad |
| Zirkulärer Datei-Hash auf PDF | Hash nur in DB; optional neutrale Beleg-ID drucken |
| Falscher/veralteter Zahlungsstand im Beleg | Beleg friert Ausstellungsstand ein; Zahlungen nur in App/OP |
| dompdf-Rendering-/Asset-Probleme | schlichte ticket-konforme Vorlage, lokale Assets; Rendering außerhalb der DB-Transaktion |
| PDF im falschen Moment (vor Nummer) | Reihenfolge: Nummer+sent commit → dann PDF mit finaler Nummer |

## 10. Definition of Done

1. `invoice_files` additiv um `file_role`/`sha256`/`generated_at`/`generated_by`/`immutable` erweitert; `invoices.final_pdf_file_id`/`pdf_failed_at` ergänzt.
2. `InvoicePdfService.generateFinalPdf` rendert (dompdf) die finale Rechnung, speichert unveränderlich, berechnet + speichert SHA-256, verknüpft den Beleg — idempotent.
3. Erzeugung bei `draft → sent` **nach** Nummernvergabe; Storno/Gutschrift analog beim Senden.
4. PDF-Fehler rollt den Statuswechsel **nicht** zurück; Retry (Job + Button) erzeugt genau einen Beleg; keine Nummernlücke.
5. Finaler Beleg unveränderlich: kein Überschreiben, kein physisches Löschen, kein Neu-Rendern; Korrektur nur via Storno-PDF.
6. Download liefert immer die gespeicherte Datei (Hash stabil); PDF enthält Nummer/Kopf/Positionen/Summen/Zahlungsziel; Zahlungsstand **nicht** eingefroren.
7. UI: Anzeigen/Download + Beleg-/Retry-Badge, keine Delete-Aktion für `final_pdf` — ticket-Design; Tests (§8) grün; Live-Daten unangetastet.

## 11. Nicht im Scope

**Layout-Perfektion/Design-Feinschliff** · **DATEV/Journal** · **E-Mail-Versand des Belegs** · **Bank/SEPA/QR-Zahlcode** · **ZUGFeRD/XRechnung/E-Rechnung** · dynamische Zahlungsübersicht im Beleg · eigene `invoice_documents`-Tabelle (später) · playground-Optik.

---
**Ein-Satz-Fazit:** Beim Versand rendert der `InvoicePdfService` einen finalen, unveränderlichen PDF-Beleg mit der endgültigen Nummer, speichert ihn mit SHA-256 und liefert ihn danach nur noch aus dem Storage aus; Storni bekommen einen eigenen Beleg, ein Renderfehler erzeugt nie eine Nummernlücke, und der eingefrorene Beleg bleibt frei von späterem Zahlungsstand — ohne DATEV/Journal.
