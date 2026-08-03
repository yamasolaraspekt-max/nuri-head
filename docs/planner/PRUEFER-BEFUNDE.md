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

## 5b. NACHHALTEN — die Regel, die Yama am 30.07. gegen 20:32 gesetzt hat

> **„du sollst ständig die drei daran erinnern, bitte euer Fehler zu beheben, und nicht ständig noch
> mal testen, sondern dahinter sein, bis der Fehler behoben ist."**

**Damit endet meine Aufgabe nicht mit der Meldung.** Ein Befund gilt erst als erledigt, wenn er
**gemessen geschlossen** ist — nicht, wenn er zugestellt wurde.

**Was ich ab jetzt bei jedem Takt tue:**

```text
1. offene Befunde je Adressat zaehlen        (Planner · Generator · Evaluator · Yama)
2. Alter je Befund seit Erstmeldung           (Stunden, nicht "laenger")
3. MAHNUNG ins Ledger, solange etwas offen ist — mit Nummer, Alter, Adressat
4. erst schliessen, wenn ICH die Behebung nachgemessen habe
```

**Was ich NICHT tue:** denselben Befund neu prüfen, um ihn erneut zu belegen. *Er ist belegt. Was
fehlt, ist die Behebung* — und die schuldet nicht die Messung, sondern der Adressat. **Neu gemessen
wird nur die Behauptung „ist behoben".**

**Was ich weiterhin nicht tue:** selbst beheben. *Aufdecken ist nicht beheben* — sonst prüfe ich meine
eigene Arbeit. **Nachhalten ist die dritte Sache neben Messen und Melden, nicht der Ersatz für die
Rollentrennung.**

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
| PB-023 | `ui-bauordnung.md` | **P2** | 175 `hp-`-Klassen, 0 im Styleguide — keine Regressionsfläche; **eigenarbeit an 60** | **ERLEDIGT** (`8d5008f1`) — von mir nachgemessen: 8 Familien, **43 Klassen in echten class-Attributen, 0 ohne Regel**; Feature-Suite 7/115 gruen | Pruefer |
| PB-024 | `ui-bauordnung.md` / `studioDaten.ts` | **P2** | Insel-Palette: 42 Hexwerte, 0 Verweise auf `--sa-` — nicht verdrahtet | **GESCHNITTEN** (`56abee2f`) — selbes Blatt | Generator |
| PB-025 | `ui-bauordnung.md` | P3 | schützt `partials/sa-ui.blade.php` — die Datei gibt es nicht | **WIDERLEGT — mein Fehler** (`9e76e758`): sie liegt unter `admin/layouts/partials/`, ich hatte `resources/views/partials/` geprüft | Prüfer |
| PB-022 | `arbeitskompass-ticket.md` | **P2** | kennt 0 von 9 laufenden Posten; letzte Lage vom 21.07., CLAUDE.md schickt aber dorthin | **ERLEDIGT** (`56abee2f`) — nachgemessen: Datei zuletzt 01.08. 11:12 | Prüfer |
| PB-021 | `CLAUDE.md` (Skill-Pflicht) | **P2** | 12 von 22 vorgeschriebenen Fach-Linsen existieren an keinem der beiden Skill-Orte | **ERLEDIGT** (`56abee2f`) — CLAUDE.md nennt nur noch vorhandene; die 12 stehen als Korrektur namentlich da | Prüfer |
| PB-019 | `docs/auftraege/` (aktive Blätter) | **P2→P3** | Validator **benennt** `KEIN KOPF` (kein F-14) — aber `exit 0` ließe 6 aktive Blätter durch ein Gate | **ERLEDIGT** (`56abee2f`) — Gegenprobe von mir: Blatt ohne Kopf mit `status: aktiv` gibt **EXIT 1**; zwei echte Blätter sperren | Prüfer |
| PB-020 | `AUFTRAGSSCHEMA.md` | P3 | Beispiel nennt `zaehle-statische-stile.sh` — die Datei gibt es nicht | **ERLEDIGT** (`9e76e758`) — beide genannten Skripte existieren, von mir nachgezählt | Prüfer |
| PB-033 | `probe_*_tmp.mjs` | ~~P2~~ | beide Kladden entfernt (3 → 1 Datei) | **ERLEDIGT** | 30.07. 10:0x |
| PB-018 | `.gitignore` (Ursache) | ~~P2~~ | Klasse gedeckelt statt Namen — 5 Probennamen ignoriert, laufende Arbeit sichtbar | **ERLEDIGT** | 30.07. |
| PB-017 | (Arbeitsbaum) | **P1** | 466 geänderte + 8 neue Dateien ungesichert, im Ledger aber als geliefert und geprüft geführt | **ANGENOMMEN — Umfang groesser** | 30.07. 09:28 — gemessen 13 Dateien / 885+232 Zeilen / 10 unverfolgt; Ledger-Korrektur sofort, Sicherung haengt an Yamas A-oder-B-Entscheid |
| PB-016 | `inventur.sh` / `AUFTRAGSTAFEL.md` | P3 | Inventur zählt Zeilen: 21 Zeilen für 17 Posten (vier doppelt) | **ANGENOMMEN, ABER ANDERS GESCHNITTEN** | 30.07. 09:28 — Zaehlfehler im Werkzeug, nicht im Bestand; Posten `scripts/inventur.sh` an den Generator |
| PB-015 | `AUFTRAGSTAFEL.md` | **P2** | zwei Posten tragen `⚡ AKTIV` (5 Vorkommen) — §1c verlangt genau einen | **ANGENOMMEN** | 30.07. 09:28 — AUF-38 traegt ⏸ ZURUECKGESTELLT; Barriere in §1c, Zaehlung ankert auf `^ **AUF- → 1 |
| PB-013 | `docs/agents/regeln/kern.md` | **P1** | „wird IMMER geladen" — vom vorgeschriebenen Startpfad aus mit 0 Verweisen unerreichbar | **ANGENOMMEN** | 30.07. 09:20 — Kopfkasten in allen fuenf Startblaettern; Gegenprobe 5x 1 Treffer |
| PB-014 | `docs/agents/` (Struktur) | **P1** | zwei vollständige Regelsätze für dieselben drei Rollen, 1534 Z., ohne Verweis aufeinander | **ANGENOMMEN — es sind DREI** | 30.07. 09:20 — `00-REGELWERK.md` (377 Z.) ist die Arbeitsgrundlage, `regeln/` nachrangig; **Commit-Zeitpunkt offen an Yama** |
| PB-011 | `FEHLERKLASSEN.md` | **P2** | drei Zähler zu niedrig; in zehn Prüfrunden kein Befund eingetragen | **ERLEDIGT** (`56abee2f`) — F-09 steht auf 8 mit Datum 01.08.; die Zeile führt sich selbst als offen ohne Barriere | Prüfer |
| PB-012 | `FEHLERKLASSEN.md` | **P2** | die Barriere von F-14 ist ein Absatz — nach zwei Stunden gebrochen; R9 verlangt mehr | **ERLEDIGT mit Restnotiz** (`56abee2f`) — drei Regeln, davon nur Punkt 2 (`assert`) wirklich mechanisch; F-15 neu für die durchkommenden Mutationen | Prüfer |
| PB-009 | `bestandsaufnahme-studio-rahmen-2026-07-29.md` | **P2** | Konflikttabelle sperrt eine freie Datei; Anker auf einen Rahmen, der seit T1/T3 umgebaut ist (217→159 Z.) | **ANGENOMMEN** | 30.07. 09:05 — HISTORISCH-Kopf; fuer den Stand gilt die Auftragstafel |
| PB-010 | `stilschicht.test.ts` | P3 | Wirkungs-Zusage prüft gegen 3 tote Bezeichner — **`eigenarbeit: ja`, Urteil beim Evaluator** | **ERLEDIGT** (`81a87923`) — von mir nachgemessen: Liste raus, `istStatisch` entscheidet; die alten Namen stehen nur noch im Begruendungstext (6 Nennungen, 0 in der Regel); Suite 58/0 | Pruefer |
| PB-008 | `generator-auftrag-auf83-t2t3-kopfleiste.md` | P3 | aktives Blatt führt `T1a` als offenen Schritt — Code und Register führen ihn als erledigt (`97a2e2a4`) | **ANGENOMMEN** | 30.07. 09:05 — Blatt traegt HISTORISCH-Kopf, verbindlich sind T2/T3/T3-N1 |
| PB-007 | `zuschnitt-auf48-hausplanerapp-zerlegen.md` | P2 | Schnittkanten als absolute Zeilennummern — am Commit korrekt, im Baum schon 4/62 Zeilen abgewandert | **ANGENOMMEN** | 30.07. 09:05 — Zuschnitt §7: Schnittkanten ueber Namen statt Zeilennummern |
| PB-026 | `docs/agents/regeln/kern.md` | P3 | Z1 sagt „NACHRANGIG", Z11 weiter „IMMER geladen" — zehn Zeilen auseinander | **ERLEDIGT** (`9e76e758`) — Z11/Z12 aufgelöst, Befund im Text genannt | Prüfer |
| PB-027 | `…b01-ai-workflow-sichern.md` | **P2** | Blatt `status: aktiv`, Tafel `⏸ ZURÜCKGESTELLT` (F-08b) | **ERLEDIGT** (`56abee2f`) — Blatt trägt `status: zurueckgestellt` mit Grund | Prüfer |
| PB-028 | `…vorherbilder-und-auf86…md` | P3 | Blatt `status: aktiv`, hat keine Tafelzeile | **ERLEDIGT** (`56abee2f`) — Tafelzeile `EVAL-2026-07-30-A+B` angelegt | Prüfer |
| PB-029 | `~/wissensregister/register.md` | P3 | `CODE-001` zeigt nach `~/Projekte/` — Verzeichnis existiert nicht | offen | — |
| PB-030 | `~/wissensregister/kategorien/` | P3 | acht Verweise mit `…`/`{` abgekürzt, gegen das eigene Schema | offen | — |
| PB-031 | `docs/*.md` (Sammel) | P3 | 68 von 923 genannten Code-Pfaden nicht auffindbar (Papierstopp) | **ERLEDIGT** (`0a588d7b`) — von mir gefahren: **57 von 996**; das Werkzeug ueberspringt jetzt historische Papiere (3 Marken) und Platzhalter. Die vorherige Steigerung kam von den Richtigstellungen selbst | Pruefer |
| PB-032 | (Index) | ~~P1~~ | 7 Dateien gestaged → committet in `40fa52de`, kein Beifang | **ERLEDIGT** | 30.07. 09:53 |
| PB-034 | `bauordnung.md` | P3 | Ist-Belege veraltet **und verschlechtert**: `DB::` 267→338, Klammern 96/387→**75/406** | **ERLEDIGT** (`9e76e758`) — §2.2 trägt jetzt 75/406 mit Messdatum 01.08. | Prüfer |
| PB-035 | `fahrplan-dashboard-versionen.md` | **P2** | führt UI-9 als „❌ 0 Dateien" — Palette hat 191 Z., 2 Tests, 4 Konsumenten (F-07) | **ERLEDIGT** (`9e76e758`) — Zeile führt jetzt ✅ gebaut mit 191 Z./2 Tests | Prüfer |
| PB-036 | `ticket-code-reuse/references/…md` | P3 | 2 von 59 Skill-Pfaden zeigen auf den Vor-Port-Zustand | **ERLEDIGT** (`56abee2f`) — Zeile 54 nennt beide nicht mehr; sie stehen nur noch im Korrekturtext | Prüfer |
| PB-037 | `buehnenBreite.test.ts:23` | ~~P1~~ | Filter auf die Subtraktion geschärft, Rechnung unberührt, Gate 1409/1409 | **ERLEDIGT** | 30.07. |
| PB-038 | (Historie) `fe47879c` | **P2 · SICHERHEIT** | **von mir verursacht**; Weg A ausgeführt, HEAD sauber, Klasse gedeckelt | **GESCHLOSSEN** — Yama hat gewechselt (18:02, `67903953`) | 30.07. |
| PB-040 | `db` + Ledger | Eine gelaufene Migration lag in **0 Commits**; AUF-88-P1 fertig im Baum, kein Bericht | **P2** (Sicherung) + **blockiert die Evaluation** | **ERLEDIGT** (A: `fba60e6e` · B: Ledger 12:28) | — |
| PB-041 | `massnahmenplan-2026-07-30.md` + `FEHLERKLASSEN.md` | M4-Zahlen, F-04-Zelle, `bestand.sh`, `VORLAGE.md` | **P2** | **ERLEDIGT** 22:49 — alle fünf Punkte nachgemessen | — |
| PB-043 | `ChatController.php` + `config/logging.php` | **ERLEDIGT**: Teil 1 (`fddec527`) `Log::info` am `?debug=1`-Schalter · Teil 2 (`9e294323`) Stapel schreibt in `daily`, 14 Tage; ~~zwei unbedingte `Log::info` schreiben 64 086 Zeilen in ein nicht rotierendes Log | **P2** | offen | Planner |
| PB-044 | `--env=testing` ohne `.env.testing` | Ein Schalter, der auf die Test-Umgebung zeigt, traf **stillschweigend die Arbeits-DB** | P3→**P2** (Planner) | **ERLEDIGT** 21:43 — nachgemessen | — |
| PB-045 | mein eigener Messbefehl | `--date=format:` zeigt die Zone des Committers — **45 von 116** heutigen Commits zwei Stunden zu früh | **P3** | **ERLEDIGT** (Barriere gesetzt) | Prüfer |
| PB-039 | `PRUEFER-BEFUNDE.md` (mein Register) | Acht Befunde hatten einen Abschnitt, aber **keine Zeile** — Ursache F-14 (`str.replace` traf nicht) | **P2** | **ERLEDIGT** (38 Zeilen = 38 IDs) | Prüfer |
| PB-042 | (Betrieb) `git log` | **109 Commits heute, 2 davon Produktivcode, 66 von mir** — docs/Code 7:1, mein Register allein 4 265 Z. | **P2** | **ERLEDIGT** — Yama-Entscheidung `18f7d1dc` (3 Regeln: Produktivcode zuerst · Register kippen · Blätter nur gegen Vorfall); Kipp 1 vollzogen 03.08. (5 473→554 Z vorn, Archiv 4 933 Z) | Prüfer |
| PB-046 | Objekt-Planer @ **375 px** (Browser) | 8 Bedienelemente ausserhalb, kein Bildlauf; **Planner: 375 = Ankunfts-, kein Bedienziel** → AUF-91 | **P2→P3** (Begründung geprüft, sie trägt) | **ERLEDIGT nach Ziel** — `154e4867`; Ziel war *sagen*, nicht *bedienbar machen*. Überlauf besteht fort (Beobachtung) | Prüfer |
| PB-047 | `SidebarCountController.php:16,29,162` | `$user?->name` ist ein **String**, der Parameter fordert `?int` — **464 `local.ERROR`**, die Seitenleisten-Zahlen kommen nie an | **P2** | **ERLEDIGT** — `1b0b61a5`, von mir nachgemessen (Runde 635) | Prüfer |
| PB-048 | `resources/views/**` (805 Blades) | **Vorsortierung statt Sichtprobe**: 319 Blades tragen ein Layout-Risikomerkmal, die 18 dichtesten benannt — und mein erstes Muster war zu **90 %** falsch | **Hinweis** (kein Mangel) | offen | Planner |
| PB-049 | `scripts/auftrag-pruefen.mjs` (Erlaubnisliste) | Aus meiner Umgebung **EXIT 0 auf alle drei Fernziele**, auch auf das fremde `upstream` — die Liste schützt nur, was durch das Skript läuft | **P1** | **ERLEDIGT** — `54b2696e`, Grenze steht jetzt in der Liste | Prüfer |
| PB-050 | `bote-auftrag-pw01-…md` | Zwei Zahlen ohne Befehl in einem Blatt, das Zahlen ohne Befehl verbietet | **P3** | offen | Planner |
| PB-051 | `bote-auftrag-pw01-…md` | Die Prämisse des Blattes (der Prüfer erreiche die Fernziele nicht) ist **nicht erfüllt** — gemessen | **P1** | offen | Planner |
| PB-052 | `bote-auftrag-pw01-…md` | Alle sechs Kriterien messen gegen eine **Fernreferenz**, die veraltet sein kann | **P2** | offen | Planner |
| PB-053 | `.git/index` (Arbeitskopie) | **Der Index stand 3 Commits zurück** (Blob-Stand `fb0ee00d`, 01:27) — ein `git commit` ohne `-a` hätte **213→365 Zeilen gelöscht** | **P1** | **ERLEDIGT** 03.08. 07:58, von mir nachgemessen: `diff --cached --stat` leer, Index==HEAD==Baum (987/935/37507), **nichts verloren** | Prüfer |
| PB-054 | `docs/handoff-status.md` (Überschriften) + Commit-Zonen | **KORRIGIERT 08:1x**: nichts ist rückdatiert, ich hatte die Anzeige für die Uhr gehalten. Rest: eine Instanz legt Commits mit `Z` statt `+02:00` ab; Ledger-Überschriften nennen Zahlen ohne Messung | **P2→P3** | offen | Planner |
| PB-055 | `.git/index` (Arbeitskopie) | **WIEDERHOLUNG von PB-053, schärfer**: Index steht auf `5df61a37` (08:48), HEAD ist `b4cbcf23` — ein `git commit` würde **1 685 Zeilen löschen, darunter den frisch gebauten Z-06-N1-Code** (`freigabe.ts` 128 Z, sein Test 227 Z als `D` gestaged) | **P1** | offen — **R9: zweite Wiederholung, jetzt ist eine technische Barriere fällig** | **Yama** (Index) + Planner (Barriere) |

---


---

> **Gekippt am 03.08.2026 (PB-042 (b), erster Kipp durch den Prüfer).** Die Protokolle der
> Runden 1–n und aller GESCHLOSSENEN Befunde stehen in
> [`PRUEFER-BEFUNDE-ARCHIV-2026-07.md`](PRUEFER-BEFUNDE-ARCHIV-2026-07.md). Vorn stehen nur
> die Registertabelle (vollständig) und die Abschnitte der OFFENEN Befunde.

### PB-029 · P3 · Ein Register-Eintrag zeigt in ein Verzeichnis, das es nicht gibt

```yaml
befund:
  id: PB-029
  datei: "~/wissensregister/register.md"
  stelle: "Z35, Eintrag CODE-001"
  behauptung: "CODE-001 | ~/Projekte/altcrm/app/HeatCalc.php | Code |
    PHP-Heizlast-Rechner, DIN-nah, reine Funktion"
  gemessen: |
    ~/Projekte/            existiert nicht
    ~/Downloads ~/Documents ~/Desktop   existieren
    Verweise auf ~/Projekte im ganzen Register:  1  (dieser)
  befehl: "ls -d ~/Projekte ; grep -rn '~/Projekte' ~/wissensregister/"
  commit: "9ea619be"
  schwere: P3
  wirkung: |
    CLAUDE.md schickt vor jeder neuen Aufgabe ins Register und sagt: "Passende
    Eintraege -> Originaldatei bei Bedarf voll lesen; so fliesst vorhandenes Wissen
    in neue Arbeit ein statt neu erfunden zu werden." Genau dieser Eintrag ist ein
    Heizlast-Rechner - und die Heizlast-Uebernahme ist ein laufender Strang
    (wberechnung, Phase 1.4). Wer ihn sucht, findet die Zeile und nicht die Datei.
    Ein Verweis-Index mit einem toten Verweis kostet nicht viel; er kostet
    Vertrauen in die uebrigen 58.
  eigenarbeit: nein
```

### PB-030 · P3 · Acht Verweise sind mit `…` oder `{` abgekürzt und damit nicht nachfahrbar

```yaml
befund:
  id: PB-030
  datei: "~/wissensregister/kategorien/*.md"
  stelle: "die Spalte 'Datei' mehrerer Eintraege"
  behauptung: |
    register.md Z17: "Datei | Dateiname + absoluter Pfad (Verweis, keine Kopie)"
  gemessen: |
    Vorkommen von "…" in kategorien/:  8
    Vorkommen von "{"  in kategorien/:  8
    Beispiele der Form (Pfade gekuerzt wiedergegeben, keine Inhalte):
      ~/Downloads/{claude_code_prompt_media...     Sammelklammer statt Einzelpfad
      ~/Downloads/Gemini_Generated_Image_c6h7occ…png   Auslassung mitten im Namen
      ~/Desktop/_Normen_SHK/…                      Verzeichnis ohne Datei
  befehl: |
    grep -roc '…' ~/wissensregister/kategorien/*.md
    grep -roc '{'  ~/wissensregister/kategorien/*.md
  commit: "9ea619be"
  schwere: P3
  wirkung: |
    Das eigene Schema verlangt "absoluter Pfad". Eine Auslassung im Dateinamen ist
    kein Pfad, sondern eine Beschreibung - man kann sie lesen, aber nicht oeffnen.
    Fuer den Planner-Agenten, den CLAUDE.md ausdruecklich auf dieses Register
    verweist, heisst das: acht Eintraege sind Hinweise, keine Verweise.
  eigenarbeit: nein
```

## 66. Runde 61 — **PB-043 · P2: 212 MB Log, davon 64 086 Zeilen aus zwei Debug-Zeilen im Chat-Polling**

**Gemessen 12:13 CEST gegen `6b8a7fa0`.** *Gefunden nicht in einem Papier, sondern weil ich nachsah,
ob überhaupt noch etwas im Haus läuft — das Laravel-Log war die einzige Spur.*

```text
ls -lh storage/logs/laravel.log                      ->  212M   (eine Datei)
grep -c 'getEmployeesAndGroups' storage/logs/laravel.log  ->  64 086
grep -c 'local.ERROR' ...                            ->     404   (davon heute: 0)
Zeilen in 12:00-12:11                                ->      36   = laufender Betrieb

app/Http/Controllers/Chat/ChatController.php:70   Log::info('[Chat] ... IN',  [auth_user_id, auth_employee_id, customer_id])
app/Http/Controllers/Chat/ChatController.php:281  Log::info('[Chat] ... OUT', [employees_returned, groups_returned])
   -> beide unbedingt, kein `config('app.debug')`, kein Level-Gate

config/logging.php:21   'default' => env('LOG_CHANNEL', 'stack')
config/logging.php:57   'stack' => channels: ['single']        <- eine Datei, keine Rotation
config/logging.php:68   'daily' => days: 14                    <- existiert, ist aber nicht die Vorgabe
```

**Der Endpunkt wird vom Frontend gepollt, und jeder Aufruf schreibt zwei Zeilen.** 64 086 Zeilen aus
zwei Anweisungen — **das ist keine Protokollierung, das ist ein Nebenprodukt.**

## 75. Runde 234 — **PB-043 Teil 1 erledigt, nachgemessen. Und mein Zähler stand zu niedrig.**

**21:49 CEST (`date`), Commit `fddec527`.**

```text
ChatController.php:78   $debug = $request->boolean('debug');
                :80     if ($debug) { Log::debug('[Chat] … IN',  …) }
                :115                  Log::debug('[Chat] employees fetched', …)
                :299                  Log::debug('[Chat] … OUT', …)
Umfang: 45 Einfuegungen / 21 Loeschungen · committet 21:48
```

**Nicht gelöscht, sondern an eine Bedingung gehängt, die es dort schon gab** — denselben
`?debug=1`-Schalter, den die Antwort ohnehin auswertet. *Wer die Diagnose braucht, bekommt sie; wer
pollt, erzeugt keine Zeile.* **Das ist Reuse statt Löschen und besser als das, was mein Befund
verlangt hat** — ich hatte nur „unbedingt" beanstandet, nicht gesagt, wohin damit.

### Und was es über PB-043 sagt

**Ich habe heute Mittag geschrieben: „404 Fehlermeldungen liegen zwischen 64 086 Poll-Zeilen
begraben."** *Hier ist einer davon — 464 Mal, seit dem 07.07., in einem Log, das niemand liest, weil es
229 MB gross ist.* **`PB-043` war nie ein Aufräum-Befund. Er war der Grund, warum dieser hier 24 Tage
unentdeckt blieb.**

**Ballbesitz: Planner.**

---

## 78. Runde 435 — **PB-048: die 805 Blades vorsortiert. Und 90 % meiner ersten Messung waren Unsinn.**

**Gemessen 07:5x CEST gegen `fcde5afc`.** *Umsetzung des Planner-Vorschlags von 00:42: die Fläche
statisch vorsortieren, damit eine Instanz die dreißig riskantesten ansieht statt achthundertfünf.*

## 79. Runde 437 — **PB-043 vollständig erledigt. Der letzte technische Befund ist geschlossen.**

**Nachgemessen 08:01 CEST gegen `9e294323`.**

```text
config/logging.php:70   'channels' => ['daily']      (war ['single'])
config/logging.php:81   'daily' => …  :85  'days' => 14
Commit 9e294323 (07:59)  14 Einfuegungen, 1 Loeschung — eine Zeile Wirkung, dreizehn Zeilen Begruendung
```

**Der Kommentar im Code nennt die Messung, nicht die Absicht:** *„Vorher stand hier `['single']`: EINE
Datei ohne Boden. Gemessen waren das 229 327 818 Bytes (219 MB), in denen 2 099 Fehlermeldungen
begraben lagen."*

> **Er hat meine Zahl übernommen und weitergezählt:** ich hatte 2 054 Fehler gemeldet, er misst 2 099.
> *Der Unterschied sind die 45, die seit meiner Messung dazugekommen sind — darunter die 14
> Sidebar-Fehler aus `PB-047`.* **Eine Zahl, die zwischen zwei Messungen wächst, ist ein Beleg, kein
> Widerspruch.**

### PB-050 · P3 · **Zwei Zahlen ohne Befehl in einem Dokument, das Zahlen ohne Befehl verbietet**

`docs/STAND.md:15` („3 Merges") und Abschnitt 3 („55 Commits") · **Erledigt wenn:** beide Zahlen
tragen den Befehl, mit dem sie entstanden sind — oder sie sind entfernt.
**Ballbesitz: Planner.**

---

**P-01 ist damit in allen vier Teilen gefahren.** *Teil 0 hat einen P1 erzeugt (PB-049), Teil 1+2
keinen Befund gegen den Bestand, Teil 3 einen P3 (PB-050).* **Repariert habe ich nichts** (P-01-05).

## PB-054 · P2 · **Die Uhrzeiten im Ledger gehen neun Stunden vor**

**gemessen:**

```
befehl: git --no-optional-locks log -1 --format='%ad' --date=format:'%d.%m. %H:%M' <sha>
  77cc72f7  ->  03.08. 02:48      Ledger-Ueberschrift dazu: "Generator, 03.08. 12:0x"
  6ae8e266  ->  03.08. 02:45      Ledger-Ueberschrift dazu: "Generator, 03.08. 11:5x"
  Systemzeit beim Messen: 2026-08-03 07:09:59 CEST
```

**wirkung:** Das Ledger ist die Buchführung. Wer darin nach „was lag wie lange" sucht — und genau das
ist meine stehende Aufgabe (NACHHALTEN) — bekommt Alter, die um neun Stunden daneben liegen. Ein
Posten, der laut Ledger „vor zehn Minuten" kam, ist in Wahrheit neun Stunden alt. Das ist dieselbe
Klasse wie die Zeitzonen-Korrektur vom 02.08. (`a7b7ec33`), nur an einer anderen Stelle: dort die
Messung, hier die Überschrift.

**erledigt wenn:** eine Ledger-Überschrift und die Commit-Zeit ihres Commits liegen **unter 60 Minuten**
auseinander.

**Ballbesitz: Planner.**

### PB-054 · Nachtrag 03.08. 07:5x — **die Reihenfolge der Geschichte ist nicht die Reihenfolge der Zeit**

Der erste Beleg war eine Überschrift gegen einen Commit. Dieser hier ist härter, weil er **nur mit
git** auskommt und **meine eigenen Taktzeilen** als Zeugen hat.

```
befehl: git --no-optional-locks reflog --date=format:'%d.%m %H:%M' --format='%gd %gs' -6
  HEAD@{03.08 07:52} commit: evaluator: Schlangen-Abgleich 2 …
  HEAD@{03.08 05:50} commit: planner: fuenf gebaute Blaetter eingetragen …     <-- dazwischen
  HEAD@{03.08 07:15} commit: evaluator: zweiter Stapel abgenommen …
  HEAD@{03.08 02:50} commit: PB-049 eingearbeitet …
```

Das Reflog ist **anhängend geführt** — die Reihenfolge der Zeilen ist die Reihenfolge der Ereignisse.
`3da947c8` steht **zwischen** 07:15 und 07:52, stempelt aber **05:50**. Autor- und Commit-Zeit sind
beide 05:50 (`git log -3 --format='%h %ad %cd'`), es ist also kein nachträglich gesetztes Autordatum,
sondern die Uhr der schreibenden Instanz.

**Zweiter, unabhängiger Zeuge:** meine Taktzeile um **07:08** meldete `HEAD 54b2696e · letzter Commit
vor 253 min`. Ein Commit von 05:50 hätte dort stehen müssen. Er stand nicht.

**wirkung:** `git log --since=…`, jedes „was lag wie lange", jede Reihenfolge-Aussage über wer worauf
geantwortet hat, ist unzuverlässig. Zwei Symptome, zwei Richtungen: das Ledger schreibt **+9 h**
(Generator-Überschriften), dieser Commit **−85 min**. Gemeinsam ist nur: **die Uhren in diesem Haus
gehen nicht gleich.**

**erledigt wenn:** die Reflog-Zeitstempel dreier aufeinanderfolgender Commits **monoton steigen**.

### PB-054 · **KORREKTUR 03.08. 08:1x — meine Diagnose war falsch, der Befund schrumpft**

Ich habe zweimal geschrieben, Commits seien **rückdatiert** und „die Uhren in diesem Haus gehen nicht
gleich". **Das stimmt nicht.** Der Grund, warum ich es glaubte: `git log --date=format:` zeigt jede
Zeit in **der Zeitzone, die im Commit steht** — nicht in Ortszeit. Ich habe die Anzeige für die Uhr
gehalten.

```
befehl: git --no-optional-locks log -8 --format='%h %cI  %cd' --date=format-local:'%d.%m %H:%M'

  1de3e240  2026-08-03T06:13:56Z         ortszeit 08:13
  8d9036a0  2026-08-03T06:06:57Z         ortszeit 08:06
  bda8672d  2026-08-03T08:04:54+02:00    ortszeit 08:04
  e19a6b17  2026-08-03T07:59:55+02:00    ortszeit 07:59
  ba275c23  2026-08-03T05:55:48Z         ortszeit 07:55
  fd0151b4  2026-08-03T07:52:54+02:00    ortszeit 07:52
  3da947c8  2026-08-03T05:50:20Z         ortszeit 07:50
  9610f5e5  2026-08-03T07:15:26+02:00    ortszeit 07:15
```

**In Ortszeit ist alles lückenlos monoton:** 07:15 · 07:50 · 07:52 · 07:55 · 07:59 · 08:04 · 08:06 ·
08:13. **Kein Commit ist rückdatiert. Keine Uhr geht falsch. Die Reihenfolge der Geschichte IST die
Reihenfolge der Zeit.** Auch die Reflog-Zeilen, die ich als Beweis angeführt habe, waren derselbe
Anzeigefehler.

**Was übrig bleibt — und es bleibt etwas:** eine der schreibenden Instanzen legt ihre Commits mit
**`Z` (UTC) statt `+02:00`** ab (`1de3e240`, `8d9036a0`, `ba275c23`, `3da947c8` — alle vier vom
Planner; alle Commits mit `+02:00` sind von Evaluator und mir). **Der Zeitstempel ist richtig, die
gespeicherte Zonenangabe nicht.** Wirkung: jede Anzeige ohne `-local` steht bei diesen Commits zwei
Stunden daneben — genau der Fehler, den ich gerade selbst gemacht habe. Er kostet keinen Datenwert,
aber er kostet jeden, der Alter aus `git log` abliest, zwei Stunden Irrtum. Von P2 auf **P3**.

**erledigt wenn:** `git log -10 --format='%cI'` zeigt für alle zehn Commits `+02:00`.

*Der zweite Teil von PB-054 (Ledger-Überschriften „12:0x" für einen Commit von 02:48) ist davon
unberührt und bleibt bestehen — das ist keine Zeitzone, das sind Zahlen ohne Messung.*

---

## PB-055 · P1 · **PB-053 ist zurück — und diesmal hält der Index den Engpass-Code als Löschung fest**

Heute früh (PB-053) stand der Index drei Commits zurück und hätte 365 docs-Zeilen gelöscht; er wurde
still geräumt. **Jetzt, fünf Stunden später, dasselbe Muster — aber der Inhalt ist Produktivcode:**

```
befehl: git --no-optional-locks status --porcelain | grep -E '^(D|\?\?)'
  D  resources/planner/hausplaner/__tests__/herkunftUndFreigabe.test.ts
  D  resources/planner/hausplaner/geometry/freigabe.ts
  ?? (dieselben zwei Pfade als untracked — Datei da, Index sagt: löschen)

befehl: git --no-optional-locks diff --cached --stat | tail -1
  38 files changed, 113 insertions(+), 1685 deletions(-)

befehl: git rev-parse :…/domain/validation.ts  -> Blob == Commit 5df61a37 (08:48)
        HEAD ist b4cbcf23 (09:09) · .git/index zuletzt geschrieben 09:14
```

**Die Dateien selbst sind unversehrt** (`freigabe.ts` 128/128 Zeilen, Test 227/227 gegen HEAD). Aber
wer jetzt `git commit` ohne `-a` fährt, **löscht den ganzen Z-06-N1-Bau aus `6d93fc97`** — die
Sperre, die B10 trägt, ihren Test, und setzt 36 weitere Dateien auf den Stand von 08:48.

**Das Muster dahinter:** eine Instanz refresht den Index (mtime 09:14 — NACH dem letzten Commit),
ohne zu staggen, was sie meint. Zweimal am selben Tag. **R9 greift: bei der zweiten Wiederholung
ist eine technische Barriere fällig, kein Vorsatz.** Der Ort ist benannt (PB-053):
`scripts/commit-pruefen.sh` prüft je Pfad `diff --quiet`, aber nie *ob der Index hinter HEAD liegt*.
Eine Zeile der Form „`git diff --cached --numstat | awk '{d+=$2}'` > Schwelle ⇒ STOPP mit Meldung"
würde beide Vorfälle gefangen haben. **Wie sie aussieht, entscheidet der Planner — nicht ich.**

**erledigt wenn:** (a) `git diff --cached --stat` gegen HEAD leer ist UND (b) das Commit-Tor einen
Index-hinter-HEAD-Zustand nachweisbar rot meldet (Probe mit absichtlich zurückgesetztem Index).

**Ballbesitz: Yama (Index räumen — schreibender Eingriff) · Planner (Barriere schneiden).**

---

# P-01 — Befehls-Inventur: ABSCHLUSS (03.08. 09:2x, HEAD `b4b26282`)

**Auftrag `bereit` seit 01.08. 22:3x.** Teil 0 wurde am 02.08. geliefert (PB-049: aus meiner
Umgebung EXIT 0 auf alle drei Fernziele, auch das fremde `upstream`; die Antwort auf „welche
Umgebung kann pushen" lautet: **meine** — und daraus wurde die stehende Push-Vertretung PW-02).
Hier die restlichen Teile. **Population am eigenen Zeitpunkt gemessen — die Blätter sind seit dem
Schnitt des Auftrags in Strang-Verzeichnisse umgezogen (W-08), das Blatt sagt `docs/auftraege/*.md`
flach (142 Treffer), gemessen wird rekursiv:**

### PB-055 · Nachtrag 09:3x — **die Ursache steht im Tor selbst, mein „jemand refresht" war falsch**

`scripts/commit-pruefen.sh`, Kopfkommentar Stufe 5: *„Der Index liegt AUSSERHALB des Mounts
($TMPDIR, je Prozess eigener Pfad) … ⚠ PREIS, ehrlich benannt: der STAGING-Zustand ueberlebt den
Sitzungswechsel nicht."* **Alle Tor-Commits laufen über einen Wegwerf-Index; der geteilte
`.git/index` wird von ihnen nie fortgeschrieben.** Darum zeigt er 08:48 (den letzten Nicht-Tor-
Stand) und hält jede seither via Tor gebaute Datei als `D`. Das ist kein fremder Eingriff, sondern
der benannte Preis der Lock-Vermeidung — **mein „eine Instanz refresht den Index" ist zurückgenommen.**

**Was bleibt, ist die Falle:** der Preis-Kommentar sagt „hier wird ohnehin mit ausdrücklichen Pfaden
committet" — aber der geteilte Index ist für jeden **nackten** `git commit` weiterhin scharf
(heute: 1 787+ Löschzeilen, inkl. Z-06-N1-Bau). Die R9-Barriere gehört also nicht ins Tor (das ist
sauber), sondern **gegen den Weg am Tor vorbei** — und der dritte `D`-Fall (`z07`-Blatt, 09:2x)
zeigt, dass der Weg begangen wird. Bleibt P1, Zuschnitt beim Planner.

