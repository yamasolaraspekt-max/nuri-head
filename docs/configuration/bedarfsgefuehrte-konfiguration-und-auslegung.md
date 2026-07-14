# Bedarfsgeführte Konfiguration und Auslegung — Gesamtarchitektur

**Stand:** 2026-07-14 · **read-only Analyse** (kein Bau, keine Migration, kein Commit) · **Git-Ausgangsstand:** `a00bb0a`
**Grundlage:** Masterentscheidung + fünf codebelegte Bestandsaufnahmen. **Begleitdokumente:** ADR-0001, Anforderungs-/Lückenmatrix, Modul-/Abhängigkeitsmatrix, Umsetzungsfahrplan, Vollständigkeitsbericht.

---

## 1. Kurzentscheidung

Alle Konfiguratoren/Auslegungen bauen auf **denselben** führenden Kunden-, Objekt-, Gebäude- und Projektdaten auf. **Bedarf → Datenprüfung → Berechnung → Varianten → Filter → Auswahl → Freigabe → Angebot.** Produktvorgaben sind Filter, keine Eignungsnachweise (Details ADR-0001).

## 2. Ist-Befund: zwei entkoppelte Welten (codebelegt)

**Welt 1 — freistehende Energie-Rechner** (`app/Http/Controllers/Energie/*`, Sidebar „Energie" `sidebar.blade.php:531-548`): produktzentriert, objektlos, transient. WR-/WP-Auslegung starten mit Geräteauswahl (`EnergieAuslegungController.php:199`); Heizlast-/Sanierungs-/Energiekonzept-Rechner erzeugen `HeizlastProjekt` und löschen es sofort wieder (`HeizlastController.php:137-171`). Heizlast (kW) wird in WP-Auslegung, Sanierung und Energiekonzept **jeweils neu** eingegeben, obwohl der Heizlast-Rechner sie berechnet — es gibt **keine Übergabe**.

**Welt 2 — objektgebundene Offer-Pipeline** (`app/Http/Controllers/Customer/Offer/*`, `app/Services/Offer/*`): sauber an `new_leads → lead_alternative_adds → lead_product_lists` gebunden (`KonfigurationsprojektService.php:35-63`), **read-only**, nur WP verdrahtet (`verdrahtet=$istWp`, `:83`; alle anderen Gewerke Platzhalter `:96`). `OfferReadinessService` liest bewusst aus den Quelltabellen statt Kopien (`OfferReadinessService.php:14-19`).

**Brücke fehlt:** Kein Absprung „Objekt → WP-/WR-Auslegung mit vorbefüllten Daten"; einzige objektnahe Naht ist die Grundriss-Persistenz (`GrundrissController::speichern`, 422 ohne Objekt).

## 3. Gesamtworkflow (Ziel)

```
Einstieg (Kunde/Objekt/Anfrage/Projekt/Angebot/Auftrag — nie eigener DB-Stamm)
  → Modulaktivierung aus lead_product_lists (beauftragte Gewerke) + fachliche Abhängigkeiten
  → Datenübernahme (Kunde/Objekt/Formulare/Bestand/frühere Berechnungen/Gebäudemodell)
  → Datenprüfung (vorhanden/veraltet/widersprüchlich/geschätzt/fehlt/blockierend)
  → Bedarf + Randbedingungen → Berechnung (Service, nicht Controller)
  → Varianten (N) → Filter (Query/View) → Ranking → Empfehlung
  → Auswahl (Nutzer, mit Begründung bei Abweichung) → Freigabe (Rolle) → Angebot (Snapshot)
```

## 4. Dynamische Modulaktivierung

`lead_product_lists`-Zeile = aktiviertes Gewerk (`KonfigurationsprojektService.php:44-48`, `GEWERK_WP=2`). Ziel: aus den beauftragten Produktgruppen den Modulpfad bestimmen (WP → Heizlast/Heizflächen/hydraulik/Elektro/Aufstellung; PV+Speicher → Dach/PV/WR/Speicher/Netz/Messkonzept; Fenster/Türen/Fassade/Dach → Geometrie/Öffnungen/Aufbauten). Fachliche Abhängigkeiten ergänzen Module automatisch (WP→Heizlast, PV→Dachgeometrie, Speicher→Last/Erzeugung, Wallbox→Netz/Last, Fenster→Öffnung+Aufmaß). **Heute nur WP „verdrahtet"; alle anderen Platzhalter.**

## 5. Datenübernahme & Datenprüfung

Vor manueller Neueingabe sucht das System bestehende Daten (Kunde/Objekt/Anfrage/Formulare/Bestandsaufnahme/frühere Berechnungen/freigegebene Gebäudemodelle/Heizlast/Verbrauch/Angebote/Pläne/Katalog) und bietet sie zur Prüfung an. Jeder übernommene Wert trägt Quelle/Quelldatensatz/Erfassungsdatum/Bearbeiter/Vertrauensstatus/Version/bestätigt-Flag. **Werte nie still überschreiben.** Vor jeder Berechnung: Datenprüfung (vorhanden+bestätigt / veraltet / widersprüchlich / geschätzt / automatisch erkannt / manuell überschrieben / fehlt / blockierend).

**Ist:** Das Fundament existiert am `Anforderungsprofil` — jeder EAV-Wert trägt `datenlage` (gemessen/berechnet/geschätzt), `quelle`, `erfassungsweg` (`AnforderungsprofilWert.php:24-27`); `HeizlastBelastbarkeit` klassifiziert daraus belastbar/eingeschränkt/vorläufig/unzureichend read-only. **Es fehlt:** die aktive Datenübernahme in die Energie-Tools und die Datenprüf-Vorstufe vor der Berechnung.

## 6. Gebäudemodell & LiDAR

Führend = **`anforderungsprofile.gebaeude_geometrie`** (versioniert, objektgebunden; Schreibpfad `GrundrissController::schreibeGeometrieVersion`). Der 2D-SVG-Grundriss-Editor (`grundriss_editor.blade.php`) + Topologie-Gate (`TopologieGate`) + Plan-Import (DXF/PDF via FastAPI `import-service/`) sind real. **Parallel-Geometrien** (Risiko): `raum_geometrien` (transient/legacy), `sanierungs_varianten.massnahmen`, `p_v_roof_plans.roof_structures` (PV-Insel), `RoofAreaEstimator` (fehlerhafte OSM/Web-Mercator-Schätzung). three.js-Dach-/PV-Planer (`public/js/pvtools`, `roof_config/*.blade`) sind **Playground/Test-Routen** ohne DB-Naht. **LiDAR/IFC/Punktwolke existiert nicht** (belegt: keine Treffer). 2D ist primär, 3D wird abgeleitet; Bauteiländerung muss alle Auswertungen aktualisieren (heute nicht durchgängig). LiDAR ist künftig Aufnahmequelle mit Bestätigungs-Workflow, nie automatische Modellwahrheit.

## 7. Auslegungsmodule (Ist-Kurzfassung)

Heizlast ✓ (Kern portiert, transient) · Heizflächen/Heizkörper ✓ (**einziger echter Angebots-Write** via `deal_measurement_items`, Preis bewusst NULL) · hydraulischer Abgleich ✓ (Teil der Heizlast) · WP ✓ (Match/Bivalenz/JAZ/Ranking, read-only, „informativ nicht verbindlich", nur luft_wasser) · PV ✓ (Rechner; `PvProjektService` „in ticket gebrochen") · Speicher **teilweise** (nur WR-Kompatibilität, keine kWh-Dimensionierung) · **Wallbox fehlt** · **Netz/Messkonzept fehlt** (nur Stammdaten-Modelle) · Dach **teilweise** (Schätzer) · Fenster/Türen nur als Heizlast-Bauteil · Fassade/Sanierung ✓ (transient) · Energiekonzept ✓ (transient). Details in der Modul-/Abhängigkeitsmatrix.

## 8. UI/UX-Grundmuster

Kopf (Kunde/Objekt/Projekt/aktive Gewerke/Datenqualität/Modellversion/Berechnungsstatus/Blocker/Filter) · dynamische Prozessnavigation (nur relevante Module) · Arbeitsbereich (Bedarf/vorhandene+fehlende Daten/Randbedingungen/Berechnungen/Varianten/Begründungen/Warnungen/Aufgaben) · Ergebnisbereich je Variante (Eignung/Belastbarkeit/Quellen/Ergebnisse/Warnungen/Preisstatus/Verfügbarkeit/Auswahl-/Freigabestatus). Light+Dark und Tastatur sind Pflicht-Prüfpunkte. **Ist:** kein solcher gemeinsamer Rahmen; jedes Energie-Tool hat eine eigene Formularseite.

## 9. Servicegrenzen & Versionierung

Controller: Autorisierung/Ownership/Form-Request/DTO/Servicekoordination/Response/ViewModel. Fachlogik in benannten Services (Gebäudegeometrie, Topologie, Heizlast, Heizflächen, WP-Matching, Bivalenz, WP-Ranking, PV-Sizing, PV-Ertrag, Speicher-Sizing, Wallbox/Lastmanagement, Produktfilter, Kosten, Förderung, Dokument, Angebotsübergabe). **Ist:** teils erfüllt — Rechenkerne sind Services, aber `EnergieAuslegungController::wpErgebnis`/`EnergiekonzeptController::baueWp` klammern Kosten/Förderung/Dokument noch im Controller (WP Stufe 3b P1a/P1b läuft dazu). Versionierung: Änderung relevanter Eingaben muss abhängige Ergebnisse `stale` setzen — **heute nur an der Profil-Versionskette, kein Ergebnis-Invalidierungs-Trigger** (`app/Observers`/`app/Listeners` leer).

## 10. Angebotsübergabe

Belegkette `offers → offer_details (sections-JSON) → deals → deal_measurements → invoices` vorhanden. `offer_details.sections` ist die Positions-Wahrheit; daneben ältere `offer_product_lists` (**Doppelstruktur-Kandidat**). Auslegung → Angebot ist **noch nicht verdrahtet** (Vorschläge read-only, `AuslegungVorschlagService.php:99`); einziger echter Write ist Heizkörper → `deal_measurement_items`. Auswahl-/Freigabestatus für Auslegungspositionen nicht persistiert.

## 11. Risiken

Parallel-Geometrien (**3 schreibbare Stores + 2 abgeleitete/Schätz-Quellen**) und Doppel-Positionsstruktur → zweite Wahrheiten · produktzentrierter Einstieg → Fehlauslegung · fehlender Ergebnis-Stale → veraltete „aktuelle" Ergebnisse · fehlende Preis-/Verfügbarkeitswahrheit (Auto-Preisanker konstruktiv unmöglich: Spec-`product_id` nullable, Deklaration ohne `->foreign()`-Constraint) · **`/ids/callback`-Auth-Widerspruch** (Route soll „PUBLIC" sein, ist aber per Konstruktor-`auth` gated → externer Callback vermutlich funktional gebrochen; **kein** Public-Write-Leck, aber ohne HMAC/Secret/IP falls bewusst zu öffnen — `IdsController.php:24`, `route:list -v`) · Konfigurator-/Angebots-Routen ohne Policy/Ownership · fehlende Module (Wallbox/Netz/Messkonzept) als spätere Insellösungen.

---

*Diese Datei ist die Landkarte. Die verbindliche Muss-Erfassung steht in `anforderungs-und-lueckenmatrix.md`; die Reihenfolge in `umsetzungsfahrplan.md`.*
