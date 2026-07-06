# Entscheidungen / Weichen (strang-übergreifend)

> Betriebsordnung 1.4 + G7: jede neue Weiche VOR dem Bau — Datum · Alternativen · Begründung. Additiv gepflegt, je Strang ein Abschnitt. Änderung einer getroffenen Weiche = Eskalation an Yama.

## Strang `accounting` (FiBu)

### Stufe (i) — Schema-Fundament (2026-07-05)

- **W-ACC-1 · Weiche 5 in `accounting_documents` (kein `project_id`).** Alternativen: (a) playgrounds `project_id` übernehmen; (b) weglassen. **Gewählt: (b).** Begründung: Weiche 5 (Kunde→Objekt→Gewerk, kein „Projekt"). Auftragsbezug über `deal_id` (lose).
- **W-ACC-2 · Ketten-Referenzen lose (kein FK) für `customer_id`/`deal_id`.** Alternativen: (a) harte FK auf `customers`/`deals`; (b) nullable Spalte ohne FK. **Gewählt: (b).** Begründung: die FiBu nicht an den CRM-Lebenszyklus koppeln (Soft-Delete/Umbenennung dort darf FiBu nicht brechen); Integrität über den einen definierten Anker (W-ACC-3).
- **W-ACC-3 · `source_invoice_id` als einziger harter Ketten-Anker (FK → `invoices`, `nullOnDelete`).** Alternativen: (a) kein FK; (b) FK. **Gewählt: (b).** Begründung: Umsatzdefinition — die FiBu dockt an die festgeschriebene Rechnung; additive FK auf FiBu-eigener Tabelle berührt `invoices` nicht.
- **W-ACC-4 · `mapping_key`-Architektur (`account_mappings`), keine harten Kontonummern.** Vorgabe Yama (SKR-Entscheidung 2026-07-05). Begründung: Rahmen-Neutralität (SKR03/04) — Buchungs-Engine löst nur über semantische Keys auf.
- **W-ACC-5 · `booking_stack_id` ohne FK (Buchungsstapel = spätere Stufe).** Alternativen: (a) `booking_stacks` jetzt bauen; (b) nullable Spalte ohne FK. **Gewählt: (b).** Begründung: Scope-Disziplin Stufe (i) = Fundament; Stapel-Logik gehört zur Buchungs-Engine (Stufe iv).
- **W-ACC-6 · `accounting_fiscal_years` minimal neu entworfen.** Alternativen: (a) playground-Tabelle 1:1 portieren; (b) minimale GoBD-Periode (client/label/start/end/is_closed). **Gewählt: (b).** Begründung: playgrounds `accounting_fiscal_years` war im Scan nicht auslesbar; das Fundament braucht nur die Perioden-Klammer — Felderweiterung additiv bei Bedarf. **Folge-Posten möglich** falls spätere Stufen mehr Felder brauchen.
- **W-ACC-7 · Stufe (i) SKR-neutral, KEIN Kontenrahmen-Seed.** Vorgabe Yama. Seed (SKR03+SKR04) = eigener Posten (ii) nach externer Klärung.
- **W-ACC-8 · Rahmen-Wechsel laufender Mandanten NICHT im Scope.** Vorgabe Betriebsordnung 2.3 — nur GJ-Wechsel/Stichtag (GoBD). Dokumentierte Nicht-Übernahme.

## Strang `heizlast` (Klima-Daten)

### Stufe (i) — Datenquelle `klima_plz` (2026-07-06)

- **KLIMA-PLZ-DB-SOURCE · Interne Datenquelle: DB statt Laufzeit-CSV.** Alternativen: (a) Laufzeit-CSV-Lookup (`database/data/klima_plz.csv`); (b) DB-Query gegen `klima_plz`. **Gewählt: (b).** Begründung: Produktionsreife Referenzdaten gehören in die DB (Indexierung, Seed-Idempotenz, einfachere Wartung). Diff-Status: **Diff=0 (Muster C1)** — öffentliche Service-API unverändert (`findByPlz()`, `getNormAussentempForPlz()`), keine Aufrufer-Änderung, keine Schema-Änderung. CSV bleibt erhalten als Referenz/Seed-Grundlage; Seeder weiterhin idempotent. Nachweis: Fokus-Tests und Identitäts-Tests laufen unverändert grün; Commit/Hash: `2fc1c08` (2026-07-06, atomar: Docblock + Entscheidung + Manifest-Zeile).

*Folge-Posten:* Dokumentation im Service-Docblock (`app/Services/Klima/KlimaPlzService.php`) hinzugefügt. Wenn notwendig: Performance-Monitoring der Query-Last und optionaler Index auf `klima_plz.plz`.
