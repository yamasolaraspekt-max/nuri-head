# S1-05 / S1-06 — Teilzahlungen (`invoice_payments` + PaymentService) & `payment_status`/Offene Posten

**Stand:** 2026-07-02 · **Doppel-Detail-Ticket — KEIN Code, KEINE Migration geschrieben, keine bestehende Datei geändert.**
**Führend:** `ticket`. **playground:** nur Konzeptquelle, keine Design-Vorlage — UI später nur im ticket-Design. Planner-/Kanban-Änderungen unberührt.
**Priorität:** P1 · **Sprint:** 1 · **Grundlage:** S1-01…S1-04, A1 = Option 1 (Kanzlei führt FiBu; ticket liefert Belege + OP). **Kein** Journal/DATEV/Kontierung.

- **S1-05:** Zahlungsereignisse in `invoice_payments`; `paid_amount` daraus abgeleitet; PaymentService.
- **S1-06:** `payment_status` + offener Posten (open/partially_paid/paid + überfällig); getrennt vom Beleg-`status`; Kanzlei-OP-Übersicht.

---

## 1. Ist-Befund

- **`app/Models/Invoice.php`:** `getOpenAmountAttribute()` (Z. 70–73) = `max(0, round(total_amount − paid_amount, 2))`; `paid_amount`, `paid_at`, `payment_note` in `$fillable`; `$appends = ['open_amount']`.
- **`InvoiceController::applyStatusAccounting()` (≈Z. 1178–1203):** **alles-oder-nichts** — bei `paid` `paid_amount = total`, sonst 0 (Code-Kommentar „no partial-payment input"). Keine Zahlungshistorie, kein Zahlungsnachweis je Zahlung.
- **`updateStatus()` (≈Z. 29 ff.):** setzt Status; keine echte OP-Logik.
- **Migrationen:** `2026_05_08_124416_add_payment_fields_to_invoices_table` (paid_amount/paid_at/payment_note). **Keine** `invoice_payments`-Tabelle.
- **List/Analytics:** Dashboards leiten Beträge heuristisch ab; `open_amount` hängt an `paid_amount`.
- **Folge (R3):** Teilzahlung/echte offene Posten heute nicht abbildbar.

---

## 2. Datenmodell-Konzept (additiv)

### `invoice_payments` (neu) — S1-05
| Feld | Typ | Bemerkung |
|---|---|---|
| id | id | |
| invoice_id | unsignedBigInteger, FK invoices | |
| amount | decimal(12,2) | positiver Zahlbetrag (Zugang auf die Forderung) |
| paid_at | date | Zahlungsdatum |
| method | string(30), nullable | z. B. Überweisung/Bar/Karte |
| reference | string(100), nullable | Verwendungszweck/Beleg-Ref (auch für Idempotenz nutzbar) |
| note | text, nullable | |
| created_by | unsignedBigInteger, nullable, FK employees | |
| updated_by | unsignedBigInteger, nullable, FK employees | |
| reversed_at | timestamp, nullable | **Void-Zeitpunkt** (Korrektur ohne Löschen) |
| reversed_by | unsignedBigInteger, nullable, FK employees | |
| timestamps | | |

**Bewertung `reversed_at`/`reversed_by`: empfohlen JA.** Zahlungen werden **nicht gelöscht**, sondern **ge-void-et** (soft) — erhält Nachweis/Historie (GoBD-freundlich, Kanzlei-tauglich). Eine ge-void-ete Zahlung zählt nicht mehr in `paid_amount`.

### `invoices` erweitern
- **`payment_status`** string(16), default `open` — S1-06.
- **`is_overdue`** boolean, default false — abgeleiteter Überfälligkeits-Marker (siehe §4-Empfehlung), damit Listen/Filter effizient bleiben.

### `paid_amount` — Cache oder berechnet? **Empfehlung: Cache (denormalisiert), gepflegt ausschließlich vom PaymentService.**
Begründung: (a) `open_amount` (bestehendes Attribut) und viele Listen/Analytics/OP-Filter lesen `paid_amount` direkt — eine Live-`SUM()` je Zeile wäre für Sortierung/Filter/Export teuer; (b) die Kanzlei-OP-Liste muss sortier-/filterbar sein; (c) einziger Schreiber = PaymentService (`recalcPaymentState`), damit kein Wildwuchs. `paid_amount = Σ(nicht ge-void-eter invoice_payments.amount)`. **Guard:** außerhalb des PaymentService darf `paid_amount` nicht gesetzt werden (S1-03-Allow-Liste enthält es nur für den Service-Pfad). Ein **Reconcile-Command** prüft Drift (`paid_amount` vs. Σ payments).
- **`paid_at`:** Datum, an dem die Rechnung **vollständig** bezahlt wurde (die Zahlung, die `paid_amount ≥ total` erreicht); wird geleert, wenn durch Void wieder darunter.

---

## 3. Service-Design — `InvoicePaymentService` (S1-05)

Alle Methoden in `DB::transaction` + **`lockForUpdate()` auf die Rechnungszeile** (verhindert Race auf `paid_amount`).

- **`addPayment(Invoice $inv, Money $amount, Date $paidAt, ?method, ?reference, ?note, Employee $actor): InvoicePayment`**
  - **Guards:** Rechnung muss **ausgestellt** sein (`isIssued`), **nicht** Draft, **nicht** `is_reversed`, Status nicht `cancelled`; `amount > 0`.
  - **Overpayment-Guard:** Zahlung, die `paid_amount + amount > total_amount` ergäbe → **blockieren** (`OVERPAYMENT`, 422). Empfehlung: **blockieren** (Toleranz 0) statt „overpaid" zulassen — hält OP sauber; Skonto/Rundung/Überzahlung ist Kanzlei/späteres Thema.
  - **Idempotenz (optional):** wenn `reference` gesetzt und für dieselbe Rechnung bereits vorhanden → Duplikat abweisen.
  - danach `recalcPaymentState()`.
- **`voidPayment(InvoicePayment $p, Employee $actor): void`** — **Empfehlung: `voidPayment` (soft), nicht `delete`.** Setzt `reversed_at/reversed_by`; danach `recalcPaymentState()`. Bereits ge-void-ete Zahlung → idempotent/abgewiesen.
- **`recalcPaymentState(Invoice $inv): void`** — `paid_amount = Σ(nicht-void payments)`; `open_amount` folgt; `payment_status` + `is_overdue` + `paid_at` neu setzen (§4). Einziger Schreiber dieser Felder.

---

## 4. Payment-Status-Regeln (S1-06)

**Empfehlung: zwei orthogonale Achsen sauber trennen** — Zahlungsfortschritt **und** Überfälligkeit —, aber als **vier Anzeigezustände** ausgeben (wie gewünscht):

**Basis `payment_status` (Zahlungsfortschritt), berechnet in `recalcPaymentState`:**
| Bedingung | payment_status |
|---|---|
| `total = 0` | `paid` (nichts offen) — Sonderfall, siehe unten |
| `paid = 0` | `open` |
| `0 < paid < total` | `partially_paid` |
| `paid ≥ total` | `paid` (+ `paid_at` setzen) |

**Überfälligkeit (`is_overdue`, abgeleitet):** `is_overdue = (payment_status ∈ {open, partially_paid}) AND due_date < heute AND NOT is_reversed`.

**Anzeige-Status (die vier vom Ticket geforderten):** `paid` → „bezahlt"; sonst `is_overdue` → **„überfällig"**; sonst `partially_paid` → „teilbezahlt"; sonst „offen". → Damit erscheint eine teilbezahlte, überfällige Rechnung als **überfällig**, ohne dass die Teilzahlungsinfo (in `payment_status`/`paid_amount`) verloren geht.

**Sonderfälle / Bewertung `reversed`/`credited` als payment_status: NICHT aufnehmen.**
- Storno/Reversal ist die **Beleg-Achse** (`is_reversed`/`is_reversal` aus S1-04), **nicht** die Zahlungsachse. Ein `reversed`/`credited` im `payment_status` würde die Achsen wieder vermischen (gleiches Prinzip wie „status ≠ payment_status").
- **`is_reversed` Ursprungsrechnung:** aus dem OP herausgenommen (ihr offener Betrag ist durch den Storno neutralisiert) → `is_overdue = false`; `payment_status` bleibt informativ, wird aber im OP nicht als offen gezählt.
- **`cancelled` (Beleg-Status):** kein OP.
- **`total = 0`:** als `paid` behandeln (kein offener Posten); im UI ggf. neutral „—".

---

## 5. Zusammenspiel mit S1-04 (Storno/Gutschrift)

- **Offene Rechnung + Storno:** Sobald der Storno **gesendet** ist (`original.is_reversed = true`), gilt die Ursprungsrechnung als neutralisiert → **nicht mehr als offener Posten**. Der **Storno-/Gutschrift-Beleg selbst** trägt einen **negativen** offenen Betrag.
- **Fachliche Bewertung „Storno-Beleg als negativer OP?" → JA (empfohlen für Kanzlei-Übergabe).** Der Gutschrift-/Storno-Beleg erscheint als **eigener, negativer offener Posten**. Netting je Kunde/Ursprung (`original_invoice_id`) ist reine **Sicht** in der OP-Liste. So sieht die Kanzlei sowohl die Ursprungsforderung, ihre Aufhebung als auch — bei bereits bezahlter Rechnung — das offene **Guthaben des Kunden** (Rückzahlung/Verrechnung offen).
- **Bezahlte Rechnung + Storno:** keine automatische Zahlungskorrektur (S1-04-Warnung); der negative OP des Storno-Belegs repräsentiert das offene Guthaben, bis es manuell/über die Kanzlei ausgeglichen ist.
- **Sprint-1-Umfang:** OP-Netting wird **dargestellt/vorbereitet** (Flags + negativer OP des Reversal), aber es gibt **keine automatische Zahlungsverrechnung** zwischen Original und Storno.

---

## 6. Controller-/UI-Auswirkungen (nur ticket-Design)

- **„Zahlung erfassen"-Modal:** Betrag, Datum, Methode, Referenz, Notiz; Overpayment-Fehler via Toastr.
- **Zahlungsliste je Rechnung:** Tabelle der `invoice_payments` (inkl. ge-void-ete grau/durchgestrichen), Summe, offener Betrag.
- **„Zahlung stornieren/void":** nur erlaubt, wenn Zahlung nicht bereits ge-void-et; Bestätigungsdialog; recalc.
- **OP-Badge** je Rechnung: offen/teilbezahlt/bezahlt/überfällig (ticket-Badge-Stil), plus negativer-OP-Badge bei Storno-Belegen.
- **Analytics:** Zähler/Filter offen/teilbezahlt/bezahlt/überfällig in der Rechnungsliste.
- **Kanzlei-Übergabeliste (vorbereitet):** OP-Daten (Rechnung, Kunde, `total/paid/open`, `payment_status`, Fälligkeit, Storno-Bezug) sortier-/filterbar bereitstellen — der eigentliche Export ist **S1-08**.
- Zahlungsdaten sind der **einzige** änderbare Teil ausgestellter Rechnungen (S1-03) und laufen ausschließlich über den PaymentService. Keine playground-Optik.

---

## 7. Tests

- **Teilzahlung → `partially_paid`:** eine Zahlung < total → `paid < total`, Anzeige „teilbezahlt".
- **Vollzahlung → `paid` + `paid_at`:** Zahlungen summieren zu `total` → `paid`, `paid_at` gesetzt.
- **Überzahlung → blockiert (`OVERPAYMENT`, 422):** empfohlene Regel (kein „overpaid"-Status).
- **Zahlung auf Draft blockiert:** `addPayment` auf Draft → Fehler (nicht ausgestellt).
- **Zahlung auf reversed/cancelled:** **blockiert** (Empfehlung) — auf `is_reversed`-Original und `cancelled`-Beleg keine neuen Zahlungen.
- **Void aktualisiert Zustand:** `voidPayment` → `paid_amount`/`payment_status`/`paid_at` korrekt zurückgerechnet.
- **Overdue korrekt:** offene/teilbezahlte Rechnung mit `due_date < heute` → `is_overdue = true`/Anzeige „überfällig"; bezahlte nie überfällig; reversed nie überfällig.
- **Mehrere Zahlungen summieren korrekt:** Σ nicht-void = `paid_amount`; `open_amount` stimmt.
- **Race:** parallele `addPayment` → `lockForUpdate` verhindert falsche Summen/Overpayment-Umgehung.
- **Regression:** bestehende Rechnungs-Flows unverändert; Live-Daten unangetastet.

## 8. Risiken & Guards

| Risiko | Guard |
|---|---|
| Race auf `paid_amount` bei parallelen Zahlungen | `DB::transaction` + `lockForUpdate` auf Rechnung |
| `paid_amount`-Drift (Cache ≠ Σ payments) | einziger Schreiber = PaymentService; Reconcile-Command |
| Overpayment verunreinigt OP | Overpayment-Guard (Toleranz 0, 422) |
| Zahlung gelöscht → Historie weg | Void statt Delete (`reversed_at/by`) |
| Zahlung auf storniertem/cancelled Beleg | Guards blockieren; nur ausgestellte, nicht-reversed, positive Rechnungen |
| Achsen-Vermischung (status vs. payment_status vs. reversal) | drei getrennte Achsen: `status` (Beleg), `payment_status`+`is_overdue` (Zahlung/OP), `is_reversal`/`is_reversed` (Korrektur) |
| Overdue veraltet (kein Trigger) | recalc bei jeder Zahlung + täglicher/loginbasierter Refresh-Command; Listen können `due_date` zusätzlich live prüfen |
| Zahlung auf negativem Storno-Beleg (Refund) | in Sprint 1 out of scope; nur positive Forderungen |

## 9. Definition of Done

1. `invoice_payments` (additiv) inkl. Void-Feldern; PaymentService `addPayment`/`voidPayment`/`recalcPaymentState` transaktional + gesperrt.
2. `paid_amount` als vom Service gepflegter Cache = Σ nicht-void payments; `open_amount` konsistent; `paid_at` bei Vollzahlung.
3. `payment_status ∈ {open, partially_paid, paid}` + `is_overdue` abgeleitet; vier Anzeigezustände korrekt; getrennt von Beleg-`status` und Reversal-Achse.
4. Overpayment blockiert; Zahlungen nur auf ausgestellten, nicht-reversed, nicht-cancelled, positiven Rechnungen.
5. Storno-Zusammenspiel: reversed Original nicht als OP; Storno-Beleg als **negativer** OP dargestellt; keine automatische Zahlungsverrechnung.
6. UI: Zahlungs-Modal, Zahlungsliste (inkl. void), OP-Badges, Analytics-Filter, vorbereitete Kanzlei-OP-Daten — ticket-Design.
7. Alle Tests (§7) grün; Live-Daten unangetastet; **keine** Kontierung/Journal/DATEV.

## 10. Nicht im Scope
**Bankimport/Kontoauszug** · **DATEV/Journal/Festschreibung** · **automatische Rückzahlung** · **Mahnwesen/Mahnstufen** · **Skonto/§17-USt-Korrektur** · **Kontierung/Steuerschlüssel** · **partielle Gutschrift** (S1-04-Nachfolger) · **Refund-Zahlungen auf negative Storno-Belege** · Kanzlei-**Export** selbst (S1-08) · playground-Optik.

---
**Ein-Satz-Fazit:** Zahlungen werden als ge-void-bare Ereignisse in `invoice_payments` erfasst, `paid_amount`/`open_amount` als servicegepflegter Cache abgeleitet und der offene Posten über `payment_status` + `is_overdue` (getrennt von Beleg- und Reversal-Achse) sauber dargestellt — inklusive negativem OP für Storno-Belege als Kanzlei-taugliche OP-Basis, ohne jede Kontierung oder Buchung.
