# W-03 · Wand bearbeiten — GRENZEN

## Die Grenze, die man beim Wort „nicht gebaut" falsch versteht

**Fünf Operationen fehlen — aber nur in EINER von vier Schichten.**

```text
                 Registry   Vertrag   Paket   Landkarte
trimmen             0          1        1       1 (fehlt)
verlaengern         0          1        1       1 (fehlt)
versatz             0          1        1       1 (fehlt)
teilen              0          1        1       1 (fehlt)
verbinden           0          1        1       1 (fehlt)
```

> ***Sie sind vertraglich beschrieben, im Werkzeugpaket aufgeführt und in der Landkarte mit
> Begründung vermerkt.*** **Was fehlt, ist der Registry-Eintrag** — die Schicht, die ein Werkzeug
> anklickbar macht.

**Und das ist kein Sonderfall dieser fünf:** `werkzeugVertrag.ts` führt **111**, `werkzeugPaket.ts`
**101**, `toolRegistry.ts` **12**. *Die Registry ist das Nadelöhr des ganzen Hauses.*

## ZWEI Fundamente, nicht eines — und daran hängt der Zuschnitt

| Operation | Fundament | Zustand |
|---|---|---|
| `trimmen` · `verlaengern` · `versatz` | `geometry/geradenGeometrie.ts` (**A-32**) | `BETRIEBSBESTAETIGT` |
| `teilen` · `verbinden` | `store/hausplanerStore.ts`, `executeCommands` (**A-31**) | `BETRIEBSBESTAETIGT` |

> ***A-32 löst eine RECHENfrage, A-31 eine AUSFÜHRUNGSfrage.*** *Sie überschneiden sich nicht.*
>
> **Wer W-03 als EINEN Bau schneidet, baut entweder drei Operationen ohne Klammer oder zwei ohne
> Rechnung** — und merkt es am Ende, wenn ein Undo die geteilte Wand halb zurücknimmt.

**Beide Zustände sind am Bau-Stand geprüft und nicht aus dem Auftrag übernommen** — *dort stand noch
„A-32 gebaut und noch nicht abgenommen".*

## Die Formeln der Registerzeile trugen nicht

**`F-003`, `F-004`, `F-030` standen dort; alle drei sind am Code widerlegt** (`lotAufWand` 0,
`geradenGeometrie` 0, `wandBaender` 0 im Panel). **Gebaut ist `F-001`.** *Berichtigt mit W-03/1;
siehe `3-FORMELN`.*

> ***F-004 ist seit A-32 gebaut — aber von W-03 nicht aufgerufen.*** *Sie gehört erst in diese
> Zeile, wenn `trimmen`, `verlaengern` und `versatz` gebaut sind.* **Heute wäre sie dort eine Zusage,
> die der Code nicht einlöst.**

## Was das Werkzeug auch im gebauten Teil nicht kann

| Grenze | Beleg |
|---|---|
| **keine Mehrfachbearbeitung** — die Felder hängen an `selectedWall`, einer Wand | `EigenschaftenPanel.tsx:108`, `:113` |
| **keine freie Wandstärke** über das Feld — nur die sechs der Liste | `:89`, `WANDSTAERKEN` |
| **die Länge ändert das ENDE**, nie den Anfang | `:113-121` |
| **kein Material ohne Katalog** | `MAUERWERK` |

> **Der Sonderfall ist trotzdem gelöst:** *eine Wand mit einer Stärke außerhalb der Liste behält sie
> und bekommt einen Eintrag „(aktuell)"* (`:331`). **Die Liste schreibt nichts still um** — das wäre
> die stille Änderung, die niemand bemerkt.

## Und die gebaute Hälfte ist NICHT verriegelt

**Kein Wächter hält Material, Stärke, Höhe oder Länge** — fünf Muster, null Treffer (siehe
`6-PRUEFUNG`).

> ***Das ist die Grenze, die am wenigsten aussieht wie eine.*** *Der Code ist da, er wirkt, und
> nichts hält ihn fest.* **Wer `setzeWandLaenge` anfasst, bekommt von keiner Zusage widersprochen —
> auch dann nicht, wenn er statt des Endes den Anfang bewegt.**
