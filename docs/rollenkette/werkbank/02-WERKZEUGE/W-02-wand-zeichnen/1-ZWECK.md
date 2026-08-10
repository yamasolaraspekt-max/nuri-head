# W-02 · Wand zeichnen — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

Der Planer soll aus zwei Punkten eine **Wand mit Dicke, Höhe und Seiten** bekommen — und an jeder
Ecke einen **sauberen Anschluss**, ohne die Ecken selbst konstruieren zu müssen.

## Wann greift der Anwender danach?

Beim Aufziehen des Grundrisses — dem ersten Schritt jedes Projekts. **Alles Weitere hängt daran:**
Öffnungen, Räume, Decken, Mengen.

## Was wäre ohne dieses Werkzeug?

Wände wären Striche ohne Stärke. **Ohne Wandfläche gibt es keine Mengenermittlung** — und der
Befund, der `wandFlaeche.ts` ausgelöst hat, sagt es wörtlich: *„Die Öffnungen liegen im Modell, aber
niemand zieht sie von einer Wandfläche ab, weil es keine Wandfläche gibt."*

## Die zwei Verträge in je einem Satz

1. **mm-Integer-Welt** — Wände leben in ganzzahligen Millimetern (`istGanzzahlig`, `resources/planner/hausplaner/geometry/wallGeometry.ts:53`).
2. **Entweder Mengen oder Meldungen, nie beides und nie halb** (`resources/planner/hausplaner/geometry/wandFlaeche.ts:96`).
