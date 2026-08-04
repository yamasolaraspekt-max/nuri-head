# A-02 — Ein Lock ist ein Rest, wenn ihn NIEMAND HÄLT

```yaml
auftrag: A-02
titel: "Commit-Tor: Halter fragen statt Ruhe raten - und bei Blockade ENV_BLOCKED melden statt raeumen"
zustand: ENTWURF
ballbesitz: plan-pruefer
basis_sha: 93a9691f
pruef_sha: ""
release_sha: ""
letztes_votum: ""
naechster_schritt: "Plan-Pruefer prueft die Definition of Ready nach ARBEITSREGELN §5"
```

## Anlass — ein Vorfall mit Selbstanzeige, nicht eine Idee

**04.08., 22:45 und 22:47.** Zwei vollstaendige Git-Indizes wurden beiseitegeschoben
(`next-index-28.lock` 887 796 B · `next-index-30.lock` 888 008 B, *„Git index, version 2,
6997 entries"*). Minuten spaeter fehlten **44 Dateien** im Arbeitsbaum, darunter die
BETRIEBSORDNUNG, das gesamte Regelwerk und der Validator.

**Der Verursacher hat sich selbst angezeigt** und den zeitlichen Zusammenhang belegt: die zwei
Dateien tragen exakt die Zeitstempel seiner beiden Commits. Er raeumte **vor jedem Commit
pauschal alle Sperrdateien**, ohne Alters-, Ruhe- oder Halterpruefung.

> **Die Kausalitaet bis zu den 44 Dateien ist NICHT belegt und wird hier nicht behauptet.**
> Belegt ist: es wurde ueber 888 kB fremden Zustand entschieden, ohne die eine Frage zu stellen,
> die eine Antwort haette.

**Warum das ein Auftrag ist und keine Ermahnung:** das Verhalten ist heute nur durch Selbst\-
disziplin abgestellt. §13 verlangt bei der zweiten Wiederholung derselben Fehlerklasse eine
Ursachenpruefung — und es ist mindestens die dritte: Evaluator raeumte von Hand (03.08.),
Vorplanner raeumte pauschal (04.08.), das Tor selbst raeumt zu weich.

## Die eigentliche Ursache — sie liegt nicht dort, wo ich zuerst gesucht habe

*Mein erster Schnitt (`w10-lock-halter-statt-vermutung.md`, aufgehobenes Schema) zielte auf das
Tor. Die Selbstanzeige zeigt: geraeumt wurde **daneben**, von Hand.*

> **Wer am Werkzeug vorbei raeumt, tut es, weil das Werkzeug ihn blockiert.**
> Ein Tor, das ohne Not sperrt und keinen Ausweg kennt, erzieht zum Umgehen — und der Umweg
> ist gefaehrlicher als der Fall, gegen den das Tor gebaut wurde.

**Deshalb zwei Haelften, die zusammengehoeren:** seltener zu Unrecht sperren **und** einen
legitimen Ausweg anbieten, der nicht Raeumen heisst.

## Ist-Zustand, an Basis 93a9691f gemessen

```text
commit-pruefen.sh:114   if { GROESSE -eq 0 && ALTER -ge 60; } || [ STILL -eq 1 ]; then
                        -> das || macht Stillstand HINREICHEND: Groesse egal, 888 kB egal
commit-pruefen.sh:103   Kommentar, woertlich:
                        "Ein laufender Vorgang schreibt. Wer 120s nicht schreibt, laeuft nicht mehr."
                        -> genau diese Annahme hat der Vorfall widerlegt
lsof im Tor             1 Treffer - AUSSCHLIESSLICH im Kommentar (Zeile 102). Das Tor fragt NICHT.
ENV_BLOCKED im Tor      0 Treffer - §3 kennt den Zustand, das Werkzeug nicht
heute beiseitegelegt    4 Dateien in .git/_locks_beiseite/2026-08-04/
```

**Und die Trennschaerfe der Auskunft, selbst gefahren (03.08., Wegwerf-Repo):**

```text
Lock von lebendem Prozess gehalten   lsof -> 1 Halter    mtime stillstehend: JA
verwaister Lock                      lsof -> 0 Halter    mtime stillstehend: JA
-> die Ruhe trennt die Faelle NICHT. lsof trennt sie exakt.
```

## Ziel und Nutzen

Ein Lock wird beiseitegelegt, **wenn ihn niemand haelt** — und wenn das nicht feststellbar ist,
bleibt er liegen und das Tor **meldet `ENV_BLOCKED`**, statt zu raten oder den Aufrufer ins
Handaufraeumen zu treiben.

## Nicht-Ziele

- **Keine Aenderung an Stufe 4/5** (Ort, Zeitpunkt, ausgelagerter Index). A-02 aendert nur, **was
  als Rest gilt** und **was bei Unklarheit passiert**.
- **Kein Loeschen von Locks.** Beiseitelegen bleibt beiseitelegen.
- **Keine Regel gegen Handaufraeumen im Text.** *Ein Verbot ist kein Riegel; A-02 macht das
  Umgehen unnoetig, statt es zu verbieten.*

## Scope

```text
scripts/commit-pruefen.sh                  Halter-Abfrage · entschaerfter Rueckfall · ENV_BLOCKED
scripts/__tests__/commitPruefen.test.mjs   die Zusagen
```

## Akzeptanzkriterien

**Jedes P1 ist an Basis 93a9691f wirksam rot** — der Plan-Pruefer bestaetigt das vor dem Bau.

**A-02-1 (P1):** Ein Lock **mit Inhalt**, alt und still, **ohne Halter** -> beiseite, Commit
gelingt. *Das ist der Fall des Evaluators (317 s, 885 kB, dreifach als tot belegt) — er muss
weiter funktionieren, sonst ist A-02 eine Ruecknahme statt einer Verbesserung.*

**A-02-2 (P1, der Vorfall):** Ein Lock **mit Halter** -> bleibt liegen, **egal wie alt, egal wie
still, egal wie gross**. Gegenprobe im selben Test: derselbe Lock nach Prozessende -> beiseite.
*Ohne die zweite Haelfte waere „raeumt nie auf" auch gruen.*

**A-02-3 (P1):** Ohne `lsof` faellt das Tor auf die **konservative** Regel zurueck
(`0 Byte UND >= 60 s`) und raeumt **weniger**, nie mehr. *Ein Werkzeug, das ohne sein Messgeraet
mehr aufraeumt als mit, ist die gefaehrlichste Bauart ueberhaupt.*

**A-02-4 (P1, der Ausweg):** Bleibt ein Lock liegen, meldet das Tor `ENV_BLOCKED` samt Grund und
Halter-Auskunft und endet mit einem Exitcode, der sich von einem fachlichen Fehlschlag
unterscheidet. *Der Aufrufer soll melden koennen, ohne zu raten — genau das hat der Vorplanner
sich vorgenommen, und er braucht dafuer ein Werkzeug, das die Klasse ausspricht.*

**A-02-5 (P1, Mutationsprobe):** Mindestens sechs Mutationen fallen, darunter: lsof entfernt ·
lsof-Ergebnis ignoriert · `||` wieder eingesetzt · harte Grenze fuer Inhalt entfernt · Rueckfall
raeumt mehr statt weniger · ENV_BLOCKED durch normalen Abbruch ersetzt.

## Pruefbefehle

```text
A-02-1/-2/-3/-4   node --test scripts/__tests__/commitPruefen.test.mjs
                  (reines .mjs, KEIN TypeScript-Loader noetig - im Unterschied zur Insel)
A-02-5            Verfahren: je Mutation die Suite fahren, Datei md5-identisch wiederherstellen,
                  Ergebnis als Tabelle im Bericht
Gesamttor         node --test scripts/__tests__/*.mjs      Basis misst der Bauende vor dem Zug
```

## Kantenliste

```text
1  lsof nicht installiert                    -> A-02-3, konservativer Rueckfall
2  lsof antwortet langsam (Netzlaufwerk)     -> Zeitgrenze; laeuft sie ab, gilt "Halter unbekannt"
                                                = LIEGT + ENV_BLOCKED. OHNE ZUSAGE, mit Grund:
                                                eine kuenstliche Verzoegerung waere ein eigenes
                                                Messgeraet. Am Code zu belegen.
3  Halter ist ein fremder Prozess            -> A-02-2, der Schutzfall
4  Halter ist der eigene Lauf                -> kommt nicht vor: Stufe 4 laeuft VOR dem ersten
                                                git-Aufruf dieses Laufs
5  Lock verschwindet zwischen zwei Proben    -> `[ -e "$lock" ] || continue` steht bereits
6  zwei Laeufe raeumen gleichzeitig          -> mv ist atomar; der Zweite faellt auf continue
7  Lock liegt tief (refs/heads/<zweig>.lock) -> Suche ist rekursiv, unveraendert
```

## Auswirkungen (§5)

```text
API · Server · Schema · Migration · Bestandsdaten · Bundle    KEINE
Das Werkzeug beruehrt ausschliesslich .git/*.lock des eigenen Arbeitsbaums.
Browserabnahme    NICHT ANWENDBAR - keine sichtbare Aenderung am Produkt.
```

## Rueckweg

Ein Commit ohne Datenmigration; `git revert` stellt die alte Regel her. **Beiseitegelegte Locks
werden nie geloescht, nur verschoben** — ein Rueckbau verliert nichts.

## Offene Punkte fuer den Plan-Pruefer

1. **Ist A-02-1 an der Basis wirklich rot?** Der Fall des Evaluators funktioniert heute — moeglicher\-
   weise ist das Kriterium eine `must_preserve`-Kontrolle wie A-01-2 und gehoert von der
   Rot-Pflicht ausgenommen. *Ich vermute es, habe es aber nicht gemessen und schreibe es deshalb
   nicht als Tatsache.*
2. **Exitcode fuer `ENV_BLOCKED`**: ein eigener Wert (z. B. 3) oder Text auf stderr? Beides ist
   messbar; die Wahl beruehrt jeden Aufrufer und gehoert vor den Bau.
