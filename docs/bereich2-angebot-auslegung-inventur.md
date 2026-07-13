# Bereich 2 — Angebot, Auslegung & Kalkulation — Tiefen-Inventur

**Stand:** 2026-07-11 · **read-only** · **kein Bau/Import/Refactor/Löschen/Überschreiben/Automatisierung/Commit.**
**Auftrag:** Yama-Freigabe „Tiefen-Inventur Bereich 2". Nur Inventur, Anker-Klärung, Befund.
**Methode:** 4 parallele read-only-Lesestränge (Anker · BivalenzService-Verdrahtung · Angebotssystem/Kalkulation/Übernahme · Auslegungs-Breite), jeder mit `datei:zeile`-Belegen firsthand am Code.
**Geltung:** `docs/rueckfall-archiv-regeln.md` (kein Löschen/Überschreiben ohne Rückfallpfad; Archivierung von Alt-Logik = Variante B).

> **Ein-Satz-Befund:** Die gesamte sichtbare Auslegung (`/admin/energie/*`) läuft heute als **stand-alone Taschenrechner ohne CRM-Anker** (Eingabe = Formular, Ausgabe = Bildschirm/PDF, kaum Persistenz), das **Angebot** hat **zwei parallele Positions-Wahrheiten** und **zwei Kalkulations-Engines**, und die fachlich wertvollste Rechenlogik (`BivalenzService`) liegt **ungenutzt** — während ein **aktives Frontend dieselbe Bivalenz-Logik im JavaScript nachbaut**.

---

## SCHRITT 0 — ANKER-KLÄRUNG: Woran hängt die Auslegung fachlich?

### 0.1 Kurzantwort
**An nichts aus dem CRM.** Jeder Auslegungs-Endpoint nimmt seine Eingaben aus dem **HTTP-Formular** (manuelle Freieingabe) und wirft das Ergebnis auf Bildschirm/PDF. Kein Endpoint liest/schreibt `new_leads` (Kunde), `lead_alternative_adds` (Objekt), `lead_product_lists` (Gewerk) oder `offers` (Angebot). Belegt per Grep über `app/Http/Controllers/Energie/`: **kein einziger Treffer** auf `new_lead|lead_alternative|lead_product|offer|anforderungsprofil`.

### 0.2 Endpoint → Anker → Persistenz (Beleg-Auszug)

| Route | Controller::Methode | Anker | Ergebnis persistiert? | Beleg |
|---|---|---|---|---|
| `wp-auslegung.berechnen` | `EnergieAuslegungController::wpBerechnen` | **STAND-ALONE** (`heizlast_kw`, `wp_typ`, `investition`…) | flüchtig (nur View) | `EnergieAuslegungController.php:196-220,272` |
| `wr-auslegung.berechnen` | `::berechnen` | **STAND-ALONE** (`module_index`, `inverter_index`, `parallel_strings`…) | flüchtig | `:48-55,105` |
| `heizlast.berechnen` | `HeizlastController::berechnen` | **STAND-ALONE** (`raum.*`, `bauteile[]`) | **transient** `HeizlastProjekt` + sofort `delete()` | `HeizlastController.php:56,120-183` (delete `:171`) |
| `sanierung.berechnen` | `SanierungController::berechnen` | **STAND-ALONE** | transient + `$projekt->delete()` | `SanierungController.php:158-217` |
| `energiekonzept.berechnen` | `EnergiekonzeptController::berechnen` | **STAND-ALONE**, Kunde = **Freitext** (`kunde.name/plz/ort/berater`) | flüchtig / transient | `EnergiekonzeptController.php:133-136,352-410` |
| `grundriss.speichern` | `GrundrissController::speichern` | **STAND-ALONE** (Geometrie-JSON) | **PERSISTENT** `HeizlastProjekt`+`HeizlastRaum`+`RaumGeometrie` — **OHNE** Lead/Objekt/User-FK | `GrundrissController.php:208-281` |
| `plan-upload.store` | `PlanUploadController::store` | Anker = **`user_id`** (+optional `heizlast_projekt_id`) | PERSISTENT `PlanUpload` | `PlanUploadController.php:45-70` |

Nur **zwei** Endpoints persistieren überhaupt dauerhaft (`grundriss.speichern`, `plan-upload.store`) — und selbst dort **ohne Kunden-/Objekt-Bindung**. `HeizlastProjekt::$fillable` enthält `name, standort_plz, …, energiekonzept_id, ergebnis` — **keine** `lead_id`/`objekt_id`/`user_id` (`HeizlastProjekt.php:19-23`). Die Grundriss-Liste zeigt daher **alle** Projekte global, ungefiltert (`GrundrissController.php:47`).

### 0.3 Der gebaute, aber tote „richtige" Anker: `Anforderungsprofil`
Es existiert die **eigentlich vorgesehene** führende Bedarfsschiene — sauber entworfen, versioniert, polymorph:
- `Anforderungsprofil::ERLAUBTE_ANKER = [LeadAlternativeAdd::class, LeadProductList::class]` → **Objekt kanonisch, Gewerk optional, Kunde bewusst ausgeschlossen** (`Anforderungsprofil.php:44-50`).
- `saving`-Hook `verankerungPruefen()` mit exists-Check (`:57-74`).
- Rechen-Adapter `AnforderungsprofilHeizlastAdapter::berechneUndSchreibe()` → **derselbe** `HeizlastRechner` wie der aktive Pfad (`AnforderungsprofilHeizlastAdapter.php:50`).

**Aber:** Grep über `app/Http/Controllers/` + `routes/` = **kein Controller, keine Route** nutzt es; `berechneUndSchreibe()` hat **0 externe Aufrufer**. Das Anker-Framework ist gebaut, aber an keine Fläche angeschlossen.

### 0.4 Führend (SOLL) vs. Doppelwahrheiten (IST)
- **Was führend sein MUSS (SOLL):** `anforderungsprofile`, verankert am **Objekt (`lead_alternative_adds`)** kanonisch / **Gewerk (`lead_product_lists`)** optional — als versionierte Bedarfs-/Operanden-Wahrheit der Auslegung. Kunde (`new_leads`) klammert darüber, ist aber bewusst **nicht** der Auslegungs-Anker.
- **Doppelwahrheiten (belegt):**
  1. **Zwei Heizlast-Eingabemodelle → EIN Kern.** Aktiver Pfad: Formular → transienter `HeizlastProjekt` → `HeizlastProjektService::fuerProjekt()` → `HeizlastRechner` (`HeizlastProjektService.php:296-323`). Toter Pfad: `Anforderungsprofil` + `gebaeude_geometrie`-JSON → `AnforderungsprofilHeizlastAdapter` → **derselbe** `HeizlastRechner`. Zwei Gebäude-Geometrie-Wahrheiten für dieselbe Rechnung.
  2. **`heizlast_kw` wird in der WP-Auslegung frei getippt** (`EnergieAuslegungController.php:200`), obwohl der Heizlast-Rechner `auslegungsheizlast_kw` liefert — **keine** Verknüpfung. Die Kette ist an der wichtigsten Stelle per Hand-Übertragung aufgebrochen.
  3. **Kundenidentität als Freitext** im Energiekonzept (`:133-136`) statt `new_leads`-Bezug.
- **Falsche/fehlende Anker:** Kunde/Objekt/Gewerk/Angebot fehlen komplett als Auslegungs-Anker; `grundriss.speichern` erzeugt **verwaiste** `HeizlastProjekt`-Datensätze ohne Eigentümer.

---

## SCHRITT 1 — TIEFEN-INVENTUR ANGEBOT / AUSLEGUNG

### 1.1 Angebotssystem — Datenmodell & die zentrale Doppelwahrheit

| Tabelle | Model | Hält | Beleg |
|---|---|---|---|
| `offers` | `Offer` | Kopf (offer_no, customer/product/alternative/department, status) | `Offer.php:17-29` |
| `offer_folders` | `OfferFolder` | Angebots-**Varianten** je Offer + Workflow-Status | `OfferFolder.php:13-26` |
| `offer_details` | `OfferDetail` | **Angebotsinhalt als JSON `sections`** + Totals-Snapshot + Branding/AGB/Cover + `angebot_snapshot_sections` | `OfferDetail.php:13-58` |
| `offer_product_lists` | `OfferProductList` | **relationale Einzelpositionen** (qty/unit_price/line_net/cost/vat) | `OfferProductList.php:14-38` |

**DOPPELWAHRHEIT — bestätigt: `offer_details.sections` (JSON, führend) vs. `offer_product_lists` (relational, Legacy):**
- **Führend/live = JSON `sections`.** Geschrieben von `OfferFolderController::persistDetailSections()` (`:2250-2277`) + `OfferController::processOffer()` (`:2289-2311`); gelesen von Deal-Materialliste (`DealMaterialListController::extractMaterialsFromSections:752`) + Feinaufmaß (`DealMeasurementController.php:236,257`). Der ganze Ordner-Wizard (`OfferFolderController`, 3812 Z.) arbeitet nur auf `sections`.
- **Legacy/verwaist = `offer_product_lists`.** Einziger Schreiber `OfferDetailsController::update()` (`:136-164`) — und der schreibt gegen eine **nicht existente Spalte `folder_id`** (`:102-105`; keine Migration legt sie an → laufzeitverdächtig/tot) und referenziert den **alten** Katalog `product_master_sets` (`OfferProductList.php:63`) statt `master_sets`. Route `offer.details.update` existiert (`routes/web.php:3543`), gerendert von der WP-Konfig-View (siehe 1.9 Kandidat A, vermutlich totes Legacy).
- **Verstoß gegen „Eine Wahrheit je Sachverhalt"** + zusätzliche Spalten-Doppelbenennung `offer_folder_id` (neu) ↔ `folder_id` (tot).

### 1.2 Kalkulation — Wahrheit auf Set-Ebene + zwei Angebots-Engines
- **Kalkulations-Wahrheit sitzt auf `MasterSet`-Ebene, nicht im Angebot:** `vkPerPiece = EK + GK + Wagnis + DB` (`MasterSetController.php:455-462`); Marge pro Komponente (`master_set_components.margin`, Default 50 %), GK/Wagnis pro Set. `CostingSet` (`costing_sets`) = Vorgabe-/Default-Sätze (aw_minutes, overheads, risk/profit, Rundung, Rollen-Defaults; `CostingSet.php:10-48`), verknüpft via `master_sets.costing_set_id`.
- **PARALLELRECHNUNG im Angebot (bestätigt):** zwei Summier-Engines schreiben beide `offer_details.total_net` mit **unterschiedlicher Basis**:
  - **Engine 1** `OfferController::calculateOfferSections()` (`:1951-2046`) — **rechnet VK/EK selbst neu** (qty×preis, rekursiv, + Lohn, + Set-Multiplier). Kommentar: „the backend must never trust those values. Every save recalculates" (`:1677-1685`).
  - **Engine 2** `OfferFolderController::calculateDetailTotals()` (`:2279-2310`) — **vertraut `node['total']`**, summiert nur, **schließt Lohn aus**.
  - → Je nach Speicherpfad kann derselbe Ordner **abweichende Totals** bekommen.
- **`EconomicAssumptionController` = dormant** (alle Methoden leer, `:13-64`); `EconomicCalculation` = separater Wirtschaftlichkeits-CRUD an `lead.solarSystems/heatPumps`, **nicht** Teil der Angebotskalkulation (`EconomicCalculationController.php:19-56`).

### 1.3 Angebotsübernahme (Angebot → Auftrag/Deal)
- **Mechanik** `OfferFolderController::changeDocumentStatus()` (`:3074-3392`): setzt `document_status='deal'`, kopiert `sections` → `angebot_snapshot_sections`, **schreibt `deals` per Raw-SQL** (`DB::table('deals')->insert/update`, `:3131-3168`) mit **nur `price=total_net` + FK-Links** (offer_id/offer_folder_id/offer_number/order_number), setzt `lead_product_lists.status='deal'`, storniert Geschwister-Ordner.
- **Zweiter Kopierschritt** `DealMeasurementController`: kopiert `sections` als `sections_snapshot` + erzeugt `DealMeasurementItem`-Zeilen (`:236-274`).
- **BRUCHSTELLEN (das „Angebot→Auftrag 🔴"):**
  1. **Positionen brechen ab** — beim Deal-Anlegen wandern nur Preis + FK, **keine Positions-/Materialstruktur** (`:3132-3168`).
  2. **Keine persistierte Bestellliste** — die Auftrags-Materialliste wird **on-the-fly aus `offer_details.sections`** abgeleitet (`extractMaterialsFromSections:752`); der Auftrag hängt weiter am Angebots-JSON.
  3. **Dreifach-Schreibung des Material-Status** in `offer_details.sections` + `deal_measurements.sections_snapshot` + `deal_measurement_items` (`saveMaterialPayloadToAllSources:1883-1966`) → Divergenzrisiko.
  4. **`total_net`-Divergenz** durch Engine-Mismatch (1.2) → `deals.price` nicht garantiert deckungsgleich.
  5. **Status `auftrag` unerreichbar** — `OfferDetail::DOCUMENT_STATUS_AUFTRAG` existiert (`OfferDetail.php:11`), aber Umschalt-Endpoint erlaubt nur `offer|deal` (`:3081`).
  6. **Deal-Kopf per Raw-SQL** umgeht Model-Events/Guards des `Deal`-Models.

### 1.4 Auslegungen — WP
- **Aktiver WP-Flow** `EnergieAuslegungController::wpBerechnen/wpErgebnis`: Nutzer wählt **manuell ein** Gerät (`wp_index` aus `CatalogDeviceRepository->heatPumps()`, `:362`), tippt `heizlast_kw` frei; berechnet Vorlauf/q_heiz/q_ww/Strom via `JazService` + `WarmwasserService` (`:395-405`); zeigt Förderung. **`BivalenzService` wird NICHT aufgerufen** (siehe „Besonders geprüft").
- **`WaermepumpenMatchService::kandidaten()`** (Kandidatenliste, Design-Punkt; `:71-75`) ist **nur im Heizlast-Rechner** verdrahtet (`HeizlastController.php:180`), **nicht** im WP-Auslegungs-Flow.

### 1.5 Auslegungen — PV/WR
- **WR-Flow** ruft `InverterSizingService` **direkt** auf (Einzel-WR; `EnergieAuslegungController.php:28,116,149`; View bestätigt „Rechnung liegt serverseitig im InverterSizingService", `wr_auslegung.blade.php:8`).
- **`PvProjektService` (Mehrdach-Projekt-Bündler) = 0 Aufrufer** (`PvProjektService.php:15`); der abhängige **`StringBuilderService`** hängt am toten Zweig mit. → Es fehlt ein verdrahteter PV-Projekt-Bündler; der Flow ist Einzel-WR-zentriert.

### 1.6 Auslegungen — Heizlast
- **Zwei-Verfahren-Dublette (kein Wrapper!):** `HeizlastProjektService` (raumweiser Norm-naher Kern über `HeizlastRechner`, echte Bauteil-U-Werte, EN 442/1264-Abgleich) = **verdrahtet, führend** (5 Aufrufer). `HeizlastService` (überschlägiges **Wohnflächen-/Baualtersverfahren**, „ersetzt KEINE raumweise Planung", `HeizlastService.php:5-10,17,49`) = **eigenständiges gröberes Verfahren, 0 Aufrufer** — **nicht** überholter Wrapper, sondern nie angeschlossener Schnell-Schätzer. *(Bei etwaiger späterer Archivierung: Variante B.)*

### 1.7 Auslegungen — Heizkörper *(gesund)*
- Eigener Konfigurator hinter Feature-Gate `EnsureHeizkoerperEnabled` (`routes/web.php:2479-2483`); Services `RadiatorPerformanceService`/`CompatibilityService`/`RadiatorCatalogAdapter`/`HydraulicService` alle **≥1 Aufrufer, verdrahtet**. **Dockt ans Angebot an**: `stueckliste.uebernehmen` schreibt **additiv** in `deal_measurement_items (kind='heizkoerper')` mit `offer_id` (`HeizkoerperController.php:115,196,204`). Belegkonform, keine Leiche.

### 1.8 Katalog/Sets — Geräte-Wahrheit gespalten
- **Serverpfad = eine Wahrheit:** `CatalogDeviceRepository` (SELECT-Adapter auf `inverters`/`product_pv_module_specs`/`batteries`/`product_heat_pump_specs`; `:14-53`) — 3 Aufrufer, **alle im Auslegungspfad** (`EnergiekonzeptController:41`, `EnergieAuslegungController:27`, `WaermepumpenMatchService:18`).
- **Angebotspfad nutzt CatalogDeviceRepository NICHT** — der neue Angebots-Wizard zieht Sets aus **`master_sets`/`master_set_components`** (`OfferWizardController.php:670-719`, Hydration `OfferFolderController:963-1101`); der alte Pfad aus **`product_master_sets`** (Katalog-Doppelung parallel zur Positions-Doppelung).

### 1.9 Formular-/Checklisten-Engine — nicht im Angebot verdrahtet
- `FormulaEvaluationService`/`VisibleIfService` = verdrahtet, **aber nur im Produkt-Formular-Admin** (`ProductFormulaController`, `routes/web.php:2885-2893`) — **nicht** im Angebot (Grep in `resources/views/admin/offer/**` + Offer-Controllern: kein `FormulaEvaluationService`/`ProductFormula`).
- **`SmartroutingService` = 0 Aufrufer** (nur Test; `:19`), **`PlausibilityService` = 0 externe Aufrufer**.

### 1.10 Ticket-/playground-/wberechnung-Bausteine (Herkunft)
- **Ticket-eigen, gebaut:** Angebots-Wizard/Ordner-System, `master_sets`/`CostingSet`, `CatalogDeviceRepository`, Heizkörper-Modul, Energie-Controller/Views.
- **playground-Herkunft:** Formel-Engine (`FormulaEvaluationService` eval-frei, `VisibleIf`, `Smartrouting`) — **gebaut, im Angebot isoliert**; PV-Dachbelegungs-Datenbasis (Datenmodell, Kategorie B).
- **wberechnung-Herkunft:** gesamter Energie-Rechenkern (`Heizlast/Energie/Klima`) transplantiert + zum großen Teil verdrahtet; **Rest-Referenz** in wb: `OpenMeteoKlimaService`, `StringBuilderService` (in ticket vorhanden, aber toter Zweig), `InverterSuggestionService`, `AuslegungService`/`*HandoffService`, `MassstabVorschlagService`, `wp_material_sets.json` (siehe `wberechnung-uebernahme-inventur.md`).

---

## BESONDERS GEPRÜFT

### B1. Was fehlt zum Verdrahten von `BivalenzService`
**Kontrakt** (`BivalenzService::berechne()`, `:35-46`): braucht `WpKennlinie $wp, float $phiHlKw, float $qHeizKwh, float $qWwKwh, bool $wwMitWp, float $vorlaufC, ?string $plz, …`. Nutzt intern `KlimaBinService` + `WpKennlinieService`. Liefert reiches Array: `bivalenzpunkt_c`, `bivalenz_status/-_hinweis`, `laufstunden_h`, `jaz`/`jaz_nur_wp`, `strom{verdichter/estab/pumpen/gesamt}`, `waerme{q_estab…}`, `estab_waerme_anteil_pct`, `saison{…}`, `klima{zone/theta_e}`.

**Lücke exakt:**
- **(a) Aufruf-Ort:** `EnergieAuslegungController::wpErgebnis()`, **nach Zeile 405** (Vorlauf/q_heiz/q_ww/Strom liegen vor) und vor `return`-Array `:428`; zusätzlich DI in den Konstruktor (`:26-33` — heute `repo, service, jaz, ww, kosten, foerderung`, **BivalenzService fehlt**).
- **Beleg „JazService JA / BivalenzService NEIN":** `JazService` injiziert `:31`, genutzt `:395-405`; `BivalenzService` nirgends injiziert/aufgerufen (repo-weiter Grep: nur eigene Klassendefinition).
- **(b) Eingaben:** alle Pflicht-Operanden liegen bereits als lokale Variablen vor (`$hp:362`, `heizlast_kw:404`, `qHeizKwh:404`, `qWwKwh:400`, `wwMitWp:364`, `vorlaufTemp:396`). **Einzige fehlende Eingabe: `$plz`** — das WP-Formular hat **kein PLZ-Feld** (`wpBerechnen`-Validierung `:198-220`, `wpDefaults:483-507`). Ohne PLZ fällt `KlimaBinService` auf Default-Zone `'mitte'` zurück (`:146-154`) → rechnet, aber Klimazone geraten statt standortgenau.
- **(c) View-Felder:** `wp_auslegung.blade.php` (`:219-373`) + `wp_auslegung_dokument.blade.php` (`:89-167`) rendern **kein einziges Bivalenz-Feld** — zu ergänzen: Bivalenzpunkt+Ampel, Laufstunden, E-Stab-Anteil (`strom.estab_kwh/estab_pct`, `estab_waerme_anteil_pct`), Strom-Aufteilung Verdichter/E-Stab/Pumpen, JAZ-Paar, Saison-Verteilung, Klimazone.
- **(d) Fach-Entscheidung (Operanden-Gate):** heute zeigt der Flow **Richtwert-JAZ** (`JazService`, Tabellen-Lookup, geräteunabhängig) + daraus abgeleiteten Strom (`:405`); `BivalenzService` liefert **geräte-spezifische, aus der Kennlinie simulierte** JAZ + Strom. Beim Verdrahten ist zu entscheiden, **welcher Wert führend** wird — sonst stehen zwei JAZ/Strom-Zahlen nebeneinander („Eine Wahrheit je Sachverhalt").

> **Kern:** Zum Verdrahten fehlt real wenig — DI + ein Aufruf nach `:405`, ein PLZ-Feld, Bivalenz-Sektionen in 2 Views, **und die Fachentscheidung JAZ-Wahrheit.** Der abhängige `WpKennlinieService` ist ebenfalls faktisch tot (einziger Verweis = BivalenzService-Konstruktor) und wird durch dasselbe Verdrahten mit-aktiviert.

### B2. Unverdrahtete Services (Bereich-2-relevant)

| Service | Aufrufer | Rolle | Befund |
|---|---:|---|---|
| `Heizlast/BivalenzService` | **0** | Bin-Simulation/Bivalenz/JAZ/E-Stab/Strom | Kron-Service, siehe B1 |
| `Heizlast/WpKennlinieService` | 0 extern | Kennlinie/COP je Punkt | tot, hängt an BivalenzService |
| `Energie/PvProjektService` | **0** | Mehrdach-PV-Bündler | isoliert; InverterSizingService wird direkt genutzt |
| `Energie/StringBuilderService` | 1 (nur PvProjekt) | String-Topologie | toter Zweig |
| `Heizlast/HeizlastService` | **0** | Wohnflächen-/Baualter-Überschlag | eigenes Verfahren, nie angeschlossen |
| `Form/SmartroutingService` | 0 (nur Test) | Kontext→Formular-Routing | im Angebot nicht verdrahtet |
| `Form/PlausibilityService` | 0 extern | Plausibilitäts-Warnungen | nicht verdrahtet |
| `Anforderungsprofil/…Adapter` | 0 extern | Bedarfs-Anker→Heizlast | Anker-Framework tot (Schritt 0) |

### B3. Frontends, die „falsch"/parallel rechnen (zweite Rechen-Wahrheit)
1. **`checklist/profitablity_calculation/profit.blade.php` — AKTIV.** JS berechnet `bivalenzpunkt` (`nat >= -10 ? -5 : -7`), Heizlast, `empfohleneWpKw`, `empfohlenePv`, `empfohleneBatterie` (`:517,598`). **Baut genau `BivalenzService` + `WaermepumpenMatchService` im JS nach** — während die Services ungenutzt daneben liegen. **Wichtigster Beleg für „gebaut, aber Frontend rechnet selbst".**
2. **`offer/configuration/offer/config.blade.php` — AKTIV** (via `OfferWizardController:63`). Preis/Marge/MwSt/Summen komplett clientseitig (`calcLineTotal:6319`, `calcPosSettings:5406`, `updateTaxRate:5229`). **Kein dedizierter PHP-Pricing-Service gefunden** → Angebots-Preiswahrheit lebt primär im JS; ob der Server bei Persistenz nachrechnet, offen (Engine 1 behauptet Recompute — 1.2).
3. **`offer/.../wp/index.blade.php` — vermutlich totes Legacy** (Renderer `Old/OfferConfigController` nicht geroutet). Enthält hartcodierten JS-Gerätekatalog + `estJAZ()` + volle WP-Auslegung im JS (`:937,1060-1064,1438-1451`) — zweite Geräte-/Rechen-Wahrheit, aktuell wohl unerreichbar.

### B4. Welche Datenquelle führend sein MUSS
- **Auslegungs-Eingang (Bedarf/Operanden):** `anforderungsprofile` (verankert an Objekt/Gewerk) — **nicht** Formular-Freieingabe.
- **Angebots-Positionen:** `offer_details.sections` (EINE Wahrheit) — `offer_product_lists` stilllegen (Variante B, Beleg-Trail).
- **Kalkulation:** `MasterSet`/`CostingSet`-Ebene; im Angebot **eine** Summier-Engine (Engine-Doppelung auflösen).
- **Geräte:** `CatalogDeviceRepository` (auch für den Angebotspfad, statt `master_sets`-Geräte-JS-Katalog).
- **WP-JAZ/Strom:** eine Wahrheit (Bivalenz-Simulation **oder** Richtwert) — Fach-Entscheidung nötig.

### B5. Anker falsch/unklar
- **Fehlend:** Kunde/Objekt/Gewerk/Angebot an der Auslegung.
- **Verwaist:** `HeizlastProjekt` ohne Eigentümer-FK (`grundriss.speichern`).
- **Tot-aber-richtig:** `Anforderungsprofil`-Anker (Objekt kanonisch/Gewerk optional) gebaut, nicht angeschlossen.
- **Zweitname-Falle:** `offer_folder_id` (aktiv) ↔ `folder_id` (tot) am selben `OfferDetail`.

### B6. UI-/Workflow-Brüche
- **Auslegung → Angebot ⬜** — kein Endpoint schreibt Auslegungs-Ergebnisse ins Angebot; Übertragung nur manuell (Nutzer tippt Werte ab).
- **`heizlast_kw` Hand-Übertragung** Heizlast-Rechner → WP-Auslegung (kein Datenfluss).
- **Angebot → Auftrag 🔴** — nur Preis+FK, keine Positionen; Bestellliste on-the-fly; Status `auftrag` tot; Deal per Raw-SQL (1.3).
- **Zwei Kalkulations-Engines** → potenziell abweichende Totals im selben Ordner.
- **Bivalenz-Fachlogik im JS** statt Service (B3.1).

---

## Evaluator-Notiz (Selbstprüfung dieser Runde)

- **Belegt (firsthand, 2026-07-11, mit `datei:zeile`):** Anker-Losigkeit aller Energie-Endpoints (Grep + Controller-Volltext); `Anforderungsprofil`-Anker gebaut/0 Aufrufer; `offer_details.sections` vs. `offer_product_lists`-Doppelung (inkl. `folder_id`-Schemabruch); zwei Angebots-Kalkulations-Engines; Übernahme offer→deal nur Preis+FK; BivalenzService-Kontrakt + exakte Lücke (nur PLZ + DI + Aufruf + View-Felder); PvProjekt/StringBuilder/HeizlastService/Smartrouting/Plausibility = 0 Aufrufer; Heizkörper gesund + ans Angebot angedockt; CatalogDeviceRepository = Serverpfad-Wahrheit; `profit.blade.php` baut Bivalenz im JS nach.
- **Ehrliche Restunsicherheiten (nicht bis zu Ende verfolgt):** ob die WP-Konfig-Legacy-View real geroutet/erreichbar ist (Blade-JS-Ebene nicht komplett getracet); ob der Server die JS-Preise in `config.blade.php` bei Persistenz nachrechnet; welcher UI-Button welche der zwei Angebots-Engines trifft; vollständiges `deals`-Restschema; vollständige `CostingSet→MasterSet`-Ableitungslogik.
- **Keine Bewertung/Lösung:** dies ist Inventur + Befund. Priorisierung, Ziel-Wahrheit-Entscheidungen und Umsetzungspakete folgen **erst** nach Yama-Prüfung (dann Generator/Evaluator getrennt, Umsetzung mit Archiv+Manifest wo Variante B).
- **Nicht gemacht (korrekt):** kein Bau/Import/Refactor/Löschen/Überschreiben/Automatisierung/Commit. `rueckfall-archiv-regeln.md` beachtet.

---

*Nächster Schritt laut Auftrag: **STOPP.** Yama prüft diese Tiefen-Inventur. Erst danach (auf Freigabe) Bewertung/Ziel-Wahrheiten bzw. das erste Umsetzungspaket (z. B. `BivalenzService` verdrahten).*
