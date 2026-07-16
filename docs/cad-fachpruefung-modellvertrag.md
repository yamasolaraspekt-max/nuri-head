# CAD-/Architektur-Fachprüfung des Modellvertrags — Gate vor G1a

> **Rolle:** CAD-/Architektur-Fachagent (Perspektive Architekt · Bauzeichner · CAD-Systemexperte mit Bestandsaufmaß-Praxis).
> **Auftrag:** Rang-4, „CAD-/Architektur-Fachprüfung des Modellvertrags (Gate vor G1a)". **Read-only, doc-only.**
> **Getrennte Rolle:** Diese Fachprüfung ersetzt weder den technischen Evaluator noch die Yama-Entscheidung. Gesamtfreigabe modellvertragsrelevanter Wellen = CAD-Fachprüfung + technische Evaluatorprüfung + Yama.
> **Prüfgegenstand:** Modellvertrag **wie in Claude-Code-Masterprompt V3.3 spezifiziert** — B2 (Einheiten/Koordinaten/Geometrie), B9 (kanonisches Geometriemodell), B10 (Toleranzvertrag), B19 (Scan-Erfassung), Teil C (Datenmodell inkl. C1/C2), D5–D13. Zusätzlich als Repo-Beleg: der nachgelagerte, **nicht abgenommene** Schema-Entwurf `resources/schemas/building-model/v1.schema.json` + `app/Services/BuildingModel/CanonicalBuildingModelValidator.php` (uncommitted).
> **Stand:** V3.3 (Auftragstext) · Repo `HEAD=975f44d` · Schema-Entwurf `v1.schema.json` uncommitted (Beweis­stück, nicht Prüfnorm) · Datum 2026-07-15.

---

## 0. Einordnung: eine Roadmap, keine dritte

Der Slice-Plan „3D-1A … 3D-8" ist **Alias** auf die kanonischen G-Wellen — keine zweite Roadmap:

| Alias | Kanonisch |
|---|---|
| 3D-1A Modellvertrag | **G1a** (Schema/Analyse) — *diese Prüfung ist ihr Eintritts-Gate* |
| 3D-1B Persistenz | G1b |
| 3D-2A Editor-Shell · 2B Wandeditor · 2C Topologie/Räume · 2D Öffnungen/Undo | G2a · G2b · G2c · G2d |
| 3D-3 Three.js-Viewer | G6 |
| 3D-4 Dach und PV | G7/G8 (Dachübergabe) |
| 3D-5 LiDAR · 3D-6 Punktwolken/Mehrraum | G4L-a/b · G4L-c/d |
| 3D-7 Fenster/Türen/Fassade | G8+ (Fassade) + D10-Umfang in G2d |
| 3D-8 Angebots-/Montageübergabe | G7 (Raumbuch/Listen) + D14-Übergaben |

**Kernaussage der Prüfung vorab:** Der in **V3.3 spezifizierte Vertrag ist fachlich deutlich stärker** als der bereits im Repo liegende, minimale und nicht abgenommene Schema-Entwurf. Die härtesten CAD-Mängel, die man einem naiven „Start-/Endknoten + wall_type"-Modell vorwerfen würde (undefinierte Wandreferenz, XY-verankerte Öffnungen), sind auf **Spezifikationsebene bereits geschlossen** — B9.1 definiert `reference_line_type inner/axis/outer`, B9.2 verankert Öffnungen parametrisch über die **Station** entlang der gerichteten Bezugslinie. Die verbleibenden Punkte sind **echte Planner-Entscheidungen und Umsetzungsauflagen für G1a**, nicht Grundsatzmängel. Der Repo-Entwurf `v1.schema.json` bleibt **hinter** dieser Spezifikation zurück (keine `reference_line_type`, XY-Knotenwand, keine Station, kein Bezug auf den zentralen Toleranzvertrag) und darf daher **nicht** als G1a-Ergebnis herangezogen werden — er ist Vorstudie, kein Vertrag.

---

## 1. Prüfpunkte (Befund → Bewertung → Begründung aus Bau-/Aufmaßpraxis)

### 3.1 Modellhierarchie und Bauteile
- **Befund:** V3.3 D1/Teil C definieren Grundstück/Bezugssystem → Gebäude → Geschoss → Bauteil. Jedes Bauteil trägt UUID, Typ, Geschoss, Geometrie, Material/Aufbau, Kennwerte, Quelle, Status, Version (D1, Teil C, B12). Bauteil und Darstellung sind getrennt (B2 „Geometrie und Darstellung sind getrennt", B9).
- **Bewertung:** **erfüllt.**
- **Begründung:** Für Bestandsaufnahme ist genau diese Trennung entscheidend — der Aufmaßtechniker ändert das Bauteil (Wand), nicht ein Renderbild. Geschoss als Pflichtebene ist von Anfang an vorgesehen (kein späterer Nachrüst-Bruch). **Ein bestandskritischer Bauteiltyp fehlt strukturell nicht** (Wand, Öffnung, Fenster, Tür, Decke, Bodenplatte, Raum, Treppe, Schacht, Dach, Maßkette sind katalogisiert). *Hinweis:* Brüstungen/Attika, Unterzüge/Stützen und Fassaden-Teilflächen sind als spätere Bauteiltypen benannt (D-Katalog/G8+), nicht Gate-relevant.

### 3.2 Wandreferenz und Wandgeometrie
- **Befund:** B9.1 legt fest: gerichtete Bezugslinie (mm-Integer), `reference_line_type ∈ {inner, axis, outer}`, Innen-/Außenkante **deterministisch abgeleitet, nicht als zweite Wahrheit persistiert**, Dickenänderung „immer über die definierte Fixkante". Teil C `building_walls.reference_line_type`. Höhe „bzw. Höhenprofil". Wandaufbau als eigene Schichtentabelle (`building_wall_layers`, `building_assemblies`) getrennt vom Wandkörper. D7: Bezugslinie nachträglich ohne Neuzeichnen umstellbar.
- **Bewertung:** **erfüllt mit Auflage (CAD-A1, CAD-E1).**
- **Begründung:** Das ist die fachlich korrekte Lösung — im realen Aufmaß misst man mal Außenkante (Fassade), mal lichtes Innenmaß (Raum); eine feste Referenz + deterministische Kantenableitung verhindert die klassische „½-Wanddicke-Drift" in Flächen. **Offen bleibt:** (a) welche Referenz die **Aufnahme-Voreinstellung** ist und ob die drei Typen verlustfrei ineinander umrechenbar bleiben (B9.1 fordert Umstellbarkeit; das Schema muss das garantieren, nicht nur behaupten) → CAD-E1; (b) bei Dickenänderung ist die **Fixkante pro Wand** zu bestimmen (nicht global) — sonst wandert die Fassade oder die Raumkante ungewollt. Mehrschichtaufbau ist sauber getrennt (gut). Gekrümmte/nicht-orthogonale Wände: **schema-seitig nicht ausgeschlossen** (freie mm-Knoten), Bögen sind Zielkatalog, nicht Gate.

### 3.3 Topologie und Anschlüsse
- **Befund:** B9.3 „Raumpolygone werden aus der validierten Wandtopologie abgeleitet". B10 Toleranzvertrag (Punktgleichheit, max. Wandanschluss-Spalt, Mindestsegment, Selbstschnitt). C2 `building_wall_junctions` **optional**, mit **Entscheidungspflicht D19**: Wandanschlüsse deterministisch abgeleitet **oder** als eigene Entität persistiert — „G2c darf ohne sie nicht beginnen".
- **Bewertung:** **erfüllt im Vertrag, aber blockierende Planner-Entscheidung offen (CAD-E2 / D19).**
- **Begründung:** L-/T-/X-/Stumpf-Anschlüsse sind der Kern jeder Grundriss-Digitalisierung. Ob man sie **rechnet** (aus Achsen + Dicke + Reihenfolge) oder **speichert** (als Junction mit Auflösungsregel), ist eine echte Architektur-Weiche: gerechnet = weniger Zustand, aber jede Kantenlängen-/Gehrungsfrage muss deterministisch sein; gespeichert = explizit, aber pflegebedürftig. **Ohne diese Entscheidung ist ein Grundriss optisch geschlossen, topologisch aber uneindeutig** — genau der Fehler, der später falsche Raumflächen und falsche Nettowandflächen erzeugt. Fast-geschlossene Konturen fängt B10 (max. Wandanschluss-Spalt) ab — **richtig als sichtbarer Blocker, nicht als stilles Zuschnappen** (deckt sich mit A4/B2 „keine automatische Reparatur").

### 3.4 Öffnungen
- **Befund:** B9.2/Teil C `building_openings`: `wall_id`, `station_mm` (Position entlang gerichteter Bezugslinie), `host_segment_index`, Breite, Höhe, `sill_height_mm`, `lintel_height_mm`, **Rohbau- und Fertigmaße getrennt** (`rough_width_mm`/`rough_height_mm`), Orientierung/Anschlag. „Absolute XY sind nur abgeleitete Renderdaten." Bei ungültiger Station **Blocker, kein stilles Verschieben**. Fenster/Tür erweitern die Öffnung 1:1 (`building_windows.opening_id`, `building_doors.opening_id`, DIN L/R-Anschlag).
- **Bewertung:** **erfüllt mit Auflage (CAD-A3).**
- **Begründung:** Parametrische Station statt XY ist die einzige Lösung, die eine Wandverlängerung/-verschiebung/-teilung überlebt — das Fenster bleibt „2,10 m ab Wandanfang", nicht „bei Bildschirmpunkt X". Rohbau/Fertig getrennt ist Aufmaß-Realität (Bestellmaß ≠ lichtes Maß). **Offen:** die Station braucht eine **eindeutige Herkunftsdefinition** — von welchem Wandende (Start der gerichteten Linie), gemessen bis zur **näheren Öffnungskante** (nicht Mitte), und über `host_segment_index` bei Polylinienwänden. Ohne diese Festlegung driften 2D-Editor, 3D und Heizlast-Laibung auseinander. Brüstung/Sturz sind über `sill_height_mm`/`lintel_height_mm` referenziert — **Bezug OKFF/Wandunterkante muss im Vertrag benannt sein** (CAD-A3).

### 3.5 Räume, Flächen, Volumen
- **Befund:** B9.3 Raum aus validierter Wandtopologie; manueller Raum nur mit ausdrücklicher Kennzeichnung; „abgeleitet und manuell nie gleichzeitig führend". Teil C `building_rooms.geometry_source ∈ {derived_topology, manual}`, `derived_from_revision`, `topology_signature`, `manual_reason`; `net_area`/`volume` mit `computed_at` + Override-Kennzeichnung. B3 Recalc eventbasiert. A3-Nicht-Ziel: **DIN 277 (geom. NGF) ≠ WoFlV (rechtliche Wohnfläche)** — Regelwerk vor betreffender Welle festzulegen.
- **Bewertung:** **erfüllt mit offener Entscheidung (CAD-E3).**
- **Begründung:** Jede Fläche ist über `derived_from_revision` + `topology_signature` auf ihre Geometrie **rückführbar** — das ist die Voraussetzung, um später zu beweisen, warum eine Fläche sich geändert hat. Overrides sind gekennzeichnet (B12), nicht still. **Wichtig und richtig getrennt:** geometrische NGF ist nicht Wohnfläche — die Vermischung ist ein häufiger, teurer Fehler (Wohnflächen sind rechtlich mit Anrechnungsfaktoren für Schrägen/Balkone belegt). **Offen (CAD-E3):** welches Wohnflächen-Regelwerk wann gilt. DG-Schrägen sind im **Volumenmodell nicht strukturell ausgeschlossen** (Raumhöhe je Raum, Höhenprofil an der Wand) — aber erst mit der Dach-/Schrägen-Welle fachlich abgesichert (A8).

### 3.6 Höhen
- **Befund:** D5/Teil C `building_floors`: `finished_floor_level` (OKFF), `raw_floor_level` (Rohdecke), `clear_height` (lichte Höhe), `storey_height` (Geschosshöhe), `slab_thickness`, Aufbauten; `building_rooms.clear_height` je Raum. B2: „Höhen als Z relativ zu OKFF Erdgeschoss = ±0,00". B3: „Deckenstärke geändert → Geschosshöhen" (explizite Abhängigkeitskette).
- **Bewertung:** **erfüllt mit Auflage (CAD-A2).**
- **Begründung:** OKFF / OK-Rohdecke / lichte Höhe / Geschosshöhe sind **als getrennte Felder** vorhanden — genau richtig, weil man sie im Bestand einzeln misst und nicht auseinander ableiten darf (Fußbodenaufbau variiert). **Auflage:** der Vertrag muss festschreiben, **welche Höhen geführt (gemessen) und welche abgeleitet** sind, damit `calculation_state` (C1) greift und eine Höhenänderung sich **definiert** fortpflanzt (OKFF EG = ±0,00 als eindeutiger Nullpunkt; Geschosse relativ; `bottom_z` einer Wand konsistent zum Geschoss). Ohne diese Festlegung entstehen zwei Höhenwahrheiten (gemessen vs. gerechnet).

### 3.7 Geschosse und Gebäudeteile
- **Befund:** D5 Standardgeschosse (UG/KG/EG/OG/DG/Staffel) + `floor_number`; Teil B/E: mehrere Gebäude je Modell (`building_models` → mehrere `building_floors`); FG-1/B1 begrenzt zunächst die **UI**, nicht das Schema. Geschoss duplizieren erzeugt **neue UUIDs/Beziehungen** (D5).
- **Bewertung:** **erfüllt.**
- **Begründung:** KG/EG/OG/DG sind über `floor_number`/OKFF **nicht nur über Namen** definiert (sortierbar, höhenverankert) — Pflicht, sonst kollabiert die 3D-Stapelung. Anbauten/Gebäudeteile sind über die Gebäude-Ebene abbildbar. **Die erste UI-Begrenzung auf ein Geschoss darf nicht zur Schema-Begrenzung werden** — im Schema explizit als Array angelegt (im Repo-Entwurf korrekt: `buildings[].storeys[]`). Wichtig für G2: bei „Geschoss duplizieren" **neue UUIDs** — sonst teilen sich zwei Geschosse dieselbe Wand-Identität (Datenkorruption).

### 3.8 Decken, Bodenplatten, spätere Dächer
- **Befund:** Teil C `building_slabs`: `slab_type` (Decke/Bodenplatte), Geometrie (Außenpolygon + optional Innenaussparungen, B9.4), `thickness`, `ground_contact`. B9.4 Umlaufsinn kanonisch: Außenring im Uhrzeigersinn, Innenringe gegen den Uhrzeigersinn; Importgeometrie beim Import normalisiert + protokolliert. Dach: **eigene späte Welle** (B18 „Dach als eigener Schritt auf der fertigen Gebäudehülle"; G7/G8); D-Katalog trennt Traufe/First/Ortgang/Neigung.
- **Bewertung:** **erfüllt mit Auflage (CAD-A4).**
- **Begründung:** Geschossdecke und Bodenplatte sind fachlich getrennt (`slab_type`, `ground_contact`) — Pflicht für U-Wert/Erdreichbezug. Deckenöffnungen (Treppenauge, Schacht) sind über Innenringe + `building_stairs`/`building_shafts` später ergänzbar. **Auflage CAD-A4:** der Umlaufsinn (B9.4) ist bisher **nur für Slabs** kanonisch festgeschrieben — er muss **auch für Wand-/Raum-Umläufe** verbindlich sein (Rauminnen-Seite, Außennormale, Wand-Azimut hängen daran). **Dach wird korrekt NICHT als Grundrisspolygon behandelt** — Dachfläche ≠ Grundfläche ist ausdrücklich getrennt; das verhindert den häufigen PV-Fehler „Dachfläche = Grundriss".

### 3.9 Revisionen und Bearbeitung
- **Befund:** Stabile UUIDs (Teil C). `schema_version` (JSON-Geometrie) **und** `model_revision`/`building_model_versions` **getrennt** (C1, B4). Freigegebene Versionen unveränderlich, Bearbeitung erzeugt neuen Draft (C3). Undo/Redo als Command-Pattern (B4/B11 `building_change_sets`, `command_id`). Herkunft manuell/importiert/gescannt (B12 `confidence_status`, Erfassungsmethode). Vorschläge verwerfbar ohne Änderung bestätigter Geometrie (B17/B19). Mehrbenutzer: pessimistisches Lock (B5, `building_model_locks`).
- **Bewertung:** **erfüllt.**
- **Begründung:** Schema-Version ≠ Modellrevision ist eine wichtige, oft übersehene Trennung — sonst kann man ein Modell nicht migrieren, ohne seine Bearbeitungshistorie zu zerstören. Bestätigte Revisionen werden **nie still überschrieben** (C3 + „keine Soft-Deletes auf Nachweistabellen", Teil C). Undo über fachliche Commands (nicht Pixel-Undo) ist korrekt. Herkunft je Wert (B12) ist Aufmaß-Gold: „woher weiß ich diese Wandstärke?" muss beantwortbar sein.

### 3.10 Import und Scan
- **Befund:** B6 Import = immer **Vorschlag mit Pflicht-Bestätigung**; Originaldatei unverändert + gehasht (B13). B9.5 Transformationskette `source → page/layer transform → calibration transform → model` reproduzierbar; Neukalibrieren verändert **nicht still** vorhandene Modellgeometrie (Nutzer entscheidet ausdrücklich). B19 LiDAR/RoomPlan/Mesh/Punktwolke **nie automatisch führend**; Roh-Mesh und kanonisches Modell strikt getrennt (B19.4); Adapter-Pflicht (B19.5, keine Hersteller-Formatabhängigkeit im Kernmodell); Kontrollmaße + Konfidenz + Abweichungsansicht (B19.6/7). B17 keine Black-Box-KI als Geometriequelle.
- **Bewertung:** **erfüllt.**
- **Begründung:** Das ist die fachlich zwingende Haltung: ein Scan/Plan ist **Vorschlag**, kein Modell — erst Kontrollmaß + Nutzerbestätigung + Topologie-Gate machen daraus verwendbare Geometrie. Die reproduzierbare Transformationskette (B9.5) ist genau das, was fehlt, wenn später jemand fragt „warum ist der Plan 3 cm verschoben?". Rohscan getrennt + zugriffsgeschützt (B19.4/B13) ist auch datenschutzrechtlich Pflicht (private Innenräume). **Keine strukturelle Lücke** — nur nachgelagerte Wellen (G4/G4L).

### 3.11 Bestandsworkflow (14 Schritte, Architektensicht)
Bewertung je Schritt: **unterstützt** (Vertrag trägt es als Entwurfsabsicht) / **später** (wellen-gated) / **würde blockiert** (strukturell ausgeschlossen).

| # | Schritt | Bewertung | Anmerkung |
|---|---|---|---|
| 1 | Objekt/Gebäude wählen | unterstützt | `building_models.object_id`/`configuration_project_id`, B8-Verknüpfung |
| 2 | Geschoss anlegen | unterstützt | `building_floors`, D5 |
| 3 | Bezugspunkt/Nord | unterstützt | B2 Koordinatensystem, `north_angle`, 0°=Nord |
| 4 | Außenkontur | unterstützt | B9.1 Wände, D7 (Polylinie/Rechteck/Polygon) |
| 5 | Innenwände | unterstützt | B9.1, `wall_type=internal` |
| 6 | Wandstärken | unterstützt | `reference_line_type` + Fixkante — **CAD-E1 offen** |
| 7 | Fenster/Türen | unterstützt | B9.2 Station, `building_windows/doors` |
| 8 | Räume | unterstützt | B9.3 derived — **abhängig von CAD-E2/D19** |
| 9 | Höhen/Decken | unterstützt | D5/`building_slabs` — **CAD-A2 (Höhendatum)** |
| 10 | Kontrollmaße | unterstützt | `building_dimensions` (typisierte Anker), B19 Kontrollmaße |
| 11 | Abweichungen | später | B19.7 Abweichungsansicht (G4L) |
| 12 | bestätigen | unterstützt | `status`, `confidence_status`, Revision |
| 13 | 3D kontrollieren | später | G6 Viewer (rein lesend, B1/B2) |
| 14 | für Fachmodule freigeben | später | G7-Übergabe / `building_calculation_states` |

- **Reale Bestandsgebäude (schief/uneben/gewachsen):** **unterstützt** — freie mm-Knoten, keine Orthogonal-Zwangsbedingung, Höhenprofil an der Wand, Raumhöhe je Raum. Unebenheiten/Schrägen bleiben über Roh-Mesh (B19.1) als Referenz erhalten, ohne führende Wandgeometrie zu werden.
- **3D vollständig abgeleitet:** **ja** (B2 „3D-Modell ist nie die Datenquelle", B1 Viewer rein lesend).
- **Späterer CAD-/IFC-Austausch nicht blockiert:** **nicht blockiert**, mit Auflage — `reference_line_type=axis` erfüllt die IfcWallStandardCase-Achsenforderung; **IfcRelSpaceBoundary** verlangt Raum↔Wand-Verknüpfung (CAD-A5). B16 fordert vollständiges JSON-Export-/Restore-Paket.
- **Kein unnötiges Voll-BIM:** **korrekt vermieden** — A3-Nicht-Ziele schließen Tragwerk, Genehmigung, IFC-Roundtrip, TGA-Trassen aus; B18 hält „BIM-light".

---

## 2. Auflagen (verbindlich)

| ID | Titel | Befund/Ziel | Betrifft | Schwere | Ziel-Welle |
|---|---|---|---|---|---|
| **CAD-A1** | Wandreferenz vertraglich fixieren | `reference_line_type ∈ {inner,axis,outer}` ist in B9.1 gesetzt, aber der G1a-**Schema-Entwurf** (`v1.schema.json`) kodiert ihn **nicht**. G1a muss `reference_line_type` als Pflichtfeld der Wand aufnehmen **und** die deterministische Innen-/Außenkanten-Ableitung + Fixkanten-Regel bei Dickenänderung spezifizieren/testen. | B9.1, TeilC.building_walls, v1.schema.json | pflichtpflichtig (**pruefpflichtig**) | G1a |
| **CAD-A2** | Höhendatum + Fortpflanzung festschreiben | Vertrag muss benennen, welche Höhen (OKFF/Rohdecke/lichte/Geschosshöhe/Deckenstärke) **geführt** und welche **abgeleitet** sind; ±0,00 = OKFF EG; Wand-`bottom_z` konsistent zum Geschoss; Abhängigkeitskette (B3) je Höhenänderung. | B2, B3, D5, TeilC.building_floors, C1.calculation_state | pruefpflichtig | G1a→G1b |
| **CAD-A3** | Öffnungs-Station eindeutig definieren | Station = Abstand entlang gerichteter Bezugslinie **ab Start**, gemessen bis **nähere Öffnungskante**, über `host_segment_index` bei Polylinien; `sill_height_mm`/`lintel_height_mm` mit benanntem Bezug (OKFF/Wandunterkante); Rohbau- vs. Fertigmaß: welches führt die Topologieprüfung. | B9.2, TeilC.building_openings | pruefpflichtig | G1a |
| **CAD-A4** | Umlaufsinn auch für Wand/Raum kanonisch | B9.4 fixiert Slab-Umlauf; G1a muss den **Raum-/Wand-Umlaufsinn** (Rauminnen-Seite, Außennormale, Wand-Azimut) verbindlich festlegen — Voraussetzung für Innen/Außen-Bestimmung, Fassadenausrichtung und PV. | B9.4, B9.1, B9.3 | pruefpflichtig | G1a→G2c |
| **CAD-A5** | Raum-Boundary an Wände binden | `building_rooms` (derived_topology) muss die Boundary auf **Wand-/Kanten-Referenzen** stützen (nicht nur freie Knotenliste), damit Netto-Wandfläche je Raum, Laibungszuordnung und spätere IfcRelSpaceBoundary robust sind. | B9.3, TeilC.building_rooms | pruefpflichtig | G2c |
| **CAD-A6** | Toleranzvertrag zentralisieren + zusammenführen | B10 verlangt **einen** benannten, getesteten Toleranzsatz. Der Repo-Entwurf-Validator vergleicht exakt + hartkodiert `1e-6` und ignoriert `config/geometrie.php`/`GeometrieToleranz`/`TopologieGate`. G1a-Umsetzung muss **denselben** Toleranzvertrag nutzen (Mindestsegment, Selbstberührung, Wandanschluss-Spalt), sonst zweites Geometrie-Urteil. | B10, config/geometrie.php, TopologieGate | **pruefpflichtig** | G1a |
| **CAD-A7** | Höhenprofil/Schrägen nicht wegdefinieren | Wand `height bzw. Höhenprofil` (B9.1) und Raumhöhe je Raum müssen im G1b-Schema so angelegt sein, dass Kniestock/Abseite/DG-Schräge und unterschiedliche Raumhöhen später **ohne Strukturbruch** möglich sind (nicht rein prismatisch festzementieren). | B9.1, D5, D-Dach | hinweis→pruefpflichtig | G1b (Weichenstellung), G7/G8 (Ausbau) |

*Der Repo-Entwurf `v1.schema.json`/`CanonicalBuildingModelValidator.php` erfüllt CAD-A1/A3/A4/A6 heute **nicht** und ist deshalb ausdrücklich **kein** G1a-Vertrag, sondern Vorstudie. Das ist kein Mangel des V3.3-Vertrags, sondern Beleg dafür, dass G1a erst noch stattfinden muss.*

---

## 3. Offene Planner-Entscheidungen

| ID | Entscheidung | Optionen | Empfehlung (CAD-fachlich) | Bis Welle |
|---|---|---|---|---|
| **CAD-E1** | Aufnahme-Voreinstellung der Wandreferenz + Umstellbarkeit | (a) Default `axis` + verlustfreie Umrechnung inner/outer über Dicke; (b) Default `outer` (fassadentreu); (c) Default `inner` (raumtreu) | (a) **`axis` als kanonische Speicherreferenz**, Anzeige/Eingabe wahlweise inner/outer, Fixkante pro Wand wählbar. Achse ist die einzige dickenunabhängige, verlustfrei umrechenbare Referenz und deckt die IFC-Achsenforderung. | G1a (ADR), vor G1b |
| **CAD-E2** | D19 — Wandanschlüsse abgeleitet oder persistiert (`building_wall_junctions`) | (a) deterministisch abgeleitet; (b) als Entität persistiert | Entscheidung ist **blockierend für G2c**. CAD-Tendenz: **abgeleitet** für L/T/X-Standardfälle (weniger Zustand), **persistierte Junction nur** für Sonderauflösungen (Gehrung/Versatz). ADR in G1a Pflicht. | **G1a (ADR), zwingend vor G1b** |
| **CAD-E3** | Flächen-Regelwerk: DIN 277 (geom. NGF) vs. WoFlV (Wohnfläche) | (a) nur DIN 277 geometrisch; (b) DIN 277 + optional WoFlV mit Anrechnungsfaktoren | (a) **zunächst nur geometrische NGF/BGF (DIN 277)** ausweisen; WoFlV erst mit eigener, ausdrücklich beauftragter Regelwerk-Welle (A3-Nicht-Ziel). Keine „Wohnfläche" ohne festgelegtes Regelwerk. | vor der Flächen-Ausweis-Welle |

---

## 4. Gesamturteil

**freigabefähig_mit_auflagen.**

Der in **V3.3 spezifizierte Modellvertrag ist fachlich baureif** als Grundlage für den Eintritt in G1a: Hierarchie, Wandreferenzlinie (inner/axis/outer mit deterministischer Kantenableitung), parametrisch verankerte Öffnungen (Station statt XY), getrennte Höhenbegriffe, revisions-/schema-getrennte Versionierung, Vorschlags-Pflicht bei Import/Scan, „eine Geometrie-Wahrheit" (kein zweiter Store, 3D rein abgeleitet), zentralisierter Toleranzvertrag und Clean-Room-Referenzdisziplin sind vorhanden und fachlich korrekt. Die härtesten CAD-Grundmängel eines naiven Modells sind auf Spezifikationsebene **bereits vermieden**.

**Freigabe zum G1a-Start ist an folgende Bedingungen geknüpft:**
1. **CAD-E2 (D19)** — Wandanschluss abgeleitet vs. persistiert — ist als **ADR in G1a** zu entscheiden; ohne diese Entscheidung darf **G2c nicht beginnen** (blockierend downstream, nicht für G1a-Start selbst).
2. Die Auflagen **CAD-A1/A3/A4/A6** sind in der G1a-Schema-/Validator-Umsetzung abzuarbeiten und **vom technischen Evaluator einzeln** nachzuprüfen — insbesondere: der bereits im Repo liegende `v1.schema.json`-Entwurf **erfüllt sie nicht** und ist als G1a-Vertrag **nicht heranzuziehen**.
3. **CAD-E1/E3** sind Planner-Entscheidungen mit Empfehlung; CAD-E1 vor G1b, CAD-E3 vor der Flächen-Ausweis-Welle.

Keine blockierende Auflage verhindert den **Start** der reinen Analyse-/Schema-Welle G1a; die blockierenden Wirkungen (D19) liegen an der Schwelle zu G1b/G2c. Höchste Risikoklasse offener Punkte: **prüfpflichtig** (keine strukturellen Grundmängel des V3.3-Vertrags).

---

## 5. Maschinenlesbarer Anhang

```json
{
  "pruefung": "cad-modellvertrag-v1",
  "gegenstand": ["V3.3#B2", "V3.3#B9", "V3.3#B10", "V3.3#B19", "V3.3#TeilC", "V3.3#D5-D13"],
  "stand": "V3.3-auftragstext + repo HEAD=975f44d + entwurf v1.schema.json(uncommitted)",
  "gesamturteil": "freigabefaehig_mit_auflagen",
  "befunde": [
    {
      "id": "CAD-A1",
      "typ": "auflage",
      "pruefpunkt": "3.2",
      "titel": "Wandreferenz im G1a-Schema kodieren",
      "befund": "reference_line_type inner/axis/outer ist in B9.1 spezifiziert, im Repo-Entwurf v1.schema.json aber nicht enthalten.",
      "begruendung": "Ohne fixe Bezugslinie + deterministische Kantenableitung + Fixkanten-Regel driften Fassaden- und Raumflaechen um eine halbe Wanddicke; Aufmass misst mal aussen, mal lichtes Innenmass.",
      "betrifft": ["B9.1", "TeilC.building_walls", "v1.schema.json"],
      "schwere": "pruefpflichtig",
      "ziel_welle": "G1a"
    },
    {
      "id": "CAD-A2",
      "typ": "auflage",
      "pruefpunkt": "3.6",
      "titel": "Hoehendatum und Fortpflanzung festschreiben",
      "befund": "OKFF/Rohdecke/lichte/Geschosshoehe/Deckenstaerke sind getrennt vorhanden; welche gefuehrt vs. abgeleitet sind, ist nicht festgelegt.",
      "begruendung": "Sonst zwei Hoehenwahrheiten (gemessen vs. gerechnet); Fussbodenaufbau variiert und darf nicht rueckgerechnet werden.",
      "betrifft": ["B2", "B3", "D5", "TeilC.building_floors", "C1.calculation_state"],
      "schwere": "pruefpflichtig",
      "ziel_welle": "G1a"
    },
    {
      "id": "CAD-A3",
      "typ": "auflage",
      "pruefpunkt": "3.4",
      "titel": "Oeffnungs-Station eindeutig definieren",
      "befund": "Station entlang gerichteter Bezugslinie ist gesetzt; Startende, gemessene Kante (nahe Kante), host_segment_index-Bezug und Bruestung/Sturz-Bezug muessen benannt werden.",
      "begruendung": "Ohne eindeutige Herkunft driften 2D-Editor, 3D-Viewer und Heizlast-Laibung auseinander; Rohbau- vs. Fertigmass muss die Topologiepruefung eindeutig fuehren.",
      "betrifft": ["B9.2", "TeilC.building_openings"],
      "schwere": "pruefpflichtig",
      "ziel_welle": "G1a"
    },
    {
      "id": "CAD-A4",
      "typ": "auflage",
      "pruefpunkt": "3.8",
      "titel": "Umlaufsinn auch fuer Wand/Raum kanonisch",
      "befund": "B9.4 fixiert nur Slab-Umlauf; Raum-/Wand-Umlaufsinn (Innenseite/Aussennormale/Wand-Azimut) ist offen.",
      "begruendung": "Innen/Aussen-Bestimmung, Fassadenausrichtung und PV-Ausrichtung sind ohne festen Umlaufsinn nicht deterministisch, besonders bei schiefen Waenden.",
      "betrifft": ["B9.4", "B9.1", "B9.3"],
      "schwere": "pruefpflichtig",
      "ziel_welle": "G1a"
    },
    {
      "id": "CAD-A5",
      "typ": "auflage",
      "pruefpunkt": "3.11",
      "titel": "Raum-Boundary an Waende binden",
      "befund": "building_rooms.derived_topology stuetzt sich bisher auf eine freie Knotenliste ohne verbindliche Wand-/Kanten-Referenz.",
      "begruendung": "Netto-Wandflaeche je Raum, Laibungszuordnung und IfcRelSpaceBoundary brauchen die Kante-zu-Wand-Bindung; sonst kann eine Raumkante ohne Wand verlaufen.",
      "betrifft": ["B9.3", "TeilC.building_rooms"],
      "schwere": "pruefpflichtig",
      "ziel_welle": "G2c"
    },
    {
      "id": "CAD-A6",
      "typ": "auflage",
      "pruefpunkt": "3.3",
      "titel": "Toleranzvertrag zentralisieren und zusammenfuehren",
      "befund": "Repo-Entwurf-Validator vergleicht exakt + hartkodiert 1e-6 und ignoriert config/geometrie.php/GeometrieToleranz/TopologieGate.",
      "begruendung": "B10 verlangt einen benannten, getesteten Toleranzsatz; zwei Toleranzregime erzeugen divergente Geometrie-Urteile (min_segment, Selbstberuehrung, Wandanschluss-Spalt).",
      "betrifft": ["B10", "config/geometrie.php", "TopologieGate"],
      "schwere": "pruefpflichtig",
      "ziel_welle": "G1a"
    },
    {
      "id": "CAD-A7",
      "typ": "auflage",
      "pruefpunkt": "3.5",
      "titel": "Hoehenprofil/Schraegen nicht wegdefinieren",
      "befund": "Wand height bzw. Hoehenprofil (B9.1) und Raumhoehe je Raum muessen G1b-schema-seitig Kniestock/Abseite/DG-Schraege offen halten.",
      "begruendung": "Ein rein prismatisches Wandmodell wird zur eingebauten Sackgasse fuer DG/Bestand; die Weiche muss jetzt gestellt werden, Ausbau spaeter.",
      "betrifft": ["B9.1", "D5", "D-Dach"],
      "schwere": "hinweis",
      "ziel_welle": "G1b"
    },
    {
      "id": "CAD-E1",
      "typ": "entscheidung",
      "pruefpunkt": "3.2",
      "titel": "Aufnahme-Voreinstellung Wandreferenz",
      "befund": "Default-reference_line_type und verlustfreie Umstellbarkeit sind offen.",
      "begruendung": "Achse ist die einzige dickenunabhaengige, verlustfrei umrechenbare Referenz und erfuellt die IFC-Achsenforderung.",
      "betrifft": ["B9.1"],
      "schwere": "pruefpflichtig",
      "ziel_welle": "G1a"
    },
    {
      "id": "CAD-E2",
      "typ": "entscheidung",
      "pruefpunkt": "3.3",
      "titel": "D19 Wandanschluesse abgeleitet vs. persistiert",
      "befund": "building_wall_junctions ist optional; Entscheidung offen.",
      "begruendung": "Ohne Entscheidung ist ein Grundriss optisch geschlossen, topologisch aber uneindeutig; blockiert G2c.",
      "betrifft": ["C2.building_wall_junctions", "D19", "B9.1"],
      "schwere": "blockierend",
      "ziel_welle": "G1a"
    },
    {
      "id": "CAD-E3",
      "typ": "entscheidung",
      "pruefpunkt": "3.5",
      "titel": "Flaechen-Regelwerk DIN 277 vs. WoFlV",
      "befund": "Regelwerk fuer Wohnflaeche ist bewusst offen (A3-Nicht-Ziel).",
      "begruendung": "Geometrische NGF (DIN 277) ist nicht rechtliche Wohnflaeche (WoFlV mit Anrechnungsfaktoren); Vermischung ist ein teurer Fehler.",
      "betrifft": ["A3", "B9.3"],
      "schwere": "pruefpflichtig",
      "ziel_welle": "vor Flaechen-Ausweis-Welle"
    },
    {
      "id": "CAD-H1",
      "typ": "erfuellt",
      "pruefpunkt": "3.10",
      "titel": "Import/Scan als Vorschlag, kein Store",
      "befund": "B6/B9.5/B17/B19 fuehren Import und Scan als bestaetigungspflichtigen Vorschlag; Roh-Mesh getrennt, nie fuehrend.",
      "begruendung": "Fachlich zwingend; erst Kontrollmass + Bestaetigung + Topologie-Gate machen verwendbare Geometrie.",
      "betrifft": ["B6", "B9.5", "B17", "B19"],
      "schwere": "hinweis",
      "ziel_welle": "G4/G4L"
    }
  ],
  "workflow_check": {
    "schritt_01_objekt_gebaeude": "unterstuetzt",
    "schritt_02_geschoss": "unterstuetzt",
    "schritt_03_bezugspunkt_nord": "unterstuetzt",
    "schritt_04_aussenkontur": "unterstuetzt",
    "schritt_05_innenwaende": "unterstuetzt",
    "schritt_06_wandstaerken": "unterstuetzt_mit_entscheidung_CAD-E1",
    "schritt_07_fenster_tueren": "unterstuetzt",
    "schritt_08_raeume": "unterstuetzt_abhaengig_CAD-E2",
    "schritt_09_hoehen_decken": "unterstuetzt_mit_auflage_CAD-A2",
    "schritt_10_kontrollmasse": "unterstuetzt",
    "schritt_11_abweichungen": "spaeter",
    "schritt_12_bestaetigen": "unterstuetzt",
    "schritt_13_3d_kontrollieren": "spaeter",
    "schritt_14_freigabe_fachmodule": "spaeter"
  }
}
```
