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
| PB-021 | `CLAUDE.md` (Skill-Pflicht) | **P2** | 12 von 22 vorgeschriebenen Fach-Linsen existieren an keinem der beiden Skill-Orte | offen | — |
| PB-019 | `docs/auftraege/` (aktive Blätter) | **P2** | 6 von 15 aktiven Blättern ohne YAML-Kopf — der Validator findet dort nichts zu fahren | offen | — |
| PB-020 | `AUFTRAGSSCHEMA.md` | P3 | Beispiel nennt `zaehle-statische-stile.sh` — die Datei gibt es nicht | offen | — |
| PB-018 | `k01n1b.mjs` | **P2** | Klartext-Zugang im Wurzelverzeichnis; `.gitignore`-Muster greifen nur bei passendem Namen | **ANGENOMMEN** | 30.07. 09:28 — Sicherheitsposten an Yama: `.gitignore` + `mv`; liegt ausserhalb meiner Schreibflaeche |
| PB-017 | (Arbeitsbaum) | **P1** | 466 geänderte + 8 neue Dateien ungesichert, im Ledger aber als geliefert und geprüft geführt | **ANGENOMMEN — Umfang groesser** | 30.07. 09:28 — gemessen 13 Dateien / 885+232 Zeilen / 10 unverfolgt; Ledger-Korrektur sofort, Sicherung haengt an Yamas A-oder-B-Entscheid |
| PB-016 | `inventur.sh` / `AUFTRAGSTAFEL.md` | P3 | Inventur zählt Zeilen: 21 Zeilen für 17 Posten (vier doppelt) | **ANGENOMMEN, ABER ANDERS GESCHNITTEN** | 30.07. 09:28 — Zaehlfehler im Werkzeug, nicht im Bestand; Posten `scripts/inventur.sh` an den Generator |
| PB-015 | `AUFTRAGSTAFEL.md` | **P2** | zwei Posten tragen `⚡ AKTIV` (5 Vorkommen) — §1c verlangt genau einen | **ANGENOMMEN** | 30.07. 09:28 — AUF-38 traegt ⏸ ZURUECKGESTELLT; Barriere in §1c, Zaehlung ankert auf `^| **AUF-` → 1 |
| PB-013 | `docs/agents/regeln/kern.md` | **P1** | „wird IMMER geladen" — vom vorgeschriebenen Startpfad aus mit 0 Verweisen unerreichbar | **ANGENOMMEN** | 30.07. 09:20 — Kopfkasten in allen fuenf Startblaettern; Gegenprobe 5x 1 Treffer |
| PB-014 | `docs/agents/` (Struktur) | **P1** | zwei vollständige Regelsätze für dieselben drei Rollen, 1534 Z., ohne Verweis aufeinander | **ANGENOMMEN — es sind DREI** | 30.07. 09:20 — `00-REGELWERK.md` (377 Z.) ist die Arbeitsgrundlage, `regeln/` nachrangig; **Commit-Zeitpunkt offen an Yama** |
| PB-011 | `FEHLERKLASSEN.md` | **P2** | drei Zähler zu niedrig; in zehn Prüfrunden kein Befund eingetragen | offen | — |
| PB-012 | `FEHLERKLASSEN.md` | **P2** | die Barriere von F-14 ist ein Absatz — nach zwei Stunden gebrochen; R9 verlangt mehr | offen | — |
| PB-009 | `bestandsaufnahme-studio-rahmen-2026-07-29.md` | **P2** | Konflikttabelle sperrt eine freie Datei; Anker auf einen Rahmen, der seit T1/T3 umgebaut ist (217→159 Z.) | **ANGENOMMEN** | 30.07. 09:05 — HISTORISCH-Kopf; fuer den Stand gilt die Auftragstafel |
| PB-010 | `stilschicht.test.ts` | P3 | Wirkungs-Zusage prüft gegen 3 tote Bezeichner — **`eigenarbeit: ja`, Urteil beim Evaluator** | **ANGENOMMEN, ABER ANDERS GESCHNITTEN** | 30.07. 09:05 — kein Papier-, ein Testbefund; geht als Posten an den Generator |
| PB-008 | `generator-auftrag-auf83-t2t3-kopfleiste.md` | P3 | aktives Blatt führt `T1a` als offenen Schritt — Code und Register führen ihn als erledigt (`97a2e2a4`) | **ANGENOMMEN** | 30.07. 09:05 — Blatt traegt HISTORISCH-Kopf, verbindlich sind T2/T3/T3-N1 |
| PB-007 | `zuschnitt-auf48-hausplanerapp-zerlegen.md` | P2 | Schnittkanten als absolute Zeilennummern — am Commit korrekt, im Baum schon 4/62 Zeilen abgewandert | **ANGENOMMEN** | 30.07. 09:05 — Zuschnitt §7: Schnittkanten ueber Namen statt Zeilennummern |

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

## 13. Runde 6 (30.07.) — `bestandsaufnahme-auf50-werkzeuge-2026-07-26.md`: **keine Beanstandung**

**Zwei Stände gemessen** — der eigene (`72e3d31`, den das Papier selbst nennt) und `f1e0bdb7`.
*Hinweis zur Messgüte:* HEAD wanderte während der Runde von `ef3169d5` auf `f1e0bdb7`; nach der
Nachforderungs-Regel wird die Messung damit neu gebunden — alle Zahlen unten stehen gegen
`f1e0bdb7`.

| Behauptung | `72e3d31` | `f1e0bdb7` |
|---|--:|--:|
| Werkzeuge im Paket **101** | 101 | **101** |
| Funktionsverträge **110** | 110 | **110** (110 verschieden, 110 mit `familie`) |
| Werkzeug-Modi der Zeichenfläche **7** | 7 | **7** |
| Command-Typen im Modell **19** | 19 | **19** |
| `app/` löst an **34** Stellen aus | — | **34** |
| Familie `create` **40** · `modify` **20** | — | **40** · **20** |

**Sechs Aussagen, sechs Treffer — und keine hat sich in vier Tagen bewegt.**

Die sieben Modi im Wortlaut, heute:

```ts
type Werkzeug = 'auswahl' | 'wand' | 'fenster' | 'tuer' | 'dach' | 'treppe' | 'decke';
```

Befehle:

```sh
git show "$C":resources/planner/hausplaner/app/tools/werkzeugPaket.ts | grep -cE "id: '"
git show "$C":resources/planner/hausplaner/app/tools/werkzeugVertrag.ts | grep -cE "werkzeugId: '"
git show "$C":resources/planner/hausplaner/app/HausplanerApp.tsx | grep -A4 "type Werkzeug"
git show "$C":resources/planner/hausplaner/domain/commands.types.ts | grep -oE "'[A-Z][A-Z_]+'" | sort -u | wc -l
git grep -h -oE "type: '[A-Z][A-Z_]+'" "$C" -- 'resources/planner/hausplaner/app' | wc -l
```

### Statusabgleich, kein Befund

Der Kernsatz des Papiers — ***„7 von 101 sind angeschlossen. 94 haben heute keinen Empfänger."*** —
ist **vier Tage später unverändert wahr**. Nach dem Raster ist das **kein Befund**: eine Fläche, die
laut Tafel offen ist, ist offener Umfang und nicht Schwäche. Und die Tafel führt sie so:
`AUF-50 · OFFEN OHNE AUFTRAG`, seit heute früh mit Stufenplan, *„Blatt für Stufe 1 folgt"*.

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

### Drei eigene Fehler in dieser Runde, alle vor dem Register gefangen

1. **zsh-Modifikator:** `git show $c:pfad` liest zsh als `$c` mit dem Modifikator `:r`.
   Ergebnis: **überall Null.** Ohne Gegenlesen wäre daraus ein spektakulärer Falschbefund geworden
   (*„keine Werkzeuge mehr im Paket"*). Behoben durch Anführungszeichen um beide Teile.
2. **zu enges Muster:** `familie: '[a-z]+'` verfehlte `'assign-or-calculate'` — die Summe ergab 101
   statt 110. Aufgedeckt hat es der Widerspruch zur Gesamtzahl, nicht die Sorgfalt.
3. **unquotierter Heredoc:** dieser Abschnitt wurde beim ersten Schreiben von der Shell
   *ausgeführt* statt geschrieben — jedes Fragment in Rückwärts-Anführungszeichen verschwand.
   Der Commit `6b106209` trägt die zerpflückte Fassung; diese hier ersetzt sie.

**Die Barriere wächst um einen dritten Punkt:** *ein Heredoc, dessen Text Rückwärts-Anführungszeichen
oder `$` enthält, wird gequotet* (`<<'PY'`). Punkte 1 und 2 stehen schon in §11.

**Und die Lehre, die über alle drei geht:** zweimal hat nicht die Sorgfalt gerettet, sondern eine
**zweite Zahl, gegen die die erste stimmen musste** — eine Summe gegen eine Gesamtzahl, eine Null
gegen die Erwartung. *Deshalb braucht jede Messung eine Gegenrechnung, auch die des Prüfers.*

**Kein Befund. Ballbesitz: Prüfer.**

---

## 14. Runde 7 (30.07.) — `t1-entscheidungsgrundlage-ticket-shell-2026-07-29.md`

**Gemessen gegen `769bc61d`, durchgehend mit `git show`** — `objekt.blade.php` liegt geändert im
Arbeitsbaum, ein Blick in die Datei hätte gegen einen Stand gemessen, den es nicht gibt.

### Was trägt — und es ist der größte Teil

| Behauptung | Gemessen |
|---|---|
| `app.blade.php` = **11 189** Zeilen | **11189** |
| `@yield('title')` **Z11** | **11** |
| `@yield('style')` / `@stack('style')` **2535 / 2536** | **2535 / 2536** |
| `@include('admin.layouts.sidebar')` **4452** | **4452** |
| `@yield('content')` **4837** | **4837** |
| `@stack('scripts')` / `@yield('script')` **11148 / 11149** | **11148 / 11149** |
| `<main class="main-wrapper">` **4554** | **4554** |
| `id="mainContentScroll"` **4836** | **4836** |
| Routen **4983** / **4988** | **4983** / **4988** |

**Neun Anker, neun Treffer, zeilengenau in einer Datei mit 11 189 Zeilen.**

*Eine Ausnahme ohne Wirkung:* `</header>` steht bei **4833**, das Papier nennt **4835**. Zwei
Zeilen, mitten in einem Block, den die Nachbarangaben eingrenzen. **Kein Befund** — wer der Angabe
folgt, landet richtig.

### PB-008 · P3 · Ein aktives Auftragsblatt führt einen Schritt als offen, den der Code erledigt hat

```yaml
befund:
  id: PB-008
  datei: "docs/auftraege/generator-auftrag-auf83-t2t3-kopfleiste.md"
  stelle: "Abschnitt 'Die Reihenfolge, in der das steht', Punkt 3 (Z198)"
  behauptung: "T1a / T4 - die Insel nimmt ihre Masse vom Behaelter statt vom Fenster.
    Das ist zugleich der erste Zerlegungsschritt von AUF-48, siehe
    t1-entscheidungsgrundlage-ticket-shell-2026-07-29.md"
  gemessen: |
    Beide Messungen, auf denen die Entscheidungsgrundlage steht, existieren nicht mehr:
      min-height: calc(100vh - 46px)   -> 0 Treffer in beiden Hausplaner-Blades
      innerWidth - 220 - 268           -> 0 Treffer in HausplanerApp.tsx
    Stattdessen steht dort, was das Papier vorgeschlagen hat:
      studio.blade.php:41   #hausplaner-root { width: 100%; height: 100%; }
      HausplanerApp.tsx:370 const gemesseneBreite = useGemesseneBreite(inhaltRef)
      HausplanerApp.tsx:371 const breite = buehnenBreite(gemesseneBreite)
      eigenes Modul app/dashboard/buehnenBreite.ts
    Der Code benennt es selbst als erledigt:
      HausplanerApp.tsx:1444  "seit AUF-83-T1a wird der ohnehin gemessen (buehnenBreite.ts)"
    Und das Register fuehrt es so:
      "AUF-83-T1a  erledigt"  ·  "T1a ist seit 09:58 GEBAUT (97a2e2a4)"
  befehl: |
    git show HEAD:resources/views/admin/hausplaner/studio.blade.php | grep -c '100vh'
    git grep -n "innerWidth" HEAD -- 'resources/planner/hausplaner' | grep -v __tests__
    git show HEAD:resources/planner/hausplaner/app/HausplanerApp.tsx | grep -n 'buehnenBreite'
  commit: "769bc61d"
  schwere: P3
  wirkung: |
    Wer das aktive Blatt liest, um zu wissen was als naechstes kommt, sieht T1a als offenen
    Schritt. Gebaut ist es seit 09:58. Fuer den Planner ist das folgenlos - er hat T1a selbst
    abgenommen. Gefaehrlich wird es fuer eine Instanz, die neu dazukommt und das Blatt als
    Fahrplan liest: sie baut die Behaelter-Messung ein zweites Mal. Das ist genau der Fall,
    den CLAUDE.md am TopologieGate festhaelt - "Ist-Beleg aus dem CODE, nicht aus dem Papier".
  eigenarbeit: nein
```

**Warum nur P3 und nicht P2:** die Tafel — das führende Register — zeigt `AUF-83-T3-N1` als aktiv
und führt `T1a` als erledigt. **Das Blatt widerspricht dem Register, aber das Register gewinnt.**
Der Schaden entstünde erst, wenn jemand das Blatt ohne die Tafel liest.

**Erledigt wenn:** *Punkt 3 der Reihenfolge im Blatt nennt `T1a` als erledigt (mit `97a2e2a4`) oder
enthält ihn nicht mehr; ein `grep -n "T1a" ` auf dem Blatt zeigt keine Zukunftsform.*

**Zum Papier selbst: keine Beanstandung.** Eine Entscheidungsgrundlage, deren Vorschläge umgesetzt
wurden, ist nicht falsch — sie ist eingelöst. *Der Mangel steckt im Blatt, das sie zitiert, nicht in
ihr.*

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung — neun Anker, neun Treffer |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | **PB-008** — Blatt und Register sagen Verschiedenes über denselben Schritt |
| **L4 Kausalität** | keine Beanstandung — die Kette *Befund → Vorschlag → Code* ist geschlossen und im Code benannt |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | nicht geprüft |

**Ballbesitz: Planner.**

---

## 15. Runde 8 (30.07.) — `bestandsaufnahme-studio-rahmen-2026-07-29.md` + ein Befund gegen mich selbst

**Gemessen gegen `09781ce7`.** Das Papier ist die erklärte **„Grundlage"** des aktiven
Auftragsblatts `generator-auftrag-auf83-t2t3-kopfleiste.md`.

### Was trägt

| Behauptung | Gemessen |
|---|---|
| `speicherAnzeige.ts:45` — `text: 'Testfläche — wird nicht gespeichert'` | **Z45**, wörtlich |
| Kontextmenüs: `onContextMenu`/`onDoubleClick` **0 Treffer** in der Insel | **0** |
| `befehlspalette` in `werkzeugPaket.ts:427` | **427** |
| `befehlspalette` in `werkzeugVertrag.ts:1309` | **1309** |

### PB-009 · P2 · Die Konflikttabelle des aktiven Blatts beschreibt einen Bestand von gestern Abend

```yaml
befund:
  id: PB-009
  datei: "docs/planner/bestandsaufnahme-studio-rahmen-2026-07-29.md"
  stelle: "Abschnitt 'Wer hat die Datei heute' (Z165-172) und die Anker in Z47, Z109-111"
  behauptung: |
    HausplanerStudio.tsx (217 Z.) frei · HausplanerApp.tsx (2305 Z.) · ConfigWizard.tsx:
    "AUF-38 Scheibe 5 - laeuft gerade" · Studio-Navigation klappbar 266<->66 px
    (HausplanerStudio.tsx:25 navZu) · Werkzeugleiste HausplanerApp.tsx:1371 ·
    Panel HausplanerApp.tsx:1793 · studio.blade.php:34 hp-scratch
  gemessen: |
    HausplanerStudio.tsx      159 Zeilen  (Papier 217)
    navZu im Produktivcode    0 Treffer   (die klappbare Navigation gibt es nicht mehr)
    studio.blade.php          hp-scratch 0 Treffer - absichtlich entfernt, das Blade
                              dokumentiert es selbst in Z52/Z59
    HausplanerApp.tsx         2308 Zeilen (Papier 2305); Z1371 und Z1793 tragen anderen Inhalt
    ConfigWizard.tsx          Scheibe 5 laeuft NICHT - Tafel: "SCHEIBE 5 ANGEHALTEN",
                              Quittung TRAEGT NICHT (d4e73fe2)
  befehl: |
    git show HEAD:resources/planner/hausplaner/app/HausplanerStudio.tsx | wc -l
    git grep -ln "navZu" HEAD -- 'resources/planner/hausplaner'
    git show HEAD:resources/views/admin/hausplaner/studio.blade.php | grep -c 'hp-scratch'
  commit: "09781ce7"
  schwere: P2
  wirkung: |
    Der Abschnitt existiert, um zwei Schreiber auf einer Datei zu verhindern - er ist das
    Werkzeug gegen genau den Schaden, den der Evaluator am 28.07. gemeldet hat (Bundle
    vorgelaufen, Sichtprobe unfuehrbar). Er fuehrt ConfigWizard.tsx als besetzt, obwohl die
    Scheibe angehalten ist: eine Datei wird gesperrt, die frei ist. Und er beschreibt eine
    klappbare Navigation als Muster zum Abschauen, die es nicht mehr gibt.
    Die Zeilenzahlen sind der kleinere Teil - die Belegung ist der gefaehrliche.
  eigenarbeit: nein
```

**Wie bei PB-007 ist das kein Sorgfaltsbefund:** die Aufnahme war am 28.07. um 23:05 richtig. Am
nächsten Morgen hat **AUF-83-T1/T3 genau die Datei umgebaut, die sie beschreibt** — von 217 auf 159
Zeilen. *Eine Aufnahme des Rahmens, die den Umbau des Rahmens überlebt, kann es nicht geben.*

**Erledigt wenn:** *der Abschnitt „Wer hat die Datei heute" nennt für jede Datei einen Zustand, der
mit der Tafel übereinstimmt — messbar: kein Eintrag führt eine Scheibe als „läuft gerade", die auf
der Tafel angehalten oder abgenommen ist.*

### PB-010 · P3 · Meine eigene Zusage prüft gegen drei Bezeichner, die es nicht mehr gibt

```yaml
befund:
  id: PB-010
  datei: "resources/planner/hausplaner/__tests__/stilschicht.test.ts"
  stelle: "Z284 - die Wirkungs-Zusage aus AUF-38 Scheibe 4"
  behauptung: "const dynamisch = /\?|navZu|offeneHubs|imExperte|navBreit|\bst\.|\bp\.|\bf\./"
  gemessen: |
    navZu       0 Treffer im Produktivcode
    offeneHubs  0
    navBreit    0
    imExperte   3   (der einzige, der ueberlebt hat)
    Die Suite ist gruen (1394/1394) und HausplanerStudio.tsx hat heute 5 Inline-Stile,
    davon 0 offen - es entsteht also KEIN falsches Rot, heute.
  befehl: |
    for m in navZu offeneHubs imExperte navBreit; do
      echo "$m $(git show HEAD:resources/planner/hausplaner/app/HausplanerStudio.tsx | grep -c $m)"
    done
  commit: "09781ce7"
  schwere: P3
  wirkung: |
    Drei tote Bezeichner in einer Zusage sind ein zweiter Massstab neben
    scripts/statische-inline-stile.mjs - genau die zweite Wahrheit, gegen die das Skript
    gebaut wurde. Ein kuenftiger dynamischer Block mit einem NEUEN Bezeichner wird als
    "ohne Grund" gemeldet, obwohl er einen hat: falsches Rot an einer Stelle, an der
    niemand mehr hinsieht, weil die Zusage seit Wochen gruen ist.
  eigenarbeit: JA
```

> **`eigenarbeit: ja` — dieser Befund trifft meine eigene Arbeit als Generator** (AUF-38 Scheibe 4).
> **Das Urteil liegt beim Evaluator, nicht bei mir.** Meine Messung ist die Vorlage, nicht die
> Entscheidung. *Ich melde ihn trotzdem, weil eine Prüfinstanz, die ihre eigenen Spuren auslässt,
> ihre Unabhängigkeit von der falschen Seite her verliert.*

**Bemerkenswert:** die Datei **weiß es schon**. In Z417 steht: *„Scheibe 4 trug dafür eine
handgeschriebene Regex-Liste (`navZu`, `offeneHubs`, …) — die musste jeder neue Bezeichner
nachziehen, und sie war ein zweiter Maßstab."* Der Nachfolger hat das Problem benannt und die
Skript-Zusage danebengestellt — **aber die alte Liste steht weiter da und prüft weiter.**

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | PB-009 — Zeilenzahlen und Anker |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | PB-009, PB-010 — zwei Maßstäbe für dieselbe Sache, zweimal |
| **L4 Kausalität** | PB-010 — die Kette *Marker → Einstufung* greift ins Leere |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | keine Beanstandung |

**Ballbesitz: Planner** (PB-009) · **Evaluator** (PB-010, `eigenarbeit`).

---

## 16. Runde 9 (30.07.) — `inventur-werkzeug-icons-2026-07-25.md`: **keine Beanstandung**

**Gemessen gegen `8f423b2b`.**

| Behauptung | Gemessen |
|---|---|
| **9** Werkzeuge, die die Leiste wirklich rendert (`toolRegistry.ts`) | **9** |
| **54** Einträge im Katalog (`toolCatalog.ts`) | **stillgelegt** — `toolCatalog.ts` ist heute ein 25-Zeilen-Weiterreichen des 110er-Pakets; die 54 liegen als Trail in `toolCatalogStillgelegt.ts` |
| **110** im neuen Paket (`src/tool-registry.json`) | Pfad existiert nicht mehr — das Paket ist `werkzeugPaket.ts` (101) + Registry (9) = **110** |

**Kein Befund.** Die 54 beschreiben den Zustand **vor** I2/AUF-21, und **der Code sagt das selbst**:
*„Vorher standen hier 54 aus einem InDesign-Paket abgeleitete Einträge … Sie sind stillgelegt, nicht
gelöscht."* Zitiert wird das Papier nur von **abgeschlossener** Arbeit (`I1`, Archiv-Einträge AUF-20
und AUF-21). Nach dem Raster ist eine historische Aufnahme, die als Herkunft zitiert wird, keine
Schwäche.

### Die Icon-Abdeckung, weil sie sichtbar brechen würde

```text
Werkzeug-IDs (Paket 101 + Registry 9)   110
SVG-Dateien in public/hausplaner/icons/tools/   111
IDs ohne Icon-Datei                       0
Icon-Dateien ohne Werkzeug                1   (_sprite)
```

**Lückenlos.** *Nebenbefund ohne Wirkung, geprüft und verworfen:* das Feld `icon` im Paket führt
`icons/<id>.svg`, die Dateien liegen unter `icons/tools/`. **Das Feld wird nicht als URL benutzt** —
`werkzeugGruppen.ts:100` baut den Pfad aus der `id`: `/hausplaner/icons/tools/${t.id}.svg`, und der
Kommentar darüber nennt genau diesen Grund. Kein Bild bricht.

### Dritte Beinahe-Fehlmeldung, wieder von der Gegenrechnung gefangen

Mein erster Abgleich verglich `icons/<id>.svg` gegen `icons/tools/<id>.svg` und meldete
**101 fehlende Icons**. Gefangen hat es nicht die Sorgfalt, sondern die Unmöglichkeit: *101 kaputte
Icons in einer Oberfläche, die täglich benutzt wird, hätte jemand gesehen.*

**Damit ist das Muster dreimal aufgetreten** (Runde 2: falsche Spalte · Runde 6: zsh-Modifikator und
zu enges Muster · Runde 9: Pfad-Präfix). **Jedes Mal war es die Extraktion, nie die Bewertung**, und
jedes Mal hat eine zweite Zahl es gefangen. Die Barriere aus §11 deckt es bereits ab; die Zahl
gehört trotzdem ins Register, weil sie zeigt, **wo** die Prüfarbeit fehleranfällig ist: nicht im
Urteil, sondern im `grep`.

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | keine Beanstandung — der stillgelegte Katalog steht als Trail daneben, nicht als zweite Wahrheit |
| **L4 Kausalität** | keine Beanstandung — Werkzeug → Icon-Datei ist lückenlos |
| **L5 Plausibilität** | keine Beanstandung *(und zum dritten Mal die Linse, die meinen eigenen Fehler fing)* |
| **L6 Workflow** | nicht geprüft |

**Kein Befund.**

---

## 17. Runde 10 (30.07.) — das Fehlerklassen-Register selbst

**Gemessen gegen `8ad89ce2`.** Neue Fläche, nicht `docs/planner/`: **`docs/auftraege/FEHLERKLASSEN.md`**
— das Register, das §2 dieses Eingangs mir selbst als Maßstab nennt. **An seinen Zählern hängt R9:**
*„Bei der zweiten Wiederholung derselben Fehlerklasse muss eine Barriere stehen."* Ein Zähler, der
zu niedrig steht, löst keine Barriere aus.

### PB-011 · P2 · Die Zähler laufen dem Baum hinterher, und der Prüfer taucht in keinem auf

```yaml
befund:
  id: PB-011
  datei: "docs/auftraege/FEHLERKLASSEN.md"
  stelle: "Spalte 'Zaehler', Zeilen 33-47"
  behauptung: "F-14 = 3 (zuletzt 30.07. 06:58) · F-12 = 4 (06:38) · F-06 = 6 (29.07. 09:59)"
  gemessen: |
    F-14 "Schreibvorgang scheitert, Commit gelingt trotzdem":
      Zaehler 3, letzter Eintrag 06:58. Vorfall 6b106209 um 08:55 - unquotierter
      Heredoc, Shell fuehrte den Text aus, Commit lief mit rc=0 durch.
      Treffer auf "6b106209" im Register: 0 · im Ledger: 0.   -> wahr ist 4
    F-12 "Der vorlaufende Baum kostet eine Messung":
      Zaehler 4. In Runde 6 wanderte HEAD waehrend meiner Messung von ef3169d5
      auf f1e0bdb7 - im Register nicht erfasst (0 Treffer).   -> wahr ist mindestens 5
    F-06 "Zusage prueft Gestalt statt Wirkung":
      Zaehler 6, zuletzt 29.07. PB-010 ist eine weitere Auspraegung.  -> mindestens 7
    Nennungen von Pruefer-Befunden im ganzen Register: 0
  befehl: |
    grep -c "6b106209" docs/auftraege/FEHLERKLASSEN.md docs/handoff-status.md
    grep -c "PB-0" docs/auftraege/FEHLERKLASSEN.md
    git log -1 --format='%ad' --date=format:'%d.%m %H:%M' -- docs/auftraege/FEHLERKLASSEN.md
  commit: "8ad89ce2"
  schwere: P2
  wirkung: |
    R9 loest an der ZWEITEN Wiederholung aus. Ein Zaehler, der eine Auspraegung nicht
    kennt, verzoegert die Barriere um genau diese eine - und die naechste Wiederholung
    ist die, die man haette verhindern koennen. Dass in zehn Pruefrunden kein einziger
    Befund in eine Klasse eingetragen wurde, heisst: der Pruefer speist das Register
    nicht, obwohl er die Fehlerklassen als Maßstab vorgesetzt bekommt (§2).
  eigenarbeit: teilweise - zwei der drei Auspraegungen sind meine
```

### PB-012 · P2 · Die Barriere von F-14 ist ein Absatz — und sie hat nach zwei Stunden versagt

```yaml
befund:
  id: PB-012
  datei: "docs/auftraege/FEHLERKLASSEN.md"
  stelle: "F-14, Spalte 'Barriere'"
  behauptung: "Jeder Fliesstext geht in einen dreifach angefuehrten Rohstring, nie in
    Zeichenkettenverkettung. Und die eigentliche Barriere: nach jedem Schreibskript
    git status lesen, BEVOR committet wird."
  gemessen: |
    Barriere eingetragen  30.07. 06:58
    Verstoss              30.07. 08:55  (6b106209, zwei Stunden spaeter)
    Der Verstoss stammt von einer Instanz, die das Register nie gelesen hatte -
    es steht in keinem Startblock, in keinem Takt-Text und in keiner Rollenakte.
  befehl: |
    git log -1 --format='%ad' --date=format:'%d.%m %H:%M' 6b106209
    grep -rl "FEHLERKLASSEN.md" docs/agents/ docs/auftraege/AUFTRAGSTAFEL.md
  commit: "8ad89ce2"
  schwere: P2
  wirkung: |
    R9 sagt woertlich: "Ein Absatz, ein Hinweis oder 'kuenftig darauf achten' zaehlt
    NICHT als Barriere." Die Barriere von F-14 ist genau das - eine Verhaltensregel in
    einem Dokument. Sie hat den naechsten Vorfall nicht verhindert, und zwar nicht aus
    Nachlaessigkeit, sondern weil die handelnde Instanz das Dokument nicht kannte.
    Eine Barriere, die vom Lesen abhaengt, ist eine Bitte.
  eigenarbeit: nein
```

**Der Vorschlag, entscheiden tut der Planner:** was F-14 verlangt — *nach jedem Schreibskript
`git status` lesen, bevor committet wird* — ist **mechanisierbar**. Ein Schreibskript, das die
Zieldatei nach dem Schreiben gegen ein erwartetes Merkmal prüft (Zeilenzahl gewachsen, Marker
vorhanden) und bei Abweichung mit ungleich Null endet, macht aus der Bitte eine Sperre. *Ich baue
es nicht — ich melde, dass die vorhandene Fassung R9 nicht genügt.*

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | PB-011 — drei Zähler zu niedrig |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | PB-012 — das Register verletzt seine eigene Regel R9 |
| **L4 Kausalität** | **PB-012** — die Kette *Barriere → Verhalten* reißt nachweislich: 06:58 eingetragen, 08:55 gebrochen |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | PB-011 — der Prüfer ist als Speiser des Registers nirgends vorgesehen |

**Ballbesitz: Planner.**

---

## 18. Runde 11 (30.07.) — zwei Regelwerke für dieselben Rollen

**Gemessen gegen `96ff85e3`.** Fläche: `docs/agents/regeln/kern.md`, das §2 dieses Eingangs mir als
Maßstab nennt.

### PB-013 · P1 · „Wird IMMER geladen, von allen Rollen" — vom vorgeschriebenen Startpfad aus unerreichbar

```yaml
befund:
  id: PB-013
  datei: "docs/agents/regeln/kern.md"
  stelle: "Zeile 3"
  behauptung: "Eine Seite. Diese Regeln werden IMMER geladen, von allen Rollen, vor jedem Vorgang."
  gemessen: |
    Treffer auf "kern.md" in:
      CLAUDE.md                                  0
      docs/agents/00-REGELWERK.md                0
      docs/agents/01-planner.md                  0
      docs/agents/02-generator.md                0
      docs/agents/03-evaluator.md                0
      docs/agents/04-claude-code-startanweisung  0
    CLAUDE.md schreibt als Startlektuere vor:
      docs/agents/00-zyklus.md · 04-claude-code-startanweisung.md
      · 05-fachagenten-produkt-architektur-frontend.md
    Keines dieser drei nennt kern.md. Erreichbar ist es nur ueber Ledger, Tafel,
    Auftragsblaetter und die Dateien in demselben Verzeichnis.
  befehl: |
    grep -c "kern.md" CLAUDE.md docs/agents/0*.md
    grep -oE "docs/agents/[0-9a-z-]+\.md" CLAUDE.md | sort -u
  commit: "96ff85e3"
  schwere: P1
  wirkung: |
    Eine Regel, die sich fuer universell erklaert und vom vorgeschriebenen Startpfad
    nicht erreichbar ist, gilt fuer niemanden, der neu anfaengt. Ich bin der Beleg:
    elf Pruefrunden, und ich habe kern.md erst in dieser Runde gelesen - obwohl §2
    dieses Eingangs es mir nennt und ich es haette lesen muessen.
    Dieselbe Mechanik wie PB-012, eine Ebene hoeher.
  eigenarbeit: nein
```

### PB-014 · P1 · Zwei vollständige Regelsätze für dieselben drei Rollen, ohne Verweis aufeinander

```yaml
befund:
  id: PB-014
  datei: "docs/agents/ (Strukturbefund)"
  stelle: "docs/agents/*.md gegen docs/agents/regeln/*.md"
  behauptung: "(keine - es gibt zwei Saetze, und beide gelten)"
  gemessen: |
    Satz A - von CLAUDE.md vorgeschrieben:
      00-zyklus.md      100 Z.  28.07. 21:30
      01-planner.md     103 Z.  28.07. 21:30
      02-generator.md   134 Z.  28.07. 23:40
      03-evaluator.md    84 Z.  28.07. 21:30
      00-REGELWERK.md   377 Z.  30.07. 05:29
    Satz B - von kern.md als "immer geladen" bezeichnet:
      kern.md           146 Z.  30.07. 06:34
      planner.md        160 Z.  30.07. 06:08
      generator.md      113 Z.  30.07. 06:34
      evaluator.md      112 Z.  30.07. 05:40
      plan-reviewer.md  205 Z.  30.07. 05:37
    Verweise von Satz A auf Satz B: 0
    Verweise von CLAUDE.md auf Satz B: 0
    Zusammen 1534 Zeilen Regeln fuer drei Rollen, in zwei Ablagen.
  befehl: |
    ls docs/agents/*.md docs/agents/regeln/*.md
    grep -c "regeln/" docs/agents/0*.md CLAUDE.md
  commit: "96ff85e3"
  schwere: P1
  wirkung: |
    "Eine Wahrheit je Sachverhalt" ist die erste Dauerregel dieses Repositoriums.
    Hier stehen zwei Wahrheiten ueber die Rollen selbst. Welche gilt, haengt davon ab,
    welchen Pfad eine Instanz zufaellig zuerst liest - der vorgeschriebene fuehrt zu
    Satz A, die aktuellen Auftragsblaetter fuehren zu Satz B. Zwei Instanzen koennen
    beide regelkonform arbeiten und trotzdem Verschiedenes tun.
    Beleg aus dem Bestand: Satz A fuehrt die Rollengrenze "der Generator prueft seine
    eigene Arbeit nicht" (02-generator.md); in Satz B (regeln/generator.md) kommt
    dieselbe Formulierung nicht vor.
  eigenarbeit: nein
```

**Was ich NICHT behaupte:** dass Satz B falsch ist oder Satz A veraltet. **Welcher führt, ist eine
Planner-Entscheidung** — ich melde, dass es zwei gibt und dass keiner den anderen nennt. *Ein
Befund, der die Ablösung gleich mitentscheidet, hätte den Planner ersetzt statt ihm zugearbeitet.*

**Erledigt wenn:** *`grep -c "regeln/kern.md" CLAUDE.md` liefert mindestens 1, **oder** die Dateien
unter `docs/agents/regeln/` tragen im Kopf, welcher der beiden Sätze führt und was mit dem anderen
geschieht.*

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung — der Inhalt beider Sätze ist in sich stimmig |
| **L2 Effizienz** | 1534 Zeilen Regeln für drei Rollen in zwei Ablagen |
| **L3 Konsistenz** | **PB-014** — zwei Wahrheiten über die Rollen selbst |
| **L4 Kausalität** | **PB-013** — die Kette *Regel → Leser* ist am vorgeschriebenen Startpfad unterbrochen |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | PB-013 — wer neu anfängt, liest den falschen Satz und weiß es nicht |

**Ballbesitz: Planner.**

---

## 19. Runde 12 (30.07.) — die schichtübergreifende Kette Zod → Schema → PHP: **keine Beanstandung**

**Gemessen gegen `6c5fbb8b`.** Keine Papierfläche, sondern die **Behauptung**, die `CLAUDE.md` und
der `bauplaner-3d`-Skill gemeinsam aufstellen: *„Zod ändern ⇒ IMMER regenerieren, sonst 422."* Das
ist der Vertrag zwischen Insel und Server — **die Stelle, an der Datenverlust entstünde**, und
zugleich die, vor der `PB-001` fälschlich als offener Blocker warnte.

**Die Kette, Glied für Glied am Code:**

```text
domain/validation.ts (Zod)
  -> npm run schema:hausplaner        erzeugt scene-document-v2.schema.json
  -> npm run schema:hausplaner:check  Teil von build UND test  (package.json:7-10)
  -> SceneDocumentValidator.php:12    const SCHEMA_PATH = 'resources/.../scene-document-v2.schema.json'
                              :42    file_get_contents(base_path(self::SCHEMA_PATH))
  -> SpeichereHausplanerDokumentRequest.php   ruft den Validator im Speicherweg
```

**Kein Glied fehlt, und keines liest eine Kopie** — der PHP-Validator liest genau die Datei, die der
Generator erzeugt. *Eine zweite Wahrheit gäbe es hier, wenn jemand das Schema nach `app/` kopiert
hätte; das ist nicht der Fall.*

**Und der Vertrag ist geprüft** — `tests/Feature/Hausplaner/HausplanerSpeichernNutzlastTest.php`:

| Abgewiesen wird | |
|---|---|
| unbekanntes Zukunftsfeld ohne Schemawechsel · Float-Millimeter · unbekannter Node-Typ · Nullwand · verwaiste Öffnung · überstehende Öffnung · fremde `projectId` · abweichende Hüllen-/Szenen-Version · abweichende Revision · gewechselte Dokument-ID · übergroße Szene | **11 Fälle, alle 422** |
| Revisionskonflikt | **409**, mit erwarteter Revision im Rumpf |

**Das Beste daran ist der Hilfssatz `assert422OhneMutation`:** er prüft nicht nur den Statuscode,
sondern dass `scene_json`, Revision und Checksum **unverändert bleiben**. *Eine Ablehnung, die
nebenbei schreibt, wäre schlimmer als eine Annahme* — und genau das kann hier niemand mehr
versehentlich einbauen.

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | keine Beanstandung — **eine** Schemadatei, von beiden Seiten gelesen, keine Kopie |
| **L4 Kausalität** | keine Beanstandung — die Kette ist an jedem Glied belegt und am Ende getestet |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | keine Beanstandung — die Ablehnung schreibt nichts, der Nutzer verliert nichts |

**Kein Befund.**

### Vierte Beinahe-Fehlmeldung — und diesmal wäre sie ein P2 gewesen

Mein erster Griff war `git grep -ln "SceneDocumentValidator" -- tests` → **leer**, und daraus wäre
*„der wichtigste Vertrag der Insel hat keinen Test"* geworden. **Der Test prüft das Verhalten über
die Route, nicht die Klasse über ihren Namen** — und das ist die bessere Bauart, nicht die
schlechtere.

**Das Muster ist jetzt viermal dasselbe:** falsche Spalte · zsh-Modifikator · Pfad-Präfix · Suche
nach dem Namen statt nach der Wirkung. **Alle vier in der Extraktion, keiner im Urteil.** Und der
vierte ist der lehrreichste, weil er dieselbe Verwechslung ist, die `K9` den Zusagen verbietet:
*Gestalt statt Wirkung geprüft.* **Ich habe die Regel, die ich prüfe, beim Prüfen gebrochen.**

---

## 20. Runde 13 (30.07.) — die Auftragstafel gegen ihre eigene Kardinalregel

**Gemessen gegen `552240f5`.**

### PB-015 · P2 · Zwei Posten tragen `⚡ AKTIV`, einer davon laut eigenem Text „vormals aktiv"

```yaml
befund:
  id: PB-015
  datei: "docs/auftraege/AUFTRAGSTAFEL.md"
  stelle: "§1c (Z146-160) gegen die Postenzeilen Z293 (AUF-38) und Z295 (AUF-83)"
  behauptung: "Auf dieser Tafel traegt GENAU EIN Posten die Markierung ⚡ AKTIV.
    Der Generator zieht nur diesen."
  gemessen: |
    Postenzeilen mit der Marke:            2   (AUF-38 Z293, AUF-83 Z295)
    Vorkommen der Marke in diesen Zeilen:  5
    Im Einzelnen:
      AUF-38  "⚡ AKTIV · SCHEIBE 3 ABGENOMMEN - FREIGABE (Evaluator, 28.07.)"
      AUF-38  "⚡ AKTIV · [vormals aktiv: AUF-38 SCHEIBE 8a (EngineFlaeche) ...]"
      AUF-38  (Fliesstext: "die Marke stand auf ⚡ AKTIV" - Erzaehlung, keine Marke)
      AUF-83  "⚡ AKTIV - VORGEZOGEN (Yama, 29.07., 08:20) >>> AUF-83-T3-N1 <<<"
      AUF-83  "T1b (⚡ AKTIV, Sperre gefallen, generator-auftrag-auf83-t1b-ticket-shell.md)"
  befehl: |
    git show HEAD:docs/auftraege/AUFTRAGSTAFEL.md | grep -E '^\| \*\*[A-Z]+-[0-9]' | grep -c '⚡ AKTIV'
    git show HEAD:docs/auftraege/AUFTRAGSTAFEL.md | grep -E '^\| \*\*[A-Z]+-[0-9]' | grep -o '⚡ AKTIV' | wc -l
  commit: "552240f5"
  schwere: P2
  wirkung: |
    §1c existiert, weil am 25.07. der falsche Posten gezogen wurde - "nicht aus
    Nachlaessigkeit, sondern weil beides gleich aussah. Ein Wort auf der Tafel
    unterscheidet sie." Heute steht dieses Wort an zwei Posten und fuenf Stellen.
    Eine Instanz, die die Regel woertlich befolgt - "zieh den mit der Marke" - findet
    zwei Zeilen. Dass AUF-38 eine davon ausdruecklich als "vormals aktiv" fuehrt,
    hilft nur dem, der den Fliesstext liest; die Regel verweist auf die MARKE.
  eigenarbeit: nein
```

**Die Zeile weiß es besser als die Tafel.** In derselben AUF-38-Zelle steht die Lehre eines
früheren Vorfalls: *„… indem er die **Marke** liest; die Marke stand auf `⚡ AKTIV`. Meine Sperre
stand als **Satz im Fließtext** derselben Zelle. **Zwei widersprüchliche …"*** — damals Marke gegen
Fließtext, **heute Marke gegen Marke.** Dieselbe Klasse, eine Stufe härter: der Fließtext kann eine
Marke nicht mehr entschärfen, wenn zwei Marken nebeneinanderstehen.

**Erledigt wenn:** *`grep -E '^\| \*\*[A-Z]+-[0-9]' AUFTRAGSTAFEL.md | grep -o '⚡ AKTIV' | wc -l`
liefert genau `1`.* Erzählende Erwähnungen im Fließtext gehören dann in eine andere Schreibweise
(z. B. „die Marke") — sonst zählt der Befehl sie mit, und die Regel bleibt unprüfbar.

**Ein Vorschlag, entscheiden tut der Planner:** dieselbe Zeile ist mechanisch prüfbar. Als Wächter
im Gate — *„genau eine Marke, sonst rot"* — wäre §1c das erste Mal eine **Barriere** statt einer
Regel. Das ist genau der Punkt aus `PB-012`, hier mit einem Einzeiler lösbar.

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung — beide Posten sind sachlich richtig beschrieben |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | **PB-015** — die Tafel widerspricht ihrer eigenen §1c |
| **L4 Kausalität** | **PB-015** — die Kette *Marke → gezogener Posten* ist nicht mehr eindeutig |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | **PB-015** — wer die Regel wörtlich befolgt, findet zwei Ziele |

**Ballbesitz: Planner.**

---

## 21. Runde 14 (30.07.) — Tafel gegen Archiv, und die Zahl auf dem Schirm

**Gemessen gegen `54e7416f`.**

### Was trägt: die Trennung Tafel ↔ Archiv

```text
Posten auf der lebenden Tafel   10 verschiedene Nummern
Posten im Archiv                81
in beiden gefuehrt               0
```

**Keine Doppelführung.** Ein Posten, der zugleich als offen und als abgeschlossen geführt wird, wäre
der teuerste Zustand dieser Ablage — den gibt es nicht.

### PB-016 · P3 · Die Inventur zählt Zeilen, aber vier Posten belegen zwei Zeilen

```yaml
befund:
  id: PB-016
  datei: "scripts/inventur.sh (Z70-76) gegen docs/auftraege/AUFTRAGSTAFEL.md"
  stelle: "zaehle() { ... grep -c . ; } auf die Abschnitte 3a/3b"
  behauptung: "(implizit) eine Zeile = ein Posten"
  gemessen: |
    Abschnitt 3a "Arbeitsvorrat"       15 Zeilen  /  13 verschiedene Posten
      doppelt: AUF-48 + "AUF-48 (Zuschnitt)"  ·  AUF-50 + "AUF-50 (Stufenplan)"
    Abschnitt 3b "Abnahme-Stapel"       6 Zeilen  /   4 verschiedene Posten
      doppelt: AUF-83-T3 zweimal  ·  AUF-83-T2 zweimal
    Zusammen: 21 Zeilen fuer 17 Posten.
  befehl: |
    git show HEAD:docs/auftraege/AUFTRAGSTAFEL.md | awk '
      /^### 3a\./ {a=1;next} /^### 3b\./ {a=2;next} /^### 3c\./ {a=3;next} /^## / {a=0}
      /^\| \*\*AUF-/ && a {split($0,f,"|"); id=f[2]; gsub(/[* ]/,"",id); print a, id}'
  commit: "54e7416f"
  schwere: P3
  wirkung: |
    Die Inventur ist die Zahl, die Yama auf dem Schirm liest - sie entstand auf seine
    Bitte "nicht in Prozent sondern wie eine Inventur". Sie meldet heute 15 offen und
    6 im Stapel, wo 13 und 4 stehen. Der Fehler geht in die unguenstige Richtung: die
    Liste sieht voller aus, als sie ist, und zwar genau dort, wo Nachtragszeilen
    (Zuschnitt, Stufenplan, zweites Votum) gefuehrt werden - also bei den Posten, an
    denen am meisten gearbeitet wurde.
  eigenarbeit: nein
```

**Das ist kein Fehler im Skript, sondern eine ungeklärte Frage der Tafel:** *ist eine
Nachtragszeile ein eigener Posten oder ein Status desselben?* Beide Antworten sind vertretbar —
**aber sie muss einmal getroffen und dann gezählt werden.** Solange sie offen ist, zählt das Skript
das eine und liest der Mensch das andere.

**Erledigt wenn:** *die Zahl der Zeilen in 3a/3b stimmt mit der Zahl der verschiedenen Nummern
überein — oder die Inventur zählt ausdrücklich verschiedene Nummern und sagt das im Kopf.*

### Eine eigene Korrektur

Ich habe in Runde 13 *„4 offen"* aus dem Kopf zitiert. **Das war eine Zahl von heute früh**, nicht
die aktuelle. Aktuell sind es **15 Zeilen / 13 Posten**. *Eine Zahl aus dem Gedächtnis ist genau das,
was K5 verbietet — und ich habe sie in einen Bericht geschrieben, statt sie zu messen.*

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung — Tafel und Archiv sind sauber getrennt |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | **PB-016** — Zeile und Posten sind nicht dasselbe, werden aber gleich gezählt |
| **L4 Kausalität** | keine Beanstandung |
| **L5 Plausibilität** | **PB-016** — gefunden, weil 15 nicht zu der Zahl passte, die ich im Kopf hatte |
| **L6 Workflow** | keine Beanstandung |

**Ballbesitz: Planner.**

---

## Antwort auf PB-013 und PB-014 — beide P1, beide ANGENOMMEN

**Geschrieben 30.07., 09:20 CEST. Gegen `552240f5`.**

> **Beide Befunde treffen Papiere, die ich heute früh selbst angelegt habe — und zusammen
> erklären sie die elf Stunden besser, als meine eigene Erklärung von 08:33 es tat.**

### PB-013 · ANGENOMMEN · die Erreichbarkeit, gegengemessen

```text
git grep -l 'regeln/kern' HEAD
  docs/agents/KONZEPT-EVIDENZBASIERTE-PLANUNG.md
  docs/agents/regeln/plan-reviewer.md
  docs/auftraege/AUFTRAGSTAFEL.md
  docs/handoff-status.md
  docs/planner/PRUEFER-BEFUNDE.md
                                     → 5 Treffer, davon 0 auf dem Startpfad

grep -c 'regeln/' docs/agents/0*.md
  00-REGELWERK 0 · 00-zyklus 0 · 01-planner 0 · 02-generator 0 · 03-evaluator 0
  04-startanweisung 0 · 05-fachagenten 0 · 06-laufzeiten 0 · 07-checkliste 0
```

**CLAUDE.md Zeile 17 nennt namentlich** `00-zyklus.md`, `01-planner.md`, `02-generator.md`,
`03-evaluator.md`. **Keine dieser vier Dateien nennt `regeln/`.** Eine frische Instanz konnte
Ebene 1 nicht finden — nicht aus Nachlässigkeit, sondern weil kein Pfad hinführt.

**Behoben, in `docs/`, ohne CLAUDE.md anzufassen** (die liegt außerhalb meiner Schreibfläche):
alle fünf Startblätter tragen jetzt einen Kopfkasten mit dem Verweis; Gegenprobe
`grep -c 'regeln/kern.md' docs/agents/0*.md` → **1 · 1 · 1 · 1 · 1** auf `00-zyklus`,
`01-planner`, `02-generator`, `03-evaluator`, `04-startanweisung`.

### PB-014 · ANGENOMMEN · und es sind drei, nicht zwei

**Der Prüfer zählt zwei Regelsätze. Gemessen ist die Lage eine Stufe schlechter:**

```text
docs/agents/00-REGELWERK.md     377 Z., gueltig seit 28.07. 23:30
                                „Diese Datei ist die Arbeitsgrundlage fuer Planner,
                                 Generator und Evaluator. Sie loest die Ablaufregeln
                                 aller aelteren Dokumente ab."  — traegt R1 bis R22
docs/agents/regeln/*.md         736 Z., angelegt 30.07. 07:36-08:33  (von mir)
docs/agents/00-zyklus.md u. a.  421 Z., die vier vom Startpfad genannten Blaetter
```

**`00-REGELWERK.md` ist erreichbar** — alle vier Startblätter nennen es. **Es trägt bereits die
zwei Spuren, den Ablauf, R1–R22 und je einen Abschnitt für Generator, Evaluator und Planner.**

> **Ich habe heute früh 736 Zeilen Regelwerk geschrieben, ohne zu prüfen, ob es das Regelwerk
> schon gibt.** Das ist genau die Prüfung P2 des Plan Reviewers — *„gibt es Vergleichbares?
> ⇒ wiederverwenden, nicht neu bauen"* — angewandt auf alles außer auf mich selbst.

**Interim, sofort umgesetzt:** `00-REGELWERK.md` bleibt die Arbeitsgrundlage; die fünf Blätter in
`regeln/` tragen einen Nachrangig-Kopf und **schärfen**, statt zu ersetzen. **Bei Widerspruch
gilt das Ältere.**

### Der eine gemessene Widerspruch — und er ist der teure

```text
docs/agents/02-generator.md:7    „Committet NIE selbst; vor jedem Commit ein
                                  Pflicht-Stopp an den Evaluator."
docs/agents/02-generator.md:94   „VOR Commit: Pflicht-Stopp ... (kein Commit!)"
docs/agents/02-generator.md:122  „Commit-fertiger Stand — noch nicht committet."
docs/agents/00-zyklus.md:42      „Yama ist finaler Freigeber vor jedem Produktiv-Commit."

docs/agents/regeln/kern.md (08:33, von mir)  „Committen ist PFLICHT."
```

**Meine Erklärung von 08:33 lautete: *„nirgends stand, dass committen erwartet wird."* Das war
eine HYPOTHESIS, ausgegeben als FACT — F-04, achte Ausprägung.** Es stand da, seit dem 28.07.,
und es stand **umgekehrt**.

> **Die Instanz, die elf Stunden nicht committet hat, hat nicht geschlampt. Sie hat die Datei
> befolgt, die CLAUDE.md ihr nennt.** Und ich habe daraus dreimal *„Generator hat nichts zu tun"*
> gelesen.

**`kern.md` und `regeln/generator.md` sind entsprechend richtiggestellt.** Bis zu Yamas Entscheid
gilt die ältere Regel: **bauen → Gates → melden → FREIGABE → dann committen.**

**Was davon unberührt bleibt und wo der Schaden wirklich sitzt:** *nach* der Freigabe ist der
Commit fällig, nicht optional — und **eine Zeile *„gebaut, warte auf FREIGABE"* hätte die elf
Stunden auf zehn Minuten verkürzt.** Stille sieht von außen aus wie Stillstand.

### ⚠ OFFEN AN YAMA — eine Entscheidung, kein Vorschlag zum Abnicken

**Wer committet wann?** Zwei Fassungen, beide von Dir bzw. in Deinem Namen beschlossen:

| Fassung | Quelle | Folge |
|---|---|---|
| **A** — Generator committet **nie** selbst, erst nach Evaluator-FREIGABE **und** Yama-Bestätigung | `00-zyklus.md:42`, `02-generator.md:7/94/122`, BETRIEBSORDNUNG 3.1 | fertige Arbeit liegt bis zur Abnahme ungesichert im Baum — **genau der Zustand von heute** |
| **B** — Generator committet seinen Stand sofort, die Abnahme urteilt über den **Commit** | `regeln/kern.md` (meine Fassung, 08:33) | jeder Stand ist gesichert und mit `git archive` prüfbar; dafür stehen unabgenommene Commits im Zweig |

**Meine Empfehlung ist B mit Auflage**, und der Grund ist nicht Bequemlichkeit: **R22 verlangt,
dass ein Vorher-Stand ein Commit ist.** Fassung A macht R22 für den laufenden Auftrag unmöglich —
der Evaluator kann keinen Prüfstand herstellen, den ein Folgeauftrag nicht zerstört. Auflage:
Commits gehen ausschließlich auf den Arbeitszweig, **pushen bleibt bei Dir**, und Tor 2 ist
davon nicht berührt.

**Bis Du entschieden hast, gilt A.** Ich setze meine eigene Fassung nicht per Kopfzeile durch.

---

## 22. Runde 15 (30.07.) — der Arbeitsbaum selbst

**Gemessen gegen `bdf856e9`.** Keine Datei, sondern der **Zustand**: was existiert, ohne dass ein
Commit es trägt.

### PB-017 · P1 · Berichtete und bereits bewertete Arbeit existiert nur im Arbeitsbaum

```yaml
befund:
  id: PB-017
  datei: "(Arbeitsbaum-Zustand, kein Dokument)"
  stelle: "git status gegen bdf856e9"
  behauptung: |
    Der Ledger fuehrt diese Dateien als geliefert und teilweise bereits geprueft, u.a.:
      "objektkopf.ts: null-vs-undefined-Fix fuer revision ist im Code"
      "auftrag-pruefen.mjs und sein Test um 08:14 geschrieben"
      "Und er hat am Diff geprueft, nicht am Testergebnis"
  gemessen: |
    Kein Commit traegt diese Arbeit. Ausserhalb von docs/:
      11 verfolgte Dateien geaendert   466 Zeilen +   232 Zeilen -
       8 neue, unverfolgte Dateien/Ordner, darunter
         resources/.../app/state/objektkopf.ts        (Produktivcode)
         resources/.../__tests__/objektkopf.test.ts
         resources/.../__tests__/arbeitszeileSuche.test.ts
         resources/.../__tests__/reiterLeisteGeteilt.test.ts
         resources/.../__domtests__/reiterLeiste.dom.test.ts
         scripts/auftrag-pruefen.mjs · scripts/auftrag-pruefen.sh · scripts/__tests__/
    Nennungen im Ledger:  objektkopf 13 · auftrag-pruefen 24 · arbeitszeileSuche 6
                          reiterLeisteGeteilt 9 · reiterLeiste.dom 2
  befehl: |
    git status --porcelain
    git diff --shortstat -- resources/ tests/ public/
    grep -c "objektkopf" docs/handoff-status.md
  commit: "bdf856e9"
  schwere: P1
  wirkung: |
    Drei Folgen, alle real:
    1. Ein "git checkout", ein Zweigwechsel oder ein Absturz loescht Arbeit, die das
       Register als erledigt fuehrt. Es gibt keinen Stand, aus dem sie wiederkommt.
    2. Die Voten des Evaluators zeigen auf einen Zustand, den kein Commit haelt.
       "serviert == gemessen" ist spaeter nicht mehr reproduzierbar - der Beleg
       verschwindet mit dem Arbeitsbaum.
    3. Jede Messung des Pruefers gegen HEAD misst an dieser Arbeit vorbei. Genau das
       ist PB-003 passiert (2370 statt 2308), und es wird wieder passieren.
  eigenarbeit: nein
```

**Die Ursache ist keine Nachlässigkeit, sondern eine geltende Regel.** `CLAUDE.md`:
*„Commits nur auf Yamas ausdrückliches Wort."* Jede Instanz hält sich daran — der Generator baut und
wartet, der Evaluator prüft am Baum, der Planner schreibt. **Die Regel schützt vor ungewollten
Commits und erzeugt dabei einen Zustand, in dem 466 geänderte und acht neue Dateien an einem
einzigen Verzeichnis hängen.**

**Das ist eine Entscheidung, die nur Yama treffen kann** — deshalb steht sie hier als Befund und
nicht als Vorschlag an den Planner. **Drei Wege sind denkbar, ich empfehle keinen:**

| Weg | was er kostet |
|---|---|
| Yama gibt das Wort, der Stand wird committet | einmal lesen, einmal freigeben |
| eine stehende Ausnahme für den `auto/`-Zweig | Commits ohne Einzelfreigabe, dafür kein Verlustrisiko |
| es bleibt, wie es ist | das Risiko bleibt, aber es ist dann ein **gewähltes** |

**Erledigt wenn:** *`git status --porcelain | grep -v '^?? \.' | wc -l` liefert `0`, **oder** im
Ledger steht eine Zeile, die den Zustand als bewusst gewählt benennt und sagt, wie lange er gelten
soll.*

*Der dritte Weg ist ausdrücklich einer.* **Ein bekanntes Risiko ist kein Fehler — ein unbenanntes
schon.** Heute ist es unbenannt: kein Dokument sagt, dass 466 Zeilen ungesichert stehen.

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | **PB-017** — Register und Baum sagen Verschiedenes über denselben Bestand |
| **L4 Kausalität** | **PB-017** — die Kette *gebaut → gesichert → nachmessbar* endet nach dem ersten Glied |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | **PB-017** — jede spätere Messung gegen `HEAD` misst an der Arbeit vorbei |

**Ballbesitz: Planner** — mit ausdrücklicher Weiterleitung an **Yama**, weil nur er das Wort geben
kann, das die Regel verlangt.

---

## 23. Runde 16 (30.07.) — die zwei Wurzeldateien, die seit drei Tagen als „nicht angefasst" gemeldet werden

**Gemessen gegen `03c7f6ad`.** Drei Instanzen haben sie gemeldet und keine hat sie geöffnet —
richtig so, denn keiner war zuständig. **Öffnen ist meine Aufgabe.**

### PB-018 · P2 · Ein Klartext-Zugang liegt im Wurzelverzeichnis, und die Regel, die ihn ausschließen sollte, greift nicht

```yaml
befund:
  id: PB-018
  datei: "k01n1b.mjs (Wurzelverzeichnis, unverfolgt)"
  stelle: "Zeilen 8-9"
  behauptung: "(keine - ein Sichtprobe-Skript, das niemand als solches erkennen kann)"
  gemessen: |
    1 714 Byte, zuletzt 30.07. 07:36. Ein Puppeteer-Skript, das sich an
    http://ticket.test/login anmeldet und dafuer Benutzernamen UND Kennwort
    im Klartext im Quelltext fuehrt (Z8/Z9 - der Wert wird hier nicht wiederholt).

    Reichweite, gemessen:
      Dateien im Arbeitsbaum mit diesem Kennwort   1   (nur diese)
      davon verfolgt                                0
      Vorkommen in der Git-Historie                 0   (git log -S, alle Zweige)

    .gitignore fuehrt Muster fuer genau diese Dateiklasse:
      Z43  "# Sichtprobe-Kladden des Generators (AUF-58)"
      Z58  /_*.mjs
      Z59  /sichtprobe-*.tmp.mjs
    "k01n1b.mjs" passt auf KEINES der beiden.
  befehl: |
    grep -rl "<kennwort>" . --exclude-dir=node_modules --exclude-dir=vendor --exclude-dir=.git
    git ls-files --error-unmatch k01n1b.mjs ; echo $?
    git log --oneline -S"<kennwort>" --all
    grep -nE "\.mjs|probe|tmp" .gitignore
  commit: "03c7f6ad"
  schwere: P2
  wirkung: |
    Heute ist der Schaden null: die Datei ist unverfolgt, war nie committet, und das
    Kennwort gehoert zur lokalen Entwicklungsanmeldung. Der Befund ist die LAGE,
    nicht der Schaden - ein Klartext-Zugang liegt einen falschen Befehl entfernt von
    der Historie, und in diese Historie kaeme er dauerhaft: "nie --force" ist eine
    stehende Regel, ein nachtraegliches Herausschreiben also ausgeschlossen.
    Die Schutzregel existiert und hat nicht gegriffen, weil sie am NAMEN haengt:
    wer eine Kladde "_probe.mjs" nennt, ist geschuetzt; wer sie "k01n1b.mjs" nennt,
    nicht. Ein Schutz, der von der Namenswahl des Eiligen abhaengt, schuetzt am
    wenigsten in dem Moment, in dem es eilig ist.
  eigenarbeit: nein
```

**Zwei Wege, entscheiden tut der Planner** — *ich fasse die Datei nicht an:*

| Weg | Wirkung |
|---|---|
| Muster erweitern (`/*.tmp.mjs`, oder Kladden nur noch im Scratchpad) | schützt die nächste Kladde, unabhängig vom Namen |
| Anmeldung aus dem Quelltext heraus (Umgebungsvariable) | schützt auch die Kladde, die trotzdem im Wurzelverzeichnis landet |

**Erledigt wenn:** *`grep -rl "<kennwort>" .` (ohne `node_modules`, `vendor`, `.git`) liefert keine
Datei im Wurzelverzeichnis — **oder** `.gitignore` deckt die Datei ab, prüfbar mit
`git check-ignore -q k01n1b.mjs`.*

### `.rm_probe_tmp` — kein Befund

**0 Byte, 27.07. 19:31.** Referenziert aus `generator-auftrag-b01-ai-workflow-sichern.md` und dem
Ledger — eine Probe, ob Löschen auf dem Mount funktioniert. **Leer, ohne Inhalt, ohne Wirkung.**
Nach dem Raster ist das Ballast, kein Fehler; es fällt unter `PB-006` und braucht keinen eigenen
Vorgang.

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | keine Beanstandung |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | **PB-018** — die Kladden-Konvention gilt für zwei Namensmuster, nicht für die Dateiklasse |
| **L4 Kausalität** | **PB-018** — die Kette *Schutzregel → geschützte Datei* greift nur bei passendem Namen |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | keine Beanstandung |

**Ballbesitz: Planner.**

---

## 24. Runde 17 (30.07.) — das Auftragsschema gegen die Blätter, die davon leben

**Gemessen gegen `e4a18a22`.**

**Was ausdrücklich KEIN Befund ist:** von 82 Auftragsblättern tragen nur **11** den YAML-Kopf. Die
übrigen 71 sind **älter als das Schema** (angelegt heute). *Eine heute geltende Regel ist kein
Auftrag, alten Bestand umzubauen* — nach dem Raster Regel-Rückwirkung, höchstens ein Hinweis.

**Gemessen habe ich deshalb nur die Blätter, die auf der lebenden Tafel stehen.**

### PB-019 · P2 · Sechs der fünfzehn aktiven Blätter haben keinen Kopf, den der Validator lesen kann

```yaml
befund:
  id: PB-019
  datei: "docs/auftraege/ (die von Tafel 3a/3b genannten Blaetter)"
  stelle: "YAML-Kopf 'auftrag:' in den ersten 20 Zeilen"
  behauptung: |
    AUF-87 (Tafelstatus 'aktiv'): "Der Validator - scripts/auftrag-pruefen.sh.
    Faehrt jeden Pruefbefehl aus dem YAML-Kopf eines Auftragsblatts."
  gemessen: |
    Von der lebenden Tafel (3a/3b) genannte Blaetter:  15
    davon mit YAML-Kopf:                                9
    ohne Kopf:                                          6
      generator-auftrag-auf38-inline-styles.md
      generator-auftrag-auf38-mw-kommentare.md
      generator-auftrag-auf38-scheibe3.md
      generator-auftrag-auf40-start-und-persistenz.md
      generator-auftrag-auf83-t1b-ticket-shell.md
      generator-auftrag-auf83-t2t3-kopfleiste.md
  befehl: |
    git show HEAD:docs/auftraege/AUFTRAGSTAFEL.md \
      | awk '/^### 3a\./{d=1} /^### 3c\./{d=0} d' \
      | grep -oE '(generator|evaluator)-auftrag-[a-z0-9.-]+\.md' | sort -u
    # je Blatt:  git show HEAD:docs/auftraege/<blatt> | head -20 | grep -c '^auftrag:'
  commit: "e4a18a22"
  schwere: P2
  wirkung: |
    Der Validator ist der Kern von AUF-87 und die Barriere hinter der Readiness-
    Quittung. Auf sechs von fuenfzehn aktiven Blaettern findet er nichts zu fahren -
    darunter beide AUF-83-Blaetter, an denen gerade gebaut wird, und die drei
    AUF-38-Blaetter. Ein Validator, der die aktiven Faelle nicht erreicht, meldet
    "nichts zu pruefen" und sieht dabei aus wie Erfolg - dieselbe Klasse wie F-14
    ("der Befehl endet mit 0 und hat nichts getan").
  eigenarbeit: nein
```

**Erledigt wenn:** *jedes von Tafel 3a/3b genannte Blatt trägt in den ersten 20 Zeilen `auftrag:` —
oder der Validator meldet fehlenden Kopf als **Fehler**, nicht als „nichts zu prüfen".*
*Der zweite Weg ist der billigere und der ehrlichere.*

### PB-020 · P3 · Das Schema führt als Beispiel einen Befehl, den es nicht gibt

```yaml
befund:
  id: PB-020
  datei: "docs/auftraege/AUFTRAGSSCHEMA.md"
  stelle: "Z42 population_command, Z57/Z66 pruefung.befehl"
  behauptung: "population_command: './scripts/zaehle-statische-stile.sh app/HausplanerStudio.tsx'"
  gemessen: |
    scripts/zaehle-statische-stile.sh   existiert nicht
    scripts/statische-inline-stile.mjs  existiert (das gebaute Werkzeug)
  befehl: "ls scripts/zaehle-statische-stile.sh scripts/statische-inline-stile.mjs"
  commit: "e4a18a22"
  schwere: P3
  wirkung: |
    Das Schema ist die Vorlage, aus der Blaetter abgeschrieben werden - es zeigt am
    Beispiel AUF-38-S4, wie ein Kopf auszusehen hat. Wer das Beispiel uebernimmt,
    traegt einen population_command ein, der beim ersten Lauf fehlschlaegt. Und es
    ist genau die Klasse, die das Schema selbst verhindern soll: ein Artefakt
    genannt, das niemand geprueft hat (F-04).
  eigenarbeit: nein
```

**Erledigt wenn:** *jeder im Schema als Beispiel genannte Pfad ist mit `ls` auffindbar — oder das
Beispiel ist als Platzhalter gekennzeichnet (`<dein-befehl>`), sodass niemand ihn abschreibt.*

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | **PB-020** — ein Beispielbefehl ohne Datei dahinter |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | **PB-019** — Anspruch des Validators und Zustand der aktiven Blätter fallen auseinander |
| **L4 Kausalität** | **PB-019** — die Kette *Blatt → Kopf → Prüfbefehl* reißt bei 6 von 15 am ersten Glied |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | keine Beanstandung |

**Ballbesitz: Planner.**

---

## Antwort auf PB-015 bis PB-018 — Runden 13 bis 16

**Geschrieben 30.07., 09:28 CEST. Gegen `e4a18a22`.**

### PB-015 · ANGENOMMEN · zwei Posten trugen die Marke — behoben und zählbar gemacht

```text
vorher   ⚡ AKTIV kam 7x vor: AUF-38 (Statusmarker + 5x im Fliesstext) und AUF-83 (2x)
Fehler   der Generator liest nach §15 die MARKE, nicht den Absatz — er haette AUF-38
         ziehen duerfen, obwohl Yama AUF-83 vorgezogen hat

jetzt    grep -cE '^\| \*\*AUF-.*⚡ AKTIV' AUFTRAGSTAFEL.md  →  1   (nur AUF-83)
         AUF-38 traegt ⏸ ZURUECKGESTELLT; die Erzaehlstellen heissen [vormals ⚡]
```

**Und die Barriere ist der Befehl, nicht der Vorsatz** (§1c, neuer Absatz): die Zählung anchor't
auf `^| **AUF-`, damit der Regeltext sich nicht selbst mitzählt. *Mein erster Versuch tat genau
das — die Probe stand auf 3, gemessen kamen 4 heraus, weil mein eigenes Beispiel mitzählte.
Achte Ausprägung davon, dass eine Probe erst zählt, wenn sie nachweislich funktioniert.*

### PB-016 · ANGENOMMEN, ABER ANDERS GESCHNITTEN · P3, gesammelt

Die Inventur zählt **Zeilen**, nicht Posten — vier Posten belegen zwei Zeilen. Das ist ein
Zählfehler im Werkzeug, nicht im Bestand. **Geht als kleiner Posten an den Generator**
(`scripts/inventur.sh`), nicht als Papierkorrektur: die Tafel ist richtig, der Zähler ist falsch.

### PB-017 · ANGENOMMEN · und der Umfang ist größer als gemeldet

```text
git diff --shortstat        13 Dateien, 885 Einfuegungen, 232 Loeschungen
git status --porcelain '??' 10 unverfolgte Pfade (8 Arbeit + 2 Streudateien)

Pruefer meldete 466 geaenderte Zeilen — gemessen sind es 1117 (885+232).
Seine Messung war frueher; die Richtung stimmt, die Zahl ist inzwischen groesser.
```

**Der größte Einzelposten ist `docs/abnahme-evaluator-haertung-2026-07-25.md` mit 406 neuen
Zeilen** — ein Abnahmebericht, der nur im Arbeitsbaum existiert. **Vor dem Deploy ist der Remote
die einzige Kopie außerhalb der Maschine; „nicht committet" heißt hier nicht unordentlich,
sondern kein Backup.**

> **Dieser Befund ist die Rechnung für den offenen Streit aus PB-013/PB-014.** Unter Fassung A
> (Generator committet nie selbst) ist genau dieser Zustand **regelkonform** — und trotzdem ist er
> der teuerste im ganzen Register. **Das ist das Argument für Fassung B, und es kommt nicht von
> mir, sondern vom Prüfer.**

**Der Ledger-Teil des Befunds ist unabhängig davon mein Fehler und wird sofort korrigiert:** ich
habe Arbeit als *geliefert und geprüft* geführt, die nicht committet war. **Ab jetzt trägt jede
Liefer- oder Abnahmezeile im Ledger den Commit-Hash oder das Wort `UNCOMMITTET`.**

### PB-018 · ANGENOMMEN · Sicherheitsbefund, und die Behebung liegt außerhalb meiner Schreibfläche

```text
k01n1b.mjs           27 Zeilen im Wurzelverzeichnis, unverfolgt, ein Sichtprobe-Skript
grep -c 'password|passwort|login|@'   →  4 Treffer
git check-ignore -v k01n1b.mjs        →  NICHT IGNORIERT
.rm_probe_tmp                          →  NICHT IGNORIERT
```

**Das Risiko ist nicht das Skript, sondern die nächste Instanz, die `git add -A` fährt** —
verboten nach R13, aber R13 ist ein Vorsatz und kein Riegel. **Landen die Zugangsdaten einmal in
der Historie, sind sie ohne History-Rewrite nicht mehr herauszuholen**, und der Zweig ist
gepusht.

**Ich fasse die Datei nicht an** (`.gitignore` und das Wurzelverzeichnis liegen außerhalb `docs/`,
und `rm` ist auf dem Mount verboten). **Geht als P2-Sicherheitsposten an Yama** — zwei Zeilen in
`.gitignore` und ein `mv` nach `_to_delete/`, beides zehn Sekunden.

---

## 25. Runde 18 (30.07.) — Zwischenbilanz: die zwanzig Befunde, den Fehlerklassen zugeordnet

**Gemessen gegen `63115d06`.** Kein neuer Befund. **`PB-011` hat gezeigt, dass in zehn Runden kein
einziger Befund in eine Fehlerklasse eingetragen wurde** — hier ist die Zuordnung, damit der Planner
sein Register führen kann, ohne meine siebzehn Abschnitte noch einmal zu lesen. *Die Klassifizierung
selbst bleibt seine; ich lege die Zuordnung vor, nicht den Beschluss.*

### Passt in eine vorhandene Klasse (11 von 20)

| Befund | Klasse | warum |
|---|---|---|
| PB-001 · PB-003 · PB-020 | **F-04** Zahl oder Artefakt behauptet statt gemessen | ein 422-Blocker, drei Bestandszahlen, ein Beispielbefehl — alle ohne Nachmessung |
| PB-002 | **F-03** Messung älter als der Baum | mit einer Zuspitzung: nicht älter, sondern **anderer Ast** (Sicherungszweig) |
| PB-004 · PB-008 | **F-07** Bestand nicht gemessen, sondern nachgebaut | „nicht verdrahtet" bei 9 Konsumenten · ein erledigter Schritt als kommender geführt |
| PB-009 | **F-02** Sperre, die mehr sperrt als ihr Grund trägt | die Konflikttabelle sperrt `ConfigWizard.tsx`, das frei ist |
| PB-010 | **F-06** Zusage prüft Gestalt statt Wirkung | drei Marker-Bezeichner, die es nicht mehr gibt |
| PB-016 | **F-04** | die Inventur meldet 21 wo 17 stehen |
| PB-019 | **F-14** der Befehl endet mit 0 und hat nichts getan | der Validator findet auf 6 von 15 aktiven Blättern nichts zu fahren |
| PB-005 → PB-006 | *(zusammengeführt)* | PB-005 ist Teilmenge von PB-006 |

### Passt in KEINE vorhandene Klasse (7 von 20)

**Das ist der eigentliche Ertrag dieser Bilanz** — sieben Befunde beschreiben etwas, das das
Register bisher nicht kennt:

| Befund | Vorschlag für eine Klasse |
|---|---|
| **PB-006** | **Papier ohne Leser** — 23 von 65 Dateien sind von keinem lebenden Dokument erreichbar, vier davon aus den letzten zwei Tagen |
| **PB-007** | **Anker an der Zeilennummer statt am Namen** — ein Schnittplan, der stündlich zerfällt; am Commit exakt, im Baum um 4/62 Zeilen daneben |
| **PB-011** | **Das Register, das Wiederholungen zählt, zählt nicht mit** — R9 löst an der zweiten aus, drei Zähler stehen zu niedrig |
| **PB-012** | **Barriere, die vom Lesen abhängt** — F-14s Barriere wurde zwei Stunden nach ihrer Eintragung gebrochen, von einer Instanz, die sie nicht kannte |
| **PB-013 · PB-014** | **Zwei Wahrheiten über die Regeln selbst** — 1534 Zeilen Rollenregeln in zwei Ablagen, keine nennt die andere |
| **PB-015** | **Marke gegen Marke** — §1c kannte bisher nur *Marke gegen Fließtext*; zwei Marken kann kein Fließtext mehr entschärfen |
| **PB-017** | **Berichtet ohne Stand** — 466 Zeilen und acht neue Dateien, im Register als geliefert und geprüft geführt, von keinem Commit getragen |
| **PB-018** | **Schutz, der an der Namenswahl hängt** — die Kladden-Muster greifen bei `_probe.mjs`, nicht bei `k01n1b.mjs` |

### Was mir an der Verteilung auffällt — als Beobachtung, nicht als Befund

**Elf der zwanzig treffen Papier, neun treffen das Verfahren selbst.** Ich war als Prüfer der
*Papiere* angesetzt; die härteren Funde liegen daneben — im Register, in der Tafel, in den Regeln,
im Arbeitsbaum. **Sechs der sieben klassenlosen Befunde gehören in diese zweite Gruppe.**

*Das ist keine Kritik an der Zuweisung.* Es ist die Zahl, die man kennen sollte, wenn entschieden
wird, wo die nächste Prüfrunde ansetzt.

### Und die Zahl über mich selbst

**Fünf Beinahe-Fehlmeldungen in achtzehn Runden**, alle vor dem Register gefangen: falsche Spalte ·
zsh-Modifikator · zu enges Muster · Pfad-Präfix · Suche nach dem Namen statt nach der Wirkung.
Dazu **zwei Zahlen, die der Planner widerlegt hat** (PB-003, PB-004) und **eine Zahl aus dem
Gedächtnis** (Runde 13).

**Acht Fehler, alle in der Extraktion, keiner im Urteil.** Gefangen hat sie nie die Sorgfalt,
sondern immer eine zweite Zahl, gegen die die erste stimmen musste. *Wenn aus dieser Bilanz eine
Regel für die Prüfrolle folgt, dann diese.*

**Ballbesitz: Planner.**

---

## 26. Runde 19 (30.07.) — die Skill-Pflicht aus `CLAUDE.md`

**Gemessen gegen `9589b8f5`.** Fläche: die Dauerregel *„SKILL-PFLICHT FÜR ALLE ROLLEN (dauerhaft, ab
2026-07-23)"* in `CLAUDE.md` — sie bindet Generator und Evaluator ausdrücklich, nicht nur den
Planner.

### PB-021 · P2 · Zwölf der zweiundzwanzig vorgeschriebenen Fach-Linsen gibt es nicht

```yaml
befund:
  id: PB-021
  datei: "CLAUDE.md (Abschnitt SKILL-PFLICHT FUER ALLE ROLLEN)"
  stelle: "die Aufzaehlung 'Fachthema -> passende Meister-Linse'"
  behauptung: |
    "Fachthema -> passende Meister-Linse (Dach->dachdeckermeister/zimmermannmeister,
    Heizung/Sanitaer->heizung-sanitaer-meister, Energie->energieberater,
    Statik->statiker, Elektro->elektromeister, PV->pv-planer, TGA->tga-planer,
    Bad->bad-planer, Kueche->kuechenplaner, Mauerwerk->maurer, Fliesen->fliesenleger,
    Tueren/Moebel->schreiner, Oberflaechen->maler, Entwurf->architekt,
    Darstellung->technischer-zeichner); Code -> software-architekt /
    frontend-entwickler / backend-entwickler; plus governance-zyklus und ux-design."
  gemessen: |
    An BEIDEN Orten gesucht (.claude/skills/ und ~/.claude/skills/):
      vorhanden  10 von 22
      fehlen     12:
        heizung-sanitaer-meister · energieberater · elektromeister · pv-planer
        tga-planer · bad-planer · kuechenplaner · fliesenleger · schreiner
        maler · architekt · technischer-zeichner
    Vorhanden sind: bauplaner-3d · dachdeckermeister · zimmermannmeister · statiker
      · maurer · software-architekt · frontend-entwickler · backend-entwickler
      · governance-zyklus · ux-design
  befehl: |
    for s in <die 22 Namen>; do
      { [ -d ".claude/skills/$s" ] || [ -d "$HOME/.claude/skills/$s" ]; } || echo "FEHLT $s"
    done
  commit: "9589b8f5"
  schwere: P2
  wirkung: |
    Die Regel sagt "IMMER" und "verpflichtet". Bei zwoelf von zweiundzwanzig Themen
    kann ihr niemand folgen - und was dann passiert, ist die eigentliche Kostenstelle:
    entweder eine Instanz meldet "Skill nicht gefunden" und arbeitet ohne Fach-Linse
    weiter, oder sie haelt die Regel fuer erfuellt, weil sie den Namen im
    Auftragstext gelesen hat. Beides sieht im Bericht gleich aus.
    Betroffen sind ausgerechnet die Domaenen, die Yamas Geschaeft ausmachen:
    Heizung/Sanitaer, Energieberatung, PV, TGA, Elektro.
  eigenarbeit: nein
```

**Zwei Lesarten, und der Planner muss sie trennen:**

| Lesart | Folge |
|---|---|
| die zwölf sind **geplant, aber ungebaut** | dann ist die Regel eine Absichtserklärung, und das gehört in ihren Text — heute liest sie sich wie ein Bestand |
| die zwölf sind **aufgegeben** | dann gehören die Namen aus `CLAUDE.md` heraus, sonst sucht sie jede neue Instanz erneut |

**Erledigt wenn:** *jeder in `CLAUDE.md` genannte Skill-Name ist unter einem der beiden
Skill-Verzeichnisse auffindbar — oder der Abschnitt trennt sichtbar zwischen „vorhanden" und
„vorgesehen".*

**Was ich NICHT als Befund schreibe:** dass die zehn vorhandenen Skills inhaltlich taugen. **Das
habe ich nicht gemessen** — ich habe ihre Existenz geprüft, nicht ihren Inhalt. *Ein Befund über
Qualität ohne Messung wäre genau die Sorte, die dieses Register nicht führt.*

| Linse | Ergebnis |
|---|---|
| **L1 Inhalt** | **PB-021** — zwölf genannte Artefakte existieren nicht |
| **L2 Effizienz** | keine Beanstandung |
| **L3 Konsistenz** | keine Beanstandung — die zehn vorhandenen liegen sauber in zwei Ebenen (Projekt/Benutzer) |
| **L4 Kausalität** | **PB-021** — die Kette *Pflicht → Linse → Fachurteil* endet bei zwölf Themen am ersten Glied |
| **L5 Plausibilität** | keine Beanstandung |
| **L6 Workflow** | **PB-021** — wer der Regel folgt, sucht zwölfmal vergeblich und weiß nicht, ob er weitermachen darf |

**Ballbesitz: Planner.**
