# Wiederverwendungsgrenzen

## Aus Ticket/CRM wiederverwenden (bevorzugt)
CRM-Seitenrahmen, Projektkopf (`x-page-head`), Navigation/Sidebar, Tabs, Formulare, Buttons,
Tabellen, Filter, Suchfelder, Modale, Statusanzeigen, Kommentare, Aufgaben, Aktivitäten,
Dokumente, Uploads (`PlanUpload`), Freigaben, Benachrichtigungen, Benutzer-/Verantwortlichkeits-
logik, Rechte-/Org-Bindung (`hasPermission`, `permission:`), Audit-Trails, Fehler-/Ladezustände,
Bearbeitungssperre-Partial, Designsystem-Tokens `--sa-*`.

## Planner-spezifisch neu (nur wo keine Grundlage besteht)
BuildingDocument, CAD-Geometrie, Constraint-Engine, Snapping, 2D-CAD-Canvas, 3D-Viewport,
Transform-Gizmo, Mesh-Generierung, parametrische Bauteile, TGA-Routing, Elektrodiagramme,
PBR-Darstellung, Renderpipeline, Blender-Worker.

## Beispiel
3D-Viewport wird neu entwickelt — aber Projektkopf, Toolbar-Rahmen, Dialoge, Speicherstatus,
Aufgaben, Kommentare und Dokumente stammen aus vorhandenen CRM-/Ticket-Komponenten.

## Fachbedeutung nicht verwässern
Ticket-Status ≠ Planner-Objektstatus · Ticket-Aufgabe ≠ Geometrie-Issue · Ticket-Anhang ≠
3D-Asset · Ticket-Kommentar ≠ CAD-Anmerkung → Adapter/Projektion (R4).
