# Geräte-Spec-Standard — kanonisches Schema, Import/Export, Validierung (Design, Stufe 1)

> **Status:** read-only Design-Entwurf (Stufe 1). **Kein Bau vor Freigabe je Weiche.** Ziel (Yama):
> standardisierte Datenstruktur je Gerätetyp + generischer Import/Export, damit Gerätedaten künftig
> **massenhaft importiert** statt handgepflegt werden. **Architektur vor Daten.** Der laufende
> `WberechnungImportSeeder` bleibt unberührt (letzter seiner Art); der Import-Command dieses Standards
> ist sein Nachfolger. Stand 2026-07-05.

---

## 1. Ist-Schema kartiert (belegt)

Ein Gerät = **1 `products`-Zeile** (Identität + Handelsfelder) **+ 1 Spec-Detailzeile** je Typ. Alle
Spec-Felder `nullable`, Einheit im Feldnamen **und** im Migrations-Kommentar. Die Spec-Tabellen wurden
1:1 aus den wberechnung-Auslegungs-Contracts abgeleitet (Feldnamen = Interface).

### 1.1 `products` — Identität + Mapping (Kern, gilt für alle Typen)
`brand_id` · `product` · `model` · `category` · `article_group` (FK `article_groups`) · `heatpump_type` ·
`construction_type` · `refrigerant` · `phase_count` · `scop` · `noise_level_db` · `short_description` ·
`status` · **`imported_from`** (neu `150006`, Herkunfts-Marker). *(Dedup-Schlüssel: `brand_id` + `model`.)*

### 1.2 `product_heat_pump_specs` (WP) — Migration `150004`
| Feld | Einheit | Zweck |
|---|---|---|
| `heizleistung_{am7,a2,a7}_w35_kw` | kW | Heizleistung A-7/A2/A7 bei W35 |
| `heizleistung_am7_w55_kw` | kW | Heizleistung A-7 bei W55 (Vorlauf-Ableitung) |
| `cop_{am7,a2,a7}_w35`, `cop_am7_w55` | – | COP zu den Punkten |
| `scop_35`, `scop_55` | – | SCOP je Vorlaufebene |
| `modulation_{min,max}_kw` | kW | Modulationsbereich |
| `max_vorlauf_c`, `aussen_heizen_min_c` | °C | Einsatzgrenzen |
| `leistungskurve` | JSON | dichte Kennlinie **oder null** |
| **`kurve_semantik`** | enum | `en14511_nenn` \| `volllast_max` \| null (neu `150005`) |
| `geraetetyp`, `serie`, `kaeltemittel` | – | Match-Labels |

### 1.3 `product_pv_module_specs` (PV) — Migration `150003` (= SizingModule-Contract, `InverterSizingService:81-243`)
`pmpp_wp` [Wp] · `voc_v`/`vmpp_v` [V] · `isc_a`/`impp_a` [A] · `tk_{voc,isc,pmpp,vmpp}_pct_k` [%/K] ·
`u_sys_max_v` [V] · `sicherung_max_a` [A]. *(11 Felder, 1:1 Interface.)*

### 1.4 `inverters` (WR) — Bestand + `150001` (+18, SizingInverter-Contract)
Bestand: `dc_nominal_power`/`max_dc_power` [W] · `ac_nominal_power`/`max_ac_power` [W/VA] · `num_mpp_trackers` ·
`min_mpp_voltage`/`max_mpp_voltage` [V] · `max_input_current_per_mpp` [A] · `max_input_voltage` [V].
Neu (`150001`): `dc_operating_max_voltage`/`dc_startup_voltage` [V] · `max_dc_ac_ratio` [–] · `max_array_power_wp` [Wp] ·
`is_hybrid`/`eps_capable` [bool] · §14a-Block (`controllable_14a`, `vde4105_compliant`, `active_power_limit` …) ·
`operating_temp_{min,max}_c`/`temp_derating_from_c` [°C] · Batterie-Kopplung `battery_{min,max}_voltage` [V],
`battery_max_charge_power_w` **[W]**, `battery_max_current_a` [A].

### 1.5 `batteries` (Speicher) — Bestand + `150002` (+4, SizingBattery-Contract, `InverterSizingService:403-430`)
Bestand: `battery_type` · `nominal_voltage` [V] · `capacity_{10min…100h}` · `num_cells` · Maße.
Neu (`150002`): `min_voltage`/`max_voltage` [V] · `max_charge_power_kw` **[kW]** · `max_current_a` [A].

> ⚠️ **Einheiten-Falle (belegt `150002:20`):** Batterie `max_charge_power_kw` [**kW**] vs. WR
> `battery_max_charge_power_w` [**W**] — der Kern rechnet ×1000. **Die Einheit gehört zwingend in den
> Feldnamen** (Validierungsregel V3). Das ist die strukturelle Lehre neben der Buderus-COP-Lektion.

### 1.6 Heutige Leser (Spec-Konsumenten in ticket)
Nur die **Heizkörper-Kette**: `RadiatorCatalogAdapter:26` liest `q_norm_w_pro_m` → `q_norm_w`
(`RadiatorPerformanceService`). Die WP/PV/WR/Bat-Spec-Tabellen haben **noch keine ticket-Leser** — ihre
Auslegungs-Kerne sind Teil B (in wb). → Das **Mindestprofil** (Teil 3) leitet sich daher aus den
**wb-Kernen** ab, die die künftigen ticket-Leser werden (per grep belegt).

---

## 2. Kanonisches JSON-Format (versioniert, `spec_version:"1.0"`)

Vier Blöcke, gleich für jeden Gerätetyp: **identitaet** (Pflicht) · **fachdaten** (typ-spezifisch, Einheit
im Feldnamen) · **semantik** (nur wo nötig, z. B. WP) · **herkunft** (Pflicht, Prüf-/Auslegungssteuerung).

### 2.1 Wärmepumpe
```json
{
  "spec_version": "1.0",
  "geraetetyp": "waermepumpe",
  "identitaet": { "hersteller": "Buderus", "modell": "WLW-7 MB AR", "serie": "Logatherm WLW MB AR", "kategorie": "waermepumpe" },
  "fachdaten": {
    "heizleistung_am7_w35_kw": 6.71, "heizleistung_a2_w35_kw": 2.87, "heizleistung_a7_w35_kw": 2.84,
    "heizleistung_am7_w55_kw": null,
    "cop_am7_w35": 2.36, "cop_a2_w35": 4.06, "cop_a7_w35": 4.85, "cop_am7_w55": null,
    "scop_35": 4.58, "scop_55": 3.52, "modulation_min_kw": 1.3, "modulation_max_kw": 7.1,
    "max_vorlauf_c": 75, "aussen_heizen_min_c": -22,
    "bauart": "monoblock", "kaeltemittel": "R290 (Propan)", "phasen": null, "schall_volllast_db": 57.7
  },
  "semantik": { "kurve_semantik": "en14511_nenn", "leistungskurve": null },
  "herkunft": {
    "datenquelle": "hersteller_datenblatt", "imported_from": "wberechnung",
    "verifikations_status": "datenblatt_verifiziert", "verifikations_datum": "2026-07-05",
    "datenblatt_referenz": "Buderus 6721874368 (2024/04)", "quelle_url": null
  }
}
```

### 2.2 PV-Modul
```json
{
  "spec_version": "1.0", "geraetetyp": "pv_modul",
  "identitaet": { "hersteller": "AIKO", "modell": "AIKO-A480-MAH54Mw", "serie": "Neostar 2P", "kategorie": "pv_modul" },
  "fachdaten": {
    "pmpp_wp": 480, "voc_v": 41.3, "vmpp_v": 34.86, "isc_a": 14.38, "impp_a": 13.78,
    "tk_voc_pct_k": -0.22, "tk_isc_pct_k": 0.05, "tk_pmpp_pct_k": -0.26, "tk_vmpp_pct_k": null,
    "u_sys_max_v": 1500, "sicherung_max_a": 25
  },
  "herkunft": { "verifikations_status": "importiert_ungeprueft", "datenblatt_referenz": "AIKO Neostar 2P V4.1", "…": "…" }
}
```

### 2.3 Wechselrichter / 2.4 Batterie
Gleiche vier Blöcke; `fachdaten` = die inverters- bzw. batteries-Felder aus §1.4/§1.5 (Einheit im Namen).
WR trägt zusätzlich `semantik` nur, falls Kennfelder (Wirkungsgradkurve) mitkommen; sonst entfällt der Block.

**Einheiten-Konvention:** Einheit **immer** im Feldnamen (`_kw`, `_w`, `_v`, `_a`, `_c`, `_pct_k`, `_wp`).
JSON-Werte rein numerisch, kein Einheiten-String im Wert. Fehlender Wert = `null` (nie `0`, nie geraten).

---

## 3. Validierungsregeln (kodifiziertes Fachwissen — eine Quelle, zwei Verwender: Import + Prüfer)

| # | Regel | Konsequenz bei Verstoß |
|---|---|---|
| **V1** | kW **und** COP eines Betriebspunkts stammen aus **derselben Betriebsart** (kein Volllast-kW + Nenn-COP — Buderus-Lektion) | Ablehnung mit Befund |
| **V2** | Einheiten-Plausibilität: Heizleistung 1–50 kW (nicht W); Spannung 0–1500 V; COP 1–7 | Ablehnung |
| **V3** | Einheit steckt im Feldnamen; unbekanntes/einheitloses Feld | Ablehnung (nie stummes Mapping) |
| **V4** | `leistungskurve` darf `null` sein; **erfundene Zwischenpunkte verboten**. Kennfelddaten ⇒ `kurve_semantik` Pflicht | Ablehnung |
| **V5** | Identität vollständig (`hersteller`+`modell`+`kategorie`); Dedup-Schlüssel `brand_id`+`model` | Ablehnung |
| **V6** | Unbekannte Felder ⇒ **Ablehnung mit Fehlerliste**, nie stilles Verwerfen, nie Raten | Ablehnung |
| **V7** | `herkunft.verifikations_status` ∈ {`datenblatt_verifiziert`, `importiert_ungeprueft`} + `datenblatt_referenz` gesetzt | Ablehnung |

Regeln liegen als **eine** deklarative Definition je Gerätetyp (`SpecSchema::rules('waermepumpe')`), die
**Import (Teil 1)** und **Eignungs-Prüfer (Teil 3)** gemeinsam nutzen — nicht zwei Regelwerke.

---

## 4. Import-Architektur (Command + Service, kein UI)

```
php artisan spec:import <datei.json|.csv> [--typ=waermepumpe] [--commit] [--update]
```

**Ablauf (DB-frei bis `--commit`):**
1. **Laden** JSON (kanonisch) oder CSV (Zubringer, Header = Feldnamen → intern auf JSON gemappt).
2. **Validieren** je Zeile gegen `SpecSchema::rules($typ)` (Abschnitt 3).
3. **DRY-RUN-Report** (Default, ohne `--commit`): Tabelle *angelegt / geskippt (Dedup) / abgelehnt (mit Fehlerliste)* + Summen. **Kein Write.**
4. **Write** nur mit `--commit`: `products` + Spec-Detailzeile in einer Transaktion. **skip-if-exists** (Dedup `brand_id`+`model`) ist Default. **`--update`** nur explizit, **immer** mit **Feld-Diff-Report**. **Downgrade-Schutz:** ist die Bestandszeile `verifikations_status='datenblatt_verifiziert'`, überschreibt `--update` sie **nicht** still — nur mit zusätzlichem **`--allow-downgrade`**, das den Status auf `importiert_ungeprueft` setzt **und** den Downgrade loggt. Nie stummes Update, nie stiller Qualitätsverlust.
5. **Marker je Lauf:** `import_batch_id` (UUID) auf jeder geschriebenen Zeile ⇒ zeilengenauer Rückbau `spec:import:rollback <batch-id>`. (Ergänzt `imported_from` um die Lauf-Granularität.)

**Service-Schnitt:** `SpecImportService` (Parsing/Validierung/Dry-Run/Write, wiederverwendbar), `spec:import` = dünner Command-Wrapper. Idempotent (2× = 1×) durch Dedup.

---

## 5. Export (Gegenstück, Roundtrip-fähig)

```
php artisan spec:export --typ=waermepumpe [--hersteller=…] [--status=datenblatt_verifiziert] > out.json
```
Liest `products`+Spec-Detailzeile, schreibt **dasselbe kanonische JSON** wie der Import erwartet.
**Roundtrip = Testkriterium:** `export → import --dry-run` ergibt „alle geskippt, 0 abgelehnt" (semantisch identisch).

---

## 6. Zwei-Kanal-Realität (belegt) — kein Vermischen

| | **Handelskanal** | **Auslegungskanal** |
|---|---|---|
| Zweck | Preise, SKU, Bestellung | Rechnen/Auslegen |
| Quelle | OMD / IDS / DATANORM | Datenblatt → Spec-Import |
| Tabellen | `supplier_connections` (OMD Phase 1), künftig `supplier_article_map` | Spec-Tabellen (`product_*_specs`, `inverters`, `batteries`) |
| Schlüssel | `sku`/`article_no`/EAN | `brand_id`+`model` |

**Befund:** `supplier_article_map` **existiert in ticket noch nicht** (nur der OMD-Phase-1-Stack
`supplier_connections`/`request_config`). Beide Kanäle hängen über `products` zusammen (eine Produktzeile,
zwei Datenquellen), berühren aber **verschiedene Detailtabellen**. **Der Spec-Import fasst den
OMD-Namespace nicht an** (`app/Services/Suppliers/Omd/*` = Tabu). Verbindung zu OMD nur als Kontrakt-Skizze
(Teil 3.4): OMD kann ein Datenblatt-PDF liefern → Kandidat für den Übersetzungs-Workflow (Teil 2).

---

## 7. Migrations-Weichen (additiv, nullable — je einzeln freizugeben)

| Weiche | Tabelle | Feld | Zweck | Teil |
|---|---|---|---|---|
| **M-A** | **`products`** (zentral, entschieden A1) | `verifikations_status` (enum), `verifikations_datum` (date), `datenblatt_referenz` (string) | Herkunft/Prüfstand am Gerät (statt nur im Import-Log) | 1 |
| **M-B** | geschriebene Spec-Zeilen | `import_batch_id` (uuid, nullable) | Lauf-genauer Rückbau | 1 |
| **M-C** | `products` | `auslegungsstatus` (enum: `auslegungsfaehig`\|`teilweise`\|`nur_handelsdaten`) | Eignungs-Ergebnis am Gerät, bei jedem Spec-Write neu berechnet | 3 |

*(M-A/M-B/M-C laufen in dieser Stufe nur gegen `ticket_testing`; produktiv im M5-Deploy-Paket.)*

> **Entschieden (A1):** `verifikations_status`/`_datum`/`datenblatt_referenz` **an `products`** (ein Ort, gilt
> fürs Gerät). Annahme **„1 Produkt = 1 Spec-Satz"** — bei künftiger 1:n-Spec wandert der Status mit
> (dann M-A überdenken).

---

## 8. Bau-Reihenfolge (freigegeben — je Stufe eigener Pflicht-Stopp + Commit)

Alle Migrationen additiv/nullable, **nur `ticket_testing`**; main-Ausführung als Posten ins **M5-Deploy-Paket**
(Roadmap §6, Muster `44c77b4`).

1. **`SpecSchema`-Regelquelle (V1–V7) + `spec:import --dry-run`.** Tests: je Regel Verletzung → Ablehnung mit
   Fehlerliste · Dry-Run-Report korrekt (angelegt/geskippt/abgelehnt) · **kein Write**.
2. **Migrationen M-A + M-B (testing) + `--commit`-Pfad.** ✅ **umgesetzt** (`30fe611`-Nachfolger): 3 Migrationen
   (`150007` M-A · `150008` M-B · `150009` `spec_import_batches`), `spec:import --commit` (atomar, Herkunftskette),
   `spec:rollback {batch_id}` (**eigener Command**, gewählt für saubere Trennung Import↔Rollback). **Rollback-Weiche
   entschieden:** Update-Batches werden **abgelehnt** (überschriebene Vorwerte nicht wiederherstellbar) — erkannt
   über `spec_import_batches` (nur Lauf-Modus/Zählung, **keine** Zeilen-Vorwerte = keine Schatten-Historie).
   Tests: Herkunftskette · Idempotenz · skip-Dedup · `--update`-Felddiff · **Downgrade-Schutz** (`--allow-downgrade`
   → `importiert_ungeprueft` + Log) · Batch-Rückbau isoliert · Update-Batch-Ablehnung. 9 Tests grün.
3. **`SpecEligibilityService` + M-C + `spec:recheck`.** Tests: Mindestprofile je Typ (Code-belegte Felder) ·
   Status-Neuberechnung bei Spec-Write · `recheck` nach Regeländerung (persistierte Stati werden sonst stale) ·
   Fehlliste **on-the-fly** korrekt.
4. **`spec:export` + Roundtrip-Test** (export → import → identischer Stand) + **Kurzdoku für den Yama-Workflow**
   (Datenblatt → Template → JSON → `spec:import --dry-run` → `--commit`).
