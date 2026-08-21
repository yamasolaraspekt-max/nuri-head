# Z1-W1-2 — Ball erhalten, Abnahme NICHT begonnen. Begründung und Messung.

**evaluator · 21.08. · Fernstand `2feccdca`**

Mit `6ece5379` (integrator, 21.08. 13:56) liegt `Z1-W1-2` auf meinem Ball. **Ich beginne die
Abnahme nicht** — und das ist keine Verweigerung, sondern die Kette.

## Die Messung

| Ort | Zustand | Ball |
|---|---|---|
| Tafelzeile `:97` | `ENTWURF` | **Evaluator** |
| Datensatz | `ENTWURF` | `evaluator` |

Beide Orte sind **einig** — keine Drift. Aber `ENTWURF` ist der Zustand *vor* `BEREIT`. Die Kette
lautet `ENTWURF → BEREIT → IN_ARBEIT → CODE_FERTIG → ABNAHME`. **Die Abnahme wird von
`CODE_FERTIG` gerufen, nicht vom Ball.** Ein Auftrag, den niemand als fertig gemeldet hat, hat
keine Erklärung des Generators, gegen die ich prüfen könnte.

**Und gebaut ist er trotzdem:** `bau_sha: 60c04eef` (21.08. 13:33, generator) — selbst geöffnet:
2 Dateien, 60 Zeilen, `dachGeometrie.ts` **und** `dachGeometrie.test.ts`. Der DoR ist laut
Datensatz erteilt (`plan-pruefer §144`). Es fehlt also **nur die Meldung**.

**Gegenprobe, ob die Meldung nur nicht angekommen ist:** alle Commits mit `Z1-W1-2` im Betreff
durchsucht — **0 Treffer** für eine Wortlaut-Marke `zustand: Z1-W1-2 · CODE_FERTIG`. Sie wurde nie
gesetzt.

**Und ob es vielleicht so gewollt ist** (die E15-Frage): nein — der Plan-Prüfer führt es als
Befund, zweimal am selben Tag: `c4fc07e0` §169 *„Z1-W1-2 ist gebaut … die Meldung fehlt"* und
`39604ba9` §170 *„drei Aufträge gebaut, keiner je BEREIT geworden"*.

## Wo der Ball wirklich hingehört

Der Integrator schreibt es selbst in `ballbesitz_grund`: *„Gesetzt ist der, den die Kette **nach
dem Bau** ruft … Ich habe transportiert, nicht entschieden."* Das ist sauber — er hat den Ball
vorausgesetzt, nicht den Zustand erfunden. Nur ist die Voraussetzung noch nicht eingetreten.

**Vor mir liegen zwei Schritte, beide nicht meine:**
1. Der **Generator** meldet `Z1-W1-2 · CODE_FERTIG` (der Bau steht bereits).
2. Der **Integrator** trägt den Zustand in beide Orte.

Danach nehme ich den Ball sofort an — der Prüfstand steht, und Kriterium E verlangt eine
Browserabnahme, für die ich die Bühne ohnehin brauche.

**Ich trage den Zustand nicht selbst nach.** Das wäre der fremde Zustand, den ich laut Takt nicht
nachtrage — und `docs/STATUS.md` ist mir nach A-37-6 ohnehin gesperrt.

## Eigener Aufbaufehler, gleich bemerkt

Mein Python-Regex zum Auslesen der Zusatzfelder warf `PatternError: global flags not at the start`
— ich hatte `(?m)` mitten in eine Alternative gesetzt. Die Felder habe ich stattdessen direkt
gelesen. Der Fehler hat keine Zahl dieser Messung berührt; er steht hier, weil ein Werkzeugfehler,
den man nicht nennt, beim nächsten Mal als Befund zurückkommt.
