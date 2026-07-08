# Backlog — Strang `accounting` (FiBu)

> Freigegebene Posten in Reihenfolge (Betriebsordnung 3.4). Quelle: Phase-0-Befund `docs/accounting/phase-0-fibu-transplant-befund.md` §8. Schreiben: Yama (immer) · Bauer (nur zwingende Folge-Posten mit Entscheidungs-Beleg). Prüfer nie.
> Geltende Entscheidungen: `docs/accounting/umsatzdefinition.md` · `docs/entscheidungen.md` · Betriebsordnung 2.3 (FiBu-Sondergates).

## Posten

### (i) FiBu-Schema-Fundament — additiv, SKR-neutral, KEIN Seed  ·  **✅ FREIGEGEBEN + IN MAIN (2026-07-08)**
> Prüfer-Protokoll (frische Instanz): geprüfter Hash `28b074b` → cherry-pick nach main `f371ee6`. G1 Suite: 121 Unit grün; die 3 Suite-Fehler (Invoice/Pusher `cURL localhost:6001`) sind **vorbestehend auf main** (Broadcasting-Env, keine Regression). G3 Additiv: nur `Schema::create` (9 Tabellen), 0 destruktive Muster. G8 FiBu: SKR-neutral, kein Seed, `mapping_key` (keine harten Kontonummern), Anker `source_invoice_id`→`invoices` read-only. **Votum: FREIGABE.** Lokal migriert (9 Tabellen erzeugt). Tag-X-Prod-Migration bleibt offener Manifest-Posten. **Nächster Posten: (ii) Kontenrahmen-Seed — Gate Steuerberater (SKR03/04).**
- **Scope:** FiBu-eigene Tabellen (`chart_of_accounts`, `accounting_clients`, `accounts`, `tax_codes`, `account_mappings`, `accounting_fiscal_years`, `accounting_documents`, `accounting_journal_entries`, `accounting_journal_lines`), Timestamps `2026_07_05_180001–180003`, `imported_from`-Marker auf seedbaren Referenztabellen. Nur lokal/Test.
- **Auflagen:** additiv (nur CREATE); `down()` rollback-bewiesen; Weiche 5 (kein `project_id`); `mapping_key`-Architektur (keine harten Kontonummern); Ketten-Anker `source_invoice_id`→`invoices` read-only; kein Kontenrahmen-Seed (eigener Posten).
- **Abnahme-Anker:** `migrate` (isoliert, `--path`) grün, alle 9 Tabellen erzeugt; `migrate:rollback` entfernt sie rückstandslos (Rückbau-Beweis); Suite ≥ Vorgänger; Manifest-Zeile (Tag-X-Prod-Lauf); Bilanz/STRAENGE fortgeschrieben.
- **Tag-X-Anteil:** Prod-Migration der 180001–180003 (Skript+Rückbau+Manifest bereit; ausführen Yama/Ramin).

### (ii) Kontenrahmen-Seed (SKR03 + SKR04) + Debitoren-/USt-Mapping
- **Scope:** vollständige `chart_of_accounts`-Seeds SKR03 **und** SKR04, `accounts`, `tax_codes`, `account_mappings` (semantische Keys), Default Solar Aspekt = SKR03. `imported_from`, idempotent, marker-basierter Teardown.
- **Auflagen:** startet erst nach externer SKR-Klärung-Bestätigung (Yama); Rahmen-Neutralität vorbereitet.
- **Abnahme-Anker:** Seed idempotent, Teardown 0; beide Rahmen vollständig.

### (iii) Belegfluss-Anker — festgeschriebene `invoices` → Kopf-Buchungssatz
- **Scope:** Ableitung Buchungssatz (Forderung/Debitor an Erlöse+USt) aus festgeschriebener Rechnung; `accounting_documents` + `journal_entries/lines` befüllen; Auflösung über `mapping_key`.
- **Auflagen:** Kopf-Buchung (kein Positions-Split); 11 Bestandsrechnungen NICHT nachbefüllt.
- **Abnahme-Anker:** handgerechneter Referenz-Fall = erzeugter Buchungssatz (zifferngenau); Rahmen-Neutralitäts-Test SKR03/SKR04.

### (iv) Buchungs-Engine + GoBD-Gates
- **Scope:** append-only, Storno statt Edit, lückenloser Nummernkreis, Maker-Checker, Festschreibung.
- **Auflagen (2.3):** **kein produktiver Buchungssatz vor grünem GoBD-Beweis-Testsatz.**
- **Abnahme-Anker:** 4 GoBD-Beweis-Tests grün (Unveränderlichkeit/Nummernkreis/Maker-Checker/Audit-Trail).

### (v) Auswertungen (SuSa/BWA/UStVA)
- **Abnahme-Anker:** gegen Referenz-Geschäftsjahr mit bekannten Soll-Werten (Zahlen-Wahrheit).

### (vi) DATEV-EXTF-Export
- **Auflagen (2.3):** **kein DATEV-Export vor grünem DATEV-Testpaket** + playground-Konformitäts-Prüfer grün. Versand = Yama (2.2-2).
- **Abnahme-Anker:** Testpaket grün, Konformitäts-Prüfer grün.

### (vii) Positions-Erlöskonten-Split
- **Scope:** `invoice_items`→Erlöskonten je Artikel/Kategorie (Übertragungs-Gap wird geschlossen).
- **Abnahme-Anker:** Summe Positions-Split = Kopf-Betrag.

### (viii) Kreditoren-/Bestelllisten-Seite (Wareneingang)
- **Scope:** Eingangsrechnungen/Kreditoren; Feinkartierung `DealMaterialList*`. Eigener Block, später.
