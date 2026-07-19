# Stufe (ii) — Planner-Spec: Kontenrahmen-Seed + Debitor-/USt-Mapping

Rolle: **Planner** (kein Produktionscode). Heimat-App: **ticket**. Bau/Test **nur gegen `ticket_testing`**.
Grundlage: `phase-0-fibu-transplant-befund.md` §8 (ii) + §6 (1–3). Governance: Rollentrennung — Generator/Evaluator getrennt.

## Ausgangslage (verifiziert, read-only)
Stufe (i) (`2026_07_05_180001_create_accounting_foundation_tables.php`) hat bereits angelegt:
`chart_of_accounts`, `accounting_clients` (default_chart_of_account_id, tax_number),
`accounts` (account_number/name/type/category, chart_of_account_id, accounting_client_id nullable),
`tax_codes` (input_tax_account_id, output_tax_account_id), `account_mappings`
(chart_of_account_id, account_id, tax_code_id). Alle mit `imported_from`-Marker. **Die Tabellen sind LEER.**
→ Stufe (ii) FÜLLT sie idempotent. **Keine neue Tabelle, kein Schema-Umbau.**

## Ziel & Entscheidung (eine Festlegung, keine Alternativen)
1. **Kontenrahmen = SKR03 als Default** (Handwerk/Elektro/Solar). Technisch SKR-flexibel über
   `chart_of_accounts`; der Seeder ist parameterisierbar (SKR03 jetzt, SKR04 später ohne Umbau).
   Marker `imported_from='seed:skr03'`. **Fiskalische SKR-Bestätigung = Steuerberater-Gate VOR Live**,
   nicht vor `ticket_testing` (Bau läuft mit SKR03-Default weiter).
2. **USt-/Steuerschlüssel (DE):** `tax_codes` für **19 %**, **7 %**, **0 %/steuerfrei**, **§13b
   Reverse-Charge (Bauleistung — für Solar/Elektro zentral)**, **innergemeinschaftlich** — je mit
   `output_tax_account_id`/`input_tax_account_id` auf die SKR03-USt/VSt-Konten. Zuordnung `tax_rate → tax_code`.
3. **Debitoren-Mapping = Sammeldebitor** (ein Forderungskonto SKR03 **1400**) für Stufe (ii);
   `customer → account` über `account_mappings` (`mapping_type='debitor'`). **Einzeldebitoren = spätere
   additive Stufe.** Kreditoren analog später (nicht hier).

## Nahtstellen (wo — und wo bewusst NICHT)
- Neue **Seeder** unter `database/seeders/Accounting/…`, idempotent via `updateOrCreate` auf
  natürlichen Schlüssel (`account_number` + `chart_of_account_id`).
- Ein **Default-Mandant** in `accounting_clients` (Solar Aspekt), `default_chart_of_account_id → SKR03`.
- SKR-Stammkonten **rahmenweit** (`accounting_client_id = null`); `tax_codes` **mandantengebunden**.
- **KEIN** Eingriff in `invoices` / `deals` / `customers` — die Kette bleibt Wahrheit und unberührt.
  Das Debitor-Mapping **liest** `customers`, **schreibt nur** in `account_mappings`.
- Erweiterungspunkt (nur kapseln, nicht bauen): SKR04-Seed + Einzeldebitoren später ohne Schema-Umbau.
- Auth-Adapter (`permission:*` → ticket-RBAC) **nicht hier** — eigener Strang.

## Kantenliste
- Seeder **zweimal** laufen → keine Duplikate (idempotent, natural key). *(Kern-Gegenbeweis)*
- **SKR-Wechsel:** zweiter `chart_of_accounts`-Eintrag; Seeds über `imported_from`-Marker getrennt.
- **§13b Reverse-Charge:** eigener `tax_code` (0 % ausgewiesen, Steuerschuldumkehr) — **nicht** mit
  „0 % steuerfrei" verwechseln.
- **Satz-Historie** (7 %/19 % Zeitraum-Wechsel): Stufe (ii) = aktuelle Sätze; Historisierung späterer Punkt (notiert).
- **Sammeldebitor:** alle Kunden auf EIN Konto → OP-Abgleich je Kunde erst mit Einzeldebitoren (bewusst später).
- **Rahmenweit vs. mandantengebunden:** Stammkonten `client_id null`, `tax_codes` client-gebunden — FK-Konsistenz prüfen.
- **Teardown:** marker-basiertes Löschen (`imported_from` LIKE 'seed:%') → betroffene Tabellen wieder 0.

## Abnahmekriterien (Evaluator misst SELBST, gegen `ticket_testing`)
1. Migration + `db:seed` (SKR03 + tax_codes + Debitor-Mapping) → **grün**, keine Fehler.
2. `chart_of_accounts`: 1 SKR03; `accounts`: Standardkonten vorhanden (repräsentativ: Forderungen **1400**,
   Erlöse 19 % **8400**, Erlöse 7 % **8300**, USt 19 % **1776**, USt 7 % **1771**, VSt 19 % **1576**),
   je mit `imported_from='seed:skr03'`.
3. **Idempotenz (Gegen-Beweis):** Seeder zweimal → identische Zeilenzahl, kein Duplikat.
4. `tax_codes`: 19 %/7 %/0 %/§13b vorhanden; `output_tax_account_id`/`input_tax_account_id` gesetzt und
   auf existierende `accounts` verweisend (FK gültig).
5. **Debitor-Mapping:** ein Sammeldebitor-`account_mapping` (`mapping_type='debitor'` → 1400) vorhanden;
   ein Test-Kunde löst auf dieses Konto auf.
6. **Kette unberührt (Gegen-Beweis gegen stille Änderung):** `invoices`/`deals`/`customers` Zeilenzahl **und**
   Schema vor/nach Seed identisch.
7. **Teardown:** Seed-Rollback → betroffene Tabellen 0, `ticket_testing` sauber.

## Pflicht-Stopp
Planner-Ende — **kein Produktionscode gebaut**. Ballbesitz: Übergabe an **Generator** (nach Yamas OK dieser
Spec): Seeder + ggf. minimale additive Ergänzungen **nur gegen `ticket_testing`**. Danach **Evaluator**
(unabhängig, `references/pruefrahmen.md`, Gegen-Beweis je Kriterium). **Offen an Yama:** SKR03-Default ok?
SKR-Wahl final = Steuerberater vor Live.
