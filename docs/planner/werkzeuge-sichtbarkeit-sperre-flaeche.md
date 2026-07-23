# Planner-Lückenspec — Sichtbarkeit (Auge), Sperre (Schloss), Flächen-Werkzeuge

> **Rolle:** Planner. **Stand:** 2026-07-23. **Anlass:** Yama vermisst im Hausplaner/Dachplaner die
> Werkzeuge: Ebenen ein-/ausblenden (Auge), sperren/freigeben (Schloss), Fläche markieren/sperren/
> freigeben/eingrenzen. Dies ordnet ein, was schon steht und was fehlt — als konkrete Bau-Vorgabe.

## 1. Was bereits steht (Fundament)

- **Datenmodell:** jeder Node **und jedes Dach** trägt `visible` und `locked` (persistiert). ConfigWizard
  setzt `visible:true, locked:false`. Der 3D-Renderer `szene.ts` blendet `visible === false` bereits aus
  (Nodes UND `roofs`). → Auge braucht KEINE Modelländerung.
- **Activation-Engine:** `locked` ist bereits Sperre — `toolRegistry` blockt Löschen gesperrter Objekte;
  `toolTypes`/`toolContext` definieren Auswahlzustände `locked/released/foreign`. → Schloss braucht KEINE
  neue Gate-Logik, nur ein Setz-Werkzeug.
- **Konzept:** im Tool-Dashboard-Bericht vorgesehen — F1 `visibilityState`, F6 Selektionsmodell
  „Isolieren/Sperren/Ausblenden", UI-5 (Sperren), UI-6 (Sichtbarkeit je Workspace), UI-8/§33
  (Sichtbarkeit/Layer), §21 (Rechte/Sperre/Freigabe im UI = „fehlt, geplant").

## 2. Was fehlt (zu bauen)

| # | Werkzeug | Fehlt | Modell/Logik da? | Aufwand |
|--:|---|---|---|---|
| 1 | **Auge** — Objekt/Gruppe ein-/ausblenden | Bedienelement + Command | ja (`visible` + Renderer) | **klein** |
| 2 | **Schloss** — sperren/freigeben | Bedienelement + Command | ja (`locked` + Activation) | **klein** |
| 3 | **Ebenen-/Layer-Panel** (Auge+Schloss je Zeile, Photoshop-artig) | ganzes Panel | teils (Felder da; Baum/Gruppen fehlen) | **mittel** |
| 4 | **Fläche markieren** (Marquee/Rubber-Band-Mehrfachauswahl) | Werkzeug + Selektions-Logik | nein (F6/§7 fehlt) | **mittel** |
| 5 | **Fläche sperren / freigeben** (Region auf einmal) | Werkzeug (Massen-`locked`) | Feld ja, Massenaktion nein | **klein–mittel** |
| 6 | **Fläche eingrenzen** (auf Region beschränken/clippen) | Werkzeug + Bereichs-Konzept | teils (`zone`/`restricted_area` nutzbar) | **mittel–groß** |
| 7 | Dieselben im **Dachplaner** (Dächer/Dachflächen) | Auge/Schloss-Controls | `roof.visible` ja | **klein** |

## 3. Konkrete Bau-Vorgabe

**Commands (Modellwahrheit `hausplanerStore`, typed + inverse-patch):**
- `SET_NODE_VISIBLE { ids[], visible }` · `SET_NODE_LOCKED { ids[], locked }` (Massen-fähig → deckt 5 mit ab).
- Roofs analog (`SET_ROOF_VISIBLE/LOCKED`) oder generisch über eine gemeinsame Sichtbar-/Sperr-Schnittstelle.

**Werkzeuge (Registry §22, in die Activation-Engine):**
- `sichtbarkeit-toggle` (Auge) und `sperre-toggle` (Schloss) — aktiv, wenn ≥1 Objekt selektiert; Schloss
  liefert bei gesperrt den Grund im Tooltip (kein stsummes Deaktivieren).
- `flaeche-markieren` (Marquee) — braucht zuerst F6 Selektionsmodell (UI-5).
- `flaeche-eingrenzen` — als Bereichs-Werkzeug auf `zone` (`restricted_area`) aufsetzen.

**UI-Ebenen:** Auge/Schloss erscheinen (a) als Zeilen-Icons im neuen **Ebenen-/Layer-Panel** (UI-8/§33)
und (b) kontextuell in der Optionsleiste bei Selektion (UI-5). Barrierefrei: Zustand nie nur über Icon —
Auge/Schloss immer Icon **und** Tooltip/aria-Label (ux-design: Status nicht nur Farbe/Form).

## 4. Einordnung in den Fahrplan (Reihenfolge, kausal)

1. **Auge + Schloss zuerst** (klein, Fundament steht): Commands `SET_*_VISIBLE/LOCKED` + Toolbar-Toggles +
   Icons im Eigenschaftenpanel. Wirkt sofort in 2D/3D (Renderer respektiert `visible` schon).
2. **Ebenen-/Layer-Panel** (UI-8/§33): Liste/Baum je Objekt/Gruppe mit Auge+Schloss je Zeile.
3. **Fläche markieren** (UI-5 Selektionsmodell/F6): Marquee → Mehrfachauswahl → dann Massen-Sperre/-Sichtbar.
4. **Fläche eingrenzen** (Bereich/`zone`): zuletzt, weil Bereichs-Konzept am meisten trägt.

**Guardrails:** additiv, keine Änderung an der persistierten `roofType`-Enum; `visible`/`locked` sind bereits
im Schema — Commands nutzen sie, kein 422. Jede Stufe eigener Planner→Generator→Evaluator-Zyklus.
