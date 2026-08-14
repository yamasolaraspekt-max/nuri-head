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

> ***Und die erste Fassung dieses Blattes ist ihr selbst zum Opfer gefallen*** — *in genau diesem
> Abschnitt.* **Sie führte `eineWerkzeugzeile.test.ts` und `rechte.test.ts` als Wächter des
> Ansichtszustands auf. Beide sind Fehltreffer des Wortes:**
>
> ```text
> eineWerkzeugzeile.test.ts:71   'Ansichtsmodus' ist ein GRUPPENNAME im Markup;
>                                geprueft wird die REIHENFOLGE der Beschriftungen
> rechte.test.ts:138             'modus' steht in einem SUCHMUSTER fuer das
>                                Abhaengigkeits-Array; geprueft wird laut :134-137
>                                ausdruecklich, dass 'rechte' in der Liste steht
> ```
>
> **Keiner der beiden bewacht die Ansicht.** *Gefunden hat es der Evaluator (14.08.); die
> Berichtigung steht unten.* **Der Träger ist nicht der Import — er ist die QUELLE.**

## Die Wächter, nach ZUGRIFFSART getrennt

### QUELLE — der Ansichtszustand, und er ist zeichengenau gebunden

**Alle drei lesen QUELLTEXT; keiner importiert den Store.** *Die Ansicht ist ein Zustand der
Oberfläche, und geprüft wird, wie die Oberfläche ihn verdrahtet.*

| Wächter | Stelle | Zusage |
|---|---|---|
| `kopfrahmen.test.ts` | **`:93` (K-03, Bindung)** mit `modusKnoepfe()` `:85-89` | **jeder Knopf zeigt SEINEN Zustand und schaltet auf SEINEN Modus** |
| `buehne.test.ts` | **`:184-188` (K-05, Grenze)** | die **Hülle** hängt an `modus`, die Bühne selbst kennt ihn nicht |
| `ansichtBereit.test.ts` | **`:161`/`:164`** | die Bühnenbreite darf **nicht** am Modus hängen |

**K-03 ist die tragende Zusage — und sie prüft drei Dinge je Knopf** (`:98-104`):

```text
zeilen.length === 3                          es sind DREI Knoepfe
label="2D" / "Split" / "3D"                  jeder ist da
aktiv={modus === '2d'|'split'|'3d'}          jeder zeigt SEINEN Zustand
setModus('2d'|'split'|'3d')                  jeder schaltet auf SEINEN Modus
```

> ***Und sie ist aus DREI durchgekommenen Mutationen entstanden*** — der Kommentar sagt es wörtlich:
> *„`aktiv={modus === '2d'}` auf `'3d'` gedreht, der Split-Knopf mit fremdem Zustand, der 3D-Knopf
> mit fremdem Ziel. **Die Knöpfe sahen unverändert aus und zeigten den falschen Zustand an.**"*
>
> **Das ist die Klasse, die kein Modelltest findet:** *am Zustand ist nichts falsch, nur die Bindung
> zeigt woandershin.* **`modusKnoepfe()` schneidet dafür die `<OpGruppe name="Ansichtsmodus">`
> heraus und bricht ab, wenn es sie nicht findet** (`:89`, *„die Zusage misst Leere"*).

**K-05 hält die Gegenrichtung fest:** *`assert.doesNotMatch(buehne, /modus/)` — **die Bühne darf den
Ansichtsmodus NICHT kennen.*** Die Hülle blendet sie in 3D aus und trägt im Split die Trennlinie.

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
| **`setModus` gegen Verwechslung** | keine Zusage hält fest, dass die **zwei** `setModus` getrennt bleiben |

> ***Die letzte Zeile ist die wichtigste dieses Blattes.*** *Die Verwechslung, die `1-ZWECK`
> beschreibt, ist durch **nichts** ausgeschlossen — nur durch die Typen: `setModus('guided')` auf dem
> Modell-Store bricht `tsc`, weil `'guided'` kein `HausplanerModus` ist.* **Das Typsystem trägt
> hier, was sonst ein Wächter tragen müsste** — und es trägt nur, solange die Wertemengen sich nicht
> überschneiden.

### Was hier ausdrücklich NICHT steht

**Der Ansichtsmodus-SCHALTER gehört nicht in diese Liste.** *Er ist durch **K-03** zeichengenau
gebunden — Zustand und Ziel je Knopf, aus drei durchgekommenen Mutationen entstanden.*

> *Die erste Fassung dieses Blattes schwieg an dieser Stelle, und Schweigen wird hier als „ungesichert"
> gelesen. **Ein Blatt, das eine vorhandene Verriegelung nicht nennt, lädt dazu ein, sie zu
> ersetzen oder zu entfernen** — und der nächste Bau hielte den Schalter für ungeprüftes Gebiet.*
