# W-09 · Treppe — GRENZEN

## Der Kern: was `bestanden` bedeutet — und was nicht

`resources/planner/hausplaner/geometry/treppenBerechnung.ts` prüft **sieben** Regeln:

| Prüfung | Zeile | Schwere | wirkt auf `bestanden`? |
|---|---|---|---|
| `steigung-max` | **83** | **fehler** | **ja** |
| `auftritt-min` | **85** | **fehler** | **ja** |
| `schrittmass` | **87** | **fehler ODER warnung** (gestaffelt) | **nur im Fehlerfall** |
| `bequemlichkeit` | **89** | warnung | **nein** |
| `sicherheit` | **91** | warnung | **nein** |
| `laufbreite` | **94** | fehler | **ja — aber nur wenn angegeben** |
| `durchgangshoehe` | **98** | fehler | **ja — aber nur wenn angegeben** |

**Die Regel dahinter, wörtlich:**

```text
bestanden: !p.some((x) => x.schwere === 'fehler' && !x.bestanden)
```

### Daraus folgen zwei Sätze, die im Blatt stehen müssen

**1 · Eine verletzte Bequemlichkeits- oder Sicherheitsregel lässt `bestanden` auf `true`.**
Beide sind `warnung`. *Die Treppe ist dann normwidrig unbequem und gilt trotzdem als bestanden.*

**2 · Zwei Prüfungen laufen nur, wenn die Eingabe da ist.** Fehlt die Laufbreite, wird sie nicht
geprüft — **und `bestanden` bleibt `true`**, obwohl die Treppe zu schmal sein könnte.

> **`bestanden` heißt: „keine der DURCHGEFÜHRTEN harten Prüfungen ist verletzt."**
> **Es heißt NICHT: „die Treppe entspricht DIN 18065."**
> *Das ist eine **Teilaussage**, und die Plakette „Alle Prüfungen bestanden" sagt mehr, als die
> Rechnung weiß — zweimal: sie verschweigt die Warnungen und die nicht durchgeführten Prüfungen.*

## Was bei einer Normverletzung passiert — kein stilles Nichts

**Gemessen an `resources/planner/hausplaner/geometry/treppenBerechnung.ts:83-99`:** jede verletzte Regel erzeugt einen
Prüfeintrag **mit Klartext, Ist-Wert, Sollwert und Nutzungsbereich**, zum Beispiel:

```text
"Steigung 205,0 mm > zulaessig 200 mm (wohnung)."
"Auftritt 215,0 mm unter Mindestmass 230 mm (wohnung)."
```

**Kein Default, keine stille Korrektur, kein Zurechtbiegen.** *Die Auflage ist damit erfüllt, ohne
dass etwas gebaut werden muss — das gehört gesagt, weil ein erfüllter Auftrag ohne Bau leicht wie
ein übersehener aussieht.*

## Der Nutzungsbereich ist eine Normwahl

Dieselbe Treppe: `wohnung` zulässig, `gebaeude` nicht (`:53-55`). **Wer den Bereich falsch setzt,
bekommt ein richtiges Ergebnis zur falschen Frage.**

## Zulieferung an A-15 — Norm und Folge, ohne Klassifikation

| Modul | nennt eine Norm? | was eine Verletzung bedeutet |
|---|---|---|
| `treppenBerechnung.ts` | **ja** — `:5` und `:58` (DIN 18065) | Sturzgefahr: Steigung, Auftritt, Laufbreite, Durchgangshöhe sind Sicherheitsmaße |
| `treppe2D.ts` | **ja** — `:6`, verweist auf `berechneTreppe` (DIN 18065) | zeichnet nur; eine Verletzung entsteht hier nicht, sie wird **abgebildet** |
| `treppe3D.ts` | **nein** | dieselbe Lage: Darstellung, keine Prüfung |

> **Das ist Zulieferung, keine Klassifikation.** *Welche Klasse daraus folgt, entscheidet A-15 nach
> Yamas Achse 2 — nicht dieses Blatt.*
