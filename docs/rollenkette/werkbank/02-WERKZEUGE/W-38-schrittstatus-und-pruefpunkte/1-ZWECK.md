# W-38 · Schritt-Status und Prüfpunkte — ZWECK

> **Dies ist eine ABLESUNG, keine Vorgabe.** *Der Code existiert:
> `resources/planner/hausplaner/app/studioDaten.ts`, 257 Zeilen. Jede Aussage in diesen sieben
> Blättern ist an ihm gemessen und trägt ihre Fundstelle.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Planer soll auf einen Blick sehen, wie weit ein Planungsschritt ist — und zwar mit demselben
Wort an jeder Stelle des Programms.**

## Wann greift der Anwender danach?

*In der geführten Planung, bei jedem Schritt.* **Er sieht eine Plakette am Schritt („Vollständig",
„In Bearbeitung", „Prüfung erforderlich", „Offen") und darunter die einzelnen Prüfpunkte, die zu
diesem Urteil geführt haben.** *Er greift nicht aktiv danach — es ist die Sprache, in der ihm
Fortschritt gemeldet wird.*

## Woran merkt er, dass es fehlt?

**Er merkt es nicht am Fehlen, sondern an der Uneinheitlichkeit.** *Ohne ein gemeinsames
Statusmodell nennt der eine Bereich einen Schritt „fertig", der nächste „abgeschlossen", der dritte
zeigt einen grünen Haken ohne Wort — und der Planer muss raten, ob das dasselbe meint.*

> **Der belegte Fall steht im Code selbst:** *`ok` hieß früher ein Wort aus der Freigabe-Sprache und
> behauptete damit einen **Vorgang**, den es nicht gegeben hatte.* **`studioDaten.ts:245-254`
> hält fest, warum daraus „Vollständig" wurde: der Wert wird aus dem Dokument abgeleitet und heißt
> „alle Prüfpunkte dieses Schrittes sind erfüllt" — niemand hat etwas geprüft und bestätigt.**
> *Genau das ist der Zweck dieses Werkzeugs: **ein Zustand, kein Vorgang**.*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

```text
NICHT  den Status BERECHNEN            -> das tut app/dashboard/fahrschritte.ts  (W-34)
NICHT  den Status ANZEIGEN             -> das tut app/GuidedView.tsx             (W-34)
NICHT  Rechenpanels bewerten           -> das tut app/dashboard/enginePanels.ts  (W-37)
NICHT  Gueltigkeit von Ergebnissen     -> das ist W-40, ein ANDERER Wortschatz
```

**W-38 ist das Vokabular, nicht der Satz.** *Es stellt vier Stufen, vier Datenformen und eine
Beschriftungstabelle bereit. Wer daraus ein Urteil bildet oder es zeichnet, ist ein anderes
Werkzeug.*

> **Die Abhängigkeitsrichtung ist die eines Stufe-6-Werkzeugs:** *W-38 hängt von keinem anderen
> Werkzeug ab — es wird von vielen **benutzt**. `braucht: alle` in der Registerzeile bezeichnet
> diese Richtung und ist keine Vorbedingung.* **Für eine Ablesung ist die Frage ohnehin
> gegenstandslos: abgelesen wird eine Datei, die es gibt.**
