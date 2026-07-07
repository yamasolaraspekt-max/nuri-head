# wberechnung → ticket — Vollständige Transfer-Inventur (Funktionalität)

> Belegte Checkliste JEDES wberechnung-Service (Datei-Scan 2026-07-07: DB/Http-Refs + Zeilen).
> Zweck: „nichts Nutzbares bleibt bei wberechnung liegen." Datenimporte sind separat ~99 % erledigt
> (siehe `docs/wberechnung-transfer-datenmatrix` bzw. `katalog-reconciliation-plan.md §8`). Hier geht es
> um **Rechenkerne + Wirtschaftlichkeit + Features**. Verdikt-Legende:
> **[ERLEDIGT]** schon in ticket · **[REUSE]** ticket-Äquivalent vorhanden · **[PORT]** DB-frei, direkt portierbar
> (ggf. Contract-Swap Waermepumpe→WpKennlinie) · **[ADAPTER]** DB-/Http-gebunden, braucht Naht · **[UI]** eigener
> Blade-Bau · **[WELLE]** eigener großer Posten · **[SKIP]** kein Nutzen.

## ✅ FORTSCHRITT (Stand 2026-07-07, alles in main)
**Rechenkerne live:** InverterSizing, WpKennlinie(+Kurven), HeizlastRechner, Anforderungsprofil, Heizkörper-EN-442,
Hydraulik · **13 DB-freie Kerne portiert** (Jaz, Warmwasser, Verbrauch, Hoehenkorrektur, KlimaBin, HeizlastService,
HeizlastKonstanten, HeizlastEingabe, Bivalenz, Fussbodenheizung, **KostenService, FoerderungService/KfW, PvProjektService**) ·
**Bivalenz-Klima-Naht** (KlimaBinService) · **WaermepumpenMatchService** (Adapter auf CatalogDeviceRepository).
**Sichtbar/live:** WR-Auslegung (`/admin/energie/wr-auslegung`) + WP-Auslegung inkl. Wirtschaftlichkeit+KfW
(`/admin/energie/wp-auslegung`). **Daten:** Katalog + Referenz + klima_plz(8168) + Heizkörper(30) + WP-Kurven(19).
Tests: 73 Unit grün. KfW-Smoke 8.750€, Bivalenzpunkt −1,5°C/JAZ 3,53, WP-Match 5 Treffer.

**Heizlast-Projekt-Domäne GEBAUT (volltreu, 2026-07-07):** 4 Migrationen (heizlast_projekte→raeume→bauteile,
sanierungs_varianten) + Models + Konstruktion/Material/Baualtersklasse + Enum KonstruktionTyp. **UWertService,
HeizlastProjektService** (Nähte: RadiatorSpec/RadiatorPerformanceService/HydraulicService/KlimaPlz-findByPlz),
**SanierungsWirtschaftlichkeitService** (Amortisation/Einsparung/**BAFA**) portiert + End-to-End verifiziert
(Test-Projekt→Heizlast 1,32 kW→Sanierungs-Vergleich fehlerfrei). 81 Tests grün. Commit `ced8784`.

**UI LIVE (2026-07-07):** 3 Energie-Werkzeuge in Sidebar-Sektion „Energie" verlinkt + je kundenfertiges PDF:
`/admin/energie/wr-auslegung` (WR-String + PDF), `/admin/energie/wp-auslegung` (WP + Wirtschaftlichkeit + KfW + PDF),
`/admin/energie/sanierung` (Ist/Nachher-Heizlast + Amortisation/Einsparung/**BAFA** + PDF). Sanierung nutzt die
volle Heizlast-Projekt-Domäne transient (Cascade-Cleanup). 81 Tests grün.

**ENERGIEKONZEPT LIVE (2026-07-07):** `/admin/energie/energiekonzept` — fasst WP-Auslegung + Sanierung zu EINEM
kundenfertigen Beratungsangebot-PDF (Gesamt-Investition + Gesamt-Förderung KfW+BAFA + Eigenanteil). Nav-Eintrag gesetzt.
Deckt die AuslegungService-Orchestrierung fachlich ab (ohne den DB-/Geocoding-gebundenen wb-Orchestrator zu portieren).

**PV im Energiekonzept LIVE (2026-07-07):** `PvgisErtragService` (kanonisch, v5_3 PVcalc, klima_plz-lat/lon-Geocoding,
Graceful-Fallback ~950 kWh/kWp) — **keine Dopplung** (PVToolsController unangetastet). Energiekonzept deckt jetzt
**PV + Wärmepumpe + Sanierung** in einem Beratungsangebot-PDF (Gesamt-Investition/Förderung/Eigenanteil/Ersparnis).

**GRUNDRISS-WELLE GEBAUT (2026-07-08):** Kern `9ad45a9` (raum_geometrien + RaumGeometrie + GeometrieAbleitungService,
Polygon→Bauteile→Heizlast). **Editor `d9715e7`** — jQuery/SVG-Neubau (Yama-Wahl, governance-konform statt Alpine):
`/admin/energie/grundriss` — Raum zeichnen (Raster/Snap), Wand-/Öffnungs-/Decke-/Boden-Eigenschaften, „Vorschau"=Live-Heizlast
(transient), „Speichern" persistiert. Nav-Eintrag. Verifiziert 5×4m→20m²/7 Bauteile/2,87 kW.

**ZUSATZSEITEN GEBAUT (2026-07-08, `c129f6b`):** Materialliste (Referenz), Fußboden-Check (FussbodenheizungService),
Heizlast-Rechner (HeizlastProjektService, Ist-Heizlast + WP-Match) — 3 Controller + Views + Nav.

**NUR NOCH EXTERN OFFEN:** **Plan-Import** (dwg/dxf/pdf via `ImportServiceClient` + Async-Klassifizierung `PlanKlassifizieren`)
— echte externe Dienste, nicht in ticket (braucht Bereitstellung des Import-/OCR-Dienstes). Alt-Anmerkung Kleine Checks/Listen
(Materialliste, Fußboden-/Heizkörper-Check) — teils REUSE ticket. Externe APIs (OpenMeteo/Geocoding) = optional, klima_plz führt.
PVToolsController→PvgisErtragService-Delegation = späterer Konsolidierungs-Posten (eine Wahrheit).
*(Parallel-Instanz „Angebots-WP-Konfigurator" im git-Stash `stash@{0}`.)*

## A. Rechenkerne — Heizlast / Wärmepumpe / PV

| Service (Z.) | DBrefs | Verdikt | Anmerkung |
|---|---|---|---|
| InverterSizingService (658) | 0 | **ERLEDIGT** | portiert (Diff=0) |
| WpKennlinieService (216) | 0 | **ERLEDIGT** | portiert (+ Kurven-Daten) |
| HeizlastRechner (136) | 0 | **ERLEDIGT** | b2a-Port |
| HeizlastNormwerte (88) | 0 | **ERLEDIGT** | in ticket |
| RaumHuelleService (78) | 0 | **ERLEDIGT** | in ticket |
| KlimaPlzService (82) | 0 | **ERLEDIGT** | ticket-eigener + klima_plz |
| HeizkoerperService (121) | 0 | **REUSE** | = ticket `RadiatorPerformanceService` (EN-442) |
| HydraulikService (96) | 0 | **REUSE** | = ticket `HydraulicService` |
| **JazService (34)** | 0 | **PORT** | Jahresarbeitszahl — WP-Auslegung |
| **WarmwasserService (60)** | 0 | **PORT** | Warmwasser-Bedarf |
| **VerbrauchsService (65)** | 0 | **PORT** | Verbrauchsabschätzung |
| **HoehenkorrekturService (88)** | 0 | **PORT** | Höhen-/Druckkorrektur |
| **KlimaBinService (156)** | 0 | **PORT** | Temperatur-Bins (JAZ-Integration) |
| **HeizlastKonstanten (173)** | 0 | **PORT** | Konstanten/Support (von Kernen gebraucht) |
| **HeizlastEingabe (102)** | 0 | **PORT** | Eingabe-DTO |
| **HeizlastService (88)** | 0 | **PORT** | Heizlast-Aggregation |
| **BivalenzService (365)** | 0 | **PORT** | Bivalenzpunkt — Contract-Swap + Klima-Inject (ticket KlimaPlz/KlimaBin statt OpenMeteo) |
| **FussbodenheizungService (283)** | 0 | **PORT** | FBH-Auslegung |
| HeizlastProjektService (347) | 0 | **PORT** (prüfen) | DB-frei laut Scan; Projekt-Orchestrierung — Modelltyp-Hints prüfen |
| AuslegungHandoffService (71) | 0 | **PORT** | Übergabe-DTO WP-Auslegung |

## B. Wirtschaftlichkeit / PV-Projektierung  ← ausdrücklich mit übernehmen

| Service (Z.) | DBrefs | Verdikt | Anmerkung |
|---|---|---|---|
| **KostenService (48)** | 0 | **PORT** | Kostenrechnung (Investition/Betrieb) |
| **FoerderungService (156)** | 0 | **PORT** | Förderung (BEG/KfW/BAFA-Logik) |
| **PvProjektService (133)** | 0 | **PORT** | PV-Projekt-Dimensionierung + Ertrag/Wirtschaftlichkeit |
| **SanierungsWirtschaftlichkeitService (315)** | 2 | **ADAPTER** | **Sanierungs-Wirtschaftlichkeit** (Amortisation/Einsparung). Rechen-Kern portierbar; die 2 DB-Refs = U-Wert-Bezug (Artikel/FensterSpec/konstruktion) → auf ticket `konstruktionen`/Direkteingabe umbiegen (Fallback-Kette). Kern-Ökonomie NICHT DB-gebunden. |

## C. DB-/Http-gebundene Kerne — Adapter nötig (Naht wie CatalogDeviceRepository)

| Service (Z.) | DBrefs | Verdikt | Naht |
|---|---|---|---|
| WaermepumpenMatchService (104) | 1 | **ADAPTER** | `Waermepumpe::query()` → `CatalogDeviceRepository->heatPumps()` |
| UWertService (214) | 1 | **ADAPTER** | `FensterSpec` → konstruktionen/materials + Direkteingabe |
| GeometrieAbleitungService (185) | 2 | **ADAPTER** | Artikel/FensterSpec → Repository/Direkteingabe |
| AuslegungService (314) | 1 | **ADAPTER** | Orchestrator → dünner ticket-Controller nutzt Repo + KlimaPlz + portierte Kerne |
| WpHandoffService (63) | 1 | **ADAPTER** | Handoff/Persistenz — projekt-gebunden |
| OpenMeteoKlimaService (177) | 1 (Http) | **ADAPTER/EXTERN** | Live-Klima-API; Alternative = ticket klima_plz (bereits führend) |
| GeocodingService (103) | 3 (Http) | **EXTERN** | Adresse→Koordinaten (externer Dienst, nur Anbindung) |

## D. Features / Controller (eigener UI-Bau im ticket-Design)

`WrAuslegungController` **[ERLEDIGT — WR-Seite live]** · `WaermepumpeController` **[UI — Schritt C]** ·
`HeizlastController` (Heizlast-Eingabe) · `PvgisController` (PV-Ertragsplaner, PVGIS-API) ·
`SanierungController` (Sanierungs-Szenarien + Wirtschaftlichkeit) · `EnergiekonzeptController` (Energiekonzept-PDF) ·
`MateriallisteController` · `FussbodenCheckController` · `HeizkoerperCheckController` · `KatalogController` [REUSE ticket-Katalog].

## E. Eigene Welle (schwer)
`GrundrissController` + `PlanUploadController` (Grundriss-Editor / Plan-Import — Storage, Geometrie, evtl. 3D). **[WELLE]**

---

## Abarbeitungs-Reihenfolge (max. fachlicher Nutzen zuerst)
1. **PORT-Block Heizlast/WP** (A): Jaz, Warmwasser, Verbrauch, Hoehenkorrektur, KlimaBin, HeizlastKonstanten, HeizlastEingabe, HeizlastService, Bivalenz, AuslegungHandoff. → WP-Auslegung rechnet vollständig.
2. **PORT-Block Wirtschaftlichkeit/PV** (B): KostenService, FoerderungService, PvProjektService. → Angebots-/Wirtschaftlichkeits-Rechnung.
3. **FussbodenheizungService** (A).
4. **UI** (C/D): WP-Auslegungs-Seite, dann Heizlast-Eingabe, PV-Planer, Sanierung.
5. **ADAPTER-Block** (C): WaermepumpenMatch, UWert, Geometrie, SanierungsWirtschaftlichkeit, AuslegungService-Orchestrierung.
6. **WELLE**: Grundriss/Plan-Import.

**Nichts aus dieser Liste wird stillschweigend fallen gelassen** — [SKIP]/[REUSE] sind begründet, alles andere hat einen Posten.
</content>
