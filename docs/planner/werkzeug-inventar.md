# Hausplaner Werkzeug-Inventar

Gesamt: **110 Werkzeuge**

## Auswahl

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Auswahl | Einzelne Objekte auswählen. | Alle Workspaces; Ausgangspunkt jeder Bearbeitung. | `select.svg` |
| Direktauswahl | Punkte, Kanten oder Teilflächen auswählen. | Geometrische Detailkorrektur. | `direct-select.svg` |
| Rechteckauswahl | Mehrere Objekte mit Auswahlrahmen erfassen. | Grundriss, Fassaden, technische Netze. | `box-select.svg` |
| Lassoauswahl | Unregelmäßige Objektgruppen auswählen. | Komplexe Grundrisse und TGA-Bereiche. | `lasso-select.svg` |

## Bearbeiten

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Verschieben | Objekte entlang X, Y oder Z verschieben. | Alle bearbeitbaren Fachobjekte. | `move.svg` |
| Drehen | Objekte um einen Bezugspunkt drehen. | Möbel, Bauteile, Referenzen. | `rotate.svg` |
| Skalieren | Freie Geometrie proportional oder achsweise skalieren. | Importe, Referenzen und freie Objekte. | `scale.svg` |
| Horizontal spiegeln | Objekt an vertikaler Achse spiegeln. | Grundrisse, Türen, Fensterteilungen. | `mirror-horizontal.svg` |
| Vertikal spiegeln | Objekt an horizontaler Achse spiegeln. | Dachkonturen, Symmetrien. | `mirror-vertical.svg` |
| Kopieren | Auswahl in Zwischenablage kopieren. | Alle Workspaces. | `copy.svg` |
| Duplizieren | Sofortige Kopie am aktuellen Ort erzeugen. | Fenster, Möbel, technische Objekte. | `duplicate.svg` |
| Löschen | Auswahl nach Abhängigkeitsprüfung entfernen. | Alle bearbeitbaren Objekte. | `delete.svg` |
| Sperren | Objekt vor Änderungen schützen. | Referenzen, freigegebene Bauteile. | `lock.svg` |
| Entsperren | Bearbeitung gesperrter Objekte zulassen. | Berechtigte Nutzer. | `unlock.svg` |
| Einblenden | Ausgeblendete Objekte sichtbar machen. | Layer- und Modellsteuerung. | `show.svg` |
| Ausblenden | Objekte temporär ausblenden. | Koordination und Modellprüfung. | `hide.svg` |
| Gruppieren | Mehrere Objekte als Gruppe behandeln. | Möbel, Symbole, technische Komponenten. | `group.svg` |
| Ausrichten | Objekte an Kante, Achse oder Mitte ausrichten. | Innenraum, Fassaden, Elektro. | `align.svg` |
| Verteilen | Objekte gleichmäßig verteilen. | Fenster, Leuchten, PV-Module. | `distribute.svg` |

## Zeichnen

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Linie | Gerade Linie zwischen zwei Punkten zeichnen. | Hilfsgeometrie und technische Zeichnung. | `line.svg` |
| Polylinie | Zusammenhängende Liniensegmente zeichnen. | Konturen, Leitungswege und Grundstücke. | `polyline.svg` |
| Rechteck | Rechteckige Kontur erzeugen. | Räume, Öffnungen und Flächen. | `rectangle.svg` |
| Polygon | Freie geschlossene Kontur zeichnen. | Dächer, Grundstücke und Zonen. | `polygon.svg` |
| Kreis | Kreis über Mittelpunkt und Radius erzeugen. | Runde Bauteile und Schächte. | `circle.svg` |
| Bogen | Kreisbogen oder tangentialen Bogen erzeugen. | Bogenwände und Leitungsführung. | `arc.svg` |

## CAD

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Trimmen | Überstehende Linien an Begrenzungen abschneiden. | Grundriss- und Detailkorrektur. | `trim.svg` |
| Verlängern | Linien bis zu einer Begrenzung verlängern. | Wände, Achsen, Leitungen. | `extend.svg` |
| Versatz | Parallele Kopie mit festem Abstand erzeugen. | Wände, Installationszonen, Dachüberstand. | `offset.svg` |
| Teilen | Linie oder Fachobjekt an einem Punkt teilen. | Wände, Leitungen, Dachflächen. | `split.svg` |
| Verbinden | Getrennte Segmente oder Objekte verbinden. | Konturen, Leitungsnetze. | `join.svg` |

## Architektur

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Wand | Parametrische Wand mit Aufbau, Höhe und Dicke erzeugen. | Architektur, Bauphysik, Heizlast. | `wall.svg` |
| Raum | Geschlossene Raumkontur erkennen oder erzeugen. | Grundriss, Heizlast, Innenraum. | `room.svg` |
| Tür | Türöffnung und Türelement platzieren. | Architektur, Bad, Küche. | `door.svg` |
| Fenster | Fenster mit Profil, Glas und Öffnungsart platzieren. | Architektur, Fassade, Heizlast. | `window.svg` |
| Treppe | Treppe zwischen Ebenen parametrisch erzeugen. | Erschließung und Schnittplanung. | `stairs.svg` |
| Dach | Dach aus Kontur oder Dachform erzeugen. | Dachplanung und Bauphysik. | `roof.svg` |
| Gaube | Gaube mit Dachöffnung und Seitenwänden einsetzen. | Dachplanung. | `dormer.svg` |
| Dachfenster | Dachfenster in geneigter Dachfläche platzieren. | Dachplanung, Heizlast. | `roof-window.svg` |
| Stütze | Tragende oder gestalterische Stütze setzen. | Architektur und Tragwerk. | `column.svg` |
| Unterzug / Träger | Horizontalen Träger oder Unterzug erzeugen. | Tragwerk und Kollisionsprüfung. | `beam.svg` |
| Öffnung | Durchbruch in Wand, Decke, Boden oder Dach erzeugen. | Architektur und TGA. | `opening.svg` |
| Boden | Bodenfläche und Bodenaufbau erzeugen. | Innenraum, Bauphysik. | `floor.svg` |
| Decke / Bodenplatte | Massive oder mehrschichtige Decke erzeugen. | Architektur, Statik, Heizlast. | `slab.svg` |
| Schnitt | Schnittlinie und abgeleitete Schnittansicht erzeugen. | Treppen, Dächer, TGA. | `section.svg` |
| Fassade / Ansicht | Orthogonale Gebäudeansicht erzeugen. | Fassade, Fenster, Dokumentation. | `elevation.svg` |

## Ansicht

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Vergrößern | Ansicht schrittweise vergrößern. | Detailbearbeitung. | `zoom-in.svg` |
| Verkleinern | Ansicht schrittweise verkleinern. | Modellübersicht. | `zoom-out.svg` |
| Alles anzeigen | Modell oder Referenz vollständig einpassen. | Alle Workspaces. | `fit-view.svg` |
| Pan / Hand | Canvas verschieben, ohne Geometrie zu ändern. | 2D und 3D. | `pan.svg` |
| Orbit | 3D-Kamera um Fokuspunkt drehen. | 3D-Ansicht. | `orbit.svg` |
| Raster | Zeichenraster ein- oder ausblenden. | 2D, Import, Grundriss. | `grid.svg` |
| Fang | Fang an Raster, Kanten, End- und Schnittpunkten. | Präzises Zeichnen. | `snap.svg` |

## Messen

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Distanz messen | Temporäre Distanz zwischen zwei Punkten messen. | Alle Planungsbereiche. | `measure-distance.svg` |
| Bemaßen | Persistente technische Maßlinie erzeugen. | 2D, Schnitt, Fassade. | `dimension.svg` |
| Winkel messen | Winkel zwischen Linien oder Flächen messen. | Dach, Treppe, Leitungen. | `measure-angle.svg` |
| Fläche messen | Geschlossene Fläche ermitteln. | Räume, Fassaden, Dächer. | `measure-area.svg` |
| Volumen messen | Körper- oder Raumvolumen ermitteln. | Heizlast, TGA. | `measure-volume.svg` |

## Import

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Datei importieren | PDF, Bild, DWG, DXF, IFC oder SVG laden. | Import & Nachzeichnen. | `import-file.svg` |
| Bild importieren | Rasterbild oder Scan als Referenz laden. | Bestandsgrundrisse. | `import-image.svg` |
| Kalibrieren | Bildmaßstab über bekannte Strecke bestimmen. | Grundrissimport. | `calibrate.svg` |
| Beschneiden | Referenz auf relevanten Bereich beschneiden. | PDF und Bild. | `crop.svg` |
| Nordrichtung setzen | Nordrichtung und Gebäudebezug festlegen. | Import, PV, Verschattung. | `set-north.svg` |
| Grundriss erkennen | Wände, Räume, Fenster, Türen und Maße erkennen. | KI-/Regelerkennung. | `recognize.svg` |
| KI-Assistent | Vorschläge, Erkennung und Korrektur unterstützen. | Import, Planung, Prüfung. | `ai-assistant.svg` |
| Erkennung bestätigen | Erkanntes Objekt als Fachobjekt übernehmen. | Review-Modus. | `approve-detection.svg` |

## Material

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Material zuweisen | Technisches oder sichtbares Material zuordnen. | Fassade, Fenster, Türen, Dach. | `material.svg` |
| Textur / PBR | PBR-Texturensatz und Mapping wählen. | 3D-Darstellung und Rendering. | `texture.svg` |
| Material aufnehmen | Material von Objekt übernehmen und übertragen. | Innenraum und Fassade. | `paint.svg` |

## Fassade

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Fassadensystem | Putz, Klinker, Holz oder Plattensystem zuweisen. | Fassadenplanung. | `facade.svg` |
| Klinker / Verband | Klinkerformat, Fuge und Verband konfigurieren. | Fassade. | `brick.svg` |

## Bauphysik

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Dämmung | Dämmstoff und Dämmstärke festlegen. | Wand, Dach, Decke, Boden. | `insulation.svg` |
| U-Wert | U-Wert berechnen, prüfen oder überschreiben. | Heizlast und Bauphysik. | `u-value.svg` |
| Thermische Hülle | Wärmeübertragende Gebäudehülle markieren. | Heizlast-Datencheck. | `thermal-envelope.svg` |
| Lüftung | Lüftungszonen und Luftwechsel definieren. | Heizlast und TGA. | `ventilation.svg` |

## Heizung

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Heizkörper | Heizkörper platzieren und auf Raumheizlast auslegen. | Heizung. | `radiator.svg` |
| Fußbodenheizung | Heizfläche, Sperrflächen und Heizkreise planen. | Heizung. | `floor-heating.svg` |
| Pumpe | Pumpe platzieren und Förderdaten zuweisen. | Hydraulik. | `pump.svg` |
| Wärmepumpe | Wärmepumpe platzieren und auslegen. | Wärmeerzeugung. | `heat-pump.svg` |
| Hydraulischer Abgleich | Volumenströme, Druckverluste und Ventile abgleichen. | Heizsystem. | `hydraulic-balance.svg` |

## TGA

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Rohrleitung | Rohr mit System, Dimension, Höhe und Gefälle zeichnen. | Sanitär, Heizung, Kälte. | `pipe.svg` |

## Sanitär

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Sanitäranschluss | Wasser- und Abwasseranschlüsse definieren. | Bad, Küche, TGA. | `sanitary.svg` |

## Bad

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Badewanne | Badewanne mit Bewegungs- und Anschlussflächen platzieren. | Badplanung. | `bath.svg` |
| Dusche | Dusche, Rinne und Gefällebereich planen. | Badplanung. | `shower.svg` |
| WC | WC mit Vorwand, Abwasser und Bewegungsfläche platzieren. | Badplanung. | `toilet.svg` |

## Küche

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Küchenplanung | Küchenform und Arbeitszonen anlegen. | Küchenplanung. | `kitchen.svg` |
| Schrank | Küchenschrank oder Möbelmodul konfigurieren. | Küche und Innenraum. | `cabinet.svg` |
| Gerät | Elektro- oder Küchengerät einsetzen. | Küche, Elektro. | `appliance.svg` |

## Elektro

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Elektroplanung | Elektro-Workspace und Systemfunktionen öffnen. | Elektroplanung. | `electric.svg` |
| Steckdose | Steckdose mit Typ und Montagehöhe platzieren. | Elektro, Küche, Bad. | `socket.svg` |
| Schalter | Schalter, Taster oder Dimmer platzieren. | Elektro. | `switch.svg` |
| Leuchte | Leuchte oder Lichtpunkt platzieren. | Elektro und Innenraum. | `light.svg` |
| Verteiler | Elektroverteiler und Stromkreisbelegung konfigurieren. | Elektro. | `distribution-board.svg` |

## PV

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| PV-Modul | PV-Modul oder Modulreihe platzieren. | PV-Planung. | `pv-module.svg` |
| Batteriespeicher | Speicher platzieren und Kapazität zuweisen. | PV und Energiesystem. | `battery.svg` |
| Wallbox | Wallbox mit Stellplatz und Stromkreis platzieren. | Elektro und Mobilität. | `wallbox.svg` |

## Workflow

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Wizard / Assistent | Geführten Fachprozess starten oder fortsetzen. | Alle Fachbereiche. | `wizard.svg` |
| Prozessübersicht | Abhängige Planungsschritte und Status anzeigen. | Technische Planung. | `workflow.svg` |
| Übergabepaket | Versioniertes Datenpaket an Fachmodul übergeben. | Heizlast, TGA, Dach, Elektro. | `handoff-package.svg` |
| Freigeben | Berechnung, Planung oder Revision freigeben. | Prüfung und Dokumentation. | `approve.svg` |

## Zusammenarbeit

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Kommentar | Kommentar an Objekt oder Position setzen. | Teamarbeit. | `comment.svg` |
| Historie | Änderungsverlauf eines Objekts oder Projekts anzeigen. | Audit und Revision. | `history.svg` |
| Revision | Versionierten Planungsstand erzeugen oder vergleichen. | Projektsteuerung. | `revision.svg` |

## Prüfung

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Prüfen | Regel- oder Vollständigkeitsprüfung starten. | Alle Fachbereiche. | `check.svg` |
| Warnungen | Warnungen und Abweichungen anzeigen. | Prüfungscenter. | `warning.svg` |
| Fehler | Blockierende Fehler anzeigen und bearbeiten. | Prüfungscenter. | `error.svg` |

## System

| Werkzeug | Funktion | Einsatz | SVG |
|---|---|---|---|
| Einstellungen | Workspace, Toolbar und Benutzeroptionen konfigurieren. | System. | `settings.svg` |
| Suche | Werkzeuge, Objekte und Dokumente durchsuchen. | Global. | `search.svg` |
| Command-Palette | Alle Befehle und Werkzeuge zentral aufrufen. | Expertenmodus. | `command-palette.svg` |
| Exportieren | Aktuelle Ansicht, Modell oder Daten exportieren. | Dokumentation. | `export.svg` |
| PDF / Planblatt | Druckfähiges Planblatt oder PDF erzeugen. | Dokumentation. | `pdf.svg` |
