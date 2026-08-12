# W-15 · Material und Farbe — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt.
> Keine abgeschriebenen Formeln.** Eine Formel, die an zwei Orten steht, wird an
> einem Ort korrigiert und am anderen vergessen.

## Benutzte Formeln

**KEINE — und das ist eine Aussage, kein leeres Feld.**

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| — | — | — |

## Warum keine — begründet statt weggelassen

**Material und Farbe rechnen nicht. Sie ordnen zu.** *Das Werkzeug nimmt Kennungen entgegen
(`surfaceMaterialId`, `variantId`, `textureSetId`) und schreibt Kennungen zurück
(`materialAssignmentIds`, `updatedAssignmentId`) — **zwischen Eingang und Ausgang steht keine
Größe, die berechnet würde.*** Nachlesbar an der Ein- und Ausgabetabelle in `2-FUNKTION.md`, deren
Zeilen sämtlich aus `werkzeugVertrag.ts:874-908` zitiert sind.

> **Ein begründetes „keine" ist ein Befund, eine leere Spalte ist keiner.** *Die Unterscheidung ist
> hier belegbar wichtig: bei einer früheren Zuordnungsrunde fielen **sieben von zehn**
> F-Zuordnungen, weil sie geraten statt gemessen waren (`603eddc2`). **Eine Formel zu nennen, die
> das Werkzeug nicht braucht, ist derselbe Fehler in die andere Richtung.***

## Wo die Mathematik STATTDESSEN liegt

**Nicht hier, aber in der Nachbarschaft — und das ist genau die Grenze aus `1-ZWECK.md`:**

| Werkzeug | Formel | Registerzeile |
|---|---|---|
| **W-23** Deckung und Material | **F-050** | `REGISTER.md:60` |
| **W-20** Stückliste und Mengen | F-011, F-023 | `REGISTER.md:78` |

> *Wer aus einer Materialzuweisung eine **Menge** machen will, rechnet — aber dann steht er in W-20
> oder W-23, nicht in W-15.* **Die Trennung „Zuweisung ohne Rechnung / Rechnung mit Menge" ist damit
> nicht nur behauptet, sondern an den Formelspalten des Registers ablesbar.**

## Fehlt eine Formel?

**Nein.** *Sollte Stufe 2 doch eine brauchen — etwa für die Abbildung einer Textur auf eine Fläche
(`mapping`, `werkzeugVertrag.ts:901`) —, gilt die Reihenfolge: erst hier benennen, dann in die
Sammlung eintragen, dann auf die Nummer verweisen. **Nicht umgekehrt.***

## Genauigkeit

**Entfällt.** *Es gibt keine Eingangsgröße mit Einheit, keine Rundung und keine Toleranz — die
Eingaben sind Kennungen, kein Maß.* **Ein „ε" hier hinzuschreiben wäre eine Genauigkeitsangabe über
etwas, das nicht gemessen wird.**
