# W-30 · Flachdach-Aufbau — PRÜFUNG

## Der Wächter: `dachformVorlagen.test.ts` — 1410 Z., 105 Zusagen, alle grün

**Selbst gefahren:** `ℹ tests 105 · pass 105 · fail 0`

> ***Es ist kein Flachdach-Wächter, sondern der Wächter ALLER Dachformen*** — *W-30 hat keinen
> eigenen.* **Vier seiner Zusagen betreffen das Flachdach unmittelbar.**

## Die zwei, auf die es ankommt

> ***„clampPitchGrad: 0→{1,true}, 90→{85,true}, 35→{35,false}; Flach [1.5,8]"*** (`:226-235`).
> **Acht Behauptungen in einer Zusage**, *darunter drei allein für das Flach-Band:*
> `clampPitchGrad(0, 1.5, 8) → {1.5, true}` · `(10, …) → {8, true}` · `(3, …) → {3, false}`.
> *Dazu `NaN` und `Infinity` → `geklemmt: true`.* **Die Zusage hält nicht nur den Wert, sondern
> auch die MELDUNG fest** — *`geklemmt` ist der Unterschied zwischen Korrektur und stillem
> Abschneiden.*

> ***„filterVorlagen: status verfügbar = 88 (inkl. 6 Flachdach-L/T/U); category flat = rect +
> L/T/U-Polygon"*** (`:262-271`) *mit* `assert.equal(flatVerf.length, 28)`. **Eine harte Zahl über
> den Bestand** — *sie fängt jede Vorlage, die verschwindet oder dazukommt, ohne dass jemand es
> wollte.*

## Was KEIN Wächter hält — und hier ist die Lücke der Befund selbst

| ungeprüft | Folge |
|---|---|
| **die zwei Flachdach-Welten** | dass `dachVorlage.ts:23` **0°** setzt, während `dachformVorlagen.ts:497` auf **1,5°** klemmt, prüft **niemand** |
| **`>= 2 %` gegen `1,5°`** | der Text empfiehlt 1,15°, die Klemmung verlangt 1,5° — keine Zusage vergleicht sie |
| **`attika`** | Feld und Sinnbild, **keine** Zusage auf den Wert |
| **Abläufe** | nichts zu prüfen, weil nichts da ist |

> ***Die erste Zeile ist die teuerste.*** *Beide Seiten sind für sich geprüft — die Insel hat ihre
> Dachformen, die Vorlagen haben ihre Klemmung.* **Was fehlt, ist eine Zusage, die sie
> gegeneinander hält.** *Und sie fehlt nicht aus Nachlässigkeit: die zwei Module kennen einander
> nicht, also gibt es keinen Ort, an dem so eine Zusage natürlich läge.*

## Wie diese Ablesung rot werden könnte

**Durch eine falsche Ablesung — und ich bin zweimal knapp daran vorbei:**

1. *Meine erste Fassung schrieb „Gefälle nur als Text".* **Gemessen gibt es `clampPitchGrad(pitch,
   1.5, 8)` mit eigenem Band und acht Zusagen.** *Ich hatte nur den `spannweiteHinweis` gesehen.*
2. *Danach hätte ich „Gefälle ist gerechnet" schreiben können und wäre wieder zu grob gewesen.*
   **Die Rechnung gilt für die VORLAGEN-Welt; die Insel-Welt hat sie nicht.**

> ***Beide Zwischenstände wären plausibel gewesen und beide falsch*** — *derselbe Verlauf wie bei
> W-29, wo erst die dritte Messung trug.* **Zum vierten Mal in dieser Runde: die erste Messung ist
> die, der man am wenigsten trauen darf.**

**Alle Zahlen mit Träger:** *1410 Z. und 105 Zusagen für `dachformVorlagen.test.ts`, 28 verfügbare
Flachdach-Vorlagen, 13 Flaggen in `FLAGS_FLACH` (alle `false`), Band `[1,5°; 8°]` = `[2,62 %;
14,05 %]`.*
