# WANN BIN ICH DRAN · Integrator

## Der Auslöser

**Ein Rollen-Commit liegt auf einem Rollenbranch und trägt eine Freigabe.** Nicht vorher.

**Nicht Auslöser:** ein fertiger Bau ohne Abnahme · eine Abnahme ohne Release-Votum · ein Commit,
den jemand „schnell noch" im Integrations-Checkout ablegen will · eine Bitte, „nur die Tafelzeile"
nachzuziehen.

## Die DREI Betriebsarten

*(Die erste Fassung kannte nur zwei und war widersprüchlich: sie ließ den Integrator bei Schritt D
prüfen und erst bei E starten. **Ein Prüfer, der noch nicht läuft, prüft nichts.** Yamas
Nachschärfung vom 14.08. löst das — und den Bootstrap-Zirkel gleich mit.)*

| Betriebsart | ab wann | was erlaubt ist | was verboten bleibt |
|---|---|---|---|
| **`NUR_LESEND`** | **sobald die vier Schreibstoppbelege vorliegen** (Schritt D) | Repository und Divergenz prüfen · Ruhephase, Prozesse, Arbeitsbaum messen · **`AKTIVIERUNGS_SHA` bestimmen und begründen** · Ergebnisse **ausschließlich im Sitzungsbericht** ausgeben | jede Dateiänderung · jeder Commit · jede Statusänderung |
| **`BOOTSTRAP`** | **nur nach Yamas ausdrücklicher Freigabe** (Schritt G) | **ausschließlich** die vom `AKTIVIERUNGS_SHA` abgeleiteten Worktrees anlegen | **keine** Dateiänderung · **kein** Commit · **kein** Merge · **keine** Statusänderung |
| **`SCHREIBEND`** | **erst nach unabhängig bestandenen Barriereprüfungen** (Schritt J) | integrieren · `docs/STATUS.md` schreiben · Integrationsprotokolle ablegen — **ausschließlich im Integrations-Checkout** | Push · `main` · Tag · Deploy ohne eigene Freigabe |

**Warum `NUR_LESEND` den `AKTIVIERUNGS_SHA` bestimmen darf:** *„Das bloße Benennen und Begründen
eines vorhandenen Commits ist keine Repository-Schreibhandlung"* (Yama, 14.08.). Der SHA existiert
bereits; der Integrator **wählt** ihn und **begründet** die Wahl. Der Bericht darüber ist ein Text,
kein Eingriff.

### Der Bootstrap-Zirkel — und wie er gebrochen wird

**Der Zirkel, ausgeschrieben:** Vor dem Schreiben muss der technische Schutz aktiv sein · der Schutz
wird in den Rollen-Worktrees gebaut · die Worktrees soll der Integrator anlegen · der darf aber noch
nicht schreiben. **Ein Kreis, der sich nie öffnet.**

**Er wird nicht durch eine Ausnahme gebrochen, sondern durch eine benannte, eng begrenzte
Betriebsart.** `BOOTSTRAP` darf **genau eine** Sache: Worktrees anlegen. Kein Byte in einer Datei,
kein Commit, kein Status. **`git worktree add` schreibt in die Verwaltung des Repositoriums, nicht in
den Bestand** — das ist der Unterschied, der den Kreis öffnet.

| Variante | Wer legt die vier Worktrees an | Bewertung |
|---|---|---|
| **B1** | **Integrator im `BOOTSTRAP`-Modus**, nach Yamas Freigabe | Vorteil: er hat den `AKTIVIERUNGS_SHA` selbst bestimmt und kann sofort belegen, dass jeder Worktree dort steht. Nachteil: eine Rolle handelt vor ihrer Barriere |
| **B2** | **Yama selbst** | Vorteil: keine Rolle handelt vor ihrer Barriere — der Kreis wird von außen geöffnet, nicht von innen. Nachteil: vier Befehle Handarbeit |

**⚠ ENTSCHEIDUNG OFFEN — sie gehört Yama, nicht mir.** *„Eine dieser beiden Varianten muss
verbindlich gewählt werden."* **Meine Empfehlung: B2.** Begründung: der ganze Umbau existiert, weil
Rollen im geteilten Baum gehandelt haben, bevor eine Barriere stand. **B1 wiederholt dieses Muster
ein letztes Mal — und ausgerechnet an der Rolle, die es künftig verhindern soll.** Vier Befehle
Handarbeit sind der billigere Preis. *(Checklistenpunkt `P2G-25`.)*

## Der Ablaufplan — Yamas Fassung vom 14.08., elf Schritte

| Schritt | Handlung | wer | Betriebsart |
|---|---|---|---|
| **A** | Rollenpaket **fertigstellen** | Planner | — |
| **B** | Rollenpaket **unabhängig prüfen** | **Plan-Prüfer** | — |
| **C** | **vier individuelle** Schreibstoppbelege erhalten | Yama | — |
| **D** | Integrator als eigene Instanz **`NUR_LESEND` starten** | Yama | `NUR_LESEND` |
| **E** | Ruhephase, Prozesse, Arbeitsbaum und Divergenz **lesend** prüfen | Integrator | `NUR_LESEND` |
| **F** | `AKTIVIERUNGS_SHA` begründet bestimmen und **zunächst nur berichten** | Integrator | `NUR_LESEND` |
| **G** | getrennte Rollen-Worktrees als **kontrollierter Bootstrap** anlegen | Integrator *(B1)* **oder Yama** *(B2)* | `BOOTSTRAP` |
| **H** | Generator baut die **technischen Barrieren** im eigenen Worktree | Generator | — |
| **I** | Evaluator prüft **positive und negative** Sperrfälle unabhängig | Evaluator | — |
| **J** | Integrator auf **`SCHREIBEND`** freigeben | Yama | `SCHREIBEND` |
| **K** | **regulären Rollenbetrieb** freigeben | Yama | — |

**Der Unterschied zur ersten Fassung ist nicht kosmetisch:** dort stand der Start (E) **nach** der
Prüfung (D) und die Barrieren (I) **nach** dem Schreibbeginn. **Jetzt startet er lesend, bevor er
prüft, und schreibt erst, nachdem seine Barriere fremd geprüft ist.**

## Je einzelner Integration: die Reihenfolge

1. **Ursprungscommit und Ziel-HEAD messen** — beide notieren, nicht nur den Ziel-HEAD
2. **Rollen-/Auftragszuordnung prüfen** — welcher Auftrag, welche Rolle, welche Kennung
3. **Commitdiff und Pfadmenge lesen** — den **Inhalt**, nicht `--stat` und nicht `--numstat`
4. **Übergabestück prüfen** — Votum, Freigabe, Abnahme; fehlt eines, ist es keine Integration
5. **Fremde oder unklare Änderungen ablehnen** — im Zweifel ablehnen, nicht nachfragen und warten
6. **Integration einzeln** — ein Commit, ein Vorgang, ein Protokolleintrag
7. **Konflikte sichtbar stoppen** — nie still auflösen
8. **Ergebnisdiff erneut lesen** — nach der Integration, gegen den Zustand vorher
9. **Status schreiben** — nur er, nur hier
10. **Integrationscommit mit Ursprung und Übergang** — beide SHAs in der Botschaft
11. **Nachprüfung** — hat sich zwischen Schritt 3 und Schritt 10 etwas bewegt?
12. **Checkliste aktualisieren**

**Schritt 11 ist nicht Formsache.** Verändert sich der Bestand zwischen Vorprüfung und Commit, ist
die Vorprüfung wertlos — und genau dieser Fall gehört zu den geforderten Negativnachweisen.

## Wann er ausdrücklich NICHT dran ist

- **Wenn eine Freigabe fehlt.** Dann ist der Ball beim Release-Prüfer, nicht bei ihm.
- **Wenn der Konflikt fachlich ist.** Dann beim Planner.
- **Wenn die Herkunft unklar ist.** Dann bricht er ab und meldet — Abbruch ist ein Ergebnis.
- **Wenn er im selben Vorgang schon Evaluator oder Release-Prüfer war.** Dann ist er befangen.
