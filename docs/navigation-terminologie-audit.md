# Navigations-Terminologie-Audit

**Projekt:** ticket (Solar/PV-Business-App)  ·  **Stand:** 2026-06-27

**Methodik:** 24 parallele Agenten haben jeden der **129 Menüpunkte** über Route → Controller → View bis zum tatsächlichen Seiteninhalt zurückverfolgt und geprüft, ob der Navi-Titel (Fachbegriff) den Inhalt korrekt wiedergibt.

## Zusammenfassung

- ✅ **passt:** 58
- 🟡 **ungenau:** 55  (richtig, aber unpräzise)
- 🔴 **irreführend:** 16  (Titel suggeriert etwas anderes als der Inhalt)

> **Umsetzungsstand (2026-06-27):** 15 der 16 irreführenden Titel **und alle 55 „ungenau"-Titel** wurden direkt in der Sidebar umbenannt. Ausnahme: „Neuer Kontakt" (Partner) — verlinkt auf `inquiry.create` und würde „Neue Anfrage" doppeln; das ist vermutlich ein **falscher Link** statt eines Wording-Problems → siehe offene Produktfrage unten.

---

## 🔍 Sonderfall: „Phasen" / „Schritte" (Admin › System) — vertieft geprüft

**Kein funktionaler Bug** (beide Links funktionieren), aber die Begriffe sind über alle Ebenen verdreht:

| Menü-Label | Route / Modell | Seitentitel | Items heißen dort |
|---|---|---|---|
| **Schritte** | `task_phase.index` / **TaskPhase** | „Arbeitsschritte – Produkte & Leistungen" | Arbeitsschritte (führt in die „Phasenverwaltung") |
| **Phasen** | `stages.index` / **Stage** | „Projekt-Struktur" | „Schritt / Stage" |

**Befund:**
- Das Modell **TaskPhase** (= Phase) bedient die **„Schritte"**-Seite, das Modell **Stage** (= Schritt/Stufe) bedient die **„Phasen"**-Seite → die Code-Namen sind **invers** zu den Menü-Labels.
- Am gravierendsten: Der Menüpunkt **„Phasen"** öffnet eine Seite mit Titel **„Projekt-Struktur"**, deren Einträge **„Schritt / Stage"** heißen — der Begriff „Phasen" passt dort zu **nichts**.

**Empfehlung (rein Wording, ohne Code-Risiko):**
- „Phasen" → **„Projekt-Struktur"** (passt zum Seitentitel) oder einheitlich **„Stages"**.
- „Schritte" kann bleiben (Seite nennt sich selbst „Arbeitsschritte").
- Eine *Modell-Umbenennung* (TaskPhase ↔ Stage) wäre die sauberste, aber riskante Lösung → besser separat als Refactor.

---

## 🔴 Irreführend (16) — zum Abhaken

- [ ] **CRM › Anfrageliste** → **Kundenanfragen**
  - _Inhalt:_ Liste von Anfragen mit Typ Kunde
  - _Warum:_ Die Seite filtert ausschließlich Anfragen mit pre_type='Kunde' und ist somit keine allgemeine Anfrageliste, sondern eine spezifische Kundenanfragen-Liste; der interne Beschreibungstext lautet 'Verwalten Sie Ihre Kundenanfragen'.
- [ ] **CRM › Webseiten** → **Website-Leads**
  - _Inhalt:_ Website-Leads aus WordPress Fusion-Formularen
  - _Warum:_ Die Seite zeigt Eingangs-Leads aus dem WordPress-Frontend (Fusion-Forms) und betitelt sich 'WEBSITE LEADS'; 'Webseiten' suggeriert eine Verwaltung von Webseiten statt einer Lead-Eingangsübersicht.
- [ ] **CRM › Neuer Kontakt** → **Neue Anfrage**
  - _Inhalt:_ Formular zur Erstellung einer neuen Anfrage
  - _Warum:_ Die Seite (View 'admin.inquiry.contact', Titel 'ANFRAGE AUFNAHME') ist ein vollständiges Anfrage-Erfassungsformular (Inquiry), nicht ein allgemeines Kontaktformular; unter 'Partner' wirkt der Begriff 'Neuer Kontakt' fehl am Platz und verschleiert, dass eine CRM-Anfrage angelegt wird.
- [ ] **CRM › Hersteller** → **Alle Marken / Partner**
  - _Inhalt:_ Ungefilterte Liste aller Brand-Einträge aller Typen
  - _Warum:_ brand.index zeigt ohne Typ-Filter alle Datensätze der Brand-Tabelle (Hersteller, Architekten, Banken, Subunternehmer usw.); die View-Überschrift ist zwar 'HERSTELLER', der tatsächliche Datenbestand umfasst aber sämtliche Partner-Typen, sodass der Titel trügerisch ist.
- [ ] **CRM › Dienstleister** → **Externe Firmen**
  - _Inhalt:_ Liste externer Unternehmen / Zeitarbeitsfirmen
  - _Warum:_ Die View heißt 'Zeitarbeitfirma' und verwaltet Einträge der Tabelle 'external_personals' (Zeitarbeitsfirmen mit company_name, tax_id, Preismodell); der Begriff 'Dienstleister' ist im Solar-CRM-Kontext irreführend, da die Seite explizit Zeitarbeitsfirmen adressiert, nicht allgemeine Dienstleister.
- [ ] **Vertrieb › Neue Vorlage** → **Neues Angebot**
  - _Inhalt:_ Angebots-Editor zum Erstellen eines neuen Angebots (optional als Vorlage)
  - _Warum:_ Die Seite ('SADESK - Smart Angebot') öffnet den vollständigen Angebots-Editor mit Kundenwahl und Positionskalkulation; 'Als Vorlage erstellen' ist nur eine optionale Checkbox, der primäre Zweck ist das Erstellen eines neuen Angebots, nicht einer Vorlage.
- [ ] **Vertrieb › Rechnung Canvas** → **Rechnungen (Canvas-Hinweis)**
  - _Inhalt:_ Dieselbe Rechnungslisten-Seite mit JS-Hinweisalert für Canvas-Modus
  - _Warum:_ Der Link öffnet exakt dieselbe Rechnungsübersichts-Seite wie 'Rechnungen'; der Parameter ?open_canvas=1 löst nur einen alert-Hinweis aus, öffnet aber keinen echten Canvas-Editor – der eigentliche Canvas (canvas.blade.php) ist ein separater Edit-Screen pro Rechnung.
- [ ] **Support › Offen** → **Ticket-Übersicht**
  - _Inhalt:_ Zentrales Ticket-Board mit Tabs Aktiv, Meine, In Bearbeitung, Archiv, Junk, Alle
  - _Warum:_ ProblemController@index rendert das Board admin.problem.problem_board, das alle Tickets nach mehreren Status-Tabs filtert (Standard-Tab: 'Aktiv'); der Begriff 'Offen' suggeriert fälschlicherweise eine reine Liste offener Tickets.
- [ ] **Personal › Betriebsfrei** → **Feiertagskalender**
  - _Inhalt:_ Betriebsinterne Feiertagsjahre (Jahreseinträge) aktivieren/verwalten
  - _Warum:_ Die Seite verwaltet jahresbezogene Feiertagskonfigurationen (Holiday-Model: year + holiday-Text + Publish-Status) für den internen Betriebskalender; 'Betriebsfrei' suggeriert einzelne betriebsfreie Tage, nicht einen konfigurierbaren Jahreskalender.
- [ ] **Artikel & Lager › Stempel** → **Stamm-Listen**
  - _Inhalt:_ Stamm-Listen: Artikel-Sammlungen nach Projekt oder Serie
  - _Warum:_ Die Seite heißt intern "Stamm-Listen & Sets" und verwaltet Sammlungen von Stamm-Artikeln; der Begriff "Stempel" suggeriert einen Druckvermerk oder Siegelstempel statt Artikel-Stammlisten.
- [ ] **Artikel & Lager › Formeln** → **Checklisten-Formulare**
  - _Inhalt:_ Liste von Checklisten-Formularen je Artikel-Gruppe
  - _Warum:_ Die Seite zeigt keine mathematischen Formeln, sondern je Artikel-Gruppe hinterlegte Formular-Checklisten (ProductFormula mit Feldern); das Blade trägt den Titel 'Formulaliste' und die H1 lautet 'FORMULARE'.
- [ ] **Finanzen › Spesenarten** → **Filial-Betriebskosten**
  - _Inhalt:_ Betriebskosten je Filiale und Jahr (Miete, Versicherung, Raten, Personal)
  - _Warum:_ Die Seite zeigt 'BRANCH & HAUSKOSTEN' – strukturierte Betriebskosten (Miete, Versicherungen, sonstige Kosten, Personalgehälter, Raten) pro Niederlassung und Jahr; 'Spesenarten' suggeriert Reise-/Auslagenspesen-Kategorien, was inhaltlich nicht zutrifft.
- [ ] **Admin › Limit-User** → **Eingeschränkte Benutzer**
  - _Inhalt:_ Benutzerliste gefiltert auf is_admin≠1, CRUD
  - _Warum:_ Der englische Hybrid-Begriff 'Limit-User' ist kein deutscher Fachbegriff; die Seite zeigt Benutzer mit eingeschränkten Rechten (is_admin != 1), im UI selbst als 'Limited Users' bezeichnet.
- [x] **Admin › Phasen** → **Projekt-Struktur** _(umgesetzt — passt zum Seitentitel)_
  - _Inhalt:_ Stages (Arbeitsprozessschritte) je Produkt und Version konfigurieren
  - _Warum:_ Die Route führt zum StageController, der Stage-Datensätze verwaltet; das Blade nennt sie intern durchgehend 'Schritte' (Neuer Schritt, Schritt bearbeiten), während TaskPhase-Objekte (die eigentlichen Phasen) über die Route task_phase.index verwaltet werden – die Labels 'Phasen' und 'Schritte' sind im Menü faktisch vertauscht.
- [ ] **Berichte › Tage** → **Tagesberichte**
  - _Inhalt:_ Tagesberichte der Mitarbeiter mit Arbeitszeiten und Aktivitäten
  - _Warum:_ Der einsilbige Begriff 'Tage' gibt weder die Entität (Mitarbeiter-Tagesbericht) noch die Funktion (Zeiten- und Aktivitätsübersicht) wieder; die Seite titelt sich selbst 'Tagesbericht – Mitarbeiter-Zeiten & Aktivitäten' und listet alle Mitarbeiter mit ihrem täglichen Berichtsstatus.
- [ ] **System › Papierkorb** → **Datenbankbereinigung**
  - _Inhalt:_ DB-Garbage-Collection: endgültiges Löschen soft-deleted Datensätze
  - _Warum:_ Die Seite ist kein Papierkorb zum Wiederherstellen gelöschter Objekte, sondern ein Admin-Tool zur endgültigen Bereinigung (Purge) aller soft-deleted Datenbankeinträge systemweit – der Begriff Papierkorb suggeriert fälschlicherweise eine Wiederherstellungsfunktion.

## 🟡 Ungenau (55) — zum Abhaken

- [ ] **CRM › Postfach** → **Posteingang (Legacy)**  ·  _IMAP-Posteingang aus Legacy-Leads-Tabelle mit Compose_
- [ ] **CRM › Mail-Einstellungen** → **E-Mail-Konten (System)**  ·  _Liste der IMAP/SMTP-Serverkonfigurationen mit Test- und Aktivierungsfunktion_
- [ ] **CRM › Mail-Regeln** → **Domain-Filter**  ·  _Whitelist erlaubter Absender-Domains für Lead-E-Mail-Sync_
- [ ] **CRM › Chat** → **Bitrix24-Chat**  ·  _Bitrix24-Chat-Übersicht mit Kanal- und Nachrichtenliste_
- [ ] **CRM › Meine Liste** → **Meine Anfragen**  ·  _Anfragen-Liste des eingeloggten Nutzers_
- [ ] **CRM › Verifiziert** → **Veröffentlichte Anfragen**  ·  _Anfragen mit Status published/veröffentlicht_
- [ ] **CRM › Kunden** → **Leads / Kunden**  ·  _Gruppenheader ohne eigene Zielseite_
- [ ] **CRM › Neu** → **Neuer Lead**  ·  _Formular Neuen Kunden/Lead anlegen mit Objektdaten_
- [ ] **CRM › Liste** → **Leadliste**  ·  _Sortierbare, filterbare Lead-Listentabelle_
- [ ] **CRM › Historie** → **Kundenakte**  ·  _Vollständige Kundenakte: Timeline, Notizen, Termine, Angebote, Rechnungen_
- [ ] **CRM › Warteliste** → **Warteschleife**  ·  _Liste von Leads im Warteschleife-Status_
- [ ] **CRM › Kontakte** → **Alle Kontakte**  ·  _Gesamtliste aller Kontakttypen aus dem System_
- [ ] **CRM › Subunternehmer** → **Nachunternehmer**  ·  _Gefilterte Liste der Brand-Einträge Typ sub_contractor_
- [ ] **Vertrieb › Angebotskonfigurator** → **Angebots-Assistent**  ·  _Geführter Assistent mit 4 Wegen zur Angebotserstellung_
- [ ] **Vertrieb › Feinaufmaß** → **Feinaufmaß-Kanban**  ·  _Kanban-Board für Feinaufmaß-Vorgänge mit Planungs-Spalten_
- [ ] **Projekte › To-dos** → **Persönliche Notizen**  ·  _Persönliche Notizen/Sticky-Notes mit Offen-/Erledigt-Tabs_
- [ ] **Projekte › Kalender** → **Mein Kalender**  ·  _Persönlicher FullCalendar mit Aufgaben und Kundenterminen_
- [ ] **Projekte › Übersicht** → **Terminübersicht**  ·  _Kundentermine als Kanban- und Listenansicht mit Filtern_
- [ ] **Projekte › Verträge** → **Wartungsverträge**  ·  _Paginierte Liste aller Wartungsverträge mit Status, Filter, Kanban und Kalender_
- [ ] **Projekte › Checklisten** → **Wartungs-Checklisten**  ·  _Listenverwaltung von Wartungs-Checklisten-Vorlagen mit Tabs aktiv/archiviert/Papierkorb_
- [ ] **Support › Fehler** → **Fehlerkatalog**  ·  _Fehlerhandbuch: Liste von Fehlercodes, Problemtypen, Ursachen und Lösungen_
- [ ] **Personal › Neu** → **Mitarbeiter anlegen**  ·  _Formular zum Anlegen eines neuen Mitarbeiters_
- [ ] **Personal › Arbeitszeiten** → **Zeitpläne**  ·  _Zeitpläne aller Mitarbeiter: Soll/Ist-Stunden, Genehmigungsstatus je Monat_
- [ ] **Personal › Lohnkosten** → **Lohn & Vollkosten**  ·  _Lohn- und Vollkosten-Übersicht je Mitarbeiter und Abrechnungsmonat_
- [ ] **Personal › Länder** → **Länder & Nationalitäten**  ·  _Liste Länder mit Staatsangehörigkeit/Nationalität_
- [ ] **Personal › Feiertage** → **Gesetzliche Feiertage**  ·  _Gesetzliche Feiertage nach Bundesland/Stadt mit Kalender_
- [ ] **Personal › Urlaub** → **Urlaubsanspruch**  ·  _Urlaubstage-Anspruch pro Jahr konfigurieren und aktivieren_
- [ ] **Personal › Steuerdaten** → **Steuerklassen**  ·  _Steuersätze (%) und Steuerklassen mit Bemerkung verwalten_
- [ ] **Personal › Positionen** → **Stellen & Qualifikationen**  ·  _Mehrtabige Verwaltung Stellen, Qualifikationen, Kalkulation, Hierarchie_
- [ ] **Artikel & Lager › Neu** → **Neuer Artikel**  ·  _Mehrstufiger Wizard zum Anlegen eines neuen Artikels_
- [ ] **Artikel & Lager › Vergleich** → **Preisvergleich**  ·  _Produkt-Preisvergleich nach Lieferantenpreisen_
- [ ] **Artikel & Lager › Heizkörper** → **Heizkörper-Konfigurator**  ·  _Heizkörperkonfiguration je Kunde und Objekt_
- [ ] **Artikel & Lager › Schnittstellen** → **Lieferanten-Schnittstellen**  ·  _Liste und Verwaltung von Lieferanten-Shop-Verbindungen_
- [ ] **Artikel & Lager › Rabatte** → **Rabattgruppen**  ·  _Paginierte Liste von Rabattgruppen mit Prozentwert_
- [ ] **Artikel & Lager › Gruppen** → **Artikel-Gruppen**  ·  _Liste von Artikel-Gruppen mit Sub-Gruppen (CRUD)_
- [ ] **Artikel & Lager › Vorschläge** → **Anfragevorschläge**  ·  _Anfragevorschläge: produktbasierte Zuordnung von Abteilungen und Stellen_
- [ ] **Artikel & Lager › Assets** → **Betriebsmittel**  ·  _Betriebsmittel- und Übergabeliste (zwei Tabs)_
- [ ] **Artikel & Lager › Anforderungen** → **Lagerausgaben**  ·  _Liste interner Lagerausgabe-Anforderungen_
- [ ] **Artikel & Lager › Einkauf** → **Kaufanfragen**  ·  _Liste interner Kaufanfragen an den Einkauf_
- [ ] **Artikel & Lager › Fahrzeuge** → **Maschinen & Fahrzeuge**  ·  _Maschinenbestand-Liste für Maschinen und Fahrzeuge_
- [ ] **Finanzen › Förderung** → **BEG-Förderungen**  ·  _Liste BEG-Fördereinträge: Heizungstyp, Förderprozentsatz, Maximalbetrag_
- [ ] **Finanzen › Raten** → **Ratenzahlungen (Assets)**  ·  _Ratenzahlungsliste für Fahrzeuge/Maschinen und Vermögenswerte_
- [ ] **Admin › Admins** → **Admin-Benutzer**  ·  _Benutzerliste gefiltert auf is_admin=1, CRUD_
- [ ] **Admin › Rollen** → **Berechtigungen**  ·  _Berechtigungsmatrix pro Benutzer, Lesen/Schreiben/Löschen je Modul_
- [ ] **Admin › Profile** → **Mein Profil**  ·  _Eigenes Benutzerprofil: Kontodaten, Rolle, Berechtigungsübersicht_
- [ ] **Admin › Schritte** → **Arbeitsschritte**  ·  _Leistungsabschnitte und Arbeitsschritte je Produkt/Artikelgruppe verwalten_
- [ ] **Admin › Standorte** → **Filialen**  ·  _Filialen-Liste mit Kontakt-, Finanz- und Farbdaten verwalten_
- [ ] **Arbeitsbereich › Pipeline** → **Lead-Kanban**  ·  _Lead-Kanban-Board mit Vertriebsphasen (Lead, Angebot, Auftrag, Projekt)_
- [ ] **Berichte › Alle Berichte** → **Berichts-Übersicht**  ·  _Filter- und Suchcenter für aktuelle Berichte aller Mitarbeiter, Kunden, Abteilungen_
- [ ] **Berichte › Meine Berichte** → **Überfällige Berichte**  ·  _Überfällige Berichte des aktuellen Mitarbeiters (Berichtscenter)_
- [ ] **Projekte › Aufgaben** → **Meine Aufgaben**  ·  _Board/Liste persönlicher Aufgaben des eingeloggten Mitarbeiters_
- [ ] **Projekte › Team-Aufgaben** → **Allgemeine Aufgaben**  ·  _Kanban-Board allgemeiner Aufgaben für alle oder Abteilungen sichtbar_
- [ ] **Personal › Zuweisungen** → **Stellenbesetzung**  ·  _Drag-and-Drop-Matrix: Mitarbeiter Abteilungen und Positionen zuordnen_
- [ ] **System › Hilfe** → **Wissensdatenbank**  ·  _Wissensdatenbank-Kategorien mit Suchfunktion (Help Center)_
- [ ] **System › Hinweise** → **Systemwarnung**  ·  _Konfiguration und Verlauf systemweiter Warnhinweis-Banner_

## ✅ Passt (58) — keine Änderung nötig

- CRM · **Kommunikation** — Oberkategorie ohne eigene Seite
- CRM · **Lead E-Mails** — Paginierter Lead-E-Mail-Posteingang mit Ungelesen-Badge und Domain-Filter
- CRM · **Lead-Konten** — Liste der IMAP-Konten für Lead-E-Mail-Abruf mit CRUD
- CRM · **Anfragen** — Oberbegriff für CRM-Eingangsanfragen-Bereich
- CRM · **Neue Anfrage** — Eingabeformular neue Anfrage anlegen
- CRM · **Junk** — Anfragen mit Status junk (Spam/ungültig)
- CRM · **Papierkorb** — Soft-gelöschte Anfragen wiederherstellen oder einsehen
- CRM · **Junk** — Liste als Junk markierter Leads mit Junk-Grund
- CRM · **Gelöscht** — Liste soft-gelöschter Leads mit Löschgrund
- CRM · **Partner** — Navigationsgruppe ohne eigene Seite
- CRM · **Lieferanten** — Liste und Verwaltung aller Lieferanten
- CRM · **Architekten** — Gefilterte Liste der Brand-Einträge Typ architect
- CRM · **Banken** — Gefilterte Liste der Brand-Einträge Typ bank
- CRM · **Versicherungen** — Gefilterte Liste der Brand-Einträge Typ insurance
- Vertrieb · **Angebote** — Gruppenüberschrift Vertrieb-Angebote-Menü
- Vertrieb · **Übersicht** — Angebotsliste mit Statistiken, Status-Filtern und Kanban-Ansicht
- Vertrieb · **Vorlagen** — Vorlagenbibliothek mit Suche, Filtermöglichkeit und Vorlagendetails
- Vertrieb · **Aufträge** — Abschnitts-Gruppe Vertrieb › Aufträge
- Vertrieb · **Übersicht** — Paginierte Liste aller Aufträge mit Status-Filtern und Kennzahlen
- Vertrieb · **Rechnungen** — Rechnungsliste mit Tabellen-, Karten-, Kunden- und Kanban-Ansicht
- Vertrieb · **Junk** — Gefilterte Liste aller als Junk markierten Aufträge
- Vertrieb · **Gelöscht** — Auftragsliste mit soft-gelöschten Einträgen (Papierkorb)
- Projekte · **Notizen** — Gruppenüberschrift, kein eigener Seiteninhalt
- Projekte · **Kategorien** — Paginierte Liste der Notiz-Kategorien (Name, Typ, Farbe)
- Projekte · **Termine** — Gruppe mit Kalender- und Terminübersicht-Unterseiten
- Projekte · **Wartung** — Oberpunkt ohne eigene Zielseite
- Support · **Tickets** — Gruppenüberschrift für Support-Ticketsystem
- Support · **Neues Ticket** — Formular zur Erstellung eines neuen Support-Tickets
- Personal · **Mitarbeiter** — Gruppenüberschrift ohne eigene Zielseite
- Personal · **Übersicht** — Tabellenliste aller Mitarbeiter mit Status-Tabs (aktiv/inaktiv)
- Personal · **Teams** — Liste aller Teams mit Teamleitern und Mitgliedern
- Personal · **Arbeitsorte** — Verwaltung von Arbeitsplatz-Standorten (Typ, Name, GPS-Koordinaten)
- Personal · **Anwesenheit** — Check-in/Check-out-Tabelle mit Datum, Status und Filterfunktion je Mitarbeiter
- Personal · **Krankheit & Urlaub** — Auswertung Kranken- und Urlaubstage mit Balkendiagrammen und Effizienzwerten je Mitarbeiter
- Personal · **HR-Daten** — Sammelgruppe für HR-Stammdaten-Pflege
- Personal · **Vertragstypen** — Liste Vertragsarten mit Laufzeit (Vollzeit, Teilzeit …)
- Personal · **Sprachen** — Sprachenliste für Mitarbeiterprofile pflegen
- Personal · **Organisation** — Gruppenheader ohne eigene Seite
- Personal · **Abteilungen** — Filterable Liste aller Abteilungen mit Statistiken
- Personal · **Organigramm** — Interaktiver Abteilungs-Baumchart mit Drag-and-Drop
- Artikel & Lager · **Artikel** — Gruppenheader ohne eigene Seite
- Artikel & Lager · **Katalog** — Gesamtliste aller Artikel mit Filter und Suche
- Artikel & Lager · **Favoriten** — Ordnerverwaltung für Lieblingsprodukt-Sammlungen
- Artikel & Lager · **Master-Sets** — Verwaltung vordefinierter Artikel- und Leistungspakete
- Artikel & Lager · **GC Online / IDS** — Artikelsuche im GC-Online-Shop per IDS-Protokoll
- Artikel & Lager · **Artikel-Daten** — Gruppenheader ohne eigene Zielseite
- Artikel & Lager · **Einheiten** — Paginierte Liste aller Maßeinheiten (CRUD)
- Artikel & Lager · **Lager** — Gruppenheader ohne eigene Seite
- Artikel & Lager · **Inventar** — Lagerbestandsliste mit Lagerorten, Kategorien, Quantitäten
- Artikel & Lager · **Lieferscheine** — Liste aller Lieferscheine
- Artikel & Lager · **Übergaben** — Liste von Gegenstand-Übergaben zwischen Mitarbeitern
- Finanzen · **Finanzen** — Navigationsgruppe ohne eigene Seite
- Admin · **Benutzer** — Gruppenüberschrift, keine eigene Seite
- Admin · **System** — Gruppenüberschrift, keine eigene Zielseite
- Arbeitsbereich · **Dashboard** — Persönliches Mitarbeiter-Dashboard mit Aufgaben, Terminen, Urlaubsstatus
- Projekte · **Projektplanung** — Montage-Projekte Cockpit: Kanban/Liste nach Kunde, Objekt, Fortschritt
- System · **KI-Wissen** — Verwaltung von KI-Chat-Lernthemen mit Medienzuordnung
- System · **Feedback** — Paginierte Liste interner Feedback-Meldungen mit Status-Statistiken
