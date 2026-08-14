# W-18 · Topologie prüfen — FUNKTION

## Die ACHT Ausfuhren von `geometry/kontur.ts` — am Bau-Stand gezählt

**175 Zeilen, acht Ausfuhren.**

| Fundstelle | Ausfuhr | was es ist |
|---|---|---|
| `:41` | `KonturPunkt` | `{ x, y }` |
| `:47` | `KonturGrund` | `'zu-wenig-punkte' \| 'selbstschnitt' \| 'keine-flaeche'` |
| `:49` | `KonturUrteil` | `{ ok, grund }` |
| `:55` | `KONTUR_MIN_PUNKTE` | `3` |
| `:61` | `KONTUR_MELDUNG` | die drei Sätze für den Anwender |
| `:109` | `schneidetSichSelbst()` | **F-013** |
| `:135` | `pruefeKontur()` | das Urteil |
| `:156` | `konturStatusText()` | der Text zum Urteil |

**Ein einziger Import** (`:39`): `signierteFlaeche` aus `roomDetection`. *Sonst nichts — das Modul
steht für sich.*

## `schneidetSichSelbst()` — F-013 in gebauter Form

```text
:111  n < 4  ->  false          „Ein Dreieck kann sich nicht selbst schneiden."
:114  jede Kante i gegen jede Kante j > i
:118  benachbart = j === i+1  ODER  (i === 0 && j === n-1)   ->  uebersprungen
:122  streckenSchneiden(...)  ->  true
```

> ***Die zweite Bedingung in `:118` ist der ganze Trick*** — *`(i === 0 && j === n-1)` fängt, dass
> die **letzte Kante Nachbar der ersten** ist. Der Doc-Kommentar sagt es selbst: „die Kontur ist
> geschlossen, auch wenn man das beim Zählen leicht vergisst."*
>
> **Ohne diese Zeile meldete jede geschlossene Kontur einen Selbstschnitt an ihrer Schlussecke** —
> also genau die richtigen Konturen wären ungültig.

**Benachbarte Kanten teilen einen Eckpunkt; das ist kein Schnitt, sondern die Ecke.** *Deshalb
werden sie ausgenommen und nicht toleriert.*

## `pruefeKontur()` — und die Reihenfolge ist eine Aussage

**Wörtlich aus `:132-133`:**

> *„Die Reihenfolge ist nicht beliebig: **zu wenige Punkte zuerst**, weil man über zwei Punkte weder
> Schnitt noch Fläche sinnvoll aussagen kann."*

```text
1  weniger als KONTUR_MIN_PUNKTE (3)   ->  'zu-wenig-punkte'
2  schneidetSichSelbst                 ->  'selbstschnitt'
3  keine Flaeche (signierteFlaeche)    ->  'keine-flaeche'
sonst                                   ->  ok
```

> **Wer die Reihenfolge dreht, bekommt bei zwei Punkten die Meldung „umschließt keine Fläche"** —
> richtig und nutzlos. *Der Anwender soll den nächsten Handgriff lesen, nicht die formal
> zutreffendste Aussage.*

## Der Anschluss

```text
app/HausplanerApp.tsx:30   // Z-05: die Konturpruefung ist reine Geometrie und wohnt dort,
                           //       nicht hier.
                     :31   import { pruefeKontur, konturStatusText, KONTUR_MIN_PUNKTE,
                                     type KonturGrund } from '../geometry/kontur'
```

**Vier Symbole, eine Importzeile** — *der Kommentar darüber begründet die Schichtung, statt sie nur
zu vollziehen.*

## Ausgabe

| Was | Typ | Wohin |
|---|---|---|
| `KonturUrteil` | `{ ok, grund }` | die Zeichenlogik: darf geschlossen werden? |
| `konturStatusText(...)` | `string` | die Statuszeile beim Zeichnen |

## Kommando (für Rückgängig)

**Keines.** *Die Prüfung entscheidet, ob ein Befehl überhaupt entsteht — sie ist ihm vorgelagert und
hinterlässt selbst keinen Zustand.*

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne):** *nein.*
- **Rechnet in Schicht 2 (Geometrie):** **F-013**; F-004 nur mittelbar, siehe `3-FORMELN`.
- **Lebt in Schicht 3 (Anwendung):** angebunden über `HausplanerApp.tsx:31`.
- **Zeigt sich in Schicht 4/5:** als Statustext beim Zeichnen.
