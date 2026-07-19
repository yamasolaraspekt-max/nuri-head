---
name: ticket-code-reuse
description: Untersucht den bestehenden Ticket-Bereich des Laravel-CRM als primäre Wiederverwendungsquelle für neue Planner-, CAD-, UI-, Workflow-, Dokument-, Aufgaben-, Freigabe-, Kommentar-, Status- und Projektfunktionen. Verwenden, BEVOR neue Planner-Komponenten oder parallele Fachlogik erstellt werden. Verhindert Greenfield-Neuentwicklung, wenn geeigneter Ticket-Code bereits vorhanden ist.
---

# ticket-code-reuse

## Ziel
Nutze den umfangreichen vorhandenen Ticket-/CRM-Code kontrolliert für den neuen webbasierten
2D-/3D-CAD-Planer. Der Ticket-Bereich ist eine produktive Plattform mit UI-Komponenten,
Layoutstrukturen, Workflows, Statusmodellen, Aufgaben, Kommentaren, Dokumenten, Uploads,
Freigaben, Historien, Benachrichtigungen, Projektbezügen, Rechte-/Org-Bindungen, Tests und
Designsystem-Elementen. Behandle diesen Bestand als **primäre Wiederverwendungsquelle**.

## Oberste Regel
**Beginne nicht bei null.** Erstelle keine neue Komponente, keinen neuen Service und keinen
neuen Workflow, bevor geprüft wurde, ob eine passende oder erweiterbare Lösung bereits im
Ticket-Bereich existiert. „Neu bauen ist einfacher" ist keine ausreichende Begründung.

## Trigger
Läuft bei JEDEM Planner-Slice, bevor Produktivcode entsteht. Kein Planner-Slice darf mit
„neue Dateien anlegen" beginnen.

## Arbeitsablauf
1. **Auftragsbezug bestimmen** — welche Funktionen braucht dieser Slice? (Projektkopf, Sidebar,
   Detailpanel, Status, Kommentare, Aufgaben, Upload, Dokumente, Historie, Freigabe,
   Benachrichtigung, Suche, Filter, Katalog, Formular, Tabelle, Modal, Tabs, Karten,
   Workflowstatus, Audit-Trail, Versionierung, Anhänge, Aktivitätsverlauf …)
2. **Ticket-Code gezielt suchen** — mindestens Routen, Controller, Actions, Services, Models,
   Policies, Requests, Resources, Events, Jobs, Notifications, Views, Blade-/Alpine-Komponenten,
   JS/TS, CSS, Tests, Doku, vorhandene Planner-/Playground-Bereiche, gemeinsames Designsystem.
   Nicht nur nach identischen Begriffen suchen, sondern nach **funktional vergleichbaren** Lösungen.
   Startpunkte siehe `references/ticket-component-inventory.md`.
3. **Abhängigkeiten prüfen** — Ticket-Bindung, allgemeine Nutzbarkeit, Extraktionsbedarf,
   Adapter-Option, Mandanten-/Projektbindung, greifende Policies, schützende Tests, weitere Nutzer,
   Gefährdung bestehender Ticket-Funktionen bei Änderung.
4. **Reuse-Klassifizierung** — jeden Kandidaten nach R1–R5 einordnen (siehe
   `references/reuse-classification.md`).
5. **Reuse-Matrix erstellen** — Vorlage `references/reuse-matrix-template.md`.
   **Ohne Reuse-Matrix keine Neuentwicklung beginnen.**

## Reuse-Klassen (Kurz)
- **R1 direkt wiederverwenden** — ohne fachliche Änderung (Button, Modal, Tabs, Statusbadge, Upload, Projektkopf, Aktivitätsanzeige).
- **R2 konfigurierbar erweitern** — additiv (Spalte, Variante, optionaler Planner-Kontext, Filteroption).
- **R3 in gemeinsames Modul extrahieren** — Ticket + Planner brauchen dieselbe Logik; Vertrag erhalten, Ticket-Tests grün, neue gemeinsame Tests, keine Massenverschiebung, nicht mit Featurebau vermischt.
- **R4 Adapter/Projektion** — Ticket-Komponente unverändert; Planner nutzt dünne Integrationsschicht (Planner-Issue → Aufgabenmodell, Planner-Dokument → Dokumentenablage, Planner-Aktivität → Aktivitätssystem).
- **R5 bewusst nicht wiederverwenden** — mit technischer Begründung (fachlich zu eng, Sicherheitsmodell nicht übertragbar, Technik veraltet/instabil, Performance/2D-3D-Echtzeit nicht abbildbar, fehlende Testbarkeit, Änderung gefährdet Ticket unverhältnismäßig).

## UI-Grenzen
Vorhandenes Ticket-Layout + Designsystem sind Ausgangspunkt (Styleguide, `--sa-*`-Tokens,
`x-page-head`, Sidebar, Buttons, Pills, Inputs). Planner-Spezialkomponenten sind erlaubt für:
2D-Canvas, 3D-Viewport, Transform-Gizmo, CAD-Toolbar, Layersteuerung, Objektbaum, Materialbrowser,
technische Diagramme. **Kein zweites Designsystem nur für den Planner.** Für normale CRM-Oberflächen
(Projektkopf, Navigation, Buttons, Dialoge, Formulare, Tabellen, Status, Kommentare, Aufgaben,
Dokumente, Freigaben, Historie) bestehende Ticket-Komponenten nutzen.

## Fachlogik-Grenzen
Ticket-Logik ist nicht automatisch Planner-Fachlogik: ein Ticket-Status ≠ Planner-Objektstatus,
eine Ticket-Aufgabe ≠ Geometrie-Issue, ein Ticket-Anhang ≠ 3D-Asset, ein Ticket-Kommentar ≠
objektspezifische CAD-Anmerkung. Bei unterschiedlicher Fachbedeutung Adapter/Projektion (R4).

## Tests
- R1: bestehende Tests ausführen + Planner-Integration ergänzen.
- R2: Ticket-Regression + neue Variante testen.
- R3: Verhalten vor Extraktion charakterisieren, gemeinsame Modultests, Ticket-Regression, Planner-Integration.
- R4: Mapping, Fremd-Org-/Projektbindung, Seiteneffekt-Freiheit testen.

## Ausgabe / Abschlussbericht
1. untersuchte Ticket-Bereiche, 2. Wiederverwendungskandidaten, 3. Reuse-Matrix,
4. direkt wiederverwendet, 5. erweitert, 6. vorgeschlagene Extraktionen, 7. Adapter,
8. bewusst verworfen (mit Begründung), 9. betroffene Ticket-Tests, 10. Risiken für Ticket,
11. neue Dateien, 12. geänderte bestehende Dateien, 13. Bestätigung: keine unnötige
parallele Implementierung.

## Pflicht-Stopp
War der Auftrag nur eine Reuse-Prüfung: nach der Analyse STOPPEN. Keine automatische Umsetzung.
Kein Commit. Kein Push.
