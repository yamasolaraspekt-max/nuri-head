# Stränge & Zuständigkeiten (wberechnung → ticket)

Verbindliche Strang-Trennung, damit **kein Rechenkern doppelt gebaut** wird und Sperr-Dateien respektiert
werden. **Begriffsregel: Stufen IMMER mit Strang-Präfix benennen** — `Katalog-ii`, `Heizkörper-iii`, `M3`, `B1`
— **nie nacktes „Stufe (ii)"**. Stand: 2026-07-04.

## Strang HEIZKÖRPER — parallele Instanz (NICHT anfassen)
- Roadmap **M0–M5** (Strang A): Fundament → Ventiltechnik → IDS-Mapper → **Rechenkern-Port (M3)** → **UI/Nav (M4)** → Prod-Migration (M5).
- Enthält: Rechenkern-Port (`RadiatorPerformanceService`/`HydraulicService` via Adapter), **Mapper-Kette** (`SupplierArticleMapper`/`IdsMapper`/OMD-Datanorm-Stubs).
- Commits: `5f2bcd9` (i, 8 Migrationen) · `80598a9` (ii-a Models) · `09eea5e` (ii-b EN-442) · `22e335d` (ii-c Ventiltechnik) · `1503854` (iii-a Mapper) · `f13f277` (iii-b Live-Hook).

## Strang KATALOG-CUT-OVER — DIESE Instanz
- `katalog-reconciliation-plan.md` Stufen **(i)–(iv)**: Schema additiv → Import → Adapter → Rechenkern.
- **Katalog-(i)** = Commit `217473f` (4 Spec-Migrationen `150001–150004`, **auf main migriert** 2026-07-04).
- **Jetzt: Katalog-(ii) = Import-Seeder** (wb-Bestand → `products`+Spec, `imported_from='wberechnung'`-Marker, Dedup `hersteller+modell`).
- Roadmap-Einordnung **B1**, **Cut-over-gebunden** (siehe Gate-Prüfung).
- Bereits gebaut (dieser Instanz, Real-Geräte, nicht wb-Import): Fox-ESS/LONGi-Seeder (`46b1986`) + OMD Phase 1 (`35a2904`).

## Strang WBERECHNUNG-APP — /Herd/wberechnung (NICHT anfassen, read-only)
- A-3d Raster/OCR, `MassstabVorschlagService`, `PlanBildVermessen` (Grundriss-Vermessung/Kalibrierung).
- Roadmap **B5** „eigene Welle, optional später". Quell-App bleibt **read-only** (auch beim Katalog-Import).

## Geteilte Sperr-Dateien (Navi-Strang, uncommittet — NICHT überschreiben)
- `resources/views/admin/layouts/sidebar.blade.php`
- `app/Http/Controllers/EconomicCalculationController.php`

## Tabu-Zonen (nie anfassen)
- `/api/planner` + Sanctum-Setup
- **OMD-Namespace** `app/Services/Suppliers/Omd/*` (eigener OMD-Phasen-Strang dieser Instanz; beim Katalog-Import nicht berühren)
