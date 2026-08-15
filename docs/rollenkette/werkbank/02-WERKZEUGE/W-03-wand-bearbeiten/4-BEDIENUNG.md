# W-03 · Wand bearbeiten — BEDIENUNG

## Der Weg: Wand auswählen, Eigenschaften-Panel

**Vier Felder, in dieser Reihenfolge** (`app/rahmen/EigenschaftenPanel.tsx:323-341`):

| Feld | Beschriftung | Art |
|---|---|---|
| Material | **„Mauerwerk"** | Auswahlliste aus `MAUERWERK` |
| Stärke | **„Wandstärke (mm)"** | Auswahlliste: `115 · 150 · 175 · 240 · 300 · 365` |
| Höhe | **„Höhe (mm)"** | Zahlenfeld, `min={100}` |
| Länge | **„Länge (mm)"** | Zahlenfeld, `min={1}` |

**Die Beschriftung heißt „Mauerwerk" und nicht „Material"** — *das ist die Sprache des Fachs, nicht
die des Datenmodells (`construction.materialId`).*

## Der feine Zug bei der Wandstärke

**Eine Wand kann eine Stärke haben, die nicht in der Liste steht** — etwa aus einem Import oder
einem älteren Stand. **Dann bekommt sie einen eigenen Eintrag** (`:331`):

```text
{selectedWall.thickness} mm (aktuell)
```

> ***Die Liste setzt den Wert nicht stillschweigend auf den nächsten Listenwert.*** *Der Anwender
> sieht seine 200 mm als „200 mm (aktuell)" und entscheidet selbst, ob er auf 240 wechselt.*
> **Ohne diesen Zweig zeigte die Liste einen Wert an, den die Wand nicht hat** — und der erste Klick
> irgendwo im Panel schriebe ihn fest.

## Die Länge — sichtbar gerechnet, nicht gespeichert

**Das Feld zeigt `Math.round(Math.hypot(...))`** (`:339`) — *die Länge ist kein Feld der Wand,
sondern der Abstand ihrer Endpunkte.* **Beim Ändern verschiebt `setzeWandLaenge()` den Endpunkt
entlang der bestehenden Achsrichtung** (`:113-121`).

```text
!(neu > 0)  ->  nichts passiert     (faengt auch NaN aus einem leeren Feld)
len === 0   ->  nichts passiert     (ohne Richtung gibt es kein Verlaengern)
sonst       ->  MOVE_NODE, Endpunkt neu, ganzzahlig gerundet
```

> **Anfang und Richtung bleiben.** *Wer „Länge" ändert, erwartet, dass sich das **Ende** bewegt —
> nicht die ganze Wand.*

## Was der Anwender NICHT kann

**Fünf geometrische Operationen fehlen:** `trimmen` · `verlaengern` · `versatz` · `teilen` ·
`verbinden`.

> ***Sie fehlen nur in der Registry.*** *Vertrag, Werkzeugpaket und Landkarte führen sie bereits —
> es gibt keinen anklickbaren Eintrag.* Siehe `1-ZWECK` und `7-GRENZEN`.

**Was er ersatzweise tut:** *löschen und neu zeichnen.* **Bei einer Ecke aus zwei Wänden kostet ihn
das die Gehrung und die Öffnungen, die an der Wand hängen.**

## Rückgängig

**Jede Änderung ist ein Befehl und damit ein Undo-Schritt** — `UPDATE_NODE` für Material, Stärke und
Höhe, `MOVE_NODE` für die Länge.

> *Wenn `teilen` und `verbinden` gebaut werden, gilt seit A-31 dasselbe für sie: **zwei Befehle in
> einer Klammer, EIN Undo.*** *Vorher wären es zwei Schritte gewesen, und der Anwender hätte die
> geteilte Wand halb zurückgenommen.*
