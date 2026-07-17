# PLANNER-SPEC — A3: Rechte-Mapping Hausplaner an die echte ticket-Konvention

**Rolle:** Planner (kein Code) · **Datum:** 2026-07-17 · **Heimat-App:** ticket · **Bau-Gate:** ERST NACH T-d
(ändert die derzeit „In Abnahme" stehenden Hausplaner-Routen — würde sonst die Sichtprobe verschieben).

## 0 · Ist-Beleg (grep, Code = Wahrheit)
- Zugriffs-Konvention der App: `permission:Item,action` mit EINEM großgeschriebenen Item + CRUD-Aktion —
  z. B. `permission:Users,read|add|update|delete` (web.php 2271–2275). Grantbare Items stehen in
  `UserRollController::permissionModules()` (`config('permissions', [...])`, Keys kapitalisiert:
  'Users' => 'Benutzerverwaltung', 'Invoice' => 'Rechnung', …). Die Sidebar gated je Eintrag über
  `$hasPermission('Item','is_read')` gegen `user_rolls` (Spalten is_read/is_add/is_update/is_delete),
  `is_admin` = Bypass. `User::hasPermission($item,$action)` mappt read→is_read, update→is_update.
- **Ausreißer:** die EINZIGEN `permission:`-Routen der ganzen App mit gepunktetem Item sind meine
  Hausplaner-Routen `permission:hausplaner.view` / `permission:hausplaner.manage` (Item-String
  „hausplaner.view"/„hausplaner.manage", Aktion default read). Diese Items sind NICHT in
  `permissionModules()` → für Nicht-Admins nicht grantbar → heute öffnet nur `is_admin` (der FLAG).

## 1 · Ziel & Entscheidung
Hausplaner an die echte Konvention angleichen: **EIN Item `Hausplaner`** mit CRUD-Aktionen, registriert
in `permissionModules()`, grantbar im BESTEHENDEN Rechte-Admin (`user_roll.blade`/`UserRollController`).
Damit kann Yama Mitarbeitern lesen (öffnen) bzw. verwalten (speichern) ohne `is_admin` geben. Ersetzt die
Ausreißer-Routen 1:1. Keine neue Mechanik, keine Migration (Tabellen `user_rolls`/`user_roll_items` da).

## 2 · Vertrag (exakt)
- **Registry:** in der Items-Liste (config/permissions.php falls vorhanden, sonst der Fallback-Array in
  `UserRollController::permissionModules()`) ergänzen: `'Hausplaner' => 'Gebäudeplaner (Hausplaner)'`.
- **Routen** (web.php, die 6 `hausplaner.objekt.*`), Umstellung der Middleware:
  - lesen → `permission:Hausplaner,read`: `seite`, `snapshots.liste`, `katalog`.
  - schreiben → `permission:Hausplaner,update`: `speichern`, `snapshots.erstellen`, `snapshots.wiederherstellen`.
  - (NICHT `manage` als Aktion — `hasPermission`-match() kennt 'manage' nicht → fiele auf is_read;
    `update` ist die korrekte Aktion für is_update.)
- **Button** (`admin/objekte/akte.blade.php`): Bedingung von `hasPermission('hausplaner.view')` auf
  `hasPermission('Hausplaner')` (Default-Aktion read) — Sichtbarkeit == Lese-Route, wie heute gespiegelt.

## 3 · Nahtstellen / Grenzen
- Berührt: die 6 Hausplaner-Routen (nur Middleware-String), die Button-Bedingung, die Items-Registry.
- KEINE DB-Migration, KEINE Datenänderung an Bestand; `user_rolls`/`user_roll_items` bleiben unverändert.
- Sidebar-Eintrag NICHT Teil dieses Pakets (Einstieg bleibt an der Objektakte). Optional später.

## 4 · Kantenliste
- `is_admin` → Button sichtbar, alle Routen offen (unverändert).
- Nutzer mit `is_read` auf 'Hausplaner' → Button + öffnen + lesen; PUT/Speichern ohne `is_update` → 403.
- Nutzer mit `is_update` → speichern erlaubt.
- Nutzer ohne Grant → kein Button UND 403 (fail-closed, Sichtbarkeit == Zugriff bleibt erhalten).

## 5 · Abnahmekriterien (Evaluator, Gegen-Beweis)
- `php artisan route:list | grep hausplaner` → 3× `permission:Hausplaner,read`, 3× `permission:Hausplaner,update`;
  kein `hausplaner.view`/`.manage` mehr; die alte `hausplaner.dachplaner`-Route unberührt.
- `permissionModules()` (bzw. config) enthält Key `Hausplaner` → erscheint im Rechte-Admin (user_roll.blade).
- Laufzeit: Test-Nutzer NUR mit is_read → GET seite 200, PUT speichern 403. Mit is_update → PUT erlaubt
  (409 nur bei Revisionskonflikt, nicht 403). Ohne Grant → kein Button + GET 403. `is_admin` → alles offen.
- `@can(` in akte.blade weiterhin 0; Button-Sichtbarkeit == Lese-Route (beide `hasPermission('Hausplaner')`).
- git-diff: nur web.php (6 Zeilen), akte.blade (1 Zeile), permissionModules()/config (1 Zeile). Volle Suite grün.

## 6 · Governance / Stopp
BAU erst nach T-d (die Routen stehen bis dahin „In Abnahme"). Keine Migration/keine Datenänderung.
Rollentrennung: diese Spec = Planner; Bau = Generator (grep-first: permissionModules-Ort real prüfen); Abnahme = Evaluator.
