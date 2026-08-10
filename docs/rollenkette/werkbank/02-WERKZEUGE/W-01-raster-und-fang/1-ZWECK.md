# W-01 · Raster und Fang — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

Der Planer soll Punkte setzen können, die **genau** dort landen, wo sie hingehören — am Endpunkt
einer Wand, auf ihrer Flucht, im rechten Winkel oder im Raster — **ohne millimetergenau zielen zu
müssen**.

## Wann greift der Anwender danach?

In jedem Moment, in dem er einen Punkt setzt oder zieht: Wand zeichnen, Wand verschieben, Öffnung
platzieren. **Er ruft das Werkzeug nicht auf — es liegt unter jedem Klick.**

## Was wäre ohne dieses Werkzeug?

Zwei Wände, die aussehen wie verbunden, sind es um 3 mm nicht. Der Fehler ist **unsichtbar**, bis
eine Fläche nicht schließt oder eine Mengenermittlung falsch rechnet.

## Der Vertrag in einem Satz

**Es gibt genau EINE Fang-Entscheidung, und sie sagt, WAS sie gefangen hat.**

> *Belegt in `resources/planner/hausplaner/geometry/fangKern.ts`, Kopfkommentar: „Eine Wahrheit" und
> „Nie stilles Fangen: `art` benennt, WAS gefangen wurde (oder `keiner`)".*
