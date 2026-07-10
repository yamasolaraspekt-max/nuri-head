# EXPERTEN-INVENTUR 02 — Angebot & Auslegung (Fachplanung PV / WP / Heizkörper / Wallbox)

> **Rolle:** Angebots-/Auslegungs-Experte (CRM-AUTOMATISIERUNG-MASTER, Stufe 1).
> **Status:** READ-ONLY-Audit, Stand 2026-07-10. Repo `/Users/yamanuri/Documents/ticket`, Branch `private/app-code-backup`.
> **Zieldatei:** Dies ist die **einzige** von diesem Auftrag geschriebene Datei. Baut auf `docs/audit/{code-audit, intelligenz-audit, automatisierungs-hebel}.md`, `docs/glossar.md`, `docs/architektur-entscheidungen.md`, `docs/crm-inventur-03-angebot-konfiguration.md` (nicht angefasst).
> **Bereich:** Produktauswahl · Auslegung (Heizlast/PV/WP/Heizkörper) · Kalkulation · Angebotserstellung · Wirtschaftlichkeit · Förderung (KfW/BEG) · Formular-Engine.
> **TABU (nur an Nähten betrachtet):** Nuriva, Video/Jitsi, Invoice-Zone (interne Rechnungsqualität), Legacy Bitrix/NIBE/IMAP.

---

## 0. Methode & Datenbasis

- **Beleg-Disziplin:** jeder Fund trägt `Datei:Zeile` oder eine SQL-Messung. Hochrechnungen und **NICHT-VERIFIZIERT** sind markiert.
- **Datenbasis-Warnung:** lokale Dev-Restore, ~82 % der Tabellen leer. Kern-Zeilen im Bereich: `offers` 29, `offer_details` 29, `offer_folders` 29, `offer_kanban_stages` 30, `product_radiator_specs` 30, `heizlast_projekte` 1 — und **0 Zeilen** in `profitability_calculations`, `profitability_data`, `product_formulas`, `product_formula_routing_rules`, `foerderungen`, `anforderungsprofile`, `radiator_installations`, `deal_measurements`, `deal_measurement_items` (SQL firsthand, information_schema). Aussagen sind daher **strukturell (Code)**, nicht volumengemessen; „0 Aufrufer"/„0 Zeilen" belegen **Reifegrad**, nicht Nichtgebrauch in Prod.
- **Reife-Skala je Funktion (1–5):** 1 = Skelett/tot · 2 = gebaut, un-verdrahtet · 3 = produktiv, aber Lücken · 4 = produktiv, robust · 5 = mitdenkend (Operanden-Gate, plausibilisiert, integriert).

---

## 1. DAS ZENTRALE BILD — Zwei getrennte Welten

Der Bereich zerfällt hart in **zwei nicht verbundene Hälften**:

**(A) Der Angebots-Kern (alt, umsatztragend, integriert-in-sich).** `offers → offer_folders → offer_details(sections JSON)`, gefüttert aus Master-Sets, Costing-Sets, Vorlagen und dem Katalog (`distributor_prices`). Server-autoritative Summen, saubere FK-Kette Kunde→Objekt→Gewerk→Angebot. Reif, aber mit einer Preis-Integritätslücke und ~3 MB totem Editor-Blade.

**(B) Die Auslegungs-/Rechen-Welt (jung, fachlich exzellent, ISOLIERT).** `app/Services/{Heizlast, Energie, Heizkoerper, Anforderungsprofil, Form}/*` — DIN-EN-12831-Heizlast, EN-442-Heizkörper, VDE-PV-String-Auslegung, BEG/KfW-Förderung, eval-freie Formel-Engine. Physikalisch/normativ hochwertig, mit vorbildlichem Operanden-Gate — aber **fast vollständig vom Angebot abgekoppelt**.

> **Kern-Befund (firsthand grep, bestätigt Explore + `automatisierungs-hebel.md` H-B5):**
> **KEIN Auslegungs-Service wird aus einem Angebots- oder Wirtschaftlichkeits-Controller aufgerufen.**
> `grep` über `app/Http/Controllers/Customer/Offer/*` und `.../ProfitCalculator/*` nach `Heizlast|Heizkoerper|Anforderungsprofil|WaermepumpenMatch|InverterSizing|Bivalenz` → **0 Treffer**. Rückrichtung (`OfferDetail|offer_details|biography_data` in den Service-Verzeichnissen) → **0 Treffer**.
> **Die EINZIGE gebaute Brücke „Rechen-Ergebnis → Angebots-/Auftragsposition"** ist das Heizkörper-Modul: `Heizkoerper/HeizkoerperController::uebernehmen()` schreibt `DealMeasurementItem` (`app/Http/Controllers/Heizkoerper/HeizkoerperController.php:150-232`). Alle anderen Auslegungs-Ergebnisse (Heizlast, PV-String, WP-Auswahl, Förderung, Wirtschaftlichkeit) enden in **Blade-Dokument-Views** oder eigenen Insel-Tabellen — sie müssen **von Hand ins Angebot getippt** werden.
> Der **designierte** Brücken-Baustein `AnforderungsprofilHeizlastAdapter` (Auslegung→Profil→Angebot) hat **0 Aufrufer** (`grep` firsthand) — die Brücke ist gebaut, aber **tot**.

---

## 2. IST-FUNKTIONEN — Raster (Beleg · Stärken · Schwächen · Reife)

### Block A — Angebotserstellung & Kalkulation (der Kern)

#### A1 · Angebotserstellung / Wizard
- **IST (Beleg):** Kette Lead→Objekt→Gewerk→Angebot. `OfferWizardController::createOffer` validiert `customer_id`/`alternative_id`/`product_ids[]` (`:336-344`), löst `LeadProductList` auf (`:363-378`), legt `Offer` an (`:424`) + Folder (raw `DB::table('offer_folders')->insertGetId` `:488`) + leeres `offer_details` (`:520-619`) + `biography_data`-„created"-Event (`:560-569`). Live-Editor: `resources/views/admin/offer/configuration/offer/config.blade.php` (**1,16 MB**, gerendert `OfferWizardController::index :63`, Route `GET /wizard` `web.php:3274`). `offer_details` trägt `sections` (Positions-Baum-JSON), `placed_images`, `cover_text(_html)`, `agb_*`, Branding, `total_net/tax_rate/total_gross`, `document_status` (offer|deal|auftrag), `biography_data` (Audit-Log) (SHOW COLUMNS firsthand; `OfferDetail.php:13-58`).
- **STÄRKEN:** saubere FK-Kette ohne denormalisierte Namen; klarer Objekt-Bezug; race-sichere Nummernvergabe `Offer::generateOfferNo` (lockForUpdate + withTrashed + do/while, `Offer.php:51-96`); Mehrbenutzer-Präsenz (Broadcast `offer-folder.{id}`).
- **SCHWÄCHEN:** **Drei bis vier divergente Schreibpfade** — `createOffer` (raw DB), `OfferController::store` (Folder ohne `offer_details`, `:1001`), `processOffer` (Eloquent `updateOrCreate`, `:2291-2312`), `useTemplate`. Überall defensive `Schema::hasColumn(...)`-Guards (`OfferWizardController:528-596`) = Zeichen von Schema-Drift. `saveDocument`/`processOffer` validieren `sections`/`tax_rate` kaum. **~3 MB totes Editor-Blade** parallel (`config.blade copy.php` 841 KB, `old/config.blade.php` 974 KB, `Old/`-Ordner; keine `view()`-Referenz) — deckt Code-Audit `1.5` (`config.blade` 25k Z., 74,5 % Inline-JS).
- **REIFE: 3** (produktiv, aber redundante Pfade + Alt-Last).

#### A2 · Angebots-Kalkulation (Summen / Preisbildung)
- **IST (Beleg):** Summen **server-autoritativ**: `OfferController::calculateOfferSections` (`:1951-2046`) läuft `sections[].items[]` rekursiv (`offerLineTotals :1847`, `offerLaborTotals :1815`), `tax_amount = round(net*rate/100,2)` (`:2028`), gespeicherte Summen kommen aus `$calculation`, **nicht** vom Client — „Frontend values are ignored here" (`:2307-2311`). Lohn-Preisbildung über Costing-Sets→Master-Sets (`MasterSetController::taskCostingPayload`, Rate-Modi full_cost/sell_rate/wage_only; `crm-inventur-03 §5`). Einzelpreis-Quelle: Katalog `distributor_prices`, im Wizard best-price `MIN(COALESCE(purchase_price,discount_price,price))` (`OfferWizardController:1181-1197`, `mapDistributorPrice :917-938`).
- **STÄRKEN:** Aggregation ist server-autoritativ und rekursiv korrekt; Katalog-Vorbefüllung der Preise; Kalkulations-Kette (Costing-Set→Master-Set→Summe) sauber getrennt.
- **SCHWÄCHEN (Integritätslücke, hoch):** die Autorität gilt nur der **Aggregation, nicht den Einzelpreisen**. `offerNodeVkPrice/EkPrice` (`:1765-1798`) lesen die vom Browser gesendeten `vk`/`ek` je Position — ein Client kann **beliebige Stückpreise** senden, der Server summiert sie nur nach. `distributor_prices` werden beim **Speichern nicht re-verifiziert**. Division `(qty / offerPriceUnitValue) * vkPrice` (`:1903-1904`) — `offerPriceUnitValue :1738` muss Null-Divisor garantiert abfangen (**NICHT-VERIFIZIERT**).
- **REIFE: 3** (robuste Aggregation, aber Einzelpreis-Vertrauen = offene Flanke).

#### A3 · Vorlagen (Templates) / Produktauswahl
- **IST (Beleg):** `OfferTemplatePickerController::useTemplate` kopiert Vorlage komplett (`sections`, Branding, `cover_text=strip_tags(cover_text_html)`, `:377-390`), Totals→0, `usage_count++`. Match-Suche `OfferTemplateController::wizardSearch` (`:712`, SQL LIKE über name/description/sections `:722-729`) mit `match_score`/`match_reasons`.
- **STÄRKEN:** Vorlagen-Wiederverwendung + Nutzungs-Telemetrie vorhanden; Positions-Übernahme funktioniert.
- **SCHWÄCHEN:** **Auto-Vorschlag nach Gewerk nur kosmetisch** — `calculateWizardTemplateScore` (`:850-887`) zählt nur Query-Wort-Überlappung, liefert **flat 50 bei leerem Query** (`:852-853`, kein Gewerk-Scoring); „Gewerk passt" wird ausgegeben, sobald `article_group_id` bloß non-null ist (`:893-895`), nicht am realen Gewerk gebunden. Kein echter Gewerk→Vorlage-Router. (= `automatisierungs-hebel.md` H-V3 „letzte Meile fehlt".)
- **REIFE: 3** (funktioniert manuell; Intelligenz vorgetäuscht, nicht real).

#### A4 · Angebot → Auftrag / Folgeprozesse
- **IST (Beleg):** **Kein** `Offer→Deal`-Model-Trigger in den Offer-Controllern; „Deal" ist ein `document_status`-Phasenwert derselben Mappe (`OfferDetail.php:9-11`, Übergang in `OfferFolderController:153-158,471-527`). `updateStatus` (`:1276`) flippt nur das Enum (draft/sent/negotiation/final/cancel) + Broadcast — **keine** Materialliste/Kalkulation/Aufgabe bei Annahme.
- **STÄRKEN:** — (bewusst schlanker Kopfsatz; Positions-Durchreichung liegt im Rechnungs-Canvas, `intelligenz-audit` H-D3).
- **SCHWÄCHEN:** der Medienbruch „Angebot angenommen → Kickoff" (= `intelligenz-audit` K2/I-11, H-A5). Kein Auslegungs-Bezug beim Übergang.
- **REIFE: 2** (Zustands-Flip ohne fachliche Folge).

### Block B — Auslegung / Fachplanung (die isolierte Rechen-Welt)

#### B1 · Heizlast-Auslegung (DIN EN 12831-1)
- **IST (Beleg):** `HeizlastRechner::berechne` (`app/Services/Heizlast/HeizlastRechner.php:28-65`) — Φ_T = Σ fx·A·(U+ΔU_WB)·Δθ, Φ_V = 0,34·n·V·Δθ, Standard- vs. Auslegungsheizlast, spezifische W/m². Normwerte `HeizlastNormwerte` (θint/fx/Luftwechsel/Wärmebrücken/Komfort, DIN-belegt, `:14-88`). Plausi-Band `plausi()` (`:58-67`, <10 „sehr niedrig", >120 „sehr hoch", `warnung`-Flag). Geometrie-Ableitung `GeometrieAbleitungService` (Shoelace-Polygonfläche `:118-132`, U-Quellen-Kette A>Konstruktion>Baualter `:152-181`). Einstieg `Energie/HeizlastController` (`:56-184`) baut ein **transientes** Einzonen-Projekt (create→delete, `:137-171`) → Blade-Ergebnis + WP-Vorschlag.
- **STÄRKEN:** normativ sauber, ohne DB voll testbar, echtes Plausi-Band, mehrstufige U-Wert-Herkunft mit Strategie-Kennzeichnung. Reifste Rechen-Domäne des Repos.
- **SCHWÄCHEN:** **(a)** Ergebnis wird **nicht persistiert und nicht ans Angebot angedockt** (transient, verworfen; nur `GrundrissController::speichern :209` persistiert überhaupt ein Projekt — daher `heizlast_projekte`=1, `_raeume`/`_bauteile`=0). **(b) PLZ-Miss → still Default-Norm-Klima, DREI divergente Fallbacks**: `HeizlastController:127` `?? -8.5`, `HeizlastRechner:30` `?? -12.0`, `HeizlastProjektService::loeseHoehenkorrektur:274` `?? -12.0` (`getNormAussentempForPlz` liefert `null` bei Miss, `KlimaPlzService:36-41`) — Operanden-Gate-Bruch (rechnet mit erfundenem Klima weiter, unmarkiert, und uneinheitlich; = `intelligenz-audit` P5/I-4, H-B4). PLZ nur `nullable|string|max:10` (`:94`), keine 5-Stellen-Prüfung. Band 10–120 statt 1–500 (alles >120 in einen Kübel). Wiederaufheizung 11 W/m² ist `to_verify`-Richtwert (`HeizlastRechner:131-134`).
- **REIFE: 4** (Rechenkern), **als Angebots-Baustein: 2** (isoliert).

#### B2 · PV-/Wechselrichter-Auslegung
- **IST (Beleg):** `InverterSizingService` (31 KB, VDE/EN-Regelwerk: Spannungsfenster `:109`, String-Prüfung `:152`, Stromregeln `:191`, Strangsicherung `:221`, Überdimensionierung `:241`, MPPT-Ausrichtung `:302`, Netzregeln `:328`, Batterie-Kompatibilität `:403`, WR-Vorschlag `:473`, `evaluateKonfiguration :507`). Ertrag über **echte externe PVGIS-API** `PvgisErtragService` (`https://re.jrc.ec.europa.eu/api/v5_3/PVcalc`, `Http::timeout(15)->retry`, `:31-76`). Orchestrierung `PvProjektService` + `Energie/EnergieAuslegungController` (`wr-auslegung`, `web.php:5461-5477`).
- **STÄRKEN:** tiefes, standardkonformes String-Design (weit mehr als „kWp/1,2"); externe Ertragsprognose statt Faustformel; reine, testbare Rechenfunktionen (Rückgabe-Arrays).
- **SCHWÄCHEN:** **keine Persistenz** (`EnergieAuslegungController` erzeugt nur `wr_auslegung`-Blade + `wr_auslegung_dokument`-View, `:38-143`, **kein** `customer_id`/`offer_id`); **0 Aufrufer aus Offer** (grep firsthand). Ergebnis = Druck-Dokument im Browser, nie eine Angebotsposition. Der Wrapper `PvProjektService` (`:15`, `auswerten :27`) hat zudem **0 Aufrufer** — toter Zwischen-Layer.
- **REIFE: 4** (Kern), **als Angebots-Baustein: 2** (isoliert, ohne Persistenz).

#### B3 · Wärmepumpen-Auslegung & -Matching
- **IST (Beleg):** `WaermepumpenMatchService::kandidaten` (`:23-76`) matcht berechnete Heizlast gegen `CatalogDeviceRepository->heatPumps()` (ticket-Katalog = eine Wahrheit), Auslegungspunkt A-7/W35|W55, COP/SCOP, Deckungsgrad, Ampel `passt/monoenergetisch/zu_gross/zu_klein` (`:95-106`), Taktgefahr-Hinweis bei Mindestmodulation > Bedarf (`:91-93`), Max-Vorlauf-Sperre (`:83-85`). Daneben `BivalenzService`, `WpKennlinieService`, `JazService`, `WarmwasserService`.
- **STÄRKEN:** fachlich ehrliches Matching mit Bewertung + Warnungen; ticket-Katalog statt Fremd-Model; W55-Schätz-Kennzeichnung (`:88-89`).
- **SCHWÄCHEN:** Geräte-DB aktuell **nur Luft/Wasser-R290** (`:25-27` gibt sonst leere Liste) — Sole/Wasser fehlt. Ergebnis fließt nur in Blade (`wp-auslegung`/`energiekonzept`), nicht ins Angebot. **Reife-Asymmetrie:** der **genaue** Simulator `BivalenzService` (VDI 4645/4650, DIN EN 14825, Bin-Simulation, Verdichter/Heizstab-Split, System-JAZ) ist **dormant** (nur `tests/Unit/Heizlast/BivalenzServiceTest`, 0 Controller-Aufrufer, samt `WpKennlinieService`); der **wired** Pfad nutzt stattdessen `JazService` — ein selbstdeklariertes `to_verify`-Konstanten-Lookup (`JazService:7,23`), **nicht** VDI-4650-Güte. Die beste WP-Rechnung liegt also brach, die grobe läuft.
- **REIFE: 4** (Matching-Kern) · **Bivalenz-Simulator: 2 (tot)** · **als Angebots-Baustein: 2**.

#### B4 · Heizkörper-Modul (die EINZIGE verdrahtete Auslegung)
- **IST (Beleg):** `RadiatorPerformanceService` (EN-442 q_real/Leistungstabelle/Ampel/Mindest-Vorlauf), `CompatibilityService` (D3-Regeln, Ventil/Anschluss/Kopf-Norm, `datenqualitaet`), `HydraulicService`, `RadiatorCatalogAdapter`. `HeizkoerperController::berechnen/kompatibilitaet` (JSON-Endpunkte) **und `uebernehmen()` schreibt `DealMeasurementItem`** (kind='heizkoerper', Replace-per-Raum in Transaction, Positionen server-re-derived, `:122-232`). Preise ehrlich `NULL` statt 0 (`:207-211`), Ownership-Gate (`:145`), Completed-Sperre 423 (`:146-148`). Hinter Feature-Flag + auth (`web.php:2477-2480`). Katalog `product_radiator_specs` (30 Zeilen — **lebendig**).
- **STÄRKEN:** **Referenz-Muster „Rechnung → Position"** (das einzige gebaute); kein Client-Vertrauen; additiv (kind-scoped Soft-Delete); Datenqualität + „kein Preis"-Ehrlichkeit vorbildlich. Alpine-Scope korrekt (CLAUDE.md).
- **SCHWÄCHEN:** dockt an **`deal_measurement_items` (Aufmaß/Auftrag)**, nicht an `offer_details` — greift also erst nach Angebotsannahme, nicht in der Angebotsphase. Ownership-Gap bekannt/eskaliert (`:118`). `radiator_installations`/`deal_measurement_items` = 0 Zeilen (Reife jung).
- **REIFE: 4** (gebaut + verdrahtet, jung/ungenutzt).

#### B5 · Anforderungsprofil + Registry + Heizlast-Adapter (die tote Brücke)
- **IST (Beleg):** `SchluesselRegistry` (Whitelist ~17 Schlüssel + Einheit/Cast/Pflicht, `:20-43`; `DATENLAGE = gemessen|berechnet|geschaetzt`). `AnforderungsprofilWert::saving` wirft bei unbekanntem Schlüssel/fehlendem `wert_num`. `AnforderungsprofilHeizlastAdapter::berechneUndSchreibe` (`:32-55`) mit **echtem Operanden-Gate** (`gate()` verweigert ohne `norm_aussentemp_c`/Räume, `:57-72`, **keine** stillen Kern-Defaults), Datenlage-Durchreichung (geschätzte/ungeprüfte U-Werte → `ergebnis_hinweis`, `:74-103`), Rückschreibung `datenlage='berechnet'`.
- **STÄRKEN:** **audit-sicheres „angenommen vs. gemessen"-Register**; der Adapter ist das **Gegenmodell** zum stillen PLZ-Default (B1) — hier wird verweigert statt geraten. Genau der geplante Brücken-Pfad Auslegung→Angebot.
- **SCHWÄCHEN (gravierend):** `AnforderungsprofilHeizlastAdapter` + `AnforderungsprofilService` haben **0 Aufrufer** (grep firsthand); `anforderungsprofile`/`_werte` = 0 Zeilen. **Die vorbildliche Brücke läuft nirgends.** = `automatisierungs-hebel.md` H-B5, TEIL-2-Multiplikator.
- **REIFE: 2** (exzellent gebaut, komplett un-verdrahtet).

### Block C — Formular-/Checklisten-Engine (FS-02…05)

#### C1 · FormulaEvaluationService (eval-freie Formel-Engine, FS-03)
- **IST (Beleg):** `evaluate()` (`app/Services/Form/FormulaEvaluationService.php:100`) — Tokenizer→Shunting-Yard→RPN, **kein eval/new Function**. Operanden-Gate (Referenz-Standard): leere Formel→`unvollstaendig` (`:112-116`); fehlender Operand→`fehlend`, kein Wert (`:129-133,333-341,371-378`); geschätzter/ungeprüfter Operand→rechnet, `verbindlich=false` (`istUngeprueft :437-454`, `:158-162`); Division/0→kein Wert (`:659,144-148`); unbekannte Bezeichner (`alert`/`require`/`__proto__`)→als fehlender Operand behandelt, nie ausgeführt (`:330-341`). Wired via `ProductFormulaController::evaluate` (`:266,300`, Route `product.formula.evaluate` `web.php:2895`). Begleiter: `VisibleIfService` (server-autoritative `visible_if`, `:18`), `FormSchemaValidator` (v2-Schema, `:15`), `UnitConversionService`.
- **STÄRKEN:** **der Referenz-Standard für Operanden-Gate** im ganzen Repo; sicher (kein Code-Exec, DoS-Caps 2000 Zeichen/32 Nesting); alle Begleiter server-autoritativ + getestet.
- **SCHWÄCHEN:** nur über **eine** Endpunkt-Naht wired; `product_formulas` = 0 Zeilen.
- **REIFE: 4** (gebaut, sicher, wired — aber dünn genutzt).

#### C2 · Live-Checkliste (der naive Umgehungs-Pfad)
- **IST (Beleg):** `LeadProductChecklistValueController` (Routen `web.php:2896-2897`) lädt/speichert Checklisten mit **naivem Filter** `ProductFormula::where('product_id',…)->get()` (`:41` load, `:116` save) — **kein** object_type/Phase/Priorität, **keine** Engine, **keine** Validierung, **kein** visible_if, **keine** Plausibilität. Nutzt v1-`name`-Key (`:119-127`), nicht v2-`key`.
- **STÄRKEN:** funktioniert für einfache Produkt-Checklisten.
- **SCHWÄCHEN:** die gesamte FS-02…05-Engine läuft **parallel und ungenutzt** daneben; „jeder sieht alles pro Produkt". FS-08 (Antwort-Persistenz/Fill-Wiring) schließt das erst — **geplant, nicht gebaut** (`backlog-formulare.md:48-49`).
- **REIFE: 2** (Alt-Pfad aktiv, neue Engine umgangen).

#### C3 · SmartroutingService (FS-05) — TOT
- **IST (Beleg):** `route()` (`SmartroutingService.php:27`) matcht `article_group_id`/`lead_product_list_id`/`object_type` gegen `product_formula_routing_rules`, Spezifitäts→Prioritäts→ID-Tiebreak (`:52-54`), Fallback/`ambiguous`. **0 Produktiv-Aufrufer** (grep firsthand, nur Test). `product_formula_routing_rules` = **0 Zeilen** (auch Regel-Tabelle leer).
- **STÄRKEN:** genau das gewünschte Kontext-Routing „PV→PV-Welt", fertig + getestet.
- **SCHWÄCHEN:** doppelte Lücke — **un-verdrahtet UND ohne Regeln**. = `intelligenz-audit` I-1/R2, H-V2.
- **REIFE: 2** (gebaut, tot).

#### C4 · PlausibilityService — TOT
- **IST (Beleg):** `pruefe()` (`PlausibilityService.php:46`) — negative Fläche/Länge→`hoch` (`:68-74`), Menge 0 (`:77-83`), Raumhöhe-Band 1.5–4 m (`:86-95`), Einheiten-Mix (`:99-106`), Feld-min/max. Nur Warnung, nie Block. **0 Aufrufer, 0 Tests** (grep firsthand).
- **SCHWÄCHEN:** reine Sanity-Intelligenz liegt fertig brach. = `intelligenz-audit` I-2 (Quick-Win „nur einhängen").
- **REIFE: 2** (gebaut, tot).

### Block D — Wirtschaftlichkeit & Förderung

#### D1 · Wirtschaftlichkeit (ProfitabilityCalculation)
- **IST (Beleg):** `ProfitabilityCalculationController` — CRUD + Config-Provisioning, **keine PHP-Rechenmathematik**. `edit()` (`:75-311`) baut großen `$frontendConfig` mit Prefill aus `new_leads`+`lead_alternative_adds`+`lead_product_lists`+`article_groups`+`p_v_roofs` (`:91-148`); die KPIs (Ersparnis/Amortisation/ROI/CO₂) rechnet **der Browser (React/JS)**. `saveCalculationReport` (`:408-461`) speichert die **vom Client vorberechneten** Zahlen (+`config/computed/customer_snapshot` JSON), Validierung nur `nullable|numeric`. An Lead/Objekt/Produkt gekoppelt (keyed, `:25`), **aber nicht ans Angebot**. `profitability_calculations` = 0 Zeilen.
- **STÄRKEN:** kräftiger Prefill aus dem CRM (echte Durchreichung des Kunden-/Dach-Kontexts); Snapshot-Wiederherstellung (`:296-298`).
- **SCHWÄCHEN:** **Server vertraut Client-Zahlen** (keine Nachrechnung, kein Operanden-Gate); **massive stille Defaults** (costPV 16000, costBattery 8000, preisStrom 0,35, JAZ 3.5/4.0/4.5, Verbrauchs-Fallbacks nach Baualter/Personen, `:200-289,371-389`) ohne „geschätzt"-Flag — Gegenteil des Anforderungsprofil-Standards. **Isoliert** (kein Offer-Bezug). Paralleler Zweit-Rechner `ProfitabilityDataController::analyze` (`:51-113`, server-seitig, aber hart-codierte Defaults) + **broken** `getProfitabilityData` (`:119`, nutzt nicht-importierte Klasse/falsche Spalten). **4 tote Routen** (`web.php:3953,3954,3955,3973` → fehlende Methoden, fatal-on-hit).
- **REIFE: 2** (Client-Mathematik, Insel, tote Routen).

#### D2 · Förderung (KfW/BEG) — drei disjunkte Implementierungen
- **IST (Beleg):** **(A) `FoerderungService`** (`app/Services/Heizlast/FoerderungService.php`) — echter BEG-EM/KfW-458-Rechner: Grund 30 % + Effizienz 5 % + Klimageschwindigkeit 20 % + Einkommen 30 %, Cap 70 % (`:18-26`), Hülle 15 %+iSFP 5 % (`:29-30`), Deckel je WE (`:42-53`), vermietet/selbst-Split (`:59-113`). Sätze **hart-codiert** (statutorisch). Aufgerufen aus `EnergieAuslegungController:416`, `EnergiekonzeptController:301`, `SanierungsWirtschaftlichkeitService:252`. **(B) `Admin/FoerderungController` + `Foerderung`** — reines Förder-**Register** (CRUD, KfW/BAFA/Land, Antragsstatus, Auto-Nr. `FOE-YYYY-NNNN`), **keine** Rechnung; `foerderungen` = 0 Zeilen; Selbstbeschreibung „Ersatz für kaputtes BEG-Modul". **(C) `BegFundingsController`** — altes Modul, Route **auskommentiert** „DEAKTIVIERT (Fatal-Error, Tier-C)" (`web.php:877`), toter Code.
- **STÄRKEN:** (A) ist die **fachlich reifste, korrekteste** Berechnung des Bereichs (statutorische Sätze 2024/25, WE-Split, iSFP, Klima-Eligibility nach Heizungsart/Alter `:145-155`).
- **SCHWÄCHEN:** (A) **isoliert in der Energie-Welt** — kein Weg in Angebot/Kunde; `ProfitabilityData.kfw_subsidy` wird von **keinem** Service aus (A) befüllt (die zwei Förder-Welten treffen sich nie). Drei disjunkte „Förderung"-Stränge = Redundanz + Verwirrung.
- **REIFE: (A) 4 · (B) 2 · (C) 1.**

#### D3 · Energiekonzept-Orchestrator (Sanierung + WP + Förderung + PV)
- **IST (Beleg):** `EnergiekonzeptController` (`:40-118`) verklammert Heizlast + `JazService` + `WarmwasserService` + `KostenService` + `FoerderungService` + `SanierungsWirtschaftlichkeitService` + `PvgisErtragService` zu **einem** kundenfertigen Beratungs-/PDF-Dokument (`energiekonzept_dokument`-Blade, `:118`). `SanierungsWirtschaftlichkeitService` rechnet echte Vorher/Nachher-Wirtschaftlichkeit (Gradstunden/KlimaBins, Vorlauf-Machbarkeit, Amortisation über 30 J.), guardet Div/0 + emittiert Vorlauf-Hinweis (`:303-305`).
- **STÄRKEN:** die integrierteste **fachliche** Zusammenführung des Bereichs; echte Server-Mathematik mit Divisions-Guard.
- **SCHWÄCHEN:** erzeugt nur ein **Blade-Dokument** — **keine** Persistenz auf Kunde/Angebot (transientes HeizlastProjekt, `energiekonzepte`-Archiv separat). Energiepreis/-steigerung defaulten still (12 ct/3 %). Damit dieselbe Isolation wie B1–B3.
- **REIFE: (Rechnung) 4 · (Integration ins CRM) 2.**

---

## 3. AUTOMATISIERUNGS-REIFE — gesamt

| Funktion | Fach-Reife | Verdrahtung ins Angebot | Automatisierungs-Reife (1–5) |
|---|---|---|---|
| A1 Angebotserstellung/Wizard | 3 | — (ist der Kern) | **3** |
| A2 Kalkulation/Summen | 4 | server-autoritativ | **3** (Einzelpreis-Vertrauen) |
| A3 Vorlagen/Auto-Vorschlag | 3 | manuell | **2** (Vorschlag kosmetisch) |
| A4 Angebot→Auftrag-Kickoff | — | — | **2** (Zustands-Flip) |
| B1 Heizlast (DIN EN 12831) | 4 | **isoliert** | **2** (+PLZ-Default-Bruch) |
| B2 PV/WR-Auslegung (VDE) | 4 | **isoliert** | **2** |
| B3 WP-Matching | 4 | **isoliert** | **2** |
| B4 Heizkörper (EN-442) | 4 | **verdrahtet → Aufmaß** | **4** |
| B5 Anforderungsprofil/Adapter | 4 (Bau) | Brücke **tot (0 Aufrufer)** | **2** |
| C1 Formel-Engine (FS-03) | 4 | 1 Endpunkt | **4** |
| C2 Live-Checkliste | 2 | Alt-Pfad umgeht Engine | **2** |
| C3 Smartrouting (FS-05) | 2 | **tot + 0 Regeln** | **2** |
| C4 PlausibilityService | 2 | **tot** | **2** |
| D1 Wirtschaftlichkeit | 2 | **isoliert** | **2** (Client-Mathe) |
| D2 Förderung BEG/KfW (Service A) | 4 | **isoliert** | **2** |
| D3 Energiekonzept-Orchestrator | 4 | **isoliert (nur PDF)** | **2** |

**Gesamturteil des Bereichs:** Der Angebots-/Auslegungs-Bereich ist ein **Paradox** — er enthält die **fachlich stärkste Rechen-Intelligenz des ganzen CRM** (Grad ~4: DIN-Heizlast, VDE-PV-String, EN-442-Heizkörper, BEG/KfW, eval-freie Formel-Engine mit Referenz-Operanden-Gate) und zugleich die **schwächste Verdrahtung**: außer dem Heizkörper-Modul (→ Aufmaß) speist **keine** Auslegung das Angebot. Alle Ergebnisse enden in Insel-Tabellen oder Druck-Blades und werden von Hand abgetippt. Der billigste Intelligenzgewinn liegt daher **nicht im Bauen**, sondern im **Verdrahten des Vorhandenen** — Priorität: die tote Anforderungsprofil-Brücke (B5) als Referenz-Naht Auslegung→Angebot aktivieren (Muster liegt im Heizkörper-Pfad B4 vor), Formel-Engine/Smartrouting/Plausibility einhängen (C1/C3/C4), PLZ-Default flaggen (B1) und die Einzelpreis-Flanke der Kalkulation schließen (A2).

---

## 4. Gelesen / Nicht-gelesen

**Firsthand gelesen (Datei belegt):** `HeizlastRechner.php`, `HeizlastNormwerte.php`, `GeometrieAbleitungService.php`, `FoerderungService.php`, `WaermepumpenMatchService.php`, `Energie/HeizlastController.php`, `Heizkoerper/HeizkoerperController.php`, `AnforderungsprofilHeizlastAdapter.php`, `SchluesselRegistry.php`, `ProfitabilityCalculationController.php` (voll); `EnergieAuslegungController.php`/`EnergiekonzeptController.php` (Struktur+Persistenz-Greps); `docs/crm-inventur-03-angebot-konfiguration.md`, `intelligenz-audit.md`, `automatisierungs-hebel.md` (voll). **SQL firsthand:** Row-Counts aller Bereichs-Tabellen, `offer_details`/`offers` SHOW COLUMNS. **Grep firsthand:** Isolations-Nachweis (Auslegungs-Services in Offer/Profit-Controllern = 0), Adapter-/Smartrouting-/Plausibility-Aufrufer.

**Via Explore-Agenten (mit file:line belegt, stichprobenartig gegengeprüft):** Angebots-Erstellung/Preis/Vorlagen-Pfad (`OfferWizardController`/`OfferController`/`OfferTemplate*`); Formular-Engine im Detail (`FormulaEvaluationService`/`VisibleIf`/`FormSchemaValidator`/`SmartroutingService`/`PlausibilityService`/`ProductFormulaController`/`LeadProductChecklistValueController`, `backlog-formulare.md`); Wirtschaftlichkeit/Förderung (`ProfitabilityData*`, `Admin/FoerderungController`, `BegFundings`, `SanierungsWirtschaftlichkeitService`, `KostenService`); PV/WP-Kern-Detail (`InverterSizingService`, `PvgisErtragService`, `Bivalenz`/`WpKennlinie`/`Jaz`).

**Nicht/nur oberflächlich gelesen:** Methodenkörper von `OfferFolderController` (3.810 Z.) und `MasterSetController` (2.464 Z.) im Detail; die ~1,16-MB-`config.blade.php` (nur Route/Größe, kein Inline-JS-Audit); `BivalenzService`/`WpKennlinieService`/`UWertService`/`KlimaBinService`-Körper (nur Signatur/Einordnung); Costing-Set-Rate-Modi end-to-end; sämtliche Blade-Dokument-Templates der Energie-Zone.

---

## 5. NICHT-VERIFIZIERT / Selbstkritik

- **„0 Aufrufer"/„0 Zeilen"** sind statischer grep + Dev-Restore — stark, aber **kein Beweis** gegen Reflection/String-Dispatch oder Prod-Nutzung. Für `AnforderungsprofilHeizlastAdapter`, `SmartroutingService`, `PlausibilityService` als **tot** eingestuft auf dieser Basis (**NICHT-VERIFIZIERT** gegen dynamische Aufrufe).
- **Clientseitige Verkettung nicht geprüft:** Ob die ~1,16-MB-`config.blade.php` oder das Profit-Blade **im Browser** doch Auslegungs-Werte zieht (z. B. `heating_load_calculation` als `customWpKw`, `ProfitabilityCalculationController:281`), ist **serverseitig widerlegt, clientseitig NICHT VERIFIZIERT**. Der Isolations-Befund gilt hart für die **PHP-Ebene**; ein JS-Audit der Editor-Blades steht aus.
- **Preis-Integrität A2:** dass `offerPriceUnitValue` (`:1738`) nie 0 zurückgibt und die Einzelpreis-Flanke real ausnutzbar ist, ist **NICHT-VERIFIZIERT** (kein Laufzeit-Exploit gefahren, nur Code-belegt).
- **Heizkörper-Verdrahtung (B4)** ist Code-belegt (`uebernehmen :150-232`), **nicht** zur Laufzeit ausgeführt (Feature-Flag + 0 Zeilen `deal_measurement_items`).
- **Fach-Korrektheit der Rechenkerne** (DIN-EN-12831-Faktoren, BEG-Sätze, VDE-Regeln) wurde **strukturell** beurteilt (plausibel, normativ kommentiert, `to_verify`-Marker im Code), **nicht** gegen die geltende Normfassung nachgerechnet.
- **TABU respektiert:** Invoice-Zone nur an der Naht „Angebot→Position→Rechnungs-Canvas" gestreift, nicht inhaltlich bewertet.
