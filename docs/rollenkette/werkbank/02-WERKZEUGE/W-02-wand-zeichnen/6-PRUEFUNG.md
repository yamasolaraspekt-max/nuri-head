# W-02 · Wand zeichnen — PRÜFUNG

## Was eine Prüfung hier belegen muss

1. **Die Azimut-Konvention** — Normale statt Achse, Nord = +y, 0–359 ganzzahlig.
   *Ein um 90° gedrehter Azimut sieht in der Draufsicht unauffällig aus.*
2. **Die mm-Invariante** — dass Gespeichertes ganzzahlig bleibt, auch wenn gerechnet wird.
3. **Dass Meldungen Meldungen bleiben** — kein Ergebnis, das Zahlen und Zweifel mischt.
4. **Länge 0** — eine Wand ohne Ausdehnung ergibt kein Band, nicht ein leeres.

## Warum Punkt 3 eigens geprüft wird

Der Ergebnistyp trennt **Mengen** und **Meldungen** in zwei Zweige. Der Grund steht im Code:

> *„Ein Ergebnistyp, der Zahlen und Zweifel gleichzeitig zulässt, wird an der ersten Aufrufstelle
> halb ausgewertet: die Zahlen nimmt man, die Meldungen übersieht man."*
> `resources/planner/hausplaner/geometry/wandFlaeche.ts:96`

**Eine Prüfung, die nur die Zahlen ansieht, prüft genau die Hälfte, die immer stimmt.**
