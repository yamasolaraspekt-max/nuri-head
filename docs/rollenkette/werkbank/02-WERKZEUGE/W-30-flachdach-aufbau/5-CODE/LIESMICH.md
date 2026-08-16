# W-30 · Flachdach-Aufbau — CODE

## Der Gegenstand liegt in EINER großen Datei — und in einer zweiten, die davon nichts weiß

| Ort | Was |
|---|---|
| `geometry/dachformVorlagen.ts` | `category 'flat'`, `svgFlach` (`:644`), `FLAGS_FLACH` (`:1355`), `attika` (`:163`/`:223`), `clampPitchGrad` (`:402`), Warnungen `PULT_GEFAELLE` (`:488`) und `PITCH_GEKLEMMT` |
| `geometry/dachVorlage.ts` | `DachForm` (`:9`) mit `'flach'`, `neigungGrad: 0` (`:23`) — **die Insel-Welt** |
| `app/rahmen/EigenschaftenPanel.tsx` | `:251` das Auswahlfeld, `:258` Neigung 0…89 |
| `__tests__/dachformVorlagen.test.ts` | 1410 Z., **105** Zusagen, darunter die Flach-Klemmung (`:226`) und die Vorlagenzahl (`:271`) |

```text
Beruehrung zwischen dachVorlage.ts und dachformVorlagen.ts:   KEINE
clampPitchGrad-Verbraucher in app/ und dachVorlage.ts:        NULL
attika-Verbraucher ausserhalb dachformVorlagen.ts:            KEINER
```

## Die 28 Flachdach-Vorlagen

```text
dachformVorlagen.test.ts:271   flatVerf.length === 28   // 22 rect + 6 L/T/U
                        :269   darunter flach-bitumen, flach-gruendach
                        :270   l-shape-flat, flach-t, flach-u
```

> **Ein Flachdach kann auch L-, T- oder U-förmig sein** — *sechs der 28 sind es.* *Damit greift für
> sie die Polygon-Zerlegung aus `dachAusschnitt`/`dachGeometrie` genauso wie für geneigte Dächer.*

## Wo Flachdach sonst noch vorkommt

```text
dachAusschnitt.ts · dachGeometrie.ts · gaubeGeometrie.ts
aufbauOrientierung.ts · dachVerschneidung.ts · scene.types.ts:403
```

**`scene.types.ts:403`** *ist die knappste Definition im ganzen Bestand:*

> `azimut_grad: number | null;   // null = horizontal (Flachdach); sonst abgeleitet aus der First-Richtung`

> ***Ein Flachdach ist im Schema dadurch definiert, dass es KEINE Ausrichtung hat.*** *Das ist
> sauber und hat eine Folge:* **ohne Azimut gibt es keine First-Richtung, und ohne First-Richtung
> keine Gefällerichtung** — *genau die Größe, die `gefaelleRichtung` für das Pultdach führt und für
> das Flachdach nicht.*
