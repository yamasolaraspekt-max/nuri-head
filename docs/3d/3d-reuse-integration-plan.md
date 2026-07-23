# 3D-Reuse-Integration-Plan

> **Rolle:** PLANNER (read-only) — dieser Plan ist eine **Spezifikation**, kein Code. Keine
> Implementierung, keine Pakete, keine Migration, kein Commit in diesem Durchgang.
> **Stand:** 2026-07-23. **Leitprinzip (verbindliche Reihenfolge):**
> **REUSE → ADAPT → EXTRACT → CONSOLIDATE → REFACTOR → NEW.** NEW nur, wenn alle davor nachweislich
> nicht tragen. **Schreib-Heimat:** `ticket`. **3D bleibt React 19 + three.js** (keine Blade-Migration
> der Insel, gemäß `ZIEL-STRUKTUR-UND-PLAN.md`).

## 0. Grundentscheidung

Der rechteckige Produktions-Dachkern (Strang B) **bleibt die Basis** und wird **erweitert**, nicht
ersetzt. Die reiche Playground-Engine (Strang A, `src/utils/`) wird als **reine TS-Module** in den
ticket-Hausplaner **übernommen** und an das bestehende Store-/Renderer-Modell **angeschlossen**. Der
Seiten-Monolith `DachplanerProPage.tsx` wird **nicht** übernommen — nur seine three.js-Renderteile
dienen als Extraktionsvorlage.

## 1. Einstufung je Funktionsblock (Quelle → Ziel → Maßnahme → Abnahmemaß)

Maßnahme-Codes: **R**=Reuse (unverändert übernehmen) · **A**=Adapt (an Store/Typen anpassen) ·
**E**=Extract (aus Monolith herauslösen) · **C**=Consolidate (Dubletten zu einer Wahrheit) ·
**RF**=Refactor · **N**=New.

| # | Funktion | Quelle (Playground) | Ziel (ticket) | Maßn. | Abnahmemaß |
|--:|---|---|---|:--:|---|
| 1 | Klemm-/Umrechnungslogik | `utils/dachWerte.ts` | `geometry/dachWerte.ts` | R | Port-Test grün (aus `dachWerte.test.ts`) |
| 2 | Dachform-Vorlagen L/T/U | `utils/dachformVorlagen.ts` | `geometry/dachformVorlagen.ts` | A | Vorlage je Form erzeugt gültige Kontur; Test aus `dachformVorlagen.test.ts` |
| 3 | Verschneidung L/T (Kehl/Grat) | `utils/dachVerschneidung.ts` | `geometry/dachVerschneidung.ts` | R | eingefrorenes Fixture/Regressionsschloss bleibt grün |
| 4 | Verschneidung U-Form | `utils/dachUForm.ts` | `geometry/dachUForm.ts` | R | `dachUForm.test.ts` grün |
| 5 | Dachöffnung (Loch-Polygon) | `utils/dachOeffnung.ts` + `dachAusschnitt.ts` | `geometry/dachOeffnung.ts` | A | maßhaltiges Loch je Aufbau; `dachAusschnitt.test.ts` grün |
| 6 | Gauben-Geometrie (5 Typen) | `utils/gaubeGeometrie.ts` | `geometry/gaubeGeometrie.ts` | A | Anschluss ans Hauptdach ohne Rückwand-über-First (EA17-Fix); `gaubeGeometrie.test.ts` grün |
| 7 | Dachaufbauten Platzierung/Orientierung/Status | `utils/aufbauPlatzierung.ts`,`aufbauOrientierung.ts`,`aufbautenStatus.ts` | `geometry/aufbau*.ts` | A | Kamin/Dachfenster/Gaube auf Fläche 0..1 platzierbar; Status-Tests grün |
| 8 | Sparren-Trennung an Öffnung | `utils/sparrenTrennung.ts` | `geometry/sparrenTrennung.ts` | R | `sparrenTrennung.test.ts` grün |
| 9 | Auswechslungen | `utils/auswechslung.ts` | `geometry/auswechslung.ts` | R | `auswechslung.test.ts` grün |
| 10 | Schifterschnitt-Liste | `utils/schifterListe.ts` | `geometry/schifterListe.ts` | R | `schifterListe.test.ts` grün |
| 11 | Holzmengen/Holz-Bauteile | `utils/holzMengen.ts`,`holzBauteile.ts` | `geometry/holz*.ts` | R | beide Port-Tests grün |
| 12 | Linien-Bauteile (Schneefang) | `utils/linienBauteile.ts` | `geometry/linienBauteile.ts` | R | Port-Test grün |
| 13 | Dach-Typen (RoofShape L/T/U, ObstacleType) | `stores/roofTypes.ts` | `domain/scene.types.ts` + Zod | C | **eine** Typwahrheit; Schema `schema:hausplaner` regeneriert, `schema:hausplaner:check` Exit 0 |
| 14 | 3D-Render Gauben/Aufbauten/Verschneidung | `pages/energie/DachplanerProPage.tsx` (Renderteile) | `renderers/three-d/dachMesh.ts` (+ neue Helfer) | E | Gaube/Dachfenster erscheinen im 3D-Mesh; Sichtprüfung + Mesh-Kontur-Test |
| 15 | Eindeckung/Ziegel-Produkt | `services/dachproduktService.ts` | ticket-Service (Bauordnung: Auth-Gate!) | A | kein Endpunkt ohne Autorisierungsprüfung |
| 16 | Material-/Montage-Panels | `components/energie/*Panel.tsx` | Hausplaner-UI | A | in v9-Studio eingebunden, Tokens (ux-design) eingehalten |

**Nicht übernehmen (N/A):** `DachplanerProPage.tsx` als Ganzes (Monolith), Store `roofConfigStore.ts`
als Ganzes (der Hausplaner hat bereits `hausplanerStore.ts` als einzige Modellwahrheit → **CONSOLIDATE**
statt zweitem Store).

## 2. Reihenfolge / Wellen (jede Welle Governance-geprüft, RED blockiert)

- **W-1 Fundament (reine Logik, kein Render):** #1, #13 (Typen/Schema zuerst — sonst 422-Desync wie bei
  `produkt.typ`). Danach #3, #4, #2. Abnahme: alle Port-Tests grün, Schema-Gate Exit 0.
- **W-2 Öffnungen & Gauben (Logik):** #5, #6, #7. Abnahme: Gaubentypen + Dachfenster als Loch/Aufbau
  berechnet, Tests grün.
- **W-3 3D-Anschluss:** #14 — Verschneidung/Gauben/Aufbauten ins `dachMesh`/`szene.ts`. Abnahme:
  Sichtprüfung (Screenshot) + Mesh-Kontur-Tests; nur aktive Ebene bleibt Regel oder wird bewusst
  erweitert.
- **W-4 Zimmerer-Stückliste:** #8–#12. Abnahme: Holz-/Schifter-/Auswechslungs-Tests grün.
- **W-5 Material/Produkt/PV & UI:** #15, #16. Abnahme: Auth-Gate belegt, UI-Tokens (ux-design) erfüllt.

## 3. Nahtstellen & Bauordnung (ticket)

- **Eine Modellwahrheit:** neuer Dach-Zustand fließt in `hausplanerStore.ts` (typed Command + Immer
  inverse-patch), **kein** zweiter Store. 2D/3D bleiben read-only.
- **Schema-Gate zuerst:** jede neue Typ-/Feld-Einführung (RoofShape L/T/U, ObstacleType) erst in
  `validation.ts` (Zod) → `npm run schema:hausplaner` → Commit des regenerierten
  `scene-document-v2.schema.json`. Lehre aus `970f0cc`→`aecc517`: Zod ohne Schema-Regen = 422/RED.
- **Auth-Gate:** `dachproduktService`-Anbindung nur mit Ownership-Prüfung (kein Endpunkt ohne Gate).
- **Build über Gate:** `npm run build:hausplaner` (nicht `npx vite build`); auf ARM-Device
  Rollup-Optional-Dep-Bug → x64-Container bauen.

## 4. Kantenliste (wo es erfahrungsgemäß bricht)

Nicht-rechteckige Kontur (der bestehende `pruefeRechteckigeKontur`-Wurf muss für L/T/U bewusst
geöffnet werden, sonst wirft `dachMeshWelt`); Gaubenanschluss über First (EA17); Loch-Polygon nicht
maßhaltig (EA25); Typ-Dublette RoofShape/ObstacleType (R2-Konzept → CONSOLIDATE); Schema-Desync
(422); Mehrgeschoss-Filter in `szene.ts`; Speicher-/Dispose-Disziplin bei zusätzlichen Meshes.

## 5. Rollen-Übergabe (Governance)

- **Dieser Durchgang (PLANNER):** fünf Dokumente erstellt, **read-only**, keine Implementierung, kein
  Commit — Ballbesitz endet hier.
- **Nächster Schritt (nicht jetzt):** Evaluator-Re-Abnahme des Schema-Fix `aecc517` (S0) steht noch aus;
  erst danach beginnt der **Generator** mit W-1 nach dieser Spezifikation. Der **Evaluator** prüft je
  Welle unabhängig mit Gegen-Beweis (Tests selbst ausführen, RED blockiert die nächste Welle).

## 6. Warum NEW hier fast nicht vorkommt

Von 16 Funktionsblöcken: **8× R, 6× A, 1× E, 1× C, 0× N.** Der einzige echte Extraktionsaufwand ist
das Herauslösen der three.js-Renderteile aus dem 3786-Zeilen-Monolith (#14); alles andere ist reine,
bereits getestete Logik, die portiert bzw. an das Store-Modell angepasst wird. Genau dieses Muster
(Playground-Logik → ticket-Hausplaner) ist mit Strang B bereits **erfolgreich vorexerziert** (Doc 4).
