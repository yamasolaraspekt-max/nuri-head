# W-22 · Gaube — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

Er setzt eine Gaube oder einen Kamin auf ein **geneigtes** Dach — und das Ding muss **anschließen**.
Nicht schweben, nicht im Dach stecken, nicht über den First ragen.

## Warum das schwerer ist, als es aussieht

Ein Aufbau steht **lotrecht**, die Fläche darunter ist **geneigt**. Jede Kante trifft die Dachhaut in
einer anderen Höhe. **Der Anschluss ist die eigentliche Arbeit, nicht der Körper.**

## Was das Werkzeug dazu mitbringt

Es rechnet nicht nur — **es prüft sich selbst** und gibt eine **Ampel** aus: grün, gelb oder rot,
mit dem verletzten Kriterium im Klartext (`resources/planner/hausplaner/geometry/gaubeGeometrie.ts:409`).

## Der Name ist enger als das Modul

Das Werkzeug heißt „Gaube". Das Modul kann **Gauben, Kamine und die Prüfung**. Der Dateikopf spricht
von **stehenden Dachaufbauten**. *Wer nach „Kamin" sucht, findet dieses Blatt nicht — deshalb steht
der Satz hier.*

## Was es ausdrücklich NICHT ist

> *„**KEINE Dacheindeckung, KEINE Statik, KEINE Schneelast** — nur Lage/Höhe/Anschluss-Geometrie.
> **Realer Tragwerksplaner/Dachdecker bleibt nötig.**"* (`resources/planner/hausplaner/geometry/gaubeGeometrie.ts:28-29`)
