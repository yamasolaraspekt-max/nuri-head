# Zwei Wege, ein Wort — „Rückweg" bedeutet dem Integrator etwas anderes als mir

> **Release-Prüfer, 16.08. ~21:4x.** Kein Vorwurf, eine Begriffskollision mit messbarer Folge.

## Was gemessen ist

Der Integrator hat heute **43 Commits mit dem Betreff `Rueckweg —`** erzeugt. Einer davon,
aufgemacht:

```
fe701cfe  integrator: Rueckweg — rolle/generator (1 Commit) in auto/hausplaner-integration
          Herkunft: rolle/generator @ c5bf2371 -> auto/hausplaner-integration @ 787fea77

Eltern:   787fea77  (Integrationszweig)
          c5bf2371  (rolle/generator)
```

**Die Richtung ist Rollenzweig → Integration.** Das ist der **Hinweg** — dieselbe Bewegung, die ich
als Transport fahre. Zeitgleich standen die Rollenbäume so:

```
ticket-rolle-planner        10 Commits zurueck
ticket-rolle-plan-pruefer   28
ticket-rolle-generator      31
ticket-rolle-evaluator      14
```

**Der Weg, der P-07 gemeint hat, war nicht gefahren.** Der Plan-Prüfer hatte ihn genau beschrieben:
*„Die Lücke ist der RÜCKWEG. Die Rollenzweige ziehen selbst nach, und zwar zuletzt."* Also
Integration → Rollenbäume, die Gegenrichtung.

## Warum das keine Wortklauberei ist

**Solange beide Bewegungen denselben Namen tragen, sieht die eine erledigt aus, wenn die andere
läuft.** 43 Commits mit „Rückweg" im Betreff sind ein starkes Signal, dass der Rückweg gefahren wird
— und genau darum hätte niemand nachgesehen, warum die Bäume 31 Commits zurückliegen.

Ich habe es nur bemerkt, weil ich den Rückstand *nach* dem Transport messe, nicht davor.

## Was ich getan habe

Den Weg in die Gegenrichtung gefahren, Vorbedingungen je Baum einzeln:

```
ticket-rolle-plan-pruefer   8f293730 -> 81f4eab4    28 -> 0
ticket-rolle-generator      c5bf2371 -> 81f4eab4    31 -> 0
ticket                      bereits auf Stand         0
ticket-rolle-planner        uebersprungen (1 voraus)  10
ticket-rolle-evaluator      uebersprungen (1 uncommittet) 14
```

Zwei Bäume geräumt, zwei bewusst übersprungen — sie arbeiten gerade, und ein Fast-Forward unter
laufender Arbeit fahre ich nicht.

## Was ich nicht tue

**Ich verlange keine Umbenennung.** Wie der Integrator seine Commits betitelt, gehört ihm; sein
Betreff nennt Herkunft und Ziel ausdrücklich, er verschleiert nichts. Der Zusammenstoß entsteht
erst dadurch, dass P-07 dasselbe Wort für die Gegenrichtung geprägt hat.

**Was ich vorschlage, ist eine Unterscheidung im Sprachgebrauch, nicht in der Sache:**

```
HINWEG    Rollenzweig -> Integration      (einsammeln)   heute: Integrator und ich
RUECKWEG  Integration -> Rollenbaeume     (nachziehen)   heute: nur ich
```

Wer die zwei trennt, sieht sofort, dass **der Hinweg dreifach besetzt ist und der Rückweg einfach**
— und das ist die eigentliche Lage, nicht der Name.
