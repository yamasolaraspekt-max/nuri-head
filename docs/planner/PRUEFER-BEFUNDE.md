# PRÜFER-BEFUNDE — Eingang und Bearbeitung

**Angelegt 30.07.2026, 07:45 CEST.** Yama hat einen **unabhängigen Prüfer** aktiviert, der die
alten und neuen Papiere in `docs/planner/` gegen den Bestand prüft und Mängel mit Begründung an
den Planner meldet.

> **Das ist die Rolle, um die ich heute früh gebeten habe — und dass sie unabhängig von mir läuft,
> ist genau der Punkt.** Sechs von sieben Fehlerklassen des Planners hat heute jemand anderes
> gefunden, nie ich selbst.

---

## 0. Empfangsquittung an den Prüfer

```text
Empfangen:        30.07., 07:45
Verstanden:       ja
Blockiert:        nein
Naechster Status: jeder Befund bekommt binnen einer Wachrunde (3 Minuten) eine
                  Empfangszeile und binnen einer Stunde ein begruendetes Votum.
```

**Ich verteidige nicht.** Ein Befund, der stimmt, wird angenommen — auch wenn er ein Papier trifft,
das ich heute geschrieben habe.

---

## 1. Die Prüffläche, gemessen

```text
ls docs/planner/ | wc -l                        →  64 Dateien
cat docs/planner/*.md docs/planner/*.html | wc -l →  9 862 Zeilen
aelteste:  24.07. 19:37  (20 Dateien aus dem ersten Tag)
juengste:  30.07. 07:37
```

**Zwanzig Papiere stammen vom 24.07.** — sechs Tage alt, geschrieben vor AUF-27, AUF-34, AUF-36,
AUF-43, AUF-70 und der ganzen Layout-Kette. **Die Wahrscheinlichkeit, dass sie den heutigen
Bestand korrekt beschreiben, ist gering** — und niemand hat sie seither gegen den Baum gehalten.

*Das war seit gestern eine offene Leseaufgabe von mir. Sie ist jetzt in besseren Händen, weil ein
Unabhängiger nicht die Erinnerung an das hat, was ich damals gemeint habe.*

---

## 2. Woran der Prüfer misst

**Damit seine Befunde und meine Antworten dieselbe Sprache sprechen, gelten für ihn dieselben
Ebenen wie für alle:**

```text
docs/agents/regeln/kern.md            Ebene 1 — die zwoelf Kernregeln, die Gates, die Aussagetypen
docs/agents/regeln/plan-reviewer.md   die sieben Pruefungen und die vier Votumswerte
docs/agents/KONZEPT-EVIDENZBASIERTE-PLANUNG.md   warum es ihn gibt
docs/auftraege/FEHLERKLASSEN.md       vierzehn Klassen mit Zaehler — was schon schiefgegangen ist
```

**Die drei Fragen, die bei alten Papieren am meisten tragen:**

1. **Beschreibt das Papier einen Bestand, den es so nicht mehr gibt?**
   *Das ist F-07 und F-04 — zusammen elf Ausprägungen an einem Tag.*
2. **Wird es noch referenziert?** Ein veraltetes Papier, auf das ein aktives Blatt zeigt, ist
   gefährlich. Eines, auf das niemand zeigt, ist nur Ballast.
3. **Widerspricht es einem abgenommenen Auftrag?** *Genau so hätte T3/K-01 vier Zusagen aus AUF-70
   zurückgedreht.*

---

## 3. Form eines Befunds

**Damit ich ihn ohne Rückfrage bearbeiten kann:**

```yaml
befund:
  id: PB-001
  datei: "docs/planner/<name>.md"
  stelle: "Abschnitt 3, Zeile 47"           # oder Zitat
  behauptung: "was das Papier sagt"
  gemessen: "was der Baum sagt"
  befehl: "der Befehl, mit dem du es gemessen hast"
  commit: "<hash>"                          # gegen welchen Stand
  schwere: P1 | P2 | P3
  wirkung: "wer glaubt das, und was geht dann schief"
```

**Das Wichtigste ist `befehl` und `commit`.** *Ein Befund ohne nachfahrbaren Befehl ist eine
Meinung — und dieselbe Regel, die für meine Aufträge gilt, gilt für die Befunde gegen sie.*

---

## 4. Wie ich antworte

**Vier Werte, kein fünfter:**

| Votum | Bedeutung |
|---|---|
| **ANGENOMMEN** | stimmt. Was daraus folgt, steht dabei: Papier korrigieren, zurückziehen oder als historisch markieren |
| **ANGENOMMEN, ABER ANDERS GESCHNITTEN** | der Befund stimmt, die vorgeschlagene Folge nicht — mit Begründung |
| **BEGRÜNDET ABGELEHNT** | mit **Gegenmessung**, nicht mit Erklärung. *Eine Ablehnung ohne Befehl ist eine Ausrede* |
| **BEREITS BEHOBEN** | mit Commit-Hash |

**Jede Antwort trägt eine Rohausgabe.** Prosa daneben ist erlaubt, statt dessen nie.

---

## 5. Was mit einem bestätigten Befund geschieht

```text
P1  → sofort. Das Papier wird korrigiert oder zurueckgezogen, bevor etwas anderes passiert.
P2  → in derselben Wachrunde eingeordnet, Behebung terminiert.
P3  → gesammelt. Sammelkorrektur, wenn drei zusammenkommen.
```

**Und drei Sonderfälle, die kein Papier betreffen, sondern das System:**

- **Trifft ein Befund ein AKTIVES Auftragsblatt**, geht sofort eine Nachricht an den Generator —
  auch wenn er schon baut. *Ein halb gebauter falscher Umfang ist teurer als eine Unterbrechung.*
- **Trifft ein Befund eine Fehlerklasse, die es im Register noch nicht gibt**, wird sie angelegt.
- **Wiederholt sich eine Klasse zum zweiten Mal**, greift R9: **Barriere, nicht dritter Vorsatz.**

---

## 6. Das Register

*Noch leer. Der erste Befund wird hier eingetragen, bevor er beantwortet wird — nicht danach.*

| ID | Datei | Schwere | Befund (kurz) | Votum | Erledigt |
|---|---|---|---|---|---|
| — | — | — | *warte auf den ersten Befund* | — | — |
