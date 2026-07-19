---
name: planner-repository-audit
description: Read-only-Bestandsaufnahme von Planner-Code UND dem gesamten relevanten Ticket-Bestand (Oberfläche, Workflows, Aufgaben/Issues, Kommentare, Dokumente, Uploads, Projektbezug, Statusmodelle, Freigaben, Aktivitäten, Designsystem, Tests). Liefert das Inventar als Grundlage für die Reuse-Matrix. Ändert nichts.
---

# planner-repository-audit

## Ziel
Vollständiges, reales Inventar erstellen — Planner + Ticket — als Faktenbasis für Reuse-Entscheidungen.

## Trigger
Vor größeren Slices, bei Architektur-Fragen, zur Aktualisierung des Komponenten-Inventars.

## Scope (read-only)
Backend: Routen, Controller, Actions, Services, Models, Requests, Resources, Policies,
Middleware, Events, Jobs, Notifications, Upload-/Dokument-/Aktivitäts-/Status-/Aufgaben-/
Kommentarlogik, Projekt-/Org-Bindung.
Frontend: Blade-Layouts/-Komponenten, Alpine, JS/TS, CSS, Designsystem, Projektkopf, Sidebar,
Tabs, Modale, Formulare, Tabellen, Karten, Filter, Suchfelder, Status-, Upload-, Dokument-,
Aktivitäts-, Fehler-, Lade-, Leerzustände.
Tests: Controller-/Feature-/Unit-/View-/Policy-/Fremd-Org-/Projektzugriffs-/Upload-Tests,
Factory-Muster, Regressionsgruppen.
Weiteres: vorhandene Planner-Prototypen, Playground, 2D/3D, Geometrie, Objektkataloge,
Heizlast/WP/PV, Exporte, Stücklisten, Montageplanung.

## Nicht-Scope
Keine Änderung, kein Refactor, kein Commit.

## Vorgehen
Gezielt suchen (nicht nur nach „Planner"): funktional vergleichbare Bausteine an Deals, Leads,
Objekten, Angeboten. Jeden Fund mit Pfad + Verantwortung + Abhängigkeiten + Tests + Bindung notieren.

## Ausgabe
Reuse-Inventar (Tabelle): Komponente/Funktion · Pfad · fachliche Verantwortung · Abhängigkeiten ·
aktuelle Nutzer · Tests · Mandanten-/Projektbindung · mögliche Planner-Nutzung · Änderungsrisiko.
Schreibt/aktualisiert `docs/planner/ticket-code-inventory.md`.

## Pflicht-Stopp
Nach dem Inventar stoppen. Keine automatische Umsetzung. Kein Commit. Kein Push.
