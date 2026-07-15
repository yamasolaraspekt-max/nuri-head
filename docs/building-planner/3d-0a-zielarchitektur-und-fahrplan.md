# 3D-0A — Zielarchitektur & Slice-Fahrplan (Vorschlag)

**Stand:** 2026-07-15 · **read-only Vorschlag** (keine Implementierung, keine Freigabe). **HEAD:** `59daa10`. Bindet ADR-0001 (Bedarf führt), `docs/configuration/*`, CLAUDE.md (Alpine-Scopes, keine 2. Wahrheit).

---

## 1. Zielbild (kanonisches Gebäudemodell)

**Ein** versioniertes, objektverankertes, gate-geschütztes Gebäudemodell als führende Geometrie-Wahrheit. Aufbaustufen:

- **Gebäude** (an `LeadAlternativeAdd`), **Geschosse** (storey), **Wände** (+ Wandanschlüsse), **Räume**, **Öffnungen** (Fenster/Türen), **Decken**, **Bodenplatten**, **Dach-Vorbereitung** (obere Kontur), **Nordwinkel**, **Höhen**, **Einheit** (mm-Integer), **Koordinatensystem** (lokal, definierter Ursprung), **Revision/Version**, **Herkunft** (manuell/Import/Scan), **Berechnungsstatus** (current/stale).
- **Topologie-Gate** bleibt vorgelagerter Pflichtdurchgang (übernommen aus G0b).

## 2. Schichten

| Schicht | Rolle | Regel |
|---|---|---|
| **2D-Editor** | primärer, präziser Bearbeitungsmodus (Polygon, Raster/Fang, numerische Eingabe, Topologie, Raumableitung) | SVG (wie heute) — bleibt bevorzugt; mm-Integer; **einziger fachlicher Schreiber** der Geometrie |
| **3D-Viewer** | **rein abgeleitet** aus dem kanonischen 2D-/Modell (Extrusion 2D-Polygon + Höhe; Wände/Decken/Öffnungen/Geschosse; später Dach/Fassade) | **kein** eigener fachlicher Schreibzustand; hält KEINE zweite Geometriewahrheit |
| **Import** | DXF/PDF/Bild als **Vorschlag/Underlay** (bestehender Python-Service) → Kalibrierung → Nutzerprüfung → Gate → Modell | nie automatisch führend; DWG/IFC/GLTF später |
| **LiDAR** | Aufnahmequelle → Scan-Zwischenformat → Vorschläge → Kontrollmaße → Nutzerprüfung → Gate → Modell | Mesh/Punktwolke **nie** ungeprüft führend |
| **Modulübergaben** | Heizlast, WP, PV, Dach, Speicher, Wallbox, Fenster/Türen, Fassade, Angebot, Montage | lesen aus dem **einen** Modell; keine eigene Objekt-/Geometrie-Kopie |

## 3. Framework-Weiche — RATIFIZIERT (3D-0B, 2026-07-15)
- **Kanonische Geometrie = Variante C (gewählt):** `gebaeude_geometrie` bleibt alleinige fachliche Übergangs-Schreibwahrheit (G0c-2 erhalten, kein Dual-Write, keine relationale Zweitwahrheit, Cutover = eigener Yama-Slice).
- **2D-Editor:** im bestehenden Ticket-Frontend (Blade + Alpine, SVG/Canvas, bestehender Vite-Build); keine SPA, kein zweites Frontend, keine Browser-Datenwahrheit; schreibt nur über kontrollierte Ticket-Services.
- **3D-Viewer:** isoliertes **Three.js-Modul** über Vite, in Blade montiert, **rein abgeleitet** (kein eigener Store, keine DB, kein Schreiben aus der Szene); Meshes ↔ kanonische Element-IDs.
- **React:** **nicht** als produktives Zweitframework in `ticket`; playground-Bundle bleibt **`nur_als_referenz`** (nicht kopieren/dekompilieren; Source-Beschaffung = eigener Inventurslice, blockiert das kanonische Modell nicht).
- Details/Wortlaut: `3d-0b-kanonische-geometrie-entscheidungsvorlage.md` (Abschnitt „RATIFIZIERT").

## 4. Reuse-Leitplanken (aus 0A)
- **Behalten/verdrahten:** `gebaeude_geometrie` (G1, kanonisch), `TopologieGate`, `GeometrieToleranz`, Shoelace/RaumHülle (Mirror-Kerne — nur aufrufen).
- **Anpassen portieren:** playground-Persistenz-Schema (U3), Feature-Extraktoren (U4) — mit Objekt-Mapping + Validierung.
- **Referenz:** playground-Bundle (U1), Insel-Muster (U2), `snap.js`/`freihand.js` (U13), Erkennungs-Muster (U11).
- **Daten migrieren:** `roof_tiles`/Montage (U5), GLB-Assets (U6) — in den EINEN Katalog.
- **Nicht übernehmen:** hartkodierte MODULE_TYPES (U8), wberechnung-Kern-Mirror (U16), Demo-Gebäude (U9).
- **Lizenz prüfen:** PyMuPDF (AGPL), Google-Solar (U7/U15).

## 5. Empfohlene Folge-Slices (klein, prüfbar — keine hier freigegeben)

| Slice | Inhalt | Vorbedingung |
|---|---|---|
| **3D-0B** | Entscheidung kanonische Geometriequelle (`gebaeude_geometrie` erweitern vs. `building_models` neu) + Framework-Weiche | 0A grün |
| **3D-0C** | Mapping-/Migrationskonzept der Stores (Dual-Read/Single-Write, kein Dual-Write) | 0B |
| **3D-1A** | JSON-Schema + Verträge des Gebäudemodells (Gebäude/Geschoss/Wand/Raum/Öffnung/…, Einheit/Koord/Revision) | 0B |
| **3D-1B** | additive Kerndatenstruktur (Migration) — **nach** grünem G0c-Gate + Yama-Migrationsfreigabe | 3D-1A |
| **3D-2A** | Editor-Shell + Koordinatensystem (mm, Ursprung, Nordwinkel) | 3D-1B |
| **3D-2B** | Wandeditor (Polylinie, Fang, numerisch) | 3D-2A |
| **3D-2C** | Topologie + Räume (Raumableitung, Wandanschlüsse) | 3D-2B |
| **3D-2D** | Öffnungen + Command-Verlauf (Undo/Redo) | 3D-2C |
| **3D-3** | read-only Three.js-Viewer (Extrusion aus 2D) | 3D-2* + Framework-Weiche |
| **3D-4** | Dach + PV-Übergabe (kanonisches Dach; PV liest daraus) | 3D-3 |
| **3D-5** | LiDAR-Proof-of-Concept (Scan→Vorschlag→Gate) | 3D-4 |
| **3D-6** | Mehrraumscan, Mesh, Punktwolken | 3D-5 |
| **3D-7** | Fenster/Türen/Fassade | 3D-2*/3D-4 |
| **3D-8** | Angebots-/Montageübergabe | 3D-4/7 |

**Keine** dieser Wellen wird durch 0A/0B freigegeben. Jede braucht eigenen Startblock + Yama-Freigabe; jede Migration = Pflicht-Stopp. **Nach 3D-0B ist der nächste mögliche Slice `3D-1A` (JSON-Schema-/Modellvertrag)** — gibt selbst noch keine Migration/Editor/Viewer frei. **Security-Gate (ratifiziert):** vor jeder sichtbaren/extern erreichbaren Auslieferung des 2D-/3D-Planers müssen die ungeschützten Demo-Routen (`/roofs`,`/solar`,`/testnav*`) entfernt/geschützt/lokal-beschränkt sein (eigener kleiner Security-Slice; blockiert nicht das Schema, aber spätestens Editor-Integration/Viewer-Auslieferung).

## 6. Risiken
Zweite Geometriewahrheit (G1↔G3 playground) · fehlender playground-Frontend-Source · Framework-Bruch (React/Three vs. Blade) · PyMuPDF-AGPL · Azimut-Doppelkonvention · Multi-Raum/Geschoss-Modelllücke · ungeschützte Demo-Routen (`/roofs`,`/solar`,`/testnav*`) · defekter pvtools-Code · kein Stale-Trigger.

## 7. Nicht-Ziele
Keine allgemeine BIM-Autorensoftware, keine Tragwerks-/Genehmigungsplanung, keine ungeprüfte KI/LiDAR-Planung, keine automatische Bestell-/Förderzusage, kein Dual-Write, keine parallele Objekt-/Geometrie-DB, kein 3D-Bau in dieser Welle.
