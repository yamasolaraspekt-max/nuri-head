# RELEASE-MANIFEST — Tag-X-Deploy-Liste

> **Lokal-First (ab 2026-07-05, bis Tag X).** Produktion ist für alle Instanzen **tabu**. Diese Datei
> sammelt **fortlaufend ALLE produktionspflichtigen Posten** seit dem letzten Produktions-Stand.
> **Pflicht:** Jeder Commit mit prod-pflichtigem Teil (Migration · Seeder-Lauf · `.env`/Flag · Frontend-Fix
> mit Nutzerwirkung · Härtungs-Schalter · Config) trägt **im selben Commit** seine Zeile hier nach.
> **Posten ohne Zeile = Governance-Verstoß (gleichrangig Tabu-Bruch).**
>
> **Baseline (letzter Produktions-Stand):** von Yama zu bestätigen. **Reihenfolge:** Migrations-Timestamp.
> Am Tag X wird diese Liste zum Deploy-Runbook ausgearbeitet (Befehle, Erwartungs-Outputs, Rollbacks) und
> **einmal** mit Ramin auf dem Server ausgeführt.

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

**16 committet + lokal Ran** (HK 9 · Katalog 6 · S-3 1) · **3 Spec Pending** (M5.1 nach Abnahme). Abhängigkeit: alle ALTER-Basistabellen existieren; HK-interne FK-Ordnung durch Timestamps gedeckt.

## B) Seeder-Läufe
| Seeder | Wirkung | lokal | Rollback |
|---|---|---|---|
| `WberechnungImportSeeder` | `products.imported_from='wberechnung'=24` (19 WP + 5 PV), skip-Dedup | gelaufen | `DELETE … WHERE imported_from='wberechnung'` |
| *(offen)* `RadiatorSpecSeeder` (HK-Katalog, Kermi-Specs) | füllt `product_radiator_specs` | **noch nicht** (Katalog-Strang) | Marker-Delete |

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
