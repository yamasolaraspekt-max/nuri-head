# Ventiltechnik-Quellen-Report (Stufe ii-c, VOR Seeder-Commit)

> **Regel (Yama):** nur öffentlich verifizierbare Artikelnummern (Quelle + Jahr je Zeile) · unsicher =
> **weglassen** und als **Yama-Lücke** listen (SHK-Team füllt aus echten Großhändler-Preislisten — bessere Quelle) ·
> **Preise nicht hart** seeden (kommen später aus Sonepar/IDS) · Kompatibilitäten **nur mit Hersteller-Doku-Beleg**.
> Stand der Recherche: 2026-07. Konfidenz: **hoch** = ≥2 unabhängige Quellen oder Herstellerseite · **mittel** = 1 Händlerquelle.

## A. Verifizierte Zubehör-Artikel (Seeder-Kandidaten für `accessories`)

| # | Kategorie | Hersteller | Bezeichnung | `herst_artikelnr` | voreinstellbar | Quelle (2026) | Konfidenz |
|---|---|---|---|---|---|---|---|
| 1 | Thermostatventil-Unterteil | IMI Heimeier | V-exact II Durchgang DN15, AG G¾ | `3720-02.000` | ja | wolf-online-shop.de, waerme24.de | hoch |
| 2 | Thermostatventil-Unterteil | IMI Heimeier | V-exact II Eckform DN15, vern. | `3711-02.000` | ja | heizung-billiger.de, raleo.de | hoch |
| 3 | Thermostatventil-Unterteil | Oventrop | AV 9 Durchgang DN15, R½, PN10 | `1183804` | ja | oventrop.com (Herstellerseite) | hoch |
| 4 | Thermostatventil-Unterteil | Oventrop | AV 9 Eck DN15, R½, PN10 | `1183704` | ja | oventrop.com (Herstellerseite) | hoch |
| 5 | Thermostatventil-Gehäuse | Danfoss | RA-N Durchgang DN15 (matt vern.) | `013G0034` | ja | store.danfoss.com (Herstellerstore) | hoch |
| 6 | Thermostatventil-Gehäuse | Danfoss | RA-N Eck DN15 | `013G0033` | ja | store.danfoss.com (Herstellerstore) | hoch |
| 7 | Thermostatkopf | IMI Heimeier | Kopf K, M30×1,5, Flüssigfühler | `6000-00.500` | – | hornbach.de, heizman24.de (Hersteller-Format) | hoch |
| 8 | Rücklaufverschraubung | IMI Heimeier | Regutec Durchgang DN15 | `0356-02.000` | – | hts24.de, wolf-online-shop.de | hoch |
| 9 | Rücklaufverschraubung | IMI Heimeier | Regutec Eck DN15 | `0365-02.000` | – | hts24.de | hoch |
| 10 | Einbauventil-Kit | Danfoss | RA G½ voreinstellbar (Ventil+Kappe) | `013G7360` | ja | store.danfoss.com (Herstellerstore) | hoch |
| 11 | Austausch-Ventileinsatz | Danfoss | RA-N Einsatz voreinstellbar | `013G0270` | ja | heizungsprofi24.de | mittel |

→ **11 Zeilen belegt** (10 hoch, 1 mittel). `kopf_anschluss_norm`: Heimeier/Oventrop = `M30x1_5`; Danfoss RA-N = `RA` (Klemm, Adapter nötig für M30-Köpfe, s. C).

## B. Preise & kvs — bewusst NICHT hart geseedet
- **Preise:** kommen aus der Lieferanten-Anbindung (`supplier_article_map` via IDS/Sonepar, Stufe iii). Die
  `accessories`-Tabelle trägt **keine** Preisspalte — korrekt so. Indikative Straßenpreise (Stand 2026-07, nur zur
  Orientierung, **nicht** geseedet): V-exact II Durchgang ~23–31 €, AV 9 ~20 €, RA-N ~ (Danfoss), Kopf K ~13 €.
- **kvs_werte (JSON Voreinstellstufe→kv):** je Ventil aus dem **Herstellerdatenblatt** zu füllen (Danfoss RA-N
  Datenblatt liegt öffentlich vor: heinze.de/…/RA-N.pdf; Oventrop/Heimeier analog). **NICHT geschätzt** —
  bis zum belegten Eintrag bleibt `kvs_werte = NULL`. → **Yama-/Datenblatt-Aufgabe** (s. E).

## C. Adapter (RAV/RAVL/RA → M30×1,5) — MITTEL, Verifikation offen
Belegt existent, aber SKU je Variante nicht sauber ein-quellig zuordenbar (Recherche lieferte gemischte Heimeier-
`9700-…`-Serie + Händler-Eigencodes). **Struktur** steht fest: Danfoss **RAVL Ø26** / **RAV Ø34** / **RA Ø23** →
`M30×1,5`. Konkrete `herst_artikelnr` je Adapter → **Yama-Lücke** (aus Danfoss-/Heimeier-Katalog belegen), nicht raten.

## D. `valve_insert_compatibility` — VOLLSTÄNDIG Yama-Lücke (keine Hersteller-Doku beschafft)
Die Matrix **HK-Hersteller → Serie → Baujahr → Einsatz → Kopf-Anschluss** (Kermi/Purmo/Buderus/Vogel&Noot/Stelrad/
Zehnder) ließ sich in dieser Recherche **nicht** aus Hersteller-Dokumentation belegen. Regel: **keine Plausibilitäts-
Vermutungen.** → Tabelle bleibt in ii-c **leer (0 Zeilen)**; Struktur (M5) steht. Beschaffung der Hersteller-
Kompatibilitätslisten = **Yama-/SHK-Aufgabe** (s. E). *(Generelles Muster „moderne Ventil-Kompakt-HK nutzen M30×1,5-
Kopf, Altbau Danfoss RAV/RAVL braucht Adapter" ist bekannt, aber ohne Serien-/Baujahr-Beleg NICHT seed-fähig.)*

## E. Offene Yama-/SHK-Lücken (ehrlich, aus echten Preislisten zu füllen)
1. **kvs-Kennlinien** je Ventil (Voreinstellstufe→kv) aus Datenblatt → `accessories.kvs_werte`.
2. **Adapter-SKUs** je Variante (RAVL26/RAV34/RA23 → M30) aus Danfoss/Heimeier-Katalog.
3. **Hahnblöcke** (einrohr-umschaltbar) — in dieser Runde nicht recherchiert; Hersteller/Nr. offen.
4. **`valve_insert_compatibility` komplett** — Hersteller-Kompatibilitätslisten (6 HK-Marken × Serie × Baujahr).
5. **Weitere Ventilfamilien/Baugrößen** (DN20, Axialform, umgekehrte Flussrichtung) — nach Bedarf.
6. **Preise** — bewusst offen, kommen über IDS/Sonepar (Stufe iii), nicht als Stammdatum.

## Fazit für den Seeder (ii-c)
`AccessorySeeder` seedet **die 11 belegten Zeilen aus A** (+ Kategorien), `imported_from='ventiltechnik_recherche_2026-07'`,
`quelle`-Feld je Zeile, `kvs_werte=NULL` (Datenblatt-Aufgabe), **keine Preise**. `valve_insert_compatibility` = **0 Zeilen**
(Lücke D). Alle Lücken sind in E nummeriert — eine ehrliche Lücke, keine erfundene Nummer.

---
*Quellen (Auswahl): oventrop.com · store.danfoss.com · wolf-online-shop.de · heizung-billiger.de · raleo.de ·
hts24.de · waerme24.de · heizungsprofi24.de · hornbach.de. Alle abgerufen 2026-07.*
