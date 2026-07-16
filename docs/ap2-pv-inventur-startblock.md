# Startblock — AP-2: PV-Funktionsinventur (read-only)

**Stand:** 2026-07-14 · **read-only Vorbereitung — KEIN Bau, kein Commit, kein Push, keine Migration, kein Seeder.**
**Kapitel:** 1/8 (Landkarte / 3D-PV). **Grundlage:** `docs/gesamtfahrplan-gebaeude-energie-angebot.md` §8 (AP-2), §9/10; Gap-Analyse `docs/bereich2-gebaeude-energie-konfigurationsplattform-gap-analyse.md` §9.

## Ziel
Vollständige, ehrliche read-only Inventur **aller** PV-Funktionen in ticket, playground und wberechnung — als Voraussetzung für jede PV-Bauentscheidung (3D-PV, PV am gemeinsamen Kern). PV ist heute der schwächste, fragmentierteste Strang und **nirgends am versionierten Kern** geführt.

## Warum jetzt (und warum read-only)
- Blockiert nichts, klärt aber L-6 (PV nicht am Kern) + die 3D-PV-Migrations-Entscheidung (AP-9).
- Analog zur WP-Auslegungswizard-Gap-Analyse: erst wissen, dann bauen.

## Ergebnisdokument
`docs/bereich2-pv-funktionsinventur.md` (neu, read-only).

## Analyseumfang / konkret zu erfassen
**ticket:**
- Models: `SolarSystem`, `ProductPV`/`product_pv_module_specs`, `Inverter`, `Battery`/`BatterySystem`/`BatteryInverter`, `PowerOptimizer`, `PVRoof`/`PVLongRoof`/`PVRoofPlan`, `OfferRoofLayoutConfiguration`, `ProfitabilityCalculation`/`ProfitabilityData`/`EconomicAssumption`/`EconomicCalculation`, `PVChecklist`/`PVLongChecklist`, `PVTools`.
- Services: `Energie/InverterSizingService`, `PvProjektService` (0 Aufrufer?), `PvgisErtragService`, `KostenService`, `RoofAreaEstimator`, `GoogleGeocoder`; DTOs/Contracts `Energie/Dto/*`, `Energie/Contracts/*`.
- Controller/Views: `PVToolsController` (Legacy-PVGIS), `SolarSystemController`, `Economic*`/`Profitability*Controller`, `OfferRoofLayoutConfigurationController`; three.js-Editoren `roof_config/*`, `solar/configuration/*`.
- **Doppelspur ausdrücklich klären:** `PVToolsController::fetchByPostcode` (Legacy-PVGIS) vs. `PvgisErtragService` (kanonisch) — welche ist führend, wo wird welche aufgerufen?
- **Altlast bestätigen:** `Radiator.php` = Wechselrichter (nicht Heizkörper); Abgrenzung zu `RadiatorSpec`.
- **Kern-Frage:** Gibt es PV-Schlüssel in `SchluesselRegistry` (kwp, spez. Ertrag, Autarkie …)? PV-Adapter? PV-Persistenz übers `Anforderungsprofil`? (Erwartung: nein.)

**playground (Vergleich/Kandidaten, nicht importieren):** `Auslegung`/`AuslegungMppt`/`AuslegungString`/`AuslegungErgebnis`, `StringBuilderService`, `SchutzkomponentenService`, `KabelService`, `EpsBoxService`, `PerformanceService`, `WirtschaftlichkeitEngine`, `KonfiguratorAngebotService`, 3D-Planer (`DachplanerProPage.tsx`, `src/utils/*`, `PvBelegungExtractor`, `RoofTemplate`/`config_json`), `SolarController::buildingInsights`.

**wberechnung (Rest-Referenz):** `PvProjektService`, `StringBuilderService`, `InverterSuggestionService`.

## Vorgehensweise (read-only)
1. ticket-PV-Modelle/Services/Controller/Views/Tabellen vollständig auflisten (Pfade + kurze Rolle).
2. Für jede Funktion: führende Wahrheit? persistiert/transient? am `Anforderungsprofil`-Kern? Doppelspur?
3. playground/wberechnung nur als Vergleich (Übernehmbarkeit, Kopplung, Stack).
4. Ergebnis: (A) ticket-PV-Karte, (B) Doppelspuren/Altlasten, (C) „PV am Kern"-Gap, (D) Übernahme-Kandidaten mit Risiko, (E) Empfehlung nächster PV-Schritt.

## Nicht-Ziele
Kein Bau, keine Migration, kein Seeder, keine Doppelspur-Auflösung, keine 3D-Migration, keine PV-Schlüssel/Adapter-Erstellung — nur Inventur.

## Risiken / Stop
- Reiner Lese-Auftrag; kein STOP-Kriterium außer „Inventur vollständig".
- Falls beim Lesen ein Schreibbedarf/Datenproblem auffällt → nur **dokumentieren**, nicht handeln.

## Yama-Abnahme
Nicht erforderlich für die reine Inventur; erforderlich vor jedem daraus folgenden Bau. Evaluator (falls genutzt) strikt read-only.

*Ende Startblock AP-2.*
