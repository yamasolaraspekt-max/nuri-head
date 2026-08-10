# W-02 · Wand zeichnen — CODE

**Angebunden aus zwei vorhandenen Dateien** — dieses Blatt wurde am 10.08.2026 **aus dem Code
abgeleitet**, nicht umgekehrt.

## `resources/planner/hausplaner/geometry/wallGeometry.ts` — 317 Zeilen, 12 Ausfuhren

`Punkt` · `wandLaenge()` · `punktAufWand()` · `azimutDerNormalen()` · `istGanzzahlig()` ·
`WandEingabe` · `WandBand` · `wandBaender()` · `TuerAnschlag` · `TuerOeffnung` ·
`TuerBlattGeometrie` · `tuerBlattGeometrie()`

## `resources/planner/hausplaner/geometry/wandFlaeche.ts` — 238 Zeilen, 6 Ausfuhren

`Bezugsmass` · `WandMengen` · `MeldungArt` · `Meldung` · `WandFlaecheErgebnis` · `wandMengen()`

## AUSSCHLÜSSE — bewusst NICHT Teil dieses Werkzeugs

```text
resources/planner/hausplaner/geometry/wandaufbau.ts       nicht angebunden
resources/planner/hausplaner/geometry/linienBauteile.ts   nicht angebunden
```

**Beide Dateien existieren im Repo und werden hier ausdrücklich nicht beschrieben.** *Der Auftrag
grenzt W-02 auf Geometrie und Mengen ein; Schichtaufbau und Linienbauteile bekommen eigene Blätter.*

## Was gebaut ist und was nicht

**Gebaut:** beide Rechenschichten, rein und ohne DOM-, Store- oder Befehls-Abhängigkeit.
**Nicht Gegenstand dieser Stufe:** die Werkzeugschicht. *Stufe 2 (`GEBAUT`) folgt als eigener Auftrag.*
