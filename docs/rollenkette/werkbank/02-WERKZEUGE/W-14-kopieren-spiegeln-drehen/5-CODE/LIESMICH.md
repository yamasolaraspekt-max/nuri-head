# W-14 · Kopieren, Spiegeln, Drehen — CODE

**EIN Geometriemodul, 75 Zeilen, NEUN Ausfuhren** — am Bau-Stand gezählt.

| Modul | Z | Ausfuhren |
|---|---|---|
| `resources/planner/hausplaner/geometry/editierGeometrie.ts` | 75 | `Punkt` (7) · `Achse` (12) · `versetzePunkt()` (15) · `versetzteWand()` (20) · `spiegelePunkt()` (34) · `spiegelteWand()` (46) · `Bbox` (55) · `bbox()` (63) · `achsenMitte()` (73) |

## Die Verbraucher — je Ausfuhr, über Importzeilen erhoben

```text
app/sammelBefehle.ts:23         versetzteWand · spiegelteWand · bbox · achsenMitte
                                 (die Befehlslisten fuer duplizieren und spiegeln, A-31)
app/rahmen/Buehne.tsx:40        versetzteWand        (Ziehen auf der Buehne)
app/dashboard/einpassen.ts:21   bbox                 (Ansicht einpassen)
app/dashboard/Kopfrahmen.tsx:30 type Achse           (nur der Typ)
app/HausplanerApp.tsx:112       type Achse           (nur der Typ)
```

## `bbox` und `achsenMitte` sind NACHBARN, nicht Kern

**Sie liegen in derselben Datei, gehören aber zwei Aufgaben:**

```text
achsenMitte   -> die Spiegelachse                    gehoert zu W-14
bbox          -> die Spiegelachse UND das Einpassen  einpassen.ts:21 (W-12)
```

> ***Und der alte Anschluss-Nachtrag „brauchen BEIDE (W-13 und W-14)" ist am 13.08. berichtigt
> worden:*** *W-13s Auswahl-Module importieren `editierGeometrie` **null Mal**.* **Die Nachbarschaft
> ist W-12, nicht W-13.**

## Der Weg vom Knopf zum Befehl

```text
SPIEGELN     Kopfrahmen.tsx:315/:316
             -> HausplanerApp.tsx:695  spiegeleGrundriss(achse)
             -> app/sammelBefehle.ts   befehleSpiegeln(WAENDE, achse)
             -> executeCommands([...MOVE_NODE])      EIN Undo-Schritt (A-31)

DUPLIZIEREN  toolRegistry.ts:273
             -> befehleDuplizieren(...)  nutzt versetzteWand (500/500)
             -> executeCommands([...ADD_NODE])       EIN Undo-Schritt (A-31)

LOESCHEN     toolRegistry.ts:249
             -> befehleLoeschen(...)     -> executeCommands([...REMOVE_NODE])

VERSCHIEBEN  Buehne.tsx:40  versetzteWand beim Ziehen -> MOVE_NODE
```

> **`spiegeln` und `verschieben` haben KEINEN Registry-Eintrag** — *gemessen: `'spiegeln'` 0
> Treffer, `'verschieben'` 0 Treffer in `toolRegistry.ts`.*

## `drehen` gibt es nicht

```text
toolRegistry.ts        'drehen'  ->  0 Treffer
editierGeometrie.ts    keine Trigonometrie, keine Matrix
domain/scene.types.ts:193-196   transform.rotation — am ObjectNode, NICHT an der Wand
commands/applyCommand.ts:203    MOVE_NODE fuer undefinierte Typen -> CommandAbgelehnt
```

*Der Grund ist das Schema, nicht die fehlende Rechnung* — siehe `7-GRENZEN`.

## Kein eigener Befehl

**W-14 kommt mit `MOVE_NODE`, `ADD_NODE` und `REMOVE_NODE` aus.** *Was es zusätzlich brauchte und
seit A-31 hat, ist die **Klammer**: `executeCommands`, damit eine Operation über N Knoten **ein**
Undo-Schritt bleibt.*
