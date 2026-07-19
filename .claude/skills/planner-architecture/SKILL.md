---
name: planner-architecture
description: Zielarchitektur des Planners im bestehenden Laravel-CRM. Ordnet vorhandenen Ticket-Code ein (gemeinsame CRM-Shell, Projektkontext, Dokumente, Aufgaben, Kommentare, Status/Aktivitäten, Rechte/Org, Designsystem) und trennt das neue Planner-Fachmodul (BuildingDocument, CAD-Geometrie, 2D/3D-Renderer, TGA, Elektro, Rendering) über eine Integrationsschicht (Adapter, Projektionen, Events, gemeinsame Services).
---

# planner-architecture

## Ziel
Eine Architektur, die CRM-Bestand wiederverwendet und Planner-Spezifik sauber trennt.

## Zielbild
```
Bestehendes Ticket-/CRM-System
├── gemeinsame CRM-Shell (Layout, x-page-head, Sidebar, Designsystem --sa-*)
├── Projektkontext (LeadAlternativeAdd / alternative_id)
├── Dokumente / Uploads (PlanUpload, Dokumentenablage)
├── Aufgaben / Kommentare / Status / Aktivitäten  (ticketweit; per Adapter)
├── Rechte / Organisation (User::hasPermission, permission:Item,action)
└── gemeinsames Designsystem (Styleguide)

Neues Planner-Fachmodul
├── BuildingDocument (SceneDocument, mm-Ganzzahl, versioniert)
├── CAD-Geometrie / Constraint / Snapping
├── 2D-Renderer (Konva)  ·  3D-Renderer (three)
├── TGA · Elektro · Rendering
└── Objektkatalog

Integrationsschicht
├── Adapter (Planner→Ticket-Fachmodelle)
├── Projektionen (Szene→Heizlast/Geometrie)
├── Events
└── gemeinsame Services (kontrolliert extrahiert, R3)
```

## Regeln
- CRM-Shell + Designsystem wiederverwenden; kein zweites Designsystem.
- Planner-Spezialkomponenten nur für CAD (Canvas, Viewport, Gizmo, Layer, Objektbaum, Material).
- Fachbedeutung nicht verwässern: Ticket-Status ≠ Objektstatus, Ticket-Aufgabe ≠ Geometrie-Issue → Adapter.
- Additiv-only an der DB (Ticket ist live).

## Ausgabe
Architektur-Einordnung des Slices + betroffene Schichten + Integrationspunkte.
Schreibt/aktualisiert `docs/planner/claude-skill-architecture.md`.

## Pflicht-Stopp
Planung/Beschreibung; kein Produktivcode. Kein Commit. Kein Push.
