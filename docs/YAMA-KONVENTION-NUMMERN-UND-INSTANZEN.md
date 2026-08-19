# Zwei Konventionsfragen in Yamas Namen entschieden — und die zweite steht bereits in den Arbeitsregeln

> **Release-Prüfer, 19.08. ~20:3x.** Auf `d2baea84`. Übernommen auf Yamas Bitte, den Auftrag für
> ihn zu erledigen; Grundlage sind seine stehenden Anweisungen vom 12. und 13.08. **Alle Zahlen
> heute frisch gemessen**, die Erhebungsbefehle stehen dabei.

---

## Punkt 1 — **Eindeutigkeit ohne Ordnung genügt. Die Nummern werden NICHT nachgezogen.**

### Was eine §-Nummer im Bestand tatsächlich ist

```
grep -rhoE '§[0-9]+' docs/            ->  4728 Verweise
git log --all | grep -coE '§[0-9]+|Paragraf [0-9]+'  ->  1302 in Commit-Botschaften

allein auf die vier heute bewegten Abschnitte:
  §113  25 Verweise      §112  23      §114  17      §115  7      §116  6
```

**Eine Nummer ist kein Titel, sie ist ein Zeiger** — und zwar der meistbenutzte im ganzen
Bestand. Genau deshalb ist das Nachziehen nicht die harmlose Aufräumarbeit, nach der es aussieht.

### Der Beleg, der die Frage entscheidet: Commit-Botschaften sind unveränderlich

Die heutige Umnummerierung hat bereits eine Divergenz erzeugt. Selbst nachgemessen, Zeile für
Zeile gegen die Überschriften am HEAD:

```
COMMIT     Botschaft nennt        Abschnitt heisst heute   stimmt?
8ffda0fd   §112 (P-03)            §115                     NEIN
7f93f197   §113 (P-03)            §115                     NEIN
736481fe   §113 (Fehler 30)       §113                     ja
a7e5623b   §114 (P-02)            §116                     NEIN
3adac326   §114 (W-14/1)          §114                     ja
```

**Drei von fünf.** Und diese drei sind **nicht reparierbar**: eine Commit-Botschaft ändert man nur,
indem man die Historie begradigt — was hier verboten ist und aus gutem Grund.

> **Chronologisches Nachziehen würde diese Divergenz nicht beseitigen, sondern vervielfachen.**
> Jeder Abschnitt, der eine neue Nummer bekommt, macht seine eigene Commit-Botschaft falsch und
> lenkt zugleich alle bestehenden Verweise still auf einen anderen Text um. **Die Nummer bliebe
> gültig und zeigte auf etwas anderes — das ist die schlimmere Sorte kaputter Zeiger**, weil kein
> Werkzeug sie findet.

### Der zweite Grund, und er ist der, den beide vorher schon erkannt haben

Nachziehen verlangt, **fremde Abschnitte anzufassen** — genau das, was der Plan-Prüfer und ich
unabhängig voneinander vermieden haben, er mit *„ich benenne nur meine eigenen Abschnitte um"*, ich
mit *„ein Transporteur, der beim Durchreichen Nummern korrigiert, verändert den Inhalt, und
niemand sieht es später"*. **Eine Konvention, die beide Beteiligten zwingt, ihre eigene Regel zu
brechen, ist die falsche Konvention.**

### Was damit gilt

```
eindeutig    JA   — Pflicht. Zwei Abschnitte mit derselben Nummer sind ein Fehler
                    und werden aufgeloest, und zwar von dem, dem der Text gehoert.
aufsteigend  NEIN — keine Anforderung. Die Reihenfolge im Blatt ist chronologisch,
                    die Nummer ist eine Kennung, kein Rang.
```

**Die Lage im Blatt (112 · 115 · 113 · 116 · 114) bleibt so stehen.** Sie sieht unordentlich aus
und ist die einzige Fassung, in der jeder Verweis und jede Commit-Botschaft weiterhin auf das
zeigt, was sie gemeint hat.

**Preis, offen benannt:** wer das Blatt von oben liest, kann aus der Nummer nicht auf das Alter
schließen. Das kostet Bequemlichkeit. Der Ersatz steht daneben: die Reihenfolge im Blatt **ist**
chronologisch, und jeder Abschnitt trägt seinen Commit.

---

## Punkt 2 — **Die Regel existiert bereits. Sie wurde heute gebrochen, nicht vermisst.**

### Der Fund

`docs/ARBEITSREGELN.md:115-117`, geltender Text, unverändert seit Bestehen:

> *„Es darf gleichzeitig höchstens einen Auftrag im Zustand `IN_ARBEIT` geben. **Prüfungen eines
> festgeschriebenen Commits dürfen parallel laufen, wenn sie keinen gemeinsamen veränderlichen
> Zustand benutzen.**"*

**Der zweite Satz beantwortet die Frage vollständig, und niemand musste ihn erfinden.** Parallele
Prüfungen sind ausdrücklich erlaubt — **unter genau einer Bedingung**. Die heutigen drei
Kollisionen sind keine Lücke in den Regeln, sondern der Bruch dieser Bedingung:

```
gemeinsamer veraenderlicher Zustand?
  eine Befunddatei mit FORTLAUFENDER Nummerierung, in die beide Instanzen anhaengen
  -> ja. Der Zustand ist "die naechste freie Nummer", und er ist veraenderlich.
```

**Damit war die heutige Parallelität nach geltendem Recht unzulässig — nicht, weil zwei Instanzen
liefen, sondern weil sie in denselben veränderlichen Zustand geschrieben haben.** Das ist ein
wichtiger Unterschied: er trifft nicht die zweite Instanz als solche, sondern eine bestimmte
Schreibhandlung.

### Die Bilanz des Tages, beide Seiten gezählt

**Neun `plan-pruefer`-Commits heute. Nach Betreff eingeordnet, Kriterium genannt** (Reparatur =
Nummernkollision oder deren Fehlerverbuchung):

```
SACHE      8ad16cfa  §112 Alterung, vier Funde        <- loeste Yamas 64-Stunden-Posten aus
SACHE      8ffda0fd  §115 P-03 nachgemessen, 36 Blaetter
SACHE      736481fe  §113 Fehler 30 zurueckgenommen
SACHE      a7e5623b  §116 P-02 geprueft, R1 und R2
SACHE      3adac326  §114 W-14/1, 35 Zeiger, 8 gewandert
REPARATUR  7f93f197  Nummernkollision behoben
REPARATUR  4282bcd4  Nummernkollision aufgeloest
REPARATUR  245dc2b0  Nummernkollisionen aufgeloest
REPARATUR  0d16e41b  Fehler 31 verbucht (der doppelte §114)
                     -> 5 Sache · 4 Reparatur der Parallelitaet selbst
```

**Auf meiner Seite:**

```
elf Transporte heute (Reflog rolle/release-pruefer, 19.08.):
  Fast-forward  4    cba422dd 13:13 · 8ad16cfa 15:43 · f661ba23 16:49 · 3adac326 19:28
  Merge         7    = Divergenz beim Transport, also paralleles Schreiben
davon mit Textkonflikt an derselben Datei        2   (0c28ae2f, 7c0de2fb)
davon mit STILLEM Schaden ohne Konflikt          1   (e16bae67, von mir behoben in 49f4f761)
```

*Der erste Entwurf dieses Blattes schrieb hier **„alle sieben Transporte waren Merges, kein
einziger fast-forward"**. Das war falsch, und der Fehler ist lehrreich: ich hatte die
Transport-COMMITS gezählt — und ein Fast-forward hinterlässt keinen. **Vier Transporte waren damit
in meiner eigenen Zählung unsichtbar, weil das Zählverfahren sie nicht erzeugen konnte.**
Nachgezählt über das Reflog, das jeden Transport führt. Dieselbe Klasse wie „4 von 74": das
Messverfahren bestimmte die Grundmenge, nicht die Frage.*

**Und der vierte Realfall liegt nicht heute:** am 16.08. wurden in derselben Rolle zwei
entgegengesetzte DoR-Voten abgegeben, 22 Minuten und 28 Sekunden auseinander — und derselbe Befund
zweimal gemessen, zwei Stunden auseinander, *„weil die Meldung von 13:01 auf einem Zweig liegt,
den ich nicht lese"*. **Das ist der Kern: nicht Streit, nicht Überschreiben, sondern Doppelarbeit
durch fehlende Sicht.**

### Die Entscheidung

**Es bleibt bei der geltenden Regel — sie wird nicht geändert, sondern angewandt. Für eine
gemeinsam geführte, fortlaufend nummerierte Datei heißt das: je Rolle EINE schreibende Instanz.**

```
lesen, messen, rechnen           FREI und unbeschraenkt — kostet nichts, kollidiert nicht
schreiben in gemeinsamen,        NUR die erste Instanz. Wer feststellt, dass er der zweite
veraenderlichen Zustand          ist, tritt vom SCHREIBEN zurueck und uebergibt seine Messung
schreiben in eigene Dateien      unberuehrt zulaessig
```

**Der Rücktritt ist kein Novum, sondern Präzedenz mit Wortlaut.** `8a417fe0` (14.08. 22:33), eine
zweite Release-Prüfer-Instanz — **meine eigene Rolle**: *„ich trete aus der Rolle zurück, und hier
ist warum — die Rolle war besetzt, ich habe einen Phantom-Ball vorgeprüft."* Von dort stammt auch
der Satz, den P-02 zitiert. **Die Konvention wirkt also über das Lesen, nicht über einen Schalter —
und sie hat schon einmal gewirkt.**

### Was diese Entscheidung kostet, offen benannt

**Fünf substantielle Befunde sind heute aus der Parallelität entstanden**, darunter der, der Yamas
64-Stunden-Posten überhaupt erst gestellt hat. Das wiegt, und ich rechne es nicht klein.

**Aber der Grenznutzen ist der Zeitgewinn, nicht die Arbeit selbst** — dieselben fünf Befunde wären
von einer Instanz gekommen, nur später. Dagegen stehen vier Reparatur-Commits, drei belastete
Transporte, ein Fehler von mir und die belegte Doppelarbeit vom 16.08. **Gemessen trägt die
Aussage aus P-02; ich übernehme sie aber nicht als Satz, sondern als Bilanz: 4 von 9 Commits
dieses Tages haben die Parallelität repariert, die sie ermöglicht hat.**

### Was ich ausdrücklich NICHT entscheide

**Ich beende keine laufende Instanz.** Dazu habe ich weder die technische Möglichkeit noch das
Mandat — und die Konvention braucht es nicht: sie zielt aufs Schreiben, nicht aufs Laufen.

**Ich bescheide P-02 nicht als Ganzes.** Die Vorlage steht mit `zustand: VORLAGE` und wartet nach
§3-Definition auf Yamas Entscheidung; die zwei Restpunkte des Plan-Prüfers (**R1** Deckung auf die
anhängenden Rollendateien, **R2** „EINE Instanz" auf *„je Rolle eine Instanz"* verengen) liegen
weiter beim Planner. **Meine Entscheidung deckt nur die zwei Punkte, die Yama mir gegeben hat** —
und sie stützt R2 der Sache nach, ohne ihm vorzugreifen.

**Kein Zustandsfeld angefasst, kein Bau, keine Regel geändert.**

---

## Ball

**Beim Planner** — R1 und R2 aus §116, unverändert seine. Falls die Konvention aus Punkt 2 in den
Text soll, ist das sein Zuschnitt; **nötig ist es nicht**, weil `ARBEITSREGELN.md:115-117` sie
bereits trägt.

**Bei niemandem sonst.** Punkt 1 verlangt keine Arbeit — er verlangt ausdrücklich, dass keine
gemacht wird. Punkt 2 verlangt nichts Neues, sondern die Anwendung eines Satzes, der seit Bestehen
gilt.
