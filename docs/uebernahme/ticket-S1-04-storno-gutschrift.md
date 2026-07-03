# S1-04 — Storno/Gutschrift für ausgestellte Rechnungen: Korrekturweg statt Löschen/Editieren

**Stand:** 2026-07-02 · **Detail-Ticket — KEIN Code, KEINE Migration geschrieben, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — UI später nur im ticket-Design. Planner-/Kanban-Änderungen unberührt.
**Priorität:** P1 · **Sprint:** 1 · **Grundlage:** `ticket-S1-01-…` (Nummernkreis), `ticket-S1-02-…` (Löschsperre), `ticket-S1-03-…` (Editiersperre), A1 = Option 1 (Kanzlei führt FiBu; ticket liefert Belege + OP).

---

## 1. Begrifflichkeit & Empfehlung Sprint 1

- **Stornorechnung (Vollstorno):** hebt eine **komplett fehlerhafte/ungewollte** Rechnung vollständig auf — negative 1:1-Kopie der Ursprungsrechnung, Grund „Storno". Die Ursprungsrechnung wird dadurch neutralisiert.
- **Kaufmännische Gutschrift (Teil-/Vollgutschrift):** mindert die Forderung — z. B. Preisnachlass, Rücknahme, Reklamation; kann **teilweise** sein (einzelne Positionen/Beträge). *(Nicht zu verwechseln mit der umsatzsteuerlichen „Gutschrift" nach §14 Abs. 2 UStG = Beleg des Leistungsempfängers — das ist hier NICHT gemeint.)*
- **Wann was:** Vollstorno bei Fehlausstellung/Rückabwicklung der ganzen Rechnung; (Teil-)Gutschrift bei nachträglicher Minderung einzelner Positionen.

**Empfehlung Sprint 1: EIN technischer Korrekturtyp = Vollstorno (voller negativer Beleg).** Das Dokument kann per `type`-Label als „Stornorechnung" **oder** „Gutschrift" geführt werden, mechanisch ist es in Sprint 1 immer ein **vollständiger** negativer Beleg mit Referenz zur Ursprungsrechnung. **Partielle Gutschrift** (Positions-/Betragsauswahl) wird **verschoben** (eigenes späteres Ticket) — sie braucht Auswahl-/Restwertlogik und steuerliche Klärung. So bleibt Sprint 1 klein und korrekt.

## 2. Ist-Befund

- **`app/Models/Invoice.php`:** `type` = freier String (u. a. „Rechnung", „Gutschrift", „Stornorechnung"); `STATUS = [draft, sent, paid, overdue, cancelled]`. **Kein** `original_invoice_id`/`is_reversal`/`reversal_*` im `$fillable` (Z. 14–45). `signedInvoiceAmount` (Deal-Deckelung, Controller ≈Z. 1306–1311) dreht bei Gutschrift/Storno nur das Vorzeichen — **ohne** Referenzfeld.
- **`InvoiceController`:** `updateStatus` (≈Z. 29 ff.); heute existiert **kein** Storno-Workflow, `cancelled` ist nur ein Status-String ohne verknüpften Beleg.
- **`InvoiceHistory`:** Audit-Log vorhanden (`event_type`, `old/new_status`, `note`) — nutzbar zur Protokollierung des Storno.
- **S1-02/S1-03:** haben `is_cancelled/cancelled_at` nur **konzeptionell** erwähnt, aber **keine Migration** gebaut → hier wird das Reversal-Datenmodell erstmals real definiert (konsolidierte Namensgebung, siehe §3).

## 3. Datenmodell-Konzept (additiv, nullable)

**Am Reversal-Beleg (Storno/Gutschrift):**
| Feld | Typ | Zweck |
|---|---|---|
| `is_reversal` | bool, default false | markiert das Dokument als Storno/Gutschrift |
| `original_invoice_id` | unsignedBigInteger, nullable, self-FK | Referenz auf die Ursprungsrechnung |
| `reversal_reason` | text, nullable | Grund der Korrektur |

**An der Ursprungsrechnung:**
| Feld | Typ | Zweck |
|---|---|---|
| `is_reversed` | bool, default false | Ursprungsrechnung wurde storniert (gesetzt, wenn Storno **gesendet**) |
| `reversed_at` | timestamp, nullable | Zeitpunkt |
| `reversed_by` | unsignedBigInteger, nullable, FK employees | Verursacher |
| `reversal_invoice_id` | unsignedBigInteger, nullable, self-FK | **Rückverweis** auf den Storno-Beleg |

**Bewertung `reversal_invoice_id` (Rückverweis am Original): empfohlen JA.** Zwar aus `WHERE original_invoice_id = X` ableitbar, aber der gespeicherte Rückverweis macht (a) Idempotenz-Prüfung O(1), (b) UI-Verlinkung trivial, (c) den Zustand eindeutig. Geringe Redundanz, hoher Nutzen.

**Status vs. Flags — Empfehlung: KEINE neuen Status-Enum-Werte.** `status` bleibt die **Zahlungs-/Lebenszyklus**-Achse (draft/sent/paid/overdue/cancelled). Der Reversal-Zustand ist eine **eigene Achse** über `is_reversed`/`is_reversal`. Gründe: (a) eine bezahlte Rechnung soll `paid` bleiben (Historie/OP-Netting), nicht künstlich `cancelled` werden; (b) „reversed"/„credit_note" als Status würde die Zahlungsachse überladen. → UI zeigt „storniert" aus dem **Flag**, nicht aus `status`. `type`-Label „Stornorechnung"/„Gutschrift" beschreibt die Belegart.

*(Konsolidierung: die in S1-02/S1-03 nur erwähnten `is_cancelled/cancelled_at` werden durch `is_reversed/reversed_at` ersetzt — es gab keine Migration, also kein Konflikt. Überlappt bewusst mit den Phase-1-Ankern `original_invoice_id`/`is_reversed` — zusammenführen, nicht doppeln.)*

## 4. Service-Design — `InvoiceReversalService`

**`createFullReversal(Invoice $original, string $reason, Employee $actor, string $label = 'Stornorechnung'): Invoice`**

Ablauf (in `DB::transaction`):
1. **Guards:** `$original` muss **ausgestellt** sein (`InvoiceIssueState::isIssued`, geteilt mit S1-02/03) — Drafts können **nicht** storniert werden (die werden gelöscht). **Idempotenz:** existiert bereits ein nicht-verworfener Reversal (Draft **oder** gesendet) zu `$original` → **blockieren** (`ALREADY_REVERSED`).
2. **Reversal-Beleg als Draft** anlegen: Kopf 1:1 kopieren (`customer_id`, `object_id`, `deal_id`, `offer_detail_id`, `currency`, `tax_rate`, `service_from/to`), `issue_date = heute`, `type = $label` („Stornorechnung"/„Gutschrift"), `is_reversal = true`, `original_invoice_id = original.id`, `reversal_reason = $reason`, **`invoice_no = NULL`** (Nummer erst bei `sent`, S1-01).
3. **Positionen kopieren mit negiertem Vorzeichen:** je `invoice_item` `qty` negieren (Einzelpreis bleibt real, `line_total` wird negativ) → Summen `subtotal/tax_amount/total_amount` negativ. *(Alternative: `line_total` direkt negieren — Empfehlung negatives `qty`, damit Beleg lesbar bleibt.)*
4. **Rückverweis sperren:** `original.reversal_invoice_id = reversal.id` setzen (verhindert Doppelstorno). **`is_reversed` NOCH NICHT** setzen (erst bei `sent`).
5. **History:** Eintrag in `invoice_histories` an beiden Belegen.
6. Rückgabe des Reversal-Drafts.

**Beim Senden des Reversal (`draft → sent`):** eigene Nummer via `InvoiceNumberService` (S1-01); danach `original.is_reversed = true`, `reversed_at = now`, `reversed_by = actor`.
**Wird der Reversal-Draft vor dem Senden gelöscht** (Draft ohne Nummer, S1-02 erlaubt): `original.reversal_invoice_id` wieder auf `NULL` (Retry möglich).

**Wichtige Querschnitt-Koordination:** die Reversal-Linkage-Felder am Original (`is_reversed`, `reversed_at`, `reversed_by`, `reversal_invoice_id`) sind **keine Inhaltsfelder** → sie müssen in die **erlaubte dirty-Menge von S1-03** aufgenommen werden, damit der Edit-Guard sie passieren lässt.

## 5. Nummernkreis

- Storno/Gutschrift bekommt eine **eigene** Nummer über `InvoiceNumberService` **beim Senden** (nie die Ursprungsnummer wiederverwenden).
- **Empfehlung: derselbe fortlaufende Rechnungsnummernkreis** (`type='invoice'`, `RE-…`) für Rechnungen **und** Storno/Gutschrift → durchgängig lückenlose Nummerierung, wie von Kanzleien erwartet. *(Ein getrennter Kreis, z. B. `GU-…`, ist eine Steuerberater-Option — Default: gemeinsamer Kreis.)*

## 6. Status-/OP-Verhalten

- **Ursprungsrechnung bleibt inhaltlich unverändert** (S1-03); erhält nur die Reversal-Flags/Verweise.
- **Noch offene Rechnung:** der negative Reversal-Beleg trägt einen negativen offenen Betrag; das **Netting** (offener Posten Original ↔ Storno) übernimmt die OP-/Payment-Logik in **S1-05/06** — S1-04 bereitet es über die Flags/Referenzen nur vor. Keine eigene OP-Rechnung hier.
- **Bereits bezahlte Rechnung:** **Warnung** „Rückzahlung/Verrechnung mit Kanzlei klären" anzeigen; **keine** automatische Zahlungskorrektur, keine automatische Rückzahlung. Der Storno dokumentiert nur die Aufhebung; die tatsächliche Erstattung/Verrechnung ist manuell/Kanzlei.
- **Keine** DATEV-Buchung, **kein** Journal.

## 7. Controller-/UI-Auswirkungen (nur ticket-Design)

- **Aktion „Storno/Gutschrift erstellen"** nur bei **ausgestellten** Rechnungen (sonst ausgeblendet). Bei Draft: kein Storno, sondern Löschen (S1-02).
- **Modal:** Grund (Pflicht), Belegart-Auswahl (Stornorechnung/Gutschrift-Label), **Vorschau der negativen Positionen** + Summen; bei bezahlter Ursprungsrechnung **Warnhinweis** „Rückzahlung/Verrechnung klären".
- **Verlinkung beidseitig:** Ursprungsrechnung zeigt Badge „storniert" + Link zum Storno-Beleg; Storno-Beleg zeigt „Storno zu RE-…" + Rücklink.
- **Storno-Beleg** durchläuft denselben Draft→Sent→PDF-Fluss (Nummer bei `sent`, PDF in S1-07).
- Bestehende ticket-Komponenten (Modal/Badge/Toastr/Select2); **keine playground-Optik**.

## 8. Tests

- **`sent`-Rechnung → Storno-Entwurf:** erzeugt Reversal-Draft mit `is_reversal`, `original_invoice_id`, negativen Summen, ohne Nummer.
- **`paid`-Rechnung → Storno mit Warnung:** Reversal-Draft entsteht; UI/Response enthält Warnung „Rückzahlung/Verrechnung klären"; keine automatische Zahlungsänderung.
- **Draft nicht stornierbar:** `createFullReversal` auf Draft → Fehler; Draft wird stattdessen gelöscht (S1-02).
- **Doppelstorno blockiert:** zweiter `createFullReversal` bei vorhandenem Reversal → `ALREADY_REVERSED` (422).
- **Referenz:** Storno referenziert Original (`original_invoice_id`); Original referenziert Storno (`reversal_invoice_id`) nach Anlage.
- **Original unverändert:** Kopf/Positionen/Summen des Originals bleiben identisch; nur Reversal-Flags gesetzt.
- **Nummer erst bei Senden:** Reversal-Draft hat `invoice_no = NULL`; nach `sent` genau eine eigene Nummer (nicht die Ursprungsnummer).
- **Summen negativ:** `subtotal/tax_amount/total_amount` des Storno = negiertes Original.
- **Draft-Storno gelöscht → Retry:** nach Löschen des Reversal-Drafts ist `original.reversal_invoice_id` wieder NULL, neuer Storno möglich.
- **Regression:** normale Rechnungs-Flows unverändert; Live-Daten unangetastet.

## 9. Risiken & Guards

| Risiko | Guard |
|---|---|
| Doppelstorno | Idempotenz via `reversal_invoice_id` gesetzt + Prüfung auf vorhandenen Reversal |
| Storno eines Drafts | `isIssued`-Guard; Draft → nur Löschen |
| Original wird durch Storno „editiert" (S1-03-Konflikt) | Reversal-Linkage-Felder in S1-03-Allow-Liste; Positionen/Beträge des Originals bleiben unberührt |
| Verwaister Rückverweis nach Draft-Löschung | beim Löschen des Reversal-Drafts `reversal_invoice_id` zurücksetzen |
| Falsches Vorzeichen/Summen | negatives `qty`, `recalcTotals` auf dem Storno-Draft, Test auf negierte Summen |
| Bezahlte Rechnung → stille Zahlungskorrektur | **keine** Automatik; nur Warnung; Zahlungsausgleich in S1-05/06 bzw. Kanzlei |
| Nummernkreis-Bruch | eigene Nummer über `InvoiceNumberService`, gemeinsamer fortlaufender Kreis |

## 10. Definition of Done

1. Datenmodell (additiv/nullable) für Reversal (Beleg-Seite + Original-Seite) inkl. beidseitiger Referenz.
2. `InvoiceReversalService::createFullReversal` erzeugt vollständigen negativen Storno-Draft mit Referenzen; Idempotenz greift.
3. Storno erhält eigene Nummer erst bei `sent` (S1-01); Ursprungsnummer nie wiederverwendet.
4. Ursprungsrechnung bleibt inhaltlich unverändert; erhält Reversal-Flags/Verweise; `is_reversed` erst bei gesendetem Storno.
5. Bezahlte Ursprungsrechnung → Warnung, keine automatische Zahlungskorrektur.
6. UI: Storno-Aktion nur bei ausgestellten Rechnungen, Modal mit Grund + Positions-Vorschau + Warnung, beidseitige Verlinkung, ticket-Design.
7. Alle Tests (§8) grün; S1-03-Allow-Liste um Reversal-Felder ergänzt; Live-Daten unangetastet; keine DATEV/Journal.

## 11. Nicht im Scope

**DATEV/Journal/Festschreibung** · **automatische Rückzahlung** · **Teilzahlungsausgleich/OP-Netting** (S1-05/06) · **partielle Gutschrift** (späteres Ticket) · **PDF-Hash** (S1-07, Storno nutzt denselben PDF-Fluss) · **Steuerlogik/§17-USt-Korrektur** (Kanzlei/Steuerberater) · playground-Optik.

---
**Ein-Satz-Fazit:** Der `InvoiceReversalService` erzeugt zu einer ausgestellten Rechnung einen vollständigen, negativ gespiegelten Storno-/Gutschrift-Beleg mit beidseitiger Referenz und eigener Nummer bei Versand, lässt das Original unangetastet, warnt bei bereits bezahlten Rechnungen und überlässt Zahlungsausgleich (S1-05/06) und Buchung (Kanzlei) den zuständigen Schichten — ohne jede eigene FiBu-Aussage.
