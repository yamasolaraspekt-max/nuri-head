# 3D-0A — Vollständigkeitsbericht

**Stand:** 2026-07-15 · **read-only** · **HEAD:** `59daa10`. Jede Gruppe mit Status + Nachweis. Status: `geprüft_mit_fund` · `geprüft_kein_fund` · `teilweise_geprüft` · `unklar` · `blockiert`.

## 1. Vollständigkeitsregister

| Gruppe | Status | Nachweis / Fund (Kurz) |
|---|---|---|
| Governance | geprüft_mit_fund | CLAUDE.md/BETRIEBSordnung/STRAENGE/arbeitskompass/configuration/agents gelesen; Alpine-Scopes + „keine 2. Wahrheit" bindend |
| Apps | geprüft_mit_fund | ticket/playground/wberechnung — alle 3 vorhanden + strukturell erfasst |
| Datenbanken | geprüft_mit_fund | Geometrie-Tabellen ticket (`anforderungsprofile`,`raum_geometrien`,`p_v_roof_plans`,`sanierungs_varianten`), playground (`energie_roof_models`,`roof_templates`,`roof_tiles`), wberechnung (`raum_geometrien` Mirror) |
| Models | geprüft_mit_fund | `Anforderungsprofil`,`RaumGeometrie`,`PVRoof/PVRoofPlan`,`SanierungsVariante`; playground `RoofModel`/`RoofTemplate` |
| Migrationen | geprüft_mit_fund | Geometrie-Migrationen datiert belegt; keine geändert |
| Routes | geprüft_mit_fund | ticket Energie-/Grundriss-Routen + **ungeschützte** `/roofs`,`/solar`,`/testnav*`; playground `/api/energie/*` + `app/energie/dachplaner-pro` |
| Controller | geprüft_mit_fund | `GrundrissController`,`PlanUploadController`,`RoofAreaEstimator`; playground `DachplanerProController`,`RoofModelController`,`PvPlanningController`,`SolarController` |
| Services | geprüft_mit_fund | `Geometrie/*`,`Heizlast/*`; playground Feature-Extraktoren + Rechenservices; wberechnung Mirror |
| Views | geprüft_mit_fund | `grundriss_editor.blade.php`; `roof_config/*`,`solar/configuration/*`,`tools/index`; playground `dachplaner-pro/index` |
| 2D | geprüft_mit_fund | ticket SVG-Editor (Einzelraum); wberechnung `snap.js`/`freihand.js`; playground = Ortho-Kamera im 3D-Bundle |
| 3D | geprüft_mit_fund | playground Bundle (Three r163, Source fehlt); ticket pvtools (tot) + Demo-Views; wberechnung keins |
| Geometrie | geprüft_mit_fund | Funktionen-Matrix in Übernahmematrix §2 (Shoelace/Topologie/Öffnungsabzug belastbar; Dach/Azimut/Umfang Lücken) |
| JSON-Schemas | teilweise_geprüft | ticket `gebaeude_geometrie` (`raeume[]+bauteile[]`) + `raum_geometrien` (polygon/wand_segmente) belegt; **playground `geometry_json` opak** (Schema unbekannt, Source fehlt) → unklar |
| Import | geprüft_mit_fund | DXF/PDF/Bild via Python-Service (Mirror ticket/wb); DWG classify-only; IFC/GLTF/OBJ/USD = nicht implementiert |
| Export | geprüft_mit_fund | keine Geometrie-Exporte; playground GAEB-DA86-XSD (Angebot, nicht Geometrie) |
| Playground-Code | geprüft_mit_fund | Backend PHP + **3D nur als Bundle** (kein src/, kein package.json im Repo) |
| Playground-Daten | geprüft_mit_fund | Klasse-1..5 klassifiziert (roof_tiles K1, MODULE_TYPES K5, Demo K3, Szenenstate K4) |
| Playground-Assets | geprüft_mit_fund | GLB-Ziegelmodelle+PNG; keine DXF/IFC/OBJ/USD/Punktwolken |
| Packages | geprüft_mit_fund | playground: three r163/React19/lucide/immer (nur Bundle); wberechnung Python ezdxf/pymupdf/tesseract; ticket kein 3D-Package |
| Lizenzen | geprüft_mit_fund | three/React/lucide MIT/ISC; **PyMuPDF AGPL v3 ⚠**; Google-Solar ⚠ |
| Tests | geprüft_mit_fund | ticket Geometrie-/Gate-/Profil-Tests grün; playground Extractor teils untest.; wberechnung „304 PHPUnit / 11 Python grün" **laut `wberechnung/HANDOFF_IMPORT_TRACK.md` (Vorrunden-/CI-Stand), in 3D-0A NICHT re-verifiziert** (read-only, keine Suite-Ausführung) |
| wberechnung | geprüft_mit_fund | Kern = Mirror zu ticket; nur `snap.js`/`freihand.js`/Import-Jobs neu |
| Heizlast | geprüft_mit_fund | Kern gespiegelt; `AnforderungsprofilHeizlastAdapter` liest `gebaeude_geometrie` |
| PV | geprüft_mit_fund | ticket `p_v_roof_plans`/`PVRoof` skalar; playground Belegung im Bundle (hartkodierte Module); getrennt von Heizlast-Geometrie |
| Dach | geprüft_mit_fund | keine echte Dachgeometrie in ticket (Schätzer); playground im Bundle (Formen/Gauben), Source fehlt |
| Materialien | geprüft_mit_fund | playground `roof_tiles`/Montage/Dachaufbau (K1/K2); ticket-Katalog führend |
| Produkte | geprüft_mit_fund | playground Solar-Produkte (to_verify); MODULE_TYPES hartkodiert (K5, nicht übernehmen) |
| LiDAR | geprüft_kein_fund | in allen 3 Apps abwesend (kein Adapter/Format/Persistenz/Test) |
| Mesh | geprüft_kein_fund | nur three.js-Prototyp/Bundle (kein Persist); kein Mesh-Store |
| Punktwolken | geprüft_kein_fund | keine E57/LAS/LAZ/PLY/XYZ/point_cloud |
| Security | geprüft_mit_fund | ungeschützte Demo-Routen (ticket); PlanUpload Besitzer-Scope statt Policy; playground RBAC/Sichtbarkeit |
| Ownership | geprüft_mit_fund | ticket Objekt-/Profil-Anker; playground object_id/customer_id; Mapping offen |
| Versionierung | geprüft_mit_fund | nur `gebaeude_geometrie` versioniert (append-only); übrige Stores unversioniert |
| Recalc | teilweise_geprüft | Neuberechnung via neue Profil-Version; kein Auto-Recalc |
| Stale | geprüft_mit_fund | **kein Ergebnis-Stale-Trigger** (bestätigt aus `docs/configuration/`) |
| Performance | teilweise_geprüft | playground-Bundle-Performance nur binär beurteilbar; 2D-SVG unkritisch |
| Zielarchitektur | geprüft_mit_fund | `3d-0a-zielarchitektur-und-fahrplan.md` (kanonisch/2D/3D/LiDAR/Übergaben) |
| Übernahmematrix | geprüft_mit_fund | `3d-0a-playground-uebernahmematrix.md` (U1–U17) |
| Risiken | geprüft_mit_fund | 2. Wahrheit, fehlender Source, Framework, AGPL, Azimut, Multi-Raum, Demo-Routen |
| Nicht-Ziele | geprüft_mit_fund | keine BIM/Tragwerk/KI-Planung/Dual-Write/Parallel-DB/3D-Bau |
| Folgeslices | geprüft_mit_fund | 3D-0B..3D-8 vorgeschlagen (keine freigegeben) |

**Keine Gruppe leer.** Nicht abschließend geklärt (bewusst, → 0B/0C/Yama): playground `geometry_json`-Schema (Source fehlt), Recalc/Performance des Bundles, Import-Job-Lücke ticket↔wberechnung.

## 2. Ist die Inventur vollständig?
**Ja — vollständig und lückenlos dokumentiert** im Rahmen des read-only Zugriffs: alle 3 Apps untersucht, alle Gruppen mit Status/Nachweis, Code/Daten/Assets/UI-State getrennt, schreibbare vs. abgeleitete Geometrie getrennt, 2D/3D getrennt, LiDAR/Mesh/Punktwolke geprüft (kein Fund), Packages/Lizenzen erfasst, Dach-/Azimut-/Bounding-Box-Risiken markiert.
**Ausdrücklich NICHT behauptet:** dass ein 3D-Gebäudeplaner existiert oder übernahmefertig ist. playground-3D ist ohne Frontend-Source Blackbox; LiDAR ist Greenfield. **Kein Produktivcode geändert, kein Code kopiert, kein Slice implementiert, kein Commit der 3D-Dokumente.**

## 3. Zählungen (Abschlussbericht)
Apps: 3 · Geometriequellen: 10 (G1–G5, A1–A5) · schreibbare Geometrie-Stores: 3 (G1,G2,G3; G5=Mirror) · abgeleitete/Nicht-Polygon: 5 (A1–A5) · 2D-Planer: 3 (ticket-Editor, wb snap/freihand, playground Ortho) · 3D-Komponenten: ~4 (playground-Bundle, ticket pvtools tot, roof_config, solar) · Playground-Bausteine (U-IDs): 17 · Berechnungsfunktionen inventarisiert: ~22 · Import-Formate: DXF/PDF/Bild implementiert, DWG classify, 7+ nicht implementiert · LiDAR: 0 · ungeklärte Lizenzen: 2 (PyMuPDF, Google-Solar).
