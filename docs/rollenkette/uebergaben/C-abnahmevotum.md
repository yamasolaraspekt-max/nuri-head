# C · ABNAHMEVOTUM

> **Von:** Evaluator · **An:** Release-Prüfer

| Feld | Wert |
|---|---|
| Auftrag | A-nn |
| **Vorgänger** | Baubericht B, Bau-SHA `<sha>` |
| **Prüf-SHA** | `<sha>` — hier wurde geprüft |
| Getrennte Umgebung | ja/nein — **Generator und Evaluator nie dieselbe Testdatenbank** |

## Befund je Kriterium

> **Regel: jeder Befund braucht seinen eigenen Beleg.** Ohne Beleg keine Abnahme —
> auch nicht bei einer Reparatur.

| Nr | Votum | Beleg (Ausgabe/Datei:Zeile) |
|---|---|---|

## Mutationsprobe — eigene, nicht die des Generators

| Mutation | erkannt? |
|---|---|

## Grenzen geprüft

| Fall aus A | Absage erscheint? | erreicht sie die Oberfläche? |
|---|---|---|

> Die zweite Spalte ist die entscheidende. Eine geworfene Absage, die der
> Renderer schluckt, ist keine Absage — das war der A-01-Fehler.

## Gesamtvotum

**ABGENOMMEN** · NACHBESSERN · SPEC_BLOCKED

| bei NACHBESSERN/SPEC_BLOCKED | |
|---|---|
| Fehlerklasse | CODE · SPEC · UMGEBUNG · BEWEIS · REGRESSION |
| Ball geht an | Generator (CODE) · Planner (SPEC) |
| **Ein Befund = ein Votum.** Gemischte Befunde werden geteilt, nicht gebündelt. | |
