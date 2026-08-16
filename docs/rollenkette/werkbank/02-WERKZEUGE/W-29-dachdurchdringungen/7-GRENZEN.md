# W-29 · Dachdurchdringungen — GRENZEN

## Der Befund: eine Behauptung steht neben einer Rechnung, die es besser weiß

```text
dachOeffnung.ts:91    auswechslungErforderlich: true          fest, immer
auswechslung.ts:87    analysiereAuswechslung(...)             gerechnet, 11 Zusagen
                      -> wechselErforderlich
                      -> pruefpflichtig, wenn nicht ableitbar

Produktivaufrufer von analysiereAuswechslung   NULL
Import von auswechslung in dachOeffnung        NULL
Aufrufer von oeffnungRechteck                  dachAusschnitt.ts:303
```

> ***Das behauptende Modul ist das, das läuft.*** *Für den Anwender heißt das: **jede** Dachöffnung
> meldet „Auswechslung erforderlich" — auch der Kamin, der laut Zusage `auswechslung.test.ts:33`
> zwischen zwei Sparren liegt und keinen schneidet.*
>
> **Eine Warnung, die immer kommt, ist die, die weggeklickt wird** *(A-03).* *Und sie kommt hier
> nicht aus Vorsicht, sondern aus einer nicht gezogenen Leitung.*

### Warum das trotzdem kein Fehler im engeren Sinn ist

**Der Hinweistext daneben gibt sich ausdrücklich als vorläufig aus** (`dachOeffnung.ts:93`):

> „Schematische Öffnung/Prüffeld. Dachausschnitt, Auswechslung und Statik fachlich prüfen."

> ***Das Modul verspricht keine Bemessung, es schiebt sie an den Menschen.*** **Der Befund ist
> deshalb nicht „hier steht etwas Falsches", sondern: daneben liegt eine geprüfte Rechnung, die
> diese Zurückhaltung an vielen Stellen überflüssig machen könnte.**

### Und die Entscheidung darüber gehört NICHT in eine Ablesung

**Sie ändert eine Auskunft über Statik an den Anwender.** *Wer `analysiereAuswechslung` anschließt,
verwandelt ein „immer prüfen" in ein „hier nicht nötig" — und dieser Satz trägt Verantwortung.*

> **Festgehalten, nicht entschieden.** *Der Anschluss wäre ein Bau, kein Blatt; er braucht einen
> eigenen Auftrag, eine fachliche Freigabe und eine Zusage, die Behauptung und Rechnung vergleicht.*

## Das Werkzeug ist vollständig gerechnet und nirgends angeschlossen

```text
780 Zeilen Geometrie · 82 gruene Zusagen
ADD_ROOF_AUFBAU im Schema und im Reducer gebaut (applyCommand.ts:332)
REMOVE_ROOF_AUFBAU ebenso (:343)
PRODUKTIVE AUFRUFER: keiner
toolRegistry: 0 Treffer fuer alle fuenf Durchdringungsarten
```

> ***Dieselbe Lage wie W-27/1 vor seinem Bau — nur weiter fortgeschritten.*** *Dort fehlte die
> Bedienung zu einer Engine; hier steht zusätzlich schon das **Modell**.* **Was fehlt, ist genau
> ein Registry-Eintrag, ein Handler und der Weg vom Klick zur Flächenkoordinate.**

## Drei der fünf Arten kennt nur die Rechnung

```text
istEinfacheDurchdringung(art)   Fenster · Kamin · Luefter · Lichtkuppel   -> true
istGaubeDurchdringung(art)      Gaube                                      -> eigener Fall

im Katalog (werkzeugPaket.ts)   gaube :147 · dachfenster :151
in app/ gesamt                  kamin 0 · luefter 0 · lichtkuppel 0
```

> **Die Rechnung ist breiter als der Katalog.** *Wer die drei fehlenden ergänzt, baut nichts Neues
> in der Geometrie — er macht sichtbar, was schon rechnet.*

## Was hier NICHT gerechnet wird, obwohl der Name es nahelegt

- **Keine Statik.** *`wechselLaengeM` ist eine Spannweite, keine Bemessung* (`3-FORMELN`).
- **Kein Geradenschnitt** (F-004). *Löcher werden über Rechteckbänder und Abstände geprüft.*
- **Keine Neigung.** *Alles läuft in Flächenkoordinaten; die Neigung steckt in der Fläche.*
- **Keine Projektion vom Grundriss auf die Dachfläche.** *Die fehlt, und sie ist die Voraussetzung
  für jede Bedienung* (`4-BEDIENUNG`).

## Nachbarschaft — nur abgegrenzt

```text
W-07   Dach aus Kontur   liefert die Flaeche, auf der hier geschnitten wird
W-21   Sparren/Lattung   liefert die Sparren, die hier geschnitten werden;
                         sparrenTrennung.ts:3 nennt auswechslung im Kommentar,
                         ohne sie zu importieren — eine Kante im Text, nicht im Code
W-30   Flachdach         eigenes Blatt
```
