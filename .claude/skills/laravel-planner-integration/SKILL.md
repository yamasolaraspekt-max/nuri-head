---
name: laravel-planner-integration
description: Regelt die Anbindung der React/Three-Insel an das Laravel-CRM: Blade-Mount + eingebettete Szene (kein Lade-Fetch), Web-Routen (Session/CSRF) mit permission:Item,action, Rechte-Registry, Build-Pipeline (isoliert vom Vue-Build) und additive DB. Nutzt vorhandene CRM-Bausteine (Shell, Sperre, Rechte), baut nichts parallel.
---

# laravel-planner-integration

## Ziel
Die Planner-Insel sauber, rechtegebunden und CI-konform ins CRM einbinden.

## Muster (wiederverwenden)
- **Mount**: Blade-Seite mit `#hausplaner-root` + `<script type="application/json" id="...-scene">
  @json($dokument->scene_json)</script>` (Szene eingebettet, kein Fetch). Bundle per `@if
  file_exists(...) <script type="module" src="{{ asset(...) }}">`.
- **CRM-Shell**: Projektkopf `x-page-head`, Sidebar-Item, Designsystem-Tokens `--sa-*` verwenden.
- **Bearbeitungssperre**: `@include('admin.layouts.partials.bearbeitungs-sperre', [...])`.
- **Routen**: Web (Session+CSRF), `->middleware('permission:Item,action')`,
  Route-Model-Binding `{objekt}` (LeadAlternativeAdd, 404 bei unbekannt).
- **Rechte-Registry**: Item in `UserRollController::permissionModules()` eintragen; Blade-Gate
  `auth()->user()->hasPermission('Item')`; Admin-Bypass über `is_admin`.
- **Build isoliert**: eigener `vite.hausplaner.config.ts` (`publicDir:false`, fixe Ausgabenamen),
  eigener tsconfig, Scripts `build:hausplaner`/`test:hausplaner`/`tsc:hausplaner`; berührt weder
  `resources/js` noch das Laravel-Vite-Manifest.
- **Route-/View-Cache**: Closure-Routen sind nicht cachebar → nach Änderungen ggf.
  `php artisan optimize:clear`; Blades kompilieren bei Änderung neu.

## Nicht-Scope
Kein zweites Auth-/Rechte-/Upload-/Dokumentensystem. Keine Vue-Build-Beeinflussung.

## Prüfungen
- Route rechtegebunden; Nicht-Admin ohne Recht bekommt 403/kein Button.
- Szene lädt eingebettet (0 Fetches); Speichern respektiert Revision/409.
- Build isoliert grün (tsc + build + tests).

## Pflicht-Stopp
Wenn nur Planung: stoppen. Kein Commit. Kein Push.
