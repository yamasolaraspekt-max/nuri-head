# W-21 · Sparren und Lattung — FUNKTION

## Sechs Module in zwei Naturen

### RECHNEN — aus Eingaben wird ein Ergebnis

| Modul | Z / Ausfuhren | was es tut |
|---|---|---|
| `resources/planner/hausplaner/geometry/sparrenBerechnung.ts` | 131 / 7 *(Ablesung; 13.08. gemessen **151 / 8** — s. `5-CODE`)* | Schneelast und **Vorbemessung nach Eurocode** |
| `resources/planner/hausplaner/geometry/sparrenTrennung.ts` | 67 / 3 | Sparren an einer Öffnung trennen, **und ob das sicher ist** |
| `resources/planner/hausplaner/geometry/auswechslung.ts` **(neu, W-21/2)** | 174 / 5 | **Auswechslung an einer Dachöffnung** — welche Sparren geschnitten werden und ob ein Wechselholz sicher ableitbar ist |

### Was `auswechslung.ts` genau leistet — und wo es aufhört

```text
sparrenPositionenU(breiteM, rafterDistM, rafterWidthM = 0.08)   -> number[]
    das Sparrenraster einer Flaeche in u-Richtung (parallel Traufe).

analysiereAuswechslung(flaeche, oeffnung, rafterDistM, opts?)   -> AuswechslungAnalyse
    1. Oeffnungsrechteck + Sicherheitsrand (Vorgabe 0,05 m) in Meterkoordinaten
    2. Randzonen: Traufe · First · Ortgang links · Ortgang rechts (Vorgabe 0,3 m)
    3. betroffene Sparren = die Rasterpositionen im u-Bereich der Oeffnung
    4. flankierende tragende Sparren links und rechts eindeutig?
    5. NUR wenn eindeutig UND keine Randzone UND nicht teilweise ausserhalb:
       wechselAnzahl = 2 (oben+unten), wechselLaengeM = Abstand der Flanken
       sonst: pruefpflichtig = true, wechselAnzahl = 0, wechselLaengeM = 0
```

**Die tragende Zeile des Moduls ist die fünfte, und sie ist eine Verweigerung.** *Der Dateikopf sagt
es selbst:* „**Ehrlichkeit:** Wechselhölzer werden **NUR** als echte Bauteile geführt, wenn die
angrenzenden tragenden Sparren eindeutig bestimmbar sind … Sonst `pruefpflichtig` = true … und
**KEINE erfundenen Mengen**." *(`auswechslung.ts:15-19`)* — **dieselbe Haltung wie
`OFFENE_HOLZBAUTEILE` in `7-GRENZEN`, nur eine Stufe früher: dort wird die Lücke gemeldet, hier wird
sie im Einzelfall entschieden.**

*Der Kopf warnt außerdem vor einer Verwechslung, die naheliegt:* **die Aufbau-TIEFE (`depth`, Höhe
über der Fläche) ist NICHT die Öffnungshöhe** (`:11-13`). *Öffnungsbreite misst in u, Öffnungshöhe in
v — beides in der Dachebene.*

### AGGREGIEREN — aus der bereits gezeichneten 3D-Liste werden Mengen

| Funktion | Fundstelle | Eingabe |
|---|---|---|
| `schifterMengenAusListe()` | `schifterListe.ts:141` | `holzliste` |
| `holzBauteileAusListe()` | `holzBauteile.ts:56` | `holzliste` |
| `holzMengenAusListe()` | `holzMengen.ts:44` | `holzliste` |

**Drei der sechs Module konstruieren nicht — sie zählen zusammen, was die 3D-Geometrie schon erzeugt
hat.** *Ohne diesen Satz liest jemand `holzMengenAusListe()` als Konstruktion und wundert sich, warum
nichts herauskommt, wenn noch nichts gezeichnet ist.*

**`auswechslung.ts` gehört zu keiner der drei** — es liest keine `holzliste`, sondern rechnet aus
Flächenmaßen und einer Öffnung. *Deshalb steht es oben bei RECHNEN und nicht hier.*

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
