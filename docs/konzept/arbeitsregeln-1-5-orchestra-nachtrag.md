# KONZEPT — Nachtrag ARBEITSREGELN 1.5: die vier Orchestra-Übernahmen

> **Zustand: ENTWURF.** Dieses Blatt hat **keine Prozessautorität**. Es gilt erst, wenn Yama es
> freigibt und der Inhalt in `docs/ARBEITSREGELN.md` (dann Fassung 1.5) **eingearbeitet** ist —
> als Nachtrag der einen Quelle, nie als Parallelwerk daneben. Der Grund steht in der eigenen
> Geschichte: die Gabelung zweier Regelfassungen vom 05.08. (`BEFUND-ZWEI-REGELWERKE.md`) hat
> eine Yama-Entscheidung und die Zusammenführung zu 1.4.2 gekostet. Das passiert nicht noch einmal.

**Angelegt:** 20.08.2026, auf Yamas Freigabe (b) · **Verfasser-Rolle:** Planner
**Herkunft:** Abgleich des extern vorgeschlagenen „Orchestra"-Modells (Intendant → Dirigent →
Planner → Planprüfer → Generator → Evaluator → Integrator → Post-Integration-Evaluator → Releaser)
gegen die vorhandene Rollenkette. Messung 20.08.: sechs von neun Rollen existieren bereits
(`docs/rollenkette/rollen/1-planner` … `6-integrator`, ARBEITSREGELN §4); übernommen wird nur,
was fehlt.

---

## 1. Ziel & Entscheidung

**Vier Übernahmen, nicht mehr.** Das Orchestra-Modell wird **nicht installiert** (kein eigenes
CLAUDE.md, kein `state.json`, keine eigenen Gate-Dateien); seine vier echten Neuerungen werden
als §-Nachträge in die ARBEITSREGELN eingearbeitet:

### N1 — Post-Integrations-Prüfung (schließt den dokumentierten fehlenden Rückfluss)

**Entscheidung:** Nach **jedem Integrations-Commit** prüft eine Instanz, die an diesem Vorgang
**weder gebaut noch integriert** hat, den integrierten Stand als Ganzes: Zusammenspiel der
Arbeitspakete, Testsuite auf dem Integrationsstand, Regressionen, gemeinsame Datenflüsse,
Rechtegrenzen. Urteil `PASS`/`FAIL` mit Rohausgabe-Beleg; `FAIL` geht mit begrenztem Fix-Auftrag
an den Generator zurück, **nicht** an den Integrator.

**Zuschnitt:** kein siebter Rollenordner. Die Prüfung ist ein **Mandat der bestehenden Rolle
`4-evaluator`** („Integrations-Abnahme"), ausgeführt von einer frischen Instanz. Begründung:
`BEFUND-ZWEI-RELEASE-PRUEFER-UND-DER-FEHLENDE-RUECKFLUSS.md` zeigt, dass schon zwei Träger
desselben Prüfnamens Verwirrung erzeugen — eine siebte Rolle vermehrt das Problem, ein
zusätzliches Mandat einer bestehenden nicht. *Verworfene Alternative:* eigener Ordner
`7-post-integrations-pruefer` — mehr Worktree-Pflege, kein Prüfgewinn.

**Kettenfolge neu (nur Reihenfolge, keine neue Rolle):**
`… → 4-evaluator (je Paket) → 6-integrator → 4-evaluator (Integrations-Abnahme, frische Instanz) → 5-release-pruefer`

### N2 — Release-Hook (das Gate wird technisch, nicht nur disziplinarisch)

**Entscheidung:** Ein `PreToolUse`-Hook blockiert `git push` und Deploy-Befehle, solange keine
ausdrückliche Freigabe-Marke für genau diesen Vorgang vorliegt. Damit wird das bestehende harte
Gate „Commit/Push+Deploy nur auf Yamas Auftrag" vom Merksatz zur Maschinenprüfung.

**Zuschnitt:** Hook-Definition in `.claude/settings.json` je Arbeitsbaum. **Bekannte Kante:**
diese Datei ist seit `79aeb1e6` git-ignoriert — der Hook erreicht die neun Worktrees nicht über
einen Commit. Deshalb: eine **committete Vorlage** `.claude/settings.hook-vorlage.json` plus ein
Einricht-Schritt je Worktree in der Umstellungs-Checkliste. Ein Hook, der nur im Hauptbaum lebt,
wäre ein Gate mit acht offenen Türen.

### N3 — Nachvollzugs-Matrix (Traceability) als Planner-Pflichtteil

**Entscheidung:** Jeder Spur-A-Auftrag enthält eine Tabelle
`Abnahmekriterium → Arbeitspaket → (nach Umsetzung) Commit-SHA → Testbeleg`.
Der Evaluator prüft **gegen die Matrix**, nicht gegen den Fließtext; ein Kriterium ohne Zeile ist
ein Planprüfer-Befund (`REVISE`). Der A-37-Befund („die zwei Bau-SHAs stehen jetzt in FELDERN
statt in einem Satz") hat genau diese Form bereits im Kleinen erzwungen — N3 macht sie zur Regel.

### N4 — Dirigent als benannte Rolle (orchestriert, baut nie)

**Entscheidung:** Die faktisch existierende Steuerungsrolle (Intake, Agentenauswahl je Aufgabe
nach dem Roster in `docs/regelwerk/AGENTEN-UND-SKILLS.md`, Gate-Verwaltung, Übergaben) wird als
Agent `.claude/agents/dirigent.md` definiert: **keine** Produktcode-Werkzeuge in der eigenen Hand,
keine Freigabe eigener Ergebnisse, Freigaben bleiben ausnahmslos bei Yama (= Intendant; der
Begriff wird als Synonym erwähnt, nicht eingeführt — es gibt schon genug Rollenwörter).
Agententiefe bleibt auf zwei Ebenen begrenzt: Dirigent → Rolle → Fach-Linse.

**Ausdrücklich NICHT übernommen** (mit Grund, damit die Frage nicht wiederkehrt):
- **`state.json`** — `docs/STATUS.md` ist der namentlich benannte einzige Statusträger (§16);
  die Frage „STATUS.md oder Maschinendatei" wurde in P-01 entschieden (Beleg `8fc5edb8`).
- **Starterpaket-Installation** — zweites Regelwerk; s. o.
- **Neun Rollen für jede Änderung** — Spur B bleibt. Orchestra-Tiefe gilt für Spur A;
  die drei Schutzregeln der Kurzspur bleiben unverändert.

## 2. Spur

**Spur A.** Berührt Autorisierung (N2), Prozessrecht (N1, N3, N4) und die Regelquelle selbst.
Ein Regelnachtrag ist nie Kurzspur.

## 3. Nahtstellen

| Wo | Was | Was bewusst NICHT |
|---|---|---|
| `docs/ARBEITSREGELN.md` | §4 (Evaluator-Mandat N1, Dirigent N4), §-Folge Integrator (N1-Kette), neuer §-Absatz Release-Hook (N2), Planner-Pflichtteile (N3), §19 Änderungsverzeichnis → 1.5 | keine bestehende Regel wird abgeschwächt |
| `.claude/agents/dirigent.md` | neue Agent-Definition (nach Freigabe) | kein Umbau der 15 vorhandenen |
| `.claude/settings.hook-vorlage.json` | committete Hook-Vorlage (N2) | kein Eingriff in lokale settings.json der Worktrees ohne Checklisten-Schritt |
| `docs/rollenkette/` | Checklisten-Ergänzung: Kettenfolge + Hook-Einrichtung je Worktree | keine neuen Rollenordner |
| `docs/STATUS.md` | **unberührt** | — |

## 4. Kantenliste

1. **Ignorierte settings.json** (N2): Hook verteilt sich nicht per Git — ohne Vorlage + Checkliste
   entsteht ein Gate, das nur dort gilt, wo zufällig jemand es einrichtete.
2. **Hook-Muster zu eng/zu weit** (N2): blockt er `git push` per Textmuster, fängt er
   `git push --force` mit, aber evtl. nicht ein Deploy-Skript; die Befehlsliste gehört in die
   Vorlage, nicht in Prosa.
3. **Frische-Nachweis** (N1): „frische Instanz" muss prüfbar sein — die Integrations-Abnahme
   nennt im Bericht, dass sie an keinem Paket des Vorgangs beteiligt war; ohne diese Zeile ist
   das Urteil ungültig.
4. **Matrix verkommt zur Förmlichkeit** (N3): eine Matrix, die nach dem Bau rückwirkend gefüllt
   wird, beweist nichts — der Planprüfer prüft sie **vor** der Umsetzung auf Vollständigkeit.
5. **Rollenwort-Inflation** (N4): „Dirigent" und „Intendant" dürfen keine dritte Vokabelschicht
   über Planner/Yama legen — der Nachtrag definiert Dirigent einmal und verweist sonst.
6. **Worktree-Drift**: bis die Rollen-Worktrees den 1.5-Stand ziehen, arbeiten sie unter 1.4.2 —
   der Nachtrag nennt ein Stichtags-Datum, ab dem die Integrations-Abnahme verlangt wird.

## 5. Rückweg & Entdeckung

**Rückweg:** Alles additiv. Regelfassungen sind versioniert — 1.4.2 bleibt vollständig im
Änderungsverzeichnis §19; Rücknahme = Nachtrag zurückdrehen (ein Commit, keine Datenmigration).
Hook: Vorlage löschen + Checklisten-Schritt streichen; er trägt keinen Zustand. Vor dem Wirksamwerden
liegt der Stand gepusht auf `fork` (Kopie außerhalb der Maschine).
**Entdeckung:** Wenn N1 falsch zugeschnitten ist, zeigt es sich als Stau an derselben Stelle, die
der Rückfluss-Befund beschreibt — Integrations-Commits, deren Abnahme niemand für sich beansprucht;
sichtbar in `docs/STATUS.md` als Zeilen ohne Ballbesitz. Wenn N2 falsch greift, meldet der Hook
Blockaden bei legitimen Vorgängen — jede Fehlblockade wird als Befund erfasst, nicht still umgangen.

## 6. Abnahmekriterien (messbar)

1. `grep -c "Integrations-Abnahme" docs/ARBEITSREGELN.md` ≥ 2 (Rollenmandat + Kettenfolge), und
   die Kettenfolge nennt die frische Evaluator-Instanz **nach** dem Integrator.
2. Hook-Probe: in einem Baum mit eingerichteter Vorlage wird ein `git push` ohne Freigabe-Marke
   **blockiert** (Rohausgabe der Ablehnung), mit Marke **durchgelassen** — beide Ausgaben im Beleg.
3. Ein Muster-Auftrag nach 1.5 enthält die Matrix; der Planprüfer weist einen Auftrag mit einem
   matrixlosen Kriterium nachweislich als `REVISE` zurück (ein durchgespielter Fall genügt).
4. `.claude/agents/dirigent.md` existiert, führt **keine** Edit/Write-Werkzeuge, und §19 der
   ARBEITSREGELN trägt den Eintrag „Fassung 1.5" mit Herkunft dieses Blattes.
5. `docs/STATUS.md` ist von allen vier Übernahmen unberührt (`git log -- docs/STATUS.md` zeigt
   keinen Commit dieses Vorgangs).

## 7. Heimat-App

**ticket** (dieses Repository), Branch `auto/hausplaner-integration`. Die Regeländerung selbst
schreibt allein die Rolle, die Yama dafür freigibt; dieses Blatt ist der Entwurf, nicht der Vollzug.

---

**Ball:** bei **Yama** — Freigabe oder `REVISE` dieses Entwurfs. Nach Freigabe: Einarbeitung in
ARBEITSREGELN 1.5 als eigener Spur-A-Vorgang mit Planprüfer.
