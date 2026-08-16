# W-26 · Dachschichten (Aufbau) — FORMELN

## Die einzige Rechnung: zwei Schwellenvergleiche

`neigungBrauchtZusatzmassnahme(pitchGrad, mindestneigungGrad, rdnGrad)` (`:432-452`)

```text
pitch < rdnGrad        -> NEIGUNG_UNTER_RDN      Schwere 'warnung'
                          „liegt unter der Regeldachneigung (Richtwert X°).
                           Zusatzmassnahmen ..."
pitch < mindestneigung -> NEIGUNG_UNTER_MINDEST  Schwere 'warnung'
```

**Beide sind `warnung`, keine `fehler`** — *sie sperren nichts.* **Und beide prüfen gegen
`Number.isFinite`**, *bevor sie vergleichen* — **ein fehlender Wert erzeugt also keine Warnung
statt einer falschen.**

> ***Das ist die richtige Richtung für einen Richtwert:*** *eine Neigung unter der
> Regeldachneigung ist nicht verboten, sie verlangt Zusatzmaßnahmen.* **Ein `fehler` hier hätte
> Bauformen ausgeschlossen, die es gibt.**

## Warum diese Formel im Widerspruch zur Entscheidung zu stehen SCHEINT — und es nicht tut

**Die Regeldachneigung ist eine Eigenschaft der EINDECKUNG.** *Ein Ziegel hat eine andere RDN als
Trapezblech.* **Ein Werkzeug, das „deckungsneutral" ist, kann sie streng genommen nicht kennen.**

**Der Code sagt genau das — im Kommentar am Feld** (`:119-120`):

```text
rdnGrad: number;             // Regeldachneigung als allgemeiner RICHTWERT
                             // (produktabhaengig zu pruefen)
mindestneigungGrad: number;  // RICHTWERT (produktabhaengig zu pruefen)
```

**und zusätzlich als eigene Felder** (`:117-118`):

```text
regeldachneigungAbhaengigVonMaterial: boolean   // RDN ist produkt-/materialabhaengig
lattmassAbhaengigVonProdukt: boolean            // Deckmass/Lattung ist produktabhaengig
```

> ***Das Haus weiß, dass sein Richtwert produktabhängig ist, und sagt es an drei Stellen.***
> **Es rechnet trotzdem mit einem allgemeinen Wert — und nennt ihn Richtwert statt Bemessung.**
> *Das ist dieselbe Ehrlichkeit wie `ABWASSER_VORBEHALT` in FG-02 und `SCHNEEFANG_HINWEIS`: die
> Grenze steht neben der Zahl.*

## Was NICHT gerechnet wird, obwohl die Felder es nahelegen

| Feld | was eine Rechnung daraus machen würde | heute |
|---|---|---|
| `battenDistCm` | Lattenabstand → Anzahl Latten je Dachfläche | **nur gespeichert** |
| `konterlattungMm` | Höhe der Belüftungsebene → Aufbauhöhe | **nur gespeichert** |
| `unterdeckungKlasse` | Klasse → zulässige Unterschreitung der RDN | **nur gespeichert** |
| `firstausbildung` · `gratausbildung` · `kehlausbildung` | Detailausbildung → Zubehörmengen | **nur gespeichert** |
| `empfohleneEindeckung` | Vorschlag → Vorbelegung der Produktauswahl | **nur gespeichert** |

> **Die dritte Zeile ist die interessanteste:** *`unterdeckungKlasse` ist fachlich genau der Wert,
> der bestimmt, wie weit man unter die Regeldachneigung gehen darf.* **Er steht neben der
> RDN-Prüfung und wird von ihr nicht gelesen** — *die Warnung könnte präziser sein, als sie ist,
> mit Daten, die schon dastehen.*

## Ein Zahlenbeispiel aus dem Bestand

```text
:1723   pitchedDachdecker({ empfohleneEindeckung: 'ziegel', rdnGrad: 22,
                            mindestneigungGrad: ... })
:1384   konterlattungMm: [24, 48]
:1410   konterlattungMm: [0, 0]
```

> *Die `[0, 0]` ist die Flachdach-Vorlage* — **ein Vertrag, der auch dort gefüllt wird, wo er
> keinen Sinn ergibt, weil das Feld nicht optional ist.** *Kein Schaden, aber ein Hinweis darauf,
> dass der Block als Formular und nicht als Rechengrundlage entstanden ist.*
