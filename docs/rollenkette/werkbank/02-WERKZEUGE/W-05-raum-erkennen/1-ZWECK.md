# W-05 · Raum erkennen — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Keines, das er selbst anstößt** — und genau das ist der Zweck. Er zeichnet Wände; die Räume
dazwischen **entstehen von allein**. Fläche und Volumen stehen da, ohne dass jemand einen Raum
umrandet hat.

## Was wäre ohne dieses Werkzeug?

Jeder Raum müsste von Hand nachgezogen werden — und bei jeder verschobenen Wand noch einmal.
**Räume wären Zeichnung statt Folge.**

## Woran der Anwender es merkt

Er zieht eine Wand, und der geschlossene Bereich ist plötzlich ein Raum mit Zahlen.
*Er merkt es daran, dass etwas da ist, das er nicht angelegt hat.*

## Die Zusicherung, die dieses Werkzeug gibt

> *„jede Halbkante gehört zu genau einer Fläche ⇒ **KEINE Endlosschleife**, auch nicht bei offenen
> Wandzügen"* (`resources/planner/hausplaner/geometry/roomDetection.ts:9-12`)

**Ein Werkzeug, das automatisch läuft, darf nicht hängenbleiben.** *Es hat keinen Abbrechen-Knopf,
weil es keinen Start-Knopf hat.*
