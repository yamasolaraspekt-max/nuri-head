# W-15 · Material und Farbe — ZWECK

> **Reifegrad ENTWORFEN, nicht BESCHRIEBEN.** *Die anderen Blätter der Werkbank **lesen** vorhandenen
> Code ab. Dieses **gibt vor**, was zu bauen ist — Quelle ist der Werkzeugvertrag, kein Modul. Beim
> Übergang `ENTWORFEN → GEBAUT` ist zu prüfen, ob die Vorgabe mit der Ablesung übereinstimmt.*

## Welches Problem des Anwenders löst dieses Werkzeug?

**Er will einer Fläche ansehen, woraus sie besteht** — und das eintragen, ohne dafür ein zweites
Programm zu öffnen.

## Wann greift der Anwender danach?

*Wenn die Geometrie steht und die Frage von „wo" zu „woraus" wechselt:* die Wände sind gezogen, die
Räume erkannt, das Dach liegt — jetzt bekommt die Außenwand ihren Putz, das Bad seine Fliese, die
Fassade ihre Farbe.

## Woran merkt er, dass es fehlt?

**Er schreibt es woanders auf.** *Eine Liste neben dem Plan, ein Vermerk im Angebot, eine
Farbnummer auf einem Ausdruck.* **Und damit ist die Zuordnung an genau der Stelle nicht mehr da, wo
die Fläche liegt** — jede spätere Mengenermittlung muss sie von Hand wieder zusammensuchen.

## Was ist ausdrücklich NICHT Zweck dieses Werkzeugs?

| Nicht dieses Werkzeug | Sondern | Belegt an |
|---|---|---|
| Dachdeckung und ihre Menge | **W-23 Deckung und Material** (LEER, hängt an W-07/W-08, trägt `F-050`) | `REGISTER.md:60` |
| Mengen und Stückliste | **W-20 Stückliste und Mengen** (LEER, hängt an W-05/W-08) | `REGISTER.md:78` |
| Fassadenaufbau, Klinker, Dämmung | eigene Vertragseinträge mit eigenen `werkzeugId` | `werkzeugVertrag.ts:850, :862, :910` |

> **Die erste Zeile ist die wichtige:** *„Material" steht **zweimal** in der Werkbank — als W-15
> („Material und Farbe") und als W-23 („Deckung und Material"). **Beide sind LEER, also ist die
> Grenze noch nie gezogen worden.*** *Dieses Blatt zieht sie so: **W-15 belegt eine Fläche mit einem
> Oberflächenmaterial; W-23 rechnet aus einer Dachfläche die Deckung samt Menge.** Der Unterschied
> ist Zuweisung gegen Berechnung — und er ist im Vertrag sichtbar, weil W-15s drei Einträge
> `assign-or-calculate` mit **Zuweisungs**-Ergebnissen führen (`materialAssignmentIds`).*

## Der Zweck der Zuweisung selbst — offen, und das ist eine Messung

**Der Vertrag sagt, WAS zugewiesen wird, aber nicht WOZU.** *Gemessen, damit hier kein Zweck erfunden
wird:*

```text
grep -rn 'surfaceMaterialId|materialAssignment' resources/  (ohne werkzeugVertrag.ts)
  ->  0 Treffer
```

> **Niemand verbraucht die Zuweisung.** *Es gibt keinen Verbraucher, der aus `materialAssignmentIds`
> etwas macht — keine Stückliste, kein Angebot, keine Darstellung.* **Damit ist der Zweck jenseits
> von „die Zuordnung steht am Objekt statt auf einem Zettel" ungeklärt, und dieses Blatt sagt das,
> statt ihn zu behaupten.** *Wer Stufe 2 baut, braucht dafür eine Antwort von Yama — sonst entsteht
> ein Werkzeug, dessen Ergebnis nirgends ankommt.*
