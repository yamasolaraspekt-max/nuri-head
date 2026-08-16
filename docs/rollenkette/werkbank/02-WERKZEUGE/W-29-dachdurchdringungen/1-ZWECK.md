# W-29 · Dachdurchdringungen — ZWECK

> ***EINORDNUNG: W-29 ist eine ABLESUNG mit einem BEFUND*** — *gemessen, nicht angenommen.*
> **Die Rechnung ist stark gebaut, ein Werkzeug gibt es nicht, und an einer Stelle behauptet der
> gebaute Weg etwas, das der ungenutzte Weg ausrechnet.**

```text
RECHNUNG      GEBAUT   geometry/dachOeffnung.ts     96 Z.,  4 Exporte
                       geometry/auswechslung.ts    174 Z.,  5 Exporte
                       geometry/dachAusschnitt.ts  510 Z., 20 Exporte
WAECHTER      GEBAUT   auswechslung.test.ts     90 Z., 11 Zusagen
                       dachAusschnitt.test.ts  594 Z., 71 Zusagen
                       zusammen 82 Zusagen, alle gruen
WERKZEUG      FEHLT    toolRegistry: 'dachfenster' 0 · 'kamin' 0 · 'luefter' 0
                       'lichtkuppel' 0 · 'gaube' 0
OBERFLAECHE   FEHLT    kein Verbraucher ausserhalb der Geometrie
```

> ***Dieselbe Lage wie bei W-27/1: die Engine läuft, die Bedienung fehlt.*** *Nur dass hier ein
> zweiter Befund darunterliegt, der schwerer wiegt als die fehlende Bedienung.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Ein Dach hat Löcher** — *Dachfenster, Kamin, Lüfter, Lichtkuppel.* **Und ein Loch im Dach ist
nicht dasselbe wie ein Loch in einer Wand:** *unter der Dachhaut liegen Sparren, und wer einen
Sparren durchschneidet, muss die Last umlenken.* **Das Bauteil dafür heißt Auswechslung.**

> ***Die Frage, die dieses Werkzeug beantworten muss, ist deshalb nicht „wo ist das Loch", sondern
> „was trägt danach".***

## Der tragende Punkt: ZWEI Module beantworten dieselbe Frage — eines rechnet, eines behauptet

```text
auswechslung.ts:87   analysiereAuswechslung(flaeche, oeffnung, opts)
                     -> betroffeneSparren      Anzahl geschnittener Sparren
                     -> spanntMehrereFelder    mehr als ein Sparrenfeld?
                     -> naheRandzone           First / Traufe / Ortgang?
                     -> wechselErforderlich    GERECHNET
                     -> pruefpflichtig         wenn NICHT sicher ableitbar
                     -> wechselAnzahl          0 oder 2 (oben + unten)
                     -> wechselLaengeM         Spannweite zwischen tragenden Sparren

dachOeffnung.ts:91   auswechslungErforderlich: true        FEST VERDRAHTET
```

**Gemessen, nicht vermutet:**

```text
Produktivaufrufer von analysiereAuswechslung        NULL
  alle elf Treffer stehen in auswechslung.test.ts
'auswechslung' in dachOeffnung.ts                   ZWEI Treffer
  beide sind Feldnamen (:40 Typ, :91 Wert) — KEIN Import, KEIN Aufruf
Aufrufer von oeffnungRechteck                       dachAusschnitt.ts:303
```

> ***Das behauptende Modul ist das, das läuft.*** *`dachAusschnitt` ruft `oeffnungRechteck` — und
> bekommt `auswechslungErforderlich: true`, immer, unabhängig von der Lage der Öffnung.* **Die
> Rechnung, die es besser weiß, wird von keinem Produktivpfad angefasst.**
>
> **Für den Anwender heißt das: jede Dachöffnung meldet „Auswechslung erforderlich"** — *auch die,
> bei der die Rechnung sagt, dass kein Sparren geschnitten wird.* **Eine Warnung, die immer kommt,
> ist die, die weggeklickt wird** *(A-03).* *Und sie kommt hier nicht aus Vorsicht, sondern aus
> einer nicht gezogenen Leitung.*

## Was der gebaute Weg dafür an Ehrlichkeit mitbringt

**Der Hinweistext daneben** (`dachOeffnung.ts:93`) *sagt es fast selbst:*

> „Schematische Öffnung/Prüffeld. Dachausschnitt, Auswechslung und Statik fachlich prüfen."

> ***Das ist keine Notlüge, sondern eine bewusste Zurückhaltung*** — *das Modul gibt sich als
> **schematisch** aus und schiebt die fachliche Prüfung an den Menschen.* **Der Befund ist deshalb
> nicht „hier steht etwas Falsches", sondern:** *daneben liegt eine Rechnung, die genau diese
> Zurückhaltung überflüssig machen könnte, und sie ist nicht angeschlossen.*

**Ob sie angeschlossen wird, ist eine Fachentscheidung und kein Ablesevorgang** — *sie ändert eine
Auskunft an den Anwender über Statik.* Siehe `7-GRENZEN`.
