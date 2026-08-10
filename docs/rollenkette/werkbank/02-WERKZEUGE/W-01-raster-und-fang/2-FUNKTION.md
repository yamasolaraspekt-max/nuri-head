# W-01 · Raster und Fang — FUNKTION

## Was tut das Werkzeug, Schritt für Schritt?

1. Der Aufrufer übergibt einen **Rohpunkt** und die **Toleranz in mm**.
2. Der Kern prüft die Kandidaten **in fester Rangfolge** und nimmt den ersten Treffer.
3. Er gibt den **gefangenen Punkt** zurück — **und die Art des Fangs**.

## Die Rangfolge ist Teil des Vertrags

```text
endpunkt > mittelpunkt > achse > verlaengerung > ortho > raster > keiner
```

*Ohne feste Rangfolge springt der Fang bei dichten Grundrissen zwischen zwei Kandidaten hin und her
— und das sieht aus wie ein Wackelkontakt, nicht wie eine Entscheidung.*
**`ortho` behält seinen Platz über `raster`; die drei neuen Arten schieben sich darüber, nicht
dazwischen.**

## Was das Werkzeug NICHT selbst tut

- **Es rechnet keine Bildschirmpixel um.** Der Aufrufer wandelt px über den Zoom in mm-Toleranz.
- **Es zeichnet nichts.** Reine Funktion, ohne Konva und ohne three.
- **Es entscheidet nicht, ob gefangen werden soll** — `aktiv: false` schaltet es ab.

## Die elf Ausfuhren

| Ausfuhr | Rolle |
|---|---|
| `FangArt` | die sieben Arten, zugleich die Rangfolge |
| `FangPunkt` | Punkt mit Art |
| `FangOptionen` | Konfiguration je Aufruf |
| `FangErgebnis` | Rückgabe **mit** Art, nicht nur Koordinate |
| `lotAufGerade()` | Lotfußpunkt — auf die **Gerade** |
| `fange()` | die Hauptfunktion |
| `FANG_PX = 12` | Toleranz in Bildschirmpixeln |
| `toleranzAusZoom()` | zoomabhängige Toleranz in mm |
| `WandStrecke` | Eingabeform |
| `wandFangpunkte()` | Fangpunkte aus Wänden ableiten |
| `FANG_TEXT` | Beschriftung je Art |

> *Alle elf gemessen in `resources/planner/hausplaner/geometry/fangKern.ts` (276 Zeilen).*
