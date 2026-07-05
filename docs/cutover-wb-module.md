# Cut-over wberechnung → ticket — Modul-Inventur (Stufe 0)

**Stand:** 2026-07-05 · **read-only** (Code-Analyse live, DB-Beleg, Roadmap). Grundlage für Pflicht-Stopp 1:
je Roadmap-Lücke *aufnehmen/verzichten*, je Überschneidung *welche Version gewinnt*. Belege inline.
Master-Roadmap: `docs/roadmap-wberechnung-funktionen-in-ticket.md`.

## 0. Verifizierter Kern-Befund

**wberechnung ist eine Funktions-Werkstatt, keine Produktions-DB.** Live-Zeilen (`database/database.sqlite`, 2026-07-05):

| gefüllt | leer (Prototyp) |
|---|---|
| `energiekonzepte` **6** · Kataloge: `waermepumpen` 19, `heizkoerper` 30, `pv_modules` 3, `inverters` 3, `batteries` 2 · `users` 1 | `heizlast_projekte` 0 · `heizlast_raeume` 0 · `heizlast_bauteile` 0 · `wp_auslegungen` 0 · `auslegungen` 0 · `sanierungs_varianten` 0 · `plan_uploads` 0 |

→ **Kaum Projektdaten zu migrieren** (nur 6 Energiekonzepte). Alle Rechenkerne sind reine Service-Layer (kein Framework-Lock-in, portierbar wie der M3-Heizkörper-Port). *(Korrigiert ggü. Inventur-Agent: `heizlast_projekte`=0 nicht 30; `heizkoerper`=30 nicht 6.)*

## 1. Modul-Tabelle

| # | Modul | zentrale Dateien (wb) | genutzt? | Roadmap-Slot | ticket-Gegenstück | portierbar |
|---|---|---|---|---|---|---|
| A | Heizlast DIN EN 12831 | `Services/Heizlast/HeizlastProjektService`, `HeizlastRechner`, `HeizlastNormwerte` | 0 Projekte | M0–M5-nah / **B-Lücke (Kern)** | ❌ keine Route/View | ✅ pure Services |
| B | Heizkörper-Check EN 442 | `HeizkoerperCheckController`, `HeizkoerperService` | 30 Kat. | **M3 ✓ (Port) + M4 UI** | ✅ **Rechenkern portiert** (`Services/Heizkoerper/*`) | ✅ byte-genau (done) |
| C | Heizkörper-Katalog | `Models/Heizkoerper`, `heizkoerper` | 30 | M0/M2 | ✅ `product_radiator_specs` (testing) | Schema ✓ |
| D | Ventiltechnik/Hydraulik | `HydraulikService`, `HeizkoerperService::minVorlauf` | — | M1/M3 iv-b | ✅ `CompatibilityService` | ✅ portiert |
| E | HK-Aufnahme (Installation) | (neu in ticket) | — | **M4 UI** | ✅ `radiator_installations` (0 Zeilen) | Neu-UI |
| F | WP-Kennlinie/JAZ | `WpKennlinieService`, `Models/Waermepumpe` | 19 WP, 0 Ausl. | **B2 (Lücke, detailliert)** | ❌ | ✅ pure Service |
| G | WP-Bivalenz + Strom-Split | `BivalenzService`, `JazService` | 0 | **B2 (Lücke)** | ❌ | ✅ (UI-verwoben) |
| H | Wirtschaftlichkeit/Sanierung | `SanierungsWirtschaftlichkeitService`, `SanierungController` | 0 | **B3/B4** | ⚠️ `EconomicCalculationController` = **CRUD ohne Rechenkern** | ✅ Kern portierbar |
| I | PV-/WR-Auslegung | `PvProjektService`, `StringBuilderService`, `InverterSizingService` | 3/3/2 Kat., 0 Ausl. | **B2 (Lücke)** | ❌ | ✅ Contracts, pure |
| J | PVGIS-Ertrag | `PvgisController`, `GeocodingService` | 0 | **B2/B3** | ⚠️ `PVToolsController` (halb) | ✅ Service |
| K | Grundriss + Plan-Import (A-3d) | `GrundrissController`, `PlanUploadController`, 4 Jobs, `MassstabVorschlagService` | 0 uploads | **B5 (eigene Welle W6)** | ❌ | ⚠️ Queue+Storage — **ausgeklammert** |
| L | Sanierungsrechner IST↔Maßnahme | `SanierungController`, `Models/SanierungsVariante` | 0 | B3/B4 | ❌ | ✅ (hängt an A) |
| M | Fußbodenheizung EN 1264 | `FussbodenheizungService`, `FussbodenCheckController` | — | **B3 ODER Verzicht** | ❌ | ✅ pure |
| N | Beschaffung/Connectoren | `KatalogController`, `Procurement/*` (OMD/Datanorm/IDS/OCI) | Basis-Kat. | **A4 → B1** | ⚠️ Supplier-Stack offen (OMD-Strang parallel!) | ⚠️ Supplier-Entscheid |
| O | Förderung BAFA/KfW | `FoerderungService` | 0 | **B3/B4 (integriert)** | ❌ | ✅ pure |
| P | Klimadaten OpenMeteo/ASHRAE | `OpenMeteoKlimaService`, `KlimaBinService`, `KlimaPlzService` | 0 | **B2 (Lücke)** | ❌ | ✅ Service+API |
| Q | Energiekonzept-Bundler | `EnergiekonzeptController`, `Models/Energiekonzept` | **6 (einzig gefüllt)** | **B4 (Lücke)** | ❌ (ticket `EconomicCalculation` ≠ Struktur) | ⚠️ Schema neu |

## 2. Funktionsvergleiche (Module mit ticket-Gegenstück)

**B/Heizkörper:** wb rechnet EN-442 (`qReal`), hydraulischen Abgleich (kv/Voreinstellung), Min-Vorlauf-Suche, Ampel. ticket hat den **Rechenkern byte-genau portiert** (M3), aber **keine UI/Routen** und 0 Daten. → **ticket behalten + erweitern (M4 UI im ticket-Design).** Kern bleibt unverrückt.

**J/PVGIS:** wb `PvgisController` + `GeocodingService` = vollständiger Server-Abruf (Geocoding → PVGIS v5.2 → Jahresertrag + Monatsverteilung, gecacht). ticket `PVToolsController` = **halber Stub** (Geocoding da, PVGIS-Teil unfertig, keine Persistenz/Views). → **wb-Kern übernehmen, ticket-Stub ablösen** (keine Doppelung). B2 Port + B3 Views.

**H/Wirtschaftlichkeit:** wb `SanierungsWirtschaftlichkeitService` = validierter Rechenkern (IST↔Maßnahme, 30-J-Projektion, BAFA, Amortisation, JAZ-Kopplung). ticket `EconomicCalculationController` = **CRUD-Skelett ohne Rechenkern** (speichert nur Endwerte). → **ticket um wb-Kern erweitern (B3/B4)**; wb-Logik ist geprüft, Neuschreiben = Fehlerrisiko. *(OFFEN: `EconomicCalculation`-Model beibehalten oder auf Sanierungsvarianten umstellen.)*

**In allen drei Fällen: Doppelstruktur ist NICHT die Empfehlung.**

## 3. Roadmap-Lücken (Vorschlag je Modul)

| Modul | Vorschlag | Begründung |
|---|---|---|
| A Heizlast-Kern | **neuer B-Punkt B2-Kern** (Basis für H/L) | Ohne Heizlast keine Sanierung/WP-Größe; pure Services, portierbar |
| F+G WP-Kennlinie/Bivalenz | **B2a** „WP-Auslegung" (zusammen) | beide WP-Logik; nach B1 (Katalog); parallel zu I |
| I PV-/WR-Sizing | **B2a** „WR/PV-Auslegung" | nach B1, parallel zu F/G |
| P Klimadaten | **B2b** „Klimaprofile" | Heizlast-Input; parallel zu A |
| H+O+L Wirtschaft/Förderung/Sanierung | **B3/B4** (gebündelt) | Views + Nav; hängen an A |
| Q Energiekonzept-Bundler | **B4** „Energiekonzepte" | einziges gefülltes Modell; neue ticket-Struktur nötig |
| M Fußbodenheizung | **B3-Unterpunkt ODER Verzicht** | Nische; Heizkörper hat Vorrang — ehrliche Verzicht-Option |
| K Grundriss/Plan-Import | **B5 (W6) ODER Verzicht bis dahin** | schwerste Views, Queue/Storage; nicht kritisch; A-3d-Parallelstrang |
| N Beschaffung | **A4-Entscheid VOR B1** | Supplier-Stack (OMD/Datanorm/IDS) — blockiert Katalog-Reconciliation |

## 4. Entscheidungen (Pflicht-Stopp 1 — getroffen 2026-07-05)

**Überschneidungen (Version, die gewinnt):**
1. **PVGIS** → ✅ wb-Kern übernimmt; ticket-`PVToolsController` **abgelöst** (Route/Nav-kompatibel, keine Parallelroute). Slot B2/B3.
2. **Wirtschaftlichkeit** → ✅ ticket um `SanierungsWirtschaftlichkeitService` **erweitern**; Model-Frage offen bis B3. ⚠️ Sperre: `EconomicCalculationController` uncommittet — erst nach dessen Commit.
3. **Heizkörper** → ✅ ticket behalten + M4-UI (Kern-Port bleibt).

**Roadmap-Aufnahmen (alle bestätigt):**
- **B2a-Reihenfolge:** zuerst A (Heizlast) + P (Klima), dann F+G (WP) + I (PV/WR) parallel.
- H/O/L → B3/B4 · Q → B4.
- **M (Fußbodenheizung):** ✅ aufgenommen als **letzter B3-Unterpunkt** (erste Streichposition).
- **K (Grundriss/A-3d):** ✅ B5/W6, **kein Verzicht**; wb bleibt für K benutzbar bis B5 (differenzierter Einfrier-Vermerk).
- **N (Beschaffung):** ✅ A4 entschieden — ticket-Supplier-Stack, wb-Procurement **nicht portiert**, B1 dockt an ticket an.

→ In der Roadmap (§9) festgeschrieben. Weiter mit **Stufe 1 (Daten-Inventur)**.
