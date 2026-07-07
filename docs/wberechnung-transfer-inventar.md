# wberechnung → ticket — Vollständige Transfer-Inventur (Funktionalität)

> Belegte Checkliste JEDES wberechnung-Service (Datei-Scan 2026-07-07: DB/Http-Refs + Zeilen).
> Zweck: „nichts Nutzbares bleibt bei wberechnung liegen." Datenimporte sind separat ~99 % erledigt
> (siehe `docs/wberechnung-transfer-datenmatrix` bzw. `katalog-reconciliation-plan.md §8`). Hier geht es
> um **Rechenkerne + Wirtschaftlichkeit + Features**. Verdikt-Legende:
> **[ERLEDIGT]** schon in ticket · **[REUSE]** ticket-Äquivalent vorhanden · **[PORT]** DB-frei, direkt portierbar
> (ggf. Contract-Swap Waermepumpe→WpKennlinie) · **[ADAPTER]** DB-/Http-gebunden, braucht Naht · **[UI]** eigener
> Blade-Bau · **[WELLE]** eigener großer Posten · **[SKIP]** kein Nutzen.

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
