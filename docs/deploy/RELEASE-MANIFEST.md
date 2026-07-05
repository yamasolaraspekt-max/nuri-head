# RELEASE-MANIFEST — Tag-X-Deploy-Liste

> **Lokal-First (ab 2026-07-05, bis Tag X).** Produktion ist für alle Instanzen **tabu**. Diese Datei
> sammelt **fortlaufend ALLE produktionspflichtigen Posten** seit dem letzten Produktions-Stand.
> **Pflicht:** Jeder Commit mit prod-pflichtigem Teil (Migration · Seeder-Lauf · `.env`/Flag · Frontend-Fix
> mit Nutzerwirkung · Härtungs-Schalter · Config) trägt **im selben Commit** seine Zeile hier nach.
> **Posten ohne Zeile = Governance-Verstoß (gleichrangig Tabu-Bruch).**
>
> **Katalog-Seeder-Regel (ab 2026-07-05, aus dem Fox-ESS-Fall):** Teardown IMMER **marker-basiert**
> (`imported_from`, ersatzweise `version`), **NIE über `brand_id`/Gruppen-Zugehörigkeit** — geteilte
> Stammdaten (Marken, Gruppen) sind per Definition **mehrbesitzt**. Marke/Gruppe nur entfernen, wenn danach
> keine products mehr dranhängen (sonst stehen lassen + im Output melden).
>
> **Baseline (letzter Produktions-Stand):** von Yama zu bestätigen. **Reihenfolge:** Migrations-Timestamp.
> Am Tag X wird diese Liste zum Deploy-Runbook ausgearbeitet (Befehle, Erwartungs-Outputs, Rollbacks) und
> **einmal** mit Ramin auf dem Server ausgeführt.
>
> **Regel-Ersetzung (2026-07-05 — ersetzt „nur `ticket_testing` bis Cut-over", stirbt nicht stillschweigend):**
> Die **lokale `ticket`-DB (Dev)** trägt ab **2026-07-05** die Cut-over-/Framework-Migrationen **150xxx + 170xxx**
> (rein additive `CREATE`/`ALTER`, `down()`/Teardown rollback-bewiesen). **Hetzner-Produktion bleibt unberührt bis Release**
> (diese Liste ist das Deploy-Runbook). Grund: ein halb-migrierter Zustand (150xxx auf Dev, 170xxx nur testing) ist schlechter
> als ein konsistentes Dev-Schema — Integrations-Tests (Fox-ESS-Seeder + Anforderungsprofile) brauchen EIN Schema.
>
> **Migrations-Record lokale `ticket`-DB (Trail):**
> - **Batch 14** — `150001–150004` (Katalog-i, Spec-Schema WR/Bat/PV/WP) · `217473f` · **2026-07-04** · Katalog-Cut-over-Instanz.
> - **Batch 15** — `150005–150006` (kurve_semantik + `imported_from`) · **2026-07-05** · Katalog-Cut-over-Instanz.
> - **Batch 16** — `170001–170006` (B2a Referenz-Kataloge + Anforderungsprofil) · **2026-07-05** · diese Instanz (Tabellen leer: Schema; Seeder separat).
> - **Batch 17** — `170007` (Geometrie-Spalte `gebaeude_geometrie`, B2a-3 Heizlast-Adapter) · **2026-07-05** · diese Instanz.
> - **Spec `150007–150009`** bleiben bewusst **Pending** (Strang B, M5.1 nach Abnahme — NICHT selektiv mitmigriert).
> - **Governance-Klärung** (Fox-ESS-Frage „wer/wann 150xxx auf main?"): beantwortet = Batch 14/15. Eine *explizite* Yama-Freigabe
>   für den Dev-Lauf ist im Trail nicht gesondert vermerkt → durch diese Regel-Ersetzung **rückwirkend legitimiert** (Dev, additiv, Prod unberührt).

## A) Migrationen (Timestamp-Reihenfolge)
| Migration | Strang | lokal | Rollback |
|---|---|---|---|
| `2026_07_04_140001` create product_radiator_specs | HK | Ran [15] | `down()` (drop) |
| `2026_07_04_140002` create accessory_categories | HK | Ran [15] | `down()` (drop) |
| `2026_07_04_140003` create accessories | HK | Ran [15] | `down()` (drop) |
| `2026_07_04_140004` create heating_circuits | HK | Ran [15] | `down()` (drop) |
| `2026_07_04_140005` ALTER radiator_installations (+10 Spalten) | HK | Ran [15] | `down()` (drop columns/FK) |
| `2026_07_04_140006` create valve_insert_compatibility | HK | Ran [15] | `down()` (drop) |
| `2026_07_04_140007` create supplier_article_map | HK | Ran [15] | `down()` (drop) |
| `2026_07_04_140008` create radiator_connection_factors | HK | Ran [15] | `down()` (drop) |
| `2026_07_04_140009` ALTER accessories (imported_from) | HK | Ran [15] | `down()` (drop column) |
| `2026_07_04_150001` ALTER inverters (wb-Felder) | Katalog-i | Ran (`217473f`) | `down()` |
| `2026_07_04_150002` ALTER batteries (wb-Felder) | Katalog-i | Ran (`217473f`) | `down()` |
| `2026_07_04_150003` create product_pv_module_specs | Katalog-i | Ran (`217473f`) | `down()` |
| `2026_07_04_150004` create product_heat_pump_specs | Katalog-i | Ran (`217473f`) | `down()` |
| `2026_07_05_150005` ALTER product_heat_pump_specs (kurve_semantik) | Katalog | Ran [15] | `down()` |
| `2026_07_05_150006` ALTER products (imported_from) | Katalog | Ran [15] | `down()` |
| `2026_07_05_160000` ALTER deal_measurement_items (Preise nullable, S-3) | HK/SEC | Ran [15] | `down()` **nur waisenfrei** (vorher NULL→0) |
| `2026_07_05_150007` ALTER products (verifikation) | **Spec/B** | **Pending** | bedingt Strang-B-Abnahme (M5.1) |
| `2026_07_05_150008` ALTER spec_targets (import_batch_id) | **Spec/B** | **Pending** | ⬏ |
| `2026_07_05_150009` create spec_import_batches | **Spec/B** | **Pending** | ⬏ |
| `2026_07_05_170001` create materials | **B2a/C** | Ran (lokal) | `down()` (drop) |
| `2026_07_05_170002` create konstruktionen | **B2a/C** | Ran (lokal) | `down()` (drop) |
| `2026_07_05_170003` create baualtersklassen | **B2a/C** | Ran (lokal) | `down()` (drop) |
| `2026_07_05_170004` create klima_plz | **B2a/C** | Ran (lokal) | `down()` (drop) |
| `2026_07_05_170005` create anforderungsprofile | **B2a/C** | testing ✓ · ticket **Ran [16]** | `down()` (drop) |
| `2026_07_05_170006` create anforderungsprofil_werte | **B2a/C** | testing ✓ · ticket **Ran [16]** | `down()` (drop) |
| `2026_07_05_170007` ALTER anforderungsprofile (gebaeude_geometrie, B2a-3) | **B2a/C** | testing ✓ · ticket **Ran [17]** | `down()` (drop column) |
| `2026_07_05_180001` create accounting foundation (chart_of_accounts/clients/accounts/tax_codes/account_mappings/fiscal_years) | **Accounting/E** | testing ✓ · **Prod = Tag-X (Stufe i)** | `down()` (drop 6, FK-Reihenfolge) |
| `2026_07_05_180002` create accounting_documents (FK source_invoice_id→invoices) | **Accounting/E** | testing ✓ · **Prod = Tag-X** | `down()` (drop) |
| `2026_07_05_180003` create accounting_journal (entries+lines) | **Accounting/E** | testing ✓ · **Prod = Tag-X** | `down()` (drop 2) |

> **Accounting Stufe (i) (Strang E):** `180001–180003` sind **testing-verifiziert** (Migrate 9/9, Rollback 0/9, Suite 186) und **rein additiv** (nur CREATE, `imported_from`-Marker, kein Bestandseingriff). **Prod-Lauf = eigener Tag-X-Posten** (nicht in der obigen „23 committet"-Prod-Zählung enthalten; ausführen Yama/Ramin).

**23 committet** (HK 9 · Katalog 6 · S-3 1 · B2a-Referenz 4 · B2a-Anforderungsprofil 3) · **3 Spec Pending** (M5.1 nach Abnahme). Abhängigkeit: 170xxx (B2a) unabhängig von 150xxx/160xxx; `konstruktionen`→`materials` und `anforderungsprofil_werte`→`anforderungsprofile` (FK). **Hinweis:** alle 170xxx sind auf `ticket` (real) **Ran (Batch 16, 2026-07-05)** — siehe Migrations-Record im Kopf; „Ran (lokal)" bei 170001–04 = Dev-DB `ticket`.

## B) Seeder-Läufe
| Seeder | Wirkung | lokal | Rollback |
|---|---|---|---|
| `WberechnungImportSeeder` | `products.imported_from='wberechnung'=24` (19 WP + 5 PV), skip-Dedup | gelaufen | `DELETE … WHERE imported_from='wberechnung'` |
| `RadiatorSpecSeeder` (HK-Katalog) | `product_radiator_specs=30` | **gelaufen lokal 2026-07-05** | `TRUNCATE`/Marker-Delete |
| `AccessorySeeder` (HK-Zubehör) | `accessories=11` + `accessory_categories=5` | **gelaufen lokal 2026-07-05** | Marker-Delete |
| `ReferenzKatalogSeeder` (B2a-1) | `materials=23` + `konstruktionen=5` + `baualtersklassen=25` + `klima_plz=8168`, `imported_from='wberechnung'`, `verifikations_status` je Zeile | **gelaufen lokal 2026-07-05** | `ReferenzKatalogTeardownSeeder` (Marker-Delete) |
| `FoxEssLongiCatalogSeeder` (Fix 2) | Fox-ESS 16 + LONGi 10 products `imported_from='fox-longi-seed'` + 13 inverters + 2 batteries (`version`-Fallback) + neue Marken/Gruppen | **gelaufen lokal; Marker-Nachtrag 2026-07-05** | `FoxEssLongiCatalogTeardownSeeder` (**marker-basiert**, schont mehrbesitzte Marken) |

> Code der HK-Seeder gehört dem Katalog-/HK-Strang (Tabu); die **Läufe** hier = lokale Umgebungs-Arbeit (Tag-X-Posten: müssen produktiv wiederholt werden).

## C) Console-Commands (Daten)
| Command | Zweck | Pflicht | Rollback |
|---|---|---|---|
| `deal-measurements:backfill-owner` | `created_by ← deals.employee_id` | **`--dry-run` zuerst** (Waisen-Zahl in den Bericht), dann Lauf | irreversibel (nur additiv setzend) |

## D) `.env` / Flags
| Key | Wert | lokal | Rollback |
|---|---|---|---|
| `HEIZKOERPER_MODULE_ENABLED` | `true` | gesetzt | → `false` (Sofort-Rückzug des HK-Moduls) |
| `DEAL_MEASUREMENT_ORPHAN_HARD_DENY` | *(default false)* | — | Härtung nach Tag X |
| `DEAL_MEASUREMENT_{ASSIGN,UNLOCK,DELETE}_HARD_DENY` | *(default false)* | — | Härtung nach Tag X |

## E) Frontend (Nutzerwirkung)
| Posten | Commit | Wirkung |
|---|---|---|
| unlock-409-Resync + Material-Row-Mapper null-erhaltend | `71175f5` | Doppel-Unlock still; „kein Preis" ehrlich NULL (Material-Liste zeigt ohnehin keinen Preis) |
| M4-b Sidebar-Menüpunkt HK-Konfigurator | `41822c2`/`baa66f9` *(noch nicht in `backup-private`)* | HK-Modul im Menü sichtbar (sonst nur per URL) |

## F) Nachläufe NACH Tag X (kein Teil des Deploys)
- **14-Tage-Deny-Beobachtung** (5 Zähler `*_denied_count`/`*_orphan_write_count`) startet **nach** dem Produktions-Release (lokal mit 1 Nutzer sinnlos). Kriterium 0 → je Flag ein **Härtungs-Commit** (`*_HARD_DENY=true`) — selbst wieder Manifest-Posten.
- **SEC-DM-2** (`@index`-Write-on-read), Image-Präzisions-Weiche, W-Website, W-new-leads-Sicht.
- **`deal_invoices` DROP — AUSSTEHEND (Accounting/Tag-X-Posten):** die Rechnungs-Alt-Schiene ist code-seitig stillgelegt (Controller/Model/Routen/Views entfernt, Rückbau-Commit; `invoices` = führend). **Tabelle `deal_invoices` + Migration `2025_06_23_053704` bleiben bewusst stehen** — der DROP ist ein **separater, explizit zu beauftragender** Posten (0 Zeilen, kein Datenverlust), erst nach Yama-Freigabe. **NICHT** Beifang.
