# W-14 · Kopieren, Spiegeln, Drehen — FUNKTION

## Die NEUN Ausfuhren von `geometry/editierGeometrie.ts` — am Bau-Stand gezählt

**75 Zeilen, neun Ausfuhren.**

| Fundstelle | Ausfuhr | was es ist |
|---|---|---|
| `:7` | `Punkt` | `{ x, y }` |
| `:12` | `Achse` | `'vertikal' \| 'horizontal'` |
| `:15` | `versetzePunkt()` | ein Punkt um `(dx, dy)` |
| `:20` | `versetzteWand()` | **Translation** — s. u. |
| `:34` | `spiegelePunkt()` | ein Punkt an einer Achse |
| `:46` | `spiegelteWand()` | beide Endpunkte gespiegelt |
| `:55` | `Bbox` | `{ minX, minY, maxX, maxY }` |
| `:63` | `bbox()` | die Hülle einer Punktmenge, `null` bei leer |
| `:73` | `achsenMitte()` | die Spiegelachse aus der Hülle |

## `versetzteWand` ist eine TRANSLATION, kein Parallelversatz

**Der Rumpf, wörtlich** (`:19-27`):

```ts
/** Wand-Endpunkte um (dx,dy) versetzen (bewegen/duplizieren mit Versatz). */
export function versetzteWand(start, end, dx, dy) {
  return { start: versetzePunkt(start, dx, dy), end: versetzePunkt(end, dx, dy) };
}
```

> ***Beide Endpunkte um DENSELBEN Vektor.*** *Die Wand wandert; Richtung und Länge bleiben.*
>
> **Ein Parallelversatz ist etwas anderes:** *er legt eine neue Achse im **senkrechten** Abstand `d`
> daneben und braucht dafür die **Normale** — eine Größe, die hier nicht vorkommt.*

**Diese Verwechslung ist teuer gewesen:** *aus ihr entstand am 13.08. eine falsche
Fahrplan-Einordnung und eine Zusage an Yama, beide zurückgezogen (A-29).* **Das Wort trägt sie:
„versetzen" und „Versatz" sind dasselbe Wort und zwei Vorgänge.**

*Der Parallelversatz liegt seit A-32 in `geometry/geradenGeometrie.parallelVersatz` — nicht hier.*

## Die Spiegelung, und was sie NICHT erhält

```text
spiegelePunkt(p, achse, pos)     :34
  vertikal    -> x' = 2·pos − x,  y bleibt
  horizontal  -> y' = 2·pos − y,  x bleibt
  beide Werte gerundet (Math.round)

spiegelteWand(start, end, ...)   :46   beide Endpunkte durch spiegelePunkt
```

**Der Doc-Kommentar nennt die Nebenwirkung selbst** (`:41-43`):

> *„Beide Endpunkte werden gespiegelt; die Wand behält dadurch ihre Lage relativ zur Achse, **ihre
> Laufrichtung kehrt sich um** (unkritisch, da Länge/Dicke gleich bleiben)."*

> ***Und die Richtungsumkehr ist nicht nur harmlos — sie ist der Grund, warum Öffnungen richtig
> mitwandern.*** *Nachgerechnet statt behauptet:*
>
> ```text
> Wand (0,0)–(1000,0), Oeffnung bei offsetFromWallStart = 200
> Spiegelung vertikal an pos = 500
>   -> gespiegelte Wand: start (1000,0), end (0,0)     start bleibt start
>   -> Oeffnung 200 ab start liegt nun bei x = 800
>   -> wahres Spiegelbild von x = 200 an x = 500  =  800     stimmt ueberein
> ```
>
> **Weil `start` und `end` an ihrem Platz gespiegelt werden statt vertauscht zu werden, misst
> `offsetFromWallStart` weiter vom richtigen Ende** — die Öffnung landet genau dort, wo ihr
> Spiegelbild hingehört. *Wer die zwei Endpunkte beim Spiegeln vertauschte, bekäme dieselbe Wand und
> eine Öffnung auf der falschen Seite.*

## Der Bezug je Operation — und der Weg dorthin

```text
duplizieren   toolRegistry.ts:273    Registry-Werkzeug, wirkt auf die AUSWAHL
loeschen      toolRegistry.ts:249    Registry-Werkzeug, wirkt auf die AUSWAHL
spiegeln      Kopfrahmen.tsx:315/:316 -> HausplanerApp.tsx:695
                                     wirkt auf ALLE WAENDE, kein Registry-Eintrag
verschieben   kein Werkzeug          Ziehen auf der Buehne (Buehne.tsx:40 nutzt
                                     versetzteWand), Panel-Felder, Duplizieren mit Versatz
drehen        —                      nicht gebaut, siehe 7-GRENZEN
```

## Rückgängig — seit A-31 ein Schritt

**`spiegeleGrundriss` bildet EINE Befehlsliste** (`befehleSpiegeln`) **und übergibt sie an
`executeCommands`** — *ein `produceWithPatches`, ein Historien-Eintrag.*

> ***Vorher war es ein Undo-Schritt PRO WAND.*** *Ein Undo drehte eine Wand zurück und ließ den
> Grundriss halb gespiegelt stehen — einen Zustand, den es zeichnerisch nicht geben kann.* **Seit
> A-31 nimmt ein Undo die ganze Spiegelung zurück.**

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne):** **ja** — `MOVE_NODE` (spiegeln, verschieben), `ADD_NODE`
  (duplizieren), `REMOVE_NODE` (löschen).
- **Rechnet in Schicht 2 (Geometrie):** `editierGeometrie.ts` — **ohne F-Nummer**, siehe
  `3-FORMELN`.
- **Lebt in Schicht 3 (Anwendung):** `app/sammelBefehle.ts`, `app/HausplanerApp.tsx`.
- **Zeigt sich in Schicht 4/5:** Kopfrahmen-Knöpfe, Werkzeugleiste, Bühne.
