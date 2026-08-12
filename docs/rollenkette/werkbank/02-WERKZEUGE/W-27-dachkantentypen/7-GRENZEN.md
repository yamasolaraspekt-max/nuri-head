# W-27 · Dachkantentypen — GRENZEN

> **Dieses Blatt ist Pflicht.**
> Der teuerste Fehler des Projekts bisher: ein Dach, das bei nicht-rechteckiger
> Kontur unsichtbar verschwand statt eine Absage zu geben. Die Domäne verweigerte
> korrekt — der Renderer schluckte die Absage mit `catch { continue; }`.
> **Ein Werkzeug ohne benannte Grenze baut genau diesen Fehler wieder ein.**

## Die Lücke ist der KANTENTYP — nicht die Sache

> **Diese Unterscheidung ist die Auflage dieses Auftrags, und sie steht hier zuerst, weil eine zu
> weite Formulierung teurer ist als eine fehlende Zahl.**

**Was FEHLT — die Erkennung, mit den Befehlen gemessen:**

```text
in resources/planner/**/*.ts(x):
  TopologyJoinType   0        cornerType      0        joinType        0
  analyzeTopology    0        isInnerReflex   0
```

**Was DA IST — je Begriff die Trefferzeile, nicht die Zahl:**

| Sache | Fundstelle | Was genau |
|---|---|---|
| **Ortganglänge** | `geometry/dachformVorlagen.ts:291` | `export function ortgangFlaechenlaengeM(…)`, **mit Testzusage** (`dachformVorlagen.test.ts:151-154`, `= 10.6`) |
| **Ortgangausbildung** | `dachformVorlagen.ts:127` · `:1386` · `:1410` | Feld mit Klartextwerten je Dachform |
| **Grat/Kehle am Sparren** | `geometry/schifterListe.ts:58` | `klassifiziereSchifter` → `'kehle' \| 'grat' \| 'voll' \| 'beidseitig'`, mit Testzusage (`schifterListe.test.ts:24-27`) |

> **Deshalb ist der Satz „den Ortgang gibt es in der Insel nicht" VERBOTEN.** *Er ist falsch, und er
> ist teuer: wer ihn liest, baut eine Ortganglänge neu — **die es gibt, exportiert und getestet**.*
>
> **Und dasselbe gilt für Grat und Kehle.** *Die Insel klassifiziert beide bereits — **am
> Sparren**:*
>
> ```text
> VORHANDEN  klassifiziereSchifter  ->  reicht dieser SPARREN bis Traufe und First?
> FEHLT      joinType               ->  was entsteht an DIESER ECKE des Grundrisses?
> ```
>
> *Beide meinen dasselbe Bauteil, aber die eine leitet es aus dem **Sparrenverlauf** ab, die andere
> aus der **Grundrissgeometrie**.* **Was fehlt, ist die zweite.**

**Die Regel dahinter, in den Worten des Plan-Prüfers:** *„**null Vorkommen eines MUSTERS ist kein
Beleg für die Abwesenheit der SACHE**."* **Das ist die Umkehrung von H-8** — *H-8 sagt, dass viele
Vorkommen keine Herkunft belegen; hier belegt keines keine Abwesenheit.* **Derselbe Denkfehler in
zwei Richtungen.**

## Was dieses Werkzeug NICHT kann

| Fall | Warum nicht | Was der Anwender stattdessen sieht |
|---|---|---|
| **Die Kontur annehmen** | W-07 lehnt nicht-rechteckige Konturen weiterhin ab (1 % Toleranz) | die bisherige Absage — **W-27 ändert daran nichts** |
| **Ecken ohne Kantentypen deuten** | die Typen sind Eingabe, keine Ableitung | Absage: „bitte alle Kanten belegen" |
| **Straight Skeleton** | anderer Weg — `skelett` **0 Treffer** in acht Dachmodulen | entfällt |
| **Restausgleich, Anschlussdetails** | W-27 klassifiziert Ecken, es konstruiert nicht | entfällt |
| **Doppelter Punkt** | der Winkel ist dort bedeutungslos | **Vorgabe:** Meldung statt Ersatzwinkel (`6-PRUEFUNG.md` K-8) |

## Die Abweichung zur Registerzeile — benannt, nicht behoben

```text
REGISTER.md:94   "W-27  Dachkantentypen  First·Grat·Kehle·Traufe·Ortgang"
Prototyp         KANTEN (EdgeTopologyType):  TRAUFE · GIEBEL · PULT_WAND · WALM · TEILWALM
                 ECKEN  (TopologyJoinType):  grat · kehle · ortgang · neutral
```

> **Das Register nennt fünf Namen in EINER Liste; der Prototyp trennt Kanten von Ecken.** *`First`
> und `Grat` sind dort **keine Kantentypen** — sie entstehen an Ecken bzw. zwischen Flächen.* **Die
> Trennung ist die bessere und wird übernommen; die Registerzeile wird beim BAU nachgezogen, nicht
> jetzt.** *Wer sie heute umschreibt, ändert einen Zustand, den dieses Blatt nur beschreibt.*

## Die Absagekette

| Fall | Fehlername *(Vorgabe)* | Wer fängt | Anwendertext steht in |
|---|---|---|---|
| Weniger als drei Punkte | `ZuWenigeEcken` | Schicht 3 | `4-BEDIENUNG.md` |
| Kantenzahl ≠ Punktzahl | `KantenUnvollstaendig` | Schicht 3 | `4-BEDIENUNG.md` |
| Doppelter Punkt | `EckeOhneWinkel` | Schicht 2 → 3 | `4-BEDIENUNG.md` |

**`neutral` ist KEIN Fall dieser Kette.** *Es ist ein gültiges Ergebnis und darf niemals eine Absage
auslösen* — siehe `2-FUNKTION.md`.

## Fänger-Prüfung

- [ ] Jeder Fehlerpfad ist durch einen Test belegt: **die Meldung erreicht die Oberfläche**
- [ ] Kein `catch { }` ohne Weiterreichen
- [ ] **Kein Ersatzwinkel ohne Hinweis** — die Verschärfung gegenüber dem Prototyp

## Bekannte Ungenauigkeiten

| Größe | Abweichung | Ab wann stört es |
|---|---|---|
| Eckwinkel | `acos` mit geklemmtem Skalarprodukt (`:203`) | bei fast gestreckten Ecken, unkritisch |
| 180°-Schwelle | **ohne Toleranz** (`> 180`) | bei exakt gestreckten Ecken — als K-7 geprüft |

## Was später kommen könnte

- **W-07s V1-Grenze aufheben** — *die Planner-Entscheidung nach diesem Auftrag; W-27 liefert die
  Voraussetzung, nicht die Entscheidung.*
- **Anzeige der Ecktypen** am Modell.
- **Die Verbindung zu `klassifiziereSchifter`** — *zwei Wege zu Grat und Kehle, die zusammenpassen
  müssen. **Wer sie verdrahtet, prüft zuerst, ob sie dasselbe sagen.***
