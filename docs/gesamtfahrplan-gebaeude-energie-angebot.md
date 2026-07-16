# Gesamtfahrplan — Gebäude-, Energie- und Angebotskonfiguration

**Stand:** 2026-07-14 · **read-only** · **kein Bau, kein Commit, kein Push, keine Migration, keine Datenänderung, kein Seeder, keine Löschung, keine playground-Stilllegung.**
**Zweck:** Ein dauerhafter, kapitelweiser Fahrplan, der jederzeit zeigt: woran arbeiten wir, warum, was ist fertig, was fehlt, was kommt als Nächstes, wann ist ein Kapitel abgeschlossen, welche Kapitel hängen zusammen, wo Backend+Frontend gemeinsam zu prüfen sind, wo Browser-Test möglich ist, wo noch nicht gebaut werden darf.

**Grundlagen (firsthand, read-only):**
- `docs/bereich2-gebaeude-energie-konfigurationsplattform-gap-analyse.md` (Plattform-vs-Insel, 12 Fragen)
- `docs/bereich2-wp-auslegungswizard-gap-analyse.md` (WP-Rechenkern-Gap)
- `docs/wberechnung-uebernahme-inventur.md`, `docs/playground-ticket-vergleich-entscheidung.md`
- `docs/zielbild-objekt-zentriertes-crm.md`, `docs/architektur-entscheidungen.md` (Weichen 1/5/6), `docs/hierarchie-objekt-projekt-bestandsaufnahme.md`
- ticket-/playground-/wberechnung-Code-Inventuren (2026-07-14)
- CLAUDE.md (DAUERDIREKTIVE, Optimierungs-Reihenfolge, Operanden-Gate, Startblock-Pflicht)

**Statuswerte (einheitlich im ganzen Dokument):** `nicht begonnen` · `inventarisiert` · `konzipiert` · `teilweise gebaut` · `browserprüfbar` · `fachlich blockiert` · `fertig für MVP` · `abgeschlossen`.

---

## 1. Grundsatz

> **Wir bauen keine Insel-Lösungen. Wir bauen eine modulare Gebäude-, Energie-, Produkt- und Angebotskonfigurationsplattform IM bestehenden ticket-System** — im ticket-Datenmodell, mit ticket-Auth, ticket-CI (Vuexy/Blade/jQuery), ticket-Navigation. playground und wberechnung sind **Bauteile-Lager/Referenz**, keine zweiten Systeme.

Die Plattform umfasst als **eine** verbundene Kette am **Objekt** (Weiche 5: *das Objekt klammert, der Auftrag führt aus*):

gemeinsamer Konfigurationsarbeitsraum → Gebäudegrundlagen → Gebäudegeometrie → Referenzdaten → Heizlast/Gebäudebewertung → Wärmepumpe → 3D-PV → Batteriespeicher → Wallbox → Gesamtenergie → Dach/Fassade/Fenster/Türen → technische Prüfung → Angebotsübergabe → Projektakte.

**Vier Leitregeln (aus CLAUDE.md, verbindlich):**
1. **Eine Wahrheit je Sachverhalt** — keine zweite Auslegungs-/Preis-/Positions-/Objekt-Wahrheit.
2. **Optimierungs-Reihenfolge:** ① Konzept → ② Workflow → ③ vorhandene Bausteine verknüpfen → ④ erst dann automatisieren.
3. **Operanden-Gate:** fehlender/unsicherer Operand → fragen/markieren, nie stillschweigend weiterrechnen.
4. **Additiv & datenschutzfest:** keine destruktiven Schritte als Beifang; Bestandsdaten unantastbar.

**Zentrale Architektur-Erkenntnis (aus der Gap-Analyse):** Der gemeinsame Kern existiert in ticket zu **~60–70 % bereits in Fragmenten** (versioniertes `Anforderungsprofil` + Bauphysik-Modell `heizlast_*` + three.js-3D-Dach + Produktkonfiguration `master_sets`/`offer_details`) — bisher **nur für den WP/Heizlast-Strang durchgezogen**. Der Weg ist **Konsolidierung + Projekt-Klammer + PV/Speicher-Einhängung**, kein Neubau.

---

## 2. Kapitelstruktur (Übersicht)

Die vorgeschlagene 0–15-Struktur wird übernommen und um **Kapitel 16 (Projektakte)** ergänzt (in Punkt-1-Liste enthalten, in der Kapitelliste fehlte sie). Reihenfolge der *Bearbeitung* ≠ Kapitelnummer (s. §4/§6).

| Kap | Titel | Rolle |
|---|---|---|
| 0 | Governance, Arbeitsweise, Rückfall, Commit/Push | Querschnitt, Dauerkapitel |
| 1 | Systemlandkarte & Datenwahrheiten | Fundament (Inventur) |
| 2 | Gemeinsamer Konfigurationsarbeitsraum | **Kern-Unlock** |
| 3 | Gemeinsame Gebäude-/Objektbasis | Fundament |
| 4 | Gemeinsame Gebäudegeometrie | Fundament (2D/3D) |
| 5 | Referenzdatenbanken | Querschnitt-Fundament |
| 6 | Heizlast & Gebäudebewertung | Fachmodul (reif) |
| 7 | Wärmepumpen-Systemkonfigurator | Fachmodul (reif) |
| 8 | 3D-PV-Planer-Migration (playground) | Fachmodul (unreif) |
| 9 | Batteriespeicher & Wallbox | Fachmodul |
| 10 | Gesamtenergie | Cross-Gewerk (Krönung) |
| 11 | Dach/Fassade/Fenster/Türen | Fachmodule (Aufmaß/Positionen) |
| 12 | Angebotsworkflow & Angebotsübergabe | Ausgang (reif) |
| 13 | Technische Prüfung, Freigabe, Versionierung | Querschnitt |
| 14 | UI/UX & Designsystem | Querschnitt |
| 15 | Tests, Browserpfade, Abnahme | Querschnitt |
| 16 | Projektakte | Ausgang |

---

## 3. Kapitel im Detail (Pflichtstruktur je Kapitel)

> Legende Wiederverwendung: **ticket** = führend/vorhanden · **pg** = playground · **wb** = wberechnung.

### Kapitel 0 — Governance, Arbeitsweise, Rückfall, Commit-/Push-Regeln
- **Ziel:** Arbeitsregeln durchsetzen: Drei-Rollen-Zyklus (Planner→Generator→Evaluator getrennt), Evaluator strikt read-only, Archiv+Manifest, kein Löschen ohne Freigabe, **kein Push ohne ausdrückliches Yama-„Push frei"**, path-scoped Commits.
- **Warum nötig:** Verhindert genau die Fehler der Vergangenheit (Evaluator committete/pushte autonom). Bindet jede Instanz.
- **Führende Wahrheit:** `docs/BETRIEBSORDNUNG.md` > CLAUDE.md > `docs/architektur/bauordnung.md` > `docs/agents/*`.
- **ticket-Bausteine:** `docs/agents/00-04`, `docs/BETRIEBSORDNUNG.md`, `docs/rueckfall-archiv-regeln`, `_archiv/*`.
- **pg/wb:** —
- **Wiederverwendet:** vollständig (Bestand). **Nicht übernommen:** — **Offene Lücken:** keine strukturellen.
- **Backend/Frontend:** — · **Testpfad:** — · **Browser-Prüfpfad:** —
- **Risiken:** Regel-Erosion bei Autonomie-Läufen. · **Abhängigkeiten:** bindet alle. · **Stop-Kriterium:** entfällt (Dauerkapitel). · **Fortschritt:** 100 % (etabliert). · **Nächster Slice:** —

### Kapitel 1 — Systemlandkarte & Datenwahrheiten
- **Ziel:** Vollständige Karte: vorhandene Bausteine, führende Wahrheiten, Doppelwahrheiten, Risiken.
- **Warum nötig:** Ohne Wahrheiten-Karte baut man Parallelstrukturen (DAUERDIREKTIVE-Verstoß).
- **Führende Wahrheiten (festgeschrieben):** Umsatz→`invoices`; Status/Phasen→`lead_stages`; Katalog→ticket-Artikel-DB (ein Katalog); Positionen→`offer_details.sections`; Preis→`CatalogPriceGuard`/`component_id`; Objekt→`lead_alternative_adds`; Auslegung/Bedarf→`Anforderungsprofil` (versioniert).
- **ticket-Bausteine:** die 3 Inventuren + `docs/crm-inventur-*`, diese Gap-Analysen.
- **pg-Bausteine:** `AnlagenKonfiguration` (Blaupause), `PlanungskontextController` (Lesefassade-Vorbild).
- **wb-Bausteine:** Zwei-Schichten-Regel (Produkt→Physik), Bauphysik-Kern.
- **Wiederverwendet:** die Inventuren als lebende Karte. **Nicht übernommen:** playground-Tabellen 1:1, zweite Kataloge.
- **Offene Lücken:** dedizierte **PV-Inventur** fehlt noch (analog WP-Gap); Doppelspur-Befunde (2× PVGIS, `Radiator.php`-Altlast) nicht abschließend kartiert.
- **Backend/Frontend:** — (Analyse). · **Testpfad:** — · **Browser-Prüfpfad:** —
- **Risiken:** Karte veraltet. · **Abhängigkeiten:** speist alle. · **Stop-Kriterium:** Karte deckt alle 7 Entitäten + PV/WP/Speicher; PV-Inventur ergänzt. · **Fortschritt:** ~80 % (PV-Inventur offen). · **Nächster Slice:** PV-Funktionsinventur (read-only).

### Kapitel 2 — Gemeinsamer Konfigurationsarbeitsraum ★ Kern-Unlock
- **Ziel:** Ein „Konfigurationsprojekt", das **mehrere Gewerke/Energiesysteme je Objekt** klammert und mit Kunde/Objekt/Anfrage/Projekt/Angebot/Auftrag verbunden ist — mit Status, Version, Verlauf.
- **Warum nötig:** Heute nur 1:1 `offers`/`projects` je `article_group` — keine objektübergreifende Klammer. Ohne diese Klammer bleibt jedes Fachmodul eine Insel.
- **Führende Wahrheit:** **Objekt = `lead_alternative_adds`** (Weiche 5) als Klammer; Bedarf/Auslegung = `Anforderungsprofil`. **Zu entscheidende Weiche:** reicht die Objekt-Klammer + `Anforderungsprofil`, oder braucht es eine schlanke „Konfigurationsprojekt"-Sicht darüber?
- **ticket-Bausteine:** `lead_alternative_adds`, `lead_product_lists`, `Anforderungsprofil` (polymorph an Objekt/Gewerk, versioniert, EAV), `offer_details`.
- **pg-Bausteine (Blaupause, NICHT importieren):** `AnlagenKonfiguration` + `AnlagenKonfigPosition` (je Gewerk) + `AnlagenKonfigSnapshot` (sha256), `PlanungskontextController` (read-only Aggregation je Objekt mit „fehlt je Gewerk").
- **wb-Bausteine:** — (Berechnungsträger, kein Projektcontainer).
- **Wiederverwendet:** Objekt-Klammer + `Anforderungsprofil` als Träger. **Nicht übernommen:** playground-`anlagen_*`-Tabellen als zweites System; keine zweite Objekt-/Projekt-Datenhaltung.
- **Offene Lücken:** keine „mehrere Systeme je Objekt"-Sicht; keine gemeinsame Energiesystem-Entität; keine objekt-granulare Statuszusammenfassung.
- **Backend-Aufgaben:** Konzept „Konfigurationsprojekt-Sicht" (read-model aus Objekt + n×Gewerk + Anforderungsprofile), **erst Konzept, dann ggf. schlanke Aggregation** — additiv, keine neue Objekt-Tabelle vorschnell.
- **Frontend/UI-Aufgaben:** Arbeitsraum-Shell je Objekt (Gewerke-Spalten PV/WP/Speicher/Wallbox), Schrittnavigation, rechte Infospalte — Ticket-CI.
- **Testpfad:** read-model liefert je Objekt korrekte Gewerk-/Reife-/Fehlt-Aggregation; kein Schreibpfad.
- **Browser-Prüfpfad:** Objektprofil → Arbeitsraum-Tab: zeigt alle Gewerke am Objekt + Status.
- **Risiken:** zweite Projekt-Wahrheit (playground-Import) — verboten; Überbau (zu viel Struktur vor Bedarf).
- **Abhängigkeiten:** Basis für 6/7/8/9/10/12. Hängt an Weiche 5 (entschieden) + Weiche 1 (`lead_stages`).
- **Stop-Kriterium:** Klammer-Weiche entschieden (Objekt-Klammer vs. eigene Projekt-Sicht) + read-only Arbeitsraum-Sicht browserprüfbar, kein zweiter Wahrheitsträger.
- **Fortschritt:** ~25 % (Anker + Träger da, Klammer-Sicht fehlt; UX-2 Objektprofil-Tabblock als Vorstufe). · **Nächster Slice:** Konzept „Konfigurationsprojekt-Sicht" (read-only) + Klammer-Weiche.

### Kapitel 3 — Gemeinsame Gebäude-/Objektbasis
- **Ziel:** Führende Gebäudegrunddaten: Standort/PLZ, Klima/NAT/Heizgradtage, Gebäudetypologie, Nutzung, Personen, Flächen.
- **Warum nötig:** Objektdaten liegen **redundant** auf `lead_alternative_adds` (flach) UND `heizlast_projekte` (Berechnung) — ohne führende Entität droht dritte Redundanz.
- **Führende Wahrheit:** **zu entscheiden** — `lead_alternative_adds` als führendes Gebäude-Stammobjekt, `heizlast_projekte` als Berechnungssicht darauf (empfohlen: keine neue Tabelle, sondern Konsolidierung/Adapter).
- **ticket-Bausteine:** `lead_alternative_adds` (Baujahr/Fläche/Dach/Hülle), `building_data` (U-Wert je Baujahr), `building_types`/`building_type_values`, `heizlast_projekte`, `KlimaPlz`/`KlimaPlzService` (8168 PLZ).
- **pg-Bausteine:** `ProjectObject`/`objects`, `EnergieObjectProfile` (Verbrauch/Wunsch je Gewerk, `field_meta`) — Feld-Vorbild.
- **wb-Bausteine:** `HeizlastProjekt` (PLZ→NAT, Sanierungsstufe), `klima_plz.csv`.
- **Wiederverwendet:** `lead_alternative_adds` + Klima-Referenz. **Nicht übernommen:** zweite Objekttabelle; tote `customer_alternative_adds`.
- **Offene Lücken:** kein `Building`-Model/Adapter; Redundanz nicht aufgelöst; Nutzung/Personen teils nur flach.
- **Backend-Aufgaben:** Konzept „führende Gebäudebasis + Adapter zu Berechnung" (Operanden-Gate: Datenlage je Feld). · **Frontend:** Gebäude-Stammdaten-Panel je Objekt (Ticket-CI).
- **Testpfad:** ein Objekt liefert konsistente Grunddaten an Heizlast/PV ohne Doppelpflege. · **Browser-Prüfpfad:** Objekt → Gebäudedaten-Tab.
- **Risiken:** Redundanz-Auflösung berührt Bestandsdaten → strikt additiv/Adapter. · **Abhängigkeiten:** speist 4/5/6/7/8. · **Stop-Kriterium:** führende Gebäudebasis konzipiert, Adapter-Weg belegt, kein Bestands-UPDATE als Beifang. · **Fortschritt:** ~40 % (Daten+Klima da, Konsolidierung offen). · **Nächster Slice:** Konzept Gebäudebasis+Adapter.

### Kapitel 4 — Gemeinsame Gebäudegeometrie
- **Ziel:** Gemeinsame Geometrie (Gebäudekörper, Dachflächen, Fassaden, Öffnungen, Hindernisse) als 2D/3D-Grundlage für PV, Dach, Fassade, Heizlast — **einmal gepflegt, mehrfach genutzt**.
- **Warum nötig:** Heute **uneinheitlich**: SVG-Grundriss (Heizlast) vs. two three.js-Editoren (Dach/Solar) vs. `raum_geometrien` vs. `anforderungsprofile.gebaeude_geometrie` (JSON) — keine gemeinsame Geometrie-Wahrheit.
- **Führende Wahrheit:** **zu definieren** — gemeinsames Geometriemodell (Vorbild: wb `RaumGeometrie`, **entkoppelt von der Rechen-Engine** via `GeometrieAbleitungService`).
- **ticket-Bausteine:** `raum_geometrien`, `GeometrieAbleitungService`, `RaumHuelleService`, `GrundrissController` (SVG), `roof_config/roof.blade.php` (three.js 0.161), `solar/configuration/configure.blade.php` (three.js 0.126+GLTF), Mapbox.
- **pg-Bausteine:** `EnergieRoofModel` (`geometry_json`), `RoofTemplate` (`config_json`), 3D-Planer-Utils (`src/utils/*` framework-frei), `PvBelegungExtractor` (kWp serverseitig aus Geometrie).
- **wb-Bausteine:** `RaumGeometrie` (Polygon+Wandsegmente), Entkopplungs-Prinzip (Vorbild).
- **Wiederverwendet:** Entkopplungsprinzip + ticket-three.js-Basis. **Nicht übernommen:** React-3D-Insel als Pflicht (s. Kap. 8), doppelte Geometrie-Silos.
- **Offene Lücken:** kein gemeinsames Geometriemodell; three.js-Versionen uneinheitlich (0.126/0.161/pg 0.184); Dach- vs. Raum-Geometrie nicht verbunden.
- **Backend-Aufgaben:** Konzept „ein Geometriemodell, Ableitungs-Services je Gewerk". · **Frontend:** Geometrie-Editor-Strategie (SVG/three.js vereinheitlichen) — Ticket-CI, Alpine-Verbot beachten.
- **Testpfad:** Geometrie → abgeleitete Flächen für Heizlast UND Belegung für PV aus **einer** Quelle. · **Browser-Prüfpfad:** Geometrie-Editor rendert, speichert `geometry_json`, PV/Heizlast lesen daraus.
- **Risiken:** three.js/React-Stack-Konflikt; Scope-Explosion. · **Abhängigkeiten:** **Voraussetzung für Kap. 8 (3D-PV) und Kap. 11.** · **Stop-Kriterium:** gemeinsames Geometrie-Datenmodell + Contract definiert (Konzept), bevor 3D-PV migriert wird. · **Fortschritt:** ~30 % (Bausteine da, uneinheitlich). · **Nächster Slice:** Geometriemodell-Konzept + Contract (read-only).

### Kapitel 5 — Referenzdatenbanken
- **Ziel:** Klima/PLZ, U-Werte, Dämmstoffe, Gebäudetypologien, Bestandsheizungen/Nutzungsgrade, Warmwasserprofile, Produktdaten — mit Quellen/Versionen/Verifikationsstatus.
- **Warum nötig:** Referenzdaten sind die gemeinsame **Physikschicht** (Zwei-Schichten-Regel: Produkt→Physik, nie umgekehrt). Ohne Versions-/Quellenpflege drohen Scheingenauigkeit und stille Fehler.
- **Führende Wahrheit:** ticket-Referenztabellen (`materials`/`konstruktionen`/`baualtersklassen`/`klima_plz`) — je Zeile `verifikations_status`.
- **ticket-Bausteine:** `materials` (23), `konstruktionen` (5), `baualtersklassen` (25, TABULA), `klima_plz` (8168, DWD), `building_data`, `UWertService` (ISO 6946).
- **pg-Bausteine:** Dach-/Eindeckungs-/Montage-Kataloge, ETIM-Merkmale.
- **wb-Bausteine:** Quelle der obigen Kataloge + `KlimaBinService` (TRY-Näherung), `HoehenkorrekturService`.
- **Wiederverwendet:** ticket-Referenzkataloge (übernommen). **Nicht übernommen:** doppelte Kataloge, ungeprüfte Richtwerte als „Wahrheit".
- **Offene Lücken:** viele Werte `to_verify` (JAZ-Richtwerte, Wiederaufheizzuschlag, Klima-Bin-σ); Warmwasserprofile/Schallregeln dünn; Produktdaten-Versionierung (OMD/IDS) inert.
- **Backend-Aufgaben:** Quellen/Versionen härten (Norm-Fassungen, echte TRY-Daten prüfen). · **Frontend:** Referenz-Admin (read-only-Anzeige Quelle/Status).
- **Testpfad:** jeder Referenzwert trägt Quelle+Status; Physikschicht standalone testbar. · **Browser-Prüfpfad:** Referenz-Ansicht zeigt Quelle/Verifikation.
- **Risiken:** Scheingenauigkeit; Rechtsraum-/Norm-Änderungen. · **Abhängigkeiten:** speist 6/7/9/10. · **Stop-Kriterium:** Kernkataloge quellenbelegt+versioniert, `to_verify` markiert. · **Fortschritt:** ~55 % (Kataloge da, Härtung offen). · **Nächster Slice:** `to_verify`-Register + Quellen-Audit (read-only).

### Kapitel 6 — Heizlast & Gebäudebewertung
- **Ziel:** DIN-Heizlast (raumweise + Verbrauchsmethode + Typologie), Bauteilberechnung, Wegevergleich, **Datenqualität/Ampeln, keine Scheingenauigkeit**.
- **Warum nötig:** Heizlast ist Fundament der WP-Größe. Reifster Strang — aber als Stand-alone-Tool, nicht am Objekt-Anker; Belastbarkeit nicht erzwungen.
- **Führende Wahrheit:** `Anforderungsprofil` (Heizlast-Werte mit `datenlage`/`quelle`), Rechenkern `HeizlastRechner`.
- **ticket-Bausteine:** `app/Services/Heizlast/*` (HeizlastRechner byte-genau, HeizlastProjektService, RaumHuelle, UWert, Warmwasser, Verbrauch, KlimaBin, Höhenkorrektur), `heizlast_projekte/raeume/bauteile`, `EnergieAuslegungController`/`HeizlastController`, `AnforderungsprofilHeizlastAdapter`.
- **pg-Bausteine:** `EnergieHeizlastCalc` (Vergleich). · **wb-Bausteine:** Quelle des Kerns (Diff=0).
- **Wiederverwendet:** kompletter ticket-Heizlast-Kern. **Nicht übernommen:** zweite Heizlast-Wahrheit; überschlägiges Verfahren als Plattformkern (nur UX-Schnellpfad).
- **Offene Lücken:** **Belastbarkeit nicht erzwungen** (Reife prüft nur *Existenz* eines Heizlast-Werts, nicht `datenlage`) → schärfste Lücke (= Paket 5b); Kette läuft als `/admin/energie/*`, nicht am Objekt-Anker; Aggregat-Datenqualitäts-Ampel fehlt.
- **Backend-Aufgaben:** 5b Belastbarkeits-Gate (klein); später Kette an Objekt-Anker koppeln. · **Frontend:** Datenqualitäts-Ampel je Eingabegruppe.
- **Testpfad:** WP-Reife blockiert bei unbelastbarer Heizlast-Datenlage; Rechenkern-Regressionstests. · **Browser-Prüfpfad:** Heizlast-Formular → Ampel → Reifestatus.
- **Risiken:** Scheingenauigkeit; Fehl-Dimensionierung ohne Gate. · **Abhängigkeiten:** braucht 3/4/5; speist 7. · **Stop-Kriterium:** Belastbarkeits-Gate greift; Datenlage sichtbar. · **Fortschritt:** ~65 % (Kern da+verdrahtet, Gate+Anker offen). · **Nächster Slice:** **Paket 5b (jetzt, s. §9)**.

### Kapitel 7 — Wärmepumpen-Systemkonfigurator
- **Ziel:** WP-Auslegung, Bivalenz, JAZ, Heizstabanteil, Vorlauf, Warmwasser, Puffer, Systembauart, **Herstellervergleich + Ranking**, Komponentenliste.
- **Warum nötig:** Kernprodukt; Master-Prompts fordern Varianten (mono/monoenergetisch/bivalent) + Ranking + strukturierten Ergebnisbericht.
- **Führende Wahrheit:** `Anforderungsprofil` (WP-Auslegungswerte), Matching `WaermepumpenMatchService`, Positionen `offer_details.sections`.
- **ticket-Bausteine:** `BivalenzService` (**0 Aufrufer, isoliert — Krone**), `JazService`, `WpKennlinieService`, `WaermepumpenMatchService`, `WarmwasserService`, `FussbodenheizungService`, `FoerderungService`, WP-Katalog (19 Geräte), `WpKatalogMatchingService`, `AuslegungVorschlagService` (P3-c read-only).
- **pg-Bausteine:** `WaermepumpeSpec`/`WpKennfeld` (Vergleich), Konfigurator-Angebotspipeline (Idee).
- **wb-Bausteine:** Quelle Bivalenz/JAZ/Kennlinie.
- **Wiederverwendet:** ticket-WP-Kern. **Nicht übernommen:** WP-Solitär mit eigener Persistenz; zweite Auslegungswahrheit.
- **Offene Lücken:** kein geführter Wizard; **kein Varianten-Vergleich mit Ranking+Begründung**; `BivalenzService` nicht erreichbar (WP-Route rechnet JAZ ohne Bivalenz-Ranking); Puffer/WW-Speicher-Dimensionierung; strukturierter Ergebnisbericht.
- **Backend-Aufgaben:** `BivalenzService` an Kern verdrahten; Varianten-/Ranking-Read-model; **alles am `Anforderungsprofil`-Kern, nicht WP-eigen**. · **Frontend:** WP-Konfigurator-UI (Ticket-CI), Varianten-Vergleich, Ergebnisbericht.
- **Testpfad:** Varianten-Ranking deterministisch+begründet; keine WP-Größe ohne belastbare Heizlast. · **Browser-Prüfpfad:** WP-Konfigurator → Varianten → Ranking → Komponentenliste → Angebotsübergabe.
- **Risiken:** Insel-Bau (verboten); erfundene Operanden. · **Abhängigkeiten:** braucht 2/3/4/5/6; speist 12. · **Stop-Kriterium:** WP als Fachmodul am gemeinsamen Kern, Varianten browserprüfbar, Belastbarkeits-Gate aktiv. · **Fortschritt:** ~45 % (Rechenkern da, Wizard/Varianten/Ranking offen; 4a-Cockpit read-only Vorstufe). · **Nächster Slice:** nach Kap. 2/6 — Bivalenz verdrahten + Varianten-Read-model (Konzept zuerst).

### Kapitel 8 — 3D-PV-Planer-Migration (playground)
- **Ziel:** vollständige PV-Funktionsinventur, Funktionsparität, Migration in ticket-CI, gemeinsame Geometrie, Speicherung, Tests, Angebotsübergabe.
- **Warum nötig:** 3D-PV ist gefordert; playground hat einen 3D-**Dachkonstruktions-/Belegungsplaner** (BOM/Werkstattplan) — **kein** Ertragsplaner.
- **Führende Wahrheit:** gemeinsame Geometrie (Kap. 4) + `config_json`-Contract; PV-Ertrag = `PvgisErtragService` (kanonisch).
- **ticket-Bausteine:** three.js-Editoren (`roof_config`, `solar/configuration`), `PvgisErtragService`, `InverterSizingService`, `PvProjektService` (isoliert).
- **pg-Bausteine:** `DachplanerProPage.tsx` (3786 Z., `@ts-nocheck`, React), `src/utils/*` (framework-freie Geometrie-Utils), `RoofTemplate`/`config_json`, `PvBelegungExtractor`, `Auslegung*`/`StringBuilder`/`Performance`/`WirtschaftlichkeitEngine`.
- **wb-Bausteine:** `StringBuilderService`, `InverterSuggestionService` (Rest-Referenz).
- **Wiederverwendet (Kandidaten):** framework-freie Utils + `config_json`-Contract + Extractor-Muster (SSOT serverseitig). **Nicht übernommen:** React-Insel als Pflicht; frontend-kWp; verwaiste pg-Enden.
- **Offene Lücken:** kein Ertrag/Verschattung/Wirtschaftlichkeit im Planer; Stack-Konflikt (React vs. Blade/jQuery); TS-Altschuld.
- **Backend-Aufgaben:** PV an `Anforderungsprofil`-Kern (Registry+Adapter), Belegung→Positionen. · **Frontend:** Migrations-Strategie-Entscheidung (einbetten/three.js-ausbauen/neu).
- **Testpfad:** Belegung→kWp serverseitig; PV-Ertrag reproduzierbar. · **Browser-Prüfpfad:** 3D-Planer rendert in ticket, speichert Geometrie, Angebotsübergabe.
- **Risiken:** **höchste** (Prototyp-Qualität, stack-fremd, Scope). · **Abhängigkeiten:** **braucht Kap. 4 (Geometrie) zuerst**; speist 10/12. · **Stop-Kriterium:** PV-Inventur + Geometriemodell stehen, Migrations-Weg entschieden — erst dann Bau. · **Fortschritt:** ~10 % (Inventur begonnen). · **Nächster Slice:** **read-only PV-Funktionsinventur (s. §10, Option A)**.

### Kapitel 9 — Batteriespeicher & Wallbox
- **Ziel:** Speicher aus PV/Haushalt/WP/Wallbox, Ladeprofile, Lastmanagement, Netzanschluss, Autarkie.
- **Warum nötig:** Vervollständigt das Energiesystem; hängt an PV+WP-Ergebnissen.
- **Führende Wahrheit:** `Anforderungsprofil` (Speicher/Wallbox-Werte) + Energiesystem-Entität (Kap. 2).
- **ticket-Bausteine:** `Battery`/`BatterySystem`/`BatteryInverter`, `Contracts/SizingBattery`, `SolarSystem` (dünn), `ElectricVehicle`.
- **pg-Bausteine:** `LastmanagementService` (Kandidat), `EpsBoxService`, Speicher-Specs (EK6/EK12).
- **wb-Bausteine:** `batterie_wr_kompatibilitaet` (Rest).
- **Wiederverwendet:** ticket-Speicher-Modelle + ggf. pg-Lastmanagement (selektiv). **Nicht übernommen:** zweite Energie-Wahrheit.
- **Offene Lücken:** Sizing nicht am Kern; Lastmanagement fehlt; Netzanschluss-Prüfung.
- **Backend:** Speicher/Wallbox-Sizing am Kern. · **Frontend:** Speicher/Wallbox-Panel im Arbeitsraum.
- **Testpfad:** Sizing deterministisch aus PV/WP/Verbrauch. · **Browser-Prüfpfad:** Arbeitsraum → Speicher/Wallbox → Ergebnis.
- **Risiken:** Abhängigkeit von PV/WP-Reife. · **Abhängigkeiten:** braucht 7/8; speist 10. · **Stop-Kriterium:** Sizing am Kern, browserprüfbar. · **Fortschritt:** ~15 %. · **Nächster Slice:** nach WP/PV — Konzept Speicher-Sizing.

### Kapitel 10 — Gesamtenergie
- **Ziel:** PV-Erzeugung, WP-Strom, Speicher, Wallbox, Netzbezug, Eigenverbrauch, Autarkie, Betriebskosten, CO₂, Wirtschaftlichkeit — je Objekt.
- **Warum nötig:** Die Cross-Gewerk-„Krönung" (Schicht 3 Zielbild); nur sinnvoll, wenn Fachmodule am gemeinsamen Kern liegen.
- **Führende Wahrheit:** Aggregation aus `Anforderungsprofil` je Gewerk (kein neuer Rechner).
- **ticket-Bausteine:** `PvgisErtragService`, `BivalenzService` (WP-Strom), `KostenService`, `SanierungsWirtschaftlichkeitService`, `ProfitabilityCalculation`.
- **pg-Bausteine:** `PerformanceService`, `WirtschaftlichkeitEngine` (Vergleich).
- **Wiederverwendet:** vorhandene Teil-Rechner. **Nicht übernommen:** zweite Gesamtbilanz-Wahrheit.
- **Offene Lücken:** keine objekt-weite Energiebilanz; Eigenverbrauch/Autarkie/CO₂ nicht aggregiert.
- **Backend:** Gesamtbilanz-Read-model. · **Frontend:** Energiebilanz-Dashboard je Objekt.
- **Testpfad:** Bilanz konsistent mit Einzel-Fachmodulen. · **Browser-Prüfpfad:** Objekt → Gesamtenergie.
- **Risiken:** Scheingenauigkeit; verfrüht. · **Abhängigkeiten:** braucht 7/8/9. · **Stop-Kriterium:** Bilanz aus Fachmodulen aggregiert, keine Parallelrechnung. · **Fortschritt:** ~5 %. · **Nächster Slice:** später.

### Kapitel 11 — Dach/Fassade/Fenster/Türen-Konfiguratoren
- **Ziel:** gemeinsame Geometrie + Bauteildaten für Angebote UND Heizlast nutzen — **keine Doppelpflege**.
- **Warum nötig:** Diese Gewerke teilen Geometrie/Hülle mit Heizlast/PV; Fenster-/Türtausch ändert Heizlast → WP-Größe (Cross-Gewerk).
- **Führende Wahrheit:** gemeinsame Geometrie (Kap. 4) + Bauteil (`heizlast_bauteile`).
- **ticket-Bausteine:** `heizlast_bauteile` (Fenster/Tür als Bauteil), Dach-Geometrie, `UWertService`.
- **pg-Bausteine:** Eindeckungs-/Dach-Kataloge, 3D-Dachkonstruktion (Kap. 8).
- **Wiederverwendet:** Bauteil-/Geometriemodell. **Nicht übernommen:** separate Fenster-Dummies (wb-Verzicht).
- **Offene Lücken:** Aufmaß-/Positionslogik (Fenster einzeln) fehlt; Kopplung Hülle→Heizlast nicht durchgängig.
- **Backend:** Aufmaß→Bauteil→Heizlast-Kopplung. · **Frontend:** Aufmaß-Erfassung an Geometrie.
- **Testpfad:** Fensteränderung propagiert in Heizlast. · **Browser-Prüfpfad:** Geometrie → Fenster → Heizlast-Neurechnung.
- **Risiken:** Scope; Geometrie-Abhängigkeit. · **Abhängigkeiten:** braucht 4; berührt 6. · **Stop-Kriterium:** Bauteil-Kopplung ohne Doppelpflege. · **Fortschritt:** ~10 %. · **Nächster Slice:** später (nach Geometrie).

### Kapitel 12 — Angebotsworkflow & Angebotsübergabe
- **Ziel:** aus **freigegebenen** Konfigurationen Angebotspositionen erzeugen: `offer_details.sections`, Vorlagen, Materialliste, Kundenbericht.
- **Warum nötig:** Der Ausgang der Plattform; hier greift die festgeschriebene Belegkette (Angebot→Auftrag→Rechnung).
- **Führende Wahrheit:** `offer_details.sections` (Positionen) + `CatalogPriceGuard` (Preis, `component_id`).
- **ticket-Bausteine:** `OfferReadinessService`, `OfferReadinessGate` (WP-Gate, gebaut), `master_sets`/`master_set_components`, `AuslegungVorschlagService` (P3-c), `WpKatalogMatchingService`, Angebots-Wizard/Cockpit (4a).
- **pg-Bausteine:** `KonfiguratorAngebotService`/`KonfiguratorPipelineService` (Idee), Angebotsampel (Konzept, s. playground-Vergleich Prio 2).
- **Wiederverwendet:** ticket-Angebotskette + Reife-Gate. **Nicht übernommen:** playground-Invoice-Welt; zweite Positions-/Preiswahrheit.
- **Offene Lücken:** Konfiguration→Positionen-Automatik blockiert am **Preisanker (OMD/IDS inert)**; Materialliste/Kundenbericht aus Konfiguration.
- **Backend:** Konfiguration→`offer_details`-Übergabe (erst wenn Preisanker steht). · **Frontend:** Angebots-Übergabe-UI, Kundenbericht.
- **Testpfad:** Gate blockiert unreife/unbelastbare Angebote; Positionen korrekt aus Konfiguration. · **Browser-Prüfpfad:** Konfiguration → „Angebot erstellen" → `offer_details`.
- **Risiken:** Preisanker fehlt (OMD/IDS) → kein Schreibpfad bauen. · **Abhängigkeiten:** braucht 7/8/9 + Preis (OMD/IDS). · **Stop-Kriterium:** Übergabe nur mit gültigem Preisanker + Reife-Gate. · **Fortschritt:** ~40 % (Gate+Reife+Cockpit da, Übergabe-Schreibpfad blockiert). · **Nächster Slice:** blockiert bis OMD/IDS — dortiger Feinplan `bereich2-p3d2b-*`.

### Kapitel 13 — Technische Prüfung, Freigabe, Versionierung
- **Ziel:** technische Freigabe, Prüfchecklisten, dokumentierte Abweichungen, Versionen, Verlauf, „wer hat was geändert".
- **Warum nötig:** Vor Angebotsübergabe muss eine Auslegung **freigegeben** sein; Nachvollziehbarkeit (GoBD-nah).
- **Führende Wahrheit:** `Anforderungsprofil`-Versionierung (append-only, aktive Version, `abgeloest_durch_id`) + Historie.
- **ticket-Bausteine:** `Anforderungsprofil`-Versionierung, `AnforderungsprofilService` (anlegen/aktivieren/neueVersion), `customer_histories`.
- **pg-Bausteine:** `AnlagenKonfigSnapshot` (sha256, Vorbild), `history_entries` (Konzept).
- **Wiederverwendet:** ticket-Versionierung. **Nicht übernommen:** zweites Audit-System.
- **Offene Lücken:** Freigabe-Workflow (Prüfschritt Projektleiter, vgl. Weiche 6); Prüfchecklisten; Abweichungs-Doku.
- **Backend:** Freigabe-Status + Prüfschritt am Profil. · **Frontend:** Freigabe-/Prüf-UI, Versionsverlauf.
- **Testpfad:** nur freigegebene Version → Angebot; Verlauf lückenlos. · **Browser-Prüfpfad:** Auslegung → Prüfen → Freigeben → Version.
- **Risiken:** Freigabe umgehbar. · **Abhängigkeiten:** braucht 6/7; Tor zu 12. · **Stop-Kriterium:** Freigabe erzwungen, Verlauf sichtbar. · **Fortschritt:** ~20 % (Versionierung da, Freigabe-Workflow offen). · **Nächster Slice:** Konzept Freigabe-Workflow.

### Kapitel 14 — UI/UX & Designsystem
- **Ziel:** Ticket-CI (Vuexy/Blade), Arbeitsraum, Schrittnavigation, rechte Infospalte, Statuschips, Dark Mode, responsive, Barrierefreiheit.
- **Warum nötig:** Konsistenz; kein zweites Design (playground ist keine Vorlage). UX-Audit belegt: kein Design-System (253 Farben/152 Buttons).
- **Führende Wahrheit:** ticket-CI/Vuexy + `docs/ux-frontend-audit.md`.
- **ticket-Bausteine:** Vuexy-Layout, `admin.layouts.app`, bestehende Panels, UX-2 Objektprofil-Tabblock.
- **pg-Bausteine:** Arbeitsraum-/Schrittnavigations-Idee (Konzept, nicht Stil). · **wb:** —
- **Wiederverwendet:** ticket-CI. **Nicht übernommen:** playground-Design/Alpine-Wildwuchs (nur 2 erlaubte Scopes).
- **Offene Lücken:** kein Design-System; Arbeitsraum-Shell fehlt; Dark Mode/Barrierefreiheit uneinheitlich.
- **Backend:** — · **Frontend:** Arbeitsraum-Shell-Konzept, Komponenten-Bibliothek, Statuschips.
- **Testpfad:** — (visuell). · **Browser-Prüfpfad:** Arbeitsraum-Shell in Ticket-CI, Dark Mode, responsive.
- **Risiken:** zweite CI (verboten); Alpine-Wildwuchs. · **Abhängigkeiten:** rahmt 2/6/7/8. · **Stop-Kriterium:** Arbeitsraum-Shell-Konzept in Ticket-CI, Design-Regeln fixiert. · **Fortschritt:** ~20 %. · **Nächster Slice:** Arbeitsraum-Shell-Konzept (mit Fachagenten).

### Kapitel 15 — Tests, Browserpfade, Abnahme
- **Ziel:** je Kapitel Teststrategie, Browserprüfung, Regression, Evaluator, Abnahmekriterien.
- **Warum nötig:** Qualitäts-/Abnahme-Tor; UI-Änderungen brauchen Browser-/Screenshot-Prüfung (CLAUDE.md-Fachagenten-Pflicht).
- **Führende Wahrheit:** `tests/Feature/*`, Test-DB `ticket_testing` (Isolation SET FK=0/sql_mode='').
- **ticket-Bausteine:** bestehende Feature-Tests (Cockpit 9 Tests, Reife-Gate, Progressbar), RefreshDatabase-Muster.
- **Wiederverwendet:** Test-Infra. **Nicht übernommen:** —
- **Offene Lücken:** keine kapitelweise Abnahme-Matrix; Browser-Pfade nicht dokumentiert.
- **Backend/Frontend:** Test- + Browser-Pfad je Kapitel. · **Testpfad:** Regression grün vor Commit. · **Browser-Prüfpfad:** je Kapitel definiert.
- **Risiken:** ungetestete UI. · **Abhängigkeiten:** Querschnitt. · **Stop-Kriterium:** Abnahme-Matrix je Kapitel. · **Fortschritt:** ~30 %. · **Nächster Slice:** Abnahme-Matrix-Vorlage.

### Kapitel 16 — Projektakte
- **Ziel:** alle Konfigurationen/Auslegungen/Angebote/Freigaben je Objekt als 360°-Akte (chronologisch).
- **Warum nötig:** objekt-zentrierte 360°-Sicht (Zielbild); Ausgang für Betrieb/Service.
- **Führende Wahrheit:** Objekt (`lead_alternative_adds`) + Historie + Konfigurationsprojekt-Sicht (Kap. 2).
- **ticket-Bausteine:** Kundenprofil/Objekt-Galerie, `customer_histories`, Objektprofil-Tabblock (UX-2).
- **Wiederverwendet:** Objektprofil. **Nicht übernommen:** zweite Akte.
- **Offene Lücken:** Aggregation aller Gewerke/Versionen je Objekt.
- **Backend:** Akte-Read-model. · **Frontend:** Zeitleiste/Akte je Objekt.
- **Testpfad:** Akte zeigt alle Vorgänge je Objekt. · **Browser-Prüfpfad:** Objekt → Projektakte.
- **Risiken:** verfrüht. · **Abhängigkeiten:** braucht 2/12/13. · **Stop-Kriterium:** Akte aggregiert, read-only. · **Fortschritt:** ~15 %. · **Nächster Slice:** später.

---

## 4. Fachlich richtige Reihenfolge

**Grundprinzip (Zielbild + CLAUDE.md):** erst Fundament (Konzept→Workflow→Verknüpfung), dann Fachmodule, dann Cross-Gewerk. **Nicht** WP/PV weiterbauen, bevor die gemeinsame Gebäude-/Konfigurationsbasis konzipiert ist.

**Bearbeitungsreihenfolge (≠ Kapitelnummer):**

```
0 Governance (dauerhaft)   1 Landkarte (fast fertig, PV-Inventur nachziehen)
        │
        ▼
[SICHERHEITS-FIX PARALLEL, sofort]  ── Kap. 6 / Paket 5b (Belastbarkeits-Gate)
        │
        ▼
2 Konfigurationsarbeitsraum (Klammer-Weiche)  ← Kern-Unlock
        │
        ├──► 3 Gebäudebasis ─┐
        │                    ├──► 4 Geometriemodell ──► (Voraussetzung Kap. 8)
        │    5 Referenzdaten ─┘
        ▼
6 Heizlast am Kern ──► 7 WP am Kern (Varianten/Ranking)
        │
        ├──► 8 3D-PV (NACH Geometrie) ──► 9 Speicher/Wallbox ──► 10 Gesamtenergie
        │
        ▼
12 Angebotsübergabe (blockiert bis Preisanker OMD/IDS) · 13 Freigabe · 16 Projektakte
14 UI/UX + 15 Tests: Querschnitt, durchgängig
11 Dach/Fassade/Fenster: nach Geometrie, parallel zu 9/10
```

**Konkrete Antworten auf die Bewertungsfragen:**
- **Muss Kap. 2/3/4 vor WP weitergeführt werden?** → **Ja.** WP darf nicht als Insel weiter; die gemeinsame Klammer (2) + Gebäudebasis (3) + Geometrie (4, zumindest konzeptionell) müssen stehen, bevor WP-Wizard/Varianten (7) gebaut werden. **Ausnahme:** 5b (reiner Guard) läuft unabhängig.
- **Kann 5b trotzdem vorher als Sicherheitsfix laufen?** → **Ja** (s. §9). Klein, additiv, plattform-neutral.
- **Muss vor 3D-PV zuerst die gemeinsame Geometrie definiert werden?** → **Ja** (s. §10, Option B nach A). 3D-PV ohne gemeinsames Geometriemodell erzeugt ein weiteres Geometrie-Silo.
- **Was kann parallel laufen?** → Governance (0) + Landkarte/PV-Inventur (1) + 5b-Guard (6); später Referenzdaten-Härtung (5) parallel zu Konzept 2/3/4; UI/UX (14) + Tests (15) durchgängig.
- **Was darf erst später gebaut werden?** → 8 (nach 4), 9/10/11 (nach 7/8), 12 (nach Preisanker), 13/16 (nach 2/12).

---

## 5. Aktueller Stand — Einordnung aller bisherigen Arbeiten

| Bisherige Arbeit | Kapitel | Reifegrad | Bleibt / wandert |
|---|---|---|---|
| **P1-a Preis-Integrität** (`CatalogPriceGuard`, `component_id`) | 12 (+5) | fertig | **bleibt** (führende Preiswahrheit) |
| **WP-Formular** (`ProductFormula`, F2) | 6/7 | teilweise | bleibt; später am Kern verankern |
| **F2 v2 Render/Speichern** (Alpine-Scope Formulare) | 6/7/14 | teilweise | bleibt (erlaubter Alpine-Scope) |
| **Angebotsreife** (`OfferReadinessService`) | 12 | fertig für MVP | bleibt (führende Reife on-the-fly) |
| **Angebotsreife-Gate** (`OfferReadinessGate`, alle WP-Pfade) | 12 | fertig | bleibt |
| **Kanban/Badge/Filter** (Readiness-Badges) | 12/15 | fertig | bleibt |
| **useTemplate H1/H2/H3** (Robustheit, 500→422) | 12 | fertig | bleibt |
| **Auslegungsvorschau P3-c** (`AuslegungVorschlagService`, read-only) | 7/12 | Vorstufe (read-only) | bleibt; wandert in Arbeitsraum |
| **Katalog-Matching P3-d0a** (`WpKatalogMatchingService`, Diagnose) | 7/12 | Vorstufe (read-only) | bleibt; speist Angebotsübergabe |
| **UX-2 Objektprofil-Tabblock** (3 Panels gebündelt) | 2/16 | Vorstufe | **wandert** in Arbeitsraum-Shell (Kap. 2) |
| **Paket 4a Angebotsworkflow-Cockpit** (read-only) | 2/7/12 | Vorstufe (read-only) | bleibt; **wird Gewerk-Kachel** im Arbeitsraum |
| **WP-Auslegungswizard-Gap-Analyse** (Doku) | 7 | konzipiert | bleibt (Input für Kap. 7) |
| **id=16 Seeder-Fix** (WP-Artikelgruppe per Name) | 5/6 | fertig | bleibt |
| **Doku-/Governance-Pakete** (agents, betriebsordnung, bauordnung) | 0 | fertig | bleibt (Dauerkapitel) |
| **wberechnung-Energie-Transplant** (`Heizlast/Energie/Klima`-Services) | 5/6/7 | teilweise gebaut | bleibt; **an Objekt-Anker koppeln** |

**Muster:** Alles Gebaute **bleibt bestehen** (keine zweite Wahrheit erzeugt). Die **read-only Vorstufen** (P3-c, P3-d0a, UX-2, 4a-Cockpit) wandern konzeptionell in den gemeinsamen Arbeitsraum (Kap. 2) — sie werden **generalisiert, nicht ersetzt**.

---

## 6. Fortschrittsübersicht (große Tabelle)

| Kap | Fortschritt | Status | erledigt | offen | blockiert | nächster Schritt |
|---|---:|---|---|---|---|---|
| 0 Governance | 100 % | abgeschlossen | Regeln/Zyklus/Archiv | — | — | Dauerbetrieb |
| 1 Landkarte | 80 % | inventarisiert | 3 Inventuren+Gap | PV-Inventur | — | PV-Funktionsinventur |
| 2 Arbeitsraum | 25 % | konzipiert | Anker+Träger | Klammer-Sicht | — | Klammer-Weiche+Konzept |
| 3 Gebäudebasis | 40 % | teilweise gebaut | Daten+Klima | führende Entität | — | Konzept Basis+Adapter |
| 4 Geometrie | 30 % | teilweise gebaut | 3D/SVG/Modelle | gemeinsames Modell | — | Geometriemodell-Konzept |
| 5 Referenzdaten | 55 % | teilweise gebaut | Kernkataloge | Härtung/Quellen | — | to_verify-Register |
| 6 Heizlast | 65 % | teilweise gebaut | Rechenkern | Gate+Anker | — | **Paket 5b (jetzt)** |
| 7 WP-Konfigurator | 45 % | teilweise gebaut | Rechenkern+P3-c | Wizard/Varianten/Ranking | Insel-Verbot | Bivalenz verdrahten (nach 2) |
| 8 3D-PV | 10 % | inventarisiert | Erst-Inventur | Parität/Migration | wartet auf Kap. 4 | PV-Inventur (read-only) |
| 9 Speicher/Wallbox | 15 % | inventarisiert | Modelle | Sizing am Kern | wartet 7/8 | später |
| 10 Gesamtenergie | 5 % | nicht begonnen | Teil-Rechner | Bilanz | wartet 7/8/9 | später |
| 11 Dach/Fassade/Fenster | 10 % | inventarisiert | Bauteilmodell | Aufmaß/Kopplung | wartet Kap. 4 | später |
| 12 Angebotsübergabe | 40 % | teilweise gebaut | Gate/Reife/Cockpit | Schreib-Übergabe | **Preisanker OMD/IDS** | OMD/IDS-Feinplan |
| 13 Freigabe/Version | 20 % | teilweise gebaut | Versionierung | Freigabe-Workflow | — | Konzept Freigabe |
| 14 UI/UX | 20 % | konzipiert | UX-Audit | Arbeitsraum-Shell | — | Shell-Konzept (Fachagenten) |
| 15 Tests/Abnahme | 30 % | teilweise gebaut | Test-Infra | Abnahme-Matrix | — | Matrix-Vorlage |
| 16 Projektakte | 15 % | inventarisiert | Objektprofil | Aggregation | wartet 2/12 | später |

---

## 7. MVP-Plan

| MVP | Umfang | Abhängigkeiten | Browserprüfbar | NICHT enthalten | Abnahmekriterien |
|---|---|---|---|---|---|
| **MVP 0** — Governance + Inventur + Zielarchitektur | Kap. 0/1 + beide Gap-Analysen + dieser Fahrplan | — | — | jeder Bau | Inventuren+Gap+Fahrplan liegen vor, Wahrheiten benannt, PV-Inventur eingeplant |
| **MVP 1** — Konfigurationsarbeitsraum + Gebäudegrundlagen | Kap. 2 (Klammer-Sicht read-only) + Kap. 3 (führende Gebäudebasis konzipiert) | MVP 0 | Objekt → Arbeitsraum-Sicht (alle Gewerke, read-only) | Schreibpfade, PV-Bau | Arbeitsraum zeigt je Objekt alle Gewerke+Status, kein zweiter Wahrheitsträger, Ticket-CI |
| **MVP 2** — WP-MVP: belastbare Heizlast + read-only Systemvorschlag | Kap. 6 (5b-Gate) + Kap. 7 (Bivalenz verdrahtet, Varianten-Vorschau read-only) | MVP 1 | Heizlast-Ampel, WP-Varianten-Vorschau, Reife-Gate blockiert | Angebots-Schreibpfad, Preisanker | keine WP-Größe ohne belastbare Heizlast; Varianten mono/monoenergetisch/bivalent read-only sichtbar+begründet |
| **MVP 3** — 3D-PV-Planer in ticket integriert | Kap. 4 (Geometriemodell) + Kap. 8 (Migration/Bau) | MVP 1 (+ Geometrie) | 3D-Planer rendert in ticket, speichert Geometrie | PV-Ertragsdetails falls separat | Belegung→kWp serverseitig; Geometrie geteilt mit Heizlast; Ticket-CI |
| **MVP 4** — PV/WP/Speicher/Wallbox-Gesamtenergie | Kap. 9 + 10 | MVP 2 + MVP 3 | Objekt → Gesamtenergie-Bilanz | Feinoptimierung Lastmanagement | Bilanz konsistent aus Fachmodulen, keine Parallelrechnung |
| **MVP 5** — Angebotsübergabe + technische Freigabe + Projektakte | Kap. 12 + 13 + 16 | MVP 2/4 + Preisanker (OMD/IDS) | Konfiguration → Angebot → Akte | — | nur freigegebene+belastbare Konfiguration → `offer_details`; Akte je Objekt |
| **MVP 6** — Dach/Fassade/Fenster/Türen-Konfiguratoren | Kap. 11 | MVP 3 (Geometrie) | Aufmaß → Bauteil → Heizlast-Neurechnung | — | gemeinsame Geometrie/Bauteil, keine Doppelpflege |

---

## 8. Nächste 10 konkrete Arbeitspakete (klein, prüfbar, sequenziert)

> Die ersten Pakete sind bewusst klein und überwiegend read-only/konzeptionell; nur **AP-1** ist ein kleiner Bau.

| AP | Kap | Ziel | Warum jetzt | Scope | Nicht-Ziele | Backend | Frontend | Tests | Browserpfad | Risiko | Aufwand | Abhängigkeiten | Stop-Kriterium |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| **AP-1 · Paket 5b** | 6 | Heizlast-Belastbarkeits-Gate in WP-Reife | verhindert falsche Verbindlichkeit; plattform-neutral | Reifekriterium um `datenlage`-Prüfung schärfen | keine neue Tabelle, kein Wizard | `OfferReadinessService`-Kriterium erweitern (liest `anforderungsprofil_werte.datenlage`) | Reife-Anzeige „Heizlast belastbar?" | Feature: Reife blockiert bei unbelastbar; grün bei berechnet | Reife-Panel zeigt Belastbarkeits-Blocker | niedrig | S | keine | Gate greift, Tests grün, kein Schreibpfad |
| **AP-2 · PV-Inventur** | 1/8 | vollständige read-only PV-Funktionsinventur (ticket/pg/wb) | Voraussetzung für PV-Bau (§10 A) | Doku `docs/bereich2-pv-funktionsinventur.md` | kein Bau | — | — | — | — | niedrig | M | keine | Doku deckt alle PV-Funktionen+Doppelspuren |
| **AP-3 · Klammer-Weiche** | 2 | Konzept „Konfigurationsprojekt-Sicht" + Weiche Objekt-Klammer vs. eigene Sicht | Kern-Unlock; blockiert 6/7/8 | Konzeptdoku (read-model-Skizze) | keine neue Objekt-/Projekt-Tabelle | — | Arbeitsraum-Sicht-Skizze | — | — | mittel | M | AP-2 | Weiche entschieden, kein zweiter Wahrheitsträger |
| **AP-4 · Gebäudebasis-Konzept** | 3 | führende Gebäude-Entität + Adapter (Redundanz auflösen) | verhindert dritte Objekt-Redundanz | Konzeptdoku | kein Bestands-UPDATE | Adapter-Skizze `lead_alternative_adds`↔`heizlast_projekte` | Gebäudedaten-Panel-Skizze | — | — | mittel | M | AP-3 | Adapter-Weg belegt, additiv |
| **AP-5 · Geometriemodell-Konzept** | 4 | gemeinsames Geometrie-Datenmodell + `config_json`-Contract | Voraussetzung für 3D-PV (§10 B) | Konzeptdoku | keine three.js-Migration | Geometrie-Contract-Skizze | Editor-Strategie (SVG/three.js) | — | — | mittel | M | AP-4 | Contract definiert, Entkopplung Engine |
| **AP-6 · Arbeitsraum-Shell-Konzept** | 14/2 | UI-Shell je Objekt (Ticket-CI, Dark Mode, Schrittnavigation) | rahmt alle Fachmodule | Konzept + Fachagenten (Konzeption/Workflow/Architektur/Frontend) | kein Bau | — | Shell-Wireframe (Ticket-CI) | — | — | niedrig | M | AP-3 | Shell-Konzept in Ticket-CI, Design-Regeln fixiert |
| **AP-7 · Bivalenz verdrahten (Konzept)** | 7 | `BivalenzService` (0 Aufrufer) an WP-Route/Kern anbinden — Konzept + Verdrahtungsplan | Fach-Krone unerreichbar (VDI 4645) | Konzept + Testskelett | kein WP-Solitär | Verdrahtungs-Skizze am `Anforderungsprofil`-Kern | Bivalenz-Ergebnis-Panel-Skizze | Testskelett | — | mittel | M | AP-3, AP-1 | Plan am Kern, nicht WP-eigen |
| **AP-8 · WP-Varianten-Read-model (Konzept)** | 7 | Varianten mono/monoenergetisch/bivalent + Ranking, read-only | Master-Prompt-Kern; nach Klammer | Konzeptdoku (Ranking-Kriterien, Begründung) | keine Persistenz-Insel | Read-model-Skizze am Kern | Varianten-Vergleich-Wireframe | — | — | mittel | M | AP-7 | Ranking deterministisch+begründet, keine 2. Wahrheit |
| **AP-9 · 3D-PV-Migrations-Entscheidung** | 8 | Weg festlegen: einbetten / ticket-three.js ausbauen / neu | vor jedem 3D-Bau | Entscheidungsdoku (Pro/Contra, Aufwand) | kein Bau/Import | — | Migrations-Skizze | — | — | hoch | M | AP-2, AP-5 | Weg entschieden, Stack-Konflikt bewertet |
| **AP-10 · Abnahme-Matrix-Vorlage** | 15 | kapitelweise Test-/Browser-/Abnahme-Matrix | Qualitätstor für alle folgenden Pakete | Vorlage + Ausfüllung Kap. 6/7 | — | Testpfad-Vorlage | Browserpfad-Vorlage | — | — | niedrig | S | keine | Matrix nutzbar, Kap. 6/7 ausgefüllt |

**Sequenz:** AP-1 (Bau) + AP-2 (Inventur) können **parallel/sofort**. AP-3 ist der Gate zu AP-4→AP-5→AP-6/AP-7→AP-8; AP-9 nach AP-2+AP-5; AP-10 begleitend. Kein AP ist „riesig" — jeweils ein prüfbares Ergebnisdokument oder ein kleiner, getesteter Guard.

---

## 9. Spezielle Entscheidung: Paket 5b (Heizlast-Belastbarkeits-Gate)

**Entscheidung: JA — 5b darf sofort kommen, als kleiner Sicherheits-/Fachlogik-Slice, unabhängig vom größeren Zielbild.**

**Begründung:**
- **Es ist ein Guard, kein Struktur-Bau.** Es schärft ein **bereits existierendes** WP-Reifekriterium (`OfferReadinessService`), sodass „verbindliche WP-Größe" eine **belastbare Heizlast-Datenlage** verlangt. Die `datenlage`-Spalte in `anforderungsprofil_werte` existiert schon; heute wird nur die *Existenz* eines Heizlast-Werts geprüft, nicht seine Belastbarkeit — das ist die schärfste Lücke aus `bereich2-wp-auslegungswizard-gap-analyse.md` §5.
- **Es ist plattform-neutral.** Keine neue Tabelle, kein WP-Konfigurator, keine Persistenz, keine zweite Wahrheit. Es funktioniert unverändert weiter, egal wie die Klammer-Weiche (AP-3) fällt.
- **Es folgt dem Operanden-Gate** (bei unsicherer Datenlage blockieren/markieren statt weiterrechnen) — genau die CLAUDE.md-Direktive.
- **Es verhindert genau den gefährlichsten Fehler** (falsche Verbindlichkeit einer WP-Dimensionierung auf unbelastbarer Heizlast), bevor der große Umbau steht.

**Grenze:** 5b **nur** als Read-/Gate-Logik. **Kein** Auslegungs-Schreibpfad, **keine** Varianten-Persistenz (das wäre 5a → gesperrt bis Klammer steht). = **AP-1**.

---

## 10. Spezielle Entscheidung: 3D-PV

**Entscheidung: A zuerst (jetzt), dann B (vor Bau) — konkret: A → B → (später Bau nach Migrations-Weg).**

- **A. Vollständige playground-PV-Funktionsinventur (JETZT):** = **AP-2**. Wir brauchen die vollständige Karte (was ist Konstruktion/Belegung/BOM vs. Ertrag/Wirtschaftlichkeit/Elektroauslegung; was ist `@ts-nocheck`-Prototyp; welche Utils sind framework-frei; welche pg-APIs sind Pflicht). Ohne diese Karte ist jede Migrations-Entscheidung Raten. Kleiner, read-only, sofort machbar.
- **B. Gemeinsames Geometriemodell zuerst (vor Bau):** = **AP-5**. Der 3D-Planer erzeugt/braucht Geometrie; ohne gemeinsames Geometriemodell (Kap. 4) entsteht ein **weiteres Geometrie-Silo** neben `raum_geometrien`/`roof_config`. Also: **kein 3D-PV-Bau vor dem Geometrie-Contract.**
- **C. Kleiner read-only Vergleich:** in A enthalten (Teil der Inventur).
- **D. Noch warten:** gilt für den **Bau/die Migration** — ja, der Bau wartet auf A+B+Migrations-Weg (**AP-9**).

**Begründung:** 3D-PV ist der **unreifste, stack-fremdeste (React), schuldenreichste** Baustein und blockiert nichts. Reihenfolge: erst wissen (A), dann Fundament (B/Geometrie), dann Weg entscheiden (AP-9), dann — deutlich später — bauen. **Nicht** vor WP, **nicht** vor Geometrie.

---

## 11. Spezielle Entscheidung: UI/UX

**Entscheidung: Konzept des gemeinsamen Arbeitsraums als UI-Shell ZUERST (als Konzept/Wireframe, nicht als Vollbau) — parallel dürfen reife Fachmodule ihre read-only-Vorstufen behalten.**

**Begründung:**
- Die Fachmodule (WP-Varianten, 3D-PV, Speicher) brauchen einen **gemeinsamen Rahmen** (Arbeitsraum je Objekt mit Gewerke-Spalten, Schrittnavigation, rechte Infospalte, Statuschips). Baut man Fachmodule ohne diesen Rahmen weiter, entstehen **UI-Inseln**, die später teuer zusammengeführt werden — genau das Anti-Muster.
- **Aber:** Der Arbeitsraum wird **zuerst als Konzept/Wireframe** entschieden (**AP-6**, mit den vier Pflicht-Fachagenten Konzeption/Workflow/Architektur/Frontend-Design), **nicht** als Vollbau vor den Fachmodulen — sonst baut man eine leere Shell.
- **Ticket-CI verbindlich:** Vuexy/Blade/jQuery; **kein** playground-Design; Alpine nur in den zwei erlaubten Scopes (heizkoerper/formulare). Dark Mode + responsive + Barrierefreiheit sind Shell-Anforderungen. Das UX-Audit (`docs/ux-frontend-audit.md`) zeigt: kein Design-System vorhanden → die Shell muss die Design-Regeln (Farben/Buttons/Statuschips) mitdefinieren.
- **Reihenfolge:** Shell-**Konzept** (AP-6) parallel zur Klammer-Weiche (AP-3); Shell-**Bau** erst, wenn Klammer + erstes Fachmodul (WP read-only) tragen. Die vorhandenen read-only-Vorstufen (4a-Cockpit, UX-2-Tabblock) bleiben bis dahin und wandern dann in die Shell.

---

## 12. Regeln (verbindlich für alle Kapitel/Pakete)

- Keine Codeänderung außerhalb ausdrücklich freigegebener Pakete · Kein Commit · Kein Push (nur nach „Push frei") · Keine Migration (außer unvermeidbar → STOPP+Yama) · Keine Datenänderung · Kein Seeder gegen Dev-DB (nur Test-DB, wenn beauftragt) · Keine Löschung (Rückfall-/Archiv-Regeln) · Keine playground-Stilllegung · **Keine zweite Datenhaltung · keine zweite CI · keine zweite Rechteverwaltung · keine versteckten Frontend-Berechnungen** (kWp/Heizlast immer serverseitig, SSOT).
- Zusätzlich (CLAUDE.md): Kapitel-Startblock-Pflicht vor jedem Kapitel; Fachagenten-Pflicht bei UI/Workflow/Architektur; Operanden-Gate; Bau erst nach Konzept→Workflow→Verknüpfung.

---

## 13. Ergebnis / nächster Schritt

**Ergebnisdokument:** `docs/gesamtfahrplan-gebaeude-energie-angebot.md` (dieses Dokument).

**Empfohlener Einstieg (klein, sequenziert):**
1. **AP-1 (Paket 5b)** — kleiner Sicherheits-Guard, sofort baubar (nach Yama-Freigabe + Kapitel-Startblock).
2. **AP-2 (PV-Inventur)** — read-only, parallel.
3. **AP-3 (Klammer-Weiche)** — Kern-Unlock, danach 4/5/6/7.

**STOPP.** Kein Bau, kein Commit, kein Push. Yama prüft den Fahrplan und entscheidet das nächste Kapitel/Paket.

---

*Ende des Gesamtfahrplans. Read-only erstellt; keine Code-/Schema-/Datenänderung. Dieses Dokument ist die lebende Landkarte — es wird bei jedem abgeschlossenen Paket fortgeschrieben (Fortschritt %, Status, nächster Slice).*
