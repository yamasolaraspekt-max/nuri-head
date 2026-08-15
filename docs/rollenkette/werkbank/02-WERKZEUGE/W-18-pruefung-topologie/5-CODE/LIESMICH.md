# W-18 · Topologie prüfen — CODE

**EIN Modul, 175 Zeilen, ACHT Ausfuhren** — am Bau-Stand gezählt.

| Modul | Z | Ausfuhren |
|---|---|---|
| `resources/planner/hausplaner/geometry/kontur.ts` | 175 | `KonturPunkt` (41) · `KonturGrund` (47) · `KonturUrteil` (49) · `KONTUR_MIN_PUNKTE` (55) · `KONTUR_MELDUNG` (61) · `schneidetSichSelbst()` (109) · `pruefeKontur()` (135) · `konturStatusText()` (156) |

**Ein einziger Import** (`:39`): `signierteFlaeche` aus `roomDetection`. *Das Modul steht sonst für
sich — es kennt weder Wände noch Befehle noch die Bühne.*

## Der Anschluss

```text
app/HausplanerApp.tsx:30   // Z-05: die Konturpruefung ist reine Geometrie und wohnt dort,
                           //       nicht hier.
                     :31   import { pruefeKontur, konturStatusText, KONTUR_MIN_PUNKTE,
                                     type KonturGrund } from '../geometry/kontur'
```

**Vier Symbole in einer Zeile, mit einer Begründung darüber.** *Der Kommentar beantwortet die Frage,
die ein Leser sonst stellt: warum liegt die Prüfung nicht bei der Zeichenlogik.*

## Die Namensgleichheit, die man kennen muss

```text
app/tools/toolRegistry.ts:230   id: 'kontur'          das WERKZEUG zum ZEICHNEN
geometry/kontur.ts              das MODUL zum PRUEFEN
```

> ***Zwei Dinge, ein Wort.*** *Gemessen: ein `import` auf `geometry/kontur` liefert **eine**
> Testdatei, das **Wort** „kontur" liefert **zwölf** — elf treffen die Werkzeug-ID.* **Wer die
> Verriegelung über das Wort zählt, schreibt „zwölf Wächter" und hat einen.**

## F-004 liegt woanders — und seit A-32 an zwei Stellen

```text
wallGeometry.ts:62 · :106       F-004 als GEHRUNGSDETAIL (Bandkanten-Schnittpunkt)
geradenGeometrie.ts:84          F-004 in REINER Form (geradenSchnitt, seit A-32)
```

**Beide sind für W-18 ohne Belang:** *`kontur.ts` importiert keines von beiden und rechnet seinen
Streckenschnitt selbst.* **`geradenGeometrie` hat überhaupt keinen Produktivverbraucher** — nur
seinen eigenen Test. Siehe `3-FORMELN`.

## Kein eigener Befehl

**Die Prüfung ist einem Befehl VORGELAGERT** — sie entscheidet, ob überhaupt einer entsteht, und
hinterlässt selbst keinen Zustand und keinen Historien-Eintrag.
