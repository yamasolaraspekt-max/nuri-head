# Bereich 2 — Verifikation der offenen Vermutungen (Ergänzung)

**Stand:** 2026-07-11 · **read-only** · **kein Bau/Refactor/Löschen/Commit/Lösung.**
**Zweck:** Die in `docs/bereich2-angebot-auslegung-inventur.md` Punkt 10 offen markierten Vermutungen firsthand am Code verifizieren.
**Methode:** 4 parallele read-only-Lesestränge, je `datei:zeile` + Verdikt (BELEGT / WIDERLEGT / WEITER UNKLAR) + Risiko-wenn-ungeklärt.
**Geltung:** `docs/rueckfall-archiv-regeln.md`.

> **Ergebnis in einem Satz:** 6 der 7 Punkte sind jetzt **belegt/widerlegt**; die schärfsten neuen Befunde: der Server **vertraut den Einzelpreisen aus dem Browser** (kein Katalog-Anker → manipulierbar), die MasterSet-**Gemeinkosten/Wagnis-Spalten existieren gar nicht** (VK still zu niedrig), und `offer_details.folder_id` ist eine **tickende, aber derzeit unerreichbare Schema-Falle**.

---

## 1. Ist `wp/index.blade.php` real geroutet/erreichbar? — **WIDERLEGT (totes Legacy)**
- Gerendert **nur** von `Old/OfferConfigController::wp_config()` (`app/Http/Controllers/Old/OfferConfigController.php:30`).
- Dieser Controller ist **in keiner Route registriert** (repo-weiter Grep `OfferConfigController` → nur die eigene Datei; auch die Methodennamen `wp_config`/`masterSets` tauchen in `routes/` nicht auf).
- Kuriosum ohne Widerspruch: liegt unter `Old/`, trägt aber `namespace …Customer\Offer` (`:2`) — es gibt aber nur **diese eine** Datei.
- **Verdikt:** totes Legacy, nicht erreichbar. Die im Inventur-Dokument als „vermutlich tot" markierte zweite Rechen-Wahrheit (JS-Gerätekatalog + `estJAZ` + WP-Auslegung im JS) ist damit **bestätigt unerreichbar**.
- **Risiko wenn ungeklärt:** niedrig für den Betrieb, aber **Wartungs-/Verwechslungsfalle** — `offer/offer/configuration/wp/**` + `Old/OfferConfigController` sehen aktiv aus und könnten in einer Optimierung fälschlich als „führende" WP-Konfig gepflegt werden. Kandidat für belegte Stilllegung (kein Löschen ohne Freigabe, Variante B).

## 2. Welche Route/Controller/View-Kette trifft die WP-Konfig? — **BELEGT**
- **Aktive Angebots-Konfig (nicht WP-spezifisch):** `GET /offers/wizard` → `OfferWizardController::index` (`routes/web.php:3277`) rendert `admin.offer.configuration.offer.config` (`OfferWizardController.php:63`) — also `resources/views/admin/offer/configuration/offer/config.blade.php`, **nicht** `offer/offer/configuration/wp/index`.
- Ordner-Ansicht: `GET /offers/folders/{folder}` → `OfferFolderController::show` → `admin.offer.folder-show` (`routes/web.php:3450`, `OfferFolderController.php:787`).
- **Verdikt:** Es gibt **keine** aktive WP-spezifische Konfig-View mehr; die WP-Auswahl läuft im generischen `config.blade.php`-Wizard bzw. serverseitig über die Energie-Controller (getrennter Strang). Die alte `wp/index.blade.php` ist ersetzt, nicht ergänzt.
- **Risiko:** siehe Punkt 1.

## 3. Rechnet der Server Angebots-Preise nach oder vertraut er JS? — **GEMISCHT (kritische Lücke: Einzelpreis)**
- **Summen: serverseitig neu.** `OfferController::processOffer` ruft `calculateOfferSections()` und schreibt dessen Ergebnis; Client-`total_net`/`total_gross` werden **verworfen** („Server-authoritative totals. Frontend values are ignored", `OfferController.php:2307-2311`; Kommentar „must never trust" `:1681-1683`).
- **ABER Einzelpreis: aus dem Client.** `offerLineTotals()` rechnet `vk = (qty/priceUnit) * offerNodeVkPrice($node)` (`:1903-1904`), und `offerNodeVkPrice/EkPrice/Qty` lesen **reine Array-Felder** aus dem Request (`:1765-1785`, `:1754-1763`) — **keine** DB-/Katalog-Abfrage (Grep `master_set_components|Product::|->price` im Speicherpfad = 0 Treffer).
- **Zweiter Pfad vertraut sogar der Zeilensumme.** `OfferFolderController::calculateDetailTotals()` summiert nur `node['total']` (`:2286`), kein qty×Preis-Recompute. Ausnahme: Distributor-Wechsel überschreibt `node.total` vorher aus DB — dort aber **EK-basiert** (`:2432-2433`).
- **Kein Pricing-Service:** kein `app/Services/Offer*`, kein `Pricing|OfferCalc` (Grep) — Logik liegt **inline in zwei Controllern**.
- **Client schickt die gerechneten Werte mit:** `config.blade.php` submittet `sections` (je Node `price/qty/ek/total`) + `total_net/tax_rate/total_gross` an `POST /offers/save-document` (`:7954-7974`).
- **Verdikt:** Server rechnet die **Summe** gegen, vertraut aber dem **Einzelpreis** des Browsers; keine Katalog-Absicherung.
- **Risiko wenn ungeklärt:** **(hoch)** manipulierbarer Payload kann beliebige Einzelpreise setzen — der „Recompute" bestätigt sie nur; zusätzlich Divergenz zweier Engines und EK-statt-VK-Gefahr im Distributor-Flow; keine Garantie, dass gespeicherte Preise den Katalogpreisen entsprechen (Angebot↔Auftrag↔FiBu). *(Sicherheits-/Integritäts-relevant — als Befund markiert, nicht gelöst.)*

## 4. Welche UI-Buttons treffen welche Angebots-Engine? — **BELEGT (saubere Trennung)**

| Button/Aktion | View | Route | Engine |
|---|---|---|---|
| Angebot/Dokument speichern (Wizard) | `configuration/offer/config.blade.php:7974` | `POST /offers/save-document` | **1 — recompute** (`calculateOfferSections`) |
| Dokumentstatus ändern (offer→deal) | `folder-show.blade.php:5417,6629` | `PATCH …/document-status` | **2 — trust** (`persistDetailSections`) |
| Material-Bestellstatus | `folder-show.blade.php:5422` | `POST …/material-order-status` | **2 — trust** |
| Material final bestätigen | `folder-show.blade.php:5441` | `POST …/material-final-status` | **2 — trust** |
| Distributor/Material wechseln | `folder-show.blade.php:5423` | `POST …/material-change` | **2 — trust** (mit DB-Preis-Override) |

- **Verdikt:** Wizard-View → Engine 1; Folder-View → Engine 2. **Kein Kreuzaufruf** (folder-show ruft `save-document` nicht auf). Die zwei Engines leben auf View-Ebene getrennt, schreiben aber dieselbe `offer_details`-Schiene.
- **Ehrlichkeits-Vorbehalt:** Engine-1-Kette ist wörtlich verfolgt (harter URL `:7974`); Engine-2-Bindung läuft über `data-*`-Attribute + generische fetch-Wrapper — Route-Wahrheit über `route()`-Helper eindeutig, Attribut→Handler plausibel aber nicht Zeile-für-Zeile getract.
- **Risiko wenn ungeklärt:** solange getrennt, kein Doppelschreib-Konflikt; jede künftige Verknüpfung (Ordner-Aktion, die auch Preise rechnet) riskiert abweichende Totals → „zweite Wahrheit" vor jeder Automatisierung klären.

## 5. Wie läuft CostingSet → MasterSet & Angebotsübernahme wirklich? — **BELEGT (zwei getrennte Kalk-Welten + fehlende Spalten)**
- **CostingSet wirkt NUR auf Arbeit, und nur in einem nicht-persistierten Vorschau-Endpoint.** `saveCostingSettings()` schreibt bloß 3 Verweis-/Modus-Felder (`costing_set_id`, `costing_rate_mode`, `costing_fallback`; `MasterSetController.php:2139-2154`). `taskCostingPayload()` (`:2156-2340`) rechnet Labor-Sätze/AW aus `CostingSetRole` und gibt sie **per JSON zurück — speichert nichts**.
- **Material-VK je Komponente ist FREI gesetzt (per-Komponenten-`margin`, Default 50), NICHT aus CostingSet.** VK-Kern `MasterSetController.php:455-458`: `vkPerPiece = purchase_price + gk + wagnis + db`.
- **NEUER BEFUND — stille Null:** die Quell-Spalten `global_gemeinkosten`/`global_wagnis`/`global_mat_margin` auf `master_sets` **existieren in KEINER Migration** (Grep = 0). Also sind GK/Wagnis immer `?? 0` → **VK = `purchase_price × (1 + margin/100)`**, Gemeinkosten/Wagnis laufen still ins Leere.
- **CostingSet-Materialfelder** (`material_overhead_percent`, `risk_percent`, `profit_percent`, …) werden in `MasterSetController` **nirgends** konsumiert (nur eigenes CRUD in `CostingSetController`).
- **Persistierte Set-Totals kommen vom Client** (`main_total/labor_total/total = $data[...] ?? 0`, `:588-591`); serverseitige Fallback-Summe `:520` ist **EK-basiert**.
- **Übernahme `changeDocumentStatus` (`:3131-3168`):** `deals` bekommt einen **flachen Kopf** — u.a. `price = detail->total_net`, `offer_id`, `offer_folder_id`, `offer_number (= offers.offer_no)`, `order_number (= Deal::generateOrderNo)`; **keine Positionen** (bleiben in `offer_details.sections` + Snapshot). Match bestehender Deal über `customer_id + alternative_id + product_id` (`:3147-3151`).
- **Verdikt:** zwei nicht zusammengeführte Kalkulationswelten (CostingSet-Labor ↔ Komponenten-margin); Übernahme = eingefrorener Preis-Skalar ohne Positionen.
- **Risiko wenn ungeklärt:** **(hoch, kaufmännisch)** GK/Wagnis-Aufschläge still wirkungslos → VK systematisch zu niedrig **ohne Fehlermeldung** (verstößt gegen Operanden-Gate: stiller 0-Wert statt Markierung). Match über `customer+alternative+product` (nicht `offer_folder_id`) kann bei mehreren Angeboten desselben Produkts **denselben Deal überschreiben**; `deals.price` friert ein und folgt späteren Angebotsänderungen nicht.

## 6. Relevantes Rest-Schema `deals` / `offers` / `offer_details` — **BELEGT; `folder_id` WIDERLEGT**
- **`deals`:** `customer_id, product_id, alternative_id, service_id?, department_id?, employee_id, price(10,2)?, order_number?, offer_number?, deal_status?, status?, project_status?, offer_id?, offer_folder_id?, measurement_status?` (+ softDeletes, Index `deals_status_index`). **Keine Positions-/Line-Item-Spalte, keine Positionstabelle** — nur der Skalar `price`. Bei der Übernahme bleiben `info/sign_date/location/confirmed_at/delivered_at/checked_by/reviewer_id/project_status` NULL.
- **`offers`:** `offer_no?(unique)`, `customer_id, product_id, alternative_id, service_id?, department_id?, service, created_by?, created_for?, status?`.
- **`offer_details`:** `offer_id, offer_folder_id?, offer_no?, sections(json), placed_images(json), total_net(15,2 def0), tax_rate(5,2 def19), total_gross(15,2 def0), document_status(20), angebot_snapshot_sections(json), angebot_snapshot_at, material_history(json), biography_data(json), brand_*/cover_*/agb_*/branch_footer`. `document_status`-Konstanten `offer|deal|auftrag` (`OfferDetail.php:9-11`).
- **`offer_folders`:** `offer_id, customer_id, alternative_id, product_id, created_by, name?, color, history?, status?, document_status(20), offer_status(40), deal_status(40)`.
- **`folder_id`-Verdacht: WIDERLEGT/BELEGT gemischt.** Die Spalte `offer_details.folder_id` **existiert nicht** (alle Migrationen legen nur `offer_folder_id` an; einziger `folder_id`-Migrationstreffer ist `bitrix_chats.disk_folder_id`). **Zwei Code-Stellen** referenzieren dennoch `folder_id` auf `offer_details`: `Offer::detailForFolder()` (`Offer.php:271-274`, **nirgends aufgerufen**) und `OfferDetailsController::updates()` (`:231-238`, **nicht geroutet**) — beide **derzeit unerreichbar**.
- **Verdikt:** Schema klar; `folder_id` ist eine **latente, aber tote** Falle.
- **Risiko wenn ungeklärt:** sobald einer der beiden Pfade verdrahtet/geroutet wird, feuert MySQL „Unknown column 'folder_id'". Namensdrift `folder_id ↔ offer_folder_id` vor jeder Aktivierung vereinheitlichen.

## 7. HeizlastService / PvProjektService / SmartroutingService / PlausibilityService — Reserve oder tot?

| Service | Echte Prod-Aufrufer | Test | Config-Flag | Verdikt |
|---|---:|---|---|---|
| `Heizlast/HeizlastService` | **0** | nein | nein | **FAKTISCH TOT** (Absicht code-seitig nicht entscheidbar) |
| `Energie/PvProjektService` | **0** | nein | nein | **FAKTISCH TOT** (zieht `StringBuilderService` als toten Zweig mit) |
| `Form/SmartroutingService` | **0** | **JA (5 grüne Tests, FS-05)** | nein | **BEWUSSTE RESERVE** (gebaut + test-gepflegt, aber produktiv dormant; Regel-Tabelle 0 Zeilen) |
| `Form/PlausibilityService` | **0** | nein | nein | **FAKTISCH TOT** |
- **Streng:** „bewusste Reserve" nur bei `SmartroutingService` behauptet — einziger konkreter Code-Beleg (5-Methoden-Testsuite `tests/Feature/Form/SmartroutingServiceTest.php` + FS-05-Marker). Bei den anderen drei: kein Test, kein Flag, kein Reserve-Docblock → Absicht **code-seitig nicht entscheidbar**.
- **Doku-Zusatz (aus `docs/`, nicht Code):** alle vier werden in Audits/Backlog als „gebaut, isoliert, 0 Aufrufer" geführt und einer geplanten Verdrahtungs-/Klärungsrunde zugeordnet (`docs/audit/experten/02-angebot-auslegung.md`, `docs/audit/intelligenz-audit.md`, `docs/backlog-formulare.md`). Doku-seitig also als Reserve **intendiert**, aber offen.
- **Caveat (ehrlich):** „0 Aufrufer" ist statischer Grep; Reflection/String-Dispatch wurde nicht gefunden, ist aber nicht positiv ausgeschlossen (die Audits notieren denselben Vorbehalt).
- **Risiko wenn ungeklärt:** blindes Einhängen von `HeizlastService`/`PvProjektService` erzeugt eine zweite Rechen-Wahrheit; `SmartroutingService` ist reif, aber unverdrahtet **und ungefüttert** (0 Regeln); `PlausibilityService`-Eingabeprüfungen liegen brach.

---

## Zusammenfassung der Verdikte

| # | Frage | Verdikt |
|---|---|---|
| 1 | `wp/index.blade.php` geroutet? | **WIDERLEGT** — totes Legacy |
| 2 | Welche Kette trifft WP-Konfig? | **BELEGT** — keine aktive WP-View; `OfferWizardController`→`config.blade.php` |
| 3 | Server rechnet Preise nach? | **GEMISCHT** — Summe ja, **Einzelpreis client-bestimmt** (Risiko hoch) |
| 4 | Buttons→Engine? | **BELEGT** — Wizard=Engine1, Folder=Engine2, getrennt |
| 5 | CostingSet→MasterSet/Übernahme? | **BELEGT** — 2 Kalk-Welten; **GK/Wagnis-Spalten fehlen** (still 0); Übernahme ohne Positionen |
| 6 | Rest-Schema / `folder_id`? | **BELEGT + `folder_id` WIDERLEGT** (latente tote Falle) |
| 7 | Reserve oder tot? | Smartrouting=**Reserve**; die 3 anderen=**faktisch tot** |

**Neue, im ersten Inventur-Dokument noch offene Erkenntnisse:** (a) Preis-Integrität — kein Katalog-Anker beim Speichern (manipulierbar); (b) `master_sets.global_gemeinkosten/global_wagnis` existieren nicht → stiller 0-Aufschlag; (c) `deal`-Match über `customer+alternative+product` mit Überschreib-Gefahr; (d) `folder_id` als tickende, aber unerreichbare Schema-Falle.

## Evaluator-Notiz
- **Belegt (firsthand, `datei:zeile`):** alle 7 Punkte; besonders die Preis-Client-Vertrauens-Lücke, die fehlenden MasterSet-Spalten und die `folder_id`-Toten-Pfade sind neu und code-belegt.
- **Weiter unklar / ehrlich offen:** die Engine-2-Bindung (data-Attribut→JS-Handler) nicht Zeile-für-Zeile getract; ob der Server die JS-Preise irgendwo doch katalog-prüft (kein Fund, aber Reflection nicht 100 % ausgeschlossen); Absicht hinter den 3 „faktisch toten" Services nicht code-entscheidbar.
- **Nicht gemacht (korrekt):** keine Bewertung, kein Zielkonzept, kein Umsetzungspaket, kein Bau/Refactor/Löschen/Commit. `rueckfall-archiv-regeln.md` beachtet.

---

*Nächster Schritt laut Auftrag: **STOPP.** Kein Bewertungskapitel, kein Zielkonzept, kein Umsetzungspaket. Yama prüft.*
