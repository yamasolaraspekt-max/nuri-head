# W-12 · Ansicht und Kamera — PRÜFUNG

## Die Falle zuerst: das Wort `modus` liefert doppelt so viele Dateien wie der Import

**Am Bau-Stand gemessen:**

```text
'modus' als WORT in Testdateien                      11
IMPORT aus store/hausplanerStore                      5
davon mit 'modus' im Rumpf                            2
```

> ***Elf gegen zwei.*** *Und der Grund ist genau der aus `1-ZWECK`: `modus` heißt zweimal etwas
> anderes — die Studio-Achse (`start`/`guided`/`expert`) trägt dasselbe Wort.* **Wer die Wächter
> über das Wort zählt, zählt Tests mit, die von W-12 nichts wissen.**

*Dieselbe Falle wie bei W-18, wo das Wort „kontur" zwölf Testdateien lieferte und der Import eine.*

## Die Wächter, nach ZUGRIFFSART getrennt

### IMPORT — Ansichtszustand

| Wächter | was er berührt |
|---|---|
| `eineWerkzeugzeile.test.ts` | `modus` 2× — die Ansicht als Bedingung für die Werkzeugzeile |
| `rechte.test.ts` | `modus` 1× |

*Drei weitere Dateien importieren den Store, ohne `modus` zu benutzen — sie prüfen andere Felder und
gehören nicht zu W-12.*

### IMPORT — 3D-Szene

```text
dachAusKontur.test.ts     importiert renderers/three-d/szene
sonnenRichtung.test.ts    ebenso
```

> **Beide prüfen NICHT die Kamera** — *sie prüfen, was in der Szene entsteht (Dachflächen,
> Sonnenstand).* **Für Kamera und `OrbitControls` gibt es keinen eigenen Wächter** — siehe unten.

### QUELLE — der Rasterschalter

**`kopfrahmen.test.ts:110`** liest den Kopfrahmen als Text und verlangt wörtlich:

```text
assert.match(kopf, /icon="grid" aktiv=\{rasterAn\}/,
             'der Raster-Schalter zeigt einen verdrehten Zustand')
```

**Und der Kommentar darüber (`:108`) nennt den Anlass:**

> *„Beide Mutationen (`aktiv={!rasterAn}`, `aktiv={!scene.settings.snapEnabled}`) kamen durch."*

> ***Das ist eine Zusage, die aus einer Fangprobe entstanden ist.*** *Ein verdrehter Schalter zeigt
> „aus", wenn das Raster an ist — und beide Zustände sehen plausibel aus. Kein Modelltest hätte das
> gefunden, weil am Modell nichts falsch ist.*

## Was NICHT geprüft wird — und es ist mehr, als man erwartet

| ungeprüft | Folge |
|---|---|
| **Kamera und `OrbitControls`** | kein Wächter. *Umkreisen/Zoomen/Schwenken kommen aus `three`; ein Bruch fiele erst im Bild auf.* |
| **`GridHelper` in 3D** | kein Wächter auf `szene.ts:212-215` |
| **Die 2D-Rasterkette als Ganzes** | verriegelt ist der **Schalter** (`kopfrahmen.test.ts`), nicht die Zeichenstelle `Buehne.tsx:146` |
| **`setModus` gegen Verwechslung** | keine Zusage hält fest, dass die zwei `setModus` getrennt bleiben |

> ***Die letzte Zeile ist die wichtigste dieses Blattes.*** *Die Verwechslung, die `1-ZWECK`
> beschreibt, ist durch **nichts** ausgeschlossen — nur durch die Typen: `setModus('guided')` auf dem
> Modell-Store bricht `tsc`, weil `'guided'` kein `HausplanerModus` ist.* **Das Typsystem trägt
> hier, was sonst ein Wächter tragen müsste** — und es trägt nur, solange die Wertemengen sich nicht
> überschneiden.
