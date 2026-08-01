# Ticket-Komponenten-Inventar (Startpunkte)

Zweck: reale Startpunkte für die Reuse-Suche. **Verifiziert** = in dieser Sitzung im laufenden
Repo (`/Users/yamanuri/Documents/ticket`) gesehen. **Zu verifizieren** = plausibel vorhanden,
aber noch nicht inventarisiert — vor Nutzung per Skill (Phase 2) real suchen. Keine erfundenen Pfade.

## Designsystem / CI  (VERIFIZIERT)
- Living Styleguide: `resources/views/admin/styleguide/index.blade.php` (Route `styleguide.index`, nur Admin).
- CI-Tokens (CSS-Variablen): `--sa-accent #93c21c`, `--sa-accent-hover #7baa18`,
  `--sa-accent-light #f4fae7`, `--sa-accent-ink #ffffff`, `--sa-danger #ef4444`,
  `--sa-warning #f59e0b`, `--sa-success #10b981`, `--sa-info #6b7280`. Font: **Inter**.
- Seitenkopf-Komponente: `<x-page-head title="" sub="" current="">` mit `<x-slot:actions>`
  (verwendet z. B. in `resources/views/admin/objekte/akte.blade.php`).
- Button-/Pill-Klassen: `gk-btn-soft` (Akte), Styleguide `sg-btn-primary|soft|danger`,
  `sg-pill-*`, `sg-chip`, `sg-input` (grüner Fokus-Ring), `sg-count`.
- Navigation/Sidebar: `resources/views/admin/layouts/sidebar.blade.php`
  (Sektionen mit `etage`, Items mit `label/icon(lucide)/url/active_routes`;
  Sektion „Planung & 3D" = Etage „Tools").
- Icons: lucide (`<i data-lucide="...">`), gerendert per createIcons.

## Bearbeitungssperre / gleichzeitiges Editieren  (VERIFIZIERT)
- Eine Mechanik für alle Editoren: `resources/views/admin/layouts/partials/bearbeitungs-sperre.blade.php`
  (`@include('admin.layouts.partials.bearbeitungs-sperre', ['bereich'=>'...', 'sperrId'=>$id])`).
  Heartbeat 30 s, Banner + Events `sperre:locked|frei`, Freigabe per sendBeacon.
  Routen: `system.sperre.ping`, `system.sperre.leave`. Sperre gilt JE DOKUMENT.

## Rechte / Sicherheit / Org-Bindung  (VERIFIZIERT)
- `app/Models/User.php`: `hasPermission(string $item, string $action='read')`,
  `isSuperAdmin()` (Spalte `is_admin` → Bypass), Relation `user_rolls`.
- Middleware `permission:Item,action` (zweiwertig, z. B. `permission:Hausplaner,read|update`).
- Rechte-Registry (grantbare Items): `permissionModules()` in
  `app/Http/Controllers/User/UserRollController.php`.
- Kanonisches Objekt: Model `LeadAlternativeAdd` (Anker `alternative_id`), Route-Model-Binding
  `{objekt}` → 404 bei unbekannt.

## Dokumente / Uploads  (VERIFIZIERT, planner-nah)
- Plan-Upload (Bild/PDF): `app/Http/Controllers/Energie/PlanUploadController.php`
  (index/store/destroy/bild), Model `app/Models/PlanUpload.php`; Routen `energie.plan-upload*`.
- `app/Models/BuildingModelVersion.php`.

## Vorhandener Planner (Hausplaner) — Wiederverwendungsbasis  (VERIFIZIERT)
- Controller: `app/Http/Controllers/Hausplaner/HausplanerController.php`
  (seite/speichern/snapshotErstellen/snapshotListe/wiederherstellen/katalog).
- Domain-Models: `app/Domain/Hausplaner/Models/HausplanerDocument.php`, `HausplanerSnapshot.php`.
- Actions: `app/Domain/Hausplaner/Actions/ErstelleLeeresSzenenDokument.php`,
  `SpeichereHausplanerDokument.php` (Revision + Checksum + `lockForUpdate`, 409-Konfliktpfad),
  `StelleSnapshotWieder.php`.
- Migration: `database/migrations/2026_07_16_211128_create_hausplaner_foundation_tables.php`
  (`hausplaner_documents`, `hausplaner_snapshots`, `hausplaner_catalog_items`).
- Insel-Bundle (TS-Quelle): `resources/planner/hausplaner/` — 2D (Konva) + 3D (three) Renderer,
  Zustand-Store, Zod-Validierung, Commands, Geometrie, Dach; Tests unter `__tests__/*.test.ts`
  (node strip-types Runner: `test:hausplaner`).
- Views: `resources/views/admin/hausplaner/objekt.blade.php` (persistierend, am Objekt),
  `studio.blade.php` (Scratch, Tools-Navi).
  *KORREKTUR 01.08.2026 (Befund PB-036): hier standen `dachplaner.blade.php` und `planer/planer.js`
  als W1-Insel. **Beide gibt es nicht mehr** — `find resources -name 'dachplaner.blade.php' -o -name
  'planer/planer.js'` liefert null Treffer. Sie stammen aus dem Zustand VOR dem Port in
  `resources/planner/hausplaner/`. Eine Inventur, die auf Verschwundenes zeigt, schickt jeden,
  der wiederverwenden will, ins Leere.*
- Build: `vite.hausplaner.config.ts`, `tsconfig.hausplaner.json`, npm-Scripts
  `build:hausplaner`/`test:hausplaner`/`tsc:hausplaner`.

## Energie-/Fach-Tools (mögliche Adapter-Quellen)  (VERIFIZIERT als Routen)
- `energie.grundriss`, `energie.plan-upload`, `energie.wr-auslegung`, `energie.wp-auslegung`,
  `energie.sanierung`, `energie.energiekonzept`, `energie.heizlast`.
- Heizlast-Projektion/Geometrie: `app/Services/Heizlast/GeometrieAbleitungService.php`,
  `app/Domain/Hausplaner/Actions/UebernehmeSzeneInAuslegung.php` (Szene → gebaeude_geometrie).

## NOCH ZU VERIFIZIEREN (nicht erfunden — Phase 2 real suchen)
Vor Nutzung im Repo suchen; NICHT annehmen, dass Pfade existieren:
- Aufgaben/Tasks, Kommentare, Erwähnungen, Beobachter.
- Aktivitätsverlauf / Audit-Trail / Historie (allgemein, ticketweit).
- Benachrichtigungen (Notifications/Events/Jobs).
- Allgemeine Status-/Workflow-/Phasen-/Freigabe-/Eskalationsmodelle.
- Generische Tabellen-/Filter-/Such-/Modal-/Leerzustand-Komponenten außerhalb des Styleguide.
- Factory-/Tenant-/Org-/Projektzugriffs-Testmuster.

Suchhinweis: nicht nur nach „Planner"/„Hausplaner" suchen, sondern nach funktional
vergleichbaren Bausteinen (z. B. bestehende Kommentar-/Aufgaben-/Aktivitätslogik an Deals,
Leads, Objekten oder Angeboten).
