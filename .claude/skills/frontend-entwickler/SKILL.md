---
name: frontend-entwickler
description: Code-Linse Frontend für ticket-CRM (Blade/jQuery/Vuexy) und Hausplaner-Insel (React 19 + three.js + Konva). Laden bei UI-/Render-/Interaktions-Aufgaben — inkl. Scope-Grenzen (React nur in der Insel), Token-Disziplin, A11y.
---

# frontend-entwickler

## Ziel
Sichtbare Änderungen sauber, additiv und im richtigen Scope bauen — React nur in der gekapselten Hausplaner-
Insel, das übrige CRM bleibt Blade/jQuery.

## Prüf-Linse
- **Scope-Grenze.** React/TSX AUSSCHLIESSLICH in `resources/planner/hausplaner/` (gebautes Bundle
  `public/hausplaner/hausplaner.js`). Alpine nur in den zwei freigegebenen Scopes. Kein Framework-Wildwuchs.
- **Renderer sind dünn.** three/Konva rechnen NICHT neu — sie lesen die reinen Geometrie-Funktionen. Vertices
  über die EINE Achsen-Umrechnung (`weltZuThree`). Kein zweiter Rechenweg im Render.
- **Token-Disziplin.** Nur `T.*`/`--sa-*`-Tokens, 0 roher Hex in geänderten Zeilen (Hex lebt in
  `studioDaten.ts`/Token-Dateien). Vor neuer Komponente `/admin/styleguide` prüfen; geteilte Bausteine
  (`studioUi`) statt Einweg-Markup.
- **A11y.** Zustand als Farbe UND Text (nie nur Farbe); Kontrast ≥ AA; Fokus sichtbar; Status-Grün = `T.ok`
  (nicht Marke `T.brand`).
- **Zustände.** Lade/Leer/Fehler/Erfolg gestaltet; ehrlicher Leer-Fall (kein leerer PNG/Screen der wie „ok" aussieht).
- **Determinismus prüfbar.** Für Sicht-Abnahme `?fixture=…&capture=1`; Bundle-Cache per Hard-Reload umgehen.

## Rote Flaggen
- Roher Hex in einer View; neue Komponente ohne Styleguide-Abgleich; Nur-Farbe-Status.
- Zweite Berechnung im Renderer; React außerhalb der Insel.
- „Screenshot als Beweis" statt reproduzierbarer Fixture + Unit-Test.

## Gate
`tsc:hausplaner 0 · build:hausplaner 0`; UI-Änderung mit Browser-/Screenshot-Prüfung in 3 Viewports
(1440/1024/375), außer reine Doku.
