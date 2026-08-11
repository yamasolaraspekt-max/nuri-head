# W-22 · Gaube — GRENZEN

## Die Ampel: drei Stufen, und AK4 ist absichtlich nicht kritisch

Alles hängt an einer Zeile (`resources/planner/hausplaner/geometry/gaubeGeometrie.ts:491`):

```text
rot    wenn ein KRITISCHES Kriterium verletzt ist
gelb   wenn alles Kritische ok ist, aber irgendein Kriterium verletzt ODER feasible === false
gruen  sonst
```

**Kritisch sind AK1, AK2, AK3, AK5** — der Filter nennt sie einzeln. **AK4 fehlt in dieser Aufzählung.**

| Kriterium | Zeile | kritisch? | was es prüft |
|---|---|---|---|
| **AK1** kein Vertex über First | 450 | **ja** | `maxY <= yRidge - tol` |
| **AK2** Anschlusskante auf Hauptdach | 457 | **ja** | der Anschluss sitzt |
| **AK3** keine Rückwand über Dach | 465 | **ja** | `sHi <= REF_OFF + tolAnschluss` |
| **AK4** Front lotrecht (Pultgaube) | 474 | **NEIN** | Δlx und Δlz ≈ 0, Höhe positiv |
| **AK5** Kaminsockel spaltfrei | 483 | **ja** | `sUp <= -tol` — der Sockel steckt **unter** dem Dach |

**Eine schiefe Front macht gelb, kein Rot.** *Das ist eine Entscheidung: sie ist ein Schönheitsfehler,
kein Anschlussfehler — und der Unterschied steht im Code, nicht in einer Meinung.*

**Bei einem Kamin wird AK1 mit `ok: true` gesetzt** (Z.485) — er wird nicht geprüft, sondern als
erfüllt eingetragen, mit `ist: 'lotrecht'`. *Wer AK1 als Messung liest, liest hier eine Setzung.*

## Die Höhe wird GEKLEMMT, nicht abgelehnt

```text
Machbarkeit    d*tan(a) > h            sonst ist der Anschluss unmoeglich
Entwaesserung  h <= d*(tan a - tan(minNeigung))    "sonst h klemmen"
```

*Wörtlich aus dem Dateikopf, `resources/planner/hausplaner/geometry/gaubeGeometrie.ts:25-26`.*

**Der Anwender gibt eine Höhe ein und bekommt möglicherweise eine andere.** *Ob er das erfährt, ist in
`6-PRUEFUNG` als offene Frage notiert — ich habe es nicht gemessen.*

## Untere Schranken

`MIN_PULT_GRAD = 5` — *„Schleppgaube: Mindest-Entwässerungsneigung"*.
`MIN_FLACH_GRAD = 2` — *„Flachdachgaube: noch flacher zulässig"*.
**Zwei Zahlen im Code, kein Parameter.**

## Was das Werkzeug ausdrücklich nicht kann

> *„KEINE Dacheindeckung, KEINE Statik, KEINE Schneelast — nur Lage/Höhe/Anschluss-Geometrie.
> Realer Tragwerksplaner/Dachdecker bleibt nötig."* (Z.28-29)

| Fall | Folge |
|---|---|
| echte X-Verschneidung mit Kehlen | nicht Gegenstand — `dachVerschneidung.ts` |
| Auswechslung am Gaubenrand | **`auswechslung.ts`, 174 Z — in keinem Blatt zuhause** |
| Eindeckung, Statik, Schneelast | ausdrücklich außerhalb |
| ein Werkzeug zum Anfassen | **existiert nicht** — kein Registry-Eintrag |
