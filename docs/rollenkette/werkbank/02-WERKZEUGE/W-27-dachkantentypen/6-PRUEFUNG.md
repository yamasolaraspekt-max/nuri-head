# W-27 · Dachkantentypen — PRÜFUNG

> **Regel: jedes Kriterium muss VOR dem Bau wirksam rot sein.**
> Ein Kriterium, das schon grün ist, bevor gebaut wurde, prüft nichts.

**Alle Kriterien sind heute rot, und der Rot-Beleg ist derselbe: die Erkennung existiert in der
Insel nicht** — `TopologyJoinType`, `cornerType`, `joinType`, `analyzeTopology`, `isInnerReflex`
je **0 Treffer** in `resources/planner/`.

## Abnahmekriterien

| Nr | Kriterium | Wie gemessen |
|---|---|---|
| **K-1** | Rechteck: **4 Ecken außen, 0 innen, 0 Grate, 0 Kehlen** | Satteldach mit zwei Traufen und zwei Giebeln → **4 Ortgänge**, 0 Grate |
| **K-2** | **L-Form**: genau **eine innere Ecke** wird als `kehle` erkannt | die einspringende Ecke; die übrigen außen |
| **K-3** | **Walmdach**: die vier Ecken sind `grat`, nicht `ortgang` | *`WALM` zählt als traufseitig* — siehe K-6 |
| **K-4** | **Umlaufsinn**: dasselbe Polygon **im Uhrzeigersinn** gezeichnet liefert **dieselben** Typen | Punkte umdrehen, Ergebnis vergleichen |
| **K-5** | `neutral` tritt auf und ist **kein Fehler** | Ecke zwischen zwei Giebeln |
| **K-6** | `TEILWALM` und `WALM` zählen wie `TRAUFE` | drei Zusagen, je Typ eine |
| **K-7** | Eine Ecke von exakt **180°** gilt als `aussen` | die Schwelle ist `> 180`, ohne Toleranz |
| **K-8** | Doppelter Punkt liefert **eine Meldung**, keinen stillen Ersatzwinkel | der Prototyp fängt nur ab (`:201`) |

> ### K-4 ist die Zusage, an der eine eigene Implementierung scheitert
>
> *Der Winkel allein sagt nicht, ob eine Ecke einspringt — **das entscheidet das Kreuzprodukt im
> Verhältnis zum Umlaufsinn** (`isCCW ? cross > 0 : cross < 0`).* **Wer den Umlaufsinn wegläßt,
> bekommt bei umgekehrt gezeichnetem Polygon alle Ecken falsch herum — und zwar leise, weil die
> Zahl der Ecken stimmt.**

> ### K-8 ist eine VERSCHÄRFUNG gegenüber der Quelle
>
> *Der Prototyp rechnet mit `Math.hypot(...) || 1` an einem doppelten Punkt einfach weiter (`:201`).*
> **Das Ergebnis ist ein bedeutungsloser Winkel, der wie ein gemessener aussieht.** *Für den Bau
> verlangt dieses Blatt eine Meldung — begründet in `4-BEDIENUNG.md`.*

## Fangprobe (Mutationsprobe)

| Mutation | Muss erkannt werden von |
|---|---|
| `isCCW`-Berücksichtigung entfernen | **K-4** |
| `WALM`/`TEILWALM` aus `prevIsTraufe` streichen | **K-3 und K-6** |
| `angleDeg > 180` zu `>= 180` ändern | **K-7** |
| `joinType`-Vorbelegung `'neutral'` zu `'grat'` ändern | **K-5** |
| `360 - baseAngle` zu `baseAngle` vereinfachen | **K-2** — die innere Ecke wird zur äußeren |

> **Die vorletzte Zeile ist die gefährlichste**, weil sie **keine** Absage erzeugt: *jede Ecke bekäme
> einen Typ, die Zählung stimmte, und das Dach wäre falsch.* **Ein Fehler, der eine plausible Zahl
> liefert, ist teurer als einer, der abbricht.**

## Automatische Tests

| Datei | Prüft |
|---|---|
| `resources/planner/hausplaner/__tests__/kantenTopologie.test.ts` *(neu beim Bau)* | K-1 bis K-8 |

*Der Dateiname ist Vorgabe, nicht Ablesung — heute existiert er nicht.*

## Sichtprüfung

- [ ] 1440 px · 1024 px · 375 px
- [ ] **Die Ecktypen sind am Modell unterscheidbar** (Grat/Kehle/Ortgang), auch bei kleiner Ansicht

*Betrifft erst die Anzeige; W-27 selbst rechnet.*

## Bestandsprobe

- [ ] Ein vor der Änderung gespeichertes Dokument lädt danach unverändert

> **Hier besonders wichtig:** *W-27 wäre der erste Schritt dahin, dass **W-07 nicht-rechteckige
> Konturen annimmt**. Solange W-07s Grenze steht, ändert sich für Bestandsdokumente nichts — **wer
> beides in einem Zug baut, ändert die Annahmebedingung für alte Dokumente**, und das gehört
> gemessen.*
