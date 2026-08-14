# W-16 · Grundriss unterlegen — FORMELN

> **Regel: hier werden nur F-Nummern aus `01-MATHEMATIK/FORMELSAMMLUNG.md` genannt. Keine
> abgeschriebenen Formeln.**

## Benutzte Formeln

| F-Nr | Wofür in diesem Werkzeug | Grenzfall betrifft uns? |
|---|---|---|
| **F-001** Abstand zweier Punkte | die geklickte Strecke messen | **ja, aber anders** — s. u. |

**Am Code erhoben:**

```text
app/unterlage/kalibrierung.ts:26   return Math.hypot(b.x - a.x, b.y - a.y)
```

**Das ist F-001 in gebauter Form** — *`d = √((x₂−x₁)² + (y₂−y₁)²)`, geschrieben als `Math.hypot`.*

### Der Grenzfall von F-001 wirkt hier anders als bei einer Wand

**Die Sammlung sagt:** *„`d < ε` (0,5 mm) → beide Punkte gelten als **derselbe**. Eine Wand mit
`d < ε` darf nicht angelegt werden — sie erzeugt später eine Division durch null."*

> ***Genau diese Division steht hier*** — `eingegebeneLaengeMm / gemessen` (`:43`). **Das Modul
> schützt sich mit einer schärferen Bedingung: `gemessen <= 0`** (`:41`).
>
> **Beide Schwellen sind NICHT dieselbe, und das gehört gesagt:** *F-001s ε von 0,5 mm ist eine
> **fachliche** Schwelle („das ist derselbe Punkt"), `<= 0` ist die **rechnerische**.*
>
> **Nachgerechnet, nicht behauptet** — bei `alterMassstab = 1` und eingegebenen 1000 mm:
>
> ```text
> Abstand 0,3 mm   ->  3333,33      (unter F-001s eps, trotzdem ein Ergebnis)
> Abstand 0,5 mm   ->  2000,00      (genau auf eps)
> Abstand 0   mm   ->  null
> ```
>
> **Der Aufrufer bekommt bei 0,3 mm keine `null`, sondern eine unbrauchbare Zahl.**

*Das ist keine Beanstandung des Baus — F-001s ε gilt für **Wandanlagen**, und die Kalibrierung ist
keine. Es ist die Stelle, an der die zwei Schwellen auseinandergehen, und sie steht hier, damit
sie nicht übersehen wird.*

## Die Maßstabsrechnung hat KEINE F-Nummer — als Lücke gemeldet, nicht erfunden

```text
berechneMassstab:  neuerMassstab = alterMassstab · (eingegebeneLaenge / gemessen)
```

**Gemessen: die FORMELSAMMLUNG kennt sie nicht.** *Kein Abschnitt zu Maßstab, Kalibrierung oder
Verhältnisrechnung — vier Muster über die ganze Datei, null Treffer.*

> ***Eine erfundene Nummer wäre schlimmer als eine gemeldete Lücke.*** *Ob eine
> Verhältniskorrektur überhaupt in ein **Geometrie**-Verzeichnis gehört, ist eine Frage an die
> Sammlung und nicht an dieses Blatt.* **Hier steht: sie ist gebaut, sie ist geprüft
> (`kalibrierung.test.ts`), und sie trägt keine Nummer.**

## Was NICHT gerechnet wird

**Keine Bildverarbeitung, keine Entzerrung, keine Kantenerkennung.** *Die Insel skaliert ein
fertiges Bild; PDF-Klassifizierung und Rasterung passieren auf dem Server (`…/status`).*

## Normative Größen

**Keine.**
