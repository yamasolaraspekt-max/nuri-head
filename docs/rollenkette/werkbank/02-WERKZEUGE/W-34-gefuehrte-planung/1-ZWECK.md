# W-34 · Geführte Planung — ZWECK

> **Ablesung, keine Vorgabe.** *Der Code existiert:
> `resources/planner/hausplaner/app/dashboard/fahrschritte.ts` (202 Z.) und
> `app/GuidedView.tsx` (165 Z.). Jede Aussage hier ist an ihm gemessen und trägt ihre Fundstelle.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Planer soll an elf Schritten sehen, wie weit sein Projekt ist — und zwar so, dass jeder grüne
Haken auf einer Angabe im Gebäudemodell beruht und nicht auf einer Behauptung.**

## Wann greift der Anwender danach?

*Beim Öffnen der geführten Planung, und danach bei jedem Schritt.* **Er sieht eine Plakette je
Schritt, darunter die Prüfpunkte, die zu diesem Urteil geführt haben, und einen Hinweis, der die
Zahlen nennt** — *„3 von 4 Geschossen bebaut", „12 Fenster, 5 Türen, 1 Treppe im Modell".*

## Woran merkt er, dass es fehlt?

**Er merkt es nicht am Fehlen — er merkt es am Lügen.** *Und dieser Fall ist eingetreten und
belegt: bis AUF-39 zeigte ein **frisch angelegtes, leeres** Projekt grüne Haken.*

```text
fahrschritte.ts:4-8, woertlich:
  "Was vorher dastand, war eine Notluege, und der Code sagte es selbst: studioDaten.ts trug den
   Vermerk 'Praesentativ — echte Zustands-Ableitung folgt aus dem Modell' und behauptete fuer ein
   frisch angelegtes Projekt 'Bauherr & Adresse ✓', 'Massstab erkannt · 1:50 ✓'.
   Ein leeres Projekt zeigte gruene Haken."
```

> **Die Regel, an der dieser Posten steht oder fällt** — *`fahrschritte.ts:12-15`:*
> **„Was das Modell nicht weiß, wird nicht behauptet."** *Ein Schritt ohne Datengrundlage ist ein
> **leerer** Schritt, kein grüner.*

## Und die Leistung, die man leicht für einen Mangel hält

**Sechs der elf Schritte können heute gar nichts bestätigen** — *weil das Gebäudemodell die nötigen
Angaben nicht führt.* **Statt sie grün zu zeigen, sagt jeder von ihnen, WELCHE Angabe fehlt.**

> *Das ist kein Mangel des Werkzeugs, sondern sein Zweck. Die vollständige Liste steht in
> `7-GRENZEN.md` — sie ist zugleich eine Liste möglicher nächster Posten, die nicht erfunden wurde,
> sondern im Code steht.*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

```text
NICHT  die vier Statusstufen DEFINIEREN     -> das ist W-38 (app/studioDaten.ts, BESCHRIEBEN).
                                               W-34 BENUTZT sie, GuidedView.tsx:4.
NICHT  Rechenpanels bewerten                -> W-37
NICHT  das Gebaeudemodell AENDERN           -> die Ableitung liest nur; bewacht von
                                               fahrschritte.test.ts K2
NICHT  die fehlenden Angaben ERGAENZEN      -> sechs Luecken sind benannt, nicht geschlossen;
                                               jede waere ein eigener Posten
```

**W-34 ist die Ableitung und ihre Ansicht — nicht das Vokabular und nicht das Modell.**
