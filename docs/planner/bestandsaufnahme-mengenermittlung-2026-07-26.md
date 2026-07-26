# Bestandsaufnahme — Flächen-, Volumen- und Massenermittlung

**Vom:** Planner · **26.07.2026** · **Anlass:** Yamas Vorlage „Geometrische Flächen-, Volumen- und
Massenermittlung" (33 Abschnitte). **Auftrag: Bestand aufnehmen, nicht bauen.**

**Alles unten ist gemessen** — `grep`/`sed` gegen `resources/planner/hausplaner/`, Stand `a9a6951`.
Wo ich nichts gefunden habe, steht **„nicht vorhanden"** und nicht „vermutlich fehlt".

---

## 1. Das Ergebnis in drei Sätzen

**Das Fundament ist besser als erwartet.** Polygonflächen, Raumerkennung mit echter Flächenzahl,
Materialschichten mit U-Wert-Rechnung, Dachgeometrie mit Neigung und Azimut, Maßketten und **eine
fertige Mengenermittlung für Holz** sind gebaut und getestet.

**Was fehlt, ist nicht die Geometrie — es ist die Buchhaltung darüber.** Es gibt keine Stelle, die
eine ermittelte Menge **festhält**, keine Aggregation über Raum → Geschoss → Gebäude, keinen
Öffnungsabzug an Wänden und keine Rückverfolgbarkeit.

**Die Vorlage beschreibt ein eigenes Teilprodukt.** Sie ist deutlich größer als alles, was seit
Wochen an der Insel gebaut wurde — **und sie ist die erste Sache, die den Hausplaner vom Zeichenwerkzeug
zum Kalkulationswerkzeug macht.** Beides gehört gesagt.

---

## 2. Was vorhanden ist — gemessen, mit Fundstelle

### 2.1 Geometrie und Rechnung

| Können | Wo |
|---|---|
| **Polygonfläche in m²** | `geometry/polygonFlaeche.ts` → `polygonFlaecheM2()` |
| **Raumerkennung mit Fläche** | `geometry/roomDetection.ts` → `erkenneRaeume()`, `signierteFlaeche()`; liefert `flaecheMm2` je Raum |
| **Grundrissfläche, Formen, Ecken** | `geometry/grundriss.ts` → `grundrissFlaecheM2()`, `eckenAnalyse()`, vier Formen |
| **Bounding-Box, Maßketten** | `geometry/masskette.ts`, `geometry/bemassung.ts` |
| **Materialschichten + U-Wert** | `geometry/wandaufbau.ts` → `Schicht`, `BauteilArt` (`aussenwand · innenwand · dach · boden`), `berechneUWert()` |
| **Mengenermittlung Holz** | `geometry/holzMengen.ts` → `holzMengenAusListe()` — **eine vollständige Mengenrechnung existiert bereits** |
| **Dachstuhl-Stücklisten** | `geometry/schifterListe.ts`, `sparrenBerechnung.ts`, `sparrenTrennung.ts` |
| **Dachflächen, Ausschnitte, Aufbauten** | `dachGeometrie.ts`, `dachWerte.ts`, `dachAusschnitt.ts`, `dachOeffnung.ts`, `dachVerschneidung.ts`, `gaubeGeometrie.ts`, `dachUForm.ts` |
| **Belegung einer Fläche mit Elementen** | `geometry/pvBelegung.ts` — das Muster für „wieviel passt auf diese Fläche" |
| **Heizungsseitige Auslegung** | `fbhAuslegung.ts`, `heizkoerperLeistung.ts`, `heizkreisVerteiler.ts` |

### 2.2 Das Datenmodell trägt schon viel

| Trägt | Feld |
|---|---|
| Wand | `thickness`, `height`, `start`, `end`, `materialId?` |
| Öffnung | `width`, `height`, `sillHeight`, `hostWallId`, `offsetFromWallStart`, `thermalProperties.uValue` |
| Dach | `neigungGrad`, `firstAzimutGrad`, `ueberstandMm`, `traufhoeheMm`, `polygon`, `aufbauten[]` |
| Decke | `polygon`, `oeffnungen[]`, **`schichten[]`** (`materialId`, `dickeMm`) |
| Zone | `zoneType: 'room'` (abgeleitet aus der Raumerkennung) |
| Dokument | `materials: MaterialDefinition[]`, `roofs[]`, `ceilings[]`, `levels[]` |

**Damit sind Neigung, Orientierung, Öffnungsmaße und Schichtdicken bereits im Modell** — die vier
Größen, an denen solche Vorhaben sonst zuerst scheitern.

### 2.3 Die Oberfläche zeigt bereits Flächen

`HausplanerApp.tsx:1486` schreibt die Raumfläche in den Plan; `:2112` und `:2128` zeigen
**„Räume: n · Fläche gesamt: x m²"**. **Eine Aggregation über Räume existiert also schon** — als
Anzeige, nicht als Ergebnis.

### 2.4 Die Werkzeuge sind schon benannt

Der 110er-Katalog trägt bereits die Kategorie **„Messen"**:

```
distanz-messen · bemassen · winkel-messen · flaeche-messen · volumen-messen
```

und eine Kategorie **„Material"**. **Die Werkzeugnamen aus der Vorlage sind zur Hälfte schon
vergeben** — sie sind unter den 110, die in AUF-50 funktionstüchtig gemacht werden sollen.

---

## 3. Was fehlt — gemessen, nicht vermutet

### 3.1 Die drei Lücken, die alles andere tragen

**(a) Keine Wandflächenrechnung.** `grep` auf `wandflaeche`, `nettoflaeche`, `bruttoflaeche` über
die gesamte Insel: **kein Treffer außerhalb von Tests.** Die Öffnungen liegen im Modell, aber
**niemand zieht sie von einer Wandfläche ab** — weil es keine Wandfläche gibt.

**(b) Keine Mengen-Datenstruktur.** Es gibt kein `QuantityTakeoff`, kein Ergebnisobjekt, keine
Persistenz. **Jede heute sichtbare Fläche wird beim Rendern neu gerechnet und danach vergessen.**
Damit ist auch nichts freigebbar, nichts vergleichbar und nichts nachvollziehbar.

**(c) Keine Aggregationskette.** Raum → Geschoss → Gebäude → Projekt existiert **nicht**. Die eine
vorhandene Summe (Räume eines Geschosses) ist eine Zeile im JSX, keine Funktion.

### 3.2 Was im Modell fehlt

| fehlt | Folge |
|---|---|
| **Dichte am Material** | `MaterialDefinition` trägt `id`, `name`, `color`, `uValue` — **keine Dichte, keine Stärke**. ⇒ **Gewichte und Materialmengen sind heute nicht ableitbar.** |
| **Grundstück und Außenanlagen** | `grep` auf `grundstueck`, `garten`, `terrasse`, `balkon` in `scene.types.ts`: **kein Treffer.** Abschnitt 3.5 der Vorlage hat im Modell **keine Grundlage** |
| **Manuelle Fläche/Volumen** | keine Struktur, kein Command, kein Werkzeug |
| **Regelwerke** | Abzugsregeln (übermessen, Mindestgröße, Laibung) existieren nirgends — auch nicht als Konstante |
| **Verschnitt/Zuschlag** | nicht vorhanden |

### 3.3 Was im Ablauf fehlt

- **Invalidierung.** Ändert sich Geometrie, veraltet heute nichts — es gibt nichts zum Veralten.
- **Rückverfolgbarkeit.** Keine Formel, keine Quelle, keine Revision an einem Ergebnis.
- **Export.** `HausplanerApp.tsx:607` — **PNG, und sonst nichts.** Kein XLSX, kein CSV, kein PDF.
- **Prüfungen** (offene Kontur, doppelt abgezogen, negatives Volumen): nicht vorhanden.

---

## 4. Meine Einschätzung zur Vorlage

**Sie ist fachlich gut und im Umfang unrealistisch als ein Vorhaben.** 33 Abschnitte, über 300
benannte Größen, elf Ermittlungsebenen. Zum Vergleich: die gesamte Layout-Inventur hatte **neun**
Befunde und hat zwei Tage gebraucht.

**Was ich an ihr übernehmen würde, ohne Abstriche:**

1. **Der Architekturgrundsatz.** *Jede Menge muss bis zum Ursprungsobjekt, zur Geometrie, zur Formel
   und zur Revision zurückverfolgbar sein.* **Das ist die Regel, die den Unterschied zwischen einer
   Zahl und einem Angebot ausmacht** — und sie muss vor der ersten Zeile stehen, nicht danach.
2. **„Bestandscode-first, keine parallele Flächenengine."** Steht in Abschnitt 31 der Vorlage und
   deckt sich mit K4. **Es gibt bereits eine Flächenrechnung; eine zweite wäre der schlimmste
   Ausgang dieses Vorhabens.**
3. **Die Trennung Fachobjekt → Geometrie → Abzug → Zuordnung → Aggregation.** Sie ist richtig und
   erklärt, warum (a), (b) und (c) oben die Reihenfolge bestimmen.

**Wovon ich abraten würde:**

- **Alles auf einmal.** Grundstück, Außenanlagen, GAEB-Export, ERP-Übergabe und Fassade nach
  Himmelsrichtung sind eigene Vorhaben und blockieren das, was heute Geld wert wäre.
- **Mit dem Dashboard anfangen.** Eine Auswertungsfläche über Zahlen, die es nicht gibt, ist die
  geführte Planung von heute Morgen noch einmal — nur größer.
- **Mit der manuellen Flächenmarkierung anfangen.** Sie ist sichtbar und verführerisch, aber sie
  liefert **Zahlen ohne Herkunft**. Erst muss die automatische Kette stehen, sonst wird das manuelle
  Werkzeug die Hauptquelle — und dann ist die Rückverfolgbarkeit von Anfang an verloren.

---

## 5. Was ich als Reihenfolge vorschlage

**Nicht als Auftrag — als Vorschlag, über den Yama entscheidet.** Jede Stufe ist für sich nützlich
und für sich abnehmbar.

| Stufe | Inhalt | Warum zuerst |
|---|---|---|
| **M1** | **Wandfläche brutto/netto je Wand**, mit Öffnungsabzug. Rein, ohne DOM, ohne Persistenz | Es ist die eine fehlende Rechnung, auf der Fassade, Putz, Dämmung, Anstrich und Heizlast **alle** aufsetzen. Alles Nötige liegt im Modell |
| **M2** | **Ein Mengenergebnis als Datenstruktur** — Wert, Einheit, Bezugsobjekt, Formel, Revision. Noch ohne Persistenz | Ohne sie ist jede weitere Zahl flüchtig. **Hier gehört der Architekturgrundsatz hin, nicht später** |
| **M3** | **Aggregation Raum → Geschoss → Gebäude** für das, was M1/M2 liefern | Der erste Moment, in dem Yama eine Zahl sieht, die er heute von Hand rechnet |
| **M4** | **Mengenliste als Fläche + Export (CSV/XLSX)** | Ab hier ist es ein Werkzeug für Angebote, nicht mehr für den Bildschirm |
| **M5** | **Materialmengen** — braucht **vorher** die Dichte am Material (Schema-Erweiterung, additiv) | Erst hier wird aus Fläche eine Bestellmenge |
| **M6** | **Manuelle Fläche/Volumen** | Erst wenn die automatische Kette steht und die manuelle Ergänzung bleibt, was sie sein soll |
| **später** | Grundstück, Außenanlagen, Fassade nach Himmelsrichtung, GAEB, Verschnittregelwerke | Eigene Vorhaben |

**M1 bis M3 sind zusammen kleiner als das, was seit gestern Abend gebaut wurde.** Das ist der Grund,
warum ich sie zuerst nennen würde.

---

## 6. Zwei Fragen, die vor M1 zu klären sind

**Beide gehören Yama, beide sind fachlich, keine ist technisch:**

1. **Nach welchem Maß wird gerechnet?** Rohbaumaß oder Fertigmaß; lichte Höhe oder konstruktive
   Höhe. **Die Vorlage nennt beides als Option — aber die erste gebaute Zahl legt die Gewohnheit
   fest**, und eine spätere Umstellung macht alle vorher ermittelten Mengen falsch.
2. **Wie wird übermessen?** Werden kleine Öffnungen abgezogen oder übermessen, und ab welcher Größe?
   Das ist in jedem Gewerk anders geregelt und **entscheidet über jede Netto-Zahl im System.**

**Solange diese zwei offen sind, würde ich M1 nicht beauftragen** — nicht weil es technisch nicht
ginge, sondern weil die Zahl sonst richtig gerechnet und fachlich falsch wäre.

---

## 7. Was diese Bestandsaufnahme **nicht** ist

**Kein Auftrag, kein Zeitplan, keine Zusage.** Es ist gemessen, was da ist und was fehlt.
**Nichts davon steht auf der Auftragstafel** — bis Yama entscheidet, ob und in welchem Zuschnitt.

**Und die laufende Arbeit ist unberührt:** AUF-40 Teil A, AUF-74, AUF-58 und AUF-75 stehen, die
Layout-Inventur ist nach zwei Posten abgeschlossen. **Dieses Vorhaben ist größer als alles davon
zusammen und darf es nicht verdrängen, solange es nicht entschieden ist.**
