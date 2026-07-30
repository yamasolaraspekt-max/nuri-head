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
| PB-001 | `tool-dashboard-current-state.md` | **P1** | warnt vor 422-Blocker, der behoben ist (`schema:check` = 0) | **ANGENOMMEN** | 30.07. 08:45 — Kopf gesetzt, Warnkasten entwertet |
| PB-002 | `tool-dashboard-current-state.md` | P2 | gemessen gegen Sicherungszweig, nicht gegen den Arbeitszweig | **ANGENOMMEN** | 30.07. 08:45 — Bezugspunkt im Kopf benannt |
| PB-003 | `tool-dashboard-current-state.md` | P2 | drei Bestandszahlen daneben: 900↔2370 Z., 11↔19 Commands, 30↔50 Dateien | **ANGENOMMEN** | 30.07. 08:45 — alle drei nachgemessen, Kopf traegt die Ist-Werte |
| PB-004 | `tool-dashboard-current-state.md` | P2 | „Registry NICHT verdrahtet" — fünf UI-Konsumenten gemessen | **ANGENOMMEN** | 30.07. 08:45 — 9 Konsumenten gemessen, Kopf korrigiert |
| PB-005 | `docs/planner/` (Sammel) | P3 | elf Papiere vom 24.07. ohne eingehenden Verweis | **ANGENOMMEN, ABER ANDERS GESCHNITTEN** | Sammelposten, kein Loeschen — siehe Ledger 08:45 |
| PB-006 | `docs/planner/` (Sammel) | P3 | **23 von 65** Papieren von keinem lebenden Dokument erreichbar — darunter **vier** aus den letzten zwei Tagen | **ANGENOMMEN, ABER ANDERS GESCHNITTEN** | 30.07. 08:47 — Sammelposten mit PB-005, kein Loeschen |

---

## 7. Befunde — Runde 1 (30.07.2026)

**Fläche:** `docs/planner/tool-dashboard-current-state.md` (229 Zeilen, angelegt 24.07. 19:37).
**Gemessen gegen:** `67ac4ea0`. **Hinweis zur Messgüte:** der Arbeitsbaum trug zum Messzeitpunkt
**23 unversionierte Änderungen**; alle Befehle unten lesen jedoch nur Dateien, die davon nicht
betroffen sind, bzw. Git-Objekte.

**Warum ausgerechnet dieses Papier zuerst:** es behauptet einen **Ist-Zustand** schon im Namen, es
ist **sechs Tage alt**, und es wird **noch referenziert** — von `docs/fahrplan-dashboard-versionen.md`
(dreimal, u. a. *„Das Design ist also nicht neu zu erfinden. Es steht in
tool-dashboard-current-state.md §6/§8/§9"*) und von
`docs/auftraege/generator-auftrag-dashboard-v2-flaechen.md` als **Grundlage**. Nach §2 Frage 2 ist
genau das der gefährliche Fall: *ein veraltetes Papier, auf das ein aktives Blatt zeigt.*

### PB-001 · P1 · Warnung vor einem Blocker, den es nicht mehr gibt

```yaml
befund:
  id: PB-001
  datei: "docs/planner/tool-dashboard-current-state.md"
  stelle: "Zeilen 9-11 (Kasten ganz oben, vor dem Inhaltsverzeichnis)"
  behauptung: "970f0cc ergaenzte produkt.typ in domain/validation.ts, ohne
    scene-document-v2.schema.json zu regenerieren ... lehnt jede Oeffnung mit produkt.typ ab
    (422 -> Bauart-Szenen unspeicherbar). Fix ist eine Zeile."
  gemessen: "Schema und Zod sind in Sync. Exit-Code 0."
  befehl: "npm run schema:hausplaner:check ; echo $?"
  commit: "67ac4ea0"
  schwere: P1
  wirkung: "Der Kasten steht als Warnung VOR allem anderen im Papier und nennt einen
    unspeicherbaren Zustand mit Datenverlust-Charakter. Wer das Papier heute oeffnet - und zwei
    aktive Blaetter schicken ihn hin - haelt einen behobenen Blocker fuer offen. Zwei Kosten:
    entweder jemand baut den Fix ein zweites Mal, oder er misstraut dem Schema-Gate, das
    tatsaechlich gruen ist."
  eigenarbeit: nein
```

### PB-002 · P2 · Der Stand, gegen den gemessen wurde, ist ein anderer Zweig

```yaml
befund:
  id: PB-002
  datei: "docs/planner/tool-dashboard-current-state.md"
  stelle: "Zeile 4"
  behauptung: "Stand: HEAD 2f12c64 (v9-Welle), Branch private/app-code-backup"
  gemessen: "Der Commit existiert, aber der genannte Zweig ist ein Sicherungszweig
    (private/app-code-backup + remotes/backup-private/...), nicht der Arbeitszweig
    auto/hausplaner-integration. Das Papier misst also einen anderen Ast als den, auf dem
    seither gebaut wird."
  befehl: "git cat-file -t 2f12c64 ; git branch -a --list '*app-code-backup*'"
  commit: "67ac4ea0"
  schwere: P2
  wirkung: "Jede Zahl im Papier ist gegen einen Sicherungsstand gemessen. Wer sie heute als
    Ausgangswert uebernimmt, rechnet gegen einen Ast, auf dem seit sechs Tagen nichts passiert.
    Das ist die Wurzel von PB-003 bis PB-005 - nicht fuenf Einzelfehler, sondern ein falscher
    Bezugspunkt."
  eigenarbeit: nein
```

### PB-003 · P2 · Drei Bestandszahlen sind um Faktoren daneben

```yaml
befund:
  id: PB-003
  datei: "docs/planner/tool-dashboard-current-state.md"
  stelle: "Zeile 50 · Zeile 60 · Zeile 68"
  behauptung: "HausplanerApp.tsx = die eigentliche CAD-Flaeche (~900 Z.) ·
    11 Command-Typen · geometry/ (30 Dateien)"
  gemessen: "2370 Zeilen (Faktor 2,6) · 19 Command-Typen · 50 Dateien"
  befehl: |
    wc -l < resources/planner/hausplaner/app/HausplanerApp.tsx
    grep -oE "'[A-Z][A-Z_]+'" resources/planner/hausplaner/domain/commands.types.ts | sort -u | wc -l
    ls resources/planner/hausplaner/geometry/*.ts | wc -l
  commit: "67ac4ea0"
  schwere: P2
  wirkung: "Das Papier ist als Ist-Inventar in `fahrplan-dashboard-versionen.md` als UI-1 ✅
    abgehakt. Ein Inventar, dessen drei Kernzahlen um 2,6x / +8 / +20 danebenliegen, traegt keine
    Aufwandsschaetzung. Die acht fehlenden Command-Typen sind der schwerste Teil: wer die Liste
    fuer vollstaendig haelt, uebersieht Decken, Dachaufbauten, Sperren und Sichtbarkeit."
  eigenarbeit: nein
```

### PB-004 · P2 · „NICHT verdrahtet" gilt nicht mehr

```yaml
befund:
  id: PB-004
  datei: "docs/planner/tool-dashboard-current-state.md"
  stelle: "Zeilen 64-66, Ueberschrift 2.3"
  behauptung: "Werkzeug-Registry (Teil-Foundation, NICHT verdrahtet) ... (a) kein UI-Konsument"
  gemessen: "Fuenf Konsumenten in app/, darunter HausplanerApp.tsx selbst."
  befehl: "grep -rl 'werkzeugRegistry\|toolRegistry' resources/planner/hausplaner/app/"
  commit: "67ac4ea0"
  schwere: P2
  wirkung: "Die Aussage ist die Begruendung fuer einen Bauauftrag (Registry ans UI anschliessen).
    Sie ist erledigt - laut Ledger durch I4 (110 Werkzeuge sichtbar). Wer das Papier als Grundlage
    nimmt, plant eine Verdrahtung, die es gibt, und riskiert die zweite Wahrheit, gegen die die
    Bauordnung ausdruecklich steht."
  eigenarbeit: nein
```

### PB-005 · P3 · Zehn Papiere desselben Tages, auf die niemand mehr zeigt

```yaml
befund:
  id: PB-005
  datei: "docs/planner/ (Sammelbefund)"
  stelle: "elf Dateien vom 24.07. 19:37"
  behauptung: "(keine - es geht um Ballast, nicht um eine falsche Aussage)"
  gemessen: "Null eingehende Verweise aus docs/ auf: werkzeug-verknuepfung-einsatz-konzept.md,
    werkzeug-benchmark-indesign.md, ticket-reuse-boundaries.md, integration-65-tools-paket.md,
    faehigkeiten-landkarte-und-registry.md, claude-skill-roadmap.md, claude-skill-matrix.md,
    claude-skill-architecture.md, claude-hook-safety.md, claude-agent-matrix.md,
    architektur-3d-hausplaner-zielbild.md"
  befehl: |
    for f in $(ls -t docs/planner/ | tail -20); do
      n=$(grep -rl "$f" docs/ --include='*.md' | grep -v "docs/planner/$f" | wc -l)
      echo "$n  $f"; done | sort -rn
  commit: "67ac4ea0"
  schwere: P3
  wirkung: "Nach der eigenen Regel in §2 Frage 2 ist ein Papier ohne eingehenden Verweis
    *nur Ballast* - es richtet keinen Schaden an, aber es vergroessert die Prueflaeche, die
    jemand bei jeder Runde erneut sichten muss. Sammelposten, keine Einzelvorgaenge."
  eigenarbeit: nein
```

### Die sechs Linsen an dieser Fläche — auch die ohne Fund

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | PB-001, PB-003, PB-004 — Aussagen, die der Baum widerlegt |
| **L2 Effizienz** | keine Beanstandung — das Papier trifft keine Aufwandsaussage, die messbar falsch wäre |
| **L3 Konsistenz** | PB-002 — zwei Bezugspunkte (Sicherungszweig ↔ Arbeitszweig) für denselben Sachverhalt |
| **L4 Kausalität** | PB-004 — die behauptete Wirkung *„kein UI-Konsument"* ist am Code nicht mehr auffindbar |
| **L5 Plausibilität** | keine Beanstandung — die genannten Werte sind größenordnungsmäßig plausibel, nur veraltet |
| **L6 Workflow** | keine Beanstandung — das Papier beschreibt keinen Arbeitsweg, den ich gegen die Oberfläche halten könnte |

**Was ich NICHT als Befund geschrieben habe**, obwohl es auffiel: dass das Papier §6/§8/§9 als
Design-Vorlage dient, während die Oberfläche seither vier Layout-Wellen hinter sich hat. **Das wäre
eine ungemessene Behauptung** — ich habe die Abschnitte nicht gegen die heutige Oberfläche gehalten.
Es steht hier als **Hinweis auf die nächste Runde**, nicht als Befund.

**Ballbesitz: Planner.**

---

## 8. Runde 2 (30.07.) — `eindeutschung-110-paket-ids.md`: **keine Beanstandung**

**Gemessen gegen `9e4fac05`.** Diese Fläche habe ich mir vorgenommen, weil sie das **am häufigsten
referenzierte** alte Papier ist (fünf eingehende Verweise, u. a. *„Führende Quelle — nicht neu
erfinden"* im Auftragsblatt zu AUF-31) und sich selbst **„führende Wahrheit"** nennt. Ein falscher
führender Bestand wäre der teuerste Fund überhaupt.

**Sie ist nicht falsch. Vier Aussagen, vier Treffer:**

| Behauptung | Gemessen | Befehl |
|---|---|---|
| die vollständige Tabelle führt **110** | Tabelle hat **110** Zeilen, **110** verschiedene dt. IDs | `awk -F'\|' '/^\| *[0-9]+ *\|/' … \| wc -l` |
| `tool-registry-paket.json` hat **110** Einträge | **110** | `python3 -c "import json; …len(x)"` |
| **16** IDs sind schema-gebunden (⛔) | **16** | `grep -cE '⛔ \`' …` |
| AUF-34-Nachtrag: Z41 `oeffnung`, Z98 `uebergabepaket` berichtigt | beide im Papier korrekt **und** beide im Code vorhanden | `grep -cE "id: '(oeffnung\|uebergabepaket)'" …/werkzeugPaket.ts` → 2 |

**Der Abgleich Papier ↔ Code geht auf:** `werkzeugPaket.ts` führt **101** IDs, im Papier stehen
**110**. Die **neun** Differenzen sind *exakt* die neun, die das Papier selbst mit `*(9)*` markiert
— Legende: *„deckt eine der 9 bestehenden Registry-IDs (Konvergenz)"* — und alle neun sind im Code
auffindbar (4–6 Fundstellen je ID). **101 + 9 = 110.** In der Gegenrichtung: **null** IDs im Code,
die das Papier nicht kennt.

```sh
comm -23 papier.txt code.txt   # auswahl dach decke duplizieren fenster loeschen treppe tuer wand
comm -13 papier.txt code.txt   # (leer)
```

### Eine Beinahe-Fehlmeldung, die ich offenlege

**Mein erster Auszug war falsch** — er las die falsche Tabellenspalte und lieferte *„7 IDs im
Papier, 0 im Code"*. Hätte ich das gemeldet, stünde hier ein P1 gegen ein Papier, das stimmt.
Gefangen hat es die Plausibilität: *ein Papier mit „110" im Dateinamen kann keine 7 Zeilen führen.*

**Die Lehre gehört ins Register, nicht in eine Fußnote:** die Extraktion einer Grundgesamtheit ist
selbst ein Messwerkzeug und muss geeicht werden, bevor sie zum Befund wird — hier an der
Zeilenzahl (110) und an der Gegenrichtung (leer). *Dieselbe Fehlerklasse, die AUF-38 vier Scheiben
gekostet hat, nur diesmal auf der Prüfseite.*

### Die sechs Linsen

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung — vier Zahlenaussagen geprüft, alle vier treffen |
| **L2 Effizienz** | keine Beanstandung — keine Aufwandsaussage im Papier |
| **L3 Konsistenz** | keine Beanstandung — die parallele Tabelle `werkzeug-namen-deutsch.md` ist ausdrücklich **stillgelegt** und verweist hierher; **eine** führende Wahrheit, wie verlangt |
| **L4 Kausalität** | keine Beanstandung — die Kette Papier → `werkzeugPaket.ts` → Registry ist in beide Richtungen geschlossen |
| **L5 Plausibilität** | keine Beanstandung *(und die Linse, die meinen eigenen Messfehler gefangen hat)* |
| **L6 Workflow** | keine Beanstandung — das Papier beschreibt keinen Arbeitsweg |

**Kein Befund. Ballbesitz bleibt beim Prüfer** — hier ist nichts zu beantworten.

*Für den Planner ist das trotzdem eine Information: **das meistreferenzierte alte Papier trägt.**
Die sechs Tage Alter sagen nichts über die Haltbarkeit — `tool-dashboard-current-state.md` vom
selben Tag ist an vier Stellen überholt, dieses hier an keiner. Der Unterschied ist nicht das
Datum, sondern dass dieses Papier **gepflegt** wurde: der AUF-34-Nachtrag steht drin.*

---

## 9. Runde 3 (30.07.) — PB-006, und eine Korrektur an meinem eigenen PB-005

**Gemessen gegen `d536301d`.**

### Zuerst die Korrektur: PB-005 hat mit dem falschen Maß gemessen

PB-005 zählte *„Papiere ohne eingehenden Verweis aus `docs/`"* und fand elf. **Das Maß war zu
schwach.** Ich habe beim Weitersuchen gesehen, dass die fünf Papiere, die ich als *„wird noch
referenziert"* aussortiert hatte, **ausschließlich von anderen toten Papieren** referenziert
werden — `ticket-reuse-matrix.md` etwa nur von `claude-skill-roadmap.md`, und die hat selbst null
eingehende Verweise.

*Ein Verweis von einem toten Papier hält nichts am Leben.* **PB-005 bleibt gültig, ist aber eine
Teilmenge von PB-006** und sollte mit ihm zusammen bearbeitet werden.

### PB-006 · P3 · Ein Drittel der Prüffläche ist von keinem lebenden Dokument erreichbar

```yaml
befund:
  id: PB-006
  datei: "docs/planner/ (Sammelbefund, ersetzt und erweitert PB-005)"
  stelle: "23 von 65 Eintraegen"
  behauptung: "(keine - Mengenbefund)"
  gemessen: |
    65 Eintraege in docs/planner/
    42 von einem LEBENDEN Dokument erreichbar
    23 von keinem  (35 %)
    Verteilung nach letzter Aenderung:
      19.07. 11 · 23.07. 6 · 25.07. 1 · 26.07. 1 · 28.07. 3 · 29.07. 1
  befehl: |
    cat docs/handoff-status.md docs/auftraege/*.md docs/agents/*.md \
        CLAUDE.md docs/arbeitskompass-ticket.md > /tmp/lebend.txt
    for f in $(ls docs/planner/); do
      grep -q "$f" /tmp/lebend.txt || echo "$f"; done
  commit: "d536301d"
  schwere: P3
  wirkung: "Die Prueflaeche ist um ein Drittel groesser als der Bestand, den irgendjemand benutzt.
    Jede Pruefrunde - meine wie die des Planners - sichtet 23 Papiere, auf die kein Ledger, keine
    Tafel, kein Auftragsblatt und keine Regel zeigt."
  eigenarbeit: nein
```

**Der Teil, der nicht nach Altlast aussieht — und der eigentliche Grund für diesen Befund:**

| Datei | zuletzt geändert | erreichbar von einem lebenden Dokument |
|---|---|---|
| `fortschritt-2026-07-29.html` | **29.07.** | nein |
| `ablauf-und-regeln-vorschlag-2026-07-27.md` | **28.07.** | nein |
| `regelwerk-pruefer-einordnung-2026-07-27.md` | **28.07.** | nein |
| `ai-workflow-pruefbefund-2026-07-27.md` | **28.07.** | nein |

**Vier Papiere aus den letzten zwei Tagen, auf die nichts zeigt.** Das ist kein Aufräum-Thema,
sondern ein laufender Vorgang: *das Papier entsteht, wird im Ledger als Fließtext erwähnt — und
danach findet es niemand mehr über seinen Namen.* `regelwerk-pruefer-einordnung-2026-07-27.md` ist
die Einordnung, aus der **diese Rolle hier** hervorgegangen ist; sie ist über keinen Dateinamen
auffindbar.

**Erledigt wenn:** *für jedes Papier in `docs/planner/` gilt entweder — sein Name kommt in
mindestens einem lebenden Dokument vor, oder es steht in einem Verzeichnis/Abschnitt, der es
ausdrücklich als historisch führt.* Messbar mit dem Befehl oben: die Liste ist leer oder alle
verbliebenen Einträge liegen unter dem Historisch-Pfad.

### Die sechs Linsen an dieser Fläche

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | nicht geprüft — dieser Befund misst Erreichbarkeit, nicht Inhalt |
| **L2 Effizienz** | PB-006 — die Prüffläche ist um 55 % größer als der benutzte Bestand |
| **L3 Konsistenz** | keine Beanstandung |
| **L4 Kausalität** | PB-006 — die Kette *Papier → Verweis → Leser* reißt bei 23 von 65 |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | keine Beanstandung — kein Arbeitsweg betroffen |

**Ballbesitz: Planner.**
