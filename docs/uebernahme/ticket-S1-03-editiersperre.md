# S1-03 — Editiersperre ab Ausstellung: gesendete/nummerierte Rechnungen sind inhaltlich unveränderlich

**Stand:** 2026-07-02 · **Detail-Ticket — KEIN Code, KEINE Migration geschrieben, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — UI später nur im ticket-Design. Planner-/Kanban-Änderungen unberührt.
**Priorität:** P1 · **Sprint:** 1 · **Grundlage:** `sprint-1-tickets-rechnungsschiene.md`, `ticket-S1-01-nummernkreis.md`, `ticket-S1-02-loeschsperre.md`, A1 = Option 1.

---

## 1. Ist-Befund (betroffene Dateien/Methoden)

- **`app/Http/Controllers/Invoice/InvoiceController.php`:**
  - `update()` (≈Z. 236): ändert Kopf-/Positionsdaten **ohne** Status-/Ausstellungs-Prüfung → auch `sent`/`paid` änderbar.
  - `syncItems()` (Positions-Sync) + `Invoice::recalcTotals()` → Beträge/Positionen nachträglich änderbar.
  - `updateStatus()` (≈Z. 29 ff.) / `applyStatusAccounting()` (≈Z. 1178–1203): Statuswechsel.
- **`app/Models/Invoice.php`:** `recalcTotals()` (Z. 130–146) `forceFill(subtotal/tax_amount/total_amount)->save()` — überschreibt Beträge; `$fillable` (Z. 14–45) enthält alle Kopf-/Betragsfelder.
- **`app/Http/Controllers/Invoice/InvoiceCanvasController.php`:** `save` (≈Z. 186/381) macht „delete+recreate" der Items → inhaltliche Änderung auch nach Ausstellung möglich.
- **`app/Models/InvoiceItem.php` / `InvoiceFile.php`:** Positions-/Dateihandling ohne Ausstellungs-Sperre.
- **`routes/web.php`:** `/invoices` Update-/Item-/Canvas-Routen.
- **Folge (R7):** ausgestellte Belege sind nachträglich still veränderbar → GoBD-/Kanzlei-Instabilität.

## 2. Zielverhalten

- **Draft ohne `invoice_no` bleibt voll editierbar.**
- Sobald **`invoice_no` vergeben** ist **oder** `status ∈ {sent, paid, overdue, cancelled}`: **Kopf- und Positionsdaten gesperrt.**
- **Zahlungsdaten** bleiben **kontrolliert** änderbar — ausschließlich über PaymentService/Status-Endpunkte (S1-05/06), nicht über den generischen Content-Editor.
- **Statuswechsel** `sent → paid/overdue/cancelled` bleibt erlaubt; `sent → draft` gesperrt (aus S1-01).
- Inhaltliche Änderungsversuche an ausgestellten Rechnungen → **422** mit klarer Meldung.
- **Korrekturen nur über Storno/Gutschrift (S1-04)** — hier nicht gebaut.

## 3. Inhaltlich gesperrte Felder (bei ausgestellter Rechnung)

**Gesperrt (Kopf):** `customer_id`, `object_id`, `deal_id`, `offer_detail_id`, `invoice_no`, `type`, `issue_date`, `due_date`, `service_from`, `service_to`, `currency`, `tax_rate`, `subtotal`, `tax_amount`, `total_amount`, **`notes`**, sowie `source_offer_detail_id`/`source_offer_items_hash`/`source_offer_synced_at`/`source_offer_updated_at` (Re-Sync würde Belegdaten überschreiben). Ebenso `department_id`/`project_id`/`cost_center_id`, **falls/sobald vorhanden** (Phase 1).
**Gesperrt (Positionen):** alle `invoice_items` (anlegen/ändern/löschen, inkl. Canvas „delete+recreate").

**Bewertung `notes`:** **gesperrt.** `notes` ist die auf dem Beleg gedruckte Rechnungsnotiz → Teil des ausgestellten Belegs; nachträgliche Änderung würde den Beleg verfälschen. Wird eine **interne, nicht gedruckte** Kanzlei-/Übergabe-Notiz gebraucht, ist das ein **separates** Feld (`internal_note`) — **nicht** `notes`, und **außerhalb S1-03** (additiv später).

## 4. Erlaubte Felder/Aktionen (bei ausgestellter Rechnung)

- **Zahlung:** `paid_amount`, `paid_at`, `payment_note`, `payment_status` — **nur** über PaymentService/Zahlungs-Endpunkte (S1-05/06), nicht über `update()`.
- **Statuswechsel:** `sent → paid | overdue | cancelled` (über `updateStatus`, Transition-Matrix §6). `overdue ↔ sent` je nach Fälligkeit zulässig.
- **Interne Kanzlei-/Übergabe-Notiz:** nur falls als **separates** Feld eingeführt (nicht `notes`); optional/später.
- **Datei-Download:** erlaubt. **Datei-Änderung/-Löschung:** gesperrt (S1-02). Zusätzliches Anhängen von Nachweisen regelt S1-02, nicht S1-03.
- **`finalized_at`, `updated_by`, `updated_at`:** technische Metadaten (durch Systemprozesse), keine Inhaltsänderung.

## 5. Datenmodell-Konzept

**Empfehlung: keine Migration nötig — Ableitung über `invoice_no` + `status` genügt.**
- „Ausgestellt" = `invoice_no IS NOT NULL` **oder** `status ∈ {sent, paid, overdue, cancelled}`. Identische Definition wie S1-02 → **eine gemeinsame Wahrheit** (`InvoiceIssueState::isIssued(Invoice)`), von S1-02-Guard und S1-03-Guard geteilt.
- **`locked_at`/`issued_at` nicht erforderlich.** Optional additiv nullable **`finalized_at`** (gesetzt beim `draft → sent`) — **nur informativ/Audit**, nicht als Guard-Bedingung. Empfehlung: in S1-03 **weglassen** oder rein informativ; die Sperre hängt nicht daran. So bleibt S1-03 migrationsfrei und widerspruchsfrei zu S1-02.

## 6. Service-Design — `InvoiceEditGuard`

Zentrale Wahrheit für „darf inhaltlich geändert / darf Status gewechselt werden?".

**Methoden (Konzept):**
- `canEditContent(Invoice $invoice): GuardResult` — Kopf-Inhalt änderbar? (nur Draft ohne Nummer).
- `canEditItems(Invoice $invoice): GuardResult` — Positionen änderbar? (nur Draft ohne Nummer).
- `canChangeStatus(Invoice $invoice, string $to): GuardResult` — Statuswechsel gemäß Matrix.
- `GuardResult = { allowed, code, message }`.

**Status-Transition-Matrix (erlaubt = ✔):**
| von \ nach | draft | sent | paid | overdue | cancelled |
|---|---|---|---|---|---|
| draft | ✔ | ✔ (→ Nummernvergabe S1-01) | – | – | ✔ (Draft verwerfen ≙ löschen, S1-02) |
| sent | ✖ (nur via Storno) | ✔ | ✔ | ✔ | ✔ (proper Reversal via S1-04) |
| overdue | ✖ | ✔ | ✔ | ✔ | ✔ |
| paid | ✖ | ✖ | ✔ | (bei Rückstand) | ✔ |

**Codes/Meldungen (deutsch):**
| code | Bedingung | Meldung |
|---|---|---|
| `INVOICE_LOCKED_ISSUED` | Kopf-Änderung an ausgestellter Rechnung | „Ausgestellte Rechnungen können inhaltlich nicht geändert werden. Bitte stornieren." |
| `ITEMS_LOCKED_ISSUED` | Positions-Änderung an ausgestellter Rechnung | „Positionen ausgestellter Rechnungen sind gesperrt." |
| `STATUS_TRANSITION_INVALID` | unzulässiger Statuswechsel (z. B. `sent → draft`) | „Dieser Statuswechsel ist nicht zulässig." |
| `OK` | erlaubt | — |

**HTTP:** Business-Regel → **422** (einheitlich mit S1-02); 403 akzeptabel, falls als Autorisierung modelliert.

## 7. Controller-/Model-Auswirkungen

- **`InvoiceController::update`:** zuerst `canEditContent()`; bei Sperre 422, keine Änderung. Draft-Pfad unverändert.
- **`syncItems()` / Positions-Endpunkte:** `canEditItems()` voranstellen; bei Sperre 422.
- **`Invoice::recalcTotals()`:** nur für Drafts ausführen; bei ausgestellter Rechnung nicht aufrufen (und das `forceFill()->save()` wird zusätzlich vom Model-Event abgefangen).
- **`InvoiceCanvasController::save`:** wenn der Aufruf Inhalt/Positionen verändern würde und die Rechnung ausgestellt ist → blockieren; „delete+recreate" der Items für ausgestellte Rechnungen unterbinden.
- **`updateStatus()`:** `canChangeStatus()` gemäß Matrix; `sent → draft` 422.
- **Model-Sicherheitsnetz (`Invoice::updating`):** `getDirty()`-Schlüssel gegen die **gesperrte Attributmenge** (§3) prüfen; ist die Rechnung ausgestellt und ein gesperrtes Attribut dirty → Abbruch (fängt auch `forceFill`/direktes `save()` außerhalb der Controller). Erlaubte dirty-Schlüssel: `paid_amount`, `paid_at`, `payment_note`, `payment_status`, `status` (via Matrix validiert), `finalized_at`, `updated_by`, `updated_at`. Analog `InvoiceItem::saving`/`deleting` → blockieren, wenn Eltern-Rechnung ausgestellt.

## 8. UI-Auswirkungen (nur ticket-Design)

- **Edit-Button nur bei Draft ohne Nummer** anzeigen; sonst ausgeblendet/deaktiviert.
- **Ausgestellte Rechnung:** **read-only-Ansicht** (Kopf + Positionen nicht editierbar); Positionsbereich gesperrt.
- **Badge „ausgestellt / gesperrt"** im ticket-Badge-Stil.
- **Hinweisleiste:** „Rechnung ausgestellt — Korrektur nur über Storno/Gutschrift" (Aktion selbst in S1-04).
- **Zahlung/Status:** weiterhin über die dafür vorgesehenen Aktionen/Modals (S1-05/06), klar getrennt vom gesperrten Content.
- Fehlermeldungen via **Toastr**; bestehende ticket-Komponenten; keine playground-Optik.

## 9. Tests

- **Draft editierbar:** Kopf + Positionen änderbar; `recalcTotals` läuft.
- **`sent` nicht editierbar:** `update()` auf Kopf → 422 `INVOICE_LOCKED_ISSUED`.
- **`invoice_no` gesetzt aber `draft`:** nicht editierbar (gilt als ausgestellt) → 422.
- **Positionen nach `sent`:** `syncItems`/Canvas-„delete+recreate" → 422 `ITEMS_LOCKED_ISSUED`.
- **`total_amount` stabil:** nach `sent` bleiben `subtotal/tax_amount/total_amount` unverändert; `recalcTotals` greift nicht.
- **Status `paid` erlaubt:** über `updateStatus`/PaymentService zulässig; Nummer/Inhalt unverändert.
- **`sent → draft` blockiert:** 422 `STATUS_TRANSITION_INVALID`.
- **Model-Netz:** direkter `$invoice->update([...gesperrt...])` / `forceFill()->save()` auf ausgestellter Rechnung wird durch `updating`-Event verhindert; nur Zahlungs-/Status-Felder passieren.
- **Regression:** Draft-Flow (anlegen/bearbeiten/senden) unverändert grün; Live-Daten unverändert.

## 10. Risiken & Guards

| Risiko | Guard |
|---|---|
| Umgehung über `update()`/`syncItems`/Canvas | Guard auf allen Content-Pfaden + Matrix auf `updateStatus` |
| Umgehung über `forceFill`/direktes `save()` | `Invoice::updating`-Event prüft dirty-Keys gegen gesperrte Menge (Defense-in-Depth) |
| `recalcTotals` überschreibt Beträge | nur für Drafts aufrufen; Event fängt Rest |
| Uneinheitliche „Ausgestellt"-Definition | gemeinsame `InvoiceIssueState::isIssued()` (mit S1-02 geteilt) |
| Zahlungs-/Statusänderung fälschlich blockiert | Zahlungs-/Statusfelder explizit in Allow-Liste; getrennte Endpunkte |
| Draft mit Alt-Nummer (vor S1-01) | gilt als ausgestellt (`invoice_no NOT NULL`) → gesperrt |
| Interne Notiz vs. Beleg-`notes` verwechselt | `notes` bleibt gesperrt; interne Notiz nur als separates Feld (später) |

## 11. Definition of Done

1. Zentraler `InvoiceEditGuard` (`canEditContent`/`canEditItems`/`canChangeStatus`) mit klaren Codes/Meldungen (422).
2. `update`, `syncItems`, `recalcTotals`, Canvas-`save`, `updateStatus` nutzen den Guard; ausgestellte Rechnungen inhaltlich unveränderlich.
3. Model-`updating`-Event (Invoice + InvoiceItem) als Sicherheitsnetz mit definierter gesperrter/erlaubter Attributmenge.
4. Zahlungs-/Statusänderungen weiter möglich über die dafür vorgesehenen Endpunkte; `sent → draft` gesperrt.
5. UI: Edit nur bei Draft ohne Nummer; ausgestellte Rechnung read-only mit Sperr-Badge + Storno-Hinweis; ticket-Design.
6. Alle Tests (§9) grün; **keine Migration** nötig; Live-Daten unangetastet; `total_amount` ausgestellter Rechnungen nachweislich stabil.

## 12. Nicht im Scope

**Storno/Gutschrift erzeugen** (S1-04) · **Teilzahlung** (S1-05/06) · **PDF-Hash/Belegerzeugung** (S1-07) · **DATEV/Journal/Festschreibung** · internes Notizfeld (optional später) · Datei-Anhang-Regeln (S1-02) · playground-Optik.

---
**Ein-Satz-Fazit:** Ein zentraler Edit-Guard plus Model-`updating`-Sicherheitsnetz friert Kopf, Positionen und Beträge ausgestellter Rechnungen ein, lässt nur Zahlungs-/Statusänderungen über dedizierte Endpunkte zu und verweist Korrekturen auf Storno — migrationsfrei und ohne buchhalterische Aussage.
