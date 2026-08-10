# W · wand zeichnen — GRENZEN

> **Dieses Blatt ist Pflicht.**
> Der teuerste Fehler des Projekts bisher: ein Dach, das bei nicht-rechteckiger
> Kontur unsichtbar verschwand statt eine Absage zu geben. Die Domäne verweigerte
> korrekt — der Renderer schluckte die Absage mit `catch { continue; }`.
> **Ein Werkzeug ohne benannte Grenze baut genau diesen Fehler wieder ein.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| **Wand der Länge 0** | `wandBaender()` überspringt sie (`wallGeometry.ts:159-160`, `continue`) | kein Band — die Wand erscheint nicht |
| **Spitze Ecken verschneiden** | F-004 ist im Code nicht angebunden; die Sammlung nennt es für Winkel < 15° | der Anschluss, den `wandBaender()` liefert — ohne Verschneidung |
| **Mengen trotz Fehler liefern** | `WandFlaecheErgebnis` hat zwei Zweige, nie beide | **Meldungen statt Zahlen** — nie halbe Zahlen |
| **Schichtaufbau rechnen** | `wandaufbau.ts` ist **Ausschluss** dieses Werkzeugs | nichts — Aufbau gehört in ein eigenes Blatt |
| **Linienbauteile** | `linienBauteile.ts` ist **Ausschluss** | nichts — eigenes Blatt |
| **Nicht-ganzzahlige Punkte annehmen** | mm-Integer-Welt; `istGanzzahlig()` prüft (`wallGeometry.ts:53`) | die Invariante schlägt an, bevor gespeichert wird |
| **Azimut der Wandachse liefern** | gemessen wird die **Normale** (Spec ▲K2) | einen um 90° anderen Wert — **absichtlich** |

## Der teuerste Fehler, gegen den dieses Blatt schützt

**Halbe Auswertung.** Ein Ergebnis, das Zahlen **und** Zweifel gleichzeitig trägt, wird an der ersten
Aufrufstelle halbiert: *die Zahlen nimmt man, die Meldungen übersieht man.* **Deshalb sind es zwei
Zweige und nicht ein Objekt mit einem Warnungsfeld.**
