# W-21 · Sparren und Lattung — CODE

**Angebunden aus SECHS vorhandenen Modulen** — Stand 13.08.: **690 Zeilen, 32 Ausfuhren**. Jede
Zeilenzahl einzeln nachgezählt, nicht aus der Summe abgeleitet.

> **W-21/2 hat `auswechslung.ts` aufgenommen (13.08.).** *Bis dahin führte dieses Blatt fünf Module,
> und `W-22/5-CODE/LIESMICH.md:36` wie `W-22/7-GRENZEN.md:55` meldeten die Datei als „in keinem Blatt
> zuhause". Der Grund für W-21 steht in `1-ZWECK`.*

**Zwei Zahlen des alten Kopfes stimmten schon vor dieser Aufnahme nicht mehr — beim Anfassen
gemessen, hier benannt statt fortgeschrieben:**

| war im Kopf | gemessen 13.08. | woran es liegt |
|---|---|---|
| 496 Z / 25 Ausfuhren (fünf Module) | **516 Z / 27** für dieselben fünf | `sparrenBerechnung.ts` ist gewachsen (s.u.), **und** die 25 waren schon zur Ablesung eine zu wenig: die Tabelle darunter nennt **26** Namen |
| `sparrenBerechnung.ts` 131 Z / 7 | **151 Z / 8** | achte Ausfuhr `N003_VORBEHALT` (`:100`) — die gebaute Form von **Yamas A-14-Auflage** „keine stille Zahl"; `berechneSparren()` steht dadurch nicht mehr auf `:86`, sondern auf **`:105`** |

*Die Zeile der Tabelle bleibt auf dem Stand der Ablesung stehen: **das Nachziehen der W-21-Ablesung
ist nicht der Auftrag von W-21/2** (Scope: „keine Neubewertung der zwölf Kriterien"), und eine still
korrigierte Zahl wäre später nicht mehr als Drift erkennbar. Wer sie nachzieht, findet hier, was zu
prüfen ist.* **Und `:86` ist zugleich die Belegstelle von N-003 in der Formelsammlung** — siehe
`3-FORMELN`.

| Modul | Z | Ausfuhren |
|---|---|---|
| `resources/planner/hausplaner/geometry/sparrenBerechnung.ts` | 131 *(Ablesung; 13.08. gemessen **151**)* | `Schneezone` (16) · `Holzklasse` (17) · `bodenschneelast()` (33) · `formbeiwertSchnee()` (45) · `SparrenEingabe` (52) · `SparrenErgebnis` (68) · `berechneSparren()` (86 → **13.08. :105**) · *neu:* `N003_VORBEHALT` (100) |
| `resources/planner/hausplaner/geometry/sparrenTrennung.ts` | 67 | `SparrenTeilstueck` (19) · `sparrenTeilstuecke()` (37) · `istSicherTrennbar()` (59) |
| `resources/planner/hausplaner/geometry/schifterListe.ts` | 152 | `Punkt2D` (28) · `SchifterArt` (30) · `SchifterSparren` (32) · `SchifterMengen` (40) · `klassifiziereSchifter()` (58) · `schifterAusFlaeche()` (94) · `schifterMengen()` (113) · `HolzStueckRef` (134) · `schifterMengenAusListe()` (141) |
| `resources/planner/hausplaner/geometry/holzBauteile.ts` | 82 | `HolzStueckRef` (22) · `HolzBauteilMengen` (28) · `OFFENE_HOLZBAUTEILE` (45) · `holzBauteileAusListe()` (56) |
| `resources/planner/hausplaner/geometry/holzMengen.ts` | 64 | `HolzStueck` (23) · `HolzMengen` (29) · `holzMengenAusListe()` (44) |
| `resources/planner/hausplaner/geometry/auswechslung.ts` **(neu, W-21/2)** | **174** | `FlaecheMasse` (24) · `Oeffnung` (31) · `AuswechslungAnalyse` (42) · `sparrenPositionenU()` (69) · `analysiereAuswechslung()` (87) |

## Eine Doppelung, die auffallen soll

**`HolzStueckRef` gibt es zweimal** — `schifterListe.ts:134` und `holzBauteile.ts:22`. *Kein Import
verbindet sie.* **Dieselbe Lage wie `MassPunkt` bei W-11:** ändert eine Seite, divergieren sie stumm.
*Hier nur benannt, nicht gemessen, ob die Felder heute deckungsgleich sind.*

## Die zweite Doppelung, und sie ist die teurere: das Sparrenraster steht zweimal

Mit `auswechslung.ts` führt dieses Blatt **dieselbe Sparren-Schleife an zwei Stellen**:

```text
auswechslung.ts:74-77    numRafters = min(2000, max(1, floor(b / dist)))
                         u = rw/2 + i * ((b - rw) / numRafters)
schifterListe.ts:98-100  numRafters = min(2000, max(1, floor(uM / rd)))
                         u = rw/2 + i * ((uM - rw) / max(1, numRafters))
```

*Gemessen: `numRafters` kommt im ganzen Inselcode an genau diesen zwei Stellen vor (Tests
ausgenommen), und **kein Import verbindet sie** — `auswechslung.ts` importiert nichts.*

**Der Unterschied ist nicht kosmetisch:** `schifterListe.ts` schützt den Nenner mit `max(1, …)`,
`auswechslung.ts` nicht. *Hier trägt `numRafters ≥ 1` aus derselben Zeile — die zwei Fassungen sind
heute gleichwertig. Aber die eine hält ihre Annahme fest und die andere verlässt sich darauf.*

**Und beide berufen sich auf eine Quelle, die in diesem Repository nicht liegt:** *„identisch zur
Engine"* (`auswechslung.ts:66-67`) und *„Spiegelt die Sparren-Schleife aus `buildRoofFace` **EXAKT**"*
(`schifterListe.ts:89`). **`buildRoofFace` und `DachplanerProPage` sind im ganzen Repo nur erwähnt,
nirgends definiert** — gemessen über alle `.ts/.tsx/.js` außerhalb `node_modules` und `public/`.
*Die Treue zur Engine ist damit hier nicht nachprüfbar, sondern nur behauptet. Das ist kein Fehler
der Module; es ist die Grenze dessen, was dieses Blatt belegen kann.*

## Was gebaut ist und was nicht

**Gebaut:** die Rechen- und Aggregationsschicht, rein — der Kopf sagt *„Keine three/Konva",
„Rein (keine React-/THREE-Abhängigkeit)"*.
**Nicht vorhanden:** eine Werkzeugschicht.
