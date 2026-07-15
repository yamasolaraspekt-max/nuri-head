# 3D-0A — Geometriequellen-Register

**Stand:** 2026-07-15 · **read-only** · **HEAD:** `59daa10`. Vollständiger Nachweis aller Geometrie-Stores + abgeleiteten/Nicht-Geometrie-Quellen über die drei Apps. Belege am Code.

## 1. Register (alle Quellen)

| ID | App | Pfad/Tabelle/Service | Geometrietyp | liest | schreibt | versioniert | Einheit | Koordinaten | produktiv | Zielstatus |
|---|---|---|---|---|---|---|---|---|---|---|
| **G1** | ticket | `anforderungsprofile.gebaeude_geometrie` (JSON, `Anforderungsprofil.php:33`; Schreiber `GrundrissController::schreibeGeometrieVersion:317`) | `raeume[]+bauteile[]` (abgeleitet) | Heizlast-Adapter | ja (neue Profil-Version) | **JA** (append-only, 1 aktiv/Objekt) | m² (Bauteil `flaeche_m2`) | keine Roh-Koord. mehr | **produktiv** (getestet) | **`kanonisch_behalten`** |
| **G2** | ticket | `raum_geometrien` (`RaumGeometrie`, Mig. `2026_07_08_180005`) | `polygon:[{x,y}]` + `wand_segmente[]` roh | Ableitung/Vorschau | nur transienter Vorschau-Pfad (`baueProjekt`, danach Cascade-Delete) | nein | **mm-Integer** | 2D kartesisch | **transient** (aktiver Save schreibt NICHT hierher) | **`in_kanonisch_migrieren`** (Roh-2D-Layer ins kanonische Modell heben) |
| **G3** | playground | `energie_roof_models.geometry_json` (`RoofModelController.php:11`, „reine Persistenz") | opaker Three.js-Frontend-State | Planer-Bundle | ja (Upsert je Objekt) | nein (nur `is array`-Check) | unklar (Frontend) | Frontend | **produktiv** (Bundle) | **`entscheidung_erforderlich`** (Schema unbekannt; nicht kanonisch ohne Source) |
| **G4** | playground | `roof_templates.config_json` | Szenen-/Vorlagen-State | Planer | ja | nein | unklar | Frontend | produktiv | **`nur_testfixture`/`entscheidung`** (Vorlagen; K4) |
| **G5** | wberechnung | `raum_geometrien` (Mig. `2026_06_27_131437`) | wie G2 | Heizlast | ja | nein | mm | 2D | produktiv (wb) | **`verwerfen` (Mirror)** — identisch zu G2, nicht doppelt |
| **A1** | ticket | `RoofAreaEstimator` (`app/Services/RoofAreaEstimator.php`) | Dachfläche m² (Schätzer) | DB/OSM/Fallback | **nein** (kein Persist) | – | m² | Lat/Lon→Meter | produktiv (Schätzer) | **`nur_lesen`** (schwach, keine Tests) |
| **A2** | ticket | `p_v_roof_plans.roof_structures` + `PVRoof` (`roof_pitch/azimuth/height`) | **skalare Dachmaße**, kein Polygon | PV-Angebot | ja | nein | Grad/m (skalar) | – | produktiv (PV) | **`entscheidung_erforderlich`** (PV-Dach separat; später an kanonisches Dach andocken) |
| **A3** | ticket | `sanierungs_varianten.massnahmen` (`SanierungsVariante`) | **keine Geometrie** (Maßnahmen `{ziel,u_neu,…}`) | Sanierung | ja | nein | U-Werte | – | produktiv | **`nur_lesen`** (kein Geometrie-Store) |
| **A4** | playground | `RoofTemplateFeatureExtractor::roofArea` (Grundriss/cos) | abgeleitete Dachfläche | Matching | nein | – | m² | – | produktiv | **`nur_lesen`** (Näherung, „kein Aufmaß") |
| **A5** | playground | `PvBelegungExtractor` (kWp aus Belegung) | abgeleitet (keine Geometrie) | PV | nein | – | kWp | – | produktiv | `nur_lesen` |

## 2. Bewertung „drei schreibbare + zwei abgeleitete" (ticket-intern, aus `docs/configuration/`) — bestätigt + app-übergreifend ergänzt
- **ticket schreibbare Geometrie-Stores:** G1 (führend), G2 (transient). *(A2 `p_v_roof_plans` ist skalar, kein Polygon → kein vollwertiger Geometrie-Store; A3 ist keine Geometrie.)* → intern **2 echte + PV-skalar**; die frühere Zählung „3 schreibbar" bezog `p_v_roof_plans` mit ein, das hier präziser als **skalar** eingeordnet wird.
- **ticket abgeleitet:** A1 `RoofAreaEstimator`, A3/A2 (Nicht-Polygon).
- **App-übergreifend zusätzlich:** **G3** (playground opaker State) + **G5** (wberechnung Mirror). → **Gesamt schreibbare Geometrie-Stores: G1, G2, G3** (G5 = Mirror von G2). **Grundrisiko bestätigt: ≥2 unabhängig schreibbare Geometrien fürs selbe Objekt** (G1 ticket vs. G3 playground) — Konsolidierung nötig.

## 3. Modell-/Prozessbezug je Quelle
| ID | Objektbezug | Anforderungsprofil | Heizlast | PV | Dach | Version/Revision | Ownership | Recalc/Stale |
|---|---|---|---|---|---|---|---|---|
| G1 | **ja** (`LeadAlternativeAdd`) | **führend** | ja (Adapter) | nein | nein | append-only Version | created_by | kein Trigger; via neue Version |
| G2 | indirekt (über Projekt) | – | ja (Zulieferer) | nein | nein | keine | – | transient (Cascade-Delete) |
| G3 | `object_id`/`customer_id` (playground-Modell) | nein | nein | ja | ja | keine | RBAC/Sichtbarkeit | – |
| A1 | Standort/PLZ | nein | nein | ja (Fläche) | ja (Schätzung) | – | – | – |
| A2 | ja (PVRoof) | nein | nein | ja | skalar | keine | – | – |

## 4. Konsolidierungs-Kernaussage
- **Kanonisch führen:** **G1** (`gebaeude_geometrie`) — objektverankert, versioniert, gate-geschützt. G0c-2 bleibt erhalten.
- **G2** als Roh-2D-Editierschicht ins kanonische Modell heben (Z/Geschoss additiv), danach read-only.
- **G3/G4** (playground) nur nach Schema-Rekonstruktion + Objekt-Mapping; **kein** paralleler Schreibpfad → sonst zweite Wahrheit.
- **A1/A2/A4** bleiben abgeleitet/lesend; PV-Dach (A2) später an das **eine** kanonische Dach andocken (kein zweites PV-Dachmodell).
- **G5** (wberechnung) = Mirror → nicht übernehmen.

*(Objekt-Zuordnung, Migrationsreihenfolge, Dual-Read/Single-Write: siehe `3d-0c-mapping-und-migrationskonzept.md`.)*
