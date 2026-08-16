# W-30 · Flachdach-Aufbau — FORMELN

> **Am Code erhoben.** *Die Registerzeile sagt* `ungeprüft — dachformVorlagen (attika, svgFlach)`.
> **Gemessen: die genannten zwei sind Datenfeld und Zeichnung; die einzige RECHNUNG steht woanders.**

## Die eine Rechnung: eine Klemmung mit eigenem Band

```text
:497  cat === 'flat' ? clampPitchGrad(pitch, 1.5, 8) : clampPitchGrad(pitch)
:402  clampPitchGrad(pitchGrad, min = 1, max = 85) -> { wert, geklemmt }
```

> ***Keine F-Nummer aus der Sammlung trifft das.*** *Eine Klemmung ist keine Geometrie, sondern eine
> Bereichsentscheidung.* **Was sie fachlich trägt, ist das BAND** — *`[1,5°; 8°]` ist die Definition
> „Flachdach" in Zahlen.*

## Die Umrechnung, die im Code NICHT steht und für das Verständnis nötig ist

```text
Grad -> Prozent:   tan(g) * 100
  1,5 Grad  ->   2,62 %
  8,0 Grad  ->  14,05 %
  2 %       ->   1,15 Grad
```

> **Der Code rechnet ausschließlich in GRAD, die Fachwelt spricht in PROZENT** — *und der einzige
> Prozentwert im Werkzeug steht in einem Freitext* (`:1599`). **Eine Umrechnung gibt es nirgends.**
> *Das ist die Stelle, an der die Abweichung zwischen `>= 2 %` und `1,5°` entstehen konnte, ohne
> dass sie jemandem auffällt.*

## Was NICHT gerechnet wird

| erwartet | gemessen |
|---|---|
| **Gefälledämmung** (Keildämmung, Dicken über die Fläche) | keine — `dachstuhltyp` nennt sie als **Text** |
| **Attika-Höhe, -Länge, -Fläche** | keine — `attika` ist eine Zahl, die nur `ja/nein` fürs Sinnbild wird |
| **Abläufe** (Anzahl, Lage, Einzugsfläche) | **kommt im ganzen Werkzeug nicht vor** |
| **Pfützenbildung** | keine — `spannweiteHinweis` sagt „beachten", das Modul prüft nichts |

> ***Die dritte Zeile ist die auffälligste.*** *„Abläufe" steht im Titel der Registerzeile und im
> ganzen Inselbaum nirgends als Gegenstand* — **die Einzugsfläche je Ablauf wäre die klassische
> Flachdach-Rechnung, und sie fehlt vollständig.**
