# Ticket-Reuse-Prinzipien

Der Ticket-Bereich ist Fundament, nicht Altlast. Neu gebaut werden nur die CAD-spezifischen
Teile; Layout, Workflow, Rechte, Dokumente, Aufgaben und Zusammenarbeit kommen möglichst aus
dem vorhandenen Ticket-System.

## Grundsätze
- Bestehender Ticket-Code wird vor jeder Neuentwicklung geprüft.
- Kein vollständiger Neubau des Planner-Layouts.
- Kein zweites CRM-Designsystem.
- Keine zweite Aufgaben-, Kommentar-, Dokumenten-, Upload-, Freigabe- oder Aktivitätslogik.
- Keine zweite Benutzer-/Rechte- oder Organisations-/Projektbindung.
- Planner-Speziallogik bleibt fachlich getrennt.
- Gemeinsame Logik wird kontrolliert extrahiert (R3), nicht kopiert.
- Ticket-spezifische Logik wird nicht durch übermäßige Verallgemeinerung beschädigt.
- Adapter (R4) sind zulässig und häufig besser als riskante Großrefactorings.
- Bestehende Ticket-Tests bleiben verbindlich.

## Warum
Die Greenfield-Insel (eigene Inline-Styles im Hausplaner) ist der Grund, warum der Planner
„nicht nach CRM" aussieht und sich fremd bedient. Wiederverwendung des Designsystems und der
CRM-Komponenten löst Konsistenz, CI-Treue und Wartbarkeit an der Wurzel.
