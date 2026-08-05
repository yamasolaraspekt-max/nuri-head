# Verbindliche Arbeitsregeln

**Version:** 1.4.1
**Gültig seit:** 04.08.2026 · **Fassung 1.4 seit:** 05.08.2026
**Autorität:** Yama
**Geltung:** gesamtes Repository, alle Menschen, Agenten, Rollen, Worktrees und Arbeitszweige

## 1. Alle früheren Prozessregeln sind aufgehoben

Dieses Dokument ist die **einzige verbindliche Quelle für Arbeitsablauf, Rollen, Übergaben,
Qualitätstore, Statusführung und Freigaben**.

Alle früheren Prozessfassungen sind mit Inkrafttreten dieser Version vollständig aufgehoben.
Das gilt insbesondere für frühere Betriebsordnungen, Rollenregelwerke, Agentenzyklen,
Auftragstafeln als Prozessautorität, Handoff-Regeln, Laufzeit- und Taktregeln,
Parallelbetriebsregeln sowie ältere Planner-/Generator-/Evaluator-Anweisungen.

Erhaltene Aufträge, Abnahmen, Ledgers, Statusseiten und historische Berichte sind ausschließlich
**fachliche Nachweise und Historie**. Aus ihnen entsteht keine Arbeitsregel. Alte Regelverweise in
solchen Dokumenten sind ungültig und werden nicht befolgt.

Bei einem Widerspruch gilt diese Rangfolge:

1. aktuelle ausdrückliche Anweisung von Yama,
2. dieses Dokument,
3. freigegebener aktueller Auftrag für seinen fachlichen Umfang,
4. fachliche Architektur-, Sicherheits- und Produktspezifikationen,
5. Code und Tests als Beleg des Ist-Zustands,
6. historische Dokumente nur als Kontext.

Eine Ausnahme von diesen Regeln ist nur gültig, wenn Yama sie ausdrücklich für den konkreten
Vorgang erteilt. Aus früheren Ausnahmen entsteht kein Gewohnheitsrecht.

## 2. Ziel

Es wird produktionsreif gearbeitet: nachvollziehbar, reproduzierbar, sicher, fachlich korrekt und
ohne verdeckte Restarbeiten. Nicht die Menge der Berichte zählt, sondern ein belastbarer Beweis an
einem exakt benannten Stand.

Grundsätze:

- eine führende Wahrheit je Sachverhalt,
- genau ein aktiver Bauauftrag,
- kleine, klar abgegrenzte Änderungen,
- vorhandenen Code zuerst prüfen und wiederverwenden,
- keine Behauptung ohne Messung,
- keine Freigabe ohne unabhängige Gegenprobe,
- keine sichtbare Änderung ohne Browserabnahme,
- keine Persistenzänderung ohne Server-, Schema-, Migrations- und Konfliktprüfung,
- keine Veröffentlichung ohne Yamas Freigabe.

## 3. Verbindliche Zustände

Jeder Auftrag befindet sich in genau einem Zustand:

```text
ENTWURF
→ BEREIT
→ IN_ARBEIT
→ CODE_FERTIG
→ ABNAHME
→ ABGENOMMEN oder NACHBESSERN
→ RELEASE_PRUEFUNG
→ RELEASE_FREI
→ VEROEFFENTLICHT
→ BETRIEBSBESTAETIGT
```

Zusätzliche Blockzustände:

- `SPEC_BLOCKED`: Auftrag ist widersprüchlich, unvollständig oder nicht machbar.
- `ENV_BLOCKED`: Umgebung verhindert eine gültige Prüfung.
- `DECISION_BLOCKED`: eine ausdrücklich Yama vorbehaltene Entscheidung fehlt.
- `RELEASE_BLOCKED`: der abgenommene Stand ist nicht sicher oder nicht reproduzierbar
  veröffentlichbar.

Beim Eintritt in `ENV_BLOCKED`, `DECISION_BLOCKED` oder `RELEASE_BLOCKED` wird der vorherige
Prüfzustand als `fortsetzung_zustand` gespeichert. Eine Rückkehr ist nur nach dokumentierter
Beseitigung des Blockers, durch dieselbe verantwortliche Rolle und ohne verdeckte Inhaltsänderung
zulässig. `SPEC_BLOCKED` und `NACHBESSERN` erfordern dagegen einen neuen Plan beziehungsweise
Inhalts-Commit.

`CODE_FERTIG` bedeutet ausschließlich, dass der Generator seinen Bau und seine Eigenprüfung
abgeschlossen hat. Es bedeutet nicht, dass die Aufgabe abgenommen, mergebar oder veröffentlichbar
ist.

Es darf gleichzeitig höchstens einen Auftrag im Zustand `IN_ARBEIT` geben. Prüfungen eines
festgeschriebenen Commits dürfen parallel laufen, wenn sie keinen gemeinsamen veränderlichen
Zustand benutzen.

## 4. Rollen und Verantwortungen

### Planner

Der Planner untersucht den aktuellen Code, klärt Ziel und Nicht-Ziele, prüft Abhängigkeiten und
erstellt einen kleinen Auftrag. Er ist Eigentümer von Spezifikationsfehlern.

### Plan-Prüfer

Der Plan-Prüfer prüft vor dem Bau unabhängig die Machbarkeit, Vollständigkeit und Messbarkeit des
Auftrags. Planner und Plan-Prüfer dürfen nicht dieselbe Instanz sein.

### Generator

Der Generator arbeitet ausschließlich am freigegebenen Auftrag. Er verändert weder Kriterien noch
Scope stillschweigend. Einen unerfüllbaren Auftrag gibt er als `SPEC_BLOCKED` zurück.

### Evaluator

Der Evaluator prüft einen exakten Commit unabhängig. Er liest zuerst Auftrag, Diff und Code und
führt eigene Gegenproben aus. Den Generatorbericht liest er erst danach zum Abgleich. Generator und
Evaluator dürfen nicht dieselbe Instanz sein.

### Release-Prüfer

Der Release-Prüfer prüft, dass exakt der abgenommene Commit veröffentlicht werden soll, alle
Artefakte reproduzierbar sind, Migration und Rückweg tragen und keine fremden Änderungen in den
Release geraten. Er veröffentlicht nicht selbst und darf beim selben Auftrag weder Generator noch
Evaluator gewesen sein.

### Yama / Veröffentlichung

Nur Yama genehmigt Veröffentlichung: Push, Merge nach `main`, Tags, Deployments, produktive
Datenoperationen, Force-Operationen und endgültige Löschung fachlicher Daten.

Ein genehmigter Push auf einen Arbeits- oder PR-Prüfbranch ist ausschließlich Transport zur
Prüfung und setzt den Auftrag noch nicht auf `VEROEFFENTLICHT`. Dieser Zustand beginnt erst mit
der Integration in den ausdrücklich freigegebenen Zielbranch oder mit dem ausdrücklich benannten
Deployment. Push und Zielintegration bleiben getrennte Freigaben.

**Vertretungsregel (Fassung 1.4, auf Yamas mündliche Weisung vom 05.08.2026):** Yama wird bei der
Veröffentlichung **ständig durch den Release-Prüfer vertreten**. Der Release-Prüfer genehmigt und
führt in Yamas Namen ohne Einzelrückfrage aus: Push von Arbeitszweigen, Merge nach `main`, Tags und
Deployments in Nicht-Produktionsumgebungen — **ausschließlich** für Stände, die zuvor das Votum
`RELEASE_FREI` nach den Release-Regeln dieses Dokuments erhalten haben. Sicherungs-Pushes von
Arbeitszweigen auf Yamas eigene Fernziele (`fork`, `backup-private`) sind Datensicherung und laufen
als stehende Aufgabe des Release-Prüfers (Prüfungen davor: keine aktiven Locks, Fast-Forward je
Ziel, keine Geheimnisse im Ausgang, nur die eigenen Fernziele — niemals `upstream`, niemals Force).

**Bei Yama persönlich verbleiben — von der Vertretung ausgenommen:** Veränderungen an
Produktionssystemen (Hetzner), produktive Datenoperationen, Force-Operationen und die endgültige
Löschung fachlicher Daten. Jede in Vertretung ausgeführte Veröffentlichung wird mit Auftrag, Commit
und Vorher-/Nachher-Stand je Ziel protokolliert. Yama kann die Vertretung jederzeit formlos
widerrufen.

## 5. Definition of Ready

Ein Auftrag darf nur `BEREIT` werden, wenn der Plan-Prüfer alle folgenden Punkte belegt hat:

- exakter Basis-SHA,
- fachliches Ziel und messbarer Nutzen,
- Ist-Beleg aus aktuellem Code,
- kleinster sinnvoller Scope und betroffene Dateien,
- ausdrückliche Nicht-Ziele,
- Konfliktprüfung gegen laufende Änderungen,
- vollständige Abhängigkeitskette,
- positive und negative Akzeptanzfälle,
- jedes P1-Kriterium ist vor dem Bau wirksam rot,
- kein Kriterium ist bereits erfüllt,
- kein Kriterium ist unerfüllbar,
- jeder Prüfbefehl wurde auf Syntax und Aussagekraft geprüft,
- erforderliche Testdaten, Benutzerrolle, Route und Browserpfad sind benannt,
- Auswirkungen auf API, Server, Schema, Migration, Bestandsdaten und Bundle sind bewertet,
- Rückweg bei riskanten Änderungen ist beschrieben.

Fehlt ein Punkt, bleibt der Auftrag `ENTWURF` oder wird `SPEC_BLOCKED`.

## 6. Isolierte Arbeitsumgebungen

Jede schreibende Rolle arbeitet in einem eigenen Worktree und Arbeitszweig. Jede prüfende Rolle
prüft einen festgeschriebenen Commit in einem getrennten Checkout.

Veränderliche Zustände werden niemals geteilt:

- eigener Git-Index je Rolle,
- eigene Testdatenbank je Lauf oder Rolle,
- eigener Server-Port,
- eigene temporäre Verzeichnisse,
- reproduzierbare Seed- und Fixture-Daten.

Insbesondere dürfen Generator und Evaluator nicht gleichzeitig dieselbe `ticket_testing`-Datenbank
verwenden. Ein durch Locks, konkurrierende Datenbankläufe, fehlende Browsersteuerung oder defekte
Testinfrastruktur verursachter Lauf ist `ENV_BLOCKED` und kein Produktfehler.

## 7. Arbeitsweise des Generators

Vor der ersten Änderung bestätigt der Generator:

- Basis-SHA stimmt,
- Scope ist frei von fremden Änderungen,
- Auftrag ist machbar,
- Ausgangsmessungen stimmen,
- vorgesehene Tests können den Fehler tatsächlich erkennen.

Während des Baus gelten:

- keine Nebenbaustellen,
- keine Umbenennung oder Ersetzung eines Kriteriums,
- keine Erweiterung des Scopes ohne neuen Plannerentscheid,
- keine zweite Implementierung einer vorhandenen Funktion,
- keine Abschwächung bestehender Tests,
- neue Logik wird mit einer wirksamen Negativ- oder Mutationsprobe abgesichert,
- fachfremde Änderungen bleiben unangetastet.

Wird eine Voraussetzung falsch, stoppt der Generator und meldet die passende Blockklasse.

## 8. Verbindliches Qualitätstor

Vor `CODE_FERTIG` wird für den exakten Generator-Commit das risikogerechte Tor vollständig gefahren.

Grundtor:

1. Scope-Diff gegen Basis-SHA,
2. Formatierung und statische Analyse,
3. TypeScript beziehungsweise entsprechende Sprachprüfung,
4. Unit-Tests,
5. Komponenten-/DOM-Tests,
6. relevante Backend- und Featuretests,
7. Schema-Prüfung,
8. frischer Build,
9. Nachweis, dass getrackte Artefakte aus den aktuellen Quellen stammen,
10. Prüfung auf unbeabsichtigte Änderungen außerhalb des Scopes.

Zusatz für sichtbare Änderungen:

- Browserabnahme mit Screenshots,
- Pflicht-Viewports 1440, 1024 und 375 Pixel Breite,
- Tastatur, Fokus, Escape und Screenreader-relevante Zustände,
- Browserkonsole ohne neue Fehler,
- relevante Netzwerkaufrufe geprüft.

Zusatz für Persistenz, Authentifizierung oder Datenverträge:

- Speichern und Neuladen,
- gültige und ungültige Nutzlast,
- Rechte- und Mandantengrenzen,
- Revision und Konfliktfall, insbesondere HTTP 409,
- Servervalidator und Client-Schema,
- Migration und Bestandsdokumente,
- unveränderte Daten bei abgewiesenen Requests,
- Rückwärtskompatibilität oder ausdrücklich freigegebener Migrationsschnitt.

Ein grüner Inseltest ersetzt niemals die Prüfung der angeschlossenen Server-, Bundle-, Daten- oder
Browserkette.

## 9. Definition of Done

Eine Aufgabe darf nur `ABGENOMMEN` werden, wenn der Evaluator unabhängig bestätigt:

- Prüfung exakt auf dem gemeldeten Commit,
- sauberer und nachvollziehbarer Scope,
- alle anwendbaren Kriterien erfüllt,
- vollständiges Qualitätstor grün,
- mindestens eine wirksame Gegenprobe,
- keine offene P0/P1-Abweichung,
- Browserabnahme bei sichtbarer Wirkung,
- keine ungeprüfte Persistenz- oder Migrationskante,
- kein veraltetes Bundle,
- keine offenen Restarbeiten, die zur eigentlichen Aufgabe gehören.

Nicht ausgeführte Akzeptanz wird ausdrücklich als offen gemeldet und verhindert `ABGENOMMEN`, wenn
sie für den Auftrag erforderlich ist.

## 10. Release-Prüfung

`ABGENOMMEN` ist noch keine Veröffentlichungserlaubnis. Der Evaluator prüft immer einen
unveränderlichen **Inhalts-Commit**. Danach wird jede Entscheidung als eigener
**Statusübergang-Commit** festgehalten. Ein Statusübergang darf ausschließlich
`docs/AKTUELLER_AUFTRAG.yaml` ändern, enthält genau einen erlaubten Zustandswechsel und verweist auf
seinen unmittelbaren Vorgänger. Jede weitere Datei sowie jede Produkt-, Regel-, Plan- oder
Teständerung ist darin verboten. Der aktuelle Statuscommit enthält nicht seine eigene, vor seiner
Erzeugung unbekannte SHA; Git selbst ist die Wahrheit über den aktuellen Commit.

Vor `RELEASE_FREI` prüft der Release-Prüfer unabhängig:

- Evaluator-Votum, `inhalt_sha`, `pruef_sha` und `release_sha` nennen denselben Inhalts-Commit,
- die lückenlose Statuskette beginnt direkt beim geprüften Inhalts-Commit und jeder weitere
  Statusübergang ist direkter Nachfolger des vorherigen Statuscommits,
- jeder Statusdiff ändert ausschließlich `docs/AKTUELLER_AUFTRAG.yaml` und genau einen erlaubten
  Zustand,
- der letzte Status vor dem Merge lautet `RELEASE_FREI`,
- Merge-Ziel und Release-Diff enthalten ausschließlich freigegebene Änderungen,
- alle erforderlichen CI- und Qualitätstore laufen auf dem Release-Kandidaten erneut grün,
- Bundle und sonstige Artefakte sind frisch und reproduzierbar,
- Konfigurationen, Umgebungsvariablen und Abhängigkeiten sind vollständig,
- Migrationen sind vorwärts und im Rückweg geprüft,
- Backup-, Wiederherstellungs- und Abbruchweg sind benannt,
- Sicherheits-, Rechte-, Mandanten- und Datenschutzgrenzen bleiben erhalten,
- Smoke-Test und betriebliche Nachprüfung sind vorbereitet,
- es gibt keine offenen P0/P1-Befunde.

Für Pull Requests gilt zusätzlich:

- erlaubt ist ausschließlich ein normaler Merge-Commit; Squash- und Rebase-Merge sind verboten,
- der erste Parent des Merge-Commits ist exakt die vom Release-Prüfer festgeschriebene
  `merge_basis_sha`,
- der zweite Parent ist exakt der durch Git und die commitgebundene Release-Prüfung ausgewiesene
  PR-Head im Zustand `RELEASE_FREI`,
- bewegt sich der Basisbranch nach der Release-Prüfung, wird der Merge `RELEASE_BLOCKED` und der
  vollständige Release-Diff gegen die neue, gesondert festgeschriebene `merge_basis_sha` erneut
  geprüft,
- unmittelbar nach dem Merge wird dessen SHA in einem Statusübergang auf dem Zielbranch
  festgehalten; erst dieser Übergang darf `VEROEFFENTLICHT` setzen,
- nach Merge oder Push werden Zielbranch und Remote-SHA erneut gelesen und gegen den freigegebenen
  Stand geprüft.

Nur nach `RELEASE_FREI` darf Yama die Veröffentlichung genehmigen. Nach der Veröffentlichung wird
der reale Zielstand geprüft: Version/Commit, Migrationen, zentrale Smoke-Tests, Logs und
Fehlerindikatoren. Erst danach lautet der Zustand `BETRIEBSBESTAETIGT`. Ein fehlgeschlagener
Release- oder Smoke-Test führt zu `RELEASE_BLOCKED` und dem vorbereiteten Rückweg.

## 11. Kurze Beweisberichte

Berichte werden nicht als fortlaufende Erzählung geführt. Pro Auftrag gibt es einen kompakten,
maschinenlesbaren Stand.

Planner:

```yaml
auftrag: ID
basis: SHA
plan_pfad: "..."
plan_sha256: "..."
ziel: "..."
nicht_ziele: []
scope: []
akzeptanzfaelle: []
risiken: []
votum: ENTWURF|SPEC_BLOCKED|DECISION_BLOCKED
```

Plan-Prüfer:

```yaml
auftrag: ID
basis: SHA
plan_sha256: "..."
votum: BEREIT|ENTWURF|SPEC_BLOCKED|ENV_BLOCKED|DECISION_BLOCKED
machbarkeit: pass|fail|offen
rote_ausgangslage: pass|fail|offen
definition_of_ready: pass|fail
blocker: []
befunde: []
```

Generator:

```yaml
auftrag: ID
basis: SHA
commit: SHA
scope: []
tests:
  statisch: pass|fail|nicht_anwendbar
  unit: "Zahl/Zahl"
  backend: "Zahl/Zahl|nicht_anwendbar"
  schema: pass|fail|nicht_anwendbar
  build: pass|fail|nicht_anwendbar
  browser: pass|offen|nicht_anwendbar
abweichungen: []
offene_akzeptanz: []
```

Evaluator:

```yaml
auftrag: ID
commit: SHA
votum: ABGENOMMEN|NACHBESSERN|SPEC_BLOCKED|ENV_BLOCKED|DECISION_BLOCKED
fehlerklasse: CODE|SPEC|UMGEBUNG|BEWEIS|KEINE
gegenprobe: "..."
browser: pass|offen|nicht_anwendbar
befunde: []
```

Release-Prüfer:

```yaml
auftrag: ID
inhalt_sha: SHA
status_commit: SHA # exakter geprüfter PR-Head
merge_ziel: branch
merge_basis_sha: SHA
merge_verfahren: merge_commit
merge_sha: SHA|null
votum: RELEASE_FREI|RELEASE_BLOCKED|ENV_BLOCKED|DECISION_BLOCKED
ci: pass|fail
artefakte_reproduzierbar: true|false
migration: pass|nicht_anwendbar
rueckweg: pass|nicht_anwendbar
smoke_test_plan: "..."
befunde: []
```

Zahlen ohne zugehörigen Befehl und Commit gelten nicht als Beweis.

## 12. Behandlung roter Abnahmen

Jeder rote Befund wird genau einer Klasse zugeordnet:

- `CODE`: gültiger Auftrag, Umsetzung fehlerhaft,
- `SPEC`: Auftrag falsch, unvollständig oder unerfüllbar,
- `UMGEBUNG`: Prüfung durch geteilten oder defekten Zustand ungültig,
- `BEWEIS`: Umsetzung möglicherweise korrekt, erforderlicher Nachweis fehlt,
- `REGRESSION`: bestehende Funktion wurde beschädigt.

Verantwortung folgt der Klasse. Ein Spezifikationsfehler wird nicht dem Generator zugerechnet. Ein
Umgebungsfehler erzeugt kein fachliches Rot.

Nach der zweiten roten Runde derselben Aufgabe gilt Pflichtstopp:

1. keine weitere Reparatur auf Verdacht,
2. gemeinsame Ursachenanalyse,
3. Auftrag neu schneiden,
4. neue Basis und neue Kriterien,
5. erneute Plan-Prüfung.

**Präzisierung (Fassung 1.4.1, aus der lokalen Fassung 1.2.1 vom 05.08. übernommen — „der rote
Weg": wer, wie, wann zurück, welche Kriterien):**

### 12.1 Wer den Ball bekommt

| Klasse | Ball | Zustand | Bemerkung |
|---|---|---|---|
| `CODE` | Generator | `NACHBESSERN` | |
| `SPEC` | **Planner** | `SPEC_BLOCKED` | **nicht** `NACHBESSERN` — der Zustand richtet sich an den Bauenden, und ihn trifft kein Vorwurf |
| `UMGEBUNG` | Rolle, deren Umgebung es ist | `ENV_BLOCKED` | erzeugt **kein** fachliches Rot |
| `BEWEIS` | Generator | `NACHBESSERN` | der Code darf unverändert bleiben; geschuldet ist der Nachweis |
| `REGRESSION` | Generator | `NACHBESSERN` | immer P0 |

**Ein Befund mit CODE- und SPEC-Anteil wird geteilt, nicht gemittelt.** Beide Teile bekommen eine
eigene Klasse und einen eigenen Ball. **Der SPEC-Teil wird zuerst behoben** — sonst arbeitet der
Bauende gegen ein Kriterium, das er nicht erfüllen kann.

### 12.2 Wie die Nachbesserung läuft

- **Sie läuft auf der Linie des Baus**, nie auf einem anderen Zweig. *Zwei Zweige erzeugen zwei
  Fassungen derselben Reparatur, die beim Merge auf denselben Zeilen kollidieren.*
- **Der Umfang ist der Befund**, nichts sonst. Keine Nebenreparaturen, auch keine offensichtlichen.
- **Der Bauende ändert kein Kriterium und fügt keines hinzu.** Braucht die Reparatur ein neues,
  geht sie an den Planner zurück (§7).
- **Tests dürfen wachsen, nie schrumpfen.** Eine Reparatur, die eine Zusage abschwächt, ist
  abzulehnen, auch wenn der Befund verschwindet.

### 12.3 Rückweg zur Abnahme

Die Aufgabe geht auf `CODE_FERTIG` zurück — **kein eigener Zustand für Nachbesserungen.** Die
Meldung nennt zusätzlich zu §11:

- die neue Prüf-SHA **auf der Linie des Baus**,
- je Befund: was geändert wurde,
- **je Befund die Zwei-Richtungs-Probe:** dieselbe Probe war vorher rot und ist nachher grün, beide
  Richtungen selbst gemessen. *Eine Reparatur ohne den vorherigen Rot-Beleg ist eine Behauptung.*

### 12.4 Welche Kriterien bei der Wieder-Abnahme gelten

**Alle — aber nicht alle gleich tief.**

```text
das rote Kriterium        volle Pruefung samt eigener Gegenprobe des Evaluators
die vorher gruenen        Pruefbefehle erneut fahren (sie sind Befehle, das kostet wenig)
die Mutationsprobe        IMMER erneut - eine Reparatur kann sie stumpf machen
die Browserabnahme        nur erneut, wenn die Reparatur sichtbares Verhalten beruehrt
```

> **Warum nicht nur das rote Kriterium.** Eine Reparatur ist eine Änderung, und Änderungen brechen
> Nachbarn. Die grünen Kriterien sind ausführbare Befehle — sie erneut zu fahren kostet Minuten,
> sie zu überspringen kostet eine Regression, die niemand sucht.

### 12.5 Abgenommen trotz Befund

**Ein `SPEC`-Befund blockiert die Abnahme nicht**, wenn der Bau den Auftrag erfüllt, wie er
geschnitten war. Er wird als Befund verbucht und erzeugt einen **neuen Auftrag**.

> *Sonst haftet der Bauende für einen Fehler des Planners — und `NACHBESSERN` wäre an die falsche
> Rolle adressiert. Der Auftrag war erfüllt; falsch war, was verlangt wurde.*

**Die Abnahme nennt den Befund ausdrücklich**, mit Klasse, Schwere und dem Auftrag, der daraus
entsteht. **Ein verschwiegener Befund ist keine Abnahme, sondern eine Unterschrift auf Verdacht.**

## 13. Pflichtprüfung nach jeweils zehn Aufgaben

Die Qualität von Planner, Plan-Prüfer, Generator, Evaluator, Release-Prüfer und des
Veröffentlichungs-/Entscheidungsschnitts bei Yama wird **nicht nach Zeiträumen** bewertet. Es gibt
keine monatliche oder kalendarische Qualitätsprüfung.

Nach jeweils zehn fortlaufend nummerierten Planner-Aufträgen ist vor Aufgabe elf eine verbindliche
Prozess- und Skill-Prüfung durchzuführen. Ein Auftrag zählt, sobald der Planner ihn dem Plan-Prüfer
erstmals vorlegt. Zur Zehnergruppe gehören damit auch zurückgewiesene, blockierte oder später
abgebrochene Aufträge; schlechte Pläne dürfen nicht aus der Statistik verschwinden.

Für jede Zehnergruppe werden mindestens gemessen:

- beim ersten Plan-Review `BEREIT` oder zurückgewiesen,
- `SPEC_BLOCKED` beim Generator,
- Spezifikationsfehler, die erst der Evaluator gefunden hat,
- vom Generator oder Evaluator nachträglich ergänzte Schichten, Kriterien oder Testfälle,
- unerfüllbare oder bereits vor dem Bau grüne Kriterien,
- fehlende Server-, Schema-, Migrations-, Bundle- oder Browserkanten,
- falsche rote Ergebnisse durch die Umgebung,
- Anzahl der Nachbesserungsrunden je Auftrag,
- Freigabe im ersten Evaluatorlauf,
- Generator-Scopeabweichungen und vom Generator verursachte Regressionen,
- vom Evaluator übersehene Fehler und falsche rote Voten,
- Unterschiede zwischen Abnahme-Commit und Release-Kandidat,
- fehlgeschlagene Builds, Migrationen, Releases, Smoke-Tests oder Rückwege,
- verspätete, widersprüchliche oder am falschen Commit erteilte Veröffentlichungsentscheidungen,
- Fehler, die erst nach Veröffentlichung gefunden wurden.

Die Prüfung endet mit genau einer Entscheidung:

- Skills tragen unverändert,
- Planner-Skill nachschärfen,
- Plan-Prüfer-Skill nachschärfen,
- Generator-Skill nachschärfen,
- Evaluator-Skill nachschärfen,
- Release-Skill nachschärfen,
- Veröffentlichungs- oder Entscheidungsschnitt mit Yama präzisieren,
- technische Barriere ergänzen,
- Prozessregel präzisieren.

Erforderliche Änderungen werden vor Aufgabe elf umgesetzt und mit frischen Gegenfällen getestet.
Eine neue Zehnergruppe beginnt erst danach. Der Zähler wird nie wegen Sitzung, Monatswechsel,
Branchwechsel oder Rollenwechsel zurückgesetzt.

Schwere Fehler warten nicht auf die Zehnergrenze. Ein P1-Spezifikationsfehler, ein unerfüllbarer
Auftrag, eine übersehene Daten-/Sicherheitskante oder die zweite Wiederholung derselben
Fehlerklasse löst die Skill- und Ursachenprüfung sofort aus. Der Vorfall bleibt trotzdem Bestandteil
seiner laufenden Zehnergruppe.

## 14. Git, Commits und Veröffentlichung

- Nur ausdrücklich geprüfte Pfade werden gestaged; niemals `git add -A`.
- Vor jedem Commit wird `git diff --cached --name-only` geprüft.
- Fremde und untracked Arbeiten werden nicht übernommen.
- Lokale Sicherungscommits bleiben klein, thematisch und rückgängig machbar.
- Commit und Push sind getrennte Vorgänge.
- Kein Push, kein Merge nach `main`, kein Tag, kein Deploy und kein Force ohne ausdrückliche
  Freigabe von Yama.
- Kein destruktives Bereinigen eines fremden oder unklaren Arbeitsbaums.

## 15. Daten- und Sicherheitsgrenzen

- Tests laufen ausschließlich gegen eindeutig benannte Testdatenbanken.
- Keine Tests, Seeds oder Messungen gegen Produktivdaten.
- Änderungen oder Löschungen bestehender fachlicher Daten brauchen einen eigenen Auftrag und Yamas
  ausdrückliche Freigabe.
- Authentifizierung, Rechte, Mandanten- und Portalgrenzen werden serverseitig geprüft.
- Bei Geld-, Datenschutz-, Auth-, Portal- oder Datenbankwirkung ist der vollständige
  Planner-Plan-Prüfer-Generator-Evaluator-Release-Prüfer-Prozess Pflicht.
- Geheimnisse, Zugangsdaten und personenbezogene Daten werden nicht in Berichte oder Git geschrieben.

## 16. Statusführung

Die einzige manuell geführte Statuswahrheit ist
[`docs/AKTUELLER_AUFTRAG.yaml`](AKTUELLER_AUFTRAG.yaml). Der aktuelle Stand wird aus diesem
getrackten Datensatz, dem darin festgeschriebenen Git-Stand und dem letzten unabhängigen Votum
erzeugt, nicht aus alten Erzähltexten. Ein Validator darf erst wieder als Autorität verwendet
werden, wenn er ausdrücklich gegen diese Version der Arbeitsregeln gebaut und unabhängig geprüft
wurde. Alte Auftragsvalidatoren und deren Statusschema sind aufgehoben.

Die Datei nennt immer mindestens:

- `regel_version` und SHA-256 von `docs/ARBEITSREGELN.md`,
- fortlaufende `planner_laufnummer` und daraus abgeleitete `zehnergruppe`,
- Auftrag sowie Pfad und SHA-256 des freigegebenen Plans,
- Zustand und Ballbesitzer,
- `inhalt_sha`, Vorgänger-Commit, vorherigen und neuen Zustand,
- Basis-, Prüf-, Release-, Merge-Basis- und Merge-SHA, soweit vorhanden; `basis_sha` bleibt die
  ursprüngliche Plan-/Inhaltsbasis, `merge_basis_sha` ist der gesondert geprüfte Zielbranch-Stand,
  und `pruef_sha` sowie `release_sha` bezeichnen den geprüften Inhalts-Commit,
- Merge-Ziel und Merge-Verfahren,
- verantwortliche Rolle und unveränderlichen Beleg der Entscheidung,
- letztes Votum, offene Akzeptanz und nächsten konkreten Schritt,
- Prozessquittung der zuletzt übernehmenden Rolle.

Vor jedem Start, Wiederanlauf, Rollenwechsel und nach jeder Kontextkürzung liest die übernehmende
Rolle in dieser Reihenfolge `CLAUDE.md`, diese Arbeitsregeln, das Statusartefakt und den dort
benannten Plan. Sie vergleicht Regelversion, Regel-Hash, Plan-Hash und Basis-SHA. Die
Prozessquittung nennt Rolle, gelesene Versionen/Hashes und Zeitpunkt. Fehlt eine Angabe oder stimmt
sie nicht, findet keine fachliche Arbeit statt; der Vorgang wird passend blockiert.

Der abgebende Ballbesitzer schreibt jede gültige Zustandsänderung zusammen mit neuem
Ballbesitzer, Votum, offenen Punkten und nächstem Schritt in genau diese Datei. Die übernehmende
Rolle prüft die Fortschreibung, bevor sie arbeitet. Sitzung, Monat, Branch, Worktree oder
Kontextverlust dürfen weder Planner-Laufnummer noch Zehnergruppe zurücksetzen. Der Plan-Hash bleibt
ab `BEREIT` unverändert; jede fachliche Änderung an Scope oder Kriterien führt zurück zu Planner
und Plan-Prüfer und erzeugt einen neuen Plan-Hash.

Nach der unabhängigen Abnahme erstellt oder bestätigt die jeweils verantwortliche Rolle genau einen
Statusübergang-Commit. Er setzt Vorgänger-Commit, alten und neuen Zustand, Ballbesitzer,
`inhalt_sha`, Prüf-/Release-SHA, gegebenenfalls `fortsetzung_zustand`, Votum, Beleg, offene
Akzeptanz und nächsten Schritt. Der nächste
Statusübergang muss sein direkter Kind-Commit sein. Der Prüfer verifiziert Pfadmenge, Elternkette,
Hashes, Rollenverantwortung und Votum. Statuscommits werden nicht als Inhaltsänderung bewertet.

Die SHA des aktuellen Statuscommits wird niemals in diesem Commit selbst gespeichert. Sie wird aus
Git beziehungsweise dem commitgebundenen GitHub-Votum gelesen. Dasselbe gilt für die Remote-SHA
nach dem Push: Sie muss dem lokalen Statuscommit entsprechen und wird von außen verglichen, nicht
selbstreferenziell in denselben Commit geschrieben.

Die einzige erlaubte Unterbrechung der direkten Status-Elternkette ist der vorab freigegebene
Merge-Commit. Er muss die in Abschnitt 10 festgelegten zwei exakten Parents besitzen. Der unmittelbar
folgende Statuscommit ist direkter Kind-Commit dieses Merge-Commits, übernimmt den Zustand
`RELEASE_FREI` vom freigegebenen zweiten Parent und darf als einzigen Übergang
`RELEASE_FREI → VEROEFFENTLICHT` dokumentieren.

Erlaubte Übergänge und Eigentümer:

| Von | Nach | Verantwortlich |
|---|---|---|
| `ABNAHME` | `ABGENOMMEN`, `NACHBESSERN`, `SPEC_BLOCKED`, `ENV_BLOCKED` oder `DECISION_BLOCKED` | Evaluator |
| `ABGENOMMEN` | `RELEASE_PRUEFUNG` | Release-Prüfer |
| `RELEASE_PRUEFUNG` | `RELEASE_FREI`, `RELEASE_BLOCKED`, `ENV_BLOCKED` oder `DECISION_BLOCKED` | Release-Prüfer |
| `RELEASE_FREI` | `VEROEFFENTLICHT` oder `RELEASE_BLOCKED` | Yama beziehungsweise ausdrücklich beauftragte Veröffentlichungsrolle |
| `VEROEFFENTLICHT` | `BETRIEBSBESTAETIGT` oder `RELEASE_BLOCKED` | Release-Prüfer als unabhängige Betriebsprüfung |
| `ENV_BLOCKED` | gespeicherter `fortsetzung_zustand` | dieselbe prüfende Rolle wie vor der Blockade |
| `DECISION_BLOCKED` | gespeicherter `fortsetzung_zustand` | dieselbe prüfende Rolle nach Yamas dokumentierter Entscheidung |
| `RELEASE_BLOCKED` | `RELEASE_PRUEFUNG` | Release-Prüfer, nur ohne Inhaltsänderung und mit behobenem Release-Blocker |

Ein Statuscommit darf keinen zweiten Übergang bündeln. Ein neuer Inhalts-Commit beendet die
Statuskette, setzt den Auftrag auf `ABNAHME` zurück und benötigt eine neue Evaluator-Abnahme. Jede
weitere Datei, ein nicht erlaubter Übergang, eine falsche Rolle, eine Lücke in der Elternkette oder
ein geänderter Inhalts-SHA ist P1 und blockiert Release beziehungsweise Merge.

Bei `ENV_BLOCKED` und `DECISION_BLOCKED` muss `fortsetzung_zustand` einem für die verantwortliche
Rolle gültigen Prüfzustand entsprechen. Verändert die Behebung Scope, Kriterien, Regeln, Tests oder
Produktinhalt, ist die Rückkehr als Statuscommit verboten; dann gilt ausschließlich der neue
Inhalts-Commit mit neuer Abnahme. Bei bewegter Mergebasis setzt der Release-Prüfer zunächst
`RELEASE_BLOCKED`, danach mit aktualisierter `merge_basis_sha` wieder `RELEASE_PRUEFUNG` und führt
die vollständige Release-Prüfung erneut aus.

Für alle produktiven Aufträge sind versionierte Rollen-Skills mit Pfad, Version und SHA-256 in der
Prozessquittung Pflicht. Solange diese Skills noch nicht eingerichtet und unabhängig geprüft sind,
darf nach dem Governance-Bootstrap kein Produktauftrag `BEREIT` erreichen.

Es gibt keine zweite manuelle Statuswahrheit. Historische Ledgers werden nicht fortgeschrieben, um
den aktuellen Zustand zu bestimmen.

## 17. Übergang des vorhandenen Bestands

Alte Statuswerte wie „bereit", „gebaut" oder „abgenommen" werden nicht automatisch übernommen.
Ein bestehender Auftrag darf erst weiterlaufen, nachdem sein aktueller Commit und seine Kriterien
gegen diese Regeln neu eingeordnet wurden.

Die Umstellung erfolgt ohne pauschales Löschen fachlicher Arbeit:

1. aktuellen Commit und fremde Änderungen sichern,
2. genau einen Auftrag auswählen,
3. Definition of Ready nachprüfen,
4. fehlende Full-Stack- und Browserkanten ergänzen,
5. Generator oder Evaluator gemäß echtem Zustand einsetzen,
6. erst nach Abnahme den nächsten Auftrag aktivieren.

## 18. Verbotene Abkürzungen

Unzulässig sind insbesondere:

- „Tests grün, also fertig" ohne Prüfung der angeschlossenen Kette,
- sichtbare Freigabe ohne Browser,
- stilles Austauschen eines unerfüllbaren Kriteriums,
- Prüfen eines bewegten Arbeitsbaums statt eines Commits,
- gleichzeitige Läufe gegen dieselbe Datenbank,
- Berichte mit veralteten SHAs oder abgeschriebenen Zahlen,
- Status aus historischen Ledgers ableiten,
- Scope-Erweiterung als Nebenbei-Aufräumen,
- Selbstabnahme durch den Generator,
- Veröffentlichung ohne Yamas Freigabe.

Diese Regeln gelten ab sofort. Frühere Prozessregeln werden weder zitiert noch wieder aktiviert.
