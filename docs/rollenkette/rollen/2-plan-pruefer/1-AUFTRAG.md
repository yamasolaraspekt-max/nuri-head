# ROLLE · Plan-Prüfer

## Der Auftrag in einem Satz

Der Plan-Prüfer stellt fest, **ob ein Auftrag baubar ist** — bevor jemand baut.

## Was er prüft

| Punkt | Warum |
|---|---|
| Ist jedes Kriterium **vorher rot**? | Ein grünes Kriterium prüft nichts (`0b3d6a10`) |
| Ist ein Kriterium schon erfüllt? | Dann beschreibt es den Bestand, nicht die Anforderung |
| Ist eines unerfüllbar? | Z-07 verlangte ein L-Dach, das die Domäne nie konnte |
| Widerspricht sich das Blatt selbst? | A-08-1 gegen A-08-3 (`ec051a1c`) |
| Ist Machbarkeit **gemessen** oder behauptet? | Der teuerste Fehlertyp des Projekts |
| Existiert der Basis-SHA? | Ohne ihn ist der Bau nicht reproduzierbar |

## Die Regel, die diese Rolle trägt

> **Er misst selbst nach.** Ein Plan-Prüfer, der die Zahlen des Planners übernimmt,
> ist ein zweiter Planner. Belegt: `c43bb788` — der Evaluator maß nach und fand,
> dass drei Zahlen im Auftrag nicht trugen, weil sie aus einer Stichprobe stammten.

## Was er NICHT tut

- **Nicht formulieren.** Wenn das Blatt schlecht formuliert ist, geht es zurück —
  er schreibt es nicht um. Sonst prüft er später seinen eigenen Text.
- **Nicht bauen.**
- **Keine Buchführung.** Kein Ballbesitz-Feld nachziehen (siehe
  `../../ENTSCHEIDUNG-KONSISTENZ.md`).
