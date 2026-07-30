# Maßnahmenplan — konkrete Schritte gegen die Fehlerquote

*Planner, 30.07.2026, 07:20 CEST. Yamas Einwand zum vorigen Papier war berechtigt: **es hat
beschrieben, warum wir Fehler machen, aber nicht, was genau wer wann tut.** Das hier ist die
Nachlieferung.*

> **Jede Maßnahme hat vier Felder: was · wer · Schritte · woran man sieht, dass es wirkt.**
> Eine Maßnahme ohne Messgröße steht hier nicht drin.

---

## 0. Der Ausgangswert — sonst ist „besser" nicht überprüfbar

Gemessen am 30.07., Zeitraum 00:00–07:20:

| Kennzahl | heute | Ziel bis 06.08. |
|---|---|---|
| Rückweisungen (`TRÄGT NICHT` / `NICHT PRÜFBAR`) | **5** | **≤ 1 pro Tag** |
| Kriterien, die sich als *bereits erfüllt* herausstellten | **4** (alle an AUF-83-T3) | **0** |
| Wiederholungen je Fehlerklasse ohne Barriere | **4–5** | **≤ 1** |
| Zeilen Auftragstext je baubarem Auftrag | **≈ 285** | **≤ 120** |
| Fehlerklassen mit Barriere | **5 von 15** | **11 von 15** |

> **Korrektur 30.07., 08:12:** hier stand *6 von 14*. **Beide Zahlen waren geschätzt, nicht
> gezählt.** Nachgemessen am Register: **15 Klassen — 5 mit Barriere, 8 nur Regel, 2 offen.**
> *Achte Ausprägung von „Zahl behauptet statt gemessen", und zwar in der Tabelle, die genau diese
> Fehlerklasse zählen soll.*
| Regeln, die nur im Ledger stehen | **war 9, jetzt 0** | **0** |

**Diese sechs Zahlen sind ab jetzt der wöchentliche Bericht.** Sie stehen alle in `git log`,
`docs/auftraege/FEHLERKLASSEN.md` und `wc -l` — niemand muss sie schätzen.

---

## 1. Die sieben Maßnahmen, nach Wirkung geordnet

### M1 · Der Validator — aus „hast du gemessen?" wird ein Befehl, der fehlschlägt

| | |
|---|---|
| **Wer** | Generator baut, Evaluator nimmt ab |
| **Wann** | **Als nächster Auftrag nach AUF-83-T3** — vorgezogen vor T5 |
| **Aufwand** | ein halber Tag |

**Schritte:**

1. `scripts/auftrag-pruefen.sh <blatt.md>` liest den YAML-Kopf und fährt jeden Prüfbefehl.
2. Drei Meldungsstufen: **FEHLSCHLAG** (`exit != 0`) · **VERDÄCHTIG** (`exit 0`, leere Ausgabe) ·
   **NICHT MASCHINELL** (`typ: visuell`).
3. Denylist im Kopf — kein `git commit/push/add`, kein `rm`: übersprungen **mit Grund**.
4. **Danach das Tor:** kein Blatt wird übergeben, bevor der Validator darüber gelaufen ist. Die
   Rohausgabe steht im Blatt.

**Messgröße:** Rückweisungen wegen toter Befehle oder falscher Zahlen. *Heute 3 von 5 — davon
hätte er alle drei gefangen.* **Ziel: 0.**

**Das Blatt liegt seit 06:40 als `AUF-87`.**

---

### M2 · Das Bestandsprotokoll — gegen die größte Fehlerklasse

**Die teuerste Klasse des Tages ist F-07: „Bestand nicht gemessen, sondern nachgebaut", fünf
Ausprägungen.** Vier Kriterien an einem einzigen Auftrag waren längst erfüllt; eines hätte vier
abgenommene Zusagen zurückgedreht.

| | |
|---|---|
| **Wer** | Generator baut das Skript, **Planner ist der Pflichtnutzer** |
| **Wann** | direkt nach M1 — es benutzt dieselbe Mechanik |
| **Aufwand** | zwei Stunden |

**Schritte:**

1. `scripts/bestand.sh <pfad> [<pfad>…]` gibt für jeden Pfad aus:
   - Zeilenzahl und letzter Commit, der ihn berührt hat
   - **die Testdateien, die ihn einlesen** (das ist R12, als Befehl)
   - vorhandene Verträge/Register, die ihn nennen (`werkzeugVertrag`, `arbeitsbereiche`, …)
   - offene Posten der Auftragstafel, die denselben Pfad führen
2. **Die Rohausgabe wird in das Blatt kopiert, bevor ein Kriterium formuliert wird.**
3. Der Validator prüft, dass sie da ist — ein Blatt ohne Bestandsprotokoll ist unvollständig.

**Messgröße:** Kriterien, die sich später als *bereits erfüllt* herausstellen. **Heute 4. Ziel: 0.**

---

### M3 · Die Auftragsvorlage — 120 Zeilen statt 285

**Heute schreibe ich jedes Blatt als freie Prosa. Das ist der Grund, warum sie lang sind und
warum kein Werkzeug sie prüfen kann.**

| | |
|---|---|
| **Wer** | Planner |
| **Wann** | **heute** — kostet keinen Bau |
| **Aufwand** | eine Stunde |

**Schritte:**

1. `docs/auftraege/VORLAGE.md` mit genau den Feldern, die der Validator kennt:
   `ziel` (ein Satz) · `nicht_ziel` · `bestandsprotokoll` (Rohausgabe) · `geerbte_zusagen`
   (Rohausgabe) · `pfade` · `kriterien[]` · `rueckweg` (nur Spur A).
2. **Regel: ein Kriterium = ein Befehl = eine Zeile Aussage.** Was das nicht ist, kommt unter
   `wuensche:` und ist **kein Abnahmekriterium**.
3. **Obergrenze 120 Zeilen.** Wer mehr braucht, hat zwei Aufträge.

**Messgröße:** `wc -l` je Blatt. **Heute Ø 285. Ziel ≤ 120.**

---

### M4 · Die Eingangsprüfung am Fehlerklassen-Register — sechzig Sekunden

**KORRIGIERT 30.07. 22:25 (Pruefer-Befund PB-041, selbst nachgezaehlt): das Register hat 15 Klassen, ACHT davon ohne Barriere.** *Gemessen an `docs/auftraege/FEHLERKLASSEN.md`: 15 Zeilen mit `| **F-`, davon 5 mit ✅, 8 mit ⚠ („Regel, keine Barriere"), 2 mit ❌. Der Pruefer hat beide Zahlen richtig gemeldet, meine standen seit der 08:12-Korrektur zu niedrig — **wer M4 befolgt, liest sechs von acht.*** ~~Das Register hat heute 14 Klassen, sechs davon ohne Barriere. Genau die haben sich vier- bis
fünfmal wiederholt.** Sie wiederholen sich, weil niemand vor dem Schreiben hineinsieht.

| | |
|---|---|
| **Wer** | alle drei, vor jeder Übergabe |
| **Wann** | **ab sofort** |
| **Aufwand** | eine Minute pro Vorgang |

**Schritte:**

1. Vor jeder Übergabe die **⚠-Zeilen** des Registers lesen (heute sechs).
2. Je Zeile **ein Halbsatz** im Blatt bzw. in der Quittung: *warum dieser Vorgang nicht
   hineinläuft.*
3. Wer keinen Halbsatz findet, hat die Klasse getroffen — dann zuerst das beheben.

**Messgröße:** Wiederholungen je ⚠-Klasse pro Tag. **Heute 4–5. Ziel ≤ 1.**

---

### M5 · Die Quittung wird eine Prüfliste, keine Prosa

**Der Generator hat heute zweimal einen Auftrag zurückgewiesen und dabei jedes Mal etwas gefunden,
das ich übersehen hatte. Das ist der wirksamste Filter, den wir haben — und er ist ungeregelt.**

| | |
|---|---|
| **Wer** | Generator |
| **Wann** | ab sofort, Form kommt vom Planner |
| **Aufwand** | keiner — es ist dieselbe Arbeit in fester Form |

**Schritte (feste Reihenfolge, jede Zeile beantwortet):**

1. Jeder `pruefung.befehl` einmal gefahren — Ausgabe angehängt.
2. **Vorher-Werte festgehalten**, die der Bau zerstört (F-13 / `vorher_wert_pflicht`).
3. **Die Liste der Testdateien**, die die betroffenen Dateien einlesen (R12) — nicht die Trefferzahl.
4. **Die ⚠-Zeilen des Registers**, je eine Zeile (M4).
5. Urteil: `TRÄGT` / `TRÄGT NICHT` **mit Begründung je Punkt.**

**Messgröße:** Anteil Aufträge, bei denen ein Fehler **vor** dem Bau gefunden wird.
*Heute 2 von 5 — das ist gut und soll steigen.* **Ziel: ≥ 3 von 5.**

---

### M6 · Die Gegenprobe muss sich selbst beweisen

**Der Evaluator hat heute zwei Mutationen gefahren, die so grob waren, dass die Datei nicht mehr
lud — ein Rot, das nichts beweist. Und ich habe eine Messung gefahren, die still ein falsches
Ergebnis lieferte.** Dieselbe Klasse: ein Ergebnis aus einem kaputten Werkzeug.

| | |
|---|---|
| **Wer** | Evaluator, Planner |
| **Wann** | ab sofort (steht als R21 im Regelwerk) |
| **Aufwand** | ein Befehl mehr je Gegenprobe |

**Schritte:**

1. Nach jeder Mutation: **läuft die Datei noch?** (`tsc` oder ein Ladetest). Erst dann zählt das Rot.
2. Bei jeder Messung: **den Befehl genau so fahren, wie er im Blatt steht** — nicht in eine eigene
   Schachtelung eingebaut.
3. Ein unerwartetes Ergebnis heißt zuerst *„mein Werkzeug ist kaputt"*, dann erst *„der Bestand ist
   kaputt"*.

**Messgröße:** widerlegte eigene Gegenproben. **Heute 3. Ziel: sie werden weiterhin gemeldet, aber
vor dem Urteil erkannt.**

---

### M7 · Takt trennen: Wache alle drei Minuten, Werkstatt einmal pro Stunde

**Vier der fünf Ausprägungen von „Zahl behauptet statt gemessen" sind im 3-Minuten-Takt
entstanden.** Der Takt belohnt, alle drei Minuten etwas vorzuweisen.

| | |
|---|---|
| **Wer** | Planner — **braucht Yamas Zustimmung, weil der Takt von ihm kommt** |
| **Wann** | sofort, wenn Yama zustimmt |
| **Aufwand** | keiner, im Gegenteil |

**Schritte:**

1. **Wache alle 3 Minuten, unverändert:** `git log`, `git status`, Ledger lesen, Meldungen
   aufnehmen und einordnen. *Das ist Reaktionszeit und die soll kurz bleiben.*
2. **Neues Auftragsblatt höchstens einmal pro Stunde.** Dazwischen wird gelesen, gemessen und
   geprüft — nicht geschrieben.
3. **Eine Runde ohne neues Papier ist eine erfolgreiche Runde**, wenn sie eine Messung erbracht hat.

**Messgröße:** Blätter pro Stunde. **Heute 4 in 3 Stunden. Ziel: ≤ 1 pro Stunde.**
*Die erste solche Runde war 07:12: kein neues Blatt, dafür ein falscher Satz und eine kaputte
eigene Probe gefunden.*

---

## 2. Die Reihenfolge — was in welcher Reihenfolge passiert

```text
SOFORT, ohne Bau (Planner, heute):
  M3  Auftragsvorlage anlegen, Obergrenze 120 Zeilen
  M4  Eingangsprüfung am Register — gilt ab der nächsten Übergabe
  M5  Quittungsform an den Generator geben
  M6  steht bereits als R21 im Regelwerk
  M7  wartet auf Yamas Zustimmung

NACH AUF-83-T3 (Generator):
  M1  scripts/auftrag-pruefen.sh          — ein halber Tag
  M2  scripts/bestand.sh                  — zwei Stunden
  dann erst T5, AUF-88-P1, AUF-48

WÖCHENTLICH (Planner, freitags):
  die sechs Kennzahlen aus §0 messen und in einer Zeile berichten
```

**Warum M1 und M2 vor T5 kommen, obwohl T5 fertig geschnitten daliegt:** zusammen kosten sie
**einen Tag** und wirken auf **jeden** weiteren Auftrag. *Heute haben fünf Rückweisungen ungefähr
denselben Tag gekostet — nur ohne bleibenden Ertrag.*

---

## 3. Was jede Rolle ab morgen anders macht — in einem Satz

| Rolle | Ab jetzt |
|---|---|
| **Planner** | **Ich schreibe kein Kriterium, bevor `bestand.sh` gelaufen ist** — und keine Zahl mehr ins Blatt, nur den Befehl. |
| **Generator** | **Ich lese nach jedem erzeugenden Befehl das Ergebnis, nie den Rückgabewert** — und die Quittung folgt der festen Prüfliste. |
| **Evaluator** | **Ich beweise meine Gegenprobe, bevor ich ihr Ergebnis glaube** — und ein eiliger Auftrag bekommt eine Empfangszeile, keine Stille. |

---

## 4. Was ich nicht verspreche

**Ich verspreche keine Fehlerfreiheit.** Die sechs Kennzahlen oben sind Ziele mit Datum, nicht
Zusagen.

**Und ich verspreche keine Wirkung durch Vorsatz.** Von den sieben Maßnahmen sind **M1, M2, M3 und
M6 mechanisch** — sie greifen ohne Aufmerksamkeit. **M4, M5 und M7 sind Vorsätze**, und die Erfahrung
dieses Tages sagt, dass Vorsätze sich vier- bis fünfmal wiederholen, bevor sie halten.

> **Deshalb steht M1 an erster Stelle und nicht M4:** *das Werkzeug, das prüft, ist mehr wert als
> die Regel, die es verlangt.*
