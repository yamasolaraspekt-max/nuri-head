# InDesign-Vollkatalog → Hausplaner-Einordnung

> **Rolle:** Planner. **Stand:** 2026-07-23. **Auftrag (Yama):** ALLE Funktionen/Werkzeuge von InDesign
> auflisten und in unsere Software einfügen. **Methode:** vollständig auflisten, je Eintrag Verdikt
> **✅ übernehmen · 🟦 anpassen (CAD-Äquivalent) · ⛔ nicht relevant (DTP/Druck)** + Hausplaner-Slot.
> Grund für Verdikte: InDesign ist Layout/Satz; der Hausplaner ist 2D/3D-CAD. Übernommen wird die
> **Präzisions-, Auswahl-, Transform-, Ausricht-, Fang-, Ebenen- und Ansicht-Mechanik**, nicht die Typo-/
> Druckvorstufe. Übernommene Einträge werden Werkzeuge in der Registry (§22) und wandern in den Fahrplan.

---

## TEIL A — Werkzeugpalette (Toolbar), vollständig

| InDesign-Werkzeug | Verdikt | Hausplaner-Entsprechung / Slot |
|---|:--:|---|
| Auswahl (Selection, V) | ✅ | vorhanden `auswahl` |
| Direktauswahl (Direct Selection, A) | ✅ | **Knoten/Punkt bearbeiten** (Wandpunkte, Polygon-Ecken) — fehlt |
| Seite (Page) | 🟦 | Geschoss/Ebene wählen (Level) — teils via activeLevel |
| Abstand (Gap) | 🟦 | Abstand zwischen Bauteilen justieren — optional später |
| Inhaltensammler/-platzierer (Content Collector/Placer) | ⛔ | Layout-Feature, nicht CAD |
| Text (Type, T) | 🟦 | **Beschriftung/Annotation** (Raumname, Notiz, Maßtext) — reduziert |
| Pfadtext (Type on Path) | ⛔ | Typo-Feature |
| Linie (Line, \\) | ✅ | Hilfslinie/Freie Linie/Bezugslinie — fehlt |
| Zeichenstift (Pen, P) + Ankerpunkt hinzufügen/löschen/umwandeln | ✅ | **Polylinie/Freie Kontur** (Wandzug, Raumumriss, Dachkontur) — fehlt |
| Buntstift (Pencil) + Glätten/Radierer | 🟦 | Freihand-Kontur (selten im CAD) — optional |
| Rechteckrahmen / Ellipsen-/Polygonrahmen (Frame) | 🟦 | Platzhalter-Rahmen → eher **Raum/Zone-Erstellung** |
| Rechteck / Ellipse / Vieleck (M/L) | ✅ | **Primitive Formen** (Raum, Zone, Fläche, Aussparung) — fehlt |
| Schere (Scissors, C) | ✅ | **Trennen** (Wand/Pfad an Punkt teilen) — fehlt |
| Frei transformieren (Free Transform, E) | ✅ | fehlt (siehe Transform) |
| Drehen (Rotate, R) | ✅ | **Drehen** — fehlt |
| Skalieren (Scale, S) | ✅ | **Skalieren** — fehlt |
| Neigen (Shear, O) | 🟦 | selten im Grundriss — optional |
| Verlauf / Verlauf weiche Kante (Gradient, G) | ⛔ | Grafikfüllung — nicht CAD (Material stattdessen) |
| Notiz (Note) | 🟦 | **Kommentar/Notiz am Objekt** — nützlich, später |
| Pipette (Eyedropper, I) | ✅ | **Eigenschaften übertragen** (Bauart/Material von A auf B) — fehlt |
| Messen (Measure, K) | ✅ | **Messwerkzeug** (Strecke/Winkel/Fläche) — Engine `masskette/bemassung` da, Werkzeug fehlt |
| Hand (Hand, H) | ✅ | **Ansicht schieben (Pan)** — fehlt |
| Zoom (Z) | ✅ | **Zoom-Werkzeug / Zoom-zu-Auswahl** — fehlt |
| Fläche/Kontur (Fill/Stroke, X) | 🟦 | **Material/Linienstil** je Bauteil |
| Bildschirmmodus Normal/Vorschau | 🟦 | Vorschau ohne Hilfslinien/Raster (Präsentationsmodus) |

## TEIL B — Steuerungsleiste / Transformieren-Panel (Präzision)

| Funktion | Verdikt | Slot |
|---|:--:|---|
| X/Y-Position (numerisch) | ✅ | numerische Position im Eigenschaftenpanel |
| B/H-Größe (numerisch) | ✅ | numerische Maße |
| Skalierung % | ✅ | mit Skalieren-Werkzeug |
| Drehwinkel | ✅ | mit Drehen-Werkzeug |
| Neigung (Shear) | 🟦 | optional |
| Referenzpunkt (9-Punkt-Anker) | ✅ | Bezugspunkt für Transform |
| Horizontal/Vertikal spiegeln | ✅ | Spiegeln — Engine `editierGeometrie` da |
| Konturstärke / Eckenradius | 🟦 | Linienstärke Darstellung / Ecken |

## TEIL C — Anordnen / Objekt-Menü

| Funktion | Verdikt | Slot |
|---|:--:|---|
| Ausrichten & Verteilen (Align & Distribute) | ✅ | **Ausrichten/Verteilen** — fehlt komplett (Yama-Wunsch) |
| Anordnen (Bring to Front/Back, Arrange) | ✅ | Z-/Ebenen-Reihenfolge |
| Gruppieren / Gruppierung aufheben | ✅ | **Gruppieren** von Bauteilen — fehlt |
| Sperren / Entsperren (Lock/Unlock) | 🟦 | **Schloss** — Modell `locked` + Gate da, Werkzeug fehlt (Lückenspec) |
| Ausblenden / Einblenden (Hide/Show) | 🟦 | **Auge** — Modell `visible` + Renderer da, Werkzeug fehlt (Lückenspec) |
| Kopieren und versetzen (Step & Repeat) | ✅ | **Array/Reihe duplizieren** (z. B. Sparren, Stützen, PV) — fehlt |
| Duplizieren / An Ort einfügen (Paste in place) | ✅ | Duplizieren mit Versatz |
| Pathfinder (Add/Subtract/Intersect/Exclude) | ✅ | **Bool’sche Flächen** (Räume/Zonen zusammenführen/ausschneiden) — fehlt |
| Form umwandeln (Convert Shape) | 🟦 | selten |
| Zusammengesetzter Pfad (Compound Path) | 🟦 | Flächen mit Loch (z. B. Aussparung) |
| Eckenoptionen (Corner Options) | 🟦 | optional |
| Rahmen anpassen (Fitting) | ⛔ | Bild-in-Rahmen, DTP |
| Effekte (Schatten/Transparenz) | ⛔ | Grafikeffekte — nicht CAD |
| Objektstile (Object Styles) | ✅ | **Bauteil-Vorlagen/Bauarten** — teils vorhanden (Bauart-Kataloge) |

## TEIL D — Ansicht / Raster & Hilfslinien / Fang

| Funktion | Verdikt | Slot |
|---|:--:|---|
| Zoom / An Seite anpassen / Zoom-zu-Auswahl | ✅ | Ansicht |
| Bildschirmmodus (Vorschau/Normal) | 🟦 | Präsentationsmodus |
| Lineale (Rulers) | ✅ | **Lineal** am Rand — fehlt |
| Hilfslinien (Ruler Guides) ziehen | ✅ | **Hilfslinien** — fehlt |
| Intelligente Hilfslinien (Smart Guides) | ✅ | **Auto-Ausrichthilfen** (Kanten/Mittelpunkt/Abstände) — fehlt |
| An Hilfslinien ausrichten (Snap to Guides) | ✅ | Fang an Hilfslinien |
| Dokumentraster / an Raster ausrichten (Grid/Snap) | ✅ | **Raster** sichtbar + Raster-Fang — Fang-Engine `fangKern` (raster) da |
| Grundlinienraster (Baseline Grid) | ⛔ | Typo |
| Magnet/Fang global an-aus | ✅ | **Magnet-Umschalter** — `snapSettings` im Konzept, Verdrahtung fehlt |

## TEIL E — Bedienfelder (Panels)

| Panel | Verdikt | Slot |
|---|:--:|---|
| Ebenen (Layers) | ✅ | **Layer-Panel** mit Auge/Schloss je Zeile (UI-8/§33) |
| Seiten (Pages/Master) | 🟦 | **Geschosse/Ebenen** (Levels) + evtl. Vorlagen-Geschoss |
| Ausrichten (Align) | ✅ | siehe Teil C |
| Transformieren (Transform) | ✅ | siehe Teil B |
| Pathfinder | ✅ | siehe Teil C |
| Info | ✅ | **Info** (Länge/Fläche/Anzahl der Auswahl) |
| Eigenschaften (Properties) | ✅ | kontextuelles Eigenschaftenpanel (UI-5) |
| Verknüpfungen (Links) | 🟦 | **Produkt-/DB-Verknüpfung** (Bauart↔Katalog) |
| Preflight (Prüfung) | ✅ | **Wächter/Validierung** (existiert konzeptuell: Zod-Gate, Bauordnung) |
| Farbfelder/Farbe (Swatches/Color) | 🟦 | **Material-/Farbpalette** je Bauteil |
| Kontur (Stroke) | 🟦 | Linienstil/-stärke |
| Objektstile/Absatz-/Zeichenformate | 🟦/⛔ | Objektstile → Bauteil-Vorlagen ✅; Text-/Absatzformate ⛔ (Typo) |
| Tabellen / Zellenformate | 🟦 | **Stückliste/Bauteilliste** als Tabelle (relevant!) |
| Effekte / Konturenführung / Trennungsvorschau / Trapping | ⛔ | Druckvorstufe |
| CC Libraries / Hyperlinks / Lesezeichen / Index | ⛔ | DTP/Publishing |

## TEIL F — Datei / Bearbeiten (Kernbefehle)

| Funktion | Verdikt | Slot |
|---|:--:|---|
| Neu / Öffnen / Speichern | ✅ | vorhanden (Store, 409-Konflikt) |
| Rückgängig / Wiederholen (Undo/Redo) | ✅ | vorhanden (typed Command + inverse-patch) |
| Ausschneiden/Kopieren/Einfügen | ✅ | Bauteile — teils; „an Ort einfügen" fehlt |
| Duplizieren / Step&Repeat | ✅ | siehe Teil C |
| Suchen/Ersetzen (Find/Change) | 🟦 | **Objekte finden/filtern** (nach Typ/Bauart) — nützlich |
| Rechtschreibung | ⛔ | Typo |
| Platzieren (Place) | 🟦 | **Import** (Grundriss-PDF/Bild als Unterlage) — nützlich später |
| Exportieren (PDF/EPUB) | 🟦 | **PDF-Export Plan/Stückliste** ✅ (PDF); EPUB ⛔ |
| Verpacken (Package) | 🟦 | Projekt-Paket exportieren (`configuratorPackage` existiert) |
| Drucken / Broschüre | 🟦 | **Plan drucken** ✅; Broschüre ⛔ |
| Voreinstellungen / Tastaturkürzel | ✅ | Einstellungen + Shortcuts (Registry hat Shortcuts) |

---

## Zusammenfassung & Einordnung in den Fahrplan

**Übernehmen (✅) — das professionelle CAD-Werkzeugset**, geclustert (wird Registry §22 + Fahrplan):
- **Auswahl/Navigation:** Auswahl ✓, Direktauswahl/Knoten, Hand/Pan, Zoom, Marquee-Auswahl.
- **Erstellen:** Linie, Pen/Polylinie, Rechteck/Ellipse/Vieleck, Schere/Trennen.
- **Transform:** Verschieben, Drehen, Skalieren, Spiegeln, Frei-Transform, Referenzpunkt, numerisch X/Y/B/H.
- **Anordnen:** Ausrichten & Verteilen, Anordnen (Z), Gruppieren, Sperren/Ausblenden, Step&Repeat, Pathfinder.
- **Fang/Führung:** Magnet-Umschalter, Fang (Endpunkt/Mitte/Schnitt/Lot/Raster), Hilfslinien, Smart Guides, Raster, Lineale.
- **Panels:** Ebenen/Layer, Ausrichten, Transform, Info, Eigenschaften, Preflight(=Wächter), Objektstile(=Bauarten), Tabelle(=Stückliste).
- **Datei/Bearbeiten:** Undo/Redo ✓, Suchen/Filtern, Platzieren(Unterlage), PDF-Export, Paket, Drucken, Shortcuts.
- **CAD-Ergänzung über InDesign hinaus:** **Kompass/Nordpfeil + „nach Nord ausrichten"** (Azimut) — kein InDesign-Werkzeug, aber für Bau/PV Pflicht.

**Anpassen (🟦):** Text→Beschriftung, Seiten→Geschosse, Farbe/Kontur→Material/Linienstil, Verknüpfungen→Produkt-DB, Notiz→Objekt-Kommentar, Platzieren→Grundriss-Unterlage.

**Nicht relevant (⛔) — bewusst NICHT einbauen:** Textsatz (Zeichen-/Absatz-/Pfadtext, Formate, Rechtschreibung, Grundlinienraster), Grafikeffekte/Verläufe/Transparenz, Druckvorstufe (Separation/Trapping/Konturenführung), Publishing (EPUB, Hyperlinks, Index, Lesezeichen, CC Libraries), Content Collector.

**Umsetzungsreihenfolge** (kausal, günstig zuerst — baut auf Benchmark-Stufen A/B/C auf):
1. **Stufe A** (Engine steht, nur sichtbar machen): Magnet-Umschalter+Fang, Auge/Schloss, Verschieben/Spiegeln, Bemaßung, Zoom/Pan, Undo/Redo-UI.
2. **Stufe B:** Ausrichten&Verteilen, Marquee-Auswahl, Drehen/Skalieren, Kompass/Nord, Gruppieren, Step&Repeat, Layer-Panel.
3. **Stufe C:** Direktauswahl/Knoten, Pen/Polylinie, Pathfinder, Pipette, Hilfslinien/Smart Guides/Lineal, Objektstile-Panel, Tabellen/Stückliste, Platzieren-Unterlage.

Jede Gruppe = eigener Planner→Generator→Evaluator-Zyklus, additiv, ohne Schema-Bruch; Barrierefreiheit
(Werkzeugzustand nie nur Farbe/Form: Icon + Tooltip/aria) durchgängig.
