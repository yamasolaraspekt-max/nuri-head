# W-03 · Wand bearbeiten — CODE

**W-03 hat kein eigenes Modul.** *Die Bearbeitung wohnt im Eigenschaften-Panel, das mehrere
Werkzeuge bedient — Wände, Öffnungen, Dächer, Treppen, Fenster.*

| Datei | Z | Ausfuhren |
|---|---|---|
| `resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx` | 563 | `EigenschaftenPanelEigenschaften` (54) · `EigenschaftenPanel()` (76) |

## Der Wand-Anteil, Stelle für Stelle

```text
:89    WANDSTAERKEN = [115, 150, 175, 240, 300, 365] as const
:108   aktualisiereWand(changes)   -> UPDATE_NODE
:113   setzeWandLaenge(neu)        -> MOVE_NODE  (:120)
         :114  !(neu > 0)  -> return
         :117  len = Math.hypot(dx, dy)          F-001
         :118  len === 0   -> return
:324   Mauerwerk       select, construction.materialId
:330   Wandstaerke     select, mit Sonderfall :331 fuer Werte ausserhalb der Liste
:336   Hoehe           input, min 100
:339   Laenge          input, min 1 — der Wert wird GERECHNET, nicht gelesen
```

> ***Zwei Wege, ein Panel:*** *Eigenschaften gehen über `UPDATE_NODE`, die Länge über `MOVE_NODE`.*
> **Der Unterschied ist nicht Geschmack** — die Länge ist Geometrie, siehe `2-FUNKTION`.

## Die fünf fehlenden Operationen, über vier Schichten gemessen

```text
                 toolRegistry   werkzeugVertrag   werkzeugPaket   werkzeugLandkarte
trimmen                0              1                1            1 (marke 'fehlt')
verlaengern            0              1                1            1 (marke 'fehlt')
versatz                0              1                1            1 (marke 'fehlt')
teilen                 0              1                1            1 (marke 'fehlt')
verbinden              0              1                1            1 (marke 'fehlt')

zum Vergleich, dieselben Dateien insgesamt:
  werkzeugVertrag.ts  111 · werkzeugPaket.ts 101 · toolRegistry.ts 12
```

> **Es fehlt genau die Registry — die Schicht, die ein Werkzeug anklickbar macht.** *Und sie ist mit
> 12 von 111 das Nadelöhr des ganzen Hauses, nicht ein Sonderfall dieser fünf.*

## Die zwei Fundamente, beide vorhanden

```text
geometry/geradenGeometrie.ts   A-32, BETRIEBSBESTAETIGT   -> trimmen, verlaengern, versatz
store/hausplanerStore.ts       A-31, BETRIEBSBESTAETIGT   -> teilen, verbinden
  executeCommands(...)           eine Liste, EIN Undo-Schritt
```

**Beide am Bau-Stand geprüft, nicht aus dem Auftrag übernommen** — *der Auftrag sagte noch „A-32
gebaut und noch nicht abgenommen".*

## Kein eigener Befehl

**W-03 kommt mit `UPDATE_NODE` und `MOVE_NODE` aus.** *Auch die fünf fehlenden Operationen bräuchten
keinen neuen Befehlstyp — `teilen` und `verbinden` brauchen die **Klammer** um zwei vorhandene, und
die liegt seit A-31 im Store.*
