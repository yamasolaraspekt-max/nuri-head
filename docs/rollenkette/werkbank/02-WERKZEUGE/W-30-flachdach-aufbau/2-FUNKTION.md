# W-30 · Flachdach-Aufbau — FUNKTION

> **Ablesung des vorhandenen Codes.** *Jede Zeilenangabe einzeln geöffnet.*

## Das Flachdach ist eine KATEGORIE, keine eigene Engine

```text
dachformVorlagen.ts:49    EngineRoofShape = 'sattel'|'pult'|'walm'|'rect'|'l-shape'|'t-shape'|'u-shape'
                   :1580  category: 'flat'
                   :1581  engineShape: 'rect'
                   :557   'sattel'|'pult'|'walm'|'rect'  -> von der Engine getragen
```

> ***`category` und `engineShape` sind zwei verschiedene Achsen***: *`flat` sagt, was es fachlich
> ist; `rect` sagt, wie die Engine es zeichnet.* **Ein Flachdach ist für die Engine ein Rechteck
> mit sehr kleiner Neigung** — *und genau deshalb braucht es die eigene Klemmung.*

## Die Gefälle-Klemmung — die einzige echte Rechnung des Werkzeugs

```text
:497   const clamp = cat === 'flat' ? clampPitchGrad(pitch, 1.5, 8)
                                    : clampPitchGrad(pitch)          // 1 .. 85
:402   clampPitchGrad(pitchGrad, min = 1, max = 85)
       :407  nicht endlich  -> min, geklemmt true
       :408  < min          -> min, geklemmt true
       :409  > max          -> max, geklemmt true
       :410  sonst          -> Wert, geklemmt false
:400   Kommentar: „PITCH_GEKLEMMT-Warnung … (kein stilles Abschneiden)"
```

> ***Das Band `[1,5°; 8°]` ist die fachliche Aussage dieses Werkzeugs in Zahlen.*** *Unter 1,5°
> steht das Wasser, über 8° ist es kein Flachdach mehr.* **Und `geklemmt` ist der Grund, warum die
> Klemmung nicht schadet:** *sie schneidet nicht still ab, sondern meldet, dass sie eingegriffen
> hat.*

## Die zweite Warnung: das Gefälle hat nur EINE Richtung

```text
:488  engineShape === 'pult' && gefaelleRichtung === 'laenge'
      -> PULT_GEFAELLE, schwere 'warnung'
      „Pult-Gefaelle ueber die Laenge wird von der Engine nicht abgebildet
       (nur ueber die Breite). Vorlage als geplant fuehren."
:86   gefaelleRichtung?: 'breite' | 'laenge'   // nur Pult (Engine fix 'breite')
```

> **Das Feld kennt zwei Richtungen, die Engine eine.** *Statt die zweite zu verschweigen oder
> stillschweigend umzudeuten, wird die Vorlage **als geplant geführt** und der Anwender erfährt es.*
> ***Dieselbe Haltung wie `OFFENE_HOLZBAUTEILE` bei W-25: die Grenze wird ausgesprochen.***

## Die Attika: ein Feld und ein Strich

```text
:163   attika?: number   // m, optional (Flachdach)
:223   attika: number    // in VorlagenApply
:644   svgFlach(p, attika: boolean)
:648     attika ? svgRect(24, 39, 84, 4, ...) : ''
:1133  case 'rect': base = svgFlach(pal, hasTok('attika'))
```

> ***Die Attika entscheidet über EINEN Rechteckstrich im Sinnbild — und über sonst nichts.***
> *Das Feld ist eine Zahl in Metern, das Sinnbild fragt nur `ja/nein` ab.* **Kein Verbraucher
> außerhalb der Vorlagendatei; keine Höhe, keine Länge, keine Fläche wird daraus gerechnet.**

## Die Zimmerer-Angaben tragen die Fachaussage

```text
:1592  dachstuhltyp   „Tragdecke mit Gefaelledaemmung (Flachdach, Richtwert)"
:1593  flags          FLAGS_FLACH — ALLE DREIZEHN auf false
:1598  abbundhinweis  „Tragdecke mit Gefaelledaemmung; kein Sparrendach."
:1599  spannweite     „Durchbiegung/Pfuetzenbildung beachten; Gefaelle >= 2 % (Richtwert)."
:1600  lastabtrag     „Abdichtung -> Daemmung -> Tragdecke -> Waende."
```

> ***`FLAGS_FLACH` setzt alle dreizehn Zimmerer-Flaggen auf `false`*** (`:1355-1359`) — **kein
> Sparren, keine Pfette, kein Kehlbalken, kein Wechsel.** *Das ist die knappste mögliche Aussage
> darüber, dass hier die ganze Holzbau-Rechnung aus W-21 und W-25 nicht greift.*
>
### Und der Text sagt eine andere Zahl als der Code — nachgerechnet

```text
Klemmuntergrenze  1,5 Grad   =  2,62 % Gefaelle
Klemmobergrenze   8,0 Grad   = 14,05 % Gefaelle
spannweiteHinweis „>= 2 %"   =  1,15 Grad
                              -> 2 % liegt UNTER der Klemmuntergrenze
```

> ***Der Hinweistext empfiehlt ein Gefälle, das die Klemmung nicht durchlässt.*** *Wer 2 % eingibt
> — 1,15° —, bekommt `PITCH_GEKLEMMT` und landet bei 1,5°, also 2,62 %.* **Der Code ist strenger
> als sein eigener Text.**
>
> **Das ist kein Widerspruch im Ergebnis** *(die Klemmung korrigiert nach oben, also in die sichere
> Richtung)* **, aber einer in der Auskunft:** *der Anwender liest 2 % als zulässig und erlebt eine
> Warnung.* *Welche der beiden Zahlen die fachlich gewollte ist, entscheidet dieses Blatt nicht —
> es hält fest, dass es zwei sind.*
