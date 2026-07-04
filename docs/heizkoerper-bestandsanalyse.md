# Heizkörper-Modul — Bestandsanalyse (Phase A · NUR ANALYSE)

> **Reine Analyse. Kein Code, keine Migration, kein Model, keine Änderung an Bestandscode.**
> Quellen: ticket-MySQL-Schema (`information_schema`) + Code beider Repos (belegt via 4 Lese-Agenten +
> Governance-Dateien). Basis: `/Users/yamanuri/Documents/ticket` (Laravel 11, MySQL, Blade/Alpine/Vuexy) ·
> `/Users/yamanuri/Herd/wberechnung` (Laravel **13**, SQLite, Blade/Alpine/Tailwind v4, PHPUnit 12,
> Heizlast DIN EN 12831, aktuell gemessen 271 Tests). Stand: 2026-07-04.
> Verzahnt mit `wberechnung-transplant-vorbereitung-landkarte.md` + `katalog-reconciliation-plan.md`.

## Leitbefund (in einem Satz)
Der **EN-442-Rechenkern + hydraulische Abgleich existiert praktisch fertig in wberechnung**; die
**Katalog-, Bestandsaufnahme-, Stücklisten- und Angebots-Andockpunkte existieren in ticket** — das Modul ist
überwiegend **Reconciliation + Adapter + Empfehlungs-/Schema-Aufsatz**, kaum Neubau von Physik. Die größten
offenen Punkte sind **zwei Konflikte** (Bestandsaufnahme-Doppelung, konkurrierende Lieferantenstacks), nicht fehlende Physik.

---

## A1 — Bestandsaufnahme (Fundstellen, belegt)

### A1.1 · wberechnung — EN-442-Physik & Heizlast-Datenmodell (praktisch fertig)
| Baustein | Schlüssel | Datei:Zeile |
|---|---|---|
| Katalog `heizkoerper` | `q_norm_w_pro_m, exponent_n, norm_bedingung('75/65/20'), bauart, bauhoehe/bautiefe_mm` + Kermi-kalibrierter Seeder | `…/create_heizkoerper_table.php`; `HeizkoerperSeeder.php:24-56`; Model `Heizkoerper.php:12-30` |
| **EN-442-Kern** `HeizkoerperService` (DB-frei) | Δt_log (`:93-108`), Δt_norm aus `norm_bedingung` (`:111-120`), `qReal=Q_norm·(Δt/Δt_norm)^n` (`:20-34`), `minVorlauf` Bisektion (`:42-62`), Ampel grün/gelb≥90%/rot (`:82-90`), Leistungstabelle 5 Szenarien (`:70-79`) | `app/Services/Heizlast/HeizkoerperService.php` |
| **Hydraulik** `HydraulikService` | Massenstrom `V̇=Q/(1,163·Δϑ)` (`:32-35`), `kv=V̇/√Δp` (`:38-43`), Voreinstellung DN15 (`:46-55`), **Abgleich Verfahren B** raumweise + Pumpen-Förderhöhe (`:63-95`) | `app/Services/HydraulikService.php` |
| Raum + Heizlast | `heizlast_raeume(theta_int_c, grundflaeche_m2, **heizkoerper**-JSON [{heizkoerper_id,laenge_m,anzahl}])`, `heizlast_projekte(**ziel_vorlauf_c, spreizung_k**, ergebnis-JSON)`, `heizlast_bauteile`, `konstruktionen`, `raum_geometrien` | Migrationen `2026_06_21_…`; `HeizlastRechner.php:113-128` (`auslegungsheizlast_w`) |
| Verdrahtung End-to-End | `HeizlastProjektService::emitterAbgleich()` → gebäudeweit `benoetigte_max_vorlauftemp_c` → WP-Auswahl; Standalone-UI `HeizkoerperCheckController` / `FussbodenCheckController` | `HeizlastProjektService.php:93-225` |
| Szenarien | `[[75,65],[55,45],[50,40],[45,35],[35,28]]` **inline** (nicht zentralisiert) | `HeizkoerperService.php:72` |
| **Fehlt in wberechnung** | Verfahren A; echtes Strang-/Rohrnetz; herstellerspez. Ventil-kvs (nur generisch DN15); Szenario-Config zentralisiert; dediziertes Emitter-Pivot (Heizkörper liegen als JSON im Raum) | — |

### A1.2 · ticket — Katalog, Stückliste, Angebot, Bestandsaufnahme
| Struktur | Zweck | Datei / Andock |
|---|---|---|
| `products` + `article_groups` (+`sub_article_groups`, `brands`) | Artikel-Stammdaten; Heizkörper = Artikel mit `article_group='Heizkörper'` (kein neuer Enum) | `Product.php`; `create_products_table.php` |
| `master_sets` + `master_set_components` | Stückliste/Bundle; **`type='haupt'\|'zubehoer'`** existiert, `parent_id`-Hierarchie, `distributor_id`/`product_id`/`qty`/Preise | `create_master_set_components_table.php`; `+add_commercial_fields…` |
| `deal_measurement_items` | Durchstich **offer→master_set→component→product→Bestellung** mit `qty_*`, Preisen, `stock_allocation` | `create_deal_measurement_items_table.php` |
| `offer_product_lists` / `offer_details` | Angebotspositionen (relational bzw. JSON-Snapshot) | — |
| **`radiator_installations`** | **echte Heizkörper-Ist-Aufnahme** je Kunde/Objekt (an Kundenakte, `NewLeadsController:2197`): `room, room_size, width/height/depth, niche_*, supply_valve(_presettable), return_valve, renew_thermostat_head, limbs, image` — **ohne** EN-442/Produkt-Link | `RadiatorInstallation.php`; `RadiatorInstallationController`; Views `admin/configurations/radiator/*` |
| `product_formulas` | JSON-Formular-Builder je `article_group` (versioniert) — mögliches Aufnahme-Formularschema | `ProductFormula.php` |
| **`radiators` / `Radiator` / RadiatorController** | ⚠️ **Fehlbenannt = Wechselrichter** (Route `product.inveter.store`, PV-Spalten) — **ignorieren** | `RadiatorController.php:13`; `routes/web.php:2382` |

### A1.3 · ticket — Lieferantenschnittstellen (Realitäts-Check)
| Kanal | Reifegrad (ticket) | Felder / Bilder / Attribute |
|---|---|---|
| **IDS/OCI** (Punchout; Sonepar/WKE, GC-Online, FEGA) | **produktiv** | ArtNr, EAN, Preise (UVP/EK), VAT, Einheit, Verfügbarkeit; **Bilder nur opportunistisch** (`saveProductImageIfAvailable` → `product_images`); **keine ETIM** |
| **DATANORM** | **Stub** | nur `T;A;` → ArtNr+Text, keine Preise, keine Persistenz (`DatanormController:14-48`) |
| **OMD / UGL / BMEcat** | **nicht vorhanden** (0 Treffer) bzw. nur Dropdown | — |
| `supplier_article_map` | **existiert nicht**; De-facto-Mapping = `distributor_prices` (distributor_id+product_id+article_no) | Andock: `resolveProduct()`/`upsertDistributorPrice()` |

### A1.4 · wberechnung — eigene Beschaffungsschicht (KONKURRIERT mit ticket!)
ARCHITEKTUR.md §57–107: **`artikel` + typisierte `*_specs` + `artikel_merkmale`(ETIM) + `lieferanten_artikel`**. Connectoren
(`App\Services\Procurement\SupplierConnector`): **DATANORM echt** (A-Satz→`artikel`+`lieferanten_artikel`), **openMASTERDATA
echt** (`OpenMasterdataClient`→`artikel_merkmale` ETIM, sonst Offline-Double), **IDS/UGL/OCI = strukturierte Stubs**.
→ **OMD/ETIM existieren — in wberechnung, nicht in ticket.** Zwei komplementäre, konkurrierende Supplier-Stacks.

### A1.5 · UI-Patterns (Funktionsmuster in wberechnung, Design in ticket)
| Pattern | Fundstelle (Datei:Zeile / x-data) | Wiederverwendbar für |
|---|---|---|
| **Heizkörper-Konfigurator existiert schon**: 2-Spalten (Eingabe/Live-Ergebnis sticky), Debounce-POST an JSON-Endpunkt, `x-data="heizkoerperCheck(@js($payload))"` | wb `heizkoerper-check/index.blade.php:15,27,124,136` | **direkte Blaupause** B4/B7-UI |
| **Szenario-Tabelle „je Systemtemperatur (EN 442)" mit Deckung%+Ampel je Zeile** | wb `heizkoerper-check/index.blade.php:104-116` | B7 Kernanforderung — praktisch fertig |
| Ampel rot/gelb/grün als Badge+Dot, server-seitiges Enum → `:class` | wb `heizkoerper-check…:50-54,145-148` (Farbcodes `#3f7d5a/#b7822a/#b65a4e`) | B7 Ampel 1:1 |
| **Editierbare Positionsliste** (`<template x-for>` + `addHk()/removeHk(i)`, Inline-`x-model.number` + Live-`berechne()`) | wb `heizkoerper-check…:56-73` | B3-Erfassung / B14-Positionen |
| Mehrstufiger Konfigurator (nummerierte Sektionen 1·/2·/3·), Methoden-Umschalter (Segmented `x-show`), Sanierungs-Presets | wb `wr-auslegung/index.blade.php:36-64`; `heizlast/index.blade.php:17-30` | Szenario-/Methoden-Stufen |
| **Parametrisches SVG** (voller Editor: `:viewBox` reaktiv, `x-for` Segmente/Öffnungen, Pointer draw/pan/zoom/snap, Maßketten) | wb `grundriss/editor.blade.php:87-156,226-375` | B9 Heizkörper-/Ventil-Schema (bewährtes Muster) |
| Workflow-Stepper (Breadcrumb) | wb `partials/workflow-stepper.blade.php` | Schritt-Navi |
| Erfassungs-Formular (jQuery/Bootstrap): Kunde→Objekt→HK, Feld-Badges — liefert **Feldliste** (Typ/Etage/Raum/Größe/Nische/Ventile) ohne Rechnung | ticket `admin/configurations/radiator/partials/radiator_section.blade.php:16-315` | B3 Stammfelder |
| Status-Pill/Score-Badge im **ticket-Vuexy-Look** | ticket `offer/…/wizard-smart.blade.php:510-529` | Ampel-Optik im Ziel-Design |
> **Korrektur/Wichtig:** **ticket nutzt Alpine.js NICHT produktiv** (nur Alt-/`*copy*`-Blades); produktiv ist
> **jQuery + Bootstrap/Vuexy**. Das **Funktionsmuster** (Debounce-POST → Server rechnet → Client zeigt State/Ampel)
> stammt komplett aus wberechnung; für ein neues ticket-Modul muss **Alpine eingeführt** ODER die Konfigurator-UI
> in tickets jQuery/Vuexy-Stack **neu gebaut** werden (→ A4-Entscheidung). Das CSS-Framework unterscheidet ohnehin
> (Tailwind ↔ Vuexy → Views neu bauen, Landkarte Achse ③).

### A1.6 · Governance
- **wberechnung** (CLAUDE.md/ARCHITEKTUR.md): Logik in Services, Controller dünn, Blade/Alpine nur Darstellung;
  **Zwei-Schichten-Stammdaten verbindlich** (Physik standalone `materials`/`konstruktionen`/`baualtersklassen`,
  U-Werte **immer** via `UWertService`; kaufbar = `Artikel`+`*_specs`+`artikel_merkmale`); neue kaufbare Komponente
  = `Artikel`+Spec (Vorbild `pv_module_specs`); **keine neuen Basis-Ordner ohne Freigabe**; PHPUnit-Pflicht
  (happy/failure/edge, keine Tests löschen), `pint` vor Commit; **Doku nur auf explizite Anfrage**.
- **Test-/Norm-Governance:** Norm-Nachweis ist **referenzfall-getrieben** (Sollwerte je Regel, Normversionen zentral
  in `SizingConstants`; DB-freie Rechenkerne wie `HeizlastRechner`/`HeizkoerperService` unit-testbar). Abnahme-Baseline
  laut `ABSCHLUSSBERICHT.md`: **281 grün / 1072 Assertions** (Suite wächst weiter — vorher gemessen 271). Für das
  Heizkörper-Modul heißt das: EN-442-/Hydraulik-Kern mit DIN-Referenzfällen testen (Δt_log, `qReal`, Deckung, Voreinstellung).
- **ticket**: **keine CLAUDE.md** (keine formalisierten Modulregeln) → Konvention aus Nachbarcode ableiten; Design
  verbindlich Vuexy/Blade (`ui-design-ticket-verbindlich`).
- **Modul-Grenze / Migration**: wberechnung ist Laravel 13/PHPUnit 12, ticket Laravel 11 — Framework-Delta beim
  Transplant beachten; die geplante wberechnung→MySQL-Migration (Reconciliation-Plan) ist der Rahmen.

---

## A2 — Checklisten-Matrix (B1–B14)

| # | Baustein | Einstufung | Fundstelle / Andockpunkt · Bewertung |
|---|---|---|---|
| **B1** | radiator_categories/types/performances (param. EN-442) | **VORHANDEN** (wb) | `heizkoerper`-Katalog + Exponent/Normbedingung, Kermi-kalibriert. → in ticket-Katalog überführen (additive Spec, `katalog-reconciliation-plan`-Muster). |
| **B2** | radiator_models (Herstellerschicht) | **TEILWEISE** | wb `heizkoerper.hersteller/typ/bauart` ohne echte Modell-/Varianten-Schicht; ticket `products.brand_id`+`article_group`. Fehlt: dedizierte Modell-Ebene. |
| **B3** | room_radiators (Bestandsaufnahme) | **KONFLIKT** | ticket `radiator_installations` (reiche Ist-Aufnahme, an Kundenakte, ohne EN-442/Produkt-Link) **vs** wb `heizlast_raeume.heizkoerper`-JSON (Rechen-Zuweisung zum Katalog, ohne physische Aufnahme). Zwei Repräsentationen — **A4-Entscheidung**. |
| **B4** | RadiatorPerformanceService (ΔT_log, n, Szenarien) | **VORHANDEN** (wb) | `HeizkoerperService` DB-frei, komplett. Direkt transplantierbar (Adapter). |
| **B5** | Anschlussart-Korrekturfaktoren | **FEHLT** | kein Fund in wb-Physik. Andock: Aufsatz auf `HeizkoerperService`/Katalog (Korrekturfaktor-Tabelle). |
| **B6** | Einrohr: heating_circuits + Strangkaskade | **TEILWEISE** | `HydraulikService` Verfahren B (raumweise, Gesamt-V̇, Förderhöhe) da; **kein** Strang-/Rohrnetz-Modell, **keine** Einrohr-Kaskade. Fehlt: `heating_circuits` + Kaskadenlogik. |
| **B7** | Deckungsgrad + Ampel je Szenario | **VORHANDEN** (wb) | `status`/`deckung_pct` je Raum + gebäudeweit `benoetigte_max_vorlauftemp_c`. |
| **B8** | Empfehlungs-Engine (Upgrade/Länge/Lüfter/Kühlung/2.HK/Fallback) | **FEHLT** | kein Fund; Aufsatz auf `leistungstabelle`/`minVorlauf`. Neu (aber auf fertiger Physik). |
| **B9** | SVG-Schemazeichnungen parametrisch | **TEILWEISE** | wb Grundriss-Editor = parametrisches Alpine/SVG (Muster wiederverwendbar); HK-Schema selbst neu. |
| **B10** | Bild-Pipeline über OMD/DATANORM | **KONFLIKT** | Realität: **OMD/UGL fehlen in ticket**, DATANORM=Stub, DATANORM liefert keine Bilder; Bilder real nur via **ticket-IDS opportunistisch** (`product_images`). wb hat OMD (ETIM-Attribute, nicht zwingend Bilder). Konzept-Annahme trifft nicht zu — **A4**. |
| **B11** | accessories + valve_insert_compatibility | **TEILWEISE** | Zubehör: ticket `master_set_components.type='zubehoer'` **existiert** (starker Andock). Ventil-kv/kvs-Kompatibilität: **fehlt** (wb nur generisch DN15, keine Hersteller-Ventil-DB). |
| **B12** | Voreinstellstufen / hydr. Abgleich | **VORHANDEN/TEILWEISE** | wb `HydraulikService` Verfahren B + Voreinstellung (generisch); ticket `radiator_installations.supply_valve_presettable`. Fehlt: herstellerspez. kvs. |
| **B13** | supplier_article_map → IDS/OMD/UGL | **TEILWEISE/KONFLIKT** | Tabelle existiert nirgends; ticket De-facto `distributor_prices` (IDS produktiv). OMD/UGL nur in wb (Stub/real). **Zwei Supplier-Stacks → A4-Entscheidung.** Andock ticket: `resolveProduct`/`upsertDistributorPrice`. |
| **B14** | Stücklisten-Export / Angebotsübernahme | **VORHANDEN** (ticket) | `master_sets`/`master_set_components` + `deal_measurement_items` (voller Durchstich). Wiederverwenden, **nicht** neu bauen. |

**Bilanz:** VORHANDEN 4 (B1,B4,B7,B14) · TEILWEISE 5 (B2,B6,B9,B11,B12) · FEHLT 2 (B5,B8) · KONFLIKT 3 (B3,B10,B13).

---

## A3 — Architektur-Empfehlung (Schnitt + Aufwand)

**Grundprinzip (konsistent mit Reconciliation-Plan):** *tickets Katalog/Angebot/Stückliste = Wahrheit; wberechnungs
DB-freie Rechenkerne ziehen per Adapter ein.* Das Heizkörper-Modul ist überwiegend **Reconciliation + Adapter +
Aufsatz**, nicht Neubau.

**Was ins ticket gehört (dort ist der Bestand):**
- **Katalog (B1/B2):** `products`+`article_groups` + neue typisierte `product_radiator_specs` (EN-442-Felder additiv,
  `product_id`-FK — exakt das Spec-Muster aus `katalog-reconciliation-plan`). **Aufwand S–M.**
- **Bestandsaufnahme (B3):** **`radiator_installations` erweitern**, nicht neu bauen — sie hängt schon an der
  Kundenakte. Ergänzen: Link auf Katalog-Heizkörper + Rechen-Input (`heizkoerper_id/laenge_m/anzahl`), damit die
  Ist-Aufnahme direkt in den EN-442-Kern fließt. **Aufwand M.**
- **Zubehör/Stückliste/Export (B11-Zubehör/B14):** `master_set_components`(`type='zubehoer'`) + `deal_measurement_items`
  wiederverwenden. **Aufwand S.**
- **Empfehlungs-Engine (B8) + Anschlussfaktoren (B5):** neuer Aufsatz auf der fertigen Physik. **Aufwand M (B8) / S (B5).**
- **SVG-Schema (B9):** neu, Muster aus wb-Grundriss-Editor. **Aufwand M.**

**Was aus wberechnung transplantiert wird (die Physik):**
- **EN-442-Kern (B4/B7) + Hydraulik Verfahren B (B12):** `HeizkoerperService`/`HydraulikService` sind DB-frei →
  Transplant wie der WR-Kern, Geräte-/Katalogdaten über Adapter aus ticket. **Aufwand M** (inkl. Adapter).
- **Einrohr/Strang (B6):** Verfahren-B-Basis vorhanden, Strang-/Kaskadenmodell **fehlt** → echter Neubau. **Aufwand L.**

**Supplier/Bilder (B10/B13) — die eine große Weiche:** ticket-IDS (produktiv, Sonepar) ist der reale Beschaffungs-
und der einzige (opportunistische) Bild-Kanal; wberechnungs **OMD/ETIM (`artikel_merkmale`)** ist die
**Attribut-Schicht, die ticket fehlt**. Empfehlung: **ticket-IDS als Kanal behalten**, wberechnungs
**ETIM-/`artikel_merkmale`-Konzept als additive Attribut-Schicht** nach ticket portieren (für HK-Technikmerkmale +
Bilder), `supplier_article_map` auf dem `distributor_prices`-Andock neu. **Aufwand L** (Architektur-Entscheidung zuerst).

**Timing:** Katalog/Bestandsaufnahme/Stückliste können **jetzt in ticket** entstehen (Bestand ist da); der
**Rechenkern-Transplant** koppelt an den geplanten wberechnung→ticket-Cut-over (Migrations-Kurve). Kein Doppelbau.

**Aufwands-Summe:** S: B1,B5,B11/B14 · M: B2/B3,B4/B7/B12,B8,B9 · L: B6 (Strang), B10/B13 (Supplier-Architektur).

---

## A4 — Offene Entscheidungen für Yama

1. **Bestandsaufnahme-Konflikt (B3):** `radiator_installations` (ticket, physische Ist-Aufnahme an Kundenakte) **oder**
   `heizlast_raeume.heizkoerper`-JSON (wb, Rechen-Zuweisung) als Heimat? *Empfehlung: ticket `radiator_installations`
   erweitern (Link zu Katalog + Rechen-Input), wb-JSON wird daraus gespeist.*
2. **Supplier-Stack (B13):** Zwei konkurrierende Beschaffungsschichten — **ticket-IDS** (produktiv, Sonepar) vs.
   **wberechnung DATANORM+OMD/ETIM** (real/Stub). Welche ist die Ziel-Architektur? Das Konzept nennt „IDS/OMD/UGL teilweise
   angebunden" — real ist **nur IDS in ticket produktiv**, **OMD nur in wberechnung**, **UGL nirgends**.
3. **ETIM-/Attribut-Schicht:** ticket hat **keine** technische Merkmals-/ETIM-Struktur; wberechnung hat `artikel_merkmale`.
   Portieren (empfohlen, für HK-Specs + Bilder) oder neu?
4. **Katalog-Spec-Muster:** `product_radiator_specs` als **eigene typisierte Tabelle** (konsistent mit
   `katalog-reconciliation-plan` §6.2) — für Heizkörper bestätigen?
5. **Bild-Pipeline-Realität (B10):** Bilder kommen zuverlässig **nur über ticket-IDS (opportunistisch)** — OMD/DATANORM
   liefern keine. Reduzierten Bild-Scope akzeptieren oder in eine Hersteller-Bildquelle investieren?
6. **Bau-Ort/Timing:** Katalog/Bestandsaufnahme/Stückliste **jetzt in ticket** bauen, Rechenkern erst am wberechnung→ticket-
   Cut-over transplantieren — oder alles gebündelt am Cut-over? (Koordination mit `wberechnung-transplant-vorbereitung-landkarte`.)
7. **Einrohr/Strangkaskade (B6):** echter Neubau (L) — jetzt einplanen oder als spätere Welle (wie Plan-Import/Grundriss-Editor)?
8. **UI-Technologie:** Das fertige Konfigurator-/Ampel-/Szenario-Muster ist **Alpine.js** (wberechnung); ticket nutzt
   Alpine **nicht produktiv** (jQuery/Vuexy). **Alpine in ticket einführen** (Funktionsmuster 1:1 übernehmbar, schneller)
   **oder** die Konfigurator-UI in tickets jQuery/Vuexy-Stack **neu bauen** (design-konform, mehr Aufwand)? *Empfehlung:
   Alpine für die Rechen-Konfiguratoren einführen, restliche CRUD-Flächen im Vuexy-Bestand.*

> **Ende Phase A. Kein Implementierungsbeginn ohne Freigabe.**
