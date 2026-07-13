# Systemoptimierung — Fahrplan und Pruefregeln

**Stand:** 2026-07-11  
**Zweck:** Dieses Dokument legt fest, wie Claude Code bestehende Methoden, Services, Workflows und Automatisierungen prueft, bewertet, optimiert und erst danach umsetzt.

Keine groessere Optimierung startet direkt mit Code. Zuerst wird das Gesamtsystem nach `docs/system-kapitelplan.md` in Kapitel/Domaenen aufgeteilt. Danach wird jede Domaene kapitelweise bearbeitet und endet mit einem klaren Abnahmepunkt.

---

## 1. Grundregel

Die verbindliche Reihenfolge lautet:

```text
1. Inventur
2. kritische Bewertung
3. Optimierungsvorschlag
4. Ziel-Fahrplan
5. Verknuepfung vorhandener Bausteine
6. Workflow festlegen
7. Automatisierung bestimmen
8. Umsetzung in kleinen Paketen
9. Pruefung / Gegenbeweis / Abnahme
```

Keine Stufe darf uebersprungen werden. Automatisierung kommt erst, wenn Konzept, Workflow und Verknuepfung fachlich geklaert sind.

---

## 2. Kapitelstruktur je Domaene

Vor jeder Domaenenarbeit steht die Systemeinordnung:

```text
0. Gesamtsystem in Kapitel einordnen
1. Domaene aus System-Kapitelplan auswaehlen
2. Startblock schreiben
3. Erst dann Inventur/Bewertung/Konzept beginnen
```

Jede Domaene bekommt ein eigenes Kapitel oder Dokument nach diesem Muster:

```text
Kapitel 1: Inventur
Kapitel 2: Bewertung
Kapitel 3: Soll-Konzept
Kapitel 4: Soll-Workflow
Kapitel 5: Verknuepfungsplan
Kapitel 6: Automatisierungsplan
Kapitel 7: Umsetzungspakete
Kapitel 8: Pruefplan und Abnahme
```

Vor jedem Kapitel muss Claude Code zuerst einen **Kapitel-Startblock** schreiben. Ohne diesen Startblock beginnt keine Arbeit.

Pflichtformat:

```text
KAPITEL-START
Domaene:
Kapitel:
Startpunkt:
Warum dieses Kapitel jetzt:
Ziel dieses Kapitels:
Was ich konkret pruefe:
Welche Dateien/Services/Datenquellen ich lese:
Was ich NICHT mache:
Vorgehensweise Schritt fuer Schritt:
Ergebnisdokument:
Stop-Kriterium / Ende dieses Kapitels:
Abnahme durch Yama erforderlich: ja/nein
```

Regel: Fuer jedes Kapitel braucht Yama zuerst Konzept, Analyseumfang und Vorgehensweise. Claude Code darf erst danach in die eigentliche Analyse oder Umsetzung gehen.

Fuehrende Kapitel/Domaenen stehen in:

```text
docs/system-kapitelplan.md
```

Beispiele fuer Domaenen:

- Angebot / Auslegung / Waermepumpe
- Vertrieb / CRM
- Auftrag
- Beschaffung
- Disposition
- Montage / Dokumentation
- Abnahme
- Rechnung / FiBu
- Controlling / Nachkalkulation
- Kundenprofil / Objektprofil

---

## 3. Kapitel 1 — Inventur

Ziel: Claude Code muss zuerst belegen, was schon vorhanden ist.

Vor der Inventur muss Claude Code sagen:

- wo die Inventur beginnt
- welche Suchbegriffe/Dateigruppen er nutzt
- welche Services/Controller/Views/Datenquellen zuerst gelesen werden
- wo die Inventur endet
- welches Inventur-Dokument entsteht

Pflichtfragen:

1. Welche Datenquellen existieren?
2. Welche Models, Tabellen, Migrationen und Seeder existieren?
3. Welche Services existieren?
4. Welche Controller und Routen existieren?
5. Welche Views, Wizards oder Frontends existieren?
6. Welche Tests existieren?
7. Welche Dokumentation existiert?
8. Welche externen oder importierten Daten liegen vor?
9. Welche Bausteine sind produktiv verdrahtet?
10. Welche Bausteine sind vorhanden, aber ohne Aufrufer?

Ergebnis:

```text
docs/<domaene>-inventur.md
```

Die Inventur ist read-only. Keine Umsetzung in dieser Phase.

---

## 4. Kapitel 2 — Kritische Bewertung

Ziel: Nicht nur auflisten, sondern fachlich und technisch bewerten.

Vor der Bewertung muss Claude Code sagen:

- welche Inventur-Ergebnisse bewertet werden
- welche Bewertungskriterien genutzt werden
- welche fachlichen Regeln gelten
- welche technischen Risiken besonders geprueft werden
- welches Bewertungs-Dokument entsteht

Pflichtbewertung je Baustein:

- fachlich korrekt / teilweise korrekt / falsch
- technisch sauber / riskant / Altlast
- fuehrende Wahrheit / doppelte Wahrheit / unklar
- produktiv verdrahtet / isoliert / tot
- wiederverwendbar / anzupassen / zu ersetzen
- Testabdeckung vorhanden / fehlt
- Performance-Risiko vorhanden / unklar / unkritisch
- UX-/Workflow-Bruch vorhanden / nein

Pflichtfragen:

1. Ist die bestehende Methode fachlich richtig?
2. Wo rechnet das System doppelt?
3. Wo gibt es JS-Rechnung statt Backend-Wahrheit?
4. Wo sind wertvolle Services unverdrahtet?
5. Wo fehlen Operanden?
6. Wo wird stillschweigend geraten?
7. Wo muss der Mensch bestaetigen?
8. Wo ist Automatisierung sinnvoll?
9. Wo waere Automatisierung gefaehrlich?
10. Was ist der kleinste sinnvolle Korrekturschnitt?

Ergebnis:

```text
docs/<domaene>-bewertung.md
```

---

## 5. Kapitel 3 — Optimierungsvorschlag / Soll-Konzept

Ziel: Claude Code macht einen konkreten Vorschlag, wie die Domaene fachlich sauber werden soll.

Vor dem Soll-Konzept muss Claude Code sagen:

- welche Ist-Probleme geloest werden sollen
- welche bestehenden Bausteine fuehrend bleiben
- welche Doppelstrukturen neutralisiert werden sollen
- welche offenen Entscheidungen Yama treffen muss
- welches Konzept-Dokument entsteht

Der Vorschlag muss enthalten:

- eine fuehrende Wahrheit je Sachverhalt
- Ziel-Datenfluss
- Ziel-Service-Struktur
- Wiederverwendung vorhandener Bausteine
- zu entfernende oder zu neutralisierende Doppelberechnungen
- fachliche Regeln
- Grenzen der Automatisierung
- offene Entscheidungen fuer Yama

Ergebnis:

```text
docs/<domaene>-soll-konzept.md
```

Ohne Abnahme kein Bau.

---

## 6. Kapitel 4 — Workflow bestimmen

Ziel: Der fachliche Ablauf wird aus Nutzersicht und Systemlogik definiert.

Vor dem Workflow-Kapitel muss Claude Code sagen:

- welcher Nutzerprozess betrachtet wird
- wo der Prozess beginnt
- wo der Prozess endet
- welche Rollen beteiligt sind
- welche Varianten und Fehlerfaelle betrachtet werden
- welches Workflow-Dokument entsteht

Der Workflow muss beantworten:

1. Wo beginnt der Prozess?
2. Welche Eingaben sind Pflicht?
3. Welche Eingaben koennen aus vorhandenen Daten uebernommen werden?
4. Welche Daten fehlen oft und muessen abgefragt werden?
5. Welche Zwischenergebnisse entstehen?
6. Welche Warnungen sind noetig?
7. Welche Entscheidung trifft das System?
8. Welche Entscheidung trifft der Mensch?
9. Wo endet der Prozess?
10. Was ist das naechste Systemobjekt nach Abschluss?

Ergebnis:

```text
docs/<domaene>-workflow.md
```

---

## 7. Kapitel 5 — Verknuepfungsplan

Ziel: Vorhandene Bausteine werden verkettet, statt neu gebaut.

Vor dem Verknuepfungsplan muss Claude Code sagen:

- welche vorhandenen Bausteine verkettet werden
- welche Inputs/Outputs je Baustein relevant sind
- wo Adapter noetig sein koennten
- welche alte Logik abgeloest oder neutralisiert wird
- welches Verknuepfungs-Dokument entsteht

Der Verknuepfungsplan muss enthalten:

- Service A liefert welches Ergebnis?
- Service B braucht welche Eingabe?
- Welche DTOs oder Arrays laufen zwischen den Services?
- Wo gibt es Adapterbedarf?
- Wo muss ein bestehender Controller nur orchestrieren?
- Wo muss Frontend nur anzeigen statt rechnen?
- Wo liegt die eine Backend-Wahrheit?

Ergebnis:

```text
docs/<domaene>-verknuepfungsplan.md
```

---

## 8. Kapitel 6 — Automatisierungsplan

Automatisierung wird erst nach Inventur, Bewertung, Konzept, Workflow und Verknuepfungsplan geplant.

Vor dem Automatisierungsplan muss Claude Code sagen:

- welche Schritte sicher automatisierbar wirken
- welche Schritte nur Vorschlag + Bestaetigung sein duerfen
- welche Schritte nicht automatisiert werden duerfen
- welche Operanden-Gates noetig sind
- welches Automatisierungs-Dokument entsteht

Jede Automatisierung wird in drei Klassen eingeteilt:

- **A — automatisch:** sicher ableitbar, keine Fach-/Rechtsentscheidung.
- **B — Vorschlag + Bestaetigung:** System kann vorschlagen, Mensch entscheidet.
- **C — nicht automatisieren:** zu unsicher, rechtlich/fachlich kritisch oder Operanden fehlen.

Pflichtfragen:

1. Welche Pflichtdaten fehlen?
2. Kann das System fehlende Daten aus vorhandenen Quellen ableiten?
3. Muss es stattdessen eine Aufgabe / Warnung / Rueckfrage erzeugen?
4. Welche Folgeobjekte darf das System automatisch vorbereiten?
5. Welche Folgeobjekte darf es erst nach Bestaetigung erstellen?
6. Welche Fehler- oder Widerspruchsfaelle muessen sichtbar werden?

Ergebnis:

```text
docs/<domaene>-automatisierungsplan.md
```

---

## 9. Kapitel 7 — Umsetzungspakete

Erst jetzt wird gebaut. Umsetzung erfolgt in kleinen Paketen.

Vor jedem Umsetzungspaket muss Claude Code sagen:

- welches abgenommene Konzept/Workflow-Dokument die Grundlage ist
- welche Dateien er voraussichtlich aendert
- welches Verhalten sich aendert
- welche Tests/Pruefungen laufen
- was explizit nicht geaendert wird
- wann das Paket fertig ist

Jedes Paket braucht:

- Ziel
- betroffene Dateien
- Nicht-Ziele
- erwartetes Verhalten
- Tests / Pruefung
- Rollback- oder Sicherheitsueberlegung
- Abnahmekriterium

Pakete muessen so klein sein, dass sie einzeln bewertet und committed werden koennen.

---

## 10. Kapitel 8 — Pruefung und Abnahme

Jede Umsetzung braucht eine Pruefung.

Vor der Pruefung muss Claude Code sagen:

- was genau geprueft wird
- welche Tests laufen
- welcher Gegenbeweis sinnvoll ist
- welche Regressionen besonders ausgeschlossen werden muessen
- welches Abnahmeergebnis erwartet wird

Pflichtpruefung:

- bestehende Tests bleiben gruen
- neue Logik hat mindestens einen realistischen Test oder belegte manuelle Pruefung
- Gegenbeweis, wenn moeglich
- keine zweite Wahrheit
- keine JS-Hauptberechnung, wenn Backend-Service existiert
- keine stillen Annahmen bei fehlenden Operanden
- keine unbewusste Aenderung an Bestandsdaten
- Browser-/Screenshot-Pruefung bei UI/Wizard/Layout

Abschluss:

```text
Fertig ist ein Kapitel erst, wenn Ergebnisdokument, Pruefung und Yama-Abnahme vorliegen.
```

---

## 11. Startpunkt und Ende

Claude Code muss zu Beginn jeder Domaene sagen:

```text
Ich starte bei Kapitel X.
Mein Ziel fuer diese Runde ist Y.
Ich beende diese Runde bei Z.
Ich baue noch nichts / oder ich baue nur Paket N nach Abnahme.
```

Claude Code muss zu Beginn jedes Kapitels zusaetzlich den vollstaendigen **KAPITEL-START** aus Abschnitt 2 ausfuellen.

Claude Code muss am Ende jeder Runde sagen:

```text
Erledigt:
Offen:
Naechster Schritt:
Blocker:
Abnahme erforderlich:
```

Damit ist jederzeit klar, woran gearbeitet wird, wann ein Abschnitt fertig ist und was danach kommt.

---

## 12. Aktueller Start

Aktuelle Startdomaene:

```text
Gesamtsystem ticket
```

Aktueller Startpunkt:

```text
Kapitel A: Systemlandkarte / Querschnitt aus docs/system-kapitelplan.md
```

Aktuelles Ende dieser Runde:

```text
docs/system-inventur.md
```

Danach folgt erst:

```text
Yama entscheidet, welches Fachkapitel tief bearbeitet wird.
```

Einzelthemen wie WP-Auslegung werden erst danach als Unterkapitel eingeordnet und nach derselben Methode bearbeitet.
