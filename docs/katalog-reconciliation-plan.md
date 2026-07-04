# Katalog-Reconciliation-Plan (Pflicht-Stopp vor Rechenkern-Transplantation)

> **Reine Analyse (nur gelesen: ticket-MySQL-Schema, wberechnung-Code + SQLite-Dev-DB). Kein Code, kein Bau.**
> Vorbedingung aus Weiche 1+2 (`wberechnung-transplant-vorbereitung-landkarte.md`, Yama 2026-07-04):
> **KEIN Doppel-Katalog** — tickets Katalog (`products`/`article_groups` + `inverters`/`batteries` + …) ist die
> **einzige** Wahrheit; die Rechenkerne holen Gerätedaten künftig über eine **Adapter-Schicht** aus tickets Katalog.
> Dieser Plan ist der **Pflicht-Stopp**: Mapping-Tabellen + Fehlt-Liste + Adapter-Entwurf **vor** jedem Bau.
> Stand 2026-07-04. Alle Feld-Konsum-Angaben sind **aus dem Code belegt** (Datei:Zeile), nicht aus dem Schema geraten.

---

## 0. Kernbefund (ändert die Aufgabe)

**Die Reconciliation ist zu ~90 % SCHEMA-Arbeit (additive Felder), kaum DATEN-Arbeit** — belegt:

| ticket-Katalog | Zeilen | wberechnung (SQLite-Dev) | Zeilen |
|---|---|---|---|
| `inverters` | **0** | `inverters` | 3 |
| `batteries` | **0** | `batteries` | 2 |
| `heat_pumps` (dünn, lead-gebunden) | **0** | `waermepumpen` (Spec) | 19 |
| `radiators` (**fehlbenannt**, s. §1) | **0** | `heizkoerper` | 30 |
| *(kein PV-Modul-Spec-Table)* | — | `pv_modules` | 3 |
| `products` | 44 | `artikel` | 23 |
| `distributors` | 9 | `lieferanten` | **0** |

→ **tickets Gerätekataloge sind leer** ⇒ **kein Dedup-Konflikt bei Geräten**; wb-Daten (3/2/3/19/30) sind reine
**Import-Kandidaten**. Einzige Daten-Dedup-Fläche: `artikel`(23) ↔ `products`(44). `lieferanten` ist leer ⇒ nichts zu migrieren.

**Zwei ticket-Anomalien (vor dem Mapping zu klären):**
- **`ticket.radiators` trägt Wechselrichter-Spalten** (`dc_nennleistung_kw … max_mpp_spannung_v`) — es ist **kein**
  Heizkörper-Katalog, sondern eine fehlbenannte/verunglückte Inverter-artige Tabelle (0 Zeilen). ⇒ Für Heizkörper
  gibt es **kein** nutzbares ticket-Ziel → **additiv**.
- **`ticket.heat_pumps`** = `lead_id, alternative_id, product_id, type, cop, installation_cost, annual_costs` =
  **WP-Auswahl je Lead**, **kein Spec-Katalog**. ⇒ WP-Spec ist überwiegend **additiv**.

---

## 1. Feld-Mapping je Katalog (belegt konsumierte Felder → ticket-Status)

**Prinzip:** ticket muss **nur die Felder** additiv bekommen, die die **Rechenkerne wirklich lesen** (aus Code belegt).
Alles andere (Anzeige/CRUD von wb) ist irrelevant fürs Rechnen. Legende: **✓** ticket-Äquivalent vorhanden (umbenennen/mappen)
· **➕** fehlt in ticket → **additiv** (neue nullable Spalte / Spec-Struktur).

### 1a — Wechselrichter (wb `inverters`/`inverter_specs` → `ticket.inverters`, 45 Sp.)
Konsumiert vom **aktiven** Kern `Energie\InverterSizingService` (DB-frei) + `PerformanceService`/`StringBuilderService`/`PvProjektService`.

| Konsumiertes Feld (wb) | ticket-Äquivalent | Status |
|---|---|---|
| `u_dc_max_v` | `max_input_voltage` | ✓ rename |
| `u_mppt_min_v` | `min_mpp_voltage` | ✓ |
| `u_mppt_max_v` | `max_mpp_voltage` | ✓ |
| `anzahl_mppt` | `num_mpp_trackers` | ✓ |
| `i_dc_max_mppt_a` | `max_input_current_per_mpp` | ✓ |
| `i_sc_max_mppt_a` | `max_short_circuit_current_per_mpp` | ✓ |
| `i_dc_max_string_a` | `max_input_current` | ✓ (prüfen) |
| `p_ac_nenn_w` | `ac_nominal_power` | ✓ |
| `p_ac_max_va` | `max_ac_power` | ✓ |
| `p_dc_max_w` | `max_dc_power` | ✓ |
| `phasen` | `num_phases` | ✓ |
| `wirkungsgrad_euro_pct` | `efficiency_*` (Kurve) | ✓ ableitbar/rename |
| `u_dc_betrieb_max_v`, `u_dc_start_v`, `strings_pro_mppt`, `max_dc_ac_ratio`, `max_array_wp` | — | ➕ |
| `ist_hybrid`, `na_schutz_integriert`, `vde4105_konform`, `wirkleistungsbegrenzung`, `steuerbar_14a`, `schnittstelle`, `eps_faehig` | — | ➕ (Regulatorik/§14a/Hybrid/EPS) |
| `temp_betrieb_min_c`, `temp_betrieb_max_c`, `temp_derating_ab_c` | — | ➕ (Temperatur) |
| `u_bat_min_v`, `u_bat_max_v`, `p_bat_lade_max_w`, `i_bat_max_a` | — | ➕ (Batterie-Kopplung) |

**➕-Fehlt-Liste WR (additiv):** ~15 Felder — der gesamte **Hybrid-/Batterie-/EPS-/Regulatorik-/Temperatur-Block**.
Genau die, die tickets rein-PV-orientierte `inverters` nicht kennt.

### 1b — Batterie (wb `batteries`/`battery_specs` → `ticket.batteries`, 32 Sp.)
Der Kern liest **nur 4 Felder** (schmal!):

| Konsumiert (wb) | ticket-Äquivalent | Status |
|---|---|---|
| `u_min_v` | *(ticket hat `nominal_voltage`, aber kein min/max)* | ➕ |
| `u_max_v` | — | ➕ |
| `p_lade_max_kw` | — | ➕ |
| `i_max_a` | — | ➕ |

**➕ Batterie:** 4 additive Felder. (tickets 32 Batterie-Spalten sind zellchemisch/Entladekurven-detailliert, aber
**nicht** die 4 vom Kern gebrauchten Spannungs-/Lade-Grenzen.)

### 1c — PV-Modul (wb `pv_modules` → **kein ticket-Table**)
Konsumiert: `voc_v, vmpp_v, isc_a, impp_a, pmpp_wp, tk_voc_pct_k, tk_isc_pct_k, tk_pmpp_pct_k, tk_vmpp_pct_k, u_sys_max_v, sicherung_max_a` (11 Felder).
**Status: komplett ➕** — ticket hat keine PV-Modul-Spec-Tabelle (`products` trägt nur generische PV-Attribute).
→ neue Spec-Struktur `product_pv_module_specs` (analog `inverters`/`batteries`, via `product_id`).

### 1d — Wärmepumpe (wb `waermepumpen` → ticket hat nur dünnes `heat_pumps`)
Konsumiert vom WP-Kern (`WpKennlinieService`, `BivalenzService`, `MatchService`): `leistungskurve` (3-Ebenen-Array
W35/45/55!), `heizleistung_{am7,a2,a7}_w35_kw`, `heizleistung_am7_w55_kw`, `cop_{am7,a2,a7}_w35`, `cop_am7_w55`,
`max_vorlauf_c`, `aussen_heizen_min_c`, `scop_35`, `scop_55`, `modulation_min_kw`, `modulation_max_kw` (~16 Felder).
**Status: komplett ➕** — `ticket.heat_pumps` ist Auswahl je Lead, kein Spec-Katalog. `products` überlappt nur
marginal (`scop`↔`scop_35`, `refrigerant`↔`kaeltemittel`, `phase_count`↔`phasen`).
→ neue Spec-Struktur `product_heatpump_specs` (via `product_id`), inkl. **JSON-Feld `leistungskurve`**.

### 1e — Heizkörper (wb `heizkoerper` → `ticket.radiators` unbrauchbar)
Konsumiert vom **DB-freien** `HeizkoerperService`: **nur 3 Felder** — `q_norm_w_pro_m`, `exponent_n`, `norm_bedingung`.
**Status: ➕ (klein)** — neue Spec-Struktur `product_radiator_specs` (3 Felder + Label-Felder für Auswahl).
*`ticket.radiators` bleibt unberührt (referenziert, s. §6.1) — die neue Tabelle entsteht sauber daneben.*

### 1f — Artikel (wb `artikel` → `ticket.products`) — Daten-Mapping, kein Spec
`ticket.products` (article_no, sku, ean, brand_id, discount_group, heatpump_type, scop, …) deckt den Stammsatz ab;
`wb.artikel` bringt **DATANORM/ETIM-Felder** (`datanorm_artikelnr`, `etim_klasse`, `warengruppe`, `rabattgruppe`) —
ticket hat dafür `supplier_connections` (DATANORM-Prototyp, crm-inventur #2). ➕ ggf. `etim_klasse`/`datanorm_artikelnr`
additiv an `products`, sonst mappbar.

---

## 2. Daten-Abgleich (Dedup)

- **Geräte:** tickets `inverters`/`batteries`/`heat_pumps`/`radiators` = **0 Zeilen** ⇒ **kein Dedup**; wb-Geräte
  (3 WR, 2 Bat, 3 PV, 19 WP, 30 HK) sind reine **Import-Kandidaten** in die (erweiterten) ticket-Kataloge.
- **Artikel↔products:** Dedup-Schlüssel **`hersteller`+`modell`** (wb) vs **`brand`+`model`/`article_no`** (ticket).
  Stichprobe wb.artikel: AIKO/LONGi (Module), Fox ESS (WR) — echte Geräte mit Hersteller+Modell. ticket.products (44):
  Demo-Platzhalter („PV-Modul Fronius Standard", „…Komfort/Eco/Premium") = **generische Marketing-Zeilen ohne echtes Modell**.
  ⇒ **geringe/keine echte Überschneidung**; wb.artikel füllt tickets Katalog mit **realen** Geräten.
- **Lieferanten:** `wb.lieferanten` = 0 Zeilen ⇒ nichts zu migrieren; `ticket.distributors` (9) bleibt Wahrheit (§4).

---

## 3. Adapter-Schicht (der Kern bleibt unberührt & katalogtreu)

**Belegter Ausgangspunkt (aus Code):**
- Der **aktive** WR-Kern `Energie\InverterSizingService` ist **DB-frei** und typisiert seine Eingänge über
  **leere Marker-Interfaces** `App\Services\Energie\Contracts\SizingInverter` / `SizingModule` / `SizingBattery`
  (nur `@property`-Deklarationen), heute implementiert von `Inverter`/`InverterSpec` etc. **Das Laden** passiert im
  Controller (`Artikel::…->with('inverterSpec')`). **Kein Repository/Loader-Interface existiert.**
- `HeizkoerperService` ist **vollständig DB-frei** (reines Array `q_norm_w`/`exponent_n`/`norm_bedingung`).
- Der WP-Kern liest Eloquent-`Waermepumpe`-Attribute direkt (nicht DB-frei, aber ohne eigene Query).

**Entwurf — ein Loader-Interface je Gerätetyp, das aus TICKETS Katalog liest, Objekte liefert, die die bestehenden
Kern-Contracts erfüllen. Der Rechenkern wird NICHT angefasst.**

| Kern | Naht (existiert schon?) | Adapter |
|---|---|---|
| WR/PV/Batterie (DB-frei) | ✅ `Sizing{Inverter,Module,Battery}`-Interfaces | `CatalogDeviceRepository` lädt ticket-`products`+Spec, mappt auf DTOs, die `Sizing*` implementieren. **@property-Liste der Interfaces = exakt die Pflicht-Felder, die ticket liefern muss** (= §1a/1b/1c). |
| Heizkörper (DB-frei, Array) | ✅ Array-Signatur | Mapper baut das 3-Key-Array aus ticket-`product_radiator_specs`. |
| Wärmepumpe (Eloquent-Attr.) | ⚠️ kein Interface | **Neu:** Interface `WpKennlinie` mit den ~16 `@property` aus §1d (inkl. `leistungskurve:array`); ticket-DTO implementiert es; Kern von `Waermepumpe` auf das Interface umtypisieren (minimaler, mechanischer Eingriff). |

**Konsequenz:** Die konsumierten Feldlisten aus §1 sind **1:1 die Interface-Contracts**. Ticket erweitert seine Kataloge
**genau um die ➕-Felder** — nicht mehr. Der Adapter (Repository + DTO) ist die **einzige** neue Kopplung; die
Rechen-Engine bleibt Byte-genau.

---

## 4. Lieferanten

`wb.lieferanten` (name, kundennummer, email, telefon, `kanal_config`, aktiv) ist **leer (0 Zeilen)** und dünn.
Ziel = `ticket.distributors` (reich: Adresse, Zahlungsziel, Skonto, `distributor_product`/`distributor_prices` +
`supplier_connections`). ⇒ **Kein Import, kein Doppeln** — nur beim späteren Bau die Artikel-Lieferanten-Relation
(`wb.lieferanten_artikel`) auf `distributor_product` mappen, falls wb-Artikel mit Bezugsquelle kommen.

---

## 5. Stufenplan (jede Stufe umkehrbar + verifizierbar; Harness-tauglich)

**Reihenfolge ist Pflicht — Adapter/Kern erst, wenn der Katalog die Felder trägt.**

| Stufe | Inhalt | Umkehrbar? | Verifikation |
|---|---|---|---|
| **(i) Katalog additiv erweitern** | Migrationen (nullable) für die ➕-Felder: WR-Block an `inverters`; 4 Batterie-Felder; **neue** `product_pv_module_specs`, `product_heatpump_specs` (JSON `leistungskurve`), `product_radiator_specs`. **Nichts umbenennen/löschen** (nur ADD COLUMN / CREATE). | ✅ (drop nullable/table) | `migrate:fresh` gegen `ticket_testing` grün; bestehende ticket-Tests unverändert grün |
| **(ii) wb-Katalogdaten überführen** | Import-Seeder: 3 WR, 2 Bat, 3 PV, 19 WP, 30 HK, reale `artikel` → ticket-`products`+Spec. **Marker-Feld** (`imported_from='wberechnung'`) je Zeile für restlosen Rückbau. Artikel-Dedup per `hersteller+modell`. | ✅ (Delete-by-Marker, wie Test-Harness) | Zeilen-Soll je Tabelle; Stichprobe Rechenwert vs. wb-Original |
| **(iii) Adapter bauen** | `CatalogDeviceRepository` + DTOs (`Sizing*`), `WpKennlinie`-Interface + DTO, Heizkörper-Mapper. Loader liest ticket-Katalog. | ✅ (isolierte neue Klassen) | Unit: DTO erfüllt Interface; Repository liefert für importiertes Gerät identische Feldwerte |
| **(iv) Rechenkern-Transplantation** | Kerne + Controller nach ticket, Geräte-Laden auf `CatalogDeviceRepository` umstellen (statt `Artikel::…->with`). Engine-Logik unverändert. | ⚠️ (echter Code-Umzug) | **Die 271 Tests** gegen `ticket_testing` grün (= Phase-1.4-Abnahme); Ergebnis-Parität vs. wb-SQLite-Lauf je Kern |

**Regressionswächter:** Bis (iv) grün ist, bleibt der wberechnung-MySQL-Re-Check (`scripts/wberechnung-mysql-check.sh`)
die Referenz für „der Kern rechnet noch richtig".

---

## 6. Entscheidungen (Yama 2026-07-04 — alle 4 getroffen)

**(1) `ticket.radiators` — NICHT droppen, stehen lassen, neue Tabelle daneben.**
Referenz-Check (belegt, read-only) ergab: **referenziert, nicht tot** — `app/Models/Radiator.php`,
`ProductController.php:1392` (`DB::table('radiators')->where('product_id',…)`, Produkt-Detail-„Radiator"-Tab =
`radiator.config.view`), Views `admin/configurations/radiator/*` + `admin/product/.../radiator.blade.php`.
Damit greift der Fallback: **stehen lassen, gemeldet.** Der echte Heizkörper-Katalog entsteht **neu & sauber**
als `product_radiator_specs` **daneben**. Die Anomalie (Inverter-Spalten in „radiators", 0 Zeilen) ist ein
**separates ticket-Datenqualitäts-Thema**, NICHT Teil dieses Transplants (eigenes Ticket später).

**(2) Eigene typisierte Spec-Tabellen, KEIN EAV.** `product_pv_module_specs`, `product_heat_pump_specs`,
`product_radiator_specs` — je via `product_id`-FK, analog `inverters`/`batteries`. Begründung: die Rechenkern-
Interfaces (`Sizing*`, `WpKennlinie`) verlangen **typisierte** Felder; EAV wäre typenlos, query-teuer und würde den
Adapter-Contract aufweichen. Die 3-Ebenen-WP-**`leistungskurve` als JSON-Spalte** innerhalb `product_heat_pump_specs`
(strukturiert, kein EAV-Zoo).

**(3) Nur den aktiven WR-Kern transplantieren.** `Energie\InverterSizingService` (DB-frei) zieht um; der Legacy-Kern
`InverterSuggestionService` (fragt selbst Eloquent) **fällt weg** — in der Funktions-Landkarte als *„bewusst nicht
transplantiert, abgelöst durch `Energie\InverterSizingService`"* dokumentiert (kein stiller Verlust).

**(4) Rename-Liste in Stufe (i) als belegter Migrations-Anhang.** Regel: **wo ein ticket-Feldname existiert, gewinnt
der ticket-Name** (Katalog-Wahrheit ist ticket); wb-Namen werden **gemappt, nicht übernommen** (z. B. wb `u_mppt_max_v`
→ ticket `max_mpp_voltage`). Nur die ➕-Felder (kein ticket-Äquivalent) kommen mit einem neuen, ticket-konformen Namen dazu.

> **Status: Reconciliation-Plan vollständig entscheidungsreif.** Stufenplan (i)–(iv) bestätigt. Gebaut wird am Cut-over.
