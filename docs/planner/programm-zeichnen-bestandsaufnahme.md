# Programm „Intelligentes Zeichnen" — Bestandsaufnahme und Schnitt

**Quelle:** `docs/quellen/prompt-zeichnen-2026-07-30.md` (Yama, 30.07.2026, 22:58)
**Rolle dieses Blattes:** Planner. Kein Produktionscode. Es misst, was da ist, und schneidet
das Programm in Scheiben.
**Datum der Messung:** 30.07.2026, ~23:15 CEST, Zweig `ed4259f3`.

Yama verlangt „BESTANDSCODE-FIRST" und „Keine parallele zweite Zeichenengine". Also steht
die Messung vor dem Schnitt — und die Messung ist der Grund, warum der Schnitt so und nicht
anders liegt.

---

## Teil 1 — Was gemessen wurde (Rohbefund)

Alle Befehle im Verzeichnis `resources/planner/hausplaner`.

### B-1 · Ein Fang-Kern existiert — und wird von nichts benutzt

```
befehl:  grep -rn "fangKern" --include=*.ts --include=*.tsx .
treffer: ./__tests__/fangKern.test.ts:6: import { fange, wandFangpunkte } from '../geometry/fangKern';
```

`geometry/fangKern.ts` (103 Zeilen) hat `fange()` mit Prioritätskette
Endpunkt → Ortho → Raster, benennt den Fangtyp (`FangArt`), hat eine Toleranz-Option in mm
und einen Schalter `aktiv`. Der Kopfkommentar sagt sogar ausdrücklich: *„der Aufrufer rechnet
Bildschirm-px über den Zoom in mm-Toleranz um"*.

**Es gibt keinen solchen Aufrufer.** Der einzige Verweis im ganzen Baum ist der eigene Test.

Das ist das Muster, das ich heute neunmal bei mir selbst gefunden habe: ein Posten, der
benannt ist und auf nichts zeigt. Zum zehnten Mal, diesmal im Produktivcode.

### B-2 · Der laufende Fang ist eine zweite, andere Wahrheit

`app/HausplanerApp.tsx:577–602`, Funktion `weltPunkt`:

- Endpunkt-Fang mit **fest verdrahteten 150 mm**, nicht konfigurierbar
- Raster-Fang über `scene.settings.gridSize`
- **kein Ortho**, **kein Mittelpunkt** (obwohl `wandFangpunkte()` Mittelpunkte liefert)
- **gibt den Fangtyp nicht zurück** — der Aufrufer kann nicht anzeigen, was gefangen wurde
- Toleranz in **Weltkoordinaten (mm)**, nicht in Bildschirm-Pixeln

Damit verstößt der laufende Fang gegen genau den Punkt, den Yama unter „Wichtig" hervorhebt.
Zahl dazu: der Zoom ist auf `0.02 … 1` begrenzt (`Buehne.tsx:99`). Bei Zoom 0,02 sind
150 mm = **3 Bildschirm-Pixel**. Der Fang ist dort praktisch aus.

Der Winkel-Fang liegt nochmal woanders: `mitWinkelSnap` (`HausplanerApp.tsx:606–618`),
über `scene.settings.angleSnap`.

**Drei Fang-Orte, keiner kennt den anderen.** Der Bauordnungs-Satz „eine Wahrheit für
abgeleitete Werte" ist hier verletzt.

### B-3 · Es gibt keine Tool-Lifecycle-Schnittstelle

```
befehl:  grep -rn "activate\|deactivate\|suspend\|cancel" app/tools/*.ts
```

`app/tools/` hat 22 Dateien und 4459 Zeilen — aber sie beschreiben **Verträge**: was ein
Werkzeug *darf* (`vorbedingungen.ts`, `werkzeugVertrag.ts` mit 1419 Zeilen), wie es
*dargestellt* wird (`toolPresentation.ts`), wo es *hingehört* (`werkzeugLandkarte.ts`).
`activation.ts` (126 Z.) entscheidet über *enabled/disabled*, nicht über einen Lebenslauf.

**Keine der von Yama geforderten Methoden existiert**: kein `activate`, `suspend`, `resume`,
`cancel`, `commit`, `deactivate`. Kein `InteractiveTool`.

### B-4 · Der Werkzeugwechsel räumt an vier Stellen auf — und an einer nicht

```
befehl:  grep -rn "setWerkzeug\|setActiveTool" app/ | grep -v __tests__
```

Aufräumen (`setWandStart(null); setTreppeStart(null);`) steht **viermal abgeschrieben**:

| Ort | Zeile | räumt auf |
|---|---|---|
| `HausplanerApp.tsx` (Palette) | 494–496 | ja |
| `HausplanerApp.tsx` (Taste) | 829–831 | ja |
| `GruppenzeileUndSchiene.tsx` (Leiste) | 334 | ja |
| `GruppenzeileUndSchiene.tsx` (Menü) | 354 | ja |
| **`HausplanerApp.tsx` (Rückfall)** | **371** | **NEIN** |

Zeile 371 ist der Effekt, der auf `auswahl` zurückfällt, wenn das aktive Werkzeug im
Kontext ausfällt (z. B. Wechsel nach 3D). Er ruft `setActiveTool('auswahl')` **direkt am
Store** und lässt `wandStart` stehen. Der Setter selbst (Zeile 183) räumt nicht auf — er
reicht nur durch.

Das ist derselbe Bautyp wie B-1/B-2: **eine Regel, viermal abgeschrieben, einmal vergessen.**
Nicht Nachlässigkeit — die Struktur lädt dazu ein, weil es keinen einen Ort gibt.

Escape räumt korrekt auf (`setzeWerkzeugZurueck`, Zeile 777–782, über `useEscapeEbene`).

### B-5 · Am Canvas hängt kein Verlassen-Ereignis

```
befehl:  grep -rn "onPointerLeave\|onMouseLeave\|setPointerCapture" --include=*.tsx app/
treffer: StartView.tsx:71,148,169 · SchienenSchalter.tsx:42   (beides Hover-Zustände)
```

An der Bühne (`app/rahmen/Buehne.tsx:88–107`) hängen: `onClick`, `onMouseMove`, `onWheel`,
`onDragMove`, `onDragEnd`. **Kein `onMouseLeave`, kein `onPointerLeave`.**

`setPointerCapture` kommt im ganzen Baum **nicht vor** — Yamas Verdacht „Pointer Capture
bleibt aktiv" trifft hier also nicht zu. Das ist ein Punkt, den ich streichen kann, statt
ihn zu bearbeiten.

### B-6 · Die Vorschau ist bereits getrennt — das ist die gute Nachricht

`Buehne.tsx:363–378` zeichnet zwei Vorschauen (Treppe, Wand) als Konva-`Line` in einer
`<Group listening={false}>`. Sie stehen **nicht** im Dokument; das Dokument wird erst im
`klick`-Zweig über `executeCommand` angefasst.

Yamas Abschnitt 9 („Vorschau- und Commit-Trennung") ist damit im Kern **schon erfüllt**.
Was fehlt, ist nicht die Trennung, sondern das **Beenden**.

### B-7 · Was für Decke und Dach schon steht

- `ADD_CEILING` und `ADD_ROOF` existieren als Commands (`HausplanerApp.tsx:695–722`)
- beide arbeiten heute über `gebaeudeUmriss()` — die **Bounding-Box** der Knoten
  (`HausplanerApp.tsx:566–573`), also ein Rechteck, **keine echte Kontur**
- 3D-Dachlogik ist umfangreich vorhanden: `geometry/dachGeometrie.ts`,
  `dachVerschneidung.ts`, `dachUForm.ts`, `dachAusschnitt.ts`, `sparrenBerechnung.ts`,
  `schifterListe.ts`, `renderers/three-d/dachMesh.ts`
- `geometry/polygonFlaeche.ts` und `roomDetection.ts` sind da

Yamas „keine parallele zweite Dachengine" ist also nicht nur eine Mahnung, sondern eine
sehr reale Gefahr: es steht viel Dachcode da, den man beim Neubauen übersehen würde.

---

## Teil 2 — Was der Befund am Auftrag ändert

**Yamas Kernaussage stimmt im Ergebnis, aber nicht in jedem Detail.** Er nennt sechs
mögliche Ursachen für den langen Strich. Gemessen:

| Yamas Verdacht | Befund |
|---|---|
| pointerleave nicht behandelt | **trifft zu** (B-5) |
| Pointer Capture bleibt aktiv | **trifft nicht zu** — es gibt kein Pointer Capture (B-5) |
| Preview-State bleibt aktiv | **trifft zu** — `wandStart` überlebt Zeile 371 (B-4) |
| Tool wird nicht sauber deaktiviert | **trifft zu** — es gibt kein `deactivate` (B-3) |
| globaler pointermove | **trifft nicht zu** — `onMouseMove` hängt an der Bühne, nicht am Fenster |
| Canvas-/UI-Koordinaten vermischt | **offen** — nicht belegt, nicht widerlegt |

Daraus folgt eine Erwartung, die ich **ausdrücklich als unbewiesen kennzeichne**: weil
`onMouseMove` nur an der Bühne hängt, sollte der Cursor beim Verlassen des Canvas
*einfrieren*, nicht dem Zeiger folgen. Der Strich bliebe dann als **Rest** stehen und zeigte
Richtung Rand — was von „folgt dem Zeiger" schwer zu unterscheiden ist, wenn man es sieht
statt misst. **Die erste Handlung des Generators ist deshalb, den Fehler im Browser zu
reproduzieren und zu beschreiben, was er tatsächlich sieht** — nicht, sofort zu bauen.

---

## Teil 3 — Der Schnitt

17 Abschnitte sind ein Programm. Als ein Blatt gegeben wäre es der Fehler, den ich bei
AUF-48-S4 zweimal gemacht habe. Reihenfolge nach Abhängigkeit, nicht nach Nummer im Prompt:

| Scheibe | Inhalt | Prompt-Abschnitt | Spur | Hängt ab von |
|---|---|---|---|---|
| **Z-01** | Werkzeugwechsel als **eine** Wahrheit + Canvas-Verlassen | 7, 8 (Teil) | A | — |
| **Z-02** | `fangKern` an den laufenden Weg anschließen, Toleranz in Bildschirm-px | 1 (Teil) | A | Z-01 |
| **Z-03** | Fangtyp sichtbar machen (Badge/Statusleiste) | 1, 3, 12 | A | Z-02 |
| **Z-04** | Fang-Erweiterung: Mittelpunkt, Wandachse, Verlängerung | 1 (Rest) | A | Z-02 |
| **Z-05** | Mehrpunkt-Polygonwerkzeug mit Konturprüfung | 4, 10 | A | Z-01, Z-02 |
| **Z-06** | Decke aus gezeichneter Kontur statt Bounding-Box | 4 | A | Z-05 |
| **Z-07** | Decke aus Grundriss (Vorschlagskontur, korrigierbar) | 5 | A | Z-06 |
| **Z-08** | Dach aus Kontur auf bestehende 3D-Dachlogik | 6 | A | Z-06 |
| **Z-09** | Wand-Eckanschluss: Gehrung, T, Kreuz — geometrisch | 2 | A | Z-02, Z-04 |
| **Z-10** | Direkte Maßeingabe (Länge/Winkel) | 11 | A | Z-01 |
| **Z-11** | Touch und Stift | 13 | A | Z-01, Z-02 |

**Z-01 zuerst**, weil es die einzige Scheibe ist, die von nichts abhängt, den Fehler behebt,
den Yama tatsächlich sieht, und das Fundament legt, auf dem die anderen zehn stehen. Ohne
einen Ort, an dem ein Werkzeug endet, hat jede weitere Scheibe wieder vier Aufräumstellen.

**Nicht geschnitten:** Abschnitt 14 (Commands) und 15 (Datenmodell) sind keine eigenen
Scheiben — sie sind die Bauteile, die in Z-01 … Z-11 entstehen. Ein Blatt „lege 17 Commands
an" würde 17 Posten erzeugen, die auf nichts zeigen. Genau das Muster aus B-1.

**Offen an Yama, eine Entscheidung (Prompt-Abschnitt 7 C):** Yama gibt selbst die
Standardempfehlung — *Werkzeugwechsel bricht ab, Canvas-Verlassen pausiert nur*. Ich
übernehme sie als Festlegung für Z-01. Widerspruch bitte vor der Abnahme, nicht danach.
