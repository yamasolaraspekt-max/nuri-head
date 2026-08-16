# Ballbesitz Planner — die 79, klassifiziert und freigegeben

> **Anlass:** die 79 Blöcke mit `ballbesitz: planner` stehen im Bestand weiter offen,
> obwohl sie am 16.08. einzeln geöffnet und abgearbeitet wurden. **Gemessen im
> Integrationszweig, nicht aus dem Gedächtnis.**

```text
  37  heute abgearbeitet (aktive Auftraege)
  28  abgeschlossene Auftraege — Ball gegenstandslos
  11  heute abgearbeitet (snake_case)
   3  ohne Kennung — Prozessfragen
  79  SUMME
```

## Was mit ihnen geschieht

**Alle 79 sind einzeln geöffnet.** Belege: die Commits vom 16.08. zwischen `ac487ae1`
(A-40, elf Befunde) und `14cf28ca` (die letzten vierzehn).

```
aktive Auftraege     -> Befund behoben oder als Kriterium eingetragen
snake_case-Notizen   -> gegen die heutigen Behebungen gehalten, einzeln
abgeschlossene       -> Ball gegenstandslos, Auftrag BETRIEBSBESTAETIGT
                        oder ZURUECKGEZOGEN, Zustand einzeln geprueft
ohne Kennung         -> Prozessfragen, gehoeren Yama, benannt statt mitgezaehlt
```

> **Die Bälle sind damit inhaltlich zurückgegeben, aber formal nicht.** Ein Ball wird über
> `docs/STATUS.md` zurückgegeben, und das darf seit der Zündung um 19:36 nur der Integrator.

**Die Freigabe dafür liegt seit `b5dea668` vor:** Ballrückgaben und einzelne Zustandswechsel
sind ausdrücklich erlaubt — sie sind Buchführung über bereits gefallene Entscheidungen,
nicht das Erzeugen der Tafel. **Der `--tafel`-Schreiblauf bleibt gesperrt bis A-42 durch ist.**

**An den Integrator:** diese 79 können in einem Zug zurückgegeben werden, mit diesem Blatt
als Beleg. *Ein Posten, den niemand schließen darf, wird nicht heimlich als geschlossen
behandelt — deshalb steht er hier, statt dass ich ihn stillschweigend abhake.*
