# wberechnung → ticket — Schnelltransfer-Plan

> Read-only Planungs-Dokument (Stand 2026-07-05, Worktree `strang/C`). Grundlage: `cutover-wb-abschlussbilanz.md`
> (16/215 übernommen, 199 offen), `cutover-wb-inventur.md`, `cutover-wb-module.md`, Rest-Inventur B2b.
> **Keine Umsetzung ohne Freigabe.** *(Hinweis: die genannten Dokus `playground-uebertragung-bestandsaufnahme.md`
> und `BETRIEBSORDNUNG.md` existieren in diesem Baum nicht — falls sie auf einem anderen Branch liegen, vor Umsetzung abgleichen.)*

## 1. Kurzurteil

**Bereits übertragen (byte-genau, mit Paritäts-Tests):**
- **Heizlast-Kern** (DIN EN 12831): `HeizlastRechner` + `RaumHuelleService` + `HeizlastNormwerte` (B2a-3, `d8d3870`).
- **Klima/WW**: `KlimaPlzService` + `HoehenkorrekturService` + `WarmwasserService` + `KlimaBinService` + `HeizlastKonstanten` + `HeizlastEingabe` (B2b-C, `19c5f10`).
- **U-Wert**: `UWertService` + Models `Baualtersklasse`/`Material`/`Konstruktion`/`FensterSpec` + `KonstruktionTyp` (B2b-A, Commit ausstehend).
- **Referenzdaten**: `materials`/`konstruktionen`/`baualtersklassen`/`klima_plz` (B2a-1, `9b48521`).
- **Framework**: Anforderungsprofil (versioniert, polymorph, Registry) + 3 Adapter (Heizlast/Klima/UWert) (B2a-2).
- **Geräte-Katalog**: WP/PV-Import + Fox-ESS/LONGi (in ticket `products`/`product_*_specs`, Marker `imported_from`).

**Was fehlt (die 199 offenen, fachlich wertvoll):** WP-Auslegung (`AuslegungService`+8 Deps, `WpKennlinieService`, `BivalenzService`, `JazService`), PV/WR-Sizing (`InverterSizingService`, `StringBuilderService`, `PerformanceService`), Wirtschaftlichkeit (`SanierungsWirtschaftlichkeitService`, `Energie/*` PVGIS), Grundriss (`GeometrieAbleitungService` + 2D-Editor), PDF/OCR/DXF (`import-service`, Python — bleibt extern).

**Schnellster Nutzen:** Welle 1 ist zu ~80 % fertig → **B2b-B (Auslegung) schließt die 28 Paritäts-Anker** und macht die WP-Dimensionierung rechenbar. Danach Welle 2 (Kennlinie/Bivalenz/JAZ) liefert das erste vollständige WP-Ergebnis über ein Anforderungsprofil.

## 2. Priorisierte Transfer-Wellen

| Welle | Inhalt | Ziel | Prinzip |
|---|---|---|---|
| **1 (fast fertig)** | Heizlast-Rest / Klima / Referenzdaten | fachliche Grundlage WP/PV | Service-first, Tests-first, keine UI |
| **2** | WP: `WpKennlinieService`, `BivalenzService`, `JazService`, `AuslegungService` | Adapter auf ticket-Katalog + Objekt/Gewerk (`lead_alternative_adds`/`lead_product_lists`) | Paritäts-Tests gegen wb |
| **3** | PV/WR-Sizing + Batterie-Kompatibilität | ticket `products`/`product_pv_module_specs`/`product_heat_pump_specs` — **keine zweite Artikelwelt** | Adapter liest ticket-Katalog |
| **4** | PVGIS + Wirtschaftlichkeit (`Energie/*`, `SanierungsWirtschaftlichkeitService`) | vorhandene ticket-Stubs prüfen/ersetzen (`EconomicCalculation*`) | keine doppelte Navigation |
| **5** | Energiekonzept-Bundler, Grundriss/Plan-Import, UI | erst nach stabilen Kernen; UI im ticket-Design | 2D-Editor neu bauen, `import-service` extern |

## 3. Pro Modul

| Modul | Quelle (wb) | Ziel (ticket) | Daten/Migr./Seeder | Adapter | Tests | Risiken | Nicht übernehmen |
|---|---|---|---|---|---|---|---|
| **B2b-B Auslegung** | `AuslegungService` (314) + 8 Deps + `HeizlastEingabe` | `app/Services/Heizlast/*` + Adapter | keine | phi_hl_kw→DTO→Ergebnis; Rückschr. jaz/w_el/bivalenz | 7 Anker (Φ_HL 7,8–12,4 · JAZ 2,9 · w_el 6069 · Sperrzeit 1,09) | **`GeocodingService` = online?** → offline-Klärung/Stub; großer Dep-Baum | Online-Geocoding |
| **WpKennlinie** | `WpKennlinieService` (216) | Heizlast-Adapter → `product_heat_pump_specs` | keine (Katalog da) | liest WP-Specs | Kennlinien-Parität | kurve_semantik-Feld | — |
| **PV/WR-Sizing** | `InverterSizing`(355)/`StringBuilder`(138)/`Performance`(81)/`Suggestion`(69) | Adapter → `products`/`product_pv_module_specs` | keine | phasen int\|string-Cast (Pflichtnotiz); max_ac=VA; kW×1000 | Sizing-Parität | **keine 2. Artikelwelt** | wb-`artikel`-Welt |
| **Batterie-Kompat.** | wb-Kompat-Logik | `batterie_wr_kompatibilitaet` (n:m products) | ggf. Tabelle | Adapter | Kompat-Test | — | — |
| **Wirtschaftlichkeit** | `SanierungsWirtschaftlichkeitService` (315), `Energie/*` (1013) | ticket `EconomicCalculation*` prüfen | evtl. Migr. | Adapter | Parität | Kollision mit Alt-Stub (`df1dc8c`) | doppelte Navi |
| **Grundriss** | `GeometrieAbleitungService` (185) + `resources/js/grundriss` (257) | 2D-Editor **neu im ticket-Design** | — | Geometrie→`gebaeude_geometrie` | Ableitungs-Test | Alpine nur HK-Regel (CLAUDE.md) | wb-Views |
| **PDF/OCR/DXF** | `import-service` (Python/FastAPI, ezdxf/tesseract) | **extern** (Serve-Route) | — | ticket ruft Microservice | — | externe Kette (tesseract) | PHP-Port |

## 4. Parallelisierungsmodell (max. 3 Agenten, kein Datei-Overlap)

- **Agent A — Rechenkerne + Paritäts-Tests:** `app/Services/Heizlast/*` (byte-Ports Auslegung/Kennlinie/Bivalenz/JAZ) + `app/Services/Anforderungsprofil/*Adapter` + `tests/Feature/Heizlast/*`. **Eigener Worktree `strang/C`** (dieser).
- **Agent B — Kataloge/Seeder/Migrationen:** `database/migrations/17xxxx` + `database/seeders/*` + `docs/deploy/RELEASE-MANIFEST.md` (Batterie-Kompat, evtl. Wirtschaftlichkeits-Tabellen). **Eigener Worktree `strang/B-katalog`.**
- **Agent C — ticket-Adapter/UI/Navigation:** PV/WR-Sizing-Adapter (liest `products`), spätere Views im ticket-Design, Navi-Konsolidierung. **Eigener Worktree `strang/C-ui`.**
- **Regel:** je Agent eigener Worktree (eigener Index → keine Absorption, s. STRAENGE §Arbeitsbaum-Trennung). `RELEASE-MANIFEST.md` = einziger geteilter Berührungspunkt, additiv.

## 5. Harte Regeln (verbindlich)

1. **Keine wberechnung-UI** übernehmen — Views/Optik immer neu im ticket-Design.
2. **Keine wberechnung-Auth/Navigation** — ticket ist führend; keine doppelten Navi-Begriffe.
3. **Keine Live-Daten** aus wb — nur Referenzdaten/Kataloge mit `imported_from`-Marker (rückbaubar).
4. **Keine zweite Produkt-/Artikelwelt** — alles über ticket `products`/`product_*_specs`; wb-`artikel` wird **nicht** portiert (Adapter/Mapping).
5. **Tests grün vor UI.** Jede Portierung: **Herkunfts-Kommentar** (`wb@<hash>`) + **Paritäts-Test** (byte-genauer Zahlenwert).
6. **Byte-genau + Diff=0** für Rechenkerne; Adapter übersetzt, Kern rechnet. Kern 0 Zeilen berührt.
7. **wb read-only.** Worktree-Trennung. Manifest-Pflicht je prod-Posten.

## 6. Entscheidungsliste für Yama

1. **Welche Welle sofort?** Empfehlung: **B2b-B (Auslegung)** zu Ende (schließt 28 Anker), dann Welle 2.
2. **wberechnung für neue Features einfrieren?** Empfehlung: **ja** — wb ist Referenz/Wahrheit bis der jeweilige Kern portiert ist; keine neuen wb-Features (sonst driftet die Parität).
3. **Welche Module parallel?** A (Kerne) + B (Kataloge) + C (Sizing-Adapter) — je eigener Worktree. B2b-B zuerst solo (großer Dep-Baum), dann Fächerung.
4. **Führende Begriffe in ticket:** „Anforderungsprofil" (Bedarf), „Objekt" = `lead_alternative_adds`, „Gewerk" = `lead_product_lists`, „Katalog" = ticket `products` (EIN Katalog), „Umsatz" = `invoices`. Klärungsbedarf: „Auslegung" vs. „Dimensionierung", „Energiekonzept" vs. bestehende Navi.

---
**STOPP — keine Umsetzung.** Nächster offener Bau-Schritt (nach Freigabe): B2b-B eigener Pflicht-Stopp 1 (Geocoding-/Dep-Klärung).
