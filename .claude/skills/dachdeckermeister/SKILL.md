---
name: dachdeckermeister
description: Fach-Linse Dachdeckerhandwerk für Dach-Geometrie und -Darstellung im Hausplaner (Neigung, Traufe/First/Ortgang, Kehle/Grat, Überstand, Eindeckung). Laden bei jeder Dach-Aufgabe, um Geometrie und 3D-Darstellung fachlich zu prüfen.
---

# dachdeckermeister

## Ziel
Dach-Geometrie und -Darstellung im Hausplaner fachlich absichern — die dargestellte Fläche muss der
Bauwirklichkeit und dem Grundriss entsprechen, nicht bloß „nicht leer" sein.

## Prüf-Linse (an jedem Dach-Slice)
- **Kontur sitzt auf dem Grundriss.** Traufe/Ortgang liegen auf der Wandkontur; der Dachüberstand ragt
  gleichmäßig nach außen. Versatz/Schieflage gegen den Footprint = rot (siehe `polygonBbox`-Anker in `dachMesh`).
- **Kehle & Grat.** Bei L/T/U-Verschneidung: gleiche Neigung beider Flächen + 90°-Grundrissecke ⇒ Kehle/Grat
  ist die 45°-Winkelhalbierende in der Draufsicht; Kehlneigung β = atan(tan α/√2). Innenhof (U) bleibt FREI —
  keine Fläche kippt hinein.
- **Neigung/First.** 0° = Flachdach; < 90° (cos > 0). Firstrichtung (`firstAzimutGrad`) konsistent zum
  Grundriss — sonst 90°-Fehlplatzierung. First liegt oben, Traufe unten; Höhen plausibel (Traufhöhe < First).
- **Überstand** an Traufe UND Ortgang bedacht; Aufbauten (Gaube/Kamin/Fenster) sitzen AUF der Fläche.
- **Eindeckung/Deckungsart** ist Darstellung, keine Statik — keine Traglast-Aussage aus dem Modell erfinden.

## Rote Flaggen
- „dreiecke > 0" als einziger Beleg → Ausrichtung ungeprüft (Deckungs-/Innenhof-Test fehlt).
- Zweite Flächen-/Miter-Rechnung neben `dachUForm`/`dachVerschneidung` (byte-treue Ports NICHT umschreiben).
- Erfundene Maße statt Operanden (U braucht ALLE vier: length/width/lengthB/widthB — sonst leer/Marker).

## Norm-Anker (Orientierung, nicht Selbstzweck)
Fachregeln des Dachdeckerhandwerks (Flachdach-/Deckungs-Richtlinien); DIN 1356-1 für die zeichnerische
Darstellung. Aussagen mit Beleg (Code/Norm), nie „sieht richtig aus".
