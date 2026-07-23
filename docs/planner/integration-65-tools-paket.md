# Planner-Integrationsspec — Yamas 65-Werkzeug-Paket → Hausplaner

> **Rolle:** Planner. **Stand:** 2026-07-23. **Input:** Yamas Paket (65 SVG-Icons, `tools.json`,
> `types/tool.ts`, `toolActivation.ts`, 3 Vue-Komponenten, HTML-Demo). **Ziel:** faithful integrieren,
> aber als EINE Wahrheit im React-Hausplaner, gefiltert auf CAD und erweitert um Bau-Werkzeuge.

## 1. Was direkt wiederverwendbar ist (framework-neutral) ✅
- **`tools.json`** — 65-Tool-Registry (id/label/meaning/functionDescription/usageArea/shortcut/group/
  requiresSelection/minSelectionCount/maxSelectionCount/enabledInModes/tooltip). Reine Daten.
- **`types/tool.ts`** — `ToolDefinition` + `ToolContext`. Reine Typen.
- **`toolActivation.ts`** — `getToolState()` mit Deaktivierungsgründen. Reines TS.
- **`icons/*.svg`** — 65 Line-Icons.

## 2. Was NICHT direkt läuft — Framework-Bruch 🟦
Unser Hausplaner ist **React 19 + TS (Konva/three)**; das Paket ist **Vue 3**. Die 3 `.vue`-Komponenten
(`ToolButton`, `ToolTooltip`, `ToolDashboard`) **laufen bei uns nicht** — sie werden als **React-Komponenten
nachgebaut** (Markup/Logik sind eine klare Vorlage; Aufwand klein). Daten/Typen/Aktivierung/Icons wandern
1:1, die Vue-Views werden re-implementiert.

## 3. Eine Wahrheit — mit UI-2-Registry versöhnen (kein Doppel-Register)
UI-2 hat bereits `app/tools/{toolRegistry,toolTypes,activation,toolContext}.ts` (React) mit typisierten
Aktivierungsregeln inkl. Objektzustand (`locked`). Yamas Registry ist reicher in **Metadaten**
(meaning/usageArea/tooltip/group), aber einfacher in der Aktivierung. **Zusammenführen:**
- `ToolDefinition` (unser) um Felder **meaning, usageArea, tooltip, group** erweitern (additiv).
- Yamas 65 Tools als Registry-**Inhalt** übernehmen; unsere **Activation-Engine** behalten (mächtiger:
  Objektzustand-Gates). Yamas `requiresSelection/minSelectionCount/maxSelectionCount/enabledInModes`
  als unsere Regeln ausdrücken (1:1 abbildbar).
- Ergebnis: **eine** Registry, ein Aktivierungspfad, Yamas Metadaten + unser Regel-Engine.

## 4. Filtern — nicht alle 65 gehören in einen Bauplaner ⛔
Laut `docs/planner/indesign-werkzeug-vollkatalog.md` sind mehrere der 65 reine DTP/Druck:
`content-collector`, `content-placer`, `gradient`, `gradient-feather`, `text-wrap`, `format-text`,
`effects`, `opacity`, `libraries-panel`, `links-panel` (Publishing), `share`. Diese werden **nicht
registriert** (oder als „ausgeblendet/nicht relevant" markiert), damit das Dashboard nicht zumüllt.
Übernommen wird die CAD-relevante Mehrheit (Auswahl/Transform/Ausrichtung/Navigation/Messen/Panels/System).

## 5. Erweitern — die Bau-Werkzeuge, die InDesign nicht hat (Yamas eigener Hinweis)
Additiv als eigene Gruppen: **Architektur** (Wand/Raum/Geschoss), **Dach** (Dachform, Gaube, Dachfenster,
Neigung, First — jetzt durch W-1/W-2-Engine gedeckt!), **Fenster/Tür** (Bauarten), **Treppe**,
**TGA** (Heizkörper/FBH/Verteiler), **Elektro**. Plus **Kompass/Nordpfeil** (Azimut) — kein InDesign-Tool,
für PV/Verschattung Pflicht.

## 6. Umsetzung (eigener UI-Slice, Planner→Generator→Evaluator)
1. **Assets stagen:** `icons/*.svg`, `tools.json` → ins Repo (z. B. `resources/planner/hausplaner/tools/`),
   Typen/Aktivierung in die UI-2-Dateien mergen.
2. **React-Komponenten:** `ToolButton`, `ToolTooltip`, `ToolDashboard` als React nachbauen (Vue-Vorlage).
3. **Registry-Merge:** `ToolDefinition` erweitern, 65 Tools laden, DTP-Filter, unsere Activation-Engine.
4. **Verdrahten:** Dashboard konsumiert die Registry; bestehende Werkzeuge (`auswahl/wand/fenster/tuer/
   dach/treppe`) auf die neuen IDs mappen (keine zweite Wahrheit).
5. **Barrierefrei:** Tooltip = Icon **und** Text; Deaktivierungsgrund sichtbar (Yamas Muster passt).
Abnahme: Dashboard rendert alle registrierten Tools mit Tooltip; deaktivierte liefern Grund; Filter/Suche
funktioniert; keine DTP-Tools sichtbar; `tsc`+`test` grün.

**Guardrails:** additiv, kein Schema-Bruch; auf `auto/`-Branch; kein Push/Deploy. Rollentrennung.
**Lizenz-Hinweis (Yamas Angabe bestätigt):** Icons sind eigen gezeichnet, keine Adobe-Originale — ok.
