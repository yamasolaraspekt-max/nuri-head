# Verbindliche Arbeitsregeln

**Version:** 1.2.1  
**Gültig seit:** 04.08.2026 · **Fassung 1.1 seit:** 05.08.2026 · **Fassung 1.2 seit:** 05.08.2026 · **1.2.1 seit:** 05.08.2026  
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

`CODE_FERTIG` bedeutet ausschließlich, dass der Generator seinen Bau und seine Eigenprüfung
abgeschlossen hat. Es bedeutet nicht, dass die Aufgabe abgenommen, mergebar oder veröffentlichbar
ist.

Es darf gleichzeitig höchstens einen Auftrag im Zustand `IN_ARBEIT` geben. Prüfungen eines
festgeschriebenen Commits dürfen parallel laufen, wenn sie keinen gemeinsamen veränderlichen
Zustand benutzen.

`IN_ARBEIT` wird gesetzt, **bevor die erste Datei im Scope geändert wird** — nicht danach und nicht
rückwirkend. **Ein Zustandsübergang, der an keine Handlung gebunden ist, findet nicht statt**, und
die Regel „höchstens ein Auftrag `IN_ARBEIT`" bleibt dann wirkungslos.

*Gemessen (05.08.): Bei A-01 und A-03 stand der Auftrag auf `BEREIT`, während bereits gebaut wurde.
Beide Male hat der Generator es selbst bemerkt — nachträglich. Zweimal dieselbe Klasse, ohne dass
jemand nachlässig war: der Übergang hatte keinen Auslöser.*

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

**Vertretungsregel (Fassung 1.2, auf Yamas mündliche Weisung vom 05.08.2026):** Yama wird bei der
Veröffentlichung **ständig durch den Release-Prüfer vertreten**. Der Release-Prüfer genehmigt und
führt in Yamas Namen ohne Einzelrückfrage aus: Push von Arbeitszweigen, Merge nach `main`, Tags und
Deployments in Nicht-Produktionsumgebungen — **ausschließlich** für Stände, die zuvor das Votum
`RELEASE_FREI` nach §10/§11 erhalten haben. Sicherungs-Pushes von Arbeitszweigen auf Yamas eigene
Fernziele (`fork`, `backup-private`) sind Datensicherung und laufen als stehende Aufgabe des
Release-Prüfers mit den Prüfungen aus §14.

**Bei Yama persönlich verbleiben — von der Vertretung ausgenommen:** Veränderungen an
Produktionssystemen (Hetzner), produktive Datenoperationen, Force-Operationen und die endgültige
Löschung fachlicher Daten. Jede in Vertretung ausgeführte Veröffentlichung wird mit Auftrag,
Commit und Vorher-/Nachher-Stand je Ziel protokolliert. Yama kann die Vertretung jederzeit
formlos widerrufen.

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
- führt der Auftrag eine Oberfläche oder einen Serverprozess aus, ist **getrennt** benannt:
  wohin die Testdaten gehen **und** wogegen der ausführende Prozess läuft — samt dem Befehl, der
  Letzteres beweist,
- jede vorgeschriebene Aufrufform, jedes Werkzeug und jeder Befehl ist auf der Zielmaschine
  **vorhanden** und dort **tatsächlich in Gebrauch** — beides gemessen, nicht angenommen,
- jede Anforderung ist entweder ein **Kriterium** oder ein ausdrückliches **Nicht-Ziel**; einen
  dritten Zustand gibt es nicht,
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

- der Auftrag steht auf `IN_ARBEIT` (§3),
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
- **kein Kommentar behauptet ein Verhalten, das der Code nicht hat**,
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

`ABGENOMMEN` ist noch keine Veröffentlichungserlaubnis. Vor `RELEASE_FREI` prüft der
Release-Prüfer unabhängig:

- Evaluator-Votum und Release-Kandidat zeigen auf denselben Commit,
- Merge-Ziel und Release-Diff enthalten ausschließlich freigegebene Änderungen,
- alle erforderlichen CI- und Qualitätstore laufen auf dem Release-Kandidaten erneut grün,
- Bundle und sonstige Artefakte sind frisch und reproduzierbar,
- Konfigurationen, Umgebungsvariablen und Abhängigkeiten sind vollständig,
- Migrationen sind vorwärts und im Rückweg geprüft,
- Backup-, Wiederherstellungs- und Abbruchweg sind benannt,
- Sicherheits-, Rechte-, Mandanten- und Datenschutzgrenzen bleiben erhalten,
- Smoke-Test und betriebliche Nachprüfung sind vorbereitet,
- es gibt keine offenen P0/P1-Befunde.

Nur nach `RELEASE_FREI` darf Yama die Veröffentlichung genehmigen. Nach der Veröffentlichung wird
der reale Zielstand geprüft: Version/Commit, Migrationen, zentrale Smoke-Tests, Logs und
Fehlerindikatoren. Erst danach lautet der Zustand `BETRIEBSBESTAETIGT`. Ein fehlgeschlagener
Release- oder Smoke-Test führt zu `RELEASE_BLOCKED` und dem vorbereiteten Rückweg.

## 11. Kurze Beweisberichte

Berichte werden nicht als fortlaufende Erzählung geführt. Pro Auftrag gibt es einen kompakten,
maschinenlesbaren Stand.

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
abnahme_commit: SHA
release_commit: SHA
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

Die Qualität von Planner, Plan-Prüfer, Generator, Evaluator und Release-Prüfer wird **nicht nach
Zeiträumen** bewertet. Es gibt keine monatliche oder kalendarische Qualitätsprüfung.

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
- Fehler, die erst nach Veröffentlichung gefunden wurden.

Die Prüfung endet mit genau einer Entscheidung:

- Skills tragen unverändert,
- Planner-Skill nachschärfen,
- Plan-Prüfer-Skill nachschärfen,
- Generator-Skill nachschärfen,
- Evaluator-Skill nachschärfen,
- Release-Skill nachschärfen,
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
  Freigabe von Yama **oder seines ständigen Vertreters nach §4 (Release-Prüfer, nur nach
  `RELEASE_FREI`; Ausnahmen der Vertretungsregel bleiben bei Yama)**.
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

Der aktuelle Stand wird aus Git, einem kompakten Auftragsdatensatz und dem Evaluator-Votum erzeugt,
nicht aus alten Erzähltexten. Ein Validator darf erst wieder als Autorität verwendet werden, wenn er
ausdrücklich gegen diese Version der Arbeitsregeln gebaut und unabhängig geprüft wurde. Alte
Auftragsvalidatoren und deren Statusschema sind aufgehoben.

Ein gültiger Status nennt mindestens:

- Auftrag,
- Zustand,
- Ballbesitzer,
- Basis-SHA,
- Prüf-SHA,
- Release-SHA, sobald vorhanden,
- letztes Votum,
- offene Akzeptanz,
- nächsten konkreten Schritt.

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

## 19. Änderungsverzeichnis

### Fassung 1.1 — 05.08.2026, auf ausdrückliche Anweisung Yamas

Vier Regeln, jede aus einem **gemessenen** Vorfall der Nacht vom 04. auf den 05.08. Sie sind vom
Planner formuliert und von Yama beauftragt; die Belege stehen in
[`PROZESSPRUEFUNG-01.md`](PROZESSPRUEFUNG-01.md) und in den Auftragsblättern A-01 bis A-03.

| § | Regel | Der Vorfall, der sie erzwungen hat |
|---|---|---|
| **3** | `IN_ARBEIT` wird vor der ersten Scope-Änderung gesetzt | A-01 und A-03 wurden gebaut, während sie auf `BEREIT` standen. Zweimal dieselbe Klasse |
| **5** | Testdaten-Ziel **und** Prozessbindung getrennt benennen | A-01 nannte `ticket_testing` **dreimal** und traf daneben: geseedet wurde dorthin, bedient wurde aus `ticket` |
| **5** | Vorgeschriebene Formen müssen vorhanden **und in Gebrauch** sein | A-03 baute den Riegel um `artisan serve`; benutzt wurde `php -S`. Für A-02 wurde `timeout` erwogen — es fehlt auf dieser Maschine |
| **5 / 7** | Kriterium oder Nicht-Ziel, kein dritter Zustand · kein Kommentar über nicht vorhandenes Verhalten | A-02s Kantenliste sagte „OHNE ZUSAGE … am Code zu belegen". Der Bauende löste den Widerspruch als **Kommentar** auf, der eine nicht gebaute Zeitgrenze behauptete |

> **Was diese vier verbindet.** Keine entstand aus Nachlässigkeit. Jede entstand, weil eine Regel
> **vollständig aussah und neben der Praxis herlief** — ein genannter Datenbankname ohne
> Prozessbindung, ein Riegel an der ungenutzten Tür, eine Anforderung ohne Zusage.
>
> **Papier findet Papierfehler.** Diese drei wurden erst sichtbar, als etwas lief: an der bedienten
> Bühne, am hängenden Tor, am Zustand während des Bauens. Deshalb binden alle vier neuen Regeln an
> eine **Handlung** oder eine **Messung auf der Zielmaschine**, nicht an eine Formulierung.

*Fassung 1.0 vom 04.08.2026 bleibt unverändert gültig; 1.1 ergänzt, streicht nichts.*

### Fassung 1.2.1 — 05.08.2026, auf Yamas Frage nach dem roten Weg

**Yamas Frage:** *„es muss auch ein Regel geben wer bekommt die Aufgabe, wie ist die Nachbesserung,
wann kommt die Aufgabe zurück zur Abnahme und welche Kriterien gelten."* §12 hatte die **Klassen**
und den Pflichtstopp — von den vier Fragen war eine halb beantwortet. **§12.1 bis §12.5 schließen
die Lücke.** Jede Regel hat einen Fall von heute Nacht:

| Regel | Der Fall |
|---|---|
| **12.1** Ball folgt der Klasse, gemischte Befunde werden geteilt | A-01: `CODE` beim Generator, aber der Prüfbefehl war unerfüllbar — der `SPEC`-Teil gehörte dem Planner und musste **zuerst** weg |
| **12.2** Reparatur läuft auf der Linie des Baus | A-02: zwei Fassungen derselben Reparatur (`ca5f80e4` / `6953198a`) auf zwei Zweigen, die beim Merge kollidieren |
| **12.3** Zwei-Richtungs-Probe je Befund | A-02: die Wieder-Abnahme trug den Rot-Beleg (hängendes `lsof` → 5,1 s) — deshalb war sie belastbar |
| **12.4** alle Kriterien, gestaffelt tief | A-01: fünf von sechs waren grün; ohne Wiederholung wüsste niemand, ob sie es nach der Reparatur noch sind |
| **12.5** abgenommen trotz `SPEC`-Befund | A-03: erfüllte seinen Auftrag vollständig; falsch war der Auftrag. `NACHBESSERN` hätte den Bauenden für meinen Fehler haften lassen |

**Nummer 1.2.1 statt 1.3:** Der Zweig `governance/arbeitsregeln-v1.1-20260804` führt bereits eine
**eigene 1.3**. Zwei verschiedene 1.3 wären schlimmer als die Gabelung selbst. Siehe
[`BEFUND-ZWEI-REGELWERKE.md`](BEFUND-ZWEI-REGELWERKE.md) — **die Fassungsfrage ist weiterhin offen
und gehört Yama.**
