# S1-02 — Löschsperre für Rechnungen & Belegdateien; kein physisches Löschen nach Ausstellung; Vorbereitung Storno-statt-Löschen

**Stand:** 2026-07-02 · **Detail-Ticket — KEIN Code, KEINE Migration geschrieben, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — UI später nur im ticket-Design. Planner-/Kanban-Änderungen unberührt.
**Priorität:** P0 · **Sprint:** 1 · **Grundlage:** `sprint-1-tickets-rechnungsschiene.md`, `ticket-S1-01-nummernkreis.md`, A1 = Option 1 (Kanzlei führt FiBu; ticket liefert Belege + OP).

---

## 1. Ist-Befund (betroffene Dateien/Methoden)

- **`app/Models/Invoice.php`:** `use SoftDeletes` (Z. 10). Kein `issued_at`/`locked_at`/`deletion_blocked_reason`. „Ausgestellt" ist heute nur indirekt über `invoice_no` + `status` + `paid_amount`/`open_amount` erkennbar.
- **`app/Http/Controllers/Invoice/InvoiceController.php`:**
  - `destroy()` (≈Z. 271–286): ruft `$invoice->delete()` (SoftDelete) **ohne** Status-/Bezahlt-Prüfung; entfernt zudem angehängte Dateien **physisch** aus dem Storage (≈Z. 278).
  - `uploadFiles()` / Datei-Löschung (≈Z. 480–547): Belegdateien (`invoice_files`) können per `Storage::delete` physisch entfernt werden — ohne fachliche Sperre.
- **`app/Http/Controllers/Invoice/InvoiceCanvasController.php`:** eigenes Datei-/Attachment-Handling und „delete+recreate" von Items; kann Belege/Anhänge ebenfalls berühren.
- **`app/Models/InvoiceFile.php`:** `hasMany` von `Invoice::files()`; kein `archived_at`/`delete_blocked_at`.
- **`routes/web.php`:** Delete-/Datei-Routen unter `/invoices` (+ `/invoices/canvas`).
- **Folge:** Auch `sent`/`paid`/teilbezahlte Rechnungen und deren Belege sind heute löschbar → **GoBD-Verstoß** (R1).

## 2. Zielverhalten

- **Drafts, die nie ausgestellt wurden und keine Nummer haben, bleiben löschbar** (inkl. ihrer noch nicht ausgestellten Anhänge).
- **Ausgestellte Rechnungen** (`invoice_no` vergeben **oder** Status `sent`/`paid`/`overdue`/`cancelled`) **sind nicht löschbar** — weder hart noch soft.
- **Bezahlte/teilbezahlte Rechnungen** (`paid_amount > 0` bzw. vorhandene Zahlung) sind **niemals** löschbar.
- **Belegdateien ausgestellter Rechnungen dürfen nicht physisch gelöscht** werden (höchstens später soft-archiviert/ausgeblendet).
- **Statt Löschen** kommt bei ausgestellten Rechnungen **Storno/Gutschrift** (S1-04) — dieses Ticket **bereitet die Sperrlogik vor**, baut aber noch kein Storno.
- **Kein** Journal, **keine** DATEV, **keine** Steuerlogik.

## 3. Regeln

**Löschen erlaubt (Rechnung), nur wenn ALLE gelten:**
- `status = 'draft'` **und** `invoice_no IS NULL` **und** `paid_amount = 0` **und** keine `invoice_payments` vorhanden (nie ausgestellt, nie bezahlt).

**Löschen blockiert (Rechnung), wenn EINES gilt:**
- `invoice_no` gesetzt, **oder** `status ∈ {sent, paid, overdue, cancelled}`, **oder** `paid_amount > 0`/Zahlung vorhanden.

**Nur archivieren/ausblenden (statt löschen):**
- Ausgestellte Rechnungen werden **nie** gelöscht — Korrektur/Rücknahme läuft über **Storno/Gutschrift** (S1-04). „Ausblenden" der Rechnung selbst ist **nicht** Teil von S1-02.
- **Belegdateien einer ausgestellten Rechnung:** physisch nicht löschbar; soll ein Fehl-Anhang aus der Ansicht verschwinden, dann **soft-archivieren** (Datei bleibt erhalten, wird nur ausgeblendet) — optional (siehe §4), Kern von S1-02 ist die **Sperre**.

**Dateien:**
- Datei an **löschbarem Draft** → physisch löschbar (war nie Teil eines ausgestellten Belegs).
- Datei an **ausgestellter Rechnung** → physische Löschung blockiert; höchstens soft-archivieren.

## 4. Datenmodell-Konzept

**Empfehlung: In Sprint 1 reicht ein reiner App-Guard — keine Migration nötig.** „Ausgestellt/bezahlt" ist vollständig aus vorhandenen Feldern ableitbar (`invoice_no`, `status`, `paid_amount`, künftig `invoice_payments`). Zusätzliche Spalten würden nur Redundanz/Widerspruchsrisiko schaffen.

- **`invoices`:** **kein** `locked_at`/`issued_at`/`deletion_blocked_reason` erforderlich. *(`finalized_at` kommt ohnehin in S1-03; der Löschguard braucht es nicht.)* Der Blockiergrund wird **zur Laufzeit** vom Guard geliefert, nicht persistiert.
- **`invoice_files`:** **optional** (nur falls „ausblenden statt löschen" für Fehl-Anhänge gewünscht): additiv nullable `archived_at`, `archived_by`. **Nicht zwingend für die Sperre** — Empfehlung: in S1-02 **weglassen** und erst nachrüsten, wenn der Archiv-Anwendungsfall real gebraucht wird. `delete_blocked_at` ist unnötig (Blockade ist Regel, kein Datum).

→ **S1-02 ohne Migration umsetzbar** (reiner Guard + Model-Events). Optionaler `archived_at`-Nachtrag additiv später.

## 5. Service-Design — `InvoiceDeletionGuard`

**Zweck:** eine zentrale Wahrheit für „darf gelöscht werden?". Kein Controller entscheidet selbst.

**Methoden (Konzept):**
- `canDeleteInvoice(Invoice $invoice): GuardResult` — prüft die Regeln aus §3.
- `canDeleteFile(InvoiceFile $file): GuardResult` — leitet über die zugehörige Rechnung ab (Datei löschbar ⇔ Rechnung löschbar).
- `GuardResult` = `{ allowed: bool, code: string, message: string }`.

**Fehlercodes/Meldungen (Beispiele, deutsch):**
| code | Bedingung | Meldung |
|---|---|---|
| `INVOICE_ISSUED` | `invoice_no` gesetzt oder Status ausgestellt | „Ausgestellte Rechnungen können nicht gelöscht werden. Bitte stornieren." |
| `INVOICE_HAS_PAYMENTS` | `paid_amount > 0`/Zahlung vorhanden | „Bezahlte oder teilbezahlte Rechnungen können nicht gelöscht werden." |
| `FILE_LOCKED_ISSUED_INVOICE` | Datei gehört zu ausgestellter Rechnung | „Belegdateien ausgestellter Rechnungen sind gesperrt." |
| `OK` | löschbar | — |

**HTTP-Abbildung:** Business-Regel → **422** (Unprocessable) mit `code` + `message`; **403** akzeptabel, falls als Autorisierung behandelt. Empfehlung: **422** einheitlich.

## 6. Controller-/Model-Auswirkungen

- **`InvoiceController::destroy`:** zuerst `InvoiceDeletionGuard::canDeleteInvoice()`; bei `!allowed` → 422 + `code`/`message`, **kein** `delete()`. Nur bei löschbarem Draft SoftDelete zulassen.
- **Datei-Löschung (`uploadFiles`/Datei-Route):** vor `Storage::delete` → `canDeleteFile()`; bei Sperre 422, **kein** physisches Löschen. Physisches `Storage::delete` **nur** für Dateien nie-ausgestellter Drafts.
- **`InvoiceCanvasController`:** dieselben Guards auf alle Datei-/Löschpfade anwenden; „delete+recreate" darf keine Belege ausgestellter Rechnungen entfernen.
- **Model-Sicherheitsnetz:** `Invoice::deleting`-Event und `InvoiceFile::deleting`-Event → Guard erneut prüfen und bei Sperre Abbruch (schützt auch vor direktem `->delete()` außerhalb der Controller).
- **SoftDelete:** bleibt am Model, wird aber **nur für löschbare Drafts** tatsächlich ausgelöst; ausgestellte Rechnungen werden nie soft-gelöscht.

## 7. UI-Auswirkungen (nur ticket-Design)

- **Delete-Button nur bei löschbarem Draft** anzeigen; bei ausgestellten Rechnungen ausgeblendet/deaktiviert.
- **Bei ausgestellten Rechnungen** stattdessen Hinweis/Button **„Storno/Gutschrift"** (Aktion selbst kommt in S1-04; hier nur Hinweis/Platzhalter mit Tooltip „Löschen gesperrt — Storno nutzen").
- **Belegdateien gesperrter Rechnungen:** Schloss-Icon/Badge „gesperrt", kein Löschen-Icon.
- Fehlermeldungen via bestehendes **Toastr**; keine playground-Optik, bestehende ticket-Badges/-Buttons wiederverwenden.

## 8. Tests

- **Draft ohne Nummer:** löschbar (SoftDelete), Anhänge physisch entfernbar.
- **Draft mit Nummer:** **nicht** löschbar (422 `INVOICE_ISSUED`). *(kann auftreten, falls Nummer vor S1-01-Umbau vergeben wurde)*
- **`sent`:** nicht löschbar (422 `INVOICE_ISSUED`).
- **`paid`/teilbezahlt (`paid_amount > 0`):** nicht löschbar (422 `INVOICE_HAS_PAYMENTS`).
- **`cancelled`:** nicht löschbar (422 `INVOICE_ISSUED`).
- **Datei zu Draft:** physisch löschbar.
- **Datei zu `sent`:** physische Löschung blockiert (422 `FILE_LOCKED_ISSUED_INVOICE`); Datei bleibt im Storage.
- **Model-Netz:** direktes `$invoice->delete()` auf ausgestellter Rechnung wird durch `deleting`-Event verhindert.
- **API-Antwort:** klare `code` + deutsche `message` bei allen Sperren.
- **Regression:** bestehender Draft-Flow (anlegen/bearbeiten/löschen) unverändert; Live-Daten-Row-Count unverändert; keine bezahlte/ausgestellte Rechnung/Belege verschwunden.

## 9. Risiken & Guards

| Risiko | Guard |
|---|---|
| Umgehung über direktes `->delete()` | Model `deleting`-Event ruft denselben Guard (Defense-in-Depth) |
| Umgehung über Canvas-/Alt-Pfad | Guard auf **alle** Lösch-/Datei-Pfade (Controller + Canvas + Routen) |
| Fehl-Anhang an ausgestellter Rechnung kann nicht entfernt werden | bewusst gesperrt; optionale Soft-Archivierung später (§4) statt physischer Löschung |
| „Ausgestellt"-Erkennung uneinheitlich | eine zentrale `isIssued()`/Guard-Logik (invoice_no ∨ Status ∨ Zahlung); keine verstreuten Ad-hoc-Checks |
| Draft mit Alt-Nummer (vor S1-01) fälschlich löschbar | Regel verlangt zusätzlich `invoice_no IS NULL` → solche Drafts gelten als ausgestellt und sind gesperrt |
| Versehentliche Sperre valider Draft-Löschung | Regel eng (`draft` ∧ ohne Nummer ∧ unbezahlt); Tests decken Grenzfälle |

## 10. Definition of Done

1. Zentraler `InvoiceDeletionGuard` mit `canDeleteInvoice`/`canDeleteFile`, klaren Codes/Meldungen.
2. `InvoiceController::destroy`, Datei-Löschung und `InvoiceCanvasController` nutzen den Guard; keine Löschung ohne Freigabe.
3. Physisches `Storage::delete` nur für Dateien nie-ausgestellter Drafts.
4. Model-`deleting`-Events als Sicherheitsnetz für Invoice **und** InvoiceFile.
5. UI: Delete nur bei löschbarem Draft; Sperr-Badges/Hinweise im ticket-Design; Toastr-Fehlermeldungen.
6. Alle Tests (§8) grün; Live-Daten unangetastet; **keine Migration** nötig (reiner Guard).
7. Ausgestellte/bezahlte Rechnungen und deren Belege sind nachweislich nicht mehr löschbar.

## 11. Nicht im Scope

**Storno erstellen** (S1-04) · **Gutschrift erzeugen** (S1-04) · **Teilzahlung** (S1-05/06) · **PDF-Hash/Belegerzeugung** (S1-07) · **Editiersperre der Inhalte** (S1-03) · **Journal/DATEV/Festschreibung** · Soft-Archiv-UI für Dateien (optional später) · playground-Optik.

---
**Ein-Satz-Fazit:** Ein zentraler Lösch-Guard (plus Model-Sicherheitsnetz) macht ausgestellte und bezahlte Rechnungen sowie deren Belege unlöschbar, lässt reine Entwürfe weiter löschen und bereitet den Storno-statt-Löschen-Weg vor — ohne Migration und ohne jede buchhalterische Aussage.
