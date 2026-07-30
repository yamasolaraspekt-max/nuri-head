# Quelle: Yamas Entwicklungs-Prompt „Intelligentes Zeichnen" (30.07.2026, 22:58)

**Status:** Quelle, unverändert. Nicht bearbeiten — Ableitungen gehören nach
`docs/planner/programm-zeichnen-bestandsaufnahme.md` und in die Auftragsblätter.

**Warum wörtlich hier:** PB-040-Lehre — zuerst sichern, dann berichten. Der Prompt lag
nur im Gesprächsverlauf; ein Kontextabbruch hätte ihn verloren. Was zerlegt wird, muss
gegen das Original prüfbar bleiben.

**Umfang:** 17 Abschnitte. Das ist ein **Programm**, kein Auftrag. Es wird in Scheiben
geschnitten (Z-01 …), niemals als ein Blatt gegeben — dieselbe Lehre wie bei AUF-48-S4,
das zweimal geteilt werden musste.

---

## Wortlaut

```text
AUFTRAG: INTELLIGENTES ZEICHNEN VON WÄNDEN, DECKEN UND DACHKONTUREN
MIT MAGNETISCHEM FANG, SAUBEREN ECKVERBINDUNGEN UND KORREKTEM TOOLWECHSEL

ZIEL

Verbessere die Zeichenlogik im bestehenden 2D-/3D-Hausplaner so, dass
Wände, Decken, Bodenplatten und Dachkonturen mit Maus, Touch oder Stift
präzise erstellt werden können, auch wenn der Benutzer Eckpunkte nicht
pixelgenau trifft.

Die Anwendung muss benachbarte Punkte, Kanten, Wandachsen und bestehende
Ecken intelligent erkennen und magnetisch einrasten lassen.

Zusätzlich muss beim Verlassen des Zeichenbereichs oder beim Wechsel des
Werkzeugs die aktive Vorschau sofort sauber beendet werden. Es darf kein
langer Vorschaustrich vom letzten Zeichenpunkt bis zum neuen Icon,
Sidebar-Element oder Mauszeiger außerhalb des Canvas entstehen.

BESTANDSCODE-FIRST

Vor der Implementierung prüfen:

- bestehendes Wandwerkzeug
- bestehendes Polygon- und Polylinienwerkzeug
- Snap- und Fanglogik
- Hit-Testing
- Pointer-Capture
- Tool-Statusverwaltung
- Canvas-Grenzen
- Wandanschluss- und Wandgeometrie
- 2D-/3D-Synchronisierung
- Undo/Redo
- Dach- und Deckengeometrie
- vorhandene Tests, Tickets, Branches und Playground

Keine parallele zweite Zeichenengine entwickeln.
Vorhandene funktionierende Geometrie-, Dach- und Wandlogik wiederverwenden.

==================================================
1. MAGNETISCHER FANG
==================================================

Implementiere einen zentralen SnapService für alle Zeichenwerkzeuge.

Der Fang muss mindestens unterstützen:

- Endpunktfang
- Eckpunktfang
- Mittelpunktfang
- Schnittpunktfang
- Lotfang
- Tangentialfang, falls relevant
- Wandachsenfang
- Innenkantenfang
- Außenkantenfang
- Rasterfang
- Winkelraster
- Verlängerungsfang
- parallelen Fang
- orthogonalen Fang
- Geschoss- und Bezugsebenenfang

Snap-Kandidaten werden nach Priorität bewertet.

Empfohlene Priorität:

1. exakter vorhandener Eckpunkt
2. Endpunkt einer Wand oder Kontur
3. Schnittpunkt
4. Wandachse
5. Innen- oder Außenkante
6. orthogonale Projektion
7. Winkelraster
8. allgemeines Raster

Der Nutzer muss visuell sehen:

- welcher Punkt erkannt wurde
- welcher Fangtyp aktiv ist
- auf welches Objekt gefangen wird
- welcher Abstand zum Fangpunkt besteht
- welche neue Geometrie daraus entsteht

Fang muss konfigurierbar sein:

- ein/aus
- Fangtoleranz in Bildschirm-Pixeln
- Winkelraster, zum Beispiel 5°, 15°, 30°, 45°, 90°
- bevorzugte Wandbezugslinie
- Achse, Innenkante oder Außenkante
- temporär deaktivierbar, zum Beispiel über Alt
- temporär verstärkbar, zum Beispiel über Shift

Wichtig:
Die Fangtoleranz wird in Bildschirmkoordinaten bewertet, nicht nur in
Weltkoordinaten. Dadurch bleibt sie bei unterschiedlichen Zoomstufen
bedienbar.

==================================================
2. WANDZEICHNUNG MIT INTELLIGENTEM ECKANSCHLUSS
==================================================

Beim Zeichnen einer neuen Wand von einem bestehenden Wandende zu einem
weiteren Punkt muss die Anwendung die Verbindung automatisch erkennen.

Ablauf:

1. Benutzer aktiviert Wandwerkzeug.
2. Er klickt in die Nähe eines vorhandenen Wandendes.
3. Das System fängt exakt auf den Endpunkt.
4. Benutzer bewegt die Maus zum nächsten Punkt oder Wandende.
5. Eine Live-Vorschau zeigt:
   - Wandachse
   - Wandstärke
   - Bezugslinie
   - Länge
   - Winkel
   - erwarteten Anschluss
6. Beim Klick wird die Wand exakt erzeugt.
7. Angrenzende Wände werden geometrisch verbunden.
8. Die Ecke wird abhängig vom Wandtyp fachlich korrekt ausgebildet.

Unterstützte Anschlussarten:

- stumpfer Anschluss
- Gehrungsanschluss
- T-Anschluss
- Kreuzanschluss
- Eckanschluss
- frei definierter Winkel
- Anschluss an gekrümmte Wand, sofern unterstützt

Bei zwei Wänden mit gleichem oder kompatiblem Aufbau soll eine saubere
Gehrungsdarstellung möglich sein.

Die Gehrung darf nicht nur optisch sein. Die Wandflächen und Konturen
müssen geometrisch korrekt verschnitten werden.

Bei inkompatiblen Wandaufbauten:

- keine stillschweigende falsche Gehrung
- Anschlussvorschlag anzeigen
- stumpf, achsbündig oder benutzerdefiniert wählbar
- Warnung bei überlappenden oder offenen Wandkonturen

==================================================
3. SMARTER WANDABSCHLUSS
==================================================

Beim Annähern an einen vorhandenen Punkt oder eine bestehende Wand:

- Fangpunkt hervorheben
- Anschlussart als kleines Badge anzeigen
- Vorschau der fertigen Ecke anzeigen
- Länge und Winkel aktualisieren
- gültigen und ungültigen Zustand unterscheiden

Beispiel:

"Endpunktfang · Gehrung 90°"

oder:

"Wandachse · T-Anschluss"

Bei ungültigem Anschluss:

"Anschluss nicht möglich: Wandstärke oder Geometrie kollidiert"

Der Nutzer kann den vorgeschlagenen Anschluss vor Abschluss wechseln.

==================================================
4. POLYGONBASIERTE DECKE UND BODENPLATTE
==================================================

Implementiere beziehungsweise verbessere ein Werkzeug:

"Decke aus Kontur"

Ablauf:

1. Benutzer aktiviert Deckenwerkzeug.
2. Er klickt nacheinander die Eckpunkte des Erdgeschossgrundrisses an.
3. Jeder Punkt fängt magnetisch auf:
   - Wandaußenkante
   - Wandinnenkante
   - Wandachse
   - vorhandenen Eckpunkt
4. Eine geschlossene Polygonvorschau wird angezeigt.
5. Beim Erreichen des Startpunkts wird "Kontur schließen" angeboten.
6. Alternativ schließt Enter oder Doppelklick die Kontur.
7. Das System prüft:
   - Kontur geschlossen
   - keine Selbstüberschneidung
   - mindestens drei gültige Punkte
   - keine doppelten Punkte
   - keine Nullkanten
   - keine unzulässigen Löcher
8. Aus der Kontur wird ein parametrisches Deckenobjekt erzeugt.

Parameter:

- Deckenart
- Dicke
- Höhe
- Bezugsgeschoss
- Oberkante
- Unterkante
- Aufbau
- Material
- tragend ja/nein
- Öffnungen
- Aussparungen
- Überstand
- Versatz zur Grundrisskontur

Das erzeugte Objekt muss in 2D und 3D dasselbe Fachobjekt mit derselben
Objekt-ID sein.

==================================================
5. DECKE AUTOMATISCH AUS GRUNDRISS ABLEITEN
==================================================

Zusätzlich zum manuellen Anklicken soll ein intelligenter Modus existieren:

"Decke aus Grundriss"

Der Nutzer kann:

- einen Raum wählen
- mehrere Räume wählen
- ein Geschoss wählen
- eine geschlossene Wandkontur wählen
- eine bestehende Bodenfläche wählen

Das System ermittelt daraus eine Vorschlagskontur.

Vor Übernahme anzeigen:

- erkannte Außenkontur
- Innenhöfe oder Öffnungen
- ausgeschlossene Bereiche
- Treppenöffnungen
- Schächte
- Überstände
- unsichere Konturabschnitte

Der Nutzer kann die Kontur vor dem Erzeugen manuell korrigieren.

==================================================
6. DACH AUS GEBÄUDEKONTUR
==================================================

Implementiere beziehungsweise verbessere das Werkzeug:

"Dach aus Kontur"

Wichtig:
Bestehende 3D-Dachlogik vollständig wiederverwenden.
Keine neue parallele Dachengine entwickeln.

Ablauf:

1. Benutzer wählt Erdgeschoss, Obergeschoss oder bestehende Decke.
2. Er wählt:
   - automatisch erkannte Außenkontur
   - manuell gezeichnete Kontur
   - bestehende Decken- oder Bodenplattenkontur
3. Das System zeigt die Kontur als Vorschau.
4. Benutzer bestätigt oder korrigiert Eckpunkte.
5. Danach wählt er:
   - Dachform
   - Dachneigung
   - Traufhöhe
   - Firstausrichtung
   - Überstand
   - Kniestock
   - Material
6. Die bestehende 3D-Dachfunktion erzeugt das Dach.
7. Die 2D-Dachaufsicht wird daraus abgeleitet.

Konturprüfung:

- geschlossen
- keine Selbstüberschneidung
- keine unzulässigen Innenwinkel
- keine extrem kurzen Kanten
- kein doppelter Punkt
- korrekte Orientierung
- Löcher und Innenhöfe explizit behandeln

==================================================
7. TOOLWECHSEL UND "LANGER STRICH"-FEHLER
==================================================

Der aktuelle Fehler muss behoben werden:

Wenn ein Zeichenwerkzeug aktiv ist und der Benutzer den Canvas verlässt,
um ein anderes Werkzeug wie Fenster, Tür oder Decke auszuwählen, darf die
Vorschau nicht vom letzten Zeichenpunkt bis zum Mauszeiger außerhalb des
Canvas weiterlaufen.

Ursachen prüfen:

- pointerleave nicht behandelt
- Pointer Capture bleibt aktiv
- Preview-State bleibt aktiv
- Tool wird nicht sauber deaktiviert
- globaler pointermove aktualisiert weiterhin die Vorschau
- Canvas- und UI-Koordinaten werden vermischt

Verbindliches Verhalten:

A. Maus verlässt den Canvas

- Vorschau wird eingefroren oder ausgeblendet
- keine Geometrie folgt dem Mauszeiger außerhalb des Canvas
- aktiver Startpunkt bleibt nur dann erhalten, wenn das Werkzeug einen
  pausierbaren Mehrschrittmodus unterstützt
- Status zeigt "Zeichnung pausiert" oder "Punkt wählen"

B. Benutzer klickt auf ein anderes Werkzeug

- aktuelles Werkzeug erhält cancel oder suspend
- temporäre Vorschaugeometrie wird entfernt
- Pointer Capture wird freigegeben
- Hover- und Snap-Zustände werden gelöscht
- neuer Tool-State wird aktiviert
- kein Restsegment bleibt sichtbar

C. Benutzer kehrt in den Canvas zurück

Je nach Produktentscheidung:

Variante 1:
Zeichenoperation wurde abgebrochen.

Variante 2:
Zeichenoperation wurde pausiert und kann bewusst fortgesetzt werden.

Standardempfehlung:
Werkzeugwechsel bricht die aktuelle unbestätigte Teilaktion ab.
Canvas-Verlassen allein pausiert nur die Vorschau.

D. Escape

- bricht aktuellen Punkt oder gesamten Zeichenprozess eindeutig ab
- entfernt Vorschau
- stellt den letzten bestätigten Zustand wieder her

==================================================
8. ZENTRALE TOOL-LIFECYCLE-REGEL
==================================================

Alle modalen Zeichenwerkzeuge müssen dieselbe Lifecycle-Schnittstelle
verwenden:

interface InteractiveTool {
  activate(context): void;
  suspend(context): void;
  resume(context): void;
  cancel(context): void;
  commit(context): void;
  deactivate(context): void;

  onPointerEnter(event, context): void;
  onPointerLeave(event, context): void;
  onPointerDown(event, context): void;
  onPointerMove(event, context): void;
  onPointerUp(event, context): void;
  onKeyDown(event, context): void;
}

Pflichten bei cancel und deactivate:

- temporäre Geometrie löschen
- Snap-Markierungen löschen
- Pointer Capture freigeben
- Cursor zurücksetzen
- Statushinweise zurücksetzen
- unbestätigte Commands verwerfen
- Event Listener entfernen
- keine Änderung am BuildingDocument zurücklassen

==================================================
9. VORSCHAU- UND COMMIT-TRENNUNG
==================================================

Temporäre Zeichenlinien dürfen nicht direkt Teil des BuildingDocument sein.

Trenne:

- PreviewGeometry
- committed domain geometry

Ablauf:

Pointer Move
→ PreviewGeometry aktualisieren

Klick/Enter
→ validieren
→ Command ausführen
→ Fachobjekt erzeugen
→ PreviewGeometry zurücksetzen

Werkzeugwechsel
→ PreviewGeometry vollständig entfernen

==================================================
10. INTELLIGENTE ECKPUNKTLOGIK
==================================================

Beim Polygonzeichnen müssen folgende Fälle sauber behandelt werden:

- Klick nahe Startpunkt schließt Kontur
- Klick nahe bestehendem Eckpunkt übernimmt exakt dessen Koordinate
- Klick nahe Kante kann auf die Kante projizieren
- Klick nahe Schnittpunkt nimmt Schnittpunkt
- Doppelte Punkte werden verhindert
- extrem kurze Segmentlänge wird verhindert oder bestätigt
- Selbstüberschneidung wird live angezeigt
- letzte Kante zum Startpunkt wird als Vorschau angezeigt
- ungültige Kontur kann nicht committed werden

Optional:

- Backspace entfernt letzten Punkt
- Ctrl/Cmd+Z entfernt letzten Punkt innerhalb des aktiven Tools
- Enter schließt gültige Kontur
- Escape entfernt Vorschau oder beendet Tool
- Shift erzwingt Winkelraster
- Alt deaktiviert Snap temporär

==================================================
11. DIREKTE MASSEINGABE
==================================================

Beim Zeichnen von Wand, Decke oder Dachkontur soll der Nutzer nicht nur
mit der Maus arbeiten müssen.

Unterstütze:

- Länge direkt eingeben
- Winkel direkt eingeben
- X-/Y-Abstand eingeben
- Höhe eingeben
- Dicke eingeben
- Tab zwischen Eingaben
- Enter bestätigen
- Escape abbrechen

Beispiel Wand:

1. Startpunkt klicken
2. Maus Richtung vorgeben
3. Länge "4250" eingeben
4. Winkel "90" eingeben
5. Enter
6. Wand wird exakt erzeugt

==================================================
12. VISUELLES FEEDBACK
==================================================

Während des Zeichnens anzeigen:

- aktiver Startpunkt
- aktueller Fangpunkt
- Fangtyp
- Segmentlänge
- Winkel
- Wandstärke
- Bezugslinie
- vorgeschlagene Anschlussart
- gültiger oder ungültiger Zustand
- nächste mögliche Aktion

Keine permanenten großen Hinweise.
Kompakte Kontextanzeige nahe am Cursor oder in der Statusleiste.

==================================================
13. TOUCH UND STIFT
==================================================

Für Tablet und Stift:

- größere Fangbereiche
- größere Eckpunktmarker
- Long Press für Kontextoptionen
- magnetische Fangstärke erhöhen
- Zwei-Finger-Zoom darf keine Zeichenlinie erzeugen
- Finger verlässt Canvas: Vorschau stoppen
- Stift und Finger sauber unterscheiden
- keine Geometrie durch Handballenbewegung
- Punkt kann numerisch nachkorrigiert werden

==================================================
14. COMMANDS
==================================================

Mindestens:

- StartWallDrawing
- AddWallPoint
- CompleteWallSegment
- ConnectWallEndpoint
- CreateWallJoin
- CancelWallDrawing
- StartPolygonDrawing
- AddPolygonPoint
- RemoveLastPolygonPoint
- ClosePolygon
- CreateSlabFromPolygon
- CreateSlabFromBuildingOutline
- CreateRoofFromOutline
- SuspendActiveTool
- ResumeActiveTool
- CancelActiveTool
- ClearPreviewGeometry

==================================================
15. DATEN- UND ZUSTANDSMODELL
==================================================

ToolInteractionState:

- activeToolId
- interactionPhase
- pointerInsideCanvas
- pointerCaptured
- startPoint
- currentPoint
- confirmedPoints
- hoveredSnapCandidate
- activeSnapResult
- previewGeometry
- pendingCommand
- validationState
- suspended

SnapResult:

- type
- worldPoint
- screenDistance
- sourceObjectId
- sourceGeometryPart
- priority
- confidence
- suggestedJoinType

==================================================
16. TESTS
==================================================

UNIT

- Endpunktfang
- Eckpunktfang
- Schnittpunktfang
- Winkelraster
- Fangpriorität
- Fangtoleranz bei verschiedenen Zoomstufen
- Gehrungsberechnung
- T-Anschluss
- Polygon schließen
- Selbstüberschneidung erkennen
- doppelte Punkte verhindern
- Tool-cancel löscht Preview-State

INTEGRATION

- Wand an bestehende Wand anschließen
- zwei Wände als Gehrung verbinden
- Tür und Fenster bleiben nach Wandanschluss gültig
- Decke aus Wandkontur erzeugen
- Dach aus Decken- oder Grundrisskontur erzeugen
- Toolwechsel entfernt Vorschau
- Pointer Leave stoppt Vorschau
- 2D und 3D bleiben synchron
- Undo/Redo stellt Verbindungen korrekt wieder her

E2E

1. Wandwerkzeug aktivieren
2. nahe bestehendem Wandende klicken
3. zu zweitem Wandende bewegen
4. Fang und Gehrungsvorschau prüfen
5. Wand abschließen
6. Ecke in 2D und 3D prüfen

1. Deckenwerkzeug aktivieren
2. alle Gebäudeecken anklicken
3. Startpunkt erneut anklicken
4. Kontur schließen
5. Decke erzeugen
6. Fläche und Volumen prüfen

1. Wandzeichnung starten
2. Maus aus Canvas zur Toolbar bewegen
3. Fensterwerkzeug anklicken
4. prüfen, dass kein langer Strich sichtbar bleibt
5. Fensterwerkzeug ist aktiv
6. alter Preview-State ist leer

NEGATIVTESTS

- extrem kleiner Winkel
- fast identische Punkte
- Wandenden knapp außerhalb Fangtoleranz
- inkompatible Wandstärken
- selbstschneidende Deckenkontur
- Toolwechsel während Pointer Capture
- Escape während Vorschau
- Touch-Zoom während aktiver Zeichnung

==================================================
17. ABNAHMEKRITERIEN
==================================================

Die Umsetzung ist nur abgeschlossen, wenn:

- Wandenden zuverlässig magnetisch gefangen werden
- Wände geometrisch sauber verbunden werden
- Gehrung, T- und Eckanschluss korrekt unterschieden werden
- die Maus keine pixelgenaue Positionierung verlangt
- Fangtyp sichtbar ist
- Decken über geschlossene Eckpunktkonturen erzeugt werden können
- Decken auch aus einer vorhandenen Grundrisskontur ableitbar sind
- Dächer aus vorhandenen Konturen mit bestehender 3D-Dachlogik erzeugt werden
- Polygonkonturen validiert werden
- Toolwechsel jede unbestätigte Vorschaugeometrie entfernt
- beim Verlassen des Canvas kein langer Strich hinter dem Cursor entsteht
- Escape zuverlässig abbricht
- Undo/Redo funktioniert
- 2D und 3D synchron bleiben
- Maus, Touch und Stift funktionieren
- vorhandene Wand-, Decken- und Dachfunktionen nicht parallel neu gebaut werden
```

## Kernaussage (Yamas eigener Nachsatz)

```text
Der lange Strich ist kein Darstellungsproblem, sondern ein fehlerhafter
Tool-Lifecycle- beziehungsweise Preview-State. Beim Verlassen des Canvas
und beim Werkzeugwechsel müssen Pointer Capture, Snap-State und temporäre
Vorschaugeometrie kontrolliert beendet oder pausiert werden.
```
