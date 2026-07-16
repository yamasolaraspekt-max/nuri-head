# ZIELBILD — Gebäudeplaner (2D/3D-Plattform)

**Rolle:** Planner / North-Star · **Datum:** 2026-07-16 · **Geltung:** verbindlich für ALLE Agenten
und Wellen, die am Planer arbeiten (ticket = Heimat, playground = F&E-Quelle).

Dieses Dokument nagelt die Vision fest, damit sie über Jahre trägt. Es ist die gemeinsame Wahrheit:
Wer am Planer baut, liest zuerst hier. Es verhindert zweite Datenmodelle, doppelte Renderer und
das Nachbauen von reifer Fremdtechnik.

---

## 1 · Was wir bauen

Einen **fusionierten 2D/3D-Gebäudeplaner** — aus Hausplaner und Dachplaner wird EIN Werkzeug, das
alle Gewerke am selben Gebäudemodell bedient: Grundriss zeichnen, Heizlastberechnung, Zimmermann-/
Dachdeckerplanung, PV-Belegung, und später Bad-, Küchen-, Fenster- und Innenraumplanung mit Texturen
und einer 3D-Möbel-Datenbank — bis hin zu fotorealistischen Bildern.

Ziel-Anspruch: **CAD-Präzision** (Millimeter-Wahrheit) + **fotorealistische Darstellung**. Aber der
Weg dorthin ist NICHT, AutoCAD oder Cinema 4D nachzubauen (siehe §3).

## 2 · Der unumstößliche Grundsatz: EINE Szene, viele Renderer, jedes Gewerk ein Modul

- **Eine Wahrheit:** das `SceneDocument` (Millimeter-Integer). Es gibt genau EIN Gebäudemodell je
  Objekt. Kein Gewerk hält eine eigene Kopie der Geometrie.
- **Renderer LESEN die Szene**, sie besitzen keine Daten. 2D (Konva), 3D (three.js) und die künftige
  Textur-/Foto-Schicht sind Sichten auf dieselben Daten.
- **Projektionen** rechnen aus der Szene ab (Heizlast, Dachfläche, PV) und speisen bestehende Engines.
- **Jedes Gewerk ist ein Modul** = Node-Typen + Domänenlogik + optional ein Renderer-Layer + optional
  eine Projektion/Berechnung. Ein neues Gewerk erweitert die Node-Union und den Katalog — es startet
  NIE ein zweites Datenmodell.

## 3 · Die oberste Leitplanke: BAUEN vs. ANDOCKEN

> **Wir bauen das Gebäudemodell und die Gewerke-Logik. Fotorealismus, Render-Engine und schwere
> CAD-Mathematik docken wir über offene Formate (glTF) an. Wir schreiben KEINEN Renderer und KEINE
> CAD-Engine von Null.**

| Wir BAUEN (unser Burggraben) | Wir DOCKEN AN (reife Fremdtechnik) |
|---|---|
| SceneDocument (mm-Wahrheit), Node-Typen, Katalog, Materialien | Fotorealistisches Offline-Rendering (Blender/Cycles, Path-Tracer, Render-Dienst) via **glTF-Export** |
| Gewerke-Module: Heizlast, Dach, PV, Bad, Küche, Möblierung | Echtzeit-PBR (Texturen/Licht) über **three.js** (bereits im Bundle) |
| Projektionen (Heizlast/Dachfläche/PV), Domänen-Berechnung | Constraint-/Parametrik-Solver — nur wenn je nachweislich nötig, dann Bibliothek statt Eigenbau |
| Mehrbenutzer-Schutz, Versionierung, Governance, Rechte | 3D-Möbel-Modelle als Assets (GLB), nicht selbst modelliert |

Grund: AutoCAD und Cinema 4D sind Jahrzehnte großer Teams. Unser Wert ist die **Domänen-Integration
für den Solar-/Bau-Handwerker** — die kann keine Fremdsoftware. Die Bildqualität ist zugekaufte/
angedockte Physik.

## 4 · Die Schichten (Architektur)

```
        ┌─────────────────────────────────────────────────────────┐
        │  GEWERKE-MODULE  (Node-Typen + Domäne + Berechnung)       │
        │  Grundriss · Heizlast · Dach/Zimmerei · PV · Bad · Küche  │
        │  · Fenster · Möblierung · …                               │
        └───────────────┬───────────────────────┬─────────────────┘
                        │ schreiben Commands     │ lesen/projizieren
        ┌───────────────▼───────────────────────▼─────────────────┐
        │  EINE WAHRHEIT:  SceneDocument (mm-Integer)              │
        │  Nodes: Wall · Opening · Object · Zone · Route · Roof …  │
        │  + Katalog (3D-Modelle) + MaterialDefinitions           │
        └───────────────┬───────────────────────┬─────────────────┘
          Renderer lesen │                       │ Projektionen rechnen ab
        ┌───────────────▼──────────┐   ┌─────────▼─────────────────┐
        │  RENDERER                │   │  PROJEKTIONEN → ENGINES    │
        │  2D (Konva) · 3D (three) │   │  raum_geometrien→Heizlast  │
        │  Textur/PBR · glTF→Foto  │   │  Dachfläche→PV/Statik      │
        └──────────────────────────┘   └───────────────────────────┘
```

## 5 · Gewerke-Landkarte

| Gewerk | Wird im Modell zu … | Renderer/Projektion | Status |
|---|---|---|---|
| Grundriss zeichnen | Wall/Opening/Zone-Nodes | 2D + 3D | **fertig** (P0/P1, in Abnahme in ticket) |
| Heizlastberechnung | Projektion `raum_geometrien` | → bestehende Heizlast-Engine (ticket) | Engine da, **Naht offen** |
| Zimmermann / Dachdecker | RoofNode + Dach-Geometrie | 3D + Flächen-/Azimut-Projektion | **Spec da** (Dach-Andock) — nächster Schritt |
| PV-Belegung | Zone `pv_area` auf Dachfläche | 3D + Ertrags-Projektion (PVGIS in ticket) | Node vorgesehen, Modul offen |
| Fensterplanung | Opening-Nodes + Katalog | 2D/3D + U-Wert-Projektion | Teil vorhanden (Öffnungen), Katalog offen |
| Möblierung (Wohnen/Schlafen) | ObjectNode → Katalog-3D-Modell | 3D + Textur | Node-Typ + Katalog **im Schema angelegt**, Assets offen |
| Badplanung | ObjectNode (Sanitär) + Zone | 3D + Textur | Katalog-Erweiterung, offen |
| Küchenplanung | ObjectNode (Küche) + Zone | 3D + Textur | Katalog-Erweiterung, offen |
| Elektro/Leitungen | RouteNode | 3D | Node-Typ vorgesehen, offen |
| Fotorealistisches Bild | — (Ausgabe) | glTF-Export → externe Render-Engine | angedockt, §3 |

**Erkenntnis:** Fast jedes Gewerk hat schon eine Heimat im Schema (Node-Union, Katalog, Materialien
wurden in P0 bewusst so gelegt). Es ist Auffüllen, kein Neubau.

## 6 · Möbel- & Objekt-3D-Katalog (die „Datenbank")

Deine 3D-Möbel-Datenbank ist der bestehende Katalog `hausplaner_catalog_items`:
`representation.model3dUrl` (GLB-Asset), `dimensions` (mm), `placement` (wand/boden/decke/dach),
`spec_ref` (Naht zu Herstellerdaten). Ein `ObjectNode` verweist per `catalogItemId` darauf — das
Modell wird NICHT in die Szene kopiert, nur referenziert (eine Wahrheit). GLB-Assets sind angedockte
Dateien (selbst modelliert, gekauft, oder Hersteller-Downloads), keine Eigenmodellierung im Code.

## 7 · Texturen & Fotorealismus (Stufen)

1. **PBR-Echtzeit (jetzt erreichbar):** `MaterialDefinition` steckt schon im SceneDocument; im
   3D-Renderer (three.js) wird daraus Farbe → Textur-Maps → PBR-Material. „Gut aussehend im Browser."
2. **glTF als Austausch:** Die Szene exportiert nach glTF (Standard). Damit ist sie in jeder
   professionellen Pipeline lesbar.
3. **Foto offline:** glTF → Blender/Cycles, Web-Path-Tracer oder Render-Dienst rechnet das
   fotorealistische Bild. Wir schreiben den Renderer nicht.

## 8 · Erster konkreter Schritt: Fusion Dachplaner → Hausplaner

Die alte Dach-Insel (Prototyp) und der Hausplaner werden EINS: `RoofNode` in dieselbe Szene, die
bewiesene Dach-Mathematik aus dem Prototyp als reine Funktionen (nicht die @ts-nocheck-Klasse). Die
**Dach-Andock-Spec** (`docs/hausplaner/dach-andock-spec.md`) ist der Bauplan. Danach rendert ein
3D-Bild Wände **und** Dach aus einer Wahrheit — und die Dachfläche wird die belastbare Quelle für PV.

## 9 · Governance bei dieser Größe (warum Disziplin = Überleben)

Eine Mehrjahres-Plattform stirbt an Chaos, nicht an fehlender Ambition. Es gelten unverändert:
Planner→Generator→Evaluator mit Rollentrennung; additive DB-Änderungen; eine Schreib-Wahrheit je
Wert; Mehrbenutzer-Schutz (Revision→409 + BearbeitungsSperre); keine zweiten Datenmodelle. Jedes
Gewerk-Modul durchläuft denselben Zyklus wie heute.

## 10 · Reihenfolge (grob, realistisch)

1. **Hausplaner in ticket fertig** (Einstieg an der Objektakte, BearbeitungsSperre, Browser-Abnahme).
2. **Dach-Andock** (Fusion, §8) → ein 3D-Bild Haus+Dach, Dachfläche belastbar.
3. **Heizlast-Naht** (Projektion → bestehende Engine sichtbar am Objekt).
4. **PV-Belegung** auf der Dachfläche.
5. **Textur-/PBR-Schicht** im 3D-Renderer (Echtzeit-Materialien).
6. **Möbel-Katalog + ObjectNode-Werkzeuge** (3D-Modell-Datenbank, Platzieren).
7. **Innenraum-Gewerke** (Bad, Küche, Fenster) als Katalog-Module.
8. **glTF-Export → Fotorealismus** (angedockt).

Reihenfolge ist Vorschlag, nicht Zwang — jede Stufe ist eine eigene Spec mit Stopp bei Yama.

## 11 · Was wir bewusst NICHT bauen (Anti-Scope)

- Keinen eigenen fotorealistischen Renderer / Path-Tracer.
- Keine eigene CAD-/Constraint-Engine von Null (höchstens eine Bibliothek andocken, wenn nötig).
- Kein zweites Datenmodell je Gewerk — alles hängt am SceneDocument.
- Keine Eigenmodellierung von Möbeln im Code — GLB-Assets werden angedockt.

---

*Dieses Zielbild ist lebend. Änderungen daran sind Planner-Entscheidungen und werden hier
nachgeführt, bevor Module gebaut werden, die davon abweichen.*
