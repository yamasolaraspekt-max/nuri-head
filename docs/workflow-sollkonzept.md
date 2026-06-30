# Workflow-Sollkonzept — ticket als gewerkeübergreifendes Vorgangs-System

**Status:** Soll-Beschreibung / Landkarte. **Kein Bauauftrag.**
**Zweck:** Festhalten, *wie* der Kernprozess funktionieren soll — universal über alle Gewerke, mit der gewerkespezifischen Tiefe, die ein Vollsanierer braucht.
**Erarbeitet mit:** Yama (Solar Aspekt). **Stand:** Juni 2026.

> **Wichtiger Hinweis zur Lesart:** Die gewerkespezifischen Teile (Abschnitt 8) sind ein fachlich fundierter *Rahmen*. Die konkreten Felder, Schwellenwerte und Förderkriterien gehören vom jeweiligen Meister / Fachverantwortlichen bestätigt und ergänzt — Förder- und Normwerte ändern sich, und nur die Fachleute im Betrieb kennen die gelebte Praxis. Das System soll diese Inhalte *pflegbar* halten, nicht fest verdrahten.

---

## 0. Einordnung — warum dieses Dokument

Bisher lebt der Prozess „wie Solar Aspekt arbeitet" verstreut: im Kopf von Yama, in gewachsenem Code, in einzelnen Formularen. Dieses Dokument führt ihn zum ersten Mal an einer Stelle zusammen — als *Ziel*, an dem entlang gebaut wird.

Es ist die Antwort auf eine einfache Frage: **Wie soll ein Vorgang — von der ersten Anfrage bis zum Archiv — durch ticket laufen, egal ob es um eine PV-Anlage, eine Wärmepumpe, neue Fenster oder eine Badsanierung geht?**

Es ist *nicht* der nächste Bauschritt. Der Weg dorthin führt zwingend über zwei Vorarbeiten, die in eigenen Dokumenten liegen: die **Begriffs-Konsolidierung** (was bedeuten Kunde, Objekt, Gewerk, Projekt, Auftrag eindeutig — `docs/begriffs-bestandsaufnahme.md`) und die **Architektur-Entscheidungen** (`architektur-entscheidungen.md`). Erst wenn die Sprache klar ist, kann dieser Workflow sauber gebaut werden.

---

## 1. Das Grundprinzip: ein Rahmen, gewerkespezifisch gefüllt

Solar Aspekt ist Komplettanbieter ums Gebäude. Das verlangt zwei Dinge, die sich scheinbar widersprechen:

- **Einheitlichkeit:** Jeder Mitarbeiter, egal aus welchem Gewerk, soll sich im selben System sofort zurechtfinden. Ein Vorgang ist ein Vorgang.
- **Fachtiefe:** Ein PV-Vorgang braucht völlig andere Daten als eine Badsanierung. Der Elektromeister denkt in Anschlussleistung, der Dachdecker in Tragfähigkeit, der Schreiner in Raumaufmaß.

Die Auflösung ist eine **Drei-Schichten-Architektur** (sie ist dieselbe wie im gewerkeübergreifenden Zielbild, hier auf den Workflow angewandt):

1. **Gemeinsames Gerüst** — gilt für *alle* Gewerke gleich: die Phasen, die Aufnahme-Felder, die Zuständigkeits-Ableitung, Zustand und Historie. Das ist der Rahmen, in dem sich jeder zurechtfindet.
2. **Gewerks-Fachmodule** — je Gewerk eigen: die Qualifizierungs-Formulare, die Fachdaten, die Planungstools, die Plausibilitätsregeln. Das ist die Fülle, die der Fachmann braucht.
3. **Cross-Gewerk-Intelligenz** — die Verbindungen zwischen den Gewerken am selben Objekt: Fenstertausch ändert die Heizlast, Wallbox braucht Elektro-Prüfung, PV braucht Dach-Statik.

Das **Smart Routing** ist das Bindeglied zwischen Schicht 1 und 2: Es führt den Nutzer aus dem gemeinsamen Rahmen automatisch in das richtige Fachmodul. Wähle PV → bekomme die PV-Welt. Wähle Fenster → bekomme die Fenster-Welt.

---

## 2. Die drei Dimensionen jedes Vorgangs — sauber getrennt

Der wichtigste konzeptionelle Punkt des ganzen Dokuments: Ein Vorgang hat **drei verschiedene Arten von Zustand**, die heute im System vermischt sind (Quelle der ~11 Status-Felder) und sauber getrennt gehören.

### Dimension A — die PHASE: *Wo* im Prozess steht der Vorgang?
Eine einzige, lineare Hauptachse (Abschnitt 3). Ein Vorgang ist zu jedem Zeitpunkt an *genau einer* Phase. Antwort auf: „Wie weit sind wir mit Müllers PV?"

### Dimension B — der ZUSTAND: *Wie* läuft der Vorgang an dieser Phase?
Quer zur Phase. Antwort auf: „Was ist gerade los?"
- **aktiv** — wird bearbeitet
- **pausiert** — bewusst angehalten
- **zurückgestellt** — vom Kunden/Betrieb verschoben
- **Wiedervorlage** — wartet auf einen Termin (nachfassen), mit Datum
- **gewonnen** — Zusage erhalten
- **verloren** — Absage (mit Verlustgrund)

Ein Vorgang kann „in der Beratungsphase, Zustand Wiedervorlage am 15." sein. Phase und Zustand zusammen beschreiben ihn eindeutig.

### Dimension C — die HISTORIE: *Was* ist passiert?
Keine Status, sondern Dokumentation, die an jedem Vorgang in jeder Phase hängt:
- **Notizen** (frei, jederzeit)
- **Berichte je Phase** (z. B. Gesprächsbericht nach dem Verkaufstermin)
- **Kommunikation** (E-Mail, Telefon, Termine)
- **Wiedervorlagen** (Aufgaben mit Datum)
- **Dokumente** (Angebote, Pläne, Fotos, Unterschriften)

### Plus: PFLICHTDATEN je Phase (das Plausibilitätstor)
Bestimmte Phasen verlangen, dass bestimmte Daten vollständig sind, bevor man weitergehen darf. *Das* ist die Plausibilität, die einen unseriösen Sprung verhindert (z. B. kein PV-Angebot ohne Dachdaten). Die Pflichtdaten je Phase sind **gewerkespezifisch** — sie kommen aus den Fachmodulen (Abschnitt 8) und werden über die Feld-Priorität definiert (Abschnitt 6).

---

## 3. Das universelle Phasenmodell

Eine Hauptachse für alle Gewerke. *Welche* Phasen ein Vorgang durchläuft, hängt von der Dienstleistungsart ab (Abschnitt 4); *was* in einer Phase zu tun ist, vom Gewerk (Abschnitt 8).

| # | Phase | Zweck | Vorbedingung (Tor) | Ausgang |
|---|-------|-------|--------------------|---------|
| 1 | **Anfrage** | Kontakt erfassen, Interesse festhalten | Adresse + E-Mail/Telefon + Produkt-/DL-Interesse | wird zu Kunde+Objekt+Vorgang |
| 2 | **Qualifizierung** | Die fachlichen Daten sammeln, die man zum Planen/Anbieten braucht | Mindest-Kontaktdaten vorhanden | Pflicht-Fachdaten je Gewerk vollständig |
| 3 | **Beratung / Verkauf** | Planung, Angebot, Beratungsgespräch | **Gewerk-Pflichtdaten vollständig** (Tor!) | Angebot liegt vor |
| 4 | **Entscheidung** | Kunde sagt zu oder ab | Angebot zugestellt | Zusage → 5; Absage → verloren |
| 5 | **Auftrag** | Beauftragung, Auftragsbestätigung, Feinaufmaß | Angebot angenommen *(GR — s. Architektur-Frage 2)* | Auftrag bestätigt |
| 6 | **Montage / Ausführung** | Die eigentliche Leistung erbringen | Auftrag + Material + Termin | Leistung erbracht |
| 7 | **Abnahme** | Übergabe, Abnahmeprotokoll, ggf. Mängel | Montage fertig | Abnahme erfolgt |
| 8 | **Abschluss** | Schlussrechnung, Abschlussbericht, Doku | Abnahme erfolgt | abgerechnet & dokumentiert |
| 9 | **Archiv** | Vorgang ruht, bleibt auffindbar | Abschluss erfolgt | — |

**Zwischenschritte** (pausieren, zurückstellen, nachfassen, Angebot ändern) sind **keine eigenen Phasen** — sie sind *Zustände* (Dimension B) innerhalb einer Phase. „Nach Verkaufsgespräch nachfassen" = Phase *Beratung*, Zustand *Wiedervorlage*, mit Datum. „Angebot ändern" = bleibt in Phase *Beratung*, erzeugt eine neue Angebotsversion in der Historie. Das hält die Hauptachse sauber und trotzdem den Alltag abbildbar.

---

## 4. Dienstleistungsart als Weiche

Quer zum Gewerk steht die **Dienstleistungsart**. Sie bestimmt, *welche* Phasen ein Vorgang überhaupt durchläuft. Yamas Liste: **Komplettlösung, Verkauf, Montage, Planung, Reparatur, Wartung, Sonstiges.**

Grobe Einordnung (⚠️ **von Yama zu bestätigen** — siehe offene Punkte):

| Dienstleistungsart | Typischer Phasen-Pfad |
|--------------------|------------------------|
| **Komplettlösung** | voller Pfad 1→9 (Anfrage … Archiv) |
| **Verkauf** | 1→5, Ausführung evtl. extern/separat |
| **Montage** | verkürzt: Auftrag → Montage → Abnahme → Abschluss (Qualifizierung/Beratung entfallen, wenn extern verkauft) |
| **Planung** | Anfrage → Qualifizierung → Planung als Leistung → Abschluss |
| **Reparatur** | kurz: Anfrage → (Kurz-Diagnose) → Termin → Ausführung → Abschluss |
| **Wartung** | kurz: Anfrage/Vertrag → Termin → Ausführung → Abschluss; oft wiederkehrend |
| **Sonstiges** | frei |

**Entscheidung (offen):** Ist das *ein* Workflow mit überspringbaren Phasen — oder mehrere Workflow-Typen? Empfehlung im Konzept: **ein** universelles Phasenmodell, bei dem die Dienstleistungsart definiert, welche Phasen *aktiv/übersprungen* sind. Das hält das System einheitlich (jeder findet sich zurecht) und trotzdem flexibel. Eine Wartung zeigt dann schlicht nur die für sie relevanten Phasen.

---

## 5. Die intelligente Aufnahme (Intake)

Schon heute ist die Anfrage-Aufnahme klüger als ein Standardformular — das ist ein **Aktivposten**, der erhalten und ausgebaut gehört. Bei der Aufnahme werden erfasst:

**Produkt / Dienstleistung · Abteilung · Innendienst / Außendienst · Termin · Priorität · Realisierungszeitraum.**

Das Besondere: **Vieles wird automatisch abgeleitet, nicht eingetippt.** Die Kausalitätskette:

```
Produkt/Dienstleistung gewählt
        │
        ▼
  Abteilung (automatisch — Produkte sind Abteilungen zugeordnet)
        │
        ▼
  Innendienst / Außendienst (innerhalb der Abteilung)
        │
        ▼
  Terminkalender des Außendienstes (automatisch verknüpft)
        │
        ▼
  + Priorität + Realisierungszeitraum (gewählt)
```

Das ist das gleiche Prinzip wie das Smart Routing (Abschnitt 6): *Was* der Kunde will, bestimmt *wer* zuständig ist und *welche* Daten/Tools dranhängen. Diese Kette soll erhalten, gehärtet und auf die Formulare ausgeweitet werden.

⚠️ **Offen:** Funktioniert die Auto-Ableitung heute schon zuverlässig (Aktivposten) oder hakt sie (Baustelle)? — von Yama zu bestätigen.

---

## 6. Smart Routing + gewerkespezifische Formulare mit Feld-Priorität

### Smart Routing
Für jedes Gewerk existieren eigene Formulare. Das System lotst den Nutzer automatisch zum richtigen Satz: PV gewählt → nur PV-Formulare; Fenster → nur Fenster-Formulare. Der Nutzer kämpft sich nicht durch irrelevante Felder, sondern wird zielgenau geführt.

### Feld-Priorität / Rangliste
Innerhalb eines Formulars sind die Felder **nicht gleichwertig**, sondern nach Wichtigkeit gerankt. Bedeutung (von Yama bestätigt): **die wichtigsten Felder zuerst, sie bestimmen Anordnung und Vollständigkeit.**

Die Rangliste hat eine doppelte Funktion:
1. **Führung** — die kritischen Felder stehen oben, der Nutzer erfasst zuerst das Wichtige.
2. **Plausibilitätstor** — die hochpriorisierten (Pflicht-)Felder müssen gefüllt sein, bevor der Vorgang die Phase wechseln darf (z. B. von *Qualifizierung* nach *Beratung/Angebot*). Die niedrigpriorisierten dürfen leer bleiben.

Damit *ist* die Feld-Rangliste die konkrete Definition der „Pflichtdaten je Phase" aus Abschnitt 2. Sie ist das Werkzeug, mit dem die Plausibilität durchgesetzt wird — und sie ist **pflegbar** (der Meister legt fest, welche Felder kritisch sind).

> *Mögliche Erweiterung (offen):* Die Priorität zusätzlich als **Qualifizierungs-Score** nutzen — je mehr hochpriorisierte Felder gefüllt, desto „reifer" der Lead. Das würde die Pipeline-Bewertung und den Nachfragewert speisen. Vorerst als Option vermerkt.

---

## 7. Datenfluss in die Fachtools + automatische Ableitungen

Das Prinzip, das ticket von jedem Standard-CRM abhebt: **Eine Information wird einmal erfasst und fließt dann durch das ganze System — niemand gibt sie zweimal ein, und das System leitet aus ihr fachlich Folgerichtiges ab.**

Yamas Beispiel, ausbuchstabiert:

```
Qualifizierungs-Formular PV: Dachform erfasst (z. B. Satteldach, Ziegeleindeckung)
        │
        ├──► fließt automatisch ins PV-Planungstool (keine Neueingabe)
        │
        └──► löst im Hintergrund eine fachliche Ableitung aus:
                 Dachform + Eindeckung  →  passendes Montagesystem
                 (z. B. Ziegeldach → Aufdach-Schienensystem + passender Dachhaken-Typ)
```

Das ist **Kausalität** (eine Eingabe bestimmt eine Folge) **plus Plausibilität** (das abgeleitete Montagesystem *passt* technisch zum Dach) **plus Effizienz** (keine Doppelarbeit). Dieses Prinzip gilt nicht nur für PV — es ist die Bauanleitung für *jedes* Gewerk (Abschnitt 8): erfasste Fachdaten fließen in das jeweilige Planungstool und treiben automatische, fachlich korrekte Ableitungen.

---

## 8. Die Gewerke im Detail — aus der Sicht des jeweiligen Fachmanns

Jeder Abschnitt beschreibt: die **Perspektive** des Gewerks, die **Qualifizierungsdaten** (grob nach Priorität), die **Fachtools**, die **automatischen Ableitungen**, die **Plausibilitätsregeln** (Tore) und die **Cross-Gewerk-Verbindungen**.

⚠️ Alle konkreten Felder/Schwellen/Förderwerte sind ein Vorschlag-Rahmen — vom jeweiligen Meister zu bestätigen und zu vervollständigen.

---

### 8.1 Photovoltaik — aus Sicht des PV-Planers

**Perspektive:** „Passt eine Anlage aufs Dach, was bringt sie, was kostet das Montagesystem, und hängt da noch Speicher/Wallbox/Wärmepumpe dran?"

**Qualifizierungsdaten (hoch → niedrig priorisiert):**
- *(hoch, Pflicht)* Dachform (Sattel-, Walm-, Pult-, Flach-, Krüppelwalmdach …), Dachausrichtung (Azimut), Dachneigung, nutzbare Dachfläche
- *(hoch, Pflicht)* Dacheindeckung (Ziegel, Trapezblech, Bitumen, Schiefer …) — bestimmt das Montagesystem
- *(hoch, Pflicht)* Jahresstromverbrauch (kWh), grobes Lastprofil (Tag/Nacht, Schichtarbeit)
- *(mittel)* Verschattung (Bäume, Nachbargebäude, Gauben, Kamine), Statik/Sparrenabstand
- *(mittel)* Zustand Zählerschrank, Netzanschluss, Zählerplatz → **Übergabe an Elektro**
- *(niedrig, optional)* Speicherwunsch, E-Auto/Wallbox geplant, Wärmepumpe vorhanden/geplant, Notstromwunsch

**Fachtools:** 3D-Dachplaner (Module belegen, Verschattung, Strings), Ertragssimulation (Standort/Ausrichtung/Neigung → kWh/Jahr), Wirtschaftlichkeit (Eigenverbrauch, Einspeisung, Amortisation).

**Automatische Ableitungen:**
- Dachform + Eindeckung → **Montagesystem** (Aufdach/Indach, Schiene, Dachhaken-Typ)
- Dachfläche + Modulmaß → max. Modulzahl → kWp
- kWp + Verbrauch → Wechselrichter-Dimensionierung, Speicher-Empfehlung
- PLZ → **Netzbetreiber** (für Anmeldung/MaStR)

**Plausibilitätstore:** Kein Angebot ohne Dachfläche + Ausrichtung + Verbrauch. Wechselrichterleistung muss zur Modulleistung passen. Speichergröße plausibel zum Verbrauch.

**Cross-Gewerk:** Dachdecker (Tragfähigkeit, Eindeckung, ggf. Sanierung vor Belegung), Elektro (Zählerschrank, AC-Anschluss, Netzanmeldung), Wallbox/Speicher, Wärmepumpe (Eigenstromnutzung).

---

### 8.2 Wärmepumpe & Heizung — aus Sicht des Heizungsmeisters und Energieberaters

**Perspektive:** „Welche Heizlast hat das Gebäude, welche Wärmepumpe deckt sie bei Norm-Außentemperatur, passt die Vorlauftemperatur zu den Heizflächen, und welche Förderung greift?"

**Qualifizierungsdaten (hoch → niedrig):**
- *(hoch, Pflicht)* Gebäudedaten: Baujahr, beheizte Wohnfläche, Geschosse, Bauweise
- *(hoch, Pflicht)* Gebäudehülle / Dämmstandard (Wand, Dach, Keller, Fenster) — Basis der Heizlast
- *(hoch, Pflicht)* bestehende Heizung (Öl/Gas/…, Alter, Leistung), bisheriger Verbrauch
- *(hoch, Pflicht)* Heizflächen: Heizkörper vs. Fußbodenheizung → bestimmt mögliche Vorlauftemperatur
- *(mittel)* Warmwasserbedarf (Personen, Komfort), Aufstellort (innen/außen), Schallschutz zum Nachbarn
- *(mittel)* Stromanschluss / Zählerschrank → **Übergabe an Elektro** (§14a EnWG steuerbare Last)
- *(niedrig)* PV vorhanden/geplant (Eigenstrom für WP), Pufferspeicher, Hybrid

**Fachtools:** Heizlastberechnung (DIN EN 12831 — Yamas *wberechnung*), WP-Auslegung mit JAZ/BIN-Simulation (VDI 4650), Wirtschaftlichkeit + BEG/BAFA-Förderung (Vorher/Nachher).

**Automatische Ableitungen:**
- Gebäudehülle + Fläche + Norm-Außentemperatur → **Heizlast**
- Heizlast + Vorlauftemperatur → **WP-Dimensionierung** → passendes Modell aus Produktdatenbank (Kennfeld/COP je Modell, DIN EN 14511)
- Maßnahme + Gebäude → **Förderbetrag** (BEG-EM; aktuelle Sätze pflegbar halten)
- Heizflächen → max. sinnvolle Vorlauftemperatur → JAZ-Prognose

**Plausibilitätstore:** Kein Angebot ohne Heizlast. WP-Leistung muss Heizlast bei Norm-Außentemperatur decken. Vorlauftemperatur muss zu den Heizflächen passen (Warnung bei alten Heizkörpern + niedriger VL). Förderkriterien (Gebäudealter/Maßnahme) erfüllt.

**Cross-Gewerk:** Elektro (Anschlussleistung, Zählerschrank, §14a), PV (Eigenstrom senkt Betriebskosten → bessere Wirtschaftlichkeit), Fenster/Dämmung (verbesserte Hülle → *niedrigere* Heizlast → kleinere WP → **Heizlast neu rechnen, wenn Fenster im selben Vorhaben**), Sanitär (Warmwasser, Einbindung).

---

### 8.3 Elektro — aus Sicht des Elektromeisters

**Perspektive:** „Trägt der Hausanschluss die neue Last, ist der Zählerschrank normgerecht, und was muss angemeldet werden?" — Elektro ist der **Enabler** für PV-AC, Wallbox und Wärmepumpe.

**Qualifizierungsdaten (hoch → niedrig):**
- *(hoch, Pflicht)* Zählerschrank: Alter, Zustand, VDE-Konformität, freie Plätze
- *(hoch, Pflicht)* Hausanschluss / verfügbare Netzanschlussleistung, vorhandene Absicherung
- *(hoch)* gewünschte Maßnahme(n): Wallbox, PV-AC, Speicher, Zählerschranktausch, §14a-steuerbare Lasten, Smart Home
- *(mittel)* Leitungswege, Zählerplatz, Potentialausgleich/Erdung
- *(niedrig)* Lastmanagement-Wunsch, Zukunftsplanung (weitere Wallboxen)

**Fachtools:** Anschlussleistungs-/Lastprüfung, Zählerschrank-Konfigurator, Netzbetreiber-Anmeldung (PLZ → VNB).

**Automatische Ableitungen:** Geplante Lasten (WP + Wallbox + …) → benötigte Anschlussleistung → Abgleich mit verfügbarer → Ampel „reicht / Ertüchtigung nötig". PLZ → Netzbetreiber → Anmeldepflichten.

**Plausibilitätstore (besonders wichtig — Enabler-Funktion):** Wallbox/WP dürfen nicht zugesagt werden, ohne dass die **Anschlussleistungs-Prüfung** vorliegt. Zählerschrank muss normgerecht sein, bevor PV-AC/Wallbox angeschlossen wird. → Diese Tore verhindern genau die teuren Pannen, „etwas verkauft zu haben, das die Elektrik nicht trägt".

**Cross-Gewerk:** *Praktisch alle.* PV (AC-Seite, Anmeldung), Wärmepumpe (Anschluss, §14a), Wallbox/E-Mobilität, Speicher, Schreiner/Küche (Geräteanschlüsse).

---

### 8.4 Fenster & Bauelemente — aus Sicht des Bauelement-Monteurs

**Perspektive:** „Jedes Element einzeln aufmessen, richtige Bauart und Verglasung, saubere Einbausituation — und wie wirkt sich das auf Förderung und Gebäudehülle aus?"

**Qualifizierungsdaten (hoch → niedrig):**
- *(hoch, Pflicht)* Aufmaß **je Element** (Breite × Höhe), Anzahl, Position
- *(hoch, Pflicht)* Bauart (Kunststoff/Holz/Alu/Holz-Alu), Verglasung (2-/3-fach, U-Wert), Öffnungsart
- *(hoch)* Einbausituation: Neubau / Renovierung / RAL-Montage; Anschlag; Laibung
- *(mittel)* Rollladen/Raffstore, Insektenschutz, Haustür/Nebeneingang, Sonderformen
- *(mittel)* Denkmalschutz / Auflagen
- *(niedrig)* Farbe, Griffe, Sicherheitsausstattung (RC-Klasse)

**Fachtools:** Aufmaß-Tool (Liste je Element mit Maßen/Typ), Element-Konfigurator, Förderprüfung.

**Automatische Ableitungen:** U-Wert + Maßnahme → **Förderfähigkeit** (BEG Einzelmaßnahme Gebäudehülle, Werte pflegbar). Verbesserte Hülle → Auswirkung auf **Heizlast** (→ Cross zu WP). Elementliste → Materialliste/Kalkulation.

**Plausibilitätstore:** Aufmaß je Element Pflicht vor Angebot (kein „Pauschalangebot ohne Maße"). U-Wert hinterlegt, wenn Förderung beansprucht.

**Cross-Gewerk:** Wärmepumpe/Energieberatung (bessere Fenster → geringere Heizlast → WP neu dimensionieren, wenn gemeinsames Vorhaben), Maler (Laibung/Innenanschluss nach Einbau), Rollladen → Elektro (Motoren), Dachdecker (Dachfenster).

---

### 8.5 Dachdecker — aus Sicht des Dachdeckers

**Perspektive:** „Welche Dachform und Eindeckung, in welchem Zustand, trägt das Dach die PV-Last, und muss vor der Belegung saniert/gedämmt werden?"

**Qualifizierungsdaten (hoch → niedrig):**
- *(hoch, Pflicht)* Dachform, Dachfläche, Neigung, Eindeckung, Aufbau/Unterkonstruktion
- *(hoch, Pflicht für PV)* Statik / Tragfähigkeit, Sparrenabstand, Zustand der Eindeckung
- *(mittel)* Dämmung (Aufsparren/Zwischensparren), Abdichtung (Flachdach), Entwässerung
- *(mittel)* Durchdringungen (Kamin, Gauben, Dachfenster), Absturzsicherung/Gerüstbedarf
- *(niedrig)* Begrünung, Sonderdetails

**Fachtools:** Dachaufmaß, **gemeinsamer 3D-Dachplaner mit PV** (dieselbe Geometrie — Dach und PV planen am selben Modell), Sanierungs-/Dämmkalkulation.

**Automatische Ableitungen:** Dachform/Eindeckung → **PV-Montagesystem** (geteilt mit 8.1!). Zustand + Alter → Sanierungsbedarf-Hinweis vor PV. Dämmmaßnahme → Auswirkung auf **Heizlast** (Cross zu WP) und **Förderung**.

**Plausibilitätstore:** Statik/Tragfähigkeit muss geprüft sein, bevor PV aufs Dach zugesagt wird. Eindeckungszustand bestimmt, ob vor PV saniert werden muss.

**Cross-Gewerk:** PV (Tragfähigkeit, Eindeckung → Montagesystem, gemeinsame Geometrie — der wichtigste Cross-Link im Betrieb!), Energieberatung (Dachdämmung → Heizlast), Gerüst (gemeinsam mit anderen Gewerken nutzbar).

---

### 8.6 Schreiner / Innenausbau & Küche — aus Sicht des Schreiners

**Perspektive:** „Raum genau aufmessen, Möbel/Küche planen, und sind die nötigen Anschlüsse da?" — stärker konfigurativ und geschmacksgetrieben als die normbasierten Gewerke.

**Qualifizierungsdaten (hoch → niedrig):**
- *(hoch, Pflicht)* Raumaufmaß / Grundriss, Decken-/Wandbeschaffenheit
- *(hoch)* Art des Vorhabens (Küche, Einbaumöbel, Innenausbau, Sonderbau), Materialwunsch
- *(mittel)* Elektro-/Wasser-/Abwasser-Anschlüsse, Geräteliste (Küche)
- *(mittel)* Stilrichtung, Fronten, Arbeitsplatte, Beschläge
- *(niedrig)* Beleuchtung, Sonderwünsche

**Fachtools:** Küchen-/Möbelplaner (CAD/3D), Raumaufmaß, Konfigurator (Fronten/Materialien/Geräte).

**Automatische Ableitungen:** Geräteliste → benötigte **Anschlüsse** (Cross zu Elektro/Sanitär). Maße + Konfiguration → Material-/Korpusliste → Kalkulation.

**Plausibilitätstore:** Aufmaß vorhanden. Bei Küche: Anschluss-Situation geprüft (sonst böse Überraschung bei Montage).

**Cross-Gewerk:** Elektro (Geräte-/Beleuchtungsanschlüsse), Sanitär (Spüle/Geschirrspüler), Maler (Wände vor Möbeleinbau), Bauelemente (Türen).

---

### 8.7 Maler & Fassade — aus Sicht des Malers

**Perspektive:** „Welche Flächen, welcher Untergrund, welche Vorarbeiten — und geht es nur um Optik oder auch um die Fassade/Dämmung?"

**Qualifizierungsdaten (hoch → niedrig):**
- *(hoch, Pflicht)* Flächen (Wand/Decke/Fassade) in m², Anzahl Räume
- *(hoch)* Untergrund/Zustand, nötige Vorarbeiten (Spachteln, Grundierung, Altanstrich)
- *(mittel)* innen/außen, Fassade mit/ohne **WDVS-Dämmung**, Gerüstbedarf
- *(niedrig)* Farbton, Technik (Streichen/Sprühen), Sonderflächen

**Fachtools:** Flächenaufmaß, Material-/Verbrauchsrechner.

**Automatische Ableitungen:** Fläche + Vorarbeiten → Material/Zeit/Kalkulation. WDVS → Auswirkung auf **Heizlast** und **Förderung** (Cross zu WP/Energieberatung).

**Plausibilitätstore:** Flächenaufmaß vorhanden; Untergrund/Vorarbeiten geklärt (sonst Kalkulationsrisiko).

**Cross-Gewerk:** Fenster (Laibung/Innenanschluss nach Fenstertausch), Fassadendämmung → Energieberatung/WP, Gerüst (gemeinsam mit Dach/PV/Fassade).

---

### 8.8 Speicher · Wallbox · E-Mobilität — verbundene Produkte

Meist *Anbau* an PV (Speicher, Wallbox) oder Elektro (Wallbox), selten allein.

**Kerndaten:** Speicher → Verbrauch + PV-Größe + Autarkiewunsch. Wallbox → Ladeleistung, Fahrzeug, Anschlussleistung (**Elektro-Prüfung Pflicht**), Lastmanagement, Förderfähigkeit.

**Cross-Gewerk:** PV (Speicher dimensioniert aus Anlage+Verbrauch), Elektro (Wallbox-Anschluss, §14a, Lastmanagement), Wärmepumpe (gemeinsames Lastmanagement im Haus).

---

## 9. Cross-Gewerk-Intelligenz — die dritte Schicht

Hier entsteht der eigentliche Vorsprung des Vollsanierers. Die Gewerke hängen am **selben Objekt**, und das System macht ihre Abhängigkeiten sichtbar — statt sie als getrennte Verkäufe zu behandeln.

**Erkennungs-/Vorschlagslogik (Beispiele):**
- WP geplant + alter Zählerschrank → „Elektro-Ertüchtigung nötig"
- PV geplant + alte Ölheizung → „Wärmepumpe + Förderung prüfen"
- Fenstertausch *im selben Vorhaben* → „Heizlast neu rechnen, WP ggf. kleiner dimensionieren, Förderung neu prüfen"
- Wallbox angefragt → „Anschlussleistungs-Prüfung (Elektro) vor Zusage"
- PV aufs Dach → „Tragfähigkeit (Dachdecker) prüfen; Eindeckung bestimmt Montagesystem"

**Gemeinsame Disposition:** Mehrere Gewerke am selben Objekt → Arbeiten/Termine bündeln (ein Gerüst für Dach + PV + Fassade; ein Elektriker-Termin für PV-AC + Wallbox).

**Gesamt-Sicht:** Über das Objekt eine zusammengeführte Wirtschaftlichkeit/Förderung über alle Maßnahmen — das ist die „aus einer Hand"-Sicht, die eure USP ist.

**Voraussetzung:** Diese Schicht funktioniert nur, wenn (a) mehrere Gewerke am *selben Objekt* hängen (Erfassungs-Workflow!) und (b) die Fachdaten in der gemeinsamen Struktur liegen. Deshalb steht sie *zuletzt* in der Baureihenfolge.

---

## 10. Offene Entscheidungen (zu klären, bevor gebaut wird)

1. **Dienstleistungsart-Pfade:** Wartung/Reparatur als eigener kurzer Pfad oder als universelles Modell mit übersprungenen Phasen? *(Empfehlung: ein Modell, Phasen je DL-Art aktiv/inaktiv.)*
2. **Auto-Ableitung (Produkt→Abteilung→Kalender):** funktioniert heute zuverlässig oder Baustelle?
3. **Formulare & Tools:** existieren die gewerkespezifischen Formulare/Planungstools schon (dann fehlt „nur" sauberes Smart Routing) — oder sind sie selbst noch in Arbeit (größeres Vorhaben)?
4. **Feld-Priorität:** zusätzlich als Qualifizierungs-Score nutzen (Pipeline-Reife/Nachfragewert), oder nur als Anordnung+Pflichttor?
5. **Phasen-/Zustands-Werte:** die konkreten Listen (Phasen, Zustände, Verlustgründe) als feste, pflegbare Stammdaten festlegen (ersetzt die heutigen Frei-Text-Status).
6. **Pro Gewerk:** welche Felder sind *wirklich* Pflicht (Tor) — vom jeweiligen Meister zu bestätigen.

---

## 11. Verhältnis zu den anderen Dokumenten & die Baureihenfolge

Dieses Konzept ist das **Ziel**. Der Weg dorthin ist gestaffelt und darf nicht übersprungen werden:

1. **Begriffe konsolidieren** (`begriffs-bestandsaufnahme.md` → verbindliches Glossar). Ohne klare Begriffe kein sauberer Workflow.
2. **Architektur-Entscheidungen treffen** (`architektur-entscheidungen.md`): Statusquelle, Rechnungssystem, Projekt vs. Auftrag, Angebot-Pflicht. Diese legen fest, *worauf* der Workflow läuft.
3. **Struktur ausrichten:** Kunde → Objekt → (Gewerk-Vorgänge), Mehrfachheit (mehrere Objekte je Kunde, mehrere Gewerke je Objekt) im Erfassungs-Workflow ermöglichen; Phase/Zustand/Historie sauber trennen.
4. **Dann Stück für Stück bauen:** universelles Phasenmodell → Intake-Automatik härten → Smart Routing → Fachmodule je Gewerk → zuletzt Cross-Gewerk-Intelligenz.

Verwandte Dokumente: `zielbild-objekt-zentriertes-crm.md` (das große Warum), `cockpit-inventur.md` / Controlling-Dokumente (die Auswertungs-Schicht, die auf sauberem Status + Umsatz aufbaut), `workflow-analyse.md` (die heutigen Schwächen, die dieser Soll-Prozess auflöst).

---

*Dies ist die Soll-Landkarte des Prozesses. Sie wird gebaut, wenn das Fundament (Begriffe, Architektur-Entscheidungen, Struktur) steht — nicht vorher. Die gewerkespezifischen Inhalte gehören von den jeweiligen Meistern bestätigt und gepflegt; das System hält sie pflegbar, statt sie fest zu verdrahten.*
