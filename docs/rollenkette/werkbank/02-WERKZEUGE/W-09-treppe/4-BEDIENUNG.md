# W-09 · Treppe — BEDIENUNG

## Ein Werkzeug gibt es — die Engine-Fläche

`engine-treppe` in `resources/planner/hausplaner/app/dashboard/enginePanels.ts:121`,
Titel **„Treppen-Auslegung"**. *Am Bildschirm gesehen: sie zeigt heute die Plakette
„✓ Alle Prüfungen bestanden".*

## Was der Anwender eingibt

Geschosshöhe, gewünschte Steigungshöhe, Auftritt, **Nutzungsbereich** — und **freiwillig**
Laufbreite und Durchgangshöhe.

**Die letzten beiden sind der Punkt, den man kennen muss:** *werden sie nicht angegeben, werden sie
auch nicht geprüft* (`resources/planner/hausplaner/geometry/treppenBerechnung.ts:93`, `:97`). **Das Ergebnis sagt trotzdem
„bestanden".** Siehe `7-GRENZEN`.

## Der Nutzungsbereich ändert die Norm

```text
treppenBerechnung.ts:53-55
  Bereich    Steigung hoechstens   Auftritt mindestens   Laufbreite mindestens
  wohnung          200 mm                230 mm                 800 mm
  gebaeude         190 mm                260 mm                1000 mm
  aussen           160 mm                300 mm                1000 mm
```

**Dieselbe Treppe ist in der Wohnung zulässig und im Gebäude nicht.** *Der Bereich ist kein
Komfortschalter, sondern die Wahl der Grenzwerte.*

## Was er zurückbekommt

Maße **und eine Prüfliste mit sieben Einträgen**, jeder mit Klartext, Ist-Wert und Sollwert —
nicht nur ein Urteil.
