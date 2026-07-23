# 3D-Existing-Code-Inventory

> **Rolle:** PLANNER (read-only). **Art:** Reine Bestandsaufnahme — kein Code geändert, keine
> Pakete, keine Migration, keine Route, kein Commit, kein Push.
> **Stand:** 2026-07-23. **Grundlage:** echte Funde per `git`/`grep`/`wc` in `ticket/` (Produktion,
> Schreib-Heimat) und `Documents/Playground/` (Konzept-/Codequelle, keine Produktion).
> **Zweck:** Vollständige Liste allen vorhandenen 3D-/Dach-Codes, damit **vor** jedem Neubau
> geklärt ist, was schon existiert. Grundlage für die Fähigkeitsmatrizen (Doc 2/3), den
> Herkunftsnachweis (Doc 4) und den Wiederverwendungsplan (Doc 5).

---

## 0. Kurzfassung

Es existieren **zwei getrennte 3D-/Dach-Codebestände**:

1. **ticket-Produktion (Schreib-Heimat)** — ein schlanker, integrierter 3D-Kern im Hausplaner
   (`renderers/three-d/`), der Wände, Öffnungen (echte Ausschnitte), Böden, Treppen und ein
   **rechteckiges Dach in vier Formen** (flach/pult/sattel/walm) rendert. **Keine** Gauben, **keine**
   Dachfenster, **keine** L-/T-/U-Verschneidung, **keine** Holz-/Sparren-Stückliste.

2. **Playground „Dach-&-PV-Planer" (Dach-Insel, keine Produktion)** — eine über ~28 Iterationen
   (EA11–EA28, Reparatur 3–10) gereifte, **reine, testbare Dach-Engine** in `src/utils/` plus die
   3D-Seite `DachplanerProPage.tsx`. Beherrscht **Gauben (5 Typen), Dachfenster/Kamin/Lüfter/Sat als
   Dachaufbauten, L-/T-/U-Verschneidung mit Grat-/Kehllinien, Schifterschnitt, Auswechslungen,
   Holz-/Sparren-Mengen, Dachform-Vorlagenbibliothek** — mit **18 Test-Dateien**.

**Zentraler Befund (Details in Doc 4):** Der rechteckige Produktions-Dachkern ist **nicht neu
erfunden**, sondern die aus dem Playground portierte Inkrement-Serie **S1 D-a … D-d**. Die *reiche*
Playground-Engine (`src/utils/`) ist bislang **nicht** in den Hausplaner gebrückt — sie hängt an der
separaten PV-Planer-Seite. Sie ist damit der bevorzugte Wiederverwendungs-Kandidat (Doc 5), nicht
Neuentwicklung.

---

## 1. ticket-Produktion — 3D-Renderer (`resources/planner/hausplaner/renderers/three-d/`)

| LOC | Datei | Zweck |
|---:|---|---|
| 414 | `szene.ts` | Imperativer three.js-Szenenaufbau: Wände (segmentiert), Öffnungen (echte Cutouts), **Böden via `erkenneRaeume(...)`**, Treppen, Dach (`dachMesh`). Filtert `n.levelId === activeLevel` (nur aktives Geschoss). Kein generisches Objekt-3D. |
| 124 | `segmentierung.ts` | Zerlegt Wände an Öffnungen in Segmente → echte Fenster-/Tür-Ausschnitte. |
| 108 | `dachMesh.ts` | Reines Dach-Mesh je Dachfläche (`dachMeshWelt`); wirft bei nicht-rechteckiger Kontur. |
| 122 | `platzierung.ts` | Platzierungs-/Transform-Helfer im Weltkoordinatensystem. |
| 63 | `adapter.ts` | Brücke Store-Modell → Renderer-Eingaben. |

## 2. ticket-Produktion — Dach-Geometrie/Domäne (außerhalb `three-d/`)

| LOC | Datei | Zweck |
|---:|---|---|
| 148 | `geometry/dachGeometrie.ts` | `dachFlaechen(roof)` je Form: **flach/pult/sattel/walm**; Azimut-/Neigungsableitung; `pruefeRechteckigeKontur(...)`. **Nur rechteckige Kontur.** |
| 34 | `geometry/dachVorlage.ts` | Reine Defaults je Form (Standard-Neigung, Label) fürs Anlegen. `DachForm = 'sattel'|'walm'|'pult'|'flach'`. |
| 43 | `projection/dachProjektion.ts` | 2D-Projektionsvertrag `dach_flaechen[]` (eingefrorenes Fixture). |

## 3. ticket-Produktion — Dach-Tests (`resources/planner/hausplaner/__tests__/`)

`dachGeometrie.test.ts`, `dachMesh.test.ts`, `dachModell.test.ts`, `dachProjektion.test.ts`,
`dachVorlage.test.ts` — 5 Dateien. Decken das **rechteckige** Dach ab (Formen, Mesh-Kontur, Modell,
Projektion, Vorlagen).

## 4. ticket-Produktion — angrenzender neuer Bauteil-Code (dieser Governance-Zyklus)

Nicht Dach, aber Teil desselben 3D-/Hausplaner-Kontexts und für Doc 5 relevant:

| Datei | Zweck |
|---|---|
| `geometry/oeffnungsBauarten.ts` | Premium-Katalog Fenster/Tür-Bauarten (24/24). |
| `geometry/treppenBauarten.ts` | 20 Treppen-Bauarten. |
| `geometry/heizkoerperTypen.ts` | 5 Heizkörper-Typen (schematische SVGs). |
| `geometry/werkzeugRegistry.ts` | WerkzeugNode-Registry — **noch ohne Registrierungen** (Keim, ungenutzt). |
| `app/HausplanerApp.tsx` (~1307) | 2D-Monolith; `Werkzeug = 'auswahl'|'wand'|'fenster'|'tuer'|'dach'|'treppe'` (lokaler State). |

---

## 5. Playground „Dach-&-PV-Planer" — reine Dach-Engine (`src/utils/`)

Alle Dateien sind **reine, testbare Logik** (keine three.js-Abhängigkeit), gewachsen über EA11–EA28.

| LOC | Datei | Zweck |
|---:|---|---|
| 2399 | `dachformVorlagen.ts` | Dachform-Vorlagenbibliothek (Parametrik je Form inkl. L/T/U). |
| 510 | `dachAusschnitt.ts` | Maßhaltige Dachöffnungen (Loch-Polygone). |
| 498 | `gaubeGeometrie.ts` | Gauben-/Dormer-Geometrie (Anschluss ans Hauptdach). |
| 190 | `aufbauPlatzierung.ts` | Platzierung Dach-Aufbauten (Kamin/Gaube/Dachfenster). |
| 174 | `auswechslung.ts` | Wechselhölzer an Dachöffnungen. |
| 167 | `linienBauteile.ts` | Linien-Bauteile (z. B. Schneefang). |
| 152 | `schifterListe.ts` | Schifterschnitt-Stückliste. |
| 135 | `dachVerschneidung.ts` | Kehl-/Gratlinien geneigter **L-/T-**Verschneidung (SSOT). |
| 126 | `dachUForm.ts` | Geneigte **U-Form**-Verschneidung. |
| 103 | `dachWerte.ts` | Klemm-/Umrechnungslogik (Grad/Neigung/Maße). |
| 96 | `dachOeffnung.ts` | Dachöffnungen für Gaube/Kamin/Dachfenster. |
| 82 | `holzBauteile.ts` | Holz-Bauteile (Pfetten, Grat-/Kehlsparren). |
| 67 | `sparrenTrennung.ts` | Sparren-Trennung an Öffnung (Dachfenster/Kamin). |
| 64 | `holzMengen.ts` | Holzlängen/-Mengen. |
| 61 | `aufbauOrientierung.ts` | Orientierung Dach-Aufbauten. |
| 52 | `aufbautenStatus.ts` | Status-/Gültigkeitslogik Dach-Aufbauten. |

**Summe Engine:** ~4.516 LOC reine Logik.

## 6. Playground — Typen/Store/Services der Dach-Insel

| LOC | Datei | Zweck |
|---:|---|---|
| 344 | `stores/roofConfigStore.ts` | Zustand-Store der Dach-Insel (15 Exporte). |
| 111 | `stores/roofTypes.ts` | Typen (16 Exporte): `RoofShape = sattel|pult|walm|rect|l-shape|t-shape`, `ObstacleType` (chimney/window/vent/sat/lichtkuppel + 5 Gaubentypen), `ObstacleData`, `ModuleData`, `RoofSlice`, `RoofTemplateConfig`, `BuildingParams`, `AdditionalRoof`. |
| 101 | `stores/roofVocab.ts` | Vokabular/Labels. |
| 194 | `services/dachproduktService.ts` | DB-Produkt-Anbindung (Ziegel/Eindeckung). |
| 111 | `services/roofConfiguratorService.ts` | Konfigurator-Service. |

## 7. Playground — 3D-Seite & Material-Panels

| LOC | Datei | Zweck |
|---:|---|---|
| 3786 | `pages/energie/DachplanerProPage.tsx` | Die eigentliche 3D-Dachplaner-Seite (three.js). **Monolith** — enthält Rendering + UI + viel Zustandslogik gemischt. |
| — | `components/energie/CoveringMaterialPanel.tsx`, `EindeckungMaterialPanel.tsx`, `MontagesystemPanel.tsx` | Material-/Montage-Auswahl. |

## 8. Playground — Dach-Engine-Tests (`src/services/__tests__/`)

18 Test-Dateien: `aufbautenStatus`, `auswechslung`, `dachAusschnitt`, `dachUForm`,
`dachVerschneidung`, `dachWerte`, `dachformVorlagen`, `dachproduktService`, `gaubeGeometrie`,
`holzBauteile`, `holzMengen`, `roofConfigStore.bridge`, `schifterListe`, `sparrenTrennung` (+ die
`src/hausplaner/__tests__/`-Kopien dachGeometrie/dachMesh/dachModell/dachProjektion).

## 9. Playground — `src/hausplaner/` (die Integrations-Kopie, Provenienz-Schlüssel)

`src/hausplaner/` ist eine **Kopie des Produktions-Hausplaners** mit exakt dem rechteckigen Dach:
`geometry/dachGeometrie.ts` (148), `renderers/three-d/dachMesh.ts` (108),
`projection/dachProjektion.ts` (43) — **identisch zur Produktion**. Sie **bridged die reiche
`src/utils/`-Engine noch nicht** (kein Import von `utils/gaube*`, `utils/dachVerschneidung`,
`utils/dachUForm`, `utils/dachformVorlagen`, `utils/sparren*`, `utils/aufbau*` gefunden). Das ist
genau die offene Naht (Doc 5).

## 10. Vorhandene Analyse-Dokumente (nicht neu erstellen)

- `Playground/BEFUND.md` — Bestandsaufnahme/Architektur-Befund (2026-06-15, „NUR Analyse").
- `Playground/ZIEL-STRUKTUR-UND-PLAN.md` — Zielzustand/Migration (2026-06-15); hält fest: **3D bleibt
  React 19 + three.js, wird NICHT nach Blade migriert** (prinzipbedingte JS-Insel).
- ticket: Commit `2634caa` „Architektur-Zielbild EIN 3D-Hausplaner (Schichten S0–S4, Wellen W-A..W-F)".

## 11. Nicht gefunden / bewusst leer

- **Kein** Gauben-/Dachfenster-/Verschneidungs-Code in der ticket-Produktion.
- **Keine** generische Objekt-3D-Darstellung (Heizkörper etc.) in `szene.ts`.
- `werkzeugRegistry.ts` ohne Registrierungen (kein aktives Tool-Registry).
