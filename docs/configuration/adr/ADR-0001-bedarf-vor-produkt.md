# ADR-0001 — Bedarf führt, Produkt folgt

**Status:** vorgeschlagen (Read-only-Analyse; Umsetzung slice-weise nach gesonderter Freigabe)
**Datum:** 2026-07-14 · **Heimat-App:** `ticket` · **Grundlage:** Masterentscheidung „Bedarfsgeführte Konfiguration, Auslegung und kanonisches Gebäudemodell"
**Bindet:** alle Konfiguratoren, Planungs-, Berechnungs- und Auslegungsmodule.

---

## Kontext (codebelegt, Ist-Stand `a00bb0a`)

Die Plattform besteht heute aus **zwei entkoppelten Welten**:

1. **Freistehende Energie-Rechner-Tools** (`app/Http/Controllers/Energie/*`): WR-Auslegung, WP-Auslegung, Heizlast, Sanierung, Energiekonzept, Fußboden-Check, Materialliste, PVGIS. **Produktzentriert** (Geräteauswahl `wp_index`/`module_index`/`inverter_index` als erste Eingabe, `EnergieAuslegungController.php:199,51-52`), **objektlos** (0 Referenzen auf `lead_alternative_adds`), **transient** (Rechen-Container werden per Cascade sofort gelöscht, `HeizlastController.php:171`).
2. **Objekt-gebundene Angebots-Pipeline** (`app/Http/Controllers/Customer/Offer/*` + `app/Services/Offer/*`): hängt sauber an `new_leads → lead_alternative_adds → lead_product_lists → offers`, ist aber durchgehend **read-only** und heute nur für das Gewerk WP (`product_id = 2`) verdrahtet (`KonfigurationsprojektService.php:83`, `OfferReadinessGate.php:24`).

Die bedarfsgeführte Architektur ist in Welt 2 als Lese-Modell bereits angelegt; die tatsächlichen Rechen-Einstiege (Welt 1) beginnen jedoch produktzentriert und ohne Objekt. Dazu existieren **mehrere Geometrie-Wahrheiten** (`anforderungsprofile.gebaeude_geometrie` führend; daneben `raum_geometrien`, `sanierungs_varianten.massnahmen`, `p_v_roof_plans.roof_structures`, `RoofAreaEstimator`).

## Entscheidung

**Bedarf führt. Produkt folgt.** Kein technischer Konfigurator und keine Auslegung beginnt standardmäßig produktzentriert. Der kanonische Workflow lautet:

> **Bedarf → Datenprüfung → Berechnung → Varianten → Filter → Auswahl → Freigabe → Angebot**

1. Kunde und Objekt bestimmen (Einstieg immer über bestehenden Datensatz — nie eigene Kunden-/Objekt-DB je Konfigurator).
2. Auftrag/Anfrage/Ziel erfassen; beauftragte Produktgruppen (`lead_product_lists`) aktivieren die erforderlichen Module dynamisch.
3. Vorhandene Kunden-/Objekt-/Formular-/Gebäudedaten übernehmen, Herkunft und Qualität prüfen, fehlende gezielt ergänzen.
4. Bedarf + Randbedingungen bestimmen, rechnen, **mehrere** Lösungsvarianten erzeugen.
5. Optionale Produkt-/Herstellerfilter anwenden, Empfehlung anzeigen, Nutzer wählt nachvollziehbar, technische Freigabe getrennt, Übergabe ins Angebot.

## Zulässige Ausnahme — Kunden-/Produktvorgabe

Eine ausdrückliche Produktvorgabe (Kundenwunsch, Gerätetausch, Rahmenvertrag, Ausschreibung, Bestand) wird als **Einschränkung/Filter** behandelt, **nicht** als technischer Eignungsnachweis. Das System prüft weiter: geeignet / eingeschränkt geeignet / ungeeignet / Daten unzureichend / bessere Alternative. **Eine Kunden- oder Vertriebsvorgabe darf keine technische Warnung unterdrücken.**

## Führende Datenwahrheiten (verbindlich)

| Sachverhalt | Führend | Nicht führend |
|---|---|---|
| Kunde/Objekt/Gewerk/Angebot | `new_leads` → `lead_alternative_adds` → `lead_product_lists` → `offers` | — |
| Bedarf/Randbedingungen/Ergebnisse | **`Anforderungsprofil`** (versioniert, append-only, Datenlage/Quelle je Wert) | Formularzustand, Browser/Session |
| Gebäudegeometrie | **`anforderungsprofile.gebaeude_geometrie`** (versioniert, objektgebunden) | SVG/three.js/PDF/LiDAR-Mesh/Punktwolke/Screenshot/Bounding-Box/KI-Vorschlag |
| Strukturiertes Auslegungsergebnis | versioniert am Profil bzw. eindeutig damit verbundene Ergebniseinheit | freies Textfeld, Geometrie-JSON, Angebot, Browser-State |
| Angebot | **Ausgabe/Snapshot** der gewählten + freigegebenen Variante | führende Berechnung/Geometrie/Auslegung |

## Filterregel

Filter (Hersteller, Serie, Technologie, Leistung, Maße, Schall, Preisstatus, Verfügbarkeit, …) sind **ausschließlich** Query-/View-State bzw. Einschränkung des berechneten Kandidatenraums. Filter sind **niemals** führende Anforderung, führende Berechnung, Angebotsposition, technische Freigabe oder Ersatz für fehlende Bedarfsdaten. Die Anwendung muss anzeigen, wenn ein Filter technisch bessere Lösungen ausblendet.

## Varianten / Ranking / Auswahl / Freigabe (vier getrennte Zustände)

- **Variante** = technisch mögliche Lösung. **Ranking** = Sortierung nach Kriterien. **Auswahl** = bewusste Nutzerentscheidung. **Freigabe** = technische/kaufmännische Bestätigung durch berechtigte Rolle.
- Rang 1 ist **nicht** automatisch ausgewählt, freigegeben oder angeboten. Jede Abweichung von der Empfehlung braucht eine auswählbare Begründung (Kundenwunsch/Preis/Verfügbarkeit/Lieferzeit/Kompatibilität/Montage/Lager/Rahmenvertrag/technisch/sonstig).

## Modulabhängigkeiten (Auszug)

WP benötigt Heizlast · PV benötigt validierte Dachgeometrie · Speicher benötigt Last-/Erzeugungsdaten · Wallbox benötigt Netz-/Last-/Nutzerdaten · Fenster benötigt Wandöffnung + bestätigtes Aufmaß · Fassade benötigt Außenwand-/Öffnungsflächen · Dach benötigt obere Gebäudekontur · Heizlast benötigt Räume/Bauteilflächen/thermische Eigenschaften. Änderung einer Eingabe setzt abhängige Ergebnisse auf `stale`.

## Konsequenzen

- **Positiv:** eine Objekt-/Gebäude-/Bedarfswahrheit; keine Doppelerfassung; nachvollziehbare Herkunft/Belastbarkeit; wiederverwendbare Services; klare Trennung Technik ↔ Kaufmännisches.
- **Aufwand:** die produktzentrierten Energie-Tools müssen bedarfsgeführt an Objekt/Anforderungsprofil angebunden werden; Parallel-Geometrien konsolidiert; ein Ergebnis-Stale-Mechanismus ergänzt; Controller-Fachlogik in Services extrahiert (läuft bereits, WP Stufe 3b); Rechte-/Ownership-Gates ergänzt.
- **Risiko bei Nicht-Umsetzung:** wachsende Parallelwahrheiten, Doppelerfassung, unbelastbare „exakte" Zahlen, produktgetriebene Fehlauslegung.

## Nicht-Ziele

Keine allgemeine BIM-Autorensoftware, keine Tragwerks-/Genehmigungsplanung, keine ungeprüfte KI-/LiDAR-Planung, keine automatische Bestell-/Förderzusage, keine erfundenen Preis-/Verfügbarkeitsdaten, keine parallelen Kunden-/Objekt-/Gebäude-DBs, kein Konfigurator außerhalb des ticket-Systems.

---

*Verweise: `docs/configuration/bedarfsgefuehrte-konfiguration-und-auslegung.md`, `docs/configuration/anforderungs-und-lueckenmatrix.md`, `docs/configuration/modul-und-abhaengigkeitsmatrix.md`, `docs/configuration/umsetzungsfahrplan.md`, `docs/configuration/vollstaendigkeitsbericht.md`. Bindet BETRIEBSORDNUNG.md/CLAUDE.md; bei Konflikt gelten diese.*
