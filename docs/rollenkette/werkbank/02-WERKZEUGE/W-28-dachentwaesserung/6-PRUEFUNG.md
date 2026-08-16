# W-28 · Dachentwässerung — PRÜFUNG

## Kein Wächter, und diesmal ist das richtig

```text
Zusagen, die 'dachrinne' nennen:        0
Zusagen, die 'fallrohr' nennen:         0
Waechter fuer linienBauteile.ts:        1 Datei  (deckt platziereSchneefang / Sperrzonen)
```

> ***Es gibt nichts zu prüfen, weil es nichts gibt.*** *Eine Zusage über einen Typ-Wert, den
> niemand erzeugt, würde die Schreibweise der Aufzählung einfrieren und sonst nichts* — **genau die
> Bauform F-06: ein Wächter, der einen Zustand festhält statt eine Eigenschaft zu prüfen.**

## Was ein Wächter prüfen müsste, sobald gebaut wird

| Eigenschaft | warum sie eine Eigenschaft ist und kein Beispiel |
|---|---|
| **Summe der Einzugsflächen = Dachfläche** | fängt jede Aufteilung, die doppelt zählt oder verliert — dieselbe Bilanzform wie `Restflächen + Öffnung = Bruttofläche` in W-29 |
| **mehr Fläche → nie weniger Querschnitt** | Monotonie; fängt Vorzeichen- und Einheitenfehler ohne feste Zahlen |
| **Vorbehalt wird mit ausgegeben** | wie `SCHNEEFANG_HINWEIS` und `ABWASSER_VORBEHALT`: die Grenze reist mit dem Ergebnis |
| **keine erfundenen Werte bei fehlender Regenspende** | die Bauform aus W-25: lieber „nicht ermittelbar" als eine plausible Zahl |

> **Die vierte Zeile ist die wichtigste und die unbequemste.** *Eine Regenspende ist ortsabhängig;
> ohne Ort ist die Bemessung nicht ableitbar.* **Ein Werkzeug, das dann einen Mittelwert einsetzt,
> liefert eine Zahl, die geglaubt wird** — *und der Anwender sieht ihr nicht an, dass sie geraten
> ist.*

## Die bestehende Zusage nebenan, die man dabei nicht brechen darf

`__tests__/zweiEnginesSchweigen.test.ts` **(A-17)** hält fest:

> *„Das Flag darf EINE Engine stumm schalten, nicht negative Urteile allgemein."*

> ***Wer W-28 als weitere Engine anschließt, fällt in genau diesen Prüfbereich.*** *Die Zusage ist
> nach dem Muster von `sparrenVorbehalt.test.ts` (A-14) entstanden, weil zwei Engines ihr
> Gesamturteil verloren haben.* **Eine dritte Engine ohne diese Rücksicht wiederholt den Fehler,
> für den es bereits einen Wächter gibt.**

## Wie diese Ablesung rot werden könnte

**Nicht durch fehlenden Code — durch eine zu weite Messung.** *Und ich bin einmal knapp
vorbeigelaufen:*

```text
Muster 'dachrinne|fallrohr|rinne'   ->  55 Treffer / 14 Dateien   (klingt nach Bestand)
Muster 'dachrinne|fallrohr'         ->   2 Dateien
gelesen                             ->   1 Zeile
```

> ***Die 55 waren echt und trotzdem falsch:*** *`rinne` ist eine Teilzeichenfolge und trifft
> Wörter, die mit Entwässerung nichts zu tun haben.* **Hätte ich sie übernommen, stünde hier ein
> Werkzeug „in Arbeit", das aus einem Wort besteht.**

**Alle Zahlen dieses Blattes tragen ihren Befehl:** *`grep -rn` über
`resources/planner/hausplaner` mit `--include='*.ts' --include='*.tsx'`, gefahren am 16.08.*
