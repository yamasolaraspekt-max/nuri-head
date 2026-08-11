# W-05 · Raum erkennen — GRENZEN

## Die drei Fälle, am Code gemessen

### Offener Wandzug — kein Umlauf

Er erzeugt **keinen Raum und keine Meldung**. Der Umlauf entsteht trotzdem, entartet aber und fällt
an zwei Stellen heraus:

```text
roomDetection.ts:167-168   if (polygon.length < 3) continue;
roomDetection.ts:171-172   if (flaeche <= 0) continue;
                           // "Aussenumlauf (negativ) oder entartet (0) — kein Raum."
```

**Das Ergebnis ist eine leere Liste, kein Fehler.** *Der Anwender sieht keinen Raum — und ein
fehlender Raum sieht genauso aus wie ein Raum, den es nicht geben soll.*

### Sich kreuzende Wände — X-Kreuzungen werden NICHT geteilt

Der Dateikopf sagt es selbst (Z.19-22):

> *„Echte X-Kreuzungen (zwei Wände schneiden sich abseits aller Endpunkte) werden **NICHT** geteilt
> (Snapping führt Wände auf Endpunkte/T). Ein Fall mit X-Kreuzung erzeugt schlicht **keine
> zusätzlichen Räume — nie falsche**."*

**Die Grenze ist benannt und ihre Richtung ist festgelegt:** lieber ein Raum zu wenig als einer zu
viel. *Das ist eine Entscheidung, keine Lücke.*

### Wand mit Länge 0 — fällt vorher raus

```text
roomDetection.ts:88-91   const laenge = Math.hypot(…);
                         if (laenge === 0) { continue; }
```

**Exakt gegen 0 geprüft, nicht gegen ein Epsilon** — mm-Integer-Welt. *Eine Wand von 1 mm bleibt eine
Wand.*

## Die bewussten P0-Grenzen — aus dem Dateikopf, nicht erfunden

| Grenze | Folge |
|---|---|
| **Polygone laufen über die WANDACHSEN** (Mittellinien) | die Fläche ist **Achsmaß**, nicht lichtes Maß — sie ist um die halbe Wanddicke ringsum zu groß |
| der lichte Abzug ist *„eine spätere Verfeinerung"* | die Testwerte sind **auf Achsmaß handgerechnet** |
| keine F-013-Prüfung | siehe `3-FORMELN` — gemeldet, nicht bewertet |

**Wer die Fläche für eine Wohnflächenberechnung nimmt, nimmt die falsche Zahl.** *Sie ist nicht
falsch gerechnet — sie misst etwas anderes.*

## Kein Werkzeug, kein Ausweg

Es gibt **keinen Registry-Eintrag** für W-05. Damit kann der Anwender die Erkennung **nicht
abschalten und nicht anstoßen**. *Deshalb ist die Endlosschleifen-Freiheit hier keine Feinheit,
sondern die Bedingung dafür, dass das Werkzeug überhaupt automatisch laufen darf.*
