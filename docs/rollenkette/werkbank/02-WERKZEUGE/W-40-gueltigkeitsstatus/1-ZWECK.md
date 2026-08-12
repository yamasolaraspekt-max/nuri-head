# W-40 · Gültigkeitsstatus — ZWECK

> **ABLESUNG MIT EINER ERWEITERUNG** *(Yamas Einordnung vom 12.08., W-40/1).* **Drei der vier Stufen
> sind gebaut** — *`checked`, `approved`, `outdated` in `geometry/configuratorPackage.ts:26`, samt
> Übergangstabelle und Tests.* **Einzig `blocked` fehlt: 0 Treffer auf der ganzen Insel.**
>
> **BERICHTIGT, und die alte Fassung bleibt lesbar.** *Hier stand:* **„VORGABE, keine Ablesung. Für
> `SchrittStatus` gibt es die drei Stufen nicht. Dieses Blatt gibt vor, was gebaut werden soll — wie
> W-15, W-23 und W-27."** *Der Nebensatz über `SchrittStatus` stimmt bis heute; falsch war der
> Schluss davon auf die ganze Insel.* **Und der Nachsatz von damals — „eine Gültigkeitsachse
> existiert im Bestand bereits an anderer Stelle" — stand die ganze Zeit direkt darunter.** *Ich habe
> ihn als Warnung geführt statt als Widerlegung der eigenen Überschrift.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Planer soll unterscheiden können, ob etwas *gerechnet* ist oder ob er es *bestätigt* hat — und
er soll sehen, wenn eine spätere Änderung ein bestätigtes Ergebnis ungültig gemacht hat.**

## Der tragende Punkt: ZWEI ACHSEN, nicht eine längere Liste

**Wörtlich aus der Quelle** — `docs/BERICHT-PROZESSEBENE-DREI-FRAGEN.md:130-132`, *am Bau-Stand
nachgemessen und nicht aus dem Auftragsblatt übernommen:*

```text
Die vier vorhandenen Stufen beschreiben FORTSCHRITT;
die drei fehlenden beschreiben GUELTIGKEIT.
Das sind zwei Achsen, nicht eine laengere Liste.
```

> **Ohne diesen Satz baut die nächste Rolle die drei Stufen in `SchrittStatus` hinein** — *und
> erzeugt genau die zweite Wahrheit, die der Wächter „keine verwaisten zweiten Wahrheiten"
> verbietet.* **Es sind zwei Achsen und keine zwei Wahrheiten:**

```text
FORTSCHRITT   am SCHRITT   open · prog · warn · ok        W-38, gebaut
GUELTIGKEIT   am PAKET     checked · approved · outdated  W-40, GEBAUT
                           blocked                        W-40, die EINE Erweiterung

Ein Schritt kann ok sein — gerechnet, alle Pruefpunkte erfuellt —
und das PAKET ist trotzdem nicht approved: der Nutzer hat es nicht bestaetigt.
```

**BERICHTIGT (W-40/1).** *Hier stand `GUELTIGKEIT (W-40, Vorgabe) confirmed · outdated · blocked` und
der Satz endete mit* **„und trotzdem NICHT confirmed"** *— beides an DEMSELBEN Träger.* **Die zwei
Achsen liegen an zwei verschiedenen Gegenständen, und die Namen sind nicht meine, sondern die des
gebauten Codes.**

**Damit ist eine Frage beantwortet, die W-38s eigenes `7-GRENZEN` offen gelassen hat.** *Dort steht,
es stehe im Raum, ob W-40 ein zweites Statussystem einführt; die Antwort der Quelle ist: nein, es
ist die zweite **Achse**.*

## Wann greift der Anwender danach?

*In dem Moment, in dem ein Ergebnis von einer Bestätigung abhängt.* **Der belegte Fall ist L-9:**

> *„`confirmed` trennt ‚gerechnet' von ‚vom Nutzer bestätigt' — ohne sie kann L-9 (PV erst nach
> **bestätigter** Dachgeometrie) nicht geprüft werden."* — Quelle `:127-129`

## Woran merkt er, dass es fehlt?

**Er merkt es nicht — und das ist der Punkt.** *Eine Regel, die niemand belegen kann, wirkt nicht.*

```text
L-9 gilt:      PV erst nach BESTAETIGTER Dachgeometrie.
L-9 messbar:   NEIN — es gibt keine Stufe, die Bestaetigung von Berechnung trennt.
```

> **Dieselbe Klasse wie E1 vor A-21:** *eine Anordnung, die im Gebrauch war und nirgends verankert —
> sie galt und konnte nicht gemessen werden.* **Deshalb trägt dieser Auftrag `P1` und nicht `P2`.**

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

```text
NICHT  der BAU                       kein Produktivcode; studioDaten.ts bleibt unberuehrt,
                                     W-38 ist BETRIEBSBESTAETIGT
NICHT  die Invalidierungs-MECHANIK   dass outdated PROPAGIERT, gehoert W-41.
                                     W-40 sagt nur, DASS es den Zustand gibt.
NICHT  die PV-Belegung selbst        W-31. W-40 liefert die BEDINGUNG, nicht die Auswertung.
NICHT  eine zweite Uebergangstabelle  im Bestand existiert eine — siehe 7-GRENZEN
```

**W-40 sagt, welche Stufen es gibt und was jede bedeutet.** *Alles Weitere gehört anderen
Werkzeugen — und ein Teil davon ist bereits gebaut.*
