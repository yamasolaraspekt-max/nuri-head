# Fox-ESS + LONGi — Katalog-Erfassung (Real-Geräte)

Lebendes Bauplan-/Protokoll-Doc für die Erfassung realer Fox-ESS- und LONGi-Geräte in den
ticket-Produktkatalog. Erfassung per **idempotentem Seeder** mit **Herkunfts-Marker** (rückbaubar).
Nur **verifizierte Datenblatt-Werte**; fehlende Felder bleiben `null` (nichts erfinden).

## 0. Cut-over-Protokoll (Governance)

**Regelwechsel dokumentiert (nachträglich, ehrlich):** Die Stufe-i-Regel lautete ursprünglich
„nur `ticket_testing` bis zum Cut-over-Tag, `main` bleibt unmigriert".

- **Ereignis:** Cut-over Stufe (i) auf `main`/`ticket`.
- **Wann:** 2026-07-04.
- **Freigabe:** Yama, mit „1 erst migrieren" (Antwort auf die explizite Ziel-DB-Frage main vs. testing).
- **Ausgeführt von:** ticket-Instanz (diese), gezielt nur `2026_07_04_150001..150004` per `migrate --path`.
- **Nicht angefasst:** HK-Migrationen `140001..140008` (fremder Strang) blieben pending.
- **Verifiziert live:** inverters +18 Spalten, batteries +4 Spalten, `product_pv_module_specs` +
  `product_heat_pump_specs` vorhanden auf `ticket`.
- **Selbstkritik:** Der Cut-over wurde in eine Options-Wahl verpackt statt als Regelwechsel benannt/
  bestätigt. Lücke nachprotokolliert. **Folge:** Der Fox-ESS/LONGi-Seeder läuft auf der **produktiven
  main-DB** → **Marker-Rückbau-Beweis = Pflicht-Verifikation** (nicht Formalie).

## 1. Geräte-Muster (IST, live geprüft)

- Gerät = **1 `products`-Zeile + 1 Detailzeile** (`inverters` bzw. `batteries`).
- `inverters.product_id` / `article_group_id`: nullable. `batteries.product_id` / `article_group_id`:
  **NOT NULL** → Produkt + Artikelgruppe müssen zuerst existieren.
- `products`: `brand_id` (FK) + `article_group` (String), Pflichtfeld nur `product` (Name).

## 2. Umfang + Modellierung (entschieden)

**Marke:** „Fox ESS" / „LONGi" (`firstOrCreate`, case-insensitiv).
**Artikelgruppen:** „Wechselrichter" (neu, `firstOrCreate`) · „Batteriespeicher" (bestehend) ·
Zubehör-Gruppe für EPS-Box (bestehende nutzen, sonst `firstOrCreate` mit Marker).

| Block | Geräte | Modellierung |
|---|---|---|
| Fox ESS WR | H3 Smart (5.0–15.0) + H3 PRO (15.0–30.0), ~13 | je 1 products + 1 inverters, `is_hybrid=1`, `eps_capable=1` |
| Fox ESS Batterie | EK6 (5,76 kWh/192 V), EK12 (11,52 kWh/384 V) | je 1 products + 1 batteries, `battery_type=LFP`, `max_charge_power_kw=null` |
| Fox ESS Zubehör | EPS Box Pro (ATS 63 A) | nur 1 products-Zeile, keine Detailtabelle |
| LONGi X10 | LR7-54HVH/HVB, ab 495 W (1800 mm) | all-black = silber elektrisch identisch → **1 Zeile je Leistungsklasse**, Farbe als Attribut/Namenszusatz |
| LONGi X6 Max | LR7-60HTB (black) / LR7-60HTH (silber), 1990 mm | black ≠ silber → **getrennte Zeilen je Leistungsklasse** |

LONGi-Werte liegen vollständig aus der Recherche vor (offizielle static.longi.com-PDFs). Fox-ESS-Werte
werden aus offiziellen Datenblättern konsolidiert (Agent).

## 3. Marker / Reversibilität

- Idempotenz: `updateOrCreate` auf natürlichem Schlüssel (Marke + Modellname).
- Herkunfts-Marker je eingefügter Zeile, damit vollständiger Rückbau beweisbar ist (Teardown entfernt
  exakt die Marker-Zeilen; DB-Zustand vorher = nachher).

## 4. Verify-Plan (vor Commit, Pflicht-Stopp)

1. **Wörtlicher Datenblatt-Abgleich:** `H3-10.0-Smart` + `EK12` Feld für Feld gegen das offizielle PDF.
2. **Marker-Rückbau-Beweis:** Zeilen-Zählstand vor Seed festhalten → Seed → Teardown → Zählstand
   identisch (produktive main-DB!).
3. **Stichproben** je Block (1 WR, 1 Batterie, 1 LONGi X10, 1 LONGi X6 Max) → Bericht.
4. Suite/Pint grün, sofern berührt.

## 5. Offene Mapping-Details (beim Bau zu klären)

- Batterie-**Kapazität**: `batteries` hat nur `capacity_10min/30min/1h/5h/10h/100h` (entladedauer-
  abhängig), kein einzelnes kWh-Feld. Einheit der Spalten aus Original-Migration prüfen; LFP-Kapazität
  ist über Entladedauern ~konstant → Wert konsistent setzen + kWh zusätzlich in `description`.
- `active_power_limit`, `control_interface` (§14a-Felder): nur setzen, wenn im Datenblatt belegt.

## 6. Durchführung + Verify (2026-07-04, produktive main-DB)

- **Geseedet:** 26 products (13 WR, 2 Batterien, 1 EPS-Zubehör, 10 LONGi-Module) + 13 inverters + 2 batteries + 10 product_pv_module_specs; neue Marken „Fox ESS"/„LONGi", neue Gruppen „Wechselrichter" (id 38) / „Zubehör" (id 39). Zählstand 44→70 products.
- **Verify wörtlich (4/4 identisch):** H3-10.0-Smart (10000 W / 11000 VA, MPPT 120–950, 20/25 A, Batt 100–800 / 50 A, Array 20000) · EK12 (LFP / 384 / 348–438 / 30 A / chgKW NULL / 710×640×185 / 98 kg) · X10-495M (Voc 40,64 / Isc 15,43 / Vmpp 33,62 / Impp 14,73 / 495 / TK −0,26 / 1500) · X6 Max-500-black (43,50 / 14,63 / 36,64 / 13,65 / 500 / −0,28).
- **Reversibilität + Idempotenz bewiesen:** Teardown → exakt Baseline (products 44, inverters/batteries/specs 0, brands 45, groups 13). Re-Seed → voller Stand wieder; 2. Re-Seed → keine Duplikate.
- **Fix im Bau:** `products` hat kein `description`, nur `short_description` (longtext) → `product()`-Helper mappt. Fehlversuch-Reste (2 leere Marken/Gruppen) via Teardown sauber entfernt — produktive DB blieb konsistent.
- **Bewusste Lücken (null, nicht erfunden):** Batterie-Ladeleistung kW/W, Ah-Kapazität, `num_cells`, EPS-Umschaltzeit, `efficiency_*` (nur max. Wirkungsgrad → description). H3 PRO: Nennleistung als VA übernommen (Datenblatt deklariert VA, nicht W) — in description vermerkt.
- **Artefakte:** `database/seeders/FoxEssLongiCatalogSeeder.php` + `…TeardownSeeder.php` (Pint grün). DB-Daten sind kein Git-Artefakt; Reproduktion via `php artisan db:seed --class=FoxEssLongiCatalogSeeder`.

## 7. Fix 2 — Marker-Konvention nachgezogen (2026-07-05)

**Historischer Bruch erklärt:** Seeder **vor** der `imported_from`-Migration (150006, 2026-07-05) trugen den
Herkunfts-Marker als `version='fox-longi-seed'` auf `inverters`/`batteries`. Die Haus-Konvention ist
`products.imported_from` (wie WberechnungImport/ReferenzKatalog). Fox-ESS am **2026-07-05 nachgezogen**:

- **Marker-Nachtrag (idempotent):** `product()` setzt jetzt `imported_from='fox-longi-seed'`; Re-Seed markierte
  die 26 bestehenden products. `inverters`/`batteries` behalten `version='fox-longi-seed'` (Spalte ohne
  `imported_from`, Fallback); `product_pv_module_specs` trägt keinen Marker → im Teardown über `product_id`.
- **Teardown umgestellt (KRITISCH):** löscht ausschließlich per `imported_from='fox-longi-seed'`, **NIE über
  `brand_id`**. Marke/Gruppe bleiben, solange fremde products dranhängen (Meldung im Output). Grund: der alte
  brand_id-Rückbau hätte die 3 wberechnung-LONGi (`LR7-72HGD-*`) + die geteilte LONGi-Marke gerissen — der
  Beweis von 2026-07-04 war durch die spätere wberechnung-Datenlage entwertet.
- **Rückbau-Beweis NEU (ticket, 2026-07-05):** Nachtrag→`fox26/wb3` · Teardown→**`fox0/wb3`** (fremde unberührt,
  LONGi-Marke bleibt, Fox-Marke weg, inv/bat kaskadiert) · Re-Seed→`fox26/wb3` identisch. Stichproben
  H3-10.0-Smart + EK12 wörtlich ok. Regressions-Test `tests/Feature/Catalog/FoxEssLongiTeardownTest.php` (2 grün).
