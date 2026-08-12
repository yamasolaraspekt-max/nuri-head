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

> **DREI davon sind seit W-27/1 GEFAHREN, nicht mehr nur abgelesen** — *mit Anker
> `bfc684226f02161448ef02d20b7629f3` und md5-Rücksetzung nach jeder Probe. Grundlinie 1709 pass,
> 0 fail.*

| Mutation | Muss erkannt werden von | Gefahren? |
|---|---|---|
| `isCCW`-Berücksichtigung entfernen | **K-4** | **ja — 2 FAIL**, beide K-4-Tests |
| `WALM`/`TEILWALM` aus `prevIsTraufe` streichen | **K-3 und K-6** | **ja — 1 FAIL** |
| `joinType`-Vorbelegung `'neutral'` entfernen | **K-5** | **ja — 2 FAIL** |
| `angleDeg > 180` zu `>= 180` ändern | **K-7** | nein — *abgelesen* |
| `360 - baseAngle` zu `baseAngle` vereinfachen | **K-2** — die innere Ecke wird zur äußeren | nein — *abgelesen* |

```text
Anker md5 vor den Proben   bfc684226f02161448ef02d20b7629f3
Grundlinie                 1709 Tests · 1709 pass · 0 fail

M1  isCCW = true              1707 pass · 2 FAIL
      ✖ K-4 TRAGEND: der Umlaufsinn — dasselbe Polygon in beiden Richtungen
      ✖ K-4: die L-Form hat GENAU EINE einspringende Ecke
M2  nur 'TRAUFE'              1708 pass · 1 FAIL
      ✖ WALM und TEILWALM zaehlen als Traufe im weiteren Sinn
M3  kein 'neutral'-Default    1707 pass · 2 FAIL
      ✖ der VIERTE Ausgang: … ist neutral — nicht undefined
      ✖ jede Ecke traegt IMMER einen der vier Ausgaenge — nie undefined

md5 nach jedem Ruecksetzen bfc684226f02161448ef02d20b7629f3   IDENTISCH
Suite danach               1709 pass · 0 fail · Typpruefung gruen
```

> **Keine der drei blieb grün.** *Das ist der Unterschied zu W-34-1 und W-39-5, wo je eine Fangprobe
> nichts fing — dort war es eine Aussage über einen Wächter, hier eine Messung an ihm.*

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
