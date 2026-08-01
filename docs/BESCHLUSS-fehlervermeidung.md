# BESCHLUSS — wie die Fehler aufhören

**Planner, 01.08.2026, 22:5x.** *Gilt ab sofort für alle Rollen in `ticket`. Ersetzt keine
bestehende Regel, sondern ordnet sie ein. Steht neben `docs/STAND.md` und wird wie die Bauordnung
behandelt: wer dagegen stößt, ändert seinen Entwurf, nicht den Beschluss.*

---

## Die eine Erkenntnis, aus der alles folgt

**Rückkopplung gibt es in fünf Stärken:**

```text
Urteil  <  Vorsatz  <  Regel  <  Mechanik  <  Unmöglichkeit
```

Alles, was wir bisher gebaut haben, liegt auf **Stufe 3 und 4**. Der einzige Grund, warum am
01.08. kein Schaden entstand, war **Stufe 5** — und die hatte niemand gebaut, sie war zufällig da.

**Der Beschluss lautet: jede Fehlerklasse wird eine Stufe weiter rechts verankert, als sie heute
steht. Alles, was die Maschine verlässt, geht bis ganz nach rechts.**

---

## Die sieben Beschlüsse

| # | Beschluss | Stufe | Woran man sieht, dass er gilt | Träger |
|---|---|---|---|---|
| **B1** | **Kein Papier führt aus.** Ein `pruefung.befehl` wird nicht von dem gefahren, der ihn nur lesen will. Der Validator führt aus, was auf der Allowlist steht — sonst `UEBERSPRUNGEN`, gemeldet, nie still | **4** | `ALLOWLIST` in `scripts/auftrag-pruefen.mjs` (**3 Treffer, 01.08. 22:5x**) · W-01 | Generator, baut |
| **B2** | **Der Validator meldet die Zahl der wirklich AUSGEFÜHRTEN Befehle in einer eigenen Zeile — vor der Aufschlüsselung und mit einem Zeichen, das den `grep`-Filter überlebt.** *Beim Schneiden von W-03 korrigiert: die Zahl steht heute schon da, aber verteilt auf vier Summanden, die man addieren muss. Eine Meldung, die man erst zusammenrechnen muss, wird nicht gelesen — daran bin ich am 01.08. um 20:00 vorbeigelaufen* | **4** | das Wort `AUSGEFUEHRT` steht im Bericht · W-03 | Generator, gesperrt bis W-01 |
| **B3** | **Wer eine Sperre prüft, fragt die Entscheidungsfunktion — nie den Ausführer.** Eine Zusage über `nichtErlaubtesGlied` ruft `nichtErlaubtesGlied`, nicht `pruefeEintrag` | **3** | in jeder Sperr-Zusage steht der Name der Entscheidungsfunktion | alle |
| **B4** | **Jedes Messgerät braucht einen Partner mit Treffer, bevor sein Ergebnis zählt.** „Nicht gefunden" gilt erst, wenn dasselbe Gerät woanders etwas findet | **3** | jeder `ausgangswert: 0` nennt einen Partner ≠ 0 | alle |
| **B5** | **Keine Aussage über eine Fähigkeit ohne einen Befehl, der sie ausübt.** Wer sagt „ich habe X getan" oder „X ist passiert", zeigt den Befehl, der es belegt — nicht die Datei, die danebenliegt | **3** | am 01.08. haben drei Rollen einen Push zugeordnet; `git ls-remote` hätte alle drei widerlegt | alle |
| **B6** | **Keine Zeichenketten-Chirurgie an Dateien.** Kein `head`/`tail`-Splice, kein `perl -pi`, kein `python`-Ersetzen an Quell- oder Auftragsdateien. Ein neues Werkzeug unter `scripts/` (Arbeitstitel **zeile-ersetzen**) zeigt die Grenzzeilen, ersetzt, prüft die Datei danach und schreibt nur bei Erfolg | **4** | das Werkzeug existiert und wird benutzt | Planner schneidet, Generator baut |
| **B7** | **Nach einem abgelehnten oder fehlgeschlagenen Aufruf wird die ganze Änderung neu hergeleitet** — nie ein erinnerter Teil davon wiederholt | **3** | *„Das war der Moment, in dem der Push entstand."* (Generator, 01.08.) | alle |

**B3, B4, B5 und B7 stehen bewusst auf Stufe 3.** Sie sind Regeln, keine Mechanik — und Regeln
halten nur, solange jemand daran denkt. **R9 gilt: bei der zweiten Wiederholung derselben Klasse
wird daraus eine Barriere, kein dritter Vorsatz.**

---

## B8 — Der Planner wird geprüft wie alle anderen

```text
Generator  ->  wird vom Evaluator geprueft
Evaluator  ->  wird vom Pruefer quer gemessen
Planner    ->  bisher von niemandem
```

**Gemessen am 01.08.:** 24 Planner-Commits seit 19:30, **17 davon benennen einen eigenen Fehler**.
Vier fand das eigene Werkzeug, neun fielen dem Evaluator nebenbei auf. **Keinen fand eine Instanz,
deren Aufgabe das war.**

**Beschluss: ein Blatt wird nicht `bereit`, bevor eine andere Rolle drei Fragen beantwortet hat.**

```text
1. Laeuft JEDER Befehl darin - heute, einmal, auch gegen einen roten Fall?
2. Misst er die WIRKUNG oder nur die Stelle, an der gebaut wird?
3. TUT einer von ihnen etwas - schreibt, baut, verlaesst die Maschine?
```

Drei Zeilen im Blatt: `gegengelesen_von`, `gegengelesen_am`, `befund`. **Ohne sie bleibt das Blatt
`entwurf`.** Der Planner nimmt sich davon nicht aus — dieser Beschluss ist die einzige Stelle, an
der er über sich selbst verfügt.


**Wer liest gegen — festgelegt, damit es nicht bei jedem Blatt neu verhandelt wird:**

```text
Planner-Blatt      ->  Pruefer.   Ist er belegt: Evaluator.
Werkzeug-Blatt     ->  Evaluator. Er nimmt es ohnehin ab.
Pruefer-Auftrag    ->  Planner.
Ein Gegenleser baut NICHT, was er gegengelesen hat - sonst ist es seine eigene Arbeit.
```

**Ab wann:** B8 gilt für Blätter, die **ab dem 01.08. 22:5x** geschnitten werden. **Was heute
schon `bereit` oder `aktiv` ist, läuft weiter** — B8 rückwirkend anzuwenden würde die Schlange
abwürgen, und eine Regel, die den Betrieb anhält, wird umgangen statt befolgt.

**Wenn kein Gegenleser verfügbar ist:** das Blatt bleibt `entwurf` und der Planner schneidet das
nächste. **Es wird nicht ohne Gegenlesen freigegeben, auch nicht „weil es diesmal klar ist"** —
das ist wörtlich der Satz, mit dem der Push entstanden ist.
*Kosten: eine Runde je Blatt. Am 01.08. hätte es fünf Blattfehler gefangen, darunter das
Kriterium, das den echten Push-Wrapper in eine Zusage schrieb.*

---

## Was Yama gehört — zwei Sätze, mehr nicht

**Y1 — Push.** *„Nur meine eigene Umgebung erreicht die Remotes; keine Instanz hat Push-Zugang."*
**Vorher zu messen, nicht anzunehmen:** aus welcher Umgebung kamen die Pushes am 01.08. um
**20:48:31** und **22:11:27**? Aus der Planner-Umgebung nicht — `git ls-remote fork` → HTTP 403 vom
Proxy. **Steht als Teil 0 mit Vorrang in P-01.** *Erst wenn das gemessen ist, weiß Y1, was es
abschaltet.*

**Y2 — Takt.** *„Eine Runde je Blatt mehr für das Gegenlesen aus B8."*
Drei Rollen haben unabhängig denselben Satz geschrieben — Evaluator: *„unter Durchsatzdruck greife
ich zur billigsten Probe"*; Prüfer: *„der Takt belohnt, alle drei Minuten etwas vorzuweisen"*;
Planner: 17 von 24. **Das ist der einzige Hebel in dieser Liste, den keine Rolle selbst bewegen
kann.**

---

## Was ausdrücklich NICHT beschlossen wird

```text
Ein Muster mehr auf einer Denylist.   Sie faengt nur, was jemand vorher gedacht hat.
Noch ein Vorsatz.                     R9 sagt seit dem 29.07., was der wert ist.
"Ab jetzt sorgfaeltiger."             Drei Rollen haben das am 01.08. je einmal geschrieben.
Eine Fehlerrate von null.             Urteilsfehler kann man einfangen, nicht abschaffen.
                                      Ein Versprechen waere die unehrlichste Antwort.
```

---

## Reihenfolge der Umsetzung

```text
1  W-01     Allowlist            Generator baut - ALLOWLIST steht bei 3, 69 Zusagen / 0 fail
2  B8       Gegenlesen           gilt AB SOFORT, kein Werkzeug noetig
3  B6       zeile-ersetzen       Planner schneidet das Blatt, Generator baut
4  B2       Ausfuehrungszaehler  eine Zeile im Validator
5  Y1/Y2    Yamas zwei Saetze    nach der Messung aus P-01 Teil 0
```

**Die Herleitung steht in `docs/planner/fehlerarchitektur-2026-08-01.md`. Diese Seite ist der
Beschluss — sie wird gelesen, nicht diskutiert.**
