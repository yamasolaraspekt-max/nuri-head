# A-42-12 · der Anker hat den Fehler umgedreht, nicht behoben — der Generator verliert die Hälfte seiner Bälle

> **Release-Prüfer, 16.08. ~23:0x.** Auf `c2596c40`. Der Planner hat um 22:52 einen fehlenden
> Zeilenanfang behoben; die Behebung schlägt in die Gegenrichtung durch. **Selbst nachgemessen, je
> Rolle einzeln, alle abweichenden Zeilen geöffnet.**

## Was behoben wurde, und warum das richtig war

Der Ortungsbefehl aus A-42-12 zählte ohne Anker jede Zeile mit, in der die Zeichenfolge irgendwo
vorkam — auch in Prosa. Der Planner hat das selbst nachgemessen und `^…$` ergänzt. **Der Fund war
richtig und die Richtung auch.**

## Was der neue Befehl jetzt tut

```bash
grep -cE '^ballbesitz: <rolle>$' docs/STATUS.md docs/BEFUNDNOTIZEN.md
```

Das `$` verlangt, dass die Zeile **mit dem Rollennamen endet**. Sie tut es oft nicht:

```
je Rolle, A-42-12 gegen eine Zaehlung die Zusaetze zulaesst:

  ROLLE            A-42-12    tatsaechlich   verfehlt
  planner          80         81             1
  plan-pruefer     38         39             1
  generator         5         10             5     <- die Haelfte
  evaluator         0          0             0
  release-pruefer   5          5             0
  integrator        2          2             0
```

**Die sieben verfehlten Zeilen, einzeln geöffnet** — jede ist eine echte Ballzeile:

```
1539:  ballbesitz: generator  # er kann umziehen; die Insel-Gates laufen im Rollenbaum
2319:  ballbesitz: generator  # nur Befund 1 ist fremde Arbeit: eine Zeile in der Zuordnungstabelle
2387:  ballbesitz: generator  # Nachbesserung an scripts/rollen-tor.sh; A-37 ist sein Auftrag
5578:  ballbesitz: generator (die Zahlen sind seine), nachrichtlich planner (A-07 zitiert sie)
6130:  ballbesitz: generator (unveraendert - A-08 bleibt BEREIT)
1468:  ballbesitz: planner  # oder Yama, wenn er die Prozessfrage an sich zieht
18694: ballbesitz: plan-pruefer  # 16.08. vom Planner zurueckgegeben: die Restpunkte der 1. DoR-Runde
```

**Kein Grenzfall darunter.** Fünf tragen einen YAML-Kommentar, zwei einen Klammerzusatz — beides
gängige Form in dieser Datei, keine Nachlässigkeit.

## Warum die neue Richtung die gefährlichere ist

Der Planner hat den alten Fehler selbst so eingeordnet:

> *„Fünf von sechs Rollen bekämen eine falsche Lage — und zwar eine zu große, also die Sorte, die man
> nicht bemerkt, weil man ohnehin mit Rückstand rechnet."*

**Der neue Fehler liefert eine zu kleine Zahl.** Eine zu große Zahl lässt jemanden suchen und nichts
finden; **eine zu kleine sagt ihm, er sei fertig.** Der Generator sähe 5 statt 10 offener Bälle und
hätte keinen Anlass nachzusehen. Das ist dieselbe Klasse wie A-30: die Statuswahrheit sagt nicht das
Falsche, sie sagt zu wenig — und Schweigen wird als Erledigung gelesen.

## Gegenprobe an einem unabhängigen Werkzeug

`scripts/yama-posten.py` geht nicht über eine Zeilenform, sondern über die YAML-Zäune, nimmt den
Ballwert und vergleicht dessen **erstes Wort**. Es liefert je Rolle die Zahl der rechten Spalte oben —
für `release-pruefer`, `evaluator` und `integrator` deckungsgleich mit A-42-12, bei den drei anderen
höher. **Das ist kein Zufall der Methode: Zusätze hinter dem Namen sind genau das, was eine
Wortprüfung überlebt und ein Zeilenende-Anker nicht.**

## Der Befehl, der beides hält

```bash
grep -cE '^ballbesitz: "?<rolle>"?([ #(]|$)' docs/STATUS.md docs/BEFUNDNOTIZEN.md
#                     ^        ^  Anker vorn und am Wortende, nicht am Zeilenende
#                                 -> Prosa faellt raus, Kommentare und Zusaetze bleiben drin
```

Wörtlich gefahren, nicht aus der vorigen Messung geschlossen — **das ist ein Unterschied, den ich
vor dem Melden bemerkt habe:** meine Vergleichszählung oben benutzte `( |$)`, der Vorschlag
`([ #(]|$)`. Beide *sollten* dasselbe liefern; behauptet wäre das geraten. Also gemessen:

```
  ROLLE            A-42-12  VORSCHLAG  yama-posten.py  gleich
  planner          80       81         81              ja
  plan-pruefer     38       39         39              ja
  generator         5       10         10              ja
  evaluator         0        0          0              ja
  release-pruefer   5        5          5              ja
  integrator        2        2          2              ja

Gegenprobe an der Prosa: ohne Anker 21 -> Vorschlag 5   (16 Falschtreffer entfernt)
```

**Sechs von sechs deckungsgleich mit dem unabhängigen Werkzeug, und die Prosa fällt weg.** Der
Vorschlag hält damit beide Seiten: er zählt keine Erwähnung mit und verliert keinen Kommentar.

**Die Anführungszeichen habe ich mit aufgenommen, obwohl heute keine Ballzeile welche trägt**
(`^ballbesitz: "[a-z-]+"$` → 0 Treffer): es kostet nichts, und die Datei führt sie an anderen Feldern.

## Rollengrenze

**Ich baue das nicht ein.** A-42 gehört dem Planner, und die Frage, welcher Zählweg gilt, ist Teil
seines Kriteriums. Gemessen ist hier alles, was er zum Entscheiden braucht — die Zahlen je Rolle, die
sieben verfehlten Zeilen im Wortlaut und eine Form, die beide Fehlerrichtungen vermeidet.

**Ein Hinweis zur Reihenfolge:** dieser Befund trifft nur A-42-12. **A-42-11** (Summengleichung je
Rolle) hält davon unberührt, und der Plan-Prüfer hat sie gefahren. Wer A-42-12 nachbessert, muss
A-42-11 nicht anfassen.
