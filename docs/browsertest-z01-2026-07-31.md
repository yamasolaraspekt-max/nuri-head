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
```

> ### ⚠ KORREKTUR, 31.07. ~02:4x — **diese Zeile stand hier falsch, und sie ist von mir**
>
> An dieser Stelle stand: *„Reststrich: sichtbar, bis der Zeiger die Bühne wieder betritt."*
> **Das habe ich nicht geprüft, sondern angenommen** — mein eigenes Bildschirmfoto von damals
> (`z01-5-nach-werkzeugwechsel.png`) zeigt eine **saubere Fläche**.
>
> **Der Planner hat die Lücke gefunden, bevor er abgenommen hat**, und die eine entscheidende
> Messung benannt: *„War die Ursache ein altes Konva-Bild statt ein fehlendes Aufräumen, ist der
> Fehler jetzt nur VERDECKT."* Gemessen am Bundle-Stand **vor** dem Commit (`30da5252^`,
> vorübergehend eingespielt, danach md5-identisch zurückgestellt):
>
> ```text
>                        Linien   Vorschau-Gruppen   Punkte
> leer                     58            0           —
> halb gezogen             59            1           1300,1400 -> 3000,1400
> Zeiger zur Leiste        59            1           1300,1400 -> 1300,3808   (eingefroren)
> NACH dem Leisten-Klick   57            0           —
> ```
>
> **Der Leisten-Klick hat immer korrekt aufgeräumt** — im Baum *und* in den Bildpunkten. Es gab
> nie einen Reststrich nach dem Klick.
>
> **Was das für Z-01 heißt:** der Fehler wird **nicht verdeckt**. Der einzige echte Mangel war die
> eingefrorene Vorschau, solange der Zeiger draußen ist — und der ist behoben. *K-01 misst die
> Sache: die fünf Kopien waren eine echte Dublette, nur eben nicht die Ursache dieses einen
> Symptoms.*
>
> **Die Lehre ist meine:** ich habe in einem Protokoll, dessen ganzer Zweck „aufschreiben, was man
> sieht" ist, einen Satz geschrieben, den ich nicht gesehen hatte. *Ein Bericht, der an einer
> Stelle vermutet statt misst, macht jede andere Zeile darin fraglich.*

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

---

# L-01 — nach dem Bau, drei Viewports

**Gefahren 31.07.2026 · Chrome headful · nach `npm run build:hausplaner`.** Gemessen wird die
**Vorschau-Geometrie aus der Konva-Bühne** und der Hinweistext aus dem DOM — nicht das Bild.

## 1440 × 900 — der Vollausbau

```text
ANKER               HTTP 200 · canvas 2 · Titel "SA-DESK - Hausplaner — Studio"
halb gezogen        Vorschau DA: 1300,1400 -> 2800,1400   · Hinweis: keiner
Zeiger zur Leiste   Vorschau FORT                          · Hinweis: "Zeichnung pausiert —
                                                              zurück auf die Fläche setzt fort,
                                                              Esc bricht ab"
zurueck auf Flaeche Vorschau DA: 1300,1400 -> 2340,1121    · Hinweis: keiner
```

**Das ist der ganze Auftrag in vier Zeilen.** Vorher blieb die Linie beim Verlassen stehen
(`1500,1400 -> 2930,3877`, quer über den halben Grundriss); jetzt verschwindet sie, der Zustand
wird benannt, und die Rückkehr belebt sie **ohne Klick** — mit neuer Geometrie, also lebendig und
nicht bloss wieder eingeblendet.

## Yamas E2E-Fall, wörtlich abgefahren

```text
Wand halb gezogen · Zeiger raus zur Werkzeugleiste · Fensterwerkzeug angeklickt
  -> Werkzeug "fenster"      aktiv
  -> Reststrich              FORT
  -> Hinweis                 verschwunden (kein Zug mehr, also nichts zu sagen)
```

*Vor dem Bau blieb hier der Strich sichtbar, bis der Zeiger die Bühne wieder betrat.*

## 1024 × 800

```text
halb gezogen        Vorschau DA: 0,600 -> 500,600   · Hinweis: keiner
Zeiger zur Leiste   Vorschau FORT                   · Hinweis: erscheint
zurueck auf Flaeche Vorschau DA: 0,600 -> 300,300   · Hinweis: keiner
E2E                 Werkzeug "fenster" · Reststrich FORT
```

## 375 × 760 — **hier sagt die Probe nichts, und das gehört gesagt**

```text
halb gezogen        Vorschau FORT   <- es kam gar kein Zug zustande
```

**Bei 375 px startet das Wand-Werkzeug keinen Zug** — es gibt also nichts zu pausieren, und die
Zeilen darunter messen Leere. *Das ist **nicht** ein Befund dieser Scheibe:* `PB-046` hält seit
gestern fest, dass bei 375 px acht Werkzeuge unerreichbar sind, und `AUF-91` ist das Blatt, das
dort einen ehrlichen Hinweis anbringt. **Ich melde es als „nicht gemessen", nicht als „grün".**

## Konsole

```text
Meldungen gesamt 92 · davon aus `hausplaner.js`: 0
```
