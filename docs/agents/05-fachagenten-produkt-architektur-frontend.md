# 05 — Fachagenten: Frontend Design · Konzeption · Workflow · Architektur

> **Zweck:** Diese Fachagenten ergänzen den Drei-Rollen-Zyklus. Sie sind keine Ersatzrollen für Planner/Generator/Evaluator, sondern Pflicht-Perspektiven, die je nach Auftrag im Planner und Evaluator mitlaufen.
> **Geltung:** Besonders für neue Module, Wizards, Energie-/Auslegungs-Tools, CRM/ERP-Prozessflächen, große Layout-Probleme und Architekturentscheidungen.

---

## 1. Aktivierungsregel

Diese Fachagenten werden automatisch berücksichtigt, wenn Yama Begriffe oder Ziele nennt wie:

- Frontend
- Design
- Layout
- UX
- Workflow
- Konzeption
- Gesamtkonzept
- Architektur
- Wizard
- App
- Modul
- Benutzerführung
- Prozess
- „Layout ist katastrophe“
- „alles untersuchen“
- „was können wir nutzen“

Wenn mehrere betroffen sind, laufen sie in dieser Reihenfolge:

```text
Konzeption-Agent -> Workflow-Agent -> Architektur-Agent -> Frontend-Design-Agent
```

Danach folgt die normale Umsetzung über:

```text
Planner -> Generator -> Evaluator
```

In der Praxis bedeutet das:

- Der **Planner** muss diese Fachagenten-Perspektiven in seinem Konzept abarbeiten.
- Der **Generator** baut nach diesen Vorgaben.
- Der **Evaluator** prüft explizit gegen diese Vorgaben.

---

## 2. Konzeption-Agent

### Aufgabe

Der Konzeption-Agent macht aus einem diffusen Wunsch ein tragfähiges Produktkonzept.

Er beantwortet vor Umsetzung:

1. Welches reale Problem löst die Funktion?
2. Wer nutzt sie?
3. In welchem betrieblichen Moment wird sie genutzt?
4. Welche Eingaben sind wirklich nötig?
5. Welche Entscheidungen darf das System automatisch treffen?
6. Welche Entscheidungen müssen Mensch/Fachkraft bestätigen?
7. Was ist der minimale nützliche Umfang?
8. Was gehört ausdrücklich nicht in die erste Umsetzung?
9. Welche bestehenden Ticket-Module/Daten/Services können genutzt werden?
10. Was ist das objektive Fertig-Kriterium?

### Ausgabe

Der Konzeption-Agent liefert:

- Zielbild in 5-10 Sätzen
- Nutzerrollen
- Kernfälle
- Nicht-Ziele
- fachliche Annahmen
- Risiken
- offene Fragen
- Entscheidung: bauen / erst untersuchen / nicht bauen

### Harte Regeln

- Kein UI bauen, bevor das Zielbild klar ist.
- Kein Modul erfinden, wenn vorhandene Ticket-Strukturen reichen.
- Keine Automatisierung von Fach-/Rechtsentscheidungen ohne Bestätigung.
- Kein „schönes Layout“ über falscher Fachstruktur.

---

## 3. Workflow-Agent

### Aufgabe

Der Workflow-Agent denkt nicht in Seiten, sondern in Arbeitsabläufen.

Er modelliert:

```text
Start -> Eingabe -> Prüfung -> Entscheidung -> Ergebnis -> Übernahme -> Nacharbeit
```

Für CRM/ERP gilt immer die Kette:

```text
Anfrage -> Angebot -> Auftrag -> Ausführung/Montage -> Abnahme -> Rechnung
```

### Prüffragen

1. Wo startet der Benutzer?
2. Welche Daten liegen bereits vor?
3. Welche Daten fehlen?
4. Welche Eingabe kann aus Bestand vorausgefüllt werden?
5. Wo muss validiert werden?
6. Was passiert bei unvollständigen Daten?
7. Was ist der nächste natürliche Schritt?
8. Welche Daten werden dauerhaft gespeichert?
9. Welche Daten sind nur temporäre Berechnung?
10. Wo dockt das Ergebnis in Angebot/Auftrag/Rechnung an?

### Ausgabe

Der Workflow-Agent liefert:

- Ablaufdiagramm in Textform
- Hauptpfad
- Nebenpfade/Fehlerfälle
- Speicherpunkte
- Übergabepunkte an andere Module
- Liste der UI-Schritte
- Liste der Backend-Aktionen

### Harte Regeln

- Kein Medienbruch.
- Keine doppelte Eingabe, wenn Daten vorhanden sind.
- Keine Sackgasse ohne nächsten Schritt.
- Keine versteckten Speicherungen.
- Keine Vermischung von Erfassung, Berechnung, Produktwahl und Kalkulation ohne klare Trennung.

---

## 4. Architektur-Agent

### Aufgabe

Der Architektur-Agent schützt die Struktur des Systems.

Er prüft:

1. Welche Domäne ist zuständig?
2. Welche Tabelle ist die führende Wahrheit?
3. Welche Services existieren schon?
4. Welche Controller/View-Strukturen existieren schon?
5. Welche neue Naht ist wirklich nötig?
6. Gibt es FK-/Index-/Transaktionsbedarf?
7. Wird Berechnung im richtigen Layer ausgeführt?
8. Entsteht eine zweite Wahrheit?
9. Gibt es Migrationsrisiko?
10. Gibt es Security-/DSGVO-Risiko?

### Ausgabe

Der Architektur-Agent liefert:

- Modulgrenzen
- Datenfluss
- Tabellen/Models
- Controller/Routes
- Services
- Validierung
- Persistenz
- Tests
- Risiken
- Entscheidung zu Additivität/Strangler/Refactor

### Harte Regeln

- Geschäftslogik in Services oder Model-Hooks, nicht in Blade.
- Frontend sammelt und zeigt an, Backend entscheidet/rechnet.
- Controller bleiben dünn.
- Neue Schreibpfade: `auth`, Berechtigung, Servervalidierung.
- Mehrtabellen-Schreiben in Transaktion.
- Keine neue Tabelle ohne Domänen-Heimat.
- Keine zweite Berechnungslogik.

---

## 5. Frontend-Design-Agent

### Aufgabe

Der Frontend-Design-Agent macht aus dem Konzept eine bedienbare Oberfläche.

Er ist zuständig für:

- Informationsarchitektur
- visuelle Hierarchie
- Formulare
- Wizard-Schritte
- Tabellen/Listen
- Zustände
- Fehleranzeigen
- Ergebnisdarstellung
- responsive Layouts
- Browser-Prüfung

### Design-Grundsätze für ticket

Ticket ist ein CRM/ERP-Arbeitssystem. Deshalb:

- ruhig
- kompakt
- arbeitsorientiert
- scanbar
- wiederholbar bedienbar
- keine Marketing-Hero-Flächen
- keine dekorative Kartenschlacht
- keine verschachtelten Karten
- keine übergroßen Überschriften in Tool-Flächen
- keine Texte, die erklären, was die UI offensichtlich macht

### Layout-Regeln

1. Erste Bildschirmhöhe muss die Arbeitsfläche zeigen, nicht nur Erklärung.
2. Navigation/Schritte müssen stabil bleiben.
3. Eingaben gruppieren nach Arbeitslogik, nicht nach Zufall im Code.
4. Kritische Werte immer sichtbar: Status, Pflichtdaten, Ergebnis, Warnungen.
5. Ergebniswerte mit Einheit anzeigen.
6. Fehler nahe am Feld.
7. Lange Formulare in sinnvolle Abschnitte.
8. Buttons klar nach Handlung:
   - primär: nächster fachlicher Schritt
   - sekundär: speichern, zurück, prüfen
   - destruktiv: klar getrennt
9. Responsive Layout ohne Überlappungen.
10. Browser-Screenshot-Prüfung bei größeren UI-Änderungen.

### Wizard-Regeln

Ein Wizard braucht:

- klaren Fortschritt
- kurze Schritt-Namen
- pro Schritt ein Ziel
- keine versteckten Pflichtfelder
- Zwischenspeichern oder klaren Verlusthinweis
- Zusammenfassung vor finaler Übernahme
- Ergebnis nicht nur als Zahl, sondern mit Begründung
- technische Warnungen klar sichtbar
- Rücksprung ohne Datenverlust

### Ausgabe

Der Frontend-Design-Agent liefert:

- Seitenstruktur
- Komponentenliste
- Schrittfolge
- Priorität der Informationen
- responsive Verhalten
- Fehler-/Leer-/Ladezustände
- Prüfkriterien für Browser-Test

---

## 6. Zusammenspiel bei der Wärmepumpen-Auslegung

Bei der Wärmepumpen-Auslegung müssen alle vier Fachagenten laufen:

### Konzeption

- Was soll die WP-App fachlich leisten?
- Geht es um Beratung, Vorplanung, Angebotserstellung oder finale Auslegung?
- Welche Werte sind verbindlich, welche nur Vorschlag?

### Workflow

- Bestand aus Angebot/Kunde/Objekt übernehmen.
- Fehlende Daten erfassen.
- Heizlast/Verbrauch prüfen.
- WP-Kandidaten bewerten.
- Ergebnis erklären.
- Produkt-/Set-/Kalkulationsübernahme vorbereiten.

### Architektur

- Keine JS-Hauptberechnung.
- Backend-Orchestrierung über vorhandene Services.
- Produktkatalog über `CatalogDeviceRepository`.
- Ergebnis als berechneter Snapshot oder Angebotsdetail sauber speichern.

### Frontend Design

- Kompakter Fachwizard.
- Linke Schrittführung oder obere stabile Progress-Leiste.
- Rechts/oben Ergebnis-Zusammenfassung.
- Eingabe, Prüfung, Ergebnis, Produktwahl, Kalkulation klar trennen.
- Warnungen bei schlechter Datenqualität, hoher Vorlauftemperatur, zu kleiner WP, hohem Heizstabanteil.

---

## 7. Evaluator-Pflicht bei UI-/Workflow-Aufgaben

Der Evaluator prüft zusätzlich:

- Gibt es ein klares Ziel pro Screen?
- Sind Pflichtdaten sichtbar?
- Ist die Bedienung auf Desktop und Mobil nutzbar?
- Gibt es überlappenden Text?
- Gibt es unklare Buttons?
- Sind Ergebnisse fachlich erklärt?
- Gibt es Lade-/Fehler-/Leerzustände?
- Wurde im Browser geprüft, falls UI geändert wurde?
- Wurde keine neue fachliche Frontend-Parallelberechnung eingeführt?

Bei UI-Aufgaben ohne Browser-Prüfung ist die Evaluator-Antwort maximal `NACHBESSERN`, außer es handelt sich nur um reine Text-/Dokumentationsänderungen.

---

## 8. Kurzprompt für Yama

Wenn Yama genau diese Fachagenten will:

```text
Lies CLAUDE.md, docs/agents/04-claude-code-startanweisung.md und docs/agents/05-fachagenten-produkt-architektur-frontend.md.
Arbeite mit Konzeption-Agent, Workflow-Agent, Architektur-Agent und Frontend-Design-Agent.
Danach nutze Planner -> Generator -> Evaluator.
Bei UI-Aufgaben immer Browser-/Screenshot-Prüfung einplanen.
```
