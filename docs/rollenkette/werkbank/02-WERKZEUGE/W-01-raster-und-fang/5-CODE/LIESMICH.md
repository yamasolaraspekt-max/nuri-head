# W-01 · Raster und Fang — CODE

**Angebunden an `resources/planner/hausplaner/geometry/fangKern.ts`** — 276 Zeilen, elf Ausfuhren.

## Herkunft dieses Blattes

**Die Rechenschicht war zuerst da.** Dieses Werkzeugblatt wurde am 10.08.2026 **aus dem
vorhandenen Code abgeleitet**, nicht umgekehrt. *Kein Satz in den sieben Blättern behauptet etwas,
das `fangKern.ts` nicht belegt.*

## Was gebaut ist und was nicht

```text
GEBAUT        die Rechenschicht: Arten, Rangfolge, Toleranzmodell, Zoom-Umrechnung,
              Beschriftung, Fangpunkte aus Waenden
NICHT GEBAUT  die Werkzeugschicht - in der toolRegistry gibt es kein Werkzeug
              fuer Raster und Fang
```

**Der Fang ist damit kein Werkzeug im Sinne der Werkbank, sondern eine Schicht darunter.**
*Stufe 2 (`GEBAUT`) folgt als eigener Auftrag.*
