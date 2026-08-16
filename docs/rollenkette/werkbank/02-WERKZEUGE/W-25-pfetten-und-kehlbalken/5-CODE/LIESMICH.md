# W-25 · Pfetten und Kehlbalken — CODE

## Ein Modul, vier Ausfuhren, 82 Zeilen

| Modul | Z | Ausfuhren |
|---|---|---|
| `geometry/holzBauteile.ts` | 82 | `HolzStueckRef` (22) · `HolzBauteilMengen` (28) · `OFFENE_HOLZBAUTEILE` (45) · `holzBauteileAusListe()` (56) |

```text
:29  pfettenLaenge      Summe echter Pfettenlaengen (lfm) — First-/Fusspfetten
:30  pfettenAnzahl
     gratsparrenLaenge / -Anzahl
     kehlsparrenLaenge / -Anzahl
```

## Die Nachbarn, die dieselbe Familie bedienen

```text
geometry/schifterListe.ts     152 Z., 9 Ausfuhren   — benennt die Schifter (EA28)
geometry/auswechslung.ts      174 Z., 5 Ausfuhren   — rechnet die Wechselhoelzer (W-29)
geometry/dachformVorlagen.ts  ZimmererFlags :91-95  — dreizehn Flaggen je Dachform
app/tools/faehigkeiten.ts:85-87  drei Engines der Gruppe 'dach-zimmerei'
```

> ***Vier Module arbeiten an derselben Holzliste und kennen einander nur über Kommentare.***
> `schifterListe.ts:7` *zitiert `OFFENE_HOLZBAUTEILE` wörtlich,* `auswechslung` *wird von
> `sparrenTrennung.ts:3` genannt* — **aber keiner importiert den anderen.** *Die Zusammenhänge
> stehen im Text, nicht in Kanten.*

## Verbraucher

```text
holzBauteileAusListe   ausserhalb __tests__:   KEINER
OFFENE_HOLZBAUTEILE    ausserhalb __tests__:   KEINER
ZimmererFlags          ausserhalb dachformVorlagen.ts:  KEINER
```

> **Auch W-25 ist gerechnet und nicht angeschlossen** — *die dritte Ablesung dieser Runde mit
> demselben Ergebnis, nach W-17 (Snapshot-Naht) und W-29 (`ADD_ROOF_AUFBAU`).*
