# W-14 · Kopieren, Spiegeln, Drehen — BEDIENUNG

## Vier Operationen, vier verschiedene Wege — und das ist der Kern

| Operation | wo der Anwender sie findet | worauf sie wirkt |
|---|---|---|
| **Duplizieren** | Werkzeugleiste (`toolRegistry.ts:273`) | die **Auswahl** |
| **Löschen** | Werkzeugleiste (`:249`) | die **Auswahl** |
| **Spiegeln** | zwei Knöpfe im **Kopfrahmen** | der **ganze Grundriss** |
| **Verschieben** | Ziehen auf der Bühne · Panel-Feld · Duplizieren mit Versatz | Wand bzw. Auswahl |

> ***Nur zwei der vier sind Werkzeuge im Sinne der Leiste.*** *Spiegeln ist ein Knopf, Verschieben
> ist gar keins — es passiert beim Ziehen.*

## Die zwei Spiegel-Knöpfe

```text
Kopfrahmen.tsx:315   icon="mirror-h"   title="Grundriss links/rechts spiegeln"
              :316   icon="mirror-v"   title="Grundriss oben/unten spiegeln"
beide:               disabled={waende.length === 0}
```

> ***Die Titel sagen „Grundriss" — nicht „Auswahl".*** *Das ist die wichtigste Angabe an dieser
> Stelle, denn der Anwender kommt von zwei Werkzeugen, die auf die Auswahl wirken.*
>
> **Und die Sperre passt dazu:** *sie hängt an `waende.length === 0` und nicht daran, ob etwas
> ausgewählt ist.* **Ohne Wände gibt es nichts zu spiegeln; mit Wänden geht es immer.**

**„links/rechts" und „oben/unten" statt „vertikal" und „horizontal"** — *der Code kennt
`Achse = 'vertikal' | 'horizontal'`, der Knopf sagt, was der Anwender sieht.* **Die zwei Wörter
werden regelmäßig verwechselt; die Bewegungsrichtung nicht.**

## Rückgängig — ein Schritt für die ganze Spiegelung

**Seit A-31 bildet `spiegeleGrundriss` eine Befehlsliste und übergibt sie in **einem** Zug.**

> *Vorher war jede Wand ein eigener Undo-Schritt.* **Ein Undo drehte eine Wand zurück und ließ den
> Grundriss halb gespiegelt stehen** — *ein Zustand, den es zeichnerisch nicht geben kann, und bei
> zwanzig Wänden musste der Anwender zwanzigmal drücken und dabei mitzählen.*

## Was der Anwender NICHT kann

**Drehen.** *Kein Knopf, kein Werkzeug, keine Eingabe.* Siehe `7-GRENZEN` — *der Grund ist das
Schema, nicht die Rechnung.*

**Und er kann nicht eine Auswahl spiegeln.** *Die zwei Knöpfe nehmen immer alles.* **Ob das so
bleiben soll, ist eine Produktfrage und in diesem Blatt nicht entschieden** — *sie steht als offener
Punkt bei W-14/1 (Yamas Entscheidung).*

## Was beim Spiegeln mit den Öffnungen passiert

**Sie wandern richtig mit** — *nachgerechnet, siehe `2-FUNKTION`.* **Der Anwender muss nichts
nachziehen**, weil `start` und `end` an ihrem Platz gespiegelt werden und `offsetFromWallStart`
dadurch weiter vom richtigen Ende misst.
