# W-12 · Ansicht und Kamera — BEDIENUNG

## Der Ansichtswechsel: drei Knöpfe in der Kopfzeile

**`app/dashboard/Kopfrahmen.tsx:290-292`**, jeder mit Titel:

| Knopf | Titel | setzt |
|---|---|---|
| 2D | „2D-Grundriss" | `setModus('2d')` |
| Split | „2D und 3D nebeneinander" | `setModus('split')` |
| 3D | „3D-Ansicht" | `setModus('3d')` |

**Der Titel sagt, was man SIEHT, nicht wie der Zustand heißt.** *„2D und 3D nebeneinander" ist
verständlich; `split` wäre es nicht.*

## Der Rasterschalter

**`Kopfrahmen.tsx:304`** — *ein Knopf mit `icon="grid"` und dem Titel* **„Raster ein-/ausblenden —
Hintergrund-Hilfslinien"**.

```text
aktiv={rasterAn}     der Knopf zeigt den ZUSTAND, nicht die Handlung
```

> **Die Unterscheidung ist verriegelt** (`kopfrahmen.test.ts:110`): *die Zusage verlangt wörtlich
> `icon="grid" aktiv={rasterAn}` — und der Kommentar darüber sagt, warum:* **„Beide Mutationen
> (`aktiv={!rasterAn}`, …) kamen durch."** *Ein verdrehter Schalter zeigt „aus", wenn das Raster an
> ist — und niemand merkt es, weil beide Zustände plausibel aussehen.*

**Vorbelegung: `useState(true)`** (`HausplanerApp.tsx:349`) — *das Raster ist beim Öffnen AN.*

## Die Kamera im 3D-Bild

**`OrbitControls`** (`szene.ts:178`), gebunden an die Perspektivkamera und die Zeichenfläche:

```text
Ziehen         umkreisen
Rad            zoomen
Ziehen rechts  schwenken
```

*Das sind die Vorgaben von `three`; W-12 bindet sie an, statt sie nachzubauen.*

> **Die Kameralage überlebt keinen Neuaufbau der Szene** — *sie liegt in `szene.ts` und in keinem
> Dokument.* **Wer die Ansicht wechselt und zurückkommt, steht wieder in der Ausgangslage.** *Das
> ist eine Grenze, keine Absicht — siehe `7-GRENZEN`.*

## Was der Anwender NICHT bedient

- **Die Rasterweite** — *sie folgt dem Zoom (`weltBreite = breite / zoom`), ohne Bedienelement.*
- **Die Kamera in 2D** — *dort gibt es Zoom und Verschub der Bühne, aber keine Kamera.*
- **Den Studio-Modus** (`start`/`guided`/`expert`) — *das ist die andere `modus`-Achse und gehört
  nicht hierher (siehe `1-ZWECK`).*

## Abbruch

**Es gibt nichts abzubrechen.** *Jeder Ansichtswechsel ist sofort und vollständig; kein
Zwischenzustand, keine Bestätigung, kein Undo — weil nichts am Gebäude geändert wurde.*
