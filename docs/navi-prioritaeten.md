# Navi-Priorisierung — Vorschlag für die 95 geplanten Flächen

**Datum:** 2026-07-16 · Basis: alle „· geplant"-Einträge der Sidebar (95, per Skript extrahiert — vollständig).
**Dein Schritt 2:** sortieren → priorisieren → oben platzieren → abarbeiten. Das hier ist mein Vorschlag; du streichst/verschiebst, dann setze ich die Reihenfolge in der Navi um (Wichtiges oben je Sektion).

**Die drei Stufen:**
- **A — jetzt (14):** bringt sofort Geld/Ordnung ODER der Code liegt schon im Haus (Port statt Neubau) ODER Gesetz verlangt es.
- **B — nächste Stufe (41):** wichtig, baut auf A auf.
- **C — später (40):** sinnvoll, aber verdrängt nichts von A/B.

---

## A — jetzt (14 Flächen, als 4 Wellen)

| Welle | Fläche (Etage) | Warum jetzt |
|---|---|---|
| **A1 · Geld einsammeln** | Offene Posten (Rechnungswesen) | Wer schuldet uns was — der direkteste Liquiditätshebel. Port: hausverwaltung-FiBu liegt fertig |
| | Mahnwesen (Rechnungswesen) | folgt direkt aus OP; Port ebenda |
| | DATEV-Export (Steuern) | **Code existiert**: `DatevExtfExportService` seit 08.07. in app/Services/Accounting — nur andocken |
| | Eingangsrechnungen (Buchhaltung) | **Code existiert**: `EingangsBelegflussService`; spart täglich Erfassungszeit |
| | E-Rechnungen XRechnung/ZUGFeRD (Fakturierung) | **gesetzliche Pflicht**: Empfang seit 2025, Versand wird 2027/28 Pflicht — vor der Welle sein |
| **A2 · Umsatz vorne** | Nachfassen fällig (Angebote) | der größte unbeackerte Umsatz liegt in nicht nachgefassten Angeboten |
| | Auftragseingang (Aufträge) | dein neuer Auftrags-Workflow: gewonnene Angebote → Auftrag, sauberer Start der Kette |
| | Auftragsbestätigungen (Aufträge) | das Dokument dazu — gehört zum selben Bauschritt |
| **A3 · Baustelle & Einsatz** | Mein Tag (Einsatzplanung) | Konzept + Bauordnung liegen fertig; Monteur-App V1 (Tagesliste zuerst) |
| | Materialbedarf & Bestellungen (Arbeitsvorbereitung) | verhindert den teuersten Fall: Monteur steht ohne Material |
| | Fälligkeiten (Wartung) | wiederkehrender Umsatz aus bestehenden Verträgen — Daten liegen schon da |
| | Auslastung (Controlling) | **Views existieren** (admin/capacity) — verdrahten, nicht bauen; DIE Einsatzplanungs-Kennzahl |
| **A4 · Fundament** | Gebäudeakte (Kontakte) | einmal erfassen, alle Rechner lesen — schaltet die WP-Auslegungskette frei |
| | Beleg-Erkennung (KI & Automatisierung) | gehört technisch zu Eingangsrechnungen (A1) — zusammen bauen, nicht doppelt |

## B — nächste Stufe (41)

**Abwicklung:** Pipeline · Auftragsstatus · Abnahme & Abrechnung · Bautagebuch · Baudokumentation · Qualitätsprüfung · Mängel & Nacharbeiten · Reklamationen *(Bau-Vierer erst NACH Mein Tag — die App liefert Fotos/Einträge von draußen)*
**Vertrieb:** Sets · Konfigurator *(Angebots-Beschleuniger)*
**Kontakte:** Projekt-Akte *(nach Gebäudeakte)* · Dokumenten-Center (DMS)
**Tools:** Dachplaner · Dachbelegung (PV) *(playground-Insel als Port)*
**Materialwirtschaft:** Artikelzuordnung · Warenkörbe *(playground-Lieferanten-Suite)* · Materialentnahmen
**Rechnungswesen:** Gutschriften · Journal · Ausgangsrechnungen · Kontenrahmen *(Voraussetzung für Journal/DATEV — mit A1 prüfen)* · Monatsabschluss · UStVA · Belegarchiv *(GoBD)* · Zahlungsverkehr · Kontenanbindung & Dienstleister · Kostenstellen · Kostenträger
**Personal:** Erfassung · Überstunden *(Ist-Zeiten → Lohn + Nachkalkulation)* · Lohnvorbereitung *(playground)* · Personalakte
**Controlling:** Gesamtfirma · Je Abteilung *(Cockpits)* · Umsätze · BWA *(hausverwaltung)* · Kapazität & Produktivität · Liquidität & Finanzplanung
**Kommunikation:** Kundenzugang *(Kundenportal-Konzept liegt)*
**Administration:** Formularbaukasten *(dynamische Formulare — braucht die Monteur-App für Checklisten)* · Textbausteine

## C — später (40)

**Aufträge/Sonstiges:** Interne Arbeiten · Serienbriefe · GAEB / Ausschreibungen · Exporte · Audit-Log
**Rechnungswesen:** Kassenbuch · Daueraufträge · Abschreibungen (AfA) · Bilanz & SuSa *(macht die Kanzlei — wir liefern DATEV)* · Kanzlei-Übergabe · GoBD & Prüfzentrum *(Belegarchiv B reicht zunächst)* · Versicherungen · Mieten & Pacht · Abgaben
**Personal (HR komplett):** Dienstplanung · HR-Prozesse · Beurteilungen · Bewerbungen · Einarbeitung & Austritt · Schulungen & Zertifikate · Arbeitsverträge · Lohnarten
**Unternehmen:** Mietobjekte · Vertragsmanagement · AGB & Rechtstexte
**Controlling:** Geschäftsführung *(nach Gesamtfirma)* · Bereichs-GuV · Abteilungsvergleich · Strategische Übersicht · Ziele · Investitionsplanung
**Kommunikation:** Veranstaltungen · Lieferantenzugang · Freigabe-App · Mobile Stempel-App *(wird ggf. Teil der Monteur-App statt eigener App)*
**Administration:** Agenten-Zentrale · Insights · Regeln & Abläufe · Gewerke & Leistungen · Fristen

*(Bewusst geparkt, außerhalb A/B/C: Wirtschaftlichkeit + Förderungen — kehren nach der Neu-Ausarbeitung zurück.)*

---

## Was das für die Navi heißt (nach deinem OK)

1. Je Sektion rücken A-Flächen **nach oben** (unter die Live-Einträge), C-Flächen ans Ende.
2. Die 4 A-Wellen werden der Arbeitsfahrplan: **A1 Geld → A2 Umsatz → A3 Einsatz → A4 Fundament** — jede einzeln abnehmbar, jede nutzt Vorhandenes (2× fertiger Service, 2× hausverwaltung-Port, 1× vorhandene Views).
3. Zählprobe: 14 + 41 + 40 = 95 ✓ — keine Fläche verloren. *(Korrektur R7: Stufen-Summen im ersten Wurf falsch beziffert (39/42); Inhalt der Listen war vollständig und unverändert.)*
