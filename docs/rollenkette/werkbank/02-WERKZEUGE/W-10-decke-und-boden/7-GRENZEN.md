# W-10 · Decke und Boden — GRENZEN

## Zwei Regeln, die Aufrufer treffen — keine Innereien

### (a) Höchstens EINE Decke pro Level

```text
applyCommand.ts:296   pruefeDeckeProLevel(draft, ceiling)      im ADD-Weg
              :315    pruefeDeckeProLevel(draft, ceiling)      im UPDATE-Weg,
                      Kommentar dort: „falls levelId geändert wurde"
decke.test.ts:50      „ADD_CEILING legt eine Decke an; zweite je Level
                       wird abgelehnt (max. 1)"                        ✔
```

> ***Die Prüfung steht zweimal, und das ist kein Doppel.*** *Eine zweite Decke kann auch dadurch
> entstehen, dass man eine vorhandene auf ein fremdes Level umhängt* — **derselbe Zustand über einen
> anderen Weg.**

### (b) Ganzzahligkeit wird NACH der Automatik geprüft

```text
applyCommand.ts:298   auto = mitgegebene Oeffnungen ODER treppenDurchbrueche(...)
              :299    gespeichert = { ...ceiling, oeffnungen: auto, ... }
              :300    pruefeDeckeGanzzahlig(GESPEICHERT)     <- auf dem Ergebnis
```

> ***Die Reihenfolge ist die Zusage.*** *Geprüft wird der Knoten, der gespeichert wird — samt der
> Löcher, die der Reducer selbst erzeugt hat.* **Stünde `:300` vor `:298`, prüfte er eine Eingabe,
> die es so nie in die Datei schafft** — *und `treppenDurchbrueche` rundet zwar selbst
> (`Math.round`, `:130`), aber das wäre dann Verlass auf eine fremde Zusage statt auf die eigene.*

### (c) Mitgegebene Öffnungen schalten die Automatik ab

**`applyCommand.ts:298`.** *Der Zeuge dafür steht in den Probedaten* (`studioFixtures.ts:59-61`):
*„Fixtures umgehen den `ADD_CEILING`-Reducer, der es sonst aus der Lauflinie ableitet."*

> **Es gibt keinen Schalter dafür.** *Der Zustand entsteht ausschließlich dadurch, dass ein Aufrufer
> `oeffnungen` füllt* — *und ein Anwenderweg, der das tut, existiert heute nicht.*

## `boden` ist ein Vertrag ohne Oberfläche — der Befund, nicht die Entscheidung

**Selbst gemessen:**

```text
app/tools/toolRegistry.ts         'boden'  ->  NULL Treffer
app/tools/werkzeugPaket.ts:167    { id: 'boden', label: 'Boden', kategorie: 'Architektur', … }
app/tools/werkzeugVertrag.ts:649  werkzeugId 'boden'  (commandId FloorCommand)
app/tools/werkzeugLandkarte.ts:170  { werkzeugId: 'boden', marke: 'deckt', begruendung: 'ADD_CEILING' }
                             :173  { werkzeugId: 'decke', marke: 'deckt', begruendung: 'ADD_CEILING' }
```

> ***Die Landkarte führt BEIDE auf denselben Befehl.*** *Modellseitig ist `boden` gedeckt — durch
> `ADD_CEILING`, dieselbe Deckung wie `decke`.* **Was fehlt, ist die Oberfläche.**

### Und der Satz, ohne den die nächste Rolle 110 erreichbare Werkzeuge liest

```text
toolRegistry.ts:316   export const PAKET_WERKZEUGE = 110;        <- eine KONSTANTE
               :335   export const EIGENE_WERKZEUGE = ['kontur'] <- eine LISTE
               :338   WERKZEUGE_GESAMT = 110 + 1
Registry-Eintraege selbst gezaehlt: ZWOELF
  :39 auswahl · :59 wand · :78 fenster · :96 tuer · :114 dach · :132 decke
  :150 treppe · :182 bemassen · :196 flaeche-messen · :230 kontur
  :249 loeschen · :273 duplizieren
```

> ***Die 110 sind GEZÄHLT, nicht verdrahtet.*** *Verdrahtet sind zwölf Registry-Einträge plus
> `EIGENE_WERKZEUGE`.* **`boden` ist im Paket geführt, im Vertrag beschrieben — und nicht
> erreichbar.**

### Die offene Frage gehört Yama

**`decke`s Tooltip heißt „Decke / **Bodenplatte**"** (`toolRegistry.ts:147`).

> ***Braucht W-24 überhaupt ein eigenes Werkzeug, oder ist es dieselbe Sache mit anderem
> `bauteilKind`?*** *Das Blatt hält den Befund fest und entscheidet ihn nicht* — **eine Ablesung
> entscheidet keine Einordnungsfrage.**

## Was das Werkzeug NICHT tut

| Grenze | am Code |
|---|---|
| **kein Fußbodenaufbau** | `schichten?` existiert (`scene.types.ts:357`), die Mengenermittlung ist **AUF-76/M0** und ein eigener Gegenstand |
| **keine Absage bei entarteter Treppe** | `:127` setzt bei Länge null den Ersatzwert 1 ein und liefert ein entartetes Loch |
| **keine 3D-Ansicht als Arbeitsfläche** | `supportedViews: ['2d','split']` (`:138`) |
| **kein Schutz vor selbstschneidendem Umriss auf dem Umriss-Weg** | auf dem Kontur-Weg prüft `pruefeKontur` (`HausplanerApp.tsx:831`, F-013/W-18); für `gebaeudeUmriss()` **nicht gemessen** und deshalb hier als offene Stelle und nicht als Mangel |

## Die Registerzeile trug zwei Nummern und brauchte drei

**Berichtigt mit W-10/1** — *siehe `3-FORMELN`:* **F-011 trägt, F-030 trägt zur Hälfte (die 2D-Hälfte
exakt, die Extrusion gar nicht), und F-001 fehlte.**

> *Und die Subtraktion „Fläche minus Lochpolygone" in `deckenNettoFlaecheM2` hat **keine** Nummer in
> der Sammlung.* **Als Lücke gemeldet — eine erfundene Nummer wäre schlimmer** *(Lehre aus W-21).*
