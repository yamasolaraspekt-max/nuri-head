# W-43 · Abbund-Zeichnung — BEDIENUNG

## Es gibt nichts zu bedienen

```text
toolRegistry / werkzeugPaket   abbund 0 · zimmerer 0 · werkplan 0
renderers/                     dieselben:  0
app/                           dieselben:  0
```

## Der billigste erste Schritt liegt offen — und ist keine Zeichnung

**Dreizehn Dachformen tragen je einen fertig formulierten Zimmerer-Text.** *Ihn anzuzeigen,
verlangt kein Zeichenwerkzeug, keine Geometrie und keine fachliche Freigabe:*

```text
Vorlage gewaehlt   GEBAUT
  -> abbundhinweis / spannweiteHinweis / lastabtragsweg anzeigen   FEHLT
     (drei Felder, ein Leser)
```

> ***Der Text ist für einen Menschen geschrieben und erreicht keinen.*** **Er ändert keine Zahl,
> er behauptet keine Statik — er sagt dem Zimmerer, wie diese Dachform üblicherweise abgebunden
> wird.** *Und wo er es nicht weiß, sagt er „Geplant".*

## Warum die Zeichnung selbst NICHT der nächste Schritt wäre

**Eine Abbund-Zeichnung ist ein Werkplan.** *Sie wird auf die Baustelle genommen und nach ihr wird
geschnitten.* **Alles, was das Haus heute dazu hat, trägt ausdrückliche Vorbehalte:**

```text
materialFestigkeit  'NH C24 (Richtwert)'
spannweiteHinweis   'Geplant — statisch zu pruefen'
lastabtragsweg      'Geplant — formabhaengig festzulegen'
W-25 OFFENE_HOLZBAUTEILE   vier Klassen ausdruecklich nicht ermittelt
```

> ***Eine Zeichnung sieht verbindlich aus, auch wenn ihre Grundlage es nicht ist.*** **Das ist
> der Unterschied zu einer Zahl mit Vorbehalt: der Vorbehalt reist mit der Zahl, aber nicht mit
> der Linie.** *Wer hier zeichnet, bevor W-25s vier offene Klassen geschlossen sind, erzeugt ein
> Dokument, dessen Vorbehalt sich nicht mitzeichnen lässt.*

**Festgehalten, nicht entschieden** — *die Reihenfolge (erst Bemessung, dann Darstellung) ist eine
fachliche Weiche und gehört an Yama und den Planner.*

## Vorbild im Haus für den Anzeige-Schritt

`SCHNEEFANG_HINWEIS`, `ABWASSER_VORBEHALT`, `OFFENE_HOLZBAUTEILE` — **drei Fälle, in denen ein
Modul seine Grenze als exportierte Konstante führt, damit die Oberfläche sie zeigen kann.**

> *Hier liegt die Grenze bereits IM DATENSATZ je Vorlage* — **das ist die feinere Auflösung, denn
> „Geplant" gilt nur für die eine Dachform und nicht für alle.** *Was fehlt, ist ausschließlich der
> Leser.*
