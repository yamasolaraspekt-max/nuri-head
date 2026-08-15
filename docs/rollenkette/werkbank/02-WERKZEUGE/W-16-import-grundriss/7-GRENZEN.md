# W-16 · Grundriss unterlegen — GRENZEN

## Der Vertrag: `berechneMassstab` liefert `null` statt NaN

**Wörtlich aus `kalibrierung.ts:30-31`:**

> *„Der korrigierte Maßstab. `null`, wenn die Eingabe nicht auswertbar ist (Punkte identisch, Länge
> ≤ 0) — **kein NaN, keine Division durch 0**, ein Aufrufer muss nicht selbst darauf prüfen."*

**Die drei Bedingungen, am Code:**

```text
:39   eingegebeneLaengeMm <= 0      ->  null
:39   alterMassstab       <= 0      ->  null
:41   gemessen            <= 0      ->  null   (identische Punkte)
```

> ***Das ist ein Vertrag und keine Vorsichtsmaßnahme.*** *Der Kommentar sagt ausdrücklich, dass ein
> **Aufrufer sich darauf verlassen** darf — wer die Bedingungen aufweicht, bricht eine Zusage, auf
> die anderswo verzichtet wurde zu prüfen.* **Alle drei sind einzeln verriegelt**
> (`kalibrierung.test.ts`, drei Kanten-Zusagen).

## Die Grenze, die dabei OFFEN bleibt

**`gemessen <= 0` fängt nur die Null, nicht das Fast-Null.** *Nachgerechnet bei `alterMassstab = 1`
und 1000 mm Eingabe:*

```text
Abstand 0,3 mm   ->  3333,33
Abstand 0,5 mm   ->  2000,00
Abstand 0   mm   ->  null
```

> **F-001 sagt, zwei Punkte mit `d < 0,5 mm` seien **derselbe** Punkt.** *Hier liefern sie trotzdem
> ein Ergebnis — ein sehr großes.* **Der Aufrufer bekommt keine `null`, sondern eine unbrauchbare
> Zahl, die aussieht wie ein Maßstab.**
>
> *Das ist keine Beanstandung: F-001s ε gilt für **Wandanlagen**. Es ist die Stelle, an der die
> fachliche und die rechnerische Schwelle auseinandergehen — und keine Zusage hält sie fest.*

## Was das Werkzeug nicht tut

| Grenze | Beleg |
|---|---|
| **keine Wanderkennung** aus dem Bild | die Unterlage ist eine Vorlage zum Nachzeichnen |
| **keine Entzerrung** — ein schief fotografierter Plan bleibt schief | eine Strecke gibt ein Verhältnis, keine Transformation |
| **kein Eingriff ins Modell** | `listening={false}`, kein Klick-Handler, keine Befehle (K-03) |
| **kein Undo** für Upload oder Maßstab | die Unterlage liegt in `plan_uploads`, nicht im Szenendokument |
| **keine Bildverarbeitung in der Insel** | PDF-Klassifizierung und Rasterung laufen auf dem Server (`…/status`) |

## Die Grenze, die man beim Lesen übersieht: ein einziger Maßstab

**`massstab_mm_pro_einheit` ist EIN Wert je Unterlage.** *Es gibt keine Verzerrung in x und y
getrennt.* **Ein Plan, der beim Scannen in einer Richtung gestaucht wurde, lässt sich nicht
korrigieren** — *die Kalibrierung macht ihn in einer Richtung richtig und in der anderen falsch,
ohne dass etwas warnt.*

## Und `LEER` im Register heißt nicht, was es zu heißen scheint

**`REGISTER.md:87`:** *„`LEER` heißt hier **„kein Blatt gefüllt"**, nicht „kein Code vorhanden"."*

> **Der Code ist vollständig gebaut, angeschlossen und in beiden Hälften geprüft.** *Was fehlte,
> waren die Blätter — und die Registerzeile hat genau das gesagt.* **Nach dieser Ablesung wandert
> der Reifegrad auf die Stufe, die den gefüllten Blättern entspricht** (`:6`: `n/7 BLÄTTER` bzw.
> `BESCHRIEBEN`).

## Die doppelte Einordnung — benannt, nicht entschieden

**Der Gegenstand liegt unter `Energie`** *(sechs Routen `energie.plan-upload.*`, Controller in
`app/Http/Controllers/Energie/`)*, **und das Register führt ihn als Hausplaner-Werkzeug.**

> ***Beides trifft zu.*** *Ob die Serverhälfte dort bleiben soll, ist eine eigene Frage mit eigener
> Größe — dieses Blatt hält fest, wo sie liegt, damit niemand sie in
> `app/Http/Controllers/Hausplaner/` sucht.*
