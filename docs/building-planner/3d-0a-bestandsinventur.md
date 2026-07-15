# 3D-0A — Bestandsinventur (ticket · playground · wberechnung)

**Stand:** 2026-07-15 · **read-only** (kein Produktivcode, kein Commit, kein Push, kein Code portiert) · **HEAD:** `59daa10`.
**Grundlage:** drei parallele codebelegte Inventuren. Governance gelesen/berücksichtigt: BETRIEBSORDNUNG, `CLAUDE.md`, `STRAENGE.md`, `docs/arbeitskompass-ticket.md`, `docs/configuration/*`, `docs/agents/04+05`, `docs/systemoptimierung-fahrplan.md`, AP-4-/G0-Manifeste, ADR-0001.

---

## 0. Kernbild (drei Welten)

1. **ticket — Heizlast-2D-Welt (produktiv, gegated, getestet, objektverankert):** Grundriss-Editor (SVG+jQuery) → `raum_geometrien` (transient) → **`anforderungsprofile.gebaeude_geometrie` (führend, versioniert)** → Heizlast. mm-Integer, Shoelace, **`TopologieGate`** (7 Regeln), `GeometrieToleranz`. **Kein 3D-Schreibpfad.**
2. **playground — 3D-Dachplaner (produktiv, aber nur als Binär-Bundle):** `public/planer/planer.js` = **React 19 + Three.js r163**, Quellcode **nicht im Repo**. Backend = **reine Persistenz** (`energie_roof_models.geometry_json` = opaker Frontend-State). Echte 3D-Geometrie-Mathematik nur im fehlenden Frontend-Source.
3. **wberechnung — Greenfield für 3D:** **nichts 3D** (0 Treffer three/mesh/webgl/gltf/lidar). Geometrie-/Heizlast-/Import-Kern **bereits nach ticket gespiegelt** (Mirror — strukturell belegt, Divergenz **noch nicht zeilengleich per Diff** bestätigt) — nicht doppelt übernehmen. Nur-in-wberechnung: `snap.js`/`freihand.js` (pure 2D-Geo-Helfer), Import-Jobs.

**Fazit:** Die **einzige belastbare, führende, versionierte, objektverankerte Geometrie-Wahrheit** ist `anforderungsprofile.gebaeude_geometrie` (ticket). Ein 3D-Planer dockt hier an; er baut keine zweite Wahrheit daneben.

---

## 1. Apps (Struktur)
| App | Pfad | Stack | 3D? | Rolle für 3D-Planer |
|---|---|---|---|---|
| ticket | `/Users/yamanuri/Documents/ticket` | Laravel 11, PHP 8.4, Blade+jQuery+Bootstrap, Alpine nur 2 Scopes | nur Demo-Prototypen | **Heimat**; führende 2D-Geometrie + Gate |
| playground | `/Users/yamanuri/Documents/Playground/backend-laravel` | Laravel 11/12, Blade+jQuery, **React/Three-Insel als Bundle** | ja (Bundle, kein Source) | **Referenz + Persistenz-Schema**; Quellcode fehlt |
| wberechnung | `/Users/yamanuri/Herd/wberechnung` | Laravel 13, PHP 8.4, Blade+Alpine, SQLite, Python-Import-Service | nein | Kern gespiegelt; 2D-Geo-Helfer als Referenz |

## 2. Geometriequellen (Kurz; Vollregister s. `3d-0a-geometriequellen-register.md`)
- **Schreibbar/geometrisch:** ① `anforderungsprofile.gebaeude_geometrie` (ticket, führend, versioniert) · ② `raum_geometrien` (ticket, transient/Zulieferer, mm-roh) · ③ `energie_roof_models.geometry_json` (playground, opaker Frontend-State).
- **Mirror (nicht doppelt):** `raum_geometrien` (wberechnung) = identisch zu ①/②-Zulieferer.
- **Abgeleitet/keine Polygon-Geometrie:** `RoofAreaEstimator` (ticket, Schätzer, kein Persist), `p_v_roof_plans`/`PVRoof` (ticket, **skalare** Dachmaße, kein Polygon), `sanierungs_varianten.massnahmen` (ticket, **Maßnahmen-JSON, keine Geometrie**), `roof_templates.config_json` (playground, Szenenstate).

## 3. 2D-Inventur (Kurz; Details in Register)
- **ticket Grundriss-Editor** (`resources/views/admin/energie/grundriss_editor.blade.php`): SVG+jQuery, **mm-Integer**, Raster 500 mm, Snap, numerische Eingabe, Wand/Öffnungen/Fenster/Türen, Import-Underlay + Zwei-Punkt-Kalibrierung, Topologie-Gate Pflicht, objektgebundene Persistenz (422 ohne Objekt). **Status: produktiv, aber Einzelraum** (kein Multi-Raum, kein Stockwerk-Stack, kein Redo, keine raumübergreifende Topologie).
- **wberechnung `resources/js/grundriss/{snap.js,freihand.js}`**: pure ES-Module (Shoelace, Snap, Außennormale-Azimut, Douglas-Peucker, Ortho-Regularisierung), Node-getestet. **Nicht in ticket** (ticket hat eigene Inline-Logik). Status: produktiv (wberechnung) / Referenz.
- **playground**: **kein separater 2D-Planer** — 2D ist eine Ortho-Kameraansicht im selben 3D-Bundle.

## 4. 3D-Inventur (Kurz)
- **playground `public/planer/planer.js`**: React 19 + **Three.js r163 (MIT)**, `ExtrudeGeometry`/`ShapeGeometry`/CSG-`SUBTRACT`, Dachformen (sattel/walm/krüppelwalm/mansard/zelt/bogen/…), Gauben/First/Traufe, PV-Belegung/Verschattung. **Quellcode fehlt** → Blackbox. Backend schreibt nur opaken State. **Status: produktiv, aber nur binär prüfbar.**
- **ticket 3D**: `public/js/pvtools/*` (three r0.163, MIT) = **verwaister Prototyp** (Vite-Entry fehlt, `resources/js/pvtools/` leer → Belegungstool **defekt/tot**); `roof_config/roofs.blade.php` (three r0.161 CDN) + `solar/configuration/*` (three 0.126 CDN + GLTF) = **Demo an ungeschützten Test-Routen** (`/roofs`, `/solar`, `/testnav*` außerhalb `auth`). **Keine 3D-Komponente schreibt fachliche Geometrie.**
- **wberechnung**: kein 3D.
- **Pflichtantwort (B5):** Keine gefundene 3D-Komponente rendert aus einem *validierten fachlichen* Modell mit Rückschreibung in eine geprüfte Geometrie-Wahrheit; playground hält den Frontend-State opak (Round-Trip), schreibt ihn aber ungeprüft (`is array`) → **Risiko „zweite Wahrheit" bei ungeprüfter Übernahme**.

## 5. Geometriefunktionen (Kurz; Register hat die Matrix)
- **ticket belastbar+getestet:** Shoelace-Fläche, Segmentlänge, Wandfläche (brutto), Öffnungsabzug (`RaumHuelleService`), Selbstschnitt/Orientierung/Dedup (`TopologieGate`), Toleranzvertrag (`GeometrieToleranz`/`config/geometrie.php`), Import-Bounding-Box/Kalibrierung.
- **ticket schwach/Lücke:** Dachfläche/-neigung (`RoofAreaEstimator`, Heuristik cos(Neigung), **keine Tests**), Azimut/Nordwinkel (nur optionales Feld, kein Auto-Nordwinkel), Umfang (kein dedizierter Getter), Extrusion (nur in Prototypen).
- **playground:** echte Extrusion/CSG/Verschattung **nur im fehlenden Bundle-Source**; Backend nur triviale Ableitung (Dachfläche = Grundriss/cos, ausdrücklich „kein Aufmaß").
- **wberechnung:** Shoelace/Wandfläche/Öffnungsabzug/U-Wert/Heizlast **= Mirror zu ticket** (nicht doppelt); `snap.js` Azimut (Nord=0) ↔ `InverterSizingService` Azimut (Süd=0, PVGIS) → **zwei Konventionen**.

## 6. Import/Export (Kurz)
- **ticket + wberechnung**: **Mirror-Python-Service** `import-service/` (FastAPI, **ezdxf**+**PyMuPDF**+tesseract), Endpunkte `/extract/dxf`, `/extract/pdf`, `/rasterize/pdf`, `/ocr`; **graceful-off** ohne `IMPORT_SERVICE_URL`. Formate: **DXF ✓, PDF vektor+raster ✓, Bild ✓**; **DWG classify-only (kein Parser)**; **IFC/GLTF/OBJ/USD/E57/LAS/LAZ/PLY = nicht implementiert**. ticket hat nur `PlanKlassifizieren`; wberechnung zusätzlich Extraktions-Jobs + `MassstabVorschlagService`.
- **playground**: **kein** Format-Import; nur Google-Solar-API-Proxy (buildingInsights/Geocoding). GAEB-DA86-XSD = Angebots-Export (nicht Geometrie).
- **Lizenz-Risiko:** **PyMuPDF = AGPL v3 / kommerziell** → prüfen vor App-Vertrieb.

## 7. LiDAR/Scan/Punktwolke
**In allen drei Apps vollständig abwesend** (kein RoomPlan/ARKit/Mesh/Depth/USDZ/E57/LAS/PLY-Adapter, kein Zwischenformat, keine Persistenz, keine Tests, kein UI). **Grüne Wiese.** Zielkette (nur Architektur, nicht gebaut): Scanrohdaten → versioniertes Scan-Zwischenformat → Vorschläge → Kontrollmaße → Nutzerprüfung → **Topologie-Gate** → kanonisches Modell. **Mesh/Punktwolke wird nie ungeprüft führende Geometrie.**

## 8. Objekt-/Modul-Bezug + Versionierung/Stale
- **Anker = Objekt** (`lead_alternative_adds`); Geometrie liegt am **Anforderungsprofil** (append-only, 1 aktive Version je Objekt). Heizlast liest via `AnforderungsprofilHeizlastAdapter`. PV/Dach hängt **getrennt** (skalar, kein Polygon).
- **Stale/Recalc:** **kein** Ergebnis-Invalidierungs-Trigger (bestätigt aus `docs/configuration/`); Neuberechnung nur über neue Profil-Version. Offene Frage für 3D: Geschoss-/Mehrraum-Zusammenführung ist nicht modelliert (`geschoss` nur Skalar).

## 9. Kernbefunde
1. **Eine führende Wahrheit existiert** (`gebaeude_geometrie`) + **solides Gate-Fundament** (Topologie/Toleranz/Shoelace, mm-Integer, Wächter-Tests) — richtige Basis für 3D (Extrusion aus 2D-Polygon + Höhe).
2. **2D-Editor Einzelraum-beschränkt** — zentrale Ausbaulücke für Mehrraum/Geschoss-Gebäude.
3. **playground-3D ist produktiv, aber Quellcode fehlt** → ohne Frontend-Repo nur Blackbox/Referenz; **Framework-Grundsatzentscheidung (React/Three vs. Blade)** = Yama.
4. **wberechnung-Kern bereits gespiegelt** → nichts doppelt übernehmen; nur `snap.js`/`freihand.js` + Import-Jobs als Kandidaten.
5. **Kein 3D-Schreibpfad in ticket** → kein aktuelles Zweite-Wahrheit-Risiko; aber **defekter pvtools-Code** + **ungeschützte Demo-Routen** (`/roofs`,`/solar`,`/testnav*`) als Hygiene-/Sicherheitsnotiz.
6. **Playground-Risiken:** hartkodierte MODULE_TYPES (zweite Produkt-Wahrheit), opakes `geometry_json` (serverseitig ungeprüft), fehlende Extractor-Tests.
7. **Lizenz:** PyMuPDF (AGPL) offen.
8. **LiDAR = Greenfield** in allen Apps.

## 10. Offene Unklarheiten (→ 0B/0C-Entscheidungen)
- Wo liegt der playground-Planer-**Quellcode**? (ohne Source keine echte Übernahme)
- **Framework-Entscheidung** React/Three-Insel vs. Neubau (Yama).
- Multi-Raum/Geschoss-Modell + PV-Dach↔Heizlast-Geometrie vereinen?
- Azimut-Konvention vereinheitlichen (Nord=0 vs. Süd=0).
- PyMuPDF-Lizenz; Google-Solar-Lizenz/Key.
- Stale/Recalc-Politik bei Geometrieänderung.
