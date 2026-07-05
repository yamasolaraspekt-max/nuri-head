# Cut-over wberechnung → ticket — Daten-Inventur (Stufe 1)

**Stand:** 2026-07-05 · **read-only** (SQLite live + storage). Grundlage für Pflicht-Stopp 2 (W-C1–C3).
Kategorien: **KATALOG · PROJEKTDATEN · SYSTEM · VERZICHTBAR**. „Ziel in ticket" = Schema existiert / **FEHLT** (nie erfinden).

## 0. Kern-Befund

- **PROJEKTDATEN quasi leer:** nur **6 `energiekonzepte`**. Alle anderen Projekt-/Auslegungstabellen = 0.
- **Kataloge sind der eigentliche Datenwert** — Geräte *und* Referenz-Kataloge (~200 Zeilen gesamt).
- **storage/app: 12 K, 3 Dateien** (`private/plan-uploads` faktisch leer) → **keine Disk-Migration nötig**.
- **users: 1** (Test) → ticket-Auth gilt, keine User-Migration.

## 1. Tabellen-Inventur (alle 38 Tabellen, live 2026-07-05)

### KATALOG — Geräte (→ ticket `products` + Spec-Tabellen)
| Tabelle | Zeilen | Ziel in ticket | Weg | Blocker |
|---|---|---|---|---|
| `artikel` | 23 (13 WR + 2 Bat + 5 Modul + 3 Fenster) | `products` (+ `imported_from`-Marker) | Import-Seeder, **Netto-Neuwert** | **WP-Fix-Gate** + Seeder existiert noch nicht |
| `inverter_specs` | 13 | `inverters` (Stufe-i-Felder) | via Seeder | ⬏ |
| `battery_specs` | 2 | `batteries` | via Seeder | ⬏ |
| `pv_module_specs` | 5 | `product_pv_module_specs` | via Seeder | ⬏ |
| `waermepumpen` | 19 | `product_heat_pump_specs` | via Seeder | **WP-Datenblatt-Fix (Buderus-Weiche)** |
| `pv_modules` / `inverters` / `batteries` / `inverter_battery` | 3 / 3 / 2 / 2 | Rechenkern-Referenz — z. T. redundant mit `products` | prüfen bei B2a | offen |
| `heizkoerper` | 30 | `product_radiator_specs` | **entfällt** — Parallelstrang `RadiatorSpecSeeder` hat identische Kermi-Zeilen | — |
| `fenster_specs` | 3 | (kein WR/PV) — Fenster-Bauteil-Referenz | mit B2a | Schema FEHLT |

### KATALOG — Referenz/Rechengrundlagen (→ ticket-Schema **FEHLT**)
| Tabelle | Zeilen | Rolle | Weg |
|---|---|---|---|
| `materials` | 23 | Baustoffe/λ-Werte (Heizlast) | **mit B2a (Heizlast-Kern)** — Schema neu, additiv |
| `konstruktionen` | 5 | Bauteil-Aufbauten (U-Wert) | mit B2a |
| `baualtersklassen` | 25 | DIN-Baualter-Kennwerte | mit B2a |
| `batterie_wr_kompatibilitaet` | 26 | Kompatibilitätsmatrix | mit B2a (WR/Bat-Sizing) |

### PROJEKTDATEN
| Tabelle | Zeilen | Kategorie | Weg |
|---|---|---|---|
| `energiekonzepte` | **6** | PROJEKTDATEN (einzig gefüllt) | **W-C1** — Neuerfassung *oder* mit B4-Bundler-Port; Schema FEHLT (≠ ticket `EconomicCalculation`) |
| `auslegungen`, `wp_auslegungen`, `sanierungs_varianten`, `raum_geometrien`, `heizlast_projekte/raeume/bauteile`, `plan_uploads` | 0 | PROJEKTDATEN (leer) | **entfallen** — nichts zu migrieren |

### SYSTEM / VERZICHTBAR
| Tabelle | Zeilen | Weg |
|---|---|---|
| `users` | 1 | ticket-Auth gilt, **nicht migrieren** (nur Besitz der 6 Energiekonzepte → auf ticket-User mappen) |
| `migrations` | 44 | System, entfällt |
| `lieferanten`, `lieferanten_artikel`, `bestellungen`, `bestellpositionen`, `lagerbestand`, `wareneingang`, `artikel_merkmale` | 0 | **VERZICHTBAR** — Procurement per A4 nicht portiert |
| `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `sessions`, `password_reset_tokens` | 0 | Laravel-System, entfällt |

## 2. Katalog-Delta

**OFFEN — kein Delta berechenbar:** Es existiert **kein gebauter `WberechnungImportSeeder`** (Prämisse P2 widerlegt) → kein „letzter Import"-Referenzstand. Sobald der Seeder (nach WP-Fix-Gate) gebaut ist, wird der Delta-Abgleich sinnvoll. Bis dahin: Import-Scope = **Netto-Neuwert** (19 WP + AIKO + LONGi LR7), Fox-ESS/HK bleiben draußen (ticket-Bestand gewinnt).

## 3. Entscheidungen für Pflicht-Stopp 2

- **W-C1 — die 6 Energiekonzepte:** Neuerfassung (6 Stück, kein Schema/Skript nötig) **vs.** mit B4-Bundler-Port migrieren (additives Schema). *Empfehlung: bei 6 Stück Neuerfassung ehrlich billiger als Migrations-Skript + neues Schema — es sei denn, die 6 tragen wertvolle Referenz-Szenarien.*
- **W-C2 — Einfrier-Zeitpunkt je Kategorie:** ✅ **Geräte-Katalog wb eingefroren (2026-07-05, Stufe 2)** — WP war es schon, jetzt auch WR/Bat/Modul (Fox-ESS/LONGi bereits in ticket, AIKO/LONGi-LR7 via `WberechnungImportSeeder` importiert, testing); Geräte-Änderungen laufen ab jetzt in ticket, wb nur noch Herkunftsnachweis. *(wb-README-Einfriervermerk offen — wb ist read-only, beim nächsten freigegebenen wb-Fenster nachtragen.)* Referenz-Kataloge bei B2a-Port; Projektdaten je bei Modul-Abschluss. **K (Grundriss) bleibt benutzbar bis B5** → Einfrier-Vermerk differenziert.
- **W-C3 — Katalog-Delta-Zuschnitt:** Netto-Neuwert bestätigt (redundante Fox-ESS/LONGi-WR/Bat bleiben draußen, ticket ist datenblatt-verifiziert)?
- **NEU aufgetaucht — W-C4:** Die **4 Referenz-Kataloge** (materials/konstruktionen/baualtersklassen/kompatibilitaet, ~79 Zeilen) sind Rechengrundlagen ohne ticket-Schema → mit **B2a** (Heizlast-Kern-Port) als additive Tabellen mitnehmen? *(Empfehlung: ja — der Heizlast-Kern braucht sie; ohne sie kein DIN-12831.)*
