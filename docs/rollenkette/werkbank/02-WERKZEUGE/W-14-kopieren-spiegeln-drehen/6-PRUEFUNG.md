# W-14 · Kopieren, Spiegeln, Drehen — PRÜFUNG

## Der Wächter: `__tests__/editierGeometrie.test.ts` — 52 Z., ACHT Zusagen, alle grün

**Selbst gefahren am Bau-Stand:**

```text
✔ versetzePunkt: verschiebt und rundet ganzzahlig
✔ versetzteWand: beide Endpunkte um denselben Versatz
✔ spiegelePunkt vertikal:   x an Achse gespiegelt, y bleibt
✔ spiegelePunkt horizontal: y an Achse gespiegelt, x bleibt
✔ Doppelte Spiegelung an derselben Achse ergibt den Ausgangspunkt
✔ spiegelteWand vertikal: Wand an Achse gespiegelt
✔ bbox + achsenMitte: Mittelachse durch die Auswahl
✔ bbox: leere Menge ⇒ null
ℹ tests 8 · pass 8 · fail 0
```

### Die zwei, auf die es ankommt

> ***„versetzteWand: beide Endpunkte um DENSELBEN Versatz"*** — *das ist die Zusage, die eine
> Translation von einem Parallelversatz trennt.* **Wer `versetzteWand` eines Tages zum
> Parallelversatz umbaut, wird hier rot** — und genau diese Verwechslung hat am 13.08. eine falsche
> Fahrplan-Einordnung erzeugt (A-29).

> ***„Doppelte Spiegelung ergibt den Ausgangspunkt"*** — *eine **Involution**, also eine Eigenschaft
> und kein Beispiel.* **Sie fängt jede Umstellung, die das Vorzeichen oder den Bezug verdreht** —
> ein einzelner Zahlenvergleich täte das nicht zuverlässig.
>
> *Ihre Grenze steht seit dem 14.08. in der Sammlung selbst:* **F-032s Grenzfall 2 (Rundung)** *hat
> genau an dieser Achsenspiegelung nachgerechnet, dass die Involution bei **ganzzahligen**
> Koordinaten exakt ist und bei nicht ganzzahligen um bis zu 0,5 mm driftet.* **Das Modul rundet
> jeden Punkt (`Math.round`, 6×), womit der geprüfte Fall der gebaute ist.**

**Und `bbox: leere Menge ⇒ null`** ist die Kanten-Zusage: *ohne sie käme `achsenMitte` an `null`
nicht vorbei, sondern an `Infinity` — und die Spiegelachse läge im Nirgendwo.*

## Was der Wächter NICHT hält

| ungeprüft | Folge |
|---|---|
| **der Bezug der Spiegel-Knöpfe** — „ganzer Grundriss" statt Auswahl | keine Zusage hält fest, dass `spiegeleGrundriss` über `waende` läuft und nicht über `selectedNodeIds` |
| **`disabled={waende.length === 0}`** | ein verdrehter Zustand („aktiv, wenn keine Wände") fiele nur im Bild auf |
| **die Titel** „Grundriss links/rechts" | nichts hält fest, dass sie nicht „Auswahl" sagen |
| **`spiegelteWand` horizontal** | vertikal ist verriegelt, horizontal nur über `spiegelePunkt` |

> ***Die erste Zeile ist die wichtigste.*** *Der Bezugsrahmen ist der tragende Punkt dieses
> Werkzeugs — und er ist durch **keine** Zusage gesichert.* **Wer `befehleSpiegeln(waende, achse)`
> zu `befehleSpiegeln(auswahl, achse)` ändert, bricht keinen Test** — und der Anwender bekäme einen
> Knopf, der „Grundriss" verspricht und die Auswahl spiegelt.

## Die Klammer ist anderswo verriegelt

**Dass die Spiegelung EIN Undo-Schritt bleibt, hält `__tests__/sammelBefehle.test.ts`** (A-31) —
*mit mindestens zwei Wänden, weil die alte Fassung bei einer zufällig auch richtig war.*

> *Die Zusage gehört zu A-31 und nicht zu W-14* — **aber sie schützt W-14s wichtigste
> Bedieneigenschaft**, und deshalb steht sie hier.
