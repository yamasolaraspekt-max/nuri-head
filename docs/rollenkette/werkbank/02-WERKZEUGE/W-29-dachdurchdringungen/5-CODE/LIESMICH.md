# W-29 · Dachdurchdringungen — CODE

> ***780 Zeilen Geometrie, 82 grüne Zusagen — und KEIN Produktivverbraucher.*** *Das ist die
> Bilanz dieses Werkzeugs, und sie ist an drei Modulen einzeln gemessen.*

## Die drei Module

| Modul | Z | Ausfuhren | Produktivverbraucher |
|---|---|---|---|
| `geometry/dachOeffnung.ts` | 96 | 4 | **`dachAusschnitt.ts:27`/`:303`** |
| `geometry/auswechslung.ts` | 174 | 5 | **keiner** |
| `geometry/dachAusschnitt.ts` | 510 | 20 | **keiner** |

```text
dachOeffnung.ts    :19 OeffnungEingabe · :29 OeffnungRechteck
                   :52 oeffnungVTiefeM · :60 oeffnungRechteck
auswechslung.ts    :24 FlaecheMasse · :31 Oeffnung · :42 AuswechslungAnalyse
                   :69 sparrenPositionenU · :87 analysiereAuswechslung
dachAusschnitt.ts  :32 AusschnittStatus · :289 berechneAusschnitt
                   :458 sichereLoecher · :500 flaechenBilanz  (+16 weitere)
```

> **Die einzige gezogene Leitung führt von `dachAusschnitt` zu `dachOeffnung`** (`:27` Import,
> `:303` Aufruf). *Alles andere hängt an nichts.*

## Die Bilanz der Verbraucher, gemessen

```text
Importe von './dachAusschnitt' ausserhalb __tests__     NULL
Aufrufer von analysiereAuswechslung ausserhalb __tests__ NULL
ADD_ROOF_AUFBAU: Aufrufer ausserhalb Schema und Reducer  NULL
   (die zwei Treffer sind BEGRUENDUNGSTEXTE in werkzeugLandkarte.ts:172/:176)
```

> ***Ein Werkzeug, das vollständig gerechnet und nirgends angeschlossen ist.*** *Dieselbe Lage wie
> W-27/1 vor seinem Bau — mit dem Unterschied, dass hier auch das **Modell** schon steht:*
> `ADD_ROOF_AUFBAU` *und* `REMOVE_ROOF_AUFBAU` *sind im Reducer gebaut* (`applyCommand.ts:332`,
> `:343`), *samt Ganzzahligkeitsprüfung und einer Ablehnung `aufbau_unbekannt`.*

## Der Querbezug, den der Code selbst nennt

**`geometry/sparrenTrennung.ts:3`**, *im Dateikopf:*

> „(Dachfenster/Kamin/…). Ergänzt Reparatur 9 (`auswechslung.ts`: Sicher-Entscheidung)"

> ***Ein viertes Modul weiß von der Auswechslung und nennt sie beim Namen*** — *ohne sie zu
> importieren.* **Der Zusammenhang steht in einem Kommentar statt in einer Kante.** *Als Fundstelle
> benannt; ob `sparrenTrennung` zu W-29 oder zu W-21 gehört, entscheidet dieses Blatt nicht.*

## Was in `app/` liegt und was nicht

```text
werkzeugPaket.ts:147   gaube        Katalogeintrag, Label, Icon
                :151   dachfenster  dito
werkzeugVertrag.ts     Vertragszeilen fuer beide
werkzeugThemen.ts      Themen-Bindung
toolPresentation.ts    Zonen-Regel
werkzeugLandkarte.ts:172/:176   marke: 'deckt', begruendung: 'ADD_ROOF_AUFBAU'

toolRegistry.ts        NULL Treffer fuer alle fuenf Durchdringungsarten
app/ gesamt            'kamin' 0 · 'luefter' 0 · 'lichtkuppel' 0
```

> ***`marke: 'deckt'` ist wahr und irreführend zugleich.*** *Das MODELL deckt es — der Befehl
> existiert.* **Die BEDIENUNG deckt es nicht** — *niemand ruft ihn.* **Die Landkarte beschreibt die
> Modelldeckung, und wer sie als Fertigmeldung liest, liest sie falsch.**
>
> *Das ist kein Fehler der Landkarte, sondern eine Grenze ihrer Aussage — sie steht hier, weil
> genau diese Verwechslung schon einmal einen Auftrag gekostet hat (A-29).*
