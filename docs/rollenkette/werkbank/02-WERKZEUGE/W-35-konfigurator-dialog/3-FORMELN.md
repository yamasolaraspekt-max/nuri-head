# W-35 · Konfigurator-Dialog — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **keine** | — | — |

**Der Dialog rechnet fast nichts** — *er sammelt Zahlen ein und reicht sie weiter.* **Drei Stellen
rechnen trotzdem, und sie stehen hier, weil sie sonst niemand vermutet.**

## Die drei Rechenstellen, jede geöffnet

### 1 · Die Maßklemmung in den Eingabefeldern

```ts
:117   setBreite(Math.max(100, Math.round(Number(e.target.value))))
:118   setHoehe (Math.max(100, Math.round(Number(e.target.value))))
```

**Untergrenze 100 mm, ganzzahlig, keine Obergrenze.** *`Number('')` ergibt `0`, und `Math.max(100, 0)`
ergibt `100` — ein geleertes Feld springt also auf 100, nicht auf leer.*

> **Keine Obergrenze ist eine Aussage:** *ein Fenster von 50 000 mm Breite lässt sich eintippen.*
> **Was danach damit geschieht, hängt vom Weg ab — und nur EINER der Wege prüft nach.**

### 2 · Die Einpassung in die gewählte Wand — die einzige echte Rechnung

```ts
:216   const len    = Math.hypot(wand.end.x - wand.start.x, wand.end.y - wand.start.y);
:217   const w      = Math.min(breite, Math.max(100, Math.round(len - 100)));
:218   const offset = Math.max(0, Math.round(len / 2 - w / 2));
```

**Drei Zeilen, und sie tragen die ganze Einpassung:**

```text
len     die Wandlaenge aus Anfangs- und Endpunkt (Satz des Pythagoras)
w       die Oeffnung wird auf len - 100 gedeckelt: 50 mm Rest je Seite,
        aber nie unter 100 mm — auch bei einer Wand, die kuerzer ist als 200 mm
offset  die Oeffnung wird MITTIG gesetzt: halbe Wand minus halbe Oeffnung
```

> **`w` ist der Ort, an dem die fehlende Obergrenze aus Stelle 1 aufgefangen wird** — *aber nur auf
> diesem Weg.* **Ein Fenster von 50 000 mm in einer 3 000-mm-Wand wird auf 2 900 gekürzt; dasselbe
> Fenster im autarken Weg bleibt 50 000.** *Dieselbe Eingabe, zwei Ergebnisse, je nach Lage.*

**Der Grenzfall, den `Math.max(100, …)` erzeugt:** *ist die Wand kürzer als 200 mm, wird `len - 100`
kleiner als 100, und die Öffnung wird trotzdem 100 breit — **breiter als der Platz**.* *Das
`executeCommand` darf sie dann ablehnen; die Meldung dafür steht bereit (`:227`, „passt nicht in die
Wand").* **Ob es ablehnt, ist Sache des Kommandos und damit W-42s Gegenstand, nicht dieses Blattes.**

### 3 · Die Geschosshöhe der Treppe

```ts
:196   geschosshoehe: Math.max(2000, hoehe)
```

**Untergrenze 2 000 mm, und sie liegt über der Vorbelegung des Feldes.** *Für die Treppe steht die
Höhe auf `2010` (Rückfall), das Feld heißt dort „Geschosshöhe (mm)" (`:118`).* **Wer es auf 1 500
setzt, bekommt trotzdem 2 000** — *stillschweigend, ohne Hinweis auf der Fläche.*

> **Das ist die einzige der drei Stellen, an der die Fläche etwas anderes zeigt als das, was
> weitergereicht wird.** *Sie steht deshalb auch in `7-GRENZEN.md`.*

## Fehlt eine Formel?

**Nein, und die Versuchung liegt bei Schritt 4.** *Der Schritt heißt „Prüfung" und zeigt drei
Zeilen (`:133-135`) — zwei Haken und eine Warnung.*

```text
:133   ✓  Masse plausibel
:134   ✓  DIN 18065 Schrittmass       (Treppe)   /   Norm-Anschlag korrekt
:135   !  Rastermass — 40 mm Versatz pruefen
```

> **Keine dieser drei Zeilen rechnet.** *Genau gesagt:* **`:134` hängt sehr wohl an einer Bedingung
> — `art === 'treppe'` wählt den TEXT.** *Aber keine der drei liest `breite` oder `hoehe`, und das
> Zeichen davor (`✓`, `✓`, `!`) ist an allen drei Stellen fest verdrahtet.* **„Maße plausibel" steht
> auch bei 50 000 × 100 mm da, und die Warnung steht auch dann da, wenn nichts zu warnen ist.**
>
> *Die Unterscheidung ist mir beim Gegenlesen aufgefallen und sie zählt: „hängt an keiner Bedingung"
> wäre falsch gewesen.* **Die Aussage lautet nicht „nichts ist bedingt", sondern „nichts ist
> gemessen".**
>
> **Hier wäre eine Formel fällig — und sie fehlt.** *Das ist eine Ablesung und keine Vorgabe: ich
> stelle fest, dass die Prüfung keine ist.* **Der Befund steht ausführlich in `7-GRENZEN.md`, weil er
> nicht in die Formelsammlung gehört, sondern zur Ehrlichkeit der Fläche.**

## Genauigkeit

**Alles ganzzahlig in Millimetern.** *`Math.round` in `:117`, `:118`, `:217`, `:218`.* **Kein
Gleitkomma verlässt den Dialog** — *außer aus `Math.hypot` in `:216`, und dieses Ergebnis wird in
`:217` und `:218` gerundet, bevor es weitergeht.*
