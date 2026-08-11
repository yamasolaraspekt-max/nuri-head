# W-04 · Öffnung (Tür/Fenster) — FORMELN

## Keine.

**Beide Module sind Kataloge und Nachschlagefunktionen — es wird nichts gerechnet.**

Gemessen, nicht vermutet: kein `Math.`, keine Winkel, keine Längenrechnung in
`oeffnungsBauarten.ts` und `oeffnungsTypen.ts`. Die einzigen Operationen sind `Array.find()` und
der Vorgabewert `??`.

**Die Maße in den Vorlagen sind Zahlen, keine Formeln** — `875 × 2010` für eine einflügelige Drehtür
steht im Katalog, es wird nicht hergeleitet. *DIN-nah, sagt der Dateikopf; die Quelle der Zahlen ist
der Katalog selbst.*

## Wo die Rechnung stattdessen liegt

| Rechnung | Werkzeug | Fundstelle |
|---|---|---|
| Türblatt-Schwenk | **W-02** | `resources/planner/hausplaner/geometry/wallGeometry.ts:291` |
| Öffnungsabzug von der Wandfläche | **W-02** | `resources/planner/hausplaner/geometry/wandFlaeche.ts:135` |

> **„Keine Formel" ist hier die Antwort, nicht die Lücke.** *Ein Katalog, der rechnet, wäre der Fehler.*
