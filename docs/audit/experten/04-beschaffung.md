# Experten-Inventur 04 — BESCHAFFUNG (Einkauf / Material)

> **Rolle:** Beschaffungs-Experte · **Modus:** rein lesend · **Stand:** 2026-07-10 · Branch `private/app-code-backup`
> **Baut auf:** `docs/audit/{code-audit,intelligenz-audit,automatisierungs-hebel}.md`, `docs/glossar.md`
> **TABU eingehalten:** Nuriva / Video / Invoice / Legacy (Bitrix/NIBE/IMAP) nicht bewertet.
> **Bereich:** Stückliste/Materialbedarf · Bestellwesen · Lieferanten (IDS/OCI/Sonepar/DATANORM) · Bestand · Wareneingang.
> **SQL:** firsthand read-only über CNF (ticket-DB) — alle Row-Counts unten sind gemessen.

---

## 0. Datenbild (SQL, firsthand)

Der Bereich zerfällt scharf in **zwei Zonen**: ein **gefüllter Katalog-/Preis-Kern** und eine **fast durchweg leere Prozess-Schicht** (Bestellung, Lieferanten-Connector, Wareneingang, Lager-Bewegung).

| Tabelle | Rows | Rolle | Zustand |
|---|--:|---|---|
| `products` | 94 | Artikelkatalog (EIN Katalog, s. CLAUDE.md) | **gefüllt** |
| `distributor_prices` | 88 | **führende** Product↔Distributor-Preisschiene | **gefüllt / produktiv** |
| `distributors` | 9 | Großhändler-Stammdaten (+ `payment_terms`) | gefüllt |
| `distributor_departments` | 13 | Distributor-Abteilungen | gefüllt |
| `article_groups` | 15 | Warengruppen | gefüllt |
| `materials` | 23 | Material-Stammliste (klein) | gefüllt |
| `inventories` | 15 | Lagerbestand (Menge + Standort) | gefüllt |
| `assets` / `machines` | 18 / 6 | **Betriebsmittel** (Firmen-Assets/-Maschinen), NICHT Verkaufsmaterial | gefüllt |
| `product_positions` | 13 | — | gefüllt |
| `distributor_product` | **0** | **toter Zweit-Pivot** Product↔Distributor | **leer / dupliziert** |
| `purchase_requests` | **0** | Kaufanfrage-Register | **leer** |
| `supplier_connections` | **0** | IDS/OCI-Connector-Config | **leer (Feature konfiguriert, nie genutzt)** |
| `supplier_article_map` | **0** | neutrale Hersteller-Artikel-Map (W2) | **leer** |
| `supplier_connection_mappings` / `supplier_import_logs` | 0 / 0 | Feld-Mapping / Import-Protokoll | leer |
| `imported_ids_items` | **0** | IDS-Punch-out-Rückgabe-Puffer | **leer** |
| `goods_receipts` / `goods_receipt_attachments` | **0** / 0 | Wareneingang/-ausgang (Beleg) | **leer** |
| `delivery_notes` / `delivery_note_images` | 0 / 0 | Lieferschein | leer |
| `inventory_histories` | **0** | Lager-Bewegungen (Verbrauch) | **leer (nie gebucht)** |
| `inventory_request_outs` | 0 | Material-Anforderung raus | leer |
| `planner_item_materials` / `planner_group_materials` / `planner_item_material_requests` | 0 / 0 / 0 | Planner-Materialbedarf | leer |
| `offer_asset_lists` / `offer_product_lists` | 0 / 0 | — | leer |

**Kern-Aussage:** Der **Angebots-/Preis-Teil** der Beschaffung ist real bestückt (94 Artikel, 88 Distributor-Preise über 9 Großhändler). Der **operative Beschaffungs-Prozess** (Bedarf→Bestellung→Wareneingang→Bestandsfortschreibung) ist **gebaut, aber unbenutzt** — jede Prozess-Tabelle ist leer.

---

## 1. IST-FUNKTIONEN (mit Beleg)

### 1.1 Stückliste / Materialbedarf — aus Auftrag & Aufmaß ABGELEITET (nicht abgetippt)
- **Materialliste je Gewerk/Auftrag:** `DealMaterialListController::show` (`app/Http/Controllers/Customer/Offer/DealMaterialListController.php:33-173`). Baut die Liste aus **drei Quellen**, umschaltbar per `?source=offer|feinaufmass|compare`:
  - **Angebot** → `extractMaterialsFromSections($offerDetail->sections …)` (`:56-60`, Impl. `:752+`) — die Positionen des Angebots-Belegs (`offer_details.sections`).
  - **Feinaufmaß** → `buildFeinaufmassMaterials($measurement)` aus `deal_measurements.materials_snapshot` (`:62`, `:225+`), Fallback `sections_snapshot`/`DealMeasurementItem` (`:256+,:473+`).
  - **Vergleich (Default)** → `compareOfferAndMeasurementMaterials(...)` (`:92`) diffs Plan (Angebot) vs. Ist (Aufmaß): `change_type` added/changed/removed/same.
- **Speicherung Aufmaß-Material:** `DealMeasurementMaterialController::saveMaterials` (`app/Http/Controllers/Customer/Deal/DealMeasurementMaterialController.php:16-77`) → `materials_snapshot` + Zähler + History. **`plan_qty` vs. `verbrauch_qty`, `delta_qty = verbrauch − plan`** (`:212-214`) — Delta wird berechnet, Mengen NICHT (H-B3).
- **Beleg-Kette (Glossar-konform):** Material hängt an `offer_details` (Angebot) und `deal_measurements` (Aufmaß am Auftrag), verweist per FK auf `deals`/`offers`. Bestand-Kette wird nicht dupliziert.

### 1.2 Bestand-Abgleich der Materialliste — Lager-Match ist verdrahtet
- `attachInventoryData` (`DealMaterialListController.php:1345-1460+`): matcht jede Materialzeile gegen `inventories` per **`product_id`** (bevorzugt) oder **`LOWER(TRIM(article_no))`** (`:1378-1411`), summiert Bestand, rechnet `inventory_missing_qty = max(required − onHand, 0)` (`:1428`) und setzt `stock_status` (`lager`/`bestellen`/`teilweise`/`unbekannt`) + Standort-Detail (Filiale/Raum/Regal/Reihe/Fach) (`:1430-1450`).
- Druck-Modi: `normal` / `lager` (Lagerort) / `order` = **„Bestelldruck"** (Lieferant, Bestellstatus, Liefertermin) (`:148-152`). → **Bestellung = Ausdruck**, keine elektronische Order.

### 1.3 Lieferanten-Anbindung Großhandel — IDS/OCI-Punch-out (real, aber Einkaufs-seitig „Preis-Discovery", nicht „Order-out")
- **Connector-Service:** `app/Services/Suppliers/SupplierConnectorService.php` (1380 LOC). Baut Punch-out-URL zum Großhandels-Webshop mit Auth-Param-Mapping (`buildOpenParams/buildOpenUrl` `:22-310`), empfängt **Warenkorb-Rückgabe** (`handleReturn` `:311`, `getBasketPayload` `:1020`, XML/OCI-Parser `:836-1019`), Review→Import (`importReviewedItems` `:380`, `importReviewedRow` `:530`, `findOrCreateReviewedProduct` `:630`).
- **Presets:** `standard_ids` (IDS-Connect), `standard_oci` (Basket `OCI_DATA`), `gc_online`, **Sonepar** (kein Suchfenster, direkter IDS-Sprung) — `SupplierConnectionController.php:178-233,744-780`. `connector_type` ∈ `ids,oci,api,csv,xml,bmecat,datanorm` (`:809`).
- **Import-Ziel:** `SupplierProductImportService.php` (423 LOC) schreibt Rückgabe-Artikel in **`products` + `distributor_prices`** (`importMappedItem:14`, `upsertDistributorPrice:357`), mit `update_existing`/`create_missing`-Schaltern (`:290-297`).
- **Offer-Anbindung:** `IdsSearchController::requestPriceForMaterial` (`app/Http/Controllers/Product/IDS/gconline/IdsSearchController.php:24-64`) nimmt **Offer-Folder-Positionen** (`folder->detail->sections`), schickt sie zur Preisanfrage an den Shop → `forwardToShop` (`:140-172`), Rückkehr `back/inlineBack` (`:173-183`). Event `OfferSupplierProductsReturned`.
- **Neutrale Mapping-Schicht (jung, W2):** `Mappers/IdsMapper.php` (Sonepar/IDS, produktiv gedacht) → `supplier_article_map` idempotent per `hersteller + herst_artikelnr + supplier_channel`, mit strukturiertem Skip-Log + Tages-Zähler (`:36-95`). `MapperRegistry` + `OmdMapper`/`DatanormMapper` = **Stubs** (`DatanormMapper::map()` gibt `null`).

### 1.4 DATANORM — nur Vorschau-Parser, KEIN Import
- `DatanormController` (`app/Http/Controllers/DatanormController.php`, 49 LOC): parst `T;A;`-Zeilen zu `article_no`+`description` und **rendert nur eine View** — **keine Persistenz, kein Preis, kein Product-Anlegen**. `DatanormMapper` = Stub. → DATANORM ist **Demo/Skelett**, nicht funktionsfähig.

### 1.5 Kaufanfrage (Purchase Request) — Insel-Register
- `PurchaseRequestController` (373 LOC): CRUD-Register für Einzel-Beschaffungswünsche mit `used ∈ Kunden|Mitarbeiter|Problem` (`:190`), Brand/Distributor (auch Freitext „new"), Retail-/EK-Preis, Rabatt, Anfrage-von/-an-Mitarbeiter, Bild, Status `Unpublished`/`Published`. **Nicht** an `deals`/`offers`/Materialliste/`goods_receipts` gekoppelt — freistehende Wunschliste. 0 Rows.

### 1.6 Wareneingang / -ausgang — Beleg-Register, OHNE Bestandsbuchung
- `GoodsReceiptController` (790 LOC): `store/update/issue/quickStatus` (`:213-408`). Ein GR ist ein **Dokument** mit `code` (auto `GoodsReceipt::nextCode`), Status (`inbound`→`issued`), Inbound-/Outbound-Datei-Anhängen, `qty/unit/purchase_price`. Verknüpfbar an **Beleg-Kette**: `customer_id, object_id, lead_product_list_id (=Gewerk), article_group_id, department_id` (Schema).
- **`issue()`** (`:345-407`) bucht Warenausgang (Empfänger/Projekt/Kunde/Objekt) — **aber verändert `inventories` NICHT**. Kein `InventoryHistory`, kein Mengen-Delta. → **Wareneingang → Bestand ist NICHT verdrahtet.** GR ist ein reiner Lieferschein-/Belegtracker.

### 1.7 Lager (Inventory) — manuelle Pflege + manueller Verbrauch
- `InventoryController` (776 LOC): CRUD + `useProductAjax` (`:568-636`) = Verbrauchsbuchung: dekrementiert `inventories.quantity`, schreibt `InventoryHistory` (`type='used'`, before/after, Einsatzort, Kunde). Validiert `usedQty ≤ Bestand`. **Manuell** ausgelöst, nicht aus Materialliste/GR. `inventory_histories`=0 → **nie benutzt**.
- **Low-Stock:** hart `quantity <= 5` (`:123`). **Kein** artikelspezifischer Meldebestand/Reorder-Point in `inventories` (Spalten nur `product_id, article_no, quantity, quantity_unit`). Keine Nachbestell-Auslösung.
- `assets`/`machines` = Betriebsmittel-Verwaltung (Ratenzahlung/Service/Wartung) — eigener Zweck, nicht Verkaufs-Beschaffung.

---

## 2. STÄRKEN
1. **Materialbedarf wird abgeleitet, nicht abgetippt.** Angebots-Positionen und Feinaufmaß fließen automatisch in die Materialliste; der Vergleichs-Modus (Plan vs. Ist, Delta) ist echte Intelligenz und Glossar-/Beleg-konform (`offer_details`→`deal_measurements`, FK-sauber).
2. **Bestands-Match ist gebaut.** Materialliste ↔ `inventories` per product_id/article_no inkl. Fehlmengen (`missing_qty`) und Standort — die Grundlage für „was muss bestellt werden" ist da.
3. **Großhandels-Punch-out ist ernsthaft implementiert** (IDS-Connect + OCI + GC-Online + Sonepar-Preset, XML/OCI-Basket-Parser, Review-Import in `distributor_prices`). Das ist die aufwändigste, teuerste Achse — und sie existiert.
4. **Eine führende Preisschiene:** `distributor_prices` (88 Rows) ist die reale Product↔Distributor-Wahrheit; Angebotspreise sind daraus vorbefüllt (Vorbefund automatisierungs-hebel: `OfferWizardController:917-938`).
5. **Distributoren tragen `payment_terms`** — Quelle für abgeleitete Rechnungs-Fälligkeit (H-B1) vorhanden.
6. **Neutrale Mapping-Schicht (`supplier_article_map`, IdsMapper)** ist sauber gedacht: idempotent, Skip-Logging, Abdeckungslücken sichtbar — ein solides Fundament für Multi-Lieferanten.

## 3. SCHWÄCHEN
1. **Kein Bestell-Ausgang (Order-out).** Es gibt Punch-out (Artikel/Preis holen) und **Bestelldruck** (Papier), aber **keine elektronische Bestellübermittlung** (kein UGL/IDS-ORDER-File raus, kein EDI-Auftrag). „Bestellwesen" endet am Drucker.
2. **Wareneingang bucht keinen Bestand.** `goods_receipts` und `inventories` sind **entkoppelt** — GR-`issue()` ändert die Lagermenge nicht, `inventory_histories`=0. Die Kette **Bestellung→Wareneingang→Bestand→Verbrauch** ist an **jeder** Naht offen.
3. **Prozess-Schicht komplett unbenutzt (0 Rows):** `purchase_requests`, `supplier_connections`, `supplier_article_map`, `imported_ids_items`, `goods_receipts`, `delivery_notes`, `inventory_histories`, `inventory_request_outs`, alle Planner-Material-Tabellen. Der Connector ist konfigurierbar, aber es ist **keine einzige Lieferanten-Verbindung angelegt** → im Ist läuft de facto kein automatischer Katalog-/Preis-Sync.
4. **DATANORM ist ein Skelett** (Parse-Preview ohne Persistenz; Mapper-Stub) — trotz Enum-Wert `datanorm`. Ein zentraler Großhandels-Standard fehlt praktisch.
5. **Kaufanfrage ist eine Insel** — nicht an Auftrag/Materialliste/Bedarf gekoppelt; kein Weg von „Material fehlt" → „Kaufanfrage/Bestellung".
6. **Doppel-Pivot / „Eine Wahrheit"-Verstoß:** `distributor_product` (Product-Model `:84`, 0 Rows, tot) parallel zur produktiven `distributor_prices`-Schiene (Distributor-Model `:55`). Kandidat für belegte Stilllegung.
7. **Kein Meldebestand/Reorder:** Low-Stock hart `≤5`, artikel-unabhängig; keine Nachbestell-Vorschläge.
8. **Menge aus Geometrie fehlt (H-B3, bestätigt):** Mengen werden im Aufmaß von Hand gesetzt, Maschine rechnet nur Delta.
9. **Validierungs-Löcher (aus intelligenz-audit bestätigt):** `PurchaseRequestController:203` `quantity` `nullable|numeric` **ohne `min`** → Negativmenge möglich; `DistributorPriceController:315` Preis ohne `min:0`. Snapshot-Divergenz-Risiko (`materials_snapshot`) ohne dokumentierte Invalidierung.
10. **Mass-Assignment:** `Asset` `guarded=[]` (P2, code-audit `:285`), GR-Pfad teils `$request->all()`-Muster im Umfeld.

## 4. REIFE (je Teilbereich)

| Teilbereich | Reife (1–5) | Begründung |
|---|:--:|---|
| Materialbedarf aus Angebot/Aufmaß | **4** | abgeleitet, Vergleich Plan/Ist, Delta — nur Mengen-aus-Geometrie fehlt |
| Bestands-Abgleich (Material↔Lager) | **3** | Match + Fehlmenge gebaut; hängt an manuell gepflegtem Lager, keine Reservierung |
| Lieferanten-Connector (IDS/OCI/Sonepar) | **3** | technisch stark, aber 0 aktive Verbindungen, nur Preis-Discovery, kein Order-out |
| DATANORM | **1** | Parse-Preview, kein Import (Stub) |
| Bestellwesen (Order raus) | **1** | nur Druck; keine elektronische Übermittlung |
| Kaufanfrage | **2** | funktionierendes Register, aber Insel |
| Wareneingang/-ausgang | **2** | Belegtracker + Dateien; keine Bestandsbuchung |
| Lager/Bestand + Verbrauch | **2** | CRUD + Verbrauchsbuchung vorhanden, aber ungenutzt, kein Meldebestand, kein GR-Zulauf |

## 5. AUTOMATISIERUNGS-REIFE (gesamt): **niedrig–mittel (≈ 2 / 5)**

- **Was automatisch geht:** Materialbedarf-Ableitung aus Beleg (Angebot/Aufmaß) + Plan/Ist-Delta + Bestands-Match/Fehlmenge; Punch-out-Preisimport in `distributor_prices`; Nummernvergabe (`GoodsReceipt::nextCode`).
- **Was Medienbruch bleibt:** Bedarf→Bestellung (Papier), Bestellung→Lieferant (kein Order-out), Wareneingang→Bestand (entkoppelt), Meldebestand→Nachbestellung (keins), Menge-aus-Geometrie (keins). Dazu: der Kickoff „Angebot→Auftrag stößt Materialliste an" fehlt (H-A5/I-11).
- **Fazit:** Die **Datengrundlage** für Beschaffungs-Automation ist überraschend weit (Katalog, Preise, Bedarf-Ableitung, Bestands-Match). Die **Prozess-Automation** ist praktisch **nicht in Betrieb** — leere Prozess-Tabellen und drei offene Nahtstellen (Order-out, GR→Bestand, Reorder). Größter Hebel: (a) GR→Bestand koppeln, (b) Materialliste→Bestellvorschlag (Fehlmenge×Lieferant×`payment_terms`), (c) mind. eine echte `supplier_connection` produktiv schalten.

---

## 6. Gelesen / Nicht-gelesen

**Vollständig gelesen:** `PurchaseRequestController.php` (373), `DealMeasurementMaterialController.php` (347), `DatanormController.php` (49), `DatanormMapper.php`, `IdsMapper.php`, `GoodsReceiptController::store/update/destroy/issue` (`:213-408`), `InventoryController::useProductAjax + analytics` (`:116-135,:568-636`), `DealMaterialListController::show + attachInventoryData` (`:33-173,:1345-1460`), `SupplierConnectorService` Kopf + Methoden-Index (`:1-120` + grep aller Signaturen), `SupplierProductImportService` Methoden-Index, `glossar.md`, relevante Passagen `code-audit.md`/`intelligenz-audit.md`/`automatisierungs-hebel.md`. Routen `routes/web.php:493-548`. SQL: Row-Counts + Schemata (`distributor_prices`, `inventories`, `purchase_requests`, `supplier_connections`, `goods_receipts`).

**NICHT / nur teil-gelesen (nicht verifiziert im Detail):**
- `DealMaterialListController.php` Mitte (`:175-1344`, ~1170 Zeilen): Bestelldruck-Rendering, `filterMaterials`, `compareOfferAndMeasurementMaterials`, Sektions-Extraktoren im Detail — **nur über Methoden-Grep erfasst**, nicht Zeile für Zeile.
- `SupplierConnectorService.php:120-1380` (Basket-Parser, GC-Online-Normalisierung, Auth-Template-Ersetzung) — Signaturen gelesen, Bodies nicht vollständig.
- `SupplierConnectionController.php` (1115), `IdsController.php` (327), `DistributorController.php` (1075), `DistributorPriceController.php`, `MachineController`, `AssetController`, `DeliveryNoteController`, `InventoryRequestOutController`, `MasterSetDistributorCompareController`, `MateriallisteController` (27, Energie-Strang), `PlannerItemMaterialController` (610) — nur per Grep/Kontext gestreift.
- Views/Blade (`admin.deal.material-list`, IDS-Views) — nicht gelesen.

## 7. NICHT-VERIFIZIERT (explizit)
- Ob je eine `supplier_connection` **produktiv** getestet wurde (0 Rows ⇒ vermutlich nie live; `last_test_status`-Spalte existiert, nicht abgefragt).
- Ob `distributor_product` (toter Pivot) **irgendwo** noch gelesen wird außer Model-Relation `Product.php:84` — nicht app-weit gegrept.
- Ob GR→Bestand vielleicht über einen **Observer/Job** doch gekoppelt ist: `app/{Observers,Jobs,Console/Commands,Listeners}` gegrept → **kein** Beschaffungs-Treffer außer Event `OfferSupplierProductsReturned`. (Model-Observer in `AppServiceProvider`/`EventServiceProvider` nicht einzeln geprüft.)
- Ob `materials_snapshot` gegen die Quelle invalidiert wird (Snapshot-Divergenz) — offen (auch code-audit: NICHT-VERIFIZIERT).
- Genaue Semantik `inventory_request_outs` / `planner_item_material_requests` (0 Rows) — nur als leer erfasst, Controller-Logik nicht gelesen.
- DATANORM-Standard-Abdeckung (Satzarten außer `T;A;`) — nur der eine Satztyp im Parser gesehen.

## 8. Selbstkritik
- **Row-Count = Nutzung?** Ich schließe aus 0 Rows auf „unbenutzt". Das ist bei einer lokalen, nicht deployten App (Memory: „App nur lokal") plausibel, aber **kein Beweis**, dass die Funktion kaputt ist — sie kann fertig-aber-noch-nicht-in-Betrieb sein. Reife habe ich entsprechend an **Code-Vollständigkeit**, nicht an Daten, gemessen; die Prozess-Nahtstellen-Befunde (GR bucht keinen Bestand) sind dagegen **codeseitig** belegt, nicht nur datenseitig.
- **`DealMaterialListController` (2758 LOC)** habe ich bewusst nur an den Enden (show, attachInventoryData) tief gelesen; ein Order-out o. Ä. könnte theoretisch in der ungelesenen Mitte stecken — die Routen (`:493-548`) zeigen aber **keinen** Bestell-Sende-Endpunkt, das stützt die Aussage „nur Druck".
- Ich habe **Bewertungen des Angebots-Preis-Vorbefunds** aus `automatisierungs-hebel.md` übernommen (H-A5/B1/B3) statt sie unabhängig neu zu belegen — bewusst, da Fundament-Dokument, aber es ist geerbte, nicht selbst-verifizierte Evidenz.
- Blade/Frontend ungelesen → UI-seitige Bestellfunktionen (JS-Fetch auf einen Endpunkt) könnten mir entgehen; ich habe serverseitig (Routen/Controller) gegengeprüft, was das Risiko mindert.
</content>
</invoke>
