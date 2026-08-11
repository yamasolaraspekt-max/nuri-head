# W-21 · Sparren und Lattung — FUNKTION

## Fünf Module in zwei Naturen

### RECHNEN — aus Eingaben wird ein Ergebnis

| Modul | Z / Ausfuhren | was es tut |
|---|---|---|
| `resources/planner/hausplaner/geometry/sparrenBerechnung.ts` | 131 / 7 | Schneelast und **Vorbemessung nach Eurocode** |
| `resources/planner/hausplaner/geometry/sparrenTrennung.ts` | 67 / 3 | Sparren an einer Öffnung trennen, **und ob das sicher ist** |

### AGGREGIEREN — aus der bereits gezeichneten 3D-Liste werden Mengen

| Funktion | Fundstelle | Eingabe |
|---|---|---|
| `schifterMengenAusListe()` | `schifterListe.ts:141` | `holzliste` |
| `holzBauteileAusListe()` | `holzBauteile.ts:56` | `holzliste` |
| `holzMengenAusListe()` | `holzMengen.ts:44` | `holzliste` |

**Drei der fünf Module konstruieren nicht — sie zählen zusammen, was die 3D-Geometrie schon erzeugt
hat.** *Ohne diesen Satz liest jemand `holzMengenAusListe()` als Konstruktion und wundert sich, warum
nichts herauskommt, wenn noch nichts gezeichnet ist.*

**`schifterListe.ts` ist gemischt:** `schifterAusFlaeche()` (Z.94) **konstruiert**,
`schifterMengenAusListe()` (Z.141) **aggregiert**. *Ein Modul, zwei Naturen — beim Lesen auseinanderhalten.*

## Was die Quelle liefert

```text
RoofEngine.holzliste, je Stueck:  { type: 'sparren'|'latte', name, laenge, … }
  Sparren        type 'sparren',  name beginnt mit 'Sparren'
  Schiftsparren  type 'sparren',  name beginnt mit 'Schiftsparren'  -> ZAEHLT als Sparren
  Konterlatte    type 'sparren',  name 'Konterlatte'                   (laeuft auf dem Sparren)
  Traglatte      type 'latte',    name 'Traglatte'
```

*Wörtlich aus `resources/planner/hausplaner/geometry/holzMengen.ts:11-16`.* **Der Schiftsparren zählt als Sparren** — der Code
begründet es: *„sonst Mengen-Unter-Count!"*
