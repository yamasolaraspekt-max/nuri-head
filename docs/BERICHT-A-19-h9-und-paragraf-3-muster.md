# Baubericht A-19 — H-9, und die §3-Schranke misst jetzt die Spalte statt die Zeile

```yaml
auftrag: "A-19"
rolle: "generator"
blatt: docs/auftraege/aktiv/A-19-h9-und-die-paragraf-3-musterberichtigung.md
basis_sha: c89e9096
gebaut_auf: 27665a19
gebaut_am: "12.08.2026"
zustand: CODE_FERTIG
ballbesitz: evaluator
```

> **Der Auftrag ist sein eigener erster Anwendungsfall** — und er hat mich beim Bauen ein weiteres
> Mal erwischt. *Die Stelle steht unten, nicht versteckt.*

## A-19-1 · H-9 steht in §18a, hinter H-8

```text
Rot vorher:  grep -c 'misst die Schreibweise' docs/ARBEITSREGELN.md   ->  0
Jetzt:                                                                 ->  1
H-Regeln: H-1…H-8 unverändert vorhanden (8), H-9 angehängt (1)
```

**Regel und Prüfform, beide im Wortlaut des Blattes:**

> **„Ein Muster, das eine Schreibweise voraussetzt, misst die Schreibweise und nicht die Sache."**
> **„Findet der Befehl die Zeile, die ich mit eigenen Augen gelesen habe? Erst danach zählen."**

*Dazu die neun Fälle von vier Rollen, die ausdrückliche Entscheidung **gegen** eine achte Barriere
(B5/B6/B7 stehen schon in derselben Datei; dreimal wurde gemeldet, dass eine falsch anschlagende
Warnung weggeklickt wird — **eine Barriere gegen falsche Muster wäre selbst ein Muster**), und der
Satz, was die Regel nicht kann.*

## A-19-2 · Die Abgrenzung zu H-6 steht IM Regeltext

*Als Tabelle, nicht als Nebensatz — ohne sie wird H-9 als Dublette von H-6 gelesen und weggewunken:*

| | Die Frage | Der Fehler |
|---|---|---|
| **H-6** | „Triffst du, was du meinst?" | **Fehltreffer** — trifft, was es nicht meint |
| **H-9** | „Setzt du an, wo die Sache steht?" | **richtiger Treffer, falsche Sache** |

**Ein Muster kann H-6 bestehen und an H-9 scheitern.** *Der §3-Fall ist genau das.*

## A-19-3 · Die Musterberichtigung, dreifach gegengeprobt — am lebenden Fall

```text
alt   ^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT
neu   ^\| \*\*[A-Z]+-?[0-9]+[^|]*\| *\*{0,2}`?IN_ARBEIT
```

| | Probe | Ergebnis |
|---|---|---|
| **(a)** | die Fehlalarm-Zeilen — `B7` (Z.41) und `B5N` (Z.42), **beide `BETRIEBSBESTAETIGT`** | **nicht gezählt** |
| **(b)** | eine **echte** `IN_ARBEIT`-Tafelzeile — `A-19` selbst (Z.45) | **gezählt: 1** |
| **(c)** | das Zustandsfeld-Muster (`STATUS.md:5849`) | **1 — gleiche Zahl wie (b)** |

*Zwei Angaben sind mir während des Baus unter den Händen gewandert und stehen hier in der zuletzt
gemessenen Fassung: `B5N` ist von `ABGENOMMEN` auf `BETRIEBSBESTAETIGT` gerückt, und die
Zustandsfeld-Zeile von `5806` auf `5849`.* **Beide vor dem Melden nachgemessen, nicht fortgeschrieben.**

*Zum Vergleich: das alte Muster zählte in derselben Datei **3**.*

> **(b) ist die Probe, die zählt.** *Ohne sie wäre die Berichtigung eine Abschaltung: ein Muster, das
> nie zählt, meldet immer „frei" — und das ist die gefährliche Richtung.* **Der Nachweis ist hier
> nicht konstruiert, sondern der laufende Auftrag selbst.**

**Und die Rot-Lage war beim Bauen größer als im Blatt:** *es nennt **einen** Fehlalarm, unmittelbar
vor der ersten Änderung gemessen waren es **drei** — `B7`, `B5N` und `A-19` selbst.* **Das Muster
wuchs mit jedem Auftrag, der über `IN_ARBEIT` berichtet — und das sind gerade die sorgfältigen.**

## A-19-4 · Der Doppelort bleibt

**Beide Befehle stehen weiterhin nebeneinander im Regelwerk, beide Zahlen werden weiter genannt.**
*Wer bei dieser Gelegenheit auf einen Ort verkürzt hätte, hätte die Kontrolle herausgenommen, die
Fall 4 überhaupt sichtbar gemacht hat — der Widerspruch der zwei Zahlen war der Fund.*

## A-19-5 · `must_preserve`

```text
git diff --numstat -- docs/ARBEITSREGELN.md   ->   49   1
```

**Die eine gelöschte Zeile ist die Musterzeile selbst** — genau die, die das Kriterium ausnimmt:

```diff
-Tafelzeile      grep -cE '^\| \*\*[A-Z]+-?[0-9]+.*IN_ARBEIT' docs/STATUS.md
```

| | Ergebnis |
|---|---|
| **H-1 … H-8** | **zeichengleich** (Unterschied nur an meiner Schnittgrenze, siehe unten) |
| **§3-Regel selbst** („höchstens einen Auftrag im Zustand `IN_ARBEIT`") | **unverändert** — berichtigt ist die Prüf**methode**, nicht die Regel |
| **`scripts/**`** | **0 Dateien** — dieser Auftrag fasst das Tor nicht an |
| **`resources/**` · `app/**`** | **0 Dateien** |

*Der H-1…H-8-Vergleich zeigt vier Unterschiedszeilen; alle vier sind `---` und die Überschrift
`## 18b`, die meine alte Schnittgrenze mitnahm. **Kein Regeltext.***

## A-19-6 · Die Blätter mit dem alten Muster — gemessen, NICHT geändert

**Und hier hat mich der eigene Auftrag erwischt.** *Meine erste Suche lief mit escapten
Zeichenklassen und meldete **0 Dateien** — bei einem Blatt, das ausdrücklich von vieren spricht.*
**Der Fehler war mein Suchmuster, nicht das Ergebnis: ich hatte eine Schreibweise vorausgesetzt.**
*Wörtlich gesucht (`grep -rlF`), ohne jede Annahme über Escapes:*

```text
docs/auftraege/aktiv/W-01N-suitezahl-zahlfrei.md:177      Zitat der verankerten Methode
docs/auftraege/aktiv/B5N-belegzeilen-schreibweisen.md:127 Kriteriumstext (B5N-6)
                                                    :147 Konfliktprüfung §3
docs/auftraege/aktiv/A-19-…md:89, 92, 98                  der Auftrag ÜBER das Muster
```

> **Das Blatt sagt „vier Auftragsblätter", gemessen sind es DREI** — *und eines davon ist A-19
> selbst, das den Ausdruck als seinen Gegenstand zitiert.* **Als Prüftext tragen ihn also zwei:
> W-01N und B5N.** *Beide Zahlen stehen hier; die Entscheidung gehört dem Planner und folgt nach
> diesem Auftrag.*

**Sechs weitere Dateien nennen den Ausdruck außerhalb der Auftragsblätter** — `ENTSCHEIDUNG-…`,
`MELDUNG-ERFUNDENE-SPERRE-A-12`, `BERICHT-B5N`, `BERICHT-B6`, mein `BEFUND-PARAGRAF-3…` und
`STATUS.md`. **Nichts davon angefasst.**

## A-19-7 · §3 mit der berichtigten Fassung

*Unmittelbar vor der ersten Änderung, mit der alten Fassung — und danach mit der neuen, wie das
Blatt es verlangt:*

```text
vor dem Bau    Tafelzeile alt 3  ·  Tafelzeile Spalte 2  0  ·  Zustandsfeld 0
beim Bau       Tafelzeile NEU  1  ·  Zustandsfeld        1     (beide A-19)
```

**Der Auftrag belegt seine eigene Regel: zwei Orte, zwei Zahlen, deckungsgleich.**

## Was diese Runde über die Regel selbst gezeigt hat

*Ich habe H-9 gebaut und bin beim Bauen zweimal hineingelaufen:* **einmal in A-19-6** (escapte
Zeichenklasse statt wörtlicher Suche, `0` statt `9` Treffer) — *und in der Runde davor beim
Nachmessen des B7-P2, wo mein Sucher dieselbe Prosa traf, die er messen sollte.*

> **Das ist kein Argument gegen die Regel, sondern für sie.** *Beide Male hat nicht das Muster
> gewarnt, sondern die Prüfform: **„Findet der Befehl die Zeile, die ich mit eigenen Augen gelesen
> habe?"** — bei `0 Dateien` gegen ein Blatt, das vier nennt, war die Antwort sichtbar nein.*

## Rückweg und Rückfallpunkt

```text
git apply --check -R   ->  Exit 0, Arbeitsbaum unangetastet
fork/auto/hausplaner-integration            a71471e6
backup-private/auto/hausplaner-integration  a71471e6
```

**Anders als den ganzen Tag über ist der Rückfallpunkt heute aktuell:** *der Stau ist aufgelöst,
und meine sieben vorherigen Commits liegen nachweislich außerhalb der Maschine
(`merge-base --is-ancestor` je Exit 0).* **Dieser Bau noch nicht.**

## Berührte Dateien

```text
docs/ARBEITSREGELN.md                          +49 / -1   H-9 in §18a · Musterzeile in §3
docs/BERICHT-A-19-h9-und-paragraf-3-muster.md  dieser Bericht
docs/STATUS.md                                 Zustand an beiden Orten
```
