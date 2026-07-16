# Gebäude-/Energie-/Produktkonfigurationsplattform vs. isolierter WP-Konfigurator — Gap-Analyse

**Stand:** 2026-07-14 · **read-only** · **kein Bau, kein Commit, kein Push, keine Migration, keine Datenänderung, kein Seeder.**
**Zweck:** Klären, ob die Wärmepumpe (WP) weiter als isolierter Konfigurator (Paket 5) geplant werden darf — oder ob sie als Teil eines **gemeinsamen Gebäude-/Energie-/Produktkonfigurations-Arbeitsraums** geplant werden muss. Auslöser: mehrere Master-Prompts nach Paket 4 (WP-Auslegungsworkflow, WP-Systemkonfigurator mit Herstellervergleich/Ranking, Referenzdatenbanken + Heizlastwege + MVP-Reihenfolge, UI/UX-Konzept WP-Konfigurator, modulare Gebäude-/Energie-/Produktkonfiguration inkl. 3D-PV-Planer).

**Quellen (firsthand, read-only, 2026-07-14):**
- ticket-Entitäten-Inventur (Models/Migrationen/Services/Views/Controller in `/Users/yamanuri/Documents/ticket`).
- playground-Inventur (`/Users/yamanuri/Documents/Playground` inkl. `backend-laravel` + `src/planer`).
- wberechnung-Inventur (`/Users/yamanuri/Herd/wberechnung`).
- Stehende Entscheidungen: `docs/zielbild-objekt-zentriertes-crm.md`, `docs/architektur-entscheidungen.md` (Weiche 5), `docs/hierarchie-objekt-projekt-bestandsaufnahme.md`, `docs/playground-ticket-vergleich-entscheidung.md`, `docs/wberechnung-uebernahme-inventur.md`, CLAUDE.md-DAUERDIREKTIVE.

**Geltung:** Diese Analyse **stellt** die Architekturfrage — sie **entscheidet** sie nicht. Verbindliche Freigabe/Reihenfolge = Yama.

---

## 0. Ein-Satz-Befund (ehrlich, vorab)

> **Ja — WP muss ab jetzt als Teil eines gemeinsamen Konfigurationskerns geplant werden, nicht als Insel. Das ist aber keine neue Zielbild-Entscheidung, sondern die konkrete Ausformung des bereits ratifizierten objekt-zentrierten Zielbilds. Die gute Nachricht: der gemeinsame Kern existiert in ticket zu ~60–70 % bereits in Fragmenten (versioniertes `Anforderungsprofil` + Bauphysik-Modell + three.js-3D-Dach + Produktkonfiguration) — er ist nur bisher NUR für den WP/Heizlast-Strang durchgezogen. Der Weg ist Konsolidierung + Projekt-Klammer + PV-Einhängung, KEIN Neubau von Grund auf. Ein isolierter WP-Konfigurator mit eigener Persistenz wäre zu klein UND würde genau die zweite Wahrheit erzeugen, die die DAUERDIREKTIVE verbietet.**

Der einzige Bauschritt, der **jetzt** und **unabhängig** von dieser Architekturklärung sinnvoll bleibt, ist **Paket 5b (Belastbarkeits-Gate)** — ein kleiner Sicherheits-Fix, der eine bereits bestehende WP-Reifeprüfung schärft, ohne neue Struktur zu bauen. Begründung in §11.

---

## 1. Frage 1 — Muss WP jetzt als Teil einer gemeinsamen Plattform geplant werden?

**Ja.** Drei Gründe, alle belegt:

1. **Das Zielbild ist bereits so entschieden.** `docs/zielbild-objekt-zentriertes-crm.md` legt die **Drei-Schichten-Architektur** fest: (1) gemeinsames Gerüst (Objekt→Gewerk, Zustandsmaschine), (2) Gewerks-Fachmodule (WP=Heizlast/Auslegung, PV=Dach/Ertrag, …), (3) Cross-Gewerk-Intelligenz. WP ist dort ausdrücklich **ein Fachmodul, das in ein gemeinsames Gerüst einspeist** — nicht ein Solitär. Die Master-Prompts (WP-Wizard, Systemkonfigurator, 3D-PV, modulare Gebäude-/Energie-Konfiguration) sind **nicht** ein neues Ziel, sondern die Detaillierung von Schicht 1+2 dieses Zielbilds.
2. **Weiche 5 ist ratifiziert:** *„Das Objekt klammert, der Auftrag führt aus."* (`docs/architektur-entscheidungen.md`). Die Klammer über den Gewerken ist das **Objekt** (`lead_alternative_adds`), nicht das Angebot/Produkt. WP-Auslegung, PV-Auslegung und Speicher hängen fachlich am **selben Objekt** (Fenstertausch → Heizlast ↓ → WP-Größe ↓; PV → Speicher → Wallbox → Dachlast). Ein WP-Konfigurator, der das Objekt nicht als Klammer nutzt, widerspricht der Entscheidung.
3. **Die DAUERDIREKTIVE „eine Wahrheit je Sachverhalt"** verbietet eine zweite Angebots-/Preis-/Positions-/Auslegungswahrheit. Ein eigenständiger WP-Konfigurator mit eigener Persistenz (eigene Auslegungs-/Ergebnistabellen neben `Anforderungsprofil`) wäre genau so eine zweite Wahrheit.

**Ehrliche Selbstkorrektur zur bisherigen Planung:** Paket 4 (Cockpit) und die skizzierten Paket-5-Slices haben WP implizit als **eigenständigen Strang** behandelt. Als *read-only Sicht* (Cockpit) ist das unschädlich. Als *Bauplan für einen WP-Konfigurator mit eigener Schreib-/Auslegungslogik* wäre es **zu klein** und würde die Insel zementieren. Die Gap-Analyse `bereich2-wp-auslegungswizard-gap-analyse.md` (Paket-5-Richtung) muss vor diesem Hintergrund neu eingeordnet werden — sie ist fachlich richtig, aber ihre Träger-Annahme (WP-Wizard als eigener Bau) ist unter der Plattform-Sicht zu revidieren.

---

## 2. Frage 2 — Was bedeutet das für Paket 5?

Paket 5 (WP-Auslegungswizard) zerfällt in drei Slices mit **unterschiedlichem** Plattform-Bezug:

| Slice | Inhalt | Plattform-Abhängigkeit | Verdikt |
|---|---|---|---|
| **5b** | Belastbarkeits-Gate: „keine verbindliche WP-Größe ohne belastbare Heizlast-Datenlage" | **keine** — schärft ein bestehendes Reifekriterium (`OfferReadinessService`) über die schon vorhandene `datenlage`-Spalte in `anforderungsprofil_werte` | **darf jetzt gebaut werden** (kleiner, additiver Sicherheits-Fix, s. §11) |
| **5a** | Varianten-/Ergebnis-Vorschau (monovalent/monoenergetisch/bivalent + Ranking) | **hoch** — braucht die Frage „wo werden Varianten/Ergebnisse persistiert?" → gehört an den gemeinsamen Berechnungskern (`Anforderungsprofil`), nicht an eine WP-eigene Tabelle | **warten** bis Konfig-Kern-Weiche steht |
| **5c** | Geführter Eingabe-Wizard | **hoch** — ein Wizard ist der Eingabepfad in den gemeinsamen Arbeitsraum; als WP-Solitär gebaut, verbaut er den gemeinsamen Weg | **warten** bis Arbeitsraum-Konzept steht |

**Konsequenz:** Paket 5 wird **entkoppelt**. 5b läuft als eigenständiger Sicherheits-Posten (unabhängig von der Architektur). 5a/5c werden **nicht** als WP-Insel gebaut, sondern als Teil des gemeinsamen Konfigurationsarbeitsraums neu konzipiert — d. h. sie wandern konzeptionell in die spätere Phase „WP/Heizlast als Fachmodul am gemeinsamen Kern" (§10).

---

## 3. Frage 3 — Was passiert mit Paket 4a Cockpit — Vorstufe oder Umzug?

**Beides — und zwar unkritisch, weil 4a bewusst read-only und dünn ist.**

- **Heute:** Das Cockpit (`WpAngebotsWorkflowService` + `cockpit.blade.php`) ist eine **read-only Aggregation je Gewerkzeile** (`lead_product_lists`), also bereits **objekt-/gewerk-verankert**. Es erzeugt **keine** zweite Wahrheit (kein Schreibpfad, kein Angebot, kein Preisanker, kein `component_id`) — das ist per Test abgesichert.
- **Status:** **Vorstufe.** Es ist die richtige erste Sicht („wo stehe ich / was fehlt / warum kein Angebot"), aber heute **WP-spezifisch** und **gewerk-granular** (eine Zeile = ein Gewerk).
- **Späterer Umzug:** Im gemeinsamen Arbeitsraum wird die Sicht **objekt-granular** (ein Objekt zeigt PV + WP + Speicher nebeneinander). Das heutige WP-Cockpit wird dann zu **einer Gewerk-Kachel/Spalte** innerhalb der Objekt-Sicht. Die Aggregationslogik (Technik/Preis getrennt, Reife on-the-fly, nächste Aktion) ist wiederverwendbar; nur der Einstiegspunkt wandert von „je `lead_product_list`" zu „je Objekt, aufgeklappt nach Gewerken".

**Empfehlung:** 4a **behalten, nicht zu einem WP-Workflow-Motor ausbauen.** Kein Schreibpfad, kein „Angebot-erstellen-Flow" an 4a andocken, bevor der Arbeitsraum steht — sonst verfestigt sich die WP-Insel an genau der Stelle, die generalisiert werden soll. 4a bleibt die read-only Linse, bis der gemeinsame Arbeitsraum sie aufnimmt.

---

## 4. Frage 4 — Welche gemeinsamen Entitäten brauchen wir?

Die sieben genannten Entitäten sind die richtigen. Präzisierung, was jede im Plattform-Sinn leisten muss:

| # | Entität | Rolle im gemeinsamen Kern |
|---|---|---|
| 1 | **Konfigurationsprojekt** | Klammer, die **mehrere Energiesysteme/Gewerke je Objekt** in einem Arbeitsraum bündelt (PV+WP+Speicher+Wallbox am selben Gebäude). Heute die schwächste Stelle. |
| 2 | **Gebäude** | Eine **führende** Objekt-/Gebäudeentität (Adresse, Baujahr, Fläche, Hülle, Dach) — heute redundant über zwei Tabellen verteilt. |
| 3 | **Gebäudegeometrie** | Dach-/Wand-/Raum-Geometrie (Polygone, Segmente, 2D/3D), **entkoppelt von der Rechen-Engine**. |
| 4 | **Bauteil** | Bauphysikalisches Bauteil (Wand/Dach/Fenster mit U-Wert/Fläche/Konfidenz) — **nicht** Katalog-Artikel. |
| 5 | **Energiesystem** | Das **konfigurierte** System (WP-System, PV-System, Speicher, Wallbox) als **persistierte, versionierte** Entität — heute nur transient. |
| 6 | **Berechnung** | Versionierter, belegter Auslegungs-Träger (Heizlast/JAZ/Bivalenz/PV-Ertrag) mit Datenlage/Quelle je Wert. |
| 7 | **Produktkonfiguration** | Konkrete Geräte-/Set-Zusammenstellung + Preisintegrität (führende Wahrheit `offer_details.sections` + `CatalogPriceGuard`). |

**Wichtiger Zusammenhang (die eigentliche Architektur):** Diese sieben sind **keine flache Liste**, sondern eine Kette:
`Objekt (2) → Geometrie (3) → Bauteile (4) → Berechnung (6) → Energiesystem (5) → Produktkonfiguration (7)`, alle geklammert vom **Konfigurationsprojekt (1)** und verankert am **Objekt**. Genau diese Kette ist die konkrete Ausformung von Schicht 1 des Zielbilds.

---

## 5. Frage 5 — Was existiert bereits in **ticket**?

**Kurz: der generische Rahmen steht, aber nur der WP/Heizlast-Strang ist durchgezogen. PV/Speicher hängen als Silos daneben.**

| Entität | Status in ticket | Konkret (Beleg) |
|---|---|---|
| 1 Konfigurationsprojekt | **teilweise** | Objekt-Klammer `lead_alternative_adds` + n×`lead_product_lists` (je Gewerk). `Anforderungsprofil` polymorph an Objekt **oder** Gewerk verankerbar (`ERLAUBTE_ANKER`). **Aber:** `offers`/`projects` sind 1:1 pro `article_group` — **kein** gewerkeübergreifender Arbeitsraum. |
| 2 Gebäude | **teilweise, kein Model** | Objektdaten flach auf `lead_alternative_adds` (Baujahr, Fläche, Dach, Hülle …) **und** in `heizlast_projekte` (Berechnung). `building_data`/`building_types` als Stammdaten. **Kein** `Building`/`Gebaeude`/`Objekt`-Model — „Objekt" = `lead_alternative_adds.object_name`. Redundanz zwischen flacher Formular-Ebene und Berechnungs-Ebene. |
| 3 Gebäudegeometrie | **ja (jung, uneinheitlich)** | `raum_geometrien` (Polygon/Wandsegmente, 1:1 an `heizlast_raeume`), `anforderungsprofile.gebaeude_geometrie` (JSON), `GeometrieAbleitungService`/`RaumHuelleService`. 2D-Editor `GrundrissController` (SVG). **3D vorhanden:** `roof_config/roof.blade.php` (three.js 0.161, Orbit/Transform), `solar/configuration/configure.blade.php` (three.js 0.126 + GLTFLoader). Mapbox in `package.json`. |
| 4 Bauteil | **ja** | `heizlast_bauteile`/`HeizlastBauteil` (typ, u_strategie A/B/C, u_wert, schichten, konfidenz, quelle) + `konstruktionen`, `baualtersklassen`, `UWertService` (ISO 6946). |
| 5 Energiesystem | **schwach** | Nur `SolarSystem` (dünn: kwp, battery_capacity, price). WP-„System" nur transient (`WaermepumpenMatchService`), PV-„System" nur transient (`PvProjektService`, **nicht persistiert**). Geräte existieren als Katalog-Produkte (`Inverter`, `Battery`, `Heatpump`, `ProductPV`), aber **keine** gemeinsame „konfiguriertes System"-Entität. |
| 6 Berechnung | **ja für WP/Heizlast (stark), schwach für PV** | Versionierter Träger `Anforderungsprofil` (append-only, genau eine aktive Version, EAV `anforderungsprofil_werte` mit `datenlage`/`quelle`, `SchluesselRegistry`). Volle `app/Services/Heizlast/*`-Suite (HeizlastRechner, Bivalenz, JAZ, WpKennlinie, Warmwasser, Förderung …) + Klima. **PV-Rechnung** (`InverterSizingService`, `PvgisErtragService`) läuft **nicht** über das Profil, wird **nicht** versioniert; die Registry kennt **nur** WP/Heizlast-Schlüssel. |
| 7 Produktkonfiguration | **ja (reif)** | `master_sets`/`master_set_components`, `offer_details.sections` (führende Positionswahrheit), `CatalogPriceGuard` (Preisintegrität `component_id`). |

**Der generische Rahmen ist da:** `Anforderungsprofil` (polymorph, versioniert, EAV, „additiv erweiterbar") + Bauphysik-Modell + Produktkonfiguration. Er wurde bisher **nur für WP durchgezogen** — PV/Speicher-Schlüssel, -Adapter und -Persistenz fehlen. Der Weg zum gemeinsamen Kern ist damit **Integration + Konsolidierung**, kein Neubau.

**Altlasten/Doppelspur (für Konsolidierung vormerken, nicht jetzt anfassen):** `Radiator.php` = Wechselrichter-Altlast (Namenskollision mit `RadiatorSpec`=Heizkörper); zwei PVGIS-Anbindungen (`PVToolsController` legacy vs. `PvgisErtragService` kanonisch); `HeizlastService` (0 Aufrufer, überholt von `HeizlastProjektService`); `BivalenzService` (0 Aufrufer, „Krone" nicht erreichbar).

---

## 6. Frage 6 — Was existiert in **playground**?

**Kurz: playground hat eine sauberere Projekt-Klammer als konzeptionelle Blaupause + einen 3D-Dachkonstruktionsplaner (Prototyp) — aber es bleibt „Bauteile-Lager", kein zweites System** (stehende Entscheidung `docs/playground-ticket-vergleich-entscheidung.md`).

| Entität | Status in playground | Konkret |
|---|---|---|
| 1 Konfigurationsprojekt | **ja (sauber gedacht)** | `AnlagenKonfiguration` (`anlagen_konfigurationen`): bündelt customer/project/object/offer + `AnlagenKonfigPosition` je Gewerk (pv/wr/batterie/eps/wp/wallbox/montage) + `AnlagenKonfigSnapshot` (append-only, sha256-Hash). Cross-Domain **bewusst lose gekoppelt** (nullable IDs, kein Hard-FK). |
| 2 Gebäude | **ja** | `ProjectObject`/`objects` (Adresse, Typ, Baujahr, Pläne), `Liegenschaft` (übergeordnet), `EnergieObjectProfile` (Verbrauch, Wunsch pv/wp/speicher/wallbox, `field_meta`). |
| 3 Gebäudegeometrie (Dach) | **ja** | `EnergieRoofModel` (`geometry_json`, 1:1 je `object_id`), `RoofTemplate` (`config_json` Planer-State + abgeleitete Feature-Spalten). |
| 4 Bauteil | teilweise | über Dach-/Eindeckungs-/Montage-Kataloge; kein bauphysikalisches Hüllflächen-Bauteilmodell wie ticket/wberechnung. |
| 5 Energiesystem | **ja** | `AnlagenKonfiguration` + Positionen ist faktisch die „konfiguriertes System"-Entität, die ticket fehlt. |
| 6 Berechnung | **ja (verteilt)** | `EnergieHeizlastCalc`, `EnergiePvPlanning` (`input`/`ergebnis`/`solar`), `Auslegung`/`AuslegungMppt`/`AuslegungString`/`AuslegungErgebnis`, `PerformanceService`, `WirtschaftlichkeitEngine`. |
| 7 Produktkonfiguration | **ja** | `Product`/`Article`/`*Spec`, `SolarMount`/`MountingComponent`/`RoofTile`/`RoofCovering`, `KonfiguratorAngebotService`. |

**Read-only Lesefassade als Vorbild:** `PlanungskontextController` (`/api/energie/objekte/{object}/planungskontext`) aggregiert je Objekt Kunde + Energieprofil + vorhandene PV-/Heizlast-Planungen + Dokumente **und zeigt fehlende Felder je Gewerk** — genau das gesuchte „mehrere Energiesysteme je Objekt klammernde" Konzept. **Als Konzept-Blaupause wertvoll; nicht als Code-Import** (React/eigene Tabellen, widerspricht ticket-Stack + Ein-System-Regel).

---

## 7. Frage 7 — Was existiert in **wberechnung**?

**Kurz: der bauphysikalische Gebäude-/Bauteil-/Berechnungskern — und er ist bereits weitgehend nach ticket transplantiert** (`docs/wberechnung-uebernahme-inventur.md`).

| Entität | Status in wberechnung | Konkret |
|---|---|---|
| 1 Konfigurationsprojekt | teilweise | `Energiekonzept` (payload-JSON-Container) — app-spezifisch. Kein generisches Projekt. |
| 2 Gebäude | **ja** | `HeizlastProjekt`/`heizlast_projekte` (PLZ→Norm-Außentemp, Baujahr, Sanierungsstufe). |
| 3 Geometrie | **ja (Vorbild)** | `RaumGeometrie`/`raum_geometrien` (Polygon+Wandsegmente), **entkoppelt von der Engine** via `GeometrieAbleitungService`. |
| 4 Bauteil | **ja (Vorbild)** | `HeizlastBauteil` + `UWertService` (A bekannt / B konstruktiv ISO 6946 / C Baualter), Referenz `Material`/`Konstruktion`/`Baualtersklasse`. |
| 5 Energiesystem | teilweise | `Waermepumpe`/`WpAuslegung`/`Auslegung` — je Systemtyp, nicht generisch. |
| 6 Berechnung | **ja (Kern-Vorbild)** | `HeizlastRechner` (DB-frei, DIN EN 12831, Standard- + Auslegungsheizlast getrennt), Bivalenz/JAZ/Warmwasser/Förderung; Ergebnisse als `ergebnis`-JSON-Snapshot. |
| 7 Produktkonfiguration | ja | `Artikel` + `*_specs` + ETIM/DATANORM. |

**Zwei-Schichten-Regel (die wertvollste Architekturidee):** Physik/Referenz (standalone, keine Preise) vs. Produkt (`Artikel`+Specs). Referenzrichtung strikt **Produkt→Physik, nie umgekehrt** — hält die Physikschicht standalone-testbar. Diese Regel ist der Kompass für den gemeinsamen „Bauteil/Berechnung"-Kern.

**Transplant-Stand (aus der wberechnung-Übernahme-Inventur):** Heizlast-Kern byte-genau in ticket (`HeizlastRechner`/`RaumHuelleService`/`HeizlastNormwerte`), Referenz-Kataloge (`materials`/`konstruktionen`/`baualtersklassen`/`klima_plz` 8168), Geräte-Katalog eingefroren. **Offene Arbeit ist „verdrahten", nicht „portieren"** — die Energie-Kette läuft in ticket noch als Stand-alone-`/admin/energie/*`-Tool, **nicht** an die CRM-Objekt-Anker gekoppelt. Das ist dieselbe Lücke wie in §5 (Berechnung existiert, hängt aber nicht am gemeinsamen Kern/Objekt).

---

## 8. Frage 8 — Wie steht der 3D-PV-Planer aus playground dazu?

**Ehrlich und differenziert:**

**Was er ist:** primär ein **Zimmerer-/Dachdecker-Konstruktionsplaner** (Dachstuhl, Sparren, Lattung, Eindeckung, Gauben, Kehl-/Gratlinien), sekundär ein **PV-Belegungswerkzeug** (Modulraster, Kollision mit Störflächen, Montage-Befestiger, Stückliste/BOM, Werkstattplan). Kerndatei `src/pages/energie/DachplanerProPage.tsx` — **3786 Zeilen, `// @ts-nocheck`, Gemini-Prototyp** (eigener Header: „Härtung/Backend-Anbindung als Folgeschritt").

**Was er NICHT ist:** **kein PV-Ertragsplaner.** Keine kWh-Ertragssimulation, keine echte Verschattungsanalyse (das three.js-`shadowMap` ist reines Rendering), keine Azimut-/Neigungs-Ertragsrechnung, keine Wirtschaftlichkeit, keine String-/MPPT-Elektroauslegung. Er rechnet nur `{count, kwp, weight, price}`. Die energetische PV-Kette liegt **separat im playground-Backend** (`Services/Energie/*`, `Auslegung*`, `WirtschaftlichkeitEngine`).

**Stack-Konflikt (wichtig):** Der Planer ist **React 19 + rohes three.js** — die einzige verbliebene React-Insel nach playgrounds „React→Blade"-Migration. ticket ist **Blade/Vuexy/jQuery**, Alpine nur in zwei Scopes (CLAUDE.md). React ist **stack-fremd**. Übernahme = eine React-Insel in eine Blade-App + `@ts-nocheck`-Altschuld.

**ticket ist hier NICHT auf der grünen Wiese:** ticket hat **bereits** three.js-3D-Editoren (`roof_config/roof.blade.php` 0.161, `solar/configuration/configure.blade.php` 0.126 + GLTFLoader). Die Frage ist also **nicht** „3D-Planer neu holen", sondern „ticket-eigene 3D-Editoren ausbauen **oder** playground-Planer einbetten **oder** neu bauen".

**Der wertvolle, übernehmbare Teil** (falls überhaupt): nicht die React-UI, sondern (a) die **~30 framework-freien, getesteten Geometrie-/Mengen-Utils** (`src/utils/*.ts`: Polygonfläche, Dachverschneidung, Gaubengeometrie, Holzmengen, Belegung, Werkstattplan), (b) der **`config_json`-Datenvertrag** + serverseitige Ableitung `PvBelegungExtractor`/`RoofTemplateFeatureExtractor` (kWp wird server-seitig aus der Belegung abgeleitet, Frontend-kWp wird nicht vertraut — SSOT-Muster).

**Einordnung:** Der 3D-PV-Planer ist ein **Phase-3-Kandidat, NICHT jetzt**. Er ist der **am wenigsten reife, technisch schuldenreichste, stack-fremdeste** Baustein — er blockiert nichts und darf nicht vor dem gemeinsamen Kern und vor dem reifen WP/Heizlast-Strang gebaut/migriert werden. Die Übernahme-Bewertung (einbetten vs. ticket-three.js ausbauen vs. Utils extrahieren) ist eine eigene, spätere read-only Runde.

---

## 9. Frage 9 — Welche PV-Funktionen müssen vollständig inventarisiert werden?

**PV ist heute der schwächste und fragmentierteste Strang — vor jedem PV-Bau ist eine dedizierte PV-Inventur (analog zur WP-Auslegungswizard-Gap-Analyse) Pflicht.** Sie muss mindestens erfassen:

**In ticket (verstreut, teils Doppelspur):**
- Persistenz: `SolarSystem` (dünn), `PVRoof`/`PVLongRoof`/`PVRoofPlan`/`OfferRoofLayoutConfiguration`, `ProductPV`/`product_pv_module_specs`, `Inverter`, `Battery`/`BatterySystem`/`BatteryInverter`, `PowerOptimizer`.
- Berechnung (nicht versioniert): `InverterSizingService` (VDE-AR-N 4105/4110), `PvProjektService` (**0 Aufrufer, isoliert**), `PvgisErtragService`, `KostenService`, `RoofAreaEstimator`.
- Wirtschaftlichkeit: `ProfitabilityCalculation`/`ProfitabilityData`/`EconomicAssumption`/`EconomicCalculation`.
- 3D/Geometrie: `roof_config/*` + `solar/configuration/*` (three.js), Grundriss (SVG).
- **Doppelspur:** `PVToolsController::fetchByPostcode` (Legacy-PVGIS) vs. `PvgisErtragService` (kanonisch) — zwei PVGIS-Anbindungen.
- **Altlast:** `Radiator.php` = Wechselrichter (nicht Heizkörper).

**In playground (Vergleich/Kandidaten):** `Auslegung`/`AuslegungMppt`/`AuslegungString`/`AuslegungErgebnis`, `StringBuilderService`, `SchutzkomponentenService`, `KabelService`, `EpsBoxService`, `PerformanceService` (PR/Clipping), `WirtschaftlichkeitEngine`, `KonfiguratorAngebotService`, 3D-Planer (BOM/Werkstattplan), `SolarController::buildingInsights` (Google-Solar-Proxy, Frontend fehlt).

**In wberechnung (Rest-Referenz):** `PvProjektService`, `StringBuilderService`, `InverterSuggestionService`.

**Die zentrale PV-Frage (analog zur WP-Belastbarkeit):** PV-Auslegung/-Ertrag wird **nirgends in ticket versioniert am gemeinsamen Kern** geführt — es gibt keine PV-Schlüssel in der `SchluesselRegistry`, keinen PV-Adapter, keine PV-Persistenz übers `Anforderungsprofil`. Bevor PV gebaut wird, muss entschieden sein, ob PV auf denselben versionierten Kern kommt wie WP (empfohlen) oder ein eigener Strang bleibt (widerspricht „eine Wahrheit").

---

## 10. Frage 10 — Welche Reihenfolge ist jetzt fachlich richtig?

Die vorgeschlagene Phasenfolge ist **im Kern richtig**, hat aber **eine ehrliche Korrektur**: Phase 3 (3D-PV-Migration) steht **zu früh**. Sie ist der unreifste/schuldenreichste Baustein (React-Prototyp) und blockiert nichts — während WP/Heizlast (Phase 4) der **reifste** Strang ist und nicht hinter der riskantesten Migration warten sollte. Korrigierte Reihenfolge:

| Phase | Inhalt | Status/Reife heute | Anmerkung zur Reihenfolge |
|---|---|---|---|
| **0 — Bestandsaufnahme** | Was existiert (ticket/playground/wberechnung) | **~erledigt** (diese 3 Inventuren + stehende Docs) | Ergebnis liegt vor; ggf. dedizierte PV-Inventur (§9) nachziehen. |
| **1 — Gemeinsamer Konfigurationsarbeitsraum** | Projekt-Klammer je Objekt (mehrere Gewerke/Systeme), führende Entscheidung Konfigurationsprojekt + Energiesystem-Entität; PV/Speicher an `Anforderungsprofil`-Kern (Registry+Adapter) | Rahmen da, Klammer fehlt | **Der eigentliche Unlock.** Zuerst — alles andere hängt daran. |
| **2 — Gebäude-/Geometriemodell konsolidieren** | Eine führende Gebäude-Entität (Redundanz `lead_alternative_adds` ↔ `heizlast_projekte` auflösen), Geometrie vereinheitlichen | Bauteil/Geometrie **großteils da** (aus wberechnung) | Meist Konsolidierung, kein Neubau. |
| **4 — WP/Heizlast als Fachmodul am Kern** | reife Heizlast-Kette an Objekt-Anker koppeln (statt Stand-alone `/admin/energie/*`), `BivalenzService` verdrahten, 5a/5c als Kern-Fachmodul | **reifster Strang** | **Vorziehen** vor 3D-PV. Nutzt Phase 1+2 direkt. |
| **5 — Speicher/Wallbox** | als Energiesystem-Positionen am Kern | dünn | nach WP, gleiches Muster. |
| **6 — Gesamtenergie** | Cross-Gewerk-Sicht/Bilanz je Objekt (Schicht 3) | offen | die „Krönung", zuletzt. |
| **3 — 3D-PV-Migration** | 3D-Planer bewerten (einbetten/ausbauen/neu) + PV-Rechenkette | unreif, stack-fremd | **ans Ende / isoliert.** Blockiert nichts; höchste Altschuld. |

**Merksatz der korrigierten Reihenfolge:** *Zuerst die Klammer (1) und das Gebäudemodell (2), dann den reifen WP-Strang einhängen (4), dann Speicher/Wallbox (5), dann Gesamtenergie (6) — und die 3D-PV-Migration (3) zuletzt/isoliert, weil sie die unreifste und stack-fremdeste ist.* Diese Reihenfolge folgt exakt dem Zielbild-Prinzip „erst Fundament, dann Smartness" und der systemweiten Optimierungs-Reihenfolge aus CLAUDE.md (① Konzept → ② Workflow → ③ Bausteine verknüpfen → ④ automatisieren).

---

## 11. Frage 11 — Was darf jetzt gebaut werden?

**Nur Contained, additive, plattform-neutrale Schritte, die keine zweite Wahrheit erzeugen:**

1. **Paket 5b — Belastbarkeits-Gate (empfohlen, jetzt baubar).** Begründung, warum es trotz Plattform-Klärung sinnvoll bleibt:
   - Es ist ein **Sicherheits-Fix**, kein Struktur-Bau: Es schärft ein **bereits existierendes** Reifekriterium (`OfferReadinessService`, WP) so, dass „verbindliche WP-Größe" eine **belastbare Heizlast-Datenlage** verlangt (die `datenlage`-Spalte in `anforderungsprofil_werte` existiert schon; heute wird nur die *Existenz* eines Heizlast-Werts geprüft, nicht seine Belastbarkeit — die schärfste Lücke aus `bereich2-wp-auslegungswizard-gap-analyse.md` §5).
   - Es ist **plattform-neutral:** Es baut **keine** neue Tabelle, **keinen** WP-Konfigurator, **keine** Persistenz — es liest den vorhandenen versionierten Kern und **blockiert** bei unbelastbarer Datenlage. Es funktioniert unverändert weiter, egal wie die Konfig-Kern-Weiche fällt.
   - Es folgt dem **Operanden-Gate** (bei Unsicherheit blockieren/markieren, nicht weiterrechnen) — genau die CLAUDE.md-Direktive.
   - Grenze: 5b **nur** als read-/gate-Logik; **kein** Auslegungs-Schreibpfad, **keine** Varianten-Persistenz (das wäre 5a und ist gesperrt).
2. **Read-only Analysen/Inventuren** (z. B. dedizierte PV-Inventur §9, Konzept für die Projekt-Klammer) — kein Bau.
3. **Konsolidierungs-Befunde** (Altlasten `Radiator.php`, PVGIS-Doppelspur, `HeizlastService`/`BivalenzService`-Verdrahtung) — als Befund/Vorschlag, **nicht** umsetzen, bevor die Kern-Weiche steht.

---

## 12. Frage 12 — Was darf ausdrücklich NICHT gebaut werden, bevor die Architektur geklärt ist?

**Nicht bauen, bis die Konfigurationskern-Weiche (Phase 1) entschieden ist:**

1. **Kein isolierter WP-Konfigurator / WP-Wizard mit eigener Persistenz** (5a Varianten-Persistenz, 5c geführter Wizard als WP-Solitär). → würde die Insel zementieren.
2. **Keine zweite Konfigurationsprojekt-/Arbeitsraum-Struktur.** Insbesondere **kein** Import von playgrounds `AnlagenKonfiguration`-Tabellen als zweites System (nur Blaupause). Die Klammer-Entscheidung (Objekt vs. neue Projekt-Entität) ist zuerst zu treffen — und hängt an Weiche 5 (Objekt klammert).
3. **Kein PV-Persistenz-Silo / keine zweite PV-Wahrheit.** Kein PV-Auslegungs-Schreibpfad, der **nicht** über den gemeinsamen `Anforderungsprofil`-Kern läuft.
4. **Keine 3D-PV-Migration / kein React-Insel-Import** (Frage 8) — unreif, stack-fremd, blockiert nichts.
5. **Keine neue Gebäude-/Energiesystem-Tabelle**, bevor die führende Entität entschieden ist (sonst dritte Objekt-Redundanz neben `lead_alternative_adds` + `heizlast_projekte`).
6. **Keine Auto-Anker / keine erfundenen Operanden** (Preis-/Kataloganker, Heizlast-Schätzwerte) — Operanden-Gate.
7. **Keine Verdrahtung von `BivalenzService`/Energie-Kette an CRM-Anker** als Schnellschuss — das ist Teil von Phase 4 und braucht die Kern-Weiche zuerst (sonst wird an die falsche/keine Klammer gekoppelt).

---

## 13. Empfehlung (zusammengefasst, zur Freigabe durch Yama)

1. **Architektur-Sicht bestätigen:** WP ist ein **Fachmodul am gemeinsamen Kern**, kein Solitär (= bereits ratifiziertes Zielbild). Die Master-Prompts sind die Detaillierung von Schicht 1+2, kein neues Ziel.
2. **Jetzt bauen (klein, sicher, plattform-neutral):** **Paket 5b — Belastbarkeits-Gate.** Contained, additiv, kein zweiter Wahrheitsträger.
3. **Als Nächstes entscheiden (Weiche, read-only vorbereiten):** Phase 1 — der gemeinsame Konfigurationsarbeitsraum: (a) Konfigurationsprojekt = Objekt-Klammer (Weiche 5 fortführen) oder eigene Projekt-Entität? (b) führende Gebäude-Entität? (c) PV/Speicher auf den `Anforderungsprofil`-Kern (Registry + Adapter)? (d) Energiesystem als persistierte, versionierte Entität?
4. **Reihenfolge korrigieren:** 0 (erledigt) → **1 Kern/Klammer** → **2 Gebäude/Geometrie konsolidieren** → **4 WP/Heizlast an den Kern** → **5 Speicher/Wallbox** → **6 Gesamtenergie** → **3 3D-PV zuletzt/isoliert**. (3D-PV **nicht** vor WP.)
5. **Nicht bauen** (§12): kein WP-Solitär, keine zweite Klammer, kein PV-Silo, keine 3D-PV-Migration jetzt.
6. **Vor PV-Bau:** dedizierte PV-Inventur (§9) analog zur WP-Gap-Analyse.

**Ehrliche Kernaussage:** Die bisherige WP-Planung war **als isolierte Insel gedacht zu klein** — aber der gemeinsame Kern ist **kein Neubau**, sondern zu ~60–70 % schon da (nur WP-durchgezogen). Der richtige nächste Schritt ist **nicht** „WP-Konfigurator bauen", sondern **den gemeinsamen Konfigurationskern konzeptionell schließen (Klammer + Gebäude-Entität + PV an den Kern)** — und parallel den einen sicheren, plattform-neutralen Sicherheits-Fix (5b) mitnehmen.

---

*Ende der read-only Gap-Analyse. Keine Code-, Schema- oder Datenänderung. Kein Commit, kein Push. Nächster Schritt laut Auftrag: STOPP — Yama entscheidet Architektur-Weiche + Reihenfolge.*
