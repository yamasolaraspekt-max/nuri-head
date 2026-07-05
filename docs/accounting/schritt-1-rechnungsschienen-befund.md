# Schritt 1 — Rechnungs-Schienen-Befund (Accounting-Instanz, 2026-07-05, read-only)

> **Pflicht-Stopp-Befund** vor dem FiBu-Bau: ticket hat zwei Rechnungs-Schienen. Welche trägt die Live-Daten, welche wird führend, wie wird die andere stillgelegt, wie dockt die führende an die kommende FiBu? **Yama entscheidet die führende Schiene.**

## 1. Live-Datenlage (verifiziert, `ticket`-DB)
| Schiene | Zeilen | Summe | Letzter Eintrag | Nav |
|---|---|---|---|---|
| **`invoices`** (`Invoice\InvoiceController`, `/invoices`) | **11** | **205.194,48 €** (`total_amount`) | **2026-06-30** | **verlinkt** |
| `deal_invoices` (`DealInvoiceController`, `/deal/invoices`) | **0** | — | — | nur-URL |

→ **`invoices` trägt die gesamten echten Umsätze; `deal_invoices` ist leer.** (Deckt sich mit Weiche-3-Hintergrund im `architektur-entscheidungen.md`, dort noch „~204.000 € Demo".)

## 2. Struktur-Reife der Schienen
**`invoices` (führungsreif, FiBu-nah gebaut):** `account_id` (Mandant-Anker!), `customer_id`, `object_id`, **`deal_id`** (Auftragsbezug!), `offer_detail_id`/`source_offer_detail_id` (Angebot→Rechnung), `status`, `issue_date`, `due_date`, **`service_from`/`service_to`** (Leistungszeitraum — GoBD), **`subtotal`/`tax_rate`/`tax_amount`/`total_amount`** (Netto/USt/Brutto), `paid_amount`/`paid_at` (Zahlung), `deal_limit_amount`/`deal_remaining_before`/`after` (Auftrags-Budget). Umsysteme: `invoice_items`, `invoice_files`, Payment-Felder, Deal-Link+History, Material-Source, Canvas-Display, Auftrag-Sync, `invoice_no` nullable. Controller: voll-CRUD + select(customers/objects/products/deals) + dealItems.
**`deal_invoices` (tot, aber Code lebt):** 0 Zeilen; **aber** `DealInvoice`-Model + `DealInvoiceController` (index+store) + Referenzen in `DealController` und `LeadOverviewController`, Routen `/deal/invoices` + `/deal/invoices/store`.

## 3. Empfehlung (mit Beleg) — führende Schiene = **`invoices`**
1. **Datenwahrheit:** alle 11 echten Rechnungen / 205 k€ liegen in `invoices`; `deal_invoices` = 0.
2. **Struktur:** `invoices` hat bereits Netto/USt/Brutto, Leistungszeitraum, `account_id` (Mandant), `deal_id` (Auftragsbezug) — **direkt buchungssatz-fähig**. `deal_invoices` müsste dafür erst ausgebaut werden.
3. **Anbindung:** nav-verlinkt, aktiv weiterentwickelt (Auftrag-Sync/Deal-Link 2026-06). `deal_invoices` ist eine nie gelebte Parallel-Wahrheit.

## 4. Stilllegung `deal_invoices` — **kein Daten-, nur Code-Rückbau**
- **Datenmigration: entfällt** (0 Zeilen).
- **Code-Rückbau-Paket (nach Yama-Freigabe, eigener Bau-Schritt):** Route `/deal/invoices` + `/deal/invoices/store` entfernen/auf `invoices` redirecten · `DealInvoiceController` stilllegen · Referenzen in `DealController` + `LeadOverviewController` prüfen und auf `invoices` umlenken (damit keine neue `deal_invoices`-Zeile mehr entstehen kann) · Tabelle nach bewiesener Schreiber-Freiheit als deprecated markieren (Drop erst nach Beobachtung). **Latente Zweit-Wahrheit = Risiko für die FiBu-Umsatzdefinition** → vor dem ersten Buchungslauf schließen.

## 5. FiBu-Andockung (Ausgangsrechnung → Buchungssatz)
`invoices` dockt sauber: je festgeschriebener Rechnung ein Buchungssatz **Forderung/Debitor (`customer_id`) an Erlöse + USt** — Felder vorhanden: `account_id`(Mandant), `subtotal`(Erlös-Basis), `tax_rate`/`tax_amount`(USt), `total_amount`(Forderung), `issue_date`(Buchungsdatum), `service_from/to`(Leistungsdatum). `status` kann das **Festschreib-Gate** (Entwurf→gebucht→festgeschrieben) tragen. **Weiche für die Bau-Phase:** Buchung synchron beim Festschreiben vs. asynchroner Buchungslauf — im FiBu-Transplantations-Befund (Phase 0) zu entscheiden.

## 6. Selbstkritik / offene Punkte
- **NICHT-VERIFIZIERT:** ob der `deal_invoices`-`store`-Pfad aktuell erreichbar/verlinkt ist (nur-URL-Route) — der Rückbau muss das prüfen, nicht annehmen.
- Die 11 `invoices` sind teils Demo/Test — vor dem ersten echten Buchungslauf ist ein **Datenqualitäts-Check** (USt-Sätze, Nummern, Leistungsdaten) Teil der Phase-0-Gap-Liste.
- Storno-Folgeregel (Weiche 4) bleibt offen und gehört in die FiBu-Buchungslogik (Storno statt Edit — GoBD).

---

**→ PFLICHT-STOPP.** Empfehlung belegt: **`invoices` = führende Schiene**, `deal_invoices` stilllegen (kein Daten-, nur Code-Rückbau), FiBu-Andockung über `invoices` sauber möglich. **Yama entscheidet die führende Schiene**, danach: Phase-0-FiBu-Transplantations-Befund (läuft parallel read-only) + der `deal_invoices`-Code-Rückbau als eigener Bau-Schritt (nach Cut-over / nach Freigabe).
