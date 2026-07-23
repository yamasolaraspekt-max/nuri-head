# Werkzeug-Konzept: Verknüpfungen & Einsatz je Funktion (alle 65 + CAD-Ergänzung)

> **Rolle:** Planner. **Stand:** 2026-07-23. **Zweck:** Für JEDE Funktion festlegen: (1) **Verknüpfung**
> — an welches Command / Modell-Feld / welchen Renderer / welche andere Tools+Panels sie andockt;
> (2) **Einsatz/Aktivierung** — wann sie aktiv/deaktiviert ist. Grundlage: Yamas `tools.json` (Aktivierung)
> + unser System (Store/Commands/Renderer/Activation-Engine aus UI-2, Geometrie-Engine aus W-1/W-2).

## Verknüpfungs-Typen (unser System)
- **Command** → typed Command auf `hausplanerStore` (Immer inverse-patch, undo/redo). Die *eine* Schreibwahrheit.
- **Modell-Feld** → `SceneDocument`: `nodes[]` (type/levelId/**visible**/**locked**/transform), `roofs[]`, `levels[]`.
- **Renderer** → 2D (Konva, `HausplanerApp`) und/oder 3D (`szene.ts`). Lesen nur, nie schreiben.
- **State** → `uiState` (activeTool, **selectionIds**, **snapSettings**, activeView, activeWorkspace).
- **Engine** → `fangKern` (Fang), `editierGeometrie` (verschieben/spiegeln), `masskette/bemassung` (messen),
  Dach-Engine (`dachGeometrie`, `dachformVorlagen`, `gaubeGeometrie`, `dachAusschnitt` … W-1/W-2).
- **Activation** → `getToolState()` (Yamas Logik ≈ unsere Engine): `canEdit`, `requiresSelection`,
  `minSelectionCount`, `maxSelectionCount`, `enabledInModes`, + unser **object-state**-Gate (`locked`).

Legende Verdikt: ✅ CAD übernehmen · 🟦 anpassen · ⛔ DTP (nicht bauen, nur Registry-Eintrag „ausgeblendet").

---

## Auswahl
| Tool | Verknüpfung | Einsatz/Aktivierung | |
|---|---|---|:--:|
| selection | State `selectionIds` setzen; liest alle Nodes; kein Command | immer aktiv (Grundwerkzeug) | ✅ |
| direct-selection | State `selectionIds` auf Knoten/Punkt; Command `MOVE_POINT` (Wand-/Polygonpunkt) | aktiv bei ≥1 Auswahl mit editierbarer Kontur | ✅ |

## Navigation & Ansicht (kein Modell — reine Sicht)
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| hand | Viewport-Pan (Konva/three Kamera); kein Command | immer aktiv, auch ohne canEdit | ✅ |
| zoom | Viewport-Zoom + Zoom-zu-Auswahl | immer aktiv | ✅ |
| normal-screen / preview-screen | State `activeView`-Flag „Hilfslinien/Raster aus" | immer; preview auch read-only-Modus | 🟦 |
| search | Objekt-/Tool-Suche über Registry + Nodes | immer (Ctrl+F) | ✅ |
| settings / more | Einstellungen-Panel / Kontextmenü | immer | 🟦 |

## Zeichnen / Formen / Erstellen
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| line | Command `ADD_NODE`(Linie/Hilfslinie); Renderer 2D | aktiv wenn canEdit | ✅ |
| pen | Command `ADD_NODE`(Polylinie/Kontur) über `fangKern`; Wandzug/Raumumriss/Dachkontur | canEdit | ✅ |
| pencil | Freihand→Kontur (vereinfacht) | canEdit | 🟦 |
| rectangle / ellipse / polygon | Command `ADD_NODE`(Form/Raum/Zone); Fang aktiv | canEdit | ✅ |
| rectangle-frame / ellipse-frame / polygon-frame | (DTP: Bildrahmen) → bei uns eher **Raum/Zone-Erstellung** | canEdit | 🟦 |

## Pfad
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| add-anchor / delete-anchor / convert-anchor | Command `EDIT_PATH`(Punkt +/−/Typ) auf selektierter Kontur | aktiv bei Kontur-Auswahl (direct-selection-Kontext) | ✅ |
| smooth / erase-path | Command `EDIT_PATH`(glätten/löschen Teil) | bei Kontur-Auswahl | 🟦 |
| scissors | Command `SPLIT`(Wand/Pfad an Punkt teilen) über `fangKern` | canEdit, Klick auf Kante | ✅ |

## Transformation (Engine `editierGeometrie` + numerisch)
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| free-transform | Command `TRANSFORM`(Move+Rotate+Scale) auf Auswahl | **requiresSelection ≥1**, nicht `locked` | ✅ |
| rotate | Command `ROTATE` (Winkel + reference-point) | Auswahl ≥1, nicht locked | ✅ |
| scale | Command `SCALE` (Faktor, link-proportions) | Auswahl ≥1, nicht locked | ✅ |
| shear | Command `SHEAR` | Auswahl ≥1 | 🟦 |
| reference-point | State „Bezugspunkt" (9-Punkt) für alle Transforms | immer (Modifier) | ✅ |
| link-proportions | State „Seitenverhältnis koppeln" für scale | immer (Modifier) | ✅ |

## Ausrichtung & Verteilung (NEU — fehlt komplett; Yamas Wunsch)
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| align-left/center/right/top/middle/bottom | Command `ALIGN`(Achse+Kante) — neue Rechenlogik über bbox der Auswahl | **min 2 Objekte** | ✅ |
| distribute-horizontal / -vertical | Command `DISTRIBUTE`(gleiche Abstände) | **min 2 (sinnvoll 3)** | ✅ |

## Sichtbarkeit / Sperre / Ebenen (Modell steht schon)
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| **Auge** (visibility-toggle) | Command `SET_VISIBLE`; Modell `node.visible`/`roof.visible`; Renderer blendet schon aus | Auswahl ≥1 ODER Layer-Zeile | ✅ |
| **Schloss** (lock-toggle) | Command `SET_LOCKED`; Modell `node.locked`; Activation-Gate nutzt es | Auswahl ≥1 ODER Layer-Zeile | ✅ |
| layers-panel | liest Nodes/Groups; Zeilen mit Auge+Schloss; Command `REORDER`/`SET_VISIBLE`/`SET_LOCKED` | immer sichtbar (F7) | ✅ |

## Fläche / Region (NEU)
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| marquee (fläche-markieren) | State `selectionIds` (Mehrfach per Rahmen); liest Nodes | canEdit | ✅ |
| fläche-sperren/-freigeben | Command `SET_LOCKED`(Massen) über Marquee-Auswahl | nach Marquee | ✅ |
| fläche-eingrenzen | Command `ADD_NODE`(`zone`/`restricted_area`) + Clip-Bezug | canEdit | ✅ |

## Fang / Magnet & Führung (Engine `fangKern` steht)
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| magnet-toggle | State `snapSettings.snapEnabled`; alle Zeichen-/Transform-Tools lesen es | immer (globaler Schalter) | ✅ |
| fang endpunkt/ortho/raster | Engine `fangKern.fange()`; von line/pen/rect/move genutzt | wenn Magnet an | ✅ |
| fang mitte/schnitt/lot (neu) | `fangKern` erweitern | wenn Magnet an | ✅ |
| hilfslinien / smart-guides / lineal / raster | State `snapSettings`+Guides; Renderer-Overlay | immer umschaltbar | ✅ |

## Messen / Kompass
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| measure | Engine `masskette/bemassung`; Overlay; kein Modell-Schreiben | immer | ✅ |
| **kompass / nordpfeil** (NEU) | liest/schreibt `roof.firstAzimutGrad` (Nord=+y); Command `SET_AZIMUT`; „nach Nord ausrichten" | bei Dach/Projekt-Auswahl | ✅ |

## Format / Stil / Farbe
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| eyedropper | liest Bauart/Material von A, Command `APPLY_STYLE` auf B | Auswahl-Ziel vorhanden | ✅ |
| object-style | Bauteil-Vorlagen/Bauarten (Kataloge existieren) → Command `APPLY_BAUART` | Auswahl ≥1 | ✅ |
| format-container / format-text | Container-/Textformat | 🟦 (Text reduziert) | 🟦 |
| fill/stroke/swap/default | **Material/Linienstil** je Bauteil (nicht Grafikfarbe) | Auswahl | 🟦 |
| gradient/gradient-feather/effects/opacity | Grafikeffekte | ⛔ | ⛔ |

## Panels / Struktur / QS / Zusammenarbeit
| Tool | Verknüpfung | Einsatz | |
|---|---|---|:--:|
| pages-panel | **Geschosse/Levels** (`levels[]`, activeLevel) | immer | 🟦 |
| swatches-panel | Material-/Farbpalette | immer | 🟦 |
| preflight | **Wächter/Validierung** (Zod-Gate, Bauordnung) — existiert konzeptuell | immer | ✅ |
| links-panel | **Produkt-/DB-Verknüpfung** (Bauart↔Katalog) | immer | 🟦 |
| note | Objekt-Kommentar; Command `ADD_NOTE` | canEdit | 🟦 |
| share | Freigeben/Export | 🟦 | 🟦 |

## Layout / Inhalt / Text / Rahmen-DTP — bewusst NICHT bauen
| Tool | | |
|---|---|:--:|
| page, gap | Seitenlayout/Abstand — page→Geschosse (🟦), gap optional | 🟦 |
| content-collector, content-placer | Layout-Baustein-Wiederverwendung | ⛔ |
| type, text-wrap, format-text | Textsatz | ⛔/🟦 (nur einfache Beschriftung 🟦) |
| libraries-panel | CC Libraries | ⛔ |

---

## Antwort auf „sind die Tools im Dashboard aktiv?"
- **Deine Paket-Demo (`demo/index.html`):** ja — alle 65 mit Tooltip/Filter/Aktivierung lauffähig, anklickbar.
- **Echter Hausplaner:** NEIN — aktiv sind heute nur `auswahl/wand/fenster/tür/dach/treppe`. Die 65 sind
  noch **nicht verdrahtet**. Erst der UI-Slice (Integrationsspec) macht sie im Hausplaner aktiv/ausprobierbar:
  Registry-Merge + React-`ToolButton/Tooltip/Dashboard` + Command-Verknüpfung je Tool wie oben.

## Was zuerst „echt anfassbar" wird (kausal)
Stufe A (Command+Engine stehen): **Magnet-Toggle, Auge, Schloss, Verschieben/Spiegeln, Messen, Zoom/Pan** —
diese lassen sich mit je einem Command sofort aktiv schalten. Danach Ausrichten/Verteilen, Marquee,
Drehen/Skalieren, Kompass, Layer-Panel (Stufe B), dann Pfad/Knoten/Pen/Pipette (Stufe C).
