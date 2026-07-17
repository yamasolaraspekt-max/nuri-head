# P0 — CAD-Planer: Gesamtabgleich der zwei Erweiterungs-Prompts mit dem Bestand

**Rolle:** Planner / P0 (read-only) · **Datum:** 2026-07-17 · **Status:** Analyse, kein Produktivcode, kein Commit.
**Auftrag:** Die zwei neuen Prompts (professioneller 2D/3D-CAD-Planer + Fachmodule Bad/Küche/TGA/Elektro)
gegen den bestehenden Code und die bestehende Architektur-Linie prüfen, Inventar + Umsetzungsplan vorlegen.

> **Kein-Doppelbau-Hinweis (Lehre 2026-07-17, CLAUDE.md Z.27):** Es existiert bereits eine umfangreiche
> Analyse-/Architektur-Linie (`docs/building-planner/*`, `docs/zielbild-gebaeudeplaner.md`) und ein
> laufender Planer (playground `src/hausplaner`, teil-transplantiert nach ticket). Dieses P0 baut darauf
> auf und **wiederholt** die bestehenden Dokumente nicht — es reconciled die zwei Prompts damit und
> isoliert das echte Delta.

---

## 0 · Kernbefund in einem Satz

Die zwei Prompts sind zu ~80 % ein **Superset einer bereits entschiedenen und teilweise gebauten
Architektur**. Die geforderte Foundation (eine Wahrheit, Command-System, Undo/Redo, 2D+3D-Renderer,
Revisions-Konflikt, Katalog) **existiert**. Der einzige strategische Widerspruch ist die im Prompt als
„bevorzugt" genannte Greenfield-Technik (Babylon.js / OpenCascade.js / PostgreSQL) — die durch die
Prompt-eigene Regel („bestehende belastbare Three.js-Architektur nicht automatisch migrieren") und den
Bestand **verworfen** wird. Das eigentliche Neuland sind die logischen Elektro-/TGA-Fachobjekte, der
Diagramm-Arbeitsbereich und die gewerkeübergreifende Kollisionsprüfung.

---

## 1 · Bestandsaufnahme (verdichtet, grep-belegt)

### 1.1 Backend (ticket = Heimat, LIVE)
- **Laravel 11.44.7, PHP 8.2, MySQL**, Queue=database, **Broadcast=Reverb (WebSockets vorhanden)**,
  Sanctum, `barryvdh/laravel-dompdf`, **`spatie/browsershot` (headless Chrome → PDF/Screenshot vorhanden)**.
- **411 Models**, `routes/web.php` = 5664 Zeilen (monolithisch), 112 Test-Dateien, **keine CI-Pipeline**
  (`.github/workflows` fehlt) → Qualität hängt am Governance-Zyklus, nicht an Automatik.
- `app/Services/` mit reifen Domänen: `Anforderungsprofil`, `Auslegung`, `BuildingModel`, `Energie`,
  `Geometrie`, `Heizlast`, `Heizkoerper`, `Klima`, `Offer`, `Spec`, `Sperre`, `Suppliers`.
- `app/Domain/Hausplaner/` (Models + Actions transplantiert).

### 1.2 Frontend / 3D (playground = F&E-Quelle, Bundle in ticket)
- **React 19 + TypeScript 5.8 + Vite 6 + Three.js 0.184 + Konva 9 (2D) + Zustand 5 + Immer 10 + Zod 3.**
- `src/hausplaner/` ist bereits sauber modularisiert: `domain/` (scene.types, commands.types, validation),
  `commands/` (applyCommand), `store/` (hausplanerStore + history), `renderers/three-d/`
  (adapter, szene, platzierung, segmentierung), `geometry/` (roomDetection, wallGeometry),
  `projection/` (raumProjektion), `app/` (HausplanerApp mit Konva-2D + DreiDBereich-3D), `__tests__/`.
- Fertige Insel-Bundles in ticket: `public/hausplaner/hausplaner.js` (1,18 MB) + `public/planer/` (Dach).

**→ Der Prompt-„Preferred-Stack" (React/TS/Vite/Zustand/Immer/Zod/Konva) ist bereits Realität — nur mit
Three.js statt Babylon.js.**

### 1.3 Persistenz-Rückgrat (die „BuildingDocument"-Wahrheit existiert)
Migrationen bereits vorhanden: `hausplaner_documents` (scene_json JSON + **`alternative_id` UNIQUE = ein
Plan je Objekt** + revision + checksum → **optimistische Revisionskontrolle → 409**),
`hausplaner_snapshots` (append-only), `hausplaner_catalog_items`, `anforderungsprofile` (+ `_werte`,
+ `gebaeude_geometrie`-Spalte), `raum_geometrien`, `building_model_versions`.
Der Katalog trägt schon **category / manufacturer / model / dimensions(mm) /
representation{symbol2d, model3dUrl, previewImageUrl} / placement{mode, allowRotation, allowScaling} /
spec_ref / technical_data / aktiv** — feldgleich mit der Prompt-`CatalogItem`/`CatalogRepresentation`.

### 1.4 Rechte & Mandant
App-eigenes System: `User::hasPermission($item,$action)`, `user_rolls` (is_read/add/update/delete),
Middleware `permission:Item,action`, `is_admin`-Bypass — **kein spatie/@can**. Neue Module registrieren
sich in `UserRollController::permissionModules()`. (Kein Multi-Tenant-`organization_id` wie im Prompt —
die Prompt-Mandantenregel wird hier auf **Objekt-/Projekt-Bindung** übersetzt: `alternative_id` /
`LeadAlternativeAdd`.)

### 1.5 Bestehende Analyse-Linie (nicht neu schreiben — referenzieren)
`docs/zielbild-gebaeudeplaner.md` (North-Star, verbindlich), `docs/building-planner/` mit
Bestandsinventur, Geometriequellen-Register, Playground-Übernahmematrix, Zielarchitektur & Slice-Fahrplan,
kanonischer Modellvertrag (G1a/3D-1A), CAD-Evaluationen (g1b-1), Persistenz-Entscheidungsvorlagen, ADRs,
Revision-Locking/Cutover.

---

## 2 · Die acht Prompt-Fragen, beantwortet

1. **Was existiert bereits?** Eine Wahrheit (`SceneDocument`, mm-Integer), Command-System (ADD/MOVE/
   REMOVE/UPDATE_NODE, UPDATE_SETTINGS) + Undo/Redo (`history.ts`), 2D-Renderer (Konva), 3D-Renderer
   (Three.js-Adapter), Raumerkennung, Wandgeometrie, Projektions-Kontrakt (`RaumGeometrieProjektion`),
   Persistenz mit Revisions-409, Snapshots, Katalog, Heizlast-/PV-/WP-Engines, Rechte, Reverb.
2. **Was ist wiederverwendbar?** Praktisch die gesamte Foundation (Prompt-P0+P1) und die 2D/3D-Renderer
   (Prompt-P2+P3-Basis). Katalog-Tabelle für Bad/Küche/Elektro **ohne Schemaänderung** (nur neue Kategorien +
   Zeilen). Heizlast/WP/PV bleiben führend (Projektion speist sie).
3. **Was muss erweitert werden?** Node-Union um Fach-Subtypen füllen; Fach-Werkzeuge/Commands je Gewerk;
   Katalog-Assets (GLB); 3D-PBR-Textur-Schicht; Planausgaben (Wandabwicklung, Fliesenplan, Einlinienschema);
   Projektionen je Gewerk.
4. **Was darf NICHT doppelt gebaut werden?** Kein zweites Datenmodell je Gewerk (alles am `SceneDocument`);
   kein zweiter Renderer neben Three.js/Konva; keine zweite Heizlast-/WP-/PV-Logik im Renderer; keine neue
   Analyse-Linie neben `docs/building-planner/`; **kein Renderer/keine CAD-Engine von Null** (North-Star §3).
5. **Was ist bei Änderung gefährdet?** Die LIVE-App: 411 Models, monolithische `web.php`, keine CI. Jede
   Änderung streng additiv, `permission`-gegated, Revision-409 + BearbeitungsSperre. Bundle-Größe (1,18 MB)
   und Three.js-Performance bei großen Szenen.
6. **Welche technischen Schulden bestehen?** Keine CI/automatischen Tests im Backend-Gate; `web.php`
   monolithisch; Bundle ungeteilt (kein Code-Splitting/LOD); Rechte-Mapping Hausplaner noch als Ausreißer-
   Routen (`permission:hausplaner.view`) statt Item+CRUD (Spec 55d391a liegt bereit).
7. **Empfohlene Architekturentscheidung?** Bestehende Architektur **fortführen und auffüllen**
   (Three.js + Konva + Zustand/Immer/Zod + MySQL-JSON + Reverb + Browsershot). Neue Fachobjekte
   (Stromkreis, Verteiler) als **nicht-geometrische Entitäten neben `nodes[]`**; Diagramm-Arbeitsbereich als
   **zweite Sicht auf dieselben Fachobjekt-IDs**; Kollisionsprüfung als eigene Projektion. CAD-Kernel
   (OpenCascade.js) und Blender-Render-Worker **später andocken, nicht jetzt** (§4).
8. **Verworfene Alternativen (mit Grund)?** Siehe Entscheidungsmatrix §4 — v. a. Babylon.js, PostgreSQL,
   OpenCascade.js-jetzt, Green-field-Neubau.

---

## 3 · Schema-Abgleich: Prompt-`BuildingDocument` ↔ Bestand-`SceneDocument`

| Prompt-Feld | Bestand | Bewertung |
|---|---|---|
| `documentId / projectId / organizationId` | `id / projectId` (Objekt-Anker) | org→Objekt/Projekt-Bindung (§1.4) |
| `schemaVersion / revision` | vorhanden (revision server-vergeben, base_revision→409) | **deckungsgleich** |
| `units:'mm'` | `units:'mm'` (Integer-Invariante mit Test) | **deckungsgleich** |
| `settings` | `settings{gridSize, snapEnabled, angleSnap}` | vorhanden, erweiterbar |
| `levels[]` | `Level{elevation, defaultWallHeight, floorThickness, sortOrder}` | **deckungsgleich** |
| `nodes[]` | `SceneNode = Wall\|Opening\|Object\|Zone\|Route` | vorhanden, Union erweiterbar |
| `materials[]` | `MaterialDefinition{color,uValue}` | vorhanden, PBR-Felder = Erweiterung |
| `cameras[] / renderPresets[]` | fehlt | **Delta** (P4/P7 — Kamera-/Render-Schicht) |
| `geolocation{northAngle}` | Nord=+y-Konvention gesetzt; Feld fehlt | kleines Delta |

**Node-Union deckt die Gewerke schon ab:** `ObjectNode.objectType` = radiator, heat_pump_indoor/outdoor,
buffer_tank, hot_water_tank, battery, inverter, wallbox, furniture, **sanitary**; `ZoneNode.zoneType` =
room, underfloor_heating, pv_area, maintenance_area, sound_area, restricted_area; `RouteNode.routeType` =
heating_pipe, water_pipe, refrigerant_line, **electrical_line**, pv_dc_line, drainage. → Möbel, Heizung,
WP, Speicher, PV, Wallbox, Sanitär, FBH, Wartungs-/Schall-/Sperrzonen und TGA-/Elektro-Leitungen haben
**bereits eine Heimat im Schema**. Das ist Auffüllen, kein Neubau.

---

## 4 · Entscheidungsmatrix (die klugen, belegten Festlegungen)

| Entscheidung | Empfehlung | Begründung (belegt) | Verworfen |
|---|---|---|---|
| **3D-Renderer** | **Three.js behalten** | Prompt-eigene Regel: belastbare Three.js-Arch. nicht auto-migrieren. Bundle läuft, Tests grün, Adapter/Szene fertig. | Babylon.js (Renderer-Tausch mischt sich mit Fachänderungen, wirft Bestand weg) |
| **2D-Renderer** | **Konva behalten** | Prompt nennt „Konva.js oder eigener Renderer"; Konva ist im Bundle. | Eigen-Canvas von Null |
| **UI-/Scene-State** | **Zustand + Immer + Zod behalten** | Prompt-Preferred == Bestand. | Neuaufbau |
| **Datenbank** | **MySQL behalten (JSON-Spalten)** | scene_json/gebaeude_geometrie sind bereits JSON in MySQL; 411 Models produktiv. JSONB-Vorteil rechtfertigt keine Postgres-Migration einer LIVE-App. | PostgreSQL (katastrophales Risiko, kein belegter Vorteil) |
| **CAD-Kernel (B-Rep/Boolean)** | **Andocken NUR bei Bedarf, jetzt NICHT** | Vorhandene Polygon-/Extrusions-Mathematik trägt P2–P6. Boolean-Solids (Dachdurchdringung, komplexe Ausschnitte) erst später → dann **OpenCascade.js als isolierter Slice**. North-Star §3. | OpenCascade.js sofort (WASM-Gewicht ohne aktuellen Bedarf) |
| **Fotorealismus** | **glTF-Export → externe Engine (Blender-Worker), Tail** | Reverb/Queues/Browsershot da; Prompt-P7. North-Star §3: keinen Renderer schreiben. | Eigener Path-Tracer |
| **Elektro-Stromkreis / Verteiler** | **Neue nicht-geometrische Fachentität neben `nodes[]`** | Prompt: „Stromkreis ist kein grafisches Kabel, sondern Fachobjekt." Ein `circuits[]`/`boards[]`-Feld im SceneDocument, referenziert per ID. | Stromkreis als Node/Kabel (falsche Wahrheit) |
| **Diagramm (Einlinien/Stromlauf)** | **Zweite Sicht auf dieselben Fachobjekt-IDs** | Prompt: „Gerät im Grundriss und im Stromlaufplan = dieselbe Objekt-ID." `ElectricalDiagramNode.sourceEntityId`. | Eigene Gerätedaten im Diagramm (Doppel-Wahrheit) |

---

## 5 · Erweiterte Gewerke-Landkarte (Bestand → 2 Prompts)

| Gewerk / Modul | Wird im Modell zu … | Status |
|---|---|---|
| Grundriss (Wand/Öffnung/Zone) | Wall/Opening/Zone-Nodes | **fertig** (in Abnahme ticket) |
| Heizlast | Projektion `raum_geometrien` → Engine | Engine da, **Naht offen** (P2-Linie läuft) |
| Dach / Zimmerei | RoofNode + Dach-Mathematik | Spec da, Fusion offen |
| PV-Belegung | Zone `pv_area` + Ertrags-Projektion | 6 PV-Models da, Modul offen |
| Fenster / Türen | Opening-Nodes + Katalog | Teil da, Katalog offen |
| **Möblierung/Innenraum** | ObjectNode `furniture` + Katalog-GLB | Node+Katalog da, **Assets/Tools offen** (Prompt-P6) |
| **Badplanung** | ObjectNode `sanitary` + Zone + ConnectionPoints | Node da, **ConnectionPoints/Vorwand/Fliesen offen** (P7) |
| **Küchenplanung** | ObjectNode + **parametrische KitchenCabinet** | **Greenfield** parametrisch (P8) |
| **Sanitär-/TGA-Leitungen** | RouteNode + Gefälle/Fitting/Schacht | Node da, **Routing/Gefälle/Durchbruch offen** (P9) |
| **Elektro-Installation** | ObjectNode (Elektrogeräte) im Grundriss | **Greenfield** Gerätetypen/Symbole (P10) |
| **Stromkreis / Verteiler** | **neue Fachentität** `circuits[]/boards[]` | **Greenfield** (P10) |
| **Stromlauf / Einlinien** | **Diagramm-Arbeitsbereich** (2. Sicht) | **Greenfield** (P11) |
| **Gewerke-Koordination** | `CoordinationIssue`-Projektion (Kollision) | **Greenfield** (P12) |
| **Planausgabe** (Wandabwicklung, Fliesen-, Anschluss-, Einlinienplan, DXF) | Export-Projektionen aus einer Revision | Browsershot/DomPDF da, **Plan-Renderer offen** |
| Fotorealismus | glTF-Export → externe Engine | angedockt, Tail |

---

## 6 · Das echte Delta der zwei Prompts (was WIRKLICH neu ist)

1. **Elektro-Stromkreis als Fachobjekt** (Last/Schutzorgan/RCD/Querschnitt/Spannungsfall) — neue Entität,
   keine Geometrie. Berechnung als prüfbares Fachmodul (nicht im Renderer).
2. **Verteiler/Unterverteilung** (Hutschiene, Slots, Reserveplätze, Phasenverteilung).
3. **Diagramm-Arbeitsbereich** (Einlinienschema + Stromlaufplan) als zweite 2D-Sicht auf dieselben IDs;
   Symbolbibliothek, Ports, orthogonale Verbindungen, Blattaufteilung, PDF/SVG/(DXF).
4. **Reiche Anschlusspunkte** (`ConnectionPoint` kalt/warm/abwasser/elektro) an Sanitär-/Küchen-/
   Elektroobjekten — mehr als der heutige `parameters`-Bag.
5. **Parametrische Küchenschränke** (Korpus + Module erzeugen Geometrie) — erstes echtes parametrisches Objekt.
6. **Gewerkeübergreifende Kollisions-/Koordinationsprüfung** (`CoordinationIssue`, Status open/accepted/…).
7. **Planausgabe-Renderer** (Wandabwicklung, Fliesenplan, Strangschema, Isometrie, Einlinien, DXF).
8. **CAD-Kernel & Blender-Worker** — bewusst spätere Andock-Slices, nicht Foundation.
9. **Katalog-Mini-Erweiterung:** Feld `representation.symbolDiagram` fehlt noch (für Diagramm-Symbole) →
   additive Spalten-Erweiterung erst bei P11.

---

## 7 · Risiken

- **Security:** Katalog-/Asset-Auslieferung signiert/autorisiert; Uploads streng validiert (GLB ohne
  ausführbare Inhalte); **keine beliebigen Blender-Python-Skripte**; Objekt-/Projekt-Bindung + `permission`
  je Route; keine Seiteneffekte vor Autorisierung. Mandant = Objekt-Bindung statt `organization_id`.
- **Performance:** Bundle 1,18 MB ungeteilt; Three.js braucht Instancing/LOD/Frustum-Culling/selektives
  Geschoss-Laden ab großen Szenen; Performance-Budget (≥30 FPS Bürohardware) **nur mit Messung** behaupten.
- **Lizenz:** Three.js (MIT), Konva (MIT), Zustand/Immer/Zod (MIT) — unkritisch. OpenCascade.js (LGPL) und
  DXF-Export sowie GLB-Herstellerassets **vor Einsatz lizenzrechtlich prüfen** (North-Star/Prompt-Regel).
- **Governance:** Keine CI → jeder Slice läuft zwingend durch Planner→Generator→Evaluator; additive DB;
  eine Schreib-Wahrheit; Revision-409 + BearbeitungsSperre. Größtes Risiko ist Chaos, nicht Ambition.

---

## 8 · Aktualisierter Slice-Fahrplan (reconciled, mit Pflicht-Stopps)

Die Prompt-Phasen werden auf den Bestand abgebildet — **P0/P1 sind nicht neu zu bauen, sondern
abzunehmen**. Aufgabenteilung: **Claude** = Planner-Spec + Generator (additiv, unverdrahtet, `php -l`/
gegen-grep) · **Evaluator** = unabhängiger Gegenbeweis + Testlauf · **Yama** = Migrate/Referenzfall/
Browser-Sichtproben/Go.

- **S0 — Abnahme Foundation (statt Neubau):** Hausplaner in ticket fertig (T-d Browser-Sichtproben),
  Rechte-Mapping (Spec 55d391a), P2-Naht Szene→`gebaeude_geometrie` abschließen. *(läuft bereits)*
- **S1 — Dach-Andock:** RoofNode + Dach-Mathematik als reine Funktionen → ein 3D-Bild Haus+Dach,
  Dachfläche belastbar für PV. **Stopp.**
- **S2 — PBR-/Textur-Schicht (3D):** MaterialDefinition → Three.js-PBR, HDRI/Sonne/Schatten, Qualitätsstufen.
  (Prompt-P4.) **Stopp.**
- **S3 — Möbel-Katalog + ObjectNode-Werkzeuge:** GLB-Asset-Pipeline (Upload/Validierung/LOD/Thumbnail),
  Platzieren/Ausrichten/Abstandszonen. (Prompt-P6.) **Stopp.**
- **S4 — Badplanung:** Sanitär-ConnectionPoints, Vorwand/Installationswand, Nasszonen, Fliesenraster,
  Wandabwicklung, Badstückliste. (Prompt-P7.) **Stopp.**
- **S5 — Küchenplanung:** parametrische Schränke + Arbeitsplatte + Geräte + Anschlüsse + Prüfungen
  (Arbeitsdreieck), Küchenansichten/Stückliste. (Prompt-P8.) **Stopp.**
- **S6 — Sanitär-/TGA-Routing:** RouteNode-Werkzeuge, orthogonales Routing, Abwassergefälle, Stränge,
  Schächte, Durchbrüche, Strangschema/Isometrie. (Prompt-P9.) **Stopp.**
- **S7 — Elektro-Grundriss + Stromkreise:** Elektrogeräte-Nodes/Symbole, `circuits[]`/`boards[]`-Entität,
  Gerät→Stromkreis→Verteiler, Kabelweg-Länge aus Geometrie, Lastaufstellung. (Prompt-P10.) **Stopp.**
- **S8 — Diagramm-Arbeitsbereich:** Einlinien/Stromlauf als zweite Sicht (gleiche IDs), Symbolbibliothek,
  Verbindungen, Stromkreisverzeichnis, PDF/SVG-Ausgabe. (Prompt-P11.) **Stopp.**
- **S9 — Gewerke-Koordination:** Kollisions-/Prüfregeln, `CoordinationIssue`, Koordinationsbericht.
  (Prompt-P12.) **Stopp.**
- **S10 — Fotorealismus (Andock):** glTF-Export → Blender-Render-Worker (Queue, Ressourcenlimits,
  Presets, an Revision gebunden). (Prompt-P7 des 1. Prompts.) **Stopp.**
- **S(quer) — bei Bedarf:** OpenCascade.js-Andock als isolierter Slice, sobald Boolean-Solids nachweislich
  nötig; CI-Pipeline; Bundle-Code-Splitting/LOD; `web.php`-Entflechtung.

**Reihenfolge = Vorschlag, jede Stufe eine eigene Spec mit Stopp bei Yama.**

---

## 9 · Abschluss / Integrität

- **Kein Produktivcode verändert.** Nur dieses Analyse-Dokument. Kein Commit, kein Push, keine Migration.
- Die in-flight P2-2a-Dateien (Übernahme Szene→Auslegung) wurden **NICHT** deployt — auf Halt gesetzt,
  bis P1-Freigabe für die reconciled-Linie vorliegt.
- Nächster Schritt braucht **deine Freigabe**: (a) diesen Abgleich bestätigen, (b) S0 zu Ende (P2 + T-d),
  dann (c) den ersten neuen Slice (S1 Dach-Andock **oder** vorgezogenes Elektro/Bad) benennen.
