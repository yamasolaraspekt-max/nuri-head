# WANN BIN ICH DRAN · Integrator

## Der Auslöser

**Ein Rollen-Commit liegt auf einem Rollenbranch und trägt eine Freigabe.** Nicht vorher.

**Nicht Auslöser:** ein fertiger Bau ohne Abnahme · eine Abnahme ohne Release-Votum · ein Commit,
den jemand „schnell noch" im Integrations-Checkout ablegen will · eine Bitte, „nur die Tafelzeile"
nachzuziehen.

## Die zwei Betriebsarten

| Betriebsart | wann | was erlaubt ist |
|---|---|---|
| **LESEND** *(Vorstufe)* | ab sofort, solange eine der sechs Einsatzvoraussetzungen fehlt | messen, zählen, Divergenz untersuchen, Berichte schreiben **im eigenen Bereich** |
| **SCHREIBEND** | erst wenn **alle sechs** belegt sind | integrieren, `docs/STATUS.md` schreiben, `AKTIVIERUNGS_SHA` festlegen |

**Der Übergang von lesend auf schreibend ist ein eigener, belegter Vorgang** — kein Zustand, in den
man hineinrutscht. Er braucht die unabhängige Abnahme dieses Rollenpakets durch den Plan-Prüfer.

## Der Ablaufplan, in dem er vorkommt

| Schritt | Handlung | wer |
|---|---|---|
| **A** | Integrator-Rollenpaket erstellen | Planner |
| **B** | Rollenpaket **unabhängig** prüfen | **Plan-Prüfer** |
| **C** | vier Schreibstopps **einzeln** nachweisen | Yama |
| **D** | Ruhe- und Prozessprüfung durchführen | Integrator *(lesend)* |
| **E** | Integrator zunächst **lesend** starten | Yama |
| **F** | Divergenz untersuchen | **Integrator** |
| **G** | `AKTIVIERUNGS_SHA` **begründet** bestimmen | **Integrator** |
| **H** | Rollen-Worktrees anlegen | Integrator |
| **I** | technische Barrieren aktivieren und **testen** | Generator baut, Evaluator prüft |
| **J** | erst danach regulären Rollenbetrieb freigeben | Yama |

**Er ist bei D, F, G und H dran — und bei F/G allein zuständig.** Bei A und B kommt er nicht vor:
**sein eigenes Rollenpaket nimmt er nicht ab.**

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
