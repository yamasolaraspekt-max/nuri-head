# W-03 · Wand bearbeiten — FUNKTION

## Die gebaute Hälfte — zwei Wege, und der Unterschied ist der Befehl

```text
EIGENSCHAFT                         GEOMETRIE
aktualisiereWand(changes)  :108     setzeWandLaenge(neu)  :113
  -> UPDATE_NODE                      -> MOVE_NODE  :120

Material  :324                      Laenge  :120
Staerke   :330                        len = Math.hypot(dx, dy)
Hoehe     :336                        end = start + (d/len) · neu, gerundet
```

> ***Die Länge ist keine Eigenschaft, sie ist eine Bewegung.*** *`setzeWandLaenge` verschiebt den
> **Endpunkt entlang der bestehenden Achsrichtung** — die Wand behält ihre Richtung und ihren
> Anfang.* **Deshalb `MOVE_NODE` und nicht `UPDATE_NODE`.**

**Zwei Wächter im Rumpf, beide gegen Unsinn** (`:114`, `:118`):

```text
!(neu > 0)   -> return    keine Laenge <= 0, und NaN faellt hier mit heraus
len === 0    -> return    eine Wand ohne Ausdehnung hat keine Richtung,
                          entlang der man verlaengern koennte
```

**Und das Ergebnis wird gerundet** (`Math.round`) — *die Wandenden bleiben ganzzahlig in mm, wie
überall im Modell.*

## Die fehlende Hälfte — fünf Operationen, ZWEI verschiedene Fundamente

**Das ist der Satz, ohne den W-03 als EIN Bau geschnitten wird und am falschen Ende stehenbleibt.**

### Fundament A — Geradenmathematik (`geometry/geradenGeometrie.ts`, aus A-32)

| Operation | was sie braucht |
|---|---|
| **trimmen** | Schnittpunkt zweier Wandachsen, dann auf ihn kürzen |
| **verlaengern** | derselbe Schnittpunkt, andere Richtung |
| **versatz** | die Achse parallel im Abstand `d` — `parallelVersatz` |

*Die Landkarte sagt es bei `trimmen` selbst:* **„Braucht Schnittpunktrechnung zweier Wände und ein
Kürzen auf den Schnittpunkt."**

### Fundament B — die Sammel-Ausführung (`store/hausplanerStore.ts`, aus A-31)

| Operation | was sie braucht |
|---|---|
| **teilen** | einen Knoten **ändern** UND einen **anlegen** — in **einem** umkehrbaren Schritt |
| **verbinden** | einen ändern, einen entfernen — dieselbe Klasse |

*Die Landkarte bei `teilen`:* **„Eine Wand an einem Punkt in zwei zu teilen heißt: einen Knoten
ändern UND einen anlegen, in EINEM umkehrbaren Schritt."*

> ***Die zwei Fundamente sind verschieden, und sie überschneiden sich nicht.*** *A-32 löst eine
> **Rechenfrage**, A-31 eine **Ausführungsfrage**.* **Wer W-03 als einen Bau schneidet, baut
> entweder drei Operationen ohne Klammer oder zwei ohne Rechnung** — und merkt es erst am Ende.

**Beide sind am Bau-Stand `BETRIEBSBESTAETIGT`** — *das Fundament steht, für alle fünf.*

## Ausgabe

| Was | Befehl | Rückgängig |
|---|---|---|
| Material · Stärke · Höhe | `UPDATE_NODE` | ein Undo |
| Länge | `MOVE_NODE` | ein Undo |
| *(teilen, verbinden — künftig)* | *zwei Befehle in **einem** `executeCommands`* | **ein** Undo, seit A-31 |

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne):** **ja** — über `UPDATE_NODE` / `MOVE_NODE`.
- **Rechnet in Schicht 2 (Geometrie):** heute nur `Math.hypot` in `setzeWandLaenge`; siehe
  `3-FORMELN`.
- **Lebt in Schicht 3 (Anwendung):** `app/rahmen/EigenschaftenPanel.tsx`.
- **Zeigt sich in Schicht 4/5:** als Felder im Eigenschaften-Panel.
