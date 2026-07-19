---
name: planner-security-review
description: Sicherheits-Review für Planner-Slices: Organisations-/Projektbindung, Policies, Route-Model-Binding, Fremd-Org-Verhalten, Upload-/Asset-Prüfung, additive DB, keine destruktiven Operationen. Nutzt das vorhandene Ticket-Sicherheitsmodell (hasPermission, permission:), baut kein zweites.
---

# planner-security-review

## Ziel
Jeder Planner-Zugriff ist rechte-, org- und projektgebunden; keine Datenlecks, keine destruktiven Ops.

## Prüfpunkte
- **Rechte**: jede Route/Aktion mit `permission:Item,action`; Blade-Sichtbarkeit spiegelt die
  Route-Sperre; Admin-Bypass bewusst (`is_admin`).
- **Objekt-/Projektbindung**: Route-Model-Binding (`{objekt}`=LeadAlternativeAdd), 404 statt Leak
  bei fremdem/unbekanntem Objekt.
- **Fremd-Org**: kein Zugriff über Org-Grenzen; testen.
- **Uploads/Assets**: Typ-/Größenprüfung, Zugriffsschutz auf Dateien, keine Pfad-Injektion.
- **DB additiv-only**: keine destruktiven Migrationen; Ticket ist live (~3000 Kunden).
- **Keine Geheimnisse** in Client-Bundle/URLs; CSRF bei Web-Schreibpfaden.

## Verbote (Leitplanken)
Kein `git push`/Force-Push/`reset --hard`/`clean -fd`, kein DB-Wipe, keine nicht freigegebenen
Migrationen, keine Änderungen außerhalb des Slice-Scopes.

## Ausgabe
Befund je Prüfpunkt (grün/rot) mit Beleg + konkretem Reproduktions-/Testfall bei Rot.

## Pflicht-Stopp
Review ohne Selbstumsetzung. Kein Commit. Kein Push.
