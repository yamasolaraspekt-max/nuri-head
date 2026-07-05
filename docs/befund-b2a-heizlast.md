# B2a-Befund: Heizlast-Kern (DIN EN 12831) + Klima + Referenz-Kataloge

> **Status:** read-only Befund (Stufe 1, nach M3-Port-Muster). **Kein Bau vor Freigabe.** wb strikt read-only.
> **wb-Port-Stand:** `b4a9eda` (main, Referenz-Hash für den Port). Stand 2026-07-05.

## 0. Kern-Befund
Der Heizlast-Track ist **portierbar wie M3** — die Rechenkerne sind zum großen Teil **DB-frei** (pure Services
mit Konstanten). DB-Zugriff nur an drei Adapter-Punkten: **Material-λ**, **Baualtersklasse-U**, **Heizkörper-
Katalog** (letzterer ist in ticket bereits als `product_radiator_specs` da). Die **28 wb-Tests** liefern
konkrete Paritäts-Anker. Die eigentliche Neu-Arbeit: **3 Referenz-Kataloge** (Schema+Import) + **Klima-Quelle**
+ das **Anforderungsprofil-Datenobjekt** (Phase 1 des universellen Frameworks).

---

## 1. Quell-Inventar wb (Datei:Zeile-belegt, Hash `b4a9eda`)

### 1.1 Heizlast-Rechenkern — DB-frei portierbar (wie M3)
| Service | Öffentl. Methode | DB? | Norm/Formel |
|---|---|---|---|
| `HeizlastRechner` | `berechne(array):array` (`:28`) | **pure** | Φ_T=Σ fx·A·(U+ΔU_WB)·Δθ · Φ_V=0,34·n·V·Δθ · Φ_RH (`:6-17,80-108`) |
| `HeizlastNormwerte` | `thetaInt/fx/luftwechsel/deltaUwb/plausi` | **pure** | Konstanten (THETA_INT, FX, LUFT_RHO_CP=0,34, WAERMEBRUECKEN) |
| `RaumHuelleService` | `effektiveBauteile/huellbilanz` (`:21,51`) | **pure** | Außenwand netto (Öffnungen ab) |
| `HoehenkorrekturService` | `korrigiere(:39)` | **pure** | Δθ=−0,01·Δh ab 200 m (DIN/TS 12831-1) |
| `WarmwasserService` | `qWwKwh/phiWwKw/speicherLiter` (`:27-42`) | **pure** | Personen×Konstanten |
| `HeizlastKonstanten` | `qBase/jaz/normAussentemp/vorlauf` | **pure** | Q_BASE, JAZ, DIN-Norm-Außentemp je PLZ-Ziffer |
| `HeizlastEingabe` | `fromArray` (`:60`) | DTO | immutable |

### 1.2 Adapter-pflichtig (DB-Zugriff)
| Service | Methode | liest aus DB |
|---|---|---|
| `UWertService` | `ausSchichten(:58)` Strategie B | `Material::where('name')` → `lambda_w_mk` (`:71-73`) |
| | `ausBaualter(:107)` Strategie C | `Baualtersklasse` → `u_{wand,dach,…}` (`:114,195`) |
| | `ausKonstruktion(:160)` | `Konstruktion->schichten`, `Material::find` (`:163-166`) |
| | `bekannt/fenster/tuer/fensterDirekt` | **pure** (Strategie A + Richtwerte) |
| `HeizlastProjektService` | `berechne(:35)`/`fuerProjekt(:293)` | `Heizkoerper`-Katalog (`:125,198`), `HeizlastProjekt`-Model (`:295`) |

### 1.3 Klima (3 Quellen)
| Service | Methode | Quelle |
|---|---|---|
| `KlimaPlzService` | `lookup(plz):array` (`:20`) | **Datei** `database/data/klima_plz.csv` (DWD offline) → `nat_c`, `hoehe_m` |
| `KlimaBinService` | `profil(plz):array` (`:58`) | **pure** synthetische Gauß-Bins (DIN-Zonen θ_e −10…−14°C) |
| `OpenMeteoKlimaService` | `profil(plz,lat,lon)` (`:35`) | **API** Open-Meteo (DWD/ERA5 2022–24) + Cache + Fallback auf KlimaBin |

### 1.4 Tests = Paritäts-Basis (28 Methoden)
| Testklasse | n | Anker (byte-genaue Ziele des Ports) |
|---|---|---|
| `HeizlastRechnerTest` | 5 | H_T=**17,26** W/K · H_V=**10,63** · Φ_HL≈**892/948** W · Wand-netto=24 m² |
| `UWertServiceTest` | 6 | U_ziegel≈**1,45** · U_gedämmt≈**0,28** · U_saniert≈**0,24** · U_3fach=0,8 W/m²K |
| `AuslegungServiceTest` | 7 | Φ_HL≈**7,8–12,4** kW · JAZ=2,9 · w_el≈6069 kWh · Sperrzeit-f≈1,09 |
| `KlimaPlzServiceTest` | 4 | PLZ 60311 NAT<0°C · 01067 Lat≈51,07 |
| `KlimaBinServiceTest` | 4 | Zone=sued θ_e=**−14**°C · Σh=**8760** · Winter-Bins winterlastig |
| `OpenMeteoKlimaServiceTest` | 4 | Bins aus Fake-API · Fallback synthetisch (Http::fake-Muster) |

---

## 2. Referenz-Kataloge — Schema + Ziel-Entwurf + W-C4

### 2.1 Gelesene Felder (nur diese sind auslegungswirksam)
- **`materials`** → `lambda_w_mk` [W/mK], `name` (`UWertService:71-74`). *(kategorie/rohdichte/quelle ungelesen — Metadaten.)*
- **`baualtersklassen`** → `u_wand/u_dach/u_boden/u_fenster/u_tuer` [W/m²K], `von/bis_jahr`, `sanierungsstufe`, `quelle` (`UWertService:114-122`).
- **`konstruktionen`** → `schichten[]` (`material_id`,`dicke_mm`,`lambda_override`), `typ` (`UWertService:163-177`).
- **`batterie_wr_kompatibilitaet`** → **gehört NICHT zum Heizlast-Kern** (PV/WR-Sizing-Slot, `artikel↔artikel`).

### 2.2 Ziel-Schema-Entwurf ticket (additiv, ticket-Konventionen, Einheit im Kommentar)
```php
// materials — DIN 4108-4 / ISO 10456
name (string, unique) · kategorie (string) · lambda_w_mk (decimal 6,4)  // [W/mK]
· rohdichte_kg_m3 (int, null) · quelle (string)
// konstruktionen — DIN EN ISO 6946
name · typ (string) · schichten (json)  // [{material_id, dicke_mm, lambda_override}]
· u_wert_berechnet (decimal 6,3, null)  // [W/m²K] gecacht · ist_vorlage (bool)
// baualtersklassen — IWU/TABULA
von_jahr · bis_jahr (smallint) · sanierungsstufe (string)
· u_wand/u_dach/u_boden/u_fenster (decimal 5,3) · u_tuer (null)  // [W/m²K] · quelle
```
1:1 vom wb-Schema (byte-genaue λ-/U-Werte). FK `konstruktionen.schichten[].material_id` → `materials.id`.

### 2.3 Herkunfts-Auflage (W-C4)
- **`materials` + `konstruktionen`:** DIN-belegt (4108-4 / ISO 10456 / 6946) → **direkt übernehmbar**, Stichprobe je Kategorie gegen die Norm-Tabelle.
- **`baualtersklassen`:** Quelle „**IWU/TABULA (Richtwerte, to_verify)**" — **W-C4 greift primär hier**: je Epoche 1 Stichprobe (U_wand/U_fenster) gegen die IWU-TABULA-DE-Typologie, Ergebnis in den Import-Bericht. Nicht als „datenblatt_verifiziert" markieren (bleibt Richtwert, `importiert_ungeprueft`).

---

## 3. Anforderungsprofil-Anschluss (Phase 1 des universellen Frameworks)

`HeizlastProjektService::berechne()` liefert **genau die Phase-1-Größen** (`konzept-auslegung-universal.md`):
- `gebaeude.auslegungsheizlast_kw` → **Φ_HL** (Anforderungsprofil-Kernwert, Quelle=`berechnet`).
- `vorlauf_anforderungen` → **Vorlauf** (koppelt an HK-Modul, §4).
- `WarmwasserService::phiWwKw/qWwKwh` → **WW-Bedarf**.
- Sperrzeit-Faktor (`AuslegungService`) → **Sperrzeiten**.

**Schnittstellen-Skizze (Port füllt das Framework, nicht daneben):**
```
HeizlastProjektService::berechne($eingabe)
   → Anforderungsprofil { phi_hl_kw (quelle: berechnet), vorlauf_c (quelle: HK-Kopplung),
                          ww_kw, sperrzeit_h, klima_nat_c (quelle: KlimaPlz), version, projekt_id }
```
Das **Anforderungsprofil IST das Projekt-Datenmodell** (versioniert) — **nicht** doppelt neben `heizlast_projekte`
neu bauen (Weiche W-B2a-3). Datenlage-Ausweis je Feld (gemessen/berechnet/geschätzt) beginnt hier.

---

## 4. Abgrenzung (keine Doppelbauten)

| Track | Berührung | Schnittstelle (nur benennen, nicht bauen) |
|---|---|---|
| **HK-Modul (live)** | `HeizlastProjektService::emitterAbgleich` nutzt `Heizkoerper`-Katalog (EN 442) **+ Φ_HL** | Φ_HL(Raum) → `CompatibilityService`/`RadiatorPerformanceService` (Vorlauf-Kopplung). Katalog = **`product_radiator_specs`** (schon da, `q_norm_w_pro_m`/`exponent_n`/`norm_bedingung`) — **kein Neu-Katalog** |
| **A-3d/Grundriss (B5)** | liefert Räume/Flächen | `raum_geometrien` → `HeizlastRechner.raeume[].bauteile` — **nur Schnittstelle**, A-3d bleibt B5 |
| **PVGIS/PV-WR (B2a-parallel)** | eigener Slot | `batterie_wr_kompatibilitaet` gehört **hierhin**, nicht zum Heizlast-Kern — nicht vermischen |

---

## 5. Plan — Unterstufen + Weichen

| Stufe | Inhalt | Abhängig |
|---|---|---|
| **B2a-1** Referenz-Schema + Import | 3 additive Tabellen (`materials`/`konstruktionen`/`baualtersklassen`) + Import 23/5/25 Zeilen aus wb `b4a9eda` + W-C4-Stichproben | — |
| **B2a-2** Klima | `KlimaPlzService` (`klima_plz.csv` kopieren) + `KlimaBinService` (pure Port) + optional `OpenMeteoKlimaService` (Http::fake) | — (parallel zu B2a-1) |
| **B2a-3** Heizlast-Kern-Port + Parität | pure Kerne byte-genau (Rechner/Normwerte/RaumHülle/Höhe/WW/Konstanten) + `UWertService` (Adapter Material/Baualter) + `HeizlastProjektService` (Adapter Heizkörper + Anforderungsprofil) · **28 Paritäts-Tests** als Basis | B2a-1 + B2a-2 |

### Weichen (Pflicht-Stopp)
- **W-B2a-1 — Import-Weg der Referenz-Kataloge:** eigener `ReferenzKatalogSeeder` (Rechengrundlagen, kein Geräte-Spec) **vs.** `spec:import` erweitern. *Empfehlung: eigener Seeder — die Kataloge sind keine Geräte, das Spec-Schema/Prüfer passt nicht.*
- **W-B2a-2 — Anforderungsprofil vs. `heizlast_projekte`:** das Projekt-Datenmodell **als Anforderungsprofil** (Framework Phase 1, versioniert) bauen, **nicht** das wb-`heizlast_projekte`-Schema 1:1 kopieren. *Empfehlung: Anforderungsprofil führt; `heizlast_projekte`-Felder gehen darin auf.*
- **W-B2a-3 — Klima-Quelle:** offline (`KlimaPlz`+`Bin`) als Basis; **OpenMeteo-API optional/später**. *Empfehlung: offline zuerst (kein API-Lock-in), OpenMeteo als B2a-2-Zusatz.*
- **W-B2a-4 — `baualtersklassen`-Verifikation:** Umfang der W-C4-Stichproben (je Epoche 1 vs. vollständig). *Empfehlung: je Epoche 1 gegen TABULA-DE, Rest `importiert_ungeprueft`.*
- **W-B2a-5 — Reihenfolge:** B2a-1 + B2a-2 parallel (keine Abhängigkeit), B2a-3 danach. Bestätigen?

⛔ **PFLICHT-STOPP.** Grenzen gewahrt: `/Herd/wberechnung` read-only · nur `docs/` geschrieben · kein Bau/Migration/Commit außer diesem Befund · M5-Fenster nicht berührt · Strang-Tabus (HK/A-3d/PVGIS/OMD/SEC-DM/S1) unberührt.
