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
| **Status der Umstellung** | **`UMSTELLUNG_OFFEN`** |

**Zulässige Statuswerte:** `OFFEN` · `IN_ARBEIT` · `BLOCKIERT` · `UMGESETZT_UNGEPRUEFT` ·
`NACHBESSERN` · `UNABHAENGIG_BESTAETIGT` · `ENTFÄLLT_MIT_BEGRUENDUNG`

**`UNABHAENGIG_BESTAETIGT` darf nur stehen, wenn ein anderer Prüfer den exakten Umsetzungscommit
geprüft und den tatsächlichen Beleg eingetragen hat.**

---

## ⚠ V-BLOCK — Vorbelastung: A-36 widerspricht dieser Entscheidung

| ID | Beschreibung | Rolle | Dateien | Erwartet | Rot-Beleg | Grün-Beleg | Commit | Prüf-SHA | Status | Abweichung |
|---|---|---|---|---|---|---|---|---|---|---|
| V-01 | **A-36 erklärt die Worktree-Lösung zum Nicht-Ziel und ist damit überholt** | Planner | `docs/auftraege/aktiv/A-36-*.md:78` | Widerspruch aufgelöst | *gemessen 14.08.: Zeile 78 sagt „Yama hat sie ausdrücklich **nicht** entschieden"* | — | — | — | **OFFEN** | Entscheidung V-02 nötig |
| V-02 | **Entscheidung: A-36 zurückziehen (`SPEC_BLOCKED`) oder als P2D-Teilschritt unterordnen** | Planner | A-36-Blatt, `docs/STATUS.md` | eine benannte Wahl | A-36 steht auf `ENTWURF`, DoR nicht erteilt, vier Punkte fehlen | — | — | — | **OFFEN** | siehe Vorschlag unten |
| V-03 | A-36s vier fehlende DoR-Punkte (Testdaten/Rolle/Route/Browserpfad · API/Schema/Migration/Bundle · Erstnutzer · Abhängigkeitskette) | Planner | A-36-Blatt | ergänzt **oder** entfällt mit V-02 | Befund des Plan-Prüfers in `docs/STATUS.md` | — | — | — | **BLOCKIERT** | hängt an V-02 |
| V-04 | **A-36-3 ist an einem Wort unerfüllbar** — es verlangt „mehr als eine KENNUNG", richtig ist „mehr als ein ABSCHNITT" | Planner | A-36-Blatt, Kriterium A-36-3 | ein Wort getauscht | **gemeldet `902c83f3`, nachgerechnet: bei `93960252` trägt KEINER der zwei berührten Abschnitte eine Kennung** — über Abschnitte gezählt zwei (grün), über Kennungen ein Wert (**rot und durch keinen Bau behebbar**) | — | — | — | **BLOCKIERT** | dieselben drei Wege wie P5-02b |

**Planner-Vorschlag zu V-02, zur Entscheidung durch Yama:** **Unterordnen, nicht zurückziehen.**
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
| P2C-01 | Änderung an `STATUS.md` außerhalb der Integration → abgelehnt | **BLOCKIERT** — Integrator nicht benannt |
| P2C-02 | Commit mit `STATUS.md` von normaler Rolle → abgelehnt | **BLOCKIERT** |
| P2C-03 | im Integrations-Checkout nur Integrator | **BLOCKIERT** |
| P2C-04 | Statusübergang ohne Übergabestück + Ursprungscommit → abgelehnt | **BLOCKIERT** |
| P2C-05 | Integrationscommit ohne benannte Fremdpfade → abgelehnt | **BLOCKIERT** |

**Blocker für P2C-01..05:** **Der Integrator ist nicht besetzt.** Yamas Text nennt ihn oder einen
eigenen Agenten; **eine Fachrolle darf nicht stillschweigend hineinrutschen.** Ohne Besetzung ist
die Sperre nicht baubar, weil ihr Subjekt fehlt.

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
| P4-04 | `docs/ARBEITSREGELN.md` — **neuer Abschnitt Integrator** | **BLOCKIERT** (Besetzung) |
| P4-05 | `docs/rollenkette/START-PROMPT.md` | **OFFEN** |
| P4-06 | `docs/rollenkette/LIESMICH.md` | **OFFEN** |
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
| P5-05 | **W-12/1** | **NACHBESSERN** | läuft, Ball beim Generator |
| P5-06 | **W-14/1** | **OFFEN** | Zeiger berichtigt · **Kernmodul 9/9 zeichengenau an beiden Ständen** (`a6fa9c00`) — die 13/24 aus P5-10 betrifft ausschließlich Verbraucherdateien, **nicht den Gegenstand**. Ziehbar. |
| P5-07 | **W-16/1** | **OFFEN** | tragendes Kriterium berichtigt |
| P5-08 | **W-18/1** | **OFFEN** | vollständig geprüft, 0 Funde |
| P5-09 | **A-36** | **BLOCKIERT** | siehe V-01..V-03 |
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

## ⛔ ZWEI BLOCKER, die ich nicht selbst auflösen kann

| # | Blocker | Warum ich es nicht kann | Wirkt auf |
|---|---|---|---|
| **B-1** | **Der Schreibstopp für die vier anderen Rollen ist nicht angeordnet** | *„Vor der Trennung schreibt nur der Planner"* — vier Rollen schreiben in diesem Moment weiter (letzter fremder Commit `36e60030`). **Ich kann keine Instanz anhalten.** | Welle 1→2, P0-08, PAR-02 |
| **B-2** | **Der Integrator ist nicht besetzt** | Yamas Text nennt ihn **oder** einen eigenen Agenten; **eine Fachrolle darf nicht stillschweigend hineinrutschen.** | **P2C-01..05, P4-04, Welle 6** |

---

## ZYKLUSPFLICHT

**Am Anfang jedes Arbeitszyklus:** Checkliste lesen · offene und blockierte Punkte **zählen** ·
aktuellen Punkt benennen · Voraussetzungen prüfen.
**Am Ende:** Checkliste aktualisieren · **tatsächlich ausgeführte** Prüfungen eintragen ·
**nicht ausgeführte ausdrücklich nennen** · nächsten konkreten Punkt festlegen.

**Zählstand — gerechnet, nicht fortgeschrieben.** *Gilt für **den Commit, der diese Fassung trägt**, nicht für die Datei im Arbeitsbaum. Wer die Datei ändert, rechnet mit dem Befehl unten neu — die Zahlen sind während der Erstellung **zweimal** gedriftet.*

| Status | Anzahl |
|---|---|
| `OFFEN` | **93** |
| `UMGESETZT_UNGEPRUEFT` | **31** |
| `BLOCKIERT` | **13** |
| `NACHBESSERN` | **1** |
| `ENTFÄLLT_MIT_BEGRUENDUNG` | **1** |
| `UNABHAENGIG_BESTAETIGT` | **0** — **keiner, und das ist richtig: P8 hat nicht begonnen** |
| **Summe** | **139 = alle IDs** |

**Diese Zahlen sind ein Abzug, kein Zustand.** Sie sind **während der Erstellung zweimal gedriftet**
— `P1-07b` machte aus 136 IDs 137, `P2A-11`/`P2A-12` daraus 139. **Bei Abweichung gilt der Befehl,
nicht die Tabelle.**

**Der Messbefehl steht hier, damit niemand einer festen Zahl glauben muss** — feste Zahlen driften,
das ist an dieser Datei zweimal belegt:

```
python3 - <<'EOF'
import re; from collections import Counter
L=open('docs/rollenkette/UMSTELLUNG-GETRENNTE-WORKTREES-CHECKLISTE.md',encoding='utf-8').read().split('\n')
pat=re.compile(r'\b((?:V|P[0-8][A-E]?|T|PAR)-(?:RA)?[0-9]+[a-z]?)\b')
ST=re.compile(r'\*\*(UMGESETZT_UNGEPRUEFT|OFFEN|BLOCKIERT|NACHBESSERN|ENTF\u00c4LLT_MIT_BEGRUENDUNG|UNABHAENGIG_BESTAETIGT)\*\*')
alle=set(); mit={}
for l in L:
    ids=pat.findall(l); alle.update(ids)
    if l.startswith('| ') and ids:
        m=ST.search(l)
        if m: mit.setdefault(ids[0],m.group(1))
print('IDs',len(alle),'mit Status',len(mit),'ohne',sorted(alle-set(mit)))
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

**Nächster konkreter Punkt: V-02** — Yamas Entscheidung, ob A-36 zurückgezogen oder untergeordnet
wird. **Danach P2A-02..06** (die vier fehlenden Rollen-Worktrees und der Integrations-Checkout),
**sobald B-1 und B-2 aufgelöst sind.**
