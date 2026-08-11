# W-22 · Gaube — PRÜFUNG

## Das Werkzeug prüft sich selbst — und genau das ist zu prüfen

`pruefeAufbau()` ist ein Drittel des Moduls. **Eine Selbstprüfung, die nie rot wird, ist schlimmer als
keine.** Zu belegen ist deshalb nicht, dass sie läuft, sondern **dass jede Stufe erreichbar ist**.

| zu belegen | wie |
|---|---|
| **rot** ist erreichbar | ein kritisches Kriterium verletzen (AK1, AK2, AK3 oder AK5) |
| **gelb** ist erreichbar | **AK4 verletzen** — es ist absichtlich *nicht* kritisch |
| **gelb** auch ohne verletztes AK | `geo.feasible === false` setzen |
| **grün** | alles ok |

*Die Vorfahrt steht in einer einzigen Zeile:* `resources/planner/hausplaner/geometry/gaubeGeometrie.ts:491`.

## Der Prüfpunkt, den man leicht übersieht

**Die Höhe wird geklemmt, nicht abgelehnt** (Entwässerungsschranke). Eine Prüfung, die nur „kommt ein
Ergebnis?" fragt, sieht davon nichts. *Zu prüfen ist, ob das geklemmte `h` auch **gemeldet** wird —
sonst bekommt der Anwender eine andere Gaube, als er eingegeben hat, ohne es zu erfahren.*

## Was ich NICHT geprüft habe

**Ob das Klemmen der Höhe nach außen sichtbar ist.** Ich habe die Kopplung im Dateikopf gelesen und
die Ampel-Logik gemessen — **nicht**, ob ein geklemmtes `h` im `PruefBefund` auftaucht.
*Als Frage notiert, nicht als Zusage.*
