# CLAIM — Abnahme Z1-W1-1 bis Z1-W1-5, Station besetzt

**evaluator · 21.08. 20:08 · VOR dem Prüfstand-Aufbau**

Alle fünf stehen auf `CODE_FERTIG` mit Ball `evaluator`, **an beiden Orten einig** — Tafelzeile
und Datensatz je selbst gelesen, keine Drift. Gemessen, bevor ich etwas angefasst habe.

| Kennung | bau_sha | Blatt |
|---|---|---|
| Z1-W1-1 | `2bc0d2f2` | DIN-18065-Badge sagt, was es nicht geprüft hat |
| Z1-W1-2 | `60c04eef` | Walmdach: ungültige Kontur wird abgelehnt |
| Z1-W1-3 | `d7651d9c` | Eine Formel, eine Stelle: polygonM2-Kopie |
| Z1-W1-4 | `b2371d7e` | dachWerte: eine Quelle, Stilllegung statt Löschung |
| Z1-W1-5 | `9dde4d15` | insulationType: der tote Zweig sagt, dass er tot ist |

Gemeinsame Basis `11f7c4c3`. **Wer als zweite Evaluator-Instanz hier ankommt: die Station ist
besetzt** (Grund für diese Form: `STATUS.md:3209`).

## Die Vorbedingung ist jetzt erfüllt — und das war sie vorher nicht

Ich hatte den Ball für Z1-W1-2 und Z1-W1-4 **begründet nicht angenommen**
([`BALL-Z1-W1-2-NICHT-ANGENOMMEN.md`](BALL-Z1-W1-2-NICHT-ANGENOMMEN.md),
[`BEFUND-Z1-W1-4-BALLDRIFT.md`](BEFUND-Z1-W1-4-BALLDRIFT.md)): der Zustand stand auf `ENTWURF`,
später `BEREIT`, und **die Abnahme wird von `CODE_FERTIG` gerufen, nicht vom Ball.**

Beides ist eingetreten:
- Der **Generator** hat um 19:49 gemeldet (`928680d6`).
- Der **Integrator** hat es um 20:0x in beide Orte getragen und dabei **die Ball-Drift behoben**
  (`8124bdc0` — *„der Evaluator hat mir dieselbe Ball-Drift zum zweiten Mal nachgewiesen"*).

Mein Werkzeug meldet jetzt **Ball-Drift keine**. Damit ist der Befund aus `0d14bc0c`/`dbaa9a7a`
geschlossen, und die Station ist frei für die Sache selbst.

## Was jetzt kommt

Prüfstand nach §8/§9 am Fernstand, `node_modules`/`vendor` per `cp -al` (Inodes geteilt — nichts
überschreiben, Links lösen), **§15 mit Gegenprobe**, und je Auftrag: **den Bau suchen** statt
`bau_sha` zu glauben, Messtisch vollständig, jede Zahl selbst nachgezählt. Kriterium E verlangt bei
Z1-W1-2 eine Browserabnahme — Bühnen-Wächter zuerst.

**Das Votum geht ins Blatt bzw. hierher; den Zustand trägt der Integrator nach** — `docs/STATUS.md`
ist mir nach A-37-6 gesperrt.
