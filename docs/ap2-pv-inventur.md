# AP-2 — PV-Inventur (PV / 3D / Speicher / Wallbox / Gesamtenergie)

**Stand:** 2026-07-14 · **read-only** · **kein Bau, kein Commit, kein Push, keine Migration, keine Datenänderung, kein 3D-Bau, kein playground-Code kopiert, keine zweite Wahrheit.**
**Kapitel:** 1/8/9/10 (Landkarte / 3D-PV / Speicher-Wallbox / Gesamtenergie). **Grundlage:** `docs/gesamtfahrplan-gebaeude-energie-angebot.md`, `docs/bereich2-gebaeude-energie-konfigurationsplattform-gap-analyse.md`, `docs/ap2-pv-inventur-startblock.md`.
**Quellen (firsthand, read-only, 2026-07-14):** ticket-Code (`app/Models`, `app/Services/Energie`, `app/Http/Controllers`, `resources/views/admin/{solar,roof_config,pvgis,energie}`, `SchluesselRegistry`); playground `/Users/yamanuri/Documents/Playground` (Backend + `src/`); wberechnung `/Users/yamanuri/Herd/wberechnung`.

---

## 1. Kurzfazit

> **PV ist heute in ticket ein objekt-verankertes, aber vom versionierten Kern getrenntes Silo — technisch fragmentiert, teils gebrochen, nicht am `Anforderungsprofil`.** Die PV-Modelle hängen zwar über eigene Tabellen an Kunde/Objekt (`customer_id`/`alternative_id`/`product_id`), aber es gibt **keine PV-Schlüssel in der `SchluesselRegistry`, keinen PV-Adapter, keine versionierte PV-Auslegung** wie bei WP. Der PV-Auslegungskern (`InverterSizingService`, `PvgisErtragService`, `KostenService`) ist vorhanden und läuft als **Stand-alone-Rechner** (`/admin/energie/wr-auslegung`, `Request`-Eingabe, keine Objektbindung). **`PvProjektService` ist gebrochen** (fehlende Abhängigkeit `StringBuilderService` → nicht instanziierbar → 0 Aufrufer). Der wertvolle, isolierbare PV-Rechenstoff liegt in **playground** (reife PHP-Service-Suite) und in **wberechnung** (Rest-Referenz `StringBuilderService`/`PerformanceService`). Ein 3D-PV-Belegungs-/Ertragsplaner **existiert in keinem der drei als produktreifer, persistierender Baustein** — playgrounds 3D-Planer ist ein React-Prototyp (`@ts-nocheck`, keine Persistenz), wberechnung liefert für 3D **nichts**. **Empfehlung: PV zuerst konzeptionell an den gemeinsamen Kern (AP-3) hängen und den Rechenkern reparieren/konsolidieren — 3D-PV bleibt hinter der gemeinsamen Geometrie (Kapitel 4).**

---

## 2. ticket-Bestand

### Models (objekt-verankert, eigene Tabellen — NICHT am Anforderungsprofil)
- **System/Ertrag:** `SolarSystem` (`lead_id`/`alternative_id`/`product_id`, dünn), `PVRoof`/`PVLongRoof` (`customer_id`/`alternative_id`), `PVRoofPlan` (`product_id`), `OfferRoofLayoutConfiguration` (`offer_id`/`offer_detail_id`), `LeadAlternativePvWpDetail` (an `lead_alternative_add_id`).
- **Geräte-Kataloge:** `ProductPV`/`product_pv_module_specs`, `Inverter`, `Battery`/`BatterySystem`/`BatteryInverter`, `PowerOptimizer`, `BackupGenerator`, `ElectricVehicle`.
- **Wirtschaftlichkeit:** `ProfitabilityCalculation`/`ProfitabilityData` (`customer_id`/`alternative_id`/`product_id`), `EconomicAssumption`/`EconomicCalculation`.
- **Klima/Ertrag:** `ClimateStation`/`ClimateMonthlyData`/`ClimateSolarMonthlyData`/`ClimateLocation`/`ClimateEvaluationRow`.
- **Checklisten:** `PVChecklist`/`PVLongChecklist`. **Legacy-Tool:** `PVTools`.

### Services (`app/Services/Energie/`)
- `InverterSizingService` — WR-/String-Auslegung (VDE-AR-N 4105/4110). **Verdrahtet** über `EnergieAuslegungController` (`/admin/energie/wr-auslegung`, Stand-alone-Tool).
- `PvgisErtragService` — **kanonischer** PVGIS-Jahresertrag (JRC v5_3, „EINE WAHRHEIT für den PVGIS-Ertrag").
- `KostenService` — Investkosten für Wirtschaftlichkeit.
- `PvProjektService` — **GEBROCHEN:** Konstruktor verlangt `App\Services\StringBuilderService` (`PvProjektService.php:8,19`), diese Klasse **existiert in ticket nicht** → nicht instanziierbar → 0 Aufrufer. Totes/unvollständiges Transplantat.
- Contracts `SizingModule/SizingInverter/SizingBattery/WpKennlinie`; DTOs `ModuleSpec/InverterSpec/BatterySpec/HeatPumpKennlinie`.
- Nebenan: `app/Services/RoofAreaEstimator.php` (Dachfläche aus OSM-Overpass-Polygon — echte Schätzung), `app/Services/GoogleGeocoder.php`.

### Controller
`EnergieAuslegungController` (wr-/wp-auslegung, `Request`-basiert), `EnergiekonzeptController`, `PVToolsController` (Legacy-PVGIS), `SolarSystemController`, `Economic*`/`Profitability*Controller`, `PVRoof*`/`PVLongRoof*`/`PVChecklist*Controller`, `Inverter*`/`Battery*Controller`, `ProductPVController`, `OfferRoofLayoutConfigurationController`.

### Verankerung / Registry
- **PV-Schlüssel in `SchluesselRegistry`:** **KEINE** (Registry ist WP/Heizlast-only, „additiv erweiterbar"). → PV wird **nicht** über das versionierte `Anforderungsprofil` geführt.
- PV persistiert in **eigenen** objektgebundenen Tabellen (`solar_systems`, `p_v_roofs`, `profitability_*`, `offer_roof_layout_configurations`) → objekt-verankert, **aber nicht versioniert und nicht am gemeinsamen Kern**.

---

## 3. playground-Bestand (Referenz/Bauteile-Lager — nicht importieren)

**PV-Backend-Rechenlogik (reife, isolierbare PHP-Services — der eigentliche Wert):**
`InverterSizingService` (~11 Regeln, VDE-AR-N 4105/4110, §14a), `StringBuilderService`, `PerformanceService` (PR 0,70–0,88, Clipping), `KabelService` (VDE 0298-4), `SchutzkomponentenService` (ÜSS/LS/RCD/EPS), `EpsBoxService`, `WirtschaftlichkeitEngine` (30-J-Cashflow/NPV), `FoerderEngine`. Contracts `Sizing{Module,Inverter,Battery}`. Persistenz-Schema `Auslegung*`/`auslegungen` (vom Live-Tool aber nicht beschrieben = Schema-Vorlauf).

**3D-Planer (`src/`):** `DachplanerProPage.tsx` (3.786 Z., `@ts-nocheck`, Gemini-Prototyp, **keine Persistenz**), Standalone-Vite-Bundle, Blade-Hülle. **Wertvoll nur:** framework-freie, getestete Geometrie-/Mengen-Utils `src/utils/*` (Polygonfläche, Dachverschneidung, Gauben, Holzmengen, Belegung, Werkstattplan). Serverbrücke `PvBelegungExtractor`/`RoofTemplateFeatureExtractor` (kWp serverseitig aus `config_json`). **Erzeugt:** Dachgeometrie, Belegung, kWp, BOM, Werkstattplan. **NICHT:** Ertrag, Verschattung, Elektro-Auslegung.

**Mehr-System-Klammer:** `AnlagenKonfiguration`/`AnlagenKonfigPosition`/`AnlagenKonfigSnapshot` (PV+WR+Batterie+EPS+WP+Wallbox → BOM → Snapshot), `PlanungskontextController` (read-only Aggregation je Objekt), `KonfiguratorPipelineService`. **Aber:** `PvAusleger` = Heuristik/`to_verify`, `StubAusleger` = Platzhalter.

**Speicher/Wallbox:** `LastmanagementService` (produktiv, §14a, √3), `LmWallbox`, `Battery`/`BatterySpec`, Lastprofil-Modelle (BDEW-Profile). **Google Solar:** `SolarController::buildingInsights` (Proxy, Frontend-Consumer fehlt).

**Verwaiste Enden (React→Blade-Migration):** `pvPlanungApiService.ts`, `utils/pvPlanung.ts`, PV-Frontend-Seiten — existieren nicht mehr.

---

## 4. wberechnung-Bestand (Quelle/Referenz — großteils schon nach ticket transplantiert)

- **Schon in ticket (Doppelung/Herkunft):** `PvProjektService`-Struktur, `KostenService`, `Energie/InverterSizingService`-Variante; PVGIS in ticket **moderner** (Service v5_3 statt Controller v5_2).
- **Rest-Referenz mit PV/Bilanz-Bezug (fehlt in ticket):**
  - **`StringBuilderService`** (`App\Services\StringBuilderService`) — **kritisch:** genau die von ticket-`PvProjektService` importierte, dort fehlende Klasse. String/MPPT-Verteilung.
  - `PerformanceService` — PV-Ertrag/PR/Clipping (speist Bilanz).
  - `InverterSuggestionService` — DB-gestütztes WR-Ranking.
  - `AuslegungHandoffService` — Bindeglied Auslegung → Energiekonzept-Bilanz.
  - **JS-Zeitreihenmodell** der 30-J-Wirtschaftlichkeit/Autarkie/CO₂ (`energiekonzepte/simulator.blade.php`, 2090 Z., BDEW/HTW/PVGIS) — höchster inhaltlicher Wert, schwer prüfbar; Deckung im ticket-`energiekonzept.blade.php` **nicht verifiziert**.
- **Speicher:** `batterie_wr_kompatibilitaet` (n:m Freigabeliste) — in ticket als eigene Tabelle nicht gesehen (Rest-Referenz, niedrig; Regelkern via `InverterSizingService` schon da).
- **3D/Geometrie:** wberechnung liefert **nichts** — `RaumGeometrie` ist reine Heizlast-Raumgeometrie (kein Dach/PV). Nur Google-Solar-Erkennung (bereits vorhanden).
- **Verwerfen (kein PV-Bezug):** `OpenMeteoKlimaService`, `AuslegungService`, `MassstabVorschlagService`, `wp_material_sets.json`, Legacy-`InverterSizingService`, vermutlich `GeocodingService`.

---

## 5. Datenwahrheiten

| Sachverhalt | Führende Wahrheit (Soll) | PV-Ist heute |
|---|---|---|
| Objekt (Anker) | `lead_alternative_adds` | PV nutzt ihn (customer/alternative), aber je Eigen-Tabelle |
| Auslegung/Bedarf (versioniert) | `Anforderungsprofil` (+ `SchluesselRegistry`) | **PV NICHT vertreten** — keine PV-Schlüssel, kein Adapter, keine Versionierung |
| PV-Ertrag | `PvgisErtragService` (kanonisch, v5_3) | **Doppelspur:** `PVToolsController::fetchByPostcode` (Legacy-PVGIS+Google) parallel |
| Positionen | `offer_details.sections` | `OfferRoofLayoutConfiguration` an `offer_detail` — separat |
| Preis | `CatalogPriceGuard`/`component_id` | unberührt |

**Kernbefund:** PV hat **keine** einheitliche Auslegungswahrheit am versionierten Kern; es gibt mehrere objektgebundene PV-Silos + eine PVGIS-Doppelspur.

---

## 6. UI/UX-Bestand

- **3D/three.js in ticket vorhanden:** `resources/views/admin/solar/configuration/configure.blade.php` (three.js + GLTFLoader), `resources/views/admin/roof_config/roof(s).blade.php` (three.js). Mapbox in `package.json`.
- **PVGIS-UI:** `resources/views/admin/pvgis/*`.
- **Cruft (Aufräum-Kandidaten):** `roof_config/config.blade copy 2.php`, `config.blade copy 3.php`, `pvgis/pvgis_details.blade copy.php` — verwaiste Kopien.
- **PV-Bedarfsformular:** **kein** eigenes `ProductFormula` für PV (nur WP-Feld „mit_pv_koppeln"). PV hat keine dem WP-Formular gleichwertige Bedarfserfassung.
- **Ticket-CI-Regel gilt:** Vuexy/Blade/jQuery; Alpine nur in den 2 erlaubten Scopes; playground-Design ist keine Vorlage.

---

## 7. Rechenlogik

- **ticket:** `InverterSizingService` (verdrahtet, Stand-alone-Tool), `PvgisErtragService` (kanonisch), `KostenService`. `PvProjektService` **gebrochen** (fehlende `StringBuilderService`).
- **playground (Referenz):** vollständige, reife Suite (Sizing/String/Performance/Kabel/Schutz/EPS/Wirtschaftlichkeit/Lastmanagement) — bester Kandidat für eine PV-Rechen-Bibliothek, isoliert übernehmbar.
- **wberechnung (Rest-Referenz):** `StringBuilderService` (schließt die ticket-Lücke), `PerformanceService`, `InverterSuggestionService`, `AuslegungHandoffService`.
- **Gemeinsame Lücke:** PV-Auslegung ist nirgends in ticket **versioniert am Anforderungsprofil**; sie läuft transient/silohaft.

---

## 8. 3D / Geometrie

- **ticket:** zwei three.js-Editoren (roof_config, solar/configuration) + `RoofAreaEstimator` (OSM-Dachfläche) + 2D-Grundriss (SVG, Heizlast). **Kein** produktiver, an den Objekt-Kern gebundener 3D-PV-Belegungsplaner; Geometrie uneinheitlich (three.js vs. SVG vs. `raum_geometrien` vs. `gebaeude_geometrie`-JSON).
- **playground:** 3D-Planer = React-Prototyp (`@ts-nocheck`, keine Persistenz); wertvoll nur die framework-freien `utils/*` + `config_json`/Extractor-Muster.
- **wberechnung:** **nichts** für 3D-PV (nur Google-Solar-Erkennung, bereits vorhanden).
- **Konsequenz:** Ein 3D-PV-Belegungsplaner müsste in ticket **neu** entstehen (oder playground-Utils ernten) — und **erst nach** dem gemeinsamen Geometriemodell (Kapitel 4), sonst drittes Geometrie-Silo.

---

## 9. Speicher / Wallbox / Gesamtenergie

- **Speicher (ticket):** `Battery`/`BatterySystem`/`BatteryInverter`, `Contracts/SizingBattery` — Stammdaten + Contract, **kein** verdrahtetes Sizing am Kern. wberechnung: Regelkern via `InverterSizingService` da; `batterie_wr_kompatibilitaet`-Liste Rest-Referenz.
- **Wallbox (ticket):** `ElectricVehicle`-Model; **kein** Lastmanagement. playground: `LastmanagementService` (produktiv, §14a) = starker Übernahme-Kandidat.
- **Gesamtenergie (ticket):** `EnergiekonzeptController` + Views vorhanden; ob das 30-J-Zeitreihenmodell (Autarkie/Eigenverbrauch/CO₂/Cashflow) real drinsteckt, ist **nicht verifiziert**. wberechnung: das JS-Simulator-Herz (2090 Z., BDEW/HTW) ist Rest-Referenz höchsten Werts. **Keine** aggregierte Objekt-Energiebilanz am Kern.

---

## 10. Wiederverwendbar / Reserve / Verwerfen

**Wiederverwendbar (isoliert, hoher Wert):**
- ticket: `PvgisErtragService` (kanonisch, behalten + Legacy-Doppelspur stilllegen), `InverterSizingService`, `KostenService`, `RoofAreaEstimator`, DTOs/Contracts.
- playground (als Bibliothek neu einziehen, nicht kopieren): PV-Rechen-Suite (Sizing/String/Performance/Kabel/Schutz/EPS/Wirtschaftlichkeit), `LastmanagementService`, framework-freie `utils/*`.
- wberechnung: `StringBuilderService` (schließt die gebrochene ticket-Abhängigkeit), `PerformanceService`, `AuslegungHandoffService`.

**Reserve (später prüfen):**
- playground `AnlagenKonfiguration`-Schema + `KonfiguratorPipelineService` (Datenmodell-Blaupause für die Mehr-System-Klammer — konzeptionell zu AP-3), `RoofTemplate`/Extractor (nur mit Planer-State), `SolarController`-Muster; wberechnung JS-Simulator (gegen ticket-Energiekonzept abgleichen), `InverterSuggestionService`, `batterie_wr_kompatibilitaet`.

**Verwerfen:**
- playground `DachplanerProPage.tsx` als Ganzes (React-Prototyp, `@ts-nocheck`, keine Persistenz — nur `utils/*` ernten), `PvAusleger`/`StubAusleger`, leere Gerüst-Controller, alle Blade-Views (Designbindung), verwaiste React-Enden.
- wberechnung Legacy-`InverterSizingService`, `OpenMeteoKlimaService`/`AuslegungService`/`MassstabVorschlagService`/`wp_material_sets.json` (kein PV-Bezug), `GeocodingService` (durch `GoogleGeocoder` ersetzt).
- ticket-Cruft: `roof_config/config.blade copy 2/3.php`, `pvgis_details.blade copy.php`.

---

## 11. Lücken

| # | Lücke | Schwere |
|---|---|---|
| PV-L1 | **`PvProjektService` gebrochen** — Abhängigkeit `StringBuilderService` fehlt in ticket → nicht instanziierbar | hoch (totes/gefährliches Transplantat) |
| PV-L2 | **PV nicht am versionierten Kern** — keine PV-Schlüssel in `SchluesselRegistry`, kein PV-Adapter, keine versionierte PV-Auslegung | hoch |
| PV-L3 | **PVGIS-Doppelspur** — Legacy `PVToolsController::fetchByPostcode` vs. kanonischer `PvgisErtragService` | mittel |
| PV-L4 | **Kein PV-Bedarfsformular** (kein `ProductFormula` für PV wie bei WP) | mittel |
| PV-L5 | **Kein 3D-PV-Belegungsplaner** am Objekt-Kern; Geometrie uneinheitlich | mittel (hängt an Kapitel 4) |
| PV-L6 | **Speicher/Wallbox-Sizing + Lastmanagement fehlen** (nur Stammdaten/Contract) | mittel |
| PV-L7 | **Keine aggregierte Objekt-Energiebilanz** (Gesamtenergie/Autarkie/CO₂ nicht am Kern; Deckung ungeprüft) | mittel |
| PV-L8 | **PVGIS-Ertrag nicht persistiert/versioniert** an der Auslegung | niedrig-mittel |
| PV-L9 | **UI-Cruft** (Kopie-Blades) + `SolarSystem` sehr dünn | niedrig |

---

## 12. Risiken

- **PV-L1 ist ein Live-Risiko:** Jeder Versuch, `PvProjektService` zu nutzen/aufzulösen, wirft einen Container-Fehler. Vor jeder PV-Verdrahtung muss das entschieden werden (StringBuilderService nachziehen **oder** PvProjektService als tot markieren/entfernen — eigener Posten).
- **3D-PV vor gemeinsamer Geometrie (Kapitel 4):** hohes Risiko eines **dritten Geometrie-Silos** neben `raum_geometrien`/`roof_config`/`gebaeude_geometrie`. playgrounds Planer ist Prototyp-Schuld (`@ts-nocheck`, keine Persistenz, React stack-fremd). **Nicht vor dem Geometrie-Contract bauen.**
- **Zweite Wahrheit:** PV-Rechenlogik aus playground/wberechnung „schnell kopieren" würde eine zweite Auslegungs-/Ertrags-/Bilanzwahrheit erzeugen — verboten. Übernahme nur als isolierte Bibliothek + Anbindung an **eine** führende Wahrheit (PVGIS→`PvgisErtragService`, Auslegung→Anforderungsprofil-Kern).
- **Bilanz-Scheingenauigkeit:** Das 30-J-Modell (Autarkie/CO₂) ist inhaltlich mächtig, aber richtwertlastig; ohne Datenqualität/Ampel droht Scheingenauigkeit (analog Belastbarkeits-Gate WP).
- **playground-API-Kopplung:** Der 3D-Planer braucht playground-Katalog-Endpunkte für echte Ziegel/Montage/3D-Modelle; ohne diese nur Defaults.

---

## 13. Empfehlung für AP-3 (Plattform-Klammer)

1. **PV muss in AP-3 als eigener Gewerk-Strang der Klammer mitgedacht werden** — genau wie WP. Die Klammer-Sicht je Objekt zeigt PV **neben** WP/Speicher/Wallbox.
2. **PV an den versionierten Kern binden (Konzept, nicht Bau):** PV-Schlüssel in `SchluesselRegistry` (kwp, spez_ertrag, ausrichtung, autarkie …) + PV-Adapter analog `AnforderungsprofilHeizlastAdapter` — das ist der Weg, PV-L2 ohne zweite Wahrheit zu schließen. In AP-3 nur als Naht/Konzept festhalten.
3. **playground `AnlagenKonfiguration` als Blaupause** (nicht Import) für die Mehr-System-Position je Objekt — deckt sich mit der AP-3-Weiche (Option A: Objekt-Klammer + read-model).
4. **Führende Wahrheiten festschreiben:** PV-Ertrag = `PvgisErtragService` (Legacy stilllegen); PV-Auslegung = Anforderungsprofil-Kern; Positionen = `offer_details`.

## 14. Empfehlung für nächste Slices (nach AP-3, je eigener Startblock + Yama-Freigabe)

| Slice | Inhalt | Voraussetzung | Risiko |
|---|---|---|---|
| PV-S1 | **`PvProjektService`-Bruch entscheiden:** `StringBuilderService` (aus wberechnung, isoliert) nachziehen **oder** `PvProjektService` als tot archivieren | Befund PV-L1 | niedrig |
| PV-S2 | **PVGIS-Doppelspur stilllegen:** Legacy `PVToolsController::fetchByPostcode` auf `PvgisErtragService` umleiten (eine Wahrheit) | — | niedrig |
| PV-S3 | **PV am Kern (Konzept→klein):** PV-Schlüssel-Registry + PV-Adapter (read/write Auslegung ins Profil), analog WP | AP-3 Klammer-Weiche | mittel |
| PV-S4 | **PV-Rechen-Bibliothek:** playground-Suite (Sizing/String/Performance) als isolierte Services einziehen, an `PvgisErtragService`/Kern anbinden | PV-S1/PV-S3 | mittel |
| PV-S5 | **Speicher/Wallbox-Sizing + Lastmanagement** (playground `LastmanagementService` als Kandidat) | PV-S4 | mittel |
| PV-S6 | **Gesamtenergie-Bilanz** (Deckung ticket vs. wberechnung-JS-Modell prüfen; Aggregation am Kern, Datenqualitäts-Ampel) | PV-S4/PV-S5 | mittel |
| PV-S7 | **3D-PV** — erst Geometrie-Contract (Kapitel 4), dann Migrations-Weg (AP-9): playground-`utils/*` ernten vs. ticket-three.js ausbauen vs. neu | Kapitel 4 | hoch |

**Reihenfolge-Kernaussage:** Zuerst **reparieren/konsolidieren** (PV-S1/S2) und **an den Kern hängen** (PV-S3), dann **Rechen-Bibliothek** (PV-S4), dann Speicher/Wallbox/Gesamtenergie (PV-S5/S6), **3D-PV zuletzt** hinter der gemeinsamen Geometrie (PV-S7). **3D-PV nicht vor Kapitel 4.**

---

## 15. STOPP

Read-only Inventur abgeschlossen. Keine Datei außer diesem Dokument geändert. Kein Bau, kein Commit, kein Push, keine Bauentscheidung getroffen. Nächster Schritt laut Auftrag: **STOPP** — Yama prüft die PV-Inventur und entscheidet AP-3 bzw. die nächsten PV-Slices.

*Ende AP-2.*
