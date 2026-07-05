# Stränge & Zuständigkeiten (wberechnung → ticket)

Verbindliche Strang-Trennung, damit **kein Rechenkern doppelt gebaut** wird und Sperr-Dateien respektiert
werden. **Begriffsregel: Stufen IMMER mit Strang-Präfix benennen** — `Katalog-ii`, `Heizkörper-iii`, `M3`, `B1`
— **nie nacktes „Stufe (ii)"**. Stand: 2026-07-05.

**ORT-Regel (neu):** Jeder Strang trägt eine **Ort-Zeile** (Repo/Pfad · Branch · letzter bekannter Commit),
damit „gebaut vs. nicht gebaut / wo committet" nie wieder verwechselt wird — Lehre aus zwei Fehlannahmen:
`WberechnungImportSeeder` (als „gebaut" angenommen, fehlte) und **G-3b** (Feature auf keinem Branch dieses Repos, belegt durch `964a378`).

## Strang HEIZKÖRPER — parallele Instanz (NICHT anfassen)
- **Ort:** `ticket` = `/Users/yamanuri/Documents/ticket` · Branch `private/app-code-backup` · zuletzt `89e175f`.
- Roadmap **M0–M5** (Strang A): Fundament → Ventiltechnik → IDS-Mapper → **Rechenkern-Port (M3)** → **UI/Nav (M4)** → Prod-Migration (M5).
- Enthält: Rechenkern-Port (`app/Services/Heizkoerper/*`), **Mapper-Kette** (`SupplierArticleMapper`/`IdsMapper`/OMD-Datanorm-Stubs).
- Commits: `5f2bcd9`(i)·`80598a9`(ii-a)·`09eea5e`(ii-b)·`22e335d`(ii-c)·`1503854`(iii-a)·`f13f277`(iii-b)·`6bf75b0`/`947bed6`(iv)·`dd10a0e`(iv grün)·`af8c465`/`89e175f`(M4-a v). **M4-a committet** (Stufe v komplett: Stückliste + Übernahme-Endpunkt), **26 HK-Tests grün** (Abschluss-Bilanz Teil A). *[Korrektur 2026-07-05: früher „M4 uncommittet" — jetzt committet, live belegt.]* Prod-Migration (M5) offen: `product_radiator_specs` nur auf `ticket_testing`, nicht main.

## Strang KATALOG-CUT-OVER — DIESE Instanz
- **Ort:** `ticket` · `private/app-code-backup` · Katalog-(i) `217473f`; Fox-ESS/LONGi `46b1986`; Cut-over-Analyse `8287add`; Energiekonzept-Archiv `1085c43`; **Abschluss-Bilanz** `cutover-wb-abschlussbilanz.md`.
- `katalog-reconciliation-plan.md` Stufen **(i)–(iv)**: Schema additiv → Import → Adapter → Rechenkern.
- **Katalog-(i)** = `217473f` (4 Spec-Migrationen `150001–150004`, auf main migriert 2026-07-04).
- **Katalog-(ii) = Import-Seeder** — **noch NICHT gebaut**; Gate: **WP-Fix erledigt** (`b4a9eda`, wb) → Abnahme läuft, danach bauen. Scope: Netto-Neuwert (19 WP + AIKO + LONGi LR7), `imported_from='wberechnung'`, skip-Dedup, nur `ticket_testing`.
- Cut-over-Analyse (Stufe 0+1) + **Abschluss-Bilanz** (Gewissheits-Audit: **301/301 klassifiziert, Teil D leer**, 🟡 11 A / 204 B): `docs/cutover-wb-module.md`, `cutover-wb-inventur.md`, `cutover-wb-abschlussbilanz.md`.
- **Fortschreibungs-Regel (verbindlich):** Bei jedem B-Slot-Abschluss wandert die Bilanz-Zeile **B→A** — im selben Commit wie der Slot.

## Strang WBERECHNUNG-APP / A-3d-Grundriss — /Herd/wberechnung (NICHT anfassen, read-only)
- **Ort:** `wberechnung` = `/Users/yamanuri/Herd/wberechnung` · Branch `main` · zuletzt `b4a9eda` (**WP-Daten-Fix**: Buderus EN-14511-Nenn, dichte LKs → Spalten-Fallback, `kurve_semantik`; **306 Tests grün**) · davor `eb11426`; A-3d: `3fef0dc`(OCR)·`fa837ef`(plan_uploads-Security).
- A-3d Raster/OCR, `MassstabVorschlagService`, `PlanBildVermessen` (Grundriss). Roadmap **B5**. Quell-App **read-only** — der WP-Daten-Fix ist **erledigt** (`b4a9eda`), das Schreibfenster ist **wieder zu** (strikt read-only, auch `waermepumpen*`).

## Strang G (CRM-Konversion & Belege) — eigene Instanz (NICHT anfassen)
- **Ort: UNBEKANNT / NICHT `private/app-code-backup`** — belegt durch `964a378` (kein Konversions-Feature in irgendeinem Branch dieses Repos). **Yama klärt den Arbeitsbereich der G-Instanz und trägt ihn nach.**
- **G-1…G-9**: CRM-Anfrage-Konversion, Rechnungs-/Beleg-PDF, **Schiene A** (PDF), **GAEB**, **Schiene B** (XRechnung). RBAC `crm.*`, Route-Prefix `/app`.
- **Tabu für andere Stränge:** Accounting, `finanz_safety`, Migrationen, Lohn/HR.
- Merke: `plan_uploads`/A-3d gehört **nicht** hierher (das ist der wberechnung-Grundriss-Strang, Modul K).

## Strang NAV (Navigation) — parallele Instanz (NICHT anfassen)
- **Ort:** `ticket` · `private/app-code-backup` · zuletzt ~`fa29f89` (Flächen 2/3 Tab-Leisten: `ca649b5`/`5680a71`).
- ⚠️ `sidebar.blade.php` + `EconomicCalculationController.php` teils **uncommittet** → Sperr-Dateien (unten).

## Strang OMD (Supplier-Connector) — DIESE Instanz
- **Ort:** `ticket` · `private/app-code-backup` · OMD Phase 1 `35a2904`. Mapper-Kette (`1503854`/`f13f277`) gehört zum Heizkörper-/Import-Strang.
- Phasen-gated; beim Katalog-Import nicht berühren (Tabu-Zone unten).

## Strang S1 (Rechnungsschiene-Härtung, Kanzlei-Übergabe) — eigene Instanz (NICHT anfassen)
- **Ort:** `ticket` (führend) · Branch/Commits **noch nicht belegt** (Planungsstand 2026-07-02) — *bei Umsetzung Ort-Zeile nachtragen (git-Beleg wie bei den anderen Strängen).*
- Basis: **Invoice-Modul** (`app/Models/Invoice*`, `Http/Controllers/Invoice/*`, Routes `/invoices` + `/invoices/canvas`). Planungsdok `docs/uebernahme/sprint-1-tickets-rechnungsschiene.md`.
- **S1-01…S1-11**: Nummernkreis → Löschsperre → Editiersperre-ab-sent → Storno/Gutschrift → Teilzahlung (`invoice_payments`) → payment_status → … → Legacy-Cleanup (S1-10) → Regressionssuite (S1-11). **A1 = Option 1** (Kanzlei führt FiBu; **keine** Buchhaltung im ticket).
- **Abgrenzung:** S1 = bestehende **Rechnungs-Schiene** (`/invoices`) härten. Strang **G** = CRM-Konversion + **Beleg-PDF/GAEB/XRechnung** (`/app`, `crm.*`). „Belege"-Überschneidung mit Yama klären (evtl. G ⊃ S1 oder getrennt).
- **Tabu für Cut-over/Katalog/Heizkörper:** Invoice-/Accounting-Dateien, `finanz_safety`, Rechnungs-Migrationen.

## Geteilte Sperr-Dateien (Navi-Strang, uncommittet — NICHT überschreiben)
- `resources/views/admin/layouts/sidebar.blade.php`
- `app/Http/Controllers/EconomicCalculationController.php`

## Tabu-Zonen (nie anfassen)
- `/api/planner` + Sanctum-Setup
- **OMD-Namespace** `app/Services/Suppliers/Omd/*` (beim Katalog-Import nicht berühren)
