# Stränge & Zuständigkeiten (wberechnung → ticket)

Verbindliche Strang-Trennung, damit **kein Rechenkern doppelt gebaut** wird und Sperr-Dateien respektiert
werden. **Begriffsregel: Stufen IMMER mit Strang-Präfix benennen** — `Katalog-ii`, `Heizkörper-iii`, `M3`, `B1`
— **nie nacktes „Stufe (ii)"**. Stand: 2026-07-05.

**ORT-Regel (neu):** Jeder Strang trägt eine **Ort-Zeile** (Repo/Pfad · Branch · letzter bekannter Commit),
damit „gebaut vs. nicht gebaut / wo committet" nie wieder verwechselt wird — Lehre aus zwei Fehlannahmen:
`WberechnungImportSeeder` (als „gebaut" angenommen, fehlte) und **G-3b** (Feature auf keinem Branch dieses Repos, belegt durch `964a378`).

## Strang HEIZKÖRPER — parallele Instanz (NICHT anfassen)
- **Ort:** `ticket` = `/Users/yamanuri/Documents/ticket` · Branch `private/app-code-backup` · zuletzt `dd10a0e`.
- Roadmap **M0–M5** (Strang A): Fundament → Ventiltechnik → IDS-Mapper → **Rechenkern-Port (M3)** → **UI/Nav (M4)** → Prod-Migration (M5).
- Enthält: Rechenkern-Port (`app/Services/Heizkoerper/*`), **Mapper-Kette** (`SupplierArticleMapper`/`IdsMapper`/OMD-Datanorm-Stubs).
- Commits: `5f2bcd9`(i)·`80598a9`(ii-a)·`09eea5e`(ii-b)·`22e335d`(ii-c)·`1503854`(iii-a)·`f13f277`(iii-b)·`6bf75b0`/`947bed6`(iv)·`dd10a0e`(iv grün). M4 (HK-UI) uncommittet in Arbeit.

## Strang KATALOG-CUT-OVER — DIESE Instanz
- **Ort:** `ticket` · `private/app-code-backup` · Katalog-(i) `217473f`; Fox-ESS/LONGi `46b1986`; Cut-over-Analyse `8287add`; Energiekonzept-Archiv `1085c43`.
- `katalog-reconciliation-plan.md` Stufen **(i)–(iv)**: Schema additiv → Import → Adapter → Rechenkern.
- **Katalog-(i)** = `217473f` (4 Spec-Migrationen `150001–150004`, auf main migriert 2026-07-04).
- **Katalog-(ii) = Import-Seeder** — **noch NICHT gebaut** (blockiert durch WP-Fix-Gate). Scope: Netto-Neuwert (19 WP + AIKO + LONGi LR7), `imported_from='wberechnung'`, skip-Dedup.
- Cut-over-Analyse (Stufe 0+1) abgeschlossen: `docs/cutover-wb-module.md`, `docs/cutover-wb-inventur.md`.

## Strang WBERECHNUNG-APP / A-3d-Grundriss — /Herd/wberechnung (NICHT anfassen, read-only)
- **Ort:** `wberechnung` = `/Users/yamanuri/Herd/wberechnung` · zuletzt `eb11426` (Handoff Import-Track); A-3d: `3fef0dc`(OCR)·`fa837ef`(plan_uploads-Security).
- A-3d Raster/OCR, `MassstabVorschlagService`, `PlanBildVermessen` (Grundriss). Roadmap **B5**. Quell-App **read-only** — Ausnahme: der separat freigegebene **WP-Daten-Fix** (nur `database/data/waermepumpen*`).

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

## Geteilte Sperr-Dateien (Navi-Strang, uncommittet — NICHT überschreiben)
- `resources/views/admin/layouts/sidebar.blade.php`
- `app/Http/Controllers/EconomicCalculationController.php`

## Tabu-Zonen (nie anfassen)
- `/api/planner` + Sanctum-Setup
- **OMD-Namespace** `app/Services/Suppliers/Omd/*` (beim Katalog-Import nicht berühren)
