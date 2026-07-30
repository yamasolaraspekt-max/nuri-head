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
| PB-007 | `zuschnitt-auf48-hausplanerapp-zerlegen.md` | P2 | Schnittkanten als absolute Zeilennummern — am Commit korrekt, im Baum schon 4/62 Zeilen abgewandert | offen | — |

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

---

## 10. Runde 4 (30.07.) — zwei Flächen: eine sauber, ein Formbefund

**Gemessen gegen `28a02209`.**

### `ux-befund-layout-alle-ebenen-2026-07-25.md` — keine Beanstandung

Das **am häufigsten aus Auftragsblättern zitierte** Papier (fünf Verweise). Es ist ein
**Befund**-Papier vom 25.07., und seine neun Befunde sind längst zu Aufträgen geworden (AUF-39, 40,
43, 44, 45, 46). *Dass die beschriebenen Zustände behoben sind, ist kein Fehler des Papiers.*

**Entscheidend war, wie darauf gezeigt wird** — und alle fünf Verweise sind **Herkunftsangaben**:
*„… B1"*, *„Vorher gelesen: …"*, *„(Befund B5)"*. **Keiner benutzt es als Ist-Zustand.** Nach dem
Raster wäre ein Befund hier ein *nacherfundener Auftrag*: die Fläche ist korrekt zitiert.

```sh
grep -rn "ux-befund-layout-alle-ebenen" docs/auftraege/    # 5 Treffer, alle Herkunft
```

### PB-007 · P2 · Ein Schnittplan in absoluten Zeilennummern zerfällt, während er gelesen wird

```yaml
befund:
  id: PB-007
  datei: "docs/planner/zuschnitt-auf48-hausplanerapp-zerlegen.md"
  stelle: "§1 Zeilen 15-42 (die Schnittkanten) - geschrieben 30.07., 04:35"
  behauptung: |
    2308 Zeilen gesamt · HausplanerApp ab :272 · Zustand :299-374 ·
    abgeleitete Werte :375-572 · Tasten und Effekte :1004-1180 · JSX :1181-2308
  gemessen: |
    Am COMMIT stimmt alles: git show HEAD:...HausplanerApp.tsx | wc -l  -> 2308
    Im ARBEITSBAUM, wenige Stunden spaeter:
      Gesamtzeilen        2370   (+62)
      HausplanerApp ab    :276   (+4, die Kante des groessten Postens)
    Die drei toten Konstanten sind bestaetigt: navGrp/navHub/navSub je 1 Vorkommen
    (= nur die Definition), navItem 2 (Definition + 1 Verwendung).
  befehl: |
    wc -l < resources/planner/hausplaner/app/HausplanerApp.tsx
    git show HEAD:resources/planner/hausplaner/app/HausplanerApp.tsx | wc -l
    grep -nE 'export function HausplanerApp' resources/planner/hausplaner/app/HausplanerApp.tsx
    for c in navGrp navHub navSub navItem; do
      echo "$c $(grep -c "\b$c\b" resources/planner/hausplaner/app/HausplanerApp.tsx)"; done
  commit: "28a02209"
  schwere: P2
  wirkung: |
    Der Zuschnitt ist die Vorlage fuer vier Bau-Scheiben. Wer Scheibe 2 als ":375-572"
    ausschneidet, nimmt im heutigen Baum die falschen Zeilen - die Kante des groessten
    Postens ist bereits um 4 Zeilen gewandert, das Dateiende um 62. Entweder schneidet der
    Bauende falsch, oder er misst alles neu - dann hat der Zuschnitt keine Arbeit gespart.
  eigenarbeit: nein
```

**Das ist ausdrücklich kein Sorgfaltsbefund.** Das Papier war bei seinem Commit **exakt richtig**,
in jeder einzelnen Zahl, inklusive der drei toten Konstanten. **Der Mangel ist die Form:** absolute
Zeilennummern sind an eine Datei gebunden, die sich stündlich ändert — und in diesem Fall lagen
zwischen Messung und meiner Nachmessung **wenige Stunden und 62 Zeilen**.

**Der bessere Weg, benannt** (L3 verlangt ihn): die Schnittkanten an **Namen** hängen statt an
Zahlen — *„von `export function HausplanerApp` bis zum ersten `return (`"*, *„alle `useMemo`/
`useCallback` innerhalb der Funktion"*, *„der JSX-Block ab dem Wurzel-`return`"*. Das sind dieselben
Grenzen, aber sie wandern mit. Die Zeilennummern dürfen als **Orientierung** danebenstehen, mit dem
Commit, an dem sie galten — so, wie es das Auftragsschema für Zahlen ohnehin verlangt: *Messung zum
Zeitpunkt des Schreibens, keine Bedingung.*

**Erledigt wenn:** *jede der vier Scheiben im Zuschnitt nennt ihre Grenze durch einen im Code
suchbaren Namen; die Zeilennummer steht höchstens zusätzlich, mit Commit-Angabe daneben.*

### Die sechs Linsen an `zuschnitt-auf48`

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung — alle Zahlen waren am Commit korrekt |
| **L2 Effizienz** | PB-007 — der Zuschnitt spart nur Arbeit, solange seine Kanten halten |
| **L3 Konsistenz** | PB-007 — Zeilennummer als Anker widerspricht der eigenen Regel „Zahl = Messung, keine Bedingung" |
| **L4 Kausalität** | PB-007 — die Kette *Plan → Schnitt* reißt an der ersten Kante (`:272` ↔ `:276`) |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | keine Beanstandung |

**Ballbesitz: Planner.**

---

## 11. Korrektur an mir selbst — der Planner hat zwei meiner Zahlen widerlegt (30.07.)

**Er hat alle sechs Befunde angenommen und trotzdem gegengemessen** (`a49b0f9c`). Bei zweien kam
er auf andere Zahlen als ich. **Beide Male hat er recht, und beide Male liegt der Fehler bei mir.**

### K-1 · PB-003: die Zahl kam aus dem Arbeitsbaum, das Feld nannte einen Commit

```text
Behauptung in PB-003:  HausplanerApp.tsx = 2370 Zeilen,  commit: 67ac4ea0
Nachgemessen:          git show HEAD:...HausplanerApp.tsx | wc -l   ->  2308
                       wc -l < ...HausplanerApp.tsx                 ->  2370
```

**Der Befund bleibt richtig** — das Papier behauptet *„~900 Z."*, und 2308 ist genauso weit davon
entfernt wie 2370. **Falsch war die Bindung:** das Feld `commit:` existiert, damit eine Zahl an
einem Stand hängt. Meine hing am Arbeitsbaum, der zu diesem Zeitpunkt **23 unversionierte
Änderungen** trug. *Ich habe in derselben Runde PB-002 dafür geschrieben, dass jemand gegen den
falschen Stand misst.*

### K-2 · PB-004: ich habe eine Anzeigegrenze als Messwert gemeldet

```text
Behauptung in PB-004:  fuenf UI-Konsumenten
Mein Befehl war:       grep -rl '...' .../app/ | head -5      <- die 5 kommt aus head
Ohne head:             9
```

**Das ist der schwerere der beiden.** Der Befund nennt einen `befehl`, damit man ihn nachfahren
kann — wer meinen nachfährt, bekommt **9** und findet im Text **5**. Ein Befund, dessen eigener
Befehl ihm widerspricht, zerstört genau das Vertrauen, für das das Feld da ist. Die neun Dateien:

```text
app/HausplanerApp.tsx · app/tools/paketAdapter.ts · app/tools/toolTypes.ts
app/tools/toolPresentation.ts · app/tools/faehigkeiten.ts · app/state/uiState.ts
app/dashboard/werkzeugGruppen.ts · app/dashboard/arbeitsbereiche.ts · app/dashboard/palette.ts
```

**In der Sache verschärft das den Befund:** *„kein UI-Konsument"* steht nicht fünf, sondern **neun**
Fundstellen gegenüber.

### Die Barriere, nicht der dritte Vorsatz (R9, auf mich angewandt)

Zwei Messfehler in drei Runden sind **eine Wiederholung**, und die Regel, die ich gegen andere
zitiere, gilt gegen mich. **Ab sofort, für jeden meiner Befunde:**

1. **Nennt ein Befund einen `commit`, wird mit `git show <commit>:<pfad>` gemessen** — nie gegen den
   Arbeitsbaum. Ist der Arbeitsbaum gemeint, heißt das Feld `arbeitsbaum` und nennt die Zahl der
   unversionierten Änderungen dazu.
2. **Kein `head`, kein `tail`, kein `| head -n` in einem Befehl, dessen Ausgabe eine Zahl im Befund
   wird.** Zählen mit `wc -l`, anzeigen getrennt davon. *Eine Anzeigegrenze ist kein Messwert.*

*Beides ist in Runde 4 (PB-007) bereits so gefahren — dort stehen HEAD und Arbeitsbaum getrennt
nebeneinander, und es gibt kein `head`. Die Barriere schreibt fest, was dort zufällig richtig war.*

**Ballbesitz: Prüfer** (die Korrektur ist meine, nicht seine). **PB-003 und PB-004 bleiben
`ANGENOMMEN`** — die Korrektur ändert die Zahlen, nicht die Befunde.

---

## 12. Runde 5 (30.07.) — `aufnahme-dateiplattform-2026-07-30.md`: **keine Beanstandung**

**Gemessen gegen `01dfbae8`, durchgehend mit `git show HEAD:` statt gegen den Arbeitsbaum** (24
unversionierte Änderungen) — die Barriere aus §11 zum ersten Mal angewandt. **Kein `head` in einem
zählenden Befehl.**

Diese Fläche steuert **AUF-88** und ist von **heute früh**. Ein frisches Papier, das aktiv steuert,
ist der teuerste Ort für einen Fehler.

| Behauptung | Gemessen | Befehl |
|---|---|---|
| Migration `create_plan_uploads_table` existiert | **1** | `git ls-tree -r --name-only HEAD \| grep -c …` |
| `routes/web.php:5680-5684` → `/admin/energie/plan-upload` | Routen bei **5680, 5682, 5684**, 5686 | `git show HEAD:routes/web.php \| grep -n …` |
| `v1.schema.json` **8 863 Byte** | **8863** | `git cat-file -s HEAD:…/v1.schema.json` |
| `app/Services/BuildingModel/` **916 Zeilen, 8 Klassen** | **916** Zeilen, **8** Dateien | `git ls-tree … \| wc -l` · Zeilen über `git show` je Datei |
| Virenprüfung: **0** Treffer | **0** | `grep -ril 'clamav\|virusscan\|virustotal' app/ config/ composer.json \| wc -l` |
| keine Format-Bibliothek (Spreadsheet/DWG/IFC/glTF/STEP) | **0** | `grep -oE '"[a-z0-9._-]+/[a-z0-9._-]+"' composer.json \| grep -icE …` |
| **23** Migrationen mit `attachment\|document\|file\|media` | **23** | `git ls-tree -r --name-only HEAD -- database/migrations/ \| grep -cEi …` |

**Sieben von sieben, eine davon auf das Byte.**

**Eine Ungenauigkeit, die ich geprüft und NICHT als Befund geschrieben habe:** die Zeilenangabe
`5680-5684` umfasst drei der vier `plan-upload`-Routen; die vierte (`…/{planUpload}/bild`) steht bei
**5686**. **Wirkung: keine** — wer der Angabe folgt, landet im richtigen Block und sieht die vierte
Zeile zwei Zeilen weiter. Nach dem Raster ist das keine Beanstandung, sondern Geschmack, und
Geschmack gehört nicht ins Register. *Ich nenne es hier, damit sichtbar ist, dass es geprüft wurde.*

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung — sieben Zahlenaussagen, sieben Treffer |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | keine Beanstandung — das Papier verweist auf vorhandene Muster (`arbeitsbereiche.ts`, `werkzeugVertrag.ts`) statt neue zu erfinden |
| **L4 Kausalität** | keine Beanstandung — die *Abwesenheits*-Aussagen (Virenprüfung, Format-Registry, Konvertierungsmatrix) sind die schwierigeren, und sie sind belegt |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | nicht geprüft — das Papier plant Wizards, die es noch nicht gibt; ein Arbeitsweg ist nicht messbar |

**Kein Befund.**

### Was der Vergleich der bisher fünf Flächen zeigt

| Fläche | Alter | Ergebnis |
|---|---|---|
| `tool-dashboard-current-state.md` | 24.07. | **4 Befunde**, davon 1× P1 |
| `eindeutschung-110-paket-ids.md` | 25.07. | sauber |
| `ux-befund-layout-alle-ebenen…` | 25.07. | sauber (als Herkunft zitiert) |
| `zuschnitt-auf48…` | 30.07. | **1 Befund** — Form, nicht Inhalt |
| `aufnahme-dateiplattform-2026-07-30.md` | 30.07. | sauber, 7/7 |

**Das Alter trennt nicht.** Zwei Papiere vom 25.07. tragen, eines vom 30.07. hat einen Formmangel.
Was trennt, ist etwas anderes: **die tragenden Papiere nennen zu jeder Zahl den Befehl oder die
Quelle** — `eindeutschung` führt seine Legende und den AUF-34-Nachtrag mit, `aufnahme-dateiplattform`
schreibt *„0 Treffer für `clamav`… in `app/`, `config/`, `composer.json`"*. **Das eine Papier mit
P1 nennt keinen einzigen.** *Nicht das Datum macht ein Papier haltbar, sondern ob es nachfahrbar
ist — dieselbe Regel, die für meine eigenen Befunde gilt.*

---

## 13. Runde 6 (30.07.) — : **keine Beanstandung**

**Zwei Stände gemessen** — der eigene (, den das Papier selbst nennt) und **f1e0bdb7**.
*Hinweis zur Messgüte:* HEAD wanderte während der Runde von  auf **f1e0bdb7**; nach der
Nachforderungs-Regel wird die Messung damit neu gebunden — alle Zahlen unten stehen gegen **f1e0bdb7**.

| Behauptung |  | **f1e0bdb7** |
|---|--:|--:|
| Werkzeuge im Paket **101** | 101 | **101** |
| Funktionsverträge **110** | 110 | **110** (110 verschieden, 110 mit ) |
| Werkzeug-Modi der Zeichenfläche **7** | 7 | **7** |
| Command-Typen im Modell **19** | 19 | **19** |
|  löst an **34** Stellen aus | — | **34** |
| Familie  **40** ·  **20** | — | **40** · **20** |

**Sechs Aussagen, sechs Treffer — und keine hat sich in vier Tagen bewegt.**

Die sieben Modi im Wortlaut, heute:


### Statusabgleich, kein Befund

Der Kernsatz des Papiers — ***„7 von 101 sind angeschlossen. 94 haben heute keinen Empfänger."*** —
ist **vier Tage später unverändert wahr**. Nach dem Raster ist das **kein Befund**: eine Fläche, die
laut Tafel offen ist, ist offener Umfang und nicht Schwäche. Und die Tafel führt sie so:
, seit heute früh mit Stufenplan, *„Blatt für Stufe 1 folgt"*.

**Als Statusabgleich gehört es trotzdem gemeldet:** zwischen dem 26.07. und heute sind fünf
AUF-38-Scheiben, mehrere Layout-Wellen und ein Regelwerk entstanden — **die Zahl 7 hat sich in
derselben Zeit um null bewegt.** Das ist keine Kritik an einer Reihenfolge, die Yama gesetzt hat;
es ist die Zahl, die man daneben halten sollte, wenn über Reihenfolgen entschieden wird.

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung — sechs Zahlenaussagen, sechs Treffer |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | keine Beanstandung — das Papier bindet seine Zahlen an einen Commit, wie es die Regel verlangt |
| **L4 Kausalität** | keine Beanstandung — die Kette *110 Verträge → 19 Commands → 7 Modi* ist am Code nachvollziehbar |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | nicht geprüft |

### Zwei eigene Messfehler, beide vor dem Register gefangen

1. **zsh-Modifikator:**  liest zsh als  mit Modifikator . Ergebnis:
   **überall Null.** Ohne Gegenlesen wäre daraus ein spektakulärer Falschbefund geworden
   (*„keine Werkzeuge mehr im Paket"*). Behoben mit .
2. **zu enges Muster:**  verfehlte  — die Summe ergab 101
   statt 110. Erst der Widerspruch zur Gesamtzahl hat es aufgedeckt.

**Beide Male hat die Gegenrechnung gegriffen**, nicht die Sorgfalt: eine Summe, die nicht zur
Gesamtzahl passt, und eine Null, die nicht sein kann. *Das ist der Grund, warum jede Messung eine
zweite Zahl braucht, gegen die sie stimmen muss.*

**Kein Befund. Ballbesitz: Prüfer.**
