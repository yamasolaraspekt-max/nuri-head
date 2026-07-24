---
name: bauplaner-3d
description: Leit-Skill für JEDE Arbeit am 3D-Hausplaner (React/three.js-Insel im Laravel-CRM). Liefert die Code-Landkarte, die 4 Grundregeln (messen-vor-behaupten, Docs-zuerst, kuratieren, Konzept-Vorab-Freigabe) und die Build-/Gate-Kette. Immer laden, bevor am Hausplaner gebaut oder geprüft wird.
---

# bauplaner-3d

## Ziel
Jede Hausplaner-Arbeit (Modell, Geometrie, Render, Fixture) läuft kontrolliert, additiv und belegt — nie
„aus dem Kopf". Dieser Skill ist die gemeinsame Landkarte + Grundhaltung für Planner, Generator, Evaluator.

## Code-Landkarte (Ist, gemessen — bei Zweifel `grep`/`git log`, nicht raten)
- **Domäne (eine Wahrheit):** `resources/planner/hausplaner/domain/`
  - `scene.types.ts` — `SceneDocument` = `levels[] · nodes[] (Wall/Opening/Object/Zone/Route) · roofs[RoofNode] · ceilings?[CeilingNode]`. Alle Längen GANZE mm.
  - `validation.ts` — Zod-Spiegel → `npm run schema:hausplaner` regeneriert `scene-document-v2.schema.json` (PHP-Validator liest es). **Zod ändern ⇒ IMMER regenerieren, sonst 422.**
  - `commands/applyCommand.ts` — jede Änderung ist ein Command (`HausplanerCommand`); Undo via inverse Immer-Patches. `CommandAbgelehnt` statt stillem Erfinden.
- **Geometrie (rein/testbar, KEIN three/React):** `geometry/`
  - `wallGeometry.ts` → `wandBaender` = gehrte Wand-Ecken (die EINE Ecken-Wahrheit, 2D+3D teilen sie).
  - `dachUForm.ts`/`dachVerschneidung.ts` → U- bzw. L/T-Dachflächen, **byte-treu** aus der Engine `buildCompoundPitchedFaces` gespiegelt. Nicht umformulieren.
  - `roomDetection`, `treppeObjekt`, `polygonFlaeche`.
- **Renderer (dünn über den reinen Funktionen):** `renderers/three-d/`
  - `szene.ts` — three-Aufsatz; Wände als Prisma aus `wandBaender` (Gehrung), Dach aus `dachMesh`, Decke-Slab aus `ceilings`.
  - `dachMesh.ts` — SSOT `dachRoh`; Verschneidungsformen platziert am **Grundriss-Bbox-Zentrum** (`polygonBbox`), nicht am Schwerpunkt.
  - `deckenMesh.ts`, `platzierung.ts`, `segmentierung.ts` (Öffnungen), `capture.ts` (`?capture=1` Snapshot).
  - `2d/` — Konva-Renderer DERSELBEN Daten.
- **Fixtures:** `fixtures/studioFixtures.ts` → `?fixture=<name>` lädt eine deterministische Szene (u-dach, decke-treppe) für die reproduzierbare Sicht-Abnahme.
- **Build/Gate (nativ Mac):** `npm run tsc:hausplaner` · `schema:hausplaner`(`:check`) · `test:hausplaner` · `build:hausplaner`. Bundle-Artefakt `public/hausplaner/hausplaner.js` — **nie mergen, immer neu bauen**.

## Die 4 Grundregeln (jede Rolle, jederzeit)
1. **Messen vor behaupten.** Keine Fach-/Geometrie-Aussage ohne Beleg am Code, am Test oder an der Norm.
   Spec/Doku kann der Umsetzung hinterherhinken → bei Widerspruch gilt der **Code als Wahrheit** (`grep`, `git log`).
2. **Docs zuerst.** Vor dem Bauen: Ledger (`docs/handoff-status.md`), Arbeitskompass, relevante ADRs/Specs
   und den Startblock des Slices lesen. Kein Bau ohne Einordnung.
3. **Kuratieren, nicht wuchern.** Reuse vor Neu; EINE Wahrheit je Sachverhalt; kein zweiter SSOT-Anker,
   keine parallele Miter-/Flächen-/Placement-Rechnung. Additiv an Modell/DB (kein 422, kein Bestandsbruch).
4. **Konzept-Vorab-Freigabe.** Fach-/Rechts-entscheidende Slices (Tragwerk, Auslegung, neue Bauteile) brauchen
   ZUERST ein Konzept + **Yama-Fach-Freigabe (Tor 1)**. Operanden-Gate: kein erfundener Wert — fehlt ein
   Operand, wird gefragt/markiert, nie stillschweigend weitergerechnet.

## Fach-Linsen (zusätzlich laden je Thema)
Dach → `dachdeckermeister`/`zimmermannmeister` · Statik → `statiker` · Mauerwerk → `maurer` ·
Code → `software-architekt`/`frontend-entwickler`/`backend-entwickler` · plus `governance-zyklus`, `ux-design`.

## Prozess & Tore
Der erweiterte 7-Stufen-Ablauf + die beiden Freigabe-Tore stehen in `references/prozess-erweitert.md`.
**Tor 2 (Merge nach main / Deploy ins LIVE-CRM) bleibt eine bewusste Yama-Entscheidung** — Autonomie gilt
für den ganzen Bau-/Prüf-Zyklus bis dorthin, nicht für den irreversiblen Produktiv-Schritt.
