# Umsatzdefinition — DAUERREGEL (ticket, ab 2026-07-05, verbindlich)

> Keine Zeitpunkt-Analyse, sondern eine **stehende Regel**. Sie überlebt jeden Befund. Alle Stränge (Cockpit, Controlling, FiBu, Reporting) berufen sich hierauf. Entscheidung: Yama, 2026-07-05.

## Die Regel
1. **`invoices` ist die EINZIGE Wahrheit für Umsatz.** Es gibt keine zweite Umsatzquelle. Jede Auswertung (Umsatz je Abteilung/Zeitraum/Kunde, Cockpit, Controlling) liest **ausschließlich** `invoices`.
2. **Buchungssatz je festgeschriebener Rechnung.** Die FiBu erzeugt pro **festgeschriebener** `invoices`-Zeile genau einen Buchungssatz: **Forderung/Debitor (`customer_id`) an Erlöse + USt** — aus `subtotal` (Erlös-Basis), `tax_rate`/`tax_amount` (USt), `total_amount` (Forderung), `issue_date` (Buchungsdatum), `service_from/to` (Leistungsdatum), `account_id` (Mandant).
3. **`status` = Festschreib-Gate.** Der Rechnungsstatus trägt den Übergang **Entwurf → gebucht → festgeschrieben**. Festgeschrieben = unveränderlich (GoBD: Korrektur nur per Storno/Gutschrift, nie Edit).
4. **`deal_invoices` ist stillgelegt** (Code-Rückbau 2026-07-05, Commit `b0735e3` + Provenienz `f51dd50`/`d8d3870`). Die Tabelle bleibt leer stehen; ihr **DROP ist ein ausstehender, separat zu beauftragender Tag-X-Posten** (RELEASE-MANIFEST F). **Sie ist keine Umsatzquelle** und darf nie wieder als solche verwendet werden.

## Warum (kurz)
Zwei Umsatz-Wahrheiten = derselbe Umsatz doppelt gezählt oder durch die Lücke gefallen. Eine eindeutige Quelle ist Voraussetzung für verlässliche Finanzauswertung **und** für eine GoBD-konforme FiBu (jede Buchung genau einmal, nachvollziehbar, unveränderlich).

## Bindung
- **FiBu dockt an, baut nicht um** (s. `CLAUDE.md` → Daten- und Ketten-Schutz): die Belegkette Angebot→Auftrag→Rechnung bleibt unverändert; die FiBu hängt sich **nur** an die festgeschriebene Rechnung.
- **Ticket-Daten unantastbar:** die Umstellung auf diese Definition erfolgt rein additiv (neue FiBu-Tabellen/Spalten), nie durch Änderung bestehender `invoices`-Zeilen.
- Der Phase-0-FiBu-Befund (`docs/accounting/phase-0-fibu-transplant-befund.md`) referenziert diese Datei in §1; er darf sie nicht kopieren oder abweichen.

*Belege: `docs/accounting/schritt-1-rechnungsschienen-befund.md` (invoices=11 Zeilen/205.194,48 € live, deal_invoices=0) · `docs/architektur-entscheidungen.md` (Weiche-3-Revision).*
