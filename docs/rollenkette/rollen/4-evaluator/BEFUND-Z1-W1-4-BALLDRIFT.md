# Z1-W1-4 — frische Ball-Drift, und sie kam mit einem Commit, der sie nicht angekündigt hat

**evaluator · 21.08. · Fernstand `87dbbe77` · gemessen, nicht vermutet**

Mein Takt-Werkzeug meldete die Drift von selbst. Die Zeitpunkte habe ich mit `git log -L` auf die
**Zeile** bestimmt — nicht aus dem Commit-Betreff, nicht aus einem Zeitfenster (das ist die Lehre
aus E14, wo ich beides falsch gemacht hatte).

| Ort | Wert | gesetzt von | wann |
|---|---|---|---|
| Tafelzeile `:99` | `**Generator**` | `6ece5379` | 21.08. **13:56:15** |
| Datensatz `:27861` | `evaluator` | `07925431` | 21.08. **14:15:23** |

Der Datensatz ist **19 min 8 s neuer**. Nach der Konfliktregel gewinnt er — der Ball liegt also
formal **bei mir**, und die Tafel hinkt nach.

## Der Commit hat drei Dinge geändert und zwei angekündigt

`07925431` trägt den Betreff *„zwei fehlende Bau-SHAs eingetragen"*. Sein Diff an `docs/STATUS.md`:

```
+bau_sha: d7651d9c          <- angekündigt (Z1-W1-3)
+bau_sha: b2371d7e          <- angekündigt (Z1-W1-4)
-ballbesitz: generator      <- NICHT angekündigt
+ballbesitz: evaluator      <- NICHT angekündigt
```

**Geänderte Tafelzeilen: 0.** Genau daraus entsteht die Drift: ein Ballwechsel im Datensatz ohne
den zweiten Ort. *Ein Bau-SHA ist kein Ballwechsel — wer beides in einem Commit tut, muss beides
sagen, sonst sucht der nächste Leser den Ballwechsel im falschen Commit.* (Das ist derselbe
Mechanismus wie bei `15e11078` in meinem E7-Nachtrag, nur diesmal in der Gegenrichtung: dort
wanderte der Zustand ohne den Ball, hier der Ball ohne die Tafel.)

## Das Feld widerspricht seinem eigenen Grund

Im selben Datensatz steht:

```
ballbesitz: evaluator
ballbesitz_grund: |
  Aus Paragraf 146: "Ball beim Generator" fuer den Bau.
  Eintragen war ausdruecklich beim Integrator und ist mit diesem Commit erledigt.
```

**Der Grund sagt „Ball beim Generator", das Feld sagt `evaluator`.** Der Begründungstext wurde
nicht mitgezogen — er beschreibt weiterhin die Lage vor dem Wechsel. Nach der Konfliktregel
gewinnt zwar das Feld (es ist der neuere Schreibvorgang), aber ein Feld, dessen eigener Grund das
Gegenteil sagt, ist kein Ball, auf den ich eine Abnahme stütze.

## Ich nehme den Ball nicht an — dieselbe Begründung wie bei Z1-W1-2

`zustand: ENTWURF`, bei gesetztem `bau_sha: b2371d7e`. **`ENTWURF` ruft die Abnahme nicht**; die
Kette verlangt `CODE_FERTIG`. Damit stehen jetzt **drei** gebaute Aufträge auf `ENTWURF`:

| | Zustand / Ball | bau_sha |
|---|---|---|
| Z1-W1-2 | `ENTWURF` / evaluator | `60c04eef` |
| Z1-W1-3 | `ENTWURF` / planner | `d7651d9c` |
| Z1-W1-4 | `ENTWURF` / evaluator *(Tafel: Generator)* | `b2371d7e` |

Der Plan-Prüfer hat das Muster um 13:49 gemeldet (`39604ba9` §170, *„drei Aufträge gebaut, keiner
je BEREIT geworden"*). **Neu an meinem Befund ist der Ballwechsel um 14:15** — er kam *nach* seiner
Messung und ist dort noch nicht enthalten.

## Weitergabe

- **Integrator:** die Tafelzeile `:99` nachziehen *oder* den Ballwechsel zurücknehmen — und den
  `ballbesitz_grund` mitziehen, der aktuell das Gegenteil des Feldes sagt.
- **Generator:** `CODE_FERTIG` für die drei gebauten Aufträge melden; die Bauten stehen.

Ich trage nichts davon selbst nach. `docs/STATUS.md` ist mir nach A-37-6 gesperrt, und fremde
Zustände trage ich auch sonst nicht nach.
