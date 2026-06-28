# Zielbild — ticket als objekt-zentriertes Komplettsanierungs-CRM

**Status:** Vision / Landkarte. **Kein** Bauauftrag für jetzt.
**Zweck:** Die gewerkeübergreifende Intelligenz-Vision für ticket festhalten, strukturieren und die zwingende Reihenfolge sichern — damit sie nicht verloren geht und nicht zur verfrühten Baustelle wird.
**Erstellt:** Juni 2026 · Solar Aspekt Group

---

## 0. Was dieses Dokument ist — und was nicht

Es ist die Roadmap für die *intelligente Schicht* von ticket: das, was ticket von jedem Standard-CRM/ERP unterscheidet. Es beschreibt das Zielbild und — genauso wichtig — die Reihenfolge, in der es entstehen muss, damit es trägt.

Es ist **nicht** der nächste Arbeitsschritt. Der nächste Schritt bleibt die laufende Stabilisierung. Diese Vision ist die *Krönung*, die auf einem Fundament aufsetzt, das erst stehen muss. In der falschen Reihenfolge gebaut, wird sie eine beeindruckende Schicht auf wackligem Grund — ein System, das falsche Vorschläge macht, ist schlimmer als eines, das keine macht, weil die Leute ihm dann nicht mehr vertrauen.

---

## 1. Die Grundeinsicht: objekt-zentriert + gewerkeübergreifend

Solar Aspekt ist ein **Komplettanbieter rund ums Gebäude**, nicht nur Energietechnik. Die Gewerke umfassen u. a.:

- PV / Photovoltaik
- Wärmepumpe / Heizung
- Elektro
- Batteriespeicher
- E-Mobilität / Wallbox
- Dach
- Fenster
- Küche
- Bad- / Sanierung
- Bauelemente

Der entscheidende Punkt: **Diese Gewerke hängen beim selben Kunden am selben Objekt zusammen.** Wer das Bad saniert, macht oft die Heizung mit. Wer Fenster tauscht, verbessert die Gebäudehülle → senkt die Heizlast → ändert die Wärmepumpen-Dimensionierung → beeinflusst die Förderung. Wer PV plant, braucht Speicher, Wallbox, ein tragfähiges Dach. Die Küche braucht Elektro.

Ein Standard-CRM behandelt das als getrennte Verkäufe. ticket kann es als **ein verbundenes Vorhaben am Objekt** behandeln. *Das* ist der Vorsprung, den kein generisches System hat — und er entsteht durch **Verknüpfung**, nicht durch mehr Funktionen.

**Zentrale Designentscheidung:** Der Dreh- und Angelpunkt ist das **Objekt (die Immobilie)**, nicht das einzelne Angebot oder Produkt. Ein Gebäude mit seinen Eigenschaften (Dach, Heizung, Stromanschluss, Baujahr, Gebäudehülle), und daran hängen die verschiedenen Gewerke-Maßnahmen. Dann sieht man auf einen Blick: „An diesem Objekt läuft PV (Auftrag), Wärmepumpe (Angebot), Wallbox (Idee) — Dach schon vermessen."

> Hinweis: Diese Hierarchie (Kunde → Objekt → Projekt/Maßnahme) hat *playground* bereits sauber; *ticket* fehlt sie. Ein weiterer Grund, warum das Datenmodell zuerst sauber werden muss.

---

## 2. Die Drei-Schichten-Architektur

Die Smartness funktioniert **nicht** als Einheitslogik über alle Gewerke. Jedes Gewerk ist fachlich verschieden:

- **Normbasiert / ingenieurmäßig:** Wärmepumpe, PV (Heizlast, Auslegung, BEG-Förderung) — berechenbar, regelbasiert.
- **Konfigurativ / geschmacksgetrieben:** Bad, Küche (Maße, Fliesen, Sanitärobjekte, Möbel, Varianten).
- **Aufmaß- / Positionsgeschäft:** Fenster (jedes Fenster einzeln, Maße, U-Wert, Anzahl).

Plausibilität sieht in jedem Gewerk anders aus. Deshalb: **ein gemeinsames Gerüst, in das jedes Gewerk seine eigene Fachlogik einhängt** — ein gemeinsames Skelett mit gewerksspezifischen Organen.

### Schicht 1 — Gemeinsames Gerüst (für ALLE Gewerke gleich)

Das, was alle Gewerke teilen:

- **Objekt-zentriertes Datenmodell:** Kunde → Objekt → Maßnahme(n) je Gewerk.
- **Die Kette als eine Zustandsmaschine:** Lead → Angebot → Auftrag → Auftragsbestätigung → Rechnung.
  - Jeder Übergang hat eine **Vorbedingung** (kein Auftrag ohne angenommenes Angebot).
  - Jeder Übergang hat eine **automatische Folge** (angenommenes Angebot erzeugt Auftrag, setzt Lead-Phase, benachrichtigt zuständigen MA, plant nächsten Schritt).
- **Eine Statusquelle**, nicht der Status verstreut über drei Tabellen.

Das ist der **höchste Hebel**, weil dieses Gerüst sich über alle Gewerke amortisiert. Es ist genau das, was die laufende Stabilisierung und das Datenmodell-Aufräumen leisten.

### Schicht 2 — Gewerks-Fachmodule (je Gewerk eigen)

Innerhalb jeder Maßnahme bringt jedes Gewerk sein eigenes Fachmodul mit, das in das gemeinsame Gerüst einspeist:

- Wärmepumpe → Heizlast + Auslegungslogik (DIN, VDI)
- PV → Dachflächen-/Ertragslogik, Mounting
- BEG / Förderung → Förderbetrag-Engine
- PLZ → Netzbetreiber-Zuordnung
- Fenster → Positions-/Aufmaßlogik
- Bad / Küche → Konfigurator (Positionen, Mengen, Varianten)

> Manche dieser Fachlogiken existieren in *playground* bereits (Energie-Rechner), manche müssen neu gebaut werden (Bad-Konfigurator, Fenster-Aufmaß). Alle hängen an **einem** Gerüst.

### Schicht 3 — Cross-Gewerk-Intelligenz (die Krönung)

Funktioniert erst, wenn die Gewerke in der gemeinsamen Struktur liegen und ihre Daten einspeisen:

- **Cross-Gewerk-Erkennung:** WP verkauft, Zählerschrank zu alt → „Elektro-Ertüchtigung nötig". PV geplant, alte Ölheizung → „Wärmepumpe + Förderung prüfen". Fenstertausch → „Heizlast neu berechnen, WP-Dimensionierung anpassen". Bad → „Heizung mitnehmen". Küche → „Elektro".
- **Gewerkeübergreifende Plausibilität / Abhängigkeit:** Wallbox braucht Anschlussleistungs-Prüfung (Elektro) *vor* Zusage. PV braucht tragfähiges, passend ausgerichtetes Dach (Dach-Gewerk als Vorbedingung).
- **Gemeinsame Disposition:** PV *und* WP am selben Objekt → Arbeiten bündeln (ein Gerüst, eine Anfahrt, ein Elektriker-Termin für beides).
- **Gesamt-Kalkulation / -Wirtschaftlichkeit** über alle Maßnahmen am Objekt, statt isolierter Einzelangebote.

**Voraussetzung für Schicht 3:** eine sauber strukturierte, maschinenlesbare **Wissensbasis** — welches Gewerk bedingt welches, welche Gebäudeeigenschaft löst welchen Vorschlag aus, welche Maßnahme erfordert welche Vorprüfung. Das ist genau das in 20 Jahren aufgebaute Expertenwissen, das kein Standard-CRM hat — und es ist die eigentliche Arbeit dieser Schicht.

---

## 3. Konkrete smarte Funktionen (geordnet)

### Leadmanagement
- Domänen-echtes Lead-Scoring aus *eigenen* Signalen (z. B. Süddach + alte Ölheizung + Eigentümer + guter Förderregion-PLZ = heißer Lead).
- Alterung / SLA: Leads, die zu lange in einer Phase liegen, werden markiert.
- Quellen-Attribution bis zum Auftragswert (welcher Kanal bringt Leads, die wirklich zu Aufträgen werden).
- Dublettenerkennung über mehrere Kanäle.

### Plausibilität als Tor (nicht als Nachkontrolle)
- WP-Angebot ohne hinterlegte Heizlast → blockiert / gewarnt.
- PV-Angebot ohne Dachfläche/Ausrichtung → unvollständig.
- BEG-Förderung beansprucht, Kriterien nicht erfüllt → Warnung.
- Angebotswert stark abweichend von vergleichbaren → Hinweis.

### Automation (folgt aus der Kette)
- An jedem Phasenübergang automatisch die Folgeaufgabe anlegen.
- Auftragsbestätigung automatisch erzeugen, wenn der Auftrag entsteht.
- Feinaufmaß-Termin automatisch vorschlagen, wenn Auftrag bestätigt.
- Netzbetreiber automatisch aus PLZ ziehen.
- Förderbetrag automatisch aus BEG-Engine füllen.
- Erinnerungs-Kadenz: kein Kontakt seit N Tagen → Aufgabe.

> Prinzip: Der Mensch trifft die **Entscheidung**, das System macht die **Folgearbeit**.

### Design / Sortierung
- Kundenakte als echte 360°-Ansicht: alles zu einem Kunden/Objekt auf einer Seite, als chronologische Zeitleiste.
- Rollenbasierte „was braucht heute meine Aufmerksamkeit"-Ansichten (Monteur: Termine/Aufgaben; Vertrieb: Pipeline mit alternden Leads; GF: Trichter).

---

## 4. Die zwingende Reihenfolge

Dies ist der wichtigste Teil des Dokuments. Die Vision ist richtig — aber sie hat eine nicht verhandelbare Reihenfolge.

1. **Stabilisierung** der genutzten Kernbereiche (läuft).
   Kaputte Kettenglieder reparieren: fehlende Controller-Methoden, der `users.name = employees.id`-Wurzelfehler, GET-Lösch-Routen, Rechte-Lücken. Status an *einer* Stelle.

2. **Objekt-zentriertes, sauber verknüpftes Datenmodell.**
   Kunde → Objekt → Maßnahme. Das gemeinsame Gerüst aus Schicht 1. Gilt für alle Gewerke gleich — höchster Hebel.

3. **Die Kette als eine Zustandsmaschine** mit Vorbedingungen und automatischen Folgen.

4. **Gewerks-Fachmodule** einhängen (Schicht 2) — gewerk für gewerk, teils aus playground übernommen, teils neu.

5. **Cross-Gewerk-Wissensbasis und -Erkennung** (Schicht 3) — die Krönung, zuletzt.

**Warum diese Reihenfolge zwingend ist:** Eine Zustandsmaschine oder Cross-Gewerk-Logik auf einem Datenmodell zu bauen, in dem `users.name` die `employee.id` enthält und der Status in drei Tabellen unterschiedlich steht, baut die Intelligenz auf Sand. Erst das Fundament, dann die Smartness — die Smartness wird *darauf* gebaut, nicht *daneben*.

---

## 5. Verhältnis zur aktuellen Arbeit

Die Stabilisierung ist **nicht** die langweilige Pflicht vor dem spannenden Teil. Sie **ist** der erste Schritt dieser Optimierung:

- Die Fix-Pakete reparieren genau die kaputten Kettenglieder, auf denen die Zustandsmaschine später aufsetzt.
- Das gemeinsame Gerüst, das für Fenster *und* Bad *und* Wärmepumpe gebraucht wird, ist genau das, was die Stabilisierung und das Datenmodell-Aufräumen legen.

---

## 6. Was JETZT NICHT gebaut wird

- Keine Zustandsmaschine bauen, solange die Kettenglieder noch kaputt sind.
- Keine Cross-Gewerk-Wissensbasis, solange das Datenmodell nicht objekt-zentriert und sauber ist.
- Keine neue große Baustelle aufmachen, während die Stabilisierung läuft.

**Aktueller Schritt:** P0-Block 2 (Rechte-Lücken) fertigstellen → restliche Stabilisierungs-Pakete (Datenverlust, Crashes) → operativer Seeder → objekt-zentriertes Datenmodell. *Dann* hat diese Vision einen Boden, auf dem sie trägt.

---

*Dieses Dokument ist die Landkarte. Es bleibt liegen, bis das Fundament steht — dann wird aus der stärksten Idee (gemeinsames Gerüst + Fach-Engines verbinden) ein konkreter Plan.*
