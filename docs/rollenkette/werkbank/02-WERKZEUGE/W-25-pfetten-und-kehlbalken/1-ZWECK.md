# W-25 · Pfetten und Kehlbalken — ZWECK

> ***EINORDNUNG: W-25 ist eine ABLESUNG mit einem BEFUND*** — *gemessen, nicht angenommen.*
> **Die Mengenermittlung ist gebaut und ehrlich; ihre Liste dessen, was sie NICHT kann, ist an zwei
> von vier Stellen von den Nachbarmodulen überholt worden — und die Liste weiß es nicht.**

```text
RECHNUNG      GEBAUT   geometry/holzBauteile.ts    82 Z.,  4 Exporte
WAECHTER      GEBAUT   holzBauteile.test.ts        75 Z.,  6 Zusagen
FAEHIGKEIT    GEBAUT   app/tools/faehigkeiten.ts:86  'engine-holzbauteile'
                                                     Label „Holz-Bauteile (BOM)"
WERKZEUG      FEHLT    toolRegistry 'pfette' 0 · 'kehlbalken' 0
KATALOG       FEHLT    werkzeugPaket 'pfette' 0 · 'kehlbalken' 0
```

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er braucht die Holzliste**, *und zwar die vollständige — nicht nur die Sparren.* **Pfetten,
Gratsparren, Kehlbalken und Schwellen sind das, was den Dachstuhl trägt**, *und wer sie nicht auf
der Liste hat, bestellt zu wenig Holz.*

## Der tragende Punkt: das Modul rechnet NUR, was es sicher weiß — und sagt den Rest an

**`holzBauteile.ts:40-50`, der Kopfkommentar wörtlich:**

> „Offene Holzbauteil-Klassen, die geometrisch (noch) **NICHT zuverlässig** vorliegen und deshalb
> bewusst **NICHT als echte Mengen** ausgegeben werden (**keine erfundenen Werte**). Für die
> ehrliche Dokumentation in UI/Bericht."

```text
OFFENE_HOLZBAUTEILE (:45-50), vier Eintraege:
  1  Mittelpfette      benoetigt Auflagerpunkte/Stuhl — nicht modelliert
  2  Schwelle          Auflagerschwelle — nicht modelliert
  3  Wechselholz       Oeffnungsraender + betroffene Sparren nicht eindeutig bestimmt
  4  Schiftersparren   liegen geclippt vor, aber nicht eindeutig als Schifter benannt
```

> ***Das ist die vorbildlichste Bauform, die mir in diesem Werkzeugkasten begegnet ist:*** *ein
> Modul, das seine eigene Grenze **als exportierte Konstante** führt, damit die Oberfläche sie
> anzeigen kann.* **Nicht „wir rechnen es halt ungefähr", sondern „diese vier können wir nicht, und
> hier steht warum."**

## Und genau diese Liste ist an ZWEI von vier Stellen überholt

| # | Liste sagt | gemessen |
|---|---|---|
| 3 | Wechselholz: *„Öffnungsränder + betroffene Sparren **nicht eindeutig bestimmt**"* | **`auswechslung.ts:87`** rechnet `betroffeneSparren`, `wechselAnzahl`, `wechselLaengeM` — **11 Zusagen grün** (W-29) |
| 4 | Schiftersparren: *„liegen geclippt vor, **nicht eindeutig als Schifter benannt**"* | **`schifterListe.ts`** (152 Z., 9 Ausfuhren) benennt sie: `schifter` = `kehle`, `grat` oder `beidseitig` |

**Und der Beleg für #4 steht im Nachbarmodul selbst** (`schifterListe.ts:6-8`):

> „Diese lagen bisher pauschal als „Sparren" in der Holzliste (siehe `holzBauteile.ts` →
> `OFFENE_HOLZBAUTEILE`: „Schiftersparren als eigene Klasse … nicht eindeutig benannt").
> **EA28 schließt genau diese Lücke:** benennen + als „davon"-Breakdown ausweisen."

> ***Das Nachbarmodul zitiert die Liste, sagt, dass es die Lücke schließt — und die Liste steht
> unverändert da.*** **Die Ehrlichkeit ist gebaut, ihre Pflege nicht.**
>
> *Für den Anwender heißt das: der Bericht führt zwei Posten als „nicht ermittelbar", die zwei
> geprüfte Module inzwischen ermitteln.* **Eine Untertreibung ist billiger als eine erfundene Zahl
> — aber sie ist trotzdem falsch, und sie wird geglaubt.**

## Kehlbalken: als FLAGGE hinterlegt, als Menge nicht ermittelt

```text
dachformVorlagen.ts:91-95   ZimmererFlags — DREIZEHN Flaggen je Dachform,
                            darunter kehlbalken, zange, stuhlsaeule, wechsel
                    :1339   Satteldach: kehlbalken true
Verbraucher der Flaggen ausserhalb dachformVorlagen.ts:   KEINER
holzBauteileAusListe summiert:  pfette · gratsparren · kehlsparren
```

> ***Das Wissen ist da und wird nicht abgefragt.*** *Je Dachform steht hinterlegt, welche Bauteile
> fachlich vorkommen — und der Schritt von der Flagge zur Länge fehlt.* **Das ist keine Lücke im
> Sinne von „unbekannt", sondern eine nicht gezogene Leitung** — *dieselbe Bauform wie bei W-29 und
> W-17, und heute die dritte.*

**Und ich habe es zuerst zu eng gemessen:** *`kehlbalken` in `holzBauteile.ts` ergibt null, und
daraus wäre „gibt es nicht" geworden.* **Erst die Suche über den ganzen Inselbaum findet die
Flaggen.** *Siehe `3-FORMELN`.*
