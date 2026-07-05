# Auslegungs-Eignungsprüfung — Mindestprofile, Status, Prüfer, OMD-Kontrakt (Design, Stufe 1)

> **Status:** read-only Design (Stufe 1). Die Mindestprofile sind **Code-Wahrheit**, kein Wunschzettel —
> abgeleitet aus dem, was die Auslegungs-Rechenkerne WIRKLICH lesen (grep-belegt, Datei:Zeile). Feldnamen =
> ticket-Schema; in Klammern der wb-Kern-Beleg (die künftigen ticket-Leser nach B2a-Port). Stand 2026-07-05.

---

## 1. Auslegungs-Mindestprofil je Gerätetyp (Code-belegt)

**„auslegungsfähig"** = alle Pflichtfelder vorhanden + konsistent. **„teilweise"** = Kern rechnet mit
Fallback/Derating, aber ein Genauigkeits-Feld fehlt (sichtbarer Hinweis). **„nur_handelsdaten"** = Pflicht
unvollständig → erscheint nicht in Auslegungs-Auswahlen.

### 1.1 Wärmepumpe (`WpKennlinieService`, `BivalenzService`)
| Feld (ticket) | Pflicht | Beleg (wb) | Kriterium |
|---|---|---|---|
| `heizleistung_am7_w35_kw` + `heizleistung_a2_w35_kw` + `heizleistung_a7_w35_kw` | **Pflicht¹** | `WpKennlinieService:121-123` | 3-Punkt-Leistungskurve W35 (Fallback-Stützpunkte) |
| `cop_am7_w35` + `cop_a2_w35` + `cop_a7_w35` | **Pflicht¹** | `:137-139` | 3-Punkt-COP W35 |
| `aussen_heizen_min_c` | **Pflicht** | `:30` | Einsatzgrenze (unterhalb → 0 kW) |
| `kurve_semantik` | **Pflicht bei Spalten** | (Import V4) | Semantik der Leistungsdaten |
| `heizleistung_am7_w55_kw` + `cop_am7_w55` | *teilweise* | `:148-161` | Vorlauf-Korrektur W55; fehlt → COP-Derating-Fallback |
| `leistungskurve` (dicht) | *Alternative zu ¹* | `:76-97` | ersetzt die 3-Punkt-Interpolation, falls belegt |

¹ **Entweder** die 6 W35-Stützpunkte **oder** eine belegte dichte `leistungskurve`. **V1 gilt:** kW+COP je
Punkt aus derselben Betriebsart. — *`JazService` liest KEIN WP-Modell direkt (nur DB-Richtwerte); nicht Teil des Profils.*

### 1.2 PV-Modul (`InverterSizingService`, `StringBuilderService`)
| Feld | Pflicht | Beleg | Kriterium |
|---|---|---|---|
| `voc_v`, `vmpp_v` | **Pflicht** | `:83, :86` | Spannungsfenster (Voc kalt / Vmpp heiß-kalt) |
| `isc_a`, `impp_a` | **Pflicht** | `:91, :195` | Stromregeln (Σ Impp ≤ MPPT-Limit; 1,25×Isc) |
| `pmpp_wp` | **Pflicht** | `:243` | DC-Leistung (Überdimensionierung) |
| `tk_voc_pct_k` | **Pflicht** | `:83` | Voc(T) kalt — sicherheitskritisch (Überspannung) |
| `u_sys_max_v` | **Pflicht** | `:122, :139` | Modul-Systemspannungsgrenze |
| `tk_isc/pmpp/vmpp_pct_k` | *teilweise* | `:92-104` | Fallback `tk_isc=0.05`, `tk_vmpp` aus β−α ableitbar |
| `sicherung_max_a` | *teilweise* | `:229-236` | Strangsicherung (Regel 6) |

### 1.3 Wechselrichter (`InverterSizingService`)
| Feld | Pflicht | Beleg | Kriterium |
|---|---|---|---|
| `max_input_voltage` (`u_dc_max_v`) | **Pflicht** | `:120,124` | Spannungsobergrenze DC |
| `min_mpp_voltage` / `max_mpp_voltage` | **Pflicht** | `:128,131` | MPP-Fenster |
| `dc_startup_voltage` | **Pflicht** | `:129,164` | Startspannung |
| `num_mpp_trackers` | **Pflicht** | StringBuilder`:52` | String-Verteilung |
| `max_input_current_per_mpp` | **Pflicht** | `:202` | Stromregel je MPPT |
| `ac_nominal_power`, `max_ac_power`, `max_dc_power` | **Pflicht** | `:249,331,258` | Über­dimensionierung + Schieflast |
| `num_phases` | **Pflicht** | `:334` | Schieflast-Check (1-ph ≤ 4600 VA) |
| `integrated_grid_protection`, `vde4105_compliant` | **Pflicht** | `:350,359` | Netzregeln |
| `max_dc_ac_ratio` / `max_array_power_wp` | *teilweise* | `:251-254` | Ratio-Cap (sonst Default) |
| `is_hybrid` + `battery_*` + `eps_capable` | *nur Hybrid* | `:408-423` | Batterie-Kopplung |
| `operating_temp_*`, §14a-Block, `wirkungsgrad_euro_pct` | *teilweise* | `:386-491` | Temp-Derating, Einspeisemgmt, Ranking |

### 1.4 Batterie — **schmales Profil, ehrlicher Befund**
| Feld | Pflicht | Beleg | Kriterium |
|---|---|---|---|
| `min_voltage`, `max_voltage` | *nur WR-Kompat.* | `:411-414` | Spannung im WR-Batteriefenster |
| `max_charge_power_kw` | *nur WR-Kompat.* | `:416` | ≤ WR-Ladeleistung (×1000, Einheiten-Falle §V3) |
| `max_current_a` | *nur WR-Kompat.* | `:419` | ≤ WR-Batteriestrom |

> ⚠️ **Befund:** Die Sizing-Kerne lesen von der Batterie **nur die WR-Kompatibilität** — **Kapazität (kWh),
> Chemie, Lade-/Entladeleistung, Zyklen und Temperaturfenster werden NICHT gelesen.** Es gibt also (noch)
> **keine echte Batterie-Auslegung** (Autarkie/Zyklen). Das Batterie-Mindestprofil deckt heute nur „passt an
> den WR". Echte Batterie-Auslegung ist ein **B3/B4-Konzeptpunkt** (Teil 4 §4), kein Datenproblem.

---

## 2. Status-Feld am Gerät (Weiche M-C)

`products.auslegungsstatus` (enum, nullable): `auslegungsfaehig` | `teilweise` | `nur_handelsdaten`.
**Automatische Neuberechnung bei jedem Spec-Write** (Import-Commit, künftig manuelle Pflege) via Prüfer (§3).
Additive Migration **M-C** (freigegeben A3, nur testing → M5). **Fehlliste on-the-fly** im Prüfer berechnet
(**nicht** persistiert — entschieden A3). Zusätzlich **`spec:recheck`** (Baustufe 3): rechnet alle
`auslegungsstatus` gegen die **aktuelle** Regelquelle neu — Pflicht, weil Regeländerungen persistierte Stati
stale machen.

---

## 3. `SpecEligibilityService` — EIN Prüfpfad für ALLE Kanäle

```php
SpecEligibilityService::pruefe(string $typ, array $fachdaten): EligibilityResult
// -> status: auslegungsfaehig|teilweise|nur_handelsdaten
//    fehlfelder: string[]   (welche Pflicht/Teilweise-Felder fehlen)
//    konsistenz: string[]   (V1/V2-Befunde, z.B. "kW/COP-Betriebsart gemischt")
```

**Dieselbe Regelquelle wie der Import** (`SpecSchema::rules($typ)`, 00-spec-standard §3) — eine
Regeldefinition, zwei Verwender (Import validiert beim Schreiben; Prüfer bewertet den Bestand). Nicht zwei
Regelwerke. Der Prüfer läuft (a) im Import-Commit, (b) als Backfill-Command `spec:eligibility:recheck`,
(c) beim künftigen OMD-Abruf (§4).

---

## 4. OMD-Kontrakt-Skizze (KEIN Bau — OMD-Strang ist parallel, Namespace Tabu)

Beim künftigen OMD-Abruf einer Gerätekategorie wird der Prüfer angestoßen. **Ehrliche Erwartung:**

| OMD-Datenpaket | deckt Auslegungs-Mindestprofil? |
|---|---|
| basic / prices / logistics / pictures / descriptions | **nein** (Handelsdaten) |
| documents (Datenblatt-PDF) | **indirekt** — PDF ist Quelle, nicht Struktur |

→ OMD-Geräte landen praktisch immer auf **`nur_handelsdaten`**. **Hinweis-Mechanik:** Ist im
`documents`-Paket ein Datenblatt-PDF → **Kandidat für den Übersetzungs-Workflow (Teil 2)**: Flag
`hat_datenblatt_pdf=true` + Verweis, dass das PDF durch eine Claude-Code-Session zu schema-konformem JSON
wird. **Kontrakt nur als Skizze**, keine `Omd`-Namespace-Berührung, kein Bau.

---

## 5. Konsequenz für die Auslegungs-Kerne (B2a)

- **`auslegungsfaehig`** → volle Auslegung.
- **`teilweise`** → Auslegung mit **sichtbarem Fehlfeld-Hinweis** (z. B. „W55 fehlt → COP-Derating geschätzt").
- **`nur_handelsdaten`** → **erscheint nicht** in Auslegungs-Auswahlen, bleibt aber in Angebots-/Bestell-
  Kontexten (Handelskanal) voll nutzbar.

Das koppelt den Katalog sauber an die Rechenkerne: **kein Rechnen auf Lücken**, aber **kein Verlust** von
Handelsartikeln. Der Datenlage-Ausweis (verifiziert vs. importiert_ungeprueft) wandert von hier in die
Bewertungs-Schleife des universellen Auslegungs-Konzepts (Teil 4 §3).
