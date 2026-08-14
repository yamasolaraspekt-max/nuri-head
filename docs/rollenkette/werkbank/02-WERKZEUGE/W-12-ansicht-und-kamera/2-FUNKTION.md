# W-12 · Ansicht und Kamera — FUNKTION

## Die VIER Gegenstände, jeder mit Fundstelle am Bau-Stand

### 1 · Ansichtszustand

```text
store/hausplanerStore.ts:20   export type HausplanerModus = '2d' | 'split' | '3d'
                       :28    modus: HausplanerModus          das Feld
                       :45    setModus: (modus) => void       die Schnittstelle
                       :126   setModus: (modus) => set({ modus })   der Rumpf
```

**Der Rumpf ist eine Zeile: `set({ modus })`.** *Kein Befehl, keine Historie, kein Patch — die
Ansicht ist **kein Dokumentzustand** und deshalb auch nicht rückgängig zu machen.* **Das ist
richtig so:** *wer von 3D nach 2D wechselt, hat nichts am Gebäude geändert.*

### 2 · Kamera und Steuerung

```text
renderers/three-d/szene.ts:23    import { OrbitControls } from 'three/examples/jsm/…'
                          :100   private readonly kamera: THREE.PerspectiveCamera
                          :101   private readonly steuerung: OrbitControls
                          :170   this.kamera = new THREE.PerspectiveCamera(…)
                          :178   this.steuerung = new OrbitControls(this.kamera, …domElement)
```

**Perspektivkamera, nicht orthografisch** — *das Gebäude wird angesehen, nicht aufgerissen.*
**`OrbitControls` kommt aus `three` und ist nicht nachgebaut** — *Umkreisen, Zoomen, Schwenken sind
keine eigene Rechnung dieses Werkzeugs.*

### 3 · Raster — in ZWEI SCHICHTEN, nicht in zwei Renderern

```text
3D   renderers/three-d/szene.ts:212-215
       new THREE.GridHelper(80, 80, 0xcfd6de, 0xe2e6ea)
       transparent, opacity 0.5, an die Szene gehaengt

2D   die Konva-Buehne — und die GANZE Kette, nicht eine Typzeile:
       app/HausplanerApp.tsx:349         const [rasterAn, setRasterAn] = useState(true)
                            :1261-1269   rasterLinien werden ERZEUGT (Weltmasse / zoom)
                            :1337 :1409  rasterAn wird durchgereicht
       app/dashboard/Kopfrahmen.tsx:304  der KNOPF  (icon="grid", aktiv={rasterAn})
       app/rahmen/Buehne.tsx:146         {rasterAn && rasterLinien}   die ZEICHENSTELLE
```

> ***„Beide Renderer" wäre falsch:*** *`renderers/` enthält nur `three-d/`.* **Die 2D-Ansicht ist
> keine Renderer-Schicht, sondern die Konva-Bühne in `app/rahmen/`.** *Die Unterscheidung ist
> Schicht, nicht Renderer.*

> ***Und `Buehne.tsx:62` ist als Beleg NICHT zulässig:*** *dort steht `rasterAn: boolean;` — die
> **Props-Typzeile**.* **Ein Typeintrag beweist nicht, dass gezeichnet wird** — H-8, der Ort ist
> nicht die Wirkung. *Der Beleg ist `:146`, wo `rasterLinien` wirklich in den Baum geht.*

### 4 · F-032 — Transformation eines Punktes

```text
renderers/three-d/szene.ts:621   const m = new THREE.Matrix4().makeBasis(…)
                          :627   geometrie.applyMatrix4(m)
```

**Das ist F-032 in gebauter Form** — *eine homogene Matrix, aus Basisvektoren aufgebaut und auf die
Geometrie angewandt.* Siehe `3-FORMELN`.

## Verarbeitung — es gibt keinen Zustandsautomaten

**Der Ansichtswechsel ist ein Setzen, kein Ablauf.** *Kein Zwischenzustand, kein Abbruch, keine
Bestätigung.*

```text
Knopf gedrueckt  ->  setModus('3d')  ->  set({ modus })  ->  neu gerendert
```

## Ausgabe

| Was | Typ | Wohin |
|---|---|---|
| `modus` | `HausplanerModus` | Modell-Store, gelesen von der Hauptansicht und der Activation-Engine |
| `rasterAn` | `boolean` | React-Zustand der Hauptansicht, durchgereicht an die Bühne |
| Kameralage | intern in `szene.ts` | **nirgends im Dokument** — sie überlebt keinen Neuaufbau |

## Kommando (für Rückgängig)

**Keines, und das ist Absicht.** *Weder Ansicht noch Raster noch Kameralage sind Dokumentzustand.
Ein Undo nach einem Ansichtswechsel nähme die letzte **Zeichnung** zurück — nicht den Wechsel.*

## Schichtzuordnung

- **Ändert Schicht 1 (Domäne):** *nein.*
- **Rechnet in Schicht 2 (Geometrie):** **F-032**, in `szene.ts:621/:627`.
- **Lebt in Schicht 3 (Anwendung):** `store/hausplanerStore.ts` (Ansicht), `HausplanerApp.tsx`
  (Raster).
- **Zeigt sich in Schicht 4/5:** `renderers/three-d/szene.ts`, `app/rahmen/Buehne.tsx`,
  `app/dashboard/Kopfrahmen.tsx`.
