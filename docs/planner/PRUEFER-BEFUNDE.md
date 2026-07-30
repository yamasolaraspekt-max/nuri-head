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

## 0b. Gegenquittung des PRÜFERS (30.07.2026)

**Rolle angenommen.** Gelesen habe ich vor dieser Zusage: Yamas Aktivierung, diesen Eingang
vollständig (§0–§6), das Prüfraster mit den sechs Linsen. *Eine Rolle zu bestätigen, die man nicht
gelesen hat, wäre genau der Fehler, gegen den sie gebaut ist.*

```text
Empfangen:        30.07.
Verstanden:       ja
Blockiert:        nein
Erster Befund:    folgt nach der ersten Messrunde — Register vor Antwort (§6)
```

**Was ich tue:** die Papiere gegen den **Bestand** halten und melden. **Was ich nicht tue:** bauen,
beheben, Ursachen zu Ende ermitteln. *Aufdecken ist nicht beheben; eine Prüfinstanz, die nebenbei
repariert, hat ihre Unabhängigkeit verkauft.* **Ballbesitz bleibt in jedem Befund beim Planner.**

**Ich halte mich an die Form aus §3** — fehlt ein Feld, ist es kein Befund. **`befehl` und `commit`
sind nicht Beiwerk, sondern der Befund selbst:** was ich ohne nachfahrbaren Befehl schreibe, ist
eine Meinung, und Meinungen gehören hier nicht ins Register. **Jede Fläche geht durch alle sechs
Linsen**; eine Linse ohne Fund wird ausdrücklich als *keine Beanstandung* abgehakt, denn eine
fehlende Linse ist von einer sauberen Fläche nicht zu unterscheiden.

**Und was ausdrücklich KEIN Befund ist**, damit das Register tragfähig bleibt: Geschmack ohne zweite
widersprechende Fundstelle · ungemessene Behauptung · fehlende Doku (höchstens `P3`) · eine Fläche,
die laut Ledger absichtlich noch offen ist — das ist Statusabgleich · eine heutige Regel rückwirkend
auf abgenommenen Bestand.

### Ein Interessenkonflikt, den ich selbst melde, bevor er auffällt

**Ich habe als GENERATOR an AUF-38 gebaut** — Scheiben 2, 3, 4, das Messskript, die generische
Rohwert-Zusage. Ein Teil der Prüffläche ist damit **meine eigene frühere Arbeit**.

*Ein Prüfer, der sein eigenes Werk prüft, ist genau die Konstruktion, gegen die diese Rolle
eingerichtet wurde.* Deshalb: **Befunde, die meine eigene Generator-Arbeit treffen, kennzeichne ich
im Register mit `eigenarbeit: ja`** und lege das Urteil dem **Evaluator** vor, nicht mir selbst.
Meine Messung darf dort die Vorlage sein — nie die Entscheidung.

**Unabhängigkeit ist der ganze Wert dieser Rolle. Sie hält nur, solange sie auch gegen mich gilt.**

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
