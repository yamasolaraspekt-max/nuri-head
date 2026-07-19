---
name: planner-slice-orchestrator
description: Einstiegs-Orchestrator für JEDEN Planner-Slice (2D/3D-CAD-Planer im Laravel-CRM). Erzwingt die Reihenfolge Bedarf → Ticket-Reuse-Prüfung → R1/R2/Adapter/Extraktion → erst dann Neuentwicklung. Kein Slice darf mit „neue Dateien anlegen" beginnen. Bindet den Governance-Zyklus (Planner→Generator→Evaluator) ein.
---

# planner-slice-orchestrator

## Ziel
Jeden Planner-Slice kontrolliert starten: erst Wiederverwendung prüfen, dann nur das nachweislich
Fehlende (CAD-Spezifik) neu bauen. Rollen-Trennung des Governance-Zyklus einhalten.

## Trigger
Beginn jedes Planner-/CAD-/UI-/Workflow-/Dokument-Slices.

## Pflicht-Reihenfolge (kein Slice startet mit „neue Dateien anlegen")
1. **Was wird benötigt?** — Funktionsliste des Slices.
2. **Was existiert im Ticket-Bereich?** — Skill `ticket-code-reuse` ausführen (Reuse-Matrix).
3. **Direkt wiederverwenden (R1)?**
4. **Additiv erweitern (R2)?**
5. **Adapter/Projektion (R4)?**
6. **Gemeinsam extrahieren (R3)?**
7. **Erst danach:** Was muss wirklich neu (CAD-spezifisch) gebaut werden?

## Governance-Zyklus (verbindlich getrennt)
- **Planner** — Spec/Fahrplan, KEIN Code. Enthält die Reuse-Matrix des Slices.
- **Generator** — baut additiv gemäß Spec, verifiziert lokal (tsc/Tests/Build).
- **Evaluator** — unabhängig, führt Tests aus, Gegen-Beweis, Urteil grün/rot.
Kein Rollenwechsel innerhalb eines Schritts.

## Scope
Koordination eines Slices: Bedarf, Reuse-Gate, Aufteilung neu vs. wiederverwendet, Übergabe an
Generator/Evaluator.

## Nicht-Scope
Keine Fachimplementierung im Orchestrator selbst; keine Migration; keine Ticket-Umbauten.

## Referenzen
- `ticket-code-reuse` (Reuse-Gate, Matrix)
- `planner-architecture` (Zielbild)
- `building-document`, `laravel-planner-integration` (bei neuen Fachteilen)

## Ausgabe
Slice-Plan: Bedarf, Reuse-Matrix-Verweis, R1/R2/R3/R4-Entscheidungen, echte Neubau-Teile,
Test-/Abnahmekriterien, Risiken.

## Pflicht-Stopp
Nach dem Plan stoppen, wenn nur Planung beauftragt war. Kein Commit. Kein Push.
Keine automatische Fortsetzung in die Umsetzung.
