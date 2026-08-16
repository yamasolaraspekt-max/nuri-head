# MASTER-CHECKLISTE — Umstellung auf getrennte Worktrees

> **Verbindliche Master-Checkliste** für Yamas §1-Entscheidung vom 14.08.2026.
> **Kein Punkt darf still entfernt werden.** Entfällt einer, bleibt er mit Begründung erhalten.
> **Eine allgemeine Aussage wie „alles berücksichtigt" ist verboten** — Vollständigkeit wird nur
> über einzeln abgehakte und belegte Punkte nachgewiesen.

| Feld | Wert |
|---|---|
| **Forensischer Ausgangs-SHA** | `36e600308890ba600757162cfab7a9903f3393bb` |
| festgeschrieben | 14.08.2026, unmittelbar nach der Messung |
| **Planner-Worktree** | `/Users/yamanuri/Documents/ticket-rolle-planner` |
| **Planner-Branch** | `rolle/planner` |
| Gemeinsamer Checkout | `/Users/yamanuri/Documents/ticket` · `auto/hausplaner-integration` |
| **FORENSISCHER_SHA** | **`36e600308890ba600757162cfab7a9903f3393bb`** — unveränderlicher Bezug für Ursachen- und Rückprüfung. **Nicht** der Arbeits-SHA. |
| **AKTIVIERUNGS_SHA** | **ENTFÄLLT als Konstrukt** *(14.08. 22:20)*. Er setzte einen Stand voraus, der stillsteht — den gab es an keinem Punkt dieses Tages. Ersetzt durch **den Umzugs-SHA je Rolle**; die vier Worktrees stehen auf `bc2125d9`. |
| **Status der Umstellung** | **`UMSTELLUNG_LAEUFT`** — **rollender Umzug seit 14.08. 22:20.** Fünf Worktrees stehen; es fehlt die Zustellung an die vier Rollen (`P2H-04`). |

**Zulässige Statuswerte — und ausschließlich diese:** `OFFEN` · `BLOCKIERT` ·
`UMGESETZT_UNGEPRUEFT` · `NACHBESSERN` · `UNABHAENGIG_BESTAETIGT` · `ENTFÄLLT_MIT_BEGRUENDUNG`

**`IN_ARBEIT` ist hier KEIN zulässiger Wert und stand irrtümlich in dieser Liste.** Es ist ein
**§3-Zustand für Aufträge**, nicht für Checklistenpunkte. **Das war die Wurzel des PAR-01-Fehlers:**
die Zeile trug `IN_ARBEIT`, weil diese Definition es erlaubte. Yamas Anordnung vom 14.08.:
*„Die Master-Checkliste verwendet ausschließlich ihre eigenen Statuswerte. §3-Zustände wie
IN_ARBEIT gehören nicht in dieselbe Statusspalte."* **Prüfbar:** kein §3-Zustandswort darf in
**fetter** Schreibweise in einer Punktzeile stehen (Messbefehl am Dateiende).

**`UNABHAENGIG_BESTAETIGT` darf nur stehen, wenn ein anderer Prüfer den exakten Umsetzungscommit
geprüft und den tatsächlichen Beleg eingetragen hat.**

---

## ⚠ V-BLOCK — Vorbelastung: A-36 widerspricht dieser Entscheidung

## YAMAS ENTSCHEIDUNGEN — 14.08., verbindlich nach §1

| Kennung | Entschieden | Inhalt |
|---|---|---|
| **B-1** | **JA — Schreibstopp** | Plan-Prüfer, Generator, Evaluator, Release-Prüfer schreiben **nicht mehr** in `/Users/yamanuri/Documents/ticket`. Verboten: Dateiänderung · Staging · Commit · Statusübergang · Lock-Räumen · Merge/Rebase/Reset. **Lesende Messungen bleiben erlaubt.** |
| **B-2** | **eigener sechster Agent** | Rollenmarke **`TICKET_ROLLE=integrator`**. Alleiniger Schreiber von `docs/STATUS.md`. Integriert freigegebene Rollencommits **einzeln**. Trifft **keine** fachlichen Entscheidungen, verändert **keine** Kriterien oder Prüfergebnisse, löst **keine** Konflikte still. **Darf für denselben Vorgang weder Evaluator noch Release-Prüfer sein.** Push/`main` nur nach weiterer ausdrücklicher Freigabe. |
| **V-02** | **A-36 wird zurückgezogen** | Als **eigenständiger Auftrag** zurückgezogen. Grund: A-36 erklärt die Worktree-Trennung zum **Nicht-Ziel** (Blattzeile 78) und widerspricht damit der späteren, verbindlichen Entscheidung; **ein nur meldender Hunk-Wächter verhindert Richtung B nicht.** |

**Der bisherige gemeinsame Checkout wird erst NACH belegtem Schreibstopp zum exklusiven
Integrations-Checkout.**

### Was aus A-36 erhalten bleibt — P2D untergeordnet, nicht verworfen

| aus A-36 | wohin |
|---|---|
| Hunk-Erkennung | **P2D** — ergänzende Diagnose |
| Verschärfung von §14 | **P2D** — Kontrollmechanismus |
| historische Positivproben *(die drei Nacht-Beifänge)* | **P2D** — Nachweismittel |
| Unterscheidung Dateiliste / `--numstat` / **tatsächlicher Diff-Inhalt** | **P2D** — bleibt als Handgriff |

**Die eigentliche Barriere sind getrennte Worktrees und ein einzelner Integrationsschreiber.**
A-36s Teile sind Diagnose, nicht Schutz. **Kein History-Rewrite:** die Rücknahme wird als **neuer**
Vorgang dokumentiert, bereits committete Inhalte bleiben stehen. **Den Statusübergang in
`docs/STATUS.md` vollzieht ausschließlich der Integrator.**

**Was dabei nicht verloren geht, und es ist unmittelbar zu bergen:** Der Plan-Prüfer hat A-36s drei
fehlende §5-Formalien um **09:35** vollständig gemessen (`774854ef`) — Scope ohne Route/Oberfläche/
Serverprozess, null Migrationen, Abhängigkeit auf genau zwei Dinge, und beim Bundle hat er eine
eigene schlechte Messung verworfen und richtig neu gefahren (`vite build` liest kein `scripts/*.sh`).
**Diese Messung gilt weiter für die P2D-Teile.** Er wartete auf drei Sätze von mir und hätte
**ohne weitere Runde DoR erteilt** — für einen Auftrag, der in derselben Minute zurückgezogen wurde.
**Ihn erreicht das nur über Yama; ich schreibe nicht in den Integrations-Checkout.**



| ID | Beschreibung | Rolle | Dateien | Erwartet | Rot-Beleg | Grün-Beleg | Commit | Prüf-SHA | Status | Abweichung |
|---|---|---|---|---|---|---|---|---|---|---|
| V-01 | **A-36 erklärt die Worktree-Lösung zum Nicht-Ziel und ist damit überholt** | Planner | `docs/auftraege/aktiv/A-36-*.md:78` | Widerspruch aufgelöst | *gemessen 14.08.: Zeile 78 sagt „Yama hat sie ausdrücklich **nicht** entschieden"* | — | — | — | **UMGESETZT_UNGEPRUEFT** | **Aufgelöst durch V-02:** A-36 ist zurückgezogen, der Widerspruch besteht nicht mehr. |
| V-02 | **Entscheidung: A-36 zurückziehen (`SPEC_BLOCKED`) oder als P2D-Teilschritt unterordnen** | Planner | A-36-Blatt, `docs/STATUS.md` | eine benannte Wahl | A-36 steht auf `ENTWURF`, DoR nicht erteilt, vier Punkte fehlen | — | — | — | **UMGESETZT_UNGEPRUEFT** | **ENTSCHIEDEN von Yama, 14.08.: A-36 wird als eigenständiger Auftrag ZURÜCKGEZOGEN.** Verwertbare Teile P2D untergeordnet. Statusübergang in `docs/STATUS.md` **nur durch den Integrator**. |
| V-03 | A-36s vier fehlende DoR-Punkte (Testdaten/Rolle/Route/Browserpfad · API/Schema/Migration/Bundle · Erstnutzer · Abhängigkeitskette) | Planner | A-36-Blatt | ergänzt **oder** entfällt mit V-02 | Befund des Plan-Prüfers in `docs/STATUS.md` | — | — | — | **ENTFÄLLT_MIT_BEGRUENDUNG** | **Gegenstandslos mit der Rücknahme.** Die Messung des Plan-Prüfers (`774854ef`) erledigte drei der vier Punkte; der vierte (**Erstnutzer**) ist eine Festlegung, keine Messung. **Für einen zurückgezogenen Auftrag wird keine DoR erteilt.** Die Messung gilt für die P2D-Teile weiter. |
| V-04 | **A-36-3 ist an einem Wort unerfüllbar** — es verlangt „mehr als eine KENNUNG", richtig ist „mehr als ein ABSCHNITT" | Planner | A-36-Blatt, Kriterium A-36-3 | ein Wort getauscht | **gemeldet `902c83f3`, nachgerechnet: bei `93960252` trägt KEINER der zwei berührten Abschnitte eine Kennung** — über Abschnitte gezählt zwei (grün), über Kennungen ein Wert (**rot und durch keinen Bau behebbar**) | — | — | — | **ENTFÄLLT_MIT_BEGRUENDUNG** | **Gegenstandslos mit der Rücknahme** — A-36-3 wird nicht gebaut. **Die Sache wandert nach `P2D-05`:** ein Wächter, der „mehr als eine KENNUNG" verlangt, misst etwas anderes als „mehr als einen ABSCHNITT". |

**⛔ HISTORISCH / ÜBERHOLT — der folgende Absatz ist NICHT mehr maßgeblich.**
**Maßgeblich ist Yamas Entscheidung V-02 vom 14.08.: A-36 wird als eigenständiger Punkt
ZURÜCKGEZOGEN; verwertbare Teile werden P2D untergeordnet.** Der Absatz bleibt als Beleg stehen
(A-20-4: einen Beleg schreibt man nicht um), **er begründet nichts mehr.** Er zeigt auch, wie nah
mein Vorschlag daran war, das Falsche zu empfehlen: ich hielt „unterordnen" für richtig und
„zurückziehen" nur für *vertretbar* — Yamas Begründung nennt den Grund, den ich nicht gewichtet
hatte: **ein nur meldender Hunk-Wächter verhindert Richtung B nicht.**

> *(historisch, 14.08. vor der Entscheidung)* **Planner-Vorschlag zu V-02:** **Unterordnen, nicht zurückziehen.**
Der Hunk-Wächter bleibt in P2D nützlich (er misst, wie oft die Kollision auftritt, und §14 ist
ohnehin zu ändern) — **aber seine drei Nicht-Ziele zur Aufteilung, zum Claim und zur Sperrdatei
fallen, und K5 „meldet, sperrt nicht" gilt nur noch für den Wächter, nicht für die Barriere.**
Zurückziehen wäre auch vertretbar; **still stehenlassen ist es nicht.**

**⚠ V-04 verschiebt das Gewicht (14.08., nach `902c83f3`):** A-36 trägt jetzt **zwei** Mängel im
Kriterienwerk — die vier fehlenden DoR-Punkte (V-03) **und** ein an einem Wort unerfüllbares
Kriterium (V-04). Beide sind kleine Textarbeit, **aber sie liegen alle im gemeinsamen Checkout,
den ich nicht anfassen darf.** Damit gilt: **entweder wird A-36 zurückgezogen und als Teil von
P2D neu geschnitten — dann fallen V-03 und V-04 mit ihm weg — oder es braucht drei
Einzelkorrekturen über einen Integrator, den es nicht gibt.** Das ist ein Argument, das ich
vorher nicht hatte, und es zeigt in Richtung ZURUECKZIEHEN.

---

## P0 — FORENSISCHE SICHERUNG

| ID | Beschreibung | Rolle | Erwartet | Beleg | Status |
|---|---|---|---|---|---|
| P0-01 | gemeinsamer HEAD gemessen | Planner | SHA | `36e60030` (vorher `358f0b59`, **wanderte während der Messung**) | **UMGESETZT_UNGEPRUEFT** |
| P0-02 | Branch gemessen | Planner | Name | `auto/hausplaner-integration` | **UMGESETZT_UNGEPRUEFT** |
| P0-03 | Remote-Abweichungen gemessen | Planner | je **Gegenstelle**, nicht je Name | **BERICHTIGT nach `481c7da7`:** vier Remote-NAMEN, aber nur **zwei eigene Kopien** — `fork` und `origin` zeigen auf dasselbe Repo (`nuri-head.git`), `backup-private` ist die zweite (`nurihead.git`). Als backup-private 3 Commits zurückhing, war das **die Hälfte** der Sicherung außerhalb dieser Maschine, nicht ein Drittel. | **UMGESETZT_UNGEPRUEFT** |
| P0-03b | **`upstream` gehört einem FREMDEN Konto** (`raminsadid2021/nuri-head.git`) und führt den Zweig nicht | Planner | Nachweis + Konsequenz | selbst gemessen über `git config --get remote.<name>.url`; **ein Push dorthin schriebe in ein fremdes Repository** | **UMGESETZT_UNGEPRUEFT** |
| P0-03c | **Eigener Messfehler:** ich habe in dieser Nacht mehrfach „alle drei Remotes live auf X" gemeldet — **Namen gezählt statt Gegenstellen**, damit eine Redundanz ausgewiesen, die es nicht gibt | Planner | Berichtigung im Bestand | Wortlaut war nie falsch, die erzeugte Sicherheit schon | **UMGESETZT_UNGEPRUEFT** |
| P0-04 | Worktrees gemessen | Planner | Liste | 12 Einträge, **0 prunable**; jetzt 13 mit `rolle/planner` | **UMGESETZT_UNGEPRUEFT** |
| P0-05 | uncommittierte Arbeit erfasst | Planner | Liste | **leer** | **UMGESETZT_UNGEPRUEFT** |
| P0-06 | untracked Arbeit erfasst | Planner | Liste | **leer** | **UMGESETZT_UNGEPRUEFT** |
| P0-07 | Locks nur gelesen | Planner | kein Eingriff | `.git/*.lock`: keine; **kein Lock geräumt** | **UMGESETZT_UNGEPRUEFT** |
| P0-08 | aktiver Schreibverkehr bewertet | Planner | Rollen+Zeiten | **vier Rollen schreiben weiter** — Generator 07:55, Evaluator 08:07, Plan-Prüfer 08:5x, Release-Prüfer 08:12 | **UMGESETZT_UNGEPRUEFT** |
| P0-09 | forensischer Ausgangs-SHA festgeschrieben | Planner | ein SHA | **`36e60030`**, hier im Kopf | **UMGESETZT_UNGEPRUEFT** |
| P0-10 | keine fremde Arbeit verändert | Planner | Nachweis | `git status --porcelain` im gemeinsamen Checkout **leer** nach dem Worktree-Anlegen | **UMGESETZT_UNGEPRUEFT** |

**⚠ EIGENER FEHLER, berichtigt 14.08. beim ersten Zählen:** Fünf P0-Punkte standen auf
`UNABHAENGIG_BESTAETIGT` mit der Fußnote *„vorläufig"*. **Eine Fußnote hebt einen Status nicht auf.**
Meine eigene Regel im Kopf dieser Datei verlangt für diesen Wert, dass **ein anderer Prüfer den
exakten Umsetzungscommit geprüft** hat — das ist bei keinem der fünf der Fall. **Alle fünf stehen
jetzt auf `UMGESETZT_UNGEPRUEFT`**, wo sie hingehören: die Messungen sind reproduzierbar, die
Bestätigung fehlt. **`UNABHAENGIG_BESTAETIGT` steht damit bei NULL Punkten — und das ist richtig,
weil P8 nicht begonnen hat.** *Gefunden, weil ich den Zählstand ausgerechnet habe statt ihn
fortzuschreiben; behauptet hatte ich vorher „null".*

---

## P1 — KRITISCHE VORPRÜFUNG · Ergebnis: **UMGEBUNG_TRAGFAEHIG**

| ID | Prüfpunkt | Beleg | Status |
|---|---|---|---|
| P1-01 | Ausgangscommit vorhanden und lesbar | `cat-file -e` exit 0 | **UMGESETZT_UNGEPRUEFT** |
| P1-02 | Regelwerk + START-PROMPT + fünf Rollenmappen | 1229 / 184 / 97 Z.; 7/5/5/5/5 Dateien | **UMGESETZT_UNGEPRUEFT** |
| P1-03 | `docs/STATUS.md` strukturell lesbar | 16.058 Z. | **UMGESETZT_UNGEPRUEFT** |
| P1-04 | Zäune ausgeglichen | 794 Zäune, endet **geschlossen** | **UMGESETZT_UNGEPRUEFT** |
| P1-05 | abgeschnittene/doppelte aktive Blöcke | **0 echte Duplikate**; 18 Teilblöcke = bekannte Bauart | **UMGESETZT_UNGEPRUEFT** |
| P1-06 | widersprüchliche Zustände | **0** über alle 12 offenen (Tafel = Datensatz) | **UMGESETZT_UNGEPRUEFT** |
| P1-07 | aktive/nächste Aufträge | **12 nicht im Endzustand** — **7** `BEREIT` (A-33, A-35, W-03/1, W-10/1, W-14/1, W-16/1, W-18/1) · **1** `NACHBESSERN` (W-12/1) · **1** `ENTWURF` (A-36) · **1** `DECISION_BLOCKED` (W-21L) · **2** `ABGENOMMEN` (A-05, A-12). Endzustand ist nach §3 **`BETRIEBSBESTAETIGT`** (65 Datensätze), **nicht** `ABGENOMMEN` — deshalb zählen A-05/A-12 mit, obwohl bei beiden kein Ball liegt. Gemessen aus `docs/STATUS.md` (77 Datensätze), nicht aus den Blättern: der Zustand wohnt nach §16 in der Statuswahrheit. | **UMGESETZT_UNGEPRUEFT** |
| P1-07b | **Berichtigung der Aufzählung in P1-07** | Die erste Fassung nannte **6** `BEREIT` bei einer Summe von **12** — die Aufzählung ergab 11. **Die Summe war richtig, die Aufzählung falsch:** es sind **7** `BEREIT`. Gemeldet von Yamas Prüfer, der den Widerspruch korrekt sah, aber die falsche Seite für richtig hielt. **Mein Fehler bei der Nachmessung:** ich habe zuerst über `docs/auftraege/aktiv/*.md` gezählt und 82 Blätter ohne Zustandsfeld gefunden — am falschen Gegenstand gemessen, denn die Blätter tragen `status_steht_in: docs/STATUS.md`. Beim zweiten Anlauf führte ich `BETRIEBSBESTAETIGT` nicht als Endzustand und zählte 32 statt 3. **Erst der Wortlaut der Leiter aus §3 hat es entschieden.** | **UMGESETZT_UNGEPRUEFT** |
| P1-08 | Basis-SHAs existieren | **9 von 9** | **UMGESETZT_UNGEPRUEFT** |
| P1-09 | Blätter/Kriterien/Übergaben | alle da; A-33 zwei Blätter (stillgelegter Pfad) | **UMGESETZT_UNGEPRUEFT** |
| P1-10 | bekannte Beifang-/Zuordnungsfehler | 4 Vorfälle, **alle in `docs/STATUS.md`, keiner im Auftrags-Scope** | **UMGESETZT_UNGEPRUEFT** |
| P1-11 | lokal vs. entfernt bei diesen Aufträgen | **0 Abweichungen** (Stand 08:36) | **UMGESETZT_UNGEPRUEFT** |
| P1-12 | Regelwerk + Tor baubar | 743 Z., `bash -n` OK, §14 auf `:693` wörtlich | **UMGESETZT_UNGEPRUEFT** |

**⚠ Einschränkung zu P1-12, ausdrücklich:** Grün nur für *Lesbarkeit*. **Gemessen ist R1: es gibt
nur einen `post-commit`-Hook, keinen `pre-commit` — `commit-pruefen.sh` ist durch direkten
`git commit` umgehbar.** Das ist kein Blocker, das ist der Gegenstand von P2D.

---

## P2 — TECHNISCHE BARRIERE

### P2A · Worktree-Topologie — je Funktion sieben Felder

| ID | Funktion | Verzeichnis | Branch | Schreibbereich | Status |
|---|---|---|---|---|---|
| P2A-01 | **Planner** | `ticket-rolle-planner` ✔ steht | `rolle/planner` ✔ | `docs/` ohne `STATUS.md` | **UMGESETZT_UNGEPRUEFT** |
| P2A-02 | **Plan-Prüfer** | `ticket-rolle-plan-pruefer` | `rolle/plan-pruefer` | Prüfberichte, kein `STATUS.md` | **OFFEN** |
| P2A-03 | **Generator** | `ticket-rolle-generator` | `rolle/generator` | `resources/`, `app/`, `scripts/`, Blätter | **OFFEN** |
| P2A-04 | **Evaluator** | `ticket-rolle-evaluator` | `rolle/evaluator` | Prüfberichte, Wegwerf-Repos | **OFFEN** |
| P2A-05 | **Release-Prüfer** | `ticket-rolle-release` | `rolle/release-pruefer` | Freigabeberichte | **OFFEN** |
| P2A-06 | **Integrations-Checkout** | `ticket` *(der heutige)* | `auto/hausplaner-integration` | **nur** Integrator, **nur** `STATUS.md` + Merges | **OFFEN** |
| P2A-07 | je Funktion: Ausgangs-SHA · Aktualisierungsverfahren · Abschluss/Entfernung | | | | **OFFEN** |

| P2A-08 | **Regel:** kein Rollen-Worktree darf denselben Branch wie ein anderer verwenden | | | | **OFFEN** |
| P2A-10 | **Regel (NEU nach P0-03b):** Push-Ziele werden **je Gegenstelle** geführt, nicht je Name. `fork` und `origin` sind **dieselbe Kopie**; `upstream` gehört einem **fremden Konto** und ist für Rollenbranches **gesperrt**. Ein Takt, der drei grüne Haken meldet, wo zwei Kopien stehen, weist eine Redundanz aus, die es nicht gibt. | | | | **OFFEN** |
| P2A-09 | **Regel:** neue Aufträge starten von einem festgeschriebenen Integrations-SHA; **ein alter Rollenbranch ist keine Basis** | | | | **OFFEN** |
| P2A-11 | **B-3 aufgelöst: das Tor läuft im Worktree über `NODE_PATH`** | | | | **UMGESETZT_UNGEPRUEFT** |
| P2A-12 | **BEFUND an den Generator (sein Bau): das Tor meldet die falsche Ursache** | | | | **OFFEN** |

**P2A-11 — der Weg, mit Gegenprobe belegt.** Ein Rollen-Worktree hat **kein `node_modules`**;
`commit-pruefen.sh:503` braucht `js-yaml`. Kein Symlink und keine Kopie ins Repo, sondern eine
Umgebungsvariable auf den Modulbaum des Integrations-Checkouts:

```
TICKET_ROLLE=planner NODE_PATH=/Users/yamanuri/Documents/ticket/node_modules \
  bash scripts/commit-pruefen.sh '<Botschaft>' <pfade>
```

**P2A-12 — und der Befund, der dabei anfällt, ist der schwerere.** `commit-pruefen.sh:503` leitet den
Node-Fehler nach `/dev/null` und meldet in **jedem** Fehlerfall `YAML-KOPF <pfad> — der Kopf parst
nicht`. **Das Tor kann ein fehlendes Modul nicht von einem kaputten Kopf unterscheiden.** Gemessen,
mit Gegenprobe:

| Fall | Ergebnis |
|---|---|
| gültiger Kopf, **ohne** `NODE_PATH` | `exit 1` → Meldung **„der Kopf parst nicht"** — **falsche Ursache** |
| gültiger Kopf, **mit** `NODE_PATH` | `exit 0` |
| **kaputter** Kopf, **mit** `NODE_PATH` | `exit 1` — **die Barriere bleibt scharf** |

**Warum das mehr ist als ein Schönheitsfehler:** Sobald die vier anderen Rollen in eigene Worktrees
ziehen, meldet das Tor **jedem** von ihnen bei **jedem** Commit einen Kopf-Fehler, den es nicht gibt.
**Nach A-03 wird eine Barriere, die aus dem falschen Grund sperrt, weggeklickt** — und A-30 hat das
an zwölf Fehlalarmen gemessen. **Der Mangel ist also nicht, dass sie sperrt, sondern dass sie beim
Sperren lügt.** Das Tor ist Bau des Generators; **Planner meldet, Generator behebt.**

**Yamas Anordnung dazu (14.08.):** Die Meldung *„der Kopf parst nicht"* darf **nicht für jeden
Node-Fehler** verwendet werden. Der Generator muss **drei Fälle unterscheidbar** melden:

| # | Fall | verlangte Meldung |
|---|---|---|
| 1 | **Syntaxfehler** im YAML-Kopf | „der Kopf parst nicht" — **nur hier** |
| 2 | **fehlende Modulauflösung** (`js-yaml` nicht gefunden) | **eigener, wahrer Fehlergrund** — nicht als Kopf-Fehler getarnt |
| 3 | **sonstiger Laufzeitfehler** | als solcher benannt |

**Der Evaluator prüft diese drei Fälle unabhängig.**

**Und P2A-11 gilt erst als belegt, wenn es in getrennten Wegwerf-Worktrees gezeigt ist** — Yamas
sechs Bedingungen: gültiger Kopf `exit 0` · **tatsächlich** syntaktisch kaputter Kopf `exit ≠ 0` ·
fehlende Modulauflösung mit **eigenem wahren Grund** · **keine** Symlinks · **keine** Modulkopie je
Worktree · **keine** Abschwächung bestehender Barrieren. **Meine Messung von 09:36 deckt zwei davon
ab, den dritten nicht** — er verlangt genau die Änderung aus P2A-12, die noch nicht gebaut ist.
**Reihenfolge daher: erst P2A-12 bauen, dann P2A-11 in Wegwerf-Worktrees belegen.** P2A-11 steht
deshalb weiter auf `UMGESETZT_UNGEPRUEFT` und **nicht** höher.

### P2B · Rollen- und Worktree-Prüfung — `scripts/rolle-und-worktree.sh`

| ID | Kontrolle | Status |
|---|---|---|
| P2B-01 | `TICKET_ROLLE` ist gesetzt | **OFFEN** |
| P2B-02 | `TICKET_ROLLE` hat gültiges Format | **OFFEN** |
| P2B-03 | Rolle passt zum Worktree | **OFFEN** |
| P2B-04 | Rolle passt zum Branch | **OFFEN** |
| P2B-05 | Rollen-Worktree ist **nicht** der Integrations-Checkout | **OFFEN** |
| P2B-06 | Integrations-Checkout akzeptiert **nur** den Integrator | **OFFEN** |
| P2B-07 | fehlende/widersprüchliche Zuordnung → **harter Abbruch** | **OFFEN** |

### P2C · Einzelschreiber für `docs/STATUS.md`

| ID | Sperre | Status |
|---|---|---|
| P2C-01 | Änderung an `STATUS.md` außerhalb der Integration → abgelehnt | **BLOCKIERT** — Integrator benannt, Instanz nicht gestartet |
| P2C-02 | Commit mit `STATUS.md` von normaler Rolle → abgelehnt | **BLOCKIERT** |
| P2C-03 | im Integrations-Checkout nur Integrator | **BLOCKIERT** |
| P2C-04 | Statusübergang ohne Übergabestück + Ursprungscommit → abgelehnt | **BLOCKIERT** |
| P2C-05 | Integrationscommit ohne benannte Fremdpfade → abgelehnt | **BLOCKIERT** |

**Blocker für P2C-01..05:** **Die Rolle ist entschieden und benannt** — eigener sechster Agent,
`TICKET_ROLLE=integrator` (B-2, 14.08.) —, **aber die Instanz läuft nicht und der
Integrations-Checkout ist nicht aktiviert.** Die Sperre ist damit **spezifizierbar, aber nicht
belegbar**: ihr Subjekt existiert als Festlegung, noch nicht als Betrieb. **Eine Fachrolle darf
nicht stillschweigend hineinrutschen** — Yama hat ausdrücklich gegen den Release-Prüfer entschieden.

### P2D · Commit-Tor und Hook

| ID | Punkt | Beleg / Status |
|---|---|---|
| P2D-01 | Umgehbarkeit von `commit-pruefen.sh` gemessen | **gemessen: nur `post-commit` vorhanden, kein `pre-commit`** → **UMGESETZT_UNGEPRUEFT** |
| P2D-02 | versionierter Hook (`.githooks/` + `core.hooksPath`) | **OFFEN** |
| P2D-03 | bestehende Schutzfunktionen **nicht** abgeschwächt (`must_preserve`) | **OFFEN** |
| P2D-04 | keine pauschale Lock-Räumung, keine automatischen Resets | **OFFEN** |
| P2D-05 | klare Fehlermeldung, `ENV_BLOCKED` bei unklarer Umgebung | **OFFEN** |
| P2D-06 | **verbleibende Umgehungsmöglichkeiten ehrlich dokumentiert** | **OFFEN** |

### P2E · Integrationsverfahren — zwölf Schritte

| ID | Schritt | Status |
|---|---|---|
| P2E-01 | Ursprungscommit und Ziel-HEAD messen | **OFFEN** |
| P2E-02 | Rollen-/Auftragszuordnung prüfen | **OFFEN** |
| P2E-03 | Commitdiff und Pfadmenge lesen | **OFFEN** |
| P2E-04 | Übergabestück prüfen | **OFFEN** |
| P2E-05 | fremde/unklare Änderungen **ablehnen** | **OFFEN** |
| P2E-06 | Integration **einzeln** | **OFFEN** |
| P2E-07 | Konflikte **sichtbar stoppen** | **OFFEN** |
| P2E-08 | Ergebnisdiff erneut lesen | **OFFEN** |
| P2E-09 | Status **nur** durch Integrator | **OFFEN** |
| P2E-10 | Integrationscommit mit Ursprung und Übergang | **OFFEN** |
| P2E-11 | Nachprüfung | **OFFEN** |
| P2E-12 | Checkliste aktualisieren | **OFFEN** |

---

## ⇄ MECHANISMUSWECHSEL — 14.08., 22:20: ROLLENDER UMZUG STATT SCHREIBSTOPP

**Yama hat den Schreibstopp aufgehoben und den Mechanismus gewechselt.** Statt Stillstand →
Aktivierungs-SHA → Umzug aller auf einmal gilt jetzt: **jede Rolle zieht einzeln um, sobald sie
gerade keinen offenen Vorgang hat.** Kein Stillstand, kein gemeinsamer Aktivierungspunkt.

**Warum der alte Weg nicht getragen hat — gemessen, nicht vermutet:**

| Befund | Beleg |
|---|---|
| Der Stopp war **nie zustellbar** | 11 Commits von 3 Rollen in den 29 Minuten nach der Anordnung |
| Er wirkte trotzdem — **zwölf Stunden Stille** | letzter fremder Commit 10:13:43, nächster 22:13:18 |
| **Und kippte binnen einer Minute** | Aufhebung 22:12, Generator-Commit 22:13:18 |
| Ein fester Aktivierungsstand ist **nicht haltbar** | zwischen zwei meiner Befehle wanderte HEAD von `0a297803` auf `bc2125d9` |

**Der letzte Punkt ist der entscheidende.** Ein `AKTIVIERUNGS_SHA` setzt voraus, dass der Stand
stillsteht, während man ihn prüft. **Das war an keinem Punkt dieses Tages der Fall.**

### Was jetzt gilt

| ID | Punkt | Status |
|---|---|---|
| P2H-01 | **Vier Rollen-Worktrees angelegt**, Basis `bc2125d9`, Branches `rolle/plan-pruefer` · `rolle/generator` · `rolle/evaluator` · `rolle/release-pruefer` | **UMGESETZT_UNGEPRUEFT** |
| P2H-02 | **Umzugsregel je Rolle** — einzeln, bei leerem Ballbesitz | **UMGESETZT_UNGEPRUEFT** |
| P2H-03 | **`NODE_PATH` in jeder Umzugsanleitung** — sonst weist das Tor jeden Commit mit falscher Ursache ab | **UMGESETZT_UNGEPRUEFT** |
| P2H-04 | **Zustellung an die vier Rollen** — sie müssen erfahren, dass ihr Baum existiert | **BLOCKIERT** — nur Yama erreicht die Instanzen |
| P2H-05 | **Erste Rolle umgezogen** und dort committet | **OFFEN** |
| P2H-06 | **Alle vier umgezogen** — erst danach wird der gemeinsame Checkout zum Integrations-Checkout | **OFFEN** |
| P2H-07 | **`AKTIVIERUNGS_SHA` entfällt als Konstrukt** — jede Rolle trägt ihren eigenen Umzugs-SHA | **UMGESETZT_UNGEPRUEFT** |
| P2H-08 | **Zweite Statuswahrheit — GESCHLOSSEN am 14.08. 22:51 durch `c1b3a774`** *(Merge der Release-Linie `210dcc5a` mit der Kettenlinie `09125aaf`, durch den Release-Prüfer selbst, nicht durch den Integrator)* | **UMGESETZT_UNGEPRUEFT** |
| P2H-09 | **Der Release-Prüfer arbeitet an einem `detached HEAD`**, nicht auf einem Rollenbranch | **OFFEN** |
| P2H-10 | **Doppelbesetzung einer Rolle war eingetreten** — zweite Instanz zurückgetreten, Vorgang dokumentiert | **UMGESETZT_UNGEPRUEFT** |
| P2H-11 | **Der Merge `c1b3a774` hat kein Integrationsprotokoll** — Herkunft je Commit, Konflikte, nicht Integriertes: alles ungeschrieben | **OFFEN** |
| P2H-12 | **Rückfluss — GELÖST in der Praxis, ungelöst in der Regel.** Rückstau **0** auf allen fünf Zweigen; der Weg heißt faktisch **R2**, ist aber **nicht festgelegt** | **NACHBESSERN** |
| P2H-13 | **⚠ Der Plan-Prüfer ist seit 27 Stunden still** — A-37 und A-38 warten auf seine DoR | **BLOCKIERT** |
| P2H-14 | **Eine Rollenmarke sagt nicht, WELCHE INSTANZ geschrieben hat** — Vertretungen tragen die fremde Marke | **UMGESETZT_UNGEPRUEFT** |
| P2H-15 | **`node_modules`-Nicht-Ziel ERSETZT** *(Yama §1, 16.08.)* — je Baum ein eigenes, aus **diesem** Lockfile erzeugt; kein Prüfergebnis darf vom Baum abhängen | **UMGESETZT_UNGEPRUEFT** |
| P2H-16 | **Lockfile-Prüfung im Rollen-Tor** — Yamas **Bedingung** für die Entscheidung; ohne sie ist der eigene Modulbaum Disziplin statt Mechanik | **OFFEN** — `A-37-12..14` |
| P2H-17 | **Zwei Haltbarkeiten einer Messung** — flüchtig trägt Zeitstempel, unveränderlich trägt SHA *(Nachtrag in `ARBEITSREGELN.md`)* | **UMGESETZT_UNGEPRUEFT** |

**P2H-15 — die Entscheidung hing an zwei Wörtern, und das ist der eigentliche Fund.** Yamas
Bedingung stand im A-37-Blatt als *„Kein `node_modules` je Worktree, kein Symlink, keine Modulkopie
**ins Repo**"*. **In meinem Bericht fehlte `ins Repo`** — und genau daran hängt die Reichweite:
bezieht sich die Präpositionalphrase auf alle drei Glieder, verletzen git-ignorierte Kopien nichts;
bezieht sie sich nur auf das letzte, steht *„kein `node_modules` je Worktree"* unbedingt da.

**Yama hat die engere Lesart entschieden** — *„weil eine Bedingung, die zwei Lesarten trägt, im
Zweifel die engere hat: die weite hätte ich mir sonst nachträglich zurechtgelegt."*

**Er ordnet es als H-9 auf der Regelebene ein, vierter Fall dieser Woche:** `selectionIds` ↔
`selectedNodeIds` · „Giebel" an Dach und Wand · „Orientierung" als Modullage und Himmelsrichtung ·
**und jetzt eine Bedingung, deren Geltungsbereich beim Weitertragen abfiel.** *„Nicht der Inhalt
driftet, der Geltungsbereich driftet."*

**Der tragende Grund für die Entscheidung ist nicht der Preis** — 2,0 GB sind **2,6 %** des freien
Platzes, *„zu klein, um überhaupt in der Abwägung vorzukommen"*. Sondern: **ein geteiltes
`node_modules` wäre eine zweite Wahrheit.** Jeder Rollen-Branch trägt sein eigenes
`package-lock.json`; ändert einer eine Abhängigkeit, ist ein geteiltes Modulverzeichnis für
höchstens einen Baum richtig — **und der Lauf schlägt nicht fehl, er ist grün und misst den
falschen Stand.**

**P2H-16 ist die Bedingung, nicht die Kür.** Yamas Satz: *„Ohne diese Prüfung bin ich gegen (a).
Mit ihr bin ich dafür."* **Rot-Beleg selbst gemessen:** `package-lock` **0**, `npm ci` **0**,
`hash-object` **0** Treffer unter `scripts/`. **Die Drift ist heute unbewacht.**

**Und beim Bauen der Prüfung sind zwei naheliegende Wege gemessen und verworfen worden:** der
`mtime`-Vergleich *(`git checkout` setzt sie neu — Fehlalarm bei jedem Branchwechsel)* und der Hash
von `node_modules/.package-lock.json` *(404 gegen 466 Pakete — eine andere Datei)*. **Die Marke muss
beim Installieren geschrieben werden; npm liefert sie nicht.**

**P2H-14 — gefunden an vier Fehlalarmen meines eigenen Weckers, alle am selben Tag.**

**Zwölf Commits im Bestand tragen eine Vertretung in der Marke, transparent geschrieben:**

```
evaluator         (Zweitinstanz)
generator         (vom Planner GESICHERT, nicht abgenommen)
plan-pruefer      (release-pruefer in Rollenwechsel)
release-pruefer   (in Yamas Namen)
release-pruefer   (zweite Instanz)
yama-entscheidung (in Vertretung eingetragen)
```

**Jede Messung, die nur den Namen liest, hält die Vertretung für die Rolle selbst.** Konkret: mein
Wecker meldete `rolle/plan-pruefer` als **umgezogen**, weil dort zwei Commits mit `plan-pruefer`
beginnen — beide sind *„plan-pruefer (release-pruefer in Rollenwechsel)"*, beide stehen auch im
gemeinsamen Baum, und **der Plan-Prüfer selbst hat seit 28 Stunden nichts geschrieben.**

**Die Regel, die daraus folgt und die für jedes Werkzeug gilt, das Rollen zählt:**

> **Eine Rollenmarke ist nur dann eine Instanzaussage, wenn der Doppelpunkt unmittelbar folgt.**
> `<rolle>:` oder `<rolle>-<ziffer>:` ist die Rolle selbst. **`<rolle> (…)` ist eine Vertretung**
> und zählt nicht als Arbeit dieser Rolle. Gemessen: das Muster mit Doppelpunkt trennt sauber —
> plan-pruefer 290 → 288, release-pruefer 179 → 176, planner 391 → 384.

**Und die zweite Falle desselben Weckers, weil sie dieselbe Familie ist:** „Commits über der Basis"
zählt auch einen **reinen Nachzieh-Merge**. `rolle/generator` trug 108 Commits und **null** eigene.
**Richtig misst nur `--first-parent`** — ein Commit, den die Rolle *dort* gesetzt hat, statt ihn von
woanders zu holen.

**Vier Wecker an einem Tag, vier Muster, die etwas anderes maßen als die Sache:** Textmuster statt
Ballbesitz · Tafelzeile statt yaml-Feld · Merge statt Umzug · Vertretung statt Rolle. **Dieselbe
Familie wie H-9, nur an meinem eigenen Werkzeug statt an einem fremden Blatt.**

**⇒ NACHTRAG 15.08. 13:23 — mein Befund von 12:08 ist überholt, und das gehört an den Anfang.**
Der Release-Prüfer hat geantwortet (`5ee0bd47`) und **er hat recht**. Zwischen meiner Messung und
seiner sind **drei Transporte** gelaufen. Selbst nachgemessen:

```
Rueckstau je Rollenzweig gegen den gemeinsamen HEAD:
  planner 0 · release-pruefer 0 · evaluator 0 · generator 0 · plan-pruefer 0
Divergenz gegen origin / fork / backup-private:   0 / 0
Der gemeinsame Checkout ist NICHT hinter — er ist aktuell.
```

**Mein Satz „ein Fehler, dessen Korrektur denselben Fehler macht" war um 12:08 richtig und ist es
jetzt nicht mehr.** Der Rückfluss läuft — **faktisch über ihn.**

**Was er dazu über mich sagt, und es trifft:** *„Der Planner empfiehlt R2, also mich. ICH BIN
BEFANGEN und sage es… Wer entscheidet, sollte mitlesen, dass die Empfehlung von der Rolle kommt,
die den Preis nicht zahlt."* — **Das bin ich.** Ich habe R2 empfohlen, weil es läuft; **die Last
trägt er, nicht ich.** Die Empfehlung bleibt sachlich richtig, aber sie ist keine neutrale.

**Und eine Grenze, die er zieht und die ich mir merke:** Er zieht den gemeinsamen Checkout **nicht**
selbst nach, obwohl es verlustfrei wäre — *„zwischen ‚sauber gemessen' und ‚geschrieben' liegt die
Lücke, aus der die siebenteilige Kollisionsserie kam. Ein Baum meldet Arbeit erst, wenn sie
geschrieben ist."* **Das ist derselbe Grund, aus dem ich den Konflikt-Merge nicht selbst gefahren
habe** — die Regel gilt in beide Richtungen.

**P2H-12 steht deshalb auf `NACHBESSERN`, nicht auf erledigt:** der **Rückstau** ist weg, die
**Regel** fehlt weiter. R2 läuft als Gefälligkeit, nicht als Zuständigkeit.

**P2H-13 — der Engpass hat sich verschoben, und er ist neu:**

```
Plan-Pruefer, letzter Commit:   2026-08-14 10:11:37
gemessen:                       2026-08-15 13:23:19     ->  27 Stunden
```

**A-37 (`P0`) und A-38 (`P1`) tragen beide `dor_beleg: steht aus` und `ballbesitz: plan-pruefer`.**
Beide stehen seit dem Nachtrag korrekt in der Statuswahrheit — **sichtbar, aber unbearbeitet.**
Sein Baum `rolle/plan-pruefer` hat **0 Commits**; er ist weder umgezogen noch im gemeinsamen Baum
tätig. **Damit steht die Baukette für zwei Aufträge, und die Umstellung wartet auf den Schutz, den
A-37 baut.** *(Zustellung liegt bei Yama — ich erreiche fremde Instanzen nicht.)*

**P2H-12 — die Lücke steckt in meinem eigenen Plan, und sie ist heute zweimal eingetreten.**

```
Stand 15.08. 12:08, gemessen:
  rolle/planner          5 Commits NICHT im gemeinsamen Baum, darunter 53a0947e
  rolle/release-pruefer 10 Commits NICHT im gemeinsamen Baum
  letzter Planner-Merge in den gemeinsamen Baum:  14.08. 22:51
```

**Das ist zeichengleich der Befund, den der zurückgetretene Release-Prüfer gestern über die
Release-Linie erhoben hat** — *„merget den gemeinsamen Checkout in sich hinein, 15 Mal, aber NIE
zurück"*. **Ich habe denselben Mechanismus gebaut, eine Ebene höher, und ihn nicht bemerkt, bis
meine eigene Korrektur davon betroffen war.**

**Die Folge ist bitter genau:** Der Nachtrag `53a0947e` behebt, dass A-37 und A-38 in der
Statuswahrheit fehlten und deshalb für den Plan-Prüfer unsichtbar waren. **Diese Behebung ist
selbst unsichtbar**, weil sie nur in meinem Baum liegt. *Ein Fehler, dessen Korrektur denselben
Fehler macht.*

**Warum ich den Rückfluss-Merge NICHT selbst fahre:** Er ist konfliktbehaftet. Meine Seite trägt
`A-35` auf `CODE_FERTIG`, der gemeinsame Baum auf **`ABGENOMMEN`** (`5dd5eaee`, 12:00, Evaluator).
**Nähme ich meine Seite, setzte ich den Zustand eines fremden Auftrags zurück** — Verlust an fremder
Arbeit, verboten nach der Eigentumsregel. Die richtige Auflösung **kombiniert** beide Seiten:
A-36/A-37/A-38 von mir, A-35 von ihnen. **Genau diese Handarbeit hat gestern 24 Zeilen gekostet**,
und die Rolle mit dem gehärteten Werkzeug dafür ist der Release-Prüfer, nicht ich.

**Was der Rückfluss braucht — drei Wege, einer muss gewählt werden:**

| Weg | wer merget zurück | Preis |
|---|---|---|
| **R1** | **jede Rolle selbst**, mit Protokoll | fünf Schreiber im gemeinsamen Baum — **der Zustand von vorher** |
| **R2** | **eine benannte Rolle** übernimmt es bis zum Integrator *(faktisch tut es heute der Release-Prüfer)* | eine Rolle trägt fremde Konflikte, ohne dafür zuständig zu sein |
| **R3** | **der Integrator**, wie im Zielbild | setzt voraus, dass er startet — bis dahin fließt nichts zurück |

**Meine Empfehlung: R2 als Übergang, benannt statt faktisch.** Der Release-Prüfer tut es ohnehin,
hat das Werkzeug und die Übung; **was fehlt, ist die Festlegung, dass es seine Aufgabe ist und nicht
seine Gefälligkeit.** R1 hebt den Umzug auf, R3 wartet auf einen Start, der offen ist.
**Die Entscheidung gehört Yama.**

**P2H-08 — der schwerste Befund des Tages, gemeldet vom zurückgetretenen Release-Prüfer
(`8a417fe0`), von mir nachgemessen und an einer Stelle berichtigt.**

```
Divergenz gegen ALLE drei Fernstände (origin, fork, backup-private), identisch:
    lokal voraus:      6 Commits   (W-16/1-Runden, Rücktritt — 22:27 bis 22:46)
    Fernstand voraus: 18 Commits   (Release-Linie — 08:48 bis 22:34)

docs/STATUS.md:
    gemeinsam  2898c5cc   17.600 Zeilen   zuletzt 22:46
    Release    1b2f7397   17.737 Zeilen   zuletzt 22:34
    -> VERSCHIEDEN.  Alle 18 Release-Commits berühren docs/STATUS.md.
```

**Seine Formulierung war „die Fassung, die alle anderen lesen, ist die veraltete". Das trifft es
nicht:** die lokale Fassung ist die **neuere** (22:46 gegen 22:34). **Es sind zwei Linien, die beide
neue Inhalte tragen — das ist schlimmer als veraltet.** Ein Zusammenführen erzeugt sicher Konflikte
in `docs/STATUS.md`, und zwar in beiden Richtungen.

**Was NICHT verloren ist:** `210dcc5a` liegt auf **drei** Fernständen. Die 18 Commits sind gesichert,
obwohl der Worktree einen `detached HEAD` trägt. *(Gemessen mit `branch -a --contains`, nicht
angenommen — bei einem losgelösten Kopf ohne Fernstand hätte allein das Verzeichnis sie gehalten.)*

**Was das für den Integrator heißt:** Sein erster Vorgang ist **nicht** ein einzelner Rollen-Commit,
sondern **diese Gabelung** — 24 Commits, beide Seiten mit Änderungen an der Statuswahrheit. **Genau
der Fall, für den die Rolle gebaut wurde, und er ist eingetreten, bevor sie startet.**

**⇒ NACHTRAG 22:52, gemessen: die Gabelung ist zu.** Sie bestand rund vierzehn Stunden und wurde
geschlossen, **während ich sie eintrug**:

```
Divergenz gegen origin, fork, backup-private:   lokal 0  ·  Fernstand 0
210dcc5a ist Vorfahr des gemeinsamen HEAD       -> die 18 Release-Commits sind drin
docs/STATUS.md   def60023   17.857 Zeilen   in BEIDEN Bäumen identisch
Merge c1b3a774, Eltern 210dcc5a + 09125aaf
```

**Zwei Dinge daran sind für die Umstellung wichtiger als die Erleichterung.**

**Erstens:** Der Vorgang, den ich dem Integrator als **ersten und schwersten** zugedacht hatte, ist
**ohne ihn** erledigt worden — von der Rolle, die die Gabelung verursacht hat. Das entwertet die
Rolle nicht, aber es verschiebt ihre Begründung: **sie wird nicht gebraucht, weil niemand sonst
zusammenführen könnte, sondern damit nicht jeder es nebenbei tut.**

**Zweitens, und das bleibt offen:** Der Merge lief **ohne Integrationsprotokoll**. Es gibt keine
Herkunftszuordnung je Commit, keine Liste der Konflikte, keine Aussage über nicht Integriertes —
die neun Erzeugnisse aus `4-WAS-ICH-ABLIEFERE.md` fehlen sämtlich. **24 Commits sind zusammengeführt
und niemand kann heute sagen, welche Zeile aus welcher Linie stammt.** Das ist kein Vorwurf an eine
Rolle, die unter Zeitdruck das Richtige getan hat — es ist der Beleg dafür, wozu das Protokoll da
ist. *(`P2H-11`.)*

**P2H-09:** Der Release-Prüfer arbeitet aus `ticket-release-pruefung` auf `detached HEAD` — der leere
Baum `ticket-rolle-release` mit Branch `rolle/release-pruefer` steht daneben und ist unbenutzt.
**Zwei Bäume für eine Rolle**, einer davon ohne Branch. Der Umzug für diese Rolle ist damit nicht
„noch nicht erfolgt", sondern **an den falschen Ort erfolgt**.

**P2H-10:** Eine **zweite Instanz derselben Rolle** war aktiv und hat einen Phantom-Ball vorgeprüft.
Sie ist zurückgetreten und hat die Entscheidung **gemessen statt bevorzugt** begründet: Instanz A
hat einen eigenen Worktree, 8 Rollen-Commits, 15 Merges und zwei Aufträge bis `BETRIEBSBESTAETIGT`
durchgezogen; sie selbst hatte null. **Ihr Satz gehört in dieses Blatt:** *„Der Grenznutzen einer
zweiten schreibenden Instanz ist nicht null, sondern NEGATIV: zwei Release-Prüfer erzeugen die
zweite Wahrheit, gegen die die Rolle gebaut ist."* — **Das ist die Begründung der ganzen Umstellung,
unabhängig hergeleitet von einer Rolle, die dabei ihren eigenen Platz aufgibt.**

### Die Umzugsanleitung — vier Zeilen je Rolle

```
1. Ballbesitz prüfen: kein offener Vorgang, nichts uncommittiert im gemeinsamen Checkout.
2. In den eigenen Worktree wechseln:  /Users/yamanuri/Documents/ticket-rolle-<rolle>
3. Ab sofort dort arbeiten und committen. NIE mehr im gemeinsamen Checkout schreiben.
4. Commit-Befehl (das NODE_PATH ist PFLICHT, sonst weist das Tor mit falscher Ursache ab):
     TICKET_ROLLE=<rolle> NODE_PATH=/Users/yamanuri/Documents/ticket/node_modules \
       bash scripts/commit-pruefen.sh '<Botschaft>' <pfade>
```

**Zu Schritt 4, und es ist kein Schönheitsfehler:** Ein frischer Worktree hat **kein**
`node_modules`. `commit-pruefen.sh:503` meldet dann *„der Kopf parst nicht"* — **eine Ursache, die
nicht zutrifft.** Ohne diese Zeile scheitert die erste Rolle beim ersten Commit und hält den Umzug
für kaputt. *(Gemessen mit Gegenprobe, siehe P2A-11/P2A-12.)*

### Was der Wechsel nicht ändert

- **`docs/STATUS.md` bleibt die eine Statuswahrheit** und bekommt am Ende genau einen Schreiber.
- **Der Integrator bleibt beschlossen** (B-2) und kommt **zuletzt** — wenn alle vier umgezogen sind.
- **`BOOTSTRAP` bleibt gesperrt.** Die Worktrees hat **Yama autorisiert** (B2) und der Planner als
  Infrastrukturhandlung ausgeführt, nicht der Integrator.
- **`FORENSISCHER_SHA` `36e60030` bleibt Untersuchungsstand.**

### Was der Wechsel kostet — ehrlich benannt

**Während des Umzugs gibt es zwei Wahrheiten:** wer schon umgezogen ist, schreibt in seinem Baum;
wer noch nicht, im gemeinsamen. **Die Kollision ist in dieser Phase nicht kleiner, sondern
unübersichtlicher.** Der Preis ist bewusst gewählt: **kein Stillstand.** Gegenmittel ist die
Reihenfolge — **die Rolle mit dem meisten Schreibverkehr zuerst** (gemessen: Plan-Prüfer, 141 von
500 Commits), damit die größte Quelle als erste aus dem gemeinsamen Baum verschwindet.

---

## P2F — SCHREIBSTOPP, INTEGRATOR, AKTIVIERUNGS-SHA *(neu nach Yamas Entscheidungen)*

| ID | Punkt | Status |
|---|---|---|
| P2F-01 | **Schreibstopp B-1 ZUGESTELLT** — die vier Rollen-Instanzen haben die Anweisung empfangen | **BLOCKIERT** |
| P2F-02 | Schreibstopp gemessen: **gemeinsamer Arbeitsbaum sauber** | **UMGESETZT_UNGEPRUEFT** |
| P2F-03 | Schreibstopp gemessen: **keine uncommittierten/untracked Rollenänderungen** | **UMGESETZT_UNGEPRUEFT** |
| P2F-04 | Schreibstopp gemessen: **keine laufenden Git-Schreibvorgänge** — Lock-Dateien **und** Prozesse | **UMGESETZT_UNGEPRUEFT** |
| P2F-05 | Schreibstopp **wirksam** — alle vier Bedingungen zugleich: Instanzbestätigung je Rolle · sauberer Baum · keine aktiven Git-Schreiber · Ruhephase | **NACHBESSERN** |
| P2F-06 | **Integrator-Agent gestartet** mit `TICKET_ROLLE=integrator` | **BLOCKIERT** |
| P2F-07 | **Integrations-Checkout aktiviert** — erst **nach** belegtem Schreibstopp | **BLOCKIERT** |
| P2F-08 | **`AKTIVIERUNGS_SHA` bestimmt** durch den Integrator | **BLOCKIERT** |
| P2F-09 | vorher gemessen: **lokaler HEAD** | **BLOCKIERT** |
| P2F-10 | vorher gemessen: **alle eigenen Gegenstellen** *(`fork`=`origin` sind **eine** Kopie, `upstream` ist fremd — P0-03b)* | **BLOCKIERT** |
| P2F-11 | vorher gemessen: **Ahead/Behind in BEIDE Richtungen** | **BLOCKIERT** |
| P2F-12 | vorher gemessen: **Inhalte der auseinanderliegenden Commits** | **BLOCKIERT** |
| P2F-13 | vorher gemessen: **Statusabweichungen** | **BLOCKIERT** |
| P2F-14 | vorher gemessen: **uncommittierte und untracked Arbeit** | **BLOCKIERT** |
| P2F-15 | **keine Seite automatisch bevorzugt · kein Merge/Rebase/Push ohne eigenen belegten Integrationsplan** | **OFFEN** |
| P2F-16 | **Integrator ist für denselben Vorgang weder Evaluator noch Release-Prüfer** — Trennung belegt, nicht zugesagt | **OFFEN** |

**P2F-02 bis P2F-04 — gemessen am 14.08. um 09:39 im gemeinsamen Checkout:**

```
git status --porcelain | wc -l   ->  0        Arbeitsbaum sauber
Lock-Dateien (index/HEAD/refs)   ->  keine
letzter Commit je Rolle:
  plan-pruefer     774854ef  09:35:46
  generator        9d83bde6  07:55:13
  evaluator        66167298  08:08:52
  release-pruefer  6aff69ea  08:12:17
```

**Nachgemessen um 10:08 — Bedingung 3 jetzt VOLLSTÄNDIG, denn die erste Messung war unvollständig:**

```
Lock-Dateien (index/HEAD/refs)      ->  keine
laufende git-Prozesse auf dem Repo  ->  0        <- das fehlte am 09:39
git status --porcelain              ->  0
```

**Mein Fehler dabei, benannt:** Um 09:39 habe ich P2F-04 auf `UMGESETZT_UNGEPRUEFT` gesetzt und dabei
**nur Lock-Dateien** geprüft. Yamas Bedingung lautet „aktive Git-Schreiber" — das sind **zwei** Dinge:
Locks **und** Prozesse. Ein Lock kann fehlen, während ein `git`-Prozess läuft und gleich eines
anlegt. **Der Punkt war nicht falsch, aber nur halb belegt.**

**P2F-05 steht auf `NACHBESSERN`, und das ist der wichtigste Punkt dieses Blocks.** Yamas Kriterium:
*„letzter fremder Commit zeitlich vor dem festgestellten Stopp"*, und ausdrücklich: *„eine bloße
Mitteilung ‚die Rollen wurden gestoppt' genügt nicht."* **Gemessen: der Plan-Prüfer schreibt weiter**
— `774854ef` um 09:35:46, danach `c5c6ac57` um 09:40:15. **Der Stopp ist erlassen, aber nicht
zugestellt.**

**Warum ich ihn nicht zustellen kann:** Die vier Rollen sind **eigene Instanzen**, nicht meine
Unteragenten. Der einzige Kanal zu ihnen sind Commits im gemeinsamen Checkout — und genau dorthin
darf ich nicht schreiben, weil er alleiniger Schreibraum des Integrators wird. **Die Zustellung ist
P2F-01 und liegt bei Yama, der die Instanzen fährt.** Solange sie fehlt, ist der Schreibstopp eine
Anordnung ohne Adressaten.

**Nachgemessen um 09:44, und die Lage ist schärfer als ein Nachzügler:** In den **4,5 Minuten** nach
der Stopp-Feststellung um 09:39:31 sind **zwei** weitere fremde Commits gefallen (`c5c6ac57` 09:40:15,
`e6c4f7a2` 09:42:59) — **und beide schreiben in `docs/STATUS.md`**, genau die Datei, für die B-2 den
Integrator zum **alleinigen** Schreiber macht.

**Nachgemessen um 10:08, und die Lage ist breiter als angenommen — es ist nicht eine Rolle, es sind
drei:**

```
Rollen-Commits seit 09:39:31:
   8  plan-pruefer
   2  generator
   1  evaluator
  --  release-pruefer  (0)
```

**Elf Commits von drei Rollen in 29 Minuten.** Meine Meldung von 09:44 nannte nur den Plan-Prüfer und
war damit **zu eng** — ich hatte den einen gemessen, der mich weckte, nicht die Fläche. *(Dieselbe
Lehre, die der Plan-Prüfer heute selbst gezogen hat: „die ganze Fläche gemessen statt der nächsten
Einzelstelle".)*

**Und der Betrieb läuft nicht nur weiter, er wechselt Zustände:** `039aa7c4` um 10:08:10 setzt
**W-12/1 auf `ABGENOMMEN`** (Evaluator Runde 2, Ball an den Release-Prüfer). **Das ist genau der
Vorgang, den B-2 künftig dem Integrator allein zuweist** — und er ist heute Nacht einmal an dieser
Datei kollidiert.

**Ich nenne die Rate, nicht den Stand:** eine feste Gesamtzahl driftet mit jeder Minute; **drei von
vier Rollen, elf Commits in 29 Minuten, darunter ein Zustandswechsel** bleibt auch später wahr.

**Ein Nachweis, der nicht durch Zufall grün wird — und die Ruhephase allein genügt NICHT.**
*(Yamas Nachschärfung vom 14.08.: eine commitfreie Zeit ist ein **Indiz**, kein Beleg. Eine Instanz,
die gerade liest oder nachdenkt, erzeugt zwanzig Minuten Stille und schreibt danach weiter.)*
**P2F-05 gilt erst, wenn ALLE VIER Bedingungen zugleich belegt sind:**

| # | Bedingung | Beleg |
|---|---|---|
| 1 | **Bestätigung oder Beendigung ALLER VIER Rolleninstanzen**, je einzeln | Zustellungs- oder Beendigungsnachweis je Instanz — **nicht** eine Sammelaussage |
| 2 | **Sauberer gemeinsamer Arbeitsbaum** | `git status --porcelain` → 0 Zeilen, Rohausgabe |
| 3 | **Keine aktiven Git-Schreiber** | keine `index.lock`/`HEAD.lock`/`refs/**.lock`; **zusätzlich** kein laufender `git`-Prozess auf dem Repo |
| 4 | **Ruhephase ohne Rollen-Commit** | 20 Minuten, `git log --since`, Rohausgabe |

**Bedingung 1 ist die tragende, und sie ist die einzige, die ich nicht messen kann.** Die anderen
drei kann ich lesend belegen; ob eine fremde Instanz die Anweisung *empfangen* hat, sieht man an
keinem Git-Zustand. **Deshalb steht P2F-01 vor P2F-05 und nicht daneben.** Und deshalb ist
„die Rollen wurden gestoppt" nach Yamas Wortlaut ausdrücklich **kein** Beleg.

**Und ein Zweites, das der Aktivierungs-SHA erben wird:** Zwischen `36e60030` und jetzt liegen die
Commits des Plan-Prüfers von heute Morgen — darunter die **Rücknahme** eines eigenen Fehlbefunds
(`45f26bdb`), zwei **Berichtigungen** (`bdf44881`, `c5c6ac57`) und die P-02-Prüfung, deren Ball
**eine Woche** unentdeckt lag. **Der Aktivierungs-SHA muss diese Stände tragen** — genau deshalb ist
Yamas Trennung der beiden SHAs richtig und `36e60030` als Arbeitsbasis falsch.

---

## P2G — INTEGRATOR-ROLLENPAKET, ROLLENBARRIERE, SPERRNACHWEISE *(Yamas Auftrag vom 14.08.)*

### Das Rollenpaket — vom Planner erstellt, **nicht** vom Planner abgenommen

| ID | Punkt | Status |
|---|---|---|
| P2G-01 | `docs/rollenkette/rollen/6-integrator/1-AUFTRAG.md` — Kennung, sechster Agent, zehn Festlegungen, sechs Einsatzvoraussetzungen, Verhältnis zu den fünf Rollen | **UMGESETZT_UNGEPRUEFT** |
| P2G-02 | `2-WANN-BIN-ICH-DRAN.md` — Auslöser, **DREI Betriebsarten** (`NUR_LESEND` · `BOOTSTRAP` · `SCHREIBEND`), **Ablaufplan A–K mit korrekter Startreihenfolge** (lesend starten **vor** prüfen), zwölf Integrationsschritte, Nicht-Zuständigkeit | **UMGESETZT_UNGEPRUEFT** |
| P2G-03 | `3-WAS-ICH-LESE.md` — **VIER Vorgangstypen** (A Aktivierung · B Generator-Commit · C reiner Statusübergang · D Prüf-/Freigabedokument) statt zehn pauschaler Pflichteingaben, Gegenstellen **je Gegenstelle**, sechs Verwechslungen | **UMGESETZT_UNGEPRUEFT** |
| P2G-04 | `4-WAS-ICH-ABLIEFERE.md` — neun Erzeugnisse, Belegform, Zählstand übernommen/abgelehnt/offen | **UMGESETZT_UNGEPRUEFT** |
| P2G-05 | `5-WAS-ICH-NICHT-DARF.md` — zwölf harte Grenzen, je begründet | **UMGESETZT_UNGEPRUEFT** |
| P2G-06 | **Konsistenz `docs/ARBEITSREGELN.md`** — Nachtrag „Integrator" am Dateiende, Hinweis in der Release-Prüfer-Zeile | **UMGESETZT_UNGEPRUEFT** |
| P2G-07 | **Konsistenz `docs/rollenkette/LIESMICH.md`** — Baum **mit `E-integrationsprotokoll.md`**, zwei getrennte Rollen-Einträge, Übergabestück **E** in der Tabelle, **„SECHS SICHTEN" · „FÜNF STAFFELSTÄBE" · „sechs Sichten, fünf Übergabestücke" · „Sechs gleich gebaute Wissensordner"** — alle Mengenangaben des Dokuments geprüft | **UMGESETZT_UNGEPRUEFT** |
| P2G-08 | **Konsistenz `docs/rollenkette/START-PROMPT.md`** — Rollentrennung und Git-Disziplin um Integrator/Worktrees/Aktivierungs-SHA erweitert | **UMGESETZT_UNGEPRUEFT** |
| P2G-09 | **Unabhängige Abnahme durch den Plan-Prüfer** — Vollständigkeit, Widerspruchsfreiheit, Zuständigkeitsgrenzen, Trennung Evaluator/Release-Prüfer/Integrator, positive **und** negative Sperrfälle, Konsistenz mit Arbeitsregeln und Checkliste | **OFFEN** |
| P2G-25 | **Bootstrap-Variante ENTSCHIEDEN: B2** — Yama bzw. eine ausdrücklich von Yama autorisierte Infrastrukturhandlung legt die Rollen-Worktrees an. **Begründung: keine Rolle handelt vor Aktivierung ihrer Barriere.** Die Betriebsart `BOOTSTRAP` bleibt **nur als dokumentierter Notfallweg** bestehen und ist für diese Umstellung **NICHT freigegeben** | **UMGESETZT_UNGEPRUEFT** |
| P2G-26 | **Drei Betriebsarten definiert** — `NUR_LESEND` · `BOOTSTRAP` · `SCHREIBEND`, mit Erlaubt/Verboten je Art | **UMGESETZT_UNGEPRUEFT** |
| P2G-27 | **Ablauf A–K widerspruchsfrei** — lesend starten (D) **vor** prüfen (E), schreiben (J) **nach** fremder Barriereprüfung (I) | **UMGESETZT_UNGEPRUEFT** |
| P2G-28 | **Eingaben nach Vorgangstyp getrennt** — A Aktivierung · B Generator-Commit · C reiner Statusübergang · D Prüf-/Freigabedokument | **UMGESETZT_UNGEPRUEFT** |
| P2G-29 | **Eigentumsregel berichtigt** — uncommittiert ≠ committiert; ein committierter Block gehört dem Bestand | **UMGESETZT_UNGEPRUEFT** |
| P2G-30 | **Übergabevorlage `uebergaben/E-integrationsprotokoll.md`** — 19 Pflichtfelder, Vorgangstyp, Betriebsart, zwei Pflichtsätze am Ende | **UMGESETZT_UNGEPRUEFT** |
| P2G-31 | **`LIESMICH.md`-Baum: zwei getrennte Verzeichniseinträge** für `5-release-pruefer/` und `6-integrator/` | **UMGESETZT_UNGEPRUEFT** |

**Zeilenverweise geschützt, und der erste Versuch ging schief.** `docs/ARBEITSREGELN.md` trägt **22**
Zeilenverweise aus dem Bestand, 15 davon **hinter** der Einfügestelle. Mein erster Hinweis war über
drei Zeilen umbrochen — **`ARBEITSREGELN.md:693` (§14) zeigte danach auf eine leere Zeile.** Behoben
durch eine einzeilige Fassung; **alle neun geprüften Anker tragen wieder ihren Wortlaut.** Bei
`LIESMICH.md` liegen die vier echten Verweise (3, 29, 31, 36) vor der Änderung. *(Beiläufig gemessen:
das Repo hat **39** Dateien namens `LIESMICH.md` — die Verweise `:88-104` und `:98-104` gehören zu
`werkbank/` und `5-CODE/`, nicht hierher. Eine Zählung über den Dateinamen misst den falschen
Gegenstand.)*

**Nachbesserung vom 14.08. — vier Logikfehler, alle vier echt.** Vorgelegt von Yamas Prüfer,
behoben in Commit **`f3e7659e`**; das Rollenpaket selbst steht in **`047fc6fe`**.

| # | Fehler | Behebung |
|---|---|---|
| 1 | **Ablauf widersprüchlich** — Integrator prüfte bei D, startete bei E | Ablauf **A–K**: `NUR_LESEND` starten (D), dann prüfen (E), `SCHREIBEND` erst nach fremder Barriereprüfung (J) |
| 2 | **Bootstrap-Zirkel** — Schutz vor dem Schreiben, Schutz aber erst nach den Worktrees, Worktrees aber vom nicht-schreibberechtigten Integrator | **drei Betriebsarten** statt einer Ausnahme. `BOOTSTRAP` darf **genau eine** Sache: Worktrees anlegen. `git worktree add` schreibt in die **Verwaltung**, nicht in den Bestand |
| 3 | **Eingaben-Zirkel** — ein Release-Votum hätte ein Release-Votum vorausgesetzt | **vier Vorgangstypen.** Typ C verlangt **genau einen** zuständigen Rollenbeleg; Typ D darf nicht seine eigene Freigabe voraussetzen |
| 4 | **Eigentumsregel falsch** — *„fremde Arbeit gehört ihrem Autor“* | berichtigt: **uncommittiert** bleibt der Instanz zugeordnet *(Verlust irreversibel)*, **committiert** gehört dem Bestand *(Änderung nur durch beauftragten Korrekturvorgang)* |

**Zu Fehler 4, und es ist der schwerste:** Der Satz *„Ein committeter Block gehört dem Bestand, nicht
mehr dem Autor“* steht wörtlich in `rollen/1-planner/1-AUFTRAG.md` — **ich habe ihn selbst dorthin
geschrieben und im Integrator-Paket dagegen formuliert.** Eine Lehre, die an einer Stelle steht und
an der nächsten verletzt wird, ist keine Lehre, sondern eine Notiz.

**`UNABHAENGIG_BESTAETIGT` bleibt ausdrücklich ungesetzt** — bei allen 31 P2G-Punkten. Die Abnahme
ist P2G-09 und gehört dem Plan-Prüfer.

### Technische Rollenbarriere — **Bau: Generator, Prüfung: Evaluator**

| ID | Barriere | Status |
|---|---|---|
| P2G-10 | Integrator **nur** im Integrations-Checkout | **OFFEN** |
| P2G-11 | andere Rollen dort **schreibend gesperrt** | **OFFEN** |
| P2G-12 | `docs/STATUS.md` **außerhalb** des Integrations-Checkouts gesperrt | **OFFEN** |
| P2G-13 | Integrator in **fremden Rollen-Worktrees** gesperrt | **OFFEN** |
| P2G-14 | Prüfung **unmittelbar vor dem Schreiben UND erneut beim Commit** | **OFFEN** |
| P2G-15 | **echte Fehlerursache** ausgeben — Fehler nicht pauschal als Parsefehler melden *(siehe P2A-12)* | **OFFEN** |

### Sperrnachweise — positiv **und** negativ, je einzeln

| ID | Fall | Erwartung | Status |
|---|---|---|---|
| P2G-16 | Integrator + Integrations-Checkout | **erlaubt** | **OFFEN** |
| P2G-17 | Integrator + fremder Rollen-Worktree | gesperrt | **OFFEN** |
| P2G-18 | andere Rolle + Integrations-Checkout | gesperrt | **OFFEN** |
| P2G-19 | andere Rolle + `docs/STATUS.md` | gesperrt | **OFFEN** |
| P2G-20 | fehlende Rollenkennung | gesperrt | **OFFEN** |
| P2G-21 | falscher Branch | gesperrt | **OFFEN** |
| P2G-22 | fremde Änderungen im Vorgang | gesperrt | **OFFEN** |
| P2G-23 | **Veränderung zwischen Vorprüfung und Commit** | gesperrt | **OFFEN** |
| P2G-24 | unvollständige Übergabe | **Integration abgelehnt** | **OFFEN** |

**P2G-16 ist der wichtigste der neun, und er wird am leichtesten vergessen.** Ein Schutz, der nur
sperrt, ist von einem kaputten nicht zu unterscheiden — **es muss auch belegt sein, dass der
erlaubte Fall durchgeht.** A-30 hat das an zwölf Fehlalarmen gemessen, A-03 nennt die Folge: eine
Barriere, die zu oft falsch sperrt, wird weggeklickt.

**P2G-23 ist der einzige, der eine Zeitachse prüft.** Alle anderen fragen „wer, wo, was" — dieser
fragt „hat sich zwischen meiner Prüfung und meinem Commit etwas bewegt". **Das ist der Fall, der
heute Nacht dreimal eingetreten ist.**

---

## P3 — RACE- UND SCHUTZGEGENPROBEN

| ID | Test | Erwartung | Status |
|---|---|---|---|
| T-01 | zwei Rollen, verschiedene Dateien | zwei getrennte Commits, kein Fremdpfad | **OFFEN** |
| T-02 | zwei Rollen, **dieselbe** Datei | keine sieht fremde uncommittierte Arbeit; nötigenfalls **sichtbarer** Konflikt | **OFFEN** |
| T-03 | Rolle committet `STATUS.md` außerhalb der Integration | **harter Abbruch** | **OFFEN** |
| T-04 | normale Rolle committet im Integrations-Checkout | **harter Abbruch** | **OFFEN** |
| T-05 | Integrator bearbeitet `STATUS.md` | nur mit vollständiger Ursprungsangabe | **OFFEN** |
| T-06 | falsche `TICKET_ROLLE` | harter Abbruch | **OFFEN** |
| T-07 | fehlende `TICKET_ROLLE` | harter Abbruch | **OFFEN** |
| T-08 | falscher Branch | harter Abbruch | **OFFEN** |
| T-09 | Rollencommit scheitert | Änderungen bleiben **im eigenen Worktree** | **OFFEN** |
| T-10 | Integrationskonflikt | sichtbarer Stopp, **keine** automatische Seitenwahl | **OFFEN** |
| T-11 | committeter fremder Block lokal entfernt | Löschung im Diff sichtbar; **Abbruch VOR der Rücknahme** ² | **OFFEN** |
| T-12 | Regression **Richtung A** | Committender kann keine fremden uncommittierten Zeilen aufnehmen | **OFFEN** |
| T-13 | Regression **Richtung B** ³ | liegengebliebene Rollenarbeit ist **für andere Worktrees unsichtbar** | **OFFEN** |
| T-14 | bestehende `commit-pruefen.sh`-Suite | alle vorherigen Schutzfunktionen **grün** | **OFFEN** |
| T-15 | Syntax/Struktur aller geänderten Dateien | keine Fehler | **OFFEN** |

² **Planner-Zusatz zu T-11:** Der Fall ist am 14.08. **eingetreten** — der Plan-Prüfer nahm einen
bereits committeten Block zurück, das war eine Löschung aus dem Bestand, und **nur eine parallele
Vollschreibung hat sie zufällig aufgehoben.** Die Isolation **entfernt diesen Zufall.** Deshalb muss
T-11 den Abbruch **vor** der Rücknahme belegen, nicht die Sichtbarkeit danach.

³ **T-13 ist der tragende Test.** Er belegt eine *Eigenschaft*, keine Prüfung: in einem fremden
Worktree gibt es die liegengebliebenen Zeilen **nicht**. Alle anderen Tests prüfen Barrieren;
dieser prüft, dass es nichts zu prüfen gibt.

**Je Test verpflichtend:** Aufbau · Rot-Beleg **ohne** neue Barriere · ausgeführter Befehl ·
Exit-Code · **tatsächliche Ausgabe** · erwartetes Ergebnis · Grün/Rot · Wiederherstellung.
**Keine Testumgebung darf echte Arbeitsstände oder Branches löschen.**

---

## P4 — REGELWERK

| ID | Datei | Status |
|---|---|---|
| P4-01 | `docs/ARBEITSREGELN.md` — §14 auf Hunk-Ebene | **OFFEN** |
| P4-02 | `docs/ARBEITSREGELN.md` — §3 für Ablesungen (Yamas Punkt 16) | **OFFEN** |
| P4-03 | `docs/ARBEITSREGELN.md` — §16 Einzelschreiber | **OFFEN** |
| P4-04 | `docs/ARBEITSREGELN.md` — **Abschnitt Integrator** | **UMGESETZT_UNGEPRUEFT** — *(vorher „BLOCKIERT (Besetzung)“: überholt, die Rolle ist seit B-2 benannt. Nachtrag am Dateiende, Hinweis in der Release-Prüfer-Zeile, Commit `047fc6fe`.)* |
| P4-05 | `docs/rollenkette/START-PROMPT.md` | **UMGESETZT_UNGEPRUEFT** — Rollentrennung um Integrator erweitert, Git-Disziplin um Worktrees/Integrations-Checkout/`AKTIVIERUNGS_SHA`, **B2 als verbindliche Bootstrap-Variante** *(Commit `047fc6fe`, ergänzt in der Konsistenzkorrektur)* |
| P4-06 | `docs/rollenkette/LIESMICH.md` | **UMGESETZT_UNGEPRUEFT** — Baum, Übergabestück **E**, alle Mengenangaben *(Commit `047fc6fe` + Konsistenzkorrektur)* |
| P4-07..11 | **alle fünf Rollenmappen** — dieselbe verbindliche Aussage | **OFFEN** |
| P4-12 | Doku Commit-Tor | **OFFEN** |
| P4-13 | Doku Worktree-Erstellung | **OFFEN** |
| P4-14 | Doku Integration | **OFFEN** |
| P4-15 | Doku sichere Worktree-Entfernung | **OFFEN** |

**Überholte Aussagen, je einzeln zu suchen und als historisch zu kennzeichnen (nicht umschreiben):**
| ID | überholte Aussage — suchen und als **historisch kennzeichnen** (nicht umschreiben, A-20-4) | Status |
|---|---|---|
| P4-16 | gemeinsamer Arbeitsbaum | **OFFEN** |
| P4-17 | fünf Rollen in einer Datei | **OFFEN** |
| P4-18 | benannter Beifang *(Weg B verworfen)* | **OFFEN** |
| P4-19 | Schreiben+Committen als alleiniger Schutz | **OFFEN** |
| P4-20 | direkte `STATUS.md`-Bearbeitung | **OFFEN** |
| P4-21 | Lock-Räumung | **OFFEN** |
| P4-22 | Statusübergänge | **OFFEN** |
| P4-23 | `IN_ARBEIT` bei reinen Ablesungen | **OFFEN** |

---

## P5 — KONTROLLIERTE WIEDERAUFNAHME · STARTFREIGABE je Auftrag

**Zwölf Punkte je Auftrag** (Basis-SHA existiert · eindeutig · Blatt vollständig · Kriterien
vollständig · Fundstellen am Basis-SHA geprüft · Übergaben vorhanden · keine Kollision in
Scope/Übergabe · lokaler und vorgesehener Stand widerspruchsfrei · richtiger Worktree/Branch ·
nicht von Altfehler betroffen · §3 korrekt eingeordnet).

| ID | Auftrag | Status | Anmerkung |
|---|---|---|---|
| P5-01 | **A-33** | **OFFEN** | zuerst und allein; **entfällt möglicherweise mit der Aufteilung** (erzeugte Tafel) |
| P5-02 | **A-35** | **OFFEN** | Bau, DoR erteilt. **Der NACHBESSERN-Eintrag von 09:0x ist ZURUECKGENOMMEN** — siehe P5-02b. |
| P5-02b | **RUECKNAHME: A-35-9 fehlt KEIN Operand** | **ENTFÄLLT_MIT_BEGRUENDUNG** | Der Befund `5c46941c` („die 5 mm stehen nirgends im Blatt") ist vom Plan-Prüfer in `45f26bdb` **selbst zurückgenommen**: **A-35 nennt sie auf Zeile 113** — *„Zwei 6000-mm-Wände, 5 mm Versatz"* —, dazu eine Tabelle mit vier Winkel/Abstands-Paaren und dem Kernsatz zur Winkel-gegen-Abstands-Schwelle. **Von mir in einem Aufruf gegengeprüft: die Zeile steht da.** **MEIN ANTEIL, und er ist der schwerere:** ich habe die 5 mm *nachgerechnet und bestätigt* — aber **nicht nachgesehen, ob sie schon im Blatt stehen.** Ich habe eine Abwesenheits-Behauptung übernommen und mit einer Rechnung untermauert. **Seine Regel daraus gilt für mich genauso:** *eine Aussage über Abwesenheit braucht eine Suche über das GANZE Dokument, nicht über den Abschnitt — „steht nirgends" ist ein Zählwort und braucht nach B5 eine Belegzeile.* Seine Tabelle ist dabei **präziser als seine eigene Nachrechnung** (bei 0,000057° rechnet er mit gerundetem Winkel, das Blatt mit der exakten Schwelle). |
| P5-03 | **W-03/1** | **OFFEN** | Zeiger vollständig geprüft, 0 Funde |
| P5-04 | **W-10/1** | **BLOCKIERT** | **Blatt beschreibt HEAD, `basis_sha` beschreibt `18fe2deb`** — Punkt 5 der Freigabe nicht erfüllbar |
| P5-05 | **W-12/1** | **ENTFÄLLT_MIT_BEGRUENDUNG** | **Erledigt außerhalb der Umstellung:** `039aa7c4` (14.08. 10:08) setzt W-12/1 auf `ABGENOMMEN` — Evaluator Runde 2, Ball beim Release-Prüfer, `basis_sha: b778152b`. **Eine Startfreigabe ist gegenstandslos, wenn der Auftrag durch ist.** *(Gemessen in `docs/STATUS.md`, nicht aus der Commit-Botschaft geschlossen.)* |
| P5-06 | **W-14/1** | **OFFEN** | Zeiger berichtigt · **Kernmodul 9/9 zeichengenau an beiden Ständen** (`a6fa9c00`) — die 13/24 aus P5-10 betrifft ausschließlich Verbraucherdateien, **nicht den Gegenstand**. Ziehbar. |
| P5-07 | **W-16/1** | **OFFEN** | tragendes Kriterium berichtigt |
| P5-08 | **W-18/1** | **OFFEN** | vollständig geprüft, 0 Funde |
| P5-09 | **A-36** | **ENTFÄLLT_MIT_BEGRUENDUNG** | **Zurückgezogen durch V-02** (Yama, 14.08.). Es gibt keine Startfreigabe für einen zurückgezogenen Auftrag. **Verwertbare Inhalte laufen weiter unter `P2D-05`** (Hunk-Erkennung, §14-Verschärfung, historische Positivproben, Unterscheidung Dateiliste / `--numstat` / tatsächlicher Diff-Inhalt). Der Statusübergang im Blatt und in `docs/STATUS.md` erfolgt **ausschließlich durch den Integrator**. |
| P5-10 | **Zeigerprüfung aller sieben BEREIT-Blätter** | **UMGESETZT_UNGEPRUEFT** | vom Plan-Prüfer in `cb85bd0b`, 67 Zeiger gegen den je eigenen Schnitt. Ergebnistabelle steht unten. |
| P5-11 | **Regel: Vorwärts-Berichtigung zieht den `basis_sha` mit** | **UMGESETZT_UNGEPRUEFT** | Regeltext steht unten. Gilt ab sofort; die Aufnahme in `docs/ARBEITSREGELN.md` §5 ist **P4-24**. |
| P5-12 | **sechs `basis_sha` nachziehen** (W-10/1, W-12/1, W-14/1, W-16/1, W-03/1, A-33) | **BLOCKIERT** | Schreibzugriff auf fremde Blätter im gemeinsamen Checkout — hängt an **B-1**. |
| P4-24 | **§5 um den mitgezogenen `basis_sha` ergänzen** (Regeltext aus P5-11) | **OFFEN** | Regeländerung, gehört zu P4. Ohne sie lebt P5-11 nur in dieser Checkliste. |

**P5-10 — Ergebnistabelle** *(Status steht in der P5-Zeile oben, nicht hier)*:

| Blatt | stimmig am Schnitt | Anmerkung |
|---|---|---|
| W-18/1 | **8 / 8** | vollständig stimmig |
| W-16/1 | **9 / 9** | vollständig stimmig |
| A-35 | **4 / 4** | vollständig stimmig |
| W-10/1 | 15 / 17 | die bekannte Klasse |
| W-03/1 | 3 / 4 | `werkzeugLandkarte.ts:108` |
| **W-14/1** | 13 / 24 | **NEU EINGEORDNET nach `a6fa9c00`:** die Zahl ist die schärfste der sieben und **nach Sache irreführend.** Das **Kernmodul** `editierGeometrie.ts` hat **0 Commits seit dem Schnitt** und **9 von 9 zeichengenauen Zeigern** (Name *und* Zeilennummer). **Alle Abweichungen liegen bei den VERBRAUCHERN** — `HausplanerApp.tsx` (5), `werkzeugLandkarte.ts` (2), `sammelBefehle.ts` (4), also genau in den Dateien, die A-31 und A-29 **nach** dem Schnitt angefasst haben. **Der Gegenstand des Blatts ist an beiden Ständen exakt. W-14/1 ist ziehbar**; offen bleibt allein die `basis_sha`-Frage (P5-12). |
| A-33 | 0 / 1 | `a26-ball-drift.sh:53` |

**P5-11 — die Regel, die daraus folgt und die mir gefehlt hat** *(Status oben)*:

> **Eine Vorwärts-Berichtigung zieht den `basis_sha` mit. „Zeiger ziehen" allein bricht §5s
> „exakter Basis-SHA".**

**Beide Wege schließen sich aus** — dem Schnitt treu bleiben und Drift wachsen lassen, **oder** nach
vorn pflegen und mit `basis_sha` unstimmig werden. **Der saubere Weg ist der zweite MIT mitgezogenem
Schnitt; er kostet ein Feld.** *(Formulierung des Plan-Prüfers, übernommen.)*

**Mein Anteil, benannt:** Ich habe in der Nacht **fünf Blätter nach vorn gepflegt und keinen
einzigen `basis_sha` mitgezogen** — W-10/1, W-12/1, W-14/1, W-16/1, W-03/1, dazu A-33.
**Das war nicht Nachlässigkeit im Einzelfall: ich hatte die Regel nicht.** Sein Anteil ist die
andere Hälfte — er hat die Berichtigungen verlangt, ohne den Schnitt mitzufordern, und sagt es
selbst: *„Ich habe die Klasse erzeugt, die ich seit zwei Runden melde."*

**P5-12 — Nachziehen der sechs `basis_sha`** *(Status oben; Grund: B-1)*.

---

## P6 — RÜCKAUDIT · Pakete

| ID | Paket | Umfang | Status |
|---|---|---|---|
| P6-RA1 | die Beifang-Nacht | 13.08. 22:00 – 14.08. 09:00, **4 belegte Vollzüge** | **OFFEN** |
| P6-RA2 | Merge-Auflösungen | `51fb4d31` + 13 weitere Merge-Konflikte | **OFFEN** |
| P6-RA3 | Yamas 11 Startbelege | `93960252` `ef273926` `5ac659bf` `d2551e40` `e370490e` `4654687f` `2f8cf32d` `79bb3030` `0474f53b` `65e21b01` `51fb4d31` | **OFFEN** |
| P6-RA4 | claim-Zuordnungen | **128** Stellen mit `claim` im Fließtext | **OFFEN** |
| P6-RA5 | Zeiger-Klassen | 24 Fälle der Nacht + **42 Verweise im Produktivcode** + Klasse *„Blatt gepflegt, basis_sha nicht"* | **OFFEN** |
| P6-RA6 | Gegenprüfung | **ausdrücklich überlappend markiert** | **OFFEN** |

**Vorfallsmatrix-Felder** (21, wie vorgegeben) und die Trennung *nur falsche Zuordnung / doppelter
Inhalt / widersprüchlicher Inhalt / tatsächliche Löschung / tatsächliche Überschreibung / überholt
ohne Wirkung / heute wirksam* gelten je Vorfall. **Eine Commit-Botschaft ist kein Beleg.**

---

## P7 — RÜCKWIRKENDE REPARATUREN

**Kein History-Rewrite** (Punkt 17).

| ID | Reparaturregel | Status |
|---|---|---|
| P7-01 | fehlende Inhalte durch **neue** Commits | **OFFEN** |
| P7-02 | Zuordnungen richtigstellen | **OFFEN** |
| P7-03 | Doppelte erst nach Gegenprüfung | **OFFEN** |
| P7-04 | **nie** aufgrund vermuteter Autorenschaft entfernen | **OFFEN** |
| P7-05 | Produktivcode **nur** Generator | **OFFEN** |
| P7-06 | Prüfung **nur** Evaluator | **OFFEN** |
| P7-07 | Regelfehler Planner | **OFFEN** |
| P7-08 | Freigabe Release-Prüfer | **OFFEN** |
| P7-09 | vorher/nachher dokumentieren | **OFFEN** |
| P7-10 | **nur betroffene** Nachfolger erneut prüfen | **OFFEN** |

---

## P8 — UNABHÄNGIGE ABNAHME durch den Plan-Prüfer

**17 Kriterien**, je einzeln zu belegen (Regeltext=Umsetzung · alle fünf Rollen · getrennte
Worktrees · Integrator eindeutig · `STATUS.md` einschreiberbegrenzt · falsche Rolle/Branch/Worktree
abgefangen · **Richtung A mit realer rot/grün-Gegenprobe** · **Richtung B mit realer
rot/grün-Gegenprobe** · fehlgeschlagene Commits ohne fremdes Fenster · Konflikte stoppen sichtbar ·
keine Barriere abgeschwächt · keine fremde Arbeit übernommen · historische Belege unverfälscht ·
STARTFREIGABE wirksam · Rückaudit geplant und zerlegt · **kein** Push/Merge/Rebase/Rewrite · jeder
Pflichtpunkt vorhanden). **Ergebnis nur: `FREIGABE` · `NACHBESSERN` · `ENV_BLOCKED`.**
**Alle 17: OFFEN.**

---

## PAR — PARALLELPAKETE

| Feld | Pflicht |
|---|---|
Paket-ID · Rolle · Agent/Instanz · Worktree · Branch · Basis-SHA · erlaubte Pfade · verbotene Pfade · Testressourcen · Abhängigkeiten · darf parallel mit · darf **nicht** parallel mit · Status · Ergebniscommit · Prüfcommit · Integrationsstatus | **je Paket vollständig** |

| ID | Paket | Rolle | Worktree / Branch | Status |
|---|---|---|---|---|
| PAR-01 | Regelwerk + Plan (Spur A) | **Planner** | `ticket-rolle-planner` / `rolle/planner` · Basis `36e60030` · **verboten: `docs/STATUS.md`** | **UMGESETZT_UNGEPRUEFT** — Worktree steht, Basis festgeschrieben. *(Vorher stand hier `IN_ARBEIT`: das ist §3-Vokabular für **Aufträge**, nicht für Checklistenpunkte. Zwei Vokabulare in einer Spalte machen jede Zählung unbrauchbar.)* |
| PAR-02 | Planprüfung (Welle 2) | Plan-Prüfer | eigener Worktree nötig | **BLOCKIERT** — Worktree fehlt |
| PAR-RA | Rückaudit-Pakete **RA1–RA6** (Spur B) — **geführt unter `P6-RA1..RA6`, hier nur die Parallelisierungs-Achse** | Prüfagenten | je eigener Prüf-Worktree, **überschneidungsfrei** | **OFFEN** |
| | *(vorher **eine** Sammelzeile für sechs Pakete. Sie einzeln zu numerieren hätte sechs zweite Wahrheiten neben `P6-RA1..RA6` geschaffen — dieselben Gegenstände unter zwei Kennungen. Die alte Kennung steht hier bewusst **nicht** ausgeschrieben: ein Zählskript kann eine historische Nennung nicht von einem Punkt unterscheiden.)* | | | |
| PAR-09 | Testvorbereitung (Spur C) | Evaluator | eigener Worktree, **kein Vorbau des Generatorcodes** | **OFFEN** |
| PAR-10 | aktive Werkzeugaufträge (Spur D) | Generator(en) | getrennte Worktrees/Branches, **nicht überlappende Scopes**, **keine gemeinsame veränderliche DB** | **BLOCKIERT** bis Aktivierung |

**Vor Start eines parallelen Agenten muss belegt sein:** eigener Worktree · eigener Branch ·
Basis-SHA festgeschrieben · Scope überschneidungsfrei (oder als Gegenprobe markiert) · keine
gemeinsame veränderliche Datenbank · **kein Zugriff auf `docs/STATUS.md`** · eindeutige Rückgabe.
**Fehlt einer, startet der Agent nicht schreibend.**

---

## ⛔ WAS OFFEN IST — und in welchem Zustand genau

*(Die frühere Fassung dieses Blocks nannte B-1 „nicht angeordnet" und B-2 „nicht besetzt". **Beides
ist überholt:** Yama hat am 14.08. entschieden. Was fehlt, ist nicht die Entscheidung, sondern der
Vollzug — und das ist ein anderer Zustand, der anders zu belegen ist.)*

| # | tatsächlicher Zustand | was fehlt | wirkt auf |
|---|---|---|---|
| **B-1** | **Schreibstopp beschlossen, aber noch nicht vollständig zugestellt und nicht wirksam nachgewiesen.** | Zustellung an vier Instanzen · Einzelbeleg je Instanz · Ruhephase · sauberer Baum · keine aktiven Git-Schreiber | P2F-01..05, P5-12, alle Worktree-Punkte |
| **B-2** | **Eigener sechster Integrator beschlossen und benannt** (`TICKET_ROLLE=integrator`), **aber die Instanz ist nicht gestartet und der Integrations-Checkout nicht aktiviert.** | Instanzstart · Aktivierung des Checkouts · Trennungsbeleg gegen Evaluator/Release-Prüfer | P2C-01..05, P2E-01..12, P2F-06..14 |

**Was ich selbst nicht kann, und warum es keine Ausrede ist:** Die vier Rollen sind eigene
Instanzen. Mein einziger Kanal zu ihnen wäre ein Commit im gemeinsamen Checkout — genau dort darf
ich nicht schreiben. **Die Zustellung liegt bei Yama, der die Instanzen fährt.** Alles, was ich
danach brauche, ist vorbereitet.

---

## ZYKLUSPFLICHT

**Am Anfang jedes Arbeitszyklus:** Checkliste lesen · offene und blockierte Punkte **zählen** ·
aktuellen Punkt benennen · Voraussetzungen prüfen.
**Am Ende:** Checkliste aktualisieren · **tatsächlich ausgeführte** Prüfungen eintragen ·
**nicht ausgeführte ausdrücklich nennen** · nächsten konkreten Punkt festlegen.

**Zählstand — gerechnet, nicht fortgeschrieben.** *Gilt für **den Commit, der diese Fassung trägt**, nicht für die Datei im Arbeitsbaum. Wer die Datei ändert, rechnet mit dem Befehl unten neu — die Zahlen sind während der Erstellung **zweimal** gedriftet.*

| Status | Anzahl |
|---|---|
| `OFFEN` | **112** |
| `UMGESETZT_UNGEPRUEFT` | **63** |
| `BLOCKIERT` | **21** |
| `NACHBESSERN` | **2** |
| `ENTFÄLLT_MIT_BEGRUENDUNG` | **5** |
| `UNABHAENGIG_BESTAETIGT` | **0** — **keiner, und das ist richtig: P8 hat nicht begonnen** |
| **Summe** | **203 = alle IDs** |

**Diese Zahlen sind ein Abzug, kein Zustand.** Sie sind **während der Erstellung zweimal gedriftet**
— `P1-07b` machte aus 136 IDs 137, `P2A-11`/`P2A-12` daraus 139, der neue Block **P2F** daraus 155, **P2G** daraus 179, die Nachbesserung `P2G-25..31` daraus 186, der Mechanismuswechsel `P2H` daraus 193. **Diesmal wurde das Muster BEIM Anlegen mitgeändert** (`[A-G]`→`[A-H]`), nicht hinterher bemerkt — die Lehre aus drei Fehlversuchen hat gehalten.
**Und das Muster war ein drittes Mal zu eng:** es verlangte eine Ziffer und übersah damit `PAR-RA`.
**Zusammen mit `P2F` und `P2G` sind das drei Blöcke, die der eigene Prüfer nicht sah** — die Lehre
steht oben und gilt: das Muster wird beim Anlegen mitgeändert, nicht danach bemerkt.
**Bei Abweichung gilt der Befehl, nicht die Tabelle.** Und der Befehl selbst war **zweimal** zu eng: sein Muster
kannte erst `P2F` nicht (`[A-E]`, sechzehn Punkte unsichtbar), dann `P2G` nicht (`[A-F]`, **weitere
vierundzwanzig**). **Ein Zählskript, das einen ganzen Block nicht sieht, hat denselben Mangel, den es
finden soll — und dass mir dieselbe Grenze zweimal innerhalb einer Stunde entgangen ist, heißt: das
Muster muss beim Anlegen eines neuen Blocks mitgeändert werden, nicht danach bemerkt.**

**Der Messbefehl steht hier, damit niemand einer festen Zahl glauben muss** — feste Zahlen driften,
das ist an dieser Datei zweimal belegt:

```
python3 - <<'EOF'
import re; from collections import Counter
L=open('docs/rollenkette/UMSTELLUNG-GETRENNTE-WORKTREES-CHECKLISTE.md',encoding='utf-8').read().split('\n')
pat=re.compile(r'^\|\s*\*{0,2}(PAR-RA|(?:V|P[0-8][A-H]?|T|PAR)-(?:RA)?[0-9]+[a-z]?)\*{0,2}\s*\|')  # ID nur aus SPALTE 1; PAR-RA traegt keine Ziffer
ST=re.compile(r'\*\*(UMGESETZT_UNGEPRUEFT|OFFEN|BLOCKIERT|NACHBESSERN|ENTF\u00c4LLT_MIT_BEGRUENDUNG|UNABHAENGIG_BESTAETIGT)\*\*')
P3=re.compile(r'\*\*(ENTWURF|BEREIT|IN_ARBEIT|CODE_FERTIG|ABNAHME|ABGENOMMEN|RELEASE_PRUEFUNG|RELEASE_FREI|VEROEFFENTLICHT|BETRIEBSBESTAETIGT|SPEC_BLOCKED|ENV_BLOCKED|DECISION_BLOCKED)\*\*')
alle=set(); mit={}; fremd=[]
for l in L:
    m0=pat.match(l); ids=[m0.group(1)] if m0 else []
    alle.update(ids)
    if ids:
        m=ST.search(l)
        if m: mit.setdefault(ids[0],m.group(1))
        fremd += [(ids[0],f.group(1)) for f in P3.finditer(l)]
print('IDs',len(alle),'mit Status',len(mit),'ohne',sorted(alle-set(mit)))
print('§3-Wort ALS STATUS:',fremd)   # muss [] sein
print(Counter(mit.values()))
EOF
```

**`ohne` muss leer sein.** Ist es das nicht, ist ein Pflichtpunkt für jede Zählung unsichtbar —
genau der Mangel, der bei der ersten Fassung 36 Punkte verborgen hat.

**Was sich gegenüber der ersten Fassung geändert hat, und warum die offenen Punkte von 57 auf 92
gestiegen sind:** Sie sind nicht neu entstanden. **36 Pflichtpunkte standen als Fließtext**
(`P2A-08..10`, `P2E-01..12`, `P4-16..23`, `P5-10..12`, `P7-01..10`) — sachlich mit Status
(*„alle zwölf: OFFEN"*), aber **keiner Kennung zuzuordnen**. Deshalb *konnte* keine Zählung stimmen.
**Dieselbe Fehlerfamilie**, die der Plan-Prüfer am selben Tag dreimal an seiner eigenen Ballortung
fand: ein Punkt, den das Suchmuster nicht sieht, ist für den Betrieb nicht vorhanden.

## VERBINDLICHE REIHENFOLGE — Yamas Festlegung vom 14.08.

*(Die frühere Fassung dieses Abschnitts verlangte hier eine Entscheidung zu V-02. **V-02 ist
entschieden** und darf nicht erneut als offen dargestellt werden.)*

| Schritt | Handlung | wer | Zustand |
|---|---|---|---|
| **a** | Schreibstopp an Plan-Prüfer, Generator, Evaluator, Release-Prüfer **zustellen** | **Yama** | **offen — der Engpass** |
| **b** | Zustellung **oder Beendigung** jeder Rolleninstanz **einzeln** belegen | Yama | offen |
| **c** | Gemeinsamen Checkout prüfen: sauberer Arbeitsbaum · **aktive Git-Schreiber** · neue Rollen-Commits | Planner *(lesend)* | wartet auf a/b |
| **d** | Festgelegte **Ruhephase ohne Rollen-Commit** nachweisen | Planner *(lesend)* | wartet auf a/b |
| **e** | Eigenen sechsten **Integrator starten** | Yama | offen |
| **f** | Integrator untersucht die **Divergenz** des gemeinsamen Branches und bestimmt **erst danach** den `AKTIVIERUNGS_SHA` | **Integrator** | wartet auf e |
| **g** | **Erst vom freigegebenen `AKTIVIERUNGS_SHA`** die vier übrigen Rollen-Worktrees anlegen | Integrator/Planner | wartet auf f |

**Nachgeschärft am 14.08. — Yamas Ablaufplan A–K.** *(Er ersetzt den früheren A–J, in dem der
Integrator bei D prüfte und erst bei E startete — **ein Prüfer, der noch nicht läuft, prüft
nichts.** Die alte Fassung ist damit überholt; maßgeblich ist ausschließlich diese.)*

| Schritt | Handlung | wer | Betriebsart | Status wohnt bei |
|---|---|---|---|---|
| **A** | Rollenpaket **fertigstellen** | Planner | — | Stand: `P2G-01..08`, `P2G-26..31` |
| **B** | **unabhängig prüfen** | **Plan-Prüfer** | — | Stand: `P2G-09` |
| **C** | **vier Schreibstopps einzeln** nachweisen | Yama | — | Stand: `P2F-01`, `P2F-05` |
| **D** | Integrator **`NUR_LESEND` starten** | Yama | `NUR_LESEND` | Stand: `P2F-06` |
| **E** | Ruhephase, Prozesse, Arbeitsbaum und **Divergenz** prüfen | Integrator | `NUR_LESEND` | Stand: `P2F-02..05`, `P2F-09..14` |
| **F** | `AKTIVIERUNGS_SHA` bestimmen und **nur berichten** | Integrator | `NUR_LESEND` | Stand: `P2F-08` |
| **G** | Worktrees **gemäß B2 durch Yama** anlegen | **Yama** | — *(nicht `BOOTSTRAP`)* | Stand: `P2A-02..06` |
| **H** | Generator baut die **technischen Barrieren** | Generator | — | Stand: `P2G-10..15` |
| **I** | Evaluator prüft **positive und negative** Fälle | Evaluator | — | Stand: `P2G-16..24` |
| **J** | Integrator auf **`SCHREIBEND`** freigeben | Yama | `SCHREIBEND` | offen |
| **K** | **regulären Rollenbetrieb** freigeben | Yama | — | offen |

**Die letzte Spalte nennt die Punkte, KEINEN Status.** Die Ablaufschritte A–K sind eine Sicht auf die
Punkte, keine zweite Buchführung — **ein Status an zwei Orten driftet, und diese Datei hat es dreimal
vorgeführt.** *(Gefunden vom eigenen Vollständigkeitsprüfer: er meldete `P2G-01`, `P2G-09` und
`P6-RA1` als mehrfach bestatust. Es waren Querverweise — aber der Prüfer nahm die **erste ID der
Zeile** statt die **ID in Spalte 1**. Beides ist berichtigt: der Prüfer liest Spalte 1, und die
Ablaufzeilen tragen keinen Status mehr.)*

**Schritt G trägt die Entscheidung P2G-25:** die Worktrees legt **Yama** an, nicht der Integrator.
**Die Betriebsart `BOOTSTRAP` bleibt dokumentiert, ist aber für diese Umstellung nicht freigegeben** —
**die bloße Dokumentation einer Betriebsart ist keine Erlaubnis, sie zu benutzen.**

**A ist erledigt und `UMGESETZT_UNGEPRUEFT`, B ist der nächste Schritt — und B gehört nicht mir.**
Ein Planner, der sein eigenes Rollenpaket abnimmt, ist genau der Fehler, gegen den die sechste Rolle
gebaut wird.

**Der Engpass liegt nicht mehr beim Planen.** Schritte a, b und e kann nur Yama vollziehen; c und d
sind rein lesende Messungen, die ich sofort fahre, sobald a/b vorliegen; f gehört dem Integrator.
**Nichts davon wartet auf eine weitere Planungsrunde.**

**Was ich in der Zwischenzeit NICHT tue:** keine Worktrees anlegen · nicht in den gemeinsamen
Checkout schreiben · keine Rolle aktivieren · **den `FORENSISCHEN_SHA` nicht als Arbeitsbasis
verwenden.**
