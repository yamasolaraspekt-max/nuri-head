# Reuse-Klassifizierung R1–R5

## R1 – Direkt wiederverwenden
Ohne fachliche Änderung nutzbar. Beispiele: Button, Modal, Tabs, Statusbadge, Uploadkomponente,
Projektkopf (`x-page-head`), Aktivitätsanzeige.

## R2 – Konfigurierbar erweitern
Komponente bleibt bestehen, erhält additive Optionen. Beispiele: zusätzliche Spalte, Variante,
optionaler Planner-Kontext, neue erlaubte Objektart, zusätzliche Filteroption.
Bedingung: rückwärtskompatibel, bestehende Tests bleiben grün.

## R3 – In gemeinsames Modul extrahieren
Ticket und Planner brauchen dieselbe Logik. Voraussetzungen:
1. mindestens zwei Fachbereiche brauchen die Logik,
2. gemeinsame Verantwortung eindeutig benennbar,
3. bestehender Ticket-Vertrag bleibt erhalten,
4. separat testbar,
5. nicht mit einer großen Fachfunktion vermischt.
Vermeiden: pauschale „Shared"-Ordner, unklare Helper-Sammlungen, abstrakte Basisklassen ohne
Nutzen, Massenverschiebungen, gleichzeitiger Refactor + Featurebau.

## R4 – Adapter / Projektion
Ticket-Komponente bleibt unverändert; Planner nutzt Adapter/Mapper/dünne Integrationsschicht.
Beispiele: Planner-Issue → bestehendes Aufgabenmodell; Planner-Dokument → vorhandene
Dokumentenablage; Planner-Aktivität → vorhandenes Aktivitätssystem.

## R5 – Bewusst nicht wiederverwenden (Begründungspflicht)
Zulässige Gründe: fachlich zu eng an Tickets gebunden; Sicherheitsmodell nicht übertragbar;
veraltete/instabile Technik; Performance nicht erfüllt; fehlende Testbarkeit; ungeeignete Lizenz;
2D-/3D-Echtzeit nicht abbildbar; Änderung würde das Ticket-System unverhältnismäßig gefährden.
**„Neu ist einfacher" zählt nicht.**
