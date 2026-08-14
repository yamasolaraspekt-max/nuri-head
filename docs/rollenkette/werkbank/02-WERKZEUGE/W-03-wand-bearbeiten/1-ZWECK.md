# W-03 · Wand bearbeiten — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Eine gezeichnete Wand nachträglich ändern, ohne sie neu zu ziehen** — Material, Stärke, Höhe,
Länge.

## Der tragende Punkt: es gibt ZWEI Hälften, und nur eine fehlt

**Ein Blatt, das nur „nicht gebaut" sagt, lässt die nächste Rolle etwas bauen, das der Benutzer
heute schon tun kann.**

### Gebaut — über das Eigenschaften-Panel

| Was | Fundstelle (`app/rahmen/EigenschaftenPanel.tsx`) |
|---|---|
| **Material** | `:324` — Auswahlliste, `construction.materialId` |
| **Stärke** | `:330` — Auswahlliste aus `WANDSTAERKEN` |
| **Höhe** | `:336` — Zahlenfeld, `min={100}` |
| **Länge** | `:120` — über `setzeWandLaenge()`, als `MOVE_NODE` |
| **der generische Weg** | `:108` — `aktualisiereWand(changes)` → `UPDATE_NODE` |

> ***Die Länge fällt aus der Reihe, und das ist kein Zufall:*** *Material, Stärke und Höhe sind
> **Eigenschaften** und gehen über `UPDATE_NODE`. Die Länge ist **Geometrie** — sie verschiebt den
> Endpunkt entlang der Achsrichtung und geht deshalb über `MOVE_NODE`.*

### Nicht gebaut — fünf geometrische Operationen

`trimmen` · `verlaengern` · `versatz` · `teilen` · `verbinden`

## Und „nicht gebaut" heißt hier NICHT „nirgends vorhanden"

**Über vier Schichten gemessen, am Bau-Stand:**

```text
                Registry   Vertrag   Paket   Landkarte
trimmen            0          1        1        1 (fehlt)
verlaengern        0          1        1        1 (fehlt)
versatz            0          1        1        1 (fehlt)
teilen             0          1        1        1 (fehlt)
verbinden          0          1        1        1 (fehlt)
```

> ***Drei von vier Schichten führen sie bereits.*** *Sie sind **vertraglich beschrieben**, im
> **Werkzeugpaket** aufgeführt und in der **Landkarte** mit Begründung als `fehlt` vermerkt. **Es
> fehlt genau die letzte Schicht: der Registry-Eintrag**, der ein Werkzeug anklickbar macht.*

**Und das ist kein Sonderfall dieser fünf** — die Registry ist das Nadelöhr des ganzen Hauses:

```text
werkzeugVertrag.ts   111 Vertraege
werkzeugPaket.ts     101 Paket-Werkzeuge
toolRegistry.ts       12 Eintraege
```

> *Zwölf von 111 vertraglich beschriebenen Werkzeugen sind wirklich registriert.* **Wer „W-03 ist
> nicht gebaut" liest, denkt an fehlende Rechnung — gemessen fehlt die Verdrahtung.**

## Wann greift der Anwender danach?

**Sobald etwas nicht stimmt** — eine Wand ist zu dünn, zu hoch, aus dem falschen Material, oder sie
endet zwei Zentimeter zu früh. **Für die ersten vier gibt es einen Weg; für „sie soll bis zur
Nachbarwand reichen" nicht.**

## Woran merkt er, dass es fehlt?

**Er löscht die Wand und zeichnet sie neu.** *Bei einer Wand ist das lästig; bei einer Ecke aus
zwei Wänden verliert er die Gehrung und die Öffnungen, die daran hängen.*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs

- **Kein Zeichnen.** *Das ist W-02.*
- **Keine Auswahl und keine Griffe.** *Das ist W-13.*
- **Keine Öffnungen.** *Das ist W-04 — auch wenn das Panel sie mitbedient.*
