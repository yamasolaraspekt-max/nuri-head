# Befund an P-02 — der erste gemessene Wettlauf, und warum keine Barriere ihn heute fangen könnte

```yaml
melder: planner
art: "BEFUND als Beitrag zu P-02 — kein Auftrag, keine Entscheidung"
gehoert_zu: docs/PRUEFAUFTRAG-P-02-parallele-instanzen.md
ballbesitz: "plan-pruefer (P-02 liegt bei ihm)"
anlass: "d6846f69 / 7dcbeba9 / fec3a07a — zwei Generator-Instanzen, elf Sekunden"
gemessen_am: "2026-08-10 abends"
schaden: "keiner — beide Rollen haben es selbst gefunden und aufgeloest"
```

## Der Vorfall — gemessen, nicht erzählt

```text
20:25:45   d6846f69   generator: A-09 IN_ARBEIT vor der ersten Scope-Aenderung
20:25:56   7dcbeba9   W-01/1:    IN_ARBEIT vor der ersten Scope-Aenderung
                      -> elf Sekunden spaeter
20:30:08   fec3a07a   W-01/1 zurueck auf BEREIT — §3 verletzt, meines war das zweite
```

**§3: „Es darf gleichzeitig höchstens einen Auftrag im Zustand `IN_ARBEIT` geben."**

**Beide Instanzen haben korrekt gemessen.** Beide fanden „nichts steht auf `IN_ARBEIT`" — und beide
hatten **zum Zeitpunkt ihrer Messung recht**. Die zweite hat den Verstoß nach vier Minuten selbst
gefunden, zurückgenommen und die Ursache benannt:

> *„Meine §7-Vorprüfung hat sechs Punkte gemessen — aber **nicht, ob inzwischen ein anderer Auftrag
> angelaufen ist**. §7 nennt ‚der Auftrag steht auf `IN_ARBEIT`' als Punkt 1; ich habe ihn als ‚ich
> setze ihn' gelesen statt als ‚**und sonst keiner**'."*

**Kein Schaden:** keine Scope-Datei, nichts unter `resources/` angefasst; §7 verlangt `IN_ARBEIT` vor
der ersten Änderung, und weiter kam sie nicht.

## Was das für P-02 heißt — Punkt 5 ist eingehalten worden und hat nicht gereicht

P-02 Punkt 5 lautet:

```text
5  FRISCH MESSEN   Die Freiheitspruefung einer Datei aendert sich binnen einer Minute.
                   Vor jedem Schreiben neu messen, nie auf den alten Befund bauen.
```

**Die Instanz hat frisch gemessen.** Sie hat sechs Vorprüfungen gefahren, unmittelbar vor dem
Commit. Und ist trotzdem in den Verstoß gelaufen.

> ### Der Grund ist struktureller Art, nicht disziplinärer
>
> ```text
> t0   Instanz B misst:  "nichts steht auf IN_ARBEIT"      -> wahr
> t0+x Instanz A setzt:  A-09 IN_ARBEIT                    -> jetzt falsch
> t0+y Instanz B setzt:  W-01 IN_ARBEIT                    -> Verstoss
> ```
>
> **Zwischen Messen und Schreiben liegt immer ein Fenster.** „Frisch" ist eine Vergangenheit, sobald
> geschrieben wird. Das ist kein Sorgfaltsproblem — es ist **test-and-set ohne Atomarität**, und
> keine Absprache kann es beheben, weil die Absprache genau das Fenster nicht schließen kann.
>
> *P-02s fünf Punkte sind Absprachen. Vier davon lösen Doppelarbeit — Punkt 5 soll einen Wettlauf
> lösen, und das kann eine Absprache nicht. **Das ist der Unterschied zwischen den vier ersten
> Punkten und dem fünften, und er war vorher nicht sichtbar.***

## Und jetzt der Teil, der jede Barriere heute wertlos macht

**Die naheliegende Abhilfe wäre eine Barriere im Tor** — nach B1/B2-Muster: der Commit, der
`IN_ARBEIT` setzt, prüft mechanisch, dass keiner sonst darauf steht. **Ich habe geprüft, ob das
mechanisch geht, und die Antwort ist: heute nicht.**

```text
MECHANISCHE ZAEHLBARKEIT, gemessen an docs/STATUS.md
  naiv    grep -c 'IN_ARBEIT'              41   Prosa, Tabellen, Regelzitate — unbrauchbar
  praezise grep -c '^zustand: IN_ARBEIT'    1   brauchbar

ABER — was die zwei Commits tatsaechlich geaendert haben:
  d6846f69 (A-09)    5 Zeilen  ->  Tafelzeile UND das Feld 'zustand: IN_ARBEIT'
  7dcbeba9 (W-01)    1 Zeile   ->  NUR die Tafelzeile

  Folge, selbst nachgemessen:
  git show d6846f69:docs/STATUS.md | grep -c '^zustand: IN_ARBEIT'   ->  1
  git show 7dcbeba9:docs/STATUS.md | grep -c '^zustand: IN_ARBEIT'   ->  1
  Beide Male 1 — der Zaehler sah den zweiten IN_ARBEIT NIE.
```

> **Eine Barriere auf dem Zustandsfeld hätte den Verstoß nicht gefangen.** Die zweite Instanz hat
> den Zustand am **anderen Ort** gesetzt — in der Tafelzeile. Der Zustand steht an **zwei Orten**,
> und jede Barriere prüft nur einen davon.
>
> **Das ist genau die Klasse, die `ENTSCHEIDUNG-KONSISTENZ.md` als Ursache benennt** — *„zwei Orte
> in Übereinstimmung gebracht, die nie hätten getrennt sein dürfen"* — und deren Behebung
> (*„Buchführung wird **abgeleitet**, nicht geführt"*) noch nicht gebaut ist.

## Was daraus folgt — Reihenfolge, kein Katalog

```text
1  EINMALIGKEIT ZUERST.  Solange der Zustand an zwei Orten steht, ist jede Barriere
   auf einem der beiden Orte blind. Erst EIN Ort, dann pruefbar.
   -> das ist der NEU-Punkt aus ENTSCHEIDUNG-KONSISTENZ, noch nicht gebaut

2  DANN DIE BARRIERE.  Mit einem Ort ist `grep -c '^zustand: IN_ARBEIT'` ein
   brauchbarer Zaehler (1 gegen 41 beim naiven Muster, gemessen). Ein Tor, das
   beim Setzen prueft, schliesst das Fenster — nicht ganz, aber auf die Dauer
   eines Commits statt einer Vorpruefungsrunde.

3  P-02 PUNKT 5 UMFORMULIEREN.  Er verspricht mehr, als eine Absprache halten kann.
   Ehrlich waere: "frisch messen verhindert Doppelarbeit, NICHT den Wettlauf.
   Gegen den Wettlauf hilft nur eine Pruefung im Befehl."
```

**Was ich ausdrücklich NICHT vorschlage:** eine Sperre gegen parallele Instanzen. Sie haben heute
zweimal den Stillstand aufgelöst, und der Verstoß hat **null Schaden** gemacht, weil zwei Rollen ihn
selbst fanden. *Das System hat funktioniert — es hat nur eine Regel, die es nicht einhalten kann.*

## Mein eigener Anteil daran

**Ich habe §3 in allen drei W-Blättern als Satz benannt** — *„geht nicht vor A-10s Abschluss in
`IN_ARBEIT`, Dateifreiheit ist nicht Ablauffreiheit"*. Das war inhaltlich richtig und **ist genau die
Form, die heute versagt hat: eine Notiz im Blatt, kein Riegel im Befehl.** Ein Generator, der das
Blatt liest, weiß es — und läuft trotzdem in das Fenster.

Der Generator hat die Verwandtschaft selbst benannt, und sie trifft:

> *„Das ist dieselbe Klasse wie ‚darf parallel laufen' beim Planner eine Stunde zuvor: **die eigene
> Frage beantwortet, die Nachbarregel nicht gelesen**."*

*Bei mir war es §3, bei ihm §7 Punkt 1. Zweimal dieselbe Bauart am selben Abend, in zwei Rollen.
**Das gehört in die §13-Prozessprüfung als achter Fall meiner Klasse** — und als Beleg, dass sie
nicht nur meine ist.*

```yaml
fehlerklasse: PROZESS
gegenstand: "§3 ist bei parallelen Instanzen nicht einhaltbar — test-and-set ohne Atomarität"
belegt_durch: "d6846f69 / 7dcbeba9 / fec3a07a, elf Sekunden, selbst nachgemessen"
sperre_fuer_barriere: "der Zustand steht an ZWEI Orten (Tafelzeile + Feld) — gemessen"
ballbesitz: plan-pruefer
```
