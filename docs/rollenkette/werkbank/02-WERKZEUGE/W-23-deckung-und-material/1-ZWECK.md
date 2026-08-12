# W-23 · Deckung und Material — ZWECK

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er will wissen, in welchem Abstand die Dachlatten liegen müssen** — und zwar für *diesen* Ziegel
auf *diesem* Dach, nicht allgemein.

## Wann greift der Anwender danach?

*Wenn die Dachfläche steht und die Deckung gewählt ist:* die Sparrenlänge Traufe→First ist bekannt,
das Ziegelmodell ist ausgesucht — jetzt muss die Lattung eingeteilt werden, **und sie muss in ganze
Reihen aufgehen.**

## Woran merkt er, dass es fehlt?

**Er rechnet es von Hand, und er rechnet es meistens falsch.** *Der naheliegende Weg — „Sparrenlänge
durch das größte Lattmaß, aufrunden" — liefert in bis zu **18,2 %** der Fälle einen Wert außerhalb
dessen, was der Ziegel erlaubt, **und zwar leise**.* Gemessen an den sieben belegten Modellen über
801 Sparrenlängen je Modell (**5.607 Fälle**, Vertretungsentscheid 12.08.).

> **Der Grund ist Teilbarkeit, kein Fachwissen:** *zwischen zwei Reihenzahlen liegt eine Lücke.
> Beispiel `Harzer Pfanne` bei einer Sparrenlänge von 1000 mm — `n=2` ergibt 500 mm (zu groß),
> `n=3` ergibt 333 mm (zu klein), und der Ziegel erlaubt nur **372–405**. **Es gibt hier keine
> gleichmäßige Teilung, und ein Werkzeug, das trotzdem eine Zahl nennt, erfindet sie.***

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

| Nicht dieses Werkzeug | Sondern | Belegt an |
|---|---|---|
| Materialzuweisung an Flächen (Farbe, Textur) | **W-15 Material und Farbe** (`ENTWORFEN`) | `REGISTER.md:68` |
| Mengen und Stückliste | **W-20 Stückliste und Mengen** | `REGISTER.md:78` |
| Die Lattung selbst konstruieren | **W-21L** — dieses Werkzeug liefert ihm den Operanden | Fahrplan, Operanden-Gate |

> **Die Grenze zu W-15 ist die wichtige, weil beide „Material" heißen:** *W-15 **weist zu** — eine
> Fläche bekommt ein Oberflächenmaterial. **W-23 rechnet** — aus Ziegelmaß, Dachmaß und Neigung wird
> eine Lattweite.* **Zuweisung gegen Berechnung**, und der Unterschied ist an der Formelspalte des
> Registers ablesbar: W-15 trägt **keine**, W-23 trägt **F-050**.

## Yamas Fachaussage, die den Zuschnitt bestimmt hat

> *„die eindecklattung ist abhängig von dach neigung und dach maße und zulässig überlappung der
> ziegel"* — 12.08.

**Drei Größen, nicht eine Tabelle.** *Deshalb war die Suche nach „**der** Lattweite" von Anfang an
falsch gestellt: ein Werkzeug, das einen Wert je Modell erwartet, hätte den Operanden nie gefunden —
auch wenn er vor ihm lag.* **Die Tabelle liefert den Bereich, die Rechnung liefert den Wert.**
