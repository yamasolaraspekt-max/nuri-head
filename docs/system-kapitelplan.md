# System-Kapitelplan — ticket

**Stand:** 2026-07-11  
**Zweck:** Bevor einzelne Module optimiert werden, wird das gesamte vorhandene System in fachliche Kapitel aufgeteilt. Erst danach beginnt je Kapitel die Inventur, Bewertung, Konzeption, Verknuepfung, Workflow-Definition und Automatisierung.

Dieses Dokument ist die Landkarte. `docs/systemoptimierung-fahrplan.md` ist die Methode je Kapitel.

---

## 1. Grundsatz

Wir betrachten nicht isoliert "CRM" oder "ERP" als Schlagwort. Wir betrachten das vorhandene ticket-System als zusammenhaengenden Betriebsprozess:

```text
Eingang / Kunde / Objekt
-> Qualifizierung
-> Beratung / Auslegung / Angebot
-> Auftrag
-> Beschaffung / Material / Ressourcen
-> Planung / Disposition
-> Montage / Ausfuehrung
-> Dokumentation / Abnahme
-> Rechnung / Zahlung / FiBu
-> Nachkalkulation / Controlling
-> Service / Bestand / Wiederkehr
```

Jedes Kapitel wird zuerst verstanden, dann bewertet, dann optimiert. Keine Einzelbaustelle wird vorgezogen, ohne ihre Rolle im Gesamtsystem zu kennen.

---

## 2. Hauptkapitel des Systems

### Kapitel A — Systemlandkarte / Querschnitt

Ziel: Erst verstehen, welche Module, Datenquellen, Rollen, Rechte, Navigationen und Prozessketten existieren.

Zu inventarisieren:

- Routen, Controller-Gruppen, Models, Tabellen
- Navigation / Menues / Einstiegspunkte
- Rollen, Rechte, Gates, Policies
- zentrale Datenwahrheiten
- Status-/Phasenlogiken
- bestehende Automatisierungen
- bestehende Audits und Fahrplaene

Ergebnis:

```text
docs/system-inventur.md
docs/system-kapitelbewertung.md
```

### Kapitel B — Eingang / Lead / Kunde / Objekt

Umfasst:

- Leads, Kunden, Objekt-/Adressdaten
- Formulare, Importquellen, Qualifizierung
- Kundenprofil und Objektprofil
- fehlende Pflichtdaten und Datenqualitaet

### Kapitel C — Vertrieb / CRM-Prozess

Umfasst:

- Pipeline / Kanban / Stages
- Aufgaben, Follow-ups, Wiedervorlagen
- Kontaktverlauf, Termine, Kommunikation
- Arbeitsliste / Inbox

### Kapitel D — Angebot / Auslegung / Konfiguration

Umfasst:

- Angebotsordner, OfferDetails, Templates, Sets
- Produkt-/Katalog-Auswahl
- Auslegungen wie WP, PV, Heizkoerper, Energie
- Kalkulation und Angebotsuebernahme

WP-Auslegung ist ein Unterkapitel von D, nicht der Start des Gesamtsystems.

### Kapitel E — Auftrag / Deal / Vertragsuebergang

Umfasst:

- Angebotsannahme
- Deal/Auftrag
- Auftragsstatus
- Uebergang zu Rechnung, Beschaffung, Planung

### Kapitel F — Produkte / Katalog / Sets / Preise

Umfasst:

- Artikel, Master-Sets, Komponenten
- Lieferanten, Einkaufspreise, Verkaufspreise
- technische Produktdaten
- keine zweite Katalog-Wahrheit

### Kapitel G — Beschaffung / Lager / Material

Umfasst:

- Bestelllisten
- Lieferantenprozesse
- Lager, Inventar, Wareneingang
- Materialverfuegbarkeit fuer Montage

### Kapitel H — Planung / Disposition / Ressourcen

Umfasst:

- Termine, Planner, Mitarbeiter, Teams
- Kapazitaeten, Qualifikationen, Fahrzeuge/Werkzeuge
- Rueckwaerts-/Vorwaertsplanung
- Konflikte und Engpaesse

### Kapitel I — Montage / Ausfuehrung / Tagesberichte

Umfasst:

- Montageablaeufe
- Checklisten
- Tagesberichte
- Fotos, Notizen, Rueckmeldungen
- offene Punkte aus der Baustelle

### Kapitel J — Dokumentation / Abnahme / Nachweise

Umfasst:

- Kundendokumentation
- Abnahmeprotokolle
- technische Nachweise
- Foerder-/Norm-/Pflichtdokumente
- Uebergabe an Kunde/Buero

### Kapitel K — Rechnung / Zahlung / FiBu

Umfasst:

- Rechnungen, Teilrechnungen, Gutschriften, Storno
- Zahlungsstatus
- Buchungssaetze / FiBu-Anbindung
- Umsatzdefinition
- Mahnung / offene Posten

### Kapitel L — Controlling / Nachkalkulation / Auswertung

Umfasst:

- Soll/Ist-Kosten
- Projektmargen
- Material-/Zeitabweichungen
- Prozess-KPIs
- Entscheidungsgrundlagen fuer Optimierung

### Kapitel M — Service / Betrieb / Reklamation / Wiederkehr

Umfasst:

- Servicefaelle
- Reklamationen
- Wartung
- Bestandskunden
- Folgeangebote

### Kapitel N — Querschnitt: Architektur / Sicherheit / Performance / UX

Umfasst:

- Sicherheit, Berechtigungen, Datenschutz
- Performance, Indizes, N+1
- UI/UX, Layout, Navigation
- technische Schulden
- Tests und QA

Dieses Kapitel begleitet alle anderen Kapitel als Pruefbrille.

---

## 3. Reihenfolge des Starts

Vor einer fachlichen Einzeldomaene kommt immer:

```text
Kapitel A — Systemlandkarte / Querschnitt
```

Erst wenn Kapitel A eine grobe Systemkarte liefert, entscheidet Yama, welches Fachkapitel als erstes tief bearbeitet wird.

Empfohlene naechste Reihenfolge nach Kapitel A:

```text
1. Kapitel B — Eingang / Lead / Kunde / Objekt
2. Kapitel C — Vertrieb / CRM-Prozess
3. Kapitel D — Angebot / Auslegung / Konfiguration
4. Kapitel E — Auftrag / Deal / Vertragsuebergang
5. Kapitel K — Rechnung / Zahlung / FiBu
```

Danach werden Beschaffung, Disposition, Montage, Abnahme, Controlling und Service in der realen Prozessreihenfolge bewertet.

---

## 4. Startblock fuer das Gesamtsystem

Claude Code muss vor Beginn der Systemarbeit zuerst diesen Startblock ausfuellen:

```text
KAPITEL-START
Domaene: Gesamtsystem ticket
Kapitel: A — Systemlandkarte / Querschnitt
Startpunkt: Repository-Struktur, Routen, Controller, Models, Datenbank, Navigation, bestehende Docs
Warum dieses Kapitel jetzt: Erst System in Kapitel/Domaenen aufteilen, bevor einzelne Module optimiert werden.
Ziel dieses Kapitels: belegte Systemkarte mit Hauptmodulen, Datenwahrheiten, Prozessketten, offenen Bruechen und Prioritaeten.
Was ich konkret pruefe:
Welche Dateien/Services/Datenquellen ich lese:
Was ich NICHT mache: keine Umsetzung, keine Moduloptimierung, keine Automatisierung.
Vorgehensweise Schritt fuer Schritt:
Ergebnisdokument: docs/system-inventur.md
Stop-Kriterium / Ende dieses Kapitels: Systemkarte liegt vor und Yama entscheidet das erste Tiefenkapitel.
Abnahme durch Yama erforderlich: ja
```

---

## 5. Regel fuer Einzelthemen

Wenn ein Einzelthema auftaucht, z. B. WP-Auslegung, Arbeitsliste, Rechnung oder Kundenprofil, muss Claude Code es zuerst einem Systemkapitel zuordnen:

```text
Einzelthema:
Systemkapitel:
Ist es aktueller Fokus?
Ist es Blocker?
Muss zuerst Kapitel A aktualisiert werden?
```

Ohne diese Einordnung wird nicht gebaut.
