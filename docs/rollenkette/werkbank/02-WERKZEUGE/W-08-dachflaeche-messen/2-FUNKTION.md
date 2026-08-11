# W-08 · Dachfläche messen — FUNKTION

## Die Eingangsbedingung steht vor allem anderen

**Die Ebene, in der die Punkte liegen, entscheidet über die Bedeutung des Ergebnisses** — und das
Modul kann sie nicht prüfen:

> *„Eingabe sind die 2D-Punkte der Dachfläche **IN DER (geneigten) Flächenebene**, in **Metern** (so
> liegt `surf.polygon` vor: lokale u/v-Koordinaten der Dachfläche). Damit ist das Ergebnis die echte
> geneigte Dachfläche in m²."* (`resources/planner/hausplaner/geometry/polygonFlaeche.ts:11-13`)

**Dieselben Zahlen in der Grundriss-Ebene ergeben die Grundfläche** — dieselbe Rechnung, ein anderes
Maß, und **kein Unterschied im Ergebnistyp**. *Es ist keine Zahl dabei, die verrät, welche der beiden
man in der Hand hat.*

## Die zwei Ausfuhren

| Zeile | Ausfuhr | |
|---|---|---|
| 19 | `Punkt2D` | `{x, y}` — *„nimmt beliebige Objekte mit numerischen x/y entgegen (auch `THREE.Vector2`)"* (Z.15-16) |
| 31 | `polygonFlaecheM2()` | Shoelace, Betrag, geteilt durch 2 (Z.44, 46) |

## Wer es benutzt — vier Importe, gemessen

```text
renderers/three-d/deckenMesh.ts:7
geometry/dachAusschnitt.ts:26
geometry/dachformVorlagen.ts:33
geometry/grundriss.ts:19
```

**Vier, nicht fünf.** *Der Auftrag nennt fünf Aufrufer; gemessen sind es vier Importe im
Produktivcode. Die fünfte Fundstelle ist `wandFlaeche.ts` — sie **importiert nichts**, siehe `5-CODE`.*

## Der Azimut kommt hier nicht vor — und das ist die Antwort

`polygonFlaecheM2()` nimmt **ausschließlich Punkte** entgegen. **Es gibt keinen Parameter für eine
Ausrichtung**, also auch keinen Weg, eine Konvention mitzuliefern oder wegzulassen.

**Damit gilt Antwort (a) in ihrer schärfsten Form:** das Werkzeug nimmt **überhaupt keinen**
Azimutwert und kann ihn deshalb nicht stillschweigend durchrechnen. *Die Gefahr sitzt nicht hier,
sondern eine Ebene weiter — dort, wo diese Fläche mit einer Ausrichtung zusammengeführt wird.*
**Siehe `7-GRENZEN`.**
