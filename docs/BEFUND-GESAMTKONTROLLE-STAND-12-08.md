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
