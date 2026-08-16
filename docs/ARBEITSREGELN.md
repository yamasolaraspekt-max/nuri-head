# Verbindliche Arbeitsregeln

**Version:** 1.4.2
**Gültig seit:** 04.08.2026 · **Fassung 1.4 seit:** 05.08.2026 · **1.4.2 seit:** 05.08.2026
(vereint die veröffentlichte 1.3-Linie mit den lokalen Fassungen 1.1–1.2.2; Herkunft je Regel im
Änderungsverzeichnis §19)
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

- `SPEC_BLOCKED`: **die Spezifikation ist ungültig und muss neu geschnitten werden, bevor
  (weiter) gebaut wird.** Das ist **eine** Lage, erreichbar auf zwei Wegen: vor dem Bau erkannt
  (widersprüchlich, unvollständig, nicht machbar) oder **nach dem Bau** erkannt, weil sich das
  Verlangte als falsch erwiesen hat (§12.1). *Der Weg ändert nichts am Zustand — in beiden Fällen
  liegt der Ball beim Planner, und der Bau ruht.*
- `ENV_BLOCKED`: Umgebung verhindert eine gültige Prüfung.
- `DECISION_BLOCKED`: eine ausdrücklich Yama vorbehaltene Entscheidung fehlt.
- `RELEASE_BLOCKED`: der abgenommene Stand ist nicht sicher oder nicht reproduzierbar
  veröffentlichbar.

Zwei Zustände außerhalb der Baukette, **verankert 12.08. (A-21), weil sie im Gebrauch waren und
nirgends definiert**:

- `ERLEDIGT`: **ein Auftrag ist ausgeführt und gegengeprüft, ohne jemals Code erzeugt zu haben.**
  *Ein Ausführungsauftrag — etwas wird getan, nicht gebaut.* Er durchläuft die Baukette nicht und
  endet hier. **Belegt eine `IN_ARBEIT`-Stelle nach §3: NEIN.** *Realfall: `A-06` Probedaten
  Arbeits-DB, ausgeführt `880eb726`, gegengeprüft.*
- `VORLAGE`: **ein Verfahrensvorschlag, der auf Yamas Entscheidung wartet.** *Kein Bauauftrag.* Er
  wird nicht gebaut, sondern beschieden. **Belegt eine `IN_ARBEIT`-Stelle nach §3: NEIN**, *und er
  zählt auch nicht im §13-Zähler, weil dieser Planner-Bauaufträge zählt.* **Realfall: `P-02`
  parallele Instanzen, `c2de1eec`.**

> **Warum die Angabe „belegt einen §3-Platz: ja/nein" zur Definition gehört und nicht dahinter:**
> *ohne sie ist die Definition unbrauchbar.* **§3 lässt genau einen `IN_ARBEIT` zu — wer einen
> Zustand einführt, ohne zu sagen, ob er auf diese Schranke zählt, hat kein Wort erklärt, sondern
> eine Lücke geschaffen.** *Beide zählen nicht: keiner von beiden ist ein laufender Bau.*
>
> **Sie standen vorher nirgends.** *Gemessen am 12.08. vor dieser Änderung: `ERLEDIGT` **0** Treffer
> in dieser Datei, `VORLAGE` **0** — bei je einem Auftrag, der sie trug. `P-02` definierte `VORLAGE`
> in der **Kommentarspalte seiner eigenen Tafelzeile**; wer diese eine Zeile nicht las, erfuhr die
> Regel nie.* **Derselbe Fehlertyp wie A-20s vier Zustandsorte, eine Ebene kleiner.**

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

#### Die Prüfmethode — verankert 12.08., weil sie es vorher nicht war

`IN_ARBEIT` steht an **zwei** Orten in `docs/STATUS.md`: in der Tafelzeile und im Zustandsfeld des
Auftragsblocks. **Beide werden gemessen, und zwar mit diesen zwei Befehlen:**

```text
Tafelzeile      grep -cE '^\| \*\*[A-Z]+-?[0-9]+[^|]*\| *\*{0,2}`?IN_ARBEIT' docs/STATUS.md
Zustandsfeld    grep -cE '^zustand: *IN_ARBEIT' docs/STATUS.md
```

> **Warum `[^|]*\| *\*{0,2}`?` und nicht `.*` (berichtigt 12.08., A-19):** *die erste Fassung ließ
> nach der Auftragsnummer `.*` folgen — und das reicht **bis zum Zeilenende, über alle Spalten
> hinweg**. Damit zählte **jede Tafelzeile, die das Wort `IN_ARBEIT` irgendwo im Fließtext
> erwähnt**, als laufender Auftrag.* **Gemessen am Tag der Berichtigung: die alte Fassung meldete
> `3`, die neue `0` — und `0` war richtig, es lief keiner.** *Die drei Treffer waren `B7`
> (`BETRIEBSBESTAETIGT`), `B5N` (`ABGENOMMEN`) und `A-19` selbst (`BEREIT`); alle drei trugen in
> ihrer Notizspalte einen Satz **über** `IN_ARBEIT.` **Die neue Fassung greift auf die
> Zustandsspalte: Nummer, dann alles bis zum nächsten `|`, dann die Spalte selbst.***
>
> *Das Muster wuchs also mit jedem Auftrag, der über `IN_ARBEIT` **berichtet** — und das sind genau
> die sorgfältigen. **Ein Befund über einen Zustand ist nicht dieser Zustand** (H-9).*

**Die Messung fällt unmittelbar vor der ersten Änderung** (H-4), und **beide Zahlen werden genannt**.
Weichen sie voneinander ab, ist das ein Befund und keine Nebensache: dann behauptet einer der beiden
Orte etwas Falsches, und welcher es ist, entscheidet die Messung — nicht die Erwartung.

> **Warum der Ausdruck `[A-Z]+-?[0-9]+` und nicht `[AW]-[0-9]+`:** *bis zum 12.08. war ein Muster im
> Umlauf, das nur auf `A-` und `W-`-Aufträge passte. Gemessen trägt die Tafel aber auch `B-`, `M-`
> und `P-`-Zeilen — **fünf Aufträge waren für den ersten §3-Ort unsichtbar.** Der Fall ist
> eingetreten: `B5` stand sichtbar auf `IN_ARBEIT`, und die Schranke meldete **frei**. Das ist die
> gefährliche Richtung — sie sagt „frei", während gebaut wird. Gefunden hat es der Generator
> (`c528161c`) an seinem eigenen §3-Beleg; er hat den Befehl **nicht** eigenmächtig geändert, sondern
> beide Zahlen und das berichtigte Muster gemeldet. Entschieden vom Planner
> (`docs/ENTSCHEIDUNG-PARAGRAF-3-SCHRANKE-BERICHTIGT.md`), nachgemessen und bestätigt vom
> Plan-Prüfer (`50505407`: `[AW]` findet 31 Tafelzeilen, `[A-Z]+-?[0-9]+` findet 36).*

**Und der Grund, warum diese Methode hier steht und nicht nur in Auftragsblättern:** *sie stand
vorher in vier Dateien und in dieser Regel **null Mal**. Der Zustand `IN_ARBEIT` war verankert, die
Prüfmethode war eine Gewohnheit — von Blatt zu Blatt kopiert und nie geprüft. Eine Schranke, deren
Messvorschrift nirgends verbindlich steht, ist so stark wie die zuletzt kopierte Fassung.*

**Was die Schranke NICHT erfasst, ausdrücklich:** *eine laufende **Abnahme**. `IN_ARBEIT` zählt den
Bau, nicht die Prüfung. Wer eine Datei anfassen will, auf der ein Evaluator- oder Release-Claim
liegt, muss das selbst prüfen — §3 meldet dort frei. Vorbild ist die Zurückstellung von `B6` durch
den Generator (`ee2dad24`): „während eine Abnahme auf einer Datei läuft, verschiebe ich sie nicht."*

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
Evaluator gewesen sein. **Er darf für denselben Vorgang auch nicht Integrator sein** — zur sechsten Rolle siehe den **Nachtrag „Integrator" am Dateiende** (Entscheidung B-2 vom 14.08.2026; der Abschnitt steht am Ende, damit kein Zeilenverweis wandert).

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

### Wer den Auftrag schneidet, legt seinen Platz in der Statuswahrheit an (A-20, 12.08.)

> **Wer ein Auftragsblatt schneidet, legt im SELBEN Commit Tafelzeile UND Datensatz-Block in
> `docs/STATUS.md` an** — Zustand `ENTWURF`, `dor_beleg: steht aus`.
> **Wer danach prüft oder baut, ÄNDERT Felder in diesem einen Block. Er legt keinen zweiten an.
> Nie.**

**Warum der Schneidende und nicht erst die DoR:** *würde der Block erst bei der DoR entstehen, gäbe
es ein Fenster zwischen Schnitt und Prüfung, in dem der Auftrag in der Statuswahrheit **nicht
existiert**. Genau das ist am 12.08. bei W-38 eingetreten — Blatt committet, null Blöcke, null
Tafelzeilen.* **Die Statuswahrheit sagte dort nicht das Falsche, sie sagte gar nichts.**

**Warum ein Block mit geteilter Feldhoheit und nicht zwei Blöcke:** *der A-17-Doppelblock entstand
nicht daraus, dass zwei Rollen schrieben, sondern daraus, dass ein **zweiter Block** angelegt wurde,
der `zustand` trug.* **Ein Block schließt beides: kein Fenster, kein Doppelblock.**

*Unverändert gilt der Dreiklang: **Tafelzeile, `zustand` und `dor_beleg` sind EIN Handgriff.** Wer
nur zwei davon schreibt, hat verschoben statt freigegeben — die Regel oben sagt nur, **wer** ihn
zuerst ausführt.*

---

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
- kein Kriterium ist bereits erfüllt, **und jede Fachaussage, die das Blatt nennt, trägt `nachgerechnet_an` — oder das Nachrechnen ist ein Kriterium DIESES Blattes (Nachtrag am Dateiende: „Eine Formel, die niemand rechnet, ist nicht geprüft“, verbindlich seit 16.08.2026),**
- kein Kriterium ist unerfüllbar,
- jeder Prüfbefehl wurde auf Syntax und Aussagekraft geprüft,
- erforderliche Testdaten, Benutzerrolle, Route und Browserpfad sind benannt,
- Auswirkungen auf API, Server, Schema, Migration, Bestandsdaten und Bundle sind bewertet,
- führt der Auftrag eine Oberfläche oder einen Serverprozess aus, ist **getrennt** benannt:
  wohin die Testdaten gehen **und** wogegen der ausführende Prozess läuft — samt dem Befehl, der
  Letzteres beweist,
- jede vorgeschriebene Aufrufform, jedes Werkzeug und jeder Befehl ist auf der Zielmaschine
  **vorhanden** und dort **tatsächlich in Gebrauch** — beides gemessen, nicht angenommen.
  **„In Gebrauch" gilt für VORHANDENE Formen.** Schreibt der Auftrag ein **neu zu bauendes**
  Werkzeug vor, tritt an die Stelle ein **benannter Erstnutzer**: welche Rolle es ab wann in
  welchem Ablauf benutzt. *Sonst wäre kein Auftrag BEREIT-fähig, der etwas Neues vorschreibt —
  belegt an A-04-6,*
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

### E1 — Aussagen über den Bau werden am COMMIT gemessen, nicht am Arbeitsbaum

> **Yamas Anordnung vom 10.08.**, erteilt durch den Release-Prüfer in seinem Namen und mit
> ausdrücklich übergebenem Ball (*Prozessprüfung 03, `docs/PROZESSPRUEFUNG-03.md`*).

**Vor jeder `CODE_FERTIG`-Meldung wird JEDE berührte Datei gegen den Commit geprüft, und der
Befehl steht mit seiner Ausgabe im Bericht:**

```text
git show HEAD:<pfad> | diff - <pfad>        je beruehrter Datei
```

**Der Arbeitsbaum ist kein Beleg.** *Er zeigt, was jemand geschrieben hat — nicht, was committet
wurde. Beides fällt regelmäßig auseinander: eine vergessene Datei, ein Pfad außerhalb des Scopes,
ein Commit, der die Änderung gar nicht trug.*

> **Und die Umkehrung gehört dazu, weil sie zweimal übersehen wurde:** *auch ein **leerer**
> `git diff` ist kein Beleg.* **Nach einem Commit ist er zwangsläufig leer — er wäre auch dann
> grün, wenn zwanzig fremde Zustände geändert worden wären.** *Wer belegen will, dass er nichts
> Fremdes angefasst hat, misst den Commit:*

```text
git show <bau-sha> -- <pfad>                zeigt, was der Bau WIRKLICH geaendert hat
```

**Belegte Fälle, aus denen die Regel abgelesen ist:** *A-20-5 nannte `git diff --name-only` als
Nachweis und wurde vom Evaluator zurückgewiesen (`99fc86cd`); A-21 trug denselben Nachweistyp im
eigenen Kriterienblock — in einem Blatt, dessen Auftrag genau diese Verankerung war
(`605fde3b`, berichtigt in `6fa15fb7`).*

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

### E3 — die Unterform kommt in die Tabelle, nicht als eigene Klasse

> **Yamas Anordnung vom 10.08.**, erteilt durch den Release-Prüfer in seinem Namen
> (*`docs/PROZESSPRUEFUNG-03.md`*). **Einarbeitung: wer den Zähler fortschreibt.**

**Die vierte Fehlerklasse — „Zuordnung annehmen statt messen" — führt im Zähler eine Spalte
`Unterformen mit Barriere` und bekommt KEINE fünfte Klasse daneben:**

| Unterform | Was angenommen statt gemessen wird | Barriere |
|---|---|---|
| **Ort** | Zahlen über Dateimengen ohne Bezugspunkt | **V2** — der absolute Pfad steht daneben |
| **Zeitpunkt** | Aussagen über den Fernstand ohne frischen Abgleich | **V1** — `git fetch` im selben zitierten Befehl |
| **Zustand** | Arbeitsbaum statt Commit | **NEU** — `git show HEAD:<p> \| diff` (das ist E1, §11) |

> **Warum Spalte und nicht Klasse:** *die Klasse ist **semantisch** — sie beschreibt einen Denkfehler
> (etwas für gemessen halten, das angenommen wurde). Ihre Unterformen sind es nicht: sie
> unterscheiden nur, **worüber** die Annahme lief.* **Eine fünfte Klasse würde denselben Fehler
> zweimal zählen und die Zählung genau dort unehrlich machen, wo sie ehrlich sein soll.**

**Die Spalte macht sichtbar, dass jede Unterform bereits eine Barriere hat** — *und damit auch,
welche Unterform trotz Barriere wieder auftritt. Das ist die Zahl, die den Zähler wertvoll macht.*

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

### Die vier Orte — zwei bleiben, zwei entfallen (A-20, 12.08.)

**Der Zustand eines Auftrags stand bis zum 12.08. an vier Stellen. Zwei davon waren Kopien:**

```text
BLEIBT     docs/STATUS.md · Tafelzeile            die Uebersicht
BLEIBT     docs/STATUS.md · zustand: im Datensatz die Begruendung samt dor_beleg
ENTFAELLT  Auftragsblatt  · status: im Kopf       Kopie
ENTFAELLT  Auftragsblatt  · zustand: im Fuss      Kopie der Kopie
BLEIBT     Auftragsblatt  · status_steht_in: docs/STATUS.md
```

> **`docs/STATUS.md` ist die einzige Statuswahrheit.** *Wer wissen will, ob ein Auftrag `BEREIT`
> ist, liest sie — **nicht den Blattkopf**. Das Feld `status_steht_in:` sagte das schon; es wird
> damit vom Hinweis zur einzigen Auskunft.*

**Warum die Kopien entfallen statt „disziplinierter nachgezogen" zu werden — gemessen am 12.08.:**

```text
43 Auftragsblaetter · 33 mit status: im Kopf · 10 mit zustand: im echten Blattfuss

Blattkopf gegen die Statuswahrheit in docs/STATUS.md:
  abweichend        32        uebereinstimmend  1        ohne Datensatz  0
  davon: Kopf ENTWURF, waehrend BETRIEBSBESTAETIGT gilt  22
```

**Zweiundzwanzig von 33 Blättern behaupteten `ENTWURF`, während der Auftrag abgenommen und im
Betrieb bestätigt war.** *Nur ein einziger Kopf von 33 stimmte — und **jedes** Blatt hatte einen
Datensatz.*

> **Diese drei Zahlen standen hier zuerst falsch (29 · 3 · 20), und der Fehler saß in meinem
> Raster:** *es las die Auftragskennung mit `^auftrag: "([^"]+)"` und verlangte damit
> Anführungszeichen.* **In `docs/STATUS.md` sind die Felder uneinheitlich geschrieben — 31 mit, 19
> ohne** *— und ausgerechnet `A-09`, `A-11` und `A-12` stehen ohne.* **Sie fielen aus der Messung
> und erschienen als „ohne Datensatz", obwohl alle drei einen haben.** *Gefunden hat es der
> Evaluator (`99fc86cd`); die uneinheitliche Schreibweise ist die eigentliche Falle und hat an
> einem Tag drei Rollen falsch messen lassen.*

> **Zur Herkunft der Zahlen, weil eine ältere Fassung dieses Absatzes „24 mit zustand: im Fuss" und
> „17 Widersprüche" nannte:** *beide stammten aus der Regel „die erste `^zustand:`-Zeile ist der
> Blattfuß". Die ist falsch* — **`^zustand:` steht auch in MELDEBLÖCKEN** *(yaml-Blöcke mit
> `auftrag:` und `bau_commit:`, also datierten Bauaufzeichnungen).* **Gemessen: 10 echte Blattfüße,
> 17 Meldeblöcke auf 14 Blättern — zusammen die 24.** *Von den „17 Widersprüchen" sind 4 echt
> (Kopf gegen Fuß), 13 verglichen einen Kopf mit einer Bauaufzeichnung.* **Unterschieden wird am
> BLOCK, nicht an der Zeile.**

**Eine Stelle, die es nicht gibt, kann nicht veralten.** *Ein Verfahren, das an Aufmerksamkeit
hängt, ist bei 43 Blättern und fünf Rollen keines.*

> **Der belegte Beinahe-Schaden:** *beim Bau von W-20 stand im Blattkopf `status: ENTWURF`, während
> `BEREIT` galt. Der Generator ist nur deshalb nicht gescheitert, weil er den DoR-Beleg woanders
> gesucht hat.* **Das war Findigkeit, kein Verfahren — 32 Blätter waren Fallen dieser Art.**

**Der Statusträger ist `docs/STATUS.md`** — namentlich. *Fassung 1.3 nannte hier
`docs/AKTUELLER_AUFTRAG.yaml`; P-01 mit Yamas Weisung (Fassung 1.2.2, Commit `8fc5edb8`) hat den
Träger auf die Datei festgelegt, die alle Rollen tatsächlich benutzen. Eine Regel, die den Träger
nicht benennt — oder einen benennt, den niemand führt —, wird durch die Praxis ersetzt statt
befolgt.* Der aktuelle Stand wird aus diesem getrackten Datensatz, dem darin festgeschriebenen
Git-Stand und dem letzten unabhängigen Votum erzeugt, nicht aus alten Erzähltexten. Ein Validator
darf erst wieder als Autorität verwendet werden, wenn er ausdrücklich gegen diese Version der
Arbeitsregeln gebaut und unabhängig geprüft wurde. Alte Auftragsvalidatoren und deren
Statusschema sind aufgehoben.

**Aus Fassung 1.3 des `governance`-Zweigs übernommen (Ernte, P-01-5):**

- **Ein Push ist Transport zur Prüfung, keine Veröffentlichung.** `VEROEFFENTLICHT` beginnt erst
  mit der Integration in den freigegebenen Zielbranch oder dem ausdrücklich benannten Deployment.
  Push und Zielintegration sind **zwei getrennte Freigaben**.
- **Ein Statuscommit vermischt sich nicht mit Produktivcode.** Er nennt den Zustandswechsel in der
  Botschaft und ändert **keine** Produkt-, Test- oder Regeldatei.

  > *Abgeschwächt gegenüber 1.3, die je Statuscommit **genau einen** Zustandswechsel und sonst
  > nichts verlangt. Grund: in dieser Nacht trug fast jeder Commit Blattänderung und Zustand
  > zugleich; die strengere Form wäre nach §5-Maßstab **nicht plausibel** und würde umgangen.
  > **Der Plan-Prüfer darf widersprechen** — dann gilt die strenge Form.*

Ein gültiger Status nennt mindestens:

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

## 18a. Hausregeln — Grundsätze aus gemessenen Vorfällen

> **Aufgenommen 12.08. auf Yamas ausdrückliche Anweisung** (*„H-1 bis H-7 gehören in die
> ARBEITSREGELN — und die Sammlung geht darin auf"*). **Vom Planner formuliert, von Yama
> beauftragt** — dieselbe Praxis wie bei Fassung 1.1.
>
> **Warum `18a` und nicht `19`:** *das Änderungsverzeichnis ist unter `§19` **fünffach
> referenziert** (`STATUS.md:502`, `:579`, `:802`, `:803`, `handoff-status.md:1612`). Eine
> Umnummerierung hätte fünf Verweise gebrochen. **Gemessen, bevor umnummeriert wurde.***
>
> **Diese Regeln stehen NICHT über den Abschnitten 1–18, sondern daneben.** *Bei Widerspruch gilt
> §1s Rangfolge unverändert.*

### H-1 · Eine Notiz über eine Lücke ist kein Plan für die Lücke

Wer eine Zeile schreibt, die erklärt, **warum** etwas nicht enthalten ist, hat damit **nichts
erledigt**. Ein Ausschluss ist erst gültig, wenn daneben steht, **wo die Sache stattdessen
hingeht**. „Nicht hier" ohne „sondern dort" ist ein offener Posten in Tarnkleidung.

*Belege: `W-09` (zwei Tage ohne Blatt, weil `FAHRPLAN-KLASSE-A.md:148` die Lücke nur notierte) ·
`konterlattungMm` (definiert, zweimal befüllt, von nichts gelesen) · `auswechslung.ts` (in zwei
Blättern „verwandt, nicht im Scope", in keinem zuhause).*

### H-2 · Ein Bericht, der ein Fachurteil wie eine Messung aussehen lässt, ist gefährlicher als keiner

Jede Zeile, die eine Bewertung trägt, sagt sichtbar, ob sie **gemessen** oder **geurteilt** ist. Bei
Fachurteilen steht „vorgeschlagen, nicht entschieden" oder der Name dessen, der urteilt.

### H-3 · Ein Reifegrad ist eine Ablesung, keine Bewertung

Die Tafel ist das **Instrument**, kein Zeugnis. **Ein Instrument, das schont, zeigt falsch.**
Schuldfrage und Zustandsfrage sind zu trennen: ein Altstand entschuldigt den Entstehungsweg, er
macht die Angabe nicht wahr.

*Und: eine Kennzahl, die sich durch Papier bewegen lässt, misst Papier — **der Abschlusszähler steigt
nur durch Bauten, nicht durch Schnitte.***

### H-4 · §3 sperrt die Dateien im Scope des laufenden Auftrags — nicht das Repo

„Ein Auftrag läuft" und „meine Datei ist gesperrt" sind **zwei verschiedene Messungen**. Wer die
erste macht und die zweite meint, verliert Zeit; wer es umgekehrt macht, verliert Arbeit. **Die
Scope-Sektion des `IN_ARBEIT`-Auftrags wird unmittelbar vor dem Schreiben gelesen**, nicht Minuten
davor.

*Belege in beide Richtungen: `ce30174f` (drei Minuten alte Messung → in fremden Scope geschrieben) ·
12.08. dreimal `REGISTER.md` liegen gelassen, obwohl der laufende Auftrag `app/` hielt.*

### H-5 · Ein Werkzeug darf nur urteilen, wenn es alle Bedingungen kennt, von denen das Urteil abhängt

Sonst **rechnet es Werte und schweigt**. Prüfform: Bedingungen der Aufgabe auflisten, gerechnete
zählen, vergleichen. **Die Begründung ist nicht die Schwere der Folge, sondern die
Unvollständigkeit** — eine Engine darf nicht „bestanden" sagen, was sie nicht geprüft hat.

*Belegte Fälle, die ihre Grenze selbst benennen und trotzdem urteilen: `sparrenBerechnung`
(„Ersetzt KEINE prüffähige Statik") · `fbhAuslegung:6-7` · `heizkreisVerteiler:6`.*

### H-6 · Ein Wort ist kein Beleg; erst die Stelle ist einer

*Fälle eines Tages: `bewerteDeckung` (Leistungsdeckung, nicht Dachdeckung) · `material` traf jedes
`THREE.Material` (61 Dateien) · `export` traf jedes `export function` · `GEG` traf „gegeben" ·
`< 1 mm²` wurde als Platzhalter gezählt.*

**Gehört zu B5:** *B5 verlangt, die Trefferzeilen zu lesen — H-6 sagt, warum.*

### H-7 · Ein Ist-Wert ist kein Soll-Wert

Ein Kundenaufmaß sagt, wie ein Bauteil **gebaut wurde**. Eine Fachregel sagt, wie es **gebaut werden
muss**. Ein Planungswerkzeug braucht das Zweite. **`QUELLE = Kundenaufmaß` belegt Vorkommen, nicht
Zulässigkeit — und hebt allein keine 🔴-Sperre auf.**

*Jeder Wert in einem Katalog trägt, ob er Ist oder Soll ist. Eine Tabelle aus Ist-Werten mit dem
Etikett „Fachregel" ist F-051 in neuer Gestalt, nur mit echten Zahlen.*

### H-8 · Mehrfachvorkommen ist kein Beleg — und der Ort ist kein Beleg für die Wirkung

> **(a) Dieselbe Zahl an vier Stellen ist nicht vier Belege** — sie ist ein Beleg, dreimal kopiert,
> oder gar keiner, viermal kopiert. *Die Frage ist nie „wie oft kommt sie vor", sondern **„wie oft
> kommt sie UNABHÄNGIG vor"**.* **Wer Vorkommen zählt, misst Verbreitung. Wer Herkunft prüft, misst
> Wahrheit.**
>
> **(b) Wo eine Datei liegt, sagt nichts über ihre Wirkung.** *„Steht im Produktivcode" gilt erst
> als belegt, wenn ein **Aufrufer** genannt ist — Route, `@include`, `@extends` oder ein aufgelöster
> dynamischer View-Name. **Ordnerlage genügt nicht.***

**Der belegte Fall, aus dem beide Teile stammen:** *`TIME_VARS` — elf Zeitwerte an **vier**
Fundorten, davon **null** unabhängige Herkunftsangaben. Der Kommentar der Quelle sagt es selbst:
„time assumptions (minutes) – adjust to your company values". **Ein Platzhalter, viermal
mitkopiert und nie eingelöst.*** *Und der vierte Fundort lieferte Teil (b): er liegt in
`resources/views/` und sah deshalb nach Auslieferung aus — gemessen hatte er **0** statische
View-Referenzen, die gleichnamige Route zeigt auf eine andere Datei ohne `TIME_VARS`, und seine
ganze Historie ist **ein** Commit „Checkpoint: save WIP".*

> **Beides ist dieselbe Verwechslung:** *ein Merkmal, das leicht zu zählen ist, wird für das Merkmal
> genommen, auf das es ankommt — **Vorkommen statt Herkunft, Ort statt Ausführung**.*

**Und die Reichweite der eigenen Messung gehört dazu:** *„kein statischer Aufrufer" ist eine andere
Aussage als „unerreichbar".* **Wer die dynamische Lücke nicht ausschließen kann, benennt sie —
statt sie wegzulassen.**

*Das Tor erinnert daran (`scripts/commit-pruefen.sh`): eine Botschaft, die mehrere Fundorte nennt
und keine Herkunft, bekommt eine **Warnung**. Kein Abbruch — Mehrfachvorkommen ist meistens harmlos,
jede Konstante kommt mehrfach vor.*

### H-9 · Ein Muster misst, woran es ansetzt

> **Ein Muster, das eine Schreibweise voraussetzt, misst die Schreibweise und nicht die Sache.**

**Die Prüfform, und sie ist dreifach erprobt statt erfunden:**

> **„Findet der Befehl die Zeile, die ich mit eigenen Augen gelesen habe? Erst danach zählen."**

**Die Abgrenzung zu H-6 ist der Kern — zwei Richtungen brauchen zwei Regeln:**

| | Die Frage | Der Fehler | Beispiel |
|---|---|---|---|
| **H-6** | *„Triffst du, was du meinst?"* | **Fehltreffer** — das Muster trifft, was es nicht meint | `material` traf jedes `THREE.Material`; `GEG` traf „gegeben" |
| **H-9** | *„Setzt du an, wo die Sache steht?"* | **richtiger Treffer, falsche Sache** | die `B7`-Zeile steht auf `ABGENOMMEN` und enthält den Satz „nie auf `IN_ARBEIT` gesetzt" — der Treffer stimmt, die Zählung nicht |

> **Ein Muster kann H-6 bestehen und an H-9 scheitern.** *Der §3-Fall ist genau das: der Ausdruck
> war syntaktisch korrekt und traf **genau, was dort stand** — er nahm nur einen **Befund über einen
> Zustand** für **diesen Zustand**.*

**Warum das eine eigene Regel ist und kein Einzelfall:** *am 12.08. wurden **neun** Fälle derselben
Klasse gemeldet, von **vier Rollen unabhängig** — B5s Belegzeilen, die Geheimnisprüfung auf
Token-Namen, eine Berechtigungsregel gegen den ganzen Befehlstext, das §3-Tafelzeilenmuster,
`--diff-filter=D`, die Platzhalterzählung mit spitzen Klammern, `[AW]` statt `[A-Z]+`, das erste
`zustand:`-Feld je Block, und das Zählen nur der grünen Plakette.* **In jedem einzelnen Fall war das
Muster syntaktisch korrekt. Falsch war, woran es ansetzte.**

**Keine Barriere im Tor, und das ist eine Entscheidung mit Grund:** *B5, B6 und B7 stehen bereits in
derselben Datei, und am selben Tag wurde **dreimal** gemeldet, dass eine Warnung, die bei richtiger
Arbeit anschlägt, weggeklickt wird.* **Eine Barriere gegen falsche Muster wäre selbst ein Muster —
und könnte denselben Fehler machen.** *H-9 wirkt über die DoR: eine Frage in einer vorhandenen
Liste, kein neuer Schritt.*

*Was die Regel **nicht** kann, der Ehrlichkeit halber: sie verhindert keinen falschen Ausdruck. Sie
macht die Frage danach zur Pflicht. **Alle neun Fälle wurden erst nach dem Schaden gefunden — H-9
verschiebt den Fund nach vorn, sie ersetzt ihn nicht.***

---

## 18b. Die Barriere B5 — Zählergebnis und Trefferzeilen

> **B5 · Ein Zählergebnis, das einen Befund trägt, wird nie ohne seine Trefferzeilen gemeldet.**
> *Wer `-c` benutzt, um etwas zu behaupten, führt denselben Lauf ohne `-c` und liest, was er gezählt
> hat. Gilt für alle fünf Rollen, gilt auch für Messungen über die eigene Werkbank.*

**Der Unterschied trägt die Regel.** Ohne ihn wird B5 als „nie `-c` benutzen" gelesen — und das
machte jede Suite-Meldung unlesbar:

| | Beispiel | Was die Trefferzeilen leisten |
|---|---|---|
| **Zahl als Gegenstand** | „Die Suite zählt 1692" · „0 Platzhalter" | **nichts** — die Zahl *ist* die Aussage |
| **Befund aus einer Zahl** | „CSG kommt einmal vor, **also ist es gebaut**" | **alles** — die Zeile entscheidet über den Befund |

*Der belegte Fall: der eine CSG-Treffer stand im Dateikopf — `dachAusschnitt.ts:10`, „Stufe C (NICHT
hier): … CSG". **Ein Treffer im Kommentar ist kein Code.** Und der Gegenfall aus demselben Tag:
ein Filter, der einen Fehlertyp ausschließt, erzeugt leicht einen anderen — `{2,40}` entfernte einen
falschen Treffer und verlor dabei drei echte.*

**Die Regel wirkt beim Schreiben, das Tor erinnert beim Committen.** `scripts/commit-pruefen.sh`
gibt eine **Warnung** aus, wenn eine Botschaft ein Zählwort ohne Belegzeile trägt. Bewusst keine
Sperre: eine harte Sperre auf Zahlen in Commit-Botschaften blockierte jeden legitimen Bericht, und
was bei jedem zweiten Aufruf falsch anschlägt, wird umgangen (an A-03 belegt). **Das Tor kann nicht
prüfen, ob die Messung inhaltlich stimmt — das kann kein Tor.** Es sieht nur, ob die Zeilen fehlen.

*Ort dieser Verankerung: neben den Hausregeln, weil **H-6 die Barriere dort bereits aufruft** —
„B5 verlangt, die Trefferzeilen zu lesen, H-6 sagt, warum". Eine „Barrierenliste" gibt es in diesem
Dokument nicht: `grep -n 'Barrierenliste' docs/ARBEITSREGELN.md` läuft ins Leere (A-10 — das leere
Ergebnis wird gemeldet, nicht überschrieben).*

---

## 18c. Die Barriere B6 — Summe und Menge, und die Abgrenzung zu B5

> **B6 · Eine Summe braucht eine Erhebung, keine Sammlung.**
> *Wer eine Gesamtzahl über eine Menge meldet, definiert zuerst die Menge (Pfad, Muster,
> Abgrenzung), erhebt sie vollständig und meldet Menge **und** Summe. Was beim Suchen nebenbei
> aufgefallen ist, ist ein **Fund**, keine Summe — und wird als Fund gemeldet.*

**B5 und B6 sind nicht dieselbe Klasse, und die Trennung ist ausdrücklich gesetzt.** *Ohne sie
verschmelzen die beiden, und eine von ihnen wird nicht mehr angewandt:*

| | Der Fehler | Das Gegenmittel |
|---|---|---|
| **B5** | ich habe **gezählt** und die Zeilen nicht **gelesen** | denselben Lauf ohne `-c` fahren |
| **B6** | ich habe nie gesagt, **worüber** ich zähle | die Menge zuerst benennen, dann erheben |

**Der belegte Vorfall zeigt, warum B5 hier nicht geholfen hätte:** *gemeldet waren „über 640 Zeilen
Prozessebene", erhoben wurden **1.593 Zeilen in acht Bausteinen**. **Jede einzelne Zeilenzahl war
richtig.** Falsch war, dass fünf von acht Dateien nie in der Menge waren — zwei ganz übersehen, zwei
als „(Teil)" geführt statt gezählt.* **Eine Sammlung ist ein Nebenprodukt einer anderen Suche; eine
Summe ist eine Behauptung über eine vollständige Menge.**

Was erlaubt bleibt — B6 verbietet keine Zahlen, sondern Summen ohne Menge:

```text
ERLAUBT   "StartView.tsx 267 Zeilen"                      eine Zahl über EIN Ding
ERLAUBT   "acht Bausteine, zusammen 1.593 Zeilen:         Summe MIT Menge
           StartView 267 · ConfigWizard 271 · …"
ERLAUBT   "gefunden beim Suchen: ConfigWizard (271 Z)"    als FUND gekennzeichnet
VERBOTEN  "über 640 Zeilen Prozessebene"                  Summe OHNE Menge
```

**Das Tor warnt** (`scripts/commit-pruefen.sh`), wenn eine Botschaft ein Summenwort mit einer Zahl
trägt und keine Menge nennt — Warnung, kein Abbruch, dieselbe Stufe und dieselbe Begründung wie bei
B5. *Es kann nicht prüfen, ob die Menge **vollständig** ist; es kann nur fragen, ob eine genannt
wurde. Das Summenwort braucht dabei eine Zahl in Reichweite, sonst fängt „insgesamt" jeden
Fließtext.*

---

## 19. Änderungsverzeichnis

### Fassung 1.6 — 12.08.2026, Barriere B6 verankert (Generator, Auftrag B6)

**Abschnitt 18c aufgenommen**, mit der Abgrenzung zu B5 als Tabelle (B6-5) und dem Vorfall, der
zeigt, warum B5 dort nicht geholfen hätte. *Rein additiv, wie 18b: `git diff` zeigt für dieses
Dokument 0 gelöschte Zeilen.*

### Fassung 1.5 — 12.08.2026, Barriere B5 verankert (Generator, Auftrag B5)

**Abschnitt 18b aufgenommen** mit dem Unterschied „Zahl als Gegenstand" ↔ „Befund aus einer Zahl"
(B5-4). *Zur Nummer: §19 ist in sich uneinheitlich — Fassung 1.4.2 trägt den 05.08., Fassung 1.3
den 12.08. Ich habe das gemessen und nicht begradigt; das Regelwerk gehört nicht dem Generator.
1.5 ist deshalb als nächstfreie Nummer gewählt, nicht als Aussage über die Reihenfolge.*

### Fassung 1.3 — 12.08.2026, sieben Hausregeln auf Yamas Anweisung

**Abschnitt 18a aufgenommen.** *Yama hatte H-1 bis H-7 in drei aufeinanderfolgenden Antworten
gesetzt; offen war nur der Ort. Vom Planner formuliert und gesammelt, von Yama beauftragt, vom
Plan-Prüfer gegenzulesen.*

```text
JEDE der sieben stammt aus einem GEMESSENEN Vorfall dieser Woche, keine aus einem Vorsatz:
  H-1  W-09 lag zwei Tage ohne Blatt, weil die Luecke notiert war
  H-2  meine Normnennungs-Achse gab ein Urteil als Kriterium aus
  H-3  ich nannte Zurueckstufen eine "Strafe" und verwechselte Schuld mit Zustand
  H-4  §3 einmal zu spaet gemessen (ce30174f), dreimal zu vorsichtig
  H-5  drei Engines benennen ihre Grenze selbst und urteilen trotzdem
  H-6  fuenf Suchmuster an einem Tag trafen das Wort statt den Gegenstand
  H-7  ich schlug Kundenaufmasse als Quelle fuer eine Fachregel vor
```

**Die Sammlung `docs/HAUSREGELN.md` ist damit aufgelöst** — sie trägt nur noch einen Verweis
hierher. *Zwei Fassungen einer Regel wären genau die zweite Wahrheit, die H-1 verhindern soll.*

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

| Regel | Der Fall — *„hätte verhindert"* oder *„bestätigt durch Praxis"* |
|---|---|
| **12.1** Ball folgt der Klasse, gemischte Befunde werden geteilt | A-01: `CODE` beim Generator, aber der Prüfbefehl war unerfüllbar — der `SPEC`-Teil gehörte dem Planner und musste **zuerst** weg |
| **12.2** Reparatur läuft auf der Linie des Baus | A-02: zwei Fassungen derselben Reparatur (`ca5f80e4` / `6953198a`) auf zwei Zweigen, die beim Merge kollidieren |
| **12.3** Zwei-Richtungs-Probe je Befund | *bestätigt durch Praxis* — A-02: die Wieder-Abnahme trug den Rot-Beleg (hängendes `lsof` → 5,1 s) und war deshalb belastbar |
| **12.4** alle Kriterien, gestaffelt tief | *bestätigt durch Praxis* — A-01: fünf von sechs waren grün; ohne Wiederholung wüsste niemand, ob sie es nach der Reparatur noch sind |
| **12.5** abgenommen trotz `SPEC`-Befund | *bestätigt durch Praxis* — A-03: erfüllte seinen Auftrag vollständig; falsch war der Auftrag. Der Evaluator handelte so, **bevor** es die Regel gab |

**Nummer 1.2.1 statt 1.3:** Der Zweig `governance/arbeitsregeln-v1.1-20260804` führt bereits eine
**eigene 1.3**. Zwei verschiedene 1.3 wären schlimmer als die Gabelung selbst. Siehe
[`BEFUND-ZWEI-REGELWERKE.md`](BEFUND-ZWEI-REGELWERKE.md) — **die Fassungsfrage ist weiterhin offen
und gehört Yama.**

### Fassung 1.2.2 — 05.08.2026, die vier Auflagen aus P-01

**Der Plan-Prüfer hat 1.1 und 1.2.1 `FREIGEGEBEN MIT AUFLAGE`** (gemessen an `90ebba40`). Damit
sind sie **verbindlich**; die Auflagen sind Nachbesserung am geltenden Text, keine aufschiebende
Bedingung. **Alle vier sind hiermit erledigt:**

| Auflage | Erledigung |
|---|---|
| **A1** | §3: `SPEC_BLOCKED` ist **eine** Lage („Spezifikation muss neu, bevor weitergebaut wird") mit zwei Erkennungswegen — kein neuer Zustand, weil der Ball in beiden Fällen beim Planner liegt |
| **A2** | §5: „in Gebrauch" gilt für **vorhandene** Formen; ein neu zu bauendes Werkzeug braucht einen **benannten Erstnutzer**. Belegt an A-04-6, das sonst nie `BEREIT` werden könnte |
| **A3** | §16: `docs/STATUS.md` **namentlich** benannt · 1.3-Ernte übernommen (Push = Transport · Statuscommit ohne Produktivcode) |
| **A4** | §19: die Fall-Spalte trennt jetzt *„hätte verhindert"* von *„bestätigt durch Praxis"* — 12.3/12.4/12.5 sind kodifizierte Praxis, nicht erzwungene Lehren |

**Zwei Ergebnisse seiner Prüfung, die gegen mich liefen und die ich hier festhalte:**

- **Kausalität:** mein Verdacht gegen §12.5 war richtig — **und traf auch 12.3 und 12.4.** Drei
  von neun Regeln beschreiben, statt zu verhindern.
- **Machtprüfung:** mein Verdacht war **falsch**. §12.5 entlastet den Bauenden, nicht mich: der
  `SPEC`-Befund bleibt verbucht, erzwingt einen Folgeauftrag und zählt in §13 **gegen den Planner**.

**Eine Abweichung von der Auflage habe ich ausdrücklich gemacht:** die 1.3-Regel „genau ein
Zustandswechsel je Statuscommit" ist **abgeschwächt** übernommen. *Nach meinem eigenen §5-Maßstab
wäre die strenge Form nicht plausibel — in dieser Nacht trug fast jeder Commit Blatt und Zustand
zugleich. Der Plan-Prüfer darf widersprechen; dann gilt die strenge Form.*

**Nicht erledigt und ausdrücklich offen:** die **Zweig-Zusammenführung** (`fork` enthält den
governance-Merge, wir nicht — 42 gegen 10 Commits). *Das ist Topologie, nicht Fassungsinhalt, und
sie gehört Yama.*

### Fassung 1.4 / 1.4.1 / 1.4.2 — 05.08.2026, Zusammenführung der beiden Linien (Release-Prüfer)

Die Zweig-Zusammenführung, die 1.2.2 oben als offen führt, ist auf der Arbeitslinie vollzogen:
Yama hat die 1.3 selbst durch die volle Kette veröffentlicht (PR #1, Betrieb bestätigt 04.08.),
damit ist die veröffentlichte Linie die Basis. Darauf:

| Fassung | Inhalt | Herkunft |
|---|---|---|
| **1.4** | Vertretungsregel: der Release-Prüfer genehmigt und führt in Yamas Namen aus (Push, Merge nach `main`, Tags, Deployments außer Produktion) — **ausschließlich für Stände mit `RELEASE_FREI`**; Produktion, produktive Datenoperationen, Force und endgültige Löschung bleiben bei Yama | Yamas mündliche Weisung 05.08. |
| **1.4.1** | §12.1–12.5 („der rote Weg") aus der lokalen 1.2.1 übernommen | lokale Linie |
| **1.4.2** | die vier P-01-Auflagen aus 1.2.2 eingearbeitet: §3 `SPEC_BLOCKED` eine Lage/zwei Wege · §5 benannter Erstnutzer für neue Werkzeuge (samt der drei 1.1-Punkte, die der 1.3 fehlten) · §16 Statusträger namentlich `docs/STATUS.md` (ersetzt die 1.3-Nennung `AKTUELLER_AUFTRAG.yaml` — Beleg `8fc5edb8`) mit 1.3-Ernte und dokumentierter Abschwächung · §19 vollständiges Änderungsverzeichnis | lokale 1.2.2 (P-01, Yamas Weisung), zusammengeführt vom Release-Prüfer |

---

## NACHTRAG · Integrator *(sechste Rolle, Yamas Entscheidung B-2 vom 14.08.2026)*

**Rollenkennung `TICKET_ROLLE=integrator`. Eigener sechster Agent — eine Fachrolle darf nicht
stillschweigend zum Integrator werden.**

Der Integrator führt fremde, freigegebene Arbeit zusammen: **einzeln, mit Ursprungsangabe, ohne eine
fachliche Entscheidung dabei zu treffen.** Er arbeitet ausschließlich im **Integrations-Checkout**
und ist **alleiniger Schreiber von `docs/STATUS.md`** — ausnahmslos, auch für eine einzelne
Tafelzeile.

**Er darf beim selben Vorgang weder Evaluator noch Release-Prüfer sein oder gewesen sein.** Er
ersetzt keine fehlende Freigabe, verändert keine Kriterien, löst keinen Konflikt still und
übernimmt keine Commits gesammelt. Bei Konflikten, fremden Änderungen oder unklarer Herkunft
**bricht er ab und meldet** — der Abbruch ist ein Ergebnis, keine Störung.

**Er bestimmt und begründet den `AKTIVIERUNGS_SHA`** und bewahrt den `FORENSISCHEN_SHA` als reinen
Untersuchungsstand. **Den forensischen Stand als Aktivierungsbasis auszugeben ist ihm untersagt:**
wer darauf startet, beginnt mit einem veralteten Stand und erzeugt sofort eine zweite Wahrheit.

**Bis alle sechs Einsatzvoraussetzungen belegt sind, arbeitet er ausschließlich lesend** — vier
einzelne Schreibstoppbelege · keine schreibende Altinstanz · vollständig aufgenommener
Arbeitsbaum · ausgeschlossene Schreibprozesse · gemessene Ruhephase · aktiver eigener Rollen- und
Checkoutschutz. **Eine commitfreie Zeit allein genügt nicht.**

**Sein Rollenpaket** liegt in `docs/rollenkette/rollen/6-integrator/` und wird **unabhängig vom
Plan-Prüfer** abgenommen, nicht vom Planner, der es geschrieben hat.

**Kein Push, kein Merge nach `main`, kein Tag, kein Deploy, kein Force-Push, kein Rebase und kein
Umschreiben veröffentlichter Historie ohne Yamas ausdrückliche Freigabe.**

**Betriebsarten und die Bootstrap-Entscheidung.** Der Integrator kennt drei Betriebsarten:
`NUR_LESEND` (messen und berichten, **einschließlich Bestimmung und Begründung des
`AKTIVIERUNGS_SHA`** — einen vorhandenen Commit zu benennen ist keine Schreibhandlung), `BOOTSTRAP`
(ausschließlich Worktrees anlegen) und `SCHREIBEND` (integrieren, `docs/STATUS.md` schreiben).
**Für die Umstellung vom 14.08.2026 gilt B2: Yama bzw. eine ausdrücklich von Yama autorisierte
Infrastrukturhandlung legt die Rollen-Worktrees an.** Der Integrator führt **vor** Aktivierung seiner
unabhängig geprüften Barriere **keine Git-Verwaltungsänderung** aus; `BOOTSTRAP` bleibt nur als
dokumentierter Notfallweg bestehen und ist **nicht freigegeben**. **Die bloße Dokumentation einer
Betriebsart ist keine Erlaubnis, sie zu benutzen.**

---

## NACHTRAG · Zwei Haltbarkeiten einer Messung *(Yamas Entscheidung vom 16.08.2026)*

**Jede Messung gehört einer von zwei Klassen an, und sie werden verschieden behandelt.**

| Klasse | Gegenstand | Beleg | Haltbarkeit |
|---|---|---|---|
| **UNVERÄNDERLICH** | Repo-Historie · Commits · Blattinhalte **an einem SHA** | **der SHA** | **für immer** — der Stand kann nicht wandern |
| **FLÜCHTIG** | Arbeitsbäume · installierte Module · laufende Prozesse · alles außerhalb der Versionierung | **der Zeitstempel** | **ab dem Zeitstempel, und sie läuft ab** |

> **Eine flüchtige Messung wird NUR MIT Zeitstempel notiert.** Ohne ihn ist sie eine Behauptung
> über die Gegenwart, **die morgen als Behauptung über heute gelesen wird.**

**Der Anlass, wörtlich belegt:** Am 15.08. hat der Planner in ein Auftragsblatt geschrieben, **kein
Rollenbaum** habe `node_modules` — richtig gemessen um **15:30:37**. Um **15:30:51** installierte
der Release-Prüfer seines, um **15:36:54** der Generator. **Der Befund hielt vierzehn Sekunden.**

**Kein Messfehler auf einer der beiden Seiten.** Der Plan-Prüfer, der es fand, hat es selbst so
eingeordnet: *„Der Planner hat richtig gemessen, die Umgebung ist unter dem Satz weggewandert — und
die überholte Quelle bin ich."*

**Was daraus folgt und was nicht:**

- **Nicht:** langsamer messen, oder flüchtige Messungen meiden. Beides ginge nicht.
- **Sondern:** den Zeitstempel mitschreiben — **dann ist die spätere Abweichung kein Widerspruch,
  sondern eine Beobachtung über die Zeit.** Zwei Blätter sechs Minuten später in die andere
  Richtung zu ziehen, sieht nach Schwanken aus und ist das Gegenteil: **viermal dieselbe Regel
  angewandt — gegen den Bestand messen, nicht gegen die Erinnerung.**

---

## NACHTRAG · Eine Formel, die niemand rechnet, ist nicht geprüft *(16.08.2026)*

> **Was für einen Test gilt, gilt für eine Formel: was auch ohne sie stimmt, hat sie nicht belegt.**

**Das ist keine neue Regel, sondern die Mutationsprobe — angewandt auf Fakten statt auf Code.**
Dieses Haus erkennt seit Wochen keinen Test an, der auch dann grün ist, wenn man den Code entfernt.
**Derselbe Maßstab hat an der Tür der Formelsammlung haltgemacht, und niemand hat es bemerkt.**

**Die Regel:**

> **Eine Fachaussage gilt erst als geprüft, wenn jemand sie an einem Fall nachgerechnet hat, der
> ohne sie ein anderes Ergebnis hätte.**
> **Wer eine Fachaussage in ein Blatt oder in Code übernimmt, rechnet sie — oder trägt ein, dass er
> es nicht getan hat.**

**Warum keine Prüfstation das leisten kann, und warum eine sechste Rolle die schlechteste Lösung
wäre:** Plan-Prüfer, Evaluator, Release-Prüfer, Generator und Integrator beziehen ihr Fachwissen
aus **derselben Quelle** — der Formelsammlung und dem Regelwerk. **Ein zweiter Leser desselben
Dokuments ist keine zweite Meinung, er ist dieselbe Meinung zweimal.** Ein „Fach-Prüfer" würde
F-004 aufschlagen und dasselbe falsche Vorzeichen lesen wie alle vor ihm.

**Richtigkeit kann nur aus einer Quelle kommen, die außerhalb unserer eigenen Dokumente liegt:**

| | Quelle | Stärke |
|---|---|---|
| **1** | **Rechnen** — eine unabhängige Rechnung aus Grundgrößen, die übereinstimmen **muss**. **Die Arithmetik liest unsere Blätter nicht.** | stärkste Form, **kostet nichts** |
| **2** | **Referenzfall** — ein durchgerechnetes Beispiel aus Norm, Fachbuch, Datenblatt, zertifiziertem Werkzeug | unabhängig, beschaffungsaufwendig |
| **3** | **Primärquelle** — der Normtext selbst, nicht unsere Paraphrase | nötig, wo Haftung dranhängt |

**Alle drei Fachfehler dieser Woche wären mit Nummer 1 gefallen, und Nummer 1 ist gratis:**

```
F-004          Vorzeichen vertauscht        gefunden vom GENERATOR beim Bauen
F-054          prueft Winkel statt Weite    gefunden beim RECHNEN
S-060 / S-040  standen in Spannung          gefunden beim LESEN fuer ein anderes Werkzeug
```

**Keiner von einer Prüfstation — alle drei vom Benutzer.** Und das ist kein Zufall, sondern
Mechanik: **wer eine Aussage aufschreibt, hat sie gerade geglaubt. Wer sie benutzt, braucht ein
Ergebnis** — und ein Ergebnis, das nicht passt, ist der einzige Alarm, der hier nie überhört wurde.

**Deshalb rechnet, wer BENUTZT — nicht, wer aufgeschrieben hat.**

### Die drei Zustände je Fachaussage — und der Zustand trägt den FALL, nicht die Behauptung

**Sachverstand bekommt keine Rolle, sondern ein Feld am Eintrag** — weil der Eintrag der einzige
Ort ist, den jeder Benutzer garantiert öffnet.

| Zustand | Bedeutung | tragfähig für |
|---|---|---|
| **`ABGESCHRIEBEN`** | aus einer Quelle übernommen, **Wortlaut** geprüft, **nie gerechnet** | Doku. **NICHT für einen Bau.** |
| **`NACHGERECHNET`** | jemand hat einen Fall gerechnet, der **ohne die Aussage ein anderes Ergebnis hätte**. **Fall und Sollwert stehen IM EINTRAG** | Bau |
| **`GEGENGEPRUEFT`** | zusätzlich gegen eine **äußere** Quelle gehalten (Normbeispiel, Referenzwerkzeug) | Aussagen, die **nach außen** wirken |

**Das Feld enthält nicht den Satz „wurde nachgerechnet", sondern den Fall:**

```yaml
nachgerechnet_an:
  eingabe:   Wand 10 m waagrecht, Achse um δ=3° verkippt, Versatz 1000 mm
  erwartet:  <Zahl mit Einheit>
  gerechnet: 14.08. · Planner · weicht ohne die Formel um <Zahl> ab
```

**Damit ist die Nachrechnung kein einmaliger Akt, sondern ein wiederholbarer Fall** — der nächste
Benutzer rechnet nicht neu, er **lässt laufen**. **Aus Einträgen mit Fällen wird eine Prüfsuite für
Fachwissen, genau wie die Testsuite eine für Code ist.** Erst damit ist Sachverstand eine **Zahl**
statt eines Gefühls; heute ist er beides nicht.

**Die Ampel misst etwas anderes als Richtigkeit.** Sie sagt *ausgeführt / nicht ausgeführt* — **bei
F-004 stand sie auf grün, während das Vorzeichen falsch war.** Ein Eintrag **ohne** Ampel ist nicht
„vermutlich in Ordnung", sondern **unbekannt**; dieselbe Bauform wie *„fehlt die Marke, ist der
Modulstand unbekannt — nicht etwa gültig."*

### Der Plan-Prüfer verweigert — er weiß es nicht besser, er lässt es nicht durch

**Das ist die entscheidende Trennung, und sie liegt vollständig in seiner heutigen Kompetenz.**
Er prüft Belege; **„nachgerechnet" ist ein Beleg wie jeder andere.**

```
IM DoR-SCHRITT, mechanisch pruefbar:

  Nennt das Blatt eine Fachaussage (F-/N-/S-Kennung)?
    -> ja: traegt der Eintrag `nachgerechnet_an`?
         -> ja:   DoR frei
         -> nein: DoR NUR DANN frei, wenn das NACHRECHNEN
                  ein KRITERIUM DESSELBEN BLATTES ist.

  Er entscheidet NICHT, ob die Aussage stimmt.
  Er entscheidet, ob jemand es geprueft hat.
```

**Damit kostet die Regel keine Fachkompetenz, sondern einen `grep`** — und sie kann nicht vergessen
werden, weil sie an derselben Stelle sitzt wie die Innenprüfungen aus A-39.

### Die Grenze nach außen — ENTSCHIEDEN von Yama am 16.08. als TEST, nicht als Eigenschaftswort

**Ein Adjektiv hätte zwei Lesarten und verlöre seinen Geltungsbereich beim ersten Weitertragen** —
wie „keine Modulkopie ins Repo". **Deshalb drei Fragen, jede in Sekunden am Blatt beantwortbar und
keine verlangt Fachwissen:**

```
EINE FACHAUSSAGE WIRKT NACH AUSSEN, wenn EINE der drei Fragen JA ist:

  1  NORMBEZUG   Wird das Ergebnis mit einer Normkennung verbunden
                 (DIN, EN, VDI, DIN EN …) oder als normkonform bezeichnet?

  2  DRITTER     Verlaesst das Ergebnis das Haus — Angebot, Nachweis, Plan,
                 Bericht, Ausdruck, alles was ein Kunde oder Amt bekommt?

  3  BEMESSUNG   Legt das Ergebnis eine GEBAUTE Groesse fest — Querschnitt,
                 Tragfaehigkeit, Entwaesserung, Abstand, Lastannahme?

  Dreimal NEIN -> NACHGERECHNET reicht fuer gruen.
  Einmal JA    -> gruen NUR mit Primaerquelle (GEGENGEPRUEFT).
                  Ohne sie bleibt der Eintrag GELB — nicht rot, nicht
                  gesperrt: GELB.
```

**Der Plan-Prüfer prüft damit nicht, ob die Aussage stimmt — er prüft, ob sie eine Primärquelle
braucht.** Derselbe Zuschnitt wie oben: **eine Frage und ein `grep`, keine Fachfrage.**

**Gelb ist ein Zustand, keine Sperre — und trägt ein Pflichtfeld:**

```yaml
geltungsbereich: "Nachgerechnet, nicht gegen die Norm gehalten.
                  Verwendbar fuer <…>. NICHT verwendbar als Nachweis,
                  nicht in Unterlagen fuer Dritte, nicht als
                  Bemessungsgrundlage."
```

**Das ist N-003 wörtlich, an einer zweiten Stelle angewandt.** Der Eintrag trägt seit dem 12.08.
`🟡 FACH-GATE`, einen von Yama festgelegten **Geltungsbereich** und die **DAUERGELB**-Kennzeichnung.
**Die Bauform existiert und hat sich bewährt — sie bekommt ein zweites Bauteil, keine neue
Erfindung.**

**`GEGENGEPRUEFT` darf niemand aus eigener Beurteilung setzen.** Der Zustand entsteht nur mit einer
Fundstelle:

```yaml
gegengeprueft_an: "Norm/Quelle mit Ausgabe und Jahr · Abschnitt oder
                   Beispielnummer · das dort genannte Ergebnis"
```

**Damit ist auch das kein Urteil, sondern eine Belegprüfung — von jeder Rolle verweigerbar, von
keiner erfindbar.** Wer eine Norm nicht in der Hand hat, kann den Zustand nicht setzen, und das
ist der Punkt.

**Die drei offenen Fälle, damit die Regel sofort trägt:**

| | drei Fragen | Folge |
|---|---|---|
| **W-28** Rinnenbemessung DIN 1986-100 | **1 ja · 2 ja · 3 ja** | bleibt **gelb**, bis die Norm vorliegt. **„Vertagen" ist damit eine Ableitung, keine Meinung.** |
| **F-004** Schnittpunkt zweier Geraden | nein · nein · nein | **nachgerechnet reicht für grün** — sobald der Fall im Eintrag steht |
| **S-007…S-009** Sonnenbahn, Azimut | nein · nein · nein | nachgerechnet reicht. **Die Rechnung ist längst gemacht** (51°N, 21.06./21.12., selbst gerechnet) — **es fehlt nur der Ort, an dem sie stehen bleibt.** |

Rinnenbemessung nach DIN 1986-100: dann ist „vertagen" eine Ableitung statt eines Bauchgefühls.)*

**Und die Auslösung ist die Benutzung, nicht die Inventur.** Wer alle Einträge auf einmal
nachrechnen lässt, schafft ein Vorhaben, das niemand macht — und trifft auch die, die nie jemand
benutzt. **Ein Eintrag, den nie jemand benutzt, ist in seiner Richtigkeit auch nie eine Gefahr.**
Die Regel skaliert sich selbst.

---

## Nachtrag vom 16.08. — Der Zustandswechsel IST der Commit

**Yamas §1-Entscheidung zu `e521bd98`.** `docs/STATUS.md` wird ab sofort **erzeugt, nicht
geschrieben**. Anlass: die Statuswahrheit lag in **sechs** Fassungen vor, ein Auftrag (`A-33`)
trug **fünf** verschiedene Zustände über den gesamten Lebenszyklus, und der Integrationszweig war
**86 Commits** hinter dem jüngsten Stand.

> **Damit fällt die Frage „wer darf schreiben" weg, statt beantwortet zu werden** — derselbe
> Griff wie bei den getrennten Worktrees: der Fall kann nicht mehr entstehen.

### Der Wortlaut

Eine Rolle meldet einen Zustandswechsel **als Commit-Betreff**, in genau dieser Form:

```
<rolle>: zustand: <KENNUNG> · <ZUSTAND> · <rolle> · <beleg-sha>

Beispiel:  generator: zustand: A-33 · CODE_FERTIG · generator · bau 3e22e61b

  WER   = git-Autor        — nicht aus Prosa
  WANN  = git-Zeitstempel  — nicht aus Prosa
  WAS   = Kennung + Zustand + Beleg-SHA
  WO    = im eigenen Rollenzweig, sonst nirgends
```

**Maschinell prüfbar** — der Betreff muss diesem Muster genügen:

```
^(?:[a-z-]+(?:-[0-9]+)?:\s+)?zustand:\s+[A-Z0-9-]+\s+·\s+[A-Z_]+\s+·\s+[a-z-]+(?:-[0-9]+)?\s+·\s+
```

**Die vorangestellte Rollenmarke ist Pflicht, nicht Zierde — und der Grund ist gemessen:**
`commit-pruefen.sh:73` liest jedes Präfix der Form `wort: ` als Rollenmarke. Ein Betreff, der mit
`zustand:` **beginnt**, wird als Rollenwiderspruch abgewiesen (`exit 2`); ohne jede Marke stellt
Zeile 84 `"$ROLLE: "` voran. **Beide Wege sind zu.** Deshalb steht die Rolle zweimal: einmal für
das Tor aus der Umgebung, einmal als Inhalt aus dem Text. *Zwei Leser, zwei Quellen — und deshalb
keine zweite Wahrheit.*

### Was daraus folgt

- **Niemand bearbeitet `docs/STATUS.md` von Hand.** Der **Integrator** lässt
  `scripts/status-erzeugen.sh` laufen und schreibt die Tafel daraus.
- **Je Kennung gewinnt der jüngste Zustands-Commit.** Merges zählen nicht (`--no-merges`) — sonst
  gäbe ein Transport einem alten Zustand eine neue Zeit und könnte einen neueren verdrängen.
- **Widerspruch bei gleicher Zeit wird gemeldet, nicht aufgelöst.**
- **Solange kein Integrator läuft, wird nur gemeldet, nicht geschrieben.** Ein Weg kommt vor
  seiner Sperre; A-37 Teil 2 sichert danach eine offene Tür, statt eine zu ersetzen.

### Übergangszeit

**Bis der erste Integrationslauf gefahren ist, gilt der Sechs-Zweige-Blick:** wer eine Kennung
prüft oder einen Auftrag zieht, misst über **alle sechs Zweige** und nicht gegen `HEAD` — `HEAD`
ist nachweislich nicht der jüngste Stand.
