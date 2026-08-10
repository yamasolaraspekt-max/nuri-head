# W-01 · Raster und Fang — BEDIENUNG

## Was der Anwender tut

**Nichts Eigenes.** Der Fang liegt unter jedem Setzen und Ziehen eines Punktes.

## Was er sieht

Die Art des Fangs wird benannt — nie stumm:

| Art | Beschriftung |
|---|---|
| `endpunkt` | Endpunkt |
| `mittelpunkt` | Wandmitte |
| `achse` | Wandflucht |
| `verlaengerung` | Verlängerung |
| `ortho` | rechter Winkel |
| `raster` | Raster |
| `keiner` | *(leer)* |

> *Belegt als `FANG_TEXT` in `resources/planner/hausplaner/geometry/fangKern.ts`.*

## Was der aufrufende Code stellen muss

| Feld | Pflicht | Wirkung |
|---|---|---|
| `toleranzMm` | **ja** | die einzige Fangbedingung |
| `raster` | nein | ohne Angabe kein Rasterfang |
| `ortho` | nein | Bezugspunkt für den rechten Winkel |
| `orthoToleranzMm` | nein | eigene Toleranz für `ortho` |
| `mitten` | nein | Kandidaten für `mittelpunkt` |
| `achsen` | nein | Strecken für `achse` und `verlaengerung` |
| `weg` | nein | die aktuell gezogene Strecke |
| `aktiv` | nein | `false` schaltet den Fang ab |

**Jede Fangart braucht ihren eigenen Operanden.** *Ohne ihn kann sie nicht feuern — deshalb ändert
sich das Verhalten bestehender Aufrufer nicht, wenn Arten hinzukommen.*

## Toleranz: Pixel werden zu Millimetern

`FANG_PX = 12` ist die Toleranz **in Bildschirmpixeln**; `toleranzAusZoom()` rechnet sie in mm um.
**Der Kern selbst kennt keinen Zoom.**
