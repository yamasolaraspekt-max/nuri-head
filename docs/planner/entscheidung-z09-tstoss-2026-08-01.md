# ENTSCHEIDUNGSVORLAGE — T-Stoß und Kreuzung: Achsmaß oder Flankenmaß?

**Planner, 01.08.2026, 23:0x.** *Ich habe Z-09 NICHT geschnitten. Diese Seite sagt, warum — und
macht die Frage so klein, dass sie in einem Wort zu beantworten ist.*

## Warum das Blatt nicht kam

**Z-09 heißt in der Bestandsaufnahme „Gehrung, T, Kreuz". Die Gehrung ist gebaut und abgenommen.**
Ein Blatt, das sie noch einmal verlangt, wäre F-07 — genau mein Fehler von heute früh bei Z-05.

```text
node scripts/zaehle.mjs resources/planner/hausplaner/geometry/wallGeometry.ts 'wandBaender' --wort  -> 1
grep -c "^test(" resources/planner/hausplaner/__tests__/wandBaender.test.ts                          -> 6
```

Die sechs abgenommenen Zusagen, wörtlich:

```text
90°-L-Ecke: Naht vom Inneneck zum SCHARFEN Aussenneck   gehrt
Freies Ende (kein Nachbar)                              stumpf
Gerade Fortsetzung (180°)                               stumpf
T-Stoss (drei Waende an einem Punkt)                    stumpf   <- der ganze Rest von Z-09
mm-Invariante · Laenge-0-Wand                           gilt
```

**Von Z-09 bleibt genau ein Rest, und der ist keine Geometrie-, sondern eine Fachfrage.**

## Was heute passiert — gemessen

`__tests__/wandBaender.test.ts` Zeile 60–68, abgenommen:

```text
A: (0,0)->(4000,0)     240 dick   durchgehend
B: (4000,0)->(8000,0)  240 dick   gerade Fortsetzung
C: (4000,0)->(4000,3000) 240      kommt an

C.ecken[0] = {x:3880, y:0}    C.ecken[3] = {x:4120, y:0}
```

**Die ankommende Wand beginnt auf der ACHSE der durchgehenden** (`y=0`), die von `-120` bis `+120`
reicht. **C ragt 120 mm hinein.** *Das ist die Achsmaß-Konvention und im Bauwesen zulässig — nicht
automatisch ein Fehler.* Die Alternative ist das Flankenmaß: `y = 120`.

## Was ich NICHT belege, und deshalb nicht behaupte

```text
node scripts/zaehle.mjs resources/planner/hausplaner/geometry/wandFlaeche.ts 'wandBaender' --wort -> 0
```

`wandMengen` benutzt die gehrte Geometrie **gar nicht** — es rechnet aus den Maßen im Knoten
(*„Roh = die Maße, wie sie im Knoten stehen"*). **Ob dadurch bei T-Stößen 120 mm doppelt gezählt
werden, habe ich nicht gemessen.** Der Schluss „also rechnet er zu viel" klingt plausibel und ist
unbelegt — **genau die Klasse, die mich heute viermal erwischt hat.**

## Die Frage, ein Wort

> **Enden ankommende Wände am T-Stoß und an der Kreuzung an der FLANKE der durchgehenden Wand —
> oder auf deren ACHSE, wie heute?**

```text
ACHSE (heute)  Nichts zu tun. Z-09 faellt aus der Schlange und wird in der
               Bestandsaufnahme gestrichen.
               Preis: im 3D-Koerper steckt an jedem T ein doppeltes Volumen von
               halber Wanddicke x Wandhoehe. In der Menge: ungemessen.

FLANKE         Z-09 wird geschnitten. Ankommende endet an der Flanke, durchgehende
               laeuft durch. Bei der Kreuzung laeuft die LAENGERE Achse durch, bei
               Gleichstand die zuerst angelegte - deterministisch, sonst wandert die
               Geometrie zwischen zwei Laeufen.
               Preis: die abgenommene Zusage Zeile 60-68 wird ROT. Beabsichtigt.
```

## Vorbereitet, damit die Antwort sofort baubar ist

Nahtstelle `wandBaender` in `geometry/wallGeometry.ts` · Zusagen in `__tests__/wandBaender.test.ts`
· Ausgangswert gemessen (`ecken[0].y = 0`, erwartet `120`) · Kantenliste steht:

```text
1  Kreuzung mit vier gleich langen Waenden - welche laeuft durch? Regel muss deterministisch sein.
2  Y-Stoss (drei Waende, nicht rechtwinklig) - die Flanke ist dann schraeg.
3  Ankommende duenner/dicker als die durchgehende: der Versatz ist die halbe Dicke der
   DURCHGEHENDEN, nicht der ankommenden.
4  mm-Invariante: bei ungerader Dicke (175) ist die Haelfte 87,5. Runden - einheitlich, benannt.
5  Die abgenommene Zusage wird rot. Das gehoert ins Blatt, sonst haelt der Generator es fuer
   eine Regression und dreht es zurueck.
```

**Bis die Antwort da ist, liegt Z-09 nicht in der Schlange.** *Ein Blatt, das eine Fachkonvention
rät, ist kein Blatt, sondern eine Vermutung mit Kriterien.*

---

## NACHTRAG 02.08.2026, 15:0x — festgeschrieben auf ACHSE, nicht entschieden

**Anlass: Yama wollte keine offenen Posten mehr auf seiner Liste, und dieser lag seit dem 01.08.
dort — ohne dass er je bewegt worden wäre.**

**Es bleibt bei ACHSE.** *Das ist keine Fachentscheidung des Planners — Tor 1 gehört Yama. Es ist
das Festschreiben des BESTANDS:* die ankommende Wand beginnt heute auf der Achse, das ist
Achsmaßkonvention und im Bauwesen zulässig. **Ein Posten, der „lass alles wie es ist" bedeutet,
ist keine Aufgabe, sondern eine Beobachtung.**

```text
Z-09        gestrichen aus der Schlange, gefuehrt als ENTSCHIEDEN
PREIS       an jedem T-Stoss ein doppeltes Volumen von halber Wanddicke x Wandhoehe
            im 3D-Koerper. In der Mengenermittlung: UNGEMESSEN - unveraendert seit
            dem 01.08., und ich behaupte weiterhin nicht, was ich nicht gemessen habe.
UMKEHR      ein Wort. Nahtstelle `wandBaender` in geometry/wallGeometry.ts, Zusagen in
            __tests__/wandBaender.test.ts. Am selben Tag baubar.
AUSLOESER   wenn die Mengenermittlung je gegen eine echte Rechnung gehalten wird und die
            120 mm auffallen, kommt Z-09 von selbst zurueck.
```

**Diese Seite bleibt stehen** — sie ist die Begründung, nicht das Archiv. *Wer FLANKE will, liest
sie und weiß in fünf Minuten, was zu tun ist.*
