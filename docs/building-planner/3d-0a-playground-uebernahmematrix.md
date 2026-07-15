# 3D-0A — Übernahmematrix (playground + wberechnung) & Berechnungsfunktionen

**Stand:** 2026-07-15 · **read-only** · **HEAD:** `59daa10`. Kein Code portiert, kein Package installiert.

## 1. Übernahmematrix (C1)

| ID | Quelle | Baustein | Zweck | Qualität | Abhängigkeiten | Ziel in ticket | Entscheidung | Begründung | Folge-Slice |
|---|---|---|---|---|---|---|---|---|---|
| U1 | playground | **3D-Planer-Bundle** `public/planer/planer.js`+GLB | React19/Three r163 3D-Dachplaner | produktiv, aber **nur Binär** | Frontend-Source fehlt | 3D-Viewer/Editor | **`entscheidung_erforderlich` + `nur_als_referenz`** | ohne `src/`+Vite-Config nicht wartbar/portierbar; Blackbox | 3D-3 |
| U2 | playground | Blade-Insel-Muster `dachplaner-pro/index.blade.php`+Controller | Vollbild-Insel ohne AppShell | sauber | – | Insel-Muster | **`nur_als_referenz`** (Framework = Yama) | React/Three ist NEUE Tech-Insel (ticket=Blade/jQuery) | 3D-2A |
| U3 | playground | Persistenz-Schema `energie_roof_models`/`roof_templates`/`energie_pv_plannings`+Controller | JSON-Wahrheit + abgeleitete Feature-Spalten | gut (SSOT) | Objekt-Mapping | Dach-/Planer-Persistenz | **`angepasst_portieren`** | additiv, RBAC; `object_id/customer_id`→ticket-Objekt mappen; `geometry_json` validieren statt `is array` | 3D-0C/3D-4 |
| U4 | playground | Extraktoren `RoofTemplateFeatureExtractor`, `PvBelegungExtractor` | SSOT-Ableitung aus State | klein, sauber (roofArea Näherung) | – | Feature-Ableitung | **`angepasst_portieren`** | `roofArea` als „Näherung, kein Aufmaß" kennzeichnen; Test nachrüsten | 3D-4 |
| U5 | playground | `roof_tiles` + Ziegel-Import + `BiberMontageSeeder` | verifizierte Dachziegel-/Montage-Stammdaten | **K1 belastbar** (verified) | Katalog | Katalog (EIN Katalog) | **`daten_migrieren`** | echte Stammdaten; mit ticket-Katalog abgleichen (keine 2. Wahrheit) | eigener Katalog-Slice |
| U6 | playground | GLB-Ziegelmodelle `public/planer/models/tiles/*` | 3D-Assets | K4 | an Ziegel-Slugs | Assets | **`daten_migrieren`** (falls U1 übernommen) | klein, gekoppelt | 3D-4 |
| U7 | playground | `SolarController` (Google-Solar-Proxy) | buildingInsights/Geocoding | geschlossen, Key serverseitig | Google-Lizenz/Key | Dach-Vorbefüllung | **`über_service_anbinden` + `lizenz_prüfen`** | Google-Nutzungsbedingungen + Key-Restriktion (Go-live) | 3D-4/später |
| U8 | playground | MODULE_TYPES hartkodiert im Bundle (Trina/LONGi/watts) | Modul-Presets | **K5 zweite Wahrheit** | – | – | **`nicht_übernehmen`** | Module aus ticket-Katalog, nicht hartkodiert | – |
| U9 | playground | Preset-Demo-Gebäude im Bundle | Demo | K3 | – | – | **`nur_als_referenz`** | reine Demo | – |
| U10 | playground | SolarProdukte-/Dachaufbau-/Flachdach-Seeder (`to_verify/verified=0`) | Auswahlkataloge | K2 | Datenblatt-Abgleich | – | **`als_testfixture_übernehmen` / `entscheidung_erforderlich`** | nicht ungeprüft produktiv | – |
| U11 | playground | Erkennungs-Grundgerüst `erkennung_*` | Freigabe-Skelett (keine OCR/Vision) | Muster | – | Vorschlags-/Freigabe-Muster | **`nur_als_referenz`** | „NIEMALS Auto-Übernahme"-Muster für Scan-/Import-Vorschläge | 3D-5 |
| U12 | playground | Energie-Rechenservices (Heizlast/WR/PV-Perf/Kabel/Schutz/Förderung/Lastprofil/Lastmgmt/Versorgung/Konfigurator) | Fachrechnung | echt+getestet | – | – | **`nur_als_referenz`** (eigener Strang) | kein 3D-Bezug; NICHT im Planer-Scope vermischen | separater Posten |
| U13 | wberechnung | `snap.js`/`freihand.js` (pure 2D-Geo) | Shoelace/Snap/Ortho/Normale/Douglas-Peucker | pure, getestet | – | 2D-Grundriss-Mathe | **`nur_als_referenz`** (ggf. `angepasst_portieren`) | ticket hat eigene Inline-Logik → erst Divergenz klären (2. Wahrheit vermeiden) | 3D-2B |
| U14 | wberechnung | Import-Jobs `PlanVektorExtrahieren`/`PlanPdfExtrahieren`/`PlanBildVermessen`+`MassstabVorschlagService` | DXF/PDF/Bild→Kandidaten | vorhanden (wb) | Python-Service | Import-Pipeline | **`entscheidung_erforderlich`** | ticket hat nur `PlanKlassifizieren`; klären ob Lücke oder bewusst | Import-Slice |
| U15 | wberechnung | `import-service/` (Python) | DXF/PDF/OCR | Mirror in ticket | pymupdf AGPL | – | **`über_service_anbinden` (bereits in ticket)** + **`lizenz_prüfen`** | nicht neu aufsetzen; für 3D ggf. DXF-Z/IFC ergänzen; **PyMuPDF AGPL** | Import-Slice |
| U16 | wberechnung | Heizlast-/Geometrie-Kern | Bauphysik | **Mirror** | – | – | **`nicht_übernehmen` (bereits portiert)** | schon in ticket; nur Divergenz-Abgleich | – |
| U17 | wberechnung | `klima_plz.csv`, WP-Daten | Fachdaten | K1 | – | Katalog/DB | **`daten_migrieren` (klima bereits erfolgt)** | Klima schon DB in ticket; WP-Katalog-Cutover teils erfolgt | Katalog |

## 2. Berechnungsfunktionen-Matrix (B9)

| Funktion | App | Implementierung | Aufrufer | Tests | Belastbarkeit | Fehler/Risiko | Ziel |
|---|---|---|---|---|---|---|---|
| Polygonfläche (Shoelace) | ticket | `GeometrieAbleitungService::polygonFlaecheM2:118` | Controller/Gate | ja (25/42 m²) | belastbar | – | behalten |
| Polygonfläche | wberechnung | `GeometrieAbleitungService:112` | Heizlast | ja | belastbar | Mirror | – |
| Polygonfläche | playground | Bundle (`ShapeGeometry`) | Planer | – | nur binär | Quelle fehlt | Referenz |
| Umfang | ticket | – (kein Getter) | – | – | **Lücke** | – | 3D-Modell ergänzen |
| Orientierung/Selbstschnitt | ticket | `TopologieGate::orientierung/segmenteSchneiden` | Gate R4/R5 | ja (bowtie) | belastbar | – | behalten |
| Entartung/Punktgleichheit | ticket | `TopologieGate::dedupliziere` (Tol 1mm) | Gate | ja | belastbar | – | behalten |
| Segmentlänge | ticket/wb | `segmentLaengeMm` | Wandfläche | indirekt | belastbar | – | behalten |
| Wandfläche (brutto) | ticket/wb | `ausGeometrie:42` | Heizlast | ja (15 m²) | belastbar | – | behalten |
| Öffnungsabzug (netto) | ticket/wb | `RaumHuelleService::effektiveBauteile` | Heizlast | ja | belastbar | – | behalten |
| Raumfläche/-volumen | ticket | Grundfläche×Höhe (implizit) | Lüftung | teilweise | teilweise | kein Volumen-Getter | 3D ergänzen |
| Dachfläche | ticket | `RoofAreaEstimator` (cos-Heuristik) | Wirtschaftlichkeit | **nein** | **schwach** | Default 30°, OSM-Bounding | ersetzen (kanonisches Dach) |
| Dachfläche | playground | `RoofTemplateFeatureExtractor` (Grundriss/cos) | Matching | nein | Näherung | „kein Aufmaß" | Referenz |
| Dachneigung | ticket | `parsePitchDegrees`/`PVRoof.roof_pitch` | PV | – | schwach | – | kanonisches Dach |
| Azimut/Nordwinkel | ticket | `wand_segmente.azimut_grad` (optional) | Heizlast | – | **Lücke** | kein Auto-Nordwinkel | 3D ergänzen |
| Azimut | wberechnung | `resources/js/grundriss/snap.js:95` (Nord=0) **vs** `app/Services/Energie/InverterSizingService.php:69` (Süd=0, PVGIS) | 2D/PV | teils | **Konflikt** | **2 Konventionen** | vereinheitlichen (0B/0C) |
| Bounding Box | ticket | `IMPORT.bbox`/`fitToBboxUnits` + DXF | Import-Einpassung | – | belastbar (Import) | nie als Dachfläche | Import |
| Extrusion | ticket/playground | nur Prototyp/Bundle | – | – | **nicht produktiv** | – | 3D neu (aus 2D+Höhe) |
| CSG (Öffnungen/Gauben) | playground | Bundle `SUBTRACT` | Planer | – | nur binär | Quelle fehlt | 3D neu/Referenz |
| Modulbelegung/Verschattung | playground | Bundle | Planer | – | nur binär | hartkodierte Module (U8) | 3D-4/Referenz |
| Koordinatentransformation | ticket | `lonLatToMeters` (WebMercator) | Schätzer/Editor | – | belastbar | – | behalten |
| Importkalibrierung | ticket | Zwei-Punkt `applyCalibration` + Gate 1–100 m | Editor | nur Client | teilweise | kein Server-Test | Import |

## 3. Zählung (für Abschlussbericht)
- Übernahmeentscheidungen: `direkt_wiederverwenden` **0** · `angepasst_portieren` **3** (U3,U4,U13*) · `über_service_anbinden` **2** (U7,U15) · `nur_als_referenz` **6** (U1,U2,U9,U11,U12,U13) · `als_testfixture` **1** (U10) · `daten_migrieren` **3** (U5,U6,U17) · `nicht_übernehmen` **3** (U8,U16 + G5) · `lizenz_prüfen` **2** (U7,U15) · `entscheidung_erforderlich` **3** (U1,U10,U14). *(U13 doppelt gelistet: primär Referenz.)*
- Ungeklärte Lizenzen: **2** (PyMuPDF AGPL, Google-Solar).
