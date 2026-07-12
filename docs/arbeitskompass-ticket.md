# Arbeitskompass — ticket

**Stand:** 2026-07-11  
**Zweck:** Diese Datei beantwortet jederzeit die Fragen: *Woran arbeiten wir gerade? Wann ist es fertig? Was kommt als Nächstes? Was ist geparkt?*

> Regel: Wenn eine größere Aufgabe beginnt oder endet, wird diese Datei zuerst/zuletzt aktualisiert. Keine neue große Baustelle ohne Eintrag hier.

> Systemweite Methode: **① konzeptionell optimieren → ② Workflow bestimmen → ③ vorhandene Bausteine verknüpfen → ④ erst dann automatisieren**. Keine Automatisierung vor Konzept + Workflow + Verknüpfungsplan. Keine zweite Berechnung, keine zweite Wahrheit, kein UI-Flicken über falscher Fachlogik.
> Details: [`docs/systemoptimierung-fahrplan.md`](systemoptimierung-fahrplan.md) ist verbindlich fuer Inventur, kritische Bewertung, Optimierungsvorschlag, Verknuepfung, Workflow, Automatisierung, Umsetzung und Abnahme.
> Vorher gilt [`docs/system-kapitelplan.md`](system-kapitelplan.md): Erst das gesamte vorhandene System in Kapitel/Domaenen einordnen, dann einzelne Kapitel tief bearbeiten.
> Vor jedem Kapitel muss Claude Code zuerst einen Kapitel-Startblock liefern: Wo faengt er an, was macht er, was ist das Ziel, welche Dateien/Services liest er, was macht er nicht, wo endet das Kapitel.

---

## 1. Aktueller Fokus

### AKTIV: Gesamtsystem-Kapitelplan / Systemlandkarte

**Methoden-Stufe:** Kapitel A — Systemlandkarte / Querschnitt nach `docs/system-kapitelplan.md`. Es wird noch nicht gebaut. Zuerst wird das gesamte vorhandene System in fachliche Kapitel eingeordnet. Danach entscheidet Yama, welches Kapitel tief inventarisiert und bewertet wird.

**Warum jetzt:** Einzelthemen wie WP-Auslegung, Arbeitsliste, Rechnung oder Kundenprofil duerfen nicht isoliert optimiert werden, solange die Gesamtlandkarte fehlt. Erst muss klar sein, welche Kapitel/Domaenen das System hat, wie sie zusammenhaengen und wo ein Thema im Gesamtprozess liegt.

**Aktueller Zustand:**

- Es gibt viele bereits gebaute Module, Services, Controller, Views und Automatisierungen.
- Einzelne Bereiche wurden zuletzt isoliert betrachtet.
- Es fehlt eine aktuelle Systemkarte, die alle Hauptkapitel, Prozessketten und Datenwahrheiten zusammenfasst.
- WP-Auslegung bleibt wichtig, wird aber als Unterkapitel von Angebot/Auslegung behandelt.

**Ziel dieses Fokus:** Eine belegte Kapitel-/Systemlandkarte:

```text
Eingang/Kunde/Objekt -> CRM -> Angebot/Auslegung -> Auftrag -> Beschaffung/Planung/Montage -> Abnahme -> Rechnung/FiBu -> Controlling/Service
```

**Ergebnis dieses Fokus:** `docs/system-inventur.md` mit Hauptkapiteln, vorhandenen Modulen, fuehrenden Datenwahrheiten, Bruechen, offenen Fragen und Vorschlag, welches Kapitel als erstes tief bearbeitet wird.

**Nicht-Ziel jetzt:**

- Keine Umsetzung.
- Keine WP-Detailoptimierung.
- Keine Automatisierung.
- Keine UI-/Layout-Aenderung.
- Keine Entscheidung ueber einzelne Modulumbauten ohne Systemkarte.

---

## 2. Geparktes Einzelthema: WP-Auslegung im Angebot

WP-Auslegung bleibt als wichtiges Unterkapitel erhalten, aber startet erst nach Systemlandkarte oder nach bewusster Yama-Entscheidung.

**Zwingender Workflow-Grundsatz:** Die Auslegung ist **bedarfsgetrieben**, nicht produktgetrieben. Der Benutzer waehlt nicht zuerst eine Waermepumpe aus. Die Reihenfolge ist:

```text
1. Objekt erfassen
2. PLZ / Ort als Klimadaten-Schluessel erfassen
3. Verbrauch / Bestand / Nutzerverhalten erfassen
4. technische Randbedingungen erfassen
5. Heizlast / Energiebedarf berechnen
6. mehrere passende Waermepumpen-Alternativen als Ranking vorschlagen
7. je Alternative Bivalenz, Deckungsanteil, E-Stab-Anteil, Betriebsstunden, JAZ und Stromverbrauch bewerten
8. empfohlene Alternative begruendet in Angebot/Kalkulation uebernehmen
```

Der aktuelle Zustand "erst Waermepumpe auswaehlen, danach Verbrauch/Objekt" ist fachlich falsch und darf nicht als Ziel-Workflow uebernommen werden.

**Klimadaten-Pflicht:** PLZ und Ort sind Pflicht-Eingaben bzw. aus dem Objekt/Kundenkontext zu uebernehmen. Daraus muessen fuer die Auslegung mindestens Norm-Aussentemperatur, Heizgradtage, mittlere Aussentemperatur bzw. geeignete Klima-Bins/Temperaturklassen abgeleitet werden. Diese Klimadaten sind Eingangsparameter fuer:

- Heizlast-/Bedarfsplausibilisierung
- Waermepumpen-Abdeckung ueber das Jahr
- Bivalenzpunkt je vorgeschlagener Waermepumpe
- Anteil Heizstab / Zusatzheizung
- Betriebsstunden
- erwartete JAZ / SCOP-nahe Bewertung
- Stromverbrauch und Wirtschaftlichkeit

Ohne PLZ/Ort-gestuetzte Klimadaten ist die WP-Auslegung nur eine grobe Schaetzung und nicht fachlich belastbar.

**Vernetzungs-Pflicht statt Neubau:** Die vorhandenen Bausteine sind als fuehrende Quellen zu nutzen und nicht parallel neu zu implementieren:

- `KlimaPlzService` / `klima_plz`: PLZ, Ort, NAT, Jahresmitteltemperatur, Heizgradtage, Vollbenutzungsstunden, Hoehe
- `KlimaBinService`: Temperatur-Bins / Saisonverteilung fuer Jahresbetrachtung
- `VerbrauchsService`: Verbrauchsmethode und Plausibilisierung
- `HeizlastService`, `HeizlastRechner`, `HeizlastProjektService`: Heizlast / Bedarf
- `WaermepumpenMatchService`: Kandidaten aus dem Katalog
- `WpKennlinieService`: Leistung/COP ueber Temperatur und Vorlauf
- `BivalenzService`: Bivalenzpunkt, Deckungsanteil, E-Stab-Anteil, Laufstunden, JAZ, Stromverbrauch
- `CatalogDeviceRepository`: eine Wahrheit fuer Geraete/Katalog

Der Angebots-Wizard darf diese Logik nicht in JavaScript nachbauen. Er sammelt Eingaben, ruft Backend-Services/Endpoint auf und zeigt Ergebnis/Ranking an.

**Automatisierungs-Pflicht:** Die WP-Auslegung muss in der Angebots-/CRM-Automatisierung beruecksichtigt werden. Sobald Objekt, PLZ/Ort, Verbrauch und technische Mindestdaten plausibel vorhanden sind, soll das System:

- fehlende Pflichtdaten als Arbeitslisten-/Follow-up-Hinweis sichtbar machen
- eine serverseitige Vor-Auslegung automatisch vorbereiten oder aktualisieren
- mehrere WP-Kandidaten inklusive Bivalenz/JAZ/Strom/E-Stab-Anteil ranken
- Warnungen erzeugen, wenn PLZ/Klimadaten fehlen, Verbrauch widerspruechlich ist, Heizstab-Anteil hoch ist oder Vorlauf/Heizflaechen kritisch sind
- die empfohlene Alternative fuer Angebotsuebernahme/Kalkulation bereitstellen

Claude Code muss vor einer spaeteren WP-Umsetzung explizit rueckmelden, ob diese Vernetzung bereits geplant war. Falls nein, ist der Plan vor dem Bauen zu korrigieren.

---

## 3. Fertig-Definition für den aktuellen Fokus

Die Systemlandkarte gilt erst als fertig, wenn alle Punkte erfüllt sind:

1. **Kapitelplan bestaetigt**
   - Hauptkapitel aus `docs/system-kapitelplan.md` sind fuer das vorhandene ticket-System plausibel.

2. **Systeminventur abgeschlossen**
   - wichtigste Routen, Controller-Gruppen, Models, Tabellen, Views, Services, Docs und Automatisierungen sind grob je Kapitel eingeordnet.

3. **Datenwahrheiten sichtbar**
   - fuehrende Datenquellen und erkennbare Doppelwahrheiten sind markiert.

4. **Brueche sichtbar**
   - grobe Prozessbrueche und unverdrahtete Bausteine sind markiert, ohne sie schon zu loesen.

5. **Naechstes Tiefenkapitel vorgeschlagen**
   - Claude Code macht einen begruendeten Vorschlag, wo die erste Detail-Inventur starten soll.

6. **Yama entscheidet**
   - Kein Tiefenbau ohne deine Abnahme.

---

## 4. Nächste Schritte — Schritt für Schritt

### Schritt 1 — Kapitel-Startblock fuer Gesamtsystem

**Status:** nächster Schritt  
**Ergebnis:** Claude Code sagt vorab, wo er anfaengt, was er liest, was er nicht macht und wo die Runde endet.

**Fertig, wenn:** Du den Startblock fuer Kapitel A freigibst.

### Schritt 2 — Systeminventur

**Status:** nach Startblock-Freigabe  
**Ergebnis:** `docs/system-inventur.md`

Zu pruefen:

- Routen-/Controller-Gruppen
- Models / Tabellen / Migrationen
- Services / Repositories
- Views / Navigation / Einstiegspunkte
- Tests
- bestehende Docs/Audits/Fahrplaene
- Automatisierungen / Jobs / Commands
- Rechte / Policies / Gates

### Schritt 3 — Systembewertung / Priorisierung

**Status:** nach Systeminventur  
**Ergebnis:** `docs/system-kapitelbewertung.md`

**Fertig, wenn:** Hauptbrueche, Datenwahrheiten, Doppelstrukturen und naechste Tiefenkapitel begruendet sind.

### Schritt 4 — Yama-Entscheidung erstes Tiefenkapitel

**Status:** nach Bewertung  
**Ergebnis:** bewusste Auswahl des ersten Detailkapitels, z. B. Eingang/Kunde, CRM, Angebot/Auslegung, Auftrag oder Rechnung.

---

## 4. Danach — nächste große Themen

Nach WP-Auslegung kommt nicht automatisch die nächste spontane Baustelle. Reihenfolge:

1. **WP-Auslegung abschließen** oder bewusst parken.
2. **Rückfluss Montage/Planner -> Büro** aus `docs/fahrplan-ticket-crm.md` wieder aufnehmen.
3. **Kundenprofil/Objekt-Profil Redesign** erst mit eigener Bestandsaufnahme.
4. **Große Status-/Stage-Ablösungen** nur einzeln und geplant.
5. **Hygiene/404/tote Views** nur als Lückenfüller.

---

## 5. Parkplatz — nicht jetzt

Diese Themen sind wichtig, aber aktuell nicht der Hauptfokus:

- vollständiger CRM-Status-Umbau
- Cross-Gewerk-Intelligenz
- komplette Kundenprofil-Neugestaltung
- Accounting/FiBu-Ausbau
- Navigation-Großumbau
- Alt-Code-Entfernung ohne Lebendprüfung

---

## 6. Arbeitsregel gegen Überblicksverlust

Bei jeder neuen Aufgabe fragt Claude Code zuerst:

1. Passt die Aufgabe zum aktuellen Fokus?
2. Ist sie ein Schritt im aktuellen Fahrplan?
3. Ist sie ein Blocker?
4. Oder gehört sie in den Parkplatz?

Wenn die Aufgabe nicht klar einordenbar ist, wird **nicht gebaut**, sondern der Arbeitskompass aktualisiert und Yama entscheidet.

---

## 7. Kurzstatus für den Start eines neuen Chats

```text
Aktueller Fokus im ticket ist die Wärmepumpen-Auslegung im Angebotskontext.
Erst Schritt 1: Bestandsaufnahme WP-Wizard.
Danach Schritt 2: Gesamtkonzept mit Konzeption-, Workflow-, Architektur- und Frontend-Design-Agent.
Erst danach bauen.
Siehe docs/arbeitskompass-ticket.md.
```
