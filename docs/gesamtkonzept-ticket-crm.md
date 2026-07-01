# Gesamtkonzept ticket CRM — das strukturierte Bild des ganzen Systems

**Was dieses Dokument ist:** Das eine, geordnete Bild des gesamten CRM — alle Bereiche als Kapitel, mit Inhaltsverzeichnis. Es führt die früheren Kern-Landkarten (Zielbild, Workflow, Kundenprofil, Begriffe …) mit der neuen 8-Zonen-Gesamtinventur zusammen.

**Wichtig — zwei getrennte Ordnungen:**
- **Dieses Konzept ordnet nach THEMA** (wie ist das System aufgebaut) — es ist das *Buch*, zum Nachschlagen.
- **Der Fahrplan (`fahrplan-ticket-crm.md`) ordnet nach DRINGLICHKEIT** (in welcher Reihenfolge fassen wir es an) — das ist die *Route*.
- Wir arbeiten die Kapitel **NICHT** in Nummern-Reihenfolge ab, sondern in Fahrplan-Reihenfolge. Die Kapitelnummer ist nur zum Wiederfinden.

**Stand:** Juli 2026. Quellen: 8-Zonen-Inventur (`crm-inventur-00-index.md` + 01–08) und die Kern-Landkarten.

---

## Inhaltsverzeichnis

**Teil I — Grundlagen (schon erarbeitet)**
- Kap. 0 — Vision & Prinzipien
- Kap. 1 — Begriffe / Glossar (verbindlich)
- Kap. 2 — Architektur-Weichen (Entscheidungen)

**Teil II — Der Kernprozess (schon kartiert)**
- Kap. 3 — Kunde / Objekt / Gewerk (Hierarchie)
- Kap. 4 — Workflow Lead→Angebot→Auftrag→Rechnung
- Kap. 5 — Das Kundenprofil (Oberfläche)

**Teil III — Vertrieb & Dokumente**
- Kap. 6 — Angebots- & Set-Konfiguration
- Kap. 7 — Auftrag, Auftragsbestätigung, Dokumente/PDF

**Teil IV — Waren & Material**
- Kap. 8 — Artikel / Produktkatalog
- Kap. 9 — Lager / Inventur / Beschaffung / Großhandel

**Teil V — Organisation & Ausführung**
- Kap. 10 — Abteilungen / Organisation / HR
- Kap. 11 — Projektmanagement / Planer / Aufgaben / Tickets / Assets
- Kap. 12 — Kalender / Termine

**Teil VI — Querschnitt & Auswertung**
- Kap. 13 — Dokumente / Medien / Kommunikation / KI
- Kap. 14 — Cockpit / Controlling / Auswertungen
- Kap. 15 — Mobile / API (Nuriva)

**Teil VII — Altlasten**
- Kap. 16 — Legacy / Old Code (toter Ballast)

**Anhang**
- A — Fahrplan (Abarbeitungs-Reihenfolge)
- B — Offene Detail-Inventur-Lücken
- C — Zonen-übergreifende Kernbefunde

---

## Einleitung

ticket ist ein über Jahre gewachsenes, produktives CRM/ERP für einen gewerkeübergreifenden Sanierungsbetrieb. Es ist deutlich größer als sein Kernprozess: Neben Lead→Angebot→Auftrag→Rechnung trägt es Warenwirtschaft, Beschaffung mit Großhandels-Schnittstellen, Projekt-/Montageplanung, HR, Dokumente, eine Mobile-Anbindung (Nuriva) und mehr.

Das System ist **funktional reich, aber strukturell uneinheitlich**: An mehreren Stellen existieren *konkurrierende Systeme für dieselbe Sache* (Status, Phasen, Rechnungen, Objekt-/Projekt-Begriff), und es trägt erheblichen **toten Ballast** (~58.500 Zeilen Legacy). Der rote Faden aller bisherigen Arbeit: **erst verstehen, dann Begriffe/Weichen klären, dann behutsam bauen** — nie umgekehrt.

Dieses Konzept gibt dem Ganzen eine Kapitelstruktur. Abgearbeitet wird entlang des Fahrplans (Anhang A), nicht in Kapitelnummern-Reihenfolge.

---

## Teil I — Grundlagen

### Kap. 0 — Vision & Prinzipien
Objekt-zentriertes, gewerkeübergreifendes Komplettsanierungs-CRM. Drei-Schichten-Architektur (gemeinsames Gerüst / Gewerks-Fachmodule / Cross-Gewerk-Intelligenz). → `zielbild-objekt-zentriertes-crm.md`, `workflow-sollkonzept.md`. **Status: erarbeitet.**

### Kap. 1 — Begriffe / Glossar
Verbindlich: Kunde=`new_leads`, Objekt=`lead_alternative_adds`/`alternative_id`, Gewerk=`lead_product_lists`, Angebot=`offers`, Auftrag=`deals`. Kein physischer Rename (Alias-Weg). → `glossar.md`. **Status: ratifiziert.**

### Kap. 2 — Architektur-Weichen
5 Weichen. Entschieden: Projekt=Bauphase (Objekt klammert). Offen: Statusquelle, Angebot-Pflicht (Du); Rechnungssystem, Storno-Folge (Steuerberater). → `architektur-entscheidungen.md`. **Status: teils entschieden.**

---

## Teil II — Der Kernprozess

### Kap. 3 — Kunde / Objekt / Gewerk
Echte FK-Kette Kunde→Objekt→Gewerk, voll befüllt; Mehrfachheit schema-möglich, aber 1:1:1 gelebt. → `hierarchie-objekt-projekt-bestandsaufnahme.md`. **Status: kartiert.**

### Kap. 4 — Workflow Lead→Angebot→Auftrag→Rechnung
Keine erzwungene Kette, parallele Datensätze über Tripel; ~11 Status-Felder (Schwäche 1); zwei Rechnungssysteme. → `workflow-analyse.md`. **Status: kartiert; Weichen offen.**

### Kap. 5 — Das Kundenprofil
Mega-Blade (ursprüngl. 23.145 Z.), 3 Sektionen (CSS/HTML/JS), hängt sauber an new_leads. Zerlegung läuft (CSS raus). → `kundenprofil-architektur-bestandsaufnahme.md`, `kundenprofil-zerlegung-schnittplan.md`. **Status: Zerlegung begonnen.**

---

## Teil III — Vertrieb & Dokumente

### Kap. 6 — Angebots- & Set-Konfiguration
Angebotserstellung (offers + Wizard), Set-/Produktkonfiguration. Aktiv: `master_sets`+`costing_sets`. Großer Brocken: Master-Set ~6.700 Z. + `config.blade.php` ~25.000 Z. → `crm-inventur-03-angebot-konfiguration.md`. **Status: grob kartiert; Detail-Inventur nötig.**

### Kap. 7 — Auftrag, Auftragsbestätigung, Dokumente/PDF
Auftragsdokumente = heute v.a. Datei-Ablage über `images`. **Echte Lücke: serverseitiges Angebots-/Auftrags-PDF fehlt** (`generatePdf` existiert nicht, client-seitig gerendert, Backend speichert nur JSON). → `crm-inventur-04-auftrag-dokumente.md`. **Status: grob kartiert; PDF-Lücke offen.**

---

## Teil IV — Waren & Material

### Kap. 8 — Artikel / Produktkatalog
14 Unterbereiche; `ProductController` ~1.964 Z. + Artikelgruppen. → `crm-inventur-01-artikel.md`. **Status: grob kartiert; Detail-Inventur-Kandidat.**

### Kap. 9 — Lager / Inventur / Beschaffung / Großhandel
Lager/Bestand, Bestellkette, Großhandels-Schnittstellen (`SupplierConnectionController` ~1.106 Z.; DATANORM nur Prototyp; IDS-Callbacks teils ohne Auth). → `crm-inventur-02-lager-beschaffung.md`. **Status: grob kartiert; Detail-Inventur + Sicherheitspunkt.**

---

## Teil V — Organisation & Ausführung

### Kap. 10 — Abteilungen / Organisation / HR
Abteilungen/Filialen/Teams, HR (`EmployeeController` ~3.523 Z.). HR-Tabellen 0 Zeilen → viel Code, wenig gelebt. → `crm-inventur-05-organisation-hr.md`. **Status: grob kartiert.**

### Kap. 11 — Projektmanagement / Planer / Aufgaben / Tickets / Assets
Größter Einzelbrocken: `PlannerPlanController` ~11.080 Z. **3 parallele Phasen-/Aufgaben-Systeme** (klassisch/Kanban/Planner) + `projects` neben `planner_plans` — führende Tabelle offen. → `crm-inventur-06-projekt-aufgaben-assets.md`. **Status: grob kartiert; wichtigster Detail-Inventur-Kandidat.**

### Kap. 12 — Kalender / Termine
Operatives Termin-Drehkreuz, `main_appointments`, ~3.300-Z.-Controller. → `kalender-termine-bestandsaufnahme.md`. **Status: kartiert.**

---

## Teil VI — Querschnitt & Auswertung

### Kap. 13 — Dokumente / Medien / Kommunikation / KI
Bilder/Galerien (`images`), Chat, KI (Ollama), E-Mail (**heikel — bewusst gemieden**), + 12 „Sonstige". Chat-Menülink zeigt auf alten Bitrix-Chat. → `crm-inventur-07-medien-kommunikation-rest.md`. **Status: grob kartiert.**

### Kap. 14 — Cockpit / Controlling / Auswertungen
Abteilungs-Achse solide, Kennzahlen nicht belastbar (fehlende Datenbasis). → `cockpit-inventur.md`, `controlling-bestandsaufnahme.md`. **Status: kartiert; baut auf sauberem Status+Umsatz auf (Weichen).**

### Kap. 15 — Mobile / API (Nuriva)
Externe Monteur-App; hier nur Backend-API (`/api/mobile`, `/api/planner`, Sanctum-Token). Geteilte Fläche = Datenschicht. → `nuriva-sync-anbindung-befund.md`. **Status: kartiert.**

---

## Teil VII — Altlasten

### Kap. 16 — Legacy / Old Code
~58.500 Z. tot (Old/ 40 Dateien, falscher Namespace, nicht autoloadbar; ~194 Legacy-Views), 0 Live-Routen. → `crm-inventur-08-legacy.md`. **Status: kartiert; Aufräum-Strang (später).**

---

## Anhang A — Fahrplan (Abarbeitungs-Reihenfolge)
Die Reihenfolge, in der wir die Kapitel anfassen, steht in `fahrplan-ticket-crm.md` (nach Dringlichkeit/Abhängigkeit, NICHT nach Kapitelnummer). Grundprinzip je Bereich: verstehen → entscheiden → bauen.

## Anhang B — Offene Detail-Inventur-Lücken (vorrangig)
Bereiche, die nur grob kartiert sind und eine eigene Detail-Inventur brauchen, bevor man sie anfasst:
1. **Planner / Projektmanagement** (~11k Z. + 3 Phasen-Systeme) — größter/wichtigster.
2. **Master-Set / Kalkulation** (~6.700 + 25k Z. config).
3. **Produktkatalog** (ProductController + ArticleGroup).
4. **Großhandels-Schnittstellen** + Kette Kaufanfrage→Bestellung→Wareneingang.
5. **HR-Monolith** EmployeeController (~3.523 Z.).
Mittel: Auftrags-Doku/PDF-Verkabelung, Notifications, E-Mail, KI, Chat-Menülink, Distributor↔Katalog-Grenze.

## Anhang C — Zonen-übergreifende Kernbefunde
1. ~58.500 Z. toter Old-Ballast (Aufräum-Strang).
2. Serverseitiges Angebots-/Auftrags-PDF fehlt (echte Lücke).
3. 3 parallele Phasen-/Aufgaben-Systeme + projects/planner_plans (führende Tabelle offen) — dasselbe „mehrere Wahrheiten"-Muster wie beim Status.
4. Rechte: user_rolls + hasPermission() + is_admin-Bypass, kein benanntes Rollensystem.
5. Sicherheit: öffentliche IDS-Callback-Routen ohne Auth; doppelte /fusion/webhook-Route.
6. Viel gebaut, DB oft leer (HR, Attendance) → Größe nach Code-Reife bewerten, nicht nach Daten.
7. Tote Scaffolds quer durch (DealNote, Attendance, EmployeeDepartment …).

---

*Gesamtkonzept — das thematische Bild. Abgearbeitet wird nach Fahrplan (Anhang A). Kapitel verweisen auf die Detail-Dokumente, ersetzen sie nicht.*
