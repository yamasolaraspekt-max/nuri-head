# P3-0 — Auslegungs-Inventur, Bewertung & Layout/UX-Konzept

> **Status:** read-only Bestandsaufnahme + Bewertung + Konzept. **Kein Bau, keine Migration, kein Refactor, kein Commit, keine Automatisierung, kein Schreiben in `offer_details.sections`, keine neue Wahrheit.**
> **Zweck:** Vor jeder Kopplung „Auslegung → Angebot" erst fachlich+technisch bewerten, was existiert, was führt, was übernommen/ergänzt/verworfen wird, und wie Backend **und** Frontend zusammen geplant werden (jeder Abschnitt browserprüfbar).
> **Bezug:** Paket 1 (Reife-Panel) · Paket 2a/2b (Reife-Gate) · Ziel-Wahrheiten Anforderungsprofil / `offer_details.sections` / P1-a-Preis.
> **Datum:** 2026-07-13.

---

## 1. Inventur — vorhandene Auslegungs-/Rechenbausteine

### 1.1 Heizlast (DIN EN 12831-1)
| Baustein | Ort | Rolle |
|---|---|---|
| `HeizlastRechner` | `app/Services/Heizlast/HeizlastRechner.php:28` | DB-freier Rechenkern (byte-Port aus „wberechnung"): `standardheizlast_kw`, `auslegungsheizlast_kw`, spez. Heizlast, Raum-/Hüllbilanz |
| `HeizlastProjektService` | `app/Services/Heizlast/HeizlastProjektService.php` | Orchestriert Heizlast + HK-Leistung + Hydraulik + FBH + Höhenkorrektur; liefert maßgeblichen Vorlauf |
| `AnforderungsprofilHeizlastAdapter` | `app/Services/Anforderungsprofil/AnforderungsprofilHeizlastAdapter.php:32` | Liest Profil-Werte+Geometrie → ruft `HeizlastRechner` → schreibt `phi_hl_kw`/`standardheizlast_kw`/`spezifische_heizlast_w_m2` als Profil-Werte (datenlage=`berechnet`) |
| Models | `HeizlastProjekt`, `HeizlastRaum`, `HeizlastBauteil` | Standalone-Projekt (transient in `HeizlastController`) |
| Controller/View | `Energie/HeizlastController` · `admin/energie/heizlast.blade.php` | **Standalone-Rechner-UI** (methode=`direkt`, nicht objektverankert) |
| Tests | `Unit/Heizlast/*` (KlimaBin, Höhenkorrektur, FBH…), `Feature/Heizlast/AnforderungsprofilHeizlastAdapterTest`, `ReferenzKatalogSeederTest` | gut abgedeckt |

### 1.2 Bivalenz / WP-Betriebssimulation (VDI 4645)
| Baustein | Ort | Rolle |
|---|---|---|
| `BivalenzService` | `app/Services/Heizlast/BivalenzService.php:35` | **Stärkster WP-Auslegungsbaustein:** Bivalenzpunkt, `bivalenz_status` (Zielkorridor −3…−7 °C), Deckung NE %, JAZ, Wärme-/Strombilanz (Verdichter/**E-Stab**/Pumpen), WP-Deckungsanteil, Bin-Simulation |
| `WpKennlinieService` | `app/Services/Heizlast/WpKennlinieService.php:105` | φ_max(ϑ)/COP(ϑ) aus Geräte-Stützpunkten (DIN EN 14825) + Vorlauf-Derating |
| `JazService` | `app/Services/Heizlast/JazService.php:27` | Richtwert-JAZ/COP + Stromverbrauch |
| `WaermepumpenMatchService` | `app/Services/Heizlast/WaermepumpenMatchService.php:23` | `kandidaten(kW, wpTyp, heizsystem, vorlauf)` → Katalog-Geräte mit Deckung/Ampel (`passt`/`monoenergetisch`/`zu_klein`/`zu_gross`) |
| `KlimaBinService` | `app/Services/Heizlast/KlimaBinService.php` | Temperaturhäufigkeit je PLZ (Bin-Basis) |
| Geräte-Katalog | `Repositories/CatalogDeviceRepository::heatPumps()` → `product_heat_pump_specs` | Kennlinien (35/45/55), COP/SCOP, max_vorlauf, Modulation |
| Tests | `Unit/Heizlast/BivalenzServiceTest`, `WpKennlinieServiceTest`, `WpKennlinieKurveTest`, `Unit/Energie/HeatPumpKennlinieMappingTest` | gut abgedeckt |
| **Konsument heute** | nur `Energie/HeizlastController` + `EnergieAuslegungController` (standalone) | **nicht** an Profil/Angebot gebunden |

### 1.3 WP-Auslegung durchgängig (standalone UI + Dokument)
| Baustein | Ort | Rolle |
|---|---|---|
| `EnergieAuslegungController` | `Http/Controllers/Energie/EnergieAuslegungController.php` (`wpIndex/wpBerechnen/wpDokument` :185/196/285, Kern `wpErgebnis()` :360) | Verkettet Heizlast→Jaz→Warmwasser→Verbrauch→Kosten→**Förderung**; erzeugt kundenfertiges Dokument |
| Energie-Services | `JazService`, `WarmwasserService`, `VerbrauchsService`, `Energie/KostenService`, `Heizlast/FoerderungService` (KfW/BEG) | vollständige Wirtschaftlichkeit + Förderung |
| Views | `admin/energie/wp_auslegung(.blade)`, `wp_auslegung_dokument`, `wr_auslegung(_dokument)`, `energiekonzept(_dokument)`, `sanierung(_dokument)`, `fussboden_check`, `grundriss(_editor)`, `materialliste`, `plan_upload` | reiches, aber **standalone** Energie-Toolset |
| Routen | `routes/web.php:5480–5543` (`/admin/energie/*`) | eigener Bereich, nicht im Angebots-/Objektfluss |

### 1.4 Heizkörper (Modul M4 — **dormant in prod**)
| Baustein | Ort | Rolle |
|---|---|---|
| `RadiatorPerformanceService` | `app/Services/Heizkoerper/RadiatorPerformanceService.php:28` | EN-442-Leistung, **Mindest-Vorlauf je Raum** → gebäudeweiter maßgeblicher Vorlauf (WP-Auswahl) |
| `HydraulicService` | `.../HydraulicService.php:40` | Volumenstrom/kv/Voreinstellung, Gebäudesummen |
| `RadiatorCatalogAdapter` · `CompatibilityService` | `.../RadiatorCatalogAdapter.php:23` · `.../CompatibilityService.php:19` | Katalog→Rechnung; Ventil-/Zubehör-Stückliste mit Datenqualitäts-Stufen |
| Models | `RadiatorSpec` (`product_radiator_specs`), `RadiatorInstallation` | Katalog + Ist-Aufnahme (customer/alternative) |
| Controller/Flag | `Heizkoerper/HeizkoerperController` (`uebernehmen`→`deal_measurement_items`, **Preise NULL**) · Middleware `EnsureHeizkoerperEnabled` · `config/features.php` `heizkoerper` **Default OFF** | **dormant** bis M5 |
| View/Tests | `admin/heizkoerper/konfigurator.blade.php` · `Unit/Heizkoerper/*`, `Feature/Heizkoerper/*` | gut abgedeckt, aber abgeschaltet |

### 1.5 PV / Wechselrichter / Speicher (Parallel-Domäne, **nicht** Paket-3-Fokus)
- Models `PVTools`, `PVRoofPlan`, `PVLongRoof`, `ProductPV`, `PVRoof`, `PVChecklist`, `LeadAlternativePvWpDetail`.
- Controller `PVToolsController`, `Product/ProductPVController`, `Customer/PVRoof(Plan)Controller`, `Old/PVLong*`, `Old/PVChecklist*`.
- Services `Energie/PvgisErtragService`, `PvProjektService`, `Energie/KostenService`, Contracts `SizingBattery`; `InverterSizingService` (Unit-Tests `Unit/Energie/Inverter*/Module*/Battery*`).
- **Speicher/Puffer/WW:** `Heizlast/WarmwasserService` (WW-Bedarf), Batterie-Sizing nur PV-seitig (`SizingBattery`, `KostenService`, `PromptFactory`). **Kein** WP-Puffer-/WW-Speicher-Dimensionierungs-Service.

### 1.6 Formular / Anforderungsprofil / Routing
| Baustein | Ort | Rolle |
|---|---|---|
| WP-Formular v2 | `ProductFormula` (product_id=2, 18 Felder) · `WpProduktFormularSeeder` · `FormSchemaValidator` | Bedarfsformular (statisch, keine `visible_if`/`calculation` im Pilot) |
| Antworten | `LeadProductChecklistValue.filled_values` · `LeadProductChecklistValueController` | Persistenz je Vorgang (`lead_product_list_id`) |
| Calc-Engine | `FormulaEvaluationService` (eval-frei, **Operanden-Gate** 3-stufig) · `ProductFormulaController::evaluate` · `VisibleIfService` | serverautoritativ; `Unit/Form/FormulaEvaluationServiceTest`, `Feature/Form/FormulaEvaluateEndpointTest` |
| **Anforderungsprofil** | `Anforderungsprofil` (+ `AnforderungsprofilWert`, `SchluesselRegistry`, `AnforderungsprofilService`) | **Auslegungs-Wahrheit:** versioniert, polymorph verankert (`LeadAlternativeAdd`=Objekt kanonisch / `LeadProductList`=Gewerk), Registry-validiert, Datenlage je Wert |
| Routing | `SmartroutingService` (`product_formula_routing_rules`) | Kontext → **Formular** (nicht → Artikel) |
| Klima | `AnforderungsprofilService::werteErgaenzen()` (norm_aussentemp_c per PLZ) · `Feature/Anforderungsprofil/AnforderungsprofilKlimaPlzLookupTest` | PLZ→NAT vorhanden |

### 1.7 Angebot / Katalog / Positionen (Ziel-Container)
| Baustein | Ort | Rolle |
|---|---|---|
| **`offer_details.sections`** | `OfferDetail.php:47` (array-Cast) | **Positions-Wahrheit**; verbindlich geschrieben nur in `OfferController::processOffer` (:2324–2345) |
| Preis-Autorität | `CatalogPriceGuard` (P1-a) via `component_id`→`MasterSetComponent` | überschreibt EK/VK, Marker `preis_quelle` |
| Totals | `calculateOfferSections` (:1965–2060) | serverautoritativ (Frontend-Totals verworfen) |
| Katalog | `MasterSet` (`product_master_sets`), `MasterSetComponent` (`master_set_components`), `products`, `article_groups`, `product_heat_pump_specs`, `product_radiator_specs`, `offer_product_lists` (legacy), `product_positions` | Preis-/Set-Struktur |
| Wizard (Frontend) | `admin/offer/configuration/offer/config.blade.php` (**jQuery + vanilla JS**, `State.sections`) · `wizard-smart.blade.php` · `makeComponentItem` setzt `component_id`-Anker | einziger Positions-Eingang heute |
| Reife | `OfferReadinessService`/`OfferReadinessGate` (Paket 1/2) | bündelt Formular+Profil zu Reifegrad; Gate vor Angebotsanlage |

---

## 2. Bewertung — nutzen / ergänzen / verwerfen / Reserve

**NUTZEN (führend/Reuse, fachlich+normnah):**
- `HeizlastRechner` + `HeizlastProjektService` (DIN EN 12831-1) · `BivalenzService` (VDI 4645) · `WpKennlinieService`/`JazService`/`WaermepumpenMatchService` · `RadiatorPerformanceService`/`HydraulicService`/`CompatibilityService` (EN 442).
- `Anforderungsprofil`-Framework als **Auslegungs-Wahrheit**. `FormulaEvaluationService` (Operanden-Gate). `CatalogPriceGuard` als **Preis-Wahrheit**. Wizard-`sections` als **Positions-Wahrheit**.
- `FoerderungService` (KfW/BEG), `WarmwasserService`, Klima-PLZ-Lookup.

**ERGÄNZEN (Lücken, additiv):**
- Mapper **Formular-Antworten → Anforderungsprofil-Werte**.
- Verdrahtung **Auslegung liest verankertes Profil** (statt Standalone-Direkteingabe) und schreibt Ergebnis-Werte zurück.
- **Serializer Auslegung → Positionsvorschlag** (`offer_details.sections`-Form, `component_id`-Anker, Herkunft/Datenlage-Marker).
- Regel-Mapping **Profilwert/WP-Kandidat → master_set/Komponenten**.
- **Puffer-/WW-Speicher-Dimensionierung** (fehlt komplett).
- `sperrzeit_h` in die Bilanz einbeziehen (heute nur Feld). Dedizierter `auslegungsheizlast_kw`-Registry-Key (heute unter `phi_hl_kw`).
- **WP-Preis-Sets** im Katalog (siehe Klärungspunkt).

**VERWERFEN / NICHT übernehmen:**
- Alt-Model `Heatpump.php` (Legacy-Stub, keine Auslegungslogik) + `HeatpumpSeeder`/`HeatpumpLeadSeeder` als Auslegungsquelle.
- `Old/PV*`-Controller. `app/Models/Radiator.php` (Wechselrichter-Altlast — **DO NOT DOCK**, per CLAUDE.md).
- Jede **Parallelrechnung** (z. B. Client-Totals) als Wahrheit.

**RESERVE (später/parallel, nicht jetzt koppeln):**
- **PV/WR-Domäne** komplett (WP zuerst).
- **`EnergieAuslegungController`-Standalone-UI + Dokument-Views**: fachlich wertvoll als **Referenz** für Ergebnis-Struktur und als Vorlage der Auslegungs-Darstellung — aber **nicht** als führender Angebots-Pfad (direkt-Eingabe, nicht profilverankert). Später ggf. auf Profil umstellen.
- **Heizkörper-Modul (M4)**: fachlich fertig, aber **dormant (Flag OFF bis M5)** — HK-Positionen erst nach M5 real koppelbar; bis dahin nur Rechen-Reuse (Mindest-Vorlauf).

---

## 3. Führende Wahrheit je Berechnung
| Sachverhalt | Führende Quelle | gerechnet von |
|---|---|---|
| Heizlast (Standard/Auslegung) | `Anforderungsprofil`-Werte `standardheizlast_kw`/`phi_hl_kw` | `HeizlastRechner` via Adapter |
| Vorlauf/HK-Eignung | `RadiatorPerformanceService` (Mindest-Vorlauf) | on-the-fly |
| Bivalenz/Gerät/JAZ | `BivalenzService`/`WaermepumpenMatchService` | on-the-fly (**noch nicht ins Profil geschrieben → Lücke**) |
| Wirtschaftlichkeit/Förderung | `Kosten-/Verbrauchs-/FoerderungService` | on-the-fly |
| Angebotspositionen | `offer_details.sections` | Wizard + `calculateOfferSections` |
| Preis EK/VK | Katalog via `component_id` | `CatalogPriceGuard` (P1-a) |

---

## 4. Lückenliste (technisch)
1. **Formular → Profil**: kein Mapper `filled_values` → Registry-`schluessel`/`wert`.
2. **Profil → Auslegung**: Bivalenz/Geräte-Match sind nicht ans verankerte Profil gebunden (nur Standalone-`HeizlastController`); Ergebnisse (Bivalenzpunkt, WP-Kandidat) landen nicht im Profil.
3. **Auslegung → `offer_details.sections`**: kein Serializer (Code-Marker „Paket 3", `OfferReadinessService.php:31`).
4. **Profil → Set/Artikel**: kein Regel-Mapping Profilwert/Gerät → `master_set`/Komponenten; `product_formula_routing_rules` routet nur zu Formularen.
5. **Katalog-Anker WP**: unklar, ob **WP-Preis-Sets** (`master_sets` mit `master_set_components`) existieren — `WaermepumpeKomplettloesungSeeder` legt **Phasen/Checklisten** (product_id=16) an, **keine** Preis-Komponenten. → **ohne Preis-Set keine bepreisten WP-Positionen** (Vorschlag müsste zunächst preislos bleiben, wie HK-`uebernehmen`).
6. **WP-Identität**: `article_groups.id=2` (OfferReadinessService `GEWERK_WP`) vs. `product_id=16` (Phasenseeder) — **muss geklärt werden**, welche ID WP im Set-/Angebots-Kontext trägt.
7. **Speicher**: keine WP-Puffer-/WW-Speicher-Dimensionierung.
8. **`sperrzeit`**: als Feld/Key da, nicht verrechnet.
9. **Registry**: kein dedizierter `auslegungsheizlast_kw`-Key.
10. **Tests/Frontend**: kein Test/kein UI-Schritt für „Auslegung → Vorschlag → Übernahme".

---

## 5. Layout-/UX-Konzept (Backend + Frontend zusammen, jeder Schritt browserprüfbar)

**Leitidee:** Ein **objekt-/gewerkverankerter WP-Auslegungs-Wizard** (nicht das Standalone-Energie-Tool), der Formular → Auslegung → Ergebnis → Vorschlag → Angebot **als getrennte, aufeinander aufbauende Schritte** führt. Der Mensch entscheidet; das System begründet.

**Nutzerreise (Schritte, je eigener read-only prüfbarer Endpoint):**
1. **Start** am WP-Vorgang (Objekt/Gewerk) — Einstieg aus Objektprofil/Kanban. Zuerst sichtbar: **Reifegrad (Paket 1)** + „Auslegung starten".
2. **Bedarf (Formular)** — bestehendes WP-Formular; Sichtbarkeit/Pflicht serverautoritativ (`VisibleIfService`), Operanden-Gate.
3. **Auslegung** — Heizlast/Bivalenz/Gerät **on-the-fly** aus dem Profil; je Kennzahl **Datenlage** (gemessen/berechnet/geschätzt) und **Ampel** (grün/gelb/rot, wie `RadiatorPerformanceService::status`).
4. **Ergebnis + Alternativen** — WP-Geräte-Kandidaten als **vergleichbare Karten** (Leistung kW, JAZ, Deckung %, Bivalenzpunkt °C, max. Vorlauf, Ampel, Preis wenn Set vorhanden); **Ranking** nach Status/Deckung; ein „gewählt"-Zustand. Analog Speicher-/HK-Alternativen (sobald Bausteine/Flags da).
5. **Vorschlag** — abgeleitete **Positionen** als klar markierte, **editierbare** Zeilen mit „aus Auslegung"-Badge, Herkunft-Begründung, Datenlage. **Noch kein Angebot.**
6. **Übernahme** — „In Angebot übernehmen" schiebt die Vorschlagspositionen in den bestehenden Wizard-`State.sections`; Mensch **bestätigt/ändert**.
7. **Angebot** — bestehender Save-Pfad: `CatalogPriceGuard` bepreist, `calculateOfferSections` rechnet Totals. Reife-Gate (Paket 2) bleibt vorgeschaltet.

**Trennung der Ebenen (gegen „Datenhaufen"):**
- Vier klar getrennte Zonen: **Eingabe (Formular)** · **Berechnung (Auslegung)** · **Entscheidung (Alternativen/Vorschlag)** · **Angebot (Positionen/Preis)**. Nie vermischt auf einer Fläche.
- **Progressive Disclosure:** je Karte Kurzergebnis (Kennzahl + Ampel + Empfehlung), Rohwerte/Details nur auf „Details aufklappen".
- **Auto vs. manuell sichtbar:** jede Position/Kennzahl trägt ein Kennzeichen „automatisch berechnet" (aus Profil/Auslegung, Datenlage) vs. „zu bestätigen" (Fach-/kaufm. Entscheidung). **Bestätigungs-Gate** vor Schritt 6.
- **Alternativen-Ranking:** WP/Speicher/HK je als sortierte Kartenliste (bester Status oben), mit einem hervorgehobenen „empfohlen"-Vorschlag + Begründung; Auswahl umschaltbar.
- **Konsistenz:** bestehende Vuexy/jQuery-Bausteine nutzen (Alpine hier **nicht** erlaubt); an das vorhandene Reife-Panel-Design anschließen. *(Achtung: Es existiert kein durchgesetztes Design-System — siehe UX-Audit; Wildwuchs vermeiden, Ampel-/Badge-Sprache aus Paket 1 wiederverwenden.)*

**Browserprüfbarkeit je Schritt:** Jeder Schritt bekommt zuerst einen **read-only Preview-Endpoint** (wie Paket 1), damit Backend-Ergebnis und Frontend-Darstellung isoliert prüfbar sind, bevor der Schreibpfad (Schritt 6) gebaut wird.

---

## 6. Empfohlene Reihenfolge der nächsten Pakete

**Grundsatz (systemweite Optimierungs-Reihenfolge):** ① Konzept (dieses Dokument) → ② Workflow (Schritte 1–7 oben) → ③ Bausteine verknüpfen → ④ erst dann automatisieren. **Read-only zuerst, Schreibpfad zuletzt.**

**Klare Empfehlung: mit `P3-c` als READ-ONLY-Vorschau beginnen — nicht mit P3-a.**
Begründung: Der eigentliche, code-markierte Engpass ist die **Brücke Auslegung → Positions-Struktur**. Ein read-only Vorschau-Service (analog `OfferReadinessService`: on-the-fly, **kein** Write) macht diese Brücke sofort **sichtbar und browserprüfbar**, ohne Datenrisiko — und deckt zugleich auf, ob der **Katalog-Anker (WP-Preis-Set)** fehlt (Lücke 5/6), bevor Aufwand in die Input-Seite fließt. Er läuft auf Profilen, die bereits Heizlast-Werte tragen (Adapter), und rechnet Bivalenz/Geräte-Match on-the-fly.

**Vorgeschlagene Sequenz:**
| Reihenfolge | Paket | Inhalt | Typ |
|---|---|---|---|
| **1.** | **P3-c-preview** | Read-only Vorschau: verankertes Profil → on-the-fly Auslegung → **proposed sections** (Struktur + Herkunft/Datenlage/Ampel, Positionen zunächst **preislos**, wenn kein WP-Set) + Preview-Panel | read-only, **kein Write** |
| **2.** | **P3-d0** | Read-only **Katalog-Inventur WP**: existieren WP-`master_sets`/`components`? WP-Identität 2 vs 16 klären | read-only Recon |
| **3.** | **P3-a** | Mapper Formular-Antworten → Anforderungsprofil-Werte (Operanden-Gate, additive Draft-Version) | Backend, additiv |
| **4.** | **P3-b** | Auslegung ans verankerte Profil verdrahten (Ergebnis-Werte zurückschreiben: `auslegungsheizlast_kw`, `bivalenzpunkt_c`, WP-Kandidat) | Backend, Reuse |
| **5.** | **P3-d** | Regel-Mapping Profil/Gerät → `master_set`/Komponenten (Fachentscheidung → Vorschlag+Bestätigung) | Backend, (b)-Logik |
| **6.** | **P3-e** | Wizard-Aktion „Auslegung übernehmen" → `State.sections` (bestätigen), dann bestehender Save-Pfad | Frontend (jQuery) |
| **7.** | **P3-f** | Lücken: Puffer-/WW-Speicher-Sizing · `sperrzeit` · `auslegungsheizlast_kw`-Key | Backend, additiv |

**Automatisierungs-Klassen (Operanden-Gate):** (a) auto = Formular→Profil, Rechnung, Vorschlag-Erzeugung, Bepreisung. (b) nur Vorschlag+Bestätigung = Geräte-/Speicher-/HK-Wahl, Set/Komponenten, Mengen. (c) verboten = Auto-Schreiben ins Angebot, zweiter Totals-/Status-Pfad.

---

## 7. Offene Fachfragen (für Yama, vor Bau-Freigabe)
1. **Erste Positionsklassen** im Vorschlag: WP-Gerät · Pufferspeicher · WW-Speicher · Heizkörper-Tausch · Hydraulik/Zubehör · Montage/Lohn · Förderung (nur Hinweis)? — welche zuerst?
2. **Katalog-Anker:** Bestätigen, dass P3-d0 (WP-Set-Inventur) vor dem Preis-Mapping nötig ist; wie soll ein preisloser Vorschlag zwischenzeitlich dargestellt werden (wie HK „uebernehmen" mit Preis NULL)?
3. **WP-Identität** 2 vs. 16 klären.
4. **Erster Bau-Slice:** Bestätigung P3-c-preview (read-only) als Start.

---

## 8. Nicht-Ziele (dieses Dokument und die nächsten Slices)
Kein Bau · keine Migration · kein Refactor · kein Commit (dieses Dokuments) ohne Freigabe · keine Automatisierung · kein Schreiben in `offer_details.sections` · keine zweite Wahrheit · keine Preislogik-Änderung (P1-a bleibt) · kein Alpine außerhalb erlaubter Scopes · keine PV im WP-Fokus · kein Push.
