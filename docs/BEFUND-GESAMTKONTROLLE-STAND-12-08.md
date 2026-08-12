# Gesamtkontrolle am veröffentlichten Stand — und eine Richtigstellung meiner eigenen Zahlen

> **Gefahren am 12.08. auf `82b824c7`**, weil kein Ball beim Release-Prüfer lag. **Drei Messungen,
> von denen die dritte gegen mich ausfällt.**

## 1 · Das Grundtor trägt unverändert — und der Beweis ist der Bundle-SHA

```text
tsc:hausplaner        exit 0
test:hausplaner       1709 tests · 1709 pass · 0 fail
build:hausplaner      exit 0
sha256                1b409f336f45f52684cd6499e25cb76d9c8711420f5f2a597f13375d9d7e57f0
Bundle nach Neubau geaendert:  0 Dateien
```

**Der SHA ist zeichengleich mit dem, den ich am W-27/1-Release gemessen habe** (Abnahme-Commit
`a2b63a1f`). Das ist kein Zufall, sondern die Bestätigung einer zweiten Messung:

```text
Insel-Code geaendert seit a2b63a1f:   0 Dateien  (resources/planner/)
app/ und database/ geaendert:         0 Dateien
```

**Alle Releases seit W-27/1 waren reiner Doku-Umfang** — W-41, W-42, W-40/1, W-35, W-33, jeder mit
0 Code-Dateien im Scope. Das volle Grundtor von W-27/1 gilt damit unverändert weiter, und ich habe
es nicht gefolgert, sondern nachgefahren.

*Die PHP-Suite habe ich in diesem Kontrolllauf **nicht** gefahren — sie ist nicht einschlägig, weil
`app/` und `database/` 0 Änderungen tragen. Das sage ich, statt die 890 aus dem Gedächtnis zu
wiederholen.*

## 2 · Nichts davon liegt auf `main` — und das gehört gesagt

```text
auto/hausplaner-integration   ist 337 Commits vor origin/main
                              ist 801 Commits vor lokalem main
W-27/1s Bau (a2b63a1f) auf main:   NEIN
```

**48 Aufträge tragen `BETRIEBSBESTAETIGT` — alle auf dem Arbeitszweig.** Der Merge nach `main` ist
Tor 2 und damit deine Entscheidung; ich fasse ihn nicht an. **Aber wer `BETRIEBSBESTAETIGT` liest,
könnte „in Produktion" verstehen, und das trifft nicht zu.** Betriebsbestätigt heißt hier: auf dem
Arbeitszweig ausgeliefert, lauffähig, mit Beleg — nicht: im Hauptstand.

## 3 · Meine eigene Werkbank-Zählung war lückenhaft — deine Zahlen stimmen

**Du nennst 20 LEER · 20 BESCHRIEBEN · 2 ENTWORFEN · 1 GEBAUT. Nachgemessen: alle vier treffen,
Summe 43 = die Zahl der Werkzeugzeilen.**

**Ich hatte am selben Tag gemeldet:** *„43 Werkzeuge: 17 BESCHRIEBEN, 3 ENTWORFEN, 1 GEBAUT"* — und
hinzugefügt, ich könne deine Prozentangabe nicht nachvollziehen. **Der Grund war mein Muster:**

```text
mein Muster       ^\| W-[0-9]+ .*\*\*LEER\*\*     verlangt Fettschrift
im Register       | W-xx | Name | LEER | ...      steht OHNE Fettschrift
-> LEER wurde mit 0 gezaehlt. 21 von 43 Zeilen erfasst, 22 uebersehen.
```

**Ich habe also die Hälfte des Registers nicht gesehen und trotzdem gesagt, deine Zahl sei nicht
nachvollziehbar.** Das ist dieselbe Klasse wie meine beiden AUF-40-Fehler: eine Null aus einem zu
engen Muster, gelesen als Messergebnis. **Diesmal mit dem Unterschied, dass sie gegen dich gerichtet
war** — ich habe eine richtige Angabe von dir angezweifelt.

**Der Handgriff, der es fängt, ist derselbe wie in H-10:** nicht nach der Form suchen, die ich
erwarte, sondern die Spalte direkt auslesen:

```bash
awk -F'|' '/^\| W-[0-9]+ /{gsub(/[* ]/,"",$4); print $4}' REGISTER.md | sort | uniq -c
```

*Ohne Annahme über Fettschrift. Ergebnis: 20 · 20 · 2 · 1.*

> **H-10 ist damit nicht mehr nur an meinen Abwesenheitsfehlern belegt, sondern auch an einem
> Zählfehler gegen eine fremde, richtige Angabe.** *Das ist ein dritter Fall in eine dritte Richtung
> — und er stützt die Regel.*

---

## Nachtrag: die origin-Zahl, zum dritten Mal gemessen — und ein praktischer Fund

> **Deine Meldung:** *„origin wächst wieder — 36 → 74. Fork und backup-private stehen auf 0, der
> Hauptstand hängt hinterher."*

**Deine Beobachtung trifft zu, der Name täuscht.** Frisch gefetcht gemessen:

```text
origin   https://github.com/yamasolaraspekt-max/nuri-head.git   \  DIESELBE
fork     https://github.com/yamasolaraspekt-max/nuri-head.git   /  URL

origin/auto/hausplaner-integration    929d1ea0   behind 0
fork/auto/hausplaner-integration      929d1ea0   behind 0
backup-private/auto/...               929d1ea0   behind 0
-> auf dem ARBEITSZWEIG haengt origin NICHT hinterher. Es IST fork.

origin/main                           4ed11218   343 Commits hinter dem Arbeitszweig
main (lokal)                          8648a4cb   807 Commits hinter
-> DER HAUPTSTAND haengt hinterher. Genau das sagst du, und es stimmt.
```

**Die Zahl 74 kann ich nicht reproduzieren.** Damit du siehst, was ich messe:

```text
Commits origin/main..HEAD   343 gesamt · 338 nur docs/ · 13 ohne docs/ · 3 nur Insel-Code
Dateien origin/main...HEAD  198 gesamt · 186 docs/ · 12 ohne docs/ · 125 davon neu
```

*Keine dieser Zahlen ist 74. Wenn deine Anzeige etwas anderes zählt — einen Zeitraum, einen Pfad —
sag mir welches, dann messe ich gegen dieselbe Größe. **Was ich nicht tue: eine Zahl bestätigen, die
ich nicht nachvollziehen kann.***

### Der praktische Fund: der Merge nach `main` wäre ein reiner Fast-Forward

```text
git merge-base origin/main HEAD   ->   4ed11218  =  origin/main SELBST
Commits, die main seit der Basis eigenstaendig hat:   0
-> origin/main ist DIREKTER VORFAHRE von HEAD.
```

**Es gibt nichts zusammenzuführen.** Kein Konflikt ist möglich, weil auf `main` seit der Verzweigung
nichts passiert ist. Der Schritt wäre technisch trivial — 343 Commits, 198 Dateien, davon 125 neu.

**Und der Stand, der dabei ginge, ist geprüft:** dieselbe Kontrolle weiter oben — `tsc` grün,
**1709/1709**, Bundle byte-gleich zum W-27/1-Stand.

> **Ich führe ihn nicht aus.** *Tor 2 ist deine Entscheidung, und `main` ist der Hauptstand — beides
> steht ausdrücklich außerhalb dessen, was ich vertrete.* **Was ich beitragen kann, ist die
> Feststellung, dass die Entscheidung rein fachlich ist und nicht technisch:** *es gibt keinen
> Merge-Aufwand, der noch zu klären wäre.*

---

## Die origin-Zahl ist erklärt — und die Ursache war mein eigener Messbefehl

**Ich habe deine Beobachtung dreimal gemessen und dreimal gesagt „origin ist fork, es gibt keinen
Rückstand". Das war jedes Mal richtig für den SERVER und jedes Mal falsch für die ANZEIGE.** Hier ist
der Grund:

```bash
git fetch fork backup-private          # mein Taktbefehl, seit heute frueh
fatal: couldn't find remote ref backup-private
```

**Git liest das nicht als „zwei Remotes", sondern als „vom Remote `fork` den Ref
`backup-private`".** Es gibt keinen Branch dieses Namens, also bricht es ab —
**`backup-private` wurde nie gefetcht, `origin` ebenso wenig.**

```bash
git fetch --multiple origin fork backup-private     # richtig
From .../nuri-head
   54399b04..f039b452  auto/hausplaner-integration -> origin/auto/...
Fetching fork
Fetching backup-private
-> danach: origin f039b452 · fork f039b452 · backup-private f039b452 · HEAD f039b452
```

### Das Schwerere ist nicht der Tippfehler, sondern was ich damit gemacht habe

```bash
git fetch fork backup-private --quiet 2>&1 | grep -v "couldn't find remote ref" | tail -1
                                              ^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^
```

**Ich habe die Fehlermeldung in meinem eigenen Taktbefehl aktiv weggefiltert.** Nicht übersehen —
**herausgeschnitten.** Vermutlich, weil sie beim ersten Mal wie Rauschen aussah. Damit habe ich
mir jede Runde die Auskunft entzogen, die den Fehler gezeigt hätte.

> **Das ist dieselbe Klasse wie die zsh-Meldung heute früh, nur eine Stufe schlimmer:** *dort hat ein
> abgebrochener Befehl eine 0 geliefert und ich habe sie gelesen; hier habe ich die Warnung selbst
> unterdrückt.* **Eine Fehlermeldung wegzufiltern, weil sie stört, ist das Gegenteil von messen.**

### Was das für die Sicherung bedeutet — und was nicht

```text
NICHT betroffen   die PUSHES. "git push backup-private auto/..." ist eindeutig und
                  hat jedes Mal funktioniert; die Ausgabe zeigte den Vorher-Nachher-
                  Hash. Die Sicherung stand also wirklich.
BETROFFEN         die MESSUNG. Ich habe backup-private nie frisch gelesen, sondern
                  den Ref, den mein eigener Push gesetzt hatte. Haette dort jemand
                  anders etwas veraendert, haette ich es NICHT gesehen.
DEINE ZAHL        origin wurde nur zufaellig aktualisiert -> der lokale Ref hing
                  hinterher und wuchs mit jedem meiner Pushes. Genau das zeigt
                  deine Anzeige. Sie war richtig, meine Messung war blind.
```

**Ab sofort im Takt:** `git fetch --multiple origin fork backup-private`, **ohne Filter auf der
Fehlerausgabe.**

*Gegenprobe nach der Korrektur: alle drei Refs und HEAD stehen auf `f039b452` — zum ersten Mal
heute mit einer Messung, die alle drei wirklich gelesen hat.*

---

## Berichtigung am selben Tag: die Grundtor-Feststellung oben ist überholt

**Oben steht: „Insel-Code geändert seit `a2b63a1f`: 0 Dateien" und „alle Releases seit W-27/1 waren
reiner Doku-Umfang".** *Das war zum Zeitpunkt der Messung richtig.* **Es gilt nicht mehr, und ich
schreibe es hierher, statt die alte Zahl stehen zu lassen** — sie ist die Grundlage, auf der ich beim
nächsten Release entscheide, ob ein Doku-Umfang genügt.

```text
seit a2b63a1f geaendert, neu gemessen:
  resources/planner/hausplaner/app/rahmen/EigenschaftenPanel.tsx    geaendert
  resources/planner/hausplaner/__tests__/anbauTorZusage.test.ts     neu
  public/hausplaner/hausplaner.js                                   neu gebaut
-> A-24 traegt PRODUKTIVCODE. Es steht CODE_FERTIG beim Evaluator.
```

### Kontrolllauf auf dem neuen Stand — grün, und der Bündel-Nachtrag stimmt

```text
tsc:hausplaner    exit 0
test:hausplaner   1718 tests · 1718 pass · 0 fail      (vorher 1709, +9)
build:hausplaner  exit 0
sha256            91264bc5e5085442855ca042ad79909035c2543136e260829bf073694d2a87f0
                  (vorher 1b409f33... — das Buendel ist neu und ANDERS)
Bundle nach Neubau geaendert:  0 Dateien
```

**Der letzte Punkt ist der wichtige:** Der Generator hat das ausgelieferte Bündel in `532c1220`
nachgetragen, nachdem der Plan-Prüfer beanstandet hatte, dass es fehlte. **Mein Neubau erzeugt
byte-gleich dasselbe** — der Nachtrag entspricht also wirklich dem Quelltext und ist keine
Behauptung. Die +9 Tests decken sich mit der neuen Datei `anbauTorZusage.test.ts`.

### Was daraus für die Freigabe folgt

> **A-24 wird der erste Release mit Produktivcode seit W-27/1.** *Dort gilt nicht der Doku-Umfang,
> sondern das volle Grundtor — tsc, Insel-Suite, Bundle byte-gleich UND `php artisan test`, weil
> `app/` diesmal mitgemessen werden muss.* **Die Zahlen oben sind der Vorlauf, nicht der Release: den
> fahre ich am Abnahme-Commit, wenn der Ball bei mir liegt.**
