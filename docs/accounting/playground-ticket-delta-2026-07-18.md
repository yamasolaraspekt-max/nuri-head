# Buchhaltung/DATEV — Delta playground ↔ ticket (2026-07-18, read-only Vergleich)

Grundlage: `phase-0-fibu-transplant-befund.md` (Stufenplan i–vii, additiv, gated).
Prinzip: ticket-Schema bleibt Wahrheit; playground dockt additiv an; keine Duplikate;
Live-DB erst nach Testpaket + Yama-Abnahme. Dieser Vergleich = Zustand, keine Umsetzung.

## Umfang der Quellen
- **playground/backend-laravel** (separate Laravel-App): 45 Accounting-Controller, 12 Lohn/Payroll-Controller,
  289 Models, 30 Accounting-Migrationen (von 165), 263 Tests. = komplettes FiBu/DATEV/Lohn-ERP (Prototyp, Mai–Juni).
- **ticket** (live): 5 Accounting-Services, 4 Accounting-Migrationen (Juli, neuer), Belegkette (`invoices`).

## SCHON in ticket (nicht erneut holen — Duplikatgefahr)
- Services: `BuchungsEngine`, `BelegflussService`, `EingangsBelegflussService`, `AuswertungsService`, `DatevExtfExportService`.
- Migrationen: `accounting_foundation_tables`, `accounting_documents_table`, `accounting_journal_tables`, `ticket_dunning_tables`.
- Belegkette als Wahrheit: `invoices` (+ `invoice_items`), `umsatzdefinition.md`.
- Docs: `phase-0-fibu-transplant-befund`, `schritt-1-rechnungsschienen-befund`, `umsatzdefinition`, `welle-a1-inventur-konzept`.
→ Entspricht Stufen (i) Schema-Anker, (iii) Belegfluss-Anker, Teile (iv) Buchungs-Engine, (vi) DATEV-Export-Grundgerüst.

## NUR in playground (Delta-Kandidaten, je eigene gated Stufe — NICHT bulk)
| Bereich | playground-Pfad (Controller/Domäne) | Stufe lt. Plan | Anmerkung |
|---|---|---|---|
| Kontenrahmen/SKR | `ChartOfAccounts`, `Account`, `AccountMapping`, `NumberRange` | (ii) | SKR-Wahl = Steuerberater; technisch SKR-flexibel |
| Debitor/Kreditor | `Debtor`, `Creditor`, `OpenItem`, `OpAbgleich` | (ii)/später | Kreditoren = eigener Strang |
| Steuer/USt | `TaxCode`, `TaxConfig`, `Ustava` (UStVA) | (ii)/(v) | Steuerschlüssel→USt-Konto |
| Buchungs-/GoBD-Gates | `Journal`, `Storno`, `Gobd`, `PostingPeriod`, `MonthlyClosing` | (iv) | Maker-Checker, append-only, Storno |
| Auswertungen | `Bwa`, `SuSa`, `BalanceSheet`, `Afa`, `Kostenstellenrechnung`, `CostCenter` | (v) | gegen Referenz-Geschäftsjahr |
| Bank/Kasse | `BankAccount`, `BankImport`, `CashRegister`, `PaymentRun`, payment_plans | (v)/später | Zahlläufe |
| XRechnung | `XRechnung*`, `XRechnungStammdaten`, XRechnung-Validator | (vi)-nah | E-Rechnung |
| DATEV-Konformität | `testpaket_crm_erp_datev/` + Konformitäts-Prüfer | (vi) Gate | Startzustand lt. Audit: ROT |
| FiBu-Vorschläge | `Material-FiBu`, `Personal-FiBu`, `PostingSuggestion`, `AdjustmentSuggestion` | (vii)-nah | material/personal_fibu_suggestions |
| Lohn/Payroll | `HrPayroll*`(8), `Lohnvorbereitung`, `ProjektLohnkosten`, `Lohnarten`, lohn_* Migr. | eigener Strang | großes Teilsystem |
| Gate/Prüfung | `GateReadiness`, `GateRelease`, `Pruefzentrum`, `Fristen`, check_cases | Quer-Gate | GoBD-/Freigabe-Protokoll |
| Steuerberater | `TaxAdvisor`, `Steuerberater`, `Rechnungsweg`, `Projektrechnung` | später | Export/Weg |

## Sicher SOFORT holbar (kein Code-/DB-Risiko, additiv)
- **DATEV-Testpaket** `Playground/testpaket_crm_erp_datev/` (Testfälle + Verfahrensdokumentation) → ticket,
  weil `phase-0` es als Grün-Gate für den DATEV-Export braucht.
- Referenz-/Konzept-Docs aus `Playground/docs/*` (analyse/konzepte/pruefberichte), soweit nicht schon in ticket.

## NICHT einfach kopieren (Risiko/Regel)
30 Migrationen + 289 Models + 45 Controller der separaten App: würden mit ticket-Auth (is_admin/user_rolls
statt permission:*), ticket-Kette (`invoices`) und Live-DB kollidieren → **stufenweise adaptieren**, nicht dumpen.
Auth: Adapter playground-`permission:*` → ticket-RBAC. DB: nur additive Migrationen gegen `ticket_testing`.
