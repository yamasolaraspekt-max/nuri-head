# 3D-Ticket-Capability-Matrix

> **Rolle:** PLANNER (read-only). **Stand:** 2026-07-23. **Scope:** Was der 3D-Kern der
> **ticket-Produktion** (Schreib-Heimat) heute tatsächlich kann — belegt am Code, nicht am Vorsatz.
> Legende: ✅ vorhanden · ◐ teilweise/begrenzt · ❌ fehlt.

## A. Struktur / Hülle

| Fähigkeit | Status | Beleg / Bemerkung |
|---|:--:|---|
| Wände in 3D (segmentiert) | ✅ | `szene.ts` + `segmentierung.ts` |
| Fenster-/Tür-Ausschnitte als echte Cutouts | ✅ | `segmentierung.ts` erzeugt Segmente um Öffnungen |
| Böden je erkanntem Raum | ✅ | `szene.ts` via `erkenneRaeume(waende, hoehe)`, `FARBE_BODEN=0xe3d8c4` (M0-Paket-1) |
| Treppen in 3D | ✅ | `szene.ts` rendert Treppen |
| Decken (obere Raumabschlüsse) | ❌ | nicht vorhanden |
| Mehrere Geschosse gleichzeitig sichtbar | ❌ | `szene.ts` filtert `n.levelId === activeLevel` → nur aktives Geschoss |
| Generische Objekte in 3D (Heizkörper etc.) | ❌ | nur 2D-ObjectNode; kein 3D-Pendant |

## B. Dach

| Fähigkeit | Status | Beleg / Bemerkung |
|---|:--:|---|
| Flachdach | ✅ | `dachGeometrie.ts` case `'flach'` |
| Pultdach | ✅ | case `'pult'` |
| Satteldach | ✅ | case `'sattel'` |
| Walmdach | ✅ | case `'walm'` |
| Dach-Mesh in 3D | ✅ | `dachMesh.ts` (`dachMeshWelt`) |
| 2D-Projektion `dach_flaechen[]` | ✅ | `dachProjektion.ts` (eingefrorenes Fixture) |
| Standard-Neigung/Label je Form | ✅ | `dachVorlage.ts` |
| **Nur rechteckige Kontur** | ◐ | `pruefeRechteckigeKontur(...)` wirft sonst — bewusste Grenze |
| L-/T-/U-förmiges Dach | ❌ | keine Verschneidung/Kehl-/Gratlinien |
| Gauben (Schlepp/Trapez/Flach/Giebel/Spitz) | ❌ | nicht vorhanden |
| Dachfenster / Kamin / Lüfter / Sat als Dachaufbau | ❌ | nicht vorhanden |
| Zwerchgiebel / Krüppelwalm / Mansard | ❌ | nicht vorhanden |

## C. Handwerk / Stückliste (Zimmererseite)

| Fähigkeit | Status | Bemerkung |
|---|:--:|---|
| Sparren / Pfetten | ❌ | keine Holzbauteile |
| Schifterschnitt-Liste | ❌ | — |
| Auswechslungen an Öffnungen | ❌ | — |
| Holzmengen / -längen | ❌ | — |
| Schneefang / Linien-Bauteile | ❌ | — |

## D. Material / Produkt

| Fähigkeit | Status | Bemerkung |
|---|:--:|---|
| Eindeckung/Ziegel-Produktwahl am Dach | ❌ | keine Produktanbindung im Hausplaner-Dach |
| PV-Belegung | ❌ | nicht im Hausplaner (nur in der Playground-Insel) |

## E. Qualität / Absicherung

| Aspekt | Status | Beleg |
|---|:--:|---|
| Tests Dach-Geometrie/Mesh/Projektion/Vorlage/Modell | ✅ | 5 Test-Dateien |
| Schema-Gate (Zod → JSON-Schema) | ✅ | `schema:hausplaner:check`; nach `produkt.typ`-Fix (Commit `aecc517`) grün, 287/287 |
| Build-Gate | ✅ | `build:hausplaner` = schema:check && tsc && vite build |

## Fazit (ticket)

Der Produktionskern trägt ein **vollständiges, aber bewusst rechteckiges Grundhaus mit Standarddach**.
Alles, was ein Haus „zusammenstellbar und realistisch" macht und heute fehlt — **Gauben, Dachfenster,
nicht-rechteckige Dächer (L/T/U), Decken, Mehrgeschoss-Sicht, generische Objekte in 3D, Holz-/PV-/
Material-Ebene** — ist **kein weißes Blatt**: Die Dach-/Aufbau-/Verschneidungs-/Holz-Fähigkeiten
existieren bereits reif in der Playground-Insel (Doc 3) und sind der Wiederverwendungs-Kandidat (Doc 5),
nicht Neuentwicklung.
