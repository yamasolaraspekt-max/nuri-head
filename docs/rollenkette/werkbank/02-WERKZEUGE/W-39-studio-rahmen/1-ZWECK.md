# W-39 · Studio-Rahmen — ZWECK

> **Ablesung, keine Vorgabe.** *Der Code existiert:
> `resources/planner/hausplaner/app/HausplanerStudio.tsx`, 159 Zeilen. Jede Aussage hier ist an ihm
> gemessen und trägt ihre Fundstelle.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Der Planer soll zwischen drei Arbeitsweisen wechseln können — Übersicht, geführte Planung,
Expertenmodus — ohne dass sich sein Modell, seine Revision oder sein Speicherstand ändert.**

## Wann greift der Anwender danach?

*Beim Betreten des Hausplaners und bei jedem Wechsel der Arbeitsweise.* **Der Modusschalter steht in
jedem Modus sichtbar im Kopf** *(`:109-113`)* — **auch im Expertenmodus, damit der Weg zurück in die
geführte Planung nie verloren geht.**

## Der tragende Punkt: der Rahmen ist ADDITIV

**Wörtlich aus dem Dateikopf, `:2-5`:**

```text
Hausplaner Studio (v9-Synthese) — Rahmen ueber der bestehenden App.
Kopf mit Modus-Umschalter (Uebersicht/Experte) + persistente Navigation; die Buehne zeigt je nach
Modus den Start-Launcher, die gefuehrte WizardBase oder die volle HausplanerApp (Experte).
Additiv: die HausplanerApp bleibt unveraendert (nur ein optionales Flag blendet ihre
Markenzeile aus).
```

**Der einzige Eingriff in die bestehende App ist ein optionales Flag:**

```tsx
HausplanerStudio.tsx:140
<div className="hp-experte-buehne"><HausplanerApp imStudio /></div>
```

> **Wer W-39 als „neue Oberfläche" beschreibt, verfehlt genau das, was es auszeichnet.** *Es ist
> eine **Klammer**, kein Umbau: die `HausplanerApp` wird eingebettet, nicht ersetzt, und sie bleibt
> unverändert — `imStudio` blendet allein ihre Markenzeile aus, weil der Rahmen bereits eine
> Kopfzeile trägt.*
>
> **Derselbe Bautyp wie die anderen Stufe-6-Bausteine:** *W-20 aggregiert über die echte Liste statt
> zu schätzen, W-38 legt Attrappen still und bewacht sie, W-34 zählt **bebaute** Geschosse statt
> angelegter.* **Sie greifen nicht in den Bestand ein, sie machen ihn ehrlich sichtbar.**

## Woran merkt er, dass es fehlt?

*Ohne den Rahmen gäbe es die drei Arbeitsweisen nur als getrennte Einstiege* — **und jeder Wechsel
wäre ein Neuladen mit der Frage, ob das Modell noch dasselbe ist.** *Der Titel des
Experten-Schalters (`:112`) beantwortet genau diese Sorge:*

> *„Experte — alle Werkzeuge, Projektbaum und Eigenschaften. **Dasselbe Modell und dieselbe
> Revision.**"*

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

```text
NICHT  die dreizehn importierten Module BESCHREIBEN  -> je eigenes Werkzeug, siehe 5-CODE
NICHT  die HausplanerApp AENDERN                     -> das ist der Kern, siehe oben
NICHT  eine zweite Navigation stellen                -> ausgebaut in AUF-83-T2 (:122-128):
                                                        zwei Navigationsbaeume auf einem Schirm
                                                        sind Wiederholung, keine Orientierung
NICHT  den Speicherstand BEWERTEN                    -> dashboard/speicherAnzeige.ts, hier
                                                        nur die Farbe je Gewichtung (:58)
```

**W-39 verwaltet den Modus und hängt ein — mehr nicht.** *Alles, was auf der Bühne erscheint, gehört
einem anderen Werkzeug.*
