# KONZEPT — Dachschichten-Modell: Ticket bleibt Basis, Playground-Schichtenlogik wird herausgelöst

```yaml
zustand: KONZEPT (gedacht, nicht gebaut) — Yama 21.08.2026, Wortlaut der Analyse übernommen
einordnung_dirigent: Welle 2 PRODUKT — Bau des ersten vertikalen Schnitts NACH TESTBEREIT (Gesamtauftrag: Produktarbeit nicht mit Sicherheit/Rechten vermischen). Reuse-/Extraktions-Matrix wird vorab lesend erstellt.
quellen: Playground (nur LESEN — Mehr-App-Regel, docs/regelwerk/QUELLEN.md): src/pages/energie/DachplanerProPage.tsx:2171ff (Schichtengruppen), :3617ff (Bedienung), src/stores/roofTypes.ts:90 · Ticket: resources/planner/hausplaner/domain/scene.types.ts:315 (RoofNode), renderers/three-d/szene.ts:137 (Licht), :569 (Dachmaterial), docs/rollenkette/werkbank/02-WERKZEUGE/W-26-dachschichten/1-ZWECK.md
im_bestand_gesucht: Playground-Dachplaner (Schichtengruppen, Explosionsansicht), Ticket-RoofNode/dachMesh/W-26 — Ergebnis: Schichten-Modell fehlt im Ticket, Generatoren existieren im Playground als monolithische Seite
```

## Ergebnis der Analyse (Yama, 21.08.)

Der bessere Ausgangspunkt ist eindeutig der aktuelle Ticket-Hausplaner. Der Playground sollte nicht
komplett übernommen werden. Er besitzt jedoch den besseren Dachschichten- und Explosionsmodus,
dessen Logik gezielt in den Hausplaner übertragen werden sollte.

| Bereich | Ticket-Hausplaner | Playground | Bewertung |
|---|---|---|---|
| Dachgeometrie und Planung | Sattel, Walm, Pult, Flach sowie L/T/U; im `SceneDocument` integriert | umfangreiche Dachkonstruktion, aber große monolithische Seite | **Ticket besser** |
| Gauben, Fenster, Kamin | Modell, Geometrie und 3D vorhanden; bei komplexen Dachflächen teilweise Prüfmarker | umfangreich und unmittelbar bedienbar | knapp **Playground** bei Bedienung |
| Tragwerk und Schichten | kein vollständiges Dachschichtenmodell | Sparren, Bahn, Dämmung, Konter- und Traglattung, Deckung getrennt gerendert | **Playground deutlich besser** |
| Ein-/Ausblenden | ganzes Dach sichtbar/unsichtbar, keine Schichtensteuerung | 15 einzeln schaltbare Gruppen, Transparenz, Explosionsansicht | **Playground deutlich besser** |
| Speichern/Undo | gemeinsame Modellwahrheit, Commands, Speichern | Ebenensichtbarkeit/Transparenz nur lokaler Seitenzustand | **Ticket deutlich besser** |
| Beleuchtung | ACES-Tone-Mapping, PMREM, Schatten, mehrere Lichter | einfacher | **Ticket besser** |
| Materialien | Dach weitgehend einfarbig | erkennbare Materialien, prozedurale Texturen | **Playground wirkt reicher** |
| Fotorealismus | bessere Lichtbasis, keine Dachtexturen | detaillierter, aber nur Canvas-Muster | **keiner fotorealistisch** |
| Wartbarkeit | getrennte Geometrie, Renderer, Commands, Modell | ~3.700 Zeilen in einer Seite | **Ticket deutlich besser** |

**Gut im Playground:** getrennte 3D-Gruppen für Dachstuhl/Sparren/Pfetten · Unterspannbahn/Schalung ·
Aufsparrendämmung · Konterlattung · Traglattung · Dacheindeckung · Dachöffnungen/-aufbauten ·
Gaubenstuhl/Gaubenhaut · Wechselhölzer/Anschlüsse · Kehlen/Grate — getrennt erzeugt, über
Ebenenliste schaltbar, mit Transparenz und Explosionsansicht. **Schwachpunkt:** Sichtbarkeit,
Namen, Transparenz sind nur lokaler React-Zustand, weder im `RoofSlice` noch in der Vorlage
(`roofTypes.ts:90`) — nach Neuladen nicht wiederherstellbar.

**Besser im Ticket:** Dach ist Teil des `SceneDocument` (Form, Kontur, Neigung, Azimut, Überstand,
Aufbauten, L/T/U — `scene.types.ts:315`); 3D-Grundlage moderner (`szene.ts:137`). **Aber:** Dach
bekommt nur ein `MeshStandardMaterial` mit Farbe (`szene.ts:569`), kein Schichtenmodell; W-26 bestätigt:
Dachschichten sind kein gebautes Werkzeug.

## Zielkonzept (Yama)

1. **Ticket bleibt Modell- und Planungsbasis** — `SceneDocument`, Commands, Speichern, Undo,
   Dachgeometrie, 2D/3D führend.
2. **Playground-Schichtengeneratoren werden herausgelöst** — Sparren, Dämmung, Bahnen, Lattung,
   Deckung als getrennte Rendererbausteine, nicht als Teil einer Seite.
3. **Physischer Dachaufbau kommt ins Dachmodell:** Tragwerk (Sparren, Pfetten, Wechsel) ·
   Schalung/Unterspannbahn · Dämmung/Dampfbremse · Konterlattung · Traglattung · Dacheindeckung ·
   Anschlüsse/Kehlen/Grate · Dachaufbauten/Öffnungen · PV-Montagesystem. Jede Schicht: Typ, Dicke,
   Material-/Produkt-ID, Reihenfolge.
4. **Ansichtszustand getrennt von Konstruktion:** Dicke/Material/Reihenfolge = Projektdaten; Auge,
   Transparenz, Solo, Explosionsabstand = gespeichertes Ansichtsprofil.
5. **Drei Darstellungsmodi:** Planung/CAD · Konstruktion/Schichten (Auge, Solo, Ghost, Schnitt,
   Explosion) · Präsentation/Fotorealistisch (PBR, Deckung, Schatten, Umgebung).
6. **Keine Playground-Texturen für Fotorealismus** (512×512-Canvas) — maßstäbliche PBR-Sätze
   (Base Color, Normal, Roughness, ggf. AO/Displacement); Ziegel über Instanzen/LOD.

## Testbarkeit — erste seriös testbare Ausbaustufe
Ein Dach mit **mindestens fünf Schichten** kann: angelegt und geändert · einzeln ein-/ausgeblendet ·
explodiert dargestellt · gespeichert und neu geladen · per Undo/Redo behandelt · in 2D und 3D
konsistent angezeigt werden. Danach separat die fotorealistische Materialstufe.
*(Die visuelle Qualität wurde in der Quell-Sitzung nicht per Browser abgenommen — Code-/Rendering-
Analyse, keine visuelle Endabnahme. Kein Code verändert.)*

## Nächster Schritt (Yama): Planner-Auftrag für den ersten vertikalen Schnitt
**„RoofNode-Schichten → Commands → Speichern → Ebenenpanel → 3D-Explosionsansicht"** —
Einordnung Dirigent: Auftragsblatt wird nach `planner-slice-orchestrator` geschnitten (Bedarf →
Ticket-Reuse-Prüfung → Extraktion aus dem Playground → erst dann Neubau), Reuse-/Extraktions-Matrix
vorab; **Bau nach `TESTBEREIT`** des Gesamtauftrags.
