# W-14 · Kopieren, Spiegeln, Drehen — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Etwas, das schon gezeichnet ist, noch einmal haben — versetzt, gespiegelt oder gedreht** — statt
es ein zweites Mal zu ziehen.

## Der tragende Punkt: vier Operationen, DREI verschiedene Bezugsrahmen

**Wer sie für gleichartig hält, drückt einen Knopf und bekommt etwas anderes als erwartet.**

| Operation | Bezug | Weg dorthin |
|---|---|---|
| **duplizieren** | die **Auswahl** | Registry-Werkzeug (`toolRegistry.ts:273`) |
| **loeschen** | die **Auswahl** | Registry-Werkzeug (`:249`) |
| **spiegeln** | der **GANZE Grundriss** | Knopf im Kopfrahmen — **kein** Registry-Eintrag |
| **verschieben** | Wand bzw. Auswahl | **kein** Werkzeug, mehrere Wege |

> ***Der Bruch sitzt bei „spiegeln".*** *Duplizieren und Löschen wirken auf das, was der Anwender
> ausgewählt hat. **Spiegeln wirkt auf alles** — unabhängig davon, was ausgewählt ist.*

**Und das ist belegt, nicht behauptet** (am Bau-Stand):

```text
Kopfrahmen.tsx:315   title="Grundriss links/rechts spiegeln"  disabled={waende.length === 0}
              :316   title="Grundriss oben/unten spiegeln"    disabled={waende.length === 0}
HausplanerApp.tsx:695  spiegeleGrundriss(achse)
                       -> befehleSpiegeln(WAENDE, achse)   — alle Waende, NICHT selectedNodeIds
toolRegistry.ts        'spiegeln'  ->  0 Treffer
```

> **Die Titel sagen es dem Anwender: „Grundriss".** *Die Sperre hängt an `waende.length`, nicht an
> der Auswahl — auch das ist konsequent.* **Nur ein Blatt, das „vier Operationen" schreibt, ohne den
> Bezug zu nennen, verwischt es.**

## Die vierte fehlt — und zwar aus einem Schema-Grund

**`drehen` gibt es nicht**, und der Grund liegt tiefer als „noch nicht gebaut":

```text
domain/scene.types.ts:193-196   transform: { position, rotation, scale }   — am ObjectNode
Waende                          tragen start/end. Keine Rotation.
commands/applyCommand.ts:203    MOVE_NODE fuer undefinierte Typen -> CommandAbgelehnt
```

> ***Ein Objekt kann sich drehen, eine Wand nicht.*** *Sie ist durch **zwei Punkte** beschrieben —
> eine Drehung müsste beide neu rechnen, und das ist kein `rotation`-Feld, sondern eine andere
> Geometrie.* Siehe `7-GRENZEN`, dort stehen auch die zwei offenen Fragen.

## Wann greift der Anwender danach?

**Bei Wiederholungen.** *Ein Reihenhaus, eine gespiegelte Doppelhaushälfte, zwei gleiche Zimmer
nebeneinander.*

## Woran merkt er, dass es fehlt?

**Er zeichnet dasselbe zweimal** — und die zweite Fassung stimmt nie ganz mit der ersten überein.

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs

- **Keine Auswahl.** *Was ausgewählt ist, entscheidet W-13.*
- **Kein Parallelversatz.** *`versetzteWand` ist eine **Translation** — siehe `2-FUNKTION`; die
  Verwechslung hat schon einmal eine falsche Fahrplan-Einordnung erzeugt (A-29).*
- **Kein Geschoss-Duplikat.** *Das ist W-06.*
