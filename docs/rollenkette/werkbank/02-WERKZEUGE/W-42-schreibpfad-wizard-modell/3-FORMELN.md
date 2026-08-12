# W-42 · Schreibpfad Wizard → Gebäudemodell — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **keine** | — | — |

**Ein Schreibpfad rechnet nicht — er setzt.** *Was W-42 tut, ist einen Knoten bauen und ihn als
Kommando übergeben.*

## Die Zahlen, die dabei trotzdem auftauchen

```ts
:181   transform: { position: { x: 2000, y: 500, z: 0 }, rotation: { … }, scale: { … } }
```

**Das sind FESTE STARTWERTE, keine Rechnung** — *`2000` und `500` in Millimetern.* **Das Bauteil
landet an einer festen Stelle und ist danach „im Plan verschiebbar", wie die Meldung sagt.**

> **Warum das keine Formel ist und trotzdem hierher gehört:** *ein fester Startwert ist eine
> Entscheidung, keine Ableitung.* **Wer erwartet, dass der Heizkörper an einer sinnvollen Wand
> erscheint, findet hier die Antwort: er tut es nicht, er erscheint an `x=2000, y=500`.** *Ob das
> genügt, ist eine Frage an die Bedienung und keine an die Mathematik.*

## Fehlt eine Formel?

**Nein** — *und die naheliegende Versuchung ist, eine Platzierungsregel zu erwarten.* **Es gibt
keine, und das ist eine Grenze und kein Mangel:** *`7-GRENZEN.md` benennt sie.*

## Genauigkeit

- **Alle Werte sind Millimeter, ganzzahlig.** *Keine Rundung, keine Toleranz.*
- **Die einzige abgeleitete Größe ist das Geschoss** (`:175`): *aktives Geschoss, sonst das erste,
  sonst `null` — zwei Rückfallstufen, kein Rechenweg.*
