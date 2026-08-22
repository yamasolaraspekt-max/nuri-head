# VOTUM — A-43 Posten 3 (A-43-13, Dirigent-Bereich)

**evaluator · 22.08.2026 · Auftrag `NACHPRUEFUNG-evaluator-A-43-posten3` gen 12 · Lease-Token 1**
**Basis `8a08d625` · Endstand `08e4901a` · Blatt `3aa7c730`**

## Ergebnis: ABGENOMMEN

A-43-13 verlangt: die Positivliste kennt zusätzlich `docs/backlog/` und `docs/fortschritt/`;
`scripts/`, `.githooks/`, `docs/STATUS.md`, `docs/BEFUNDNOTIZEN.md` und Produktcode bleiben
abgewiesen. *„Der Bereich wächst um zwei Verzeichnisse, er wird nicht zum Schlüssel."*

## Die vier verlangten Proben — im Wegwerf-Repo unter `$TMPDIR`

Wie A-37-22d und die Ortsangabe des Blatts es verlangen; `TICKET_ROLLE=dirigent` habe ich in
**keinem** realen Checkout gesetzt, auch nicht für die Positivprobe.

```
docs/backlog/probe-a4313.md        exit=0   durchgelassen
docs/fortschritt/probe-a4313.md    exit=0   durchgelassen
docs/STATUS.md                     exit=1   VERSTOSS 'dirigent' aendert docs/STATUS.md ausserhalb …
scripts/probe-a4313.sh             exit=1   VERSTOSS Rolle 'dirigent' schreibt ausserhalb ihres Bereichs
```

**Die drei weiteren im Kriterium genannten Pfade habe ich mitgeprüft**, weil das Kriterium sie
nennt und ein Nachweis über zwei von fünf keiner wäre:

```
.githooks/probe-a4313              exit=1
docs/BEFUNDNOTIZEN.md              exit=1
app/Http/ProbeA4313.php            exit=1   (Produktcode)
resources/planner/probe-a4313.ts   exit=1   (Produktcode)
```

**Fünf von fünf abgewiesen, zwei von zwei durchgelassen.**

## Die Meldung ist nicht nur erweitert — sie liest die Liste

Der Bau hätte die Liste im Meldungstext einfach nachziehen können. Er tut mehr: die Meldung
**liest die Positivliste aus der `case`-Anweisung selbst** und rät nicht, wenn das misslingt
(`else`-Zweig: *„sie liess sich von dort nicht lesen und wird hier NICHT geraten"*). Gemessen an
der Wirkung, nicht am Code:

```
ROLLEN-TOR  VERSTOSS  Rolle 'dirigent' schreibt ausserhalb ihres Bereichs.
            Erlaubt sind nur: docs/konzept/ docs/regelwerk/ docs/auftraege/ docs/backlog/ docs/fortschritt/
            abgewiesen: docs/sonstwo.md
```

Damit kann die Meldung nicht mehr von der Liste abdriften — genau die Fehlerklasse „zwei
Wahrheiten", gegen die A-43-11 geschrieben ist, hier ohne eigenes Kriterium mit erledigt.

## Gegenprobe: die übrigen zwölf unberührt

| Probe | Ergebnis |
|---|---|
| `git diff --stat 8a08d625..08e4901a -- scripts/ .githooks/` | **nur** `scripts/rollen-tor.sh`, 16+/2− |
| `scripts/status-erzeugen.sh` | **0 Zeilen** — das Kennungsmuster ist nicht angefasst |
| A-43-1 am neuen Stand | **8/8** erkannt, unverändert |
| A-43-8 Stichprobe (`nachpruefen`, `release_pruefen`, `steuern`) | je **0** |
| A-43-9 (`warten_dann_x`) | **7 „keine Arbeitsanweisung"** |
| A-43-10 (`quatsch`) | **7 „unbekannte aktion"** |
| Produktcode `-- resources/ app/` | **leer** |

Die Nicht-Kommentar-Änderung in `rollen-tor.sh` besteht aus genau einer erweiterten `case`-Zeile
und dem Ersatz der fest verdrahteten Meldung durch das Auslesen. Nichts davon berührt die
Aktionsliste oder das Kennungsmuster.

## Zwei Anmerkungen zur Kette

**Das Tor-Wort ist zurückgenommen.** Meine Rollenquelle trägt heute `aktion: nachpruefen` statt
`bauen`. Damit schließt sich die Kette, die mit meinem Befund von 11:2x begann — vier von sechs
Rollen fielen in „unbekannte aktion" —, über A-43-8 bis zu diesem Auftrag. **Der Hinweg war
deshalb nicht Formsache:** vor dem Merge kannte mein Tor `nachpruefen` null Mal und hätte diesen
Votum-Commit mit **7** abgewiesen. Nach dem Fast-forward `cf49113b..51eb1d0e`: ein Treffer.

**Abgrenzung zu meiner Abnahme von 13:56.** A-43-13 war dort ausdrücklich nicht Teil der Lieferung.
Ich habe es damals weder geprüft noch als Lücke gewertet — und prüfe es jetzt als eigene Nachlieferung,
so wie A-37-20 nach dem NACHBESSERN.

## Ball

**Dirigent** — A-43 ist damit vollständig (12 + Posten 3). Als Nächstes erwarte ich gen 13
(Abnahmerückstand Posten 2, Z2-W0-7).
