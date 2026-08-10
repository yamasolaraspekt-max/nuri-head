# W-01 · Raster und Fang — PRÜFUNG

## Welche Zusagen es gibt

| Datei | Was sie prüft |
|---|---|
| `fangKern.test.ts` | den Kern selbst: Arten, Rangfolge, Toleranz |
| `fussUndUeberlagerungen.test.ts` | Lotfuß und überlagerte Kandidaten |
| `fangAnschluss.test.ts` | den Anschluss an Wände |

**Zusammen 45 Zusagen** *(gemessen 10.08.2026, `grep -c "test("` über die drei Dateien).*

## Was eine Prüfung hier belegen muss

1. **Die Rangfolge**, nicht nur einzelne Arten — ein Fang, der bei zwei Kandidaten den falschen
   nimmt, ist schlimmer als keiner.
2. **Dass `art` stimmt**, nicht nur die Koordinate. *Ein richtiger Punkt mit falscher Art meldet dem
   Anwender etwas Unwahres.*
3. **Den Fall `keiner`** — dass gar nicht gefangen wird, wenn nichts in Toleranz liegt.

## Eine Grenze der Mutationsprobe, im Code festgehalten

Das Entfernen des Wächters in `lotAufGerade()` blieb in der Mutationsprobe **blind**: ohne ihn kommt
`{NaN, NaN}` zurück, `NaN ≤ toleranz` ist `false`, der Fang fällt durch — **von außen sieht alles
gleich aus**.

> **Eine Zusage kann nicht halten, was nach außen nicht sichtbar ist.**
> *Deshalb wird die Funktion direkt geprüft, obwohl sie ein Innenteil ist.*
