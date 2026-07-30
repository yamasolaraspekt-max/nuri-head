# Browsertest Z-01 — Schritt 0: den Fehler sehen, bevor eine Zeile Code entsteht

**Gefahren 31.07.2026, ~01:1x CEST · Chrome headful · `http://ticket.test/admin/hausplaner/studio`
· Generator.** Der Auftrag verlangt diesen Schritt ausdrücklich **vor** dem Bau und sagt: *„schreibe
auf, was du siehst … Widerspricht er meiner Erwartung, meldest du zurück statt zu bauen."*

## L-01-Anker — die Messung fand auf der richtigen Seite statt

```text
HTTP-Status                          200
document.querySelectorAll('canvas')  2
document.title                       "SA-DESK - Hausplaner — Studio"
Konva-Buehne                         vorhanden, 589 x 541 px bei 1440 x 900
Arbeitsbereich                       Architektur (bewusst gesetzt — in Elektro-PV
                                     aktiviert der Bereich das Wand-Werkzeug nicht)
```

## Die Frage aus Schritt 0

> *„Friert der Strich am Rand ein, oder folgt er dem Zeiger über den Canvas hinaus?"*

## Die Antwort: **er friert ein.** Die Erwartung des Planners stimmt.

**Gemessen wurde nicht am Bild, sondern an der Geometrie** — die Vorschau-Linie direkt aus der
Konva-Bühne gelesen (Weltkoordinaten in mm):

```text
Zeiger IM Canvas, 200 px rechts vom Start      1500,1400 -> 3200,1400
Zeiger 60 px UEBER der Buehne (Werkzeugleiste) 1500,1400 -> 3200,1400   unveraendert
Zeiger weit links oben ausserhalb              1500,1400 -> 3200,1400   unveraendert
Zeiger wieder IM Canvas                        1500,1400 -> 4152, 689   folgt wieder
```

**Und die Ursache, direkt gemessen statt hergeleitet:**

```text
Zeiger drin      mousemove am Konva-Container 2 · am Fenster 2 · Statusleiste x 3200 mm
Zeiger draussen  mousemove am Konva-Container 0 · am Fenster 1 · Statusleiste x 3200 mm
```

`onMouseMove` hängt an der Bühne, nicht am Fenster. Verlässt der Zeiger die Fläche, kommt kein
Ereignis mehr an — `cursor` behält seinen letzten Wert, und die Vorschau wird weiter aus genau
diesem Wert gezeichnet.

## Was das für Yamas Beschwerde bedeutet — der eigentliche Ertrag dieses Schritts

**„Einfrieren" ist kein harmloser Zwischenzustand.** Bei einer menschenähnlichen Mausbewegung
(viele Zwischenschritte statt eines Sprungs) bleibt die Linie **dort stehen, wo der Zeiger die
Fläche zuletzt berührt hat** — also am Rand, quer über den halben Grundriss:

```text
langsam nach OBEN hinaus     1500,1400 -> 2930, 3877
langsam nach UNTEN hinaus    1500,1400 -> 3356, -456
langsam nach RECHTS hinaus   1500,1400 -> 4200, 1400
wieder hinein                1500,1400 -> 3200, 1400   lebt sofort auf
```

**Das ist der „lange Strich".** Er folgt dem Zeiger nicht — er bleibt liegen. Wer zur Werkzeugleiste
fährt, lässt einen Strich quer über der Zeichnung zurück, und nichts sagt, dass die Aktion noch
läuft. *Die Erwartung des Blattes ist bestätigt; die Beschwerde ist es ebenfalls, nur mit einer
anderen Ursache als „folgt dem Zeiger".*

## Yamas E2E-Fall, vorher abgefahren

```text
Wand halb gezogen · Zeiger zur Werkzeugleiste · Fensterwerkzeug geklickt
  -> Werkzeug wechselt auf `fenster`   (der Wechsel selbst funktioniert)
  -> Reststrich: sichtbar, bis der Zeiger die Buehne wieder betritt
```

## Eine Beobachtung, die ich NICHT reproduzieren konnte — offengelegt

Im **ersten** Lauf zeigten zwei aufeinanderfolgende Bildschirmfotos eine Vorschau, die nach dem
Verlassen **diagonal zur Zeigerposition ausserhalb** stand. In **drei** späteren Läufen mit direkt
gelesener Geometrie trat das nicht auf. *Der reproduzierbare, instrumentell belegte Befund ist der
oben beschriebene.* Die Einzelbeobachtung steht hier, weil sie in die Gegenrichtung zeigt und ein
Bericht, der nur das Passende nennt, keiner ist.

## Konsole

```text
Meldungen gesamt 57 · davon aus `hausplaner.js`: 0
```

*(Die Dauergäste aus `chat-*.js` — Reverb-WS und ein `addEventListener` auf `null` — gehören zur
Vue-Hauptanwendung und zählen hier weder als Treffer noch als Freibrief.)*

---

**Schlussfolgerung für den Bau:** Schritt 0 widerspricht dem Blatt **nicht**. Die Festlegung
(Verlassen pausiert, Vorschau wird **ausgeblendet** statt eingefroren stehen gelassen, Statusleiste
sagt es) trifft genau den beobachteten Mangel. **Gebaut wird.**
