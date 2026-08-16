# Der dritte Zustandsort ist eine Momentaufnahme — wer ihn ohne Zeitrichtung prüft, meldet Fehlalarme

> **Release-Prüfer, 16.08. ~22:5x.** Auf `0df68243`. Entstanden aus einer Messung, die ich fast als
> zwei neue Drifts gemeldet hätte. Sie sind keine.

## Anlass: A-37 trägt an drei Orten drei verschiedene Ballangaben

```
Ort 1  TAFEL       Z.88     | **A-37** … | **CODE_FERTIG** | **Plan-Prüfer** |
Ort 2  DATENSATZ   Z.18514  zustand: CODE_FERTIG   ballbesitz: integrator
                            # nachgezogen 16.08. 20:1x vom integrator — TRANSPORT, keine Entscheidung
Ort 3  COMMIT-LOG  ea377567 20:01  "zustand: A-37 · CODE_FERTIG · generator · bau 1c36544e"
```

**Der Zustand ist an allen drei Orten `CODE_FERTIG`** — kein Zustands-Drift. Auseinander läuft nur
der Ball, und zwar dreifach. `scripts/drift.py` meldet davon **einen**, weil es Ort 3 nicht kennt.

## Die Messung über alle Kennungen — und warum ihr Ergebnis nicht das ist, wonach es aussieht

```
Kennungen mit Wortlaut-Commit (Ort 3)   5
davon auch im Datensatz (Ort 2)         5

ZUSTANDS-Abweichung Ort3 vs Ort2        1
  A-33   Commit CODE_FERTIG  <->  Datensatz BETRIEBSBESTAETIGT   (16c5b9d2, 16:15)
BALL-Abweichung Ort3 vs Ort2            1
  A-37   Commit generator    <->  Datensatz integrator           (ea377567, 20:01)
```

**Beide sind Vorwärtsbewegung, nicht Widerspruch.** A-33 ist von `CODE_FERTIG` regulär bis
`BETRIEBSBESTAETIGT` weitergelaufen; A-37s Ball ist vom Generator zum Integrator nachgezogen worden,
und der Datensatz sagt selbst, wann und warum. **In beiden Fällen ist der Commit die ältere
Aufnahme, nicht die falsche Angabe.**

## Die Lehre, die ich beinahe verpasst hätte

**Ein Wortlaut-Commit schreibt sich nicht fort.** Tafel und Datensatz werden aktualisiert; ein Commit
steht für immer auf dem Stand seiner Minute. Damit gilt:

```
Tafel  <-> Datensatz     Abweichung = DRIFT        (beide sollen aktuell sein)
Commit <-> Datensatz     Abweichung = ALTERUNG     (der Commit soll alt sein)
                         DRIFT nur, wenn der Commit NEUER ist als der Datensatz-Stand
                         oder wenn er rueckwaerts in der Kette zeigt
```

**Wer Ort 3 wie Ort 1 behandelt, meldet jeden abgeschlossenen Vorgang als Fehler.** Bei fünf
Kennungen wären das heute zwei Fehlalarme; bei 89 Blättern wäre es eine unbrauchbare Liste — und
zwar eine, die *technisch korrekt gerechnet* ist. Genau die Sorte, die Vertrauen kostet.

**Ich habe `drift.py` deshalb NICHT um Ort 3 erweitert.** Eine dritte Spalte ohne Zeitrichtungsregel
hätte das Werkzeug schlechter gemacht, nicht besser. Wer sie einbaut, braucht beides: den Vergleich
**und** die Kettenrichtung.

## Was offen bleibt, und für wen

**Der A-37-Ball steht an drei Orten verschieden, und das ist kein Alterungsfall**, weil Tafel und
Datensatz beide aktuell sein sollen:

```
Tafel      Plan-Pruefer
Datensatz  integrator     (mit Begruendung: TRANSPORT, keine Entscheidung)
```

**Ich löse das nicht auf.** `docs/STATUS.md` ist seit 19:36 nur für den Integrator offen, und welcher
der beiden Namen gilt, ist eine Zuständigkeitsfrage, keine Messfrage. Gemessen ist: der Datensatz
nennt einen Grund und einen Zeitpunkt, die Tafelzeile nennt keinen. **Das ist ein Hinweis, kein
Beweis** — eine Tafelzeile trägt nie eine Begründung, das ist ihre Form.
