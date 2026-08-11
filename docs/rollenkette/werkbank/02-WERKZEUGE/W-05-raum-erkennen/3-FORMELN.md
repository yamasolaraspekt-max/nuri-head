# W-05 · Raum erkennen — FORMELN

**Nur Nummern.** Die Formeln stehen in `../../01-MATHEMATIK/FORMELSAMMLUNG.md`.

## Das Register nennt vier — gemessen sieht es anders aus

| F-Nr | laut Register | im Code? | Fundstelle |
|---|---|---|---|
| **F-010** Orientierung (Schuhband) | ja | **JA** | `roomDetection.ts:70`, das Vorzeichen wird in **171** ausgewertet |
| **F-011** Fläche eines Polygons | ja | **JA, aber ABWEICHEND** | dieselbe Zeile 70 — **ohne Betrag**, siehe unten |
| **F-012** Punkt in Polygon | ja | **NEIN** | 0 Treffer |
| **F-013** Selbstschnitt-Prüfung | ja | **NEIN** | 0 Treffer — und das ist ein Befund, siehe unten |
| **F-001** Abstand zweier Punkte | **nicht genannt** | **JA** | `roomDetection.ts:88` (`Math.hypot`, Wandlänge) |

## Warum `signierteFlaeche()` weder F-010 noch F-011 ist, sondern beider Kern

**Eine Funktion, zwei Zwecke.** Sie bildet dieselbe Summe wie F-010 und halbiert sie — **ohne den
Betrag, den F-011 verlangt**. Damit trägt ein einziger Rückgabewert beide Auskünfte:

```text
Vorzeichen  ->  Orientierung   (F-010)   negativ = Aussenumlauf, wird verworfen (Z.171)
Betrag      ->  Flaeche        (F-011)   mm², daraus das Volumen
```

**Das ist Absicht und keine Nachlässigkeit.** *Wer hier den Betrag nähme, könnte den Außenumlauf
nicht mehr vom Raum unterscheiden — beide hätten dieselbe Fläche.*

**Die reine F-011 gibt es in der Insel trotzdem:** `resources/planner/hausplaner/geometry/polygonFlaeche.ts` rechnet
`Math.abs(summe) / 2` und liefert **m²**. Sie ist **Ausschluss** dieses Werkzeugs (`5-CODE`).

## Der Befund: F-011 verlangt F-013, und F-013 läuft hier nicht

Die Sammlung schreibt zu F-011:

> *„Selbstschneidendes Polygon liefert eine **falsche, aber plausible** Zahl — keine Fehlermeldung.
> **Deshalb vorher F-013 laufen lassen.**"*

**In `roomDetection.ts` gibt es keine Selbstschnitt-Prüfung** — `0 Treffer`. Ob das gefährlich ist,
hängt daran, ob der Halbkanten-Umlauf überhaupt selbstschneidende Polygone erzeugen kann; **das ist
hier nicht gemessen worden**. *Gemeldet, nicht bewertet.*
